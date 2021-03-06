<?php

if (!Knot::isAdmin()) {
	KnotErrorHandling::httpError(403);
}

KnotPage::start('default-admin');
KnotPage::title('Files - Knot Admin');

$path = (isset($_GET['path']) ? $_GET['path'] : KNOT_CONTENT_DIR);
$path = realpath($path);

$path_dir = null;
if ($path && is_dir($path)) {
	$path_dir = $path;
} else if ($path && is_file($path)) {
	$path_dir = dirname($path);
} else {
	echo '<h2>Files</h2>';
	knot_html_bootstrap_alert('Path not found!');
	exit();
}

if (!knot_starts_with($path_dir, $_SERVER['DOCUMENT_ROOT'])) {
	echo '<h2>Files</h2>';
	knot_html_bootstrap_alert('Cannot leave website root!');
	exit();
}

if (is_file($path) && isset($_POST['data'])) {
	file_put_contents($path, $_POST['data']);
}

echo '<h2>Files <small>', htmlentities($path), '</small></h2>';

echo '<div class="col-sm-3">';
echo '<h4>';
echo '<a href="?path=', urlencode(dirname($path_dir)), '"><i class="fa fa-arrow-up fa-fw"></i></a> ';
$name = basename($path_dir);
if (!$name) {
	$name = '/';
}
echo '<a href="?path=', urlencode($path_dir), '">', htmlentities($name), '</a>';
echo '</h4>';

echo '<ul class="fa-ul">';
$handle = opendir($path_dir);
while ($entry = readdir($handle)) {
	if ($entry != '.' && $entry != '..') {
		$entry_path = $path_dir . DIRECTORY_SEPARATOR . $entry;
		if (is_dir($entry_path)) {
			echo '<li><i class="fa-li fa fa-folder-o"></i> ';
		} else if (is_file($entry_path)) {
			echo '<li><i class="fa-li fa fa-file-o"></i> ';
		} else {
			continue;
		}
		echo '<a href="?path=', urlencode($entry_path), '">', htmlentities($entry), '</a></li>';
	}
}
closedir($handle);
echo '</ul>';

echo '</div>';

if (is_file($path)) {
	echo '<div class="col-sm-9">';
	echo '<h4>Edit File <small>', htmlentities(basename($path)), '</small></h4>';
	if (isset($_POST['data'])) {
		knot_html_bootstrap_alert('File saved.', 'success', true);
	}
	if (filesize($path) > 512*1024) {
		knot_html_bootstrap_alert('Cannot edit files larger than 512KB!');
	} else if (!knot_is_text_file($path)) {
		knot_html_bootstrap_alert('Not a text file!');
	} else {
		echo '<form method="POST">';
		echo '<div class="form-group">';
		echo '<textarea id="content" class="form-control" style="height: 430px; resize: vertical;" name="data">';
		KnotPage::appendRaw('main', "\n", htmlentities(file_get_contents($path)));
		echo '</textarea>';
		echo '</div>';
		echo '<button type="submit" class="btn btn-default">Save</button>';
		echo '</form>';
	}
	echo '</div>';
}

?>
