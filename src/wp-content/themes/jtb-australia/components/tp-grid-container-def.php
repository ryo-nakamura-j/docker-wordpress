<?php 
class TpGridBox {
	var $box_title;
	var $box_link;
	var $box_image;
	var $box_copy;
}
class TpGridContainer {
	var $STYLE_FLYING_TEXT = 'STYLE_FLYING_TEXT';
	var $STYLE_IMAGE_RESPONSIVE = 'STYLE_IMAGE_RESPONSIVE';
	var $STYLE_STANDARD = 'STYLE_STANDARD';
	var $id;
	var $class;
	var $classRow;
	var $title;
	var $boxList;
	var $showHeader = true;
	var $showFooter = true;
	var $style;

	function __construct() {
		$this->style = $this->STYLE_STANDARD;
	}

	public function init() {
		include("tp-grid-container-template.php");
	}
}
?>