<?php
/**
 * Global admin related function.
 */

/**
 * Queries the remote URL via wp_remote_post and returns a json decoded response.
 *
 * @param string $action The name of the $_POST action var.
 * @param array $body The content to retrieve from the remote URL.
 *
 * @return string|array Json decoded response on success, false on failure.
 * @since 2.0.0
 *
 */
function wppopups_perform_remote_request( $action, $body = [] ) {

	// Data to send to the API
	$api_params = [
		'edd_action' => $action,
		'item_id'    => '406'
	];

	$api_params = array_merge( $api_params, $body);

	// Call the API
	$response = wp_remote_get( add_query_arg( $api_params, WPPOPUPS_UPDATER_API ) , apply_filters( 'wppopups_remote_request_args', [] ) );

	// Perform the query and retrieve the response.
	$response_code = wp_remote_retrieve_response_code( $response );
	$response_body = wp_remote_retrieve_body( $response );

	// Bail out early if there are any errors.
	if ( is_wp_error( $response ) || 200 != $response_code ) {
		$error = 'Response code: ' . $response_code;
		if ( is_wp_error( $response ) ) {
			$error = $response->get_error_message();
		}
		return [ 'error' => true, 'msg' => $error ];
	}

	// Return the json decoded content.
	return json_decode( $response_body );
}

/**
 * Helper function to determine if viewing an WP Popups related admin page.
 *
 * @return boolean
 */
function wppopups_is_admin_page() {

	if ( ! is_admin() || empty( $_REQUEST['page'] ) || strpos( $_REQUEST['page'], 'wppopups' ) === false || 'wppopups-builder' === $_REQUEST['page'] ) {
		return false;
	}

	return true;
}


/**
 * Load styles for all WPPopups-related admin screens.
 */
function wppopups_admin_styles() {

	if ( ! wppopups_is_admin_page() ) {
		return;
	}

	// jQuery confirm.
	wp_enqueue_style(
		'jquery-confirm',
		WPPOPUPS_PLUGIN_URL . 'assets/css/jquery-confirm.min.css',
		[],
		'3.3.2'
	);

	// Spectrum (color picker).
	wp_enqueue_style(
		'spectrum',
		WPPOPUPS_PLUGIN_URL . 'assets/css/spectrum.min.css',
		null,
		'2.3.1'
	);

	// FontAwesome.
	wp_enqueue_style(
		'wppopups-font-awesome',
		WPPOPUPS_PLUGIN_URL . 'assets/css/font-awesome.min.css',
		null,
		'4.4.0'
	);
	// choices.
	wp_enqueue_style(
		'wppopups-choices',
		WPPOPUPS_PLUGIN_URL . 'assets/css/choices.min.css',
		null,
		WPPOPUPS_VERSION
	);
	// Main admin styles.
	wp_enqueue_style(
		'wppopups-admin',
		WPPOPUPS_PLUGIN_URL . 'assets/css/admin.css',
		[],
		WPPOPUPS_VERSION
	);
}

add_action( 'admin_enqueue_scripts', 'wppopups_admin_styles' );

/**
 * Load scripts for all WPPopups-related admin screens.
 *
 * @since 2.0.0
 */
function wppopups_admin_scripts() {

	if ( ! wppopups_is_admin_page() ) {
		return;
	}

	wp_enqueue_media();

	// jQuery confirm.
	wp_enqueue_script(
		'jquery-confirm',
		WPPOPUPS_PLUGIN_URL . 'assets/js/jquery.jquery-confirm.min.js',
		[ 'jquery' ],
		'3.3.2',
		false
	);

	// Minicolors (color picker).
	wp_enqueue_script(
		'spectrum',
		WPPOPUPS_PLUGIN_URL . 'assets/js/spectrum.min.js',
		[ 'jquery' ],
		'2.3.1'
	);

	// Choices.js.
	wp_enqueue_script(
		'choicesjs',
		WPPOPUPS_PLUGIN_URL . 'assets/js/choices.min.js',
		[],
		'2.8.10',
		false
	);

	$es6 = defined( 'WPP_DEBUG' ) || isset( $_GET['WPP_DEBUG'] ) ? 'es6/' : '';

	// Main admin script.
	wp_enqueue_script(
		'wppopups-admin',
		WPPOPUPS_PLUGIN_URL . 'assets/js/' . $es6 . 'admin.js',
		[ 'jquery' ],
		WPPOPUPS_VERSION,
		false
	);

	$strings = [
		'addon_activate'          => esc_html__( 'Activate', 'wp-popups-lite' ),
		'addon_activated'         => esc_html__( 'Activated', 'wp-popups-lite' ),
		'addon_active'            => esc_html__( 'Active', 'wp-popups-lite' ),
		'addon_deactivate'        => esc_html__( 'Deactivate', 'wp-popups-lite' ),
		'addon_inactive'          => esc_html__( 'Inactive', 'wp-popups-lite' ),
		'addon_install'           => esc_html__( 'Install Addon', 'wp-popups-lite' ),
		'addon_error'             => esc_html__( 'Could not install addon. Please download from wpforms.com and install manually.', 'wp-popups-lite' ),
		'plugin_error'            => esc_html__( 'Could not install a plugin. Please download from WordPress.org and install manually.', 'wp-popups-lite' ),
		'addon_search'            => esc_html__( 'Searching Addons', 'wp-popups-lite' ),
		'ajax_url'                => admin_url( 'admin-ajax.php' ),
		'cancel'                  => esc_html__( 'Cancel', 'wp-popups-lite' ),
		'close'                   => esc_html__( 'Close', 'wp-popups-lite' ),
		'popup_delete_confirm'    => esc_html__( 'Are you sure you want to delete this popup?', 'wp-popups-lite' ),
		'popup_duplicate_confirm' => esc_html__( 'Are you sure you want to duplicate this popup?', 'wp-popups-lite' ),
		'heads_up'                => esc_html__( 'Heads up!', 'wp-popups-lite' ),
		'isPro'                   => wppopups()->pro,
		'nonce'                   => wp_create_nonce( 'wppopups-admin' ),
		'ok'                      => esc_html__( 'OK', 'wp-popups-lite' ),
		'testing'                 => esc_html__( 'Testing', 'wp-popups-lite' ),
		'upload_image_title'      => esc_html__( 'Upload or Choose Your Image', 'wp-popups-lite' ),
		'upload_image_button'     => esc_html__( 'Use Image', 'wp-popups-lite' ),
		'upgrade_modal'           => wppopups_get_upgrade_modal_text(),
	];
	$strings = apply_filters( 'wppopups_admin_strings', $strings );

	wp_localize_script(
		'wppopups-admin',
		'wppopups_admin',
		$strings
	);
}

add_action( 'admin_enqueue_scripts', 'wppopups_admin_scripts' );

/**
 * Add body class to WP Popups admin pages for easy reference.
 *
 * @param string $classes
 *
 * @return string
 * @since 2.0.0
 *
 */
function wppopups_admin_body_class( $classes ) {

	if ( ! wppopups_is_admin_page() ) {
		return $classes;
	}

	return "$classes wppopups-admin-page";
}

add_filter( 'admin_body_class', 'wppopups_admin_body_class', 10, 1 );

/**
 * Outputs the WP Popups admin header.
 *
 * @since 2.0.0
 */
function wppopups_admin_header() {

	// Bail if we're not on a WP Popups screen or page (also exclude popup builder).
	if ( ! wppopups_is_admin_page() ) {
		return;
	}

	// Omit header from Welcome activation screen.
	if ( 'wppopups-getting-started' === $_REQUEST['page'] ) {
		return;
	}
	?>
	<div id="wppopups-header-temp"></div>
	<div id="wppopups-header" class="wppopups-header">
		<img class="wppopups-header-logo" src="<?php echo WPPOPUPS_PLUGIN_URL; ?>assets/images/wppopups-logo.png"
		     alt="WP Popups Logo"/>
	</div>
	<?php
}

add_action( 'in_admin_header', 'wppopups_admin_header', 100 );

/**
 * Remove non-WP Popups notices from WP Popups pages.
 *
 * @since 2.0.0
 */
function wppopups_admin_hide_unrelated_notices() {

	// Bail if we're not on a WP Popups screen or page.
	if ( empty( $_REQUEST['page'] ) || strpos( $_REQUEST['page'], 'popups' ) === false ) {
		return;
	}

	global $wp_filter;

	if ( ! empty( $wp_filter['user_admin_notices']->callbacks ) && is_array( $wp_filter['user_admin_notices']->callbacks ) ) {
		foreach ( $wp_filter['user_admin_notices']->callbacks as $priority => $hooks ) {
			foreach ( $hooks as $name => $arr ) {
				if ( is_object( $arr['function'] ) && $arr['function'] instanceof Closure ) {
					unset( $wp_filter['user_admin_notices']->callbacks[ $priority ][ $name ] );
					continue;
				}
				if ( ! empty( $arr['function'][0] ) && is_object( $arr['function'][0] ) && strpos( strtolower( get_class( $arr['function'][0] ) ), 'popups' ) !== false ) {
					continue;
				}
				if ( ! empty( $name ) && strpos( $name, 'popups' ) === false ) {
					unset( $wp_filter['user_admin_notices']->callbacks[ $priority ][ $name ] );
				}
			}
		}
	}

	if ( ! empty( $wp_filter['admin_notices']->callbacks ) && is_array( $wp_filter['admin_notices']->callbacks ) ) {
		foreach ( $wp_filter['admin_notices']->callbacks as $priority => $hooks ) {
			foreach ( $hooks as $name => $arr ) {
				if ( is_object( $arr['function'] ) && $arr['function'] instanceof Closure ) {
					unset( $wp_filter['admin_notices']->callbacks[ $priority ][ $name ] );
					continue;
				}
				if ( ! empty( $arr['function'][0] ) && is_object( $arr['function'][0] ) && strpos( strtolower( get_class( $arr['function'][0] ) ), 'popups' ) !== false ) {
					continue;
				}
				if ( ! empty( $name ) && strpos( $name, 'popups' ) === false ) {
					unset( $wp_filter['admin_notices']->callbacks[ $priority ][ $name ] );
				}
			}
		}
	}

	if ( ! empty( $wp_filter['all_admin_notices']->callbacks ) && is_array( $wp_filter['all_admin_notices']->callbacks ) ) {
		foreach ( $wp_filter['all_admin_notices']->callbacks as $priority => $hooks ) {
			foreach ( $hooks as $name => $arr ) {
				if ( is_object( $arr['function'] ) && $arr['function'] instanceof Closure ) {
					unset( $wp_filter['all_admin_notices']->callbacks[ $priority ][ $name ] );
					continue;
				}
				if ( ! empty( $arr['function'][0] ) && is_object( $arr['function'][0] ) && strpos( strtolower( get_class( $arr['function'][0] ) ), 'popups' ) !== false ) {
					continue;
				}
				if ( ! empty( $name ) && strpos( $name, 'popups' ) === false ) {
					unset( $wp_filter['all_admin_notices']->callbacks[ $priority ][ $name ] );
				}
			}
		}
	}
}

add_action( 'admin_print_scripts', 'wppopups_admin_hide_unrelated_notices' );

/**
 * Upgrade link used within the various admin pages.
 */
function wppopups_admin_upgrade_link() {

	// Check if there's a constant.
	$aff_url = '';
	if ( defined( 'WPPOPUPS_AFF_URL' ) ) {
		$aff_url = WPPOPUPS_AFF_URL;
	}

	$aff_url = apply_filters( 'wppopups_aff_url', $aff_url );

	// If at this point we still don't have
	// Just return the standard upgrade URL.
	if ( empty( $aff_url ) ) {
		return 'https://wppopups.com/pricing/?discount=LITEUPGRADE&amp;utm_source=WordPress&amp;utm_medium=link&amp;utm_campaign=liteplugin';
	}

	return esc_url( $aff_url );
}

/**
 * Check the current PHP version and display a notice if on unsupported PHP.
 *
 * @since 2.0.0
 */
function wppopups_check_php_version() {

	// Display for PHP below 5.4.
	if ( version_compare( PHP_VERSION, '5.4', '>=' ) ) {
		return;
	}

	// Display for admins only.
	if ( ! is_super_admin() ) {
		return;
	}

	// Display on Dashboard page only.
	if ( isset( $GLOBALS['pagenow'] ) && 'index.php' !== $GLOBALS['pagenow'] ) {
		return;
	}

	// Display the notice, finally.
	WPPopups_Admin_Notice::error(
		'<p>' .
		sprintf(
			wp_kses(
				__( 'Your site is running an outdated version of PHP that is no longer supported and may cause issues with %1$s. <a href="%2$s" target="_blank" rel="noopener noreferrer">Read more</a> for additional information.', 'wp-popups-lite' ),
				[
					'a' => [
						'href'   => [],
						'target' => [],
						'rel'    => [],
					],
				]
			),
			'<strong>WPPopups</strong>',
			'https://wppopups.com/docs/supported-php-version/'
		) .
		'</p>'
	);
	deactivate_plugins( WPPOPUPS_HOOK );
}

add_action( 'admin_init', 'wppopups_check_php_version' );

/**
 * Get an upgrade modal text.
 *
 * @return string
 * @since 2.0.0
 *
 */
function wppopups_get_upgrade_modal_text() {

	return '<p>' .
	       esc_html__( 'Thanks for your interest in WP Popups Pro!', 'wp-popups-lite' ) . '<br>' .
	       sprintf(
		       wp_kses(
		       /* translators: %s - WPPopups.com contact page URL. */
			       __( 'If you have any questions or issues just <a href="%s" target="_blank" rel="noopener noreferrer">let us know</a>.', 'wp-popups-lite' ),
			       [
				       'a' => [
					       'href'   => [],
					       'target' => [],
					       'rel'    => [],
				       ],
			       ]
		       ),
		       'https://wppopups.com/contact/'
	       ) .
	       '</p>' .
	       '<p>' .
	       wp_kses(
		       __( 'After purchasing WP Popups Pro, you\'ll need to <strong>download and install the Pro version of the plugin</strong>, and then <strong>remove the free plugin</strong>.', 'wp-popups-lite' ),
		       [
			       'strong' => [],
		       ]
	       ) . '<br>' .
	       esc_html__( '(Don\'t worry, all your popups and settings will be preserved.)', 'wp-popups-lite' ) .
	       '</p>' .
	       '<p>' .
	       sprintf(
		       wp_kses(
		       /* translators: %s - WPPopups.com upgrade from Lite to paid docs page URL. */
			       __( 'Check out <a href="%s" target="_blank" rel="noopener noreferrer">our documentation</a> for step-by-step instructions.', 'wp-popups-lite' ),
			       [
				       'a' => [
					       'href'   => [],
					       'target' => [],
					       'rel'    => [],
				       ],
			       ]
		       ),
		       'https://wppopups.com/docs/how-to-upgrade-wp-popups-from-lite-version/?utm_source=WordPress&amp;utm_medium=link&amp;utm_campaign=liteplugin'
	       ) .
	       '</p>';
}

/**
 * Check if the old popup was installed and show warning
 *
 * @since 2.0.0
 */
function wppopups_upgrade_notice() {

	$old_popups = get_posts( [ 'post_type' => 'spucpt', 'post_status' => [ 'publish', 'draft' ] ] );

	// Check if plugin upgraded or there are no popups
	if ( empty( $old_popups ) || get_option( 'wppopups_upgraded_from_1x' ) || ( isset( $_GET['page'] ) && $_GET['page'] == 'wppopups-tools' ) ) {
		return;
	}

	// Display for admins only.
	if ( ! is_super_admin() ) {
		return;
	}

	// Display the notice, finally.
	WPPopups_Admin_Notice::error(
		'<h2>WP Popups</h2><p>' .
		sprintf(
			wp_kses(
				__( 'You just upgraded to WP Popups v%1$s. We need to update the database and import your old popups. To start the migration choose which popups you want to import by <a href="%2$s" target="_blank" rel="noopener noreferrer">clicking here</a>.', 'wp-popups-lite' ),
				[
					'a' => [
						'href'   => [],
						'target' => [],
						'rel'    => [],
					],
				]
			),
			WPPOPUPS_VERSION,
			admin_url( 'admin.php?page=wppopups-tools' )
		) .
		'</p>'
	);
}

add_action( 'admin_init', 'wppopups_upgrade_notice' );
