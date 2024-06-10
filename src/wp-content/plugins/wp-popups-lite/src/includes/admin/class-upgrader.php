<?php
class WPPopups_Upgrader {
	
	public function __construct() {
		//add_action( 'upgrader_process_complete', 'process_complete', 10, 2 );
		add_action( 'admin_init', [ $this, 'upgrade' ], 10 );
	}


	public function process_complete( $upgrader_object, $options ) {

		if( $options['action'] != 'update' || $options['type'] != 'plugin' )
			return;

		$current_plugin_base = plugin_basename( __FILE__ );

		foreach( $options['plugins'] as $plugin ) {
			if( $plugin == $current_plugin_base )
				$this->upgrade();
		}

		return true;
	}


	/**
	 * Upgrade new fields
	 * @return mixed
	 */
	public function upgrade() {

		if( get_option( 'wppopups_upgrade_fields', false ) )
			return;

		$popups = wppopups()->popups->get();

		foreach( $popups as $popup ) {

			$popup_data = $popup->data;

			if( ! isset( $popup_data['fields']['optin_form_css'] ) || count( $popup_data['providers'] ) == 0 )
				continue;

			$popup_data['optin_styles']['optin_form_css'] = $popup_data['fields']['optin_form_css'];
			$popup_data['optin_styles']['submit_text'] = $popup_data['fields']['submit_text'];
			$popup_data['optin_styles']['submit_processing_text'] = $popup_data['fields']['submit_processing_text'];
			$popup_data['optin_styles']['submit_class'] = $popup_data['fields']['submit_class'];

			$has_name = $popup_data['fields']['name_field'];
			$has_gdpr = $popup_data['fields']['gdpr_field'];
			$label_email = $popup_data['fields']['email_field_text'];
			$label_name = $popup_data['fields']['name_field_text'];
			$label_gdpr = $popup_data['fields']['gdpr_field_text'];
			$has_inline = $popup_data['inline_fields']['inline_fields'];

			// Empty Fields Array
			$popup_data['fields'] = [];

			// First item, Email
			$popup_data['fields'][0] = [
				'id'						=> 0,
				'type'						=> 'email',
				'label'						=> $label_email,
				'description'				=> '',
				'required'					=> 1,
				'size'						=> 'large',
				'placeholder'				=> '',
				'limit_enabled'				=> 0,
				'limit_count'				=> 99,
				'limit_mode'				=> 'characters',
				'default_value'				=> '',
				'css'						=> $has_inline ? 'wppopups-one-half wppopups-first' : '',
				'meta'						=> [ 'delete' => false, 'duplicate' => false ],
			];


			// If it has name field
			if( $has_name ) {
				$popup_data['fields'][1] = [
					'id'						=> 1,
					'type'						=> 'text',
					'label'						=> $label_name,
					'description'				=> '',
					'required'					=> 1,
					'size'						=> 'large',
					'placeholder'				=> '',
					'limit_enabled'				=> 0,
					'limit_count'				=> 99,
					'limit_mode'				=> 'characters',
					'default_value'				=> '',
					'css'						=> $has_inline ? 'wppopups-one-half' : '',
					'meta'						=> [ 'delete' => true, 'duplicate' => true ],
				];
			}

			// If it has GDPR
			if( $has_gdpr ) {
				$popup_data['fields'][2] = [
					'id'		=> 2,
					'type'		=> 'gdpr-checkbox',
					'required'	=> 1,
					'label'		=> 'GDPR Agreement',
					'choices'	=> [
						[
							'label'	=> $label_gdpr,
							'value' => '',
						]
					],
					'description'	=> '',
					'css'			=> '',
				];
			}


			// Providers
			foreach( $popup_data['provider'] as $provider ) {
				foreach( $provider as $connections ) {
					foreach( $connections as $accounts ) {
						$popup_data['fields'][0]['provider-' . $accounts['account_id'] ] = 'provider-email';

						if( $has_gdpr )
							$popup_data['fields'][2]['provider-' . $accounts['account_id'] ] = 'provider-gdpr';
					}
				}
			}

			$popup_data['field_id'] = count( $popup_data['fields'] );

			$new_popup_id = wppopups()->popups->update( $popup->id, $popup_data );
		}

		update_option( 'wppopups_upgrade_fields', true );
	}
}

new WPPopups_Upgrader();