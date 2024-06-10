<?php
/*
Plugin Name: WP Retina 2x
Plugin URI: https://meowapps.com
Description: Make your website look beautiful and crisp on modern displays by creating + displaying retina images.
Version: 5.4.3
Author: Jordy Meow
Author URI: https://meowapps.com
Text Domain: wp-retina-2x
Domain Path: /languages

Dual licensed under the MIT and GPL licenses:
http://www.opensource.org/licenses/mit-license.php
http://www.gnu.org/licenses/gpl.html

Originally developed for two of my websites:
- Jordy Meow (https://offbeatjapan.org)
- Haikyo (https://haikyo.org)
*/

if ( class_exists( 'Meow_WR2X_Core' ) ) {
  function mfrh_admin_notices() {
    echo '<div class="error"><p>Thanks for installing the Pro version of WP Retina 2x :) However, the free version is still enabled. Please disable or uninstall it.</p></div>';
  }
  add_action( 'admin_notices', 'mfrh_admin_notices' );
  return;
}

global $wr2x_picturefill, $wr2x_retinajs, $wr2x_lazysizes,
	$wr2x_retina_image, $wr2x_core;

$wr2x_version = '5.4.3';
$wr2x_retinajs = '2.0.0';
$wr2x_picturefill = '3.0.2';
$wr2x_lazysizes = '4.0.4';
$wr2x_retina_image = '1.7.2';

// Admin
require( 'wr2x_admin.php');
$wr2x_admin = new Meow_WR2X_Admin( 'wr2x', __FILE__, 'wp-retina-2x' );

// Core
require( 'core.php' );
$wr2x_core = new Meow_WR2X_Core( $wr2x_admin );
$wr2x_admin->core = $wr2x_core;

/*******************************************************************************
 * TODO: OLD PRO,  THIS FUNCTION SHOULD BE REMOVED IN THE FUTURE
 ******************************************************************************/

add_action( 'admin_notices', 'wr2x_meow_old_version_admin_notices' );

function wr2x_meow_old_version_admin_notices() {
  if ( !current_user_can( 'install_plugins' ) )
    return;
	if ( isset( $_POST['wr2x_reset_sub'] ) ) {
    if ( check_admin_referer( 'wr2x_remove_expired_data' ) ) {
  		delete_transient( 'wr2x_validated' );
  		delete_option( 'wr2x_pro_serial' );
  		delete_option( 'wr2x_pro_status' );
    }
	}
	$subscr_id = get_option( 'wr2x_pro_serial', "" );
	if ( empty( $subscr_id ) )
		return;

	$forever = strpos( $subscr_id, 'F-' ) !== false;
	$yearly = strpos( $subscr_id, 'I-' ) !== false;
	if ( !$forever && !$yearly )
		return;
	?>
	<div class="error">
	<p>
		<h2>IMPORTANT MESSAGE ABOUT WP RETINA 2X</h2>
		In order to comply with WordPress.org, BIG CHANGES in the code and how the plugin was sold were to be made. The plugin needs requires to be purchased and updated through the new <a target='_blank' href="https://store.meowapps.com">Meow Apps Store</a>. This store is also more robust (keys, websites management, invoices, etc). Now, since WordPress.org only accepts free plugins on its repository, this is the one currently installed. Therefore, you need to take an action. <b>Please click here to know more about your license and to learn what to do: <a target='_blank' href='https://meowapps.com/?mkey=<?php echo $subscr_id ?>'>License <?php echo $subscr_id ?></a></b>.
	</p>
		<p>
		<form method="post" action="">
			<input type="hidden" name="wr2x_reset_sub" value="true">
      <?php wp_nonce_field( 'wr2x_remove_expired_data' ); ?>
			<input type="submit" name="submit" id="submit" class="button" value="Got it. Clear this!">
			<br /><small><b>Make sure you followed the instruction before clicking this button.</b></small>
		</form>
	</p>
	</div>
	<?php
}

?>
