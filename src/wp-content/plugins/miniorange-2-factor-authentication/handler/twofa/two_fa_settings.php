<?php
include 'two_fa_pass2login.php';
include_once 'two_fa_get_details.php';
include dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.'twofa'.DIRECTORY_SEPARATOR.'two_fa_setup_notification.php';
include 'class_miniorange_2fa_strong_password.php';

class Miniorange_Authentication {

	private $defaultCustomerKey = "16555";
	private $defaultApiKey = "fFd2XcvTGDemZvbw1bcUesNJWEqKbbUq";

	function __construct() {
		add_action( 'admin_init', array( $this, 'miniorange_auth_save_settings' ) );
		add_action( 'plugins_loaded', array( $this, 'mo2f_update_db_check' ) );

		global $wp_roles;
		if ( ! isset( $wp_roles ) ) {
			$wp_roles = new WP_Roles();
		}

		if ( MoWpnsUtility::get_mo2f_db_option('mo2f_activate_plugin', 'get_option') == 1 ) {
			$mo2f_rba_attributes = new Miniorange_Rba_Attributes();
			$pass2fa_login       = new Miniorange_Password_2Factor_Login();
			$mo2f_2factor_setup  = new Two_Factor_Setup();
			add_action( 'init', array( $pass2fa_login, 'miniorange_pass2login_redirect' ) );
			//for shortcode addon
			$mo2f_ns_config = new MoWpnsUtility();
			add_action( 'login_form', array( $pass2fa_login, 'mo_2_factor_pass2login_show_wp_login_form' ),10 );

			//strong password file
			$mo2f_strong_password = new class_miniorange_2fa_strong_password();

			if($mo2f_ns_config->hasLoginCookie())
			{
				add_action('user_profile_update_errors', array( $mo2f_strong_password, 'validatePassword'), 0, 3 );
				add_action( 'woocommerce_save_account_details_errors', array( $mo2f_strong_password, 'woocommerce_password_edit_account' ),1,2 );
			}
			add_filter( 'woocommerce_process_registration_errors', array($mo2f_strong_password,'woocommerce_password_protection'),1,4);
			add_filter( 'woocommerce_registration_errors', array($mo2f_strong_password,'woocommerce_password_registration_protection'),1,3);
			add_action( 'mo2f_admin_setup_wizard_load_setup_wizard_before', [ $this, 'disable_admin_bar' ] );

			add_filter( 'mo2f_shortcode_rba_gauth', array( $mo2f_rba_attributes, 'mo2f_validate_google_auth' ), 10, 3 );
			add_filter( 'mo2f_shortcode_kba', array( $mo2f_2factor_setup, 'register_kba_details' ), 10, 7 );
			add_filter( 'mo2f_update_info', array( $mo2f_2factor_setup, 'mo2f_update_userinfo' ), 10, 5 );
			add_action( 'mo2f_shortcode_form_fields', array(
				$pass2fa_login,
				'miniorange_pass2login_form_fields'
			), 10, 5 );

			add_action( 'delete_user', array( $this, 'mo2f_delete_user' ) );

			add_filter( 'mo2f_gauth_service', array( $mo2f_rba_attributes, 'mo2f_google_auth_service' ), 10, 1 );
			if ( MoWpnsUtility::get_mo2f_db_option('mo2f_login_option', 'get_option') ) { //password + 2nd factor enabled
				if ( get_option( 'mo_2factor_admin_registration_status' ) == 'MO_2_FACTOR_CUSTOMER_REGISTERED_SUCCESS' or MO2F_IS_ONPREM) {

					remove_filter( 'authenticate', 'wp_authenticate_username_password', 20 );

					add_filter( 'authenticate', array( $pass2fa_login, 'mo2f_check_username_password' ), 99999, 4 );
					add_action( 'init', array( $pass2fa_login, 'miniorange_pass2login_redirect' ) );
					add_action( 'login_form', array(
						$pass2fa_login,
						'mo_2_factor_pass2login_show_wp_login_form'
					), 10 );

					if ( get_option( 'mo2f_remember_device' ) ) {
						add_action( 'login_footer', array( $pass2fa_login, 'miniorange_pass2login_footer_form' ) );
						add_action( 'woocommerce_before_customer_login_form', array(
							$pass2fa_login,
							'miniorange_pass2login_footer_form'
						) );
					}
					add_action( 'login_enqueue_scripts', array(
						$pass2fa_login,
						'mo_2_factor_enable_jquery_default_login'
					) );

					if(get_site_option('mo2f_woocommerce_login_prompt')){
						add_action( 'woocommerce_login_form', array(
							$pass2fa_login,
							'mo_2_factor_pass2login_show_wp_login_form'
						) );
					}
					else if(!get_site_option('mo2f_woocommerce_login_prompt') && MoWpnsUtility::get_mo2f_db_option('mo2f_enable_2fa_prompt_on_login_page', 'site_option') ) {
						add_action('woocommerce_login_form_end' ,array(
							$pass2fa_login,
							'mo_2_factor_pass2login_woocommerce'
						) );
					}
					add_action( 'wp_enqueue_scripts', array(
						$pass2fa_login,
						'mo_2_factor_enable_jquery_default_login'
					) );

					//Actions for other plugins to use miniOrange 2FA plugin
					add_action( 'miniorange_pre_authenticate_user_login', array(
						$pass2fa_login,
						'mo2f_check_username_password'
					), 1, 4 );
					add_action( 'miniorange_post_authenticate_user_login', array(
						$pass2fa_login,
						'miniorange_initiate_2nd_factor'
					), 1, 3 );
					add_action( 'miniorange_collect_attributes_for_authenticated_user', array(
						$pass2fa_login,
						'mo2f_collect_device_attributes_for_authenticated_user'
					), 1, 2 );

				}

			} else { //login with phone enabled

				if ( get_option( 'mo_2factor_admin_registration_status' ) == 'MO_2_FACTOR_CUSTOMER_REGISTERED_SUCCESS' or MO2F_IS_ONPREM) {

					$mobile_login = new Miniorange_Mobile_Login();
					add_action( 'login_form', array( $mobile_login, 'miniorange_login_form_fields' ), 99999,10 );
					add_action( 'login_footer', array( $mobile_login, 'miniorange_login_footer_form' ) );

					remove_filter( 'authenticate', 'wp_authenticate_username_password', 20 );
					add_filter( 'authenticate', array( $mobile_login, 'mo2fa_default_login' ), 99999, 3 );
					add_action( 'login_enqueue_scripts', array( $mobile_login, 'custom_login_enqueue_scripts' ) );
				}
			}
		}
	}

	function define_global() {
		global $Mo2fdbQueries;
		$Mo2fdbQueries = new Mo2fDB();
	}

	function mo2f_delete_user($user_id){
		global $Mo2fdbQueries;
		delete_user_meta($user_id,'mo2f_kba_challenge');
        delete_user_meta($user_id,'mo2f_2FA_method_to_configure');
        delete_user_meta($user_id,'Security Questions');
		delete_user_meta($user_id,'mo2f_chat_id');
		$Mo2fdbQueries->delete_user_details( $user_id);
        delete_user_meta($user_id,'mo2f_2FA_method_to_test');
	}

	function mo2f_update_db_check() {

		$userid = wp_get_current_user()->ID;
		add_option('mo2f_onprem_admin' ,  $userid );
		if(is_multisite()){
			add_site_option('mo2fa_superadmin',1);
		}
		// Deciding on On-Premise solution
		$is_NC=MoWpnsUtility::get_mo2f_db_option('mo2f_is_NC', 'get_option');
		$is_NNC=MoWpnsUtility::get_mo2f_db_option('mo2f_is_NNC', 'get_option');
		// Old users 
		if ( get_option( 'mo2f_customerKey' ) && ! $is_NC ) 
			add_option( 'is_onprem', 0 );
		
		//new users using cloud  
		if(get_option( 'mo2f_customerKey' ) && $is_NC && $is_NNC)
			add_option( 'is_onprem', 0 ); 
		
		if(get_option( 'mo2f_app_secret' ) && $is_NC && $is_NNC){
			add_option( 'is_onprem', 0 ); 
		}else{
			add_option( 'is_onprem', 1 ); 

		}
		if(get_option('mo2f_network_features',"not_exits")=="not_exits"){
			do_action('mo2f_network_create_db');
			update_option('mo2f_network_features',1);
		}
		if(get_option('mo2f_encryption_key',"not_exits")=="not_exits"){
			$get_encryption_key = MO2f_Utility::random_str(16);
			update_option('mo2f_encryption_key',$get_encryption_key);

		}
		global $Mo2fdbQueries;
		$user_id = get_option( 'mo2f_miniorange_admin' );
		$current_db_version = get_option( 'mo2f_dbversion' );
		
		if ( $current_db_version < MoWpnsConstants::DB_VERSION ) {
			update_option( 'mo2f_dbversion', MoWpnsConstants::DB_VERSION );
			$Mo2fdbQueries->generate_tables();
			
		}
		if(MO2F_IS_ONPREM){
			$twofactordb = new Mo2fDB;
			$userSync = get_site_option('mo2f_user_sync');
			if($userSync<1){
				update_site_option('mo2f_user_sync',1);
				$twofactordb->get_all_onprem_userids();
			}
		}

		if ( ! get_option( 'mo2f_existing_user_values_updated' ) ) {

			if ( get_option( 'mo2f_customerKey' ) && ! MoWpnsUtility::get_mo2f_db_option('mo2f_is_NC', 'get_option')) {
				update_option( 'mo2f_is_NC', 0 );
			}

			$check_if_user_column_exists = false;

			if ( $user_id && ! MoWpnsUtility::get_mo2f_db_option('mo2f_is_NC', 'get_option') ) {
				$does_table_exist = $Mo2fdbQueries->check_if_table_exists();
				if ( $does_table_exist ) {
					$check_if_user_column_exists = $Mo2fdbQueries->check_if_user_column_exists( $user_id );
				}
				if ( ! $check_if_user_column_exists ) {
					$Mo2fdbQueries->generate_tables();
					$Mo2fdbQueries->insert_user( $user_id, array( 'user_id' => $user_id ) );

					add_option( 'mo2f_phone', get_option( 'user_phone' ) );
					add_option( 'mo2f_enable_login_with_2nd_factor', get_option( 'mo2f_show_loginwith_phone' ) );
					add_option( 'mo2f_remember_device', get_option( 'mo2f_deviceid_enabled' ) );
					add_option( 'mo2f_transactionId', get_option( 'mo2f-login-transactionId' ) );
					add_option( 'mo2f_is_NC', 0 );
					$phone      = get_user_meta( $user_id, 'mo2f_user_phone', true );
					$user_phone = $phone ? $phone : get_user_meta( $user_id, 'mo2f_phone', true );

					$Mo2fdbQueries->update_user_details( $user_id,
						array(
							'mo2f_GoogleAuthenticator_config_status' => get_user_meta( $user_id, 'mo2f_google_authentication_status', true ),
							'mo2f_SecurityQuestions_config_status'   => get_user_meta( $user_id, 'mo2f_kba_registration_status', true ),
							'mo2f_EmailVerification_config_status'   => true,
							'mo2f_AuthyAuthenticator_config_status'  => get_user_meta( $user_id, 'mo2f_authy_authentication_status', true ),
							'mo2f_user_email'                        => get_user_meta( $user_id, 'mo_2factor_map_id_with_email', true ),
							'mo2f_user_phone'                        => $user_phone,
							'user_registration_with_miniorange'      => get_user_meta( $user_id, 'mo_2factor_user_registration_with_miniorange', true ),
							'mobile_registration_status'             => get_user_meta( $user_id, 'mo2f_mobile_registration_status', true ),
							'mo2f_configured_2FA_method'             => get_user_meta( $user_id, 'mo2f_selected_2factor_method', true ),
							'mo_2factor_user_registration_status'    => get_user_meta( $user_id, 'mo_2factor_user_registration_status', true )
						) );

					if ( get_user_meta( $user_id, 'mo2f_mobile_registration_status', true ) ) {
						$Mo2fdbQueries->update_user_details( $user_id,
							array(
								'mo2f_miniOrangeSoftToken_config_status'            => true,
								'mo2f_miniOrangeQRCodeAuthentication_config_status' => true,
								'mo2f_miniOrangePushNotification_config_status'     => true
							) );
					}

					if ( get_user_meta( $user_id, 'mo2f_otp_registration_status', true ) ) {
						$Mo2fdbQueries->update_user_details( $user_id,
							array(
								'mo2f_OTPOverSMS_config_status' => true
							) );
					}

					$mo2f_external_app_type = get_user_meta( $user_id, 'mo2f_external_app_type', true ) == 'AUTHY 2-FACTOR AUTHENTICATION' ?
					'Authy Authenticator' : 'Google Authenticator';

					update_user_meta( $user_id, 'mo2f_external_app_type', $mo2f_external_app_type );

					delete_option( 'mo2f_show_loginwith_phone' );
					delete_option( 'mo2f_deviceid_enabled' );
					delete_option( 'mo2f-login-transactionId' );
					delete_user_meta( $user_id, 'mo2f_google_authentication_status' );
					delete_user_meta( $user_id, 'mo2f_kba_registration_status' );
					delete_user_meta( $user_id, 'mo2f_email_verification_status' );
					delete_user_meta( $user_id, 'mo2f_authy_authentication_status' );
					delete_user_meta( $user_id, 'mo_2factor_map_id_with_email' );
					delete_user_meta( $user_id, 'mo_2factor_user_registration_with_miniorange' );
					delete_user_meta( $user_id, 'mo2f_mobile_registration_status' );
					delete_user_meta( $user_id, 'mo2f_otp_registration_status' );
					delete_user_meta( $user_id, 'mo2f_selected_2factor_method' );
					delete_user_meta( $user_id, 'mo2f_configure_test_option' );
					delete_user_meta( $user_id, 'mo_2factor_user_registration_status' );

					update_option( 'mo2f_existing_user_values_updated', 1 );

				}
			}
		}

		if ( $user_id && ! get_option( 'mo2f_login_option_updated' ) ) {

			$does_table_exist = $Mo2fdbQueries->check_if_table_exists();
			if ( $does_table_exist ) {
				$check_if_user_column_exists = $Mo2fdbQueries->check_if_user_column_exists( $user_id );
				if ( $check_if_user_column_exists ) {
					$selected_2FA_method        = $Mo2fdbQueries->get_user_detail( 'mo2f_configured_2FA_method', $user_id );

					update_option( 'mo2f_login_option_updated', 1 );
				}
			}

		}

		
	}
	

	function feedback_request() {
		display_feedback_form();
	}
	public function disable_admin_bar() {

		global $wp_admin_bar;
		$wp_admin_bar = ''; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
	}
	function get_customer_SMS_transactions() {

		if ( get_option( 'mo_2factor_admin_registration_status' ) == 'MO_2_FACTOR_CUSTOMER_REGISTERED_SUCCESS' && MoWpnsUtility::get_mo2f_db_option('mo2f_show_sms_transaction_message', 'get_option') ) {
			if ( ! MoWpnsUtility::get_mo2f_db_option('mo2f_set_transactions', 'get_option') ) {
				$customer = new Customer_Setup();

				$content = json_decode( $customer->get_customer_transactions( get_option( 'mo2f_customerKey' ), get_option( 'mo2f_api_key' ),get_site_option('mo2f_license_type') ), true );

				update_option( 'mo2f_set_transactions', 1 );
				if ( ! array_key_exists( 'smsRemaining', $content ) ) {
					$smsRemaining = 0;
				} else {
					$smsRemaining = $content['smsRemaining'];

					if ( $smsRemaining == null ) {
						$smsRemaining = 0;
					}
				}
				update_option( 'mo2f_number_of_transactions', $smsRemaining );
			} else {
				$smsRemaining = MoWpnsUtility::get_mo2f_db_option('mo2f_number_of_transactions', 'get_option');
			}

			$this->display_customer_transactions( $smsRemaining );
		}
	}

	function display_customer_transactions( $content ) {
		echo '<div class="is-dismissible notice notice-warning"> <form name="f" method="post" action=""><input type="hidden" name="option" value="mo_auth_sync_sms_transactions" /><p><b>' . mo2f_lt( 'miniOrange 2-Factor Plugin:' ) . '</b> ' . mo2f_lt( 'You have' ) . ' <b style="color:red">' . esc_html($content) . ' ' . mo2f_lt( 'SMS transactions' ) . ' </b>' . mo2f_lt( 'remaining' ) . '<input type="submit" name="submit" value="' . mo2f_lt( 'Check Transactions' ) . ' " class="button button-primary button-large" /></form><button type="button" class="notice-dismiss"><span class="screen-reader-text">' . mo2f_lt( 'Dismiss this notice.' ) . '</span></button></div>';
	}

	function prompt_user_to_setup_two_factor() {
		global $Mo2fdbQueries;
		$user                     = wp_get_current_user();
		$selected_2_Factor_method = $Mo2fdbQueries->get_user_detail( 'mo2f_configured_2FA_method', $user->ID );
		if ( $selected_2_Factor_method == 'NONE' ) {
			if ( MoWpnsUtility::get_mo2f_db_option('mo2f_enable_2fa_for_users', 'get_option') || ( current_user_can( 'manage_options' ) && get_option( 'mo2f_miniorange_admin' ) == $user->ID ) ) {
				echo '<div class="is-dismissible notice notice-warning"><p><b>' . mo2f_lt( "miniOrange 2-Factor Plugin: " ) . '</b>' . mo2f_lt( 'You have not configured your 2-factor authentication method yet.' ) .
				'<a href="admin.php?page=mo_2fa_two_fa">' . mo2f_lt( ' Click here' ) . '</a>' . mo2f_lt( ' to set it up.' ) .
				'<button type="button" class="notice-dismiss"><span class="screen-reader-text">' . mo2f_lt( 'Dismiss this notice.' ) . '</span></button></div>';
			}
		}
	}


	function mo_auth_success_message() {
		$message = get_option( 'mo2f_message' ); ?>
		<script>
			jQuery(document).ready(function () {
				var message = "<?php echo esc_html($message); ?>";
				jQuery('#messages').append("<div  style='padding:5px;'><div class='error notice is-dismissible mo2f_error_container' style='position: fixed;left: 60.4%;top: 6%;width: 37%;z-index: 99999;background-color: bisque;font-weight: bold;'> <p class='mo2f_msgs'>" + message + "</p></div></div>");
			});
		</script>
		<?php
	}

	function mo_auth_error_message() {
		$message = get_option( 'mo2f_message' ); ?>

		<script>
			jQuery(document).ready(function () {
				var message = "<?php echo esc_html($message); ?>";
				jQuery('#messages').append("<div  style='padding:5px;'><div class='updated notice is-dismissible mo2f_success_container' style='position: fixed;left: 60.4%;top: 6%;width: 37%;z-index: 9999;background-color: #bcffb4;font-weight: bold;'> <p class='mo2f_msgs'>" + message + "</p></div></div>");
			});
		</script>
		<?php

	}

	function miniorange_auth_menu() {
		global $user;
		$user 			 = wp_get_current_user();
		$roles           = $user->roles;
		$miniorange_role = array_shift( $roles );

		$is_plugin_activated             = MoWpnsUtility::get_mo2f_db_option('mo2f_activate_plugin', 'get_option');
		$is_customer_admin               = get_option( 'mo2f_miniorange_admin' ) == $user->ID ? true : false;
		$is_2fa_enabled_for_users        = MoWpnsUtility::get_mo2f_db_option('mo2f_enable_2fa_for_users', 'get_option');
		$can_current_user_manage_options = current_user_can( 'manage_options' );
		$admin_registration_status       = get_option( 'mo_2factor_admin_registration_status' ) == 'MO_2_FACTOR_CUSTOMER_REGISTERED_SUCCESS'
		? true : false;

		if(MO2F_IS_ONPREM)
		{
			$can_current_user_manage_options = true;	// changes by prdp
			$is_customer_admin				 = true;
		}
		if ( $admin_registration_status ) {
			if ( $can_current_user_manage_options && $is_customer_admin ) {
				$mo2fa_hook_page = $this->hookpages();
			}
		} else if ( $can_current_user_manage_options ) {
			$mo2fa_hook_page = $this->hookpages();
		}


	}

	function hookpages() {
		$url = explode('handler',plugin_dir_url(__FILE__));
		if(get_site_option('mo2f_enable_custom_icon')!=1)
			$iconurl = $url[0] . '/includes/images/miniorange_icon.png';
		else
			$iconurl = site_url(). '/wp-content/uploads/miniorange/plugin_icon.png';
		$menu_slug = 'miniOrange_2_factor_settings';
		add_menu_page( 'miniOrange 2 Factor Auth', MoWpnsUtility::get_mo2f_db_option('mo2f_custom_plugin_name', 'get_option'), 'read', $menu_slug, array($this,'mo_auth_login_options'), $iconurl );			
	}

	function mo_auth_login_options() {
		global $user;
		$user = wp_get_current_user();
		update_option( 'mo2f_host_name', 'https://login.xecurify.com' );
		mo_2_factor_register( $user );
	}

	function mo_2_factor_enable_frontend_style() {
		wp_enqueue_style( 'mo2f_frontend_login_style', plugins_url( 'includes/css/front_end_login.css', __FILE__ ),[],MO2F_VERSION );
		wp_enqueue_style( 'bootstrap_style', plugins_url( 'includes/css/bootstrap.min.css', __FILE__ ),[],MO2F_VERSION );
		wp_enqueue_style( 'mo_2_factor_admin_settings_phone_style', plugins_url( 'includes/css/phone.css', __FILE__ ),[],MO2F_VERSION );
		wp_enqueue_style( 'mo_2_factor_wpb-fa', plugins_url( 'includes/css/font-awesome.min.css', __FILE__ ),[],MO2F_VERSION );
		wp_enqueue_style( 'mo2f_login_popup_style', plugins_url( "includes/css/mo2f_login_popup_ui.css", __FILE__ ),[],MO2F_VERSION );
	}

	function plugin_settings_style( $mo2fa_hook_page ) {

		if ( 'toplevel_page_miniOrange_2_factor_settings' != $mo2fa_hook_page ) {
			return;
		}

		wp_enqueue_style( 'mo_2_factor_admin_settings_style', plugins_url( 'includes/css/style_settings.css', __FILE__ ),[],MO2F_VERSION );
		wp_enqueue_style( 'mo_2_factor_admin_settings_phone_style', plugins_url( 'includes/css/phone.css', __FILE__ ),[],MO2F_VERSION );
		wp_enqueue_style( 'bootstrap_style', plugins_url( 'includes/css/bootstrap.min.css', __FILE__ ),[],MO2F_VERSION );
		wp_enqueue_style( 'bootstrap_style_ass', plugins_url( 'includes/css/bootstrap-tour-standalone.css', __FILE__ ),[],MO2F_VERSION );
		wp_enqueue_style( 'mo_2_factor_wpb-fa', plugins_url( 'includes/css/font-awesome.min.css', __FILE__ ),[],MO2F_VERSION );
		wp_enqueue_style( 'mo2f_ns_admin_settings_datatable_style', plugins_url('includes/css/jquery.dataTables.min.css', __FILE__),[],MO2F_VERSION);
	}

	function plugin_settings_script( $mo2fa_hook_page ) {
		if ( 'toplevel_page_miniOrange_2_factor_settings' != $mo2fa_hook_page ) {
			return;
		}
		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'mo_2_factor_admin_settings_phone_script', plugins_url( 'includes/js/phone.js', __FILE__ ),[],MO2F_VERSION );
		wp_enqueue_script( 'bootstrap_script', plugins_url( 'includes/js/bootstrap.min.js', __FILE__ ),[],MO2F_VERSION );
		wp_enqueue_script( 'bootstrap_script_hehe', plugins_url( 'includes/js/bootstrap-tour-standalone.min.js', __FILE__ ),[],MO2F_VERSION );
		wp_enqueue_script( 'mo2f_ns_admin_datatable_script', plugins_url('includes/js/jquery.dataTables.min.js', __FILE__ ), array('jquery'),MO2F_VERSION);

	}
	public function mo2f_setup_wizard_footer() {
		?>
		<?php wp_print_scripts( 'mo2f-setup-vue-script' ); ?>
		</body>
		</html>
		<?php
	}
	public function mo2f_setup_wizard_header() {
		?>
		<!DOCTYPE html>
		<html <?php language_attributes(); ?>>
		<head>
			<meta name="viewport" content="width=device-width"/>
			<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
			<title><?php esc_html_e( 'miniOrange 2-factor Setup Wizard', 'miniorange 2-factor-authentication' ); ?></title>
			<?php do_action( 'admin_print_styles' ); ?>
			<?php do_action( 'admin_print_scripts' ); ?>
			<?php do_action( 'admin_head' ); ?>
		</head>
		<body class="mo2f_setup_wizard">
		<?php
	}

	/**
	 * Outputs the content of the current step.
	 *
	 * @since 2.6.0
	 */

private function settings_error_page( $id = 'mo2f-setup-vue-site-settings', $footer = '' ) {
		
		wp_register_script('mo2f_qr_code_js',plugins_url( '/includes/jquery-qrcode/jquery-qrcode.js', 	 dirname(dirname(__FILE__ ))),[],MO2F_VERSION);
		wp_register_script('mo2f_qr_code_minjs',plugins_url( '/includes/jquery-qrcode/jquery-qrcode.min.js', dirname(dirname(__FILE__ ))),[],MO2F_VERSION);
		wp_register_script('mo2f_phone_js',plugins_url( '/includes/js/phone.js', 						 dirname(dirname(__FILE__ ))),[],MO2F_VERSION);
		wp_register_style( 'mo_2fa_admin_setupWizard'		, plugins_url('includes/css/setup-wizard.css', dirname(dirname(__FILE__)) ) ,[],MO2F_VERSION);
		wp_register_style('mo2f_phone_css',plugins_url('includes/css/phone.css', dirname(dirname(__FILE__))),[],MO2F_VERSION);
		$contact_url = 'https://wordpress.org/plugins/miniorange-2-factor-authentication/';
		echo '<head>';
			wp_print_scripts( 'mo2f_qr_code_js' );
			wp_print_scripts( 'mo2f_qr_code_minjs' );
			wp_print_scripts( 'mo2f_phone_js' );
			wp_print_styles( 'mo2f_phone_css' );
			wp_print_styles('mo_2fa_admin_setupWizard');
        echo '</head>';
		
		?>
			<div class="mo2f_loader" id="mo2f_loader" style="display: none;"></div>
					
			<div id="mo2f-setup-wizard-settings-area"  class="mo2f-setup-wizard-settings-area wpms-container">
				<header class="mo2f-setup-wizard-header">

							<img width="70px" height="auto" src="<?php echo plugin_dir_url(dirname(dirname(__FILE__))) . 'includes/images/miniorange-new-logo.png'; ?>" alt="<?php esc_attr_e( 'miniOrange 2-factor Logo', 'miniorange-2-factor-authentication' ); ?>" >
							<h1> miniOrange 2-factor authentication Setup</h1>

				</header>
				<div id="mo2f-setup-settings-error-loading-area-container">
					<div id="mo2f-setup-settings-error-loading-area">
						<div>
							<div id="mo2f-setup-error-js">
								<p class="subtitle" style="text-align:center;" > This setup guide will take you through all the steps you need to follow to enable the two-factor authentication for your website.</p>
								
								<br><br>
								<button type="button" style="text-align:center;display: flex;margin: auto;" class="mo2f-setup-button mo2f-setup-button-main mo2f-setup-button-large" id ='mo2f_get_started' target="_blank" class="button" rel="noopener noreferrer"> <?php esc_html_e("Let's Get Started", 'mo2f-setup'); ?></button>
								
								<br><br>
								<div style="text-align:center;display: flex;margin: auto;flex-direction: column;">
									<a href="<?php echo esc_url( $contact_url ); ?>" target="_blank" rel="noopener noreferrer">
										<?php esc_html_e( 'Facing issues? Contact Us', 'mo2f-setup' ); ?>
									</a>
								</div>
							</div>
						</div>
					</div>
					<div class="mo2f-setup-error-footer">
						<?php echo wp_kses_post( $footer ); ?>
					</div>
				</div>
				<div id = "mo2f_methods_setup_wizard">
					<div class="mo2f-setup-wizard-timeline">
						<div class="mo2f-setup-wizard-timeline-step mo2f-setup-wizard-timeline-step-active" id="mo2f-setup-wizard-step1"></div>
						<div class="mo2f-setup-wizard-timeline-step-line" id="mo2f-setup-wizard-line1"></div>
						<div class="mo2f-setup-wizard-timeline-step" id="mo2f-setup-wizard-step2"> </div>
						<div class="mo2f-setup-wizard-timeline-step-line" id="mo2f-setup-wizard-line2"></div>
						<div class="mo2f-setup-wizard-timeline-step"id="mo2f-setup-wizard-step3"> </div>
						<div class="mo2f-setup-wizard-timeline-step-line" id="mo2f-setup-wizard-line3"></div>
						<div class="mo2f-setup-wizard-timeline-step" id="mo2f-setup-wizard-step4"> </div>
						
					</div>
					<div id="mo2f-setup-settings-error-loading-area1" class="mo2f-setup-content">
					
					<p class="mo2f-step-show"> Step 1 of 4</p>
					<h3 > Select the Authentication method you want to configure </h3>
					<div class="mo2f-input-radios-with-icons">

				
					<label title="<?php echo __('You have to enter 6 digits code generated by google Authenticator App to login. Supported in Smartphones only.', 'miniorange-2-factor-authentication'); ?>">
                        <input type="radio"  name="mo2f_selected_2factor_method" class="mo2f-styled-radio"  value="Google Authenticator"  />
                   		<span class="mo2f-styled-radio-text"> Google / Microsoft / Authy Authenticator</span>
                    </label>

					<label title="<?php echo __('You will receive a one time passcode via SMS on your phone. You have to enter the otp on your screen to login. Supported in Smartphones, Feature Phones.', 'miniorange-2-factor-authentication'); ?>">
                        <input type="radio"  name="mo2f_selected_2factor_method" class="mo2f-styled-radio"  value="OTP Over SMS"  />
                        <span class="mo2f-styled-radio-text">
                        <?php echo __('SMS verification (Registration Required)', 'miniorange-2-factor-authentication'); ?>
                    </span></label>

					<label title="<?php echo __('You will receive a one time passcode on your email. You have to enter the otp on your screen to login. Supported in Smartphones, Feature Phones.', 'miniorange-2-factor-authentication'); ?>" >
                        <input type="radio"  name="mo2f_selected_2factor_method" class="mo2f-styled-radio"  value="OTP Over Email"  />
                        <span class="mo2f-styled-radio-text">
                        <?php echo __('Email verification', 'miniorange-2-factor-authentication'); ?>
                    </span>
                    </label>
					

					<label title="<?php echo __('You have to answers some knowledge based security questions which are only known to you to authenticate yourself. Supported in Desktops,Laptops,Smartphones.', 'miniorange-2-factor-authentication'); ?>" >
			            <input type="radio"  name="mo2f_selected_2factor_method" class="mo2f-styled-radio" value="Security Questions"  />
                        <span class="mo2f-styled-radio-text">
			                            <?php echo __('Security Questions ( KBA )', 'miniorange-2-factor-authentication'); ?>
                    </span>
                    </label>

					<label title="<?php echo __('You will get an OTP on your TELEGRAM app from miniOrange Bot.', 'miniorange-2-factor-authentication'); ?>" >
                        <input type="radio"  name="mo2f_selected_2factor_method" class="mo2f-styled-radio" value="OTP Over Telegram"  />
                        <span class="mo2f-styled-radio-text">
                        <?php echo __('2FA via Telegram', 'miniorange-2-factor-authentication'); ?>
                    </span>
                    </label>

				    </div>             
					<div class="mo2f-setup-wizard-step-footer" style="display: flex;">
						<div style="margin: 0px;width:30%">
			</div>
					<div class="mo2f-setup-actions" style="margin: 0px;width:30%">
							<input type="button" name="mo2f_next_step1" id="mo2f_next_step1" class="button button-primary"  value="Save and Continue" />  
                  		  
                  	</div>
					<div style="width: 40%;text-align: end;">
					 <a href="#skiptwofactor1" style=""><?php echo __('Skip Setup', 'miniorange-2-factor-authentication'); ?></a>
					 </div>
                  </div> 
                  </div> 

                 



                <div id="mo2f-setup-settings-error-loading-area2" style="display: none;" class="mo2f-setup-content">
	                <p class="mo2f-step-show"> Step 2 of 4</p>
					
	                <h3> Register with miniOrange </h3>
					
					<form name="f" id="mo2f_registration_form" method="post" action="">
						<input type="hidden" name="option" value="mo_wpns_register_customer" />
						<div class="mo2f_table_layout">
						<div style="margin-bottom:30px;">
								<div class="overlay_error" style="display: none;" id= "mo2f_Error_block">
								<p class ="popup_text" id="mo2f_Error_message" style="color: red;" >Seems like email is already registered. Please click on 'Already have an account'</p></div>
								<p> Please enter a valid email id that you have access to and select a password</p>
								<table class="mo_wpns_settings_table mo2f_width_80">
									<tr>
										<td><b><font color="#FF0000">*</font>Email:</b></td>
										<td><input style="padding: 4px;" class="mo_wpns_table_textbox" type="text" pattern="[^@\s]+@[^@\s]+\.[^@\s]+" id="mo2f_email" name="email" 
											required placeholder="person@example.com" /></td>
									</tr>

									<tr>
										<td><b><font color="#FF0000">*</font>Password:</b></td>
										<td><input style="padding: 4px;" class="mo_wpns_table_textbox" required id= "mo2f_password" type="password"
											name="password" placeholder="Choose your password (Min. length 6)" /></td>
									</tr>
									<tr>
										<td><b><font color="#FF0000">*</font>Confirm Password:</b></td>
										<td><input style="padding: 4px;" class="mo_wpns_table_textbox" id= "mo2f_confirmPassword" required type="password"
											name="confirmPassword" placeholder="Confirm your password" /></td>
									</tr>
									<tr>
										<td>&nbsp;</td>
										<td><br>
											<a href="#mo2f_account_exist">Already have an account?</a>

									</tr>
								</table>
						</div>	
						</div>
					</form>
					<form name="f" id="mo2f_login_form" style="display: none;" method="post" action="">
						<input type="hidden" name="option" value="mo_wpns_verify_customer" />
						<div class="mo2f_table_layout">
						<div style="margin-bottom:30px;">
								<div class="overlay_error" style="display: none;" id= "mo2f_Error_block">
								<p class ="popup_text" id="mo2f_Error_message" style="color: red;" >Invalid Credentials</p></div>
								
								<p>Please enter your miniOrange email and password. <a target="_blank" href="https://login.xecurify.com/moas/idp/resetpassword"> Click here if you forgot your password?</a></p>
								<table class="mo_wpns_settings_table mo2f_width_80">
									<tr>
										<td><b><font color="#FF0000">*</font>Email:</b></td>
										<td><input  style="padding: 4px;" class="mo_wpns_table_textbox" type="email" id="mo2f_email_login" autofocus="true" name="email"
											required placeholder="person@example.com" /></td>
									</tr>
									<tr>
										<td><b><font color="#FF0000">*</font>Password:</b></td>
										<td><input style="padding: 4px;" class="mo_wpns_table_textbox" required  id= "mo2f_password_login" type="password"
											name="password" placeholder="Enter your miniOrange password" /></td>
									</tr>
									<tr>
										<td>&nbsp;</td>
										<td><br>
											<a href="#mo2f_register_new_account">Go Back to Registration Page</a>

									</tr>
									
								</table>
						</div>	
						</div>
					</form>
		

	              	<div class="mo2f-setup-wizard-step-footer">
					  <div style="margin: 0px;width:30%">
		              	<a href="#previousStep2"><span class="text-with-arrow text-with-arrow-left"><svg viewBox="0 0 448 512" role="img" class="icon" data-icon="long-arrow-alt-left" data-prefix="far" focusable="false" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="16" height="18"><path xmlns="http://www.w3.org/2000/svg" fill="currentColor" d="M107.515 150.971L8.485 250c-4.686 4.686-4.686 12.284 0 16.971L107.515 366c7.56 7.56 20.485 2.206 20.485-8.485v-71.03h308c6.627 0 12-5.373 12-12v-32c0-6.627-5.373-12-12-12H128v-71.03c0-10.69-12.926-16.044-20.485-8.484z"></path></svg>  Previous Step </span></a>
						</div>
		              	<div class="mo2f-setup-actions mo2f-setup-wizard-step-footer-buttons">
		              		<input type="button" name="mo2f_next_step2" id="mo2f_next_step2" class="button button-primary"  value="Create Account and continue" />
		              		  
	              		</div>
						  <div style="width: 40%;text-align: end;">
					 <a href="#skiptwofactor2" style=""><?php echo __('Skip Setup', 'miniorange-2-factor-authentication'); ?></a>
					 </div>
	                </div> 
                 

                  </div>

                 <div id="mo2f-setup-settings-error-loading-area3" style="display: none;" class="mo2f-setup-content">
                 <p class="mo2f-step-show"> Step 3 of 4</p>
				
				 <h3 style="text-align:center;" id="mo2f_setup_method_title"> Configure 2-factor authentication </h3>

				 <div class="overlay_success" style="display: none;" id= "mo2f_success_block_configuration">
				 <p class ="popup_text" id="mo2f_configure_success_message" >An OTP has been sent to the below email.</p>
				 <br><br></div>
				
				 <div class="overlay_error" style="display: none;" id= "mo2f_Error_block_configuration">
				 <p class ="popup_text" id="mo2f_configure_Error_message" style="color: red;" >Invalid OTP</p>
				</div>
						<div id="mo2f_main_content"> </div>


	              	<div class="mo2f-setup-wizard-step-footer">
					  <div style="margin: 0px;width:30%">
		              	<a href="#previousStep3"><span class="text-with-arrow text-with-arrow-left"><svg viewBox="0 0 448 512" role="img" class="icon" data-icon="long-arrow-alt-left" data-prefix="far" focusable="false" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="16" height="18"><path xmlns="http://www.w3.org/2000/svg" fill="currentColor" d="M107.515 150.971L8.485 250c-4.686 4.686-4.686 12.284 0 16.971L107.515 366c7.56 7.56 20.485 2.206 20.485-8.485v-71.03h308c6.627 0 12-5.373 12-12v-32c0-6.627-5.373-12-12-12H128v-71.03c0-10.69-12.926-16.044-20.485-8.484z"></path></svg>  Previous Step </span></a>
					</div>
		              	<div class="mo2f-setup-actions" style="margin: 0px;width:30%">
		              		<input type="button" name="mo2f_next_step3" id="mo2f_next_step3" class="button button-primary"  value="Save and Continue" />
		              		  
	              	</div>
					  <div style="width: 40%;text-align: end;">
					 <a href="#skiptwofactor3" style=""><?php echo __('Skip Setup', 'miniorange-2-factor-authentication'); ?></a>
					 </div>
                </div> 
                 

                </div>

                 <div id="mo2f-setup-settings-error-loading-area4" style="display: none;" class="mo2f-setup-content">
                 <p class="mo2f-step-show"> Step 4 of 4</p>
					 <div style="text-align: center;">	
					 <h3 style="text-align:center;font-size: xx-large;"> Congratulations! </h3>
					 <br>
							You have successfully configured the two-factor authentication. 
							<br><br><br>
							<input type="button" name="mo2f_next_step4" id="mo2f_next_step4" class="mo2f-modal__btn button button-primary"  value="Advance Settings" />

	                 </div> 
                 </div>

                 </div>


				</div>
			</div>
		


		<script type="text/javascript">

           var selected_2FA_method = '';
            var ele = document.getElementsByName('mo2f_selected_2factor_method');
            for(i = 0; i < ele.length; i++) {
                if(ele[i].checked)
                    selected_2FA_method = ele[i].value;
            }
            jQuery("#mo2f_setup_method_title").text(selected_2FA_method);

			jQuery('#mo2f_next_step4').click(function(e){
				localStorage.setItem("last_tab", 'unlimittedUser_2fa');
				window.location.href = '<?php echo (admin_url()."admin.php?page=mo_2fa_two_fa"); ?>'; 
			});

			
		
			jQuery('#mo2f-setup-settings-error-loading-area-container').css('display', 'none');
			jQuery("#mo2f_get_started").click(function(e){
				jQuery('#mo2f-setup-settings-error-loading-area-container').css('display', 'none');
				jQuery('#mo2f_methods_setup_wizard').css('display', 'block');
				
			});

			jQuery('a[href="#previousStep3"]').click(function(e){
				document.getElementById('mo2f_success_block_configuration').style.display = "none";
		        document.getElementById('mo2f_Error_block_configuration').style.display = "none";
		        	
				var selected_2FA_method = '';
				var ele = document.getElementsByName('mo2f_selected_2factor_method');
				for(i = 0; i < ele.length; i++) {
                	if(ele[i].checked)
            			selected_2FA_method = ele[i].value;
            	}
            	if(selected_2FA_method =='OTP Over SMS')
            	{
	            	document.getElementById('mo2f-setup-settings-error-loading-area3').style.display  = "none";
	            	document.getElementById('mo2f-setup-settings-error-loading-area2').style.display  = "block";
	            	var lineElement = document.getElementById("mo2f-setup-wizard-line2");
					lineElement.classList.remove("mo2f-setup-wizard-timeline-line-active");
					var stepElement = document.getElementById("mo2f-setup-wizard-step3");
					stepElement.classList.remove("mo2f-setup-wizard-timeline-step-active");
            	}
            	else
            	{
            		var lineElement = document.getElementById("mo2f-setup-wizard-line2");
					lineElement.classList.remove("mo2f-setup-wizard-timeline-line-active");
					var stepElement = document.getElementById("mo2f-setup-wizard-step3");
					stepElement.classList.remove("mo2f-setup-wizard-timeline-step-active");
            		var lineElement = document.getElementById("mo2f-setup-wizard-line1");
					lineElement.classList.remove("mo2f-setup-wizard-timeline-line-active");
					var stepElement = document.getElementById("mo2f-setup-wizard-step2");
					stepElement.classList.remove("mo2f-setup-wizard-timeline-step-active");
					document.getElementById('mo2f-setup-settings-error-loading-area3').style.display  = "none";
            		document.getElementById('mo2f-setup-settings-error-loading-area1').style.display  = "block";
					jQuery("#mo2f_next_step1").focus();
            	
            	}
			}); 

			jQuery('a[href="#previousStep2"]').click(function(e){
				document.getElementById('mo2f-setup-settings-error-loading-area2').style.display  = "none";
            	document.getElementById('mo2f-setup-settings-error-loading-area1').style.display  = "block";
            	var lineElement = document.getElementById("mo2f-setup-wizard-line1");
				lineElement.classList.remove("mo2f-setup-wizard-timeline-line-active");
				var stepElement = document.getElementById("mo2f-setup-wizard-step2");
				stepElement.classList.remove("mo2f-setup-wizard-timeline-step-active");
            	
			}); 

			jQuery('a[href="#previousStep1"]').click(function(e){
				jQuery('#mo2f-setup-settings-error-loading-area-container').css('display', 'block');
				jQuery('#mo2f_methods_setup_wizard').css('display', 'none');
				
			}); 

			jQuery('a[href=\"#mo2f_account_exist\"]').click(function (e) {
	            document.getElementById('mo2f_registration_form').style.display = "none";
	            document.getElementById('mo2f_login_form').style.display = "block";
				    var input = jQuery("#mo2f_password_login");
                    var len = input.val().length;
                    input[0].focus();
                    input[0].setSelectionRange(len, len);
				jQuery("#mo2f_password_login" ).keypress(function(e){
							            if (e.which == 13) {	
		                                         e.preventDefault();
		                                       jQuery("#mo2f_next_step2").click();
		                                   }

						   });
			    
	            document.getElementById('mo2f_next_step2').value = 'Login and Continue';
				jQuery("#mo2f_otp_token").focus();
	    	});
			
			jQuery('a[href=\"#mo2f_register_new_account\"]').click(function (e) {
	            document.getElementById('mo2f_registration_form').style.display = "block";
				var input = jQuery("#mo2f_email");
                    var len = input.val().length;
                    input[0].focus();
                    input[0].setSelectionRange(len, len);
	            document.getElementById('mo2f_login_form').style.display = "none";
	            document.getElementById('mo2f_next_step2').value = 'Create Account and Continue';
	    	});
			
			jQuery('#mo2f_next_step3').click(function(e){
				document.getElementById('mo2f_loader').style.display = "block";
				document.getElementById('mo2f_success_block_configuration').style.display = "none";
		        document.getElementById('mo2f_Error_block_configuration').style.display = "none";
		       document.getElementById('mo2f-setup-wizard-settings-area').className = ' overlay';
				
				var selected_2FA_method = '';
				var ele = document.getElementsByName('mo2f_selected_2factor_method');
				for(i = 0; i < ele.length; i++) {
                	if(ele[i].checked)
            			selected_2FA_method = ele[i].value;
            	}
            	var data ='';
            	if(selected_2FA_method == 'Google Authenticator')
            	{
					data= { 'action':'mo_two_factor_ajax',
	                	'mo_2f_two_factor_ajax' : 'mo_2fa_verify_GA_setup_wizard',
	                	'mo2f_google_auth_code' : jQuery('#mo2f_google_auth_code').val(),
		              	'mo2f_session_id'		: jQuery('#mo2f_session_id').val()
		              	};

            	}
            	else if (selected_2FA_method =='OTP Over SMS')
            	{
            		data= { 'action':'mo_two_factor_ajax',
	                	'mo_2f_two_factor_ajax' : 'mo_2fa_verify_OTPOverSMS_setup_wizard',
	                	'mo2f_otp_token' : jQuery('#mo2f_otp_token').val()
		              	};
	
            	}
            	else if(selected_2FA_method == 'OTP Over Email')
            	{
            		data= { 'action':'mo_two_factor_ajax',
	                	'mo_2f_two_factor_ajax' : 'mo_2fa_verify_OTPOverEmail_setup_wizard',
	                	'mo2f_otp_token' : jQuery('#mo2f_otp_token').val()
		              	};

            	}
            	else if(selected_2FA_method == 'Security Questions')
            	{
            			data= { 'action':'mo_two_factor_ajax',
	                	'mo_2f_two_factor_ajax' : 'mo_2fa_verify_KBA_setup_wizard',
	                	'mo2f_kbaquestion_1' : jQuery('#mo2f_kbaquestion_1').val(),
	                	'mo2f_kbaquestion_2' : jQuery('#mo2f_kbaquestion_2').val(),
	                	'mo2f_kbaquestion_3' : jQuery('#mo2f_kbaquestion_3').val(),
	                	'mo2f_kba_ans1' 	 : jQuery('#mo2f_kba_ans1').val(),
	                	'mo2f_kba_ans2' 	 : jQuery('#mo2f_kba_ans2').val(),
	                	'mo2f_kba_ans3' 	 : jQuery('#mo2f_kba_ans3').val()
		              	};

            	}
            	var ajax_url = "<?php echo admin_url('admin-ajax.php'); ?>";
		        jQuery.post(ajax_url, data, function(response){
		        	document.getElementById('mo2f_loader').style.display = "none";
		        	      	document.getElementById('mo2f-setup-wizard-settings-area').classList.remove('overlay');
		
		        	if(response =='SUCCESS')
		        	{
		        		var lineElement = document.getElementById("mo2f-setup-wizard-line3");
						lineElement.className += " mo2f-setup-wizard-timeline-line-active";
						var stepElement = document.getElementById("mo2f-setup-wizard-step4");
						stepElement.className += " mo2f-setup-wizard-timeline-step-active";
						document.getElementById('mo2f-setup-settings-error-loading-area3').style.display  = "none";
						jQuery('#mo2f-setup-settings-error-loading-area4').css('display','block');

		        	}
		        	else
		        	{
		        		document.getElementById('mo2f_configure_Error_message').innerHTML = response;
                		document.getElementById('mo2f_Error_block_configuration').style.display = "block";
		        	}
		        });

			});

			jQuery("#mo2f_next_step2").click(function(e){
				document.getElementById('mo2f-setup-wizard-settings-area').className = ' overlay';
				document.getElementById('mo2f_loader').style.display = "block";
				document.getElementById('mo2f_Error_block').style.display = "none";
                document.getElementById('mo2f_next_step2').disabled = true;		
				var ajax_url = "<?php echo admin_url('admin-ajax.php'); ?>";
                var email = jQuery("#mo2f_email").val();
                var password = jQuery("#mo2f_password").val();
                if(jQuery("#mo2f_next_step2").val() == 'Login and Continue')
                {
                	email = jQuery("#mo2f_email_login").val();
                	password = jQuery("#mo2f_password_login").val();
                }
				var data= { 'action':'mo_two_factor_ajax',
                	'mo_2f_two_factor_ajax' : 'mo_wpns_register_verify_customer',
	                'email': email,
	                'password':password,
	                'confirmPassword' : jQuery("#mo2f_confirmPassword").val(),
	                'Login and Continue' : jQuery("#mo2f_next_step2").val()
	           	};
		              	
                jQuery.post(ajax_url, data, function(response){
                	document.getElementById('mo2f-setup-wizard-settings-area').classList.remove('overlay');
					
                	document.getElementById('mo2f_next_step2').disabled = false;
                	if(response == 'SUCCESS')
                	{
                		var lineElement = document.getElementById("mo2f-setup-wizard-line2");
						lineElement.className += " mo2f-setup-wizard-timeline-line-active";
						var stepElement = document.getElementById("mo2f-setup-wizard-step3");
						stepElement.className += " mo2f-setup-wizard-timeline-step-active";
						document.getElementById('mo2f-setup-settings-error-loading-area2').style.display  = "none";
						
					    jQuery("#mo2f_otp_token").focus();
						jQuery('#mo2f-setup-settings-error-loading-area3').css('display','block');
						
						var ajax_url = "<?php echo admin_url('admin-ajax.php'); ?>";
	            		var data= { 'action':'mo_two_factor_ajax',
		                	'mo_2f_two_factor_ajax' : 'mo_2fa_configure_OTPOverSMS_setup_wizard'
			              	};
			            jQuery.post(ajax_url, data, function(response){
			            	document.getElementById('mo2f_loader').style.display = "none";
			            	document.getElementById('mo2f_main_content').innerHTML = response;
							jQuery("#mo2f_contact_info").intlTelInput();

							jQuery('#mo2f_send_otp').click(function(e){
							document.getElementById('mo2f_loader').style.display = "block";
		            		document.getElementById('mo2f-setup-wizard-settings-area').className = ' overlay';
							
							document.getElementById('mo2f_success_block_configuration').style.display = "none";
		        			document.getElementById('mo2f_Error_block_configuration').style.display = "none";
		        	
							var data = { 'action':'mo_two_factor_ajax',
			                	'mo_2f_two_factor_ajax' : 'mo_2fa_send_otp_token',
			                	'mo2f_contact_info' 				: jQuery('#mo2f_contact_info').val(),
				              	'selected_2FA_method'	: 'SMS'	              	
				              	};
							var ajax_url = "<?php echo admin_url('admin-ajax.php'); ?>";
					        jQuery.post(ajax_url, data, function(response){
					        	document.getElementById('mo2f_loader').style.display = "none";
					        	document.getElementById('mo2f-setup-wizard-settings-area').classList.remove('overlay');
								
					        	if(response == 'SUCCESS')
					        	{
					        		$message = 'An OTP has been sent to phone number. Please enter the OTP to set the 2FA.';
					        		document.getElementById('mo2f_configure_success_message').innerHTML = $message;
                					document.getElementById('mo2f_success_block_configuration').style.display = "block";
		        		
					        	}
					        	else
					        	{
					        		document.getElementById('mo2f_configure_Error_message').innerHTML = response;
                					document.getElementById('mo2f_Error_block_configuration').style.display = "block";
		        	
					        	}
					        });

							});
							
							jQuery("#mo2f_otp_token").keypress(function(e){
							            if (e.which == 13) {	
		                                         e.preventDefault();
		                                       jQuery("#mo2f_next_step3").click();
		                                   }

						   });
						   jQuery("#mo2f_contact_info").keypress(function(e){
							            if (e.which == 13) {	
		                                         e.preventDefault();
		                                       jQuery("#mo2f_send_otp").click();
											   jQuery("#mo2f_otp_token").focus();
		                                   }

						   });
						  


		            
			            });
                	}
                	else
                	{
                		document.getElementById('mo2f_loader').style.display = "none";
                		document.getElementById('mo2f_Error_message').innerHTML = response;
                		document.getElementById('mo2f_Error_block').style.display = "block";
                	}
                	
	            });

			});

			jQuery("#mo2f_next_step1").click(function(e){
				var ele = document.getElementsByName('mo2f_selected_2factor_method');
				var selected_2FA_method = '';
				for(i = 0; i < ele.length; i++) {
                	if(ele[i].checked)
            			selected_2FA_method = ele[i].value;
            	}
                var configMessage = 'Configure '+selected_2FA_method;
                jQuery("#mo2f_setup_method_title").text(configMessage);
        		
            	if(selected_2FA_method == '')
            	{
            		return '';
            	}

            	document.getElementById('mo2f-setup-settings-error-loading-area1').style.display  = "none";
        		var lineElement = document.getElementById("mo2f-setup-wizard-line1");
				lineElement.className += " mo2f-setup-wizard-timeline-line-active";
				var stepElement = document.getElementById("mo2f-setup-wizard-step2");
				stepElement.className += " mo2f-setup-wizard-timeline-step-active";
				
            	if(selected_2FA_method !="OTP Over SMS" && selected_2FA_method != '')
            	{
            		var lineElement = document.getElementById("mo2f-setup-wizard-line2");
					lineElement.className += " mo2f-setup-wizard-timeline-line-active";
					var stepElement = document.getElementById("mo2f-setup-wizard-step3");
					stepElement.className += " mo2f-setup-wizard-timeline-step-active";
					jQuery('#mo2f-setup-settings-error-loading-area3').css('display','block');

					document.getElementById('mo2f_loader').style.display = "block";
				
					var mo2f_setup_call = "";
            		if(selected_2FA_method == "Google Authenticator")
            		{
            			mo2f_setup_call = "mo_2fa_configure_GA_setup_wizard";
            		}
            		else if(selected_2FA_method =="OTP Over Email")
            		{
            			mo2f_setup_call ="mo_2fa_configure_OTPOverEmail_setup_wizard";
            		}
            		else if (selected_2FA_method == "Security Questions")
            		{
            			mo2f_setup_call = "mo_2fa_configure_KBA_setup_wizard";
            		}
            		var ajax_url = "<?php echo admin_url('admin-ajax.php'); ?>";
            		var data= { 'action' : 'mo_two_factor_ajax',
	                	'mo_2f_two_factor_ajax' : mo2f_setup_call
		              	};
		            jQuery.post(ajax_url, data, function(response){
		            	document.getElementById('mo2f_loader').style.display = "none";
		            	document.getElementById('mo2f_main_content').innerHTML = response;
		            	
		            	if(selected_2FA_method == 'Google Authenticator')
		            	{
			            	jQuery('.mo2f_gauth').qrcode({
			                    'render': 'image',
			                    size: 175,
			                    'text': jQuery('.mo2f_gauth').data('qrcode')
			                });
		            		jQuery('a[href="#mo2f_scanbarcode_a"]').click(function(e){
				            var element = document.getElementById('mo2f_scanbarcode_a');
					            if(element.style.display === 'none')
					                element.style.display = 'block';
					            
					            else
					                element.style.display = "none";
					        });
                          jQuery("#mo2f_google_auth_code").focus();
						  jQuery("#mo2f_google_auth_code").keypress(function(e){
							            if (e.which == 13) {	
		                                         e.preventDefault();
		                                       jQuery("#mo2f_next_step3").click();
		                                   }

						   });
		            	}
		            	else if(selected_2FA_method =='OTP Over Email')
						{   
							
							var input = jQuery("#mo2f_contact_info");
                            var len = input.val().length;
                            input[0].focus();
                            input[0].setSelectionRange(len, len);
							jQuery("#mo2f_contact_info" ).keypress(function(e){
							            if (e.which == 13) {	
		                                         e.preventDefault();
		                                       jQuery("#mo2f_send_otp").click();
											   jQuery("#mo2f_otp_token").focus();
		                                   }

						   });
							jQuery("#mo2f_otp_token" ).keypress(function(e){
							            if (e.which == 13) {	
		                                         e.preventDefault();
		                                       jQuery("#mo2f_next_step3").click();
		                                   }

						   });
							jQuery('#mo2f_send_otp').click(function(e){
							document.getElementById('mo2f_loader').style.display = "block";
		            		document.getElementById('mo2f-setup-wizard-settings-area').className = ' overlay';
							
							document.getElementById('mo2f_success_block_configuration').style.display = "none";
		        			document.getElementById('mo2f_Error_block_configuration').style.display = "none";
		        	
							var data = { 'action':'mo_two_factor_ajax',
			                	'mo_2f_two_factor_ajax' : 'mo_2fa_send_otp_token',
									'mo2f_contact_info' 				: jQuery('#mo2f_contact_info').val(),
				              	'mo2f_session_id'		: jQuery('#mo2f_session_id').val(),
								'selected_2FA_method'	: 'OTP Over Email'	              	
				              	};
							var ajax_url = "<?php echo admin_url('admin-ajax.php'); ?>";
					        jQuery.post(ajax_url, data, function(response){
					        	document.getElementById('mo2f_loader').style.display = "none";
					        	document.getElementById('mo2f-setup-wizard-settings-area').classList.remove('overlay');
								
					        	if(response == 'SUCCESS')
					        	{
										message = 'An OTP has been sent to the below email please enter the OTP to set the 2FA';
										document.getElementById('mo2f_configure_success_message').innerHTML = message;
										document.getElementById('mo2f_success_block_configuration').style.display = "block";
										
									}
									else if(response == 'SMTPNOTSET')
									{
									message = '<i class="note">NOTE :- If you haven\'t configured SMTP, please set your SMTP to get the OTP over email.</i><a href="https://plugins.miniorange.com/setup-smtp-for-miniorange-two-factor-authentication" target="_blank"><span title="View Setup Guide" class="dashicons dashicons-text-page mo2f-setup-guide"></a></span>';
									document.getElementById('mo2f_configure_Error_message').innerHTML = message;
                					document.getElementById('mo2f_Error_block_configuration').style.display = "block";
								}
					        	else
					        	{
					        		document.getElementById('mo2f_configure_Error_message').innerHTML = response;
                					document.getElementById('mo2f_Error_block_configuration').style.display = "block";
		        	
					        	}
					        });
							jQuery("#mo2f_otp_token").focus();

							});
						}
						else if (selected_2FA_method =='Security Questions')
						{
							
							var mo_option_to_hide1;
					        //hidden element in dropdown list 2
					        var mo_option_to_hide2;
                            jQuery("#mo2f_kba_ans1").focus();
							jQuery("#mo2f_kba_ans3").keypress(function(e){
							            if (e.which == 13) {	
		                                         e.preventDefault();
		                                       jQuery("#mo2f_next_step3").click();
		                                   }

						   });
							jQuery('#mo2f_kbaquestion_1').change(function(){
								list = 1;
							  	var list_selected = document.getElementById("mo2f_kbaquestion_" + list).selectedIndex;
					            //if an element is currently hidden, unhide it
					            if (typeof (mo_option_to_hide1) != "undefined" && mo_option_to_hide1 !== null && list == 2) {
					                mo_option_to_hide1.style.display = 'block';
					            } else if (typeof (mo_option_to_hide2) != "undefined" && mo_option_to_hide2 !== null && list == 1) {
					                mo_option_to_hide2.style.display = 'block';
					            }
					            //select the element to hide and then hide it
					            if (list == 1) {
					                if (list_selected != 0) {
					                    mo_option_to_hide2 = document.getElementById("mq" + list_selected + "_2");
					                    mo_option_to_hide2.style.display = 'none';
					                }
					            }
							});
							jQuery('#mo2f_kbaquestion_2').change(function(){
								list = 2;
							  	var list_selected = document.getElementById("mo2f_kbaquestion_" + list).selectedIndex;
					            //if an element is currently hidden, unhide it
					            if (typeof (mo_option_to_hide1) != "undefined" && mo_option_to_hide1 !== null && list == 2) {
					                mo_option_to_hide1.style.display = 'block';
					            } else if (typeof (mo_option_to_hide2) != "undefined" && mo_option_to_hide2 !== null && list == 1) {
					                mo_option_to_hide2.style.display = 'block';
					            }
					            //select the element to hide and then hide it
					            if (list == 2) {
					                if (list_selected != 0) {
					                    mo_option_to_hide1 = document.getElementById("mq" + list_selected + "_1");
					                    mo_option_to_hide1.style.display = 'none';
					                }
					            }
							});

						}
		            });
			       
            
            	}
            	else if(selected_2FA_method == 'OTP Over SMS')
            	{
            		jQuery('#mo2f-setup-settings-error-loading-area2').css('display','block');
				
					var input = jQuery("#mo2f_email");
                    var len = input.val().length;
                    input[0].focus();
                    input[0].setSelectionRange(len, len);
					jQuery("#mo2f_confirmPassword").keypress(function(e){
							            if (e.which == 13) {	
		                                         e.preventDefault();
		                                       jQuery("#mo2f_next_step2").click();
		                                   }

						   });
            	}
            	
				
			});
			jQuery('input:radio[name=mo2f_selected_2factor_method]').click(function() {
               	

                localStorage.setItem("last_tab", 'setup_2fa');
                var selectedMethod = jQuery(this).val();
                var ajax_url = "<?php echo esc_url(admin_url('admin-ajax.php')); ?>";
                var nonce = "<?php echo esc_html(wp_create_nonce( 'miniorange-select-method-setup-wizard' )); ?>";
                
                if(selectedMethod == 'Duo Authenticator' || selectedMethod =='OTP Over Telegram')
                {
	                var data= { 'action':'mo_two_factor_ajax',
			                	'mo_2f_two_factor_ajax' : 'select_method_setup_wizard',
				                'mo2f_method': selectedMethod,
				                'nonce': nonce };
			              	
	                 jQuery.post(ajax_url, data, function(response){
	                       	window.location.href = '<?php echo esc_url(admin_url()."admin.php?page=mo_2fa_two_fa"); ?>'; 
	                });
           		}

           });

			jQuery('a[href="#skiptwofactor1"]').click(function(){
                
                localStorage.setItem("last_tab", 'setup_2fa');
                
                var nonce = "<?php echo esc_js(wp_create_nonce('mo2fskiptwofactornonce')); ?>";
                var skiptwofactorstage = 'first_page';
                var data= {
                    'action'                : 'mo_two_factor_ajax',
                    'mo_2f_two_factor_ajax' : 'mo2f_skiptwofactor_wizard',
                    'nonce'                 : nonce,
                    'twofactorskippedon'    : skiptwofactorstage,
                };
                var ajax_url = "<?php echo esc_url(admin_url('admin-ajax.php')); ?>";
                
                jQuery.post(ajax_url, data, function(response){
                    window.location.href = '<?php echo esc_url(admin_url()."admin.php?page=mo_2fa_two_fa"); ?>';
                });
                
			});
            jQuery('#mo2f_go_back_to_dashboard').click(function(){
                var nonce = "<?php echo esc_js(wp_create_nonce('mo2fskiptwofactornonce')); ?>";
                var skiptwofactorstage = 'Get_started_page';
                var data= {
                    'action'                : 'mo_two_factor_ajax',
                    'mo_2f_two_factor_ajax' : 'mo2f_skiptwofactor_wizard',
                    'nonce'                 : nonce,
                    'twofactorskippedon'    : skiptwofactorstage,
                };
                var ajax_url = "<?php echo esc_url(admin_url('admin-ajax.php')); ?>";
                
                jQuery.post(ajax_url, data, function(response){
                });
                
			});
            jQuery('a[href="#skiptwofactor2"]').click(function(){
                
                localStorage.setItem("last_tab", 'setup_2fa');
                
                var nonce = "<?php echo esc_js(wp_create_nonce('mo2fskiptwofactornonce')); ?>";
                var skiptwofactorstage = 'registration/sign-in';
                var data= {
                    'action'                : 'mo_two_factor_ajax',
                    'mo_2f_two_factor_ajax' : 'mo2f_skiptwofactor_wizard',
                    'nonce'                 : nonce,
                    'twofactorskippedon'    : skiptwofactorstage,
                };
                var ajax_url = "<?php echo esc_url(admin_url('admin-ajax.php')); ?>";
                
                jQuery.post(ajax_url, data, function(response){
                    window.location.href = '<?php echo esc_url(admin_url()."admin.php?page=mo_2fa_two_fa"); ?>';
                });
                
			});
            jQuery('a[href="#skiptwofactor3"]').click(function(){
                
                localStorage.setItem("last_tab", 'setup_2fa');
                
                var nonce = "<?php echo esc_js(wp_create_nonce('mo2fskiptwofactornonce')); ?>";
                var skiptwofactorstage = 'configuration';
                var data= {
                    'action'                : 'mo_two_factor_ajax',
                    'mo_2f_two_factor_ajax' : 'mo2f_skiptwofactor_wizard',
                    'nonce'                 : nonce,
                    'twofactorskippedon'    : skiptwofactorstage,
                };
                var ajax_url = "<?php echo esc_url(admin_url('admin-ajax.php')); ?>";
                
                jQuery.post(ajax_url, data, function(response){
                    window.location.href = '<?php echo esc_url(admin_url()."admin.php?page=mo_2fa_two_fa"); ?>';
                });
                
			});


		</script>
		<?php
	}

	/**
	 * Attempt to catch the js error preventing the Vue app from loading and displaying that message for better support.
	 *
	 * @since 2.6.0
	 */
	private function settings_inline_js() {
		?>
		<script type="text/javascript">
			window.onerror = function myErrorHandler( errorMsg, url, lineNumber ) {
				/* Don't try to put error in container that no longer exists post-vue loading */
				var message_container = document.getElementById( 'mo2f-setup-nojs-error-message' );
				if ( ! message_container ) {
					return false;
				}
				var message = document.getElementById( 'mo2f-setup-alert-message' );
				message.innerHTML = errorMsg;
				message_container.style.display = 'block';
				return false;
			}
		</script>
		<?php
	}


	public function mo2f_setup_wizard_content() {
		$admin_url = is_network_admin() ? network_admin_url() : admin_url();

		$this->settings_error_page( 'mo2f-setup-vue-setup-wizard', '<a href="' . esc_url($admin_url).'admin.php?page=mo_2fa_two_fa">' . esc_html__( 'Go back to the Dashboard', 'mo2f-setup' ) . '</a>' );
		$this->settings_inline_js();
	}

	function miniorange_auth_save_settings() {
		if ( get_site_option( 'mo2f_plugin_redirect' )) {
			delete_site_option( 'mo2f_plugin_redirect' );
			$redirect_to_finish = add_query_arg(
				array(
					'page' => 'mo2f-setup-wizard',
					'current-step' => 'welcome',
				),admin_url( 'admin.php' )
			);
			wp_safe_redirect( esc_url_raw( $redirect_to_finish ) );
		}
		if ( array_key_exists( 'page', $_REQUEST ) && sanitize_text_field($_REQUEST['page']) == 'mo_2fa_two_fa' ) {
			if ( ! session_id() || session_id() == '' || ! isset( $_SESSION ) ) {
				if(session_status() != PHP_SESSION_DISABLED )
				session_start();
			}
		}

		global $user;
		global $Mo2fdbQueries;
		$defaultCustomerKey = $this->defaultCustomerKey;
		$defaultApiKey      = $this->defaultApiKey;

		$user    = wp_get_current_user();
		$user_id = $user->ID;

		if ( current_user_can( 'manage_options' ) ) {
			
			if(strlen(get_option('mo2f_encryption_key'))>17){
				$get_encryption_key = MO2f_Utility::random_str(16);
				update_option('mo2f_encryption_key',$get_encryption_key);
			}
			
			if ( isset( $_POST['option'] ) and sanitize_text_field($_POST['option']) == "mo_auth_deactivate_account" ) {
				$nonce = sanitize_text_field($_POST['mo_auth_deactivate_account_nonce']);
				if ( ! wp_verify_nonce( $nonce, 'mo-auth-deactivate-account-nonce' ) ) {
					$error = new WP_Error();
					$error->add( 'empty_username', '<strong>' . mo2f_lt( 'ERROR' ) . '</strong>: ' . mo2f_lt( 'Invalid Request.' ) );

					return $error;
				} else {
					$url = admin_url( 'plugins.php' );
					wp_redirect( $url );
				}
			}else if ( isset( $_POST['option'] ) and sanitize_text_field($_POST['option']) == "mo_auth_remove_account" ) {
				$nonce = sanitize_text_field($_POST['mo_auth_remove_account_nonce']);
				if ( ! wp_verify_nonce( $nonce, 'mo-auth-remove-account-nonce' ) ) {
					$error = new WP_Error();
					$error->add( 'empty_username', '<strong>' . mo2f_lt( 'ERROR' ) . '</strong>: ' . mo2f_lt( 'Invalid Request.' ) );
					return $error;
				} else {
					update_option( 'mo2f_register_with_another_email', 1 );
					$this->mo_auth_deactivate();
				}
			}else if(isset($_POST['option']) and sanitize_text_field($_POST['option']) == 'mo2f_skiplogin'){
				$nonce = sanitize_text_field($_POST['mo2f_skiplogin_nonce']);
				if ( ! wp_verify_nonce( $nonce, 'miniorange-2-factor-skiplogin-failed-nonce' ) ) {
					$error = new WP_Error();
					$error->add( 'empty_username', '<strong>' . mo2f_lt( 'ERROR' ) . '</strong>: ' . mo2f_lt( 'Invalid Request.' ) );
					return $error;
				} else {
					update_option('mo2f_tour_started',2);
				}
			}else if(isset($_POST['option']) and sanitize_text_field($_POST['option']) == 'mo2f_userlogout'){
				$nonce = sanitize_text_field($_POST['mo2f_userlogout_nonce']);
				if ( ! wp_verify_nonce( $nonce, 'miniorange-2-factor-userlogout-failed-nonce' ) ) {
					$error = new WP_Error();
					$error->add( 'empty_username', '<strong>' . mo2f_lt( 'ERROR' ) . '</strong>: ' . mo2f_lt( 'Invalid Request.' ) );
					return $error;
				} else {
					update_option('mo2f_tour_started',2);
					wp_logout();
					wp_redirect(admin_url());
				}
			}else if ( isset( $_POST['option'] ) and sanitize_text_field($_POST['option']) == "mo2f_save_proxy_settings" ) {
				$nonce = sanitize_text_field($_POST['mo2f_save_proxy_settings_nonce']);
				if ( ! wp_verify_nonce( $nonce, 'mo2f-save-proxy-settings-nonce' ) ) {
					$error = new WP_Error();
					$error->add( 'empty_username', '<strong>' . mo2f_lt( 'ERROR' ) . '</strong>: ' . mo2f_lt( 'Invalid Request.' ) );
					return $error;
				} else {
					$proxyHost     = sanitize_text_field($_POST['proxyHost']);
					$portNumber    = sanitize_text_field($_POST['portNumber']);
					$proxyUsername = sanitize_text_field($_POST['proxyUsername']);
					$proxyPassword = sanitize_text_field($_POST['proxyPass']);

					update_option( 'mo2f_proxy_host', $proxyHost );
					update_option( 'mo2f_port_number', $portNumber );
					update_option( 'mo2f_proxy_username', $proxyUsername );
					update_option( 'mo2f_proxy_password', $proxyPassword );
					update_option( 'mo2f_message', 'Proxy settings saved successfully.' );
					$this->mo_auth_show_success_message();
				}

			}else if ( isset( $_POST['option'] ) and sanitize_text_field($_POST['option']) == "mo_auth_register_customer" ) {    //register the admin to miniOrange
				//miniorange_register_customer_nonce
				$nonce = sanitize_text_field($_POST['miniorange_register_customer_nonce']);
				if ( ! wp_verify_nonce( $nonce, 'miniorange-register-customer-nonce' ) ) {
					$error = new WP_Error();
					$error->add( 'empty_username', '<strong>' . mo2f_lt( 'ERROR' ) . '</strong>: ' . mo2f_lt( 'Invalid Request.' ) );

					return $error;
				} else {
					//validate and sanitize
					$email           = '';
					$password        = '';
					$confirmPassword = '';
					$is_registration = get_user_meta( $user->ID, 'mo2f_email_otp_count', true );

					if ( MO2f_Utility::mo2f_check_empty_or_null( $_POST['email'] ) || MO2f_Utility::mo2f_check_empty_or_null( $_POST['password'] ) || MO2f_Utility::mo2f_check_empty_or_null( $_POST['confirmPassword'] ) ) {
						update_option( 'mo2f_message', Mo2fConstants:: langTranslate( "INVALID_ENTRY" ) );

						return;
					} else if ( strlen( $_POST['password'] ) < 6 || strlen( $_POST['confirmPassword'] ) < 6 ) {
						update_option( 'mo2f_message', Mo2fConstants:: langTranslate( "MIN_PASS_LENGTH" ) );

					} else {
						$email           = sanitize_email( $_POST['email'] );
						$password        = sanitize_text_field( $_POST['password'] );
						$confirmPassword = sanitize_text_field( $_POST['confirmPassword'] );

						$email = strtolower( $email );
						
						$pattern = '/^[(\w)*(\!\@\#\$\%\^\&\*\.\-\_)*]+$/';

						if(preg_match($pattern,$password)){
							if ( strcmp( $password, $confirmPassword ) == 0 ) {
								update_option( 'mo2f_email', $email );

								$Mo2fdbQueries->insert_user( $user_id, array( 'user_id' => $user_id ) );
								update_option( 'mo2f_password', stripslashes( $password ) );
								$customer    = new Customer_Setup();
								$customerKey = json_decode( $customer->check_customer(), true );

								if ( strcasecmp( $customerKey['status'], 'CUSTOMER_NOT_FOUND' ) == 0 ) {
									if ( $customerKey['status'] == 'ERROR' ) {
										update_option( 'mo2f_message', Mo2fConstants:: langTranslate( $customerKey['message'] ) );
									} else {
										$this->mo2f_create_customer( $user );
										delete_user_meta( $user->ID, 'mo_2fa_verify_otp_create_account' );
										delete_user_meta( $user->ID, 'register_account_popup' );
										if(get_user_meta( $user->ID, 'mo2f_2FA_method_to_configure'))
											update_user_meta( $user->ID, 'configure_2FA', 1 );

									}
								} else { //customer already exists, redirect him to login page

									update_option( 'mo2f_message', Mo2fConstants:: langTranslate( "ACCOUNT_ALREADY_EXISTS" ) );
									update_option('mo_2factor_user_registration_status','MO_2_FACTOR_VERIFY_CUSTOMER');

								}

							} else {
								update_option( 'mo2f_message', Mo2fConstants:: langTranslate( "PASSWORDS_MISMATCH" ) );
								$this->mo_auth_show_error_message();
							}
						}
						else{
							update_option( 'mo2f_message', "Password length between 6 - 15 characters. Only following symbols (!@#.$%^&*-_) should be present." );
							$this->mo_auth_show_error_message();
						}
					}
				}
			}else if ( isset( $_POST['option'] ) and sanitize_text_field($_POST['option']) == "mo_auth_verify_customer" ) {    //register the admin to miniOrange if already exist

				$nonce = sanitize_text_field($_POST['miniorange_verify_customer_nonce']);

				if ( ! wp_verify_nonce( $nonce, 'miniorange-verify-customer-nonce' ) ) {
					$error = new WP_Error();
					$error->add( 'empty_username', '<strong>' . mo2f_lt( 'ERROR' ) . '</strong>: ' . mo2f_lt( 'Invalid Request.' ) );

					return $error;
				} else {

					//validation and sanitization
					$email    = '';
					$password = '';
					$Mo2fdbQueries->insert_user( $user_id, array( 'user_id' => $user_id ) );


					if ( MO2f_Utility::mo2f_check_empty_or_null( $_POST['email'] ) || MO2f_Utility::mo2f_check_empty_or_null( $_POST['password'] ) ) {
						update_option( 'mo2f_message', Mo2fConstants:: langTranslate( "INVALID_ENTRY" ) );
						$this->mo_auth_show_error_message();

						return;
					} else {
						$email    = sanitize_email( $_POST['email'] );
						$password = sanitize_text_field( $_POST['password'] );
					}

					update_option( 'mo2f_email', $email );
					update_option( 'mo2f_password', stripslashes( $password ) );
					$customer    = new Customer_Setup();
					$content     = $customer->get_customer_key();
					$customerKey = json_decode( $content, true );

					if ( json_last_error() == JSON_ERROR_NONE ) {
						if ( is_array( $customerKey ) && array_key_exists( "status", $customerKey ) && $customerKey['status'] == 'ERROR' ) {
							update_option( 'mo2f_message', Mo2fConstants::langTranslate( $customerKey['message'] ) );
							$this->mo_auth_show_error_message();
						} else if ( is_array( $customerKey ) ) {
							if ( isset( $customerKey['id'] ) && ! empty( $customerKey['id'] ) ) {
								update_option( 'mo2f_customerKey', $customerKey['id'] );
								update_option( 'mo2f_api_key', $customerKey['apiKey'] );
								update_option( 'mo2f_customer_token', $customerKey['token'] );
								update_option( 'mo2f_app_secret', $customerKey['appSecret'] );
								$Mo2fdbQueries->update_user_details( $user->ID, array( 'mo2f_user_phone' => $customerKey['phone'] ) );
								update_option( 'mo2f_miniorange_admin', $user->ID );

								$mo2f_emailVerification_config_status = MoWpnsUtility::get_mo2f_db_option('mo2f_is_NC', 'get_option') == 0 ? true : false;

								delete_option( 'mo2f_password' );
								update_option( 'mo_2factor_admin_registration_status', 'MO_2_FACTOR_CUSTOMER_REGISTERED_SUCCESS' );

								$Mo2fdbQueries->update_user_details( $user->ID, array(
									'mo2f_EmailVerification_config_status' => $mo2f_emailVerification_config_status,
									'mo2f_user_email'                      => get_option( 'mo2f_email' ),
									'user_registration_with_miniorange'    => 'SUCCESS',
									'mo_2factor_user_registration_status'               => 'MO_2_FACTOR_PLUGIN_SETTINGS',
									'mo2f_2factor_enable_2fa_byusers'      => 1,
								) );
								$mo_2factor_user_registration_status = 'MO_2_FACTOR_PLUGIN_SETTINGS';
								$Mo2fdbQueries->update_user_details( $user->ID, array( 'mo_2factor_user_registration_status' => $mo_2factor_user_registration_status ) );
								$configured_2FA_method = 'NONE';
								$user_email            = $Mo2fdbQueries->get_user_detail( 'mo2f_user_email', $user->ID );
								$enduser               = new Two_Factor_Setup();
								$userinfo              = json_decode( $enduser->mo2f_get_userinfo( $user_email ), true );

								$mo2f_second_factor = 'NONE';
								if ( json_last_error() == JSON_ERROR_NONE ) {
									if ( $userinfo['status'] == 'SUCCESS' ) {
										$mo2f_second_factor = mo2f_update_and_sync_user_two_factor( $user->ID, $userinfo );

									}
								}
								if ( $mo2f_second_factor != 'NONE' ) {
									$configured_2FA_method = MO2f_Utility::mo2f_decode_2_factor( $mo2f_second_factor, "servertowpdb" );

									if ( MoWpnsUtility::get_mo2f_db_option('mo2f_is_NC', 'get_option') == 0 ) {

										$auth_method_abr = str_replace( ' ', '', $configured_2FA_method );
										$Mo2fdbQueries->update_user_details( $user->ID, array(
											'mo2f_configured_2FA_method'                  => $configured_2FA_method,
											'mo2f_' . $auth_method_abr . '_config_status' => true
										) );

									} else {
										if ( in_array( $configured_2FA_method, array(
											'Email Verification',
											'Authy Authenticator',
											'OTP over SMS'
										) ) ) {
											$enduser->mo2f_update_userinfo( $user_email, 'NONE', null, '', true );
										}
									}


								}

								$mo2f_message = Mo2fConstants:: langTranslate( "ACCOUNT_RETRIEVED_SUCCESSFULLY" );
								if ( $configured_2FA_method != 'NONE' && MoWpnsUtility::get_mo2f_db_option('mo2f_is_NC', 'get_option') == 0 ) {
									$mo2f_message .= ' <b>' . $configured_2FA_method . '</b> ' . Mo2fConstants:: langTranslate( "DEFAULT_2ND_FACTOR" ) . '.';
								}
								$mo2f_message .= ' ' . '<a href=\"admin.php?page=mo_2fa_two_fa\" >' . Mo2fConstants:: langTranslate( "CLICK_HERE" ) . '</a> ' . Mo2fConstants:: langTranslate( "CONFIGURE_2FA" );

								delete_user_meta( $user->ID, 'register_account_popup' );

								$mo2f_customer_selected_plan = get_option( 'mo2f_customer_selected_plan' );
								if ( ! empty( $mo2f_customer_selected_plan ) ) {
									delete_option( 'mo2f_customer_selected_plan' );
									header( 'Location: admin.php?page=mo_2fa_upgrade' );
								} else if ( $mo2f_second_factor == 'NONE' ) {
									update_user_meta( $user->ID, 'configure_2FA', 1 );
								}

								update_option( 'mo2f_message', $mo2f_message );
								$this->mo_auth_show_success_message();
							} else {
								update_option( 'mo2f_message', Mo2fConstants:: langTranslate( "INVALID_EMAIL_OR_PASSWORD" ) );
								$mo_2factor_user_registration_status = 'MO_2_FACTOR_VERIFY_CUSTOMER';
								update_option('mo_2factor_user_registration_status',$mo_2factor_user_registration_status);
								$this->mo_auth_show_error_message();
							}

						}
					} else {
						update_option( 'mo2f_message', Mo2fConstants:: langTranslate( "INVALID_EMAIL_OR_PASSWORD" ) );
						$mo_2factor_user_registration_status = 'MO_2_FACTOR_VERIFY_CUSTOMER';
						update_option('mo_2factor_user_registration_status',$mo_2factor_user_registration_status);
						$this->mo_auth_show_error_message();
					}

					delete_option( 'mo2f_password' );
				}
			}else if  ( isset( $_POST['option'] ) and sanitize_text_field($_POST['option']) == 'mo_2factor_phone_verification' ) { //at registration time
				$phone = sanitize_text_field( $_POST['phone_number'] );
				$Mo2fdbQueries->update_user_details( $user->ID, array( 'mo2f_user_phone' => $phone ) );

				$phone     = str_replace( ' ', '', $phone );
				$auth_type = 'SMS';
				$customer  = new Customer_Setup();

				$send_otp_response = json_decode( $customer->send_otp_token( $phone, $auth_type, $defaultCustomerKey, $defaultApiKey ), true );

				if ( strcasecmp( $send_otp_response['status'], 'SUCCESS' ) == 0 ) {
					$mo_2factor_user_registration_status = 'MO_2_FACTOR_OTP_DELIVERED_SUCCESS';
					$Mo2fdbQueries->update_user_details( $user->ID, array( 'mo_2factor_user_registration_status' => $mo_2factor_user_registration_status ) );
					update_user_meta( $user->ID, 'mo_2fa_verify_otp_create_account', $send_otp_response['txId'] );

					if ( get_user_meta( $user->ID, 'mo2f_sms_otp_count', true ) ) {
						update_option( 'mo2f_message', 'Another One Time Passcode has been sent <b>( ' . get_user_meta( $user->ID, 'mo2f_sms_otp_count', true ) . ' )</b> for verification to ' . $phone );
						update_user_meta( $user->ID, 'mo2f_sms_otp_count', get_user_meta( $user->ID, 'mo2f_sms_otp_count', true ) + 1 );
					} else {
						update_option( 'mo2f_message', 'One Time Passcode has been sent for verification to ' . $phone );
						update_user_meta( $user->ID, 'mo2f_sms_otp_count', 1 );
					}

					$this->mo_auth_show_success_message();
				} else {
					update_option( 'mo2f_message', Mo2fConstants:: langTranslate( "ERROR_WHILE_SENDING_SMS" ) );
					$mo_2factor_user_registration_status = 'MO_2_FACTOR_OTP_DELIVERED_FAILURE';
					$Mo2fdbQueries->update_user_details( $user->ID, array( 'mo_2factor_user_registration_status' => $mo_2factor_user_registration_status ) );
					$this->mo_auth_show_error_message();
				}

			}else if  ( isset( $_POST['option'] ) and sanitize_text_field($_POST['option']) == "mo_2factor_resend_otp" ) { //resend OTP over email for admin

				$nonce = sanitize_text_field($_POST['mo_2factor_resend_otp_nonce']);

				if ( ! wp_verify_nonce( $nonce, 'mo-2factor-resend-otp-nonce' ) ) {
					$error = new WP_Error();
					$error->add( 'empty_username', '<strong>' . mo2f_lt( 'ERROR' ) . '</strong>: ' . mo2f_lt( 'Invalid Request.' ) );

					return $error;
				} else {
					$customer = new Customer_Setup();
					$content  = json_decode( $customer->send_otp_token( get_option( 'mo2f_email' ), 'EMAIL', $defaultCustomerKey, $defaultApiKey ), true );
					if ( strcasecmp( $content['status'], 'SUCCESS' ) == 0 ) {
						if ( get_user_meta( $user->ID, 'mo2f_email_otp_count', true ) ) {
							update_user_meta( $user->ID, 'mo2f_email_otp_count', get_user_meta( $user->ID, 'mo2f_email_otp_count', true ) + 1 );
							update_option( 'mo2f_message', Mo2fConstants:: langTranslate( "RESENT_OTP" ) . ' <b>( ' . get_user_meta( $user->ID, 'mo2f_email_otp_count', true ) . ' )</b> to <b>' . ( get_option( 'mo2f_email' ) ) . '</b> ' . Mo2fConstants:: langTranslate( "ENTER_OTP" ) );
						} else {
							update_option( 'mo2f_message', Mo2fConstants:: langTranslate( "OTP_SENT" ) . '<b> ' . ( get_option( 'mo2f_email' ) ) . ' </b>' . Mo2fConstants:: langTranslate( "ENTER_OTP" ) );
							update_user_meta( $user->ID, 'mo2f_email_otp_count', 1 );
						}
						$mo_2factor_user_registration_status = 'MO_2_FACTOR_OTP_DELIVERED_SUCCESS';
						$Mo2fdbQueries->update_user_details( $user->ID, array( 'mo_2factor_user_registration_status' => $mo_2factor_user_registration_status ) );
						update_user_meta( $user->ID, 'mo_2fa_verify_otp_create_account', $content['txId'] );
						$this->mo_auth_show_success_message();
					} else {
						update_option( 'mo2f_message', Mo2fConstants:: langTranslate( "ERROR_IN_SENDING_EMAIL" ) );
						$mo_2factor_user_registration_status = 'MO_2_FACTOR_OTP_DELIVERED_FAILURE';
						$Mo2fdbQueries->update_user_details( $user->ID, array( 'mo_2factor_user_registration_status' => $mo_2factor_user_registration_status ) );
						$this->mo_auth_show_error_message();
					}
				}


			}else if  ( isset( $_POST['option'] ) and sanitize_text_field($_POST['option']) == "mo2f_dismiss_notice_option" ) {
				update_option( 'mo2f_bug_fix_done', 1 );
			}else if  ( isset( $_POST['option'] ) and sanitize_text_field($_POST['option']) == "mo_2factor_validate_otp" ) { //validate OTP over email for admin

				$nonce = sanitize_text_field($_POST['mo_2factor_validate_otp_nonce']);

				if ( ! wp_verify_nonce( $nonce, 'mo-2factor-validate-otp-nonce' ) ) {
					$error = new WP_Error();
					$error->add( 'empty_username', '<strong>' . mo2f_lt( 'ERROR' ) . '</strong>: ' . mo2f_lt( 'Invalid Request.' ) );

					return $error;
				} else {
				//validation and sanitization
					$otp_token = '';
					if ( MO2f_Utility::mo2f_check_empty_or_null( $_POST['otp_token'] ) ) {
						update_option( 'mo2f_message', Mo2fConstants:: langTranslate( "INVALID_ENTRY" ) );
						$this->mo_auth_show_error_message();

						return;
					} else {
						$otp_token = sanitize_text_field( $_POST['otp_token'] );
					}

					$customer = new Customer_Setup();

					$transactionId = get_user_meta( $user->ID, 'mo_2fa_verify_otp_create_account', true );

					$content = json_decode( $customer->validate_otp_token( 'EMAIL', null, $transactionId, $otp_token, $defaultCustomerKey, $defaultApiKey ), true );

					if ( $content['status'] == 'ERROR' ) {
						update_option( 'mo2f_message', Mo2fConstants:: langTranslate( $content['message'] ) );

					} else {

						if ( strcasecmp( $content['status'], 'SUCCESS' ) == 0 ) { //OTP validated
							$this->mo2f_create_customer( $user );
							delete_user_meta( $user->ID, 'mo_2fa_verify_otp_create_account' );
							delete_user_meta( $user->ID, 'register_account_popup' );
							update_user_meta( $user->ID, 'configure_2FA', 1 );
						} else {  // OTP Validation failed.
							update_option( 'mo2f_message', Mo2fConstants:: langTranslate( "INVALID_OTP" ) );
							$Mo2fdbQueries->update_user_details( $user->ID, array( 'mo_2factor_user_registration_status' => 'MO_2_FACTOR_OTP_DELIVERED_FAILURE' ) );

						}
					}
				}
			}else if  ( isset( $_POST['option'] ) and sanitize_text_field($_POST['option']) == "mo_2factor_validate_user_otp" ) { //validate OTP over email for additional admin

				//validation and sanitization
				$nonce = sanitize_text_field($_POST['mo_2factor_validate_user_otp_nonce']);

				if ( ! wp_verify_nonce( $nonce, 'mo-2factor-validate-user-otp-nonce' ) ) {
					$error = new WP_Error();
					$error->add( 'empty_username', '<strong>' . mo2f_lt( 'ERROR' ) . '</strong>: ' . mo2f_lt( 'Invalid Request.' ) );

					return $error;
				} else {
					$otp_token = '';
					if ( MO2f_Utility::mo2f_check_empty_or_null( $_POST['otp_token'] ) ) {
						update_option( 'mo2f_message', 'All the fields are required. Please enter valid entries.' );
						$this->mo_auth_show_error_message();

						return;
					} else {
						$otp_token = sanitize_text_field( $_POST['otp_token'] );
					}

					$user_email = get_user_meta( $user->ID, 'user_email', true );

					$customer           = new Customer_Setup();
					$mo2f_transactionId = isset( $_SESSION['mo2f_transactionId'] ) && ! empty( $_SESSION['mo2f_transactionId'] ) ? $_SESSION['mo2f_transactionId'] : get_option( 'mo2f_transactionId' );

					$content = json_decode( $customer->validate_otp_token( 'EMAIL', '', $mo2f_transactionId, $otp_token, get_option( 'mo2f_customerKey' ), get_option( 'mo2f_api_key' ) ), true );

					if ( $content['status'] == 'ERROR' ) {
						update_option( 'mo2f_message', $content['message'] );
						$this->mo_auth_show_error_message();
					} else {
						if ( strcasecmp( $content['status'], 'SUCCESS' ) == 0 ) { //OTP validated and generate QRCode
							$this->mo2f_create_user( $user, $user_email );
							delete_user_meta( $user->ID, 'mo_2fa_verify_otp_create_account' );
						} else {
							update_option( 'mo2f_message', 'Invalid OTP. Please try again.' );
							$Mo2fdbQueries->update_user_details( $user->ID, array( 'mo_2factor_user_registration_status' => 'MO_2_FACTOR_OTP_DELIVERED_FAILURE' ) );
							$this->mo_auth_show_error_message();
						}
					}

				}
			}else if  ( isset( $_POST['option'] ) and sanitize_text_field($_POST['option']) == "mo_2factor_send_query" ) { //Help me or support
				$nonce = sanitize_text_field($_POST['mo_2factor_send_query_nonce']);

				if ( ! wp_verify_nonce( $nonce, 'mo-2factor-send-query-nonce' ) ) {
					$error = new WP_Error();
					$error->add( 'empty_username', '<strong>' . mo2f_lt( 'ERROR' ) . '</strong>: ' . mo2f_lt( 'Invalid Request.' ) );

					return $error;
				} else {

					$query = '';
					if ( MO2f_Utility::mo2f_check_empty_or_null( $_POST['EMAIL_MANDATORY'] ) || MO2f_Utility::mo2f_check_empty_or_null( sanitize_text_field($_POST['query'] ) )) {
						update_option( 'mo2f_message', Mo2fConstants:: langTranslate( "EMAIL_MANDATORY" ) );
						$this->mo_auth_show_error_message();

						return;
					} else {
						$query      = sanitize_text_field( $_POST['query'] );
						$email      = sanitize_text_field( $_POST['EMAIL_MANDATORY'] );
						$phone      = sanitize_text_field( $_POST['query_phone'] );
						$contact_us = new Customer_Setup();
						$submited   = json_decode( $contact_us->submit_contact_us( $email, $phone, $query ), true );
						if ( json_last_error() == JSON_ERROR_NONE ) {
							if ( is_array( $submited ) && array_key_exists( 'status', $submited ) && $submited['status'] == 'ERROR' ) {
								update_option( 'mo2f_message', Mo2fConstants:: langTranslate( $submited['message'] ) );
								$this->mo_auth_show_error_message();
							} else {
								if ( $submited == false ) {
									update_option( 'mo2f_message', Mo2fConstants:: langTranslate( "ERROR_WHILE_SUBMITTING_QUERY" ) );
									$this->mo_auth_show_error_message();
								} else {
									update_option( 'mo2f_message', Mo2fConstants:: langTranslate( "QUERY_SUBMITTED_SUCCESSFULLY" ) );
									$this->mo_auth_show_success_message();
								}
							}
						}

					}
				}
			}

			else if(isset( $_POST['option'] ) and sanitize_text_field($_POST['option']) == 'woocommerce_disable_login_prompt' ){
				if(isset($_POST['woocommerce_login_prompt'])){
					update_site_option('mo2f_woocommerce_login_prompt' , true);
				}
				else{
					update_site_option('mo2f_woocommerce_login_prompt' , false);	
				}
			}

			else if  ( isset( $_POST['option'] ) and sanitize_text_field($_POST['option']) == 'mo_auth_advanced_options_save' ) {
				update_option( 'mo2f_message', 'Your settings are saved successfully.' );
				$this->mo_auth_show_success_message();
			}else if  ( isset( $_POST['option'] ) and sanitize_text_field($_POST['option']) == 'mo_auth_login_settings_save' ) {
				$nonce = sanitize_text_field($_POST['mo_auth_login_settings_save_nonce']);
				if ( ! wp_verify_nonce( $nonce, 'mo-auth-login-settings-save-nonce' ) ) {
					$error = new WP_Error();
					$error->add( 'empty_username', '<strong>' . mo2f_lt( 'ERROR' ) . '</strong>: ' . mo2f_lt( 'Invalid Request.' ) );
					return $error;
				} else {
					$mo_2factor_user_registration_status = $Mo2fdbQueries->get_user_detail( 'mo_2factor_user_registration_status', $user->ID );
					if ( $mo_2factor_user_registration_status == 'MO_2_FACTOR_PLUGIN_SETTINGS' or MO2F_IS_ONPREM ) {

						if(sanitize_text_field($_POST['mo2f_login_option']) == 0 && MoWpnsUtility::get_mo2f_db_option('mo2f_enable_2fa_prompt_on_login_page', 'site_option')){
							update_option( 'mo2f_message', Mo2fConstants:: langTranslate( "LOGIN_WITH_2ND_FACTOR" ) );
							$this->mo_auth_show_error_message();
						}else{
							update_option( 'mo2f_login_option', isset( $_POST['mo2f_login_option'] ) ? $_POST['mo2f_login_option'] : 0 );
							update_option( 'mo2f_remember_device', isset( $_POST['mo2f_remember_device'] ) ? $_POST['mo2f_remember_device'] : 0 );
							if ( MoWpnsUtility::get_mo2f_db_option('mo2f_login_option', 'get_option') == 0 ) {
								update_option( 'mo2f_remember_device', 0 );
							}
							if(isset($_POST['mo2f_enable_login_with_2nd_factor']))
							{
								update_option('mo2f_login_option',1);
							}
							update_option( 'mo2f_enable_forgotphone', isset( $_POST['mo2f_forgotphone'] ) ? sanitize_text_field($_POST['mo2f_forgotphone']) : 0 );
							update_option( 'mo2f_enable_login_with_2nd_factor', isset( $_POST['mo2f_login_with_username_and_2factor'] ) ? sanitize_text_field($_POST['mo2f_login_with_username_and_2factor']) : 0 );
							update_option( 'mo2f_enable_xmlrpc', isset( $_POST['mo2f_enable_xmlrpc'] ) ? sanitize_text_field($_POST['mo2f_enable_xmlrpc']) : 0 );
							if ( get_option( 'mo2f_remember_device' ) && ! get_option( 'mo2f_app_secret' ) ) {
								$get_app_secret = new Miniorange_Rba_Attributes();
								$rba_response   = json_decode( $get_app_secret->mo2f_get_app_secret(), true ); //fetch app secret
								if ( json_last_error() == JSON_ERROR_NONE ) {
									if ( $rba_response['status'] == 'SUCCESS' ) {
										update_option( 'mo2f_app_secret', $rba_response['appSecret'] );
									} else {
										update_option( 'mo2f_remember_device', 0 );
										update_option( 'mo2f_message', Mo2fConstants:: langTranslate( "ERROR_WHILE_SAVING_SETTINGS" ) );
										$this->mo_auth_show_error_message();
									}
								} else {
									update_option( 'mo2f_remember_device', 0 );
									update_option( 'mo2f_message', Mo2fConstants:: langTranslate( "ERROR_WHILE_SAVING_SETTINGS" ) );
									$this->mo_auth_show_error_message();
								}
							}
							update_option( 'mo2f_message', Mo2fConstants:: langTranslate( "SETTINGS_SAVED" ) );
							$this->mo_auth_show_success_message();
						}
						
					} else {
						update_option( 'mo2f_message', Mo2fConstants:: langTranslate( "INVALID_REQUEST" ) );
						$this->mo_auth_show_error_message();
					}
				}
			}else if  ( isset( $_POST['option'] ) and sanitize_text_field($_POST['option']) == "mo_auth_sync_sms_transactions" ) {
				$customer = new Customer_Setup();
				$content  = json_decode( $customer->get_customer_transactions( get_option( 'mo2f_customerKey' ), get_option( 'mo2f_api_key' ),get_site_option('mo2f_license_type') ), true );
				if ( ! array_key_exists( 'smsRemaining', $content ) ) {
					$smsRemaining = 0;
				} else {
					$smsRemaining = $content['smsRemaining'];
					if ( $smsRemaining == null ) {
						$smsRemaining = 0;
					}
				}
				update_option( 'mo2f_number_of_transactions', $smsRemaining );
			}


		}

		if ( isset( $_POST['option'] ) and sanitize_text_field($_POST['option']) == 'mo2f_fix_database_error' ) {
			$nonce = sanitize_text_field($_POST['mo2f_fix_database_error_nonce']);
			
			if ( ! wp_verify_nonce( $nonce, 'mo2f-fix-database-error-nonce' ) ) {
				$error = new WP_Error();
				$error->add( 'empty_username', '<strong>' . mo2f_lt( 'ERROR' ) . '</strong>: ' . mo2f_lt( 'Invalid Request.' ) );

				return $error;
			} else {
				global $Mo2fdbQueries;

				$Mo2fdbQueries->database_table_issue();

			}
		}else if  ( isset( $_POST['option'] ) and sanitize_text_field($_POST['option']) == 'mo2f_registration_closed' ) {
			$nonce = sanitize_text_field($_POST['mo2f_registration_closed_nonce']);
			if ( ! wp_verify_nonce( $nonce, 'mo2f-registration-closed-nonce' ) ) {
				$error = new WP_Error();
				$error->add( 'empty_username', '<strong>' . mo2f_lt( 'ERROR' ) . '</strong>: ' . mo2f_lt( 'Invalid Request.' ) );
				return $error;
			} else {
				delete_user_meta( $user->ID, 'register_account_popup' );
				$mo2f_message = 'Please set up the second-factor by clicking on Configure button.';
				update_option( 'mo2f_message', $mo2f_message );
				$this->mo_auth_show_success_message();
			}
		}else if  ( isset( $_POST['option'] ) and sanitize_text_field($_POST['option']) == "mo2f_goto_verifycustomer" ) {
			$nonce = sanitize_text_field($_POST['mo2f_goto_verifycustomer_nonce']);
			if ( ! wp_verify_nonce( $nonce, 'mo2f-goto-verifycustomer-nonce' ) ) {
				$error = new WP_Error();
				$error->add( 'empty_username', '<strong>' . mo2f_lt( 'ERROR' ) . '</strong>: ' . mo2f_lt( 'Invalid Request.' ) );
				return $error;
			} else {
				$Mo2fdbQueries->insert_user( $user_id, array( 'user_id' => $user_id ) );
				update_option( 'mo2f_message', Mo2fConstants:: langTranslate( "ENTER_YOUR_EMAIL_PASSWORD" ) );
				update_option('mo_2factor_user_registration_status','MO_2_FACTOR_VERIFY_CUSTOMER');
			}
		}else if ( isset( $_POST['option'] ) and sanitize_text_field($_POST['option']) == 'mo_2factor_gobackto_registration_page' ) { //back to registration page for admin
			$nonce = sanitize_text_field($_POST['mo_2factor_gobackto_registration_page_nonce']);
			if ( ! wp_verify_nonce( $nonce, 'mo-2factor-gobackto-registration-page-nonce' ) ) {
				$error = new WP_Error();
				$error->add( 'empty_username', '<strong>' . mo2f_lt( 'ERROR' ) . '</strong>: ' . mo2f_lt( 'Invalid Request.' ) );
				return $error;
			} else {
				delete_option( 'mo2f_email' );
				delete_option( 'mo2f_password' );
				update_option( 'mo2f_message', "" );

				MO2f_Utility::unset_session_variables( 'mo2f_transactionId' );
				delete_option( 'mo2f_transactionId' );
				delete_user_meta( $user->ID, 'mo2f_sms_otp_count' );
				delete_user_meta( $user->ID, 'mo2f_email_otp_count' );
				delete_user_meta( $user->ID, 'mo2f_email_otp_count' );
				$Mo2fdbQueries->update_user_details( $user->ID, array( 'mo_2factor_user_registration_status' => 'REGISTRATION_STARTED' ) );
			}

		}else if ( isset( $_POST['option'] ) and sanitize_text_field($_POST['option']) == 'mo2f_skip_feedback' ) {

			$nonce = sanitize_text_field($_POST['mo2f_skip_feedback_nonce']);
			
			if ( ! wp_verify_nonce( $nonce, 'mo2f-skip-feedback-nonce' ) ) {
				$error = new WP_Error();
				$error->add( 'empty_username', '<strong>' . mo2f_lt( 'ERROR' ) . '</strong>: ' . mo2f_lt( 'Invalid Request.' ) );

				return $error;
			} else {
				deactivate_plugins( '/miniorange-2-factor-authentication/miniorange_2_factor_settings.php' );
			}

		}else if ( isset( $_POST['mo2f_feedback'] ) and sanitize_text_field($_POST['mo2f_feedback']) == 'mo2f_feedback' ) {
			
			$nonce = sanitize_text_field($_POST['mo2f_feedback_nonce']);
			
			if ( ! wp_verify_nonce( $nonce, 'mo2f-feedback-nonce' ) ) {
				$error = new WP_Error();
				$error->add( 'empty_username', '<strong>' . mo2f_lt( 'ERROR' ) . '</strong>: ' . mo2f_lt( 'Invalid Request.' ) );

				return $error;
			} else {
				$reasons_not_to_worry_about = array( "Upgrading to Standard / Premium", "Temporary deactivation - Testing" );

				$message = 'Plugin Deactivated:';

				if ( isset( $_POST['deactivate_plugin'] ) ) {
					if ( sanitize_text_field($_POST['query_feedback']) == '' and sanitize_text_field($_POST['deactivate_plugin']) == 'Other Reasons:' ) {
						// feedback add
						update_option( 'mo2f_message', 'Please let us know the reason for deactivation so that we improve the user experience.' );
					} else {

						if ( ! in_array( $_POST['deactivate_plugin'], $reasons_not_to_worry_about ) ) {

							$message .= sanitize_text_field($_POST['deactivate_plugin']);

							if ( sanitize_text_field($_POST['query_feedback']) != '' ) {
								$message .= ':' . sanitize_text_field($_POST['query_feedback']);
							}


							if(sanitize_text_field($_POST['deactivate_plugin']) == "Conflicts with other plugins"){
								$plugin_selected = sanitize_text_field($_POST['plugin_selected']);
								$plugin = MO2f_Utility::get_plugin_name_by_identifier($plugin_selected);

								$message .= ", Plugin selected - " . $plugin . ".";
							}

							$email = $Mo2fdbQueries->get_user_detail( 'mo2f_user_email', $user->ID );
							if ( $email == '' ) {
								$email = $user->user_email;
							}

							$phone = $Mo2fdbQueries->get_user_detail( 'mo2f_user_phone', $user->ID );;

							$contact_us = new Customer_Setup();
							$submited   = json_decode( $contact_us->send_email_alert( $email, $phone, $message ), true );

							if ( json_last_error() == JSON_ERROR_NONE ) {
								if ( is_array( $submited ) && array_key_exists( 'status', $submited ) && $submited['status'] == 'ERROR' ) {
									update_option( 'mo2f_message', Mo2fConstants:: langTranslate( $submited['message'] ) );
									$this->mo_auth_show_error_message();
								} else {
									if ( $submited == false ) {
										update_option( 'mo2f_message', Mo2fConstants:: langTranslate( "ERROR_WHILE_SUBMITTING_QUERY" ) );
										$this->mo_auth_show_error_message();
									} else {
										update_option( 'mo2f_message', Mo2fConstants:: langTranslate( "QUERY_SUBMITTED_SUCCESSFULLY" ) );
										$this->mo_auth_show_success_message();
									}
								}
							}
						}

						deactivate_plugins( '/miniorange-2-factor-authentication/miniorange_2_factor_settings.php' );

					}

				} else {
					update_option( 'mo2f_message', 'Please Select one of the reasons if your reason isnot mention please select Other Reasons' );

				}
			}

		}else if ( isset( $_POST['option'] ) and sanitize_text_field($_POST['option']) == "mo_2factor_resend_user_otp" ) { //resend OTP over email for additional admin and non-admin user
			
			$nonce = sanitize_text_field($_POST['mo_2factor_resend_user_otp_nonce']);
			
			if ( ! wp_verify_nonce( $nonce, 'mo-2factor-resend-user-otp-nonce' ) ) {
				$error = new WP_Error();
				$error->add( 'empty_username', '<strong>' . mo2f_lt( 'ERROR' ) . '</strong>: ' . mo2f_lt( 'Invalid Request.' ) );

				return $error;
			} else {
				$customer = new Customer_Setup();
				$content  = json_decode( $customer->send_otp_token( get_user_meta( $user->ID, 'user_email', true ), 'EMAIL', get_option( 'mo2f_customerKey' ), get_option( 'mo2f_api_key' ) ), true );
				if ( strcasecmp( $content['status'], 'SUCCESS' ) == 0 ) {
					update_option( 'mo2f_message', Mo2fConstants:: langTranslate( "OTP_SENT" ) . ' <b>' . ( get_user_meta( $user->ID, 'user_email', true ) ) . '</b>. ' . Mo2fConstants:: langTranslate( "ENTER_OTP" ) );
					update_user_meta( $user->ID, 'mo_2fa_verify_otp_create_account', $content['txId'] );
					$mo_2factor_user_registration_status = 'MO_2_FACTOR_OTP_DELIVERED_SUCCESS';
					$Mo2fdbQueries->update_user_details( $user->ID, array( 'mo_2factor_user_registration_status' => $mo_2factor_user_registration_status ) );
					$this->mo_auth_show_success_message();
				} else {
					update_option( 'mo2f_message', Mo2fConstants:: langTranslate( "ERROR_IN_SENDING_EMAIL" ) );
					$mo_2factor_user_registration_status = 'MO_2_FACTOR_OTP_DELIVERED_FAILURE';
					$Mo2fdbQueries->update_user_details( $user->ID, array( 'mo_2factor_user_registration_status' => $mo_2factor_user_registration_status ) );
					$this->mo_auth_show_error_message();

				}
			}

		}else if  ( isset( $_POST['option'] ) and ( sanitize_text_field($_POST['option']) == "mo2f_configure_miniorange_authenticator_validate" || sanitize_text_field($_POST['option']) == 'mo_auth_mobile_reconfiguration_complete' ) ) { //mobile registration successfully complete for all users

			$nonce = sanitize_text_field($_POST['mo2f_configure_miniorange_authenticator_validate_nonce']);
			
			if ( ! wp_verify_nonce( $nonce, 'mo2f-configure-miniorange-authenticator-validate-nonce' ) ) {
				$error = new WP_Error();
				$error->add( 'empty_username', '<strong>' . mo2f_lt( 'ERROR' ) . '</strong>: ' . mo2f_lt( 'Invalid Request.' ) );

				return $error;
			} else {
				delete_option( 'mo2f_transactionId' );
				$session_variables = array( 'mo2f_qrCode', 'mo2f_transactionId', 'mo2f_show_qr_code' );
				MO2f_Utility::unset_session_variables( $session_variables );

				$email                     = $Mo2fdbQueries->get_user_detail( 'mo2f_user_email', $user->ID );
					$TwoFA_method_to_configure = sanitize_text_field($_POST['mo2f_method']);
				$enduser                   = new Two_Factor_Setup();
				$current_method            = MO2f_Utility::mo2f_decode_2_factor( $TwoFA_method_to_configure, "server" );

				$response = json_decode( $enduser->mo2f_update_userinfo( $email, $current_method, null, null, null ), true );

				if ( json_last_error() == JSON_ERROR_NONE ) { /* Generate Qr code */
					if ( $response['status'] == 'ERROR' ) {
						update_option( 'mo2f_message', Mo2fConstants:: langTranslate( $response['message'] ) );

						$this->mo_auth_show_error_message();


					} else if ( $response['status'] == 'SUCCESS' ) {

						$selectedMethod = $TwoFA_method_to_configure;

						delete_user_meta( $user->ID, 'mo2f_2FA_method_to_configure' );


						$Mo2fdbQueries->update_user_details( $user->ID, array(
							'mo2f_configured_2FA_method'                        => $selectedMethod,
							'mobile_registration_status'                        => true,
							'mo2f_miniOrangeQRCodeAuthentication_config_status' => true,
							'mo2f_miniOrangeSoftToken_config_status'            => true,
							'mo2f_miniOrangePushNotification_config_status'     => true,
							'user_registration_with_miniorange'                 => 'SUCCESS',
							'mo_2factor_user_registration_status'               => 'MO_2_FACTOR_PLUGIN_SETTINGS'
						) );

						delete_user_meta( $user->ID, 'configure_2FA' );
							//update_user_meta( $user->ID, 'currentMethod' , $selectedMethod);
						mo2f_display_test_2fa_notification($user);

					} else {
						update_option( 'mo2f_message', Mo2fConstants:: langTranslate( "ERROR_DURING_PROCESS" ) );
						$this->mo_auth_show_error_message();
					}

				} else {
					update_option( 'mo2f_message', Mo2fConstants:: langTranslate( "INVALID_REQ" ) );
					$this->mo_auth_show_error_message();
				}
			}
		}else if  ( isset( $_POST['option'] ) and sanitize_text_field($_POST['option']) == 'mo2f_mobile_authenticate_success' ) { // mobile registration for all users(common)

			$nonce = sanitize_text_field($_POST['mo2f_mobile_authenticate_success_nonce']);

			if ( ! wp_verify_nonce( $nonce, 'mo2f-mobile-authenticate-success-nonce' ) ) {
				$error = new WP_Error();
				$error->add( 'empty_username', '<strong>' . mo2f_lt( 'ERROR' ) . '</strong>: ' . mo2f_lt( 'Invalid Request.' ) );

				return $error;
			} else {

				if ( current_user_can( 'manage_options' ) ) {
					update_option( 'mo2f_message', Mo2fConstants:: langTranslate( "COMPLETED_TEST" ) );
				} else {
					update_option( 'mo2f_message', Mo2fConstants:: langTranslate( "COMPLETED_TEST" ) );
				}

				$session_variables = array( 'mo2f_qrCode', 'mo2f_transactionId', 'mo2f_show_qr_code' );
				MO2f_Utility::unset_session_variables( $session_variables );

				delete_user_meta( $user->ID, 'test_2FA' );
				$this->mo_auth_show_success_message();
			}
		}else if  ( isset( $_POST['option'] ) and sanitize_text_field($_POST['option']) == 'mo2f_mobile_authenticate_error' ) { //mobile registration failed for all users(common)
			$nonce = sanitize_text_field($_POST['mo2f_mobile_authenticate_error_nonce']);

			if ( ! wp_verify_nonce( $nonce, 'mo2f-mobile-authenticate-error-nonce' ) ) {
				$error = new WP_Error();
				$error->add( 'empty_username', '<strong>' . mo2f_lt( 'ERROR' ) . '</strong>: ' . mo2f_lt( 'Invalid Request.' ) );

				return $error;
			} else {
				update_option( 'mo2f_message', Mo2fConstants:: langTranslate( "AUTHENTICATION_FAILED" ) );
				MO2f_Utility::unset_session_variables( 'mo2f_show_qr_code' );
				$this->mo_auth_show_error_message();
			}

		}else if  ( isset( $_POST['option'] ) and sanitize_text_field($_POST['option']) == "mo_auth_setting_configuration" )  // redirect to setings page
		{	
			
			$Mo2fdbQueries->update_user_details( $user->ID, array( 'mo_2factor_user_registration_status' => 'MO_2_FACTOR_PLUGIN_SETTINGS' ) );

		}else if  ( isset( $_POST['option'] ) and sanitize_text_field($_POST['option']) == "mo_auth_refresh_mobile_qrcode" ) { // refrsh Qrcode for all users
			
			$nonce = sanitize_text_field($_POST['mo_auth_refresh_mobile_qrcode_nonce']);

			if ( ! wp_verify_nonce( $nonce, 'mo-auth-refresh-mobile-qrcode-nonce' ) ) {
				$error = new WP_Error();
				$error->add( 'empty_username', '<strong>' . mo2f_lt( 'ERROR' ) . '</strong>: ' . mo2f_lt( 'Invalid Request.' ) );

				return $error;
			} else {
				$session_id = sanitize_text_field($_POST['mo2f_session_id']);
				$twofactor_transactions = new Mo2fDB;
				$exceeded = $twofactor_transactions->check_alluser_limit_exceeded($user_id);

				if($exceeded){
					update_option( 'mo2f_message', Mo2fConstants:: langTranslate( "USER_LIMIT_EXCEEDED" ) );
					$this->mo_auth_show_error_message();
					return;
				}

				$mo_2factor_user_registration_status = get_option( 'mo_2factor_user_registration_status');
				if ( in_array( $mo_2factor_user_registration_status, array(
					'MO_2_FACTOR_INITIALIZE_TWO_FACTOR',
					'MO_2_FACTOR_INITIALIZE_MOBILE_REGISTRATION',
					'MO_2_FACTOR_PLUGIN_SETTINGS'
				) ) ) {
					$email = $Mo2fdbQueries->get_user_detail( 'mo2f_user_email', $user->ID );
					$this->mo2f_get_qr_code_for_mobile( $email, $user->ID, $session_id );

				} else {
					update_option( 'mo2f_message', Mo2fConstants:: langTranslate( "REGISTER_WITH_MO" ) );
					$this->mo_auth_show_error_message();

				}
			}
		}else if  ( isset( $_POST['mo2fa_register_to_upgrade_nonce'] ) ) { //registration with miniOrange for upgrading
			$nonce = sanitize_text_field($_POST['mo2fa_register_to_upgrade_nonce']);
			if ( ! wp_verify_nonce( $nonce, 'miniorange-2-factor-user-reg-to-upgrade-nonce' ) ) {
				update_option( 'mo2f_message', Mo2fConstants:: langTranslate( "INVALID_REQ" ) );
			} else {
				$requestOrigin = sanitize_text_field($_POST['requestOrigin']);
				update_option( 'mo2f_customer_selected_plan', $requestOrigin );
				header( 'Location: admin.php?page=mo_2fa_account' );

			}
		}else if ( isset( $_POST['miniorange_get_started'] ) && isset( $_POST['miniorange_user_reg_nonce'] ) ) { //registration with miniOrange for additional admin and non-admin
			$nonce = sanitize_text_field($_POST['miniorange_user_reg_nonce']);
			$Mo2fdbQueries->insert_user( $user_id, array( 'user_id' => $user_id ) );
			if ( ! wp_verify_nonce( $nonce, 'miniorange-2-factor-user-reg-nonce' ) ) {
				update_option( 'mo2f_message', Mo2fConstants:: langTranslate( "INVALID_REQ" ) );
			} else {
				$email = '';
				if ( MO2f_Utility::mo2f_check_empty_or_null( $_POST['mo_useremail'] ) ) {
					update_option( 'mo2f_message', Mo2fConstants:: langTranslate( "ENTER_EMAILID" ) );

					return;
				} else {
					$email = sanitize_email( $_POST['mo_useremail'] );
				}

				if ( ! MO2f_Utility::check_if_email_is_already_registered( $email ) ) {
					update_user_meta( $user->ID, 'user_email', $email );

					$enduser    = new Two_Factor_Setup();
					$check_user = json_decode( $enduser->mo_check_user_already_exist( $email ), true );

					if ( json_last_error() == JSON_ERROR_NONE ) {
						if ( $check_user['status'] == 'ERROR' ) {
							update_option( 'mo2f_message', Mo2fConstants:: langTranslate( $check_user['message'] ) );
							$this->mo_auth_show_error_message();

							return;
						} else if ( strcasecmp( $check_user['status'], 'USER_FOUND_UNDER_DIFFERENT_CUSTOMER' ) == 0 ) {
							update_option( 'mo2f_message', Mo2fConstants:: langTranslate( "EMAIL_IN_USE" ) );
							$this->mo_auth_show_error_message();

							return;
						} else if ( strcasecmp( $check_user['status'], 'USER_FOUND' ) == 0 || strcasecmp( $check_user['status'], 'USER_NOT_FOUND' ) == 0 ) {


							$enduser = new Customer_Setup();
							$content = json_decode( $enduser->send_otp_token( $email, 'EMAIL', get_option( 'mo2f_customerKey' ), get_option( 'mo2f_api_key' ) ), true );
							if ( strcasecmp( $content['status'], 'SUCCESS' ) == 0 ) {
								update_option( 'mo2f_message', Mo2fConstants:: langTranslate( "OTP_SENT" ) . ' <b>' . ( $email ) . '</b>. ' . Mo2fConstants:: langTranslate( "ENTER_OTP" ) );
								$_SESSION['mo2f_transactionId'] = $content['txId'];
								update_option( 'mo2f_transactionId', $content['txId'] );
								$mo_2factor_user_registration_status = 'MO_2_FACTOR_OTP_DELIVERED_SUCCESS';
								$Mo2fdbQueries->update_user_details( $user->ID, array( 'mo_2factor_user_registration_status' => $mo_2factor_user_registration_status ) );
								update_user_meta( $user->ID, 'mo_2fa_verify_otp_create_account', $content['txId'] );
								$this->mo_auth_show_success_message();
							} else {
								$mo_2factor_user_registration_status = 'MO_2_FACTOR_OTP_DELIVERED_FAILURE';
								$Mo2fdbQueries->update_user_details( $user->ID, array( 'mo_2factor_user_registration_status' => $mo_2factor_user_registration_status ) );
								update_option( 'mo2f_message', Mo2fConstants:: langTranslate( "ERROR_IN_SENDING_OTP_OVER_EMAIL" ) );
								$this->mo_auth_show_error_message();
							}


						}
					}
				} else {
					update_option( 'mo2f_message', Mo2fConstants:: langTranslate( "EMAIL_IN_USE" ) );
					$this->mo_auth_show_error_message();
				}
			}
		}else if  ( isset( $_POST['option'] ) and sanitize_text_field($_POST['option']) == 'mo_2factor_backto_user_registration' ) { //back to registration page for additional admin and non-admin
			$nonce = sanitize_text_field($_POST['mo_2factor_backto_user_registration_nonce']);

			if ( ! wp_verify_nonce( $nonce, 'mo-2factor-backto-user-registration-nonce' ) ) {
				$error = new WP_Error();
				$error->add( 'empty_username', '<strong>' . mo2f_lt( 'ERROR' ) . '</strong>: ' . mo2f_lt( 'Invalid Request.' ) );

				return $error;
			} else {
				delete_user_meta( $user->ID, 'user_email' );
				$Mo2fdbQueries->delete_user_details( $user->ID );
				MO2f_Utility::unset_session_variables( 'mo2f_transactionId' );
				delete_option( 'mo2f_transactionId' );
			}

		}else if  ( isset( $_POST['option'] ) && $_POST['option'] == 'mo2f_validate_soft_token' ) {  // validate Soft Token during test for all users
			
			$nonce = sanitize_text_field($_POST['mo2f_validate_soft_token_nonce']);


			if ( ! wp_verify_nonce( $nonce, 'mo2f-validate-soft-token-nonce' ) ) {
				$error = new WP_Error();
				$error->add( 'empty_username', '<strong>' . mo2f_lt( 'ERROR' ) . '</strong>: ' . mo2f_lt( 'Invalid Request.' ) );

				return $error;
			} else {
				$otp_token = '';
				if ( MO2f_Utility::mo2f_check_empty_or_null( $_POST['otp_token'] ) ) {
					update_option( 'mo2f_message', Mo2fConstants:: langTranslate( "ENTER_VALUE" ) );
					$this->mo_auth_show_error_message();

					return;
				} else {
					$otp_token = sanitize_text_field( $_POST['otp_token'] );
				}
				$email    = $Mo2fdbQueries->get_user_detail( 'mo2f_user_email', $user->ID );
				$customer = new Customer_Setup();
				$content  = json_decode( $customer->validate_otp_token( 'SOFT TOKEN', $email, null, $otp_token, get_option( 'mo2f_customerKey' ), get_option( 'mo2f_api_key' ) ), true );
				if ( $content['status'] == 'ERROR' ) {
					update_option( 'mo2f_message', Mo2fConstants:: langTranslate( $content['message'] ) );
					$this->mo_auth_show_error_message();
				} else {
					if ( strcasecmp( $content['status'], 'SUCCESS' ) == 0 ) { //OTP validated and generate QRCode
						update_option( 'mo2f_message', Mo2fConstants:: langTranslate( "COMPLETED_TEST" ) );

						delete_user_meta( $user->ID, 'test_2FA' );
						$this->mo_auth_show_success_message();


					} else {  // OTP Validation failed.
						update_option( 'mo2f_message', Mo2fConstants:: langTranslate( "INVALID_OTP" ) );
						$this->mo_auth_show_error_message();

					}
				}
			}
		}
		
		else if  ( isset( $_POST['option'] ) && sanitize_text_field($_POST['option']) == 'mo2f_validate_otp_over_Telegram' ) { //validate otp over Telegram
			
			$nonce = sanitize_text_field($_POST['mo2f_validate_otp_over_Telegram_nonce']);

			if ( ! wp_verify_nonce( $nonce, 'mo2f-validate-otp-over-Telegram-nonce' ) ) {
				$error = new WP_Error();
				$error->add( 'empty_username', '<strong>' . mo2f_lt( 'ERROR' ) . '</strong>: ' . mo2f_lt( 'Invalid Request.' ) );

				return $error;
			} else {
				
				$otp 			= sanitize_text_field($_POST['otp_token']);
				$otpToken 		= get_user_meta($user->ID,'mo2f_otp_token',true);

				$time  			= get_user_meta($user->ID,'mo2f_telegram_time',true);
				$accepted_time	= time()-300;
				$time 			= (int)$time;
				global $Mo2fdbQueries;
				if($otp == $otpToken)
				{
					if($accepted_time<$time){
						update_option( 'mo2f_message', Mo2fConstants:: langTranslate( "COMPLETED_TEST" ) );
						delete_user_meta( $user->ID, 'test_2FA' );
						delete_user_meta($user->ID,'mo2f_telegram_time');

						$this->mo_auth_show_success_message();

					}
					else
					{
						update_option( 'mo2f_message', 'OTP has been expired please initiate another transaction for verification' );
						delete_user_meta( $user->ID, 'test_2FA' );
						$this->mo_auth_show_error_message();

					}
				}
				else
				{
					update_option( 'mo2f_message', 'Wrong OTP Please try again.' );
					$this->mo_auth_show_error_message();

				}
			}
		}
		else if  ( isset( $_POST['option'] ) && sanitize_text_field($_POST['option']) == 'mo2f_validate_otp_over_sms' ) { //validate otp over sms and phone call during test for all users
			
			$nonce = sanitize_text_field($_POST['mo2f_validate_otp_over_sms_nonce']);

			if ( ! wp_verify_nonce( $nonce, 'mo2f-validate-otp-over-sms-nonce' ) ) {
				$error = new WP_Error();
				$error->add( 'empty_username', '<strong>' . mo2f_lt( 'ERROR' ) . '</strong>: ' . mo2f_lt( 'Invalid Request.' ) );

				return $error;
			} else {
				$otp_token = '';
				if ( MO2f_Utility::mo2f_check_empty_or_null( $_POST['otp_token'] ) ) {
					update_option( 'mo2f_message', Mo2fConstants:: langTranslate( "ENTER_VALUE" ) );
					$this->mo_auth_show_error_message();

					return;
				} else {
					$otp_token = sanitize_text_field( $_POST['otp_token'] );
				}
				$mo2f_transactionId 	   = get_user_meta($user->ID, 'mo2f_transactionId', true);
				$email                     = $Mo2fdbQueries->get_user_detail( 'mo2f_user_email', $user->ID );
				$selected_2_2factor_method = $Mo2fdbQueries->get_user_detail( 'mo2f_configured_2FA_method', $user->ID );
				$customer                  = new Customer_Setup();
				$content                   = json_decode( $customer->validate_otp_token($selected_2_2factor_method , $email, $mo2f_transactionId, $otp_token, get_option( 'mo2f_customerKey' ), get_option( 'mo2f_api_key' ) ), true );

				if ( $content['status'] == 'ERROR' ) {
					update_option( 'mo2f_message', Mo2fConstants:: langTranslate( $content['message'] ) );
					$this->mo_auth_show_error_message();
				} else {
					if ( strcasecmp( $content['status'], 'SUCCESS' ) == 0 ) { //OTP validated
						if ( current_user_can( 'manage_options' ) ) {
							update_option( 'mo2f_message', Mo2fConstants:: langTranslate( "COMPLETED_TEST" ) );
						} else {
							update_option( 'mo2f_message', Mo2fConstants::langTranslate( "COMPLETED_TEST" ) );
						}

						delete_user_meta( $user->ID, 'test_2FA' );
						$this->mo_auth_show_success_message();

					} else {
						update_option( 'mo2f_message', Mo2fConstants:: langTranslate( "INVALID_OTP" ) );
						$this->mo_auth_show_error_message();
					}

				}
			}
		}else if  ( isset( $_POST['option'] ) && sanitize_text_field($_POST['option']) == 'mo2f_out_of_band_success' ) {
			$nonce = sanitize_text_field($_POST['mo2f_out_of_band_success_nonce']);

			if ( ! wp_verify_nonce( $nonce, 'mo2f-out-of-band-success-nonce' ) ) {
				$error = new WP_Error();
				$error->add( 'empty_username', '<strong>' . mo2f_lt( 'ERROR' ) . '</strong>: ' . mo2f_lt( 'Invalid Request.' ) );

				return $error;
			} else {
				$show = 1;
				if(MO2F_IS_ONPREM )
				{
					$txid   = isset($_POST['TxidEmail'])? sanitize_email($_POST['TxidEmail']):null;
					$status = get_option($txid);
					if($status != '')
					{
						if($status != 1)
						{
							update_option( 'mo2f_message', Mo2fConstants:: langTranslate( "INVALID_EMAIL_VER_REQ" ));
							$show = 0;
							$this->mo_auth_show_error_message();

						}
					}
				}
				$mo2f_configured_2FA_method           = $Mo2fdbQueries->get_user_detail( 'mo2f_configured_2FA_method', $user->ID );
				if(MO2F_IS_ONPREM and $mo2f_configured_2FA_method == 'OUT OF BAND EMAIL')
					$mo2f_configured_2FA_method = 'Email Verification';

				$mo2f_EmailVerification_config_status = $Mo2fdbQueries->get_user_detail( 'mo2f_EmailVerification_config_status', $user->ID );
				if ( ! current_user_can( 'manage_options' ) && $mo2f_configured_2FA_method == 'OUT OF BAND EMAIL' ) {
					if ( $mo2f_EmailVerification_config_status ) {
						update_option( 'mo2f_message', Mo2fConstants:: langTranslate( "COMPLETED_TEST" ) );
					} else {
						$email    = $Mo2fdbQueries->get_user_detail( 'mo2f_user_email', $user->ID );
						$enduser  = new Two_Factor_Setup();
						$response = json_decode( $enduser->mo2f_update_userinfo( $email, $mo2f_configured_2FA_method, null, null, null ), true );
						update_option( 'mo2f_message', '<b> ' . Mo2fConstants:: langTranslate( "EMAIL_VERFI" ) . '</b> ' . Mo2fConstants:: langTranslate( "SET_AS_2ND_FACTOR" ) );
					}
				} else {
					update_option( 'mo2f_message', Mo2fConstants:: langTranslate( "COMPLETED_TEST" ) );
				}
				delete_user_meta( $user->ID, 'test_2FA' );
				$Mo2fdbQueries->update_user_details( $user->ID, array(
					'mo_2factor_user_registration_status'  => 'MO_2_FACTOR_PLUGIN_SETTINGS',
					'mo2f_EmailVerification_config_status' => true
				) );
				if($show)
					$this->mo_auth_show_success_message();
			}


		}else if  ( isset( $_POST['option'] ) and sanitize_text_field($_POST['option']) == 'mo2f_out_of_band_error' ) { //push and out of band email denied
			$nonce = sanitize_text_field($_POST['mo2f_out_of_band_error_nonce']);

			if ( ! wp_verify_nonce( $nonce, 'mo2f-out-of-band-error-nonce' ) ) {
				$error = new WP_Error();
				$error->add( 'empty_username', '<strong>' . mo2f_lt( 'ERROR' ) . '</strong>: ' . mo2f_lt( 'Invalid Request.' ) );

				return $error;
			} else {
				update_option( 'mo2f_message', Mo2fConstants:: langTranslate( "DENIED_REQUEST" ) );
				delete_user_meta( $user->ID, 'test_2FA' );
				$Mo2fdbQueries->update_user_details( $user->ID, array(
					'mo_2factor_user_registration_status'  => 'MO_2_FACTOR_PLUGIN_SETTINGS',
					'mo2f_EmailVerification_config_status' => true
				) );
				$this->mo_auth_show_error_message();
			}

		}else if  ( isset( $_POST['option'] ) && sanitize_text_field($_POST['option']) == 'mo2f_duo_authenticator_success_form' ) {
			$nonce = sanitize_text_field($_POST['mo2f_duo_authenticator_success_nonce']);
		
			if ( ! wp_verify_nonce( $nonce, 'mo2f-duo-authenticator-success-nonce' ) ) {
				$error = new WP_Error();
				$error->add( 'empty_username', '<strong>' . mo2f_lt( 'ERROR' ) . '</strong>: ' . mo2f_lt( 'Invalid Request.' ) );

				return $error;
			}else{

				update_option( 'mo2f_message', Mo2fConstants:: langTranslate( "COMPLETED_TEST" ) );
				
				delete_user_meta( $user->ID, 'test_2FA' );
				$Mo2fdbQueries->update_user_details( $user->ID, array(
					'mo_2factor_user_registration_status'  => 'MO_2_FACTOR_PLUGIN_SETTINGS',
					'mo2f_DuoAuthenticator_config_status' => true
				) );
				
				$this->mo_auth_show_success_message();

			}	
		}else if  ( isset( $_POST['option'] ) and sanitize_text_field($_POST['option']) == 'mo2f_duo_authenticator_error' ) { //push and out of band email denied
 		$nonce = sanitize_text_field($_POST['mo2f_duo_authentcator_error_nonce']);
		   
			if ( ! wp_verify_nonce( $nonce, 'mo2f-duo-authenticator-error-nonce' ) ) {
				$error = new WP_Error();
				$error->add( 'empty_username', '<strong>' . mo2f_lt( 'ERROR' ) . '</strong>: ' . mo2f_lt( 'Invalid Request.' ) );

				return $error;
			} else {
				global 	$Mo2fdbQueries;
				
				update_option( 'mo2f_message', Mo2fConstants:: langTranslate( "DENIED_DUO_REQUEST" ) );
				delete_user_meta( $user->ID, 'test_2FA' );
				$Mo2fdbQueries->update_user_details( $user->ID, array(
									'mobile_registration_status' =>false,
							     ) );	
				$this->mo_auth_show_error_message();
			}

		}
		else if  ( isset( $_POST['option'] ) && sanitize_text_field($_POST['option']) == 'mo2f_validate_google_authy_test' ) {
			
			$nonce = sanitize_text_field($_POST['mo2f_validate_google_authy_test_nonce']);

			if ( ! wp_verify_nonce( $nonce, 'mo2f-validate-google-authy-test-nonce' ) ) {
				$error = new WP_Error();
				$error->add( 'empty_username', '<strong>' . mo2f_lt( 'ERROR' ) . '</strong>: ' . mo2f_lt( 'Invalid Request.' ) );

				return $error;
			} else {
				$otp_token = '';
				if ( MO2f_Utility::mo2f_check_empty_or_null( $_POST['otp_token'] ) ) {
					update_option( 'mo2f_message', Mo2fConstants:: langTranslate( "ENTER_VALUE" ) );
					$this->mo_auth_show_error_message();

					return;
				} else {
					$otp_token = sanitize_text_field( $_POST['otp_token'] );
				}
				$email    = $Mo2fdbQueries->get_user_detail( 'mo2f_user_email', $user->ID );

				$customer = new Customer_Setup();
				$content  = json_decode( $customer->validate_otp_token( 'GOOGLE AUTHENTICATOR', $email, null, $otp_token, get_option( 'mo2f_customerKey' ), get_option( 'mo2f_api_key' ) ), true );
				if ( json_last_error() == JSON_ERROR_NONE ) {

					if ( strcasecmp( $content['status'], 'SUCCESS' ) == 0 ) { //Google OTP validated

						if ( current_user_can( 'manage_options' ) ) {
							update_option( 'mo2f_message', Mo2fConstants:: langTranslate( "COMPLETED_TEST" ) );
						} else {
							update_option( 'mo2f_message', Mo2fConstants:: langTranslate( "COMPLETED_TEST" ) );
						}

						delete_user_meta( $user->ID, 'test_2FA' );
						$this->mo_auth_show_success_message();


					} else {  // OTP Validation failed.
						update_option( 'mo2f_message', Mo2fConstants:: langTranslate( "INVALID_OTP" ) );
						$this->mo_auth_show_error_message();

					}
				} else {
					update_option( 'mo2f_message', Mo2fConstants:: langTranslate( "ERROR_WHILE_VALIDATING_OTP" ) );
					$this->mo_auth_show_error_message();

				}
			}
		}else if  ( isset( $_POST['option'] ) && sanitize_text_field($_POST['option']) == 'mo2f_validate_otp_over_email' ) {
			$nonce = sanitize_text_field($_POST['mo2f_validate_otp_over_email_test_nonce']);

			if ( ! wp_verify_nonce( $nonce, 'mo2f-validate-otp-over-email-test-nonce' ) ) {
				$error = new WP_Error();
				$error->add( 'empty_username', '<strong>' . mo2f_lt( 'ERROR' ) . '</strong>: ' . mo2f_lt( 'Invalid Request.' ) );

				return $error;
			} else {
				$otp_token = '';
				if ( MO2f_Utility::mo2f_check_empty_or_null( $_POST['otp_token'] ) ) {
					update_option( 'mo2f_message', Mo2fConstants:: langTranslate( "ENTER_VALUE" ) );
					$this->mo_auth_show_error_message();

					return;
				} else {
					$otp_token = sanitize_text_field( $_POST['otp_token'] );
				}
				$email    = $Mo2fdbQueries->get_user_detail( 'mo2f_user_email', $user->ID );

				$customer = new Customer_Setup();

				$mo2f_transactionId = get_user_meta($user->ID, 'mo2f_transactionId', true);
				$content  = json_decode( $customer->validate_otp_token( 'OTP_OVER_EMAIL', $email, $mo2f_transactionId, $otp_token, get_option( 'mo2f_customerKey' ), get_option( 'mo2f_api_key' ) ), true );
				if ( json_last_error() == JSON_ERROR_NONE ) {

					if ( strcasecmp( $content['status'], 'SUCCESS' ) == 0 ) { //Google OTP validated

						update_option( 'mo2f_message', Mo2fConstants:: langTranslate( "COMPLETED_TEST" ) );
						delete_user_meta( $user->ID, 'configure_2FA');
						$Mo2fdbQueries->update_user_details( $user->ID, array( 'mo2f_configured_2FA_method' => 'OTP Over Email','mo2f_OTPOverEmail_config_status'=>true ) );
						delete_user_meta( $user->ID, 'test_2FA' );
						$this->mo_auth_show_success_message();


					} else {  // OTP Validation failed.
						update_option( 'mo2f_message', Mo2fConstants:: langTranslate( "INVALID_OTP" ) );
						$this->mo_auth_show_error_message();

					}
				} else {
					update_option( 'mo2f_message', Mo2fConstants:: langTranslate( "ERROR_WHILE_VALIDATING_OTP" ) );
					$this->mo_auth_show_error_message();

				}
			}
		}else if  ( isset( $_POST['option'] ) && sanitize_text_field($_POST['option']) == 'mo2f_google_appname' ) {
			$nonce = sanitize_text_field($_POST['mo2f_google_appname_nonce']);

			if ( ! wp_verify_nonce( $nonce, 'mo2f-google-appname-nonce' ) ) {
				$error = new WP_Error();
				$error->add( 'empty_username', '<strong>' . mo2f_lt( 'ERROR' ) . '</strong>: ' . mo2f_lt( 'Invalid Request.' ) );

				return $error;
			} else {	
				
				update_option('mo2f_google_appname',((isset($_POST['mo2f_google_auth_appname']) && $_POST['mo2f_google_auth_appname']!='') ? sanitize_text_field($_POST['mo2f_google_auth_appname']) : 'miniOrangeAu'));
			}

		}else if  ( isset( $_POST['option'] ) && sanitize_text_field($_POST['option']) == 'mo2f_configure_google_authenticator_validate' ) {
			$nonce = sanitize_text_field($_POST['mo2f_configure_google_authenticator_validate_nonce']);

			if ( ! wp_verify_nonce( $nonce, 'mo2f-configure-google-authenticator-validate-nonce' ) ) {
				$error = new WP_Error();
				$error->add( 'empty_username', '<strong>' . mo2f_lt( 'ERROR' ) . '</strong>: ' . mo2f_lt( 'Invalid Request.' ) );

				return $error;
			} else {	
				$otpToken  = sanitize_text_field($_POST['google_token']);
				$ga_secret = isset( $_POST['google_auth_secret'] ) ? sanitize_text_field($_POST['google_auth_secret']) : null;
				
				if ( MO2f_Utility::mo2f_check_number_length( $otpToken ) ) {
					$email           = $Mo2fdbQueries->get_user_detail( 'mo2f_user_email', $user->ID );
					$user            = wp_get_current_user();
					$email = ($email=='')?$user->user_email:$email;
					$twofactor_transactions = new Mo2fDB;
					$exceeded = $twofactor_transactions->check_alluser_limit_exceeded($user_id);

					if($exceeded){
						update_option( 'mo2f_message', Mo2fConstants:: langTranslate( "USER_LIMIT_EXCEEDED" ) );
						$this->mo_auth_show_error_message();
						return;
					}
					$google_auth     = new Miniorange_Rba_Attributes();
					$google_response = json_decode( $google_auth->mo2f_validate_google_auth( $email, $otpToken, $ga_secret ), true );

					if ( json_last_error() == JSON_ERROR_NONE ) {
						if ( $google_response['status'] == 'SUCCESS' ) {
							$enduser  = new Two_Factor_Setup();
							$response = json_decode( $enduser->mo2f_update_userinfo( $email, "GOOGLE AUTHENTICATOR", null, null, null ), true );
							if ( json_last_error() == JSON_ERROR_NONE ) {

								if ( $response['status'] == 'SUCCESS' ) {

									delete_user_meta( $user->ID, 'mo2f_2FA_method_to_configure' );

									delete_user_meta( $user->ID, 'configure_2FA' );

									$Mo2fdbQueries->update_user_details( $user->ID, array(
										'mo2f_user_email'                        => $email,
										'mo2f_GoogleAuthenticator_config_status' => true,
										'mo2f_AuthyAuthenticator_config_status'  => false,
										'mo2f_configured_2FA_method'             => "Google Authenticator",
										'user_registration_with_miniorange'      => 'SUCCESS',
										'mo_2factor_user_registration_status'    => 'MO_2_FACTOR_PLUGIN_SETTINGS'
									) );

									update_user_meta( $user->ID, 'mo2f_external_app_type', "Google Authenticator" );
									mo2f_display_test_2fa_notification($user);
									delete_user_meta($user->ID, 'mo2f_google_auth');

								} else {
									update_option( 'mo2f_message', Mo2fConstants:: langTranslate( "ERROR_DURING_PROCESS" ) );
									$this->mo_auth_show_error_message();

								}
							} else {
								update_option( 'mo2f_message', Mo2fConstants:: langTranslate( "ERROR_DURING_PROCESS" ) );
								$this->mo_auth_show_error_message();

							}
						} else {
							update_option( 'mo2f_message', Mo2fConstants:: langTranslate( "ERROR_IN_SENDING_OTP_CAUSES" ) . '<br>1. ' . Mo2fConstants:: langTranslate( "INVALID_OTP" ) . '<br>2. ' . Mo2fConstants:: langTranslate( "APP_TIME_SYNC" ) . '<br>3.' . Mo2fConstants::langTranslate( "SERVER_TIME_SYNC" ));
							$this->mo_auth_show_error_message();

						}
					} else {
						update_option( 'mo2f_message', Mo2fConstants:: langTranslate( "ERROR_WHILE_VALIDATING_USER" ) );
						$this->mo_auth_show_error_message();

					}
				} else {
					update_option( 'mo2f_message', Mo2fConstants:: langTranslate( "ONLY_DIGITS_ALLOWED" ) );
					$this->mo_auth_show_error_message();

				}
			}
		}else if(isset( $_POST['option'] ) && sanitize_text_field($_POST['option']) == 'mo2f_configure_duo_authenticator_validate_nonce'){
           
            $nonce = sanitize_text_field($_POST['mo2f_configure_duo_authenticator_validate_nonce']);
		
			if ( ! wp_verify_nonce( $nonce, 'mo2f-configure-duo-authenticator-validate-nonce' ) ) {
				$error = new WP_Error();
				$error->add( 'empty_username', '<strong>' . mo2f_lt( 'ERROR' ) . '</strong>: ' . mo2f_lt( 'Invalid Request.' ) );

				return $error;
			}else{

				delete_user_meta( $user->ID, 'mo2f_2FA_method_to_configure' );

				delete_user_meta( $user->ID, 'configure_2FA' );
                delete_user_meta($user->ID,'user_not_enroll');
				$Mo2fdbQueries->update_user_details( $user->ID, array(
					'mo2f_DuoAuthenticator_config_status' => true,
					
					'mo2f_configured_2FA_method'             => "Duo Authenticator",
					'user_registration_with_miniorange'      => 'SUCCESS',
					'mo_2factor_user_registration_status'    => 'MO_2_FACTOR_PLUGIN_SETTINGS'
				) );

				update_user_meta( $user->ID, 'mo2f_external_app_type', "Duo Authenticator" );
                update_option( 'mo2f_message', Mo2fConstants:: langTranslate( "VALIDATE_DUO" ) );
				$this->mo_auth_show_success_message();
			}	

		}
		else if  ( isset( $_POST['option'] ) && sanitize_text_field($_POST['option']) == 'mo2f_configure_authy_authenticator' ) {
			$nonce = sanitize_text_field($_POST['mo2f_configure_authy_authenticator_nonce']);

			if ( ! wp_verify_nonce( $nonce, 'mo2f-configure-authy-authenticator-nonce' ) ) {
				$error = new WP_Error();
				$error->add( 'empty_username', '<strong>' . mo2f_lt( 'ERROR' ) . '</strong>: ' . mo2f_lt( 'Invalid Request.' ) );

				return $error;
			} else {	
				$authy          = new Miniorange_Rba_Attributes();
				$user_email     = $Mo2fdbQueries->get_user_detail( 'mo2f_user_email', $user->ID );
				$authy_response = json_decode( $authy->mo2f_google_auth_service( $user_email ), true );
				if ( json_last_error() == JSON_ERROR_NONE ) {
					if ( $authy_response['status'] == 'SUCCESS' ) {
						$mo2f_authy_keys                      = array();
						$mo2f_authy_keys['authy_qrCode']      = $authy_response['qrCodeData'];
						$mo2f_authy_keys['mo2f_authy_secret'] = $authy_response['secret'];
						$_SESSION['mo2f_authy_keys']          = $mo2f_authy_keys;
					} else {
						update_option( 'mo2f_message', Mo2fConstants:: langTranslate( "ERROR_DURING_USER_REGISTRATION" ) );
						$this->mo_auth_show_error_message();
					}
				} else {
					update_option( 'mo2f_message', Mo2fConstants:: langTranslate( "ERROR_DURING_USER_REGISTRATION" ) );
					$this->mo_auth_show_error_message();
				}
			}
		}else if( isset( $_POST['option'] ) && sanitize_text_field($_POST['option']) == 'mo2f_configure_authy_authenticator_validate' ) {
			$nonce = sanitize_text_field($_POST['mo2f_configure_authy_authenticator_validate_nonce']);

			if ( ! wp_verify_nonce( $nonce, 'mo2f-configure-authy-authenticator-validate-nonce' ) ) {
				$error = new WP_Error();
				$error->add( 'empty_username', '<strong>' . mo2f_lt( 'ERROR' ) . '</strong>: ' . mo2f_lt( 'Invalid Request.' ) );

				return $error;
			} else {	
				$otpToken     = sanitize_text_field($_POST['mo2f_authy_token']);
				$authy_secret = isset( $_POST['mo2f_authy_secret'] ) ? sanitize_text_field($_POST['mo2f_authy_secret']) : null;
				if ( MO2f_Utility::mo2f_check_number_length( $otpToken ) ) {
					$email          = $Mo2fdbQueries->get_user_detail( 'mo2f_user_email', $user->ID );
					$authy_auth     = new Miniorange_Rba_Attributes();
					$authy_response = json_decode( $authy_auth->mo2f_validate_google_auth( $email, $otpToken, $authy_secret ), true );
					if ( json_last_error() == JSON_ERROR_NONE ) {
						if ( $authy_response['status'] == 'SUCCESS' ) {
							$enduser  = new Two_Factor_Setup();
							$response = json_decode( $enduser->mo2f_update_userinfo( $email, 'GOOGLE AUTHENTICATOR', null, null, null ), true );
							if ( json_last_error() == JSON_ERROR_NONE ) {

								if ( $response['status'] == 'SUCCESS' ) {
									$Mo2fdbQueries->update_user_details( $user->ID, array(
										'mo2f_GoogleAuthenticator_config_status' => false,
										'mo2f_AuthyAuthenticator_config_status'  => true,
										'mo2f_configured_2FA_method'             => "Authy Authenticator",
										'user_registration_with_miniorange'      => 'SUCCESS',
										'mo_2factor_user_registration_status'    => 'MO_2_FACTOR_PLUGIN_SETTINGS'
									) );
									update_user_meta( $user->ID, 'mo2f_external_app_type', "Authy Authenticator" );
									delete_user_meta( $user->ID, 'mo2f_2FA_method_to_configure' );
									delete_user_meta( $user->ID, 'configure_2FA' );
									
									mo2f_display_test_2fa_notification($user);

								} else {
									update_option( 'mo2f_message', Mo2fConstants:: langTranslate( "ERROR_DURING_PROCESS" ) );
									$this->mo_auth_show_error_message();
								}
							} else {
								update_option( 'mo2f_message', Mo2fConstants:: langTranslate( "ERROR_DURING_PROCESS" ) );
								$this->mo_auth_show_error_message();
							}
						} else {
							update_option( 'mo2f_message', Mo2fConstants:: langTranslate( "ERROR_IN_SENDING_OTP_CAUSES" ) . '<br>1. ' . Mo2fConstants:: langTranslate( "INVALID_OTP" ) . '<br>2. ' . Mo2fConstants:: langTranslate( "APP_TIME_SYNC" ) );
							$this->mo_auth_show_error_message();
						}
					} else {
						update_option( 'mo2f_message', Mo2fConstants:: langTranslate( "ERROR_WHILE_VALIDATING_USER" ) );
						$this->mo_auth_show_error_message();
					}
				} else {
					update_option( 'mo2f_message', Mo2fConstants:: langTranslate( "ONLY_DIGITS_ALLOWED" ) );
					$this->mo_auth_show_error_message();
				}
			}
		}
		else if  ( isset( $_POST['option'] ) && sanitize_text_field($_POST['option']) == 'mo2f_save_kba' ) {
			$nonce = sanitize_text_field($_POST['mo2f_save_kba_nonce']);
			if ( ! wp_verify_nonce( $nonce, 'mo2f-save-kba-nonce' ) ) {
				$error = new WP_Error();
				$error->add( 'empty_username', '<strong>' . mo2f_lt( 'ERROR' ) . '</strong>: ' . mo2f_lt( 'Invalid Request.' ) );

				return $error;
			}
			$twofactor_transactions = new Mo2fDB;
			$exceeded = $twofactor_transactions->check_alluser_limit_exceeded($user_id);
			if($exceeded){
				update_option( 'mo2f_message', Mo2fConstants:: langTranslate( "USER_LIMIT_EXCEEDED" ) );
				$this->mo_auth_show_error_message();
				return;
			}

			$kba_q1 = sanitize_text_field($_POST['mo2f_kbaquestion_1']);
			$kba_a1 = sanitize_text_field( $_POST['mo2f_kba_ans1'] );
			$kba_q2 = sanitize_text_field($_POST['mo2f_kbaquestion_2']);
			$kba_a2 = sanitize_text_field( $_POST['mo2f_kba_ans2'] );
			$kba_q3 = sanitize_text_field( $_POST['mo2f_kbaquestion_3'] );
			$kba_a3 = sanitize_text_field( $_POST['mo2f_kba_ans3'] );

			if ( MO2f_Utility::mo2f_check_empty_or_null( $kba_q1 ) || MO2f_Utility::mo2f_check_empty_or_null( $kba_a1 ) || MO2f_Utility::mo2f_check_empty_or_null( $kba_q2 ) || MO2f_Utility::mo2f_check_empty_or_null( $kba_a2) || MO2f_Utility::mo2f_check_empty_or_null( $kba_q3) || MO2f_Utility::mo2f_check_empty_or_null( $kba_a3) ) {
				update_option( 'mo2f_message', Mo2fConstants:: langTranslate( "INVALID_ENTRY" ) );
				$this->mo_auth_show_error_message();
				return;
			}

			if ( strcasecmp( $kba_q1, $kba_q2 ) == 0 || strcasecmp( $kba_q2, $kba_q3 ) == 0 || strcasecmp( $kba_q3, $kba_q1 ) == 0 ) {
				update_option( 'mo2f_message', 'The questions you select must be unique.' );
				$this->mo_auth_show_error_message();
				return;
			}
			$kba_q1 = addcslashes( stripslashes( $kba_q1 ), '"\\' );
			$kba_q2 = addcslashes( stripslashes( $kba_q2 ), '"\\' );
			$kba_q3 = addcslashes( stripslashes( $kba_q3 ), '"\\' );

			$kba_a1 = addcslashes( stripslashes( $kba_a1 ), '"\\' );
			$kba_a2 = addcslashes( stripslashes( $kba_a2 ), '"\\' );
			$kba_a3 = addcslashes( stripslashes( $kba_a3 ), '"\\' );

			$email            = $Mo2fdbQueries->get_user_detail( 'mo2f_user_email', $user->ID );
			$email = ($email=='')?$user->user_email:$email;
			$kba_registration = new Two_Factor_Setup();
			$kba_reg_reponse  = json_decode( $kba_registration->register_kba_details( $email, $kba_q1, $kba_a1, $kba_q2, $kba_a2, $kba_q3, $kba_a3, $user->ID ), true );
			if ( json_last_error() == JSON_ERROR_NONE ) {
				if ( $kba_reg_reponse['status'] == 'SUCCESS' ) {
					if ( isset( $_POST['mobile_kba_option'] ) && sanitize_text_field($_POST['mobile_kba_option']) == 'mo2f_request_for_kba_as_emailbackup' ) {
						MO2f_Utility::unset_session_variables( 'mo2f_mobile_support' );

						delete_user_meta( $user->ID, 'configure_2FA' );
						delete_user_meta( $user->ID, 'mo2f_2FA_method_to_configure' );

						$message = mo2f_lt( 'Your KBA as alternate 2 factor is configured successfully.' );
						update_option( 'mo2f_message', $message );
						$this->mo_auth_show_success_message();

					} else {
						$enduser  = new Two_Factor_Setup();
						$response = json_decode( $enduser->mo2f_update_userinfo( $email, 'KBA', null, null, null ), true );
						if ( json_last_error() == JSON_ERROR_NONE ) {
							if ( $response['status'] == 'ERROR' ) {
								update_option( 'mo2f_message', Mo2fConstants:: langTranslate( $response['message'] ) );
								$this->mo_auth_show_error_message();

							} else if ( $response['status'] == 'SUCCESS' ) {
								delete_user_meta( $user->ID, 'configure_2FA' );

								$Mo2fdbQueries->update_user_details( $user->ID, array(
									'mo2f_user_email'                      => $email,
									'mo2f_SecurityQuestions_config_status' => true,
									'mo2f_configured_2FA_method'           => "Security Questions",
									'mo_2factor_user_registration_status'  => "MO_2_FACTOR_PLUGIN_SETTINGS"
								) );
										// $this->mo_auth_show_success_message();
								mo2f_display_test_2fa_notification($user);

							}else {
								update_option( 'mo2f_message', Mo2fConstants:: langTranslate( "ERROR_DURING_PROCESS" ) );
								$this->mo_auth_show_error_message();

							}
						} else {
							update_option( 'mo2f_message', Mo2fConstants:: langTranslate( "INVALID_REQ" ) );
							$this->mo_auth_show_error_message();

						}
					}
				} else {
					update_option( 'mo2f_message', Mo2fConstants:: langTranslate( "ERROR_WHILE_SAVING_KBA" ) );
					$this->mo_auth_show_error_message();


					return;
				}
			} else {
				update_option( 'mo2f_message', Mo2fConstants:: langTranslate( "ERROR_WHILE_SAVING_KBA" ) );
				$this->mo_auth_show_error_message();


				return;
			}	


		}else if  ( isset( $_POST['option'] ) && sanitize_text_field($_POST['option']) == 'mo2f_validate_kba_details' ) {
			$nonce = sanitize_text_field($_POST['mo2f_validate_kba_details_nonce']);

			if ( ! wp_verify_nonce( $nonce, 'mo2f-validate-kba-details-nonce' ) ) {
				$error = new WP_Error();
				$error->add( 'empty_username', '<strong>' . mo2f_lt( 'ERROR' ) . '</strong>: ' . mo2f_lt( 'Invalid Request.' ) );

				return $error;
			} else {	
				$kba_ans_1 = '';
				$kba_ans_2 = '';
				if ( MO2f_Utility::mo2f_check_empty_or_null( $_POST['mo2f_answer_1'] ) || MO2f_Utility::mo2f_check_empty_or_null( $_POST['mo2f_answer_1'] ) ) {
					update_option( 'mo2f_message', Mo2fConstants:: langTranslate( "INVALID_ENTRY" ) );
					$this->mo_auth_show_error_message();

					return;
				} else {
					$kba_ans_1 = sanitize_text_field( $_POST['mo2f_answer_1'] );
					$kba_ans_2 = sanitize_text_field( $_POST['mo2f_answer_2'] );
				}
				//if the php session folder has insufficient permissions, temporary options to be used
				$kba_questions = get_user_meta($user->ID, 'mo_2_factor_kba_questions', true);

				$kbaAns    = array();
				if(!MO2F_IS_ONPREM){
					$kbaAns[0] = $kba_questions[0]['question'];
					$kbaAns[1] = $kba_ans_1;
					$kbaAns[2] = $kba_questions[1]['question'];
					$kbaAns[3] = $kba_ans_2;
				}
				//if the php session folder has insufficient permissions, temporary options to be used
				$mo2f_transactionId = get_option('mo2f_transactionId');
				$kba_validate          = new Customer_Setup();
				$kba_validate_response = json_decode( $kba_validate->validate_otp_token( 'KBA', null, $mo2f_transactionId, $kbaAns, get_option( 'mo2f_customerKey' ), get_option( 'mo2f_api_key' ) ), true );
				if ( json_last_error() == JSON_ERROR_NONE ) {
					if ( strcasecmp( $kba_validate_response['status'], 'SUCCESS' ) == 0 ) {
						delete_option('mo2f_transactionId');
						delete_option('kba_questions');
						update_option( 'mo2f_message', Mo2fConstants:: langTranslate( "COMPLETED_TEST" ) );
						delete_user_meta( $user->ID, 'test_2FA' );
						$this->mo_auth_show_success_message();
					} else {  // KBA Validation failed.
						update_option( 'mo2f_message', Mo2fConstants:: langTranslate( "INVALID_ANSWERS" ) );
						$this->mo_auth_show_error_message();

					}
				}
			}
		}
		else if  ( isset( $_POST['option'] ) && sanitize_text_field($_POST['option']) == 'mo2f_configure_otp_over_Telegram_send_otp' ) { // sendin otp for configuring OTP over Telegram
			
			$nonce = sanitize_text_field($_POST['mo2f_configure_otp_over_Telegram_send_otp_nonce']);

			if ( ! wp_verify_nonce( $nonce, 'mo2f-configure-otp-over-Telegram-send-otp-nonce' ) ) {
				$error = new WP_Error();
				$error->add( 'empty_username', '<strong>' . mo2f_lt( 'ERROR' ) . '</strong>: ' . mo2f_lt( 'Invalid Request.' ) );

				return $error;
			} else {	
				$chatID = sanitize_text_field( $_POST['verify_chatID'] );

				if ( MO2f_Utility::mo2f_check_empty_or_null( $chatID ) ) {
					update_option( 'mo2f_message', Mo2fConstants:: langTranslate( "INVALID_ENTRY" ) );
					$this->mo_auth_show_error_message();

					return;
				}

				$chatID       = str_replace( ' ', '', $chatID );
				$user = wp_get_current_user();

				update_user_meta($user->ID, 'mo2f_temp_chatID', $chatID );
				$customer      = new Customer_Setup();
				$currentMethod = "OTP Over Telegram";

				$otpToken 	= '';
				for($i=1;$i<7;$i++)
				{
					$otpToken 	.= rand(0,9);
				}
				update_user_meta($user->ID,'mo2f_otp_token',$otpToken);
				update_user_meta($user->ID,'mo2f_telegram_time',time());

				$url = esc_url(MoWpnsConstants::TELEGRAM_OTP_LINK);
				$postdata = array( 'mo2f_otp_token' => $otpToken,
					'mo2f_chatid' => $chatID
				);

				$args = array(
					'method'      => 'POST',
					'timeout'     => 10,
					'sslverify'   => false,
					'headers'     => array(),
					'body'        => $postdata,
				);

				$mo2f_api=new Mo2f_Api();
				$data=$mo2f_api->mo2f_wp_remote_post($url,$args);

				if($data == 'SUCCESS')
				{
					update_option( 'mo2f_message', Mo2fConstants:: langTranslate( "OTP_SENT" ) . 'your telegram number.' . Mo2fConstants:: langTranslate( "ENTER_OTP" ) );
					$this->mo_auth_show_success_message();
				}
				else
				{
					update_option( 'mo2f_message', 'An Error has occured while sending the OTP. Please verify your chat ID.');
					$this->mo_auth_show_error_message();

				}
				
			}
		}
		else if  ( isset( $_POST['option'] ) && sanitize_text_field($_POST['option']) == 'mo2f_configure_otp_over_sms_send_otp' ) { // sendin otp for configuring OTP over SMS
			
			$nonce = sanitize_text_field($_POST['mo2f_configure_otp_over_sms_send_otp_nonce']);

			if ( ! wp_verify_nonce( $nonce, 'mo2f-configure-otp-over-sms-send-otp-nonce' ) ) {
				$error = new WP_Error();
				$error->add( 'empty_username', '<strong>' . mo2f_lt( 'ERROR' ) . '</strong>: ' . mo2f_lt( 'Invalid Request.' ) );

				return $error;
			} else {	
				$phone = sanitize_text_field( $_POST['phone'] );

				if ( MO2f_Utility::mo2f_check_empty_or_null( $phone ) ) {
					update_option( 'mo2f_message', Mo2fConstants:: langTranslate( "INVALID_ENTRY" ) );
					$this->mo_auth_show_error_message();

					return;
				}

				$phone                  = str_replace( ' ', '', $phone );
				$session_id_encrypt = sanitize_text_field($_POST['mo2f_session_id']);
				MO2f_Utility::mo2f_set_transient($session_id_encrypt, 'user_phone', $phone);
				update_option( 'user_phone_temp', $phone );
				$customer      = new Customer_Setup();
				$currentMethod = "SMS";

				$content = json_decode( $customer->send_otp_token( $phone, $currentMethod, get_option( 'mo2f_customerKey' ), get_option( 'mo2f_api_key' ) ), true );

				if ( json_last_error() == JSON_ERROR_NONE ) { /* Generate otp token */
					if ( $content['status'] == 'ERROR' ) {
						update_option( 'mo2f_message', Mo2fConstants:: langTranslate( $content['message'] ) );
						$this->mo_auth_show_error_message();
					} else if ( $content['status'] == 'SUCCESS' ) {
						MO2f_Utility::mo2f_set_transient($session_id_encrypt, 'mo2f_transactionId', $content['txId']);


						update_option( 'mo2f_transactionId', $content['txId'] );
						update_option( 'mo2f_message', Mo2fConstants:: langTranslate( "OTP_SENT" ) . ' ' . $phone . ' .' . Mo2fConstants:: langTranslate( "ENTER_OTP" ) );
						update_option( 'mo2f_number_of_transactions', MoWpnsUtility::get_mo2f_db_option('mo2f_number_of_transactions', 'get_option') - 1 );
						$mo2f_sms = get_site_option('cmVtYWluaW5nT1RQVHJhbnNhY3Rpb25z');
						if($mo2f_sms>0)
							update_site_option('cmVtYWluaW5nT1RQVHJhbnNhY3Rpb25z',$mo2f_sms-1);

						$this->mo_auth_show_success_message();
					} else {
						update_option( 'mo2f_message', Mo2fConstants::langTranslate( $content['message'] ) );
						$this->mo_auth_show_error_message();
					}

				} else {
					update_option( 'mo2f_message', Mo2fConstants:: langTranslate( "INVALID_REQ" ) );
					$this->mo_auth_show_error_message();
				}
			}
		}
		else if  ( isset( $_POST['option'] ) && sanitize_text_field($_POST['option']) == 'mo2f_configure_otp_over_Telegram_validate' ) {
			$nonce = sanitize_text_field($_POST['mo2f_configure_otp_over_Telegram_validate_nonce']);

			if ( ! wp_verify_nonce( $nonce, 'mo2f-configure-otp-over-Telegram-validate-nonce' ) ) {
				$error = new WP_Error();
				$error->add( 'empty_username', '<strong>' . mo2f_lt( 'ERROR' ) . '</strong>: ' . mo2f_lt( 'Invalid Request.' ) );

				return $error;
			} else {

				$twofactor_transactions = new Mo2fDB;
				$exceeded = $twofactor_transactions->check_alluser_limit_exceeded($user_id);

				if($exceeded){
					update_option( 'mo2f_message', Mo2fConstants:: langTranslate( "USER_LIMIT_EXCEEDED" ) );
					$this->mo_auth_show_error_message();
					return;
				}
				$otp_token = '';
				if ( MO2f_Utility::mo2f_check_empty_or_null( $_POST['otp_token'] ) ) {
					update_option( 'mo2f_message', Mo2fConstants:: langTranslate( "INVALID_ENTRY" ) );
					$this->mo_auth_show_error_message();

					return;
				} else {
					$otp_token = sanitize_text_field( $_POST['otp_token'] );
				}

				$otp 			= get_user_meta($user->ID,'mo2f_otp_token',true);
				$time  			= get_user_meta($user->ID,'mo2f_telegram_time',true);
				$accepted_time	= time()-300;
				$time 			= (int)$time;
				global $Mo2fdbQueries;
				if($otp == $otp_token)
				{
					if($accepted_time<$time){
						if(MO2F_IS_ONPREM)
							$Mo2fdbQueries->update_user_details( $user->ID, array( 'mo2f_configured_2FA_method' => 'OTP Over Telegram',
								'mo2f_OTPOverTelegram_config_status'       => true,
								'mo_2factor_user_registration_status' => 'MO_2_FACTOR_PLUGIN_SETTINGS'
							) );
						else
							{	$Mo2fdbQueries->update_user_details( $user->ID, array(
								'mo2f_configured_2FA_method'          => 'OTP Over Telegram',
								'mo2f_OTPOverTelegram_config_status'       => true,
								'user_registration_with_miniorange'   => 'SUCCESS',
								'mo_2factor_user_registration_status' => 'MO_2_FACTOR_PLUGIN_SETTINGS',
							) );
					}
					delete_user_meta( $user->ID, 'configure_2FA' );
					update_user_meta( $user->ID, 'mo2f_chat_id',get_user_meta($user->ID,'mo2f_temp_chatID',true));

					delete_user_meta( $user->ID, 'mo2f_temp_chatID' );

					delete_user_meta( $user->ID, 'mo2f_otp_token');
					delete_user_meta( $user->ID, 'mo2f_2FA_method_to_configure' );
					mo2f_display_test_2fa_notification($user);
					update_option( 'mo2f_message','OTP Over Telegram is set as the second-factor. Enjoy the unlimited service.');
					$this->mo_auth_show_success_message();
					delete_user_meta($user->ID,'mo2f_telegram_time');
				}
				else
				{	
					update_option( 'mo2f_message','OTP has been expired please reinitiate another transaction.');
					$this->mo_auth_show_error_message();
					delete_user_meta($user->ID,'mo2f_telegram_time');
				}
			}
			else
			{
				update_option( 'mo2f_message','Invalid OTP. Please try again.');
				$this->mo_auth_show_error_message();
			}
			
		}}
		else if  ( isset( $_POST['option'] ) && sanitize_text_field($_POST['option']) == 'mo2f_configure_otp_over_sms_validate' && isset($_POST['mo2f_configure_otp_over_sms_validate_nonce'])) {
			$nonce = sanitize_text_field($_POST['mo2f_configure_otp_over_sms_validate_nonce']);

			if ( ! wp_verify_nonce( $nonce, 'mo2f-configure-otp-over-sms-validate-nonce' ) ) {
				$error = new WP_Error();
				$error->add( 'empty_username', '<strong>' . mo2f_lt( 'ERROR' ) . '</strong>: ' . mo2f_lt( 'Invalid Request.' ) );

				return $error;
			} else {

				$twofactor_transactions = new Mo2fDB;
				$exceeded = $twofactor_transactions->check_alluser_limit_exceeded($user_id);

				if($exceeded){
					update_option( 'mo2f_message', Mo2fConstants:: langTranslate( "USER_LIMIT_EXCEEDED" ) );
					$this->mo_auth_show_error_message();
					return;
				}
				$otp_token = '';
				if ( MO2f_Utility::mo2f_check_empty_or_null( isset($_POST['otp_token']) && $_POST['otp_token'] ) ) {
					update_option( 'mo2f_message', Mo2fConstants:: langTranslate( "INVALID_ENTRY" ) );
					$this->mo_auth_show_error_message();

					return;
				} else {
					$otp_token = sanitize_text_field( $_POST['otp_token'] );
					$session_id_encrypt = sanitize_text_field($_POST['mo2f_session_id']);
				}
				$mo2f_transactionId = MO2f_Utility::mo2f_get_transient( $session_id_encrypt, 'mo2f_transactionId' );

				$user_phone = MO2f_Utility::mo2f_get_transient( $session_id_encrypt, 'user_phone' );				
				$mo2f_configured_2FA_method = get_user_meta( $user->ID, 'mo2f_2FA_method_to_configure', true );
				$phone                      = $Mo2fdbQueries->get_user_detail( 'mo2f_user_phone', $user->ID );
				$customer                   = new Customer_Setup();
				$content                    = json_decode( $customer->validate_otp_token( $mo2f_configured_2FA_method, null, $mo2f_transactionId, $otp_token, get_option( 'mo2f_customerKey' ), get_option( 'mo2f_api_key' ) ), true );

				if ( $content['status'] == 'ERROR' ) {
					update_option( 'mo2f_message', Mo2fConstants:: langTranslate( $content['message'] ) );

				} else if ( strcasecmp( $content['status'], 'SUCCESS' ) == 0 ) { //OTP validated
					if ( $phone && strlen( $phone ) >= 4 ) {
						if ( $user_phone != $phone ) {
							$Mo2fdbQueries->update_user_details( $user->ID, array( 'mobile_registration_status' => false ) );

						}
					}
					$email = $Mo2fdbQueries->get_user_detail( 'mo2f_user_email', $user->ID );

					$enduser                   = new Two_Factor_Setup();
					$TwoFA_method_to_configure = get_user_meta( $user->ID, 'mo2f_2FA_method_to_configure', true );
					$current_method            = MO2f_Utility::mo2f_decode_2_factor( $TwoFA_method_to_configure, "server" );
					$response  = array();
					if(MO2F_IS_ONPREM) {
						$response['status'] = 'SUCCESS';
						if ( $current_method == 'SMS' ) {
							$Mo2fdbQueries->update_user_details( $user->ID, array( 'mo2f_configured_2FA_method' => 'OTP Over SMS' ) );
//							update_user_meta($user->ID,'currentMethod','OTP Over SMS');
						} else {
							$Mo2fdbQueries->update_user_details( $user->ID, array( 'mo2f_configured_2FA_method' => $current_method ) );//why is this needed?
//						    update_user_meta( $user->ID, 'currentMethod', $current_method );

						}
					}
					else
						$response                  = json_decode( $enduser->mo2f_update_userinfo( $email, $current_method, $user_phone, null, null ), true );

					if ( json_last_error() == JSON_ERROR_NONE ) {

						if ( $response['status'] == 'ERROR' ) {
							MO2f_Utility::unset_session_variables( 'user_phone' );
							delete_option( 'user_phone_temp' );

							update_option( 'mo2f_message', Mo2fConstants:: langTranslate( $response['message'] ) );
							$this->mo_auth_show_error_message();
						} else if ( $response['status'] == 'SUCCESS' ) {

							$Mo2fdbQueries->update_user_details( $user->ID, array(
								'mo2f_configured_2FA_method'          => 'OTP Over SMS',
								'mo2f_OTPOverSMS_config_status'       => true,
								'user_registration_with_miniorange'   => 'SUCCESS',
								'mo_2factor_user_registration_status' => 'MO_2_FACTOR_PLUGIN_SETTINGS',
								'mo2f_user_phone'                     => $user_phone
							) );

							delete_user_meta( $user->ID, 'configure_2FA' );
							delete_user_meta( $user->ID, 'mo2f_2FA_method_to_configure' );

							
							MO2f_Utility::unset_session_variables( 'user_phone' );
							delete_option( 'user_phone_temp' );

							mo2f_display_test_2fa_notification($user);
						} else {
							MO2f_Utility::unset_session_variables( 'user_phone' );
							delete_option( 'user_phone_temp' );
							update_option( 'mo2f_message', Mo2fConstants:: langTranslate( "ERROR_DURING_PROCESS" ) );
							$this->mo_auth_show_error_message();
						}
					} else {
						MO2f_Utility::unset_session_variables( 'user_phone' );
						delete_option( 'user_phone_temp' );
						update_option( 'mo2f_message', Mo2fConstants:: langTranslate( "INVALID_REQ" ) );
						$this->mo_auth_show_error_message();
					}

				} else {  // OTP Validation failed.
					update_option( 'mo2f_message', Mo2fConstants:: langTranslate( "INVALID_OTP" ) );
					$this->mo_auth_show_error_message();
				}
			}

		}else if(isset( $_POST['option'] ) && sanitize_text_field($_POST['option']) == 'mo2f_configure_duo_authenticator'){
			
             $nonce = sanitize_text_field($_POST['mo2f_configure_duo_authenticator_nonce']);

             if ( ! wp_verify_nonce( $nonce, 'mo2f-configure-duo-authenticator' ) ) {
				$error = new WP_Error();
				$error->add( 'empty_username', '<strong>' . mo2f_lt( 'ERROR' ) . '</strong>: ' . mo2f_lt( 'Invalid Request.' ) );

				return $error;
			} else {
				 if(sanitize_text_field($_POST['ikey']) == '' || sanitize_text_field($_POST['skey']) == '' || sanitize_text_field($_POST['apihostname']) == '' ){
                   update_option( 'mo2f_message', Mo2fConstants:: langTranslate( "Some field is missing, please fill all required details." ) );
                 $this->mo_auth_show_error_message();
                 return;
				 }else{
                 update_site_option('mo2f_d_integration_key',isset($_POST['ikey'])? sanitize_text_field($_POST['ikey']):'');
                 update_site_option('mo2f_d_secret_key',isset($_POST['skey'])? sanitize_text_field($_POST['skey']):'');
                 update_site_option('mo2f_d_api_hostname',isset($_POST['apihostname'])? sanitize_text_field($_POST['apihostname']):'');
                 
                 $ikey = sanitize_text_field($_POST['ikey']);
                 $skey = sanitize_text_field($_POST['skey']);
                 $host = sanitize_text_field($_POST['apihostname']);

                include_once dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR.'handler'.DIRECTORY_SEPARATOR.'twofa'.DIRECTORY_SEPARATOR.'two_fa_duo_handler.php';
 
                
                $duo_up_response = ping($skey,$ikey,$host);
              
				if($duo_up_response['response']['stat'] == 'OK'){
                 
                	$duo_check_credentials = check($skey, $ikey, $host);

                	if($duo_check_credentials['response']['stat'] == 'OK'){
                		
                        
                    }else{
                    	
                		update_option( 'mo2f_message', Mo2fConstants:: langTranslate( "Not the valid credential, please enter valid keys" ) );
		                 $this->mo_auth_show_error_message();
		                 return;
                	}

                }else{
                	
                	update_option( 'mo2f_message', Mo2fConstants:: langTranslate( "Duo server is not responding right now, please try after some time" ) );
                 $this->mo_auth_show_error_message();
                 return;
                }
                update_site_option('duo_credentials_save_successfully',1);
   		         update_option( 'mo2f_message', Mo2fConstants:: langTranslate( "Setting saved successfully." ) );
                 $this->mo_auth_show_success_message();
                 return;
                }
			}
		}else if(isset( $_POST['option'] ) && sanitize_text_field($_POST['option']) == 'mo2f_configure_duo_authenticator_abc'){

			$nonce = sanitize_text_field($_POST['mo2f_configure_duo_authenticator_nonce']);

             if ( ! wp_verify_nonce( $nonce, 'mo2f-configure-duo-authenticator-nonce' ) ) {
				$error = new WP_Error();
				$error->add( 'empty_username', '<strong>' . mo2f_lt( 'ERROR' ) . '</strong>: ' . mo2f_lt( 'Invalid Request.' ) );

				return $error;
			}else{
               include_once dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR.'handler'.DIRECTORY_SEPARATOR.'twofa'.DIRECTORY_SEPARATOR.'two_fa_duo_handler.php';
				        $ikey = get_site_option('mo2f_d_integration_key');
		                $skey = get_site_option('mo2f_d_secret_key');
		                $host = get_site_option('mo2f_d_api_hostname');

				        $user_email = $user->user_email;

                		$duo_preauth = preauth( $user_email ,true,  $skey, $ikey, $host);
                		 
                		 
                        if($duo_preauth['response']['stat'] == 'OK'){

	                         if(isset($duo_preauth['response']['response']['status_msg']) && $duo_preauth['response']['response']['status_msg'] == 'Account is active'){
	                         	update_user_meta( $user->ID , 'user_not_enroll', true  );
	                         	update_option( 'mo2f_message', Mo2fConstants:: langTranslate( "This user is already available on duo, please send push notification to setup push notification as two factor." ) );
				                 $this->mo_auth_show_success_message();
				                 return;
	                         }else if(isset($duo_preauth['response']['response']['enroll_portal_url'])){
                                 
	                        	$duo_enroll_url = $duo_preauth['response']['response']['enroll_portal_url'];
	                            update_user_meta( $user->ID , 'user_not_enroll_on_duo_before', $duo_enroll_url  );
	                            update_user_meta( $user->ID , 'user_not_enroll', true  );
	                        }else{
	                        

                               update_option( 'mo2f_message', Mo2fConstants:: langTranslate( "Your account is inactive from duo side, please contact to your administrator." ) );
			                   $this->mo_auth_show_error_message();
			                   return;
	                        }
                          
                          }else{
                          

                           update_option( 'mo2f_message', Mo2fConstants:: langTranslate( "Invalid or missing parameters, or a user with this name already exists." ) );
			                 $this->mo_auth_show_error_message();
			                 return;

                        }
			}	
		}else if(isset( $_POST['option'] ) && sanitize_text_field($_POST['option']) == 'duo_mobile_send_push_notification_inside_plugin'){

			$nonce = sanitize_text_field($_POST['duo_mobile_send_push_notification_inside_plugin_nonce']);

             if ( ! wp_verify_nonce( $nonce, 'mo2f-send-duo-push-notification-inside-plugin-nonce' ) ) {
				$error = new WP_Error();
				$error->add( 'empty_username', '<strong>' . mo2f_lt( 'ERROR' ) . '</strong>: ' . mo2f_lt( 'Invalid Request.' ) );

				return $error;
			}else{

			}	

		 }else if ( ( isset( $_POST['option'] ) && sanitize_text_field($_POST['option']) == 'mo2f_save_free_plan_auth_methods' ) ) {// user clicks on Set 2-Factor method

				 $nonce = sanitize_text_field($_POST['miniorange_save_form_auth_methods_nonce']);
			if ( ! wp_verify_nonce( $nonce, 'miniorange-save-form-auth-methods-nonce' ) ) {
				$error = new WP_Error();
				$error->add( 'empty_username', '<strong>' . mo2f_lt( 'ERROR' ) . '</strong>: ' . mo2f_lt( 'Invalid Request.' ) );
				return $error;
			} else {
				$configuredMethod = sanitize_text_field($_POST['mo2f_configured_2FA_method_free_plan']);
				$selectedAction   = sanitize_text_field($_POST['mo2f_selected_action_free_plan']);

				$cloud_methods = array('OTPOverSMS','miniOrangeQRCodeAuthentication','miniOrangePushNotification','miniOrangeSoftToken');

				if($configuredMethod == 'OTPOverSMS')
					$configuredMethod = 'OTP Over SMS';

            //limit exceed check
				$exceeded = $Mo2fdbQueries->check_alluser_limit_exceeded($user_id);

				if($exceeded){
					update_option( 'mo2f_message', Mo2fConstants:: langTranslate( "USER_LIMIT_EXCEEDED" ) );
					$this->mo_auth_show_error_message();
					return;
				}           
				$selected_2FA_method = MO2f_Utility::mo2f_decode_2_factor( isset( $_POST['mo2f_configured_2FA_method_free_plan'] ) ? $_POST['mo2f_configured_2FA_method_free_plan'] : sanitize_text_field($_POST['mo2f_selected_action_standard_plan']), "wpdb" );
				$selected_2FA_method = sanitize_text_field($selected_2FA_method);
			$onprem_methods = array('Google Authenticator','Security Questions','OTPOverTelegram','DuoAuthenticator');
				$Mo2fdbQueries->insert_user( $user->ID );
				if(MO2F_IS_ONPREM && ! in_array($selected_2FA_method, $onprem_methods) ){
					foreach ($cloud_methods as $cloud_method) {
						$is_end_user_registered = $Mo2fdbQueries->get_user_detail( 'mo2f_' . $cloud_method. '_config_status', $user->ID );
						if(!is_null($is_end_user_registered) && $is_end_user_registered == 1)
							break;
					}
				}else{
					$is_end_user_registered = $Mo2fdbQueries->get_user_detail('user_registration_with_miniorange', $user->ID ) ;
				}
				$is_customer_registered= false; 

				if(!MO2F_IS_ONPREM or $configuredMethod == 'miniOrangeSoftToken' or $configuredMethod == 'miniOrangeQRCodeAuthentication' or $configuredMethod == 'miniOrangePushNotification' or $configuredMethod == 'OTPOverSMS' or $configuredMethod == 'OTP Over SMS')
					$is_customer_registered = get_option('mo2f_api_key') ? true : false;
				$email = $Mo2fdbQueries->get_user_detail( 'mo2f_user_email', $user->ID );
				if(!isset($email) or is_null($email) or $email == '')
				{
					$email = $user->user_email;
				}
				$is_end_user_registered  = $is_end_user_registered ? $is_end_user_registered : false;
				$allowed = false;
				if(get_option('mo2f_miniorange_admin'))
					$allowed = wp_get_current_user()->ID == get_option('mo2f_miniorange_admin');
				
				if($is_customer_registered && !$is_end_user_registered and !$allowed){
					$enduser    = new Two_Factor_Setup();
					$check_user = json_decode( $enduser->mo_check_user_already_exist( $email ), true );
					if(json_last_error() == JSON_ERROR_NONE){
						if($check_user['status'] == 'ERROR'){
							update_option( 'mo2f_message', Mo2fConstants:: langTranslate( $check_user['message'] ) );
							$this->mo_auth_show_error_message();
							return;
						}
						else if(strcasecmp($check_user['status' ], 'USER_FOUND') == 0){

							$Mo2fdbQueries->update_user_details( $user->ID, array(
								'user_registration_with_miniorange' =>'SUCCESS',
								'mo2f_user_email' =>$email
							) );
							update_site_option(base64_encode("totalUsersCloud"), intval(get_site_option(base64_encode("totalUsersCloud"))) +1);

						}
						else if(strcasecmp($check_user['status'], 'USER_NOT_FOUND') == 0){

							$content = json_decode($enduser->mo_create_user($user,$email), true);
							if(json_last_error() == JSON_ERROR_NONE) {
								if(strcasecmp($content['status'], 'SUCCESS') == 0) {
									update_site_option(base64_encode("totalUsersCloud"), intval(get_site_option(base64_encode("totalUsersCloud"))) +1);
									$Mo2fdbQueries->update_user_details( $user->ID, array(
										'user_registration_with_miniorange' =>'SUCCESS',
										'mo2f_user_email' =>$email
									) );

								}
							}


						}
						else if(strcasecmp($check_user['status'], 'USER_FOUND_UNDER_DIFFERENT_CUSTOMER') == 0){
							$mo2fa_login_message = __('The email associated with your account is already registered in miniOrange. Please Choose another email or contact miniOrange.','miniorange-2-factor-authentication');
							update_option('mo2f_message',$mo2fa_login_message);
							$this->mo_auth_show_error_message();
						}

					}

				}	

				update_user_meta( $user->ID, 'mo2f_2FA_method_to_configure', $selected_2FA_method );
				if(MO2F_IS_ONPREM)
				{
					if($selected_2FA_method == 'EmailVerification')
						$selected_2FA_method = 'Email Verification';
					if($selected_2FA_method == 'OTPOverEmail')
						$selected_2FA_method = 'OTP Over Email';
					if($selected_2FA_method == 'OTPOverSMS')
						$selected_2FA_method = 'OTP Over SMS';
					if($selected_2FA_method == 'OTPOverTelegram')
						$selected_2FA_method = 'OTP Over Telegram';
					if($selected_2FA_method == 'DuoAuthenticator')
					    $selected_2FA_method = 'Duo Authenticator';
				}
			if(MO2F_IS_ONPREM and ($selected_2FA_method =='Google Authenticator' or $selected_2FA_method == 'Security Questions' or $selected_2FA_method =='OTP Over Email' or $selected_2FA_method == 'Email Verification' or $selected_2FA_method == 'OTP Over Telegram' or $selected_2FA_method == 'Duo Authenticator' ))
					$is_customer_registered = 1;

				if ( $is_customer_registered ) {
					$selected_2FA_method        = MO2f_Utility::mo2f_decode_2_factor( isset( $_POST['mo2f_configured_2FA_method_free_plan'] ) ? sanitize_text_field($_POST['mo2f_configured_2FA_method_free_plan']) : sanitize_text_field($_POST['mo2f_selected_action_standard_plan']), "wpdb" );
					$selected_2FA_method 		= sanitize_text_field($selected_2FA_method);
					$selected_action            = isset( $_POST['mo2f_selected_action_free_plan'] ) ? sanitize_text_field($_POST['mo2f_selected_action_free_plan']) : sanitize_text_field($_POST['mo2f_selected_action_standard_plan']);
					$selected_action 			= sanitize_text_field($selected_action);
					$user_phone                 = '';
					if ( isset( $_SESSION['user_phone'] ) ) {
						$user_phone = $_SESSION['user_phone'] != 'false' ? sanitize_text_field($_SESSION['user_phone']) : $Mo2fdbQueries->get_user_detail( 'mo2f_user_phone', $user->ID );
					}

				// set it as his 2-factor in the WP database and server
					$enduser = new Customer_Setup();
					if($selected_2FA_method == 'OTPOverTelegram')
						$selected_2FA_method = 'OTP Over Telegram';
					if($selected_2FA_method == 'DuoAuthenticator')
						$selected_2FA_method = 'Duo Authenticator';
					if ( $selected_action == "select2factor" ) {

						if ( $selected_2FA_method == 'OTP Over SMS' && $user_phone == 'false' ) {
							update_option( 'mo2f_message', Mo2fConstants:: langTranslate( "PHONE_NOT_CONFIGURED" ) );
							$this->mo_auth_show_error_message();
						} else {
						// update in the Wordpress DB
							$email                      = $Mo2fdbQueries->get_user_detail( 'mo2f_user_email', $user->ID );
							$customer_key               = get_option( 'mo2f_customerKey' );
							$api_key                    = get_option( 'mo2f_api_key' );	
							$customer                   = new Customer_Setup();
							$cloud_method1 = array('miniOrange QR Code Authentication','miniOrange Push Notification','miniOrange Soft Token');

							$Mo2fdbQueries->update_user_details( $user->ID, array( 'mo2f_configured_2FA_method' => $selected_2FA_method ) );

						// update the server
							if(!MO2F_IS_ONPREM)
								$this->mo2f_save_2_factor_method( $user, $selected_2FA_method );
							if ( in_array( $selected_2FA_method, array(
								"miniOrange QR Code Authentication",
								"miniOrange Soft Token",
								"miniOrange Push Notification",
								"Google Authenticator",
								"Security Questions",
								"Authy Authenticator",
								"Email Verification",
								"OTP Over SMS",
								"OTP Over Email",
								"OTP Over SMS and Email",
								"Hardware Token"
							) ) ) {

							} else {
								update_site_option('mo2f_enable_2fa_prompt_on_login_page', 0 );
							}

						}
					} else if ( $selected_action == "configure2factor" ) {

					//show configuration form of respective Two Factor method
						update_user_meta( $user->ID, 'configure_2FA', 1 );
						update_user_meta( $user->ID, 'mo2f_2FA_method_to_configure', $selected_2FA_method );
					}

				} else {
					update_option("mo_2factor_user_registration_status","REGISTRATION_STARTED" );
					update_user_meta( $user->ID, 'register_account_popup', 1 );
					update_option( 'mo2f_message', '' );

				}
			}
		}else if ( isset( $_POST['option'] ) && sanitize_text_field($_POST['option']) == 'mo2f_enable_2FA_for_users_option' ) {
			$nonce = $_POST['mo2f_enable_2FA_for_users_option_nonce'];

			if ( ! wp_verify_nonce( $nonce, 'mo2f-enable-2FA-for-users-option-nonce' ) ) {
				$error = new WP_Error();
				$error->add( 'empty_username', '<strong>' . mo2f_lt( 'ERROR' ) . '</strong>: ' . mo2f_lt( 'Invalid Request.' ) );

				return $error;
			} else {	
				update_option( 'mo2f_enable_2fa_for_users', isset( $_POST['mo2f_enable_2fa_for_users'] ) ? sanitize_text_field($_POST['mo2f_enable_2fa_for_users']) : 0 );
			}
		}else if ( isset( $_POST['option'] ) && sanitize_text_field($_POST['option']) == 'mo2f_disable_proxy_setup_option' ) {
			$nonce = sanitize_text_field($_POST['mo2f_disable_proxy_setup_option_nonce']);

			if ( ! wp_verify_nonce( $nonce, 'mo2f-disable-proxy-setup-option-nonce' ) ) {
				$error = new WP_Error();
				$error->add( 'empty_username', '<strong>' . mo2f_lt( 'ERROR' ) . '</strong>: ' . mo2f_lt( 'Invalid Request.' ) );

				return $error;
			} else {	
				delete_option( 'mo2f_proxy_host' );
				delete_option( 'mo2f_port_number' );
				delete_option( 'mo2f_proxy_username' );
				delete_option( 'mo2f_proxy_password' );
				update_option( 'mo2f_message', Mo2fConstants:: langTranslate( "Proxy Configurations Reset." ) );
				$this->mo_auth_show_success_message();
			}
		}else if ( isset( $_POST['option'] ) && sanitize_text_field($_POST['option']) == 'mo2f_enable_2FA_option' ) {
			$nonce = sanitize_text_field($_POST['mo2f_enable_2FA_option_nonce']);

			if ( ! wp_verify_nonce( $nonce, 'mo2f-enable-2FA-option-nonce' ) ) {
				$error = new WP_Error();
				$error->add( 'empty_username', '<strong>' . mo2f_lt( 'ERROR' ) . '</strong>: ' . mo2f_lt( 'Invalid Request.' ) );

				return $error;
			} else {
				update_option( 'mo2f_enable_2fa', isset( $_POST['mo2f_enable_2fa'] ) ? sanitize_text_field($_POST['mo2f_enable_2fa']) : 0 );
			}
		}else if ( isset( $_POST['option'] ) && sanitize_text_field($_POST['option']) == 'mo_2factor_test_authentication_method' ) {
		//network security feature 
			$nonce = sanitize_text_field($_POST['mo_2factor_test_authentication_method_nonce']);
			
			if ( ! wp_verify_nonce( $nonce, 'mo-2factor-test-authentication-method-nonce' ) ) {
				$error = new WP_Error();
				$error->add( 'empty_username', '<strong>' . mo2f_lt( 'ERROR' ) . '</strong>: ' . mo2f_lt( 'Invalid Request.' ) );

				return $error;
			} else {
				update_user_meta( $user->ID, 'test_2FA', 1 );


				$selected_2FA_method        = sanitize_text_field($_POST['mo2f_configured_2FA_method_test']);
				$selected_2FA_method_server = MO2f_Utility::mo2f_decode_2_factor( $selected_2FA_method, "server" );
				$customer                   = new Customer_Setup();
				$email                      = $Mo2fdbQueries->get_user_detail( 'mo2f_user_email', $user->ID );
				$customer_key               = get_option( 'mo2f_customerKey' );
				$api_key                    = get_option( 'mo2f_api_key' );

				if ( $selected_2FA_method == 'Security Questions' ) {


					$response = json_decode( $customer->send_otp_token( $email, $selected_2FA_method_server, $customer_key, $api_key ), true );

					if ( json_last_error() == JSON_ERROR_NONE ) { /* Generate KBA Questions*/
						if ( $response['status'] == 'SUCCESS' ) {
							update_option( 'mo2f_transactionId', $response['txId'] );
							$questions                             = array();

							$questions[0]                          = $response['questions'][0];
							$questions[1]                          = $response['questions'][1];
							update_user_meta($user->ID, 'mo_2_factor_kba_questions', $questions);

							update_option( 'mo2f_message', Mo2fConstants:: langTranslate( "ANSWER_SECURITY_QUESTIONS" ) );
							$this->mo_auth_show_success_message();

						} else if ( $response['status'] == 'ERROR' ) {
							update_option( 'mo2f_message', Mo2fConstants:: langTranslate( "ERROR_FETCHING_QUESTIONS" ) );
							$this->mo_auth_show_error_message();

						}
					} else {
						update_option( 'mo2f_message', Mo2fConstants:: langTranslate( "ERROR_FETCHING_QUESTIONS" ) );
						$this->mo_auth_show_error_message();

					}


				} else if ( $selected_2FA_method == 'miniOrange Push Notification' ) {
					$response = json_decode( $customer->send_otp_token( $email, $selected_2FA_method_server, $customer_key, $api_key ), true );
					if ( json_last_error() == JSON_ERROR_NONE ) { /* Generate Qr code */
						if ( $response['status'] == 'ERROR' ) {
							update_option( 'mo2f_message', Mo2fConstants:: langTranslate( $response['message'] ) );
							$this->mo_auth_show_error_message();

						} else {
							if ( $response['status'] == 'SUCCESS' ) {
								update_user_meta($user->ID, 'mo2f_transactionId', $response['txId']);
								update_user_meta($user->ID, 'mo2f_show_qr_code', 'MO_2_FACTOR_SHOW_QR_CODE');


								update_option( 'mo2f_message', Mo2fConstants:: langTranslate( "PUSH_NOTIFICATION_SENT" ) );
								$this->mo_auth_show_success_message();

							} else {
								$session_variables = array( 'mo2f_qrCode', 'mo2f_transactionId', 'mo2f_show_qr_code' );
								MO2f_Utility::unset_session_variables( $session_variables );

								delete_option( 'mo2f_transactionId' );
								update_option( 'mo2f_message', 'An error occurred while processing your request. Please Try again.' );
								$this->mo_auth_show_error_message();

							}
						}
					} else {
						update_option( 'mo2f_message', Mo2fConstants:: langTranslate( "INVALID_REQ" ) );
						$this->mo_auth_show_error_message();

					}
				}
				else if($selected_2FA_method =='OTP Over Telegram')
				{
					
					$user 		= wp_get_current_user();
					$chatID     = get_user_meta($user->ID,'mo2f_chat_id',true);
					$otpToken 	= '';
					for($i=1;$i<7;$i++)
					{
						$otpToken 	.= rand(0,9);
					}

					update_user_meta($user->ID,'mo2f_otp_token',$otpToken);
					update_user_meta($user->ID,'mo2f_telegram_time',time());

					$url = esc_url(MoWpnsConstants::TELEGRAM_OTP_LINK);
					$postdata = array( 'mo2f_otp_token' => $otpToken,
						'mo2f_chatid' => $chatID
					);

					$args = array(
						'method'      => 'POST',
						'timeout'     => 10,
						'sslverify'   => false,
						'headers'     => array(),
						'body'        => $postdata,
					);
	
					$mo2f_api=new Mo2f_Api();
					$data=$mo2f_api->mo2f_wp_remote_post($url,$args);

					if($data == 'SUCCESS')
					{
						update_option( 'mo2f_message', Mo2fConstants:: langTranslate( "OTP_SENT" ) . 'your telegram number.' . Mo2fConstants:: langTranslate( "ENTER_OTP" ) );
						$this->mo_auth_show_success_message();
					}
					else
					{
						update_option( 'mo2f_message', 'An Error has occured while sending the OTP. Please verify your chat ID.');
						$this->mo_auth_show_error_message();

					}
				}	
				else if ( $selected_2FA_method == 'OTP Over SMS' || $selected_2FA_method == 'OTP Over Email') {
					
					$phone    = $Mo2fdbQueries->get_user_detail( 'mo2f_user_phone', $user->ID );
					$check = 1;
					if($selected_2FA_method == 'OTP Over Email')	
					{
						$phone    = $Mo2fdbQueries->get_user_detail( 'mo2f_user_email', $user->ID );
						if(MoWpnsUtility::get_mo2f_db_option('cmVtYWluaW5nT1RQ', 'site_option')<=0)
						{
							update_site_option("bGltaXRSZWFjaGVk",1);		
							$check = 0;

						}

					}

					if($check == 1)
						$response = json_decode( $customer->send_otp_token( $phone, $selected_2FA_method_server, $customer_key, $api_key ), true );
					else
						$response['status'] = 'FAILED';
					if ( strcasecmp( $response['status'], 'SUCCESS' ) == 0 ) {
						if($selected_2FA_method == 'OTP Over Email')
						{
							$cmVtYWluaW5nT1RQ = MoWpnsUtility::get_mo2f_db_option('cmVtYWluaW5nT1RQ', 'site_option');
							if($cmVtYWluaW5nT1RQ>0)
								update_site_option("cmVtYWluaW5nT1RQ",$cmVtYWluaW5nT1RQ-1);
						}
						else if($selected_2FA_method == 'OTP Over SMS')
						{
							$mo2f_sms = get_site_option('cmVtYWluaW5nT1RQVHJhbnNhY3Rpb25z');
							if($mo2f_sms>0)
								update_site_option('cmVtYWluaW5nT1RQVHJhbnNhY3Rpb25z',$mo2f_sms-1);

						}
						update_option( 'mo2f_message', Mo2fConstants:: langTranslate( "OTP_SENT" ) . ' <b>' . ( $phone ) . '</b>. ' . Mo2fConstants:: langTranslate( "ENTER_OTP" ) );
						update_option( 'mo2f_number_of_transactions', MoWpnsUtility::get_mo2f_db_option('mo2f_number_of_transactions', 'get_option') - 1 );
						update_user_meta($user->ID, 'mo2f_transactionId', $response['txId']);
						update_option( 'mo2f_transactionId', $response['txId'] );
						$this->mo_auth_show_success_message();

					} else {
						if(!MO2F_IS_ONPREM or  $selected_2FA_method == 'OTP Over SMS')
							update_option( 'mo2f_message', Mo2fConstants:: langTranslate( "ERROR_IN_SENDING_OTP" ) );
						else
							update_option( 'mo2f_message', Mo2fConstants:: langTranslate( "ERROR_IN_SENDING_OTP_ONPREM" ) );

						$this->mo_auth_show_error_message();

					}
				} else if ( $selected_2FA_method == 'miniOrange QR Code Authentication' ) {
					$response = json_decode( $customer->send_otp_token( $email, $selected_2FA_method_server, $customer_key, $api_key ), true );

					if ( json_last_error() == JSON_ERROR_NONE ) { /* Generate Qr code */

						if ( $response['status'] == 'ERROR' ) {
							update_option( 'mo2f_message', Mo2fConstants:: langTranslate( $response['message'] ) );
							$this->mo_auth_show_error_message();

						} else {
							if ( $response['status'] == 'SUCCESS' ) {
								update_user_meta($user->ID, 'mo2f_qrCode', $response['qrCode']);
								update_user_meta($user->ID, 'mo2f_transactionId', $response['txId']);
								update_user_meta($user->ID, 'mo2f_show_qr_code', 'MO_2_FACTOR_SHOW_QR_CODE');
								

								update_option( 'mo2f_message', Mo2fConstants:: langTranslate( "SCAN_QR_CODE" ) );
								$this->mo_auth_show_success_message();

							} else {
								update_option( 'mo2f_message', Mo2fConstants:: langTranslate( "ERROR_DURING_PROCESS" ) );
								$this->mo_auth_show_error_message();

							}
						}
					} else {
						update_option( 'mo2f_message', Mo2fConstants:: langTranslate( "INVALID_REQ" ) );
						$this->mo_auth_show_error_message();

					}
				} else if ( $selected_2FA_method == 'Email Verification' ) {
					$this->miniorange_email_verification_call( $user );
				}
				else if($selected_2FA_method == 'Duo Authenticator'){
                    
				}


				update_user_meta( $user->ID, 'mo2f_2FA_method_to_test', $selected_2FA_method );
			}

		}else if ( isset( $_POST['option'] ) && sanitize_text_field($_POST['option']) == 'mo2f_go_back' ) {
			$nonce = sanitize_text_field($_POST['mo2f_go_back_nonce']);

			if ( ! wp_verify_nonce( $nonce, 'mo2f-go-back-nonce' ) ) {
				$error = new WP_Error();
				$error->add( 'empty_username', '<strong>' . mo2f_lt( 'ERROR' ) . '</strong>: ' . mo2f_lt( 'Invalid Request.' ) );

				return $error;
			} else {
				$session_variables = array(
					'mo2f_qrCode',
					'mo2f_transactionId',
					'mo2f_show_qr_code',
					'user_phone',
					'mo2f_google_auth',
					'mo2f_mobile_support',
					'mo2f_authy_keys'
				);
				MO2f_Utility::unset_session_variables( $session_variables );
				delete_option( 'mo2f_transactionId' );
				delete_option( 'user_phone_temp' );

				delete_user_meta( $user->ID, 'test_2FA' );
				delete_user_meta( $user->ID, 'configure_2FA' );

			}
		}
		 else if(isset($_POST['option']) && sanitize_text_field($_POST['option']) == 'mo2f_reset_duo_configuration'){
		 	$nonce = sanitize_text_field($_POST['mo2f_duo_reset_configuration_nonce']);
			if ( ! wp_verify_nonce( $nonce, 'mo2f-duo-reset-configuration-nonce' ) ) {
				$error = new WP_Error();
				$error->add('empty_username', '<strong>'. __('ERROR','miniorange-2-factor-authentication') .'</strong>: '. __('Invalid Request.', 'miniorange-2-factor-authentication'));
				return $error;
			}else{
			delete_site_option('duo_credentials_save_successfully');
			delete_user_meta($user->ID,'user_not_enroll');
			delete_site_option('mo2f_d_integration_key');
		    delete_site_option('mo2f_d_secret_key');
		    delete_site_option('mo2f_d_api_hostname');
		    update_option( 'mo2f_message',  Mo2fConstants::langTranslate("RESET_DUO_CONFIGURATON"));
            $this->mo_auth_show_success_message();
          }
		}

		else if ( isset( $_POST['option'] ) && sanitize_text_field($_POST['option']) == 'mo2f_2factor_generate_backup_codes' ) {
			$nonce = sanitize_text_field($_POST['mo_2factor_generate_backup_codes_nonce']);
			if ( ! wp_verify_nonce( $nonce, 'mo-2factor-generate-backup-codes-nonce' ) ) {
				$error = new WP_Error();
				$error->add('empty_username', '<strong>'. __('ERROR','miniorange-2-factor-authentication') .'</strong>: '. __('Invalid Request.', 'miniorange-2-factor-authentication'));
				return $error;
			} else {
			   
				$codes = MO2f_Utility::mo2f_mail_and_download_codes();

				if($codes == 'TransientActive')
				{
					update_option( 'mo2f_message',  Mo2fConstants::langTranslate("TRANSIENT_ACTIVE"));
					$this->mo_auth_show_error_message();
				}

				if($codes == 'InternetConnectivityError')
				{   
					update_option( 'mo2f_message',  Mo2fConstants::langTranslate("INTERNET_CONNECTIVITY_ERROR"));
				   	$this->mo_auth_show_error_message();
				}


				if ($codes == 'LimitReached' || $codes == 'UserLimitReached' || $codes == 'AllUsed' || $codes == 'invalid_request'){
				 	 $id = get_current_user_id();
				 	 update_user_meta($id, 'mo_backup_code_generated', 1);
                     update_user_meta($id, 'mo_backup_code_downloaded', 1);
				
                 if($codes == 'AllUsed'){
                 update_option( 'mo2f_message',  Mo2fConstants::langTranslate("USED_ALL_BACKUP_CODES"));
                 }else if($codes == 'LimitReached'){    
				 update_option( 'mo2f_message',  Mo2fConstants::langTranslate("BACKUP_CODE_LIMIT_REACH"));
                 }else if($codes == 'UserLimitReached'){
                 update_option( 'mo2f_message',  Mo2fConstants::langTranslate("BACKUP_CODE_DOMAIN_LIMIT_REACH"));
                 }else if($codes == 'invalid_request'){
                 update_user_meta($id, 'mo_backup_code_generated', 0);
                 update_user_meta($id, 'mo_backup_code_downloaded', 0);
                 update_option( 'mo2f_message',  Mo2fConstants::langTranslate("BACKUP_CODE_INVALID_REQUEST"));
                 }
				
                 $this->mo_auth_show_error_message();
                 }
			}
		}
  
		else if(isset($_POST['option']) && isset($_POST[$_POST['option']]))
		{  
			$val = str_replace('mo2f_unblock_user_','',sanitize_text_field($_POST['option']));
				
			$nonce=isset($_POST['mo2f_unblock_form_nonce'])?sanitize_text_field($_POST['mo2f_unblock_form_nonce']):'';
			if(!wp_verify_nonce($nonce,'mo2f-unblock-form-nonce'))
			{    
				
				$error = new WP_Error();
				$error->add('empty_username', '<strong>'. __('ERROR','miniorange-2-factor-authentication') .'</strong>: '. __('Invalid Request.', 'miniorange-2-factor-authentication'));
				return $error;

			}
			else
			{   
					update_site_option('mo2f_user_login_status_'.$val,0);
					update_site_option('mo2f_is_user_blocked_'.$val,0);
					update_site_option('mo2f_grace_period_status_'.$val,0);
			
			}

		}
	}

	function mo_auth_deactivate() {
		global $Mo2fdbQueries;
		$mo2f_register_with_another_email = get_option( 'mo2f_register_with_another_email' );
		$is_EC                            = ! MoWpnsUtility::get_mo2f_db_option('mo2f_is_NC', 'get_option') ? 1 : 0;
		$is_NNC                           = MoWpnsUtility::get_mo2f_db_option('mo2f_is_NC', 'get_option') && MoWpnsUtility::get_mo2f_db_option('mo2f_is_NNC', 'get_option') ? 1 : 0;

		if ( $mo2f_register_with_another_email || $is_EC || $is_NNC ) {
			update_option( 'mo2f_register_with_another_email', 0 );
			$users = get_users( array() );
			$this->mo2f_delete_user_details( $users );
			$url = admin_url( 'plugins.php' );
			wp_redirect( $url );
		}
	}

	function mo2f_delete_user_details( $users ) {
		global $Mo2fdbQueries;
		foreach ( $users as $user ) {
			$Mo2fdbQueries->delete_user_details( $user->ID );
			delete_user_meta( $user->ID, 'phone_verification_status' );
			delete_user_meta( $user->ID, 'test_2FA' );
			delete_user_meta( $user->ID, 'mo2f_2FA_method_to_configure' );
			delete_user_meta( $user->ID, 'configure_2FA' );
			delete_user_meta( $user->ID, 'mo2f_2FA_method_to_test' );
			delete_user_meta( $user->ID, 'mo2f_phone' );
			delete_user_meta( $user->ID, 'register_account_popup' );
		}

	}
	function mo2f_show_email_page($email )
	{
		?>
		<div id="EnterEmailCloudVerification" class="modal">
			<!-- Modal content -->
			<div class="modal-content">
				<div class="modal-header">
					<h3 class="modal-title" style="text-align: center; font-size: 20px; color: #2271b1">Email Address for miniOrange</h3><span id="closeEnterEmailCloud" class="modal-span-close">X</span>
				</div>
				<div class="modal-body" style="height: auto">
					<h2><i>Enter your Email:&nbsp;&nbsp;&nbsp;  <input type ='email' id='emailEnteredCloud' name='emailEnteredCloud' size= '40' required value="<?php echo $email;?>"/></i></h2> 
				</div>
				<div class="modal-footer">
					<button type="button" class="button button-primary button-large modal-button" id="save_entered_email_cloud">Save</button>
				</div>
			</div>
		</div>


		<script type="text/javascript">

			jQuery('#EnterEmailCloudVerification').css('display', 'block');

			jQuery('#closeEnterEmailCloud').click(function(){
				jQuery('#EnterEmailCloudVerification').css('display', 'none');

			});


		</script>

		<?php


	}

	function mo2f_delete_mo_options() {
		delete_option( 'mo2f_email' );
		delete_option( 'mo2f_dbversion' );
		delete_option( 'mo2f_host_name' );
		delete_option( 'user_phone' );
		delete_option( 'mo2f_miniorange_admin');
		//delete_option( 'mo2f_customerKey' );
		delete_option( 'mo2f_api_key' );
		delete_option( 'mo2f_customer_token' );
		delete_option( 'mo_2factor_admin_registration_status' );
		delete_option( 'mo2f_number_of_transactions' );
		delete_option( 'mo2f_set_transactions' );
		delete_option( 'mo2f_show_sms_transaction_message' );
		delete_option( 'mo_app_password' );
		delete_option( 'mo2f_login_option' );
		delete_option( 'mo2f_remember_device' );
		delete_option( 'mo2f_enable_forgotphone' );
		delete_option( 'mo2f_enable_login_with_2nd_factor' );
		delete_option( 'mo2f_enable_xmlrpc' );
		delete_option( 'mo2f_register_with_another_email' );
		delete_option( 'mo2f_proxy_host' );
		delete_option( 'mo2f_port_number' );
		delete_option( 'mo2f_proxy_username' );
		delete_option( 'mo2f_proxy_password' );
		delete_option( 'mo2f_customer_selected_plan' );
		delete_option( 'mo2f_ns_whitelist_ip' );
		delete_option( 'mo2f_enable_brute_force' );
		delete_option( 'mo2f_show_remaining_attempts' );
		delete_option( 'mo2f_ns_blocked_ip' );
		delete_option( 'mo2f_allwed_login_attempts' );
		delete_option( 'mo2f_time_of_blocking_type' );
		delete_option( 'mo2f_network_features' );
		
	}

	function mo_auth_show_success_message() {
		do_action('wpns_show_message', get_option( 'mo2f_message' ), 'SUCCESS');
	}

	function mo2f_create_customer( $user ) {
		global $Mo2fdbQueries;
		delete_user_meta( $user->ID, 'mo2f_sms_otp_count' );
		delete_user_meta( $user->ID, 'mo2f_email_otp_count' );
		$customer    = new Customer_Setup();
		$customerKey = json_decode( $customer->create_customer(), true );

		if ( $customerKey['status'] == 'ERROR' ) {
			update_option( 'mo2f_message', Mo2fConstants:: langTranslate( $customerKey['message'] ) );
			$this->mo_auth_show_error_message();
		} else {
			if ( strcasecmp( $customerKey['status'], 'CUSTOMER_USERNAME_ALREADY_EXISTS' ) == 0 ) {    //admin already exists in miniOrange
				$content     = $customer->get_customer_key();
				$customerKey = json_decode( $content, true );

				if ( json_last_error() == JSON_ERROR_NONE ) {
					if ( array_key_exists( "status", $customerKey ) && $customerKey['status'] == 'ERROR' ) {
						update_option( 'mo2f_message', Mo2fConstants::langTranslate( $customerKey['message'] ) );
						$this->mo_auth_show_error_message();
					} else {
						if ( isset( $customerKey['id'] ) && ! empty( $customerKey['id'] ) ) {
							update_option( 'mo2f_customerKey', $customerKey['id'] );
							update_option( 'mo2f_api_key', $customerKey['apiKey'] );
							update_option( 'mo2f_customer_token', $customerKey['token'] );
							update_option( 'mo2f_app_secret', $customerKey['appSecret'] );
							update_option( 'mo2f_miniorange_admin', $user->ID );
							delete_option( 'mo2f_password' );
							$email = get_option( 'mo2f_email' );
							$Mo2fdbQueries->update_user_details( $user->ID, array(
								'mo2f_EmailVerification_config_status' => true,
								'user_registration_with_miniorange'    => 'SUCCESS',
								'mo2f_user_email'                      => $email,
								'mo_2factor_user_registration_status' =>'MO_2_FACTOR_PLUGIN_SETTINGS'
							) );

							update_option( 'mo_2factor_admin_registration_status', 'MO_2_FACTOR_CUSTOMER_REGISTERED_SUCCESS' );
							$enduser = new Two_Factor_Setup();
							$enduser->mo2f_update_userinfo( $email, 'OUT OF BAND EMAIL', null, 'API_2FA', true );

							update_option( 'mo2f_message', Mo2fConstants:: langTranslate( "ACCOUNT_RETRIEVED_SUCCESSFULLY" ) . ' <b>' . Mo2fConstants:: langTranslate( "EMAIL_VERFI" ) . '</b> ' . Mo2fConstants:: langTranslate( "DEFAULT_2ND_FACTOR" ) . ' <a href=\"admin.php?page=miniOrange_2_factor_settings&amp;mo2f_tab=mobile_configure\" >' . Mo2fConstants:: langTranslate( "CLICK_HERE" ) . '</a> ' . Mo2fConstants:: langTranslate( "CONFIGURE_2FA" ) );
							$this->mo_auth_show_success_message();
						} else {
							update_option( 'mo2f_message', Mo2fConstants:: langTranslate( "ERROR_CREATE_ACC_OTP" ) );
							$mo_2factor_user_registration_status = 'MO_2_FACTOR_OTP_DELIVERED_FAILURE';
							$Mo2fdbQueries->update_user_details( $user->ID, array( 'mo_2factor_user_registration_status' => $mo_2factor_user_registration_status ) );
							$this->mo_auth_show_error_message();
						}

					}

				} else {
					update_option( 'mo2f_message', Mo2fConstants:: langTranslate( "INVALID_EMAIL_OR_PASSWORD" ) );
					$mo_2factor_user_registration_status = 'MO_2_FACTOR_VERIFY_CUSTOMER';
					update_option('mo_2factor_user_registration_status',$mo_2factor_user_registration_status);
					$this->mo_auth_show_error_message();
				}


			} else {
				if ( isset( $customerKey['id'] ) && ! empty( $customerKey['id'] ) ) {
					update_option( 'mo2f_customerKey', $customerKey['id'] );
					update_option( 'mo2f_api_key', $customerKey['apiKey'] );
					update_option( 'mo2f_customer_token', $customerKey['token'] );
					update_option( 'mo2f_app_secret', $customerKey['appSecret'] );
					update_option( 'mo2f_miniorange_admin', $user->ID );
					delete_option( 'mo2f_password' );

					$email = get_option( 'mo2f_email' );

					update_option( 'mo2f_is_NC', 1 );
					update_option( 'mo2f_is_NNC', 1 );

					update_option( 'mo2f_message', Mo2fConstants:: langTranslate( "ACCOUNT_CREATED" ) );
					$mo_2factor_user_registration_status = 'MO_2_FACTOR_PLUGIN_SETTINGS';
					$Mo2fdbQueries->update_user_details( $user->ID, array(
						'mo2f_2factor_enable_2fa_byusers'     => 1,
						'user_registration_with_miniorange'   => 'SUCCESS',
						'mo2f_configured_2FA_method'          => 'NONE',
						'mo2f_user_email'                     => $email,
						'mo_2factor_user_registration_status' => $mo_2factor_user_registration_status
					) );

					update_option( 'mo_2factor_admin_registration_status', 'MO_2_FACTOR_CUSTOMER_REGISTERED_SUCCESS' );

					$enduser = new Two_Factor_Setup();
					$enduser->mo2f_update_userinfo( $email, 'NONE', null, 'API_2FA', true );

					$this->mo_auth_show_success_message();

					$mo2f_customer_selected_plan = get_option( 'mo2f_customer_selected_plan' );
					if ( ! empty( $mo2f_customer_selected_plan ) ) {
						delete_option( 'mo2f_customer_selected_plan' );
						header( 'Location: admin.php?page=mo_2fa_upgrade' );
					} else {
						header( 'Location: admin.php?page=mo_2fa_two_fa' );
					}

				} else {
					update_option( 'mo2f_message', Mo2fConstants:: langTranslate( "ERROR_CREATE_ACC_OTP" ) );
					$mo_2factor_user_registration_status = 'MO_2_FACTOR_OTP_DELIVERED_FAILURE';
					$Mo2fdbQueries->update_user_details( $user->ID, array( 'mo_2factor_user_registration_status' => $mo_2factor_user_registration_status ) );
					$this->mo_auth_show_error_message();
				}


			}
		}
	}

	public static function mo2f_get_GA_parameters($user){
		global $Mo2fdbQueries;
		$email           = $Mo2fdbQueries->get_user_detail( 'mo2f_user_email', $user->ID );
		$google_auth     = new Miniorange_Rba_Attributes();
		$gauth_name= get_option('mo2f_google_appname');
		$gauth_name = $gauth_name ? $gauth_name : 'miniOrangeAu';
		$google_response = json_decode( $google_auth->mo2f_google_auth_service( $email,$gauth_name ), true );
		if ( json_last_error() == JSON_ERROR_NONE ) {
			if ( $google_response['status'] == 'SUCCESS' ) {
				$mo2f_google_auth              = array();
				$mo2f_google_auth['ga_qrCode'] = $google_response['qrCodeData'];
				$mo2f_google_auth['ga_secret'] = $google_response['secret'];
				update_user_meta($user->ID, 'mo2f_google_auth', $mo2f_google_auth);
			}else {
				update_option( 'mo2f_message', Mo2fConstants:: langTranslate( "ERROR_DURING_USER_REGISTRATION" ) );
				do_action('mo_auth_show_error_message');
			}
		}else {
			update_option( 'mo2f_message', Mo2fConstants:: langTranslate( "ERROR_DURING_USER_REGISTRATION" ) );
			do_action('mo_auth_show_error_message');

		}
	}

	function mo_auth_show_error_message() {
		do_action('wpns_show_message', get_option( 'mo2f_message' ), 'ERROR');
	}

	function mo2f_create_user( $user, $email ) {
		global $Mo2fdbQueries;
		$email      = strtolower( $email );
		$enduser    = new Two_Factor_Setup();
		$check_user = json_decode( $enduser->mo_check_user_already_exist( $email ), true );

		if ( json_last_error() == JSON_ERROR_NONE ) {
			if ( $check_user['status'] == 'ERROR' ) {
				update_option( 'mo2f_message', Mo2fConstants:: langTranslate( $check_user['message'] ) );
				$this->mo_auth_show_error_message();
			} else {
				if ( strcasecmp( $check_user['status'], 'USER_FOUND' ) == 0 ) {

					$Mo2fdbQueries->update_user_details( $user->ID, array(
						'user_registration_with_miniorange'   => 'SUCCESS',
						'mo2f_user_email'                     => $email,
						'mo2f_configured_2FA_method'          => 'NONE',
						'mo_2factor_user_registration_status' => 'MO_2_FACTOR_PLUGIN_SETTINGS'
					) );


					delete_user_meta( $user->ID, 'user_email' );
					$enduser->mo2f_update_userinfo( $email, 'NONE', null, 'API_2FA', true );
					$message = Mo2fConstants:: langTranslate( "REGISTRATION_SUCCESS" );
					update_option( 'mo2f_message', $message );
					$this->mo_auth_show_success_message();
					header( 'Location: admin.php?page=mo_2fa_two_fa' );

				} else if ( strcasecmp( $check_user['status'], 'USER_NOT_FOUND' ) == 0 ) {
					$content = json_decode( $enduser->mo_create_user( $user, $email ), true );
					if ( json_last_error() == JSON_ERROR_NONE ) {
						if ( $content['status'] == 'ERROR' ) {
							update_option( 'mo2f_message', Mo2fConstants:: langTranslate( $content['message'] ) );
							$this->mo_auth_show_error_message();
						} else {
							if ( strcasecmp( $content['status'], 'SUCCESS' ) == 0 ) {
								delete_user_meta( $user->ID, 'user_email' );
								$Mo2fdbQueries->update_user_details( $user->ID, array(
									'user_registration_with_miniorange'   => 'SUCCESS',
									'mo2f_user_email'                     => $email,
									'mo2f_configured_2FA_method'          => 'NONE',
									'mo_2factor_user_registration_status' => 'MO_2_FACTOR_PLUGIN_SETTINGS'
								) );
								$enduser->mo2f_update_userinfo( $email, 'NONE', null, 'API_2FA', true );
								$message = Mo2fConstants:: langTranslate( "REGISTRATION_SUCCESS" );
								update_option( 'mo2f_message', $message );
								$this->mo_auth_show_success_message();
								header( 'Location: admin.php?page=mo_2fa_two_fa' );

							} else {
								update_option( 'mo2f_message', Mo2fConstants:: langTranslate( "ERROR_DURING_USER_REGISTRATION" ) );
								$this->mo_auth_show_error_message();
							}
						}
					} else {
						update_option( 'mo2f_message', Mo2fConstants:: langTranslate( "ERROR_DURING_USER_REGISTRATION" ) );
						$this->mo_auth_show_error_message();
					}
				} else {
					update_option( 'mo2f_message', Mo2fConstants:: langTranslate( "ERROR_DURING_USER_REGISTRATION" ) );
					$this->mo_auth_show_error_message();
				}
			}
		} else {
			update_option( 'mo2f_message', Mo2fConstants:: langTranslate( "ERROR_DURING_USER_REGISTRATION" ) );
			$this->mo_auth_show_error_message();
		}
	}

	function mo2f_get_qr_code_for_mobile( $email, $id, $session_id = null ) {

		$registerMobile = new Two_Factor_Setup();
		$content        = $registerMobile->register_mobile( $email );

		$response       = json_decode( $content, true );
		if ( json_last_error() == JSON_ERROR_NONE ) {
			if ( $response['status'] == 'ERROR' ) {
				update_option( 'mo2f_message', Mo2fConstants:: langTranslate( $response['message'] ) );
				$session_variables = array( 'mo2f_qrCode', 'mo2f_transactionId', 'mo2f_show_qr_code' );
				MO2f_Utility::unset_session_variables( $session_variables );
				delete_option( 'mo2f_transactionId' );
				$this->mo_auth_show_error_message();

			} else {
				if ( $response['status'] == 'IN_PROGRESS' ) {
					update_option( 'mo2f_message', Mo2fConstants::langTranslate( "SCAN_QR_CODE" ) );
					MO2f_Utility::mo2f_set_transient($session_id, 'mo2f_qrCode', $response['qrCode']);
					MO2f_Utility::mo2f_set_transient($session_id, 'mo2f_transactionId', $response['txId']);
					update_user_meta($id, 'mo2f_transactionId', $response['txId']);
					MO2f_Utility::mo2f_set_transient($session_id, 'mo2f_show_qr_code', 'MO_2_FACTOR_SHOW_QR_CODE');


					$this->mo_auth_show_success_message();
				} else {
					update_option( 'mo2f_message', Mo2fConstants:: langTranslate( "ERROR_DURING_PROCESS" ) );
					$session_variables = array( 'mo2f_qrCode', 'mo2f_transactionId', 'mo2f_show_qr_code' );
					MO2f_Utility::unset_session_variables( $session_variables );
					delete_option( 'mo2f_transactionId' );
					$this->mo_auth_show_error_message();
				}
			}
		}
	}

	function mo2f_save_2_factor_method( $user, $mo2f_configured_2FA_method ) {
		global $Mo2fdbQueries;
		$email          = $Mo2fdbQueries->get_user_detail( 'mo2f_user_email', $user->ID );
		$enduser        = new Two_Factor_Setup();
		$phone          = $Mo2fdbQueries->get_user_detail( 'mo2f_user_phone', $user->ID );
		$current_method = MO2f_Utility::mo2f_decode_2_factor( $mo2f_configured_2FA_method, "server" );

		$response = json_decode( $enduser->mo2f_update_userinfo( $email, $current_method, $phone, null, null ), true );
		if ( json_last_error() == JSON_ERROR_NONE ) {
			if ( $response['status'] == 'ERROR' ) {
				update_option( 'mo2f_message', Mo2fConstants:: langTranslate( $response['message'] ) );
				$this->mo_auth_show_error_message();
			} else if ( $response['status'] == 'SUCCESS' ) {
				$configured_2fa_method = '';
				if($mo2f_configured_2FA_method =='')
					$configured_2fa_method = $Mo2fdbQueries->get_user_detail( 'mo2f_configured_2FA_method', $user->ID );
				else
					$configured_2fa_method = $mo2f_configured_2FA_method;
				if ( in_array( $configured_2fa_method, array( "Google Authenticator", "Authy Authenticator" ) ) ) {
					update_user_meta( $user->ID, 'mo2f_external_app_type', $configured_2fa_method );
				}

				$Mo2fdbQueries->update_user_details( $user->ID, array(
					'mo_2factor_user_registration_status' => 'MO_2_FACTOR_PLUGIN_SETTINGS'
				) );
				delete_user_meta( $user->ID, 'configure_2FA' );
				
				if($configured_2fa_method == 'OTP Over Email' or $configured_2fa_method=='OTP Over SMS')
				{
					update_option( 'mo2f_message', Mo2fConstants:: langTranslate( $configured_2fa_method ) . ' ' . Mo2fConstants:: langTranslate( "SET_2FA_otp" ) );
				}
				else
					update_option( 'mo2f_message', Mo2fConstants:: langTranslate( $configured_2fa_method ) . ' ' . Mo2fConstants:: langTranslate( "SET_2FA" ) );


				$this->mo_auth_show_success_message();
			} else {
				update_option( 'mo2f_message', Mo2fConstants:: langTranslate( "ERROR_DURING_PROCESS" ) );
				$this->mo_auth_show_error_message();
			}
		} else {
			update_option( 'mo2f_message', Mo2fConstants:: langTranslate( "INVALID_REQ" ) );
			$this->mo_auth_show_error_message();
		}
	}

	function miniorange_email_verification_call( $current_user ) {
		global $Mo2fdbQueries,$imagePath;
		$email           = $Mo2fdbQueries->get_user_detail( 'mo2f_user_email', $current_user->ID );

		if(MO2F_IS_ONPREM)
		{

			$challengeMobile 		= new Customer_Setup();
			$is_flow_driven_setup 	= ! ( get_user_meta( $current_user->ID, 'current_modal', true ) ) ? 0 : 1;
			
			$subject 	= '2-Factor Authentication(Email verification)';
			$headers 	= array('Content-Type: text/html; charset=UTF-8');
			$txid 		= '';
			$otpToken 	= '';
			$otpTokenD 	= '';
			for($i=1;$i<7;$i++)
			{
				$otpToken 	.= rand(0,9);
				$txid 		.= rand(100,999);
				$otpTokenD 	.= rand(0,9);
			}
			$otpTokenH 				= hash('sha512',$otpToken);
			$otpTokenDH 			= hash('sha512', $otpTokenD);


			update_user_meta($current_user->ID, 'mo2f_transactionId', $txid);
			update_user_meta($current_user->ID, 'otpToken', $otpToken);


			$userID 				= hash('sha512',$current_user->ID);
			update_site_option($userID,$otpTokenH);
			update_site_option($txid,3);
			$userIDd = $userID . 'D';
			update_site_option($userIDd,$otpTokenDH);
			$url =  get_site_option('siteurl').'/wp-login.php?'; //login page can change
			$message =  '<table cellpadding="25" style="margin:0px auto">
			<tbody>
			<td>
			<td>
			<table cellpadding="24" width="584px" style="margin:0 auto;max-width:584px;background-color:#f6f4f4;border:1px solid #a8adad">
			<tbody>
			<td>
			<td><img src="'.$imagePath.'includes/images/xecurify-logo.png" alt="Xecurify" style="color:#5fb336;text-decoration:none;display:block;width:auto;height:auto;max-height:35px" class="CToWUd"></td>
			</tr>
			</tbody>
			</table>
			<table cellpadding="24" style="background:#fff;border:1px solid #a8adad;width:584px;border-top:none;color:#4d4b48;font-family:Arial,Helvetica,sans-serif;font-size:13px;line-height:18px">
			<tbody>
			<td>
			<td>
			<p style="margin-top:0;margin-bottom:20px">Dear Customers,</p>
			<p style="margin-top:0;margin-bottom:10px">You initiated a transaction <b>WordPress 2 Factor Authentication Plugin</b>:</p>
			<p style="margin-top:0;margin-bottom:10px">To accept, <a href="'.$url.'userID='.$userID.'&amp;accessToken='.$otpTokenH.'&amp;secondFactorAuthType=OUT+OF+BAND+EMAIL&amp;Txid='.$txid.'&amp;user='.$email.'" target="_blank" data-saferedirecturl="https://www.google.com/url?q=https://login.xecurify.com/moas/rest/validate-otp?customerKey%3D182589%26otpToken%3D735705%26secondFactorAuthType%3DOUT%2BOF%2BBAND%2BEMAIL%26user%3D'.$email.'&amp;source=gmail&amp;ust=1569905139580000&amp;usg=AFQjCNExKCcqZucdgRm9-0m360FdYAIioA">Accept Transaction</a></p>
			<p style="margin-top:0;margin-bottom:10px">To deny, <a href="'.$url.'userID='.$userID.'&amp;accessToken='.$otpTokenDH.'&amp;secondFactorAuthType=OUT+OF+BAND+EMAIL&amp;Txid='.$txid.'&amp;user='.$email.'" target="_blank" data-saferedirecturl="https://www.google.com/url?q=https://login.xecurify.com/moas/rest/validate-otp?customerKey%3D182589%26otpToken%3D735705%26secondFactorAuthType%3DOUT%2BOF%2BBAND%2BEMAIL%26user%3D'.$email.'&amp;source=gmail&amp;ust=1569905139580000&amp;usg=AFQjCNExKCcqZucdgRm9-0m360FdYAIioA">Deny Transaction</a></p><div><div class="adm"><div id="q_31" class="ajR h4" data-tooltip="Hide expanded content" aria-label="Hide expanded content" aria-expanded="true"><div class="ajT"></div></div></div><div class="im">
			<p style="margin-top:0;margin-bottom:15px">Thank you,<br>miniOrange Team</p>
			<p style="margin-top:0;margin-bottom:0px;font-size:11px">Disclaimer: This email and any files transmitted with it are confidential and intended solely for the use of the individual or entity to whom they are addressed.</p>
			</div></div></td>
			</tr>
			</tbody>
			</table>
			</td>
			</tr>
			</tbody>
			</table>';
			$result = wp_mail($email,$subject,$message,$headers);
			if($result){
				$time = "time".$txid;
				$currentTimeInMillis = round(microtime(true) * 1000);
				update_site_option($time,$currentTimeInMillis);
				update_site_option( 'mo2f_message',  Mo2fConstants::langTranslate("VERIFICATION_EMAIL_SENT") .'<b> ' . $email . '</b>. ' .  Mo2fConstants::langTranslate("ACCEPT_LINK_TO_VERIFY_EMAIL"));
				
			}else{
				update_site_option( 'mo2f_message',  Mo2fConstants::langTranslate("ERROR_DURING_PROCESS_EMAIL"));
				$this->mo_auth_show_error_message();
			}

		}
		else
		{
			global $Mo2fdbQueries;
			$challengeMobile = new Customer_Setup();
			$email           = $Mo2fdbQueries->get_user_detail( 'mo2f_user_email', $current_user->ID );
			$content         = $challengeMobile->send_otp_token( $email, 'OUT OF BAND EMAIL', $this->defaultCustomerKey, $this->defaultApiKey );
			$response        = json_decode( $content, true );
			if ( json_last_error() == JSON_ERROR_NONE ) { /* Generate out of band email */
				if ( $response['status'] == 'ERROR' ) {
					update_option( 'mo2f_message', Mo2fConstants:: langTranslate( $response['message'] ) );
					$this->mo_auth_show_error_message();
				} else {
					if ( $response['status'] == 'SUCCESS' ) {
						update_user_meta($current_user->ID, 'mo2f_transactionId', $response['txId']);


						update_option( 'mo2f_transactionId', $response['txId'] );
						update_option( 'mo2f_message', Mo2fConstants:: langTranslate( "VERIFICATION_EMAIL_SENT" ) . '<b> ' . $email . '</b>. ' . Mo2fConstants:: langTranslate( "ACCEPT_LINK_TO_VERIFY_EMAIL" ) );
						$this->mo_auth_show_success_message();
					} else {
						update_option( 'mo2f_message', Mo2fConstants:: langTranslate( "ERROR_DURING_PROCESS" ) );
						$this->mo_auth_show_error_message();
					}
				}
			} else {
				update_option( 'mo2f_message', Mo2fConstants:: langTranslate( "INVALID_REQ" ) );
				$this->mo_auth_show_error_message();
			}
		}
	}
	static function low_otp_alert( $auth_type) {
		global $Mo2fdbQueries,$imagePath;
		$email = get_option('mo2f_email')?get_option('mo2f_email'):get_option('admin_email');
		if(MO2F_IS_ONPREM)
		{
			$count=0;
			if($auth_type =="email"){
				$subject = 'Two Factor Authentication(Low Email Alert)';
				$count = get_site_option('cmVtYWluaW5nT1RQ')-1; //database value is updated after function call
				$string = 'Email';
			}
			else if($auth_type =="sms"){
				$subject = 'Two Factor Authentication(Low SMS Alert)';
				$count = get_site_option('cmVtYWluaW5nT1RQVHJhbnNhY3Rpb25z')-1; //database value is updated after function call
				$string = 'SMS';
			}
			$admin_url = network_site_url();
			$url = explode('/wp-admin/admin.php?page=mo_2fa_upgrade', $admin_url);
			$headers = array('Content-Type: text/html; charset=UTF-8');
			$headers[] = 'Cc: 2fasupport <2fasupport@xecurify.com>';
			$message =  '<table cellpadding="25" style="margin:0px auto">
			<tbody>
			<td>
			<td>
			<table cellpadding="24" width="584px" style="margin:0 auto;max-width:584px;background-color:#f6f4f4;border:1px solid #a8adad">
			<tbody>
			<td>
			<td><img src="'.$imagePath.'includes/images/xecurify-logo.png" alt="Xecurify" style="color:#5fb336;text-decoration:none;display:block;width:auto;height:auto;max-height:35px" class="CToWUd"></td>
			</tr>
			</tbody>
			</table>
			<table cellpadding="24" style="background:#fff;border:1px solid #a8adad;width:584px;border-top:none;color:#4d4b48;font-family:Arial,Helvetica,sans-serif;font-size:13px;line-height:18px">
			<tbody>
			<td>
			<td>
			<p style="margin-top:0;margin-bottom:20px">Dear Customer,</p>
			<p style="margin-top:0;margin-bottom:20px"> You are going to exhaust all your '.$string.'. You have only <b>'.$count.'</b> '.$string.' remaining. You can recharge or add '.$string.' to your account: <a href='.MoWpnsConstants::rechargeLink.'>Recharge</a></p>
			<p style="margin-top:0;margin-bottom:10px">After Recharge you can continue using your current plan. To know more about our plans you can also visit our site: <a href='.$url[0].'/wp-admin/admin.php?page=mo_2fa_upgrade>2FA Plans</a>.</p>
			<p style="margin-top:0;margin-bottom:10px">If you do not wish to recharge, we advise you to <a href='.$url[0].'/wp-admin/admin.php?page=mo_2fa_two_fa>change the 2FA method</a> before you have no '.$string.' left. In case you get locked out, please use this guide to gain access: <a href='.MoWpnsConstants::OnPremiseLockedOut.'>Guide link</a></p>
			<p style="margin-top:0;margin-bottom:20px">For more information, you can contact us directly at 2fasupport@xecurify.com.</p>
			<p style="margin-top:0;margin-bottom:15px">Thank you,<br>miniOrange Team</p>
			<p style="margin-top:0;margin-bottom:0px;font-size:11px">Disclaimer: This email and any files transmitted with it are confidential and intended solely for the use of the individual or entity to whom they are addressed.</p>
			</div></div></td>
			</tr>
			</tbody>
			</table>
			</td>
			</tr>
			</tbody>
			</table>';
			$result = wp_mail($email,$subject,$message,$headers);
			if($result){
				$currentTimeInMillis = round(microtime(true) * 1000);
				update_site_option( 'mo2f_message',  Mo2fConstants::langTranslate("VERIFICATION_EMAIL_SENT") .'<b> ' . $email . '</b>. ' .  Mo2fConstants::langTranslate("ACCEPT_LINK_TO_VERIFY_EMAIL"));

			}

		}

	}
function mo_auth_activate() {
	error_log(' miniOrange Two Factor Plugin Activated');

	$get_encryption_key = MO2f_Utility::random_str(16);
	update_option('mo2f_encryption_key',$get_encryption_key);

	if ( get_option( 'mo2f_customerKey' ) && ! MoWpnsUtility::get_mo2f_db_option('mo2f_is_NC', 'get_option') ) {
		update_option( 'mo2f_is_NC', 0 );
	} else {
		update_option( 'mo2f_is_NC', 1 );
		update_option( 'mo2f_is_NNC', 1 );
	}

	do_action('mo2f_network_create_db');

	update_option( 'mo2f_host_name', 'https://login.xecurify.com' );
	update_option('mo2f_data_storage',null);
	global $Mo2fdbQueries;
	$Mo2fdbQueries->mo_plugin_activate();


}

function mo_get_2fa_shorcode( $atts ) {
	if ( ! is_user_logged_in() && mo2f_is_customer_registered() ) {
		$mo2f_shorcode = new MO2F_ShortCode();
		$html          = $mo2f_shorcode->mo2FAFormShortCode( $atts );

		return $html;
	}
}

function mo_get_login_form_shortcode( $atts ) {
	if ( ! is_user_logged_in() && mo2f_is_customer_registered() ) {
		$mo2f_shorcode = new MO2F_ShortCode();
		$html          = $mo2f_shorcode->mo2FALoginFormShortCode( $atts );

		return $html;
	}
}
}

function mo2f_is_customer_registered() {
	$email       = get_option( 'mo2f_email' );
	$customerKey = get_option( 'mo2f_customerKey' );
	if ( ! $email || ! $customerKey || ! is_numeric( trim( $customerKey ) ) ) {
		return 0;
	} else {
		return 1;
	}
}
new Miniorange_Authentication;
?>