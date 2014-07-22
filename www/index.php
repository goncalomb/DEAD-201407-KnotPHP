<?php

// Don't change this!
require 'knot-include/core.php';

$page = new HtmlPage();
$page->title('KnotPHP');

$page->head()->append('<style>body { margin: 10px 30px; font-family: "Helvetica Neue",Helvetica,Arial,sans-serif; }</style>');
$page->body()->append('<h1>It works!</h1>');

$page->output();

?>
