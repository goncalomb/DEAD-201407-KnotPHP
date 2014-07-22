<?php

final class Knot {

	private static $_config;

	public static function internal_initialize() {
		if (self::$_config === null) {
			$load_config = function() {
				$knot_config = array();
				require 'knot-content/config.php';
				return $knot_config;
			};
			self::$_config = $load_config();
		}
	}

	public static function config($key) {
		return (self::$_config !== null && isset(self::$_config[$key]) ? self::$_config[$key] : null);
	}

	private function __construct() { }

}

?>
