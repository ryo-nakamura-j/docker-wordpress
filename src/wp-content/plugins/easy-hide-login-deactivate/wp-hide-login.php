<?php
/*
  Plugin Name: Easy Hide Login
  Plugin URI: http://ciphercoin.com/
  Description: Hide wp-login.php file and increase security of your website. No files are changed on your website.
  Author: Arshid 
  Author URI: http://ciphercoin.com/
  Text Domain: easy-hide-login
  Version: 1.0.7
*/
function easy_hide_login_activate() {
	
  add_option( 'wpseh_l01gnhdlwp','root' , '', 'yes' );

}
register_activation_hook( __FILE__, 'easy_hide_login_activate' );


 function easy_hide_login_head(){

    $EHL_slug =  get_option('wpseh_l01gnhdlwp');

    if( isset($_GET['action']) && isset($_GET['key']) ) return;

    if(isset($_GET['action']) && $_GET['action'] == 'resetpass' ) return;
    if(isset($_GET['action']) && $_GET['action'] == 'rp' ) return;

    if( isset($_POST['redirect_slug']) && $_POST['redirect_slug'] == $EHL_slug) return false;
    

    if( strpos($_SERVER['REQUEST_URI'], 'action=logout') !== false ){
      check_admin_referer( 'log-out' );

      $user = wp_get_current_user();

      wp_logout();
      wp_safe_redirect( home_url(), 302 );
      die;
    }
    
    if( ( strpos($_SERVER['REQUEST_URI'], $EHL_slug) === false  ) &&
    ( strpos($_SERVER['REQUEST_URI'], 'wp-login.php') !== false  ) ){


        wp_safe_redirect( home_url( '404' ), 302 );
      exit(); 

    }

 }
add_action( 'login_init', 'easy_hide_login_head',1); 

function easy_hide_login_hidden_field(){
    $EHL_slug = get_option('wpseh_l01gnhdlwp','');
    ?>
    	<input type="hidden" name="redirect_slug" value="<?php echo $EHL_slug ?>" />
  <?php 
}
 
 add_action('login_form', 'easy_hide_login_hidden_field');

function easy_hide_login_init(){
  $EHL_slug =  get_option('wpseh_l01gnhdlwp');
  if(parse_url($_SERVER['REQUEST_URI'],PHP_URL_QUERY) == $EHL_slug ){
       
         wp_safe_redirect(home_url("wp-login.php?$EHL_slug&redirect=false"));
     exit();

  }
}
add_action( 'init', 'easy_hide_login_init'); 



//lost password url
add_filter( 'lostpassword_url',  'easy_hide_login_lostpassword', 10, 0 );
function easy_hide_login_lostpassword() {

        $EHL_slug =  get_option('wpseh_l01gnhdlwp');
    return site_url("wp-login.php?action=lostpassword&$EHL_slug&redirect=false");
}

//logout url
//add_filter( 'logout_url', 'easy_hide_logout', 10, 2 );
function easy_hide_logout( $logout_url) {

    return home_url();
}

/* This adds the "redirect_slug" field to the password reset form and re-enables the email to be sent */
add_action('lostpassword_form', 'easy_hide_login_hidden_field');

/* This sends the user back to the login page after the password reset email has been sent. This is the same behaviour as vanilla WordPress */
function ehl_easy_hide_login_lostpassword_redirect($lostpassword_redirect) {
	$EHL_slug = get_option('wpseh_l01gnhdlwp');

	return 'wp-login.php?checkemail=confirm&redirect=false&' . $EHL_slug;
}
add_filter( 'lostpassword_redirect', 'ehl_easy_hide_login_lostpassword_redirect', 100, 1 );

/*  add menue in admin */
add_action( 'admin_menu', 'wp_hide_login_plugin_menu' );

/** Step 1. */
function wp_hide_login_plugin_menu() {
        wp_enqueue_style( 'Hide_settings_style',  plugin_dir_url( __FILE__ )  . 'style.css');
        add_options_page( 'Hide Login Options', 'Easy Hide Login', 'manage_options', 'easy-hide-login', 'wp_hide_login_plugin_options' );
}

/** Step 3. */
function wp_hide_login_plugin_options() {
        if ( !current_user_can( 'manage_options' ) )  {
                wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
        } 

   if (isset($_POST['slug'])) {

     $slug = esc_sql($_POST['slug']);
     update_option('wpseh_l01gnhdlwp',$slug);

   }

   $nonce = wp_create_nonce('easy-hide-login');
  ?>
         
 
 <div class="admin_menu">
 <h2>Easy Hide Login</h2>
 <form action="options-general.php?page=easy-hide-login&_wpnonce=<?php echo $nonce; ?>" method="POST">
 <div class="row1"><label> Slug Text :</label>
 <input type="text" value="<?php echo get_option('wpseh_l01gnhdlwp','');?>" name = "slug" class="slug">
 </div>
 <div class="row2">Login url demo:  <b>example.com?slug_text</b></div>
 <div class="row3"><input type="submit" class="submit_admin" value="Submit"></div>
 </form>
 </div>
        <h3>Like this plugin?</h3>

<p><b><a href="https://wordpress.org/plugins/easy-hide-login/" target="_blank">Give it a 5 star rating</a></b> on WordPress.org.</p>
  <?php 
}


// Add settings link on plugin page
function easy_hide_login_settings_link($links) { 
  $settings_link = '<a href="options-general.php?page=easy-hide-login">Settings</a>'; 
  array_unshift($links, $settings_link); 
  return $links; 
}
 
$plugin = plugin_basename(__FILE__); 
add_filter("plugin_action_links_$plugin", 'easy_hide_login_settings_link' );

