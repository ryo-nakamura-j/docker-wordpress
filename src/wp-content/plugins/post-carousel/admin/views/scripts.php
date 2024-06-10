<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}  // if direct access

/**
 * Scripts and styles
 */
class SP_PC_Admin_Scripts {

	/**
	 * @var null
	 * @since 2.1
	 */
	protected static $_instance = null;

	/**
	 * @return SP_Post_Carousel
	 * @since 2.1
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * Initialize the class
	 */
	public function __construct() {

		add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );
	}

	/**
	 * Enqueue all styles for the meta boxes
	 */
	public function admin_scripts() {
		if ( 'sp_pc_shortcodes' === get_current_screen()->id ) {
			// CSS Files
			wp_enqueue_style( 'wp-color-picker' );
			wp_enqueue_style( 'chosen-style', SP_PC_URL . 'admin/assets/css/chosen.css', array(), SP_PC_VERSION );
			wp_enqueue_style( 'sp-pc-admin-meta-style', SP_PC_URL . 'admin/assets/css/admin-meta.css', array(), SP_PC_VERSION );
			wp_enqueue_style( 'sp-pc-google-font', 'https://fonts.googleapis.com/css?family=Raleway:300,400,500,600,700,800' );

			// JS Files
			wp_enqueue_script( 'sp-pc-admin-meta-js', SP_PC_URL . 'admin/assets/js/admin-meta.js', array( 'jquery', 'wp-color-picker' ), SP_PC_VERSION, true );
			wp_enqueue_script(
				'chosen-js', SP_PC_URL . 'admin/assets/js/chosen.js', array( 'jquery' ),
				SP_PC_VERSION, false
			);

		}

		wp_enqueue_style( 'sp-pc-admin-style', SP_PC_URL . 'admin/assets/css/admin.css', array(), SP_PC_VERSION );
		wp_enqueue_style( 'sp-pc-font', SP_PC_URL . 'public/assets/css/spfont.css', array(), SP_PC_VERSION );
	}

}

new SP_PC_Admin_Scripts();
