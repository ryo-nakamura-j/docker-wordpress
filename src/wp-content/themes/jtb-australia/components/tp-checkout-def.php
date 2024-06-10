<?php 
require_once("tp-base-ctrl.php");
require_once("tp-config-loader.php");

class TpCheckout extends TpBaseCtrl{
	var $sectionId;
	var $sectionConfig;
	var $template_source;
	
	var $tplist;
	var $lproduct;

	function __construct() { 
		parent::__construct("TpCheckout", "tp-checkout-ctrl.php");
	}

	public function init() {
		include( dirname(__FILE__) . '/../templates/'. $this->template_source );
		$this->initCtrl();
	}

	public function loadStandardSearchConfig() {

		$this->sectionConfig = array(
			"customer_fields" => get_sub_field("customer_fields"),
			"delivery_fields" => get_sub_field("delivery_fields")
		);
		
		$this->sectionConfig['itinerary_url'] = tp_itinerary_url();

		$this->tplist = json_decode($_SESSION['tpshoppingcart'], true);
		$this->lproduct = array_map(function($serviceline) {
			return array(
				'id' => $serviceline['productid'],
				'name' => $serviceline['productname'],
				'date' => $serviceline['date'],
				'currency' => $serviceline['currency'],
				'priceori' => $serviceline['price'],
				'price' => $serviceline['pricedisplay'],
				'suppliername' => $serviceline['suppliername'],
				'className' => $serviceline['className'],
				'preference1' => isset($serviceline['preference1']) ? $serviceline['preference1'] : nil,
				'category' => $serviceline['servicetype'],
				'qty' => $serviceline['qty'],
				'list' => $serviceline['servicetype']
			);
		}, $this->tplist['servicelines']);
		$_SESSION['list_products'] = $this->lproduct;
	}
} 

?>