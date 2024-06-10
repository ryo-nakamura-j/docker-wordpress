<?php 
require_once("tp-base-ctrl.php");
require_once("tp-config-loader.php");

class TpTourProductDetail extends TpBaseCtrl{
	var $sectionId;
	var $sectionConfig;
	var $template_source;

	var $productImpressions;

	function __construct() { 
		parent::__construct("TpTourProductDetail", "tp-tour-product-detail-ctrl.php");
	}

	public function init() {
		include( dirname(__FILE__) . '/../templates/'. $this->template_source );
		$this->initCtrl();
	}

	public function loadStandardSearchConfig() {

		$helper = new TpAcfHelper();
		$this->sectionConfig = array(
			'room_based' => get_sub_field('room_based'),
			'default_room_type' => get_sub_field('default_room_type'),
			'supplier_based' => get_sub_field('supplier_based'),
			'srb' => get_sub_field('service_button'),
			'arrangements_sequence' => get_sub_field('arrangements_sequence'),
			);

		$this->sectionConfig['arrangements_field_data_overwrite'] = 
			$helper->getValueMapList( 'arrangements_field_data_overwrite', 
				array( 'arrangementid', 'defaultvalue', 'isrequiredflag' ) );
				
		if (get_sub_field('enable_modify_search_panel')) {
			$this->sectionConfig['modify_search_config'] = array(
				name_value_pair('date_in_label', get_sub_field('date_in_label')),
				name_value_pair('date_out_label', get_sub_field('date_out_label')),
				name_value_pair('scu_label', get_sub_field('scu_label')),
				name_value_pair('qty_label', get_sub_field('qty_label')),
			);
		}

		
		if (get_sub_field('static_product_page')) {
			$this->sectionConfig['static_config'] = array(
				name_value_pair('supplierid', get_sub_field('static_supplier_id')),
				name_value_pair('date',  date('Y-m-d', strtotime("+" . get_sub_field('current_date_offset') .  " days"))),
				name_value_pair('scu', get_sub_field('static_scu')),
				name_value_pair('qty', get_sub_field('static_qty'))
			);
		}

		if (get_sub_field('date_range_search')) {
			$this->sectionConfig['date_range_config'] = array(
				name_value_pair('days_before', get_sub_field('date_range_days_before')),
				name_value_pair('days_after', get_sub_field('date_range_days_after'))
			);
		}
	}
} 

?>