<?php

/**
 * Ifndef condition plugin.
 *
 * @octdoc      c:plugins/ifndef
 * @copyright   copyright (c) 2011 by Harald Lapp
 * @author      Harald Lapp <harald@octris.org>
 */
class ifndef extends ifdef
/**/
{
    /**
     * Plugin type.
     *
     * @octdoc  v:ifndef/$type
     * @var     int
     */
    protected static $type = self::T_CONDITION;
    /**/

    /**
     * Call object instance as function.
     *
     * @octdoc  m:ifndef/__invoke
     * @param   array           $args               Arguments.
     */
    public function __invoke(array $args)
    /**/
    {
        return !parent::__invoke($args);
    }
}
