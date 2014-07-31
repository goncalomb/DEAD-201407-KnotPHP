<?php

/* KnotPHP by goncalomb <me@goncalomb.com> */

function knot_internal_utility_page($title, $html='', $http_code=200) {
	header_remove();
	http_response_code($http_code);
	header('Content-Type: text/html; charset=utf-8', true);

	echo "<!DOCTYPE html>\n";
	echo '<html>';
	echo '<head>';
	echo '<meta charset="UTF-8">';
	echo '<style>';
	echo 'body { margin: 20px auto; width: 700px; font-family: "Helvetica Neue",Helvetica,Arial,sans-serif; font-size: 14px; line-height: 1.5; text-align: center; color: #333333; } ';
	echo 'p, pre { font-family: "Courier New", monospace; } ';
	echo 'pre { padding: 10px; text-align: left; white-space: pre-wrap; background-color: #ebebeb; border: 1px solid #cccccc; border-radius: 3px; }';
	echo '</style>';
	echo '<title>', $title, '</title>';
	echo '</head>';
	echo '<body>';
	echo '<h1>', $title, '</h1>';
	echo $html;
	// echo '<p><strong>KnotPHP', (defined('KNOT_VERSION') ? ' ' . KNOT_VERSION : ''), '</strong></p>';
	echo '</body>';
	echo '</html>';
	echo "\n";
	exit();
}

?>
