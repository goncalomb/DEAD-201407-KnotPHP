<?php

final class KnotCache {

	private static $_hits = 0;
	private static $_misses = 0;

	private static function _getFile($key, $ensure_dir=false) {
		$sha1 = md5($key);
		$dir = KNOT_CACHE_DIR . '/' . $sha1[0] . $sha1[1];
		if ($ensure_dir) {
			knot_ensure_path($dir);
		}
		return "{$dir}/{$key}.cache";
	}

	private static function _walkCacheFiles($callback) {
		if (is_dir(KNOT_CACHE_DIR)) {
			$handle0 = opendir(KNOT_CACHE_DIR);
			while ($entry0 = readdir($handle0)) {
				$entry0_path = KNOT_CACHE_DIR . "/$entry0";
				if ($entry0 != '.' && $entry0 != '..' && is_dir($entry0_path) && preg_match('/^[0-9a-f]{2}$/', $entry0)) {
					$handle1 = opendir($entry0_path);
					while ($entry1 = readdir($handle1)) {
						$entry1_path = "$entry0_path/$entry1";
						if ($entry1 != '.' && $entry1 != '..' && is_file($entry1_path) && preg_match('/^(.*)\.cache$/', $entry1, $matches)) {
							$callback($matches[1], $entry1, $entry1_path);
						}
					}
				}
			}
			closedir($handle0);
		}
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
				self::$_hits++;
				return true;
			}
		}
		self::$_misses++;
		return false;
	}

	public static function remove($key) {
		$file = self::_getFile($key);
		if (file_exists($file)) {
			unlink($file);
		}
	}

	public static function removeByPrefix($prefix) {
		self::_walkCacheFiles(function($key, $file, $file_path) use ($prefix) {
			if (knot_starts_with($key, $prefix)) {
				unlink($file_path);
			}
		});
	}

	public static function clear() {
		knot_unlink_recursive(KNOT_CACHE_DIR);
	}

	public static function stats() {
		return array('hits' => self::$_hits, 'misses' => self::$_misses);
	}

}

?>
