<?php

/**
 * WPPopups Lite. Load Lite specific features/functionality.
 *
 * @since 1.2.0
 *
 * @package WPPopups
 */
class WPPopups_Lite {

	/**
	 * Primary class constructor.
	 *
	 * @since 1.2.x
	 */
	public function __construct() {

		$this->includes();

		add_action( 'wppopups_setup_panel_after', [ $this, 'new_popup_cta' ] );
		add_action( 'wppopups_builder_panel_buttons', [ $this, 'popup_panels' ], 20 );
		add_action( 'wppopups_builder_enqueues_before', [ $this, 'builder_enqueues' ] );

		add_action( 'admin_enqueue_scripts', [ $this, 'addons_page_enqueues' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'providers_page_enqueues' ] );
		add_action( 'wppopups_admin_page', [ $this, 'addons_page' ] );

		add_action( 'wppopups_sidebar_content_position', [ $this, 'position_cta' ] );
		add_action( 'wppopups_sidebar_content_animation', [ $this, 'animation_cta' ] );
		add_action( 'wppopups_sidebar_content_close', [ $this, 'closing_cta' ] );

		add_action( 'wppopups_popup_rules_panel_content', [ $this, 'rules_cta' ] );
		add_action( 'wppopups_popup_settings_triggers', [ $this, 'trigger_cta' ] );
		add_action( 'wppopups_popup_settings_close', [ $this, 'close_cta' ] );

		// Settings
		add_filter( 'wppopups_settings_tabs', [ $this, 'register_settings_tabs' ], 5, 1 );
		add_filter( 'wppopups_settings_defaults', [ $this, 'register_settings_fields' ], 5, 1 );
	}

	/**
	 * Include files.
	 *
	 * @since 1.0.0
	 */
	private function includes() {
	}


	/**
	 * Display/register additional templates available in the Pro version.
	 *
	 * @since 1.0.6
	 */
	public function new_popup_cta() {
		?>
		<div class="wppopups-setup-title">
			<?php esc_html_e( 'Optin Form Templates', 'wp-popups-lite' ); ?>
			<a href="<?php echo wppopups_admin_upgrade_link(); ?>" target="_blank" rel="noopener noreferrer"
			   class="btn-green wppopups-upgrade-link wppopups-upgrade-modal"
			   style="text-transform: uppercase;font-size: 13px;font-weight: 700;padding: 5px 10px;vertical-align: text-bottom;">
				<?php esc_html_e( 'Upgrade', 'wp-popups-lite' ); ?>
			</a>
		</div>
		<p class="wppopups-setup-desc">
			<?php esc_html_e( 'While WPPopups Lite allows you to create any type of popup, Premium version comes with integration to the most popular emails providers such as Mailchimp, Aweber, Mailpoet, Posmatic, Constant Contact, GetResponse, ActiveCampaign, Mailerlite, The newsletter plugin, Newsman, etc ', 'wp-popups-lite' ); ?>
		</p><?php
	}


	/**
	 * Display/register additional panels available in the Pro version.
	 *
	 * @since 1.0.0
	 */
	public function popup_panels() {

		$providers = wppopups_get_providers_available();

		if( ! empty( $providers ) )
			return;

		?>
		<button class="wppopups-panel-email-marketing-button upgrade-modal" data-panel="email-marketing">
			<i class="fa fa-bullhorn"></i><span><?php esc_html_e( 'Email Marketing', 'wp-popups-lite' ); ?></span>
		</button>
		<?php
	}

	/**
	 * Load assets for lite version with the admin builder.
	 *
	 * @since 1.0.0
	 */
	public function builder_enqueues() {

		wppopups_wp_hooks();

		$es6 = defined( 'WPP_DEBUG' ) ? 'es6/' : '';

		wp_enqueue_script(
			'wppopups-builder-lite',
			WPPOPUPS_PLUGIN_LITE_URL . 'assets/js/' . $es6 . 'admin-builder-lite.js',
			[ 'jquery', 'jquery-confirm' ],
			WPPOPUPS_VERSION,
			false
		);

		wp_localize_script(
			'wppopups-builder-lite',
			'wppopups_builder_lite',
			[
				'upgrade_title'   => esc_html__( 'is a PRO Feature', 'wp-popups-lite' ),
				'upgrade_message' => esc_html__( 'We\'re sorry, %name% is not available on your plan.<br><br>Please upgrade to the PRO plan to unlock all these awesome features.', 'wp-popups-lite' ),
				'upgrade_button'  => esc_html__( 'Upgrade to PRO', 'wp-popups-lite' ),
				'upgrade_url'     => wppopups_admin_upgrade_link(),
				'upgrade_modal'   => wppopups_get_upgrade_modal_text(),
			]
		);
	}

	/**
	 * Add appropriate scripts to providers page.
	 *
	 * @since 1.0.4
	 */
	public function providers_page_enqueues() {
		if ( wppopups_is_admin_page() && isset( $_GET['view'] ) && 'integrations' == $_GET['view'] ) {

			wp_enqueue_script(
				'wppopups-postmessage',
				WPPOPUPS_PLUGIN_URL . 'assets/js/jquery.postmessage.min.js',
				[ 'jquery' ],
				WPPOPUPS_VERSION,
				false
			);
		}
	}


	/**
	 * Add appropriate styling to addons page.
	 *
	 * @since 1.0.4
	 */
	public function addons_page_enqueues() {

		if ( ! isset( $_GET['page'] ) || 'wppopups-addons' !== $_GET['page'] ) {
			return;
		}

		// JavaScript.
		wp_enqueue_script(
			'jquery-matchheight',
			WPPOPUPS_PLUGIN_URL . 'assets/js/jquery.matchHeight-min.js',
			[ 'jquery' ],
			'0.7.0',
			false
		);

		wp_enqueue_script(
			'listjs',
			WPPOPUPS_PLUGIN_URL . 'assets/js/list.min.js',
			[ 'jquery' ],
			'1.5.0'
		);
	}

	/**
	 * Notify user that addons are a pro feature.
	 *
	 * @since 1.0.0
	 */
	public function addons_page() {

		if ( ! isset( $_GET['page'] ) || 'wppopups-addons' !== $_GET['page'] ) {
			return;
		}

		$upgrade = wppopups_admin_upgrade_link();
		$addons  = [
			[
				'name' => 'Optin Forms',
				'desc' => 'WP Popups premium comes with <strong>powerful optin form templates</strong> that integrates with the most popular email marketing providers such as Mailchimp, Aweber, Mailpoet, Posmatic, Constant Contact, GetResponse, ActiveCampaign, Mailerlite, The newsletter plugin, Newsman, etc .',
				'icon' => 'addon-icon-mailchimp.png',
			],
			[
				'name' => 'Analytics',
				'desc' => 'Measure all your popups views and conversion inside the plugin or in your Google Analytics account ',
				'icon' => 'addon-icon-analytics.png',
			],
			[
				'name' => '8 Animation Effects',
				'desc' => 'WP Popups premium adds new animations effects that will rock your users experience. ',
				'icon' => 'addon-icon-animation.gif',
			],
			[
				'name' => 'Premium popup positions',
				'desc' => 'Premium version includes new popup positions like Top/Bottom Bar , after post content ,full screen mode or sticky popups!',
				'icon' => 'addon-icon-post-submissions.png',
			],
			[
				'name' => 'A/B testing',
				'desc' => 'You can create as many popup variations as you need. With the built in analytics you can check which one perform better before deciding which is the best option to gain more subscribers..',
				'icon' => 'addon-icon-ab.png',
			],
			[
				'name' => 'Exit intention trigger and more',
				'desc' => 'Detect if user is about to leave the website and show a popup to capture the lead before they leave. More trigger methods, such as element visible in vieport, etc',
				'icon' => 'addon-icon-popup-abandonment.png',
			],
			[
				'name' => 'Geolocation',
				'desc' => 'WP Popups Geolocation allows you to show popups based on country, state or city of the user.',
				'icon' => 'addon-icon-geolocation.png',
			],
			[
				'name' => 'Premium filters',
				'desc' => 'With the advanced display rules you can target popups to almost anything. You can use multiple rules at the same time to get more control. Day and time rules, after N visited pages and much more',
				'icon' => 'addon-icon-filters.png',
			],
			[
				'name' => 'Advanced close functions',
				'desc' => 'Advanced closing methods: Control if you want users to be able to close the popup and how can they do it. Timer: You can specify how long the popup will remain open until it close itself.',
				'icon' => 'addon-icon-timer.png',
			],

		];
		?>

		<div id="wppopups-admin-addons" class="wrap wppopups-admin-wrap">
			<h1 class="page-title">
				<?php esc_html_e( 'WP Popups Addons', 'wp-popups-lite' ); ?>
				<input type="search" placeholder="<?php esc_html_e( 'Search Addons', 'wp-popups-lite' ); ?>"
				       id="wppopups-admin-addons-search">
			</h1>
			<div class="notice notice-info" style="display: block;">
				<h3><strong><?php esc_html_e( 'Upgrade to get the most of WP Popups', 'wp-popups-lite' ); ?></strong></h3>
				<p><?php esc_html_e( 'Please upgrade to the PRO plan to unlock these awesome features. Or Browse all the addons that can be purchased for WP Popups lite. ', 'wp-popups-lite' ); ?></p>
				<p>
					<a href="<?php echo $upgrade; ?>" class="wppopups-btn wppopups-btn-blue wppopups-btn-md"
					   rel="noopener noreferrer">
						<?php esc_html_e( 'Upgrade Now', 'wp-popups-lite' ); ?>
					</a>
					<a href="https://wppopups.com/features/" class="wppopups-btn wppopups-btn-blue wppopups-btn-md"
					   rel="noopener noreferrer">
						<?php esc_html_e( 'Browse Addons', 'wp-popups-lite' ); ?>
					</a>
				</p>
			</div>
			<div class="wppopups-admin-content">
				<div class="addons-container" id="wppopups-admin-addons-list">
					<div class="list">
						<?php foreach ( $addons as $addon ) : $addon = (array) $addon; ?>
							<div class="addon-container">
								<div class="addon-item">
									<div class="details wppopups-clear" style="">
										<img src="<?php echo WPPOPUPS_PLUGIN_LITE_URL; ?>assets/images/<?php echo $addon['icon']; ?>">
										<h5 class="addon-name">
											<?php
											printf(
											/* translators: %s - addon name*/
												esc_html__( '%s', 'wp-popups-lite' ),
												$addon['name']
											);
											?>
										</h5>
										<p class="addon-desc"><?php echo $addon['desc']; ?></p>
									</div>
									<div class="actions wppopups-clear">
										<div class="upgrade-button">
											<a href="<?php echo $upgrade; ?>" target="_blank" rel="noopener noreferrer"
											   class="wppopups-btn wppopups-btn-light-grey wppopups-upgrade-modal">
												<?php esc_html_e( 'Upgrade Now', 'wp-popups-lite' ); ?>
											</a>
										</div>
									</div>
								</div>
							</div>
						<?php endforeach; ?>
					</div>
				</div>
			</div>
		</div>

		<?php
	}

	/**
	 * Premium positions
	 */
	public function position_cta() {
		$this->update_cta(
			esc_html( 'Premium positions', 'wp-popups-lite' ),
			esc_html( 'With Wp Popups premium you can have full screen popups, sticky popups, insert optin forms inside yours posts and much more!', 'wp-popups-lite' )
		);
	}

	/**
	 * Premium animation
	 */
	public function animation_cta() {
		$this->update_cta(
			esc_html( 'New Animations', 'wp-popups-lite' ),
			esc_html( 'Get +40 new animations that will rock your users experience.', 'wp-popups-lite' )
		);
	}

	/**
	 * Premium close
	 */
	public function closing_cta() {
		$this->update_cta(
			esc_html( 'Advanced close methods', 'wp-popups-lite' ),
			esc_html( 'Do you need to disable close methods so popup remains active until user clicks on it.? Or you prefer the popup to close automatically after a few secods? All of these is possible with premium version.', 'wp-popups-lite' )
		);
	}

	/**
	 * Premium rules
	 */
	public function rules_cta() {
		$this->update_cta(
			esc_html( 'Advanced display rules', 'wp-popups-lite' ),
			esc_html( 'Do you need more display rules? Premium version includes several new display rules such as :', 'wp-popups-lite' ),
			[
				esc_html('Show popup at certain time', 'wp-popups-lite' ),
				esc_html('Show popup on certain days. Eg: All mondays', 'wp-popups-lite' ),
				esc_html('Show popup on certain date. Eg: show if date greater than 2017/10/10', 'wp-popups-lite' ),
				esc_html('Show after N(numbers) of pages viewed', 'wp-popups-lite' ),
				esc_html('Show/hide if another popup already converted', 'wp-popups-lite' ),
			]
		);
	}
	/**
	 * Premium triggers
	 */
	public function trigger_cta() {
		$this->update_cta(
			esc_html( 'More trigger methods', 'wp-popups-lite' ),
			esc_html( 'Get more trigger methods such as the popup "Exit intent", that will trigger popup when user leaves the page or another when a defined element of your page (an image, link, etc) becomes visible in the viewport', 'wp-popups-lite' )
		);
	}
	/**
	 * Premium close methods
	 */
	public function close_cta() {
		$this->update_cta(
			esc_html( 'Advanced closing methods', 'wp-popups-lite' ),
			esc_html( 'Update to get more features such as disabling the close button or automatically close the popup after X seconds.', 'wp-popups-lite' )
		);
	}

	/**
	 * CTA templates to place in admin builder
	 *
	 * @param $title
	 * @param $desc
	 * @param array $list
	 */
	private function update_cta( $title, $desc, $list = [] ) {
		?>
		<div class="wppopups-cta-title">
			<a href="<?php echo wppopups_admin_upgrade_link(); ?>" target="_blank" rel="noopener noreferrer"
			   class="btn-green wppopups-upgrade-link wppopups-upgrade-modal"
			   style="text-transform: uppercase;font-size: 13px;font-weight: 700;padding: 5px 10px;vertical-align: text-bottom;">
				<?php esc_html_e( 'Upgrade', 'wp-popups-lite' ); ?>
			</a>
			<?php echo $title; ?>
		</div>
		<p class="wppopups-cta-desc">
		<?php echo $desc; ?>
		</p><?php
		if( !empty( $list ) ) {
			foreach ( $list as $item ) {
				echo '<li>' . $item . '</li>';
			}
		}
	}


	/**
	 * Register settings tabs.
	 *
	 * @param array $tabs Admin area tabs list.
	 *
	 * @return array
	 * @since 2.0.0
	 *
	 */
	public function register_settings_tabs( $tabs ) {

		$providers = wppopups_get_providers_available();

		if( empty( $providers ) )
			return $tabs;

		// Add integrations tab.
		$integrations = [
			'integrations' => [
				'name'   => esc_html__( 'Integrations', 'wppopups-pro' ),
				'form'   => true,
				'submit' => esc_html__( 'Save Settings', 'wppopups-pro' ),
			],
		];

		$tabs = wppopups_array_insert( $tabs, $integrations, 'misc', 'before' );

		return $tabs;
	}


	/**
	 * Register settings fields.
	 *
	 * @param array $settings Admin area settings list.
	 *
	 * @return array
	 * @since 2.0.0
	 *
	 */
	public function register_settings_fields( $settings ) {

		$providers = wppopups_get_providers_available();

		if( empty( $providers ) )
			return $settings;

		$settings['integrations'] = [
			'integrations-heading'   => [
				'id'       => 'integrations-heading',
				'content'  => '<h4>' . esc_html__( 'Integrations', 'wppopups-pro' ) . '</h4><p>' . esc_html__( 'Manage integrations with popular providers such as Constant Contact, MailChimp, Zapier, and more.', 'wppopups-pro' ) . '</p>',
				'type'     => 'content',
				'no_label' => true,
				'class'    => [ 'section-heading' ],
			],
			'integrations-providers' => [
				'id'      => 'integrations-providers',
				'content' => '<h4>' . esc_html__( 'Integrations', 'wppopups-pro' ) . '</h4><p>' . esc_html__( 'Manage integrations with popular providers such as Constant Contact, MailChimp, Zapier, and more.', 'wppopups-pro' ) . '</p>',
				'type'    => 'providers',
				'wrap'    => 'none',
			],
		];

		return $settings;
	}

}

new WPPopups_Lite();
