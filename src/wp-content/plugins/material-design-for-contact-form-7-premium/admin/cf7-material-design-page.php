<?php

// don't load directly
if (!defined('ABSPATH')) die('-1');

class CF7_Material_Design_Admin_Page {

	private $customize_url;
    private $new_form_url;
    private $plugin_url;
    private $upgrade_url;
    private $upgrade_cost;
    private $fs;
    
    function __construct() {
		
		// Enqueue
        add_action( 'admin_enqueue_scripts', array( $this, 'add_scripts_and_styles' ) );

        // Other actions
        add_action( 'admin_menu', array( $this, 'add_menu_page' ) );

        // Set members
        $this->customize_url = admin_url( '/customize.php?autofocus[section]=cf7md_options' );
        $this->new_form_url = admin_url( '/admin.php?page=wpcf7-new' );
        $this->plugin_url = CF7MD_PLUGIN_DIR;
        global $cf7md_fs;
        $this->fs = $cf7md_fs;
        $this->upgrade_url = $cf7md_fs->get_upgrade_url( 'lifetime' );
        $this->upgrade_cost = CF7MD_UPGRADE_COST;
        $this->live_preview_url = esc_url( network_admin_url( 'plugin-install.php?tab=plugin-information&plugin=' . CF7MD_LIVE_PREVIEW_PLUGIN_SLUG ) );

	}


    /**
     * Enqueue scripts and styles
     */
    public function add_scripts_and_styles( $hook ) {
        
        // Register the admin scripts and styles
		wp_register_script( 'cf7md-slick', plugins_url( '../assets/js/lib/slick.min.js', __FILE__ ), array( 'jquery' ), '1.0', true );
		wp_register_script( 'cf7md-bundle', plugins_url( '../assets/js/cf7-material-design-bundle.js', __FILE__ ), array( 'jquery' ), '1.0', true );
        wp_register_script( 'cf7-material-design-admin', plugins_url( '../assets/js/cf7-material-design-admin.js', __FILE__ ), array( 'jquery', 'cf7md-slick' ), '1.0', true );

        wp_register_style( 'cf7-material-design-admin', plugins_url( '../assets/css/cf7-material-design-admin.css', __FILE__ ) );
        wp_register_style( 'cf7-material-design', plugins_url( '../assets/css/cf7-material-design.css', __FILE__ ), array(), '2.0' );

        // Load only on ?page=cf7md
        if( strpos( $hook, 'cf7md' ) !== false ) {
            wp_enqueue_script( 'cf7md-bundle' );
            wp_enqueue_script( 'cf7md-slick' );
            wp_enqueue_script( 'cf7-material-design-admin' );
            wp_enqueue_style( 'cf7-material-design-admin' );
            wp_enqueue_style( 'cf7-material-design' );
        }
    
    }


    /**
     * Add menu page
     */
    public function add_menu_page() {
        add_submenu_page( 'wpcf7', 'Material Design', 'Material Design', 'edit_theme_options', 'cf7md', array( $this, 'get_menu_page' ) );
    }


    /**
     * Get menu page
     */
    public function get_menu_page() {
        
        ?>
        <div class="cf7md-admin cf7md-page">
			<div class="cf7md-hero">
				<h1 class="cf7md-hero--title">Material Design</h1>
				<p class="cf7md-hero--subtitle">For Contact Form 7</p>
			</div>
			<div class="cf7md-main">

				<div class="cf7md-content">
					<div class="mdc-card" style="margin-bottom: 30px;">
						<div class="cf7md-card-header">
							<h4 class="cf7md-card-title"><?php _e( 'Plugin Settings', 'material-design-for-contact-form-7' ); ?></h4>
						</div>
						<div class="cf7md-card-body mdc-layout-grid">
							<div class="mdc-layout-grid__inner">
								<div class="cf7md-item cf7md-switch-pre-prepared mdc-layout-grid__cell mdc-layout-grid__cell--span-12">
									<label class="cf7md-label cf7md-label--static"><?php _e( 'Legacy styles', 'material-design-for-contact-form-7' ); ?></label>
									<span class="cf7md-switch-item cf">
										<div class="mdc-form-field">
											<div class="mdc-switch">
												<div class="mdc-switch__track"></div>
												<div class="mdc-switch__thumb-underlay mdc-ripple-upgraded mdc-ripple-upgraded--unbounded" style="--mdc-ripple-fg-size:28.8px; --mdc-ripple-fg-scale:1.66667; --mdc-ripple-left:10px; --mdc-ripple-top:10px;">
													<div class="mdc-switch__thumb"><input type="checkbox" value="legacy" class="mdc-switch__native-control" id="cf7md-legacy" name="cf7md-legacy" <?php echo CF7MD_STYLE_VERSION === 'legacy' ? 'checked' : ''; ?>></div>
												</div>
											</div>
											<label for="cf7md-legacy" class="cf7md-switch-label"><?php _e( 'Use legacy styles', 'material-design-for-contact-form-7' ); ?></label>
										</div>
									</span>
									<p class="cf7md-help-text"><?php _e( 'Material Design was recently updated to use boxed and outlined fields, rather than the classic underlined fields. This is a large design and code change. Activating legacy styles will force the plugin to use the legacy code for underlined fields. This is a site-wide setting.', 'material-design-for-contact-form-7' ); ?></p>
								</div>
								<?php if( get_option( 'cf7md_options[upgraded_from_v1]' ) ): ?>
									<div class="cf7md-item mdc-layout-grid__cell mdc-layout-grid__cell--span-12">
										<h4><?php _e( 'Upgrading from version 1', 'material-design-for-contact-form-7' ); ?></h4>
										<p><?php _e( 'If you\'re upgrading from version 1 of this plugin, there are a few things you should know before moving to the new styles.', 'material-design-for-contact-form-7' ); ?></p>
										<ul>
											<li><?php _e( 'The font-size settings in the customizer work slightly differently, as the new material fields cannot be easily resized, only the text around them will be resized.', 'material-design-for-contact-form-7' ); ?></li>
											<li><?php _e( 'Radio fields can no longer be turned into switches, only checkboxes can be switches.', 'material-design-for-contact-form-7' ); ?></li>
											<li><?php _e( 'You now have the option to use the outlined variants of text and select fields instead of the default boxed variant.', 'material-design-for-contact-form-7' ); ?></li>
										</ul>
										<p><?php _e( 'Once you\'ve turned off the switch above, check that your forms all still look and function as you\'d expect. If you think there\'s a bug, please post a message in the support forum.', 'material-design-for-contact-form-7' ); ?></p>
										<p><?php _e( 'If you liked the old styles better than the new styles, you can reactivate the switch above to continue to use the old styles and v1 plugin code. Note though that v1 will not receive updates other than bug fixes, and v1 is not translated.', 'material-design-for-contact-form-7' ); ?></p>
									</div>
								<?php endif; ?>
								<div class="cf7md-item mdc-layout-grid__cell mdc-layout-grid__cell--span-12">
									<button id="cf7md-legacy-submit" class="cf7md-submit-btn mdc-button mdc-button--raised mdc-button--primary mdc-ripple-surface" data-mdc-auto-init="MDCRipple"><?php _e( 'Save', 'material-design-for-contact-form-7' ); ?></button>
									<svg id="cf7md-legacy-spinner" class="cf7md-spinner" width="25px" height="25px" viewBox="0 0 66 66" xmlns="http://www.w3.org/2000/svg">
										<circle class="cf7md-spinner-path" fill="none" stroke-width="6" stroke-linecap="round" cx="33" cy="33" r="30"></circle>
									</svg>
								</div>
							</div>
							<script>
								(function($) {
									$(document).ready(function(){
										var $checkbox = $('#cf7md-legacy');
										var $submit = $('#cf7md-legacy-submit');
										var $spinner = $('#cf7md-legacy-spinner');
										setTimeout(function(){
											console.log($submit)
										}, 1000)
										$submit.click(function(e){
											console.log('clicked')
											$spinner.show();
											var data = {
												action: 'cf7md_update_legacy',
												cf7md_legacy: $checkbox.prop('checked') ? 'legacy' : 'v2'
											}
											$.post(ajaxurl, data, function(response) {
												window.location.reload(true);
											});
										});
									});
								}(jQuery));
							</script>
						</div>
					</div>

					<h2><?php _e( 'Thanks for installing!', 'material-design-for-contact-form-7' ); ?></h2>
					<p>
						<?php printf(
							/* translators: %1$s: "Material Design for Contact Form 7", %2$s: "Material Design" */
							__( '%1$s lets you style your forms with Google\'s %2$s', 'material-design-for-contact-form-7' ),
							'Material Design for Contact Form 7',
							'<a href="https://material.io/guidelines/material-design/introduction.html" target="_blank">Material Design</a>'
						); ?>
					</p>
					<h3><?php _e( 'Getting Started', 'material-design-for-contact-form-7' ); ?></h3>
					<p><?php _e( 'This plugin provides a set of shortcodes that wrap your existing Contact Form 7 form tags in the form editor. When you wrap a form tag in one of these shortcodes, its output is changed to Material Design.', 'material-design-for-contact-form-7' ); ?></p>
					<div class="mdc-card" style="margin-bottom: 20px;">
						<div class="cf7md-card-header">
							<h4 class="cf7md-card-title"><?php _e( 'Quick Start', 'material-design-for-contact-form-7' ); ?></h4>
							<p class="cf7md-card-subtitle"><?php _e( 'Fastest track to a working form', 'material-design-for-contact-form-7' ); ?></p>
						</div>
						<div class="cf7md-card-body">
							<ol>
								<li><a href="<?php echo $this->new_form_url; ?>" target="_blank"><?php _e( 'Add a new form', 'material-design-for-contact-form-7' ); ?></a>.</li>
								<li><?php /* translators: %s: "Material Design" */ printf( __( 'Delete everything from the form editor, and instead copy the example form code from the %s meta-box into the editor.', 'material-design-for-contact-form-7' ), '"Material Design"' ); ?></li>
								<li><?php _e( 'Save', 'material-design-for-contact-form-7' ); ?>.</li>
								<li><?php _e( 'Copy your form shortcode (under the form title) and paste it into a page (or post, widget, etc).', 'material-design-for-contact-form-7' ); ?></li>
								<li><?php _e( 'Save and preview the page.', 'material-design-for-contact-form-7' ); ?></li>
							</ol>
						</div>
					</div>
					<p><?php /* translators: %s: "Material Design" */ printf( __( 'After that, you can customize your form to your liking. Click the %s button alongside the CF7 form tag buttons to automatically generate shortcodes for the different field types.', 'material-design-for-contact-form-7' ), '"Material Design"' ); ?></p>
					<p><?php /* translators: %s: Plugin name */ printf( __( 'We also recommend installing the %s plugin for easier form development.', 'material-design-for-contact-form-7' ), '<a href="' .  esc_attr( $this->live_preview_url ) . '" target="_blank">Contact Form 7 Live Preview</a>' ); ?>
					<h3><?php _e( 'Documentation', 'material-design-for-contact-form-7' ); ?></h3>
					<p><?php _e( 'All shortcodes are documented in the help tab (top right of the screen) on the form editor page.', 'material-design-for-contact-form-7' ); ?></p>
					<h3><?php _e( 'Support', 'material-design-for-contact-form-7' ); ?></h3>
					<p><?php /* translators: %s: Hyperlink */ printf( __( 'Confused? Something doesn\'t look right? Have a specific question? Make a post in the %s support forum %s and I\'ll help you out.', 'material-design-for-contact-form-7' ), '<a href="https://wordpress.org/support/plugin/material-design-for-contact-form-7" target="_blank">', '</a>' ); ?></p>
					<h3><?php _e( 'Custom colors and fonts', 'material-design-for-contact-form-7' ); ?></h3>
					<p><?php /* translators: %s: Hyperlink */ printf( __( 'Customizing colors and fonts is a pro feature, but you can %s try it out for free in the customizer %s. Your changes just won\'t be saved unless you upgrade.', 'material-design-for-contact-form-7' ), '<a href="' . $this->customize_url . '">', '</a>' ); ?></p>
					<h3><?php _e( 'Like this plugin? Rate it!', 'material-design-for-contact-form-7' ); ?></h3>
					<p><a class="mdc-button mdc-button--raised" href="https://wordpress.org/support/plugin/material-design-for-contact-form-7/reviews/?rate=5#new-post" target="_blank"><?php _e( 'Leave a 5 star review', 'material-design-for-contact-form-7' ); ?></a></p>
				</div>

				<div class="cf7md-aside">
					<?php if( $this->fs->is_free_plan() ) : ?>
						<div class="mdc-card" style="margin-bottom: 32px;">
							<div class="cf7md-card-header">
								<h2 class="cf7md-card-title cf7md-card-title--large">
									<?php printf(
										/* translators: %s: Cost */
										__( 'Upgrade to Pro for %s', 'material-design-for-contact-form-7' ),
										$this->upgrade_cost
									); ?>
								</h2>
								<p class="cf7md-card-subtitle"><?php _e( 'And unlock all these extra features', 'material-design-for-contact-form-7' ); ?></p>
							</div>
							<div class="cf7md-card--slideshow">
								<div class="cf7md-card--slide">
									<div class="mdc-card__media mdc-card__media--img">
										<img src="<?php echo $this->plugin_url; ?>assets/images/features-slideshow-styles.png" alt="Custom styles">
									</div>
									<div class="mdc-card__supporting-text cf7md-card-body">
										<p><?php _e( 'Customize the colors and fonts to suit your theme.', 'material-design-for-contact-form-7' ); ?></p>
									</div>
								</div>
								<div class="cf7md-card--slide">
									<div class="mdc-card__media mdc-card__media--img">
										<img src="<?php echo $this->plugin_url; ?>assets/images/features-slideshow-switches.png" alt="Switches">
									</div>
									<div class="mdc-card__supporting-text cf7md-card-body">
										<p>
											<?php printf(
												/* translators: %1$s: open hyperlink, %2$s: close hyperlink, %3$s: shortcode */
												__( 'Turn checkboxes into %1$s switches %2$s using %3$s.', 'material-design-for-contact-form-7' ),
												'<a href="https://material.io/guidelines/components/selection-controls.html#selection-controls-switch" target="_blank">',
												'</a>',
												'<code>[md-switch]</code>'
											); ?>
										</p>
									</div>
								</div>
								<div class="cf7md-card--slide">
									<div class="mdc-card__media mdc-card__media--img">
										<img src="<?php echo $this->plugin_url; ?>assets/images/features-slideshow-icons.png" alt="Icons">
									</div>
									<div class="mdc-card__supporting-text cf7md-card-body">
										<p><?php _e( 'Add icons to text and select fields.', 'material-design-for-contact-form-7' ); ?></p>
									</div>
								</div>
								<div class="cf7md-card--slide">
									<div class="mdc-card__media mdc-card__media--img">
										<img src="<?php echo $this->plugin_url; ?>assets/images/features-slideshow-layout.png" alt="Custom layouts">
									</div>
									<div class="mdc-card__supporting-text cf7md-card-body">
										<p><?php _e( 'Easily organize your fields into columns.', 'material-design-for-contact-form-7' ); ?></p>
									</div>
								</div>
								<div class="cf7md-card--slide">
									<div class="mdc-card__media mdc-card__media--img">
										<img src="<?php echo $this->plugin_url; ?>assets/images/features-slideshow-cards.png" alt="Separate sections with cards">
									</div>
									<div class="mdc-card__supporting-text cf7md-card-body">
										<p>
											<?php printf(
												/* translators: %s: shortcode */
												__( 'Group fields into sections with the %s shortcode.', 'material-design-for-contact-form-7' ),
												'<code>[md-card]</code>'
											); ?>
										</p>
									</div>
								</div>
							</div>
							<section class="cf7md-card-actions">
								<a href="<?php echo $this->upgrade_url; ?>" class="cf7md-button">
									<?php printf(
										/* translators: %s: Cost */
										__( 'Upgrade now for %s', 'material-design-for-contact-form-7' ),
										$this->upgrade_cost
									); ?>
								</a>
							</section>
						</div>
						<h3><?php _e( 'All Pro Benefits', 'material-design-for-contact-form-7' ); ?></h3>
						<ul>
							<li><?php _e( 'Customize the colors and fonts to suit your theme.', 'material-design-for-contact-form-7' ); ?></li>
							<li>
								<?php printf(
									/* translators: %1$s: open hyperlink, %2$s: close hyperlink, %3$s: shortcode */
									__( 'Turn checkboxes into %1$s switches %2$s using %3$s.', 'material-design-for-contact-form-7' ),
									'<a href="https://material.io/guidelines/components/selection-controls.html#selection-controls-switch" target="_blank">',
									'</a>',
									'<code>[md-switch]</code>'
								); ?>
							</li>
							<li><?php _e( 'Add icons to your text and select fields.', 'material-design-for-contact-form-7' ); ?></li>
							<li><?php _e( 'Easily organize your fields into columns.', 'material-design-for-contact-form-7' ); ?></li>
							<li>
								<?php printf(
									/* translators: %s: shortcode */
									__( 'Group fields into sections with the %s shortcode.', 'material-design-for-contact-form-7' ),
									'<code>[md-card]</code>'
								); ?>
							</li>
							<li><?php _e( 'Faster support, directly through email.', 'material-design-for-contact-form-7' ); ?></li>
						</ul>
						<p><a class="mdc-button mdc-button--primary mdc-button--raised" href="<?php echo $this->upgrade_url; ?>" target="_blank">
							<?php printf(
								/* translators: %s: Cost */
								__( 'Upgrade now for %s', 'material-design-for-contact-form-7' ),
								$this->upgrade_cost
							); ?>
						</a></p>
						<p>
							Have a license already? <a href="https://users.freemius.com/plugin/771/downloads" style="display: block; margin: 3px 0 0;" target="_blank">Download the latest pro version.</a>
						</p>
					<?php else: ?>
						<div class="mdc-card" style="margin-bottom: 32px;">
							<div class="cf7md-card-header">
								<h2 class="cf7md-card-title cf7md-card-title--large"><?php _e( 'You have the pro version', 'material-design-for-contact-form-7' ); ?></h2>
								<p class="cf7md-card-subtitle"><?php __( 'Start using pro features now!', 'material-design-for-contact-form-7' ); ?></p>
							</div>
							<div class="cf7md-card-body">
								<ul style="margin-left: 5px;">
									<li><a href="<?php echo $this->customize_url; ?>"><?php _e( 'Customize the colors and fonts', 'material-design-for-contact-form-7' ); ?></a></li>
									<li>
										<?php printf(
											/* translators: %1$s: open hyperlink, %2$s: close hyperlink, %3$s: shortcode */
											__( 'Turn checkboxes into %1$s switches %2$s using %3$s.', 'material-design-for-contact-form-7' ),
											'<a href="https://material.io/guidelines/components/selection-controls.html#selection-controls-switch" target="_blank">',
											'</a>',
											'<code>[md-switch]</code>'
										); ?>
									</li>
									<li><?php _e( 'Add icons to your text and select fields.', 'material-design-for-contact-form-7' ); ?></li>
									<li>
										<?php printf(
											/* translators: %1$s, %2$s, %3$s: attribute */
											__( 'Use the %1$s, %2$s and %3$s attributes on your shortcodes to arrange them into columns', 'material-design-for-contact-form-7' ),
											'<code>desktopwidth</code>', '<code>tabletwidth</code>', '<code>mobilewidth</code>'
										); ?>
										<sup>*</sup>
									</li>
									<li>
										<?php printf(
											/* translators: %s: shortcode */
											__( 'Group fields into sections with the %s shortcode.', 'material-design-for-contact-form-7' ),
											'<code>[md-card]</code>'
										); ?>
										<sup>*</sup>
									</li>
									<li><a href="mailto:cf7materialdesign@gmail.com" target="_blank"><?php _e( 'Email me (Angus) directly for support', 'material-design-for-contact-form-7' ); ?></a></li>
								</ul>
								<p><small>* <?php _e( 'See documentation on form editor screen', 'material-design-for-contact-form-7' ); ?></small></p>
							</div>
						</div>
					<?php endif; ?>
				</div>
			</div>
        </div>
        <?php

    }
    
    
}

// Finally initialize code
$cf7_material_design_admin_page = new CF7_Material_Design_Admin_Page();
