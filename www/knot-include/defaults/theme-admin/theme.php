<?php

KnotPage::stylesheetFile('//cdn.jsdelivr.net/bootstrap/3.2.0/css/bootstrap.min.css');
KnotPage::stylesheetFile('//cdn.jsdelivr.net/fontawesome/4.1.0/css/font-awesome.min.css');
KnotPage::stylesheetFile(KnotPage::theme()->publicUrl('style.css'));

$nav_bar_link = function($href, $html) {
	echo '<li';
	if ($href == KNOT_REQUEST_URI) {
		echo ' class="active"';
	}
	echo '><a href="', $href, '">', $html, '</a></li>';
}

?>

<nav class="navbar navbar-default" role="navigation">
	<div class="container">
		<div class="navbar-header">
			<a class="navbar-brand" href="/knot-admin/">Knot Admin <small><em><?php echo KNOT_VERSION; ?></em></small></a>
		</div>
		<?php if (Knot::isAdmin()) { ?>
		<ul class="nav navbar-nav">
			<?php $nav_bar_link('/knot-admin/pages.php', '<i class="fa fa-file-text-o"></i> Pages'); ?>
			<?php $nav_bar_link('/knot-admin/files.php', '<i class="fa fa-file-code-o"></i> Files'); ?>
			<?php $nav_bar_link('/knot-admin/run-php.php', '<i class="fa fa-code"></i> Run PHP'); ?>
		</ul>
		<ul class="nav navbar-nav navbar-right">
			<li><a href="/knot-admin/?logout"><i class="fa fa-sign-out"></i> Logout</a></li>
		</ul>
		<?php } ?>
	</div>
</nav>

<div class="container">
	<div class="row">

<?php

$main = new HtmlElement('div');
$main->attribute('id', 'main');
$main->attribute('class', 'col-sm-12');

KnotPage::zone('main', $main);
KnotPage::append('body', $main);

?>

	</div>
</div>
