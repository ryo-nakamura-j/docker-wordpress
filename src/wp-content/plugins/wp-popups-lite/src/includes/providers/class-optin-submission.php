<?php

/**
 * Class WPPopups_Optin_Submission
 */
class WPPopups_Optin_Submission {
	/**
	 * Providers errors bag
	 * @var array
	 */
	private static $errors = [];

	/**
	 * WPPopups_Optin_Submission constructor.
	 */
	public function __construct() {
		// Optin submission
		add_action( 'wp_ajax_wppopups_optin_submit', [ $this, 'form_submission' ] );
		add_action( 'wp_ajax_nopriv_wppopups_optin_submit', [ $this, 'form_submission' ] );

		// Once all providers run check if any error and send response
		add_action( 'wppopups_process_complete', [ $this, 'process_entry_results' ], 40 );
	}

	/**
	 * Optin submission
	 */
	public function form_submission() {

		$form_data = $_POST;

		// check that popup ID is sent
		if ( empty( $form_data['popup'] ) ) {
			wp_send_json_error( esc_html__( 'Invalid popup ID', 'wppopups' ) );
		}


		// basic email validation
		if ( empty( $form_data['wppopups']['fields'][0] ) || ! filter_var( $form_data['wppopups']['fields'][0], FILTER_VALIDATE_EMAIL ) ) {
			wp_send_json_error( esc_html__( 'Please enter a valid email', 'wppopups' ) );
		}

		// honeypot validation
		/*if ( ! empty( $form_data['email'] ) || ! empty( $form_data['name'] ) ) {
			wp_send_json_success();

			// Logs spam entry depending on log levels set.
			wppopups_log(
				'Spam Entry ' . uniqid(),
				[ esc_html__( 'Honeypot triggered in optin submission.', 'wppopups')],
				[
					'type'    => [ 'spam' ],
					'popup_id' => absint( $form_data['popup'] ),
				]
			);
		}*/


		// Process hooks/filter - this is where most addons should hook
		// because at this point we have completed all field validation and
		// formatted the data.
		//$form_data = apply_filters( 'wppopups_process_filter', array_map( 'sanitize_text_field', $form_data ) );

		do_action( 'wppopups_process', $form_data );
		do_action( "wppopups_process_{$form_data['popup']}", $form_data );

		$form_data = apply_filters( 'wppopups_process_after_filter', $form_data );

		// Post-process hooks.
		do_action( 'wppopups_process_complete', $form_data );
		do_action( "wppopups_process_complete_{$form_data['popup']}", $form_data);
	}

	public static function setError( $msg ) {
		self::$errors[] = esc_html( $msg );
	}

	/**
	 * Check for error bag and send json accordingly
	 */
	public function process_entry_results( $form_data ) {

		if ( ! empty( self::$errors ) ) {
			wp_send_json_error( implode('<br>', self::$errors ) );
		}
		$popup = wppopups()->popups->get( $form_data['popup'] );
		$popup->data['success']['optin_success'] = apply_filters( 'wppopups_content', $popup->data['success']['optin_success'] );
		wp_send_json_success( [ $popup->data['success'], $popup->data['redirect'] ] );
	}
}

new WPPopups_Optin_Submission();