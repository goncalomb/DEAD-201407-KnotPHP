<?php

final class KnotPage {

	private static $_page;
	private static $_themeFolder;
	private static $_state;
	private static $_zones = array();
	private static $_replaceChars = array("\t", "\r", "\n");

	public static function start($theme=true) {
		if (self::$_page) {
			return null;
		}

		if ($theme === true) {
			$theme = (string) Knot::config('theme', 'default');
		}
		if (empty($theme)) {
			$theme = null;
		} else if ($theme == 'default') {
			self::$_themeFolder = KNOT_INCLUDE_DIR . '/defaults/theme';
		} else if ($theme == 'default-admin') {
			self::$_themeFolder = KNOT_INCLUDE_DIR . '/defaults/theme-admin';
		} else {
			self::$_themeFolder = KNOT_THEMES_DIR . '/' . $theme;
		}

		ob_start();
		self::$_page = new HtmlPage();
		self::$_zones['head'] = self::$_page->head();
		self::$_zones['body'] = self::$_page->body();
		knot_require_file(self::$_themeFolder . '/theme.php');
		self::append('body', ob_get_clean());
		ob_start();

		self::$_state = false;

		register_shutdown_function(array(__CLASS__, 'end'));
		return self::$_page;
	}

	public static function themeFolder() {
		return self::$_themeFolder;
	}

	public static function zone($name, $element=null) {
		if ($element && self::$_page && self::$_state === null && $name != 'head' && $name != 'body') {
			self::$_zones[$name] = $element;
		}
	}

	public static function replaceChars($chars) {
		self::flushBuffer();
		if (empty($chars)) {
			self::$_replaceChars = null;
		} else if ($chars === true) {
			self::$_replaceChars = array("\t", "\r", "\n");
		} else {
			self::$_replaceChars = str_split($chars);
		}
	}

	public static function append($zone_name) {
		if (self::$_page && isset(self::$_zones[$zone_name])) {
			self::flushBuffer();
			$data = array_slice(func_get_args(), 1);
			if (self::$_replaceChars) {
				foreach ($data as &$value) {
					if (!($value instanceof HtmlElement)) {
						$value = str_replace(self::$_replaceChars, '', $value);
					}
				}
			}
			call_user_func_array(array(self::$_zones[$zone_name], 'append'), $data);
		}
	}

	public static function flushBuffer() {
		if (self::$_page && ob_get_length()) {
			if (self::$_state === null || !isset(self::$_zones['main'])) {
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

	public static function end($abort=false) {
		if (!self::$_page || self::$_state) {
			return;
		} else if ($abort) {
			ob_end_clean();
			self::$_page = null;
			self::$_state = true;
			return;
		}
		if (isset(self::$_zones['main'])) {
			self::append('main', ob_get_clean());
		} else {
			self::append('body', ob_get_clean());
		}
		self::$_page->output();
		$cache_stats = KnotCache::stats();
		echo '<!-- ';
		echo $cache_stats['hits'], ' hits, ';
		echo $cache_stats['misses'], ' misses, ';
		echo '~', floor((microtime(true) - KNOT_MICROTIME)*100000)/100, 'ms';
		echo " -->\n";
		self::$_state = true;
	}

	private function __construct() { }

}

?>
