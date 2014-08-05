<?php

define('KNOT_MAX_ID', 2147483647);
define('KNOT_MAX_ID_5CHARS', 916132831);

function knot_id_to_base62($id) {
	$id = (int) $id;
	if ($id <= 0 || $id > KNOT_MAX_ID) {
		return null;
	}
	$str = '';
	do {
		$v = $id%62;
		$id = floor($id/62);
		if ($v >= 0 && $v <= 9) {
			$c = chr(48 + $v);
		} else if ($v >= 10 && $v <= 35) {
			$c = chr($v - 10 + 97);
		} else if ($v >= 36 && $v <= 61) {
			$c = chr($v - 36 + 65);
		}
		$str = $c . $str;
	} while ($id > 0);
	return str_pad($str, 5, '0', STR_PAD_LEFT);
}

function knot_id_from_base62($str) {
	$str = (string) $str;
	$l = strlen($str);
	if ($l != 5 && $l != 6)  {
		return 0;
	}
	$powers = array(1, 62, 3844, 238328, 14776336, 916132832);
	$id = 0;
	for ($i = 0; $i < $l; ++$i) {
		$o = ord($str[$l - $i - 1]);
		if ($o >= 48 && $o <= 57) {
			$v = $o - 48;
		} else if ($o >= 65 && $o <= 90) {
			$v = 36 + $o - 65;
		} else if ($o >= 97 && $o <= 122) {
			$v = 10 + $o - 97;
		} else {
			return 0;
		}
		$id += $v * $powers[$i];
		if ($id > KNOT_MAX_ID) {
			return 0;
		}
	}
	return $id;
}

function knot_id_parse($id) {
	if (is_string($id)) {
		$other = knot_id_from_base62($id);
		if ($other) {
			return array($other, $id);
		}
	} else if (is_int($id)) {
		$other = knot_id_to_base62($id);
		if ($other) {
			return array($id, $other);
		}
	}
	return array(0, null);
}

function knot_id_random($full=false) {
	do {
		$id = 0;
		for ($i = 0; $i < 4; ++$i) {
			$id = $id | ((mt_rand() & 255) << ($i * 8));
		}
	} while ($id <= 0 || (!$full && $id > KNOT_MAX_ID_5CHARS));
	return $id;
}

function knot_id_random_unique($full=false) {
	$stmt = Knot::getDatabase()->prepare('SELECT `ObjectId` FROM `objects` WHERE `ObjectId`=?');
	$stmt->bind_param('i', $id);
	do {
		$id = knot_id_random();
		$stmt->execute();
		$stmt->store_result();
	} while ($stmt->num_rows > 0);
	return $id;
}

?>
