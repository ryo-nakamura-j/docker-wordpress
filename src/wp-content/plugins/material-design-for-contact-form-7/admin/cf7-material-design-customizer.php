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
            'title'       => sprintf( __( '%s Forms', 'material-design-for-contact-form-7' ), 'Material Design' ),
            'description' => sprintf( __( 'Customize your %s forms', 'material-design-for-contact-form-7' ), 'Material Design' ),
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
            'label'       => __( 'Use custom styles?', 'material-design-for-contact-form-7' ),
            'description' => sprintf( __( "Note: you're on the free version. Your style changes will not take effect until you %s upgrade to pro%s", 'material-design-for-contact-form-7' ), '<a href="' . $this->upgrade_url . '">', '</a>' ),
        );
        $wp_customize->add_control( 'cf7md_options[use_custom_styles]', $checkbox_control );
        // Primary color on light
        $wp_customize->add_setting( 'cf7md_options[primary_on_light]', array(
            'type'                 => 'option',
            'capability'           => 'edit_theme_options',
            'default'              => '#3f51b5',
            'transport'            => 'postMessage',
            'sanitize_callback'    => 'sanitize_hex_color',
            'sanitize_js_callback' => '',
        ) );
        $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'cf7md_options[primary_on_light]', array(
            'label'    => __( 'Primary color (default/light theme)', 'material-design-for-contact-form-7' ),
            'section'  => 'cf7md_options',
            'settings' => 'cf7md_options[primary_on_light]',
        ) ) );
        // Primary color on dark
        $wp_customize->add_setting( 'cf7md_options[primary_on_dark]', array(
            'type'                 => 'option',
            'capability'           => 'edit_theme_options',
            'default'              => '#3f51b5',
            'transport'            => 'postMessage',
            'sanitize_callback'    => 'sanitize_hex_color',
            'sanitize_js_callback' => '',
        ) );
        $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'cf7md_options[primary_on_dark]', array(
            'label'       => __( 'Primary color (dark theme)', 'material-design-for-contact-form-7' ),
            'section'     => 'cf7md_options',
            'settings'    => 'cf7md_options[primary_on_dark]',
            'description' => __( 'You may want to choose a slightly lighter version of your primary color for better contrast in the dark theme.', 'material-design-for-contact-form-7' ),
        ) ) );
        // Button color on light
        $wp_customize->add_setting( 'cf7md_options[button_on_light]', array(
            'type'                 => 'option',
            'capability'           => 'edit_theme_options',
            'default'              => '',
            'transport'            => 'postMessage',
            'sanitize_callback'    => 'sanitize_hex_color',
            'sanitize_js_callback' => '',
        ) );
        $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'cf7md_options[button_on_light]', array(
            'label'    => __( 'Button color', 'material-design-for-contact-form-7' ),
            'section'  => 'cf7md_options',
            'settings' => 'cf7md_options[button_on_light]',
        ) ) );
        // Button hover color on light
        $wp_customize->add_setting( 'cf7md_options[button_hover_on_light]', array(
            'type'                 => 'option',
            'capability'           => 'edit_theme_options',
            'default'              => '',
            'transport'            => 'postMessage',
            'sanitize_callback'    => 'sanitize_hex_color',
            'sanitize_js_callback' => '',
        ) );
        $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'cf7md_options[button_hover_on_light]', array(
            'label'    => __( 'Button hover color', 'material-design-for-contact-form-7' ),
            'section'  => 'cf7md_options',
            'settings' => 'cf7md_options[button_hover_on_light]',
        ) ) );
        // Button text color light theme
        $wp_customize->add_setting( 'cf7md_options[btn_text_light_theme]', array(
            'type'                 => 'option',
            'capability'           => 'edit_theme_options',
            'default'              => '#FFFFFF',
            'transport'            => 'postMessage',
            'sanitize_callback'    => 'sanitize_hex_color',
            'sanitize_js_callback' => '',
        ) );
        $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'cf7md_options[btn_text_light_theme]', array(
            'label'    => __( 'Button text color', 'material-design-for-contact-form-7' ),
            'section'  => 'cf7md_options',
            'settings' => 'cf7md_options[btn_text_light_theme]',
        ) ) );
        // Button color on dark
        $wp_customize->add_setting( 'cf7md_options[button_on_dark]', array(
            'type'                 => 'option',
            'capability'           => 'edit_theme_options',
            'default'              => '',
            'transport'            => 'postMessage',
            'sanitize_callback'    => 'sanitize_hex_color',
            'sanitize_js_callback' => '',
        ) );
        $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'cf7md_options[button_on_dark]', array(
            'label'       => __( 'Button color (dark theme)', 'material-design-for-contact-form-7' ),
            'section'     => 'cf7md_options',
            'settings'    => 'cf7md_options[button_on_dark]',
            'description' => __( 'Leave blank to use the dark theme primary color', 'material-design-for-contact-form-7' ),
        ) ) );
        // Button hover color on dark
        $wp_customize->add_setting( 'cf7md_options[button_hover_on_dark]', array(
            'type'                 => 'option',
            'capability'           => 'edit_theme_options',
            'default'              => '',
            'transport'            => 'postMessage',
            'sanitize_callback'    => 'sanitize_hex_color',
            'sanitize_js_callback' => '',
        ) );
        $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'cf7md_options[button_hover_on_dark]', array(
            'label'    => __( 'Button hover color (dark theme)', 'material-design-for-contact-form-7' ),
            'section'  => 'cf7md_options',
            'settings' => 'cf7md_options[button_hover_on_dark]',
        ) ) );
        // Button text color dark theme
        $wp_customize->add_setting( 'cf7md_options[btn_text_dark_theme]', array(
            'type'                 => 'option',
            'capability'           => 'edit_theme_options',
            'default'              => '#FFFFFF',
            'transport'            => 'postMessage',
            'sanitize_callback'    => 'sanitize_hex_color',
            'sanitize_js_callback' => '',
        ) );
        $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'cf7md_options[btn_text_dark_theme]', array(
            'label'       => __( 'Button text color (dark theme)', 'material-design-for-contact-form-7' ),
            'section'     => 'cf7md_options',
            'settings'    => 'cf7md_options[btn_text_dark_theme]',
            'description' => '',
        ) ) );
        // Text hint on light
        $wp_customize->add_setting( 'cf7md_options[text_hint_on_light]', array(
            'type'                 => 'option',
            'capability'           => 'edit_theme_options',
            'default'              => '0.5',
            'transport'            => 'postMessage',
            'sanitize_callback'    => '',
            'sanitize_js_callback' => '',
        ) );
        $wp_customize->add_control( 'cf7md_options[text_hint_on_light]', array(
            'type'        => 'number',
            'priority'    => 10,
            'section'     => 'cf7md_options',
            'label'       => __( 'Label text opacity', 'material-design-for-contact-form-7' ),
            'description' => __( '1 = black, 0 = transparent, default = 0.5', 'material-design-for-contact-form-7' ),
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
            'label'       => __( 'Normal text opacity', 'material-design-for-contact-form-7' ),
            'description' => __( '1 = black, 0 = transparent, default = 0.87', 'material-design-for-contact-form-7' ),
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
            'default'              => '0.72',
            'transport'            => 'postMessage',
            'sanitize_callback'    => '',
            'sanitize_js_callback' => '',
        ) );
        $wp_customize->add_control( 'cf7md_options[text_hint_on_dark]', array(
            'type'        => 'number',
            'priority'    => 10,
            'section'     => 'cf7md_options',
            'label'       => __( 'Label text opacity (dark theme)', 'material-design-for-contact-form-7' ),
            'description' => __( '1 = white, 0 = transparent, default = 0.72', 'material-design-for-contact-form-7' ),
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
            'label'       => __( 'Normal text opacity (dark theme)', 'material-design-for-contact-form-7' ),
            'description' => __( '1 = white, 0 = transparent, default = 1', 'material-design-for-contact-form-7' ),
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
            'label'       => __( 'Base font size', 'material-design-for-contact-form-7' ),
            'description' => __( 'Resize labels, help text etc. Text inputs and select fields can not be resized except by changing the font-size of your html tag, which is recommended to be set at 16px.', 'material-design-for-contact-form-7' ),
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
            'label'       => __( 'Font family', 'material-design-for-contact-form-7' ),
            'description' => __( 'Optionally choose to use a font from your current theme. Fonts not showing up? Try refreshing.', 'material-design-for-contact-form-7' ),
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
        
        if ( class_exists( 'WP_Customize_Code_Editor_Control' ) ) {
            $wp_customize->add_control( new WP_Customize_Code_Editor_Control( $wp_customize, 'cf7md_options[custom_css]', array(
                'label'           => __( 'Custom CSS', 'material-design-for-contact-form-7' ),
                'section'         => 'cf7md_options',
                'code_type'       => 'text/css',
                'editor_settings' => array(
                'codemirror' => array(
                'gutters'     => array(),
                'lineNumbers' => false,
            ),
            ),
                'input_attrs'     => array(
                'aria-describedby' => 'editor-keyboard-trap-help-1 editor-keyboard-trap-help-2 editor-keyboard-trap-help-3 editor-keyboard-trap-help-4',
            ),
                'description'     => __( 'Add any custom CSS here. This will work even in the free version.', 'material-design-for-contact-form-7' ),
            ) ) );
        } else {
            $wp_customize->add_control( 'cf7md_options[custom_css]', array(
                'type'        => 'textarea',
                'priority'    => 10,
                'section'     => 'cf7md_options',
                'label'       => __( 'Custom CSS', 'material-design-for-contact-form-7' ),
                'description' => __( 'Add any custom CSS here. This will work even in the free version.', 'material-design-for-contact-form-7' ),
            ) );
        }
    
    }

}
// Finally initialize code
$cf7_material_design_customizer = new CF7_Material_Design_Customizer();