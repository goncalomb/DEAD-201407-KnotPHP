<?php

if (isset($_POST['password'])) {
	Knot::adminLogin($_POST['password']);
} else if (isset($_GET['logout'])) {
	Knot::adminLogout();
	header('Location: /');
	exit();
}

if (Knot::isAdmin()) {
	if (isset($_GET['clear-cache'])) {
		Knot::adminLogout();
		KnotCache::clear();
		header('Location: ' . KNOT_REQUEST_URI);
		exit();
	}
}

KnotPage::start('default-admin');
KnotPage::title('Knot Admin Panel');

if (!Knot::isAdmin()) {
	echo '<form class="form-inline" method="POST">';
	echo '<input class="form-control" style="width: 300px; margin-right: 10px;" type="password" name="password" placeholder="Password">';
	echo '<button class="btn btn-default" type="submit">OK</button>';
	echo '</form>';
	echo '<script>document.getElementsByName("password")[0].focus();</script>';
	exit();
}

echo '<h2>Cache</h2>';
echo '<p><a class="btn btn-primary" href="?clear-cache">Clear Cache</a></p>';

?>
