<?php
/**
 * Plugin Name: WP Popups Lite
 * Plugin URI:  https://wppopups.com
 * Description: Beginner friendly WordPress popup builder plugin.
 * Author:      timersys
 * Author URI:  https://timersys.com
 * Version:     2.1.5.1
 * Text Domain: wp-popups-lite
 * Domain Path: languages
 *
 * * WP Popups is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 *
 * WP Popups is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with WPPopups. If not, see <http://www.gnu.org/licenses/>.
 *
 * @license    GPL-2.0+
 * @copyright  Copyright (c) 2019, Timersys LLC
 *
 * Many of the code of this plugin was originally Written by WPForms plugin https://wordpress.org/plugins/wpforms-lite/
 * Below is their copyright notice
 *
 * WPForms is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 *
 * WPForms is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with WPForms. If not, see <http://www.gnu.org/licenses/>.
 *
 * @license    GPL-2.0+
 * @copyright  Copyright (c) 2016, WPForms LLC
 *
 * @package    WPPopups
 * @author     WPPopups
 * @since 2.0.0
 * @license    GPL-2.0+
 * @copyright  Copyright (c) 2016, WP Popups LLC
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// deactivate legacy version if present
if ( defined( 'SPU_PLUGIN_HOOK' ) ) {
	/**
	 * Deactivate if WP Popups already activated.
	 *
	 * @since 2.0.0.4
	 */
	function wppopups_deactivate_legacy() {

		deactivate_plugins( 'popups/popups.php' );
		if ( defined( 'SPUP_PLUGIN_HOOK' ) ) {
			deactivate_plugins( 'popups-premium/popups-premium.php' );
		}
	}

	add_action( 'admin_init', 'wppopups_deactivate_legacy' );

	/**
	 * Display notice after deactivation.
	 *
	 * @since 2.0.0.4
	 */
	function wppopups_legacy_notice() {

		echo '<div class="notice notice-error"><p>' . esc_html__( 'Legacy Popups has been automatically deactivated', 'wp-popups-lite' ) . '</p></div>';

	}

	add_action( 'admin_notices', 'wppopups_legacy_notice' );
}

// Don't allow multiple versions to be active
if ( class_exists( 'WPPopups' ) ) {

	/**
	 * Deactivate if WP Popups already activated.
	 *
	 * @since 2.0.0
	 */
	function wppopups_deactivate() {

		deactivate_plugins( plugin_basename( __FILE__ ) );
	}

	add_action( 'admin_init', 'wppopups_deactivate' );

	/**
	 * Display notice after deactivation.
	 *
	 * @since 2.0.0
	 */
	function wppopups_lite_notice() {

		echo '<div class="notice notice-warning"><p>' . esc_html__( 'Please deactivate WP Popups Lite before activating WPPopups.', 'wp-popups-lite' ) . '</p></div>';

		if ( isset( $_GET['activate'] ) ) {
			unset( $_GET['activate'] );
		}
	}

	add_action( 'admin_notices', 'wppopups_lite_notice' );

} else {

	/**
	 * Main WPPopups class.
	 *
	 * @since 2.0.0
	 *
	 * @package WPPopups
	 */
	final class WPPopups {

		/**
		 * One is the loneliest number that you'll ever do.
		 *
		 * @since 2.0.0
		 *
		 * @var WPPopups
		 */
		private static $instance;

		/**
		 * Plugin version for enqueueing, etc.
		 *
		 * @since 2.0.0
		 *
		 * @var string
		 */
		public $version = '2.1.5.1';

		/**
		 * The Popup handler instance.
		 *
		 * @since 2.0.0
		 *
		 * @var WPPopups_Popup_Handler
		 */
		public $popups;

		/**
		 * The Popups printer instance.
		 *
		 * @since 2.0.0
		 *
		 * @var WPPopups_Printer
		 */
		public $printer;

		/**
		 * The Logging instance.
		 *
		 * @since 2.0.0
		 *
		 * @var WPPopups_Logging
		 */
		public $logs;

		/**
		 * The Preview instance.
		 *
		 * @since 2.0.0
		 *
		 * @var WPPopups_Preview
		 */
		public $preview;

		/**
		 * The License class instance (Pro).
		 *
		 * @since 2.0.0
		 *
		 * @var WPPopups_License
		 */
		public $license;

		/**
		 * The Stats class instance (Pro).
		 *
		 * @since 2.0.0
		 *
		 * @var WPPopups_Stats
		 */
		public $stats;

		/**
		 * Paid returns true, free (Lite) returns false.
		 *
		 * @since 2.0.0
		 *
		 * @var boolean
		 */
		public $pro = false;

		/**
		 * Main WPPopups Instance.
		 *
		 * Insures that only one instance of WPPopups exists in memory at any one
		 * time. Also prevents needing to define globals all over the place.
		 *
		 * @since 2.0.0
		 *
		 * @return WPPopups
		 */
		public static function instance() {

			if ( ! isset( self::$instance ) && ! ( self::$instance instanceof WPPopups ) ) {

				self::$instance = new WPPopups();
				self::$instance->constants();
				self::$instance->includes();

				// Load Pro or Lite specific files.
				if ( self::$instance->pro ) {
					require_once plugin_dir_path( __FILE__ )  . 'pro/wppopups-pro.php';
				} else {
					require_once plugin_dir_path( __FILE__ )  . 'lite/wppopups-lite.php';
				}

				add_action( 'plugins_loaded', [ self::$instance, 'load_textdomain' ], 10 );
				add_action( 'plugins_loaded', [ self::$instance, 'objects' ], 10 );
			}

			return self::$instance;
		}

		/**
		 * Setup plugin constants.
		 *
		 * @since 2.0.0
		 */
		private function constants() {

			// Plugin version.
			if ( ! defined( 'WPPOPUPS_VERSION' ) ) {
				define( 'WPPOPUPS_VERSION', $this->version );
			}
			// Plugin hook.
			if ( ! defined( 'WPPOPUPS_HOOK' ) ) {
				define( 'WPPOPUPS_HOOK', plugin_basename( __FILE__ ) );
			}

			// Plugin Folder Path.
			if ( ! defined( 'WPPOPUPS_PLUGIN_DIR' ) ) {
				define( 'WPPOPUPS_PLUGIN_DIR', plugin_dir_path( __FILE__ ) . 'src/' );
			}

			// Plugin Folder URL.
			if ( ! defined( 'WPPOPUPS_PLUGIN_URL' ) ) {
				define( 'WPPOPUPS_PLUGIN_URL', plugin_dir_url( __FILE__ ) . 'src/' );
			}
			// Plugin lite URL.
			if ( ! defined( 'WPPOPUPS_PLUGIN_LITE_URL' ) ) {
				define( 'WPPOPUPS_PLUGIN_LITE_URL', plugin_dir_url( __FILE__ ) . 'lite/' );
			}

			// Plugin Root File.
			if ( ! defined( 'WPPOPUPS_PLUGIN_FILE' ) ) {
				define( 'WPPOPUPS_PLUGIN_FILE', __FILE__ );
			}

			// Plugin Slug - Determine plugin type and set slug accordingly.
			if ( file_exists( plugin_dir_path( __FILE__ ) . 'pro/wppopups-pro.php' ) ) {
				$this->pro = true;
				define( 'WPPOPUPS_PLUGIN_SLUG', 'wppopups' );
			} else {
				define( 'WPPOPUPS_PLUGIN_SLUG', 'wppopups-lite' );
			}
			// Plugin Updater API.
			if ( ! defined( 'WPPOPUPS_UPDATER_API' ) ) {
				define( 'WPPOPUPS_UPDATER_API', 'https://wppopups.com/' );
			}
		}

		/**
		 * Loads the plugin language files.
		 *
		 * @since 2.0.0
		 */
		public function load_textdomain() {

			load_plugin_textdomain( 'wp-popups-lite', false, WPPOPUPS_PLUGIN_DIR. '/languages/' );
		}

		/**
		 * Include files.
		 *
		 * @since 2.0.0
		 */
		private function includes() {
			//vendor
			require_once WPPOPUPS_PLUGIN_DIR . 'vendor/autoload.php';

			// Global includes.
			require_once WPPOPUPS_PLUGIN_DIR . 'includes/functions.php';
			require_once WPPOPUPS_PLUGIN_DIR . 'includes/class-printer.php';
			require_once WPPOPUPS_PLUGIN_DIR . 'includes/class-popup-handler.php';
			require_once WPPOPUPS_PLUGIN_DIR . 'includes/class-popup.php';
			require_once WPPOPUPS_PLUGIN_DIR . 'includes/class-preview.php';
			require_once WPPOPUPS_PLUGIN_DIR . 'includes/class-templates.php';
			require_once WPPOPUPS_PLUGIN_DIR . 'includes/class-rules.php';
			require_once WPPOPUPS_PLUGIN_DIR . 'includes/class-logging.php';
			require_once WPPOPUPS_PLUGIN_DIR . 'includes/class-install.php';
			require_once WPPOPUPS_PLUGIN_DIR . 'includes/class-fields.php';
			require_once WPPOPUPS_PLUGIN_DIR . 'includes/addons/class-base.php';
			require_once WPPOPUPS_PLUGIN_DIR . 'includes/providers/class-base.php';
			require_once WPPOPUPS_PLUGIN_DIR . 'includes/providers/class-optin-fields.php';
			require_once WPPOPUPS_PLUGIN_DIR . 'includes/providers/class-optin-submission.php';
			require_once WPPOPUPS_PLUGIN_DIR . 'includes/admin/class-license.php';

			// Admin/Dashboard only includes.
			if ( is_admin() ) {
				require_once WPPOPUPS_PLUGIN_DIR . 'includes/admin/class-updater.php';
				require_once WPPOPUPS_PLUGIN_DIR . 'includes/admin/common.php';
				require_once WPPOPUPS_PLUGIN_DIR . 'includes/admin/builder/class-builder.php';
				require_once WPPOPUPS_PLUGIN_DIR . 'includes/admin/builder/functions.php';
				require_once WPPOPUPS_PLUGIN_DIR . 'includes/admin/overview/class-overview.php';
				require_once WPPOPUPS_PLUGIN_DIR . 'includes/admin/class-notices.php';
				require_once WPPOPUPS_PLUGIN_DIR . 'includes/admin/ajax-actions.php';
				require_once WPPOPUPS_PLUGIN_DIR . 'includes/admin/class-menu.php';
				require_once WPPOPUPS_PLUGIN_DIR . 'includes/admin/class-review.php';
				require_once WPPOPUPS_PLUGIN_DIR . 'includes/admin/class-tools.php';
				require_once WPPOPUPS_PLUGIN_DIR . 'includes/admin/class-welcome.php';
				require_once WPPOPUPS_PLUGIN_DIR . 'includes/admin/class-triggers.php';
				require_once WPPOPUPS_PLUGIN_DIR . 'includes/admin/class-settings.php';
			}
		}

		/**
		 * Setup objects.
		 *
		 * @since 2.0.0
		 */
		public function objects() {

			// Global objects.
			$this->popups   = new WPPopups_Popup_Handler();
			$this->printer  = new WPPopups_Printer();
			$this->preview  = new WPPopups_Preview();
			$this->logs     = new WPPopups_Logging;
			$this->license  = new WPPopups_License();

			
			// All Loaded
			do_action( 'wppopups_loaded' );
		}
	}

	/**
	 * The function which returns the one WPPopups instance.
	 *
	 * @since 2.0.0
	 *
	 * @return WPPopups
	 */
	function wppopups() {

		return WPPopups::instance();
	}

	wppopups();

}
