<?php

abstract class Object {

	private static $_loadedObjects = array();

	private $_doCache = true;
	private $_id;

	public final static function getById($id, $fresh=false) {
		list($id, $id_str) = knot_id_parse($id);
		if ($id) {
			$obj_type = get_called_class();
			if (!$fresh && isset(self::$_loadedObjects[$id]) && is_a(self::$_loadedObjects[$id], $obj_type)) {
				return self::$_loadedObjects[$id];
			} else if (!$fresh && KnotCache::get('Knot-Object-' . $id_str, $obj) && is_a($obj, $obj_type)) {
				$obj->_doCache = false;
				self::$_loadedObjects[$id] = $obj;
				return $obj;
			}
			if ($obj_type == 'Object') {
				$stmt = Knot::getDatabase()->prepare('SELECT `Type` FROM `objects` WHERE `ObjectId`=?');
				$stmt->bind_param('i', $id);
				$stmt->execute();
				$stmt->bind_result($obj_type);
				if (!$stmt->fetch()) {
					return false;
				}
				$stmt->close();
			}
			if ($obj_type !== null) {
				return call_user_func(array($obj_type, '_getById'), $id);
			}
		}
		return null;
	}

	protected static function _getById($id) {
		user_error(get_called_class() . ' must override the _getById method.', E_USER_ERROR);
	}

	protected function __construct($id) {
		$this->_id = (int) $id;
		$stmt = Knot::getDatabase()->prepare('INSERT IGNORE INTO `objects` (`ObjectId`, `Type`) VALUES (?, ?)');
		$type = get_called_class();
		$stmt->bind_param('is', $this->_id, $type);
		$stmt->execute();
		self::$_loadedObjects[$this->_id] = $this;
	}

	public function id($base62=false) {
		return ($base62 ? knot_id_to_base62($this->_id) : $this->_id);
	}

	public abstract function url();

	public final function shortUrl() {
		return '/o/' . $this->id(true);
	}

	protected final function cache() {
		$this->_doCache = true;
	}

	public final function __destruct() {
		if ($this->_doCache) {
			unset($this->_doCache);
			KnotCache::set('Knot-Object-' . $this->id(true), $this);
			$this->_doCache = false;
		}
	}

}

?>
