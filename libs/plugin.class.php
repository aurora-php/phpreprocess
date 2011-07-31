<?php

/**
 * Plugin core class. Provides functionality useful for plugins.
 *
 * @octdoc      c:libs/plugin
 * @copyright   copyright (c) 2011 by Harald Lapp
 * @author      Harald Lapp <harald@octris.org>
 */
abstract class plugin
/**/
{
    /**
     * Plugin types.
     * 
     * @octdoc  d:plugin/T_UNDEFINED, T_FUNCTION, T_FILTER, T_CONDITION
     */
    const T_UNDEFINED = 1;
    const T_FUNCTION  = 2;
    const T_FILTER    = 3;
    const T_CONDITION = 4;
    /**/
    
    /**
     * Plugin type.
     *
     * @octdoc  v:plugin/$type
     * @var     int
     */
    protected static $type = self::T_UNDEFINED;
    /**/
    
    /**
     * Preprocessor instance.
     *
     * @octdoc  v:plugin/$preprocessor
     * @var     preprocessor
     */
    private $preprocessor = null;
    /**/
    
    /**
     * Plugin defaults.
     *
     * @octdoc  v:plugin/$defaults
     * @var     array
     */
    protected $defaults = array();
    /**/
    
    /**
     * Constructor.
     *
     * @octdoc  m:command/__construct
     * @param   preprocessor    $prepro         Preprocessor instance.
     * @param   array           $defaults       Plugin defaults.
     */
    public function __construct(preprocessor $prepro, array $defaults)
    /**/
    {
        $this->preprocessor = $prepro;
        $this->defaults     = $defaults;
    }
    
    /**
     * Invoke must be implemented by sub-class.
     *
     * @octdoc  m:plugin/__invoke
     * @param   array           $args           Arguments for plugin.
     * @return  mixed                           Return value.
     */
    abstract public function __invoke(array $args);
    /**/
    
    /**
     * Return type of plugin.
     *
     * @octdoc  m:plugin/getType
     * @return  int                             Type of plugin.
     */
    public static function getType()
    /**/
    {
        return static::$type;
    }
}
