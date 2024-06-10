<?php

/**
 * Register menu elements and do other global tasks.
 *
 * @package    WPPopups
 * @author     WPPopups
 * @since 2.0.0
 * @license    GPL-2.0+
 * @copyright  Copyright (c) 2016, WP Popups LLC
 */
class WPPopups_Admin_Menu {

	/**
	 * Primary class constructor.
	 *
	 * @since 2.0.0
	 */
	public function __construct() {

		// Let's make some menus.
		add_action( 'admin_menu', [ $this, 'register_menus' ], 9 );

		// Plugins page settings link.
		add_filter( 'plugin_action_links_' . WPPOPUPS_HOOK, [ $this, 'settings_link' ] );
	}

	/**
	 * Register our menus.
	 *
	 * @since 2.0.0
	 */
	public function register_menus() {

		$menu_cap = wppopups_get_manage_capability();

		// Default Popups top level menu item.
		add_menu_page(
			esc_html__( 'WP Popups', 'wp-popups-lite' ),
			esc_html__( 'WP Popups', 'wp-popups-lite' ),
			$menu_cap,
			'wppopups-overview',
			[ $this, 'admin_page' ],
			'data:image/svg+xml;base64,' . base64_encode( '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 153 151" width="153" height="151"><defs><image width="151" height="151" id="img1" href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAJcAAACXCAMAAAAvQTlLAAAAAXNSR0IB2cksfwAAAMBQTFRFAHOqAHOqAHOqAAAAAHOqAHOqAHOqAHOqAHOqAHOqAHOqAHOqAHOqAHOqAHOqAHOqAHOqAHOqAHOqAHOqAHOqAHOqAHOqAHOqAHOqAHOqAHOqAHOqAHOqAHOqAHOqAHOqAHOqL22LZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZm1Y1mKQAAAEB0Uk5TbIBMANj/mOk17gwj1BfhIOPdFtca5SbsMPAp5yQoMhzfcIA+aP989OsjhdQR0RLXGd/ZFRjY1h3cGx7lqLfAXWj80ucAAAE5SURBVHic7djJTgJBFEbhhouAOLSCMjiCeFVUhgbEWd7/rViYKC7ohKRM/5pz9rfy5S6qkoqiXFblLaWosJFVRVy4cOHChetPukqirvKmpquyta3psp1dTZfFe5oui/c1XVatabrs4FDTZfWGpsuaLU2XHR1ruuzkF2AhXHYa/kkK4rKz4BsL47LztqbL4sD/E52L1XXXcIXu0ld3hQsXLly4cOHChQsXLly4cOHChQsXLly4cOHChQsXLly4cOHChQsXLly4cOHChQsXLly4cOHChQsXLly4cP0rV9rkdcrk+t30fp5+K+Lyu3tNl/cHmi7vL29MyOXDkabLk2+YlMvHE02XD6eari+YmssfJpounw00XZ/XhaDLk0dNlz89a7o8edF0+eubpis1XLhw4cKFy+z9I6vmC+sAc2Bjpu3VAAAAAElFTkSuQmCC"/></defs><style>tspan { white-space:pre }</style><use id="Layer 1" href="#img1" x="1" y="0" /></svg>' ),
			apply_filters( 'wppopups_menu_position', '59' )
		);

		// All Popups sub menu item.
		add_submenu_page(
			'wppopups-overview',
			esc_html__( 'Popups', 'wp-popups-lite' ),
			esc_html__( 'All Popups', 'wp-popups-lite' ),
			$menu_cap,
			'wppopups-overview',
			[ $this, 'admin_page' ]
		);

		// Add New sub menu item.
		add_submenu_page(
			'wppopups-overview',
			esc_html__( 'WP Popups Builder', 'wp-popups-lite' ),
			esc_html__( 'Add New', 'wp-popups-lite' ),
			$menu_cap,
			'wppopups-builder',
			[ $this, 'admin_page' ]
		);

		do_action( 'wpform/admin_menu', $this );

		// Settings sub menu item.
		add_submenu_page(
			'wppopups-overview',
			esc_html__( 'WP Popups Settings', 'wp-popups-lite' ),
			esc_html__( 'Settings', 'wp-popups-lite' ),
			$menu_cap,
			'wppopups-settings',
			[ $this, 'admin_page' ]
		);

		// Tools sub menu item.
		add_submenu_page(
			'wppopups-overview',
			esc_html__( 'WP Popups Tools', 'wp-popups-lite' ),
			esc_html__( 'Tools', 'wp-popups-lite' ),
			$menu_cap,
			'wppopups-tools',
			[ $this, 'admin_page' ]
		);

		// Hidden placeholder paged used for misc content.
		add_submenu_page(
			'wppopups-settings',
			esc_html__( 'popups', 'wp-popups-lite' ),
			esc_html__( 'Info', 'wp-popups-lite' ),
			$menu_cap,
			'wppopups-page',
			[ $this, 'admin_page' ]
		);

		// Addons submenu page.
		add_submenu_page(
			'wppopups-overview',
			esc_html__( 'WP Popups Addons', 'wp-popups-lite' ),
			'<span style="color:#76c328">' . esc_html__( 'Addons', 'wp-popups-lite' ) . '<span>',
			$menu_cap,
			'wppopups-addons',
			[ $this, 'admin_page' ]
		);
	}

	/**
	 * Wrapper for the hook to render our custom settings pages.
	 *
	 * @since 2.0.0
	 */
	public function admin_page() {
		do_action( 'wppopups_admin_page' );
	}

	/**
	 * Add settings link to the Plugins page.
	 *
	 * @param array $links
	 *
	 * @return array $links
	 * @since 2.0.0
	 *
	 */
	public function settings_link( $links ) {

		$admin_link = add_query_arg(
			[
				'page' => 'wppopups-settings',
			],
			admin_url( 'admin.php' )
		);

		$setting_link = sprintf(
			'<a href="%s">%s</a>',
			$admin_link,
			esc_html__( 'Settings', 'wp-popups-lite' )
		);

		array_unshift( $links, $setting_link );

		return $links;
	}
}

new WPPopups_Admin_Menu();
