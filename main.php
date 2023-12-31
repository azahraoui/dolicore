<?php

global $dolibase_config;

$__DIR__ = dirname(__FILE__);

// Dolibarr detection
if (!defined('DOL_VERSION')) die('DolibaseError: Dolibarr detection failed.');

// Load Dolibase functions
require_once $__DIR__ . '/src/lib/functions.php';

// Load Dolibase config
$dolibase_config = array_merge($dolibase_config, @include($__DIR__ . '/config.php'));

// Disable PHP warnings in production
if (! isset($dolibase_config['main']['env']) || $dolibase_config['main']['env'] == 'prod') {
	error_reporting(E_ERROR);
}
