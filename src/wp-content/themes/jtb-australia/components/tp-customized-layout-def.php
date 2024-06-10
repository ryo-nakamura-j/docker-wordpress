<?php 

class TpCustomizedLayout{
	var $template_source;

	public function init() {
		include( dirname(__FILE__) . '/../templates/'. $this->template_source );
	}
} 

?>