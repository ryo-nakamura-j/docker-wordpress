<?php

// don't load directly
if (!defined('ABSPATH')) die('-1');

class CF7_Material_Design_Version {
    
    function __construct() {
        
      add_action( 'wp_ajax_cf7md_update_legacy', array( $this, 'update_legacy' ) );

    }
    

    /**
     * Update legacy
     * Activated by ajax so ends in wp_die()
     */
    public function update_legacy() {
      $legacy = $_POST['cf7md_legacy'];
      update_option( 'cf7md_options[version_switch]', $legacy);
      echo 'Success';
      wp_die();
  }

}

$cf7_material_design_version = new CF7_Material_Design_Version();