<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; }  // if direct access

/**
 * Scripts and styles
 */
class SP_PC_Scripts {

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

		add_action( 'wp_enqueue_scripts', array( $this, 'front_scripts' ) );
	}

	/**
	 * Plugin Scripts and Styles
	 */
	function front_scripts() {
		// CSS Files.
		wp_enqueue_style( 'slick', SP_PC_URL . 'public/assets/css/slick.css', array(), SP_PC_VERSION );
		wp_enqueue_style( 'sp-pc-font', SP_PC_URL . 'public/assets/css/spfont.css', array(), SP_PC_VERSION );
		wp_enqueue_style( 'sp-pc-style', SP_PC_URL . 'public/assets/css/style.css', array(), SP_PC_VERSION );

		// JS Files.
		wp_enqueue_script( 'slick-min-js', SP_PC_URL . 'public/assets/js/slick.min.js', array( 'jquery' ), SP_PC_VERSION, false );

	}

}
new SP_PC_Scripts();
