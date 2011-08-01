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
     * Asciidia plugin for phpreprocess.
     *
     * @octdoc      c:plugin/asciidia
     * @copyright   copyright (c) 2011 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    class asciidia extends plugin
    /**/
    {
        /**
         * Plugin type.
         *
         * @octdoc  v:asciidia/$type
         * @var     int
         */
        protected static $type = self::T_FILTER;
        /**/

        /**
         * Call object instance as function.
         *
         * @octdoc  m:asciidia/__invoke
         * @param   array           $args               Arguments.
         * @return  pipe                                Instance of a pipe.
         */
        public function __invoke(array $args)
        /**/
        {
            $return = false;
            $dest   = (isset($this->defaults['destination'])
                        ? rtrim($this->defaults['destination'], '/')
                        : '');

            if ($dest == '') {
                $this->error(
                    'destination directory missing -- make sure to call phpreprocess with
                    the argument "-p asciidia.destination=..."'
                );
            } elseif (!is_dir($dest) || !is_writable($dest)) {
                $this->error('destination is no directory or directory is not writable');
            } elseif (!isset($args['type'])) {
                $this->error('"type" is a required parameter');
            } elseif (!isset($args['output'])) {
                $this->error('"output" is a required parameter');
            } else {
                $dest_file = $dest . '/' . basename($args['output'], '.png') . '.png';
                
                if (is_file($dest_file)) {
                    unlink($dest_file);
                }
                
                $return = new pipe(sprintf(
                    'asciidia -t %s -i - -o %s; echo %s',
                    escapeshellarg($args['type']),
                    escapeshellarg($dest_file),
                    escapeshellarg($dest_file)
                ));
            }
        
            return $return;
        }
    }
}
