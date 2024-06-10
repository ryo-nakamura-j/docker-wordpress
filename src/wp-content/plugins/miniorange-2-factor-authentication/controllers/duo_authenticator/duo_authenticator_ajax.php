<?php
class Mo_2f_duo_authenticator
{
	function __construct(){
		add_action( 'admin_init'  , array( $this, 'mo2f_duo_authenticator_functions' ) );

	}

	public function mo2f_duo_authenticator_functions(){
		add_action('wp_ajax_mo2f_duo_authenticator_ajax', array( $this, 'mo2f_duo_authenticator_ajax' ));
		add_action( 'wp_ajax_nopriv_mo2f_duo_ajax_request', array($this,'mo2f_duo_ajax_request') );
	}

	public function mo2f_duo_ajax_request(){
		
		switch (sanitize_text_field(wp_unslash($_POST['call_type']))) {
		      case "check_duo_push_auth_status":
		      $this->mo2f_check_duo_push_auth_status();
		      break;
		}
	}

	public function mo2f_duo_authenticator_ajax(){
		switch (sanitize_text_field(wp_unslash($_POST['call_type'])))
		{
		   	  case "check_duo_push_auth_status":
		      $this->mo2f_check_duo_push_auth_status();
		      break;
		}
	}

	
	function mo2f_check_duo_push_auth_status(){

        if(!wp_verify_nonce(sanitize_text_field($_POST['nonce']),'miniorange-2-factor-duo-nonce'))
	       {
				wp_send_json("ERROR");
				exit;
	       }else{
		       include_once dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR.'handler'.DIRECTORY_SEPARATOR.'twofa'.DIRECTORY_SEPARATOR.'two_fa_duo_handler.php';
		       $ikey = get_site_option('mo2f_d_integration_key');
		       $skey = get_site_option('mo2f_d_secret_key');
		       $host = get_site_option('mo2f_d_api_hostname');
		       $current_user = wp_get_current_user();
		       
		       $session_id_encrypt = isset($_POST['session_id_encrypt']) ? sanitize_text_field($_POST['session_id_encrypt']) : '';
		       $user_id =  MO2f_Utility::mo2f_get_transient( $session_id_encrypt, 'mo2f_current_user_id' );
		       $user_email = get_user_meta($user_id,'current_user_email');
		       $user_email = isset($user_email[0])? $user_email[0]:'';
		      
		       if($user_email == '' || empty($user_email))
		        $user_email = sanitize_email($current_user->user_email);

		       $device['device'] = 'auto';
			   $auth_response = mo2f_duo_auth( $user_email,'push',$device , $skey, $ikey, $host,true);

		      if(isset($auth_response['response']['response']['result']) && $auth_response['response']['response']['result'] == 'allow'){
		        wp_send_json('SUCCESS');
			   }else{
			   
		        wp_send_json('ERROR');
			   }
			}	

	
    }

}
new Mo_2f_duo_authenticator();
?>
