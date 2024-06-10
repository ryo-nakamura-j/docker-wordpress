<?php

/**
 * Pre-configured packaged templates.
 *
 * @package    WPPopups
 * @author     WPPopups
 * @since 2.0.0
 * @license    GPL-2.0+
 * @copyright  Copyright (c) 2016, WP Popups LLC
 */
class WPPopups_Templates {

	/**
	 * Primary class constructor.
	 *
	 * @since 2.0.0
	 */
	public function __construct() {

		$this->init();
	}

	/**
	 * Load and init the base form template class.
	 *
	 * @since 2.0.0
	 */
	public function init() {

		// Parent class template
		require_once WPPOPUPS_PLUGIN_DIR . 'includes/templates/class-base.php';

		// Load default templates on WP init
		add_action( 'init', [ $this, 'load' ] );
	}

	/**
	 * Load default form templates.
	 *
	 * @since 2.0.0
	 */
	public function load() {

		$templates = apply_filters(
			'wppopups_load_templates', [
				'blank',
				'coupon',
				'elegant',
				'transparent',
			]
		);

		foreach ( $templates as $template ) {

			$template = sanitize_file_name( $template );

			if ( file_exists( WPPOPUPS_PLUGIN_DIR . 'includes/templates/class-' . $template . '.php' ) ) {
				require_once WPPOPUPS_PLUGIN_DIR . 'includes/templates/class-' . $template . '.php';
			} elseif ( file_exists( WPPOPUPS_PLUGIN_DIR . 'pro/includes/templates/class-' . $template . '.php' ) ) {
				require_once WPPOPUPS_PLUGIN_DIR . 'pro/includes/templates/class-' . $template . '.php';
			}
		}
	}
}

new WPPopups_Templates();
