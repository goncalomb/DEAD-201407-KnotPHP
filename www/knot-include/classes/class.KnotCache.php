<?php

final class KnotCache {

	private static function _getFile($key, $ensure_dir=false) {
		$sha1 = md5($key);
		$dir = KNOT_CACHE_DIR . '/' . $sha1[0] . $sha1[1];
		if ($ensure_dir) {
			knot_ensure_path($dir);
		}
		return "{$dir}/{$key}.cache";
	}

	public static function set($key, $data, $ttl=300) {
		$ttl = (int) $ttl;
		file_put_contents(self::_getFile($key, true), serialize(array(
			'ttl' => ($ttl > 0 ? $ttl : 0),
			'data' => $data
		)), LOCK_EX);
	}

	public static function get($key, &$data) {
		$file = self::_getFile($key);
		if (file_exists($file)) {
			$cache_data = unserialize(file_get_contents($file));
			if ($cache_data['ttl'] == 0 || filemtime($file) > KNOT_TIME - $cache_data['ttl']) {
				$data = $cache_data['data'];
				return true;
			}
		}
		$data = null;
		return false;
	}

	public static function remove($key) {
		$file = self::_getFile($key);
		if (file_exists($file)) {
			unlink($file);
		}
	}

	public static function clear() {
		knot_unlink_recursive(KNOT_CACHE_DIR);
	}

}

?>
