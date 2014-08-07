<?php

class KnotTheme {

	private static $_themes = array();

	private $_name;
	private $_folder;

	public static function get($name, $fallback_to_default=false) {
		if (isset(self::$_themes[$name])) {
			return self::$_themes[$name];
		}
		if ($name == 'default') {
			$folder = KNOT_INCLUDE_DIR . '/defaults/theme';
		} else if ($name == 'default-admin') {
			$folder = KNOT_INCLUDE_DIR . '/defaults/theme-admin';
		} else {
			$folder = KNOT_THEMES_DIR . '/' . $name;
			if (!is_dir($folder)) {
				return ($fallback_to_default && $name != 'default' ? self::get('default') : null);
			}
		}
		$theme = new self($name, $folder);
		self::$_themes[$name] = $theme;
		return $theme;
	}

	private function __construct($name, $folder) {
		$this->_name = $name;
		$this->_folder = $folder;
	}

	public function includeFile($file, $fallback_to_default=false) {
		$path = $this->_folder . '/' . $file;
		if (is_file($path)) {
			knot_require_file($path);
			return true;
		} else if ($fallback_to_default && $this->_name != 'default') {
			return self::get('default')->includeFile($file);
		} else {
			user_error("Theme file not found ($path).", E_USER_ERROR);
		}
		return false;
	}

}

?>
