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
    require_once(__DIR__ . '/stdlib.class.php');
    require_once(__DIR__ . '/preprocessor.class.php');
    require_once(__DIR__ . '/plugin.class.php');
    require_once(__DIR__ . '/pipe.class.php');

    /**
     * Main application class for phpreprocess.
     *
     * @octdoc      c:libs/main
     * @copyright   copyright (c) 2011 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    class main
    /**/
    {
        /**
         * Constructor.
         *
         * @octdoc  m:main/__construct
         */
        public function __construct()
        /**/
        {
        }

        /**
         * Execute application.
         *
         * @octdoc  m:main/run
         */
        public function run()
        /**/
        {
            // parse command-line arguments
            $missing = array();
            $options = stdlib::getOptions(array(
                'p' => stdlib::T_OPT_OPTIONAL | stdlib::T_OPT_NTIMES,
                'i' => stdlib::T_OPT_REQUIRED
            ), $missing);

            if (count($missing)) {
                die("./usage ...\n");
            }

            if ($options['i'] == '-') {
                $inp = STDIN; 
            } elseif (!is_file($options['i']) || !is_readable($options['i'])) {
                die("no file nor STDIN or file not readable\n");
            } else {
                $inp = $options['i'];
            }

            $params = array();
            $vars   = array();
            $tmp    = (isset($options['p'])
                        ? (!is_array($options['p'])
                            ? array($options['p'])
                            : $options['p'])
                        : array());

            array_walk($tmp, function(&$v) use (&$params, &$vars) {
                if (preg_match('/^(?:(?P<plugin>[a-z]+)\.)?(?P<name>[a-z_0-9]+)=(?P<value>[^ ]+)$/', $v, $m)) {
                    extract($m);

                    if ($plugin != '') {
                        // plugin default
                        if (!isset($params[$plugin])) $params[$plugin] = array();

                        $params[$plugin][$name] = $value;
                    } else {
                        // variable
                        $vars[$name] = $value;
                    }
                }
            });

            // setup and process document
            $info = posix_getpwuid(posix_getuid());
            preprocessor::addPluginPath($info['dir'] . '/.phpreprocess');
            preprocessor::setPluginDefaults($params);

            $p = new preprocessor();
            $p->setVars($vars);
            $p->process($inp);
        }
    }
}
