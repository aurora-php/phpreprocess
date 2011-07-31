<?php

/**
 * This plugins purpose is to insert another document into 
 * the current one.
 *
 * @octdoc      c:plugins/insert
 * @copyright   copyright (c) 2011 by Harald Lapp
 * @author      Harald Lapp <harald@octris.org>
 */
class insert extends plugin
/**/
{
    /**
     * Plugin type.
     *
     * @octdoc  v:insert/$type
     * @var     int
     */
    protected static $type = self::T_FUNCTION;
    /**/

    /**
     * Call object instance as function.
     *
     * @octdoc  m:insert/__invoke
     * @param   array           $args               Arguments.
     */
    public function __invoke(array $args)
    /**/
    {
        if ($return = (is_file($args['name']) && is_readable($args['name']))) {
            $return = $this->preprocessor->process($args['name']);
        } else {
            $preprocessor->error(sprintf('file not found or not readable "%s"', $args['name']));
        }
        
        return $return;
    }
}
