<?php

final class Knot {

	private static $_config;
	private static $_isAdmin = false;
	private static $_adminId;
	private static $_mysqliLink = null;
	private static $_object = null;

	private static function _getAdminId() {
		if (self::$_adminId === null) {
			if (!KnotCache::get('Knot-AdminSalt', $salt)) {
				$salt = knot_random_id();
				KnotCache::set('Knot-AdminSalt', $salt, 0);
			}
			self::$_adminId = sha1("Knot:{$salt}:{$_SERVER['REMOTE_ADDR']}:{$_SERVER['HTTP_USER_AGENT']}:" . self::config('admin-password-sha1'));
		}
		return self::$_adminId;
	}

	public static function internal_initialize() {
		if (self::$_config === null) {
			// Load config.
			$load_config = function() {
				$knot_config = array();
				require 'knot-content/config.php';
				return $knot_config;
			};
			self::$_config = $load_config();
			// Check admin session.
			if (isset($_COOKIE['knot-admin'])) {
				if($_COOKIE['knot-admin'] == self::_getAdminId()) {
					self::$_isAdmin = true;
				} else {
					self::adminLogout();
				}
			}
		}
	}

	public static function config($key, $default=null) {
		return (self::$_config !== null && !empty(self::$_config[$key]) ? self::$_config[$key] : $default);
	}

	public static function isAdmin() {
		return self::$_isAdmin;
	}

	public static function adminLogin($password) {
		if (sha1('Knot:' . $password) == self::config('admin-password-sha1')) {
			setcookie('knot-admin', self::_getAdminId(), 0, '/', null, false, true);
			self::$_isAdmin = true;
			return true;
		}
		return false;
	}

	public static function adminLogout() {
		setcookie('knot-admin', 'void', 1, '/', null, false, true);
		self::$_isAdmin = false;
	}

	public static function getDatabase() {
		if (!self::$_mysqliLink) {
			mysqli_report(MYSQLI_REPORT_ERROR);
			self::$_mysqliLink = new mysqli_ex(self::config('database'));
			if (!file_exists(KNOT_TMP_DIR . '/FLAG_DB_CREATED')) {
				knot_require_file('knot-include/db-create.php');
				touch(KNOT_TMP_DIR . '/FLAG_DB_CREATED');
			}
		}
		return self::$_mysqliLink;
	}

	public static function queryCount() {
		if (self::$_mysqliLink) {
			return self::$_mysqliLink->query_count();
		}
		return 0;
	}

	public static function internal_handleRequest() {
		self::$_object = Page::getByUrl(KNOT_REQUEST_URI);
		if (self::$_object) {
			self::_serveObject();
		}
	}

	private static function _serveObject() {
		KnotPage::start();
		knot_require_file(KnotPage::themeFolder() . '/template-' . get_class(self::$_object) . '.php');
		exit();
	}

	public static function getObject() {
		return self::$_object;
	}

	private function __construct() { }

}

?>
