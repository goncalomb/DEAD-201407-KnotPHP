<?php

class HtmlElement {

	private $_tag;
	private $_html = array();
	private $_attributes = array();

	public function __construct($tag) {
		$this->_tag = $tag;
	}

	public function attribute($name, $value=null) {
		if ($value === null) {
			unset($this->_attributes[$name]);
		} else {
			$this->_attributes[$name] = $value;
		}
	}

	public function append() {
		$args = func_get_args();
		foreach ($args as $arg) {
			$this->_html[] = $arg;
		}
	}

	public function clear() {
		$this->_html = array();
	}

	protected function outputOpeningTag() {
		echo '<', $this->_tag;
		foreach ($this->_attributes as $name => $value) {
			echo ' ', $name, '="', $value, '"';
		}
		echo '>';
	}

	protected function outputContent() {
		foreach ($this->_html as $part) {
			if ($part instanceof HtmlElement) {
				$part->output();
			} else {
				echo $part;
			}
		}
	}

	protected function outputClosingTag() {
		echo '</', $this->_tag, '>';
	}

	public function output() {
		$this->outputOpeningTag();
		$this->outputContent();
		$this->outputClosingTag();
	}

}

class HeadElement extends HtmlElement {

	private $_metatags = array();
	private $_styles = array();
	private $_scripts = array();
	private $_title = 'A Page';

	public function __construct() {
		parent::__construct('head');
	}

	public function metaTag($name, $content) {
		$this->_metatags[$name] = $content;
	}

	public function stylesheetFile($href) {
		$this->_styles[] = $href;
	}

	public function scriptFile($src) {
		$this->_scripts[] = $src;
	}

	public function title($title) {
		$this->_title = $title;
	}

	public function output() {
		$this->outputOpeningTag();
		echo '<meta charset="UTF-8">';
		foreach ($this->_metatags as $name => $content) {
			echo '<meta name="', $name, '" content="', $content, '">';
		}
		foreach ($this->_styles as $href) {
			echo '<link rel="stylesheet" type="text/css" href="', $href, '">';
		}
		foreach ($this->_scripts as $src) {
			echo '<script type="text/javascript" src="', $src, '"></script>';
		}
		if ($this->_title) {
			echo '<title>', $this->_title, '</title>';
		}
		$this->outputContent();
		$this->outputClosingTag();
	}

}

class BodyElement extends HtmlElement {

	private $_scripts = array();

	public function __construct() {
		parent::__construct('body');
	}

	public function scriptFile($src) {
		$this->_scripts[] = $src;
	}

	public function output() {
		$this->outputOpeningTag();
		$this->outputContent();
		foreach ($this->_scripts as $src) {
			echo '<script type="text/javascript" src="', $src, '"></script>';
		}
		$this->outputClosingTag();
	}

}

class HtmlPage {

	private $_htmlElement;
	private $_headElement;
	private $_bodyElement;

	public function __construct() {
		$this->_htmlElement = new HtmlElement('html');
		$this->_headElement = new HeadElement();
		$this->_bodyElement = new BodyElement();
		$this->_htmlElement->append($this->_headElement);
		$this->_htmlElement->append($this->_bodyElement);
	}

	public function head() {
		return $this->_headElement;
	}

	public function body() {
		return $this->_bodyElement;
	}

	public function metaTag($name, $content) {
		$this->_headElement->metaTag($name, $content);
	}

	public function stylesheetFile($href) {
		$this->_headElement->stylesheetFile($href);
	}

	public function scriptFile($src, $end=false) {
		if ($end) {
			$this->_bodyElement->scriptFile($src);
		} else {
			$this->_headElement->scriptFile($src);
		}
	}

	public function title($title) {
		$this->_headElement->title($title);
	}

	public function output() {
		echo '<!DOCTYPE html>', "\n";
		$this->_htmlElement->output();
		echo "\n";
	}

}

?>