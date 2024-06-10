<?php
/**
 * Builder related functions.
 *
 * @package    WPPopupss
 * @author     WPPopupss
 * @since 2.0.0
 * @license    GPL-2.0+
 * @copyright  Copyright (c) 2016, WP Popups LLC
 */

/**
 * Outputs fields to be used on panels (settings etc).
 *
 * @param string $option
 * @param string $panel
 * @param string $field
 * @param array $popup_data
 * @param string $label
 * @param array $args
 * @param boolean $echo
 *
 * @return string
 * @since 2.0.0
 *
 */
function wppopups_panel_field( $option, $panel, $field, $popup_data, $label, $args = [], $echo = true ) {

	// Required params.
	if ( empty( $option ) || empty( $panel ) || empty( $field ) ) {
		return '';
	}

	// Setup basic vars.
	$panel       = esc_attr( $panel );
	$field       = esc_attr( $field );
	$panel_id    = sanitize_html_class( $panel );
	$parent      = ! empty( $args['parent'] ) ? esc_attr( $args['parent'] ) : '';
	$subsection  = ! empty( $args['subsection'] ) ? esc_attr( $args['subsection'] ) : '';
	$label       = ! empty( $label ) ? esc_html( $label ) : '';
	$check_label = ! empty( $args['checkbox_label'] ) ? esc_html( $args['checkbox_label'] ) : '';
	$class       = ! empty( $args['class'] ) ? esc_attr( $args['class'] ) : '';
	$input_class = ! empty( $args['input_class'] ) ? esc_attr( $args['input_class'] ) : '';
	$default     = isset( $args['default'] ) ? $args['default'] : '';
	$placeholder = ! empty( $args['placeholder'] ) ? esc_attr( $args['placeholder'] ) : '';
	$attributes	 = ! empty( $args['attributes'] ) ? (array)$args['attributes'] : [];
	$data_attr   = '';
	$output      = '';

	// Check if we should store values in a parent array.
	if ( ! empty( $parent ) ) {
		if ( ! empty( $subsection ) ) {
			$field_name = sprintf( '%s[%s][%s][%s]', $parent, $panel, $subsection, $field );
			$value      = isset( $popup_data[ $parent ][ $panel ][ $subsection ][ $field ] ) ? $popup_data[ $parent ][ $panel ][ $subsection ][ $field ] : $default;
			$panel_id   = sanitize_html_class( $panel . '-' . $subsection );
		} else {
			$field_name = sprintf( '%s[%s][%s]', $parent, $panel, $field );
			$value      = isset( $popup_data[ $parent ][ $panel ][ $field ] ) ? $popup_data[ $parent ][ $panel ][ $field ] : $default;
		}
	} else {
		$field_name = sprintf( '%s[%s]', $panel, $field );
		$value      = isset( $popup_data[ $panel ][ $field ] ) ? $popup_data[ $panel ][ $field ] : $default;
	}

	// Check for data attributes.
	if ( ! empty( $args['data'] ) ) {
		foreach ( $args['data'] as $key => $val ) {
			if ( is_array( $val ) ) {
				$val = wp_json_encode( $val );
			}
			$data_attr .= ' data-' . $key . '=\'' . $val . '\'';
		}
	}
	if( isset( $args['premium_field'] ) && $args['premium_field'] ) {
		$data_attr .= ' disabled="disabled"';
	}

	foreach( $attributes as $attr_key => $attr_value ) {
		$data_attr .= ' ' . sanitize_key($attr_key) . '="' . sanitize_text_field($attr_value) . '"';
	}

	// Determine what field type to output.
	switch ( $option ) {

		// Text input.
		case 'text':
			$type   = ! empty( $args['type'] ) ? esc_attr( $args['type'] ) : 'text';
			$output = sprintf(
				'<input type="%s" id="wppopups-panel-field-%s-%s" name="%s" value="%s" placeholder="%s" class="%s" %s>',
				$type,
				sanitize_html_class( $panel_id ),
				sanitize_html_class( $field ),
				$field_name,
				esc_attr( $value ),
				$placeholder,
				$input_class,
				$data_attr
			);
			break;
		// Bg image.
		case 'img_upload':
			$output = sprintf(
				'<input type="hidden" id="wppopups-panel-field-%s-%s" name="%s" value="%s" class="%s" %s/>',
				sanitize_html_class( $panel_id ),
				sanitize_html_class( $field ),
				$field_name,
				esc_attr( $value ),
				$input_class,
				$data_attr
			);
			$output .= '<input type="button" id="spu-upload"    class="button meta-box-upload-button wppopups-image-upload-add"        value="Upload" />
			<input type="button" id="spu-remove" class="button meta-box-upload-button-remove wppopups-image-upload-remove" value="Remove" />
			<div class="image-preview"><img src="' . esc_attr( $value ) . '" alt=""/> </div>';
			break;

		// Textarea.
		case 'textarea':
			$rows   = ! empty( $args['rows'] ) ? (int) $args['rows'] : '3';
			$output = sprintf(
				'<textarea id="wppopups-panel-field-%s-%s" name="%s" rows="%d" placeholder="%s" class="%s" %s>%s</textarea>',
				sanitize_html_class( $panel_id ),
				sanitize_html_class( $field ),
				$field_name,
				$rows,
				$placeholder,
				$input_class,
				$data_attr,
				esc_textarea( $value )
			);
			break;

		// TinyMCE.
		case 'tinymce':
			$args                  = wp_parse_args(
				$args['tinymce'], [
					'media_buttons' => false,
					'teeny'         => false,
				]
			);
			$args['textarea_name'] = $field_name;
			$id                    = 'wppopups-panel-field-' . sanitize_html_class( $panel_id ) . '-' . sanitize_html_class( $field );
			$id                    = str_replace( '-', '_', $id );
			ob_start();
			wp_editor( $value, $id, $args );
			$output = ob_get_clean();
			break;

		// Checkbox.
		case 'checkbox':
			$checked = checked( '1', $value, false );
			$output  = sprintf(
				'<input type="checkbox" id="wppopups-panel-field-%s-%s" name="%s" value="1" class="%s" %s %s>',
				sanitize_html_class( $panel_id ),
				sanitize_html_class( $field ),
				$field_name,
				$input_class,
				$checked,
				$data_attr
			);
			$output  .= sprintf(
				'<label for="wppopups-panel-field-%s-%s" class="inline">%s',
				sanitize_html_class( $panel_id ),
				sanitize_html_class( $field ),
				$check_label
			);
			/*if ( ! empty( $args['tooltip'] ) ) {
				$output .= sprintf( ' <i class="fa fa-question-circle wppopups-help-tooltip" title="%s"></i>', esc_attr( $args['tooltip'] ) );
			}*/
			$output .= '</label>';
			break;

		// Radio.
		case 'radio':
			$options = $args['options'];
			$x       = 1;
			$output  = '';
			foreach ( $options as $key => $item ) {
				if ( empty( $item['label'] ) ) {
					continue;
				}
				$checked = checked( $key, $value, false );
				$output  .= sprintf(
					'<span class="row"><input type="radio" id="wppopups-panel-field-%s-%s-%d" name="%s" value="%s" class="%s" %s %s>',
					sanitize_html_class( $panel_id ),
					sanitize_html_class( $field ),
					$x,
					$field_name,
					$key,
					$input_class,
					$checked,
					$data_attr
				);
				$output  .= sprintf(
					'<label for="wppopups-panel-field-%s-%s-%d" class="inline">%s',
					sanitize_html_class( $panel_id ),
					sanitize_html_class( $field ),
					$x,
					$item['label']
				);
				if ( ! empty( $item['tooltip'] ) ) {
					$output .= sprintf( ' <i class="fa fa-question-circle wppopups-help-tooltip" title="%s"></i>', esc_attr( $item['tooltip'] ) );
				}
				$output .= '</label></span>';
				$x ++;
			}
			break;

		// Select.
		case 'select':
			if ( empty( $args['options'] ) && empty( $args['field_map'] ) ) {
				return '';
			}

			$input_class .= ' choicesjs-select';
			$options     = $args['options'];

			$output = sprintf(
				'<select id="wppopups-panel-field-%s-%s" name="%s" class="%s" %s>',
				sanitize_html_class( $panel_id ),
				sanitize_html_class( $field ),
				$field_name,
				$input_class,
				$data_attr
			);

			if ( ! empty( $placeholder ) ) {
				$output .= '<option value="">' . $placeholder . '</option>';
			}

			foreach ( $options as $key => $item ) {
				if ( is_array( $item ) ) {
					$output .= sprintf( '<optgroup label="%s">', esc_attr( $key ) );
					foreach ( $item as $option_value => $label ) {
						$output .= sprintf( '<option value="%s" %s>%s</option>', esc_attr( $option_value ), selected( $option_value, $value, false ), $label );
					}
					$output .= '</optgroup>';
				} else {
					$output .= sprintf( '<option value="%s" %s>%s</option>', esc_attr( $key ), selected( $key, $value, false ), $item );
				}
			}

			$output .= '</select>';
			break;
	}

	// Put the pieces together.
	$field_open = sprintf(
		'<div id="wppopups-panel-field-%s-%s-wrap" class="wppopups-panel-field %s %s ' . ( isset( $args['premium_field'] ) && $args['premium_field'] ? 'premium-only' : '' ) . '">',
		sanitize_html_class( $panel_id ),
		sanitize_html_class( $field ),
		$class,
		'wppopups-panel-field-' . sanitize_html_class( $option )
	);
	$field_open .= ! empty( $args['before'] ) ? $args['before'] : '';
	if ( ! empty( $label ) ) {
		$field_label = sprintf(
			'<label for="wppopups-panel-field-%s-%s">%s',
			sanitize_html_class( $panel_id ),
			sanitize_html_class( $field ),
			$label
		);
		if ( ! empty( $args['tooltip'] ) ) {
			$field_label .= sprintf( ' <i class="fa fa-question-circle wppopups-help-tooltip" title="%s"></i>', esc_attr( $args['tooltip'] ) );
		}
		if ( ! empty( $args['after_tooltip'] ) ) {
			$field_label .= $args['after_tooltip'];
		}
		$field_label .= '</label>';
	} else {
		$field_label = '';
	}


	$field_close = '';

	if( ! empty( $args['after'] ) )
		$field_close = sprintf('<span class="after">%s</span>', $args['after']);

	if ( isset( $args['premium_field'] ) && $args['premium_field'] ) {

		$field_close .=
			'<p>' .
			sprintf(
				wp_kses(
				/* translators: %s - WPPopups.com upgrade URL. */
					__( 'To unlock all features consider <a href="%s" target="_blank" rel="noopener noreferrer" class="btn-green btn-small wppopups-upgrade-link wppopups-upgrade-modal">Upgrading to Pro</a>.', 'wp-popups-lite' ),
					[
						'a' => [
							'href'   => [],
							'class'  => [],
							'target' => [],
							'rel'    => [],
						],
					]
				),
				wppopups_admin_upgrade_link()
			) .
			'</p>';
	}
	$field_close .= '</div>';
	$output      = $field_open . $field_label . $output . $field_close;

	// Wash our hands.
	if ( $echo ) {
		echo $output; // phpcs:ignore
	} else {
		return $output;
	}
}


/**
 * Outputs fields to be used on panels (settings etc).
 *
 * @param $field_type
 * @param $rule_id
 * @param $group_key
 * @param string $field
 * @param array $popup_data
 * @param string $label
 * @param array $args
 * @param boolean $echo
 *
 * @return string
 * @since 2.0.0
 *
 */
function wppopups_rules_field( $field_name = 'rules', $field_type = 'text', $rule_id = 0, $group_key = 0, $field = '', $rule_data = [], $label = '', $args = [], $echo = true ) {

	// Setup basic vars.
	$field     = esc_attr( $field );
	$panel     = 'rules_' . $field;
	$group_key = esc_attr( $group_key );
	$rule_id   = esc_attr( $rule_id );
	$panel_id  = esc_attr( $group_key . '_' . $rule_id );

	$label       = ! empty( $label ) ? esc_html( $label ) : '';
	$input_class = ! empty( $args['input_class'] ) ? esc_attr( $args['input_class'] ) : '';
	if ( 'select' === $field_type && ! isset( $args['clean_select'] ) ) {
		$input_class .= ' choicesjs-select';
	}
	$default     = isset( $args['default'] ) ? $args['default'] : '';
	$placeholder = ! empty( $args['placeholder'] ) ? esc_attr( $args['placeholder'] ) : '';
	$data_attr   = '';
	$output      = '';

	$field_name = sprintf( '%s[%s][%s][%s]', $field_name, $group_key, $rule_id, $field );
	$value      = isset( $rule_data[ $group_key ][ $rule_id ][ $field ] ) ? $rule_data[ $group_key ][ $rule_id ][ $field ] : $default;

	// Check for data attributes.
	if ( ! empty( $args['data'] ) ) {
		foreach ( $args['data'] as $key => $val ) {
			if ( is_array( $val ) ) {
				$val = wp_json_encode( $val );
			}
			$data_attr .= ' data-' . $key . '=\'' . $val . '\'';
		}
	}
	// Check for other attributes.
	if ( ! empty( $args['attributes'] ) ) {
		foreach ( $args['attributes'] as $key => $val ) {
			if ( is_array( $val ) ) {
				$val = wp_json_encode( $val );
			}
			$data_attr .= ' ' . $key . '=\'' . $val . '\'';
		}
	}

	// Determine what field type to output.
	switch ( $field_type ) {
		case 'text':
		case 'number':
			$type        = ! empty( $args['type'] ) ? esc_attr( $args['type'] ) : ( $field_type != 'text' ? $field_type : 'text' );
			$input_class .= ' choices__inner';
			$output      = sprintf(
				'<input type="%s" id="wppopups-panel-field-%s-%s" name="%s" value="%s" placeholder="%s" class="%s" %s>',
				$type,
				sanitize_html_class( $panel_id ),
				sanitize_html_class( $field ),
				$field_name,
				esc_attr( $value ),
				$placeholder,
				$input_class,
				$data_attr
			);
			break;
		// Select.
		case 'select':
			if ( empty( $args['options'] ) ) {
				return '';
			}

			$options = $args['options'];

			$output = sprintf(
				'<select id="wppopups-panel-field-%s-%s" name="%s" class="%s" %s>',
				sanitize_html_class( $panel_id ),
				sanitize_html_class( $field ),
				$field_name,
				$input_class,
				$data_attr
			);

			if ( ! empty( $placeholder ) ) {
				$output .= '<option value="">' . $placeholder . '</option>';
			}

			foreach ( $options as $key => $item ) {
				if ( is_array( $item ) ) {
					$output .= sprintf( '<optgroup label="%s">', esc_attr( $key ) );
					foreach ( $item as $option_value => $option_label ) {
						$output .= sprintf( '<option value="%s" %s>%s</option>', esc_attr( $option_value ), selected( $option_value, $value, false ), $option_label );
					}
					$output .= '</optgroup>';
				} else {
					$output .= sprintf( '<option value="%s" %s>%s</option>', esc_attr( $key ), selected( $key, $value, false ), $item );
				}
			}

			$output .= '</select>';
			break;
	}

	// Put the pieces together.
	$field_open = '';
	$field_open .= ! empty( $args['before'] ) ? $args['before'] : '';
	if ( ! empty( $label ) ) {
		$field_label = sprintf(
			'<label for="wppopups-panel-field-%s-%s">%s',
			sanitize_html_class( $panel_id ),
			sanitize_html_class( $field ),
			$label
		);
		if ( ! empty( $args['tooltip'] ) ) {
			$field_label .= sprintf( ' <i class="fa fa-question-circle wppopups-help-tooltip" title="%s"></i>', esc_attr( $args['tooltip'] ) );
		}
		if ( ! empty( $args['after_tooltip'] ) ) {
			$field_label .= $args['after_tooltip'];
		}
		$field_label .= '</label>';
	} else {
		$field_label = '';
	}
	$field_close = ! empty( $args['after'] ) ? $args['after'] : '';
	$output      = $field_open . $field_label . $output . $field_close;

	// Wash our hands.
	if ( $echo ) {
		echo $output; // phpcs:ignore
	} else {
		return $output;
	}
}

/**
 * Outputs fields to be used on trigger panel (settings etc).
 *
 * @param $field_type
 * @param $key
 * @param string $field
 * @param array $popup_data
 * @param string $label
 * @param array $args
 * @param boolean $echo
 *
 * @return string
 * @since 2.0.0
 *
 */
function wppopups_triggers_field( $field_type, $key, $field, $popup_data, $label, $args = [], $echo = true ) {

	// Setup basic vars.
	$field       = esc_attr( $field );
	$label       = ! empty( $label ) ? esc_html( $label ) : '';
	$input_class = ! empty( $args['input_class'] ) ? esc_attr( $args['input_class'] ) : '';
	if ( 'select' === $field_type && ! isset( $args['clean_select'] ) ) {
		$input_class .= ' choicesjs-select';
	}
	$default     = isset( $args['default'] ) ? $args['default'] : '';
	$placeholder = ! empty( $args['placeholder'] ) ? esc_attr( $args['placeholder'] ) : '';
	$data_attr   = '';
	$output      = '';

	$field_name = sprintf( '%s[%s][%s]', 'triggers', $key, $field );

	$value = isset( $popup_data['triggers'][ $key ][ $field ] ) ? $popup_data['triggers'][ $key ][ $field ] : $default;

	// Check for data attributes.
	if ( ! empty( $args['data'] ) ) {
		foreach ( $args['data'] as $key => $val ) {
			if ( is_array( $val ) ) {
				$val = wp_json_encode( $val );
			}
			$data_attr .= ' data-' . $key . '=\'' . $val . '\'';
		}
	}
	// Check for other attributes.
	if ( ! empty( $args['attributes'] ) ) {
		foreach ( $args['attributes'] as $key => $val ) {
			if ( is_array( $val ) ) {
				$val = wp_json_encode( $val );
			}
			$data_attr .= ' ' . $key . '=\'' . $val . '\'';
		}
	}

	// Determine what field type to output.
	switch ( $field_type ) {
		case 'text':
		case 'number':
			$input_class .= ' choices__inner';
			$output      = sprintf(
				'<input type="%s" id="wppopups-panel-field-%s-%s" name="%s" value="%s" placeholder="%s" class="%s" %s>',
				$field_type,
				'triggers',
				sanitize_html_class( $field ),
				$field_name,
				esc_attr( $value ),
				$placeholder,
				$input_class,
				$data_attr
			);
			break;
		// Select.
		case 'select':
			if ( empty( $args['options'] ) ) {
				return '';
			}

			$options = $args['options'];

			$output = sprintf(
				'<select id="wppopups-panel-field-%s-%s" name="%s" class="%s" %s>',
				'triggers',
				sanitize_html_class( $field ),
				$field_name,
				$input_class,
				$data_attr
			);

			if ( ! empty( $placeholder ) ) {
				$output .= '<option value="">' . $placeholder . '</option>';
			}

			foreach ( $options as $key => $item ) {
				if ( is_array( $item ) ) {
					$output .= sprintf( '<optgroup label="%s">', esc_attr( $key ) );
					foreach ( $item as $option_value => $option_label ) {
						$output .= sprintf( '<option value="%s" %s>%s</option>', esc_attr( $option_value ), selected( $option_value, $value, false ), $option_label );
					}
					$output .= '</optgroup>';
				} else {
					$output .= sprintf( '<option value="%s" %s>%s</option>', esc_attr( $key ), selected( $key, $value, false ), $item );
				}
			}

			$output .= '</select>';
			break;
	}

	// Put the pieces together.
	$field_open = '';
	$field_open .= ! empty( $args['before'] ) ? $args['before'] : '';
	if ( ! empty( $label ) ) {
		$field_label = sprintf(
			'<label for="wppopups-panel-field-%s-%s">%s',
			'triggers',
			sanitize_html_class( $field ),
			$label
		);
		if ( ! empty( $args['tooltip'] ) ) {
			$field_label .= sprintf( ' <i class="fa fa-question-circle wppopups-help-tooltip" title="%s"></i>', esc_attr( $args['tooltip'] ) );
		}
		if ( ! empty( $args['after_tooltip'] ) ) {
			$field_label .= $args['after_tooltip'];
		}
		$field_label .= '</label>';
	} else {
		$field_label = '';
	}
	$field_close = ! empty( $args['after'] ) ? $args['after'] : '';
	$output      = $field_open . $field_label . $output . $field_close;

	// Wash our hands.
	if ( $echo ) {
		echo $output; // phpcs:ignore
	} else {
		return $output;
	}
}


/**
 * Display an error message. Used on overview-table
 */
function wppopups_security_failed() {
	?>
	<div class="notice updated">
		<p>
			<?php esc_html_e( 'Security check failed. Please try again.', 'wp-popups-lite' ); ?>
		</p>
	</div>
	<?php
}


/**
 * Get the list of allowed tags, used in pair with wp_kses() function.
 * This allows getting rid of all potentially harmful HTML tags and attributes.
 *
 * @since 1.5.9
 *
 * @return array Allowed Tags.
 */
function wppopups_builder_preview_get_allowed_tags() {

	static $allowed_tags;

	if ( ! empty( $allowed_tags ) ) {
		return $allowed_tags;
	}

	$atts = [ 'align', 'class', 'type', 'id', 'for', 'style', 'src', 'rel', 'href', 'target', 'value', 'width', 'height' ];
	$tags = [ 'label', 'iframe', 'style', 'button', 'strong', 'small', 'table', 'span', 'abbr', 'code', 'pre', 'div', 'img', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'ol', 'ul', 'li', 'em', 'hr', 'br', 'th', 'tr', 'td', 'p', 'a', 'b', 'i' ];

	$allowed_atts = array_fill_keys( $atts, [] );
	$allowed_tags = array_fill_keys( $tags, $allowed_atts );

	return $allowed_tags;
}