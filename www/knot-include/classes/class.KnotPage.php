<?php

final class KnotPage {

	private static $_page;
	private static $_theme;
	private static $_state;
	private static $_zones = array();
	private static $_replaceChars = array("\t", "\r", "\n");

	public static function theme($name=null) {
		if (self::$_page) {
			return self::$_theme;
		}
		if ($name === true || ($name === null && self::$_theme === null)) {
			self::$_theme = KnotTheme::get((string) Knot::config('theme'), true);
		} else if (!$name) {
			self::$_theme = false;
		} else {
			self::$_theme = KnotTheme::get($name, true);
		}
		return self::$_theme;
	}

	public static function start($theme=null) {
		if (self::$_page) {
			return null;
		}
		if (self::$_theme === null || $theme !== null) {
			self::theme($theme);
		}

		self::$_page = new HtmlPage();
		self::$_zones['head'] = self::$_page->head();
		self::$_zones['body'] = self::$_page->body();
		if (self::$_theme) {
			ob_start();
			self::$_theme->includeFile('theme.php');
			self::append('body', ob_get_clean());
		}
		ob_start();

		self::$_state = false;

		register_shutdown_function(array(__CLASS__, 'end'));
		return self::$_page;
	}

	public static function zone($name, $element=null) {
		if ($element && self::$_page && self::$_state === null && $name != 'head' && $name != 'body') {
			self::$_zones[$name] = $element;
		}
		return (isset(self::$_zones[$name]) ? self::$_zones[$name] : null);
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

	public static function appendRaw($zone_name) {
		if (self::$_page && isset(self::$_zones[$zone_name])) {
			self::flushBuffer();
			call_user_func_array(array(self::$_zones[$zone_name], 'append'), array_slice(func_get_args(), 1));
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
		echo Knot::queryCount(), ' queries, ';
		echo '~', floor((microtime(true) - KNOT_MICROTIME)*100000)/100, 'ms';
		echo " -->\n";
		self::$_state = true;
	}

	private function __construct() { }

}

?>
