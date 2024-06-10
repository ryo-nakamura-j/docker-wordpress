<?php 
require_once("tp-base-ctrl.php");
require_once("tp-config-loader.php");

class TpProductSearch extends TpBaseCtrl{
	var $sectionId;
	var $sectionConfig;
	var $template_source;

	function __construct() { 
		parent::__construct("TpProductSearch", "tp-product-search-ctrl.php");
	}

	public function init() {
		include( dirname(__FILE__) . '/../templates/'. $this->template_source );
		$this->initCtrl();
	}

	public function loadStandardSearchConfig() {
		$helper = new TpAcfHelper();
		$this->sectionConfig = $helper->getSubFieldList( array(
			'quicksearch','search_external_rate_availability','results_page','room_based','supplier_level','default_room_type','product_info_page','pagination_max_item_on_page', 'sort_order_most_popular_db_index'
		)); 

		$this->sectionConfig['search_config'] = array(
			name_value_pair('srb', get_sub_field('default_srb')),
			name_value_pair('cty', get_sub_field('default_cty')),
			name_value_pair('dst', get_sub_field('default_dst')),
			name_value_pair('lcl', get_sub_field('default_lcl')),
			name_value_pair('qty', get_sub_field('default_qty')),
			name_value_pair('scu', get_sub_field('default_scu')),
			name_value_pair('date',  date('Y-m-d', strtotime("+" . get_sub_field('current_date_offset') .  " days"))),
		);

		$this->sectionConfig['preview_service_date'] = date('Y-m-d', strtotime("+" . get_sub_field('current_date_offset') .  " days"));

		$this->sectionConfig['date_range_config'] = array(
			name_value_pair('days_before', get_sub_field('date_range_days_before')),
			name_value_pair('days_after', get_sub_field('date_range_days_after'))
		);

		$this->sectionConfig = TpConfigLoader::loadDateConfig( $this->sectionConfig );

		$this->sectionConfig['amenity_filters'] = get_sub_field('amenity_filter');
	}
} 

?>