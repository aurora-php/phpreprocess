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
require_once(__DIR__ . '/libs/plugin.class.php');
require_once(__DIR__ . '/libs/pipe.class.php');

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
$tmp    = (isset($options['p'])
            ? (!is_array($options['p'])
                ? array($options['p'])
                : $options['p'])
            : array());

array_walk($tmp, function(&$v) use (&$params) {
    if (preg_match('/^(?P<plugin>[a-z]+)\.(?P<name>[a-z]+)=(?P<value>[^ ]+)$/', $v, $m)) {
        extract($m);
        
        if (!isset($params[$plugin])) $params[$plugin] = array();
        
        $params[$plugin][$name] = $value;
    }
});

// setup and process document
$info = posix_getpwuid(posix_getuid());
preprocessor::addPluginPath($info['dir'] . '/.phpreprocess');
preprocessor::setPluginDefaults($params);

$p = new preprocessor();
$p->process($inp);
