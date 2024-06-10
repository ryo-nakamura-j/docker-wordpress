<?php

/**
 * Addons class.
 *
 * @package    WPPopups
 * @author     WPPopups
 * @since 2.0.0
 * @license    GPL-2.0+
 * @copyright  Copyright (c) 2016, WP Popups LLC
 */
class WPPopups_Addons {

	const SLUG = 'wppopups-addons';

	/**
	 * WP Popups addons
	 *
	 * @since 2.0.0
	 * @var array
	 */
	public $addons;

	/**
	 * Primary class constructor.
	 *
	 * @since 2.0.0
	 */
	public function __construct() {

		// Maybe load addons page.
		add_action( 'admin_init', [ $this, 'init' ] );
	}

	/**
	 * Determine if the user is viewing the settings page, if so, party on.
	 *
	 * @since 2.0.0
	 */
	public function init() {

		// Check what page we are on.
		$page = isset( $_GET['page'] ) ? $_GET['page'] : '';

		// Only load if we are actually on the settings page.
		if ( self::SLUG === $page ) {

			add_action( 'admin_enqueue_scripts', [ $this, 'enqueues' ] );
			add_action( 'wppopups_admin_page', [ $this, 'output' ] );
		}
	}

	/**
	 * Enqueue assets for the addons page.
	 *
	 * @since 2.0.0
	 */
	public function enqueues() {

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
	 * Build the output for the plugin addons page.
	 *
	 * @since 2.0.0
	 */
	public function output() {

		$refresh    = isset( $_GET['wppopups_refresh_addons'] );
		$errors     = [];
		$type       = 'none';
		if ( isset( wppopups()->license ) ) {
			$errors       = wppopups()->license->get_errors();
			$type         = wppopups()->license->type();
			$this->addons = wppopups()->license->addons( $refresh );
		}
		if ( ! isset( $this->addons ) ) {
			$this->get_addons();
		}
		?>

		<div id="wppopups-admin-addons" class="wrap wppopups-admin-wrap">

			<h1 class="page-title">
				<?php esc_html_e( 'WP Popups Addons', 'wppopups-pro' ); ?>
				<a href="<?php echo esc_url_raw( add_query_arg( [ 'wppopups_refresh_addons' => '1' ] ) ); ?>" class="add-new-h2 wppopups-btn-blue">
					<?php esc_html_e( 'Refresh Addons', 'wppopups-pro' ); ?>
				</a>
				<input type="search" placeholder="<?php esc_attr_e( 'Search Addons', 'wppopups-pro' ); ?>" id="wppopups-admin-addons-search">
			</h1>

			<?php if ( empty( $this->addons ) ) : ?>

				<div class="error notice">
					<p><?php esc_html_e( 'There was an issue retrieving Addons for this site. Please click on the button above to refresh.', 'wppopups-pro' ); ?></p>
				</div>

			<?php elseif ( ! empty( $errors ) ) : ?>

				<div class="error notice">
					<p><?php esc_html_e( 'In order to get access to Addons, you need to resolve your license key errors.', 'wppopups-pro' ); ?></p>
				</div>

			<?php elseif ( empty( $type ) ) : ?>

				<div class="error notice">
					<p><?php esc_html_e( 'In order to get access to Addons, you need to verify your license key for WPPopups.', 'wppopups-pro' ); ?></p>
				</div>

			<?php else : ?>

				<?php if ( $refresh ) : ?>

					<div class="updated notice">
						<p><?php esc_html_e( 'Addons have successfully been refreshed.', 'wppopups-pro' ); ?></p>
					</div>

				<?php
				endif;

				echo '<div class="wppopups-admin-content">';

					if ( ! $refresh ) {
						echo '<p class="intro">' .
							sprintf(
								wp_kses(
									/* translators: %s - refresh addons page URL. */
									__( 'Improve your popups with our premium addons. Missing an addon that you think you should be able to see? Click the <a href="%s">Refresh Addons</a> button above.', 'wppopups-pro' ),
									[
										'a' => [
											'href' => [],
										],
									]
								),
								esc_url_raw( add_query_arg( [ 'wppopups_refresh_addons' => '1' ] ) )
							) .
							'</p>';
					}

					echo '<h4 id="addons-heading" data-text="' . esc_attr__( 'Available Addons', 'wppopups-pro' ) . '">' . esc_html__( 'Available Addons', 'wppopups-pro' ) . '</h4>';

					echo '<div class="addons-container" id="wppopups-admin-addons-list">';

						echo '<div class="list">';

						if ( $type !== 'agency' || $type !== 'pro' ) :
							echo '<div class="unlock-msg">';
								echo '<h4>' . esc_html__( 'Unlock More Features...', 'wppopups-pro' ) . '</h4>';
								echo '<p>' .
									sprintf(
										wp_kses(
											/* translators: %s - WPPopups.com Account page URL. */
											__( 'Want to get even more features? <a href="%s" target="_blank" rel="noopener noreferrer">Upgrade your WP Popups account</a> and unlock the following extensions.', 'wppopups-pro' ),
											[
												'a' => [
													'href'   => [],
													'target' => [],
													'rel'    => [],
												],
											]
										),
										'https://wppopups.com/account/'
									) .
									'</p>';
							echo '</div>';

						endif;

						$this->addon_grid( $this->addons, $type );

						echo '</div>';

					echo '</div>';

				echo '</div>';

			endif;

			echo '</div>';
	}

	/**
	 * Renders grid of addons.
	 *
	 * @param array  $addons List of addons.
	 * @param string $type_current License type user currently have.
	 *
	 *@since 2.0.0
	 *
	 */
	public function addon_grid( $addons, $type_current ) {

		$plugins = get_plugins();

		foreach ( $addons as $id => $addon ) {

			$addon           = (array) $addon;
			$plugin_basename = $this->get_plugin_basename_from_slug( $addon['slug'], $plugins );
			$status_label    = '';
			$action_class    = 'action-button';


			if ( ! in_array( $type_current, $addon['types'], true ) ) {
				$status = 'upgrade';
			} elseif ( is_plugin_active( $plugin_basename ) ) {
				$status       = 'active';
				$status_label = esc_html__( 'Active', 'wppopups-pro' );
			} elseif ( ! isset( $plugins[ $plugin_basename ] ) ) {
				$status       = 'download';
				$status_label = esc_html__( 'Not Installed', 'wppopups-pro' );
			} elseif ( is_plugin_inactive( $plugin_basename ) ) {
				$status       = 'inactive';
				$status_label = esc_html__( 'Inactive', 'wppopups-pro' );
			} else {
				$status = 'upgrade';
			}

			$image = ! empty( $addon['image'] ) ? $addon['image'] : WPPOPUPS_PLUGIN_URL . 'assets/images/wppopups-logo.png';

			echo '<div class="addon-container">';

				echo '<div class="addon-item">';

					echo '<div class="details wppopups-clear">';
						echo '<img src="' . esc_url( $image ) . '">';
						echo '<h5 class="addon-name">' . esc_html( $addon['title'] ) . '</h5>';
						echo '<p class="addon-desc">' . esc_html( $addon['excerpt'] ). '</p>';
					echo '</div>';

					echo '<div class="actions wppopups-clear">';

						// Status.
						if ( ! empty( $status ) && 'upgrade' !== $status ) {
							echo '<div class="status">';
								echo '<strong>' .
									sprintf(
										/* translators: %s - addon status label. */
										esc_html__( 'Status: %s', 'wppopups-pro' ),
										'<span class="status-label status-' . esc_attr( $status ) . '">' . $status_label . '</span>'
									) .
									'</strong> ';
							echo '</div>';
						} else {
							$action_class = 'upgrade-button';
						}

						// Button.
						echo '<div class="' . esc_attr( $action_class ) . '">';
							if ( 'active' === $status ) {
								echo '<button class="status-' . esc_attr( $status ) . '" data-plugin="' . esc_attr( $plugin_basename ) . '" data-type="addon">';
									echo '<i class="fa fa-toggle-on" aria-hidden="true"></i>';
									esc_html_e( 'Deactivate', 'wppopups-pro' );
								echo '</button>';
							} elseif ( 'inactive' === $status ) {
								echo '<button class="status-' . esc_attr( $status ) . '" data-plugin="' . esc_attr( $plugin_basename ) . '" data-type="addon">';
									echo '<i class="fa fa-toggle-on fa-flip-horizontal" aria-hidden="true"></i>';
									esc_html_e( 'Activate', 'wppopups-pro' );
								echo '</button>';
							} elseif ( 'download' === $status ) {
								echo '<button class="status-' . esc_attr( $status ) . '" data-plugin="' . esc_url( $addon['url'] ) . '" data-type="addon">';
									echo '<i class="fa fa-cloud-download" aria-hidden="true"></i>';
									esc_html_e( 'Install Addon', 'wppopups-pro' );
								echo '</button>';
							} else {
								echo '<a href="https://wppopups.com/account/" target="_blank" rel="noopener noreferrer" class="wppopups-btn wppopups-btn-blue">' . esc_html__( 'Upgrade/Get addon', 'wppopups-pro' ) . '</a>';
							}
						echo '</div>';

					echo '</div>';

				echo '</div>';

			echo '</div>';

			if ( ! empty( $this->addons[ $id ] ) ) {
				unset( $this->addons[ $id ] );
			}
		}
	}

	/**
	 * Retrieve the plugin basename from the plugin slug.
	 *
	 * @param string $slug The plugin slug.
	 * @param array  $plugins List of plugins.
	 *
	 * @return string The plugin basename if found, else the plugin slug.
	 * @since 2.0.0
	 *
	 */
	public function get_plugin_basename_from_slug( $slug, $plugins ) {

		$keys = array_keys( $plugins );

		foreach ( $keys as $key ) {
			if ( preg_match( '|^' . $slug . '|', $key ) ) {
				return $key;
			}
		}
		return $slug;
	}

	private function get_addons() {

		$addons = get_transient( '_wppopups_addons' );

		if ( false === $addons ) {

			$addons = wppopups_perform_remote_request( 'get_addons' );

			// If there was an API error, set transient for only 10 minutes.
			if ( is_array( $addons ) && isset( $addons['error'] ) ) {
				set_transient( '_wppopups_addons', false, 10 * MINUTE_IN_SECONDS );
				return false;
			}

			// If there was an error retrieving the addons, set the error.
			if ( isset( $addons->error ) ) {
				set_transient( '_wppopups_addons', false, 10 * MINUTE_IN_SECONDS );
				return false;
			}
			// Otherwise, our request worked. Save the data and return it.
			set_transient( '_wppopups_addons', $addons, DAY_IN_SECONDS );
			return $addons;
		}

		return $addons;
	}
}

new WPPopups_Addons();
