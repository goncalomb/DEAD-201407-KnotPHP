<?php

$page = Knot::getObject();

echo '<h1>', $page->title(), '</h1>';

echo '<article>';
eval('?>' . $page->content());
echo '</article>';

?>
