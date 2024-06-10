<?php

// don't load directly
if ( !defined( 'ABSPATH' ) ) {
    die( '-1' );
}
/**
 * Main plugin class
 */
class CF7_Material_Design
{
    private  $layout_atts = array(
        'desktopwidth' => 12,
        'tabletwidth'  => 8,
        'mobilewidth'  => 4,
    ) ;
    private  $fs ;
    private  $customize_url ;
    /**
     * Constructor - add hooks here and define shortcode
     */
    function __construct()
    {
        // Add scripts and styles
        add_action( 'wp_enqueue_scripts', array( $this, 'add_scripts_and_styles' ) );
        // Allow shortcodes in CF7
        add_filter( 'wpcf7_form_elements', 'do_shortcode' );
        // Set members
        global  $cf7md_fs ;
        $this->fs = $cf7md_fs;
        // Register shortcodes
        add_shortcode( 'md-form', array( $this, 'md_form_shortcode' ) );
        add_shortcode( 'md-raw', array( $this, 'md_raw_shortcode' ) );
        add_shortcode( 'md-grid', array( $this, 'md_grid_shortcode' ) );
        add_shortcode( 'md-text', array( $this, 'md_text_shortcode' ) );
        add_shortcode( 'md-textarea', array( $this, 'md_textarea_shortcode' ) );
        add_shortcode( 'md-select', array( $this, 'md_select_shortcode' ) );
        add_shortcode( 'md-checkbox', array( $this, 'md_checkbox_shortcode' ) );
        add_shortcode( 'md-radio', array( $this, 'md_radio_shortcode' ) );
        add_shortcode( 'md-accept', array( $this, 'md_accept_shortcode' ) );
        add_shortcode( 'md-file', array( $this, 'md_file_shortcode' ) );
        add_shortcode( 'md-quiz', array( $this, 'md_quiz_shortcode' ) );
        add_shortcode( 'md-captcha', array( $this, 'md_captcha_shortcode' ) );
        add_shortcode( 'md-submit', array( $this, 'md_submit_shortcode' ) );
    }
    
    /**
     * Add scripts and styles
     */
    public function add_scripts_and_styles()
    {
        // Scripts
        wp_enqueue_script(
            'autosize',
            plugins_url( '../assets/js/lib/autosize.min.js', __FILE__ ),
            array(),
            '1.0',
            true
        );
        wp_enqueue_script(
            'cf7-material-design',
            plugins_url( '../assets/js/cf7-material-design-bundle.js', __FILE__ ),
            array( 'jquery', 'autosize' ),
            CF7MD_VER,
            true
        );
        // Add ajax endpoint for logged-in users to hide the customize link
        if ( current_user_can( 'install_plugins' ) ) {
            wp_localize_script( 'cf7-material-design', 'ajax_object', array(
                'ajax_url' => admin_url( 'admin-ajax.php' ),
            ) );
        }
        // Styles
        $query_args = array(
            'family' => 'Roboto:400,500',
        );
        wp_enqueue_style(
            'cf7md_roboto',
            add_query_arg( $query_args, "//fonts.googleapis.com/css" ),
            array(),
            null
        );
        wp_enqueue_style(
            'cf7-material-design',
            plugins_url( '../assets/css/cf7-material-design.css', __FILE__ ),
            array(),
            CF7MD_VER
        );
        if ( $this->fs->can_use_premium_code() ) {
            wp_enqueue_style(
                'cf7md-material-icons',
                '//fonts.googleapis.com/icon?family=Material+Icons',
                array(),
                null
            );
        }
    }
    
    /**
     * Form shortcode
     */
    public function md_form_shortcode( $atts, $content = '' )
    {
        extract( shortcode_atts( array(
            'theme'   => '',
            'spacing' => '',
        ), $atts ) );
        $class = 'cf7md-form';
        if ( $theme !== '' ) {
            $class .= ' cf7md-theme--' . $theme;
        }
        if ( $spacing !== '' ) {
            $class .= ' cf7md-spacing--' . $spacing;
        }
        $query['autofocus[section]'] = 'cf7md_options';
        $query['return'] = get_permalink();
        $query['url'] = get_permalink();
        $customize_url = add_query_arg( $query, admin_url( 'customize.php' ) );
        ob_start();
        ?>
		<div id="cf7md-form" class="<?php 
        echo  esc_attr( $class ) ;
        ?>">
			<div class="mdc-layout-grid">
				<div class="mdc-layout-grid__inner">
					<?php 
        
        if ( is_customize_preview() ) {
            ?>
						<span class="customize-partial-edit-shortcut"><button aria-label="<?php 
            echo  esc_attr( __( "Edit form styles.", 'material-design-for-contact-form-7' ) ) ;
            ?>" title="<?php 
            echo  esc_attr( __( "Edit form styles.", 'material-design-for-contact-form-7' ) ) ;
            ?>" class="customizer-edit customize-partial-edit-shortcut-button" style="cursor: pointer !important;" data-control='{ "name": "cf7md_options[use_custom_styles]" }'><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M13.89 3.39l2.71 2.72c.46.46.42 1.24.03 1.64l-8.01 8.02-5.56 1.16 1.16-5.58s7.6-7.63 7.99-8.03c.39-.39 1.22-.39 1.68.07zm-2.73 2.79l-5.59 5.61 1.11 1.11 5.54-5.65zm-2.97 8.23l5.58-5.6-1.07-1.08-5.59 5.6z"></path></svg></button></span>
					<?php 
        }
        
        ?>
					<?php 
        echo  $this->remove_wpautop( $content ) ;
        ?>
					<?php 
        
        if ( !get_transient( 'cf7md_customize_link_closed' ) && current_user_can( 'install_plugins' ) && !is_customize_preview() ) {
            ?>
						<div class="cf7md-admin-customize-message mdc-layout-grid__cell mdc-layout-grid__cell--span-12">
							<p>
								<a href="<?php 
            echo  $customize_url ;
            ?>" title="<?php 
            echo  esc_attr( __( 'Only admins can see this message', 'material-design-for-contact-form-7' ) ) ;
            ?>"><?php 
            _e( "Customize your form's colors and fonts", 'material-design-for-contact-form-7' );
            ?></a>
								<a href="#" class="cf7md-hide-customize-message" aria-label="<?php 
            echo  esc_attr( __( "Don't show this again", 'material-design-for-contact-form-7' ) ) ;
            ?>" title="<?php 
            echo  esc_attr( __( "Don't show this again", 'material-design-for-contact-form-7' ) ) ;
            ?>">&times;</a>
							</p>
						</div>
					<?php 
        }
        
        ?>
				</div>
			</div>
		</div>
		<?php 
        $return = ob_get_contents();
        ob_end_clean();
        return $return;
    }
    
    /**
     * Raw shortcode
     */
    public function md_raw_shortcode( $atts, $content = '' )
    {
        $layout_classes = $this->get_layout_classes( $atts );
        ob_start();
        ?>
		<div class="cf7md-item <?php 
        echo  esc_attr( $layout_classes ) ;
        ?>">
			<?php 
        echo  $this->remove_wpautop( $content ) ;
        ?>
		</div>
		<?php 
        $return = ob_get_contents();
        ob_end_clean();
        return $return;
    }
    
    /**
     * Grid shortcode
     */
    public function md_grid_shortcode( $atts, $content = '' )
    {
        ob_start();
        ?>
		<div class="cf7md-grid mdc-layout-grid__inner">
			<?php 
        echo  $this->remove_wpautop( $content ) ;
        ?>
		</div>
		<?php 
        $return = ob_get_contents();
        ob_end_clean();
        return $return;
    }
    
    /**
     * Text field shortcode
     */
    public function md_text_shortcode( $atts, $content = '' )
    {
        $layout_classes = $this->get_layout_classes( $atts );
        extract( shortcode_atts( array(
            'label'    => '',
            'help'     => '',
            'outlined' => '',
            'dense'    => '',
            'icon'     => '',
        ), $atts ) );
        $modifiers = '';
        $modifiers .= ( $outlined ? ' mdc-text-field--outlined' : '' );
        $modifiers .= ( $dense ? ' mdc-text-field--dense' : '' );
        if ( $this->fs->can_use_premium_code() ) {
            $modifiers .= ( $icon ? ' mdc-text-field--with-leading-icon' : '' );
        }
        ob_start();
        ?>
		
		<div class="cf7md-item cf7md-text <?php 
        echo  esc_attr( $layout_classes ) ;
        ?>">
			<?php 
        echo  $content ;
        ?>
			<?php 
        $this->do_help( $help );
        ?>
			<div style="display: none;" class="cf7md-text-html">
				<div class="mdc-text-field <?php 
        echo  esc_attr( $modifiers ) ;
        ?>">
				<?php 
        if ( $this->fs->can_use_premium_code() ) {
            
            if ( $icon ) {
                ?>
							<i class="material-icons mdc-text-field__icon"><?php 
                echo  wp_kses_post( $icon ) ;
                ?></i>
						<?php 
            }
        
        }
        ?>	
					<?php 
        
        if ( $outlined ) {
            ?>
						<div class="mdc-notched-outline">
							<div class="mdc-notched-outline__leading"></div>
							<div class="mdc-notched-outline__notch">
								<label class="mdc-floating-label"><?php 
            echo  wp_kses_post( $label ) ;
            ?></label>
							</div>
							<div class="mdc-notched-outline__trailing"></div>
						</div>
					<?php 
        } else {
            ?>
						<label class="mdc-floating-label"><?php 
            echo  wp_kses_post( $label ) ;
            ?></label>
						<div class="mdc-line-ripple"></div>
					<?php 
        }
        
        ?>
				</div>
			</div>
		</div>
		<?php 
        $return = ob_get_contents();
        ob_end_clean();
        return $return;
    }
    
    /**
     * Textarea shortcode
     */
    public function md_textarea_shortcode( $atts, $content = '' )
    {
        $layout_classes = $this->get_layout_classes( $atts );
        extract( shortcode_atts( array(
            'label'    => '',
            'help'     => '',
            'autosize' => '1',
            'dense'    => '',
        ), $atts ) );
        $class = 'cf7md-item cf7md-textarea ' . $layout_classes;
        if ( $autosize ) {
            $class .= ' cf7md-textarea-autosize';
        }
        $modifiers = ' mdc-text-field--textarea';
        $modifiers .= ( $dense ? ' mdc-text-field--dense' : '' );
        //$modifiers .= $icon ? ' mdc-text-field--with-leading-icon' : '';
        ob_start();
        ?>

		<div class="<?php 
        echo  esc_attr( $class ) ;
        ?>">
			<?php 
        echo  $content ;
        ?>
			<?php 
        $this->do_help( $help );
        ?>
			<div style="display: none;" class="cf7md-text-html">
				<div class="mdc-text-field <?php 
        echo  esc_attr( $modifiers ) ;
        ?>">
					<?php 
        /*if( $icon ) : ?>
        			<i class="material-icons mdc-text-field__icon"><?php echo wp_kses_post( $icon ); ?></i>
        		<?php endif;*/
        ?>
					<div class="mdc-notched-outline">
						<div class="mdc-notched-outline__leading"></div>
						<div class="mdc-notched-outline__notch">
							<label class="mdc-floating-label"><?php 
        echo  wp_kses_post( $label ) ;
        ?></label>
						</div>
						<div class="mdc-notched-outline__trailing"></div>
					</div>
				</div>
			</div>
		</div>
		<?php 
        $return = ob_get_contents();
        ob_end_clean();
        return $return;
    }
    
    /**
     * Select shortcode
     */
    public function md_select_shortcode( $atts, $content = '' )
    {
        $layout_classes = $this->get_layout_classes( $atts );
        extract( shortcode_atts( array(
            'label'    => '',
            'help'     => '',
            'outlined' => '',
            'icon'     => '',
        ), $atts ) );
        $class = 'cf7md-item cf7md-select ' . $layout_classes;
        $multiple = strpos( $content, 'multiple' ) != 0;
        if ( $multiple ) {
            $class .= ' cf7md-select--multi';
        }
        $modifiers = ( $outlined ? ' mdc-select--outlined' : ' mdc-select--box' );
        //$modifiers .= $dense ? ' mdc-select--dense' : '';
        if ( $this->fs->can_use_premium_code() ) {
            $modifiers .= ( $icon ? ' mdc-select--with-leading-icon' : '' );
        }
        ob_start();
        ?>
		<div class="<?php 
        echo  esc_attr( $class ) ;
        ?>">
			<?php 
        echo  $content ;
        ?>
			<?php 
        $this->do_help( $help );
        ?>
			<div style="display: none;" class="cf7md-select-html">
				<div class="mdc-select <?php 
        echo  esc_attr( $modifiers ) ;
        ?>">
					<?php 
        if ( $this->fs->can_use_premium_code() ) {
            
            if ( $icon ) {
                ?>
							<i class="material-icons mdc-select__icon"><?php 
                echo  wp_kses_post( $icon ) ;
                ?></i>
						<?php 
            }
        
        }
        ?>	
					<i class="mdc-select__dropdown-icon"></i>
					<?php 
        
        if ( $outlined ) {
            ?>
						<div class="mdc-notched-outline">
							<div class="mdc-notched-outline__leading"></div>
							<div class="mdc-notched-outline__notch">
								<label class="mdc-floating-label"><?php 
            echo  wp_kses_post( $label ) ;
            ?></label>
							</div>
							<div class="mdc-notched-outline__trailing"></div>
						</div>
					<?php 
        } else {
            ?>
						<label class="mdc-floating-label"><?php 
            echo  wp_kses_post( $label ) ;
            ?></label>
						<div class="mdc-line-ripple"></div>
					<?php 
        }
        
        ?>
				</div>
			</div>
		</div>
		<?php 
        $return = ob_get_contents();
        ob_end_clean();
        return $return;
    }
    
    /**
     * Checkbox shortcode
     */
    public function md_checkbox_shortcode( $atts, $content = '' )
    {
        $layout_classes = $this->get_layout_classes( $atts );
        extract( shortcode_atts( array(
            'label'   => '',
            'help'    => '',
            'display' => 'stacked',
        ), $atts ) );
        $label = wp_kses_post( $label );
        $is_required = $this->detect_required( $content );
        $layout_classes .= ' cf7md-list cf7md-list__' . wp_kses_post( $display );
        ob_start();
        ?>
		<div class="cf7md-item cf7md-checkbox <?php 
        echo  esc_attr( $layout_classes ) ;
        ?>">
			<?php 
        
        if ( $label !== '' ) {
            ?>
				<label class="cf7md-label cf7md-label--static"><?php 
            echo  ( $is_required ? $label . '*' : $label ) ;
            ?></label>
			<?php 
        }
        
        ?>
			<?php 
        echo  $content ;
        ?>
			<div style="display: none;" class="cf7md-checkbox-html">
				<div class="mdc-checkbox__background">
					<svg class="mdc-checkbox__checkmark" viewBox="0 0 24 24">
						<path class="mdc-checkbox__checkmark-path" fill="none" d="M1.73,12.91 8.1,19.28 22.79,4.59"/>
					</svg>
					<div class="mdc-checkbox__mixedmark"></div>
				</div>
			</div>
			<?php 
        $this->do_help( $help );
        ?>
		</div>
		<?php 
        $return = ob_get_contents();
        ob_end_clean();
        return $return;
    }
    
    /**
     * Radio shortcode
     */
    public function md_radio_shortcode( $atts, $content = '' )
    {
        $layout_classes = $this->get_layout_classes( $atts );
        extract( shortcode_atts( array(
            'label'   => '',
            'help'    => '',
            'display' => 'stacked',
        ), $atts ) );
        $layout_classes .= ' cf7md-list cf7md-list__' . wp_kses_post( $display );
        ob_start();
        ?>
		<div class="cf7md-item cf7md-radio <?php 
        echo  esc_attr( $layout_classes ) ;
        ?>">
			<?php 
        
        if ( $label !== '' ) {
            ?>
				<label class="cf7md-label cf7md-label--static"><?php 
            echo  wp_kses_post( $label ) ;
            ?></label>
			<?php 
        }
        
        ?>
			<?php 
        echo  $content ;
        ?>
			<div style="display: none;" class="cf7md-radio-html">
				<div class="mdc-radio__background">
					<div class="mdc-radio__outer-circle"></div>
					<div class="mdc-radio__inner-circle"></div>
				</div>
			</div>
			<?php 
        $this->do_help( $help );
        ?>
		</div>
		<?php 
        $return = ob_get_contents();
        ob_end_clean();
        return $return;
    }
    
    /**
     * Accept shortcode
     */
    public function md_accept_shortcode( $atts, $content = '' )
    {
        $layout_classes = $this->get_layout_classes( $atts );
        extract( shortcode_atts( array(
            'label' => '',
            'help'  => '',
            'terms' => '',
        ), $atts ) );
        ob_start();
        $cf7v5_class = ( version_compare( WPCF7_VERSION, '5.0.0', '>=' ) ? 'cf7md-is-wpcf7v5 ' : '' );
        ?>
		<div class="cf7md-item cf7md-accept <?php 
        echo  $cf7v5_class ;
        echo  esc_attr( $layout_classes ) ;
        ?>">
			<?php 
        
        if ( $label !== '' ) {
            ?>
				<label class="cf7md-label cf7md-label--static"><?php 
            echo  wp_kses_post( $label ) ;
            ?></label>
			<?php 
        }
        
        ?>
			<?php 
        echo  $content ;
        ?>
			<label class="cf7md-accept-label"><?php 
        echo  $terms ;
        ?></label>
			<div style="display: none;" class="cf7md-checkbox-html">
				<div class="mdc-checkbox__background">
					<svg class="mdc-checkbox__checkmark" viewBox="0 0 24 24">
						<path class="mdc-checkbox__checkmark-path" fill="none" d="M1.73,12.91 8.1,19.28 22.79,4.59"/>
					</svg>
					<div class="mdc-checkbox__mixedmark"></div>
				</div>
			</div>
			<?php 
        $this->do_help( $help );
        ?>
		</div>
		<?php 
        $return = ob_get_contents();
        ob_end_clean();
        return $return;
    }
    
    /**
     * File field shortcode
     */
    public function md_file_shortcode( $atts, $content = '' )
    {
        $layout_classes = $this->get_layout_classes( $atts );
        extract( shortcode_atts( array(
            'label'    => '',
            'help'     => '',
            'nofile'   => __( 'No file chosen', 'material-design-for-contact-form-7' ),
            'btn_text' => __( 'Choose file', 'material-design-for-contact-form-7' ),
        ), $atts ) );
        $label = wp_kses_post( $label );
        $is_required = $this->detect_required( $content );
        ob_start();
        ?>
		<div class="cf7md-item cf7md-file <?php 
        echo  esc_attr( $layout_classes ) ;
        ?>">
			<?php 
        
        if ( $label !== '' ) {
            ?>
				<label class="cf7md-label cf7md-label--static"><?php 
            echo  ( $is_required ? $label . '*' : $label ) ;
            ?></label>
			<?php 
        }
        
        ?>
			<label class="cf7md-file--label">
				<span class="cf7md-file--btn mdc-button mdc-button--raised">
					<?php 
        echo  wp_kses_post( $btn_text ) ;
        ?>
				</span>
				<span class="cf7md-file--value"><?php 
        echo  wp_kses_post( $nofile ) ;
        ?></span>
				<?php 
        echo  $content ;
        ?>
			</label>
			<?php 
        $this->do_help( $help );
        ?>
		</div>
		<?php 
        $return = ob_get_contents();
        ob_end_clean();
        return $return;
    }
    
    /**
     * Quiz shortcode
     */
    public function md_quiz_shortcode( $atts, $content = '' )
    {
        $layout_classes = $this->get_layout_classes( $atts );
        extract( shortcode_atts( array(
            'label'    => '',
            'help'     => '',
            'outlined' => '',
        ), $atts ) );
        $modifiers = '';
        $modifiers .= ( $outlined ? ' mdc-text-field--outlined' : '' );
        ob_start();
        ?>
		<div class="cf7md-item cf7md-quiz <?php 
        echo  esc_attr( $layout_classes ) ;
        ?>">
			<?php 
        
        if ( $label ) {
            ?>	
				<label class="cf7md-label cf7md-label--static"><?php 
            echo  wp_kses_post( $label ) ;
            ?></label>
			<?php 
        }
        
        ?>
			<?php 
        echo  $content ;
        ?>
			<?php 
        $this->do_help( $help );
        ?>
			<div style="display: none;" class="cf7md-quiz-html">
				<div class="mdc-text-field <?php 
        echo  esc_attr( $modifiers ) ;
        ?>">
					<?php 
        
        if ( $outlined ) {
            ?>
						<div class="mdc-notched-outline">
							<div class="mdc-notched-outline__leading"></div>
							<div class="mdc-notched-outline__notch">
								<label class="mdc-floating-label"><?php 
            echo  wp_kses_post( $label ) ;
            ?></label>
							</div>
							<div class="mdc-notched-outline__trailing"></div>
						</div>
					<?php 
        } else {
            ?>
						<label class="mdc-floating-label"><?php 
            echo  wp_kses_post( $label ) ;
            ?></label>
						<div class="mdc-line-ripple"></div>
					<?php 
        }
        
        ?>
				</div>
			</div>
		</div>
		<?php 
        $return = ob_get_contents();
        ob_end_clean();
        return $return;
    }
    
    /**
     * Raw shortcode
     */
    public function md_captcha_shortcode( $atts, $content = '' )
    {
        $layout_classes = $this->get_layout_classes( $atts );
        ob_start();
        ?>
		<div class="cf7md-item cf7md-captcha <?php 
        echo  esc_attr( $layout_classes ) ;
        ?>">
			<?php 
        echo  $this->remove_wpautop( $content ) ;
        ?>
		</div>
		<?php 
        $return = ob_get_contents();
        ob_end_clean();
        return $return;
    }
    
    /**
     * Submit button shortcode
     */
    public function md_submit_shortcode( $atts, $content = '' )
    {
        $layout_classes = $this->get_layout_classes( $atts );
        extract( shortcode_atts( array(
            'style' => 'raised',
        ), $atts ) );
        ob_start();
        ?>
		<div class="cf7md-item cf7md-submit <?php 
        echo  esc_attr( $layout_classes ) ;
        ?>" data-button-style="<?php 
        echo  esc_attr( $style ) ;
        ?>">
			<?php 
        echo  $content ;
        ?>
			<svg class="cf7md-spinner" width="25px" height="25px" viewBox="0 0 66 66" xmlns="http://www.w3.org/2000/svg">
				<circle class="cf7md-spinner-path" fill="none" stroke-width="6" stroke-linecap="round" cx="33" cy="33" r="30"></circle>
			</svg>
		</div>
		<?php 
        $return = ob_get_contents();
        ob_end_clean();
        return $return;
    }
    
    /**
     * Get layout classes
     */
    private function get_layout_classes( $atts )
    {
        $desktopWidth = ( isset( $atts['desktopwidth'] ) ? $atts['desktopwidth'] : 12 );
        $tabletWidth = ( isset( $atts['tabletwidth'] ) ? $atts['tabletwidth'] : 8 );
        $mobileWidth = ( isset( $atts['mobilewidth'] ) ? $atts['mobilewidth'] : 4 );
        $classes = 'mdc-layout-grid__cell';
        
        if ( $this->fs->is__premium_only() && $this->fs->can_use_premium_code() ) {
            $classes .= ' mdc-layout-grid__cell--span-' . $desktopWidth . '-desktop';
            $classes .= ' mdc-layout-grid__cell--span-' . $tabletWidth . '-tablet';
            $classes .= ' mdc-layout-grid__cell--span-' . $mobileWidth . '-mobile';
        } else {
            $classes .= ' mdc-layout-grid__cell--span-12';
        }
        
        return esc_attr( $classes );
    }
    
    /**
     * Replace wpautop formatting
     */
    private function remove_wpautop( $content )
    {
        $content = do_shortcode( shortcode_unautop( $content ) );
        $content = preg_replace( '#^<\\/p>|^<br \\/>|<p>$#', '', $content );
        return $content;
    }
    
    /**
     * Output field help message
     */
    private function do_help( $help )
    {
        if ( $help !== '' ) {
            echo  '<p class="cf7md-help-text">' . wp_kses_post( $help ) . '</p>' ;
        }
    }
    
    /**
     * Detect required field
     */
    private function detect_required( $content )
    {
        return strpos( $content, 'wpcf7-validates-as-required' ) !== false;
    }

}
// Finally initialize code
$cf7_material_design = new CF7_Material_Design();