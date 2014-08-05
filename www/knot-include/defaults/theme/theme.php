<?php

KnotPage::stylesheetFile('//cdn.jsdelivr.net/bootstrap/3.2.0/css/bootstrap.min.css');

echo '<div class="container">';

echo '<div class="page-header">';
echo '<h1>KnotPHP <small>', KNOT_VERSION, '</small></h1>';
echo '<p><em>Default theme!</em></p>';
echo '</div>';

$main = new HtmlElement('div');

KnotPage::zone('main', $main);
KnotPage::append('body', $main);

echo '</div>';

?>
