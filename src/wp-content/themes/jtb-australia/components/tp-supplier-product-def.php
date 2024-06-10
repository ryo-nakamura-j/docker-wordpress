<?php 
require_once("tp-base-ctrl.php");
require_once("tp-config-loader.php");

class TpSupplierProductDef extends TpBaseCtrl{
	var $sectionId;
	var $sectionConfig;
	var $template_source;

	function __construct() { 
		parent::__construct(null, "tp-supplier-product-ctrl.php");
	}

	public function init() {
		// include( dirname(__FILE__) . '/../templates/'. $this->template_source );
		$this->initCtrl();
	}

	public function loadStandardSearchConfig() {

		$helper = new TpAcfHelper();
		$srb = get_sub_field( 'service_button' );

		$this->sectionConfig = $helper->getSubFieldList( array( 'upper_map', 'lower_map', 'side_image', 'map_image', 'description' ) );

		$this->sectionConfig['supplier_group'] = array();
		if ( have_rows('supplier_group') ) {
			while (have_rows('supplier_group')) {
				the_row();

				$supplier_group = $this->loadProductSearchConfig( $srb );
				array_push( $this->sectionConfig['supplier_group'], $supplier_group );
			}
		}
		else {
			$supplier_group = $this->loadProductSearchConfig( $srb );
			array_push( $this->sectionConfig['supplier_group'], $supplier_group );
		}

		$this->sectionConfig['terms_and_conditions'] = array();
		while ( have_rows('terms_and_conditions') ) {
			the_row();
			$tc = $helper->getSubFieldList( array( 'header', 'content') );
			array_push( $this->sectionConfig['terms_and_conditions'], $tc );
		}
	}

	private function loadProductSearchConfig( $srb ) {
		
		$helper = new TpAcfHelper();
		$rlt = $helper->getSubFieldList( array( 'group_colour', 'group_title', 'group_title_icon' ) );
		$rlt['searchConf'] = array(
			'srb' => $srb,
			'supplierid' => get_sub_field('supplier_id')
		);

		$rlt['productConfs'] = array();
		$rlt['numProducts'] = count(get_sub_field('products'));

		while (have_rows('products')) {
			the_row();
			$product = array(
				'productid' => get_sub_field('product_id'),
				'product_title' => get_sub_field('product_title'),
				'adult_rate_only' => get_sub_field('adult_rate_only') == 'yes',
				'dropdown_max' => get_sub_field('dropdown_max'),
				'configs' => array()
			);
			while (have_rows('rail_configuration')){
				the_row();
				$product['configs'][get_sub_field('config_setting')] = get_sub_field('config_value');
			}
			array_push($rlt['productConfs'], $product);
		}

		return $rlt;
	}
} 

?>