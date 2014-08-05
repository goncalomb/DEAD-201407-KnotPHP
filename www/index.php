<?php

// Changing this to 'true' will allow for a custom index page.
// With 'false' the index page will be the one set on the database.
define('KNOT_CUSTOM_INDEX', false);

// DON'T CHANGE THIS!
require 'knot-include/core.php';

// Custom Index page starts here...
// It will only run if KNOT_CUSTOM_INDEX = true.

KnotPage::start();
KnotPage::title('Custom Index');

echo '<p>Index works!</p>';

?>
