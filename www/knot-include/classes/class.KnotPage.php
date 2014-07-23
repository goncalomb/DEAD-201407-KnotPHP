<?php

final class KnotPage {

	private static $_page;
	private static $_state;
	private static $_zones = array();

	public static function start($theme=true) {
		if (self::$_page) {
			return null;
		}

		if ($theme === true) {
			$theme = (string) Knot::config('theme', 'default');
		}
		$theme_folder = null;
		if (empty($theme)) {
			$theme = null;
		} else if ($theme == 'default') {
			$theme_folder = KNOT_INCLUDE_DIR . '/defaults/theme';
		} else {
			$theme_folder = KNOT_THEMES_DIR . '/' . $theme;
		}

		ob_start();
		self::$_page = new HtmlPage();
		self::$_zones['head'] = self::$_page->head();
		self::$_zones['body'] = self::$_page->body();
		knot_require_file("$theme_folder/theme.php");
		self::append('body', ob_get_clean());
		ob_start();

		self::$_state = false;

		register_shutdown_function(array(__CLASS__, 'end'));
		return self::$_page;
	}

	public static function zone($name, $element=null) {
		if ($element && self::$_page && self::$_state === null && $name != 'head' && $name != 'body') {
			self::$_zones[$name] = $element;
		}
	}

	public static function append($zone_name) {
		if (self::$_page && isset(self::$_zones[$zone_name])) {
			self::flushBuffer();
			call_user_func_array(array(self::$_zones[$zone_name], 'append'), array_slice(func_get_args(), 1));
		}
	}

	public static function flushBuffer() {
		if (self::$_page && ob_get_length()) {
			if (self::$_state === null) {
				self::append('body', ob_get_clean());
			} else {
				self::append('main', ob_get_clean());
			}
			ob_start();
		}
	}

	public static function metaTag($name, $content) {
		if (self::$_page) {
			self::$_page->metaTag($name, $content);
		}
	}

	public static function stylesheetFile($href) {
		if (self::$_page) {
			self::$_page->stylesheetFile($href);
		}
	}

	public static function scriptFile($src, $end=false) {
		if (self::$_page) {
			self::$_page->scriptFile($src, $end);
		}
	}

	public static function title($title) {
		if (self::$_page) {
			self::$_page->title($title);
		}
	}

	public static function get() {
		return self::$_page;
	}

	public static function end() {
		if (!self::$_page || self::$_state) {
			return;
		}
		self::append('main', ob_get_clean());
		self::$_page->output();
		self::$_state = true;
	}

	private function __construct() { }

}

?>
