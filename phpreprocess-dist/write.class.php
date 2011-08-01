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
}
