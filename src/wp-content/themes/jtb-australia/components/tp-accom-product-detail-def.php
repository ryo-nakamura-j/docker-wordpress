<?php 
require_once("tp-base-ctrl.php");
require_once("tp-config-loader.php");

class TpAccomProductDetail extends TpBaseCtrl{
	var $sectionId;
	var $sectionConfig;
	var $template_source;

	var $productImpressions;

	function __construct() { 
		parent::__construct("TpAccomProductDetail", "tp-accom-product-detail-ctrl.php");
	}

	public function init() {
		include( dirname(__FILE__) . '/../templates/'. $this->template_source );
		$this->initCtrl();
	}

	public function loadStandardSearchConfig() {

		$this->sectionConfig = array(
			'section_heading' => get_sub_field('section_heading'),
			'default_room_type' => get_sub_field('default_room_type'),
			'supplier_based' => get_sub_field('supplier_based'),
			'qtyConfig' =>  get_sub_field('quantity_configuration'),
			'srb' => get_sub_field('service_button'),
			'search_on_load' => get_sub_field('search_on_load'),
			'show_per_scu_rate' => get_sub_field('show_per_scu_rate'),
			);

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
				name_value_pair('qtyConfig', get_sub_field('quantity_configuration')),
				name_value_pair('supplierid', get_sub_field('static_supplier_id')),
				name_value_pair('productid', get_sub_field('static_product_id')),
				name_value_pair('date',  date('Y-m-d', strtotime("+" . get_sub_field('current_date_offset') .  " days"))),
				name_value_pair('scu', get_sub_field('static_scu')),
				name_value_pair('qty', get_sub_field('static_qty'))
			);

			$this->productImpressions = array(
				'id' => get_sub_field('static_product_id'),
				'name' => the_sub_field('title'),
				'list' => get_sub_field("service_button"),
				'category' => get_sub_field("service_button"),
				'position' => '1'
			);
		}

		$this->sectionConfig = TpConfigLoader::loadDateConfig( $this->sectionConfig );
	}
} 

?>