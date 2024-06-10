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
            'selector' => '#cf7md-form .mdc-textfield--focused .mdc-textfield__label',
            'property' => 'color',
        ),
            array(
            'selector' => '#cf7md-form .mdc-textfield--focused.mdc-textfield--upgraded:not(.mdc-textfield--fullwidth):not(.mdc-textfield--multiline)::after,
#cf7md-form .mdc-multi-select:focus .mdc-list-item:checked,
#cf7md-form .mdc-checkbox__background::before,
#cf7md-form .mdc-checkbox__native-control:checked ~ .mdc-checkbox__background,
#cf7md-form .mdc-checkbox__native-control:indeterminate ~ .mdc-checkbox__background,
#cf7md-form .mdc-radio__background::before,
#cf7md-form .mdc-radio__native-control:checked + .mdc-radio__background .mdc-radio__inner-circle,
#cf7md-form .mdc-button--primary.mdc-button--raised,
#cf7md-form .mdc-switch__native-control:checked ~ .mdc-switch__background::before,
#cf7md-form .mdc-switch__native-control:checked ~ .mdc-switch__background .mdc-switch__knob,
#cf7md-form .mdc-switch__native-control:checked ~ .mdc-switch__background .mdc-switch__knob::before',
            'property' => 'background-color',
        ),
            array(
            'selector' => '#cf7md-form .mdc-textfield--multiline .mdc-textfield__input:focus,
#cf7md-form .mdc-textfield:not(.mdc-textfield--upgraded) .mdc-textfield__input:focus,
#cf7md-form .mdc-checkbox__native-control:checked ~ .mdc-checkbox__background,
#cf7md-form .mdc-checkbox__native-control:indeterminate ~ .mdc-checkbox__background,
#cf7md-form .mdc-radio__native-control:checked + .mdc-radio__background .mdc-radio__outer-circle',
            'property' => 'border-color',
        ),
            array(
            'selector' => '#cf7md-form .mdc-select:focus',
            'property' => 'border-bottom-color',
        )
        );
        // Primary on dark
        $cf7md_selectors['primary_on_dark'] = array(
            array(
            'selector' => '#cf7md-form.mdc-theme--dark .mdc-textfield--focused .mdc-textfield__label',
            'property' => 'color',
        ),
            array(
            'selector' => '#cf7md-form.mdc-theme--dark .mdc-textfield--focused.mdc-textfield--upgraded:not(.mdc-textfield--fullwidth):not(.mdc-textfield--multiline)::after,
  #cf7md-form.mdc-theme--dark .mdc-multi-select:focus .mdc-list-item:checked,
  #cf7md-form.mdc-theme--dark .mdc-checkbox__background::before,
  #cf7md-form.mdc-theme--dark .mdc-checkbox__native-control:checked ~ .mdc-checkbox__background,
  #cf7md-form.mdc-theme--dark .mdc-checkbox__native-control:indeterminate ~ .mdc-checkbox__background,
  #cf7md-form.mdc-theme--dark .mdc-radio__background::before,
  #cf7md-form.mdc-theme--dark .mdc-radio__native-control:checked + .mdc-radio__background .mdc-radio__inner-circle,
  #cf7md-form.mdc-theme--dark .mdc-button--primary.mdc-button--raised,
  #cf7md-form.mdc-theme--dark .mdc-switch__native-control:checked ~ .mdc-switch__background::before,
  #cf7md-form.mdc-theme--dark .mdc-switch__native-control:checked ~ .mdc-switch__background .mdc-switch__knob,
  #cf7md-form.mdc-theme--dark .mdc-switch__native-control:checked ~ .mdc-switch__background .mdc-switch__knob::before',
            'property' => 'background-color',
        ),
            array(
            'selector' => '#cf7md-form.mdc-theme--dark .mdc-textfield--multiline .mdc-textfield__input:focus,
  #cf7md-form.mdc-theme--dark .mdc-textfield:not(.mdc-textfield--upgraded) .mdc-textfield__input:focus,
  #cf7md-form.mdc-theme--dark .mdc-checkbox__native-control:checked ~ .mdc-checkbox__background,
  #cf7md-form.mdc-theme--dark .mdc-checkbox__native-control:indeterminate ~ .mdc-checkbox__background,
  #cf7md-form.mdc-theme--dark .mdc-radio__native-control:checked + .mdc-radio__background .mdc-radio__outer-circle',
            'property' => 'border-color',
        ),
            array(
            'selector' => '#cf7md-form.mdc-theme--dark .mdc-select:focus',
            'property' => 'border-bottom-color',
        )
        );
        // Button on light
        $cf7md_selectors['button_on_light'] = array( array(
            'selector' => '#cf7md-form .mdc-button.mdc-button--primary.mdc-button--raised',
            'property' => 'background-color',
        ) );
        // Button on dark
        $cf7md_selectors['button_on_dark'] = array( array(
            'selector' => '#cf7md-form.mdc-theme--dark .mdc-button.mdc-button--primary.mdc-button--raised',
            'property' => 'background-color',
        ) );
        // Button text colour light theme
        $cf7md_selectors['btn_text_light_theme'] = array( array(
            'selector' => '#cf7md-form .mdc-button--primary.mdc-button--raised',
            'property' => 'color',
        ) );
        // Button text colour dark theme
        $cf7md_selectors['btn_text_dark_theme'] = array( array(
            'selector' => '#cf7md-form.mdc-theme--dark .mdc-button--primary.mdc-button--raised',
            'property' => 'color',
        ) );
        // Text hint on light
        $cf7md_selectors['text_hint_on_light'] = array( array(
            'selector' => '#cf7md-form .mdc-textfield--disabled .mdc-textfield__input,
#cf7md-form .mdc-textfield--disabled .mdc-textfield__label,
#cf7md-form .mdc-textfield--disabled + .mdc-textfield-helptext,
#cf7md-form .mdc-textfield:not(.mdc-textfield--upgraded) .mdc-textfield__input:disabled,
#cf7md-form .mdc-select--disabled,
#cf7md-form .mdc-select[disabled],
#cf7md-form .mdc-select__menu .mdc-list-group,
#cf7md-form .mdc-multi-select .mdc-list-group,
#cf7md-form label,
#cf7md-form .cf7md-label--static,
#cf7md-form .cf7md-help-text,
#cf7md-form .mdc-textfield__label',
            'property' => 'color',
        ), array(
            'selector' => '#cf7md-form .mdc-multi-select',
            'property' => 'border-color',
        ) );
        // Text hint on dark
        $cf7md_selectors['text_hint_on_dark'] = array( array(
            'selector' => '#cf7md-form .mdc-textfield--theme-dark .mdc-textfield__label,
#cf7md-form.mdc-theme--dark .mdc-textfield__label,
#cf7md-form .mdc-textfield--theme-dark .mdc-textfield--disabled .mdc-textfield__input,
#cf7md-form.mdc-theme--dark .mdc-textfield--disabled .mdc-textfield__input,
#cf7md-form .mdc-textfield--theme-dark .mdc-textfield--disabled .mdc-textfield__label,
#cf7md-form.mdc-theme--dark .mdc-textfield--disabled .mdc-textfield__label,
#cf7md-form .mdc-textfield--theme-dark.mdc-textfield--disabled + .mdc-textfield-helptext,
#cf7md-form.mdc-theme--dark .mdc-textfield--disabled + .mdc-textfield-helptext,
#cf7md-form .mdc-textfield--theme-dark:not(.mdc-textfield--upgraded) .mdc-textfield__input:disabled,
#cf7md-form.mdc-theme--dark .mdc-textfield:not(.mdc-textfield--upgraded) .mdc-textfield__input:disabled,
#cf7md-form .mdc-select--theme-dark.mdc-select--disabled,
#cf7md-form.mdc-theme--dark .mdc-select--disabled,
#cf7md-form .mdc-select--theme-dark.mdc-select[disabled],
#cf7md-form.mdc-theme--dark .mdc-select[disabled],
#cf7md-form .mdc-select--theme-dark .mdc-select__menu .mdc-list-group,
#cf7md-form.mdc-theme--dark .mdc-select__menu .mdc-list-group,
#cf7md-form.mdc-theme--dark label,
#cf7md-form.mdc-theme--dark .cf7md-label--static,
#cf7md-form.mdc-theme--dark .cf7md-help-text',
            'property' => 'color',
        ) );
        // Text on light
        $cf7md_selectors['text_on_light'] = array( array(
            'selector' => '#cf7md-form .mdc-form-field,
#cf7md-form .mdc-select,
#cf7md-form .mdc-select__menu .mdc-list-item[aria-selected="true"],
#cf7md-form .mdc-select__menu .mdc-list-group .mdc-list-item,
#cf7md-form .mdc-multi-select .mdc-list-item,
#cf7md-form .cf7md-file--value,
#cf7md-form .mdc-button,
#cf7md-form .mdc-card__supporting-text,
#cf7md-form .mdc-card__title,
#cf7md-form .mdc-card__subtitle,
#cf7md-form input,
#cf7md-form textarea,
#cf7md-form select,
#cf7md-form .wpcf7-list-item label,
#cf7md-form .cf7md-accept-label',
            'property' => 'color',
        ), array(
            'selector' => '#cf7md-form .cf7md-spinner-path',
            'property' => 'stroke',
        ) );
        // Text on dark
        $cf7md_selectors['text_on_dark'] = array( array(
            'selector' => '#cf7md-form.mdc-theme--dark input, #cf7md-form.mdc-theme--dark textarea,
#cf7md-form.mdc-theme--dark select,
#cf7md-form.mdc-theme--dark .wpcf7-list-item label,
#cf7md-form.mdc-theme--dark .cf7md-accept-label,
#cf7md-form .mdc-card--theme-dark .mdc-card__subtitle,
#cf7md-form.mdc-theme--dark .mdc-card__subtitle,
#cf7md-form .mdc-card--theme-dark .mdc-card__title,
#cf7md-form.mdc-theme--dark .mdc-card__title,
#cf7md-form .mdc-card--theme-dark .mdc-card__supporting-text,
#cf7md-form.mdc-theme--dark .mdc-card__supporting-text,
#cf7md-form .mdc-button--theme-dark,
#cf7md-form.mdc-theme--dark .mdc-button,
#cf7md-form.mdc-theme--dark .cf7md-file--value,
#cf7md-form .mdc-multi-select .mdc-list-item--theme-dark,
#cf7md-form.mdc-theme--dark .mdc-multi-select .mdc-list-item,
#cf7md-form .mdc-select--theme-dark .mdc-select__menu .mdc-list-group .mdc-list-item,
#cf7md-form.mdc-theme--dark .mdc-select__menu .mdc-list-group .mdc-list-item,
#cf7md-form .mdc-select--theme-dark .mdc-select__menu .mdc-list-item[aria-selected="true"],
#cf7md-form.mdc-theme--dark .mdc-select__menu .mdc-list-item[aria-selected="true"],
#cf7md-form .mdc-select--theme-dark,
#cf7md-form.mdc-theme--dark .mdc-select,
#cf7md-form .mdc-textfield__input--theme-dark,
#cf7md-form.mdc-theme--dark .mdc-textfield__input,
#cf7md-form .mdc-form-field--theme-dark,
#cf7md-form.mdc-theme--dark .mdc-form-field,
#cf7md-form .mdc-card--theme-dark .mdc-card__actions,
#cf7md-form.mdc-theme--dark .mdc-card__actions',
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
            'selector' => '#cf7md-form .cf7md-item, #cf7md-form .mdc-form-field, #cf7md-form .mdc-textfield',
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
#cf7md-form .mdc-card__title,
#cf7md-form .mdc-card__subtitle,
#cf7md-form .mdc-button',
            'property' => 'font-family',
        ) );
    }

}
// Finally initialize code
$cf7_material_design_custom_style = new CF7_Material_Design_Custom_Style();