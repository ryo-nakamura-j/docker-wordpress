<?php

// don't load directly
if ( !defined( 'ABSPATH' ) ) {
    die( '-1' );
}
class CF7_Material_Design_Customizer
{
    private  $fs ;
    private  $upgrade_url ;
    function __construct()
    {
        // Enqueue scripts and styles
        add_action( 'customize_preview_init', array( $this, 'previewer_scripts' ) );
        add_action( 'customize_controls_enqueue_scripts', array( $this, 'control_scripts' ) );
        // Add the customizer
        add_action( 'customize_register', array( $this, 'customize_register' ) );
        // An ajax endpoint to set fonts
        add_action( 'wp_ajax_set_fonts', array( $this, 'set_fonts' ) );
        // Set members
        global  $cf7md_fs ;
        $this->fs = $cf7md_fs;
        $this->upgrade_url = $cf7md_fs->get_upgrade_url( 'lifetime' );
    }
    
    /**
     * Enqueue scripts and styles
     */
    public function previewer_scripts()
    {
        // Customizer custom script
        wp_register_script(
            'cf7-material-design-customizer',
            plugins_url( '../assets/js/cf7-material-design-customizer.js', __FILE__ ),
            array( 'jquery' ),
            '1.0',
            true
        );
        $localize = array(
            'ajaxUrl' => admin_url( 'admin-ajax.php' ),
        );
        wp_localize_script( 'cf7-material-design-customizer', 'cf7md_customize', $localize );
        wp_enqueue_script( 'cf7-material-design-customizer' );
        // Customizer preview script
        wp_enqueue_script(
            'cf7-material-design-customizer-preview',
            plugins_url( '../assets/js/cf7-material-design-customizer-preview.js', __FILE__ ),
            array( 'jquery' ),
            '1.0',
            true
        );
        global  $cf7md_selectors ;
        wp_localize_script( 'cf7-material-design-customizer', 'cf7md_selectors', $cf7md_selectors );
        wp_enqueue_script( 'cf7-material-design-customizer-preview' );
    }
    
    /**
     * Control scripts
     */
    public function control_scripts()
    {
        // Customizer preview
        wp_enqueue_script(
            'cf7-material-design-customizer-control',
            plugins_url( '../assets/js/cf7-material-design-customizer-control.js', __FILE__ ),
            array( 'jquery' ),
            '1.0',
            true
        );
    }
    
    /**
     * Ajax endpoint to set fonts
     */
    public function set_fonts()
    {
        // Get the fonts from the post data
        $fonts = $_POST["fonts"];
        $updated = update_option( 'cf7md_options[available_fonts]', $fonts );
        // Response
        echo  json_encode( array(
            'updated' => $updated,
            'fonts'   => $fonts,
        ) ) ;
        die;
    }
    
    /**
     * Register customizer items
     */
    public function customize_register( $wp_customize )
    {
        // Register a new section
        $wp_customize->add_section( 'cf7md_options', array(
            'title'       => 'Material Design Forms',
            'description' => 'Customize your Material Design forms',
            'priority'    => 160,
            'capability'  => 'edit_theme_options',
        ) );
        // Use custom styles?
        $wp_customize->add_setting( 'cf7md_options[use_custom_styles]', array(
            'type'                 => 'option',
            'capability'           => 'edit_theme_options',
            'default'              => true,
            'transport'            => 'postMessage',
            'sanitize_callback'    => '',
            'sanitize_js_callback' => '',
        ) );
        $checkbox_control = array(
            'type'        => 'checkbox',
            'priority'    => 10,
            'section'     => 'cf7md_options',
            'label'       => 'Use custom styles?',
            'description' => 'Note: you\'re on the free version. Your style changes will not take effect until you <a href="' . $this->upgrade_url . '">upgrade to pro</a>',
        );
        $wp_customize->add_control( 'cf7md_options[use_custom_styles]', $checkbox_control );
        // Primary colour on light
        $wp_customize->add_setting( 'cf7md_options[primary_on_light]', array(
            'type'                 => 'option',
            'capability'           => 'edit_theme_options',
            'default'              => '#3f51b5',
            'transport'            => 'postMessage',
            'sanitize_callback'    => 'sanitize_hex_color',
            'sanitize_js_callback' => '',
        ) );
        $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'cf7md_options[primary_on_light]', array(
            'label'    => 'Primary colour (default/light theme)',
            'section'  => 'cf7md_options',
            'settings' => 'cf7md_options[primary_on_light]',
        ) ) );
        // Primary colour on dark
        $wp_customize->add_setting( 'cf7md_options[primary_on_dark]', array(
            'type'                 => 'option',
            'capability'           => 'edit_theme_options',
            'default'              => '#3f51b5',
            'transport'            => 'postMessage',
            'sanitize_callback'    => 'sanitize_hex_color',
            'sanitize_js_callback' => '',
        ) );
        $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'cf7md_options[primary_on_dark]', array(
            'label'       => 'Primary colour (dark theme)',
            'section'     => 'cf7md_options',
            'settings'    => 'cf7md_options[primary_on_dark]',
            'description' => 'You may want to choose a slightly lighter version of your primary colour for better contrast in the dark theme.',
        ) ) );
        // Button colour on light
        $wp_customize->add_setting( 'cf7md_options[button_on_light]', array(
            'type'                 => 'option',
            'capability'           => 'edit_theme_options',
            'default'              => '',
            'transport'            => 'postMessage',
            'sanitize_callback'    => 'sanitize_hex_color',
            'sanitize_js_callback' => '',
        ) );
        $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'cf7md_options[button_on_light]', array(
            'label'       => 'Button colour',
            'section'     => 'cf7md_options',
            'settings'    => 'cf7md_options[button_on_light]',
            'description' => 'Leave blank to use the primary colour',
        ) ) );
        // Button colour on dark
        $wp_customize->add_setting( 'cf7md_options[button_on_dark]', array(
            'type'                 => 'option',
            'capability'           => 'edit_theme_options',
            'default'              => '',
            'transport'            => 'postMessage',
            'sanitize_callback'    => 'sanitize_hex_color',
            'sanitize_js_callback' => '',
        ) );
        $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'cf7md_options[button_on_dark]', array(
            'label'       => 'Button colour (dark theme)',
            'section'     => 'cf7md_options',
            'settings'    => 'cf7md_options[button_on_dark]',
            'description' => 'Leave blank to use the dark theme primary colour',
        ) ) );
        // Button text colour light theme
        $wp_customize->add_setting( 'cf7md_options[btn_text_light_theme]', array(
            'type'                 => 'option',
            'capability'           => 'edit_theme_options',
            'default'              => '#FFFFFF',
            'transport'            => 'postMessage',
            'sanitize_callback'    => 'sanitize_hex_color',
            'sanitize_js_callback' => '',
        ) );
        $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'cf7md_options[btn_text_light_theme]', array(
            'label'    => 'Button text colour',
            'section'  => 'cf7md_options',
            'settings' => 'cf7md_options[btn_text_light_theme]',
        ) ) );
        // Button text colour dark theme
        $wp_customize->add_setting( 'cf7md_options[btn_text_dark_theme]', array(
            'type'                 => 'option',
            'capability'           => 'edit_theme_options',
            'default'              => '#FFFFFF',
            'transport'            => 'postMessage',
            'sanitize_callback'    => 'sanitize_hex_color',
            'sanitize_js_callback' => '',
        ) );
        $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'cf7md_options[btn_text_dark_theme]', array(
            'label'       => 'Button text colour (dark theme)',
            'section'     => 'cf7md_options',
            'settings'    => 'cf7md_options[btn_text_dark_theme]',
            'description' => '',
        ) ) );
        // Text hint on light
        $wp_customize->add_setting( 'cf7md_options[text_hint_on_light]', array(
            'type'                 => 'option',
            'capability'           => 'edit_theme_options',
            'default'              => '0.38',
            'transport'            => 'postMessage',
            'sanitize_callback'    => '',
            'sanitize_js_callback' => '',
        ) );
        $wp_customize->add_control( 'cf7md_options[text_hint_on_light]', array(
            'type'        => 'number',
            'priority'    => 10,
            'section'     => 'cf7md_options',
            'label'       => 'Label text opacity',
            'description' => '1 = black, 0 = transparent, default = 0.38',
            'input_attrs' => array(
            'min'  => 0,
            'max'  => 1,
            'step' => 0.01,
        ),
        ) );
        // Text hint on dark
        $wp_customize->add_setting( 'cf7md_options[text_hint_on_dark]', array(
            'type'                 => 'option',
            'capability'           => 'edit_theme_options',
            'default'              => '0.5',
            'transport'            => 'postMessage',
            'sanitize_callback'    => '',
            'sanitize_js_callback' => '',
        ) );
        $wp_customize->add_control( 'cf7md_options[text_hint_on_dark]', array(
            'type'        => 'number',
            'priority'    => 10,
            'section'     => 'cf7md_options',
            'label'       => 'Label text opacity (dark theme)',
            'description' => '1 = white, 0 = transparent, default = 0.5',
            'input_attrs' => array(
            'min'  => 0,
            'max'  => 1,
            'step' => 0.01,
        ),
        ) );
        // Text on light
        $wp_customize->add_setting( 'cf7md_options[text_on_light]', array(
            'type'                 => 'option',
            'capability'           => 'edit_theme_options',
            'default'              => '0.87',
            'transport'            => 'postMessage',
            'sanitize_callback'    => '',
            'sanitize_js_callback' => '',
        ) );
        $wp_customize->add_control( 'cf7md_options[text_on_light]', array(
            'type'        => 'number',
            'priority'    => 10,
            'section'     => 'cf7md_options',
            'label'       => 'Normal text opacity',
            'description' => '1 = black, 0 = transparent, default = 0.87',
            'input_attrs' => array(
            'min'  => 0,
            'max'  => 1,
            'step' => 0.01,
        ),
        ) );
        // Text on dark
        $wp_customize->add_setting( 'cf7md_options[text_on_dark]', array(
            'type'                 => 'option',
            'capability'           => 'edit_theme_options',
            'default'              => '1',
            'transport'            => 'postMessage',
            'sanitize_callback'    => '',
            'sanitize_js_callback' => '',
        ) );
        $wp_customize->add_control( 'cf7md_options[text_on_dark]', array(
            'type'        => 'number',
            'priority'    => 10,
            'section'     => 'cf7md_options',
            'label'       => 'Normal text opacity (dark theme)',
            'description' => '1 = white, 0 = transparent, default = 1',
            'input_attrs' => array(
            'min'  => 0,
            'max'  => 1,
            'step' => 0.01,
        ),
        ) );
        // Base font size
        $wp_customize->add_setting( 'cf7md_options[base_font_size]', array(
            'type'                 => 'option',
            'capability'           => 'edit_theme_options',
            'default'              => '18',
            'transport'            => 'postMessage',
            'sanitize_callback'    => '',
            'sanitize_js_callback' => '',
        ) );
        $wp_customize->add_control( 'cf7md_options[base_font_size]', array(
            'type'        => 'number',
            'priority'    => 10,
            'section'     => 'cf7md_options',
            'label'       => 'Base font size',
            'description' => '',
        ) );
        // Font family
        $font_list = get_option( 'cf7md_options[available_fonts]' );
        $available_fonts = array();
        if ( is_array( $font_list ) ) {
            foreach ( $font_list as $font ) {
                $font = stripcslashes( $font );
                $available_fonts[$font] = $font;
            }
        }
        $available_fonts = array(
            '"Roboto", sans-serif' => 'Roboto (material design default)',
        ) + $available_fonts;
        $wp_customize->add_setting( 'cf7md_options[font_family]', array(
            'type'                 => 'option',
            'capability'           => 'edit_theme_options',
            'default'              => '"Roboto", sans-serif',
            'transport'            => 'postMessage',
            'sanitize_callback'    => '',
            'sanitize_js_callback' => '',
        ) );
        $wp_customize->add_control( 'cf7md_options[font_family]', array(
            'type'        => 'select',
            'priority'    => 10,
            'section'     => 'cf7md_options',
            'label'       => 'Font family',
            'description' => 'Optionally choose to use a font from your current theme. Fonts not showing up? Try refreshing.',
            'choices'     => $available_fonts,
        ) );
        // Custom CSS
        $wp_customize->add_setting( 'cf7md_options[custom_css]', array(
            'type'                 => 'option',
            'capability'           => 'edit_theme_options',
            'default'              => '',
            'transport'            => 'postMessage',
            'sanitize_callback'    => '',
            'sanitize_js_callback' => '',
        ) );
        $wp_customize->add_control( 'cf7md_options[custom_css]', array(
            'type'        => 'textarea',
            'priority'    => 10,
            'section'     => 'cf7md_options',
            'label'       => 'Custom CSS',
            'description' => 'Add any custom CSS here. This will work even in the free version.',
        ) );
    }

}
// Finally initialize code
$cf7_material_design_customizer = new CF7_Material_Design_Customizer();