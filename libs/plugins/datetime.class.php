<?php

/**
 * Datetime plugin.
 *
 * @octdoc      c:plugins/datetime
 * @copyright   copyright (c) 2011 by Harald Lapp
 * @author      Harald Lapp <harald@octris.org>
 */
class datetime extends plugin
/**/
{
    /**
     * Plugin type.
     *
     * @octdoc  v:datetime/$type
     * @var     int
     */
    protected static $type = self::T_FUNCTION;
    /**/

    /**
     * Call object instance as function.
     *
     * @octdoc  m:datetime/__invoke
     * @param   array           $args               Arguments.
     */
    public function __invoke(array $args)
    /**/
    {
        return strftime($args['format']);
    }
}
