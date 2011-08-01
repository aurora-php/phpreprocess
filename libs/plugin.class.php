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
