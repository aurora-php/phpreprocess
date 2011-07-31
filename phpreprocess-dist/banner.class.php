<?php

/**
 * Banner plugin for phpreprocess.
 *
 * @octdoc      c:plugin/banner
 * @copyright   copyright (c) 2011 by Harald Lapp
 * @author      Harald Lapp <harald@octris.org>
 */
class banner extends plugin
/**/
{
    /**
     * Plugin type.
     *
     * @octdoc  v:banner/$type
     * @var     int
     */
    protected static $type = self::T_FILTER;
    /**/

    /**
     * Call object instance as function.
     *
     * @octdoc  m:banner/__invoke
     * @param   array           $args               Arguments.
     * @return  pipe                                Instance of a pipe.
     */
    public function __invoke(array $args)
    /**/
    {
        $pipe = new pipe('banner');
        
        return $pipe;
    }
}
