<?php

function knot_html_bootstrap_alert($message, $type='warning', $autoclose=false) {
	$type = (in_array($type, array('success', 'info', 'warning', 'danger')) ? $type : 'warning');
	echo '<div ';
	if ($autoclose) {
		$id = 'knot-alert-' . knot_random_id(4);
		echo 'id="', $id, '" ';
	}
	echo 'class="alert alert-', $type, '">';
	echo '<i class="fa fa-', ($type == 'success' || $type == 'info' ? 'info' : 'warning'), ' fa-fw fa-lg"></i> ';
	echo $message;
	echo '</div>';
	if ($autoclose) {
		echo '<script>setTimeout(function() { var e = document.getElementById("', $id, '"); e.parentNode.removeChild(e); }, 3000);</script>';
	}
}

?>
