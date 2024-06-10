<?php

/**
 * Welcome page class.
 *
 * This page is shown when the plugin is activated.
 * TODO: welcome to new version
 * @package    WPPopups
 * @author     WPPopups
 * @since 2.0.0
 * @license    GPL-2.0+
 * @copyright  Copyright (c) 2016, WPPopups LLC
 */
class WPPopups_Welcome {

	/**
	 * Primary class constructor.
	 *
	 * @since 2.0.0
	 */
	public function __construct() {

		add_action( 'admin_menu', [ $this, 'register' ] );
		add_action( 'admin_head', [ $this, 'hide_menu' ] );
		add_action( 'admin_init', [ $this, 'redirect' ], 9999 );
	}

	/**
	 * Register the pages to be used for the Welcome screen (and tabs).
	 *
	 * These pages will be removed from the Dashboard menu, so they will
	 * not actually show. Sneaky, sneaky.
	 *
	 * @since 2.0.0
	 */
	public function register() {

		// Getting started - shows after installation.
		add_dashboard_page(
			esc_html__( 'Welcome to WPPopups', 'wp-popups-lite' ),
			esc_html__( 'Welcome to WPPopups', 'wp-popups-lite' ),
			apply_filters( 'wppopups_welcome_cap', 'manage_options' ),
			'wppopups-getting-started',
			[ $this, 'output' ]
		);
	}

	/**
	 * Removed the dashboard pages from the admin menu.
	 *
	 * This means the pages are still available to us, but hidden.
	 *
	 * @since 2.0.0
	 */
	public function hide_menu() {
		remove_submenu_page( 'index.php', 'wppopups-getting-started' );
	}

	/**
	 * Welcome screen redirect.
	 *
	 * This function checks if a new install or update has just occurred. If so,
	 * then we redirect the user to the appropriate page.
	 *
	 * @since 2.0.0
	 */
	public function redirect() {

		// Check if we should consider redirection.
		if ( ! get_transient( 'wppopups_activation_redirect' ) ) {
			return;
		}

		// If we are redirecting, clear the transient so it only happens once.
		delete_transient( 'wppopups_activation_redirect' );

		// Check option to disable welcome redirect.
		if ( get_option( 'wppopups_activation_redirect', false ) ) {
			return;
		}

		// Only do this for single site installs.
		if ( is_network_admin() || isset( $_GET['activate-multi'] ) ) {
			return;
		}

		// Check if this is an update or first install.
		$upgrade = get_option( 'wppopups_version_upgraded_from' );

		if ( ! $upgrade ) {
			// Initial install.
			wp_safe_redirect( admin_url( 'index.php?page=wppopups-getting-started' ) );
			exit;
		}
	}

	/**
	 * Getting Started screen. Shows after first install.
	 *
	 * @since 2.0.0
	 */
	public function output() {

		$class = wppopups()->pro ? 'pro' : 'lite';
		?>

		<div id="wppopups-welcome" class="<?php echo $class; ?>">

			<div class="container">

				<div class="intro">

					<div class="wppopups-logo-welcome" style="background-image: url(<?php echo 'data:image/svg+xml;base64,' . base64_encode( '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 153 151" width="153" height="151"><defs><image width="151" height="151" id="img1" href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAJcAAACXCAMAAAAvQTlLAAAAAXNSR0IB2cksfwAAAMBQTFRFAHOqAHOqAHOqAAAAAHOqAHOqAHOqAHOqAHOqAHOqAHOqAHOqAHOqAHOqAHOqAHOqAHOqAHOqAHOqAHOqAHOqAHOqAHOqAHOqAHOqAHOqAHOqAHOqAHOqAHOqAHOqAHOqAHOqL22LZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZmZm1Y1mKQAAAEB0Uk5TbIBMANj/mOk17gwj1BfhIOPdFtca5SbsMPAp5yQoMhzfcIA+aP989OsjhdQR0RLXGd/ZFRjY1h3cGx7lqLfAXWj80ucAAAE5SURBVHic7djJTgJBFEbhhouAOLSCMjiCeFVUhgbEWd7/rViYKC7ohKRM/5pz9rfy5S6qkoqiXFblLaWosJFVRVy4cOHChetPukqirvKmpquyta3psp1dTZfFe5oui/c1XVatabrs4FDTZfWGpsuaLU2XHR1ruuzkF2AhXHYa/kkK4rKz4BsL47LztqbL4sD/E52L1XXXcIXu0ld3hQsXLly4cOHChQsXLly4cOHChQsXLly4cOHChQsXLly4cOHChQsXLly4cOHChQsXLly4cOHChQsXLly4cP0rV9rkdcrk+t30fp5+K+Lyu3tNl/cHmi7vL29MyOXDkabLk2+YlMvHE02XD6eari+YmssfJpounw00XZ/XhaDLk0dNlz89a7o8edF0+eubpis1XLhw4cKFy+z9I6vmC+sAc2Bjpu3VAAAAAElFTkSuQmCC"/></defs><style>tspan { white-space:pre }</style><use id="Layer 1" href="#img1" x="1" y="0" /></svg>' );?>
		) !important;">
					</div>

					<div class="block">
						<h1><?php esc_html_e( 'Welcome to WPPopups', 'wp-popups-lite' ); ?></h1>
						<h6><?php esc_html_e( 'Thank you for choosing WP Popups - the most powerful WordPress popup builder in the market.', 'wp-popups-lite' ); ?></h6>
					</div>

					<a href="#" class="play-video"
					   title="<?php esc_attr_e( 'Watch how to create your first popup', 'wp-popups-lite' ); ?>">
						<img src="<?php echo WPPOPUPS_PLUGIN_URL; ?>assets/images/youtube-play.png"
						     alt="<?php esc_attr_e( 'Watch how to create your first popup', 'wp-popups-lite' ); ?>"
						     class="video-thumbnail">
					</a>

					<div class="block">

						<h6><?php esc_html_e( 'WP Popups makes it easy and intuitive to create a Popup. You can watch the video tutorial or read our guide on how create your first popup.', 'wp-popups-lite' ); ?></h6>

						<div class="button-wrap wppopups-clear">
							<div class="left">
								<a href="<?php echo admin_url( 'admin.php?page=wppopups-builder' ); ?>"
								   class="wppopups-btn wppopups-btn-block wppopups-btn-lg wppopups-btn-blue">
									<?php esc_html_e( 'Create Your First Popup', 'wp-popups-lite' ); ?>
								</a>
							</div>
							<div class="right">
								<a href="https://wppopups.com/docs/how-to-create-your-first-popup/?utm_source=WordPress&amp;utm_medium=link&amp;utm_campaign=welcome-page"
								   class="wppopups-btn wppopups-btn-block wppopups-btn-lg wppopups-btn-grey"
								   target="_blank" rel="noopener noreferrer">
									<?php esc_html_e( 'Read docs page', 'wp-popups-lite' ); ?>
								</a>
							</div>
						</div>

					</div>

				</div><!-- /.intro -->

				<div class="features">

					<div class="block">

						<h1><?php esc_html_e( 'WP Popups', 'wp-popups-lite' ); ?></h1>
						<h6><?php esc_html_e( 'WP Popups is the best multipurpose popup maker plugin for WordPress.', 'wp-popups-lite' ); ?></h6>

						<div class="feature-list wppopups-clear">

							<div class="feature-block first">
								<img src="<?php echo WPPOPUPS_PLUGIN_URL; ?>assets/images/icons/custom-templates.svg">
								<h5><?php esc_html_e( 'Template Builder', 'wp-popups-lite' ); ?></h5>
								<p><?php esc_html_e( 'Use a prebuilt template or create your own. Easily export them to use it in all your sites!', 'wp-popups-lite' ); ?></p>
							</div>

							<div class="feature-block last">
								<img src="<?php echo WPPOPUPS_PLUGIN_URL; ?>assets/images/icons/display-rules.svg">
								<h5><?php esc_html_e( '30+ Display rules', 'wp-popups-lite' ); ?></h5>
								<p><?php esc_html_e( 'Trigger popup based on multiple rules. There is no other Popup plugin with the same flexibility!', 'wp-popups-lite' ); ?></p>
							</div>

							<div class="feature-block first">
								<img src="<?php echo WPPOPUPS_PLUGIN_URL; ?>assets/images/icons/animations.svg">
								<h5><?php esc_html_e( '45+ Animations', 'wp-popups-lite' ); ?></h5>
								<p><?php esc_html_e( 'Animate your popup with some magic to capture your users attention.', 'wp-popups-lite' ); ?></p>
							</div>

							<div class="feature-block last">
								<img src="<?php echo WPPOPUPS_PLUGIN_URL; ?>assets/images/icons/triggers.svg">
								<h5><?php esc_html_e( 'Multiple triggers', 'wp-popups-lite' ); ?></h5>
								<p><?php esc_html_e( 'Show popup by using one more triggers combined,positions.svg like when user leaves the page, after X seconds, etc.', 'wp-popups-lite' ); ?></p>
							</div>

						</div>

						<h1><?php esc_html_e( 'Premium features', 'wp-popups-lite' ); ?></h1>
						<?php if( ! wppopups()->pro ) :?>
							<h6><?php esc_html_e( 'Upgrade to WP Popups to unlock all the magic!', 'wp-popups-lite' ); ?></h6>
						<?php endif;?>
						<div class="feature-list wppopups-clear">

							<div class="feature-block first">
								<img src="<?php echo WPPOPUPS_PLUGIN_URL; ?>assets/images/icons/ab-test.svg">
								<h5><?php esc_html_e( 'A/B Testing', 'wp-popups-lite' ); ?></h5>
								<p><?php esc_html_e( 'Create different versions, measure results and choose the best popup for your campaign.', 'wp-popups-lite' ); ?></p>
							</div>

							<div class="feature-block last">
								<img src="<?php echo WPPOPUPS_PLUGIN_URL; ?>assets/images/icons/analytics.svg">
								<h5><?php esc_html_e( 'Analytics', 'wp-popups-lite' ); ?></h5>
								<p><?php esc_html_e( 'Track conversions and impressions of your popups and integrate it with Google Analytics.', 'wp-popups-lite' ); ?></p>
							</div>

							<div class="feature-block first">
								<img src="<?php echo WPPOPUPS_PLUGIN_URL; ?>assets/images/icons/email-marketing.svg">
								<h5><?php esc_html_e( 'Email Marketing', 'wp-popups-lite' ); ?></h5>
								<p><?php esc_html_e( 'Integrates with all the popular email providers. Capture leads easily!', 'wp-popups-lite' ); ?></p>
							</div>

							<div class="feature-block last">
								<img src="<?php echo WPPOPUPS_PLUGIN_URL; ?>assets/images/icons/geolocation.svg">
								<h5><?php esc_html_e( 'Geolocation Addon', 'wp-popups-lite' ); ?></h5>
								<p><?php esc_html_e( 'With the geolocation addon you can show the popup just to geotargeted users of your choice.', 'wp-popups-lite' ); ?></p>
							</div>

							<div class="feature-block first">
								<img src="<?php echo WPPOPUPS_PLUGIN_URL; ?>assets/images/icons/age-verification.svg">
								<h5><?php esc_html_e( 'Age Verification Addon', 'wp-popups-lite' ); ?></h5>
								<p><?php esc_html_e( 'Create an Age Verification Popup to ask for user\'s age before seeing content.', 'wp-popups-lite' ); ?></p>
							</div>

							<div class="feature-block last">
								<img src="<?php echo WPPOPUPS_PLUGIN_URL; ?>assets/images/icons/idle-logout.svg">
								<h5><?php esc_html_e( 'Idle Logout Addon', 'wp-popups-lite' ); ?></h5>
								<p><?php esc_html_e( 'Log out your users after inactivity time, but give them a chance to continue logged by showing a popup.', 'wp-popups-lite' ); ?></p>
							</div>

							<div class="feature-block first">
								<img src="<?php echo WPPOPUPS_PLUGIN_URL; ?>assets/images/icons/leaving-notice.svg">
								<h5><?php esc_html_e( 'Leaving Notice Addon', 'wp-popups-lite' ); ?></h5>
								<p><?php esc_html_e( 'Show a warning to users when they click on external links before they leave your site.', 'wp-popups-lite' ); ?></p>
							</div>

							<div class="feature-block last">
								<img src="<?php echo WPPOPUPS_PLUGIN_URL; ?>assets/images/icons/login-registration.svg">
								<h5><?php esc_html_e( 'AJAX Login/Registration Addon', 'wp-popups-lite' ); ?></h5>
								<p><?php esc_html_e( 'Convert your popup into a login and registration form powered by ajax.', 'wp-popups-lite' ); ?></p>
							</div>
						</div>

						<div class="button-wrap">
							<a href="https://wppopups.com/features/?utm_source=WordPress&amp;utm_medium=link&amp;utm_campaign=liteplugin"
							   class="wppopups-btn wppopups-btn-lg wppopups-btn-grey" rel="noopener noreferrer"
							   target="_blank">
								<?php esc_html_e( 'See All Features', 'wp-popups-lite' ); ?>
							</a>
						</div>

					</div>

				</div>
				<?php if( ! wppopups()->pro ) :?>
					<div class="upgrade-cta upgrade">

					<div class="block wppopups-clear upgrade-welcome-cta">

						<div class="left">
							<h2><?php esc_html_e( 'Upgrade to PRO', 'wp-popups-lite' ); ?></h2>
							<ul>
								<li>
									<span class="dashicons dashicons-yes"></span> <?php esc_html_e( 'Analytics', 'wp-popups-lite' ); ?>
								</li>
								<li>
									<span class="dashicons dashicons-yes"></span> <?php esc_html_e( 'Exit intent', 'wp-popups-lite' ); ?>
								</li>
								<li>
									<span class="dashicons dashicons-yes"></span> <?php esc_html_e( 'Optin templates', 'wp-popups-lite' ); ?>
								</li>
								<li>
									<span class="dashicons dashicons-yes"></span> <?php esc_html_e( 'A/B Testing', 'wp-popups-lite' ); ?>
								</li>
								<li>
									<span class="dashicons dashicons-yes"></span> <?php esc_html_e( 'Email Marketing', 'wp-popups-lite' ); ?>
								</li>
								<li>
									<span class="dashicons dashicons-yes"></span> <?php esc_html_e( 'More Animations', 'wp-popups-lite' ); ?>
								</li>
								<li>
									<span class="dashicons dashicons-yes"></span> <?php esc_html_e( 'More positions', 'wp-popups-lite' ); ?>
								</li>
								<li>
									<span class="dashicons dashicons-yes"></span> <?php esc_html_e( 'Geolocation', 'wp-popups-lite' ); ?>
								</li>
								<li>
									<span class="dashicons dashicons-yes"></span> <?php esc_html_e( 'More triggers', 'wp-popups-lite' ); ?>
								</li>
								<li>
									<span class="dashicons dashicons-yes"></span> <?php esc_html_e( 'Multiple addons', 'wp-popups-lite' ); ?>
								</li>
							</ul>
						</div>

						<div class="right">
							<a href="<?php echo wppopups_admin_upgrade_link(); ?>" rel="noopener noreferrer"
							   target="_blank"
							   class="wppopups-btn wppopups-btn-block wppopups-btn-lg wppopups-btn-blue wppopups-upgrade-modal">
								<?php esc_html_e( 'Upgrade Now', 'wp-popups-lite' ); ?>
							</a>
						</div>

					</div>

				</div>
				<?php endif;?>

				<div class="footer">

					<div class="block wppopups-clear">

						<div class="button-wrap wppopups-clear">
							<div class="left">
								<a href="<?php echo admin_url( 'admin.php?page=wppopups-builder' ); ?>"
								   class="wppopups-btn wppopups-btn-block wppopups-btn-lg wppopups-btn-blue">
									<?php esc_html_e( 'Create Your First Popup', 'wp-popups-lite' ); ?>
								</a>
							</div>
							<div class="right">
								<?php if( ! wppopups()->pro ) :?>
									<a href="<?php echo wppopups_admin_upgrade_link(); ?>" target="_blank"
									   rel="noopener noreferrer"
									   class="wppopups-btn wppopups-btn-block wppopups-btn-lg wppopups-btn-trans-blue wppopups-upgrade-modal">
										<span class="underline">
											<?php esc_html_e( 'Upgrade to WP Popups Pro', 'wp-popups-lite' ); ?> <span
													class="dashicons dashicons-arrow-right"></span>
										</span>
									</a>
								<?php else: ?>
									<a href="https://wppopups.com/docs/how-to-create-your-first-popup/?utm_source=WordPress&amp;utm_medium=link&amp;utm_campaign=welcome-page"
									   class="wppopups-btn wppopups-btn-block wppopups-btn-lg wppopups-btn-grey"
									   target="_blank" rel="noopener noreferrer">
										<?php esc_html_e( 'Read docs page', 'wp-popups-lite' ); ?>
									</a>
								<?php endif; ?>
							</div>
						</div>

					</div>

				</div><!-- /.footer -->

			</div><!-- /.container -->

		</div><!-- /#wppopups-welcome -->
		<?php
	}
}

new WPPopups_Welcome();
