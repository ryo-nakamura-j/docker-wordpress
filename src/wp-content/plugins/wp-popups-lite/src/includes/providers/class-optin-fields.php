<?php

/**
 * Class WPPopups_Optin_Fields adds optins fields into popups
 */
class WPPopups_Optin_Fields {
	/**
	 * WPPopups_Optin_Fields constructor.
	 */
	public function __construct() {

		$this->popups = [];

		// Actions.
		//add_action( 'wppopups_frontend_output_success', [ $this, 'confirmation' ], 10, 3 );
		add_action( 'wppopups_frontend_output', [ $this, 'fields' ], 10, 3 );
		add_action( 'wppopups_display_field_before', [ $this, 'field_container_open' ], 5, 2 );
		add_action( 'wppopups_display_field_before', [ $this, 'field_label' ], 15, 2 );
		add_action( 'wppopups_display_field_before', [ $this, 'field_description' ], 20, 2 );
		add_action( 'wppopups_display_field_after', [ $this, 'field_error' ], 3, 2 );
		add_action( 'wppopups_display_field_after', [ $this, 'field_description' ], 5, 2 );
		add_action( 'wppopups_display_field_after', [ $this, 'field_container_close' ], 15, 2 );

		add_action( 'wppopups_frontend_output', [ $this, 'foot' ], 25, 3 );
		add_action( 'wppopups_frontend_output_form_after', [ $this, 'form_error' ], 10, 2 );
		//add_action( 'wp_enqueue_scripts', array( $this, 'assets_header' ) );
		add_action( 'wp_footer', [ $this, 'assets_scripts' ], 15 );
		//add_action( 'wp_footer', array( $this, 'footer_end' ), 99 );

		add_action( 'wppopups_popup_content_after', [ $this, 'output' ] );

		add_action( 'wppopups_popup_content_after', [ $this, 'add_bottom_content' ], 90 );
	}

	/**
	 * Add botton content of optins
	 * @param $popup
	 */
	public function add_bottom_content( $popup ) {
		if ( isset( $popup->data['bottom_content'] ) && $popup->data['bottom_content']['bottom_content'] ) {
			echo apply_filters( 'wppopups_content', $popup->data['bottom_content']['bottom_content'] );
		}
	}


	/**
	 * Load the assets in footer if needed (archives, widgets, etc).
	 *
	 * @since 1.0.0
	 */
	public function assets_scripts() {

		if ( empty( $this->popups ) ) {
			return;
		}

		$this->assets_css();
		$this->assets_js();

		do_action( 'wppopups_wp_scripts', $this->popups );
	}

	/**
	 * Load the CSS assets for frontend output.
	 *
	 * @since 1.0.0
	 */
	public function assets_css() {

		do_action( 'wppopups_frontend_css', $this->popups );

		// Load CSS per global setting.
		wp_enqueue_style(
			'wppopups-full',
			WPPOPUPS_PLUGIN_URL . 'assets/css/wppopups-full.css',
			array(),
			WPPOPUPS_VERSION
		);
	}

	/**
	 * Load the JS assets for frontend output.
	 *
	 * @since 1.0.0
	 */
	public function assets_js() {

		do_action( 'wppopups_frontend_js', $this->popups );

		// Load jQuery validation library - https://jqueryvalidation.org/.
		wp_enqueue_script(
			'wppopups-validation',
			WPPOPUPS_PLUGIN_URL . 'assets/js/jquery.validate.min.js',
			[ 'jquery' ],
			WPPOPUPS_VERSION,
			true
		);


		// Load mailcheck library - https://github.com/mailcheck/mailcheck.
		//if ( true === wppopups_has_field_type( [ 'email' ], $this->popups, true ) ) {
			wp_enqueue_script(
				'wppopups-mailcheck',
				WPPOPUPS_PLUGIN_URL . 'assets/js/mailcheck.min.js',
				false,
				WPPOPUPS_VERSION,
				true
			);
		//}
	}


	/**
	 * Primary function to render a form on the frontend.
	 *
	 * @since 1.0.0
	 *
	 * @param int  $id Popup ID.
	 */
	public function output( $popup ) {

		if( empty( $popup->data['providers'] ) ) {
			return;
		}

		if( function_exists('wppopups_is_builder_page') && wppopups_is_builder_page() ) {
			return;
		}

		// Basic information.
		$popup_data	= apply_filters( 'wppopups_frontend_popup_data', $popup->data );
		$popup_id	= absint( $popup->id );
		$settings	= $popup_data['settings'];
		$action 	= esc_url_raw( remove_query_arg( 'wppopups' ) );
		$classes 	= wppopups_setting( 'disable-css', '1' ) == '1' ? [ 'wppopups-container-full' ] : [];
		$errors 	= empty( wppopups()->process->errors[ $popup_id ] ) ? [] : wppopups()->process->errors[ $popup_id ];

		// If the form does not contain any fields - do not proceed.
		if ( empty( $popup_data['fields'] ) ) {
			echo '<!-- WPPopups: no fields, form hidden -->';
			return;
		}

		// Before output hook.
		do_action( 'wppopups_frontend_output_before', $popup_data, $popup );

		// Check for error-free completed form.
		if (
			empty( $errors ) &&
			! empty( $popup_data ) &&
			! empty( $_POST['wppopups']['id'] ) &&
			absint( $_POST['wppopups']['id'] ) === $popup_id
		) {
			do_action( 'wppopups_frontend_output_success', $popup_data, false, false );
			wppopups_debug_data( $_POST ); // phpcs:ignore WordPress.Security.NonceVerification.Missing
			return;
		}

		// Allow filter to return early if some condition is not met.
		if ( ! apply_filters( 'wppopups_frontend_load', true, $popup_data, null ) ) {
			do_action( 'wppopups_frontend_not_loaded', $popup_data, $popup );
			return;
		}

		// All checks have passed, so calculate multi-page details for the form.
		$pages = wppopups_get_pagebreak_details( $popup_data );
		if ( $pages ) {
			$this->pages = $pages;
		} else {
			$this->pages = false;
		}

		// Allow final action to be customized - 3rd param ($form) has been deprecated.
		$action = apply_filters( 'wppopups_frontend_form_action', $action, $popup_data, null );

		// Allow form container classes to be filtered and user defined classes.
		$classes = apply_filters( 'wppopups_frontend_container_class', $classes, $popup_data );
		if ( ! empty( $settings['popup_class'] ) ) {
			$classes = array_merge( $classes, explode( ' ', $settings['popup_class'] ) );
		}
		$classes = wppopups_sanitize_classes( $classes, true );

		$popup_classes = [ 'wppopups-validate', 'wppopups-form', 'spu-optin-form' ];

		$popup_atts = [
			'id'    => sprintf( 'wppopups-form-%d', absint( $popup_id ) ),
			'class' => $popup_classes,
			'data'  => [ 'popupid' => absint( $popup_id ), ],
			'atts'  => [
				'method'  => 'post',
				'enctype' => 'multipart/form-data',
				'action'  => esc_url( $action ),
			],
		];


		$popup_atts = apply_filters( 'wppopups_frontend_form_atts', $popup_atts, $popup_data );
		$tag = function_exists('wppopups_is_builder_page') && wppopups_is_builder_page() ? 'div' : 'form';

		// Begin to build the output.
		do_action( 'wppopups_frontend_output_container_before', $popup_data, $popup );

		printf( '<div class="spu-fields-container wppopups-container %s" id="wppopups-%d">', esc_attr( $classes ), absint( $popup_id ) );

		do_action( 'wppopups_frontend_output_form_before', $popup_data, $popup );

		echo '<' . $tag . ' ' . wppopups_html_attributes( $popup_atts['id'], $popup_atts['class'], $popup_atts['data'], $popup_atts['atts'] ) . '>';

		echo '<input type="hidden" name="popup" value="' . esc_attr( $popup_id ) . '"/>';

		do_action( 'wppopups_frontend_output', $popup_data, null, $errors );

		echo '</' . $tag . '>';

		do_action( 'wppopups_frontend_output_form_after', $popup_data, $popup );

		echo '</div>  <!-- .wppopup-container -->';

		do_action( 'wppopups_frontend_output_container_after', $popup_data, $popup );

		// Add form to class property that tracks all forms in a page.
		//$this->popups[ $popup_id ] = $popup_data;
		$this->popups[ $popup_id ] = $popup;

		// Optional debug information if WPPOPUPS_DEBUG is defined.
		wppopups_debug_data( $_POST ); // phpcs:ignore WordPress.Security.NonceVerification.Missing

		// After output hook.
		do_action( 'wppopups_frontend_output_after', $popup_data, $popup );
	}


	/**
	 * Form field area.
	 *
	 * @since 1.0.0
	 *
	 * @param array $popup_data   Popup data and settings.
	 * @param null  $deprecated  Deprecated in v1.3.7, previously was $form object.
	 * @param array $errors      List of all errors filled in WPPopups_Process::process().
	 */
	public function fields( $popup_data, $deprecated, $errors ) {

		// Obviously we need to have form fields to proceed.
		if ( empty( $popup_data['fields'] ) ) {
			return;
		}

		// Form fields area.
		echo '<div class="wppopups-field-container">';

		/**
		 * Core actions on this hook:
		 * Priority / Description
		 * 20         Pagebreak markup (open first page)
		 */
		do_action( 'wppopups_display_fields_before', $popup_data );

		// Loop through all the fields we have.
		foreach ( $popup_data['fields'] as $field ) :

			$field = apply_filters( 'wppopups_field_data', $field, $popup_data );

			if ( empty( $field ) || ! isset( $field['id'] ) ) {
				continue;
			}

			// Get field attributes. Deprecated; Customizations should use
			// field properties instead.
			$attributes = $this->get_field_attributes( $field, $popup_data );

			// Add properties to the field so it's available everywhere.
			$field['properties'] = $this->get_field_properties( $field, $popup_data, $attributes );

			/**
			 * Core actions on this hook:
			 * Priority / Description
			 * 5          Field opening container markup.
			 * 15         Field label.
			 * 20         Field description (depending on position).
			 */
			do_action( 'wppopups_display_field_before', $field, $popup_data );

			/**
			 * Individual field classes use this hook to display the actual
			 * field form elements.
			 * See `field_display` methods in /includes/fields.
			 */
			do_action( "wppopups_display_field_{$field['type']}", $field, $attributes, $popup_data );

			/**
			 * Core actions on this hook:
			 * Priority / Description
			 * 3          Field error messages.
			 * 5          Field description (depending on position).
			 * 15         Field closing container markup.
			 * 20         Pagebreak markup (close previous page, open next)
			 */
			do_action( 'wppopups_display_field_after', $field, $popup_data );

		endforeach;

		/**
		 * Core actions on this hook:
		 * Priority / Description
		 * 5          Pagebreak markup (close last page)
		 */
		do_action( 'wppopups_display_fields_after', $popup_data );

		echo '</div>';
	}


	/**
	 * Form footer area.
	 *
	 * @since 1.0.0
	 *
	 * @param array $popup_data   Form data and settings.
	 * @param null  $deprecated  Deprecated in v1.3.7, previously was $form object.
	 * @param array $errors      List of all errors filled in WPPopups_Process::process().
	 */
	public function foot( $popup_data, $deprecated, $errors ) {

		$optin = isset( $popup_data['optin_styles'] ) ? $popup_data['optin_styles'] : [];

		$submit_text = isset( $optin['submit_text'] ) ? $optin['submit_text'] : wppopups_default_optin_submit_text();
		$submit_text_processing = isset( $optin['submit_text_processing'] ) ? $optin['submit_text_processing'] : wppopups_default_optin_submit_processing_text();
		$submit_class = isset( $optin['submit_class'] ) ? $optin['submit_class'] : '';


		$popup_id  = absint( $popup_data['id'] );
		$submit   = apply_filters( 'wppopups_field_submit', $submit_text, $popup_data );
		$process  = 'aria-live="assertive" ';
		$classes  = '';
		$visible  = $this->pages ? 'style="display:none;"' : '';

		// Check for submit button alt-text.
		if ( ! empty( $submit_text_processing ) ) {
			
			$process .= 'data-alt-text="' . esc_attr( $submit_text_processing ) . '" data-submit-text="' . esc_attr( $submit ) . '"';

		}

		// Check user defined submit button classes.
		if ( ! empty( $submit_class ) ) {
			$classes = wppopups_sanitize_classes( $submit_class );
		}


		// Output footer errors if they exist.
		if ( ! empty( $errors['footer'] ) ) {
			$this->popup_error( 'footer', $errors['footer'] );
		}


		// optin style
		$submit_bg = isset( $optin['submit_bg_color'] ) ? $optin['submit_bg_color'] : wppopups_default_optin_submit_color( $popup_data['settings'] )[0];
		$submit_hover = isset( $optin['submit_bg_color_hover'] ) ? $optin['submit_bg_color_hover'] : wppopups_default_optin_submit_color( $popup_data['settings'] )[1];
		$submit_border = isset( $optin['submit_border_color'] ) ? $optin['submit_border_color'] : wppopups_default_optin_submit_color( $popup_data['settings'] )[2];
		$submit_color = isset( $optin['submit_text_color'] ) ? $optin['submit_text_color'] : wppopups_default_optin_submit_color( $popup_data['settings'] )[3];


		// Submit button area.
		echo '<div class="wppopups-submit-container" ' . $visible . '>';

		echo '<input type="hidden" name="wppopups[id]" value="' . esc_attr( $popup_id ) . '">';

		echo '<input type="hidden" name="wppopups[author]" value="' . absint( get_the_author_meta( 'ID' ) ) . '">';

		if ( is_singular() ) {
			echo '<input type="hidden" name="wppopups[post_id]" value="' . esc_attr( get_the_ID() ) . '">';
		}

		do_action( 'wppopups_display_submit_before', $popup_data );

		printf(
			'<button type="submit" name="wppopups[submit]" class="wppopups-submit-button %s" id="wppopups-submit-button-%d" value="wppopups-submit" %s>%s</button>',
			esc_attr( $classes ),
			esc_attr( $popup_id ),
			$process,
			esc_html( $submit )
		);

		do_action( 'wppopups_display_submit_after', $popup_data );

		echo '</div>';

		?>
		<style type="text/css">
			#spu-<?php echo esc_attr( $popup_id ); ?> .wppopups-submit-button {
				background-color: <?= esc_attr( $submit_bg );?>;
				border-color: <?= esc_attr( $submit_border );?>;
				color: <?= esc_attr( $submit_color );?>;
			}
			#spu-<?php echo esc_attr( $popup_id ); ?> .wppopups-submit-button:hover {
				background-color: <?= esc_attr( $submit_hover );?>;
			}

			#spu-<?php echo esc_attr( $popup_id ); ?> .wppopups-submit-button svg path,
			#spu-<?php echo esc_attr( $popup_id ); ?> .wppopups-submit-button svg rect{
				fill: <?= esc_attr( $submit_color );?>;
			}
		</style>
		<?php
	}

	/**
	 * Return base attributes for a specific field. This is deprecated and
	 * exists for backwards-compatibility purposes. Use field properties instead.
	 *
	 * @since 1.3.7
	 *
	 * @param array $field     Field data and settings.
	 * @param array $popup_data Popup data and settings.
	 *
	 * @return array
	 */
	public function get_field_attributes( $field, $popup_data ) {

		$popup_id 	= absint( $popup_data['id'] );
		$field_id 	= absint( $field['id'] );
		$attributes = [
			'field_class'       => [ 'wppopups-field', 'wppopups-field-' . sanitize_html_class( $field['type'] ) ],
			'field_id'          => [ sprintf( 'wppopups-%d-field_%d-container', $popup_id, $field_id ) ],
			'field_style'       => '',
			'label_class'       => [ 'wppopups-field-label' ],
			'label_id'          => '',
			'description_class' => [ 'wppopups-field-description' ],
			'description_id'    => [],
			'input_id'          => [ sprintf( 'wppopups-%d-field_%d', $popup_id, $field_id ) ],
			'input_class'       => [],
			'input_data'        => [],
		];

		// Check user field defined classes.
		if ( ! empty( $field['css'] ) ) {
			$attributes['field_class'] = array_merge( $attributes['field_class'], wppopups_sanitize_classes( $field['css'], true ) );
		}
		// Check for input column layouts.
		if ( ! empty( $field['input_columns'] ) ) {
			if ( '2' === $field['input_columns'] ) {
				$attributes['field_class'][] = 'wppopups-list-2-columns';
			} elseif ( '3' === $field['input_columns'] ) {
				$attributes['field_class'][] = 'wppopups-list-3-columns';
			} elseif ( 'inline' === $field['input_columns'] ) {
				$attributes['field_class'][] = 'wppopups-list-inline';
			}
		}
		// Check label visibility.
		if ( ! empty( $field['label_hide'] ) ) {
			$attributes['label_class'][] = 'wppopups-label-hide';
		}
		// Check size.
		if ( ! empty( $field['size'] ) ) {
			$attributes['input_class'][] = 'wppopups-field-' . sanitize_html_class( $field['size'] );
		}
		// Check if required.
		if ( ! empty( $field['required'] ) ) {
			$attributes['input_class'][] = 'wppopups-field-required';
		}

		// Check if there are errors.
		if ( ! empty( wppopups()->process->errors[ $popup_id ][ $field_id ] ) ) {
			$attributes['input_class'][] = 'wppopups-error';
		}

		// This filter is deprecated, filter the properties (below) instead.
		$attributes = apply_filters( 'wppopups_field_atts', $attributes, $field, $popup_data );

		return $attributes;
	}

	/**
	 * Return base properties for a specific field.
	 *
	 * @since 1.3.7
	 *
	 * @param array $field      Field data and settings.
	 * @param array $popup_data  Form data and settings.
	 * @param array $attributes List of field attributes.
	 *
	 * @return array
	 */
	public function get_field_properties( $field, $popup_data, $attributes = [] ) {

		if ( empty( $attributes ) ) {
			$attributes = $this->get_field_attributes( $field, $popup_data );
		}

		// This filter is for backwards compatibility purposes.
		$types = [ 'text', 'textarea', 'name', 'number', 'email', 'hidden', 'url', 'html', 'divider', 'password', 'phone', 'address', 'select', 'checkbox', 'radio' ];
		if ( in_array( $field['type'], $types, true ) ) {
			$field = apply_filters( "wppopups_{$field['type']}_field_display", $field, $attributes, $popup_data );
		} elseif ( 'credit-card' === $field['type'] ) {
			$field = apply_filters( 'wppopup_creditcard_field_display', $field, $attributes, $popup_data );
		} elseif ( in_array( $field['type'], array( 'payment-multiple', 'payment-single', 'payment-checkbox' ), true ) ) {
			$filter_field_type = str_replace( '-', '_', $field['type'] );
			$field             = apply_filters( 'wppopups_' . $filter_field_type . '_field_display', $field, $attributes, $popup_data );
		}

		$popup_id  = absint( $popup_data['id'] );
		$field_id = absint( $field['id'] );
		$error    = ! empty( wppopups()->process->errors[ $popup_id ][ $field_id ] ) ? wppopups()->process->errors[ $popup_id ][ $field_id ] : '';

		$properties = array(
			'container'   => array(
				'attr'  => array(
					'style' => $attributes['field_style'],
				),
				'class' => $attributes['field_class'],
				'data'  => array(),
				'id'    => implode( '', array_slice( $attributes['field_id'], 0 ) ),
			),
			'label'       => array(
				'attr'     => array(
					'for' => sprintf( 'wppopups-%d-field_%d', $popup_id, $field_id ),
				),
				'class'    => $attributes['label_class'],
				'data'     => array(),
				'disabled' => ! empty( $field['label_disable'] ) ? true : false,
				'hidden'   => ! empty( $field['label_hide'] ) ? true : false,
				'id'       => $attributes['label_id'],
				'required' => ! empty( $field['required'] ) ? true : false,
				'value'    => ! empty( $field['label'] ) ? $field['label'] : '',
			),
			'inputs'      => array(
				'primary' => array(
					'attr'     => array(
						'name'        => "wppopups[fields][{$field_id}]",
						'value'       => isset( $field['default_value'] ) ? apply_filters( 'wppopups_process_smart_tags', $field['default_value'], $popup_data ) : '',
						'placeholder' => isset( $field['placeholder'] ) ? $field['placeholder'] : '',
					),
					'class'    => $attributes['input_class'],
					'data'     => $attributes['input_data'],
					'id'       => implode( array_slice( $attributes['input_id'], 0 ) ),
					'required' => ! empty( $field['required'] ) ? 'required' : '',
				),
			),
			'error'       => array(
				'attr'  => array(
					'for' => sprintf( 'wppopups-%d-field_%d', $popup_id, $field_id ),
				),
				'class' => array( 'wppopups-error' ),
				'data'  => array(),
				'id'    => '',
				'value' => $error,
			),
			'description' => array(
				'attr'     => array(),
				'class'    => $attributes['description_class'],
				'data'     => array(),
				'id'       => implode( '', array_slice( $attributes['description_id'], 0 ) ),
				'position' => 'after',
				'value'    => ! empty( $field['description'] ) ? apply_filters( 'wppopups_process_smart_tags', $field['description'], $popup_data ) : '',
			),
		);

		$properties = apply_filters( "wppopups_field_properties_{$field['type']}", $properties, $field, $popup_data );
		$properties = apply_filters( 'wppopups_field_properties', $properties, $field, $popup_data );

		return $properties;
	}


	/**
	 * Display the opening container markup for each field.
	 *
	 * @since 1.3.7
	 *
	 * @param array $field     Field data and settings.
	 * @param array $popup_data Form data and settings.
	 */
	public function field_container_open( $field, $popup_data ) {

		$container                     = $field['properties']['container'];
		$container['data']['field-id'] = absint( $field['id'] );

		printf(
			'<div %s>',
			wppopups_html_attributes( $container['id'], $container['class'], $container['data'], $container['attr'] )
		);
	}

	/**
	 * Display the label for each field.
	 *
	 * @since 1.3.7
	 *
	 * @param array $field     Field data and settings.
	 * @param array $popup_data Form data and settings.
	 */
	public function field_label( $field, $popup_data ) {

		$label = $field['properties']['label'];

		// If the label is empty or disabled don't proceed.
		if ( empty( $label['value'] ) || $label['disabled'] ) {
			return;
		}

		$required = $label['required'] ? wppopups_get_field_required_label() : '';

		printf( '<label %s>%s%s</label>',
			wppopups_html_attributes( $label['id'], $label['class'], $label['data'], $label['attr'] ),
			esc_html( $label['value'] ),
			$required
		);
	}

	/**
	 * Display any errors for each field.
	 *
	 * @since 1.3.7
	 *
	 * @param array $field     Field data and settings.
	 * @param array $popup_data Form data and settings.
	 */
	public function field_error( $field, $popup_data ) {

		$error = $field['properties']['error'];

		// If there are no errors don't proceed.
		// Advanced fields with multiple inputs (address, name, etc) errors
		// will be an array and are handled within the respective field class.
		if ( empty( $error['value'] ) || is_array( $error['value'] ) ) {
			return;
		}

		printf( '<label %s>%s</label>',
			wppopups_html_attributes( $error['id'], $error['class'], $error['data'], $error['attr'] ),
			esc_html( $error['value'] )
		);
	}

	/**
	 * Display the description for each field.
	 *
	 * @since 1.3.7
	 *
	 * @param array $field     Field data and settings.
	 * @param array $popup_data Form data and settings.
	 */
	public function field_description( $field, $popup_data ) {

		$action      = current_action();
		$description = $field['properties']['description'];

		// If the description is empty don't proceed.
		if ( empty( $description['value'] ) ) {
			return;
		}

		// Determine positioning.
		if ( 'wppopups_display_field_before' === $action && 'before' !== $description['position'] ) {
			return;
		}
		if ( 'wppopups_display_field_after' === $action && 'after' !== $description['position'] ) {
			return;
		}

		if ( 'before' === $description['position'] ) {
			$description['class'][] = 'before';
		}

		printf( '<div %s>%s</div>',
			wppopups_html_attributes( $description['id'], $description['class'], $description['data'], $description['attr'] ),
			do_shortcode( $description['value'] )
		);
	}

	/**
	 * Display the closing container markup for each field.
	 *
	 * @since 1.3.7
	 *
	 * @param array $field     Field data and settings.
	 * @param array $popup_data Form data and settings.
	 */
	public function field_container_close( $field, $popup_data ) {

		echo '</div>';
	}


	/**
	 * Display any errors for the form.
	 *
	 * @since 1.3.7
	 *
	 * @param array $popup_data    Popup data and settings.
	 * @param array $popup 
	 */
	public function form_error( $popup_data, $popup ) {
		printf('<div class="optin-errors" style="display: none"></div>');
	}

}
new WPPopups_Optin_Fields();