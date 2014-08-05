<?php

$page = Knot::getObject();

KnotPage::title($page->title());

echo '<article>';
eval('?>' . $page->content());
echo '</article>';

?>
