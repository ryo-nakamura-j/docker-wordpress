<?php

/**
 * Preview class.
 * TODO: ADD preview
 * @package    WPPopups
 * @author     WPPopups
 * @license    GPL-2.0+
 * @copyright  Copyright (c) 2016, WP Popups LLC
 */
class WPPopups_Preview {

	/**
	 * Primary class constructor.
	 */
	public function __construct() {

		// Maybe load a preview page.
		add_action( 'init', [ $this, 'init' ] );

		// Hide preview page from admin.
		add_action( 'pre_get_posts', [ $this, 'popup_preview_hide' ] );
	}

	/**
	 * Determining if the user should see a preview page, if so, party on.
	 */
	public function init() {

		// Check for preview param with allowed values.
		if ( empty( $_GET['wppopups_preview'] ) ) {
			return;
		}

		// Check for authenticated user with correct capabilities.
		if ( ! is_user_logged_in() || ! wppopups_current_user_can() ) {
			return;
		}

		// Popup preview.
		if ( 'popup' === $_GET['wppopups_preview'] && ! empty( $_GET['popup_id'] ) ) {
			$this->popup_preview();
		}
	}

	/**
	 * Check if preview page exists, if not create it.
	 */
	public function popup_preview_check() {

		// This isn't a privilege check, rather this is intended to prevent
		// the check from running on the site frontend and areas where
		// we don't want it to load.
		if ( ! is_admin() || ( defined( 'DOING_AJAX' ) && DOING_AJAX ) || apply_filters( 'wppopups_disable_preview_page', false ) ) {
			return;
		}

		// Verify page exits.
		$preview = get_option( 'wppopups_preview_page' );

		if ( $preview ) {

			$preview_page = get_post( $preview );

			// Check to see if the visibility has been changed, if so correct it.
			if ( ! empty( $preview_page ) && 'private' !== $preview_page->post_status ) {
				$preview_page->post_status = 'private';
				wp_update_post( $preview_page );

				return;
			} elseif ( ! empty( $preview_page ) ) {
				return;
			}
		}

		// Create the custom preview page.
		$content = '<p>' . esc_html__( 'This is the WP Popups preview page. All your popup previews will be handled on this page.', 'wp-popups-lite' ) . '</p>';
		$content .= '<p>' . esc_html__( 'The page is set to private, so it is not publicly accessible. Please do not delete this page :) .', 'wp-popups-lite' ) . '</p>';
		$args    = [
			'post_type'      => 'page',
			'post_name'      => 'wppopups-preview',
			'post_author'    => 1,
			'post_title'     => esc_html__( 'WP Popups Preview', 'wp-popups-lite' ),
			'post_status'    => 'private',
			'post_content'   => $content,
			'comment_status' => 'closed',
		];

		$id = wp_insert_post( $args );
		if ( $id ) {
			update_option( 'wppopups_preview_page', $id );
		}
	}

	/**
	 * Preview page URL.
	 *
	 * @param int $popup_id
	 *
	 * @return string
	 */
	public function popup_preview_url( $popup_id ) {

		$id = get_option( 'wppopups_preview_page' );

		if ( ! $id ) {
			return home_url();
		}

		$url = get_permalink( $id );

		if ( ! $url ) {
			return home_url();
		}

		return add_query_arg(
			[
				'wppopups_preview' => 'popup',
				'popup_id'         => absint( $popup_id ),
			],
			$url
		);
	}

	/**
	 * Fires when popup preview might be detected.
	 */
	public function popup_preview() {

		add_filter( 'the_posts', [ $this, 'popup_preview_query' ], 10, 2 );
	}

	/**
	 * Tweak the page content for popup preview page requests.
	 *
	 * @param array $posts
	 * @param WP_Query $query
	 *
	 * @return array
	 */
	public function popup_preview_query( $posts, $query ) {

		// One last cap check, just for fun.
		if ( ! is_user_logged_in() || ! wppopups_current_user_can() ) {
			return $posts;
		}

		// Only target main query.
		if ( ! $query->is_main_query() ) {
			return $posts;
		}

		// If our queried object ID does not match the preview page ID, return early.
		$preview_id = absint( get_option( 'wppopups_preview_page' ) );
		$queried    = $query->get_queried_object_id();
		if (
			$queried &&
			$queried !== $preview_id &&
			isset( $query->query_vars['page_id'] ) &&
			$preview_id != $query->query_vars['page_id']
		) {
			return $posts;
		}

		// Get the popup details.
		$popup = wppopups()->popups->get( absint( $_GET['popup_id'] ) );

		if ( ! $popup || empty( $popup ) ) {
			return $posts;
		}

		// Customize the page content.
		$title     = ! empty( $popup->data['settings']['popup_title'] ) ? sanitize_text_field( $popup->data['settings']['popup_title'] ) : esc_html__( 'Popup', 'wp-popups-lite' );
		$content   = esc_html__( 'This is a preview of your popup. This page is not publicly accessible.', 'wp-popups-lite' );
		if ( ! empty( $_GET['new_window'] ) ) {
			$content .= ' <a href="javascript:window.close();">' . esc_html__( 'Close this window', 'wp-popups-lite' ) . '.</a>';
		}
		/* translators: %s - Popup name. */
		$posts[0]->post_title   = sprintf( esc_html__( '%s Preview', 'wp-popups-lite' ), $title );
		$posts[0]->post_content = $content;
		$posts[0]->post_status  = 'public';

		return $posts;
	}

	/**
	 * Hide the preview page from admin
	 *
	 * @param WP_Query $query
	 */
	public function popup_preview_hide( $query ) {

		// Hide the preview page from the site's edit.php post table.
		// This prevents users from seeing or trying to modify this page, since
		// it is intended to be for internal WP Popups use only.
		if (
			$query->is_main_query() &&
			is_admin() &&
			isset( $query->query_vars['post_type'] ) &&
			'page' === $query->query_vars['post_type']
		) {
			$wppopups_preview = intval( get_option( 'wppopups_preview_page' ) );

			if ( $wppopups_preview ) {
				$exclude   = $query->query_vars['post__not_in'];
				$exclude[] = $wppopups_preview;
				$query->set( 'post__not_in', $exclude );
			}
		}
	}
}
