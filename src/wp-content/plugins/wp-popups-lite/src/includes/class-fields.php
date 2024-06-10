<?php
/**
 * Load the field types.
 *
 * @since 1.0.0
 */
class WPPopups_Fields {

	/**
	 * Primary class constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->init();
	}

	/**
	 * Load and init the base field class.
	 *
	 * @since 1.2.8
	 */
	public function init() {

		// Parent class template.
		require_once WPPOPUPS_PLUGIN_DIR . 'includes/fields/class-base.php';

		// Load default fields on WP init.
		add_action( 'init', [ $this, 'load' ] );
	}

	/**
	 * Load default field types.
	 *
	 * @since 1.0.0
	 */
	public function load() {

		$fields = apply_filters(
			'wppopups_load_fields',
			[
				'text',
				'textarea',
				'select',
				'radio',
				'checkbox',
				'gdpr-checkbox',
				'divider',
				'email',
				//'url',
				'hidden',
				//'html',
				//'name',
				//'password',
				//'address',
				//'phone',
				//'date-time',
				'number',
				//'page-break',
				//'rating',
				//'file-upload',
				//'payment-single',
				//'payment-multiple',
				//'payment-checkbox',
				//'payment-dropdown',
				//'payment-credit-card',
				//'payment-total',
				//'number-slider',
			]
		);

		// Include GDPR Checkbox field if GDPR enhancements are enabled.
		if( wppopups_setting( 'gdpr', false ) ) {
			$fields[] = 'gdpr-checkbox';
		}

		foreach( $fields as $field ) {

			if ( file_exists( WPPOPUPS_PLUGIN_DIR . 'includes/fields/class-' . $field . '.php' ) ) {
				require_once WPPOPUPS_PLUGIN_DIR . 'includes/fields/class-' . $field . '.php';
			} elseif ( defined( 'WPPOPUPS_PLUGIN_PRO_DIR' ) && file_exists( WPPOPUPS_PLUGIN_PRO_DIR . 'pro/includes/fields/class-' . $field . '.php' ) ) {
				require_once WPPOPUPS_PLUGIN_PRO_DIR . 'pro/includes/fields/class-' . $field . '.php';
			}
		}
	}
}
new WPPopups_Fields();
