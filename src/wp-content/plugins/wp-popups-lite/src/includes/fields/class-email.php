<?php

/**
 * Email text field.
 *
 * @since 1.0.0
 */
class WPPopups_Field_Email extends WPPopups_Field {

	/**
	 * Primary class constructor.
	 *
	 * @since 1.0.0
	 */
	public function init() {

		// Define field type information.
		$this->name  = esc_html__( 'Email', 'wp-popups-lite' );
		$this->type  = 'email';
		$this->icon  = 'fa-envelope-o';
		$this->order = 170;

		// Define additional field properties.
		add_filter( 'wppopups_field_properties_email', [ $this, 'field_properties' ], 5, 3 );

		// Set field to default to required.
		add_filter( 'wppopups_field_new_required', [ $this, 'default_required' ], 10, 2 );

		// Set confirmation status to option wrapper class.
		add_filter( 'wppopups_builder_field_option_class', [ $this, 'field_option_class' ], 10, 2 );
	}

	/**
	 * Define additional field properties.
	 *
	 * @since 1.3.7
	 * @param array $properties
	 * @param array $field
	 * @param array $form_data
	 * @return array
	 */
	public function field_properties( $properties, $field, $form_data ) {

		if ( empty( $field['confirmation'] ) ) {
			return $properties;
		}

		$form_id  = absint( $form_data['id'] );
		$field_id = absint( $field['id'] );

		// Email confirmation setting enabled.
		$props = array(
			'inputs' => array(
				'primary'   => array(
					'block'    => array(
						'wppopups-field-row-block',
						'wppopups-one-half',
						'wppopups-first',
					),
					'class'    => array(
						'wppopups-field-email-primary',
					),
					'sublabel' => array(
						'hidden' => ! empty( $field['sublabel_hide'] ),
						'value'  => esc_html__( 'Email', 'wp-popups-lite' ),
					),
				),
				'secondary' => array(
					'attr'     => array(
						'name'        => "wppopups[fields][{$field_id}][secondary]",
						'value'       => '',
						'placeholder' => ! empty( $field['confirmation_placeholder'] ) ? $field['confirmation_placeholder'] : '',
					),
					'block'    => array(
						'wppopups-field-row-block',
						'wppopups-one-half',
					),
					'class'    => array(
						'wppopups-field-email-secondary',
					),
					'data'     => array(
						'rule-confirm' => '#' . $properties['inputs']['primary']['id'],
					),
					'id'       => "wppopups-{$form_id}-field_{$field_id}-secondary",
					'required' => ! empty( $field['required'] ) ? 'required' : '',
					'sublabel' => array(
						'hidden' => ! empty( $field['sublabel_hide'] ),
						'value'  => esc_html__( 'Confirm Email', 'wp-popups-lite' ),
					),
					'value'    => '',
				),
			),
		);

		$properties = array_merge_recursive( $properties, $props );

		// Input Primary: adjust name.
		$properties['inputs']['primary']['attr']['name'] = "wppopups[fields][{$field_id}][primary]";

		// Input Primary: remove size and error classes.
		$properties['inputs']['primary']['class'] = array_diff(
			$properties['inputs']['primary']['class'],
			array(
				'wppopups-field-' . sanitize_html_class( $field['size'] ),
				'wppopups-error',
			)
		);

		// Input Primary: add error class if needed.
		if ( ! empty( $properties['error']['value']['primary'] ) ) {
			$properties['inputs']['primary']['class'][] = 'wppopups-error';
		}

		// Input Secondary: add error class if needed.
		if ( ! empty( $properties['error']['value']['secondary'] ) ) {
			$properties['inputs']['secondary']['class'][] = 'wppopups-error';
		}

		// Input Secondary: add required class if needed.
		if ( ! empty( $field['required'] ) ) {
			$properties['inputs']['secondary']['class'][] = 'wppopups-field-required';
		}

		return $properties;
	}

	/**
	 * Field should default to being required.
	 *
	 * @since 1.0.9
	 * @param bool $required
	 * @param array $field
	 * @return bool
	 */
	public function default_required( $required, $field ) {

		if ( 'email' === $field['type'] ) {
			return true;
		}
		return $required;
	}

	/**
	 * Add class to field options wrapper to indicate if field confirmation is
	 * enabled.
	 *
	 * @since 1.3.0
	 *
	 * @param string $class
	 * @param array $field
	 *
	 * @return string
	 */
	public function field_option_class( $class, $field ) {

		if ( 'email' === $field['type'] ) {
			if ( isset( $field['confirmation'] ) ) {
				$class = 'wppopups-confirm-enabled';
			} else {
				$class = 'wppopups-confirm-disabled';
			}
		}
		return $class;
	}

	/**
	 * Field options panel inside the builder.
	 *
	 * @since 1.0.0
	 *
	 * @param array $field
	 */
	public function field_options( $field ) {
		/*
		 * Basic field options.
		 */

		// Options open markup.
		$args = array(
			'markup' => 'open',
		);
		$this->field_option( 'basic-options', $field, $args );

		// Label.
		$this->field_option( 'label', $field );

		// Description.
		$this->field_option( 'description', $field );

		// Required toggle.
		$this->field_option( 'required', $field );

		// Confirmation toggle.
		/*$fld = $this->field_element(
			'checkbox',
			$field,
			array(
				'slug'    => 'confirmation',
				'value'   => isset( $field['confirmation'] ) ? '1' : '0',
				'desc'    => esc_html__( 'Enable Email Confirmation', 'wp-popups-lite' ),
				'tooltip' => esc_html__( 'Check this option to ask users to provide an email address twice.', 'wp-popups-lite' ),
			),
			false
		);
		$args = array(
			'slug'    => 'confirmation',
			'content' => $fld,
		);
		$this->field_element( 'row', $field, $args );*/

		// Options close markup.
		$args = array(
			'markup' => 'close',
		);
		$this->field_option( 'basic-options', $field, $args );

		/*
		 * Advanced field options.
		 */

		// Options open markup.
		$args = array(
			'markup' => 'open',
		);
		$this->field_option( 'advanced-options', $field, $args );

		// Size.
		$this->field_option( 'size', $field );

		// Placeholder.
		$this->field_option( 'placeholder', $field );

		// Confirmation Placeholder.
		$lbl = $this->field_element(
			'label',
			$field,
			array(
				'slug'    => 'confirmation_placeholder',
				'value'   => esc_html__( 'Confirmation Placeholder Text', 'wp-popups-lite' ),
				'tooltip' => esc_html__( 'Enter text for the confirmation field placeholder.', 'wp-popups-lite' ),
			),
			false
		);
		$fld = $this->field_element(
			'text',
			$field,
			array(
				'slug'  => 'confirmation_placeholder',
				'value' => ! empty( $field['confirmation_placeholder'] ) ? esc_attr( $field['confirmation_placeholder'] ) : '',
			),
			false
		);
		$args = array(
			'slug'    => 'confirmation_placeholder',
			'content' => $lbl . $fld,
		);
		$this->field_element( 'row', $field, $args );

		// Hide Label.
		$this->field_option( 'label_hide', $field );

		// Hide sub-labels.
		$this->field_option( 'sublabel_hide', $field );

		// Default value.
		$this->field_option( 'default_value', $field );

		// Custom CSS classes.
		$this->field_option( 'css', $field );

		// Options close markup.
		$args = array(
			'markup' => 'close',
		);
		$this->field_option( 'advanced-options', $field, $args );


		// Options Open markup.
		$this->field_option(
			'provider-options',
			$field,
			[ 'markup' => 'open' ]
		);

		do_action( 'wppopups_field_options_provider', $field, $this );

		// Options close markup.
		$this->field_option(
			'provider-options',
			$field,
			[ 'markup' => 'close' ]
		);
	}

	/**
	 * Field preview inside the builder.
	 *
	 * @since 1.0.0
	 * @param array $field
	 */
	public function field_preview( $field ) {

		// Define data.
		$placeholder         = ! empty( $field['placeholder'] ) ? esc_attr( $field['placeholder'] ) : '';
		$confirm_placeholder = ! empty( $field['confirmation_placeholder'] ) ? esc_attr( $field['confirmation_placeholder'] ) : '';
		$confirm             = ! empty( $field['confirmation'] ) ? 'enabled' : 'disabled';

		// Label.
		$this->field_preview_option( 'label', $field );
		?>

		<div class="wppopups-confirm wppopups-confirm-<?php echo $confirm; ?>">

			<div class="wppopups-confirm-primary">
				<input type="email" placeholder="<?php echo $placeholder; ?>" class="primary-input" disabled>
				<label class="wppopups-sub-label"><?php esc_html_e( 'Email', 'wp-popups-lite' ); ?></label>
			</div>

			<div class="wppopups-confirm-confirmation">
				<input type="email" placeholder="<?php echo $confirm_placeholder; ?>" class="secondary-input" disabled>
				<label class="wppopups-sub-label"><?php esc_html_e( 'Confirm Email', 'wp-popups-lite' ); ?></label>
			</div>

		</div>

		<?php
		// Description.
		$this->field_preview_option( 'description', $field );
	}

	/**
	 * Field display on the form front-end.
	 *
	 * @since 1.0.0
	 * @param array $field
	 * @param array $deprecated
	 * @param array $form_data
	 */
	public function field_display( $field, $deprecated, $form_data ) {

		// Define data.
		$form_id      = absint( $form_data['id'] );
		$confirmation = ! empty( $field['confirmation'] );
		$primary      = $field['properties']['inputs']['primary'];
		$secondary    = ! empty( $field['properties']['inputs']['secondary'] ) ? $field['properties']['inputs']['secondary'] : '';

		// Standard email field.
		if ( ! $confirmation ) {

			// Primary field.
			printf(
				'<input type="email" %s %s>',
				wppopups_html_attributes( $primary['id'], $primary['class'], $primary['data'], $primary['attr'] ),
				esc_attr( $primary['required'] )
			);
			$this->field_display_error( 'primary', $field );

		// Confirmation email field configuration.
		} else {

			// Row wrapper.
			echo '<div class="wppopups-field-row wppopups-field-' . sanitize_html_class( $field['size'] ) . '">';

				// Primary field.
				echo '<div ' . wppopups_html_attributes( false, $primary['block'] ) . '>';
					$this->field_display_sublabel( 'primary', 'before', $field );
					printf(
						'<input type="email" %s %s>',
						wppopups_html_attributes( $primary['id'], $primary['class'], $primary['data'], $primary['attr'] ),
						$primary['required']
					);
					$this->field_display_sublabel( 'primary', 'after', $field );
					$this->field_display_error( 'primary', $field );
				echo '</div>';

				// Secondary field.
				echo '<div ' . wppopups_html_attributes( false, $secondary['block'] ) . '>';
					$this->field_display_sublabel( 'secondary', 'before', $field );
					printf(
						'<input type="email" %s %s>',
						wppopups_html_attributes( $secondary['id'], $secondary['class'], $secondary['data'], $secondary['attr'] ),
						$secondary['required']
					);
					$this->field_display_sublabel( 'secondary', 'after', $field );
					$this->field_display_error( 'secondary', $field );
				echo '</div>';

			echo '</div>';

		} // End if().
	}

	/**
	 * Format and sanitize field.
	 *
	 * @since 1.3.0
	 * @param int   $field_id     Field ID.
	 * @param mixed $field_submit Field value that was submitted.
	 * @param array $form_data    Form data and settings.
	 */
	public function format( $field_id, $field_submit, $form_data ) {

		// Define data.
		if ( is_array( $field_submit ) ) {
			$value = ! empty( $field_submit['primary'] ) ? $field_submit['primary'] : '';
		} else {
			$value = ! empty( $field_submit ) ? $field_submit : '';
		}

		$name  = ! empty( $form_data['fields'][ $field_id ] ['label'] ) ? $form_data['fields'][ $field_id ]['label'] : '';

		// Set final field details.
		wppopups()->process->fields[ $field_id ] = array(
			'name'  => sanitize_text_field( $name ),
			'value' => sanitize_text_field( $value ),
			'id'    => absint( $field_id ),
			'type'  => $this->type,
		);
	}

	/**
	 * Validate field on form submit.
	 *
	 * @since 1.0.0
	 *
	 * @param int   $field_id     Field ID.
	 * @param mixed $field_submit Field value that was submitted.
	 * @param array $form_data    Form data and settings.
	 */
	public function validate( $field_id, $field_submit, $form_data ) {

		$form_id = (int) $form_data['id'];

		parent::validate( $field_id, $field_submit, $form_data );

		if ( ! is_array( $field_submit ) && ! empty( $field_submit ) ) {
			$field_submit = array(
				'primary' => $field_submit,
			);
		}

		if ( ! empty( $field_submit['primary'] ) && ! is_email( $field_submit['primary'] ) ) {
			wppopups()->process->errors[ $form_id ][ $field_id ]['primary'] = esc_html__( 'The provided email is not valid.', 'wp-popups-lite' );
		} elseif ( isset( $field_submit['primary'] ) && isset( $field_submit['secondary'] ) && $field_submit['secondary'] !== $field_submit['primary'] ) {
			wppopups()->process->errors[ $form_id ][ $field_id ]['secondary'] = esc_html__( 'The provided emails do not match.', 'wp-popups-lite' );
		}
	}
}

new WPPopups_Field_Email();
