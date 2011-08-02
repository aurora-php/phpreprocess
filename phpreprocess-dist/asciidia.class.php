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
            $return     = false;
            $dst_path   = (isset($this->defaults['dst_path'])
                            ? rtrim($this->defaults['dst_path'], '/')
                            : '');
            $dst_format = (isset($this->defaults['dst_format'])
                            ? $this->defaults['dst_format'] . ':'
                            : '');
            $dst_scale  = (isset($this->defaults['dst_scale'])
                            ? $this->defaults['dst_scale']
                            : '');

            if ($dst_path == '') {
                $this->error(
                    'destination directory missing -- make sure to call phpreprocess with
                    the argument "-p asciidia.dst_path=..."'
                );
            } elseif (!is_dir($dst_path) || !is_writable($dst_path)) {
                $this->error('destination is no directory or directory is not writable');
            } elseif (!isset($args['type'])) {
                $this->error('"type" is a required parameter');
            } elseif (!isset($args['output'])) {
                $this->error('"output" is a required parameter');
            } elseif ($dst_scale != '' && !preg_match('/^(\d*x\d+|\d+x\d*)$/', $dst_scale)) {
                $this->error('wrong scaling parameter');
            } else {
                $dst_file = $dst_path . '/' . basename($args['output'], '.png') . '.png';
                
                if (is_file($dst_file)) {
                    unlink($dst_file);
                }
                
                $return = new pipe(sprintf(
                    'asciidia -t %s -i - -o %s %s; echo %s',
                    escapeshellarg($args['type']),
                    escapeshellarg($dst_format . $dst_file),
                    ($dst_scale != '' ? '-s ' . $dst_scale : ''),
                    escapeshellarg($dst_file)
                ));
            }
        
            return $return;
        }
    }
}
