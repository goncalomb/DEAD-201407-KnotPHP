<?php

final class KnotErrorHandling {

	private static $_htmlErrors = array(
		400 => 'Bad Request',
		403 => 'Forbidden',
		404 => 'Not Found',
		500 => 'Internal Server Error',
		503 => 'Service Temporarily Unavailable'
	);

	private static $_phpErrorConstants = null;

	private static function _getErrorConstant($value) {
		if (static::$_phpErrorConstants === null) {
			static::$_phpErrorConstants = array();
			foreach (get_defined_constants(true)['Core'] as $name => $value) {
				if (strlen($name) > 2 && $name[0] == 'E' && $name[1] == '_') {
					static::$_phpErrorConstants[$value] = $name;
				}
			}
		}
		return (isset(static::$_phpErrorConstants[$value]) ? static::$_phpErrorConstants[$value] : 'E_UNKNOWN');
	}

	public static function internal_handleError($type, $message, $file, $line) {
		if (error_reporting() !== 0) {
			$debug_message = self::_getErrorConstant($type) . '(' . $type . '): ' . $message . ' in ' . $file . ' on line ' . $line;
			@error_log(date('[d-m-Y H:i:s]', KNOT_TIME) . ' ' . $_SERVER['REQUEST_URI'] . ' - ' . $debug_message. "\n", 3, KNOT_TMP_DIR . '/php-errors.log');
			self::httpError(500, $debug_message);
		}
		return true;
	}

	public static function internal_handleShutdown() {
		$error = error_get_last();
		if ($error) {
			self::internal_handleError($error['type'], $error['message'], $error['file'], $error['line']);
		}
	}

	public static function httpError($code, $message='') {
		$code = (int) $code;
		if (!isset(self::$_htmlErrors[$code])) {
			$code = 500;
		}
		while (ob_get_level()) {
			ob_end_clean();
		}
		knot_internal_utility_page($code . ' ' . self::$_htmlErrors[$code], ($message && KNOT_DEBUG ? '<pre>' . htmlentities($message) . '</pre>' : ''), $code);
	}

}

?>
