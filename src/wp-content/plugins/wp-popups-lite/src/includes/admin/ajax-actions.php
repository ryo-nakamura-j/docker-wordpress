<?php
/**
 * Ajax actions used in by admin.
 *
 * @package    WPPopups
 * @author     WPPopups
 * @since 2.0.0
 * @license    GPL-2.0+
 * @copyright  Copyright (c) 2016, WP Popups LLC
 */

/**
 * Save a popup.
 *
 * @since 2.0.0
 */
function wppopups_save_popup() {

	// Run a security check.
	check_ajax_referer( 'wppopups-builder', 'nonce' );

	// Check for permissions.
	if ( ! wppopups_current_user_can() ) {
		die( __( 'You do not have permission.', 'wp-popups-lite' ) );
	}

	// Check for popup data.
	if ( empty( $_POST['data'] ) ) {
		die( __( 'No data provided', 'wp-popups-lite' ) );
	}

	$popup_post = json_decode( stripslashes( $_POST['data'] ) );
	$data       = [];

	if ( ! is_null( $popup_post ) && $popup_post ) {
		foreach ( $popup_post as $post_input_data ) {
			// For input names that are arrays (e.g. `menu-item-db-id[3][4][5]`),
			// derive the array path keys via regex and set the value in $_POST.
			preg_match( '#([^\[]*)(\[(.+)\])?#', $post_input_data->name, $matches );

			$array_bits = [ $matches[1] ];

			if ( isset( $matches[3] ) ) {
				$array_bits = array_merge( $array_bits, explode( '][', $matches[3] ) );
			}

			$new_post_data = [];

			// Build the new array value from leaf to trunk.
			for ( $i = count( $array_bits ) - 1; $i >= 0; $i -- ) {
				if ( $i === count( $array_bits ) - 1 ) {
					$new_post_data[ $array_bits[ $i ] ] = wp_slash( $post_input_data->value );
				} else {
					$new_post_data = [
						$array_bits[ $i ] => $new_post_data,
					];
				}
			}

			$data = array_replace_recursive( $data, $new_post_data );
		}
	}
	$args = [];
	if( isset( $_POST['publish'] ) && $_POST['publish'] == '1' ) {
		$args = [ 'post_status' => 'publish' ];
	}
	$popup_id = wppopups()->popups->update( $data['id'], $data, $args );

	wppopups_clear_caches();

	do_action( 'wppopups_builder_save_popup', $popup_id, $data );

	if ( ! $popup_id ) {
		wp_send_json_error(
			[ 'error' => __( 'An error occurred and the popup could not be saved. Try refreshing the page first', 'wp-popups-lite' ) ]
		);
	} else {
		wp_send_json_success(
			[
				'popup_name' => esc_html( $data['settings']['popup_title'] ),
				'redirect'   => admin_url( 'admin.php?page=wppopups-overview' ),
			]
		);
	}
}

add_action( 'wp_ajax_wppopups_save_popup', 'wppopups_save_popup' );

/**
 * Create a new popup
 *
 * @since 2.0.0
 */
function wppopups_new_popup() {

	// Run a security check.
	check_ajax_referer( 'wppopups-builder', 'nonce' );

	// Check for popup name.
	if ( empty( $_POST['title'] ) ) {
		die( __( 'No popup name provided', 'wp-popups-lite' ) );
	}

	// Create popup.
	$popup_title    = sanitize_text_field( $_POST['title'] );
	$popup_template = sanitize_text_field( $_POST['template'] );
	$title_exists   = get_page_by_title( $popup_title, 'OBJECT', 'wppopups' );
	$popup_id       = wppopups()->popups->add(
		$popup_title,
		[],
		[
			'template' => $popup_template,
		]
	);
	if ( null !== $title_exists ) {
		wp_update_post(
			[
				'ID'         => $popup_id,
				'post_title' => $popup_title . ' (ID #' . $popup_id . ')',
			]
		);
	}

	if ( $popup_id ) {
		$data = [
			'id'       => $popup_id,
			'redirect' => add_query_arg(
				[
					'view'     => 'content',
					'popup_id' => $popup_id,
					'newpopup' => '1',
				],
				admin_url( 'admin.php?page=wppopups-builder' )
			),
		];
		wp_send_json_success( $data );
	} else {
		die( __( 'Error creating popup', 'wp-popups-lite' ) );
	}
}

add_action( 'wp_ajax_wppopups_new_popup', 'wppopups_new_popup' );

/**
 * Update popup template.
 *
 * @since 2.0.0
 */
function wppopups_update_popup_template() {

	// Run a security check.
	check_ajax_referer( 'wppopups-builder', 'nonce' );

	// Check for popup name.
	if ( empty( $_POST['popup_id'] ) ) {
		die( __( 'No popup ID provided', 'wp-popups-lite' ) );
	}
	$old_popup  = wppopups()->popups->get(
		absint( $_POST['popup_id'] )
	);

	$popup_id = wppopups()->popups->update(
		absint( $_POST['popup_id'] ),
		$old_popup->data,
		[
			'template' => sanitize_text_field( $_POST['template'] ),
		]
	);

	if ( $popup_id ) {
		$data = [
			'id'       => $popup_id,
			'redirect' => add_query_arg(
				[
					'view'     => 'content',
					'popup_id' => $popup_id,
				],
				admin_url( 'admin.php?page=wppopups-builder' )
			),
		];
		wp_send_json_success( $data );
	} else {
		die( __( 'Error updating popup template', 'wp-popups-lite' ) );
	}
}

add_action( 'wp_ajax_wppopups_update_popup_template', 'wppopups_update_popup_template' );



/**
 * Form Builder update next field ID.
 *
 * @since 1.2.9
 */
function wppopups_builder_increase_next_field_id() {

	// Run a security check.
	check_ajax_referer( 'wppopups-builder', 'nonce' );

	// Check for permissions.
	if ( ! wppopups_current_user_can() ) {
		wp_send_json_error();
	}

	// Check for required items.
	if ( empty( $_POST['popup_id'] ) ) {
		wp_send_json_error();
	}

	wppopups()->popup->next_field_id( absint( $_POST['popup_id'] ) );

	wp_send_json_success();
}

add_action( 'wp_ajax_wppopups_builder_increase_next_field_id', 'wppopups_builder_increase_next_field_id' );


/**
 * Render a rule row
 * @since 2.0.0
 */
function wppopups_render_rule() {
	// Run a security check.
	check_ajax_referer( 'wppopups-builder', 'nonce' );

	// Check for permissions and mandatory values.
	if ( ! wppopups_current_user_can() || empty( $_POST['rule'] ) || empty( $_POST['row_key'] ) || empty( $_POST['group_key'] ) || empty( $_POST['name'] ) ) {
		wp_send_json_error();
	}

	$input = strpos( $_POST['name'], 'global' ) !== false ? 'global_rules' : 'rules';

	wp_send_json_success(
		[
			'rule_option'   => wppopups_rules_field(
				$input,
				'select',
				sanitize_key( $_POST['row_key'] ),
				sanitize_key( $_POST['group_key'] ),
				'rule',
				[],
				'',
				[
					'default' => 'page_type',
					'options' => WPPopups_Rules::options(),
				],
				false
			),
			'rule_operator' => wppopups_rules_field(
				$input,
				'select',
				sanitize_key( $_POST['row_key'] ),
				sanitize_key( $_POST['group_key'] ),
				'operator',
				[],
				'',
				[
					'default' => '=',
					'options' => WPPopups_Rules::operators( sanitize_text_field( $_POST['rule'] ) ),
				],
				false
			),
			'rule_values'   => wppopups_rules_field(
				$input,
				WPPopups_Rules::field_type( sanitize_text_field( $_POST['rule'] ) ),
				sanitize_key( $_POST['row_key'] ),
				sanitize_key( $_POST['group_key'] ),
				'value',
				[],
				'',
				[
					'default' => '',
					'options' => WPPopups_Rules::values( sanitize_text_field( $_POST['rule'] ) ),
				],
				false
			),
		]
	);
}

add_action( 'wp_ajax_wppopups_render_rule', 'wppopups_render_rule' );

function wppopups_render_trigger() {
	// Run a security check.
	check_ajax_referer( 'wppopups-builder', 'nonce' );

	// Check for permissions and mandatory values.
	if ( ! wppopups_current_user_can() || empty( $_POST['trigger'] ) || empty( $_POST['row_key'] ) ) {
		wp_send_json_error();
	}

	wp_send_json_success(
		[
			'trigger_option' => wppopups_triggers_field(
				'select',
				$_POST['row_key'],
				'trigger',
				[],
				'',
				[
					'default' => 'seconds',
					'options' => WPPopups_Triggers::options(),
				],
				false
			),
			'trigger_value'  => wppopups_triggers_field(
				WPPopups_Triggers::field_type( $_POST['trigger'] ),
				$_POST['row_key'],
				'value',
				[],
				'',
				[
					'default' => '',
					'options' => '',
				],
				false
			),
		]
	);
}

add_action( 'wp_ajax_wppopups_render_trigger', 'wppopups_render_trigger' );

/**
 * Perform test connection to verify that the current web host can successfully
 * make outbound SSL connections.
 *
 * @since 2.0.0
 */
function wppopups_verify_ssl() {

	// Run a security check.
	check_ajax_referer( 'wppopups-admin', 'nonce' );

	// Check for permissions.
	if ( ! wppopups_current_user_can() ) {
		wp_send_json_error();
	}

	$response      = wp_remote_get( 'https://wppopups.com/' );
	$response_code = wp_remote_retrieve_response_code( $response );

	if ( 200 === wp_remote_retrieve_response_code( $response ) ) {
		wp_send_json_success(
			[
				'msg' => esc_html__( 'Success! Your server can make SSL connections.', 'wp-popups-lite' ),
			]
		);
	} else {
		wp_send_json_error(
			[
				'msg'   => esc_html__( 'There was an error and the connection failed. Please contact your web host with the technical details below.', 'wp-popups-lite' ),
				'debug' => '<pre>' . print_r( map_deep( $response, 'wp_strip_all_tags' ), true ) . '</pre>',
			]
		);
	}
}

add_action( 'wp_ajax_wppopups_verify_ssl', 'wppopups_verify_ssl' );

/**
 * Deactivate addon.
 *
 * @since 1.0.0
 */
function wppopups_deactivate_addon() {

	// Run a security check.
	check_ajax_referer( 'wppopups-admin', 'nonce' );

	// Check for permissions.
	if ( ! wppopups_current_user_can() ) {
		wp_send_json_error();
	}

	$type = 'addon';
	if ( ! empty( $_POST['type'] ) ) {
		$type = sanitize_key( $_POST['type'] );
	}

	if ( isset( $_POST['plugin'] ) ) {
		deactivate_plugins( $_POST['plugin'] );

		if ( 'plugin' === $type ) {
			wp_send_json_success( esc_html__( 'Plugin deactivated.', 'wp-popups-lite' ) );
		} else {
			wp_send_json_success( esc_html__( 'Addon deactivated.', 'wp-popups-lite' ) );
		}
	}

	wp_send_json_error( esc_html__( 'Could not deactivate the addon. Please deactivate from the Plugins page.', 'wp-popups-lite' ) );
}
add_action( 'wp_ajax_wppopups_deactivate_addon', 'wppopups_deactivate_addon' );

/**
 * Activate addon.
 *
 * @since 1.0.0
 */
function wppopups_activate_addon() {

	// Run a security check.
	check_ajax_referer( 'wppopups-admin', 'nonce' );

	// Check for permissions.
	if ( ! wppopups_current_user_can() ) {
		wp_send_json_error();
	}

	if ( isset( $_POST['plugin'] ) ) {

		$type = 'addon';
		if ( ! empty( $_POST['type'] ) ) {
			$type = sanitize_key( $_POST['type'] );
		}

		$activate = activate_plugins( $_POST['plugin'] );

		if ( ! is_wp_error( $activate ) ) {
			if ( 'plugin' === $type ) {
				wp_send_json_success( esc_html__( 'Plugin activated.', 'wp-popups-lite' ) );
			} else {
				wp_send_json_success( esc_html__( 'Addon activated.', 'wp-popups-lite' ) );
			}
		}
	}

	wp_send_json_error( esc_html__( 'Could not activate addon. Please activate from the Plugins page.', 'wp-popups-lite' ) );
}
add_action( 'wp_ajax_wppopups_activate_addon', 'wppopups_activate_addon' );

/**
 * Install addon.
 *
 * @since 1.0.0
 */
function wppopups_install_addon() {

	// Run a security check.
	check_ajax_referer( 'wppopups-admin', 'nonce' );

	// Check for permissions.
	if ( ! wppopups_current_user_can() ) {
		wp_send_json_error();
	}

	$error = esc_html__( 'Could not install addon. Please download from wppopups.com and install it manually.', 'wp-popups-lite' );

	if ( empty( $_POST['plugin'] ) ) {
		wp_send_json_error( $error );
	}

	// Set the current screen to avoid undefined notices.
	set_current_screen( 'wppopups_page_wppopups-settings' );

	// Prepare variables.
	$url = esc_url_raw(
		add_query_arg(
			array(
				'page' => 'wppopups-addons',
			),
			admin_url( 'admin.php' )
		)
	);

	$creds = request_filesystem_credentials( $url, '', false, false, null );

	// Check for file system permissions.
	if ( false === $creds ) {
		wp_send_json_error( $error );
	}

	if ( ! WP_Filesystem( $creds ) ) {
		wp_send_json_error( $error );
	}

	// We do not need any extra credentials if we have gotten this far, so let's install the plugin.
	require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
	require_once WPPOPUPS_PLUGIN_DIR . 'includes/admin/class-install-skin.php';

	// Do not allow WordPress to search/download translations, as this will break JS output.
	remove_action( 'upgrader_process_complete', array( 'Language_Pack_Upgrader', 'async_upgrade' ), 20 );

	// Create the plugin upgrader with our custom skin.
	$installer = new Plugin_Upgrader( new WPPopups_Install_Skin() );

	// Error check.
	if ( ! method_exists( $installer, 'install' ) || empty( $_POST['plugin'] ) ) {
		wp_send_json_error( $error );
	}

	// get final url.
	$headers = wp_remote_head( $_POST['plugin'], apply_filters( 'wppopups_remote_request_args', [ 'timeout' => 60 ] ) );
	if ( isset( $headers['headers'] ) && isset( $headers['headers']['location'] ) ) {
		$installer->install( $headers['headers']['location'] ); // phpcs:ignore
	}

	// Flush the cache and return the newly installed plugin basename.
	wp_cache_flush();

	if ( $installer->plugin_info() ) {

		$plugin_basename = $installer->plugin_info();

		$type = 'addon';
		if ( ! empty( $_POST['type'] ) ) {
			$type = sanitize_key( $_POST['type'] );
		}

		// Activate the plugin silently.
		$activated = activate_plugin( $plugin_basename );

		if ( ! is_wp_error( $activated ) ) {
			wp_send_json_success(
				array(
					'msg'          => 'plugin' === $type ? esc_html__( 'Plugin installed & activated.', 'wp-popups-lite' ) : esc_html__( 'Addon installed & activated.', 'wp-popups-lite' ),
					'is_activated' => true,
					'basename'     => $plugin_basename,
				)
			);
		} else {
			wp_send_json_success(
				array(
					'msg'          => 'plugin' === $type ? esc_html__( 'Plugin installed.', 'wp-popups-lite' ) : esc_html__( 'Addon installed.', 'wp-popups-lite' ),
					'is_activated' => false,
					'basename'     => $plugin_basename,
				)
			);
		}
	}

	wp_send_json_error( $error );
}
add_action( 'wp_ajax_wppopups_install_addon', 'wppopups_install_addon' );

/**
 * PRO Ajax actions used in by admin.
 *
 * @package    WPPopups
 * @author     WPPopups
 * @since 2.0.0
 * @license    GPL-2.0+
 * @copyright  Copyright (c) 2016, WP Popups LLC
 */


/**
 * Verify license.
 *
 * @since 2.0.0
 */
function wppopups_verify_license() {

	// Run a security check.
	check_ajax_referer( 'wppopups-admin', 'nonce' );

	// Check for permissions.
	if ( ! wppopups_current_user_can() ) {
		wp_send_json_error();
	}

	// Check for license key.
	if ( empty( $_POST['license'] ) ) {
		wp_send_json_error( esc_html__( 'Please enter a license key.', 'wppopups-pro' ) );
	}

	wppopups()->license->verify_key( sanitize_text_field( $_POST['license'] ), sanitize_text_field( $_POST['option_name'] ), absint( $_POST['item_id'] ), true, true );
}

add_action( 'wp_ajax_wppopups_verify_license', 'wppopups_verify_license' );

/**
 * Deactivate license.
 *
 * @since 2.0.0
 */
function wppopups_deactivate_license() {

	// Run a security check.
	check_ajax_referer( 'wppopups-admin', 'nonce' );

	// Check for permissions.
	if ( ! wppopups_current_user_can() ) {
		wp_send_json_error();
	}

	wppopups()->license->deactivate_key( true, sanitize_text_field( $_POST['option_name'] ) );
}

add_action( 'wp_ajax_wppopups_deactivate_license', 'wppopups_deactivate_license' );

/**
 * Refresh license.
 *
 * @since 2.0.0
 */
function wppopups_refresh_license() {

	// Run a security check.
	check_ajax_referer( 'wppopups-admin', 'nonce' );

	// Check for permissions.
	if ( ! wppopups_current_user_can() ) {
		wp_send_json_error();
	}

	// Check for license key.
	if ( empty( $_POST['license'] ) ) {
		wp_send_json_error( esc_html__( 'Please enter a license key.', 'wppopups-pro' ) );
	}

	wppopups()->license->verify_key( sanitize_text_field( $_POST['license'] ), true );
}

add_action( 'wp_ajax_wppopups_refresh_license', 'wppopups_refresh_license' );
