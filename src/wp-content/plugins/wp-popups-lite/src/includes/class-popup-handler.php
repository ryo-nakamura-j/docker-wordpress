<?php

/**
 * All the popup goodness and basics.
 *
 * Contains a bunch of helper methods as well.
 *
 * @package    WPPopups
 * @author     WPPopups
 * @since 2.0.0
 * @license    GPL-2.0+
 * @copyright  Copyright (c) 2016, WP Popups LLC
 */
class WPPopups_Popup_Handler {

	/**
	 * Primary class constructor.
	 *
	 * @since 2.0.0
	 */
	public function __construct() {

		$this->register_cpt();

		add_action( 'admin_bar_menu', [ $this, 'admin_bar' ], 99 );
	}

	/**
	 * Registers the custom post type to be used for popups.
	 *
	 * @since 2.0.0
	 */
	public function register_cpt() {

		// Custom post type arguments, which can be filtered if needed
		$args = apply_filters(
			'popups/post_type_args',
			[
				'label'               => 'Popups',
				'public'              => false,
				'exclude_from_search' => true,
				'show_ui'             => false,
				'show_in_admin_bar'   => false,
				'rewrite'             => false,
				'query_var'           => false,
				'can_export'          => false,
				'supports'            => [ 'title' ],
			]
		);

		// Register the post type
		register_post_type( 'wppopups', $args );
	}


	/**
	 * Adds "WPForm" item to new-content admin bar menu item.
	 *
	 * @param object $wp_admin_bar
	 *
	 * @since 2.0.0
	 *
	 */
	public function admin_bar( $wp_admin_bar ) {

		if ( ! is_admin_bar_showing() || ! wppopups_current_user_can() ) {
			return;
		}

		$args = [
			'id'     => 'wppopups',
			'title'  => esc_html__( 'WP Popups', 'wp-popups-lite' ),
			'href'   => admin_url( 'admin.php?page=wppopups-builder' ),
			'parent' => 'new-content',
		];
		$wp_admin_bar->add_node( $args );
	}

	/**
	 * Fetches popups
	 *
	 * @param mixed $id
	 * @param array $args
	 *
	 * @return array|bool|null
	 * @since 2.0.0
	 *
	 */
	public function get( $id = '', $args = [] ) {

		$args = apply_filters( 'popups/get_popup_args', $args );

		if ( false === $id ) {
			return new WPPopups_Popup();
		}

		$defaults = [
			'post_type'     => 'wppopups',
			'orderby'       => 'id',
			'order'         => 'ASC',
			'no_found_rows' => true,
			'nopaging'      => true,
			'numberposts'   => -1,
			'post_status'   => 'publish',
		];

		if ( ! empty( $id ) ) {

			if ( is_array( $id ) ) {
				$args             = wp_parse_args( $args, $defaults );
				$args['post__in'] = $id;
				$popups           = get_posts( $args );
			} else {
				// If ID is provided, we get a single popup
				$popups = [ get_post( absint( $id ) ) ];
			}

		} else {
			$args   = wp_parse_args( $args, $defaults );
			$popups = get_posts( $args );
		}

		if ( empty( $popups ) ) {
			return false;
		}

		$popups = array_map( [ $this, 'map_popup_objects' ], $popups );
		// if provide single id return single popup
		if ( ! empty( $id ) && ! is_array( $id ) && ! empty( $popups[0] ) ) {
			return $popups[0];
		}

		return $popups;
	}

	/**
	 * Convert Wp_Post into WpPopups_Popup
	 *
	 * @param $popup Wp_Post
	 *
	 * @return WPPopups_Popup
	 * @see WpPopups_Popup
	 *
	 */
	public function map_popup_objects( $popup ) {
		return new WPPopups_Popup( $popup );
	}

	/**
	 * Delete popups.
	 *
	 * @param array $ids
	 *
	 * @return boolean
	 * @since 2.0.0
	 *
	 */
	public function delete( $ids = [] ) {

		// Check for permissions.
		if ( ! wppopups_current_user_can() ) {
			return false;
		}

		if ( ! is_array( $ids ) ) {
			$ids = [ $ids ];
		}

		$ids = array_map( 'absint', $ids );

		foreach ( $ids as $id ) {

			$popup = wp_delete_post( $id, true );

			if ( ! $popup ) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Add new popup.
	 *
	 * @param string $title
	 * @param array $args
	 * @param array $data
	 *
	 * @return mixed
	 * @since 2.0.0
	 *
	 */
	public function add( $title = '', $args = [], $data = [] ) {

		// Check for permissions.
		if ( ! wppopups_current_user_can() ) {
			return false;
		}

		// Must have a title.
		if ( empty( $title ) ) {
			return false;
		}

		$args = apply_filters( 'wppopups_create_popup_args', $args, $data );

		$popup_content = [
			'settings' => [
				'popup_title' => sanitize_text_field( $title ),
				'popup_desc'  => '',
			],
		];

		// Merge args and create the popup
		$popup    = wp_parse_args(
			$args,
			[
				'post_title'   => esc_html( $title ),
				'post_status'  => 'draft',
				'post_type'    => 'wppopups',
				'post_content' => wppopups_encode( $popup_content ),
			]
		);
		$popup_id = wp_insert_post( $popup );

		do_action( 'wppopups_create_popup', $popup_id, $popup, $data );

		return $popup_id;
	}

	/**
	 * Updates popup
	 *
	 * @param string $popup_id
	 * @param array $data
	 * @param array $args
	 *
	 * @return mixed
	 * @since 2.0.0
	 *
	 * @internal param string $title
	 */
	public function update( $popup_id = '', $data = [], $args = [] ) {

		// This filter breaks popups if they contain HTML.
		remove_filter( 'content_save_pre', 'balanceTags', 50 );
		remove_filter( 'content_save_pre', 'wp_targeted_link_rel' );

		// Check for permissions.
		if ( ! wppopups_current_user_can() ) {
			return false;
		}

		if ( empty( $data ) ) {
			return false;
		}

		if ( empty( $popup_id ) ) {
			$popup_id = $data['id'];
		}

		$data = wp_unslash( $data );

		if ( ! empty( $data['settings']['popup_title'] ) ) {
			$title = $data['settings']['popup_title'];
		} else {
			$title = get_the_title( $popup_id );
		}

		if ( ! empty( $data['settings']['popup_desc'] ) ) {
			$desc = $data['settings']['popup_desc'];
		} else {
			$desc = '';
		}

		$data['field_id'] = ! empty( $data['field_id'] ) ? absint( $data['field_id'] ) : '1';

		// Preserve popup meta.
		$meta = $this->get_meta( $popup_id );
		if ( $meta ) {
			$data['meta'] = $meta;
		}
		// we removed filter but we still filter content
		if( !empty( $data['content']['popup_content'] ) && function_exists('wp_targeted_link_rel') ) {
			$data['content']['popup_content'] = wp_targeted_link_rel($data['content']['popup_content']);
		}

		// Preserve fields meta.
		if ( isset( $data['fields'] ) ) {
			$data['fields'] = $this->update__preserve_fields_meta( $data['fields'], $popup_id );
		} elseif( isset( $data['providers'] ) ) {
			$data['fields'][0] = [
				'id'			=> 0,
				'type'			=> 'email',
				'label'			=> esc_html__('Email', 'wppopups-lite'),
				'description'	=> '',
				'required'		=> 1,
				'size'			=> 'large',
				'placeholder'	=> '',
				'limit_enabled'	=> 0,
				'limit_count'	=> 99,
				'limit_mode'	=> 'characters',
				'default_value'	=> '',
				'css'			=> '',
				'meta'			=> [ 'delete' => false, 'duplicate' => false ],
			];
		}

		// Disable,only admins can use plugin otherwise
		if ( ! current_user_can( 'unfiltered_html' ) ) {
			remove_filter( 'content_save_pre', 'wp_filter_post_kses' );
		}

		$popup = [
			'ID'           => $popup_id,
			'post_title'   => esc_html( $title ),
			'post_excerpt' => $desc,
			'post_content' => wppopups_encode( $data ),
		];
		// set status if passed, only publish or draft available
		$status = isset( $args['post_status'] ) ? ( $args['post_status'] == 'publish' ? 'publish' : 'draft' ) : false;
		if( $status ) {
			$popup['post_status'] = $status;
		}

		// is this an a/b ?
		if( isset( $args['post_parent'] ) ) {
			$popup['post_parent'] = absint( $args['post_parent'] );
		}

		$popup = apply_filters( 'wppopups_save_popup_args', $popup, $data, $args );

		$popup_id = wp_update_post( $popup );

		do_action( 'wppopups_save_popup', $popup_id, $popup );

		return $popup_id;
	}


	/**
	 * Preserve fields meta in 'update' method.
	 *
	 * @since 1.5.8
	 *
	 * @param array      $fields  Popup fields.
	 * @param string|int $popup_id Popup ID.
	 *
	 * @return array
	 */
	protected function update__preserve_fields_meta( $fields, $popup_id ) {

		foreach ( $fields as $i => $field_data ) {
			if ( isset( $field_data['id'] ) ) {
				$field_meta = $this->get_field_meta( $popup_id, $field_data['id'] );
				if ( $field_meta ) {
					$fields[ $i ]['meta'] = $field_meta;
				}
			}
		}

		return $fields;
	}

	/**
	 * Duplicate popups.
	 *
	 * @param array $ids
	 *
	 * @return boolean
	 * @since 2.0.0
	 *
	 */
	public function duplicate( $ids = [] ) {

		// Check for permissions.
		if ( ! wppopups_current_user_can() ) {
			return false;
		}

		if ( ! is_array( $ids ) ) {
			$ids = [ $ids ];
		}

		$ids = array_map( 'absint', $ids );

		foreach ( $ids as $id ) {

			// Get original entry.
			$popup = get_post( $id );

			// Confirm popup exists.
			if ( ! $popup || empty( $popup ) ) {
				return false;
			}

			// Get the popup data.
			$new_popup_data = wppopups_decode( $popup->post_content );

			// Remove popup ID from title if present.
			if( ! isset( $new_popup_data['settings'] ) ) {
				$new_popup_data['settings'] = [ 'popup_title' => '' ];
			}
			$new_popup_data['settings']['popup_title'] = str_replace( '(ID #' . absint( $id ) . ')', '', $new_popup_data['settings']['popup_title'] );

			// Create the duplicate popup.
			$new_popup    = [
				'post_author'  => $popup->post_author,
				'post_content' => wppopups_encode( $new_popup_data ),
				'post_excerpt' => $popup->post_excerpt,
				'post_status'  => 'draft',
				'post_title'   => $new_popup_data['settings']['popup_title'],
				'post_type'    => $popup->post_type,
			];
			$new_popup_id = wp_insert_post( $new_popup );

			if ( ! $new_popup_id || is_wp_error( $new_popup_id ) ) {
				return false;
			}

			// Set new popup name.
			$new_popup_data['settings']['popup_title'] .= ' (ID #' . absint( $new_popup_id ) . ')';

			// Set new popup ID.
			$new_popup_data['id'] = absint( $new_popup_id );

			// Update new duplicate popup.
			$new_popup_id = $this->update( $new_popup_id, $new_popup_data );

			if ( ! $new_popup_id || is_wp_error( $new_popup_id ) ) {
				return false;
			}
		}

		return true;
	}


	/**
	 * Get the next available field ID and increment by one.
	 *
	 * @since 1.0.0
	 *
	 * @param string|int $popup_id Popup ID.
	 * @param array      $args    Additional arguments.
	 *
	 * @return mixed int or false
	 */
	public function next_field_id( $popup_id, $args = [] ) {

		if ( empty( $popup_id ) ) {
			return false;
		}

		$defaults = [ 'content_only' => true ];

		$popup = $this->get( $popup_id, $defaults );

		if ( empty( $popup ) ) {
			return false;
		}

		$popup_data = $popup->data;

		if ( ! empty( $popup_data['field_id'] ) ) {

			$field_id = absint( $popup_data['field_id'] );

			if ( ! empty( $popup_data['fields'] ) &&
				max( array_keys( $popup_data['fields'] ) ) > $field_id
			) {
				$field_id = max( array_keys( $popup_data['fields'] ) ) + 1;
			}

			$popup_data['field_id'] = $field_id + 1;

		} else {
			$field_id         = '0';
			$popup_data['field_id'] = '1';
		}

		$this->update( $popup_id, $popup_data );

		return $field_id;
	}

	/**
	 * Toggle Popup status
	 *
	 * @param $popup_id
	 *
	 * @return bool
	 */
	public function toggle_status( $popup_id ) {
		// Check for permissions.
		if ( ! wppopups_current_user_can() ) {
			return false;
		}

		$popup = wppopups()->popups->get( absint( $popup_id ) );

		if ( empty( $popup ) ) {
			return false;
		}

		// Confirm popup exists.
		if ( ! $popup->status || is_wp_error( $popup->status ) ) {
			return false;
		}

		$args = [
			'post_status' => $popup->status != 'publish' ? 'publish' : 'draft',
		];

		$popup_id = $this->update( $popup->id, $popup->data, $args );

		if ( ! $popup_id || is_wp_error( $popup_id ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Get private meta information for a popup.
	 *
	 * @param string $popup_id
	 * @param string $field
	 *
	 * @return bool
	 * @since 2.0.0
	 *
	 */
	public function get_meta( $popup_id, $field = '' ) {

		if ( empty( $popup_id ) ) {
			return false;
		}

		$popup = $this->get( $popup_id );

		if ( isset( $popup->data['meta'] ) ) {
			if ( empty( $field ) ) {
				return $popup->data['meta'];
			} elseif ( isset( $popup->data['meta'][ $field ] ) ) {
				return $popup->data['meta'][ $field ];
			}
		}

		return false;
	}

	/**
	 * Update or add popup meta information to a popup.
	 *
	 * @param int $popup_id
	 * @param string $meta_key
	 * @param mixed $meta_value
	 *
	 * @return bool
	 * @since 2.0.0
	 *
	 */
	public function update_meta( $popup_id, $meta_key, $meta_value ) {

		// Check for permissions.
		if ( ! wppopups_current_user_can() ) {
			return false;
		}

		if ( empty( $popup_id ) || empty( $meta_key ) ) {
			return false;
		}

		$popup = get_post( absint( $popup_id ) );

		if ( empty( $popup ) ) {
			return false;
		}

		$data     = wppopups_decode( $popup->post_content );
		$meta_key = wppopups_sanitize_key( $meta_key );

		$data['meta'][ $meta_key ] = $meta_value;

		$data    = apply_filters( 'wppopups_update_popup_meta_args', $data, $popup );
		$popup_id = $this->update( $popup_id, $data );

		do_action( 'wppopups_update_popup_meta', $popup_id, $popup, $meta_key, $meta_value );

		return $popup_id;
	}

	/**
	 * Delete popup meta information from a popup.
	 *
	 * @param int $popup_id
	 * @param string $meta_key
	 *
	 * @return bool
	 * @since 2.0.0
	 *
	 */
	public function delete_meta( $popup_id, $meta_key ) {

		// Check for permissions.
		if ( ! wppopups_current_user_can() ) {
			return false;
		}

		if ( empty( $popup_id ) || empty( $meta_key ) ) {
			return false;
		}

		$popup = get_post( absint( $popup_id ) );

		if ( empty( $popup ) ) {
			return false;
		}

		$data     = wppopups_decode( $popup->post_content );
		$meta_key = wppopups_sanitize_key( $meta_key );

		unset( $data['meta'][ $meta_key ] );

		$data    = apply_filters( 'wppopups_update_popup_meta_args', $data, $popup );
		$popup_id = $this->update( $popup_id, $data );

		do_action( 'wppopups_delete_popup_meta', $popup_id, $popup, $meta_key );

		return $popup_id;
	}



	/**
	 * Get private meta information for a form field.
	 *
	 * @since 1.0.0
	 *
	 * @param string|int $popup_id  Popup ID.
	 * @param string     $field_id Field ID.
	 * @param array      $args     Additional arguments.
	 *
	 * @return array|bool
	 */
	public function get_field( $popup_id, $field_id = '', $args = [] ) {

		if ( empty( $popup_id ) ) {
			return false;
		}

		$defaults = array(
			'content_only' => true,
		);

		$popup = $this->get( $popup_id, $defaults );

		$popup_data = $popup->data;

		return isset( $popup_data['fields'][ $field_id ] ) ? $popup_data['fields'][ $field_id ] : false;
	}

	/**
	 * Get private meta information for a form field.
	 *
	 * @since 1.0.0
	 *
	 * @param string|int $popup_id  Popup ID.
	 * @param string     $field_id Field ID.
	 * @param array      $args     Additional arguments.
	 *
	 * @return array|bool
	 */
	public function get_field_meta( $popup_id, $field_id = '', $args = [] ) {

		$field = $this->get_field( $popup_id, $field_id, $args );
		if ( ! $field ) {
			return false;
		}

		return isset( $field['meta'] ) ? $field['meta'] : false;
	}

}
