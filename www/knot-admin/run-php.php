<?php

if (!Knot::isAdmin()) {
	KnotErrorHandling::httpError(403);
}

KnotPage::start('default-admin');
KnotPage::title('Run PHP - Knot Admin');

echo '<h2>Run PHP</h2>';
knot_html_bootstrap_alert('<strong>This will run any PHP code!</strong> Use with care.');

$code = (isset($_POST['code']) ? $_POST['code'] : '');
if ($code) {
	KnotPage::replaceChars(false);
	echo '<pre>';
	eval($code);
	echo '</pre>';
	KnotPage::replaceChars(true);
}

?>

<form method="POST">
	<div class="form-group">
		<textarea class="form-control" style="height: 300px; resize: vertical;" name="code" spellcheck="false">
		<?php echo knot_html_entities($code); ?>
		</textarea>
	</div>
	<button class="btn btn-primary" type="submit">Run PHP</button>
</form>
<script>document.getElementsByName("code")[0].focus();</script>
