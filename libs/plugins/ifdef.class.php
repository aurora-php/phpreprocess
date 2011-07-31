<?php

/**
 * Ifdef condition plugin.
 *
 * @octdoc      c:plugins/ifdef
 * @copyright   copyright (c) 2011 by Harald Lapp
 * @author      Harald Lapp <harald@octris.org>
 */
class ifdef extends plugin
/**/
{
    /**
     * Plugin type.
     *
     * @octdoc  v:ifdef/$type
     * @var     int
     */
    protected static $type = self::T_CONDITION;
    /**/

    /**
     * Call object instance as function.
     *
     * @octdoc  m:ifdef/__invoke
     * @param   array           $args               Arguments.
     */
    public function __invoke(array $args)
    /**/
    {
        return (!!$args['test']);
    }
}
