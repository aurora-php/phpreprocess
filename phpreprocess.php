#!/usr/bin/env php
<?php

/**
 * Generic preprocessor
 *
 * @octdoc      h:./phpreprocess
 * @copyright   copyright (c) 2011 by Harald Lapp
 * @author      Harald Lapp <harald.lapp@gmail.com>
 */
/**/

require_once(__DIR__ . '/libs/stdlib.class.php');
require_once(__DIR__ . '/libs/preprocessor.class.php');

// load configuration file
$info     = posix_getpwuid(posix_getuid());
$cfg_file = $info['dir'] . '/.phpreprocess.ini';
$cfg      = array();

if (is_file($cfg_file) && is_readable($cfg_file)) {
    $cfg = parse_ini_file($cfg_file, true);
}

print_r($cfg);

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

// setup and process document
$p = new preprocessor();
$p->process($inp);
