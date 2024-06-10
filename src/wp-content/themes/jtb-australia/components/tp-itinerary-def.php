<?php 
require_once("tp-base-ctrl.php");
require_once("tp-config-loader.php");

class TpItinerary extends TpBaseCtrl{
	var $sectionId;
	var $sectionConfig;
	var $template_source;
	
	var $itinProducts;

	function __construct() { 
		parent::__construct("TpItinerary", "tp-itinerary-ctrl.php");
	}

	public function init() {
		include( dirname(__FILE__) . '/../templates/'. $this->template_source );
		$this->initCtrl();
	}

	public function loadStandardSearchConfig() {
		$this->itinProducts = array_map(
			function($serviceline) {
				return array(
					'actionField' => array('list' => $serviceline->servicetype),
					'products' => array(
						'id' => $serviceline->productid,
						'name' => $serviceline->productname,
						'date' => $serviceline->date,
						'currency' => $serviceline->currency,
						'price' => $serviceline->price,
						'quantity' => $serviceline->qty,
						'category' => $serviceline->servicetype
					)
				);
			},
			json_decode($_SESSION['tpshoppingcart'])->servicelines
		);

		$this->sectionConfig = get_sub_field("itinerary_buttons");
		$this->sectionConfig['checkout_url'] = tp_checkout_url();
	}
} 

?>