<?php

/**
 * License key fun.
 *
 * @package    WPPopups
 * @author     WPPopups
 * @since 2.0.0
 * @license    GPL-2.0+
 * @copyright  Copyright (c) 2016, WP Popups LLC
 */
class WPPopups_License {

	/**
	 * Holds any license error messages.
	 *
	 * @since 2.0.0
	 * @var array
	 */
	public $errors = [];

	/**
	 * Holds any license success messages.
	 *
	 * @since 2.0.0
	 * @var array
	 */
	public $success = [];

	/**
	 * Primary class constructor.
	 *
	 * @since 2.0.0
	 */
	public function __construct() {

		// Admin notices.
		if ( wppopups()->pro && ( ! isset( $_GET['page'] ) || 'wppopups-settings' !== $_GET['page'] ) ) {
			add_action( 'admin_notices', [ $this, 'notices' ] );
		}

		// Periodic background license check.
		if ( $this->get() ) {
			add_action( 'init', [ $this, 'maybe_validate_key' ] );
		}
	}

	/**
	 * Load the license key.
	 *
	 * @since 2.0.0
	 */
	public function get() {

		// Check for license key.
		$key = wppopups_setting( 'key', false, 'wppopups_license' );

		// Allow wp-config constant to pass key.
		if ( ! $key && defined( 'WPPOPUPS_LICENSE_KEY' ) ) {
			$key = WPPOPUPS_LICENSE_KEY;
		}

		return $key;
	}

	/**
	 * Load the license key level.
	 *
	 * @since 2.0.0
	 */
	public function type() {

		$type = wppopups_setting( 'type', false, 'wppopups_license' );

		return $type;
	}

	/**
	 * Verifies a license key entered by the user.
	 *
	 * @param string $key
	 * @param string $option_name
	 * @param int $item_id
	 * @param bool $ajax
	 * @param bool $forced Force to set contextual messages (false by default).
	 *
	 * @return bool
	 * @since 2.0.0
	 */
	public function verify_key( $key = '', $option_name = 'wppopups_license', $item_id = 0, $ajax = false, $forced = false ) {

		if ( empty( $key ) ) {
			return false;
		}
		$method = 'check_license';
		if ( $forced ) {
			$method = 'activate_license';
		}
		// this is a plan
		if ( 0 === $item_id ) {
			$item_id = '999999';
		}
		// Perform a request to verify the key.
		$verify = wppopups_perform_remote_request(
			$method,
			[
				'license'  => $key,
				'is_addon' => $option_name === 'wppopups_license' ? false : true,
				'item_id'  => $item_id,
			]
		);

		// If it returns false, send back a generic error message and return.
		if ( is_array( $verify ) && isset( $verify['error'] ) ) {
			$msg = esc_html__( 'There was an error connecting to the remote key API. Please try again later.', 'wppopups-pro' ) . esc_html( $verify['msg'] );
			if ( $ajax ) {
				wp_send_json_error( $msg );
			} else {
				$this->errors[] = $msg;
				return false;
			}
		}

		// If an error is returned, set the error and return.
		if ( empty( $verify->license ) || $verify->license == 'invalid' || $verify->license == 'invalid_item_id' ) {
			$msg = esc_html__( "The provided license it's not valid", 'wppopups-pro' );

			if ( ! empty( $verify->error ) ) {
				switch ( $verify->error ) {
					case 'expired' :
						$msg = sprintf(
							esc_html__( 'Your license key expired on %s.', 'wppopups-pro' ),
							date_i18n( get_option( 'date_format' ), strtotime( $verify->expires, current_time( 'timestamp' ) ) )
						);
						break;
					case 'revoked' :
					case 'disabled' :
						$msg = esc_html__( 'Your license key has been disabled.', 'wppopups-pro' );
						break;
					case 'missing' :
						$msg = esc_html__( 'Invalid license.', 'wppopups-pro' );
						break;
					case 'invalid' :
					case 'site_inactive' :
						$msg = esc_html__( 'Your license is not active for this URL.', 'wppopups-pro' );
						break;
					case 'item_name_mismatch' :
						$msg = sprintf( esc_html__( 'This appears to be an invalid license key for %s.', 'wppopups-pro' ), 'WPPopups' );
						break;
					case 'no_activations_left':
						$msg = esc_html__( 'Your license key has reached its activation limit.', 'wppopups-pro' );
						break;
					case 'invalid_item_id':
						$msg = esc_html__( 'Your license key is not valid for this product.', 'wppopups-pro' );
						break;
					default :
						$msg = sprintf( esc_html__( 'An error occurred, please try again.(%s)', 'wppopups-pro' ), $verify->error );
						break;
				}
			}
			if ( $ajax ) {
				wp_send_json_error( $msg );
			} else {
				$this->errors[] = $msg;
			}
		}

		$option = (array) get_option( $option_name, [] );

		// If the license is disabled, set the transient and disabled flag and return.
		if ( $verify->license == 'disabled' || ! empty( $verify->error ) || $verify->license == 'invalid' || $verify->license == 'invalid_item_id' ) {
			$option['is_expired']  = false;
			$option['is_disabled'] = true;
			$option['type'] = '';
			$option['key'] = '';
			$option['is_invalid']  = false;
			update_option( $option_name, $option );
			if ( $ajax ) {
				wp_send_json_error( esc_html__( 'Your license key for WP Popups has been disabled. Please use a different key to continue receiving automatic updates.', 'wppopups-pro' ) );
			}

			return;
		}
		$success = esc_html__( 'Congratulations! This site is now receiving automatic updates.', 'wppopups-pro' );

		// Otherwise, our request has been done successfully. Update the option and set the success message.
		$option['key']         = $key;
		$option['type']        = isset( $verify->license_limit ) ? $this->license_type( $verify->license_limit ) : 'basic';
		$option['is_expired']  = false;
		$option['is_disabled'] = false;
		$option['is_invalid']  = false;
		$this->success[]       = $success;
		update_option( $option_name, $option );
		delete_transient( '_wppopups_addons' );

		delete_site_transient( 'update_plugins' );
		wp_cache_delete( 'plugins', 'plugins' );

		if ( $ajax ) {
			wp_send_json_success(
				[
					'msg' => $success,
				]
			);
		}
	}

	/**
	 * Maybe validates a license key entered by the user.
	 *
	 * @return void Return early if the transient has not expired yet.
	 * @throws Exception
	 * @since 2.0.0
	 */
	public function maybe_validate_key() {

		$key = $this->get();

		if ( ! $key ) {
			return;
		}

		// Perform a request to validate the key  - Only run every 12 hours.
		$timestamp = get_option( 'wppopups_license_updates' );

		if ( ! $timestamp ) {
			$timestamp = strtotime( '+24 hours' );
			update_option( 'wppopups_license_updates', $timestamp );
			$this->verify_key( $key );
		} else {
			$current_timestamp = time();
			if ( $current_timestamp < $timestamp ) {
				return;
			} else {
				update_option( 'wppopups_license_updates', strtotime( '+24 hours' ) );
				$this->verify_key( $key );
			}
		}
	}

	/**
	 * Deactivates a license key entered by the user.
	 *
	 * @param bool $ajax
	 *
	 * @param string $option_name
	 *
	 * @since 2.0.0
	 */
	public function deactivate_key( $ajax = false, $option_name = 'wppopups_license' ) {

		$key = $this->get();

		if ( ! $key ) {
			return;
		}

		// Perform a request to deactivate the key.
		$deactivate = wppopups_perform_remote_request( 'deactivate_license', [ 'license' => $key ] );

		// If it returns false, send back a generic error message and return.
		if ( is_array( $deactivate ) && isset( $deactivate['error'] ) ) {
			$msg = esc_html__( 'There was an error connecting to the remote key API. Please try again later.', 'wppopups-pro' ) . esc_html( $deactivate['msg'] );
			if ( $ajax ) {
				wp_send_json_error( $msg );
			} else {
				$this->errors[] = $msg;

				return;
			}
		}

		// If an error is returned, set the error and return.
		if ( ! empty( $deactivate->error ) ) {
			if ( $ajax ) {
				wp_send_json_error( $deactivate->error );
			} else {
				$this->errors[] = $deactivate->error;

				return;
			}
		}

		// Otherwise, our request has been done successfully. Reset the option and set the success message.
		$success         = esc_html__( 'You have deactivated the key from this site successfully.', 'wppopups-pro' );
		$this->success[] = $success;
		update_option( $option_name, '' );
		delete_transient( '_wppopups_addons' );

		if ( $ajax ) {
			wp_send_json_success( $success );
		}
	}

	/**
	 * Returns possible license key error flag.
	 *
	 * @return bool True if there are license key errors, false otherwise.
	 * @since 2.0.0
	 */
	public function get_errors() {

		$option = get_option( 'wppopups_license' );

		return ! empty( $option['is_expired'] ) || ! empty( $option['is_disabled'] ) || ! empty( $option['is_invalid'] );
	}

	/**
	 * Outputs any notices generated by the class.
	 *
	 * @param bool $below_h2
	 *
	 * @since 2.0.0
	 *
	 */
	public function notices( $below_h2 = false ) {

		// Grab the option and output any nag dealing with license keys.
		$key      = $this->get();
		$option   = get_option( 'wppopups_license' );
		$below_h2 = $below_h2 ? 'below-h2' : '';

		// If there is no license key, output nag about ensuring key is set for automatic updates.
		if ( ! $key ) :
			?>
			<div class="notice notice-info <?php echo $below_h2; ?> wppopups-license-notice">
				<p>
					<?php
					printf(
						wp_kses(
						/* translators: %s - plugin settings page URL. */
							__( 'Please <a href="%s">enter and activate</a> your license key for WP Popups to enable automatic updates.', 'wppopups-pro' ),
							[
								'a' => [
									'href' => [],
								],
							]
						),
						esc_url( add_query_arg( [ 'page' => 'wppopups-settings' ], admin_url( 'admin.php' ) ) )
					);
					?>
				</p>
			</div>
		<?php
		endif;

		// If a key has expired, output nag about renewing the key.
		if ( isset( $option['is_expired'] ) && $option['is_expired'] ) :
			?>
			<div class="error notice <?php echo $below_h2; ?> wppopups-license-notice">
				<p>
					<?php
					printf(
						wp_kses(
						/* translators: %s - WPPopups.com login page URL. */
							__( 'Your license key for WP Popups has expired. <a href="%s" target="_blank" rel="noopener noreferrer">Please click here to renew your license key and continue receiving automatic updates.</a>', 'wppopups-pro' ),
							[
								'a' => [
									'href'   => [],
									'target' => [],
									'rel'    => [],
								],
							]
						),
						'https://wppopups.com/account/'
					);
					?>
				</p>
			</div>
		<?php
		endif;

		// If a key has been disabled, output nag about using another key.
		if ( isset( $option['is_disabled'] ) && $option['is_disabled'] ) :
			?>
			<div class="error notice <?php echo $below_h2; ?> wppopups-license-notice">
				<p><?php esc_html_e( 'Your license key for WP Popups has been disabled. Please use a different key to continue receiving automatic updates.', 'wppopups-pro' ); ?></p>
			</div>
		<?php
		endif;

		// If a key is invalid, output nag about using another key.
		if ( isset( $option['is_invalid'] ) && $option['is_invalid'] ) :
			?>
			<div class="error notice <?php echo $below_h2; ?> wppopups-license-notice">
				<p><?php esc_html_e( 'Your license key for WP Popups is invalid. The key no longer exists or the user associated with the key has been deleted. Please use a different key to continue receiving automatic updates.', 'wppopups-pro' ); ?></p>
			</div>
		<?php
		endif;

		// If there are any license errors, output them now.
		if ( ! empty( $this->errors ) ) :
			?>
			<div class="error notice <?php echo $below_h2; ?> wppopups-license-notice">
				<p><?php echo implode( '<br>', $this->errors ); ?></p>
			</div>
		<?php
		endif;

		// If there are any success messages, output them now.
		if ( ! empty( $this->success ) ) :
			?>
			<div class="updated notice <?php echo $below_h2; ?> wppopups-license-notice">
				<p><?php echo implode( '<br>', $this->success ); ?></p>
			</div>
		<?php
		endif;

	}

	/**
	 * Retrieves addons from the stored transient or remote server.
	 *
	 * @param bool $force
	 *
	 * @return array|bool|mixed 2.0.0
	 */
	public function addons( $force = false ) {

		$key = $this->get();

		if ( ! $key ) {
			return false;
		}

		$addons = get_transient( '_wppopups_addons' );

		if ( $force || false === $addons ) {
			$addons = $this->get_addons();
		}

		return $addons;
	}

	/**
	 * Pings the remote server for addons data.
	 *
	 * @return bool|array False if no key or failure, array of addon data otherwise.
	 * @since 2.0.0
	 *
	 */
	public function get_addons() {

		$key    = $this->get();
		$addons = wppopups_perform_remote_request( 'get_addons', [ 'license' => $key ] );

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


	/**
	 * Checks to see if the site is using an active license.
	 *
	 * @return bool
	 * @since 2.0.0
	 *
	 */
	public function is_active() {

		$license = get_option( 'wppopups_license', false );

		if (
			empty( $license ) ||
			! empty( $license['is_expired'] ) ||
			! empty( $license['is_disabled'] ) ||
			! empty( $license['is_invalid'] )
		) {
			return false;
		}

		return true;
	}

	/**
	 * @param $activation_limit
	 *
	 * @return string
	 */
	private function license_type( $activation_limit ) {

		switch ( $activation_limit ) {
			case 0:
				return 'agency';
			case 5:
				return 'plus';
			case 10:
				return 'pro';
			case 1:
				return 'basic';
		}
	}
}
