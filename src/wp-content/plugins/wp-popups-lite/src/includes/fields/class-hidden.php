<?php

/**
 * Hidden text field.
 *
 * @since 1.0.0
 */
class WPPopups_Field_Hidden extends WPPopups_Field {

	/**
	 * Primary class constructor.
	 *
	 * @since 1.0.0
	 */
	public function init() {

		// Define field type information.
		$this->name  = esc_html__( 'Hidden Field', 'wp-popups-lite' );
		$this->type  = 'hidden';
		$this->icon  = 'fa-eye-slash';
		$this->order = 150;
		//$this->group = 'fancy';
	}

	/**
	 * Field options panel inside the builder.
	 *
	 * @since 1.0.0
	 *
	 * @param array $field Field data and settings.
	 */
	public function field_options( $field ) {
		/*
		 * Basic field options.
		 */

		// Options open markup.
		$this->field_option( 'basic-options', $field, [ 'markup' => 'open' ] );

		// Label.
		$this->field_option( 'label', $field );

		// Set label to disabled.
		$args = array(
			'type'  => 'hidden',
			'slug'  => 'label_disable',
			'value' => '1',
		);
		$this->field_element( 'text', $field, $args );

		// Default value.
		$this->field_option( 'default_value', $field );

		// Custom CSS classes.
		$this->field_option( 'css', $field );

		// Options close markup.
		$this->field_option( 'basic-options', $field, [ 'markup' => 'close' ] );


		// Options Open markup.
		$this->field_option( 'provider-options', $field, [ 'markup' => 'open' ] );

		do_action( 'wppopups_field_options_provider', $field, $this );

		// Options close markup.
		$this->field_option( 'provider-options', $field, [ 'markup' => 'close' ] );
	}

	/**
	 * Field preview inside the builder.
	 *
	 * @since 1.0.0
	 *
	 * @param array $field Field data and settings.
	 */
	public function field_preview( $field ) {

		// Label.
		$this->field_preview_option( 'label', $field );

		// Primary input.
		echo '<input type="text" class="primary-input" disabled>';
	}

	/**
	 * Field display on the form front-end.
	 *
	 * @since 1.0.0
	 *
	 * @param array $field      Field data and settings.
	 * @param array $deprecated Not used any more field attributes.
	 * @param array $form_data  Form data and settings.
	 */
	public function field_display( $field, $deprecated, $form_data ) {

		// Define data.
		$primary = $field['properties']['inputs']['primary'];

		// Primary field.
		printf(
			'<input type="hidden" %s>',
			wppopups_html_attributes( $primary['id'], $primary['class'], $primary['data'], $primary['attr'] )
		);
	}
}

new WPPopups_Field_Hidden();
