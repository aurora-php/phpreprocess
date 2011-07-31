<?php

/**
 * Execute a command using pipes.
 *
 * @octdoc      c:libs/pipe
 * @copyright   copyright (c) 2011 by Harald Lapp
 * @author      Harald Lapp <harald@octris.org>
 */
class pipe
/**/
{
    /**
     * Opened pipes.
     *
     * @octdoc  v:pipe/$pipes
     * @var     array
     */
    protected $pipes = array();
    /**/
    
    /**
     * Process resource handler.
     *
     * @octdoc  v:pipe/$process
     * @var     resource
     */
    protected $process = null;
    /**/
    
    /**
     * Constructor.
     *
     * @octdoc  m:pipe/__construct
     * @param   string          $command                Command to execute.
     */
    public function __construct($command)
    /**/
    {
        $descriptors = array(
           0 => array("pipe", "r"),     // stdin
           1 => array("pipe", "w"),     // stdout
           2 => array("pipe", "w"),     // stderr
        );
        $cwd = NULL;
        
        $this->process = proc_open($command, $descriptors, $this->pipes, $cwd);
    }
    
    /**
     * Destructor.
     *
     * @octdoc  m:pipe/__destruct
     */
    public function __destruct()
    /**/
    {
        $this->close();
    }
    
    /**
     * Write a row to STDIN of the executed pipe.
     *
     * @octdoc  m:pipe/write
     * @param   string      $row                Row to write.
     * @return  bool                            Returns false if writing failed.
     */
    public function write($row)
    /**/
    {
    
        if (($return = is_resource($this->process))) {
            fwrite($this->pipes[0], $row);
        }
        
        return $return;
    }
    
    /**
     * Read from STDOUT of executed pipe.
     *
     * @octdoc  m:pipe/read
     * @return  string                          Output of executed pipe.
     */
    public function read()
    /**/
    {
        if (!is_null($this->pipes[0])) {
            // close STDIN first
            fclose($this->pipes[0]);
            
            $this->pipes[0] = null;
        }
        
        $out = fgets($this->pipes[1]);
        
        return $out;
    }
    
    /**
     * Close resources.
     *
     * @octdoc  m:pipe/close
     */
    public function close()
    /**/
    {
        foreach ($this->pipes as $pipe) {
            if (!is_null($pipe)) fclose($pipe);
        }
        
        if (!is_null($this->process)) {
            proc_close($this->process);
            
            $this->pipes   = array();
            $this->process = null;
        }
    }
}
