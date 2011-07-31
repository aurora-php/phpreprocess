<?php

/**
 * write plugin for phpreprocess.
 *
 * @octdoc      c:plugin/write
 * @copyright   copyright (c) 2011 by Harald Lapp
 * @author      Harald Lapp <harald@octris.org>
 */
class write extends plugin
/**/
{
    /**
     * Plugin type.
     *
     * @octdoc  v:write/$type
     * @var     int
     */
    protected static $type = self::T_FUNCTION;
    /**/

    /**
     * Call object instance as function.
     *
     * @octdoc  m:write/__invoke
     * @param   array           $args               Arguments.
     */
    public function __invoke(array $args)
    /**/
    {
        return $args['output'];
    }
}
