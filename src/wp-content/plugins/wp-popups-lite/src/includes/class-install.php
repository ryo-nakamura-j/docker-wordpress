<?php

/**
 * Handles plugin installation upon activation.
 *
 * @package    WPPopups
 * @author     WPPopups
 * @since      1.0.0
 * @license    GPL-2.0+
 * @copyright  Copyright (c) 2016, WP Popups LLC
 */
class WPPopups_Install {

	/**
	 * Primary class constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		// When activated, trigger install method.
		register_activation_hook( WPPOPUPS_PLUGIN_FILE, [ $this, 'install' ] );

		// Watch for new multisite blogs.
		add_action( 'wpmu_new_blog', [ $this, 'new_multisite_blog' ], 10, 6 );
	}

	/**
	 * Let's get the party started.
	 *
	 * @param boolean $network_wide
	 *
	 * @since 1.0.0
	 *
	 */
	public function install( $network_wide = false ) {

		// Check if we are on multisite and network activating.
		if ( is_multisite() && $network_wide ) {

			// Multisite - go through each subsite and run the installer.
			if ( function_exists( 'get_sites' ) && class_exists( 'WP_Site_Query', false ) ) {

				// WP 4.6+.
				$sites = get_sites();

				foreach ( $sites as $site ) {
					switch_to_blog( $site->blog_id );
					$this->run_install();
					restore_current_blog();
				}
			} else {

				$sites = wp_get_sites( [ 'limit' => 0 ] );

				foreach ( $sites as $site ) {
					switch_to_blog( $site['blog_id'] );
					$this->run_install();
					restore_current_blog();
				}
			}
		} else {

			// Normal single site.
			$this->run_install();
		}

		// Abort so we only set the transient for single site installs.
		if ( is_network_admin() || isset( $_GET['activate-multi'] ) ) {
			return;
		}

		// Add transient to trigger redirect to the Welcome screen.
		set_transient( 'wppopups_activation_redirect', true, 30 );
	}

	/**
	 * Run the actual installer.
	 *
	 * @since 1.3.0
	 */
	public function run_install() {
		
		// Hook for Pro users.
		do_action( 'wppopups_install' );

		// Set current version, to be referenced in future updates.
		update_option( 'wppopups_version', WPPOPUPS_VERSION );

		// Store the date when the initial activation was performed.
		$type      = class_exists( 'WPPopups_Lite', false ) ? 'lite' : 'pro';
		$activated = get_option( 'wppopups_activated', [] );
		if ( empty( $activated[ $type ] ) ) {
			$activated[ $type ] = time();
			update_option( 'wppopups_activated', $activated );
		}
	}

	/**
	 * When a new site is created in multisite, see if we are network activated,
	 * and if so run the installer.
	 *
	 * @param int $blog_id
	 * @param int $user_id
	 * @param string $domain
	 * @param string $path
	 * @param int $site_id
	 * @param array $meta
	 *
	 * @since 1.3.0
	 *
	 */
	public function new_multisite_blog( $blog_id, $user_id, $domain, $path, $site_id, $meta ) {

		if ( is_plugin_active_for_network( plugin_basename( WPPOPUPS_PLUGIN_FILE ) ) ) {

			switch_to_blog( $blog_id );
			$this->run_install();
			restore_current_blog();

		}
	}
}

new WPPopups_Install();
