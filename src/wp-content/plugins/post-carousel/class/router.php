<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Post Carousel - route class
 *
 * @since 2.1
 */
class SP_PC_Router {

	/**
	 * @var SP_PC_Router single instance of the class
	 *
	 * @since 2.1
	 */
	protected static $_instance = null;


	/**
	 * Main SP_PC Instance
	 *
	 * @since 2.1
	 * @static
	 * @return self Main instance
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * Include the required files
	 *
	 * @since 1.0
	 * @return void
	 */
	function includes() {
		if ( sp_pc_is_pro() ) {
			include_once SP_PC_PATH . 'includes/pro/loader.php';
		} else {
			include_once SP_PC_PATH . 'includes/free/loader.php';
		}
	}

	/**
	 * SPPC function
	 *
	 * @since 1.0
	 * @return void
	 */
	function sp_pc_function() {
		include_once SP_PC_PATH . 'includes/functions.php';
	}

}
