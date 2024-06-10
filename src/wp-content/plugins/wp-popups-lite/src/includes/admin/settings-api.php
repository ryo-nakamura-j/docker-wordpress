<?php
/**
 * Settings API.
 *
 * @package    WPPopups
 * @author     WPPopups
 * @since 2.0.0
 * @license    GPL-2.0+
 * @copyright  Copyright (c) 2017, WP Popups LLC
 */

/**
 * Settings output wrapper.
 *
 * @param array $args
 *
 * @return string
 * @since 2.0.0
 *
 */
function wppopups_settings_output_field( $args ) {

	// Define default callback for this field type.
	$callback = ! empty( $args['type'] ) && function_exists( 'wppopups_settings_' . $args['type'] . '_callback' ) ? 'wppopups_settings_' . $args['type'] . '_callback' : 'wppopups_settings_missing_callback';

	// Allow custom callback to be provided via arg.
	if ( ! empty( $args['callback'] ) && function_exists( $args['callback'] ) ) {
		$callback = $args['callback'];
	}

	// Store returned markup from callback.
	$field = call_user_func( $callback, $args );

	// Allow arg to bypass standard field wrap for custom display.
	if ( ! empty( $args['wrap'] ) ) {
		return $field;
	}

	// Custom row classes.
	$class = ! empty( $args['class'] ) ? wppopups_sanitize_classes( (array) $args['class'], true ) : '';

	// Build standard field markup and return.
	$output = '<div class="wppopups-setting-row wppopups-setting-row-' . sanitize_html_class( $args['type'] ) . ' wppopups-clear ' . $class . '" id="wppopups-setting-row-' . wppopups_sanitize_key( $args['id'] ) . '">';

	if ( ! empty( $args['name'] ) && empty( $args['no_label'] ) ) {
		$output .= '<span class="wppopups-setting-label ' . ( isset( $args['premium_field'] ) && $args['premium_field'] ? 'premium-only' : '' ) . '">';
		$output .= '<label for="wppopups-setting-' . wppopups_sanitize_key( $args['id'] ) . '">' . esc_html( $args['name'] ) . '</label>';
		$output .= '</span>';
	}

	$output .= '<span class="wppopups-setting-field ' . ( isset( $args['premium_field'] ) && $args['premium_field'] ? 'premium-only' : '' ) . '">';
	$output .= $field;
	$output .= '</span>';

	$output .= '</div>';

	$output = apply_filters( 'wppopups_output_field_' . $args['type'], $output, $args );

	return $output;
}

/**
 * Missing Callback.
 *
 * If a function is missing for settings callbacks alert the user.
 *
 * @param array $args Arguments passed by the setting.
 *
 * @return string
 * @since 2.0.0
 *
 */
function wppopups_settings_missing_callback( $args ) {

	return sprintf(
	/* translators: %s - ID of a setting. */
		esc_html__( 'The callback function used for the %s setting is missing.', 'wp-popups-lite' ),
		'<strong>' . wppopups_sanitize_key( $args['id'] ) . '</strong>'
	);
}

/**
 * Settings content field callback.
 *
 * @param array $args
 *
 * @return string
 * @since 2.0.0
 *
 */
function wppopups_settings_content_callback( $args ) {
	return ! empty( $args['content'] ) ? $args['content'] : '';
}

/**
 * Settings license field callback.
 *
 * @param array $args
 *
 * @return string
 * @since 2.0.0
 *
 */
function wppopups_settings_license_callback( $args ) {

	// Lite users don't need to worry about license keys.
	if ( ! isset( $args['addon_license'] ) && ! ( wppopups()->pro || ! class_exists( 'WPPopups_License' ) ) ) {
		$output = '<p>' . esc_html__( 'You\'re using WP Popups Lite - no license needed. This is totally free!', 'wp-popups-lite' ) . '</p>';
		$output .=
			'<p>' .
			sprintf(
				wp_kses(
				/* translators: %s - WPPopups.com upgrade URL. */
					__( 'To unlock all features consider <a href="%s" target="_blank" rel="noopener noreferrer" class="wppopups-upgrade-modal">upgrading to Pro</a>.', 'wp-popups-lite' ),
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

		return $output;
	}
	$option_name = isset( $args['addon_license'] ) ? $args['addon_license'] : 'wppopups_license';
	$item_id = isset( $args['item_id'] ) ? $args['item_id'] : '';
	$key = wppopups_setting( 'key', '', $option_name );

	$output = '<input type="password" class="wppopups-setting-license-key" id="input-' . esc_attr( $option_name ) . '" value="' . esc_attr( $key ) . '" />';
	$output .= '<button id="button-' . esc_attr( $option_name ) . '" data-item-id="' . esc_attr( $item_id ) . '" data-key="' . esc_attr( $option_name ) . '" class="wppopups-setting-license-key-verify wppopups-btn wppopups-btn-md wppopups-btn-blue">' . esc_html__( 'Verify Key', 'wp-popups-lite' ) . '</button>';

	// Offer option to deactivate the key.
	$class  = empty( $key ) ? 'wppopups-hide' : '';
	$output .= '<button id="button-deactivate-' . esc_attr( $option_name ) . '" data-item-id="' . esc_attr( $item_id ) . '"  data-key="' . esc_attr( $option_name ) . '" class="wppopups-setting-license-key-deactivate wppopups-btn wppopups-btn-md wppopups-btn-light-grey ' . $class . '">' . esc_html__( 'Deactivate Key', 'wp-popups-lite' ) . '</button>';

	return $output;
}

/**
 * Settings text input field callback.
 *
 * @param array $args
 *
 * @return string
 * @since 2.0.0
 *
 */
function wppopups_settings_text_callback( $args ) {

	$default  = isset( $args['default'] ) ? esc_html( $args['default'] ) : '';
	$placeholder  = isset( $args['placeholder'] ) ? esc_html( $args['placeholder'] ) : '';
	$value    = wppopups_setting( $args['id'], $default );
	$id       = wppopups_sanitize_key( $args['id'] );
	$disabled = isset( $args['premium_field'] ) && $args['premium_field'] ? 'disabled' : '';

	$output = '<input type="text" id="wppopups-setting-' . $id . '" name="' . $id . '" placeholder="' . esc_attr( $placeholder ) . '" value="' . esc_attr( $value ) . '" ' . $disabled . '>';

	if ( ! empty( $args['desc'] ) ) {
		$output .= '<p class="desc">' . wp_kses_post( $args['desc'] ) . '</p>';
	}
	if ( isset( $args['premium_field'] ) && $args['premium_field'] ) {

		$output .=
			'<p>' .
			sprintf(
				wp_kses(
				/* translators: %s - WPPopups.com upgrade URL. */
					__( 'To unlock all features consider <a href="%s" target="_blank" rel="noopener noreferrer" class="wppopups-upgrade-modal">upgrading to Pro</a>.', 'wp-popups-lite' ),
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


	return $output;
}


/**
 * Settings number input field callback.
 *
 * @param array $args
 *
 * @return string
 * @since 2.0.0
 *
 */
function wppopups_settings_number_callback( $args ) {

	$default  = isset( $args['default'] ) ? esc_html( $args['default'] ) : '';
	$placeholder  = isset( $args['placeholder'] ) ? esc_html( $args['placeholder'] ) : '';
	$value    = wppopups_setting( $args['id'], $default );
	$id       = wppopups_sanitize_key( $args['id'] );
	$step     = isset($args['step']) ? abs( $args['step'] ) : 1;
	$disabled = isset( $args['premium_field'] ) && $args['premium_field'] ? 'disabled' : '';

	$output = '<input type="number" step="'.$step.'" id="wppopups-setting-' . $id . '" name="' . $id . '" placeholder="' . esc_attr( $placeholder ) . '" value="' . esc_attr( $value ) . '" ' . $disabled . '>';

	if ( ! empty( $args['desc'] ) ) {
		$output .= '<p class="desc">' . wp_kses_post( $args['desc'] ) . '</p>';
	}
	if ( isset( $args['premium_field'] ) && $args['premium_field'] ) {

		$output .=
			'<p>' .
			sprintf(
				wp_kses(
				/* translators: %s - WPPopups.com upgrade URL. */
					__( 'To unlock all features consider <a href="%s" target="_blank" rel="noopener noreferrer" class="wppopups-upgrade-modal">upgrading to Pro</a>.', 'wp-popups-lite' ),
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


	return $output;
}


/**
 * Settings select field callback.
 *
 * @param array $args
 *
 * @return string
 * @since 2.0.0
 *
 */
function wppopups_settings_select_callback( $args ) {

	$default = isset( $args['default'] ) ? esc_html( $args['default'] ) : '';
	$value   = wppopups_setting( $args['id'], $default );
	$id      = wppopups_sanitize_key( $args['id'] );
	$class   = ! empty( $args['choicesjs'] ) ? 'choicesjs-select' : '';
	$choices = ! empty( $args['choicesjs'] ) ? true : false;
	$data    = '';

	if ( $choices && ! empty( $args['search'] ) ) {
		$data = ' data-search="true"';
	}

	$output = $choices ? '<span class="choicesjs-select-wrap">' : '';
	$output .= '<select id="wppopups-setting-' . $id . '" name="' . $id . '" class="' . $class . '"' . $data . '>';

	foreach ( $args['options'] as $option => $name ) {
		$selected = selected( $value, $option, false );
		$output   .= '<option value="' . esc_attr( $option ) . '" ' . $selected . '>' . esc_html( $name ) . '</option>';
	}

	$output .= '</select>';
	$output .= $choices ? '</span>' : '';

	if ( ! empty( $args['desc'] ) ) {
		$output .= '<p class="desc">' . wp_kses_post( $args['desc'] ) . '</p>';
	}

	return $output;
}

/**
 * Settings checkbox field callback.
 *
 * @param array $args
 *
 * @return string
 * @since 2.0.0
 *
 */
function wppopups_settings_checkbox_callback( $args ) {

	$value   = wppopups_setting( $args['id'] );
	$id      = wppopups_sanitize_key( $args['id'] );
	$checked = ! empty( $value ) ? checked( 1, $value, false ) : '';

	$output = '<input type="checkbox" id="wppopups-setting-' . $id . '" name="' . $id . '" ' . $checked . '>';

	if ( ! empty( $args['desc'] ) ) {
		$output .= '<p class="desc">' . wp_kses_post( $args['desc'] ) . '</p>';
	}

	return $output;
}

/**
 * Settings radio field callback.
 *
 * @param array $args
 *
 * @return string
 * @since 2.0.0
 *
 */
function wppopups_settings_radio_callback( $args ) {

	$default = isset( $args['default'] ) ? esc_html( $args['default'] ) : '';
	$value   = wppopups_setting( $args['id'], $default );
	$id      = wppopups_sanitize_key( $args['id'] );
	$output  = '';
	$x       = 1;

	foreach ( $args['options'] as $option => $name ) {

		$checked = checked( $value, $option, false );
		$output  .= '<label for="wppopups-setting-' . $id . '[' . $x . ']" class="option-' . sanitize_html_class( $option ) . '">';
		$output  .= '<input type="radio" id="wppopups-setting-' . $id . '[' . $x . ']" name="' . $id . '" value="' . esc_attr( $option ) . '" ' . $checked . '>';
		$output  .= esc_html( $name );
		$output  .= '</label>';
		$x ++;
	}

	if ( ! empty( $args['desc'] ) ) {
		$output .= '<p class="desc">' . wp_kses_post( $args['desc'] ) . '</p>';
	}

	return $output;
}

/**
 * Settings image upload field callback.
 *
 * @param array $args
 *
 * @return string
 * @since 2.0.0
 *
 */
function wppopups_settings_image_callback( $args ) {

	$default = isset( $args['default'] ) ? esc_html( $args['default'] ) : '';
	$value   = wppopups_setting( $args['id'], $default );
	$id      = wppopups_sanitize_key( $args['id'] );
	$output  = '';

	if ( ! empty( $value ) ) {
		$output .= '<img src="' . esc_url_raw( $value ) . '">';
	}

	$output .= '<input type="text" id="wppopups-setting-' . $id . '" name="' . $id . '" value="' . esc_url_raw( $value ) . '">';
	$output .= '<button class="wppopups-btn wppopups-btn-md wppopups-btn-light-grey">' . esc_html__( 'Upload Image', 'wp-popups-lite' ) . '</button>';

	if ( ! empty( $args['desc'] ) ) {
		$output .= '<p class="desc">' . wp_kses_post( $args['desc'] ) . '</p>';
	}

	return $output;
}

/**
 * Settings color picker field callback.
 *
 * @param array $args
 *
 * @return string
 * @since 2.0.0
 *
 */
function wppopups_settings_color_callback( $args ) {

	$default = isset( $args['default'] ) ? esc_html( $args['default'] ) : '';
	$value   = wppopups_setting( $args['id'], $default );
	$id      = wppopups_sanitize_key( $args['id'] );

	$output = '<input type="text" id="wppopups-setting-' . $id . '" class="wppopups-color-picker" name="' . $id . '" value="' . esc_attr( $value ) . '">';

	if ( ! empty( $args['desc'] ) ) {
		$output .= '<p class="desc">' . wp_kses_post( $args['desc'] ) . '</p>';
	}

	return $output;
}

/**
 * Settings providers field callback - this is for the Integrations tab.
 *
 * @param array $args
 *
 * @return string
 * @since 2.0.0
 *
 */
function wppopups_settings_providers_callback( $args ) {

	$providers = get_option( 'wppopups_providers', false );
	$active    = apply_filters( 'wppopups_providers_available', [] );

	$output = '<div id="wppopups-settings-providers">';

	ob_start();
	do_action( 'wppopups_settings_providers', $active, $providers );
	$output .= ob_get_clean();

	$output .= '</div>';

	return $output;
}
