<?php

if (!empty($_GET['delete-id'])) {
	$page = Page::getById((int) $_GET['delete-id']);
	if ($page) {
		$page->delete();
	}
	header('Location: ' . KNOT_REQUEST_URI);
	exit();
}

if (!Knot::isAdmin()) {
	KnotErrorHandling::httpError(403);
}

KnotPage::start('default-admin');
KnotPage::title('Pages - Knot Admin');

echo '<h2>Pages</h2>';

$index = Page::getById(1);

$pages = array();
$page = (empty($_GET['id']) ? $index : Page::getById((int) $_GET['id']));
if ($page) {
	$pages = $page->childs();
} else {
	echo '<div class="alert alert-warning"><i class="fa fa-exclamation-circle"></i> Page not found!</div>';
	exit();
}

echo '<ol class="breadcrumb">';
$html_buffer = array();
$parent = $page;
while ($parent = $parent->parent()) {
	$html_buffer[] = '<li><a href="pages.php?id=' . $parent->id() . '">' . htmlentities($parent->title()) . '</a></li>';
}
foreach (array_reverse($html_buffer) as $html) {
	echo $html;
}
echo '<li>' . htmlentities($page->title()) . '</li>';
echo '</ol>';

echo '<p>';
if ($page->id() == 1) {
	echo '<a class="btn btn-default" href="edit.php?id=1"><i class="fa fa-pencil"></i> Edit Index (', htmlentities($page->title()), ')</a> ';
}
echo '<a class="btn btn-default" href="edit.php?create=Page&id=', ($page ? $page->id() : 1), '"><i class="fa fa-asterisk"></i> New Page</a>';
echo '</p>';

?>

<table class="table">
	<thead>
		<tr><th style="width: 100px;"></th><th style="width: 70px;">Id</th><th>Title</th><th>Slug</th><th>Order</th><th>Last Modified</th></tr>
	</thead>
	<tbody>
<?php

foreach ($pages as $page) {
	echo '<tr>';
	echo '<td><div class="btn-group btn-group-xs">';
	echo '<a class="btn btn-default" href="edit.php?id=', $page->id(), '"><i class="fa fa-pencil"></i> Edit</a>';
	echo '<a class="btn btn-default" href="pages.php?delete-id=', $page->id(), '"';
	echo ' onclick="return confirm(\'Delete &quot;', htmlentities($page->title()), '&quot;?\') && confirm(\'Are you sure?\');"';
	echo '><i class="fa fa-trash-o"></i></a>';
	echo '</div></td>';
	echo '<td>', $page->id(true), '</td>';
	echo '<td><a href="pages.php?id=', $page->id(), '">', htmlentities($page->title()), '</a></td>';
	echo '<td><a href="', $page->url(), '" target="_blank">', $page->slug(), '</a></td>';
	echo '<td>', $page->order(), '</td>';
	echo '<td>', $page->date('d/m/Y H:i:s'), '</td>';
	echo '</tr>';
}

?>
	</tbody>
</table>
