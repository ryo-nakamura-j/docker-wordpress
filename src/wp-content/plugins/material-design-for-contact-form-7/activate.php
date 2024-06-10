<?php

// don't load directly
if (!defined('ABSPATH')) die('-1');

class CF7_Material_Design_Update {
    
    function __construct() {
        
		add_action( 'init', array( $this, 'update_version_switch' ) );
		add_action( 'init', array( $this, 'check_upgraded' ) );
		add_action( 'admin_notices', array( $this, 'upgraded_from_v1_admin_notice' ) );
		add_action( 'admin_notices', array( $this, 'download_premium_notice' ) );
		add_action( 'wp_ajax_cf7md_dismiss_notice', array( $this, 'dismiss_admin_notice' ) );
		add_action( 'admin_print_footer_scripts', array( $this, 'dismiss_notice_script' ) );
		add_action( 'admin_init', array( $this, 'maybe_update_plugin' ) );
        add_action( 'admin_init', array( $this, 'maybe_show_message' ) );

	}
	

	/**
	 * Dismiss notice script
	 */
	public function dismiss_notice_script() {
		?>
		<script type="text/javascript">
			jQuery(document).ready(function(){
				jQuery("[data-cf7md-dismiss-forever] .notice-dismiss").click(function(
					event
				) {
					event.preventDefault();
					var $this = jQuery(this);
					var data = {
						action: "cf7md_dismiss_notice",
						notice: $this
							.closest("[data-cf7md-dismiss-forever]")
							.data("cf7md-notice")
					};

					jQuery.post(ajaxurl, data, function(response) {
						console.log("Dismiss notice response: ", response);
					});
				});
			});
		</script>
		<?php
	}
    

    /**
     * Initialize legacy / v2 setting
     */
    public function update_version_switch() {
        $setting = get_option( 'cf7md_options[version_switch]' );
        if( CF7MD_STYLE_VERSION !== $setting ) {
            update_option( 'cf7md_options[version_switch]', CF7MD_STYLE_VERSION );
        }
	}
	

	/**
     * Check if this is an upgrade from v1
     */
    public function check_upgraded() {
        if( CF7MD_UPGRADED_FROM_V1 ) {
            update_option( 'cf7md_options[upgraded_from_v1]', true );
        }
	}
	

	/**
     * Check if this is an upgrade from v1
     */
    public function upgraded_from_v1_admin_notice() {
        if( CF7MD_UPGRADED_FROM_V1 && !get_option( 'cf7md_options[upgraded_from_v1_notice_dismissed]' ) ) {
			?>
			<div class="notice notice-info is-dismissible" data-cf7md-dismiss-forever="1" data-cf7md-notice="cf7md_options[upgraded_from_v1_notice_dismissed]">
				<p>Material Design for Contact Form 7 has had a major update. <strong>Because you've upgraded from version 1, the new features are turned OFF by default.</strong> Go to the <a href="<?php echo esc_url( menu_page_url( 'cf7md', false ) ); ?>">Material Design</a> page to activate the new features.</p>
			</div>
			<?php
        }
	}


	/**
     * Allow easier downloads for multi-site license holders
     */
    public function download_premium_notice() {
		global $cf7md_fs;
		if( $cf7md_fs->is_free_plan() && !get_option( 'cf7md_options[download_premium_notice_dismissed]' ) ) {
			?>
			<div class="notice notice-info is-dismissible" data-cf7md-dismiss-forever="1" data-cf7md-notice="cf7md_options[download_premium_notice_dismissed]">
				<p>Do you have a Material Design for Contact Form 7 premium license already? <a href="https://users.freemius.com/plugin/771/downloads" style="display: block; margin: 3px 0 0;" target="_blank">Download the latest pro version.</a></p>
			</div>
			<?php
        }
	}


	/**
     * Dismiss an admin notice forever
	 * Called via ajax with $notice set as an option to update to true
     */
    public function dismiss_admin_notice() {
		$notice = sanitize_text_field( $_POST['notice'] );
		update_option( $notice, true );
		echo 'Success';
		wp_die();
	}


    /**
     * Update routine - runs if the plugin version has changed
     */
    public function maybe_update_plugin() {

        if( CF7MD_VER !== get_option('cf7md_options[plugin_ver]') ) {
            
            // Update the version stored in options
            update_option('cf7md_options[plugin_ver]', CF7MD_VER );

            // Anything else that needs to happen on update

        }

    }


    /**
     * Update routine - runs if the plugin update message has changed
     */
    public function maybe_show_message() {

        if( CF7MD_UPDATE_MESSAGE !== get_option('cf7md_options[plugin_update_message]') ) {
            
            // Update the version stored in options
            update_option('cf7md_options[plugin_update_message]', CF7MD_UPDATE_MESSAGE );

        }

    }
    
    
}

$cf7_material_design_update = new CF7_Material_Design_Update();