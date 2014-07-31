<?php

class Page extends Object {

	private static $_urlTable;

	private $_parentId;
	private $_date;
	private $_slug;
	private $_title;
	private $_content;

	protected static function _getById($id) {
		$stmt = Knot::getDatabase()->prepare('SELECT `PageId`, `ParentId`, `Date`, `Slug`, `Title`, `Content` FROM `pages` WHERE `PageId`=?');
		$stmt->bind_param('i', $id);
		$stmt->execute();
		$result = $stmt->get_result();
		if ($result->num_rows > 0) {
			return new Page($result->fetch_assoc());
		}
		return null;
	}

	private static function _getBySlug($parent_id, $slug) {
		$stmt = Knot::getDatabase()->prepare('SELECT `PageId`, `ParentId`, `Date`, `Slug`, `Title`, `Content` FROM `pages` WHERE `ParentId`=? AND `Slug`=?');
		$stmt->bind_param('is', $parent_id, $slug);
		$stmt->execute();
		$result = $stmt->get_result();
		if ($result->num_rows > 0) {
			return new Page($result->fetch_assoc());
		}
		return null;
	}

	public static function getByUrl($url) {
		if (self::$_urlTable === null && !KnotCache::get('Knot-UrlTable', self::$_urlTable)) {
			self::$_urlTable = array();
		}
		$changed = false;
		$ref = &self::$_urlTable;
		$url_parts = array_filter(explode('/', trim($url, '/')), function($x) { return !empty($x); });
		foreach ($url_parts as $part) {
			if (isset($ref[$part])) {
				$ref = &$ref[$part];
			} else {
				$page = self::_getBySlug((isset($ref[null]) ? $ref[null] : 0), $part);
				if ($page) {
					$ref[$part][null] = $page->id();
					$ref = &$ref[$part];
					$changed = true;
				} else {
					$ref = null;
					break;
				}
			}
		}
		if ($changed) {
			KnotCache::set('Knot-UrlTable', self::$_urlTable);
		}
		if ($ref && isset($ref[null])) {
			return self::getById($ref[null]);
		}
		return null;
	}

	protected function __construct($data) {
		parent::__construct((int) $data['PageId']);
		$this->_parentId = (int) $data['ParentId'];
		$this->_date = strtotime($data['Date']);
		$this->_slug = $data['Slug'];
		$this->_title = $data['Title'];
		$this->_content = $data['Content'];
	}

	public function parent() {
		if ($this->_parentId) {
			self::getById($this->_parentId);
		}
		return null;
	}

	public function date($format=null) {
		return ($format ? date($format, $this->_date) : $this->_date);
	}

	public function slug() {
		return $this->_slug;
	}

	public function title() {
		return $this->_title;
	}

	public function content() {
		return $this->_content;
	}

	public function url() {
		$parent = $this->parent();
		return ($parent ? $parent->url() : '') . '/' . $this->_slug;
	}

}

?>
