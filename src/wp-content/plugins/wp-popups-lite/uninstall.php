<?php
/**
 * Uninstall file. If selected all data from popups plugin will be deleted
 */
if( !defined( 'ABSPATH') && !defined('WP_UNINSTALL_PLUGIN') ) exit();

require_once plugin_dir_path( __FILE__ ) . 'src/' . 'includes/functions.php';
$uninstall = wppopups_setting( 'uninstall' );

if( isset( $uninstall) && '1' == $uninstall ) {
	// delete settings
	delete_option('wppopups_settings');
	delete_option('wppopups_preview_page');
	delete_option('wppopups_review');
	delete_option('wppopups_activated');
	// delete popups
	global $wpdb;

	$ids = $wpdb->get_results( "SELECT ID FROM $wpdb->posts WHERE post_type='wppopups'");
	if( !empty( $ids ) ) {
		foreach( $ids as $p ) {
			wp_delete_post( $p->ID, true);
		}
	}
}
