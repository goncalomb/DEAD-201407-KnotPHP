<?php

/* KnotPHP by goncalomb <me@goncalomb.com> */

define('KNOT_MICROTIME', (isset($_SERVER["REQUEST_TIME_FLOAT"]) ? (float) $_SERVER["REQUEST_TIME_FLOAT"] : microtime(true)));
define('KNOT_TIME', floor(KNOT_MICROTIME));

define('KNOT_ROOT_DIR', realpath(__DIR__ . DIRECTORY_SEPARATOR . '..'));
define('KNOT_INCLUDE_DIR', KNOT_ROOT_DIR . DIRECTORY_SEPARATOR . 'knot-include');
define('KNOT_CONTENT_DIR', KNOT_ROOT_DIR . DIRECTORY_SEPARATOR . 'knot-content');
define('KNOT_TMP_DIR', KNOT_ROOT_DIR . DIRECTORY_SEPARATOR . 'knot-tmp');

define('KNOT_VERSION', 'alpha');
define('KNOT_DEBUG', $_SERVER['HTTP_HOST'] == 'localhost');

$i = strpos($_SERVER['REQUEST_URI'], '?');
define('KNOT_REQUEST_URI', urldecode($i === false ? $_SERVER['REQUEST_URI'] : substr($_SERVER['REQUEST_URI'], 0, $i)));
unset($i);

set_include_path(KNOT_ROOT_DIR);

spl_autoload_register(function($class_name) {
	$file = KNOT_INCLUDE_DIR . '/classes/class.' . $class_name . '.php';
	if (file_exists($file)) {
		require $file;
	}
});

require 'knot-include/functions.php';
require 'knot-include/functions_internal.php';

knot_forbidden_path(KNOT_INCLUDE_DIR);
knot_forbidden_path(KNOT_CONTENT_DIR);

knot_ensure_path(KNOT_TMP_DIR);
knot_forbidden_path(KNOT_TMP_DIR);

header_remove();

set_error_handler(array('KnotErrorHandling', 'internal_handleError'));
register_shutdown_function(array('KnotErrorHandling', 'internal_handleShutdown'));
error_reporting(~E_ALL);
ini_set('display_errors', 0);

Knot::internal_initialize();

?>
