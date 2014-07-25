<?php

KnotPage::stylesheetFile('//cdn.jsdelivr.net/bootstrap/3.2.0/css/bootstrap.min.css');
KnotPage::stylesheetFile('//cdn.jsdelivr.net/fontawesome/4.1.0/css/font-awesome.min.css');

KnotPage::append('head', '<style>.navbar-right { font-weight: bold; } #main > *:first-child { margin-top: 0; }</style>');

?>

<nav class="navbar navbar-default" role="navigation">
	<div class="container">
		<div class="navbar-header">
			<a class="navbar-brand" href="/knot-admin/">Knot Admin</a>
		</div>
		<?php if (Knot::isAdmin()) { ?>
		<ul class="nav navbar-nav">
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
