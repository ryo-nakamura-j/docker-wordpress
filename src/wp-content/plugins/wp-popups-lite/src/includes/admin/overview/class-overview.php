<?php

/**
 * Primary overview page inside the admin which lists all popups.
 *
 * @package    WPPopups
 * @author     WPPopups
 * @since 2.0.0
 * @license    GPL-2.0+
 * @copyright  Copyright (c) 2016, WP Popups LLC
 */
class WPPopups_Overview {

	/**
	 * Primary class constructor.
	 *
	 * @since 2.0.0
	 */
	public function __construct() {

		// Maybe load overview page.
		add_action( 'admin_init', [ $this, 'init' ] );

		// Setup screen options.
		add_action( 'load-toplevel_page_wppopups-overview', [ $this, 'screen_options' ] );
		add_filter( 'set-screen-option', [ $this, 'screen_options_set' ], 10, 3 );
	}

	/**
	 * Determine if the user is viewing the overview page.
	 *
	 * @since 2.0.0
	 */
	public function init() {

		// Check what page we are on.
		$page = isset( $_GET['page'] ) ? sanitize_key( $_GET['page'] ) : '';

		// Only load if we are actually on the overview page.
		if ( 'wppopups-overview' === $page ) {

			if ( ! class_exists( 'WP_List_Table' ) ) {
				require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
			}

			// Load the class that builds the overview table.
			require_once WPPOPUPS_PLUGIN_DIR . 'includes/admin/overview/class-overview-table.php';

			// Preview page check.
			wppopups()->preview->popup_preview_check();

			add_action( 'admin_enqueue_scripts', [ $this, 'enqueues' ] );
			add_action( 'wppopups_admin_page', [ $this, 'output' ] );

			// Provide hook for addons.
			do_action( 'wppopups_overview_init' );
		}
	}

	/**
	 * Add per-page screen option to the Popups table.
	 *
	 * @since 2.0.0
	 */
	public function screen_options() {

		$screen = get_current_screen();

		if ( 'toplevel_page_wppopups-overview' !== $screen->id ) {
			return;
		}

		add_screen_option(
			'per_page',
			[
				'label'   => esc_html__( 'Number of popups per page:', 'wp-popups-lite' ),
				'option'  => 'wppopups_popups_per_page',
				'default' => apply_filters( 'wppopups_overview_per_page', 20 ),
			]
		);
	}

	/**
	 * Popups table per-page screen option value.
	 *
	 * @param mixed $status
	 * @param string $option
	 * @param mixed $value
	 *
	 * @return mixed
	 * @since 2.0.0
	 *
	 */
	public function screen_options_set( $status, $option, $value ) {

		if ( 'wppopups_popups_per_page' === $option ) {
			return $value;
		}

		return $status;
	}

	/**
	 * Enqueue assets for the overview page.
	 *
	 * @since 2.0.0
	 */
	public function enqueues() {

		// Hook for addons.
		do_action( 'wppopups_overview_enqueue' );
	}

	/**
	 * Build the output for the overview page.
	 *
	 * @since 2.0.0
	 */
	public function output() {

		?>
		<div id="wppopups-overview" class="wrap wppopups-admin-wrap">

			<h1 class="page-title">
				<?php esc_html_e( 'WP Popups', 'wp-popups-lite' ); ?>
				<a href="<?php echo admin_url( 'admin.php?page=wppopups-builder&view=setup' ); ?>"
				   class="add-new-h2 wppopups-btn-blue">
					<?php esc_html_e( 'Add New', 'wp-popups-lite' ); ?>
				</a>
			</h1>

			<?php
			$overview_table = apply_filters( 'wppopups_overview_table', new WPPopups_Overview_Table() );
			$overview_table->prepare_items();
			?>

			<div class="wppopups-admin-content">

				<form id="wppopups-overview-table" method="get"
				      action="<?php echo admin_url( 'admin.php?page=wppopups-overview' ); ?>">

					<input type="hidden" name="post_type" value="wppopups"/>

					<input type="hidden" name="page" value="wppopups-overview"/>

					<?php $overview_table->views(); ?>
					<?php $overview_table->display(); ?>

				</form>

			</div>

		</div>
		<?php
	}
}

new WPPopups_Overview();
