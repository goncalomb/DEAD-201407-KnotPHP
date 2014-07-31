<?php

/* KnotPHP by goncalomb <me@goncalomb.com> */

if (!defined('KNOT_VERSION')) {
	ob_start();

	$knot_dir = realpath(__DIR__ . '/..');
	$knot_include_dir = $knot_dir . '/knot-include';
	$knot_content_dir = $knot_dir . '/knot-content';

	require $knot_include_dir . '/functions.php';
	require $knot_include_dir . '/functions-internal.php';

	$data = file_get_contents($knot_include_dir . '/defaults/default.htaccess');
	file_put_contents($knot_dir . '/.htaccess', str_replace('{KNOT_ROOT_DIR}', $knot_dir, $data));

	$data = file_get_contents($knot_include_dir . '/defaults/default.php.ini');
	file_put_contents($knot_content_dir . '/php.ini', str_replace('{KNOT_ROOT_DIR}', $knot_dir, $data));

	if (!error_get_last()) {
		echo '<p>The .htaccess file has been configured.</p>';
		echo '<script>setTimeout(function() { window.location.reload(); }, 2000);</script>';
	}

	knot_internal_utility_page('KnotPHP Setup', ob_get_clean());
	exit();
}

if (isset($_SERVER['REDIRECT_STATUS']) && $_SERVER['REDIRECT_STATUS'] != 200 && $_SERVER['REDIRECT_STATUS'] != 404) {
	KnotErrorHandling::httpError($_SERVER['REDIRECT_STATUS']);
}

if (KNOT_REQUEST_URI == '/robots.txt') {
	http_response_code(200);
	header('Content-Type: text/plain; charset=utf-8', true);
	echo "User-agent: *\nAllow: /\n";
	exit();
}

if (KNOT_REQUEST_URI == '/divide-by-zero') {
	1/0;
}

$new_uri = rtrim(KNOT_REQUEST_URI, '/');
if ($new_uri && $new_uri != KNOT_REQUEST_URI) {
	header_remove();
	header('Location: ' . $new_uri . KNOT_REQUEST_QUERY, true, 301);
	exit();
}
unset($new_uri);

Knot::internal_handleRequest();

if (KNOT_REQUEST_URI != '' && KNOT_REQUEST_URI != '/' && KNOT_REQUEST_URI != '/index.php') {
	KnotErrorHandling::httpError(404);
}

?>
