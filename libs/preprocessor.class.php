<?php

/**
 * Preprocessor implementation.
 *
 * @octdoc      c:libs/preprocessor
 * @copyright   copyright (c) 2011 by Harald Lapp
 * @author      Harald Lapp <harald@octris.org>
 */
class preprocessor
/**/
{
    /**
     * Parser tokens.
     * 
     * @octdoc  d:preprocessor/T_START, T_COMMAND, T_IDENTIFIER, T_STRING, T_ASSIGNMENT,T_SEPARATOR, T_WHITESPACE, T_END
     */
    const T_START      = 0;
    const T_COMMAND    = 1;
    const T_IDENTIFIER = 2;
    const T_STRING     = 3;
    const T_ASSIGNMENT = 4;
    const T_SEPARATOR  = 5;
    const T_WHITESPACE = 6;
    const T_END        = 7;
    /**/
    
    /**
     * Parser patterns.
     *
     * @octdoc  v:preprocessor/$patterns
     * @var     array
     */
    protected static $patterns = array(
        self::T_START      => '\{',
        self::T_COMMAND    => '[a-z]+\(',
        self::T_IDENTIFIER => '-[a-z]+',
        self::T_STRING     => "([\"']).*?(?!\\\\)\\2",
        self::T_ASSIGNMENT => '=>',
        self::T_SEPARATOR  => '\,',
        self::T_WHITESPACE => '\s+',
        self::T_END        => '\)[\}\{]'
    );
    /**/
   
    /**
     * Parser rules.
     *
     * @octdoc  v:preprocessor/$rules
     * @var     array
     */
    protected static $rules = array(
        null               => array(self::T_START),
        self::T_START      => array(self::T_COMMAND),
        self::T_COMMAND    => array(self::T_IDENTIFIER, self::T_STRING),
        self::T_IDENTIFIER => array(self::T_ASSIGNMENT),
        self::T_ASSIGNMENT => array(self::T_STRING),
        self::T_STRING     => array(self::T_SEPARATOR, self::T_END),
        self::T_SEPARATOR  => array(self::T_STRING, self::T_ASSIGNMENT)
    );
    /**/
    
    /**
     * Registered commands.
     *
     * @octdoc  v:preprocessor/$commands
     * @var     array
     */
    protected $commands = array();
    /**/
    
    /**
     * Constructor.
     *
     * @octdoc  m:preprocessor/__construct
     */
    public function __construct()
    /**/
    {
        $this->commands['include'] = array($this, '_include');
        $this->commands['date']    = function($format) { return strftime($format); };
    }
    
    /**
     * Register a command for preprocessor.
     *
     * @octdoc  m:preprocessor/registerCommand
     * @param   string      $name               Name of command to register.
     * @param   callback    $cb                 Callback to execute for command.
     */
    public function registerCommand($name, $cb)
    /**/
    {
        $this->commands[$name] = $cb;
    }
    
    /**
     * Write error message to STDERR
     *
     * @octdoc  m:preprocessor/error
     * @param   string      $message            Error message to write.
     */
    protected function error($message)
    /**/
    {
        fwrite(STDERR, $message);
    }
    
    /**
     * Predefined include function.
     *
     * @octdoc  m:preprocessor/_include
     * @param   string      $name               Name of file to include.
     * @return  bool|string                     Content of preprocessed file or false in case of an error.
     */
    protected function _include($name)
    /**/
    {
        if ($return = (is_file($name) && is_readable($name))) {
            $return = $this->process($name);
        } else {
            $this->error(sprintf("file not found or not readable '%s'\n", $name));
        }
        
        return $return;
    }
    
    /**
     * Preprocessor command parser.
     *
     * @octdoc  m:preprocessor/parse
     * @param   string      $snippet            Snippet to parse.
     * @param   resource    $fp                 Input resource.
     * @param   string      $name               Name of current file.
     * @param   int         $line               Number of current processed line.
     * @return  array|bool                      Parsed command or false in case of an error.
     */
    protected function parse($snippet, $fp, $name, $line)
    /**/
    {
        $last_token = null;
        
        $command    = '';
        $args       = array();
        $inc        = 0;
        $block      = false;
        
        $key = '';
        
        while (strlen($snippet) > 0) {
            foreach (self::$patterns as $token => $pattern) {
                if (preg_match('/^(' . $pattern . ')/', $snippet, $match)) {
                    if ($token == self::T_WHITESPACE) {
                        continue;
                    }
                    
                    if (!in_array($token, self::$rules[$last_token])) {
                        $this->error(sprintf("possible parse error: 'unexpected token' in file '%s', line: %d\n", $name, $line));
                        
                        return false;
                    }
                    
                    $inc += strlen($match[1]);
                    
                    switch ($token) {
                    case self::T_COMMAND:
                        $command = substr($match[1], 0, -1);
                        break;
                    case self::T_IDENTIFIER:
                        $key = substr($match[1], 1);
                        break;
                    case self::T_STRING:
                        if ($key != '') {
                            $args[$key] = substr($match[1], 1, -1);
                            $key = '';
                        } else {
                            $args[] = substr($match[1], 1, -1);
                        }
                        break;
                    case self::T_END:
                        $block = (substr($match[1], -1) == '{');
                        break 3;
                    }

                    $snippet    = substr($snippet, strlen($match[0]));
                    $last_token = $token;
                    continue 2;
                }                
            }

            $this->error(sprintf("possible parse error in file '%s', line: %d\n", $name, $line));
            
            return false;
        }
        
        return array($command, $args, $inc, $block);
    }
    
    /**
     * Process specified document.
     *
     * @octdoc  m:preprocessor/process
     * @param   string      $input              Filename to process.
     * @return  bool                            Returns true, if processing succeeded otherwise false.
     */
    public function process($input)
    /**/
    {
        if (!($fp = fopen($input, 'r'))) {
            return false;
        }
    
        $return = true;
        $line   = 0;

        while (!feof($fp)) {
            $row = fgets($fp);
            ++$line;
            
            $offset  = 0;

            while (preg_match('/((?<!\\\\)\{[a-z]+\(.+)/', $row, $m, PREG_OFFSET_CAPTURE, $offset)) {
                // possibly a preprocessor command
                if (!(list($command, $args, $inc, $block) = $this->parse($m[1][0], $fp, $input, $line))) {
                    // no command, increase offset to find possible other commands in row
                    $offset = $m[1][1] + 1;
                    continue;
                } else {
                    print "command: $row";
                    print_r(array($command, $args, $inc, $block));
                
                    if (!isset($this->commands[$command]) || !is_callable($this->commands[$command])) {
                        $this->error(sprintf("unknown command or command not callable '%s'\n", $command));
                
                        $return = false;
                
                        break 2;
                    }
                    
                    if (($cb_return = call_user_func_array($this->commands[$command], $args)) === false) {
                        $return = false;
                        
                        break 2;
                    }
                
                    $row = substr_replace($row, $cb_return, $offset, $inc);

                    $offset += strlen($cb_return);
                }
            }
            
            print "$row\n";
        }
        
        fclose($fp);
        
        return $return;
    }
}
