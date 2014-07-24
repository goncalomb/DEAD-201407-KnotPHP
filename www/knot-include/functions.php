<?php

/* KnotPHP by goncalomb <me@goncalomb.com> */

function knot_ensure_path($path) {
	if (!file_exists($path)) {
		mkdir($path, 0755, true);
	}
}

function knot_forbidden_path($path) {
	if (!file_exists($path . '/.htaccess')) {
		file_put_contents($path . '/.htaccess', "Deny from all\n");
	}
}

function knot_require_file($file) {
	return require $file;
}

function knot_unlink_recursive($path) {
	if (is_dir($path)) {
		$handle = opendir($path);
		while ($entry = readdir($handle)) {
			if ($entry != '.' && $entry != '..') {
				knot_unlink_recursive("$path/$entry");
			}
		}
		closedir($handle);
		rmdir($path);
	} else {
		unlink($path);
	}
}

?>
