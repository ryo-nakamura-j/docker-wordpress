<?php

/**
 * Paragraph text field.
 *
 * @since 1.0.0
 */
class WPPopups_Field_Textarea extends WPPopups_Field {

	/**
	 * Primary class constructor.
	 *
	 * @since 1.0.0
	 */
	public function init() {

		// Define field type information.
		$this->name  = esc_html__( 'Paragraph Text', 'wp-popups-lite' );
		$this->type  = 'textarea';
		$this->icon  = 'fa-paragraph';
		$this->order = 50;
		add_action( 'wppopups_frontend_js', [ $this, 'frontend_js' ] );
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
		$this->field_option(
			'basic-options',
			$field,
			array(
				'markup' => 'open',
			)
		);

		// Label.
		$this->field_option( 'label', $field );

		// Description.
		$this->field_option( 'description', $field );

		// Required toggle.
		$this->field_option( 'required', $field );

		// Options close markup.
		$this->field_option(
			'basic-options',
			$field,
			array(
				'markup' => 'close',
			)
		);

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

		// Hide label.
		$this->field_option( 'label_hide', $field );

		// Limit length.
		$args = [
			'slug'    => 'limit_enabled',
			'content' => $this->field_element(
				'checkbox',
				$field,
				array(
					'slug'    => 'limit_enabled',
					'value'   => isset( $field['limit_enabled'] ) ? '1' : '0',
					'desc'    => esc_html__( 'Limit Length', 'wp-popups-lite' ),
					'tooltip' => esc_html__( 'Check this option to limit text length by characters or words count.', 'wp-popups-lite' ),
				),
				false
			),
		];
		$this->field_element( 'row', $field, $args );

		$count = $this->field_element(
			'text',
			$field,
			array(
				'type'  => 'number',
				'slug'  => 'limit_count',
				'attrs' => array(
					'min'     => 1,
					'step'    => 1,
					'pattern' => '[0-9]',
				),
				'value' => ! empty( $field['limit_count'] ) ? $field['limit_count'] : 1,
			),
			false
		);

		$mode = $this->field_element(
			'select',
			$field,
			array(
				'slug'    => 'limit_mode',
				'value'   => ! empty( $field['limit_mode'] ) ? esc_attr( $field['limit_mode'] ) : 'characters',
				'options' => array(
					'characters' => esc_html__( 'Characters', 'wp-popups-lite' ),
					'words'      => esc_html__( 'Words', 'wp-popups-lite' ),
				),
			),
			false
		);
		$args = array(
			'slug'    => 'limit_controls',
			'class'   => ! isset( $field['limit_enabled'] ) ? 'wppopups-hide' : '',
			'content' => $count . $mode,
		);
		$this->field_element( 'row', $field, $args );

		// Default value.
		$this->field_option( 'default_value', $field );

		// Custom CSS classes.
		$this->field_option( 'css', $field );

		// Options close markup.
		$this->field_option(
			'advanced-options',
			$field,
			array(
				'markup' => 'close',
			)
		);


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
	 *
	 * @param array $field Field data and settings.
	 */
	public function field_preview( $field ) {

		// Label.
		$this->field_preview_option( 'label', $field );

		// Primary input.
		$placeholder = ! empty( $field['placeholder'] ) ? $field['placeholder'] : '';

		echo '<textarea placeholder="' . esc_attr( $placeholder ) . '" class="primary-input" disabled></textarea>';

		// Description.
		$this->field_preview_option( 'description', $field );
	}

	/**
	 * Field display on the form front-end.
	 *
	 * @since 1.0.0
	 *
	 * @param array $field      Field data and settings.
	 * @param array $deprecated Deprecated.
	 * @param array $form_data  Form data and settings.
	 */
	public function field_display( $field, $deprecated, $form_data ) {

		// Define data.
		$primary = $field['properties']['inputs']['primary'];
		$value   = '';

		if ( isset( $primary['attr']['value'] ) ) {
			$value = wppopups_sanitize_textarea_field( $primary['attr']['value'] );
			unset( $primary['attr']['value'] );
		}

		if ( isset( $field['limit_enabled'] ) ) {
			$limit_count = isset( $field['limit_count'] ) ? absint( $field['limit_count'] ) : 0;
			$limit_mode  = isset( $field['limit_mode'] ) ? sanitize_key( $field['limit_mode'] ) : 'characters';

			$primary['data']['form-id']  = $form_data['id'];
			$primary['data']['field-id'] = $field['id'];

			if ( 'characters' === $limit_mode ) {
				$primary['class'][]            = 'wppopups-limit-characters-enabled';
				$primary['attr']['maxlength']  = $limit_count;
				$primary['data']['text-limit'] = $limit_count;
			} else {
				$primary['class'][]            = 'wppopups-limit-words-enabled';
				$primary['data']['text-limit'] = $limit_count;
			}
		}

		// Primary field.
		printf(
			'<textarea %s %s>%s</textarea>',
			wppopups_html_attributes( $primary['id'], $primary['class'], $primary['data'], $primary['attr'] ),
			$primary['required'], // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			$value // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		);
	}

	/**
	 * Enqueue frontend limit option js.
	 *
	 * @since 1.5.6
	 *
	 * @param array $forms Forms on the current page.
	 */
	public function frontend_js( $popups ) {

		// Get fields.
		$fields = array_map(
			function( $popup ) {
				return empty( $popup->data['fields'] ) ? [] : $popup->data['fields'];
			},
			(array) $popups
		);

		// Make fields flat.
		$fields = array_reduce(
			$fields,
			function( $accumulator, $current ) {
				return array_merge( $accumulator, $current );
			},
			array()
		);

		// Leave only fields with limit.
		$fields = array_filter(
			$fields,
			function( $field ) {
				return isset( $field['type'] ) && $field['type'] === $this->type && isset( $field['limit_enabled'] );
			}
		);

		if ( count( $fields ) ) {
			//$min = \wppopups_get_min_suffix();
			wp_enqueue_script(
				'wppopups-text-limit',
				WPPOPUPS_PLUGIN_URL . "assets/js/text-limit.js",
				[ 'jquery', 'wppopups' ], WPPOPUPS_VERSION, true
			);
		}
	}

	/**
	 * Format and sanitize field.
	 *
	 * @since 1.5.6
	 *
	 * @param int   $field_id     Field ID.
	 * @param mixed $field_submit Field value that was submitted.
	 * @param array $form_data    Form data and settings.
	 */
	public function format( $field_id, $field_submit, $form_data ) {

		$field = $form_data['fields'][ $field_id ];
		if ( is_array( $field_submit ) ) {
			$field_submit = array_filter( $field_submit );
			$field_submit = implode( "\r\n", $field_submit );
		}

		$name = ! empty( $field['label'] ) ? sanitize_text_field( $field['label'] ) : '';

		// Sanitize but keep line breaks.
		$value = wppopups_sanitize_textarea_field( $field_submit );

		if ( isset( $field['limit_enabled'] ) ) {
			$limit = absint( $field['limit_count'] );
			$mode  = sanitize_key( $field['limit_mode'] );

			if ( 'characters' === $mode ) {
				if ( mb_strlen( str_replace( "\r\n", "\n", $value ) ) > $limit ) {
					/* translators: %s - limit characters number. */
					wppopups()->process->errors[ $form_data['id'] ][ $field_id ] = sprintf( _n( 'Text can\'t exceed %d character.', 'Text can\'t exceed %d characters.', $limit, 'wp-popups-lite' ), $limit );
					return;
				}
			} else {
				$words = preg_split( '/[\s,]+/', $value );
				$words = is_array( $words ) ? count( $words ) : 0;
				if ( $words > $limit ) {
					/* translators: %s - limit words number. */
					wppopups()->process->errors[ $form_data['id'] ][ $field_id ] = sprintf( _n( 'Text can\'t exceed %d word.', 'Text can\'t exceed %d words.', $limit, 'wp-popups-lite' ), $limit );
					return;
				}
			}
		}

		wppopups()->process->fields[ $field_id ] = array(
			'name'  => $name,
			'value' => $value,
			'id'    => absint( $field_id ),
			'type'  => $this->type,
		);
	}
}

new WPPopups_Field_Textarea();
