<?php 

class TpHeader{
	var $rootId;
	var $sectionConfig;

	function __construct() {}

	public function init( $rootId ) {
		$this->rootId = $rootId;
		include( dirname( __FILE__ ) . '/' . 'tp-header-ctrl.php' );
	}

	public function loadConfig() {

    	$helper = new TpAcfHelper( TpAcf::getCurHeaderPostId() );

    	$this->sectionConfig = array(
			'main_menu' => TpWordpressHelper::wp_get_menu_array( 
				$helper->getField( 'main_menu' ) ),
			'secondary_menu' => TpWordpressHelper::wp_get_menu_array( 
				$helper->getField( 'secondary_menu' ) ),
			'mobile_menu' => TpWordpressHelper::wp_get_menu_array( 
				$helper->getField( 'mobile_menu' ) ),
			'mobile_menu_bottom' => TpWordpressHelper::wp_get_menu_array( 
				$helper->getField( 'mobile_menu_bottom' ) ),
			'main_menu_right_side' => TpWordpressHelper::wp_get_menu_array( 
				$helper->getField( 'main_menu_right_side' ) ),
		);

		$this->sectionConfig = array_merge( $this->sectionConfig, $helper->getFieldList( array(
			'local_office_name', 'main_remarks', 'secondary_remarks', 
			'alternative_logo_text', 'logo_link'
		)));

		$this->sectionConfig['itinerary_url'] = tp_itinerary_url();

    	$this->sectionConfig['social_url'] = $helper->getListOfKeyValue( 
    		'social_url', 'identifier', 'url' );

    	$this->sectionConfig['request_url'] = $_SERVER['REQUEST_URI'];

		$this->sectionConfig['phone_list'] = $helper->getKeyValueWithLayout( 
			'phone_list', 'identifier', 'phone_detail', array(
	    		'phone' => array( 
		    		'func' => 'getValueMap',
		    		'params' => array( 
		    			'0' => array( 'label', 'num', 'call_number' ) ,
		    		),
		    	),
		    	'phone_list' => array(
		    		'func' => 'getValueMapList',
		    		'params' => array( 
		    			'0' => 'list_content',
		    			'1' => array( 'label', 'num', 'call_number' ) ,
		    		),
		    	),
	    	));
		$this->sectionConfig['labels'] = $helper->getValueMapList( 
			'labels', array( 'more_shops', 'call_us', 'search' ), true, true );
	}
} 

?>