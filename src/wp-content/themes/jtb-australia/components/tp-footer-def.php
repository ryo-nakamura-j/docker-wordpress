<?php 

class TpFooter{
	var $rootId;
	var $sectionConfig;

	function __construct() {}

	public function init( $rootId ) {
		$this->rootId = $rootId;
		include( dirname( __FILE__ ) . '/' . 'tp-footer-ctrl.php' );
	}

	public function loadConfig() {

    	$helper = new TpAcfHelper( TpAcf::getCurFooterPostId() );

    	$this->sectionConfig = array(
			'footer_menu' => TpWordpressHelper::wp_get_menu_array( 
				$helper->getField( 'footer_menu' ) ),
		);

		$this->sectionConfig = array_merge( $this->sectionConfig, $helper->getFieldList( array(
			'local_office_name', 'alternative_logo_text', 'logo_link', 
			'copyright', 'social_text', 'office_remarks'
		)));

    	$this->sectionConfig['social_url'] = $helper->getListOfKeyValue( 
    		'social_url', 'identifier', 'url' );

    	$this->sectionConfig['office_list'] = $helper->getValueMapList( 
    		'office_list', array( 'label', 'url' ), true );

    	$this->sectionConfig['contact_form'] = $helper->getValueMapList( 
    		'contact_form', array( 'divtagpart', 'scripttagpart' ), true, true );

    	$this->sectionConfig['contact_form_mobile'] = $helper->getValueMapList( 
    		'contact_form_mobile', array( 'divtagpart', 'scripttagpart' ), true, true );

    	$this->sectionConfig['contact_list'] = $helper->getValueMapList( 
    		'contact_list', array( 'branch_name', 'phone_number_display', 'phone_number_call', 'location', 'office_time', 'direction_link', 'google_map_url' ), true );

    	$this->sectionConfig['bottom_image_list'] = $helper->getValueMapList( 
    		'bottom_image_list', array( 'description', 'img' ), true );

		$this->sectionConfig['labels'] = $helper->getValueMapList( 
			'labels', array( 'get_directions', 'search' ), true, true );
	}
} 

?>