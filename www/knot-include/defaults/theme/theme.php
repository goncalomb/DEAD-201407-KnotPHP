<?php

KnotPage::stylesheetFile('//cdn.jsdelivr.net/bootstrap/3.2.0/css/bootstrap.min.css');
KnotPage::append('head', '<style>#wrap { width: 900px; margin: 10px auto; } #main { padding: 0 20px; }</style>');

echo '<div id="wrap" class="container">';

echo '<div class="jumbotron">';
echo '<h1>KnotPHP <small>', KNOT_VERSION, '</small></h1>';
echo '<p>Default theme!</p>';
echo '</div>';

$main = new HtmlElement('div');
$main->attribute('id', 'main');

KnotPage::zone('main', $main);
KnotPage::append('body', $main);

echo '</div>';

?>
