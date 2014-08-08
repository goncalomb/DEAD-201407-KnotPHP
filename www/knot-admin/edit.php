<?php

if (!Knot::isAdmin()) {
	KnotErrorHandling::httpError(403);
}

$id = (empty($_GET['id']) ? 0 : (int) $_GET['id']);

if (!empty($_GET['create'])) {
	if ($_GET['create'] == 'Page') {
		$object = Page::create($id);
	}
	header('Location: edit.php?id=' . $object->id());
	exit();
}

KnotPage::start('default-admin');
KnotPage::title('Edit - Knot Admin');

$object = Object::getById($id);
if (!$object) {
	echo '<h2>Edit</h2>';
	knot_html_bootstrap_alert('Object not found!');
	exit();
}

echo '<h2>Edit <small>', get_class($object), ' (', $object->id(true), ')</small></h2>';

if ($object && isset($_POST['submit'])) {
	if (isset($_POST['slug'])) {
		$object->slug($_POST['slug']);
	}
	if (isset($_POST['title'])) {
		$object->title($_POST['title']);
	}
	if (isset($_POST['content'])) {
		$object->content($_POST['content']);
	}
	$object->save();
	knot_html_bootstrap_alert(get_class($object) . ' saved.', 'success', true);
}

$slug = $title = $content = '';
if ($object) {
	$slug = $object->slug();
	$title = $object->title();
	$content = $object->content();
}

?>

<form class="form-horizontal" method="POST" role="form">
	<div class="form-group">
		<label class="col-sm-1 control-label" for="slug">Slug</label>
		<div class="col-sm-5">
			<input id="slug" class="form-control" type="text" name="slug" value="<?php echo $slug; ?>">
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-1 control-label" for="title">Title</label>
		<div class="col-sm-11">
			<input id="title" class="form-control input-lg" type="text" name="title" value="<?php echo htmlentities($title); ?>">
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-1 control-label" for="content">
			Content
			<button class="btn btn-default btn-tinymce" type="button" title="Toggle editor"><i class="fa fa-bars fa-fw"></i></button>
		</label>
		<div class="col-sm-11">
			<textarea id="content" class="form-control" style="height: 200px; resize: vertical;" name="content">
			<?php KnotPage::appendRaw('main', "\n", htmlentities($content)); ?>
			</textarea>
		</div>
	</div>
	<div class="form-group">
		<div class="col-sm-offset-1 col-sm-11">
			<button type="submit" class="btn btn-default" name="submit">Submit</button>
		</div>
	</div>
</form>
