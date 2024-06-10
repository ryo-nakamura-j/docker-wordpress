<?php
/**
 * Plugin Name:     Post Carousel
 * Plugin URI:      https://shapedplugin.com/plugin/post-carousel-pro
 * Description:     The #1 Responsive Post Carousel for WordPress to Display Posts in Carousel and much more.
 * Version:         2.1.12
 * Author:          ShapedPlugin
 * Author URI:      http://shapedplugin.com/
 * License:         GPL-2.0+
 * License URI:     http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:     post-carousel
 * Domain Path:     /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Handles core plugin hooks and action setup.
 *
 * @package post-carousel
 * @since 2.1
 */
if ( ! class_exists( 'SP_Post_Carousel' ) ) {
	class SP_Post_Carousel {
		/**
		 * Plugin version
		 *
		 * @var string
		 */
		public $version = '2.1.12';

		/**
		 * @var SP_PC_MetaBox $metabox
		 */
		public $metabox;

		/**
		 * @var SP_PC_ShortCodes $shortcode
		 */
		public $shortcode;


		/**
		 * @var SP_PC_Router $router
		 */
		public $router;

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
		 * SP_Post_Carousel constructor.
		 */
		function __construct() {
			// Define constants.
			$this->define_constants();

			// Required class file include.
			spl_autoload_register( array( $this, 'autoload' ) );

			// Include required files.
			$this->includes();

			// instantiate classes.
			$this->instantiate();

			// Initialize the filter hooks.
			$this->init_filters();

			// Initialize the action hooks.
			$this->init_actions();
		}

		/**
		 * Initialize WordPress filter hooks
		 *
		 * @return void
		 */
		function init_filters() {
			add_filter( 'plugin_action_links', array( $this, 'add_plugin_action_links' ), 10, 2 );
			add_filter( 'manage_sp_pc_shortcodes_posts_columns', array( $this, 'add_shortcode_column' ) );
			add_filter( 'plugin_row_meta', array( $this, 'after_post_carousel_row_meta' ), 10, 4 );
		}

		/**
		 * Initialize WordPress action hooks
		 *
		 * @return void
		 */
		function init_actions() {
			add_action( 'plugins_loaded', array( $this, 'load_text_domain' ) );
			add_action( 'manage_sp_pc_shortcodes_posts_custom_column', array( $this, 'add_shortcode_form' ), 10, 2 );
			add_action( 'activated_plugin', array( $this, 'redirect_help_page' ) );
		}

		/**
		 * Define constants
		 *
		 * @since 2.1
		 */
		public function define_constants() {
			$this->define( 'SP_PC_VERSION', $this->version );
			$this->define( 'SP_PC_PATH', plugin_dir_path( __FILE__ ) );
			$this->define( 'SP_PC_URL', plugin_dir_url( __FILE__ ) );
			$this->define( 'SP_PC_BASENAME', plugin_basename( __FILE__ ) );
		}

		/**
		 * Define constant if not already set
		 *
		 * @since 2.1
		 *
		 * @param  string      $name
		 * @param  string|bool $value
		 */
		public function define( $name, $value ) {
			if ( ! defined( $name ) ) {
				define( $name, $value );
			}
		}


		/**
		 * Load TextDomain for plugin.
		 *
		 * @since 2.1
		 */
		public function load_text_domain() {
			load_plugin_textdomain( 'post-carousel', false, SP_PC_PATH . '/languages' );
		}

		/**
		 * Add plugin action menu
		 *
		 * @param array  $links
		 * @param string $file
		 *
		 * @return array
		 */
		public function add_plugin_action_links( $links, $file ) {

			if ( $file == SP_PC_BASENAME ) {
				$new_links = array(
					sprintf( '<a href="%s" style="%s">%s</a>', 'https://shapedplugin.com/plugin/post-carousel-pro', 'color:red;font-weight:bold', __( 'Go Pro!', 'post-carousel' ) ),
					sprintf( '<a href="%s">%s</a>', admin_url( 'edit.php?post_type=sp_pc_shortcodes' ), __( 'Shortcode Generator', 'post-carousel' ) ),
				);

				return array_merge( $new_links, $links );
			}

			return $links;
		}

		/**
		 * Add plugin row meta link
		 *
		 * @since 2.1
		 *
		 * @param $plugin_meta
		 * @param $file
		 *
		 * @return array
		 */

		function after_post_carousel_row_meta( $plugin_meta, $file ) {
			if ( $file == SP_PC_BASENAME ) {
				$plugin_meta[] = '<a href="https://shapedplugin.com/demo/post-carousel-pro/" target="_blank">' . __( 'Live Demo', 'post-carousel' ) . '</a>';
			}

			return $plugin_meta;
		}

		/**
		 * Autoload class files on demand
		 *
		 * @param string $class requested class name
		 */
		function autoload( $class ) {
			$name = explode( '_', $class );
			if ( isset( $name[2] ) ) {
				$class_name = strtolower( $name[2] );
				$filename   = SP_PC_PATH . '/class/' . $class_name . '.php';

				if ( file_exists( $filename ) ) {
					require_once $filename;
				}
			}
		}

		/**
		 * Instantiate all the required classes
		 *
		 * @since 2.1
		 */
		function instantiate() {

			$this->metabox   = SP_PC_MetaBox::getInstance();
			$this->shortcode = SP_PC_ShortCodes::getInstance();

			do_action( 'sp_pc_instantiate', $this );
		}

		/**
		 * page router instantiate
		 *
		 * @since 2.1
		 */
		function page() {
			$this->router = SP_PC_Router::instance();

			return $this->router;
		}

		/**
		 * Include the required files
		 *
		 * @return void
		 */
		function includes() {
			$this->page()->sp_pc_function();
			$this->router->includes();
		}

		/**
		 * ShortCode Column
		 *
		 * @param $columns
		 *
		 * @return array
		 */
		function add_shortcode_column() {
			$new_columns['cb']        = '<input type="checkbox" />';
			$new_columns['title']     = __( 'Carousel Title', 'post-carousel' );
			$new_columns['shortcode'] = __( 'Shortcode', 'post-carousel' );
			$new_columns['']          = '';
			$new_columns['date']      = __( 'Date', 'post-carousel' );

			return $new_columns;
		}

		/**
		 * @param $column
		 * @param $post_id
		 */
		function add_shortcode_form( $column, $post_id ) {

			switch ( $column ) {

				case 'shortcode':
					$column_field = '<input style="width: 270px;padding: 6px;" type="text" onClick="this.select();" readonly="readonly" value="[post-carousel ' . 'id=&quot;' . $post_id . '&quot;' . ']"/>';
					echo $column_field;
					break;
				default:
					break;

			} // end switch

		}

		/**
		 * Redirect after active
		 *
		 * @param $plugin
		 */
		function redirect_help_page( $plugin ) {
			if ( $plugin == SP_PC_BASENAME ) {
				exit( wp_redirect( admin_url( 'edit.php?post_type=sp_pc_shortcodes&page=help' ) ) );
			}
		}

	}
}

/**
 * Returns the main instance.
 *
 * @since 2.1
 * @return SP_Post_Carousel
 */
function sp_post_carousel() {
	return SP_Post_Carousel::instance();
}

// sp_post_carousel instance.
sp_post_carousel();
