<?php

/*
 * This file is part of phpreprocess
 * Copyright (C) 2011 by Harald Lapp <harald@octris.org>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * This script can be found at:
 * https://github.com/aurora/phpreprocess
 */
 
namespace phpreprocess {
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
         * @octdoc  d:preprocessor/T_START, T_COMMAND, T_IDENTIFIER, T_STRING, T_VARIABLE, T_ASSIGNMENT,T_SEPARATOR, T_WHITESPACE, T_END
         */
        const T_START      = 0;
        const T_COMMAND    = 1;
        const T_IDENTIFIER = 2;
        const T_STRING     = 3;
        const T_VARIABLE   = 4;
        const T_ASSIGNMENT = 5;
        const T_SEPARATOR  = 6;
        const T_WHITESPACE = 7;
        const T_END        = 8;
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
            self::T_VARIABLE   => '\$[a-z_0-9]+',
            self::T_ASSIGNMENT => '=>',
            self::T_SEPARATOR  => '\,',
            self::T_WHITESPACE => '\s+',
            self::T_END        => "\)(\}|\{\s*$)"
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
            self::T_COMMAND    => array(self::T_IDENTIFIER, self::T_END),
            self::T_IDENTIFIER => array(self::T_ASSIGNMENT),
            self::T_ASSIGNMENT => array(self::T_STRING, self::T_VARIABLE),
            self::T_STRING     => array(self::T_SEPARATOR, self::T_END),
            self::T_VARIABLE   => array(self::T_SEPARATOR, self::T_END),
            self::T_SEPARATOR  => array(self::T_IDENTIFIER)
        );
        /**/
    
        /**
         * Autoloader pathes.
         *
         * @octdoc  v:preprocessor/$pathes
         * @var     array
         */
        private static $pathes = array();
        /**/
    
        /**
         * Loaded plugins.
         *
         * @octdoc  v:preprocessor/$loaded
         * @var     array
         */
        private static $loaded = array();
        /**/
    
        /**
         * Plugin defaults.
         *
         * @octdoc  v:preprocessor/$defaults
         * @var     array
         */
        private static $defaults = array();
        /**/
    
        /**
         * Defined variables.
         *
         * @octdoc  v:preprocessor/$variables
         * @var     array
         */
        protected $variables = array();
        /**/
    
        /**
         * Constructor.
         *
         * @octdoc  m:preprocessor/__construct
         */
        public function __construct()
        /**/
        {
        }
    
        /**
         * Add a path for plugins.
         *
         * @octdoc  m:preprocessor/addPluginPath
         * @param   string      $pathname           Name of path to add.
         */
        public static function addPluginPath($pathname)
        /**/
        {
            if (is_dir($pathname) && is_readable($pathname)) {
                self::$pathes[] = $pathname;
            }
        }
    
        /**
         * Set default parameters for plugins.
         *
         * @octdoc  m:preprocessor/setPluginDefaults
         * @param   array       $defaults           Array with default settings.
         */
        public static function setPluginDefaults(array $defaults)
        /**/
        {
            self::$defaults = $defaults;
        }
    
        /**
         * Set a variable to a specified value.
         *
         * @octdoc  m:preprocessor/setVar
         * @param   string      $name               Name of variable to set.
         * @param   mixed       $value              Value to set for variable.
         */
        public function setVar($name, $value)
        /**/
        {
            $this->variables[$name] = $value;
        }
    
        /**
         * Return instance of a plugin.
         *
         * @octdoc  m:preprocessor/getPlugin
         * @param   string      $name               Name of plugin.
         * @return  plugin|bool                     Plugin instance or false in case of an error.
         */
        protected function getPlugin($name)
        /**/
        {
            if (!($found = isset(self::$loaded[$name]))) {
                foreach (self::$pathes as $path) {
                    $file = $path . '/' . $name . '.class.php';

                    if (($found = (is_file($file) && is_readable($file)))) {
                        require_once($file);

                        self::$loaded[$name] = true;
                
                        break;
                    }
                }
            }

            if (!($return = $found)) {
                $this->error(sprintf('plugin not found "%s"', $name));
            } else {
                $class  = "\phpreprocess\\$name";
                $return = array(
                    new $class(
                        $this, 
                        (isset(self::$defaults[$name]) 
                            ? self::$defaults[$name]
                            : array())
                    ),
                    $class::getType()
                );
            }
        
            return $return;
        }
    
        /**
         * Write error message to STDERR
         *
         * @octdoc  m:preprocessor/error
         * @param   string      $message            Error message to write.
         * @param   string      $name               Name of file the error occured in.
         * @param   int         $line               Number of line the error occured in.
         */
        public function error($message, $name = null, $line = null)
        /**/
        {
            fwrite(STDERR, sprintf("%s\n", $message));
        
            if (!is_null($name)) {
                fwrite(STDERR, sprintf("  in file: %s\n", $name));
            
                if (!is_null($line)) {
                    fwrite(STDERR, sprintf("  in line: %d\n", $line));
                }
            }
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
                        if ($token != self::T_WHITESPACE) {
                            if (!in_array($token, self::$rules[$last_token])) {
                                $this->error('possible parse error: "unexpected token"', $name, $line);
                        
                                return false;
                            }
                        
                            $last_token = $token;
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
                        case self::T_VARIABLE:
                            $var = substr($match[1], 1);
                            $val = (array_key_exists($var, $this->variables)
                                    ? $this->variables[$var]
                                    : null);
                    
                            if ($key != '') {
                                $args[$key] = $val;
                                $key = '';
                            } else {
                                $args[] = $val;
                            }
                            break;
                        case self::T_END:
                            $block = (substr($match[1], -1) == '{');
                            break 3;
                        }

                        $snippet = substr($snippet, strlen($match[0]));
                        continue 2;
                    }                
                }

                $this->error('possible parse error', $name, $line);
            
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
                $row    = fgets($fp);
                $offset = 0;
                ++$line;
            
                while (preg_match('/((?<!\\\\)\{[a-z]+\(.+)/', $row, $m, PREG_OFFSET_CAPTURE, $offset)) {
                    // possibly a preprocessor command
                    $offset = $m[1][1];

                    if (!(list($command, $args, $inc, $block) = $this->parse($m[1][0], $fp, $input, $line))) {
                        // no command, increase offset by one and parse row again to find possible other commands
                        ++$offset;
                        continue;
                    } elseif (!list($plugin, $type) = $this->getPlugin($command)) {
                        // command not registered
                        $this->error(sprintf("unknown command or command not callable '%s'\n", $command), $input, $line);
            
                        $return = false;
            
                        break 2;
                    } elseif ($block != ($type == plugin::T_FILTER || $type == plugin::T_CONDITION)) {
                        // wrong command type
                        if ($block) {
                            $this->error('FILTER or CONDITION required', $input, $line);
                        } else {
                            $this->error('FUNCTION required', $input, $line);
                        }
                    
                        $return = false;
                    
                        break 2;
                    } elseif ($block) {
                        // filter or condition command
                        $block_end = false;
                        
                        fwrite(STDOUT, substr($row, 0, $offset));

                        if ($type == plugin::T_CONDITION) {
                            $tmp = $plugin($args);
                            
                            if (!feof($fp)) {
                                $row = fgets($fp);

                                do {
                                    if (!($cont = !feof($fp)) || preg_match('/^\}\}/', ltrim($next = fgets($fp)))) {
                                        $row  = rtrim($row);
                                        $next = ($cont ? substr($next, 2) : '');
                                        $cont = false;
                                    }
                                    
                                    if ($tmp) fwrite(STDOUT, $row);
                                    $row = $next;
                                } while ($cont);
                            }
                        } elseif (($filter = $plugin($args))) {
                            $post = '';
                            
                            while (!feof($fp)) {
                                $row = fgets($fp);
                            
                                if (preg_match('/^\}\}/', ltrim($row))) {
                                    $post = substr($row, 2);
                                    break;
                                }
                            
                                if (!$filter->write($row)) {
                                    $this->error('unable to write to filter', $input, $line);
                                
                                    break;
                                }
                            }
                
                            if (($row = $filter->read())) {
                                while ($row !== false) {
                                    if (!($next = $filter->read())) {
                                        $row = rtrim($row);
                                    }
                                    
                                    fwrite(STDOUT, $row);
                                    $row = $next;
                                }
                            }
                        
                            unset($filter);
                            
                            $row = $post;
                        } else {
                            break 2;
                        }
                        
                        if (preg_match('/^\}\}/', ltrim($row))) $row = substr($row, 3);
                    } else {
                        // function command
                        $tmp = $plugin($args);
                        $row = substr_replace($row, $tmp, $offset, $inc);

                        $offset += strlen($tmp);
                    }
                }
            
                fwrite(STDOUT, $row);
            }
        
            fclose($fp);
        
            return $return;
        }
    }

    // add default plugin path
    preprocessor::addPluginPath(__DIR__ . '/plugins');
}
