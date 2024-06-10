<?php

/**
 * Handles all the script enqueueing and printing to the admin page
 */

// don't load directly
if (!defined('ABSPATH')) die('-1');

class CF7_Material_Design_Admin {

    private $shortcode_generator;
    private $scg_state;
    private $customize_url;
    private $demos_url;
    private $plugin_url;
    private $upgrade_url;
    private $upgrade_cost;
    private $live_preview_url;
    private $live_preview_plugin_active;
    private $fs;
    private $running_id;

	function __construct() {

        // Debugging
        //delete_transient( 'cf7md_pro_ad_closed' );
        //delete_transient( 'cf7md_customize_link_closed' );

		// Enqueue
        add_action( 'admin_enqueue_scripts', array( $this, 'add_scripts_and_styles' ) );

        // Other actions
        add_action( 'current_screen', array( $this, 'md_help_tab' ) );
        add_action( 'wp_ajax_cf7md_close_ad', array( $this, 'hide_pro_ad' ) );
        add_action( 'wp_ajax_cf7md_close_customize_link', array( $this, 'hide_customize_link' ) );

        // Set members
        $this->shortcode_generator = CF7_Material_Design_Shortcode_Generator::get_instance();
        $this->scg_state = $this->shortcode_generator->get_state();
        $this->customize_url = admin_url( '/customize.php?autofocus[section]=cf7md_options' );
        $this->demos_url = 'http://cf7materialdesign.com/demos/';
        $this->plugin_url = CF7MD_PLUGIN_DIR;
        global $cf7md_fs;
        $this->fs = $cf7md_fs;
        $this->upgrade_url = $cf7md_fs->get_upgrade_url( 'lifetime' );
        $this->upgrade_cost = CF7MD_UPGRADE_COST;
        $this->live_preview_url = esc_url( network_admin_url( 'plugin-install.php?tab=plugin-information&plugin=' . CF7MD_LIVE_PREVIEW_PLUGIN_SLUG ) );
        $this->running_id = 0;

        // This allows us to check if other plugins are active
        if( !function_exists('is_plugin_active') ) {
            include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
        }
        $this->live_preview_plugin_active = is_plugin_active( 'cf7-live-preview/contact-form-7-live-preview.php' ) || is_plugin_active( 'contact-form-7-live-preview/contact-form-7-live-preview.php' );

	}


    /**
     * Enqueue scripts and styles
     */
    public function add_scripts_and_styles( $hook ) {
        
        // Register the admin scripts and styles
        wp_register_script( 'cf7md-slick', plugins_url( '../assets/js/lib/slick.min.js', __FILE__ ), array( 'jquery' ), '1.0', true );
        wp_enqueue_script( 'md-components-js', plugins_url( '../assets/js/lib/material-components-web.min.js', __FILE__ ), array(), '1.0', true );
        wp_enqueue_script( 'nouislider', plugins_url( '../assets/js/lib/nouislider.min.js', __FILE__ ), array( 'jquery' ), '1.0', true );
        wp_enqueue_script( 'clipboard', plugins_url( '../assets/js/lib/clipboard.min.js', __FILE__ ), array( 'jquery' ), '1.0', true );
        wp_register_script( 'cf7-material-design-admin', plugins_url( '../assets/js/cf7-material-design-admin.js', __FILE__ ), array( 'jquery', 'cf7md-slick', 'md-components-js', 'nouislider', 'clipboard' ), '1.1', true );
        wp_register_script( 'cf7-material-design-shortcode-generator', plugins_url( '../assets/js/cf7-material-design-shortcode-generator.js', __FILE__ ), array( 'jquery' ), '1.0', true );
        wp_register_style( 'cf7-material-design-admin', plugins_url( '../assets/css/cf7-material-design-admin.css', __FILE__ ), array(), '2.0' );
        wp_register_style( 'cf7-material-design', plugins_url( '../assets/css/cf7-material-design.css', __FILE__ ), array(), '2.0' );

        // Localize the script with the html
        $localize = array(
            'shortcode_generator_state' => json_encode( $this->scg_state ),
            'instructions_metabox' => $this->get_metabox_html(),
            'pro_ad' => $this->get_pro_ad_html(),
            'preview_ad' => $this->get_preview_ad_html()
        );
        wp_localize_script( 'cf7-material-design-admin', 'cf7md_html', $localize );

        // Enqueued script with localized data.
        // Load only on ?page=wpcf7
        if( strpos( $hook, 'wpcf7' ) !== false ) {
            wp_enqueue_script( 'md-components-js' );
            wp_enqueue_script( 'nouislider' );
            wp_enqueue_script( 'clipboard' );
            wp_enqueue_script( 'cf7-material-design-shortcode-generator' );
            wp_enqueue_script( 'cf7-material-design-admin' );
            wp_enqueue_style( 'cf7-material-design-admin' );
            wp_enqueue_style( 'cf7-material-design' );

            add_action( 'admin_footer', array( $this, 'shortcode_generator_html' ) );
        }
    
    }


    /**
     * Shortcode generator modal html
     */
    public function shortcode_generator_html() {
        ?>

        <div class="cf7md-modal-backdrop"></div>
        <div class="cf7md-modal">
            <div class="cf7md-scg">
                <div class="cf7md-scg--header">
                    <a href="#" class="cf7md-modal-close-btn">
                        <div class="dashicons-before dashicons-no-alt"></div>
                        <div class="screen-reader-text">Close</div>
                    </a>
                    <a href="#" class="cf7md-modal-back-btn">
                        <div class="dashicons-before dashicons-arrow-left-alt"></div>
                        <div class="screen-reader-text">Back</div>
                    </a>
                    <h3 class="cf7md-scg--title">Material Design Shortcode Generator</h3>
                </div>
                <div class="cf7md-scg--body">
                    <div class="cf7md-scg--list-panel" data-panel="list">
                        <h3 class="mdc-list-group__subheader">Choose a shortcode to generate</h3>
                        <nav id="cf7md-scg-list" class="cf7md-scg--list mdc-list mdc-list--two-line mdc-list--avatar-list">
                            <div role="separator" class="mdc-list-divider"></div>
                            <?php foreach( $this->scg_state['shortcodes'] as $sc ) : ?>
                                <?php
                                $locked = isset($sc['locked']) && $sc['locked'];
                                $openTag = $locked ? '<div' : '<a href="#"';
                                $closeTag = $locked ? '</div>' : '</a>';
                                printf(
                                    '%1$s class="mdc-list-item %2$s" data-open-panel="%3$s" data-mdc-auto-init="MDCRipple">',
                                    $openTag,
                                    $locked ? 'cf7md-list-item__locked' : '',
                                    esc_attr($sc['type'])
                                )
                                ?>
                                    <span class="mdc-list-item__text">
                                        <span class="mdc-list-item__text__primary">
                                            <?php echo $sc['name']; ?>
                                            <?php echo $locked ? ' (Pro feature)' : ''; ?>
                                        </span>
                                        <span class="mdc-list-item__text__secondary"><?php echo $sc['description']; ?></span>
                                    </span>
                                    <?php if( $locked ) : ?>
                                        <a class="cf7md-list-item--upgrade-btn mdc-button" data-mdc-auto-init="MDCRipple" href="<?php echo $this->upgrade_url; ?>">Upgrade Now</a>
                                    <?php endif; ?>
                                <?php echo $closeTag; ?>
                            <?php endforeach; ?>
                        </nav>
                    </div>

                    <div class="cf7md-scg--field-panels">
                        <?php foreach( $this->scg_state['shortcodes'] as $sc ) : ?>
                            <div class="cf7md-scg--panel" data-panel="<?php echo esc_attr($sc['type']); ?>" style="display: none;">
                                <div class="cf7md-scg--panel-body">
                                    <div class="cf7md-scg--panel-header">
                                        <h3 class="cf7md-scg--panel-title">
                                            <?php echo $sc['name'] . ': <code>[' . $sc['type'] . ']</code>'; ?>
                                        </h3>
                                        <p class="cf7md-scg--panel-subtitle">
                                            <?php echo $sc['description']; ?>
                                        </p>
                                    </div>
                                    <div id="cf7md-form" class="cf7md-form cf7md-scg--fields">
                                        <p>Generate a <code>[<?php echo $sc['type']; ?>]</code> shortcode with your desired settings.</p>
                                        <?php foreach( $sc['attributes'] as $i => $att ) :
                                            $method = 'render_' . $att['renderer'] . '_field';
                                            CF7_Material_Design_Shortcode_Generator::$method( $att, $i, ++$this->running_id );
                                        endforeach; ?>
                                    </div>
                                </div>
                                <div class="cf7md-scg--footer">
                                    <textarea class="cf7md-scg--shortcode" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false"></textarea>
                                    <div class="cf7md-scg--footer-buttons">
                                        <button class="cf7md-scg--copy-btn mdc-button mdc-button--dense" data-mdc-auto-init="MDCRipple">Copy</button>
                                        <button class="cf7md-scg--insert-btn mdc-button mdc-button--raised mdc-button--primary mdc-button--dense" data-mdc-auto-init="MDCRipple">Insert</button>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>

        <?php
    }


    /**
     * Pro advertisement html
     */
    private function get_pro_ad_html() {
        if( ! $this->fs->is_free_plan() ) return '';
        if( get_transient( 'cf7md_pro_ad_closed' ) ) return '';

        ob_start();
        
        ?>
        <div id="cf7md-pro-admin" class="cf7md-card">
            <a href="#" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></a>
            <div class="cf7md-card--img-left">
                <div class="cf7md-pro-admin-slideshow">
                    <img data-about="styles" src="<?php echo $this->plugin_url; ?>assets/images/admin-slide-styles.png" alt="Custom styles">
                    <img data-about="switches" src="<?php echo $this->plugin_url; ?>assets/images/admin-slide-switches.png" alt="Switches">
                    <img data-about="columns" src="<?php echo $this->plugin_url; ?>assets/images/admin-slide-columns.png" alt="Columns">
                    <img data-about="cards" src="<?php echo $this->plugin_url; ?>assets/images/admin-slide-cards.png" alt="Cards">
                </div>
            </div>
            <div class="cf7md-card--body-right">
                <h2 class="cf7md-card-title">Upgrade to Material Design Pro</h2>
                <div class="cf7md-card-content">
                    <ul>
                        <li class="li-for-styles"><a href="https://cf7materialdesign.com/demos/custom-styles/" target="_blank" title="Demo (opens in new tab)">Customise</a> the colours and fonts. <a href="<?php echo $this->customize_url; ?>" target="_blank">Try it for free</a>.</li>
                        <li class="li-for-switches">Turn checkboxes and radios into <a href="https://cf7materialdesign.com/demos/switches/" target="_blank" title="Demo (opens in new tab)">switches</a>.</li>
                        <li class="li-for-columns">Organise your fields into <a href="https://cf7materialdesign.com/demos/columns/" target="_blank" title="Demo (opens in new tab)">columns</a>.</li>
                        <li class="li-for-cards">Group fields with <a href="https://cf7materialdesign.com/demos/field-groups/" target="_blank" title="Demo (opens in new tab)">cards</a>.</li>
                        <li>Direct email support.</li>
                    </ul>
                </div>
                <div class="cf7md-card-actions">
                    <a href="<?php echo $this->upgrade_url; ?>" class="cf7md-button">Upgrade for <?php echo $this->upgrade_cost; ?></a>
                </div>
            </div>
        </div>
        <?php            
        
        $return = ob_get_contents();
        ob_end_clean();

        return $return;
    }


    /**
     * Hide pro ad
     * Activated by ajax so ends in wp_die()
     */
    public function hide_pro_ad() {
        set_transient( 'cf7md_pro_ad_closed', array( 1 ), 1 * DAY_IN_SECONDS );
        echo 'Success';
        wp_die();
    }


    /**
     * Hide customize link
     * Activated by ajax so ends in wp_die()
     */
    public function hide_customize_link() {
        set_transient( 'cf7md_customize_link_closed', array( 1 ), 1 * DAY_IN_SECONDS );
        update_option( 'cf7md_customize_link_closed', 1 );
        echo 'Success';
        wp_die();
    }


    /**
     * Live preview ad html
     */
    private function get_preview_ad_html() {
        if( $this->live_preview_plugin_active )
            return;

        ob_start();
        
        ?>
        <span class="cf7md-live-preview-text">Sick of the <code>save &rarr; switch tabs &rarr; refresh</code> cycle for viewing your form changes? We recommend using the <a href="<?php echo esc_attr( $this->live_preview_url ); ?>" target="_blank">Contact Form 7 Live Preview</a> plugin to instantly view your changes as you make them.</span>
        <?php            
        
        $return = ob_get_contents();
        ob_end_clean();

        return $return;
    }


    /**
     * Metabox html
     */
    private function get_metabox_html() {
        ob_start();

		?>
		<div id="cf7md-instructions-metabox" class="cf7md-card">
            <h2 class="cf7md-card-title">Material Design</h2>
            <div class="cf7md-card-content">
                <p>Apply material design to your form using shortcodes which you can generate with the "Material Design" button. Your form should look something like this:</p>
                <pre>[md-form]

[md-text label="Your name"]
[text* your-name]
[/md-text]

[md-text label="Your email"]
[email* your-email]
[/md-text]

[md-textarea label="Your message"]
[textarea* your-message]
[/md-textarea]

[md-submit]
[submit "Send"]
[/md-submit]

[/md-form]</pre>
            <ul>
                <li><a href="#" class="cf7md-open-docs">Docs / Available Shortcodes</a></li>
                <li><a href="<?php echo $this->demos_url; ?>" target="_blank">Demos</a></li>
                <li><a href="https://wordpress.org/support/plugin/material-design-for-contact-form-7/reviews/?rate=5#new-post" target="_blank">Rate this plugin</a></li>
                <?php if( $this->fs->is_free_plan() ) : ?>
                    <li><a href="https://wordpress.org/support/plugin/material-design-for-contact-form-7/" target="_blank">Support</a></li>
                    <li><a href="<?php echo $this->customize_url; ?>">Try the style customizer</a> (pro feature)</li>
                <?php else: ?>
                    <li><a href="mailto:cf7materialdesign@gmail.com" target="_blank">Direct email support</a></li>
                    <li><a href="<?php echo $this->customize_url; ?>">Customize styles</a></li>
                <?php endif; ?>
                <?php if( ! $this->live_preview_plugin_active ) : ?>
                    <li><a href="<?php echo esc_attr( $this->live_preview_url ); ?>" target="_blank">Get the CF7 Live Preview plugin</a></li>
                <?php endif; ?>

            </ul>
            </div>
            <?php if( $this->fs->is_free_plan() ) : ?>
                <div class="cf7md-card-actions">
                    <a href="<?php echo $this->upgrade_url; ?>" class="cf7md-button">Upgrade for <?php echo $this->upgrade_cost; ?></a>
                </div>
            <?php endif; ?>
        </div>
		<?php            
        
		$return = ob_get_contents();
        ob_end_clean();

        return $return;
    }


    /**
     * Help tab html
     */
    private function get_help_tab_html() {

        $pro_feature_link = '(<a href="' . $this->upgrade_url . '" title="Upgrade now for ' . $this->upgrade_cost . '">pro&nbsp;feature</a>)';
        $container_class = 'cf7md-docs';
        if( $this->fs->is_paying__premium_only() ) {
            $container_class .= ' cf7md-is-premium';
            $pro_feature_link = '';
        } else {
            $container_class .= ' cf7md-is-free';
        }

        $label_attr = '<code>label</code> - the label for your form field';
        $help_attr = '<code>help</code> - (optional) text for below the field';
        $display_attr = '<code>display</code> - (optional) how to arrange the list items. Options are <code>stacked</code> (default), <code>inline</code>, <code>columns-2</code>, <code>columns-3</code> or <code>columns-4</code>';
        $width_attrs = '<span class="cf7md-pro-sc">Width attributes - ' . $pro_feature_link . ' see layout section below</span>';

        ob_start();

		?>
        <div class="<?php echo $container_class; ?>">
            <p>You can add material design to your new <em>and</em> existing forms by wrapping the form tags in these shortcodes. <strong>Note:</strong> all the shortcodes below go in your Contact Form 7 form, NOT directly on a page or post.</p>
            <p><em>What do you mean by wrap?</em> - each shortcode has an opening <em>and</em> closing 'tag'. The opening tag (E.g. <code>[md-submit]</code>) goes before your <code>submit</code> form tag, and the closing tag (E.g. <code>[/md-submit]</code>) goes after it. Ending tags are the same as starting tags, but have <code>/</code> before the tag name, and don't need any parameters. Here's a full example of wrapping your submit button in a material design shortcode:</p>
            <p style="margin-left: 20px;"><code>[md-submit][submit "Send"][/md-submit]</code></p>
            <p>Some shortcodes also have 'parameters' that let you specify more details about your field. Parameters are added to the opening tag like so:</p>
            <p style="margin-left: 20px;"><code>[md-text label="Your name"]</code></p>
            <p>Here, we give the <code>label</code> parameter a value of 'Your name', which specifies for us that the field's label should be 'Your name'.</p>
            <h4>Generate Shortcodes</h4>
            <p>The "Material Design" button included alongside the CF7 form tag buttons allows you to generate any of the available shortcodes, and in most cases will be easier than referring to this help section.</p>
            <h4>All Available Shortcodes</h4>
            <p>See these shortcodes in action, including example code, at the <a href="<?php echo $this->demos_url; ?>" target="_blank">demo site</a>.</p>
            <table class="cf7md-table">
                <thead>
                    <tr>
                        <th style="width: 110px;">Shortcode</th>
                        <th>Use</th>
                        <th style="width: 300px;">Parameters</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><code>[md-form]</code></td>
                        <td>Wraps your entire form.</td>
                        <td><code>theme</code> - optionally specify <code>theme="dark"</code> for forms on a dark background<hr />
                        <code>spacing</code> - optionally specify <code>spacing="tight"</code> for less vertical space between fields</td>
                    </tr>
                    <tr>
                        <td><code>[md-raw]</code></td>
                        <td>Wraps any miscellaneous elements.</td>
                        <td><?php echo $width_attrs; ?></td>
                    </tr>
                    <tr>
                        <td><code>[md-grid]</code></td>
                        <td>Any <code>[md-*]</code> shortcodes that are not direct descendants of <code>[md-form]</code> or <code>[md-card]</code> (E.g. if they are nested inside a <code>&lt;div></code> or another shortcode) need to be wrapped in <code>[md-grid][/md-grid]</code> to be displayed correctly. A single <code>[md-grid][/md-grid]</code> should be the only direct descendant of the <code>&lt;div></code> (or whatever the wrapper is, e.g. a shortcode), and the other elements should be direct descendants of the <code>[md-grid]</code>.</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td><code>[md-text]</code></td>
                        <td>Wraps text, email, url, tel, number and date form tags.</td>
                        <td>
                            <?php echo $label_attr; ?><hr />
                            <?php echo $help_attr; ?><hr />
                            <?php echo $width_attrs; ?>
                        </td>
                    </tr>
                    <tr>
                        <td><code>[md-textarea]</code></td>
                        <td>Wraps your textarea form tags.</td>
                        <td>
                            <code>autosize</code> - <code>1</code> (default) to auto-resize or <code>0</code> to remain static<hr />
                            <?php echo $label_attr; ?><hr />
                            <?php echo $help_attr; ?><hr />
                            <?php echo $width_attrs; ?>
                        </td>
                    </tr>
                    <tr>
                        <td><code>[md-select]</code></td>
                        <td>Wraps your drop-down menu form tags.</td>
                        <td>
                            <?php echo $label_attr; ?><hr />
                            <?php echo $help_attr; ?><hr />
                            <?php echo $width_attrs; ?>
                        </td>
                    </tr>
                    <tr>
                        <td><code>[md-checkbox]</code></td>
                        <td>Wraps your checkbox form tags.</td>
                        <td>
                            <?php echo $label_attr; ?><hr />
                            <?php echo $display_attr; ?><hr />
                            <?php echo $help_attr; ?><hr />
                            <?php echo $width_attrs; ?>
                        </td>
                    </tr>
                    <tr>
                        <td><code>[md-radio]</code></td>
                        <td>Wraps your radio form tags.</td>
                        <td>
                            <?php echo $label_attr; ?><hr />
                            <?php echo $display_attr; ?><hr />
                            <?php echo $help_attr; ?><hr />
                            <?php echo $width_attrs; ?>
                        </td>
                    </tr>
                    <tr class="cf7md-pro-sc">
                        <td><code>[md-switch]</code></td>
                        <td>Wraps checkbox OR radio form tags to turn them into <a href="https://material.io/guidelines/components/selection-controls.html#selection-controls-switch" target="_blank">switches</a>. <?php echo $pro_feature_link; ?></td>
                        <td>
                            <?php echo $label_attr; ?><hr />
                            <?php echo $display_attr; ?><hr />
                            <?php echo $help_attr; ?><hr />
                            <?php echo $width_attrs; ?>
                        </td>
                    </tr>
                    <tr>
                        <td><code>[md-accept]</code></td>
                        <td>Wraps your acceptance form tags.</td>
                        <td>
                            <code>terms</code> - (optional) the terms to which the user must agree. NOTE: If the CF7 acceptance tag has content, that content will override this terms attribute.<hr />
                            <?php echo $label_attr; ?><hr />
                            <?php echo $help_attr; ?><hr />
                            <?php echo $width_attrs; ?>
                        </td>
                    </tr>
                    <tr>
                        <td><code>[md-file]</code></td>
                        <td>Wraps your file upload tags.</td>
                        <td>
                            <code>nofile</code> - the text to show before a file is chosen (default: No file chosen)<hr />
                            <code>btn_text</code> - the button text (default: Choose file)<hr />
                            <?php echo $label_attr; ?><hr />
                            <?php echo $help_attr; ?><hr />
                            <?php echo $width_attrs; ?>
                        </td>
                    </tr>
                    <tr>
                        <td><code>[md-quiz]</code></td>
                        <td>Wraps your quiz form tags.</td>
                        <td>
                            <?php echo $label_attr; ?><hr />    
                            <?php echo $help_attr; ?><hr />
                            <?php echo $width_attrs; ?>
                        </td>
                    </tr>
                    <tr>
                        <td><code>[md-captcha]</code></td>
                        <td>Wraps your captcha form tags.</td>
                        <td>
                            <?php echo $width_attrs; ?>
                        </td>
                    </tr>
                    <tr>
                        <td><code>[md-submit]</code></td>
                        <td>Wraps your submit button form tag.</td>
                        <td><?php echo $width_attrs; ?></td>
                    </tr>
                    <tr class="cf7md-pro-sc">
                        <td><code>[md-card]</code></td>
                        <td>Wraps multiple elements (including other <code>[md-*]</code> shortcodes) to group them into sections. <?php echo $pro_feature_link; ?></td>
                        <td>
                            <code>title</code> - (optional) the title for the section<hr />
                            <code>subtitle</code> - (optional) the subtitle for the section<hr />
                            <code>titlesize</code> - optionally set to <code>large</code><hr />
                            <?php echo $width_attrs; ?>
                        </td>
                    </tr>
                </tbody>
            </table>

            <h4 id="cf7md-layout">Layout attributes <?php echo $pro_feature_link; ?></h4>
            <p>If you're on the pro version, most shortcodes have width attributes available. The width attributes specify how many columns out of 12 (desktop), 8 (tablet) or 4 (mobile) the element should occupy. The attributes are:</p>
            <ul>
                <li><code>desktopwidth</code> - how many columns out of 12 should the element occupy on large screens (>= 840px)?</li>
                <li><code>tabletwidth</code> - how many columns out of 8 should the element occupy on medium-sized screens (>= 480px)?</li>
                <li><code>mobilewidth</code> - how many columns out of 4 should the element occupy on small screens (&lt; 480px)?</li>
            </ul>
            <p>Here's an example of making two fields appear side-by-side on desktop and tablet, and vertically stacked on mobiles.</p>
            <p style="margin-left: 20px;"><code>[md-text tabletwidth="4" desktopwidth="6"][text your-name][/md-text]</code><br /><code>[md-text tabletwidth="4" desktopwidth="6"][email your-email][/md-text]</code></p>
            <p>We set <code>tabletwidth</code> to <code>4</code> (half of 8) and <code>desktopwidth</code> to <code>6</code> (half of 12). We don't need to specify <code>mobilewidth</code> because the default is always to fill all available columns.</p>

            <p><strong>Note:</strong> layout attributes will not work unless the element is a direct descendant of either <code>[md-form]</code>, <code>[md-grid]</code> or <code>[md-card]</code>.

            <h4>How can I customize the colours and fonts to match my theme?</h4>
            <?php if( $this->fs->is_paying__premium_only() ) : ?>
                <p>You're on the pro version, so you can pick your own styles in the <a href="<?php echo $this->customize_url; ?>">customizer</a>.</p>
            <?php else: ?>
                <p>Customizing colours and fonts is a pro feature, but you can <a href="<?php echo $this->customize_url; ?>">try it out for free in the customizer</a>, your styles just won't be applied until you upgrade. Once you upgrade, the styles you chose will take effect.</p>
            <?php endif; ?>

            <h4>It doesn't look right for me!</h4>
            <p>Some themes have styles that override the material design styles. If this happens to you, post a link to your form page in the <a href="https://wordpress.org/support/plugin/material-design-for-contact-form-7/" target="_blank">support forum</a> and I'll help you fix it.</p>
            <?php if( $this->fs->is_paying__premium_only() ) : ?>
                <p>You're on the pro version, so you may also <a href="mailto:cf7materialdesign@gmail.com">email me directly</a> with your questions.</p>
            <?php endif; ?>

            <h4>Integration with other plugins</h4>
            <p><strong><a href="https://wordpress.org/plugins/mailchimp-for-wp/" target="_blank">Mailchimp for WordPress</a></strong> - you can add a "Subscribe to newsletter" checkbox like so. Change the label and terms to your liking.</p>
            <pre style="margin-left: 20px;">[md-accept label="Mailchimp" terms="Subscribe me to emails"]
&lt;span class="wpcf7-form-control-wrap">&lt;input type="checkbox" name="mc4wp-subscribe" value="1" />&lt;/span>
[/md-accept]</pre>
            <p><strong><a href="https://wordpress.org/plugins/contact-form-7-multi-step-module/" target="_blank">Contact Form 7 Multi-Step Forms</a></strong> - Should work out of the box. If you want to add a "Previous" button, you can put it in the same <code>[md-submit]</code> tag as your "Next" (submit) button. E.g.</p>
            <pre style="margin-left: 20px;">[md-submit]
[previous "Previous"]
[submit "Next"]
[/md-submit]</pre>
            <p><strong><a href="https://wordpress.org/plugins/cf7-conditional-fields/" target="_blank">Conditional Fields for Contact Form 7</a></strong> - Should work out of the box :)</p>
        </div>
		<?php            
        
		$return = ob_get_contents();
        ob_end_clean();

        return $return;
    }


    /**
     * Add help tab
     */
    public function md_help_tab() {

        $screen = get_current_screen();

        //echo '<pre>' . print_r( $screen, true ) . '</pre>';
        
        if ( $screen->base != 'toplevel_page_wpcf7' && $screen->base != 'contact_page_wpcf7-new' )
            return;

        $content = $this->get_help_tab_html();

        // Add help tab
        $screen->add_help_tab( array(
            'id' => 'cf7md-help',
            'title'	=> 'Material Design',
            'content' => $content,
        ) );

    }
    
    
}

// Finally initialize code
$cf7_material_design_admin = new CF7_Material_Design_Admin();
