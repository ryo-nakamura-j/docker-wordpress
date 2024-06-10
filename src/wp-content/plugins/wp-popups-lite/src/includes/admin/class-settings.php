<?php

/**
 * Settings class.
 *
 * @package    WPPopups
 * @author     WPPopups
 * @since 2.0.0
 * @license    GPL-2.0+
 * @copyright  Copyright (c) 2016, WP Popups LLC
 */
class WPPopups_Settings {

	/**
	 * The current active tab.
	 *
	 * @since 2.0.0
	 * @var string
	 */
	public $view;

	/**
	 * Primary class constructor.
	 *
	 * @since 2.0.0
	 */
	public function __construct() {
		add_action( 'admin_init', [ $this, 'init' ] );
	}

	/**
	 * Determine if the user is viewing the settings page, if so, party on.
	 *
	 * @since 2.0.0
	 */
	public function init() {

		// Check what page we are on.
		$page = isset( $_GET['page'] ) ? sanitize_key( $_GET['page'] ) : '';

		// Only load if we are actually on the settings page.
		if ( 'wppopups-settings' === $page ) {

			// Include API callbacks and functions.
			require_once WPPOPUPS_PLUGIN_DIR . 'includes/admin/settings-api.php';

			// Watch for triggered save.
			$this->save_settings();

			// Determine the current active settings tab.
			$this->view = isset( $_GET['view'] ) ? sanitize_key( $_GET['view'] ) : 'general';

			add_action( 'admin_enqueue_scripts', [ $this, 'enqueues' ] );
			add_action( 'wppopups_admin_page', [ $this, 'output' ] );

			// Hook for addons.
			do_action( 'wppopups_settings_init' );
		}
	}

	/**
	 * Sanitize and save settings.
	 *
	 * @since 2.0.0
	 */
	public function save_settings() {

		if ( ! isset( $_POST['wppopups-settings-submit'] ) ) {
			return;
		}

		if ( ! wp_verify_nonce( $_POST['nonce'], 'wppopups-settings-nonce' ) ) {
			return;
		}

		if ( ! wppopups_current_user_can() ) {
			return;
		}

		if ( empty( $_POST['view'] ) ) {
			return;
		}

		// Get registered fields and current settings.
		$fields   = $this->get_registered_settings( $_POST['view'] );
		$settings = get_option( 'wppopups_settings', [] );

		if ( empty( $fields ) || ! is_array( $fields ) ) {
			return;
		}

		// Sanitize and prep each field.
		foreach ( $fields as $id => $field ) {

			// Certain field types are not valid for saving and are skipped.
			$exclude = apply_filters( 'wppopups_settings_exclude_type', [
				'content',
				'license',
				'premium-text',
				'providers',
			] );

			if ( empty( $field['type'] ) || in_array( $field['type'], $exclude, true ) ) {
				continue;
			}

			$value      = isset( $_POST[ $id ] ) ? $_POST[ $id ] : false;
			$value_prev = isset( $settings[ $id ] ) ? $settings[ $id ] : false;

			// Custom filter can be provided for sanitizing, otherwise use
			// defaults.
			if ( ! empty( $field['filter'] ) && function_exists( $field['filter'] ) ) {

				$value = call_user_func( $field['filter'], $value, $id, $field, $value_prev );

			} else {

				switch ( $field['type'] ) {
					case 'checkbox':
						$value = (bool) trim( $value );
						break;
					case 'image':
						$value = esc_url_raw( trim( $value ) );
						break;
					case 'color':
						$value = wppopups_sanitize_hex_color( trim( $value ) );
						break;
					case 'text':
					case 'radio':
					case 'select':
						$value = sanitize_text_field( trim( $value ) );
					default:
						$value = apply_filters('wppopups_sanitize_field_' . $field['type'], $value, $id, $field, $value_prev );
						break;
				}
			}

			// Add to settings.
			$settings[ $id ] = $value;
		}

		// Save settings.
		update_option( 'wppopups_settings', $settings );

		WPPopups_Admin_Notice::success( esc_html__( 'Settings were successfully saved.', 'wp-popups-lite' ) );
	}

	/**
	 * Enqueue assets for the settings page.
	 *
	 * @since 2.0.0
	 */
	public function enqueues() {

		$es6 = defined( 'WPP_DEBUG' ) || isset( $_GET['WPP_DEBUG'] ) ? 'es6/' : '';

		wp_enqueue_script(
			'choicesjs',
			WPPOPUPS_PLUGIN_URL . 'assets/js/' . $es6 . 'choices.min.js',
			[],
			'2.8.10',
			false
		);

		do_action( 'wppopups_settings_enqueue' );
	}

	/**
	 * Return registered settings tabs.
	 *
	 * @return array
	 * @since 2.0.0
	 */
	public function get_tabs() {

		$tabs = [
			'general' => [
				'name'   => esc_html__( 'General', 'wp-popups-lite' ),
				'form'   => true,
				'submit' => esc_html__( 'Save Settings', 'wp-popups-lite' ),
			],
			'misc' => [
				'name'   => esc_html__( 'Misc', 'wp-popups-lite' ),
				'form'   => true,
				'submit' => esc_html__( 'Save Settings', 'wp-popups-lite' ),
			],
		];

		return apply_filters( 'wppopups_settings_tabs', $tabs );
	}

	/**
	 * Output tab navigation area.
	 *
	 * @since 2.0.0
	 */
	public function tabs() {

		$tabs = $this->get_tabs();

		echo '<ul class="wppopups-admin-tabs">';
		foreach ( $tabs as $id => $tab ) {

			$active = $id === $this->view ? 'active' : '';
			$name   = $tab['name'];
			$link   = add_query_arg( 'view', $id, admin_url( 'admin.php?page=wppopups-settings' ) );
			echo '<li><a href="' . esc_url_raw( $link ) . '" class="' . esc_attr( $active ) . '">' . esc_html( $name ) . '</a></li>';
		}
		echo '</ul>';
	}

	/**
	 * Return all the default registered settings fields.
	 *
	 * @param string $view
	 *
	 * @return array
	 * @since 2.0.0
	 *
	 */
	public function get_registered_settings( $view = '' ) {

		$defaults = [
			// General Settings tab.
			'general' => [
				'license-heading'   => [
					'id'       => 'license-heading',
					'content'  => '<h4>' . esc_html__( 'License', 'wp-popups-lite' ) . '</h4><p>' . esc_html__( 'Your license key provides access to updates and addons.', 'wp-popups-lite' ) . '</p>',
					'type'     => 'content',
					'no_label' => true,
					'class'    => [ 'section-heading' ],
				],
				'license-key'       => [
					'id'   => 'license-key',
					'name' => esc_html__( 'License Key', 'wp-popups-lite' ),
					'type' => 'license',
				],
				'analytics-heading' => [
					'id'       => 'analytics-heading',
					'content'  => '<h4>' . esc_html__( 'Analytics', 'wp-popups-lite' ) . '</h4>',
					'type'     => 'content',
					'no_label' => true,
					'class'    => [ 'section-heading', 'no-desc' ],
				], // TODO premium only
				'ua-code'           => [
					'id'            => 'ua-code',
					'name'          => esc_html__( 'Google UA code', 'wp-popups-lite' ),
					'desc'          => esc_html__( 'Enter your Google UA-XXXXXX code to enable popups tracking also in Universal Google analytics.', 'wp-popups-lite' ),
					'type'          => 'text',
					'default'       => '',
					'premium_field' => ! ( wppopups()->pro ),
				],
				'm-id'           => [
					'id'            => 'm-id',
					'name'          => esc_html__( 'Google Measurement ID', 'wp-popups-lite' ),
					'desc'          => esc_html__( 'Enter your Google G-XXXXXX code to enable popups tracking also in the new Google analytics.', 'wp-popups-lite' ),
					'type'          => 'text',
					'default'       => '',
					'premium_field' => ! ( wppopups()->pro ),
				],
				'data-sampling'     => [
					'id'            => 'data-sampling',
					'name'          => esc_html__( 'Data Sampling', 'wp-popups-lite' ),
					'desc'          => sprintf(
						wp_kses(

							__( 'If your site have lot of traffic, enable data sampling by adding a number greater than 0. Check <a href="%s">the docs page</a> for more info.', 'wp-popups-lite' ),
							[
								'a' => [
									'href'   => [],
									'class'  => [],
									'target' => [],
									'rel'    => [],
								],
							]
						),
						'https://wppopups.com/docs/data-sampling'
					),
					'type'          => 'text',
					'default'       => '0',
					'premium_field' => ! ( wppopups()->pro ),
				],
			],
			// Misc. settings tab.
			'misc'    => [
				'misc-heading' => [
					'id'       => 'misc-heading',
					'content'  => '<h4>' . esc_html__( 'Misc', 'wp-popups-lite' ) . '</h4>',
					'type'     => 'content',
					'no_label' => true,
					'class'    => [ 'section-heading', 'no-desc' ],
				],
				'aff-link'     => [
					'id'      => 'aff-link',
					'name'    => esc_html__( 'Affiliate link', 'wp-popups-lite' ),
					'desc'    => sprintf(
						wp_kses(
						/* translators: %s - WPPopups.com upgrade URL. */
							__( 'You can earn money by promoting the plugin! Join our <a href="%s">affiliate program</a> and paste your affiliate link here to earn 35 percent in commissions. Once entered, it will replace the default "Powered by" on the popups.', 'wp-popups-lite' ),
							[
								'a' => [
									'href'   => [],
									'class'  => [],
									'target' => [],
									'rel'    => [],
								],
							]
						),
						'https://timersys.com/affiliates/'
					),
					'type'    => 'text',
					'default' => '',
				],
				'uninstall'    => [
					'id'      => 'uninstall',
					'name'    => esc_html__( 'Delete all data on Uninstall', 'wp-popups-lite' ),
					'desc'    => esc_html__( 'When you uninstall the plugin all popups, settings and stats will be deleted from your db', 'wp-popups-lite' ),
					'type'    => 'checkbox',
					'default' => '',
				],
			],
		];
		$defaults = apply_filters( 'wppopups_settings_defaults', $defaults );

		return empty( $view ) ? $defaults : $defaults[ $view ];
	}

	/**
	 * Return array containing markup for all the appropriate settings fields.
	 *
	 * @param string $view
	 *
	 * @return array
	 * @since 2.0.0
	 *
	 */
	public function get_settings_fields( $view = '' ) {

		$fields   = [];
		$settings = $this->get_registered_settings( $view );

		foreach ( $settings as $id => $args ) {

			$fields[ $id ] = wppopups_settings_output_field( $args );
		}

		return apply_filters( 'wppopups_settings_fields', $fields, $view );
	}

	/**
	 * Build the output for the plugin settings page.
	 *
	 * @since 2.0.0
	 */
	public function output() {

		$tabs   = $this->get_tabs();
		$fields = $this->get_settings_fields( $this->view );
		?>

		<div id="wppopups-settings" class="wrap wppopups-admin-wrap">

			<?php $this->tabs(); ?>

			<h1 class="wppopups-h1-placeholder"></h1>

			<?php
			if ( wppopups()->pro && class_exists( 'WPPopups_License' ) ) {
				wppopups()->license->notices( true );
			}
			?>

			<div class="wppopups-admin-content wppopups-admin-settings">

				<?php
				// Some tabs rely on AJAX and do not contain a form, such as Integrations.
				if ( ! empty( $tabs[ $this->view ]['form'] ) ) :
				?>
				<form class="wppopups-admin-settings-form" method="post">
					<input type="hidden" name="action" value="update-settings">
					<input type="hidden" name="view" value="<?php echo esc_attr( $this->view ); ?>">
					<input type="hidden" name="nonce"
					       value="<?php echo wp_create_nonce( 'wppopups-settings-nonce' ); ?>">
					<?php endif; ?>

					<?php do_action( 'wppopups_admin_settings_before', $this->view, $fields ); ?>

					<?php
					foreach ( $fields as $field ) {
						echo $field;
					}
					?>

					<?php if ( ! empty( $tabs[ $this->view ]['submit'] ) ) : ?>
						<p class="submit">
							<button type="submit" class="wppopups-btn wppopups-btn-md wppopups-btn-blue"
							        name="wppopups-settings-submit"><?php echo $tabs[ $this->view ]['submit']; ?></button>
						</p>
					<?php endif; ?>

					<?php do_action( 'wppopups_admin_settings_after', $this->view, $fields ); ?>

					<?php if ( ! empty( $tabs[ $this->view ]['form'] ) ) : ?>
				</form>
			<?php endif; ?>

			</div>

		</div>

		<?php
	}

}

new WPPopups_Settings();
