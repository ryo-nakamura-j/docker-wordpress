<?php

/**
 * Provider class.
 *
 * @package    WPPopups
 * @author     WPPopups
 * @since      2.0.0
 * @license    GPL-2.0+
 * @copyright  Copyright (c) 2016, WP Popups LLC
 */
abstract class WPPopups_Provider {

	/**
	 * Provider addon version.
	 *
	 * @since 2.0.0
	 *
	 * @var string
	 */
	protected $version;

	/**
	 * Provider name.
	 *
	 * @since 2.0.0
	 *
	 * @var string
	 */
	public $name;

	/**
	 * Provider name in slug format.
	 *
	 * @since 2.0.0
	 *
	 * @var string
	 */
	public $slug;

	/**
	 * Load priority.
	 *
	 * @since 2.0.0
	 *
	 * @var int
	 */
	public $priority = 10;

	/**
	 * Holds the API connections.
	 *
	 * @since 2.0.0
	 *
	 * @var mixed
	 */
	public $api = false;

	/**
	 * Service icon.
	 *
	 * @since 2.0.0
	 *
	 * @var string
	 */
	public $icon;

	/**
	 * Service icon.
	 *
	 * @since 2.0.0
	 *
	 * @var string
	 */
	public $type;


	/**
	 * Popup Object
	 * @var WPPopups_Popup|bool|null
	 */
	protected $popup;

	/**
	 * Primary class constructor.
	 *
	 * @since 2.0.0
	 */
	public function __construct() {

		$this->type = esc_html__( 'Connection', 'wppopups-lite' );

		$this->init();

		// Add to list of available providers.
		add_filter( 'wppopups_providers_available', [ $this, 'register_provider' ], $this->priority, 1 );

		// Process builder AJAX requests.
		add_action( "wp_ajax_wppopups_provider_ajax_{$this->slug}", [ $this, 'process_ajax' ] );

		// Process entry.
		add_action( 'wppopups_process_complete', [ $this, 'process_entry' ] );

		// Fetch and store the current form data when in the builder.
		add_action( 'wppopups_builder_init', [ $this, 'builder_form_data' ] );

		// Output builder sidebar.
		add_action( 'wppopups_providers_panel_sidebar', [ $this, 'builder_sidebar' ], $this->priority, 1 );

		// Output builder content.
		add_action( 'wppopups_providers_panel_content', [ $this, 'builder_output' ], $this->priority );

		// Remove provider from Settings Integrations tab.
		add_action( 'wp_ajax_wppopups_settings_provider_disconnect', [ $this, 'integrations_tab_disconnect' ] );

		// Add new provider from Settings Integrations tab.
		add_action( 'wp_ajax_wppopups_settings_provider_add', [ $this, 'integrations_tab_add' ] );

		// Add providers sections to the Settings Integrations tab.
		add_action( 'wppopups_settings_providers', [ $this, 'integrations_tab_options' ], $this->priority, 2 );

		// Add Field
		add_action( 'wppopups_field_options_provider', [ $this, 'output_fields' ], 10, 2 );
	}

	/**
	 * All systems go. Used by subclasses.
	 *
	 * @since 2.0.0
	 */
	public function init() {
	}

	/**
	 * Add to list of registered providers.
	 *
	 * @param array $providers Array of all active providers.
	 *
	 * @return array
	 * @since 2.0.0
	 *
	 */
	public function register_provider( $providers = [] ) {

		$providers[ $this->slug ] = $this->name;

		return $providers;
	}

	/**
	 * Process the Builder AJAX requests.
	 *
	 * @since 2.0.0
	 */
	public function process_ajax() {

		// Run a security check.
		check_ajax_referer( 'wppopups-builder', 'nonce' );

		// Check for permissions.
		if ( ! wppopups_current_user_can() ) {
			wp_send_json_error(
				[
					'error' => esc_html__( 'You do not have permission', 'wppopups-lite' ),
				]
			);
		}

		$name          = ! empty( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '';
		$task          = ! empty( $_POST['task'] ) ? sanitize_text_field( wp_unslash( $_POST['task'] ) ) : '';
		$id            = ! empty( $_POST['id'] ) ? sanitize_text_field( wp_unslash( $_POST['id'] ) ) : '';
		$connection_id = ! empty( $_POST['connection_id'] ) ? sanitize_text_field( wp_unslash( $_POST['connection_id'] ) ) : '';
		$account_id    = ! empty( $_POST['account_id'] ) ? sanitize_text_field( wp_unslash( $_POST['account_id'] ) ) : '';
		$list_id       = ! empty( $_POST['list_id'] ) ? sanitize_text_field( wp_unslash( $_POST['list_id'] ) ) : '';
		$data          = ! empty( $_POST['data'] ) ? array_map( 'sanitize_text_field', wp_parse_args( wp_unslash( $_POST['data'] ) ) ) : []; //phpcs:ignore

		/*
		 * Create new connection.
		 */

		if ( 'new_connection' === $task ) {

			$connection = $this->output_connection(
				'',
				[
					'connection_name' => $name,
				],
				$id
			);
			wp_send_json_success(
				[
					'html' => $connection,
				]
			);
		}

		/*
		 * Create new Provider account.
		 */

		if ( 'new_account' === $task ) {

			$auth = $this->api_auth( $data, $id );

			if ( is_wp_error( $auth ) ) {

				wp_send_json_error(
					[
						'error' => $auth->get_error_message(),
					]
				);

			} else {

				$accounts = $this->output_accounts(
					$connection_id,
					[
						'account_id' => $auth,
					]
				);
				wp_send_json_success(
					[
						'html' => $accounts,
					]
				);
			}
		}

		/*
		 * Select/Toggle Provider accounts.
		 */

		if ( 'select_account' === $task ) {

			$lists = $this->output_lists(
				$connection_id,
				[
					'account_id' => $account_id,
				]
			);

			if ( is_wp_error( $lists ) ) {

				wp_send_json_error(
					[
						'error' => $lists->get_error_message(),
					]
				);

			} else {

				wp_send_json_success(
					[
						'html' => $lists,
					]
				);
			}
		}

		/*
		 * Select/Toggle Provider account lists.
		 */

		if ( 'select_list' === $task ) {



			$groups = $this->output_groups(
				$connection_id,
				[
					'account_id' => $account_id,
					'list_id'    => $list_id,
				]
			);


			$options = $this->output_options(
				$connection_id,
				[
					'account_id' => $account_id,
					'list_id'    => $list_id,
				]
			);

			wp_send_json_success(
				[
					'html' => $groups . $options,
				]
			);
		}


		die();
	}


	/************************************************************************
	 * API methods - these methods interact directly with the provider API. *
	 ************************************************************************/

	/**
	 * Authenticate with the provider API.
	 *
	 * @param array $data
	 * @param string $popup_id
	 *
	 * @return mixed id or error object
	 * @since 2.0.0
	 *
	 */
	public function api_auth( $data = [], $popup_id = '' ) {
	}

	/**
	 * Establish connection object to provider API.
	 *
	 * @param string $account_id
	 *
	 * @return mixed array or error object
	 * @since 2.0.0
	 *
	 */
	public function api_connect( $account_id ) {
	}

	/**
	 * Retrieve provider account lists.
	 *
	 * @param string $connection_id
	 * @param string $account_id
	 *
	 * @return mixed array or error object
	 * @since 2.0.0
	 *
	 */
	public function api_lists( $connection_id = '', $account_id = '' ) {
	}

	/**
	 * Retrieve provider account list groups.
	 *
	 * @param string $connection_id
	 * @param string $account_id
	 * @param string $list_id
	 *
	 * @return mixed array or error object
	 * @since 2.0.0
	 *
	 */
	public function api_groups( $connection_id = '', $account_id = '', $list_id = '' ) {
	}

	/**
	 * Retrieve provider account list fields.
	 *
	 * @param string $connection_id
	 * @param string $account_id
	 * @param string $list_id
	 *
	 * @return mixed array or error object
	 * @since 2.0.0
	 *
	 */
	public function api_fields( $connection_id = '', $account_id = '', $list_id = '' ) {
	}


	/*************************************************************************
	 * Helper methods - these methods help to api methods. *
	 *************************************************************************/

	/**
	 * Fill Data with parsed fields
	 *
	 * @param $account_id
	 * @param $form_data
	 * @param $popup_data
	 *
	 * @return mixed
	 */
	public function parse_fields( $account_id, $form_data, $popup_data ) {

		$parse 			= [];
		$param_key		= 'provider-' . $account_id;
		$fields_post	= $form_data['wppopups']['fields'];
		$fields_popup	= $popup_data['fields'];


		if( ! is_array( $fields_post ) || count( $fields_post ) == 0 )
			return $parse;

		foreach( $fields_post as $field_id => $field_value ) {
			if( ! isset( $fields_popup[ $field_id ][ $param_key ] ) || empty( $fields_popup[ $field_id ][ $param_key ] ) )
				continue;
			
			$field_key = $fields_popup[ $field_id ][ $param_key ];

			if( $field_key == 'provider-email' || $field_key == 'provider-gdpr' )
				continue;

			$parse[ $field_key ] = is_array($field_value) ? reset($field_value) : $field_value;
		}

		return $parse;
	}

	/**
	 * Check if the GDPR field is true
	 * @param  [type] $account_id [description]
	 * @param  [type] $form_data  [description]
	 * @param  [type] $popup_data [description]
	 * @return bool
	 */
	public function parse_gdpr( $account_id, $form_data, $popup_data ) {
		$found 			= false;
		$param_key		= 'provider-' . $account_id;
		$fields_post	= $form_data['wppopups']['fields'];
		$fields_popup	= $popup_data['fields'];


		if( ! is_array( $fields_post ) || count( $fields_post ) == 0 )
			return $found;

		foreach( $fields_post as $field_id => $field_value ) {
			
			if( isset( $fields_popup[ $field_id ][ $param_key ] ) &&
				$fields_popup[ $field_id ][ $param_key ] == 'provider-gdpr'
			) {
				$found = true;
				break;
			}
		}

		return $found;
	}

	/*************************************************************************
	 * Output methods - these methods generally return HTML for the builder. *
	 *************************************************************************/

	/**
	 * Connection HTML.
	 *
	 * This method compiles all the HTML necessary for a connection to a provider.
	 *
	 * @param string $connection_id
	 * @param array $connection
	 * @param mixed $popup Popup id or popup data.
	 *
	 * @return string
	 * @since 2.0.0
	 *
	 */
	public function output_connection( $connection_id = '', $connection = [], $popup = '' ) {

		if ( empty( $connection_id ) ) {
			$connection_id = 'connection_' . uniqid();
		}

		if ( empty( $connection ) || empty( $popup ) ) {
			return '';
		}

		$output = sprintf( '<div class="wppopups-provider-connection" data-provider="%s" data-connection_id="%s">', $this->slug, $connection_id );

		$output .= $this->output_connection_header( $connection_id, $connection );

		$output .= $this->output_auth();

		$output .= $this->output_accounts( $connection_id, $connection );

		$lists  = $this->output_lists( $connection_id, $connection );
		$output .= ! is_wp_error( $lists ) && false !== $lists ? $lists : '';

		$output .= $this->output_groups( $connection_id, $connection );

		$output .= $this->output_options( $connection_id, $connection );

		$output .= '<button class="wppopups-provider-connections-save">'. esc_html__( 'Save Connection', 'wppopups-lite' ) .'</button>';

		$output .= '</div>';

		return $output;
	}

	/**
	 * Connection header HTML.
	 *
	 * @param string $connection_id
	 * @param array $connection
	 *
	 * @return string
	 * @since 2.0.0
	 *
	 */
	public function output_connection_header( $connection_id = '', $connection = [] ) {

		if ( empty( $connection_id ) || empty( $connection ) ) {
			return '';
		}

		$output = '<div class="wppopups-provider-connection-header">';

		$output .= sprintf( '<span>%s</span>', sanitize_text_field( $connection['connection_name'] ) );

		$output .= '<button class="wppopups-provider-connection-delete"><i class="fa fa-times-circle"></i></button>';

		$output .= sprintf( '<input type="hidden" name="providers[%s][%s][connection_name]" value="%s">', $this->slug, $connection_id, esc_attr( $connection['connection_name'] ) );

		$output .= '</div>';

		return $output;
	}

	/**
	 * Provider account authorize fields HTML.
	 *
	 * @return mixed
	 * @since 2.0.0
	 *
	 */
	public function output_auth() {
	}

	/**
	 * Provider account select HTML.
	 *
	 * @param string $connection_id Unique connection ID.
	 * @param array $connection Array of connection data.
	 *
	 * @return string
	 * @since 2.0.0
	 *
	 */
	public function output_accounts( $connection_id = '', $connection = [] ) {

		if ( empty( $connection_id ) || empty( $connection ) ) {
			return '';
		}

		$providers = wppopups_get_providers_options();

		if ( empty( $providers[ $this->slug ] ) ) {
			return '';
		}

		$output = '<div class="wppopups-provider-accounts wppopups-connection-block">';

		$output .= sprintf( '<h4>%s</h4>', esc_html__( 'Select Account', 'wp-popups-lite' ) );

		$output .= sprintf( '<select name="providers[%s][%s][account_id]">', $this->slug, $connection_id );
		foreach ( $providers[ $this->slug ] as $key => $provider_details ) {
			$selected = ! empty( $connection['account_id'] ) ? $connection['account_id'] : '';
			$output   .= sprintf(
				'<option value="%s" %s>%s</option>',
				$key,
				selected( $selected, $key, false ),
				esc_html( $provider_details['label'] )
			);
		}
		$output .= sprintf( '<option value="">%s</a>', esc_html__( 'Add New Account', 'wp-popups-lite' ) );
		$output .= '</select>';

		$output .= '</div>';

		return $output;
	}

	/**
	 * Provider account lists HTML.
	 *
	 * @param string $connection_id
	 * @param array $connection
	 *
	 * @return WP_Error|string
	 * @since 2.0.0
	 *
	 */
	public function output_lists( $connection_id = '', $connection = [] ) {

		if ( empty( $connection_id ) || empty( $connection['account_id'] ) ) {
			return '';
		}

		$lists    = $this->api_lists( $connection_id, $connection['account_id'] );
		$selected = ! empty( $connection['list_id'] ) ? $connection['list_id'] : '';

		if ( is_wp_error( $lists ) || ! $lists ) {
			return $lists;
		}

		$output = '<div class="wppopups-provider-lists wppopups-connection-block">';

		$output .= sprintf( '<h4>%s</h4>', esc_html__( 'Select List', 'wp-popups-lite' ) );

		$output .= sprintf( '<select name="providers[%s][%s][list_id]">', $this->slug, $connection_id );

		if ( ! empty( $lists ) ) {
			foreach ( $lists as $list ) {
				$output .= sprintf(
					'<option value="%s" %s>%s</option>',
					esc_attr( $list['id'] ),
					selected( $selected, $list['id'], false ),
					esc_attr( $list['name'] )
				);
			}
		}

		$output .= '</select>';

		$output .= '</div>';

		return $output;
	}

	/**
	 * Provider account list groups HTML.
	 *
	 * @param string $connection_id
	 * @param array $connection
	 *
	 * @return string
	 * @since 2.0.0
	 *
	 */
	public function output_groups( $connection_id = '', $connection = [] ) {

		if ( empty( $connection_id ) || empty( $connection['account_id'] ) || empty( $connection['list_id'] ) ) {
			return '';
		}

		$groupsets = $this->api_groups( $connection_id, $connection['account_id'], $connection['list_id'] );

		if ( is_wp_error( $groupsets ) ) {
			return '';
		}

		$output = '<div class="wppopups-provider-groups wppopups-connection-block">';

		$output .= sprintf( '<h4>%s</h4>', esc_html__( 'Select Groups', 'wppopups-lite' ) );

		$output .= sprintf( '<p>%s</p>', esc_html__( 'We also noticed that you have some segments in your list. You can select specific list segments below if needed. This is optional.', 'wppopups-lite' ) );

		$output .= '<div class="wppopups-provider-groups-list">';

		foreach ( $groupsets as $groupset ) {

			$output .= sprintf( '<p>%s</p>', esc_html( $groupset['name'] ) );

			foreach ( $groupset['groups'] as $group ) {

				$selected = ! empty( $connection['groups'] ) && ! empty( $connection['groups'][ $groupset['id'] ] ) ? in_array( $group['name'], $connection['groups'][ $groupset['id'] ], true ) : false;

				$output .= sprintf(
					'<span><input id="group_%s" type="checkbox" value="%s" name="providers[%s][%s][groups][%s][%s]" %s><label for="group_%s">%s</label></span>',
					esc_attr( $group['id'] ),
					esc_attr( $group['name'] ),
					$this->slug,
					$connection_id,
					$groupset['id'],
					$group['id'],
					checked( $selected, true, false ),
					esc_attr( $group['id'] ),
					esc_attr( $group['name'] )
				);
			}
		}

		$output .= '</div>';

		$output .= '</div>';

		return $output;
	}



	/**
	 * Provider account list options HTML.
	 *
	 * @param string $connection_id
	 * @param array $connection
	 *
	 * @return string
	 * @since 2.0.0
	 *
	 */
	public function output_options( $connection_id = '', $connection = [] ) {
	}


	/**
	 * Print field
	 *
	 * @param array $field
	 *
	 * @param $input
	 *
	 * @return void
	 * @since 2.0.0
	 */
	public function output_fields( $field, $input ) {

		if( empty( $this->popup ) || empty( $field ) )
			$this->builder_form_data();

		$popup_data = $this->popup->data;
		$providers = wppopups_get_providers_options();

		if ( ! empty( $popup_data['providers'][ $this->slug ] ) && ! empty( $providers[ $this->slug ] ) ) {

			foreach ( $popup_data['providers'][ $this->slug ] as $connection_id => $connection ) {

				foreach ( $providers[ $this->slug ] as $account_id => $connections ) {

					if (
						! empty( $connection['account_id'] ) &&
						$connection['account_id'] === $account_id
					) {
						$provider_fields = $this->api_fields($connection_id, $connection['account_id'], $connection['list_id'] );

						if ( ! is_wp_error( $provider_fields ) || ! empty( $provider_fields ) ) {

							$label = $this->name . ' ' . sprintf(
								esc_html__('( connection : %s )'),
								$connection['connection_name']
							);

							$options = [
								'' => esc_html__( 'Select your provider field', 'wppopups-pro' )
							];

							foreach( $provider_fields as $data )
								$options[$data['id']] = $data['name'];

							$args = [
								'label'			=> $label,
								'options'		=> $options,
								'account_id'	=> $account_id,
							];

							$input->field_option( 'provider-fields', $field, $args );
						}
					}
				}
			}
		}
		
	}


	/********************************************************
	 * Builder methods - these methods _build_ the Builder. *
	 ********************************************************/

	/**
	 * Fetch and store the current form data when in the builder.
	 *
	 * @since 2.0.0
	 */
	public function builder_form_data() {

		if( ! empty( $this->popup ) )
			return;

		if( isset( $_GET['popup_id'] ) && is_numeric( $_GET['popup_id'] ) ) {
			$popup_id = absint( $_GET['popup_id'] );
		} elseif( isset( $_POST['id'] ) && is_numeric( $_POST['id'] ) ) {
			$popup_id = absint( $_POST['id'] );
		}

		if ( ! isset( $popup_id ) )
			return;

		$this->popup = wppopups()->popups->get( $popup_id, [ 'content_only' => true ] );
	}

	/**
	 * Display content inside the panel content area.
	 *
	 * @since 2.0.0
	 */
	public function builder_content() {

		$popup_data = $this->popup->data;
		$providers = wppopups_get_providers_options();

		if ( ! empty( $popup_data['providers'][ $this->slug ] ) && ! empty( $providers[ $this->slug ] ) ) {

			foreach ( $popup_data['providers'][ $this->slug ] as $connection_id => $connection ) {

				foreach ( $providers[ $this->slug ] as $account_id => $connections ) {

					if (
						! empty( $connection['account_id'] ) &&
						$connection['account_id'] === $account_id
					) {
						echo $this->output_connection( $connection_id, $connection, $popup_data );
					}
				}
			}
		}
	}

	/**
	 * Display content inside the panel sidebar area.
	 *
	 * @since 2.0.0
	 */
	public function builder_sidebar($popup) {

		$popup_data  = $popup->data;
		$configured = ! empty( $popup_data['providers'][ $this->slug ] ) ? 'configured' : '';
		$configured = apply_filters( 'wppopups_providers_' . $this->slug . '_configured', $configured );

		echo '<a href="#" class="wppopups-panel-sidebar-section icon ' . esc_attr( $configured ) . ' wppopups-panel-sidebar-section-' . esc_attr( $this->slug ) . '" data-section="' . esc_attr( $this->slug ) . '">';

		echo '<img src="' . esc_url( $this->icon ) . '">';

		echo esc_html( $this->name );

		echo '<i class="fa fa-angle-right wppopups-toggle-arrow"></i>';

		if ( ! empty( $configured ) ) {
			echo '<i class="fa fa-check-circle-o"></i>';
		}

		echo '</a>';
	}

	/**
	 * Wraps the builder content with the required markup.
	 *
	 * @since 2.0.0
	 */
	public function builder_output() {
		?>
		<div class="wppopups-panel-content-section wppopups-panel-content-section-<?php echo esc_attr( $this->slug ); ?>"
		     id="<?php echo esc_attr( $this->slug ); ?>-provider">

			<?php $this->builder_output_before(); ?>

			<div class="wppopups-panel-content-section-title">

				<?php echo $this->name; ?>

				<button class="wppopups-provider-connections-add"
				        data-popup_id="<?php echo absint( $_GET['popup_id'] ); ?>"
				        data-provider="<?php echo esc_attr( $this->slug ); ?>"
				        data-type="<?php echo esc_attr( strtolower( $this->type ) ); ?>">
					<?php
					printf(
					/* translators: %s - Provider type. */
						esc_html__( 'Add New %s', 'wppopups-lite' ),
						esc_html( $this->type )
					);
					?>
				</button>

			</div>

			<div class="wppopups-provider-connections-wrap wppopups-clear">

				<div class="wppopups-provider-connections">

					<?php $this->builder_content(); ?>

				</div>

			</div>

			<?php $this->builder_output_after(); ?>

		</div>
		<?php
	}

	/**
	 * Optionally output content before the main builder output.
	 *
	 * @since 2.0.0
	 */
	public function builder_output_before() {
	}

	/**
	 * Optionally output content after the main builder output.
	 *
	 * @since 2.0.0
	 */
	public function builder_output_after() {
	}

	/*************************************************************************
	 * Integrations tab methods - these methods relate to the settings page. *
	 *************************************************************************/

	/**
	 * Form fields to add a new provider account.
	 *
	 * @since 2.0.0
	 */
	public function integrations_tab_new_form() {
	}

	/**
	 * AJAX to disconnect a provider from the settings integrations tab.
	 *
	 * @since 2.0.0
	 */
	public function integrations_tab_disconnect() {

		// Run a security check.
		check_ajax_referer( 'wppopups-admin', 'nonce' );

		// Check for permissions.
		if ( ! wppopups_current_user_can() ) {
			wp_send_json_error(
				[
					'error' => esc_html__( 'You do not have permission', 'wppopups-lite' ),
				]
			);
		}

		if ( empty( $_POST['provider'] ) || empty( $_POST['key'] ) ) {
			wp_send_json_error(
				[
					'error' => esc_html__( 'Missing data', 'wppopups-lite' ),
				]
			);
		}

		$providers = wppopups_get_providers_options();

		if ( ! empty( $providers[ $_POST['provider'] ][ $_POST['key'] ] ) ) {

			unset( $providers[ sanitize_key( $_POST['provider'] ) ][ sanitize_text_field( $_POST['key'] ) ] );
			update_option( 'wppopups_providers', $providers );

			do_action( 'wppopups_integrations_disconnect', $_POST['provider'], $_POST['key'] );

			wp_send_json_success();

		} else {
			wp_send_json_error(
				[
					'error' => esc_html__( 'Connection missing', 'wppopups-lite' ),
				]
			);
		}
	}

	/**
	 * AJAX to add a provider from the settings integrations tab.
	 *
	 * @since 2.0.0
	 */
	public function integrations_tab_add() {

		if ( $_POST['provider'] !== $this->slug ) { //phpcs:ignore
			return;
		}

		// Run a security check.
		check_ajax_referer( 'wppopups-admin', 'nonce' );

		// Check for permissions.
		if ( ! wppopups_current_user_can() ) {
			wp_send_json_error(
				[
					'error' => esc_html__( 'You do not have permission', 'wppopups-lite' ),
				]
			);
		}

		if ( empty( $_POST['data'] ) ) {
			wp_send_json_error(
				[
					'error' => esc_html__( 'Missing data', 'wppopups-lite' ),
				]
			);
		}

		$data = wp_parse_args( $_POST['data'], [] );
		$data = array_map( 'sanitize_text_field', $data );
		$auth = $this->api_auth( $data, '' );

		if ( is_wp_error( $auth ) ) {

			wp_send_json_error(
				[
					'error'     => esc_html__( 'Could not connect to the provider.', 'wppopups-lite' ),
					'error_msg' => $auth->get_error_message(),
				]
			);

		} else {

			$account = '<li class="wppopups-clear">';
			$account .= '<span class="label">' . sanitize_text_field( $data['label'] ) . '</span>';
			/* translators: %s - Connection date. */
			$account .= '<span class="date">' . sprintf( esc_html__( 'Connected on: %s', 'wppopups-lite' ), date_i18n( get_option( 'date_format', time() ) ) ) . '</span>';
			$account .= '<span class="remove"><a href="#" data-provider="' . $this->slug . '" data-key="' . esc_attr( $auth ) . '">' . esc_html__( 'Disconnect', 'wppopups-lite' ) . '</a></span>';
			$account .= '</li>';

			wp_send_json_success(
				[
					'html' => $account,
				]
			);
		}
	}

	/**
	 * Add provider to the Settings Integrations tab.
	 *
	 * @param array $active Array of active connections.
	 * @param array $settings Array of all connections settings.
	 *
	 * @since 2.0.0
	 *
	 */
	public function integrations_tab_options( $active, $settings ) {

		$connected = ! empty( $active[ $this->slug ] );
		$accounts  = ! empty( $settings[ $this->slug ] ) ? $settings[ $this->slug ] : [];
		$class     = $connected && $accounts ? 'connected' : '';
		$arrow     = 'right';
		/* translators: %s - provider name. */
		$title_connect_to = sprintf( esc_html__( 'Connect to %s', 'wppopups-lite' ), esc_html( $this->name ) );

		// This lets us highlight a specific service by a special link.
		if ( ! empty( $_GET['wppopups-integration'] ) ) { //phpcs:ignore
			if ( $this->slug === sanitize_key( $_GET['wppopups-integration'] ) ) { //phpcs:ignore
				$class .= ' focus-in';
				$arrow = 'down';
			} else {
				$class .= ' focus-out';
			}
		}
		?>

		<div id="wppopups-integration-<?php echo esc_attr( $this->slug ); ?>"
		     class="wppopups-settings-provider wppopups-clear <?php echo esc_attr( $this->slug ); ?> <?php echo esc_attr( $class ); ?>">

			<div class="wppopups-settings-provider-header wppopups-clear"
			     data-provider="<?php echo esc_attr( $this->slug ); ?>">

				<div class="wppopups-settings-provider-logo">
					<i title="<?php esc_attr_e( 'Show Accounts', 'wppopups-lite' ); ?>"
					   class="fa fa-chevron-<?php echo esc_attr( $arrow ); ?>"></i>
					<img src="<?php echo esc_url( $this->icon ); ?>">
				</div>

				<div class="wppopups-settings-provider-info">
					<h3><?php echo esc_html( $this->name ); ?></h3>
					<p>
						<?php
						/* translators: %s - provider name. */
						printf( esc_html__( 'Integrate %s with WPPopups', 'wppopups-lite' ), esc_html( $this->name ) );
						?>
					</p>
					<span class="connected-indicator green"><i
								class="fa fa-check-circle-o"></i>&nbsp;<?php esc_html_e( 'Connected', 'wppopups-lite' ); ?></span>
				</div>

			</div>

			<div class="wppopups-settings-provider-accounts" id="provider-<?php echo esc_attr( $this->slug ); ?>">

				<div class="wppopups-settings-provider-accounts-list">
					<ul>
						<?php
						if ( ! empty( $accounts ) ) {
							foreach ( $accounts as $key => $account ) {
								echo '<li class="wppopups-clear">';
								echo '<span class="label">' . esc_html( $account['label'] ) . '</span>';
								/* translators: %s - Connection date. */
								echo '<span class="date">' . sprintf( esc_html__( 'Connected on: %s', 'wppopups-lite' ), date_i18n( get_option( 'date_format' ), intval( $account['date'] ) ) ) . '</span>';
								echo '<span class="remove"><a href="#" data-provider="' . esc_attr( $this->slug ) . '" data-key="' . esc_attr( $key ) . '">' . esc_html__( 'Disconnect', 'wppopups-lite' ) . '</a></span>';
								echo '</li>';
							}
						}
						?>
					</ul>
				</div>

				<p class="wppopups-settings-provider-accounts-toggle">
					<a class="wppopups-btn wppopups-btn-md wppopups-btn-light-grey" href="#"
					   data-provider="<?php echo esc_attr( $this->slug ); ?>">
						<i class="fa fa-plus"></i> <?php esc_html_e( 'Add New Account', 'wppopups-lite' ); ?>
					</a>
				</p>

				<div class="wppopups-settings-provider-accounts-connect">

					<form>
						<p><?php esc_html_e( 'Please fill out all of the fields below to add your new provider account.', 'wppopups-lite' ); ?></span></p>

						<p class="wppopups-settings-provider-accounts-connect-fields">
							<?php $this->integrations_tab_new_form(); ?>
						</p>

						<button type="submit"
						        class="wppopups-btn wppopups-btn-md wppopups-btn-blue wppopups-settings-provider-connect"
						        data-provider="<?php echo esc_attr( $this->slug ); ?>"
						        title="<?php echo esc_attr( $title_connect_to ); ?>">
							<?php echo esc_html( $title_connect_to ); ?>
						</button>
					</form>
				</div>

			</div>

		</div>
		<?php
	}

	/**
	 * Error wrapper for WP_Error.
	 *
	 * @param string $message
	 * @param string $parent
	 *
	 * @return WP_Error
	 * @since 2.0.0
	 *
	 */
	public function error( $message = '', $parent = '0' ) {
		return new WP_Error( $this->slug . '-error', $message );
	}
}
