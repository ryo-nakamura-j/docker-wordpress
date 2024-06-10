<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * This files is to create meta boxes for shortcode
 *
 * @package post-carousel
 */
class SP_PC_MetaBox {

	/**
	 * The single instance of the class.
	 *
	 * @var self
	 * @since 2.0
	 */
	private static $_instance = null;

	/**
	 * Allows for accessing single instance of class. Class should only be constructed once per call.
	 *
	 * @since 2.0
	 * @static
	 * @return self Main instance.
	 */
	public static function getInstance() {
		if ( ! self::$_instance ) {
			self::$_instance = new SP_PC_MetaBox();
		}

		return self::$_instance;
	}

	/**
	 * Register the class with the WordPress API
	 *
	 * @since 2.0
	 */
	public function __construct() {
		$this->metaboxform = new SP_PC_MetaBoxForm();
		add_action( 'add_meta_boxes', array( $this, 'add_generator_meta_box' ) );
		add_action( 'save_post', array( $this, 'save_meta_box' ) );
	}

	/**
	 * The function responsible for creating the actual generator meta box
	 *
	 * @since 2.0
	 */
	public function add_generator_meta_box() {
		add_meta_box(
			'sp_pc_shortcode_options', __( 'Shortcode Options', 'post-carousel' ), array(
				$this,
				'sp_pc_shortcode_meta_boxes',
			), 'sp_pc_shortcodes', 'normal', 'default'
		);
	}

	/**
	 * Renders the the content of the meta box
	 *
	 * @since 2.0
	 */
	public function sp_pc_shortcode_meta_boxes() {

		wp_nonce_field( 'sp_pc_shortcodes_nonce_action', 'sp_pc_shortcodes_nonce_name' );

		include_once SP_PC_PATH . 'admin/views/tab-navigation.php';
	}


	/**
	 * Save Generator Meta Box
	 *
	 * @param $post_id
	 */
	public function save_meta_box( $post_id ) {

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		// Check if nonce is set
		if ( ! isset( $_POST['sp_pc_shortcodes_nonce_name'], $_POST['sp_pc_meta_box'] ) ) {
			return;
		}
		// Check if nonce is valid.
		if ( ! wp_verify_nonce( $_POST['sp_pc_shortcodes_nonce_name'], 'sp_pc_shortcodes_nonce_action' ) ) {
			return;
		}

		// Check if user has permissions to save data
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		foreach ( $_POST['sp_pc_meta_box'] as $key => $val ) {
			if ( is_array( $val ) ) {
				$val = implode( ',', $val );
			}

			update_post_meta( $post_id, $key, sanitize_text_field( $val ) );
		}

	}

}
