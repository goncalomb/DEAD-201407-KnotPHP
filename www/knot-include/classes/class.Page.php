<?php

class Page extends Object {

	private static $_urlTable;

	private $_parentId;
	private $_date;
	private $_slug;
	private $_order;
	private $_title;
	private $_content;
	private $_childPagesIds;

	protected static function _getById($id) {
		$stmt = Knot::getDatabase()->prepare('SELECT * FROM `pages` WHERE `PageId`=?');
		$stmt->bind_param('i', $id);
		$stmt->execute();
		$result = $stmt->get_result();
		if ($result->num_rows > 0) {
			return new Page($result->fetch_assoc());
		}
		return null;
	}

	private static function _getBySlug($parent_id, $slug) {
		$stmt = Knot::getDatabase()->prepare('SELECT * FROM `pages` WHERE `ParentId`=? AND `Slug`=?');
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
				$page = self::_getBySlug((isset($ref[null]) ? $ref[null] : 1), $part);
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

	public static function getChildsOf($id) {
		$pages = array();
		$stmt = Knot::getDatabase()->prepare('SELECT * FROM `pages` WHERE `ParentId`=? ORDER BY `Order` ASC');
		$stmt->bind_param('i', $id);
		$stmt->execute();
		$result = $stmt->get_result();
		while ($row = $result->fetch_assoc()) {
			$pages[] = new Page($row);
		}
		return $pages;
	}

	public static function create($parent_id) {
		$page = new self(array(
			'PageId' => knot_id_random_unique(),
			'ParentId' => (int) $parent_id,
			'Date' => date('r', KNOT_TIME),
			'Slug' => 'new-page',
			'Order' => 1000,
			'Title' => 'New Page',
			'Content' => '',
		));
		$page->save();
		return $page;
	}

	protected function __construct($data) {
		parent::__construct((int) $data['PageId']);
		$this->_parentId = (int) $data['ParentId'];
		$this->_date = strtotime($data['Date']);
		$this->_slug = $data['Slug'];
		$this->_order = (int) $data['Order'];
		$this->_title = $data['Title'];
		$this->_content = $data['Content'];
	}

	public function parent() {
		if ($this->_parentId) {
			return self::getById($this->_parentId);
		}
		return null;
	}

	public function date($format=null) {
		return ($format ? date($format, $this->_date) : $this->_date);
	}

	public function slug($slug=null) {
		if ($slug != null) {
			$this->_slug = $slug;
		}
		return $this->_slug;
	}

	public function order() {
		return $this->_order;
	}

	public function title($title=null) {
		if ($title != null) {
			$this->_title = $title;
		}
		return $this->_title;
	}

	public function content($content=null) {
		if ($content != null) {
			$this->_content = $content;
		}
		return $this->_content;
	}

	public function url() {
		$parent = $this->parent();
		return ($parent && $parent->id() != 1 ? $parent->url() : '') . '/' . $this->_slug;
	}

	public function childs() {
		$pages = array();
		if ($this->_childPagesIds === null) {
			$pages = self::getChildsOf($this->id());
			$this->_childPagesIds = array();
			foreach ($pages as $page) {
				$this->_childPagesIds[] = $page->id();
			}
			$this->cache();
		} else {
			foreach ($this->_childPagesIds as $id) {
				if ($page = Page::getById($id)) {
					$pages[] = $page;
				}
			}
		}
		return $pages;
	}

	public function save() {
		$this->_date = KNOT_TIME;
		$stmt = Knot::getDatabase()->prepare('REPLACE INTO `pages` (`PageId`, `ParentId`, `Date`, `Slug`, `Order`, `Title`, `Content`) VALUES (?, ?, FROM_UNIXTIME(?), ?, ?, ?, ?)');
		$id = $this->id();
		$stmt->bind_param('iiisiss', $id, $this->_parentId, $this->_date, $this->_slug, $this->_order, $this->_title, $this->_content);
		$stmt->execute();
		$stmt->close();
		$this->cache();
	}

	public function delete() {
		$stmt = Knot::getDatabase()->prepare('DELETE FROM `pages` WHERE `PageId`=?');
		$id = $this->id();
		$stmt->bind_param('i', $id);
		$stmt->execute();
	}

}

?>
