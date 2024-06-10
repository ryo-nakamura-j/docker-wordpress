<?php

/**
 * This is to register the shortcode generator post type.
 *
 * @package post-carousel
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
class SP_PC_ShortCodes {

	private static $_instance;

	public function __construct() {
		add_filter( 'init', array( $this, 'register_post_type' ) );
	}

	public static function getInstance() {
		if ( ! self::$_instance ) {
			self::$_instance = new SP_PC_ShortCodes();
		}

		return self::$_instance;
	}

	function register_post_type() {
		register_post_type(
			'sp_pc_shortcodes', array(
				'label'           => __( 'Generate Shortcode', 'post-carousel' ),
				'description'     => __( 'Generate Shortcode', 'post-carousel' ),
				'public'          => false,
				'show_ui'         => true,
				'show_in_menu'    => true,
				'menu_icon'       => SP_PC_URL . '/admin/assets/images/icon-32.png',
				'hierarchical'    => false,
				'query_var'       => false,
				'menu_position'   => 5,
				'supports'        => array( 'title' ),
				'capability_type' => 'post',
				'labels'          => array(
					'name'               => __( 'Post Carousels', 'post-carousel' ),
					'singular_name'      => __( 'Post Carousel', 'post-carousel' ),
					'menu_name'          => __( 'Post Carousel', 'post-carousel' ),
					'all_items'          => __( 'Post Carousels', 'post-carousel' ),
					'add_new'            => __( 'Add New', 'post-carousel' ),
					'add_new_item'       => __( 'Add New Carousel', 'post-carousel' ),
					'edit'               => __( 'Edit', 'post-carousel' ),
					'edit_item'          => __( 'Edit Post Carousel', 'post-carousel' ),
					'new_item'           => __( 'New Post Carousel', 'post-carousel' ),
					'search_items'       => __( 'Search Post Carousels', 'post-carousel' ),
					'not_found'          => __( 'No Post Carousels found', 'post-carousel' ),
					'not_found_in_trash' => __( 'No Post Carousels found in Trash', 'post-carousel' ),
					'parent'             => __( 'Parent Post Carousel', 'post-carousel' ),
				),
			)
		);
	}

}
new SP_PC_ShortCodes();
