<?php

// don't load directly
if ( !defined( 'ABSPATH' ) ) {
    die( '-1' );
}
/**
 * Custom styles
 */
class CF7_Material_Design_Custom_Style
{
    private  $options ;
    private  $selectors ;
    private  $fs ;
    /**
     * Constructor
     */
    function __construct()
    {
        // Populate options member
        $this->options = get_option( 'cf7md_options' );
        // Define members
        $this->define_selectors();
        global  $cf7md_selectors ;
        $this->selectors = $cf7md_selectors;
        global  $cf7md_fs ;
        $this->fs = $cf7md_fs;
        // Add scripts and styles
        add_action( 'wp_enqueue_scripts', array( $this, 'add_scripts_and_styles' ) );
    }
    
    /**
     * Add scripts and styles
     */
    public function add_scripts_and_styles()
    {
        // Styles
        wp_add_inline_style( 'cf7-material-design', $this->get_css() );
        // Maybe dequeue roboto
        if ( !is_admin() && $this->fs->can_use_premium_code() && isset( $this->options['use_custom_styles'] ) && $this->options['use_custom_styles'] && isset( $this->options['font_family'] ) && !strpos( $this->options['font_family'], 'Roboto' ) ) {
            wp_dequeue_style( 'cf7md_roboto' );
        }
    }
    
    /**
     * Output styles
     */
    public function get_css()
    {
        $css = '';
        // Add custom CSS last
        if ( isset( $this->options['custom_css'] ) ) {
            $css .= $this->options['custom_css'];
        }
        return $css;
    }
    
    /**
     * Define selectors as a global var
     */
    private function define_selectors()
    {
        global  $cf7md_selectors ;
        $cf7md_selectors = array();
        // Primary on light
        $cf7md_selectors['primary_on_light'] = array(
            array(
            'selector' => '#cf7md-form .mdc-text-field--focused:not(.mdc-text-field--disabled) .mdc-floating-label,
				#cf7md-form .mdc-text-field--focused:not(.mdc-text-field--disabled) .mdc-text-field__input::placeholder,
				#cf7md-form .mdc-select:not(.mdc-select--disabled) .mdc-select__native-control:focus ~ .mdc-floating-label,
				#cf7md-form .mdc-select:not(.mdc-select--disabled).mdc-select--focused .mdc-floating-label',
            'property' => 'color',
        ),
            array(
            'selector' => '#cf7md-form .mdc-text-field .mdc-text-field__input',
            'property' => 'caret-color',
        ),
            array(
            'selector' => '#cf7md-form .mdc-text-field--outlined:not(.mdc-text-field--disabled).mdc-text-field--focused .mdc-notched-outline__path,
				#cf7md-form .mdc-select--outlined:not(.mdc-select--disabled) .mdc-select__native-control:focus ~ .mdc-notched-outline .mdc-notched-outline__path',
            'property' => 'stroke',
        ),
            array(
            'selector' => '#cf7md-form .mdc-text-field .mdc-line-ripple,
				#cf7md-form .mdc-select:not(.mdc-select--disabled) .mdc-select__native-control:focus ~ .mdc-line-ripple,
				#cf7md-form .mdc-checkbox .mdc-checkbox__native-control:enabled:checked ~ .mdc-checkbox__background,
				#cf7md-form .mdc-checkbox .mdc-checkbox__native-control:enabled:indeterminate ~ .mdc-checkbox__background,
				#cf7md-form .mdc-checkbox::before,
				#cf7md-form .mdc-checkbox::after,
				#cf7md-form .mdc-radio::before,
				#cf7md-form .mdc-radio::after,
				#cf7md-form .mdc-switch.mdc-switch--checked .mdc-switch__track,
				#cf7md-form .mdc-switch.mdc-switch--checked .mdc-switch__thumb,
				#cf7md-form .mdc-switch.mdc-switch--checked .mdc-switch__thumb-underlay::before,
				#cf7md-form .mdc-switch.mdc-switch--checked .mdc-switch__thumb-underlay::after,
				#cf7md-form .mdc-switch.mdc-switch--checked .mdc-switch__thumb,
				#cf7md-form .mdc-switch.mdc-switch--checked .mdc-switch__thumb-underlay::before,
				#cf7md-form .mdc-switch.mdc-switch--checked .mdc-switch__thumb-underlay::after,
				#cf7md-form .mdc-switch:not(.mdc-switch--checked) .mdc-switch__thumb-underlay::before,
				#cf7md-form .mdc-switch:not(.mdc-switch--checked) .mdc-switch__thumb-underlay::after',
            'property' => 'background-color',
        ),
            array(
            'selector' => '#cf7md-form .mdc-text-field--textarea.mdc-text-field--focused:not(.mdc-text-field--disabled),
				#cf7md-form .mdc-text-field--textarea.mdc-text-field--focused:not(.mdc-text-field--disabled) .mdc-text-field__input:focus,
				#cf7md-form .mdc-checkbox .mdc-checkbox__native-control:enabled:checked ~ .mdc-checkbox__background,
				#cf7md-form .mdc-checkbox .mdc-checkbox__native-control:enabled:indeterminate ~ .mdc-checkbox__background,
				#cf7md-form .mdc-radio .mdc-radio__native-control:enabled:checked + .mdc-radio__background .mdc-radio__outer-circle,
				#cf7md-form .mdc-radio .mdc-radio__native-control:enabled + .mdc-radio__background .mdc-radio__inner-circle,
				#cf7md-form .mdc-switch.mdc-switch--checked .mdc-switch__track,
				#cf7md-form .mdc-switch.mdc-switch--checked .mdc-switch__thumb,
				#cf7md-form .mdc-switch.mdc-switch--checked .mdc-switch__thumb,
				#cf7md-form .mdc-text-field--outlined:not(.mdc-text-field--disabled).mdc-text-field--focused .mdc-notched-outline__leading,
				#cf7md-form .mdc-text-field--outlined:not(.mdc-text-field--disabled).mdc-text-field--focused .mdc-notched-outline__notch,
				#cf7md-form .mdc-text-field--outlined:not(.mdc-text-field--disabled).mdc-text-field--focused .mdc-notched-outline__trailing,
				#cf7md-form .mdc-select--outlined:not(.mdc-select--disabled).mdc-select--focused .mdc-notched-outline .mdc-notched-outline__leading,
				#cf7md-form .mdc-select--outlined:not(.mdc-select--disabled).mdc-select--focused .mdc-notched-outline .mdc-notched-outline__notch,
				#cf7md-form .mdc-select--outlined:not(.mdc-select--disabled).mdc-select--focused .mdc-notched-outline .mdc-notched-outline__trailing,
				#cf7md-form .mdc-text-field--textarea:not(.mdc-text-field--disabled).mdc-text-field--focused .mdc-notched-outline__leading,
				#cf7md-form .mdc-text-field--textarea:not(.mdc-text-field--disabled).mdc-text-field--focused .mdc-notched-outline__notch,
				#cf7md-form .mdc-text-field--textarea:not(.mdc-text-field--disabled).mdc-text-field--focused .mdc-notched-outline__trailing',
            'property' => 'border-color',
        )
        );
        // Primary on dark
        $cf7md_selectors['primary_on_dark'] = array(
            array(
            'selector' => '#cf7md-form.mdc-theme--dark .mdc-textfield--focused .mdc-textfield__label',
            'property' => 'color',
        ),
            array(
            'selector' => '#cf7md-form.cf7md-theme--dark .mdc-text-field:not(.mdc-text-field--outlined):not(.mdc-text-field--textarea):not(.mdc-text-field--disabled) .mdc-line-ripple,
				#cf7md-form.cf7md-theme--dark .mdc-checkbox::before,
				#cf7md-form.cf7md-theme--dark .mdc-checkbox::after,
				#cf7md-form.cf7md-theme--dark .mdc-radio::before,
				#cf7md-form.cf7md-theme--dark .mdc-radio::after,
				#cf7md-form.cf7md-theme--dark .mdc-checkbox .mdc-checkbox__native-control:enabled:checked ~ .mdc-checkbox__background,
				#cf7md-form.cf7md-theme--dark .mdc-checkbox .mdc-checkbox__native-control:enabled:indeterminate ~ .mdc-checkbox__background,
				#cf7md-form.cf7md-theme--dark .mdc-switch.mdc-switch--checked .mdc-switch__track,
				#cf7md-form.cf7md-theme--dark .mdc-switch.mdc-switch--checked .mdc-switch__thumb,
				#cf7md-form.cf7md-theme--dark .mdc-switch.mdc-switch--checked .mdc-switch__thumb-underlay::before,
				#cf7md-form.cf7md-theme--dark .mdc-switch.mdc-switch--checked .mdc-switch__thumb-underlay::after,
				#cf7md-form.cf7md-theme--dark .mdc-switch.mdc-switch--checked .mdc-switch__thumb,
				#cf7md-form.cf7md-theme--dark .mdc-switch.mdc-switch--checked .mdc-switch__thumb-underlay::before,
				#cf7md-form.cf7md-theme--dark .mdc-switch.mdc-switch--checked .mdc-switch__thumb-underlay::after,
				#cf7md-form.cf7md-theme--dark .mdc-switch:not(.mdc-switch--checked) .mdc-switch__thumb-underlay::before,
				#cf7md-form.cf7md-theme--dark .mdc-switch:not(.mdc-switch--checked) .mdc-switch__thumb-underlay::after,
				#cf7md-form.cf7md-theme--dark .mdc-select:not(.mdc-select--outlined):not(.mdc-select--disabled).mdc-select--focused .mdc-line-ripple',
            'property' => 'background-color',
        ),
            array(
            'selector' => '#cf7md-form.cf7md-theme--dark .mdc-text-field--textarea.mdc-text-field--focused:not(.mdc-text-field--disabled),
				#cf7md-form.cf7md-theme--dark .mdc-text-field--textarea.mdc-text-field--focused:not(.mdc-text-field--disabled) .mdc-text-field__input:focus,
				#cf7md-form.cf7md-theme--dark .mdc-checkbox .mdc-checkbox__native-control:enabled:checked ~ .mdc-checkbox__background,
				#cf7md-form.cf7md-theme--dark .mdc-checkbox .mdc-checkbox__native-control:enabled:indeterminate ~ .mdc-checkbox__background,
				#cf7md-form.cf7md-theme--dark .mdc-radio .mdc-radio__native-control:enabled:checked + .mdc-radio__background .mdc-radio__outer-circle,
				#cf7md-form.cf7md-theme--dark .mdc-radio .mdc-radio__native-control:enabled + .mdc-radio__background .mdc-radio__inner-circle,
				#cf7md-form.cf7md-theme--dark .mdc-switch.mdc-switch--checked .mdc-switch__track,
				#cf7md-form.cf7md-theme--dark .mdc-switch.mdc-switch--checked .mdc-switch__thumb,
				#cf7md-form.cf7md-theme--dark .mdc-switch.mdc-switch--checked .mdc-switch__thumb,
				#cf7md-form.cf7md-theme--dark .mdc-text-field--outlined:not(.mdc-text-field--disabled).mdc-text-field--focused .mdc-notched-outline__leading,
				#cf7md-form.cf7md-theme--dark .mdc-text-field--outlined:not(.mdc-text-field--disabled).mdc-text-field--focused .mdc-notched-outline__notch,
				#cf7md-form.cf7md-theme--dark .mdc-text-field--outlined:not(.mdc-text-field--disabled).mdc-text-field--focused .mdc-notched-outline__trailing,
				#cf7md-form.cf7md-theme--dark .mdc-select--outlined:not(.mdc-select--disabled).mdc-select--focused .mdc-notched-outline .mdc-notched-outline__leading,
				#cf7md-form.cf7md-theme--dark .mdc-select--outlined:not(.mdc-select--disabled).mdc-select--focused .mdc-notched-outline .mdc-notched-outline__notch,
				#cf7md-form.cf7md-theme--dark .mdc-select--outlined:not(.mdc-select--disabled).mdc-select--focused .mdc-notched-outline .mdc-notched-outline__trailing,
				#cf7md-form.cf7md-theme--dark .mdc-text-field--textarea:not(.mdc-text-field--disabled).mdc-text-field--focused .mdc-notched-outline__leading,
				#cf7md-form.cf7md-theme--dark .mdc-text-field--textarea:not(.mdc-text-field--disabled).mdc-text-field--focused .mdc-notched-outline__notch,
				#cf7md-form.cf7md-theme--dark .mdc-text-field--textarea:not(.mdc-text-field--disabled).mdc-text-field--focused .mdc-notched-outline__trailing',
            'property' => 'border-color',
        ),
            array(
            'selector' => '#cf7md-form.cf7md-theme--dark .mdc-text-field--outlined:not(.mdc-text-field--disabled).mdc-text-field--focused .mdc-notched-outline__path,
				#cf7md-form.cf7md-theme--dark .mdc-select--outlined:not(.mdc-select--disabled) .mdc-select__native-control:focus ~ .mdc-notched-outline .mdc-notched-outline__path',
            'property' => 'stroke',
        )
        );
        // Button on light
        $cf7md_selectors['button_on_light'] = array(
            array(
                'selector' => '#cf7md-form .mdc-button--outlined:not(:disabled)',
                'property' => 'border-color',
            ),
            array(
                'selector' => '#cf7md-form .mdc-button::before,
				#cf7md-form .mdc-button::after,
				#cf7md-form .mdc-button--raised:not(:disabled),
				#cf7md-form .mdc-button--unelevated:not(:disabled)',
                'property' => 'background-color',
            ),
            // Outlined version uses the background colour for text
            array(
                'selector' => '#cf7md-form .mdc-button--outlined:not(:disabled)',
                'property' => 'color',
            ),
        );
        // Button hover on light
        $cf7md_selectors['button_hover_on_light'] = array( array(
            'selector' => '#cf7md-form .mdc-button--raised:not(:disabled):hover,
				#cf7md-form .mdc-button--unelevated:not(:disabled):hover',
            'property' => 'background-color',
        ) );
        // Button text colour light theme
        $cf7md_selectors['btn_text_light_theme'] = array( array(
            'selector' => '#cf7md-form .mdc-button--raised:not(:disabled),
				#cf7md-form .mdc-button--unelevated:not(:disabled)',
            'property' => 'color',
        ) );
        // Button on dark
        $cf7md_selectors['button_on_dark'] = array( array(
            'selector' => '#cf7md-form.cf7md-theme--dark .mdc-button--raised:not(:disabled),
				#cf7md-form.cf7md-theme--dark .mdc-button--unelevated:not(:disabled)',
            'property' => 'background-color',
        ) );
        // Button hover on dark
        $cf7md_selectors['button_hover_on_dark'] = array( array(
            'selector' => '#cf7md-form.cf7md-theme--dark .mdc-button--raised:not(:disabled):hover,
				#cf7md-form.cf7md-theme--dark .mdc-button--unelevated:not(:disabled):hover',
            'property' => 'background-color',
        ) );
        // Button text colour dark theme
        $cf7md_selectors['btn_text_dark_theme'] = array( array(
            'selector' => '#cf7md-form.cf7md-theme--dark .mdc-button--raised:not(:disabled),
				#cf7md-form.cf7md-theme--dark .mdc-button--unelevated:not(:disabled)',
            'property' => 'color',
        ) );
        // Text hint on light
        $cf7md_selectors['text_hint_on_light'] = array( array(
            'selector' => '#cf7md-form .mdc-theme--text-hint-on-background,
				#cf7md-form .mdc-theme--text-disabled-on-background,
				#cf7md-form .mdc-theme--text-icon-on-background,
				#cf7md-form .mdc-theme--text-hint-on-light,
				#cf7md-form .mdc-theme--text-disabled-on-light,
				#cf7md-form .mdc-theme--text-icon-on-light,
				#cf7md-form .mdc-card__action-icons,
				#cf7md-form .cf7md-card-subtitle,
				#cf7md-form label,
				#cf7md-form .cf7md-label--static,
				#cf7md-form .cf7md-help-text',
            'property' => 'color',
        ), array(
            'selector' => '#cf7md-form .mdc-multi-select',
            'property' => 'border-color',
        ) );
        // Text hint on dark
        $cf7md_selectors['text_hint_on_dark'] = array( array(
            'selector' => '#cf7md-form .mdc-theme--text-secondary-on-dark,
				#cf7md-form .mdc-theme--text-hint-on-dark,
				#cf7md-form .mdc-theme--text-disabled-on-dark,
				#cf7md-form .mdc-theme--text-icon-on-dark,
				#cf7md-form.cf7md-theme--dark .cf7md-card-subtitle,
				#cf7md-form.cf7md-theme--dark label,
				#cf7md-form.cf7md-theme--dark .cf7md-label--static,
				#cf7md-form.cf7md-theme--dark .cf7md-help-text',
            'property' => 'color',
        ) );
        // Text on light
        $cf7md_selectors['text_on_light'] = array( array(
            'selector' => '#cf7md-form .mdc-theme--text-primary-on-background,
				#cf7md-form .mdc-theme--text-primary-on-light,
				#cf7md-form .mdc-text-field:not(.mdc-text-field--disabled) .mdc-text-field__input,
				#cf7md-form .mdc-select:not(.mdc-select--disabled) .mdc-select__native-control,
				#cf7md-form .mdc-form-field,
				#cf7md-form .cf7md-file--value,
				#cf7md-form .cf7md-card-title,
				#cf7md-form .mdc-list a.mdc-list-item,
				#cf7md-form input,
				#cf7md-form textarea,
				#cf7md-form select,
				#cf7md-form .wpcf7-list-item label,
				#cf7md-form .cf7md-accept-label,
				#cf7md-form .mdc-text-field:not(.mdc-text-field--disabled) .mdc-floating-label,
				#cf7md-form .mdc-text-field--outlined.mdc-text-field--disabled .mdc-text-field__input,
				#cf7md-form .mdc-select:not(.mdc-select--disabled) .mdc-floating-label',
            'property' => 'color',
        ), array(
            'selector' => '#cf7md-form .cf7md-spinner-path',
            'property' => 'stroke',
        ) );
        // Text on dark
        $cf7md_selectors['text_on_dark'] = array( array(
            'selector' => '#cf7md-form .mdc-theme--text-primary-on-dark,
				#cf7md-form.cf7md-theme--dark .mdc-form-field,
				#cf7md-form.cf7md-theme--dark .cf7md-file--value,
				#cf7md-form.cf7md-theme--dark .cf7md-card-title,
				#cf7md-form.cf7md-theme--dark input,
				#cf7md-form.cf7md-theme--dark textarea,
				#cf7md-form.cf7md-theme--dark select,
				#cf7md-form.cf7md-theme--dark .wpcf7-list-item label,
				#cf7md-form.cf7md-theme--dark .cf7md-accept-label,
				#cf7md-form.cf7md-theme--dark .mdc-floating-label .cf7md-theme--dark .mdc-text-field:not(.mdc-text-field--disabled) .mdc-text-field__input,
				#cf7md-form.cf7md-theme--dark .mdc-select:not(.mdc-select--disabled) .mdc-select__native-control,
				#cf7md-form.cf7md-theme--dark .mdc-select:not(.mdc-select--disabled) .mdc-select__native-control:focus ~ .mdc-floating-label,
				#cf7md-form.cf7md-theme--dark .cf7md-checkbox-label,
				#cf7md-form.cf7md-theme--dark .cf7md-radio-label,
				#cf7md-form.cf7md-theme--dark .cf7md-switch-label,
				#cf7md-form.cf7md-theme--dark .cf7md-file--value,
				#cf7md-form.cf7md-theme--dark .mdc-text-field:not(.mdc-text-field--disabled) .mdc-floating-label,
				#cf7md-form.cf7md-theme--dark .mdc-text-field--outlined.mdc-text-field--disabled .mdc-text-field__input,
				#cf7md-form.cf7md-theme--dark .mdc-select:not(.mdc-select--disabled) .mdc-floating-label',
            'property' => 'color',
        ), array(
            'selector' => '#cf7md-form.mdc-theme--dark .cf7md-spinner-path',
            'property' => 'stroke',
        ), array(
            'selector' => '#cf7md-form.mdc-theme--dark .mdc-multi-select .mdc-list-item:checked,
#cf7md-form.mdc-theme--dark .mdc-multi-select:focus .mdc-list-item:checked',
            'property' => 'background-color',
        ) );
        // Base font size
        $cf7md_selectors['base_font_size'] = array( array(
            'selector' => '#cf7md-form .cf7md-item,
				#cf7md-form .mdc-form-field,
				#cf7md-form .mdc-text-field,
				#cf7md-form .mdc-select',
            'property' => 'font-size',
        ) );
        // Font family
        $cf7md_selectors['font_family'] = array( array(
            'selector' => '#cf7md-form .cf7md-item,
				#cf7md-form input,
				#cf7md-form label,
				#cf7md-form textarea,
				#cf7md-form p,
				#cf7md-form .mdc-select,
				#cf7md-form.cf7md-form + .wpcf7-response-output,
				#cf7md-form .wpcf7-not-valid-tip,
				#cf7md-form .cf7md-card-title,
				#cf7md-form .cf7md-card-subtitle,
				#cf7md-form .mdc-button,
				#cf7md-form .mdc-floating-label,
				#cf7md-form .mdc-text-field-helper-text,
				#cf7md-form .mdc-text-field__input,
				#cf7md-form .mdc-select__native-control,
				#cf7md-form .mdc-form-field',
            'property' => 'font-family',
        ) );
    }

}
// Finally initialize code
$cf7_material_design_custom_style = new CF7_Material_Design_Custom_Style();