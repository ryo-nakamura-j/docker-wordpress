<?php
    $setup_dirName = dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.'twofa'.DIRECTORY_SEPARATOR.'setup'.DIRECTORY_SEPARATOR;
    $test_dirName = dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.'twofa'.DIRECTORY_SEPARATOR.'test'.DIRECTORY_SEPARATOR;
    include $setup_dirName.'setup_google_authenticator.php';
    include $setup_dirName.'setup_google_authenticator_onpremise.php';
    include $setup_dirName.'setup_authy_authenticator.php';
    include $setup_dirName.'setup_kba_questions.php';
    include $setup_dirName.'setup_miniorange_authenticator.php';
    include $setup_dirName.'setup_otp_over_sms.php';
    include $setup_dirName.'setup_otp_over_telegram.php';
    include $setup_dirName.'setup_duo_authenticator.php';
    include $test_dirName.'test_twofa_email_verification.php';
    include $test_dirName.'test_twofa_duo_authenticator.php';
    include $test_dirName.'test_twofa_google_authy_authenticator.php';
    include $test_dirName.'test_twofa_miniorange_qrcode_authentication.php';
    include $test_dirName.'test_twofa_kba_questions.php';
    include $test_dirName.'test_twofa_miniorange_push_notification.php';
    include $test_dirName.'test_twofa_miniorange_soft_token.php';
    include $test_dirName.'test_twofa_otp_over_sms.php';
    include $test_dirName.'test_twofa_otp_over_Telegram.php';
    
	function mo2f_decode_2_factor( $selected_2_factor_method, $decode_type ) {

		if ( $selected_2_factor_method == 'NONE' ) {
			return $selected_2_factor_method;
		}else if($selected_2_factor_method == "OTP Over Email")
		{
			$selected_2_factor_method = "EMAIL";	
		}

		$wpdb_2fa_methods = array(
			"miniOrangeQRCodeAuthentication" => "miniOrange QR Code Authentication",
			"miniOrangeSoftToken"            => "miniOrange Soft Token",
			"miniOrangePushNotification"     => "miniOrange Push Notification",
			"GoogleAuthenticator"            => "Google Authenticator",
			"AuthyAuthenticator"             => "Authy Authenticator",
			"SecurityQuestions"              => "Security Questions",
			"EmailVerification"              => "Email Verification",
			"OTPOverSMS"                     => "OTP Over SMS",
			"OTPOverEmail"					 => "OTP Over Email",
			"EMAIL"                          => "OTP Over Email",
		);

		$server_2fa_methods = array(
			"miniOrange QR Code Authentication" => "MOBILE AUTHENTICATION",
			"miniOrange Soft Token"             => "SOFT TOKEN",
			"miniOrange Push Notification"      => "PUSH NOTIFICATIONS",
			"Google Authenticator"              => "GOOGLE AUTHENTICATOR",
			"Authy Authenticator"               => "GOOGLE AUTHENTICATOR",
			"Security Questions"                => "KBA",
			"Email Verification"                => "OUT OF BAND EMAIL",
			"OTP Over SMS"                      => "SMS",
			"EMAIL"                             => "OTP Over Email",
			"OTPOverEmail"					 	=> "OTP Over Email"
		);

		$server_to_wpdb_2fa_methods = array(
			"MOBILE AUTHENTICATION" => "miniOrange QR Code Authentication",
			"SOFT TOKEN"            => "miniOrange Soft Token",
			"PUSH NOTIFICATIONS"    => "miniOrange Push Notification",
			"GOOGLE AUTHENTICATOR"  => "Google Authenticator",
			"KBA"                   => "Security Questions",
			"OUT OF BAND EMAIL"     => "Email Verification",
			"SMS"                   => "OTP Over SMS",
			"EMAIL"                 => "OTP Over Email",
			"OTPOverEmail"			=> "OTP Over Email",
			"OTP OVER EMAIL"		=> "OTP Over Email",
		);
        $methodname='';
		if ( $decode_type == "wpdb" ) {
			$methodname = isset($wpdb_2fa_methods[ $selected_2_factor_method ])?$wpdb_2fa_methods[ $selected_2_factor_method ]:$selected_2_factor_method;
		} else if ( $decode_type == "server" ) {
			$methodname = isset($server_2fa_methods[ $selected_2_factor_method ])?$server_2fa_methods[ $selected_2_factor_method ]:$selected_2_factor_method;
		} else {
			$methodname = isset($server_to_wpdb_2fa_methods[ $selected_2_factor_method ])?$server_to_wpdb_2fa_methods[ $selected_2_factor_method ]:$selected_2_factor_method;
		}
		return $methodname;

	}


	function mo2f_create_2fa_form( $user, $category, $auth_methods, $can_display_admin_features='' ) {
	global $Mo2fdbQueries;

	$miniorange_authenticator = array(
        "miniOrange QR Code Authentication",
        "miniOrange Soft Token",
        "miniOrange Push Notification",
        );
	$all_two_factor_methods = array(
		"miniOrange Authenticator",
		"Google Authenticator",
		"Security Questions",
		"OTP Over SMS",
		"OTP Over Email",
		"OTP Over Telegram",
		"Duo Authenticator",
		"Authy Authenticator",
		"Email Verification",
		"OTP Over SMS and Email",
		"Hardware Token"		
	);
	$two_factor_methods_descriptions = array(
	        ""=>"<b>All methods in the FREE Plan in addition to the following methods.</b>",
		"miniOrange Authenticator" => "Scan the QR code from the account in your miniOrange Authenticator App to login.",
        "miniOrange Soft Token"             => "Use One Time Password / Soft Token shown in the miniOrange Authenticator App",
        "miniOrange Push Notification"      => "A Push notification will be sent to the miniOrange Authenticator App for your account,
		 Accept it to log in",
        "Google Authenticator"              => "Use One Time Password shown in <b>Google/Authy/LastPass Authenticator App</b> to login",
        "Security Questions"                => "Configure and Answer Three Security Questions to login",
        "OTP Over SMS"                      => "A One Time Passcode (OTP) will be sent to your Phone number",
        "OTP Over Email"                    => "A One Time Passcode (OTP) will be sent to your Email address",
        "Authy Authenticator"               => "Enter Soft Token/ One Time Password from the Authy Authenticator App",
        "Email Verification"                => "Accept the verification link sent to your email address",
        "OTP Over SMS and Email"            => "A One Time Passcode (OTP) will be sent to your Phone number and Email address",
        "Hardware Token"                    => "Enter the One Time Passcode on your Hardware Token",
        "OTP Over Whatsapp"                 => "Enter the One Time Passcode sent to your WhatsApp account.",
        "OTP Over Telegram"                 => "Enter the One Time Passcode sent to your Telegram account",
        "Duo Authenticator"                 => "A Push notification will be sent to the Duo Authenticator App");
	$two_factor_methods_doc = array(
			"Security Questions"            	=> "https://developers.miniorange.com/docs/security/wordpress/wp-security/step-by-setup-guide-to-set-up-security-question",
			"Google Authenticator" 				=> "https://developers.miniorange.com/docs/security/wordpress/wp-security/google-authenticator",
			"Email Verification" 				=> "https://developers.miniorange.com/docs/security/wordpress/wp-security/email_verification",
			"miniOrange Soft Token" 			=> "https://developers.miniorange.com/docs/security/wordpress/wp-security/step-by-setup-guide-to-set-up-miniorange-soft-token",
			"miniOrange Push Notification"  	=> "https://developers.miniorange.com/docs/security/wordpress/wp-security/step-by-setup-guide-to-set-up-miniorange-push-notification",
			"Authy Authenticator" 				=> "",
			"OTP Over SMS" 						=> "https://developers.miniorange.com/docs/security/wordpress/wp-security/step-by-setup-guide-to-set-up-otp-over-sms",
			"OTP Over Email" 					=> "https://developers.miniorange.com/docs/security/wordpress/wp-security/otp_over_email",
			"OTP Over SMS and Email" 			=> "",
			"Hardware Token"  					=> "",
			"OTP Over Whatsapp" 				=> "https://developers.miniorange.com/docs/security/wordpress/wp-security/otp-over-whatsapp",
			"OTP Over Telegram"					=> "https://developers.miniorange.com/docs/security/wordpress/wp-security/otp-over-telegram"
		);
	$two_factor_methods_video = array(
			"Security Questions"            	=> "https://www.youtube.com/watch?v=pXPqQ047o-0",
			"Google Authenticator" 				=> "https://www.youtube.com/watch?v=6je2iARqrcs",
			"miniOrange Authenticator" 			=> "https://www.youtube.com/watch?v=oRaGtKxouiI",
			"Email Verification" 				=> "https://www.youtube.com/watch?v=OacJWBYx_AE",
			"miniOrange Soft Token" 			=> "https://www.youtube.com/watch?v=9HV8V4f80k8",
			"miniOrange Push Notification"  	=> "https://www.youtube.com/watch?v=it_dAhFcxvw",
			"Authy Authenticator" 				=> "https://www.youtube.com/watch?v=fV-VnC_5Q5c",
			"OTP Over SMS" 						=> "https://www.youtube.com/watch?v=ag_E1Bmen-c",
			"OTP Over Email" 					=> "",
			"OTP Over SMS and Email" 			=> "",
			"Hardware Token"  					=> "",
			"Duo Authenticator" 				=> "https://www.youtube.com/watch?v=AZnBjf_E2cA",
			"OTP Over Telegram"					=> "https://www.youtube.com/watch?v=3yVs67LnYts",
		);

	$two_factor_methods_EC = array_slice( $all_two_factor_methods, 0, 10 );
	$two_factor_methods_NC = array_slice( $all_two_factor_methods, 0, 9 );
	if(MO2F_IS_ONPREM or $category != 'free_plan')
	{
		$all_two_factor_methods = array(
		"Security Questions",
		"Google Authenticator",
		"Email Verification",
		"miniOrange Authenticator",
		"Authy Authenticator",
		"OTP Over SMS",
		"OTP Over Email",
		"OTP Over SMS and Email",
		"Hardware Token",
		"OTP Over Whatsapp",
		"OTP Over Telegram",
		"Duo Authenticator"
		);
		$two_factor_methods_descriptions = array(
	        ""=>"<b>All methods in the FREE Plan in addition to the following methods.</b>",
            "miniOrange QR Code Authentication" => "A QR Code will be displayed in the miniOrange Authenticator App for your account,
		scan it to log in",
		"miniOrange Authenticator" 			=> 'Supports methods like soft token, QR code Authentication, Push Notification',
            "miniOrange Push Notification"      => "A Push notification will be sent to the miniOrange Authenticator App for your account,
		 Accept it to log in",
            "Google Authenticator"              => "Use One Time Password shown in <b>Google/Authy/LastPass Authenticator App</b> to login",
            "Security Questions"                => "Configure and Answer Three Security Questions to login",
            "OTP Over SMS"                      => "A One Time Passcode (OTP) will be sent to your Phone number",
            "OTP Over Email"                    => "A One Time Passcode (OTP) will be sent to your Email address",
            "Authy Authenticator"               => "Enter Soft Token/ One Time Password from the Authy Authenticator App",
            "Email Verification"                => "Accept the verification link sent to your email address",
            "OTP Over SMS and Email"            => "A One Time Passcode (OTP) will be sent to your Phone number and Email address",
            "Hardware Token"                    => "Enter the One Time Passcode on your Hardware Token",
            "OTP Over Whatsapp"                 => "Enter the One Time Passcode sent to your WhatsApp account.",
            "OTP Over Telegram"                 => "Enter the One Time Passcode sent to your Telegram account",
            "Duo Authenticator"                 => "A Push notification will be sent to the Duo Authenticator App"
        );
	}

	$is_customer_registered = $Mo2fdbQueries->get_user_detail( 'user_registration_with_miniorange', $user->ID ) == 'SUCCESS' ? true : false;
	$can_user_configure_2fa_method = $can_display_admin_features || ( !$can_display_admin_features && $is_customer_registered );
	$is_NC = MoWpnsUtility::get_mo2f_db_option('mo2f_is_NC', 'get_option');
	$is_EC = ! $is_NC;

	$form = '<div class="overlay1" id="overlay" hidden ></div>';
	$form .= '<form name="f" method="post" action="" id="mo2f_save_' . $category . '_auth_methods_form">
                        <div id="mo2f_' . $category . '_auth_methods" >
                            <br>
                            <table class="mo2f_auth_methods_table">';

	$configured_auth_method  = $Mo2fdbQueries->get_user_detail( 'mo2f_configured_2FA_method', $user->ID );
	$selected_miniorange_method = false;
	if(in_array($configured_auth_method, $miniorange_authenticator)){
		$selected_miniorange_method = true;
	}
	for ( $i = 0; $i < count( $auth_methods ); $i ++ ) {

		$form .= '<tr>';
		for ( $j = 0; $j < count( $auth_methods[ $i ] ); $j ++ ) {
			$auth_method             = $auth_methods[ $i ][ $j ];
			if(MO2F_IS_ONPREM and $category =='free_plan')
			{
				
				if($auth_method != 'Email Verification' and $auth_method != 'Security Questions' and $auth_method != 'Google Authenticator' and $auth_method !='miniOrange QR Code Authentication' and $auth_method !='miniOrange Soft Token' and $auth_method != 'miniOrange Push Notification' and $auth_method != 'OTP Over SMS' and $auth_method != 'OTP Over Email' and $auth_method != 'Duo Authenticator')
				{
					//continue;
				}
			}
			$auth_method_abr         = str_replace( ' ', '', $auth_method );
			$configured_auth_method  = $Mo2fdbQueries->get_user_detail( 'mo2f_configured_2FA_method', $user->ID );
			$is_auth_method_selected = ( $configured_auth_method == $auth_method ? true : false );
			if($auth_method == 'miniOrange Authenticator' && $selected_miniorange_method )
				$is_auth_method_selected = true;
			$is_auth_method_av = false;
			if ( ( $is_EC && in_array( $auth_method, $two_factor_methods_EC ) ) ||
			     ( $is_NC && in_array( $auth_method, $two_factor_methods_NC ) ) ) {
				$is_auth_method_av = true;
			}
			
			$thumbnail_height = $is_auth_method_av && $category == 'free_plan' ? 190 : 160;
            $is_image = $auth_method == "" ? 0 :1;

            $form .= '<td class="mo2f_column">
                        <div class="mo2f_thumbnail" id="'.$auth_method_abr.'_thumbnail_2_factor" style="height:' . $thumbnail_height . 'px; ';
                        if(MO2F_IS_ONPREM)
                        {
                        	$iscurrentMethod = 0;
                        	$currentMethod = $configured_auth_method;
                        	if($currentMethod == $auth_method)
                        		$iscurrentMethod = 1;

                        	$form .= $iscurrentMethod ? '#07b52a' : 'var(--mo2f-theme-blue)';
                        	$form .= $iscurrentMethod ? '#07b52a' : 'var(--mo2f-theme-blue)';
                        	$form .= ';">';
	                	}
	                	else
	                	{
	                		$form .= $is_auth_method_selected ? '#07b52a' : 'var(--mo2f-theme-blue)';
                        	$form .= $is_auth_method_selected ? '#07b52a' : 'var(--mo2f-theme-blue)';
                        	$form .= ';">';
	                	
	                	}
	                	$form .= '<div>
			                    <div class="mo2f_thumbnail_method" style="width:100%";>
			                        <div style="width: 17%; float:left;padding-top:20px;padding-left:20px;">';

            if($is_image){
	            $form .= '<img src="' . plugins_url( "includes/images/authmethods/" . $auth_method_abr . ".png", dirname(dirname(__FILE__ ))) . '" style="width: 50px;height: 50px !important; line-height: 80px; border-radius:10px; overflow:hidden" />';
            }

            $form .= '</div>
                        <div class="mo2f_thumbnail_method_desc" style="width: 75%;">';
                 			 switch ($auth_method) {
	            	case 'Google Authenticator':
	 					$form .='   <span style="float:right">
				         	<a href='.$two_factor_methods_doc[$auth_method].' target="_blank">
				         	<span title="View Setup Guide" class="dashicons dashicons-text-page" style="font-size:19px;color:#413c69;float: right;"></span>
				         	</a>
				         	<a href='.$two_factor_methods_video[$auth_method].' target="_blank">
				         	<span title="Watch Setup Video" class="dashicons dashicons-video-alt3" style="font-size:18px;color:red;float: right;    margin-right: 5px;"></span>
				         	</a>
				         </span>';	
				    break;

				    case 'Security Questions':
							$form .='   <span style="float:right">
				         	<a href='.$two_factor_methods_doc[$auth_method].' target="_blank">
				         	<span title="View Setup Guide" class="dashicons dashicons-text-page" style="font-size:19px;color:#413c69;float: right;"></span>
				           	</a>
				           	<a href='.$two_factor_methods_video[$auth_method].' target="_blank">
				         	<span title="Watch Setup Video" class="dashicons dashicons-video-alt3" style="font-size:18px;color:red;float: right;    margin-right: 5px;"></span>
				         	</a>

				  
				         </span>';		
				       break;
	            	
	            	case 'OTP Over SMS':
	                      $form .='   <span style="float:right">
				         	<a href='.$two_factor_methods_doc[$auth_method].' target="_blank">
				         	<span title="View Setup Guide" class="dashicons dashicons-text-page" style="font-size:19px;color:#413c69;float: right;"></span>
				         	
				         	</a>
				         	<a href='.$two_factor_methods_video[$auth_method].' target="_blank">
				         	<span title="Watch Setup Video" class="dashicons dashicons-video-alt3" style="font-size:18px;color:red;float: right;    margin-right: 5px;"></span>
				         	</a>
				         
				         </span>';
				    break;


			     	case 'miniOrange Soft Token':
			     	$form .='   <span style="float:right">
				         	<a href='.$two_factor_methods_doc[$auth_method].' target="_blank">
				         	<span title="View Setup Guide" class="dashicons dashicons-text-page" style="font-size:19px;color:#413c69;float: right;"></span>
				         	
				         	</a>

				         	<a href='.$two_factor_methods_video[$auth_method].' target="_blank">
				         	<span title="Watch Setup Video" class="dashicons dashicons-video-alt3" style="font-size:18px;color:red;float: right;    margin-right: 5px;"></span>
				         	</a>
				         </span>';

		     		break;

		     		case 'miniOrange Authenticator':
			     	$form .='   <span style="float:right">';
			     	if(isset($two_factor_methods_doc[$auth_method])){
			     	$form .='<a href='.$two_factor_methods_doc[$auth_method].' target="_blank">
				         	<span title="View Setup Guide" class="dashicons dashicons-text-page" style="font-size:19px;color:#413c69;float: right;"></span>
				         	</a>';
				    }

				    if(isset($two_factor_methods_video[$auth_method])){
			     	$form .='<a href='.$two_factor_methods_video[$auth_method].' target="_blank">
				         	<span title="Watch Setup Video" class="dashicons dashicons-video-alt3" style="font-size:18px;color:red;float: right;margin-right: 5px;"></span>
				         	</a>';
				   	}

				    $form .='</span>';
		     		break;

		     		case 'miniOrange QR Code Authentication':
		     			$form .='   <span style="float:right">
				         	<a href='.$two_factor_methods_doc[$auth_method].' target="_blank">
				         	<span title="View Setup Guide" class="dashicons dashicons-text-page" style="font-size:19px;color:#413c69;float: right;"></span>
				         	
				         	</a>
				         	<a href='.$two_factor_methods_video[$auth_method].' target="_blank">
				         	<span title="Watch Setup Video" class="dashicons dashicons-video-alt3" style="font-size:18px;color:red;float: right;    margin-right: 5px;"></span>
				         	</a>
				         	
				         </span>';

		     		break;

		     		case 'miniOrange Push Notification':
		     			$form .='   <span style="float:right">
				         	<a href='.$two_factor_methods_doc[$auth_method].' target="_blank">
				         	<span title="View Setup Guide" class="dashicons dashicons-text-page" style="font-size:19px;color:#413c69;float: right;"></span>
				         	
				         	</a>
				         	<a href='.$two_factor_methods_video[$auth_method].' target="_blank">
				         	<span title="Watch Setup Video" class="dashicons dashicons-video-alt3" style="font-size:18px;color:red;float: right;    margin-right: 5px;"></span>
				         	</a>
				         	
				         </span>';
		     			break;  

		     		case 'Email Verification':
		     			$form .='   <span style="float:right">
				         	<a href='.$two_factor_methods_doc[$auth_method].' target="_blank">
				         	<span title="View Setup Guide" class="dashicons dashicons-text-page" style="font-size:19px;color:#413c69;float: right;"></span>
				         	
				         	</a>
				         	<a href='.$two_factor_methods_video[$auth_method].' target="_blank">
				         	<span title="Watch Setup Video" class="dashicons dashicons-video-alt3" style="font-size:18px;color:red;float: right;    margin-right: 5px;"></span>
				         	</a>
				         	
				         </span>';
		     			break;  
		     		case 'OTP Over Telegram':
						$form .='   <span style="float:right">
			         	<a href='.$two_factor_methods_doc[$auth_method].' target="_blank">
			         	<span title="View Setup Guide" class="dashicons dashicons-text-page" style="font-size:19px;color:#413c69;float: right;"></span>
			           	</a>
						<a href='.$two_factor_methods_video[$auth_method].' target="_blank">
						<span title="Watch Setup Video" class="dashicons dashicons-video-alt3" style="font-size:18px;color:red;float: right;    margin-right: 5px;"></span>
						</a>
			        	</span>';		
			       	break; 
			       	case 'OTP Over Email':
						$form .='   <span style="float:right">
			         	<a href='.$two_factor_methods_doc[$auth_method].' target="_blank">
			         	<span title="View Setup Guide" class="dashicons dashicons-text-page" style="font-size:19px;color:#413c69;float: right;"></span>
			           	</a>
			           
			        	</span>';		
			       	break; 
			       	case 'Duo Authenticator':
						$form .='   <span style="float:right">
			         		<a href='.$two_factor_methods_video[$auth_method].' target="_blank">
				         	<span title="Watch Setup Video" class="dashicons dashicons-video-alt3" style="font-size:18px;color:red;float: right;    margin-right: 5px;"></span>
				         	</a>
			           
			        	</span>';		
			       	break; 
			       	 	case 'OTP Over Whatsapp':
						$form .='   <span style="float:right">
			         	<a href='.$two_factor_methods_doc[$auth_method].' target="_blank">
			         	<span title="View Setup Guide" class="dashicons dashicons-text-page" style="font-size:19px;color:#413c69;float: right;"></span>
			           	</a>
			           
			        	</span>';		
			       	break; 
			       	case 'Authy Authenticator':
						$form .='   <span style="float:right">
			         	<a href='.$two_factor_methods_video[$auth_method].' target="_blank">
				         	<span title="Watch Setup Video" class="dashicons dashicons-video-alt3" style="font-size:18px;color:red;float: right;    margin-right: 5px;"></span>
				         	</a>
			           
			        	</span>';		
			       	break;

	            	default:
						{$form .= "";}
				    break;
	            }
			$form .=' <b>' . $auth_method .
			         '</b><br>
                        <p style="padding:0px; padding-left:0px;font-size: 14px;"> ' . $two_factor_methods_descriptions[ $auth_method ] . '</p>
                        
                        </div>
                        </div>
                        </div>';
            
			if ( $is_auth_method_av && $category == 'free_plan' ) {
				$is_auth_method_configured = 0;
				if($auth_method_abr == 'miniOrangeAuthenticator'){	
					$is_auth_method_configured = $Mo2fdbQueries->get_user_detail( 'mo2f_miniOrangeSoftToken_config_status', $user->ID );			
				}else{	
					$is_auth_method_configured = $Mo2fdbQueries->get_user_detail( 'mo2f_' . $auth_method_abr . '_config_status', $user->ID );	
				}
				if(($auth_method == 'OUT OF BAND EMAIL' or $auth_method == 'OTP Over Email') and !MO2F_IS_ONPREM )
					$is_auth_method_configured = 1;
				$chat_id = get_user_meta($user->ID,'mo2f_chat_id',true);
				$form .= '<div style="height:40px;width:100%;position: absolute;bottom: 0;background-color:';
				$iscurrentMethod = 0;
				if(MO2F_IS_ONPREM)
				{
                	$currentMethod = $configured_auth_method;
                	if($currentMethod == $auth_method || ($auth_method=='miniOrange Authenticator' && $selected_miniorange_method ) )
                		$iscurrentMethod = 1;
					$form .= $iscurrentMethod ? '#07b52a' : 'var(--mo2f-theme-blue)';
				}
				else
					$form .= $is_auth_method_selected ? '#07b52a' : 'var(--mo2f-theme-blue)';
				if(MO2F_IS_ONPREM)
				{
					$twofactor_transactions = new Mo2fDB;
					$exceeded = $twofactor_transactions->check_alluser_limit_exceeded($user->ID);
					if($exceeded){
						if(empty($configured_auth_method)){
							$can_user_configure_2fa_method = false;
						}
						else{
						$can_user_configure_2fa_method = true;	
						}
					}
					else{
					$can_user_configure_2fa_method = true;
					}
					$is_customer_registered			= true;
					$user 							= wp_get_current_user();
					$form .= ';color:white">';

					$check = $is_customer_registered? true : false;
					$show = 0;
					
					

					$cloud_methods = array('miniOrange Authenticator' , 'miniOrange Soft Token','miniOrange Push Notification');
                    
					if($auth_method == 'Email Verification' || $auth_method == 'Security Questions' || $auth_method == 'Google Authenticator' || $auth_method == 'miniOrange Authenticator' || $auth_method == 'OTP Over SMS' || $auth_method == 'OTP Over Email' || $auth_method == 'OTP Over Telegram' || $auth_method == 'Duo Authenticator')
				 	{
						$show = 1;
					}
					
					if ( $check ) {
						$form .= '<div class="mo2f_configure_2_factor">
	                              <button type="button" id="'.$auth_method_abr.'_configuration" class="mo2f_configure_set_2_factor" onclick="configureOrSet2ndFactor_' . $category . '(\'' . $auth_method_abr . '\', \'configure2factor\');"';
						$form .= $show==1 ? "" : " disabled ";
						$form .= '>';
						if($show)
							$form .= $is_auth_method_configured? 'Reconfigure' : 'Configure';
						else
							$form .= 'Available in cloud solution';
						$form .= '</button></div>';
					}
					if ( ($is_auth_method_configured && ! $is_auth_method_selected) or MO2F_IS_ONPREM) {
						$form .= '<div class="mo2f_set_2_factor">
	                               <button type="button" id="'.$auth_method_abr.'_set_2_factor" class="mo2f_configure_set_2_factor" onclick="configureOrSet2ndFactor_' . $category . '(\'' . $auth_method_abr . '\', \'select2factor\');"';
						$form .= $can_user_configure_2fa_method ? "" : " disabled ";
						$form .= $show==1 ? "" : " disabled ";
						if($show == 1 and $is_auth_method_configured and $iscurrentMethod == 0){
							$form .= '>Set as 2-factor</button>
		                              </div>';	
		                }else{
	                    	$form .= '
	                    	</button>
	                              </div>';
	                    }
					}
					
				}
				else	
				{
					if(get_option('mo2f_miniorange_admin'))
						$allowed = wp_get_current_user()->ID == get_option('mo2f_miniorange_admin');
				 	else
				 		$allowed = 1;
				 	$cloudswitch = 0;
				 	if(!$allowed)
				 		$allowed = 2;
				  	$form .= ';color:white">';
					$check = !$is_customer_registered? true : ($auth_method != "Email Verification" and $auth_method != "OTP Over Email"? true : false);
					$is_auth_method_configured = !$is_customer_registered ? 0 :1;
					if(!MO2F_IS_ONPREM and ($auth_method == "Email Verification" or $auth_method == "OTP Over Email"))
						$check = 0; 
					if ( $check ) {
						$form .= '<div class="mo2f_configure_2_factor">
	                              <button type="button" id="'.$auth_method_abr.'_configuration" class="mo2f_configure_set_2_factor" onclick="configureOrSet2ndFactor_' . $category . '(\'' . $auth_method_abr . '\', \'configure2factor\','.$cloudswitch.','.$allowed.');"';
						$form .= $can_user_configure_2fa_method ? "" : "  ";
						$form .= '>';
						$form .= $is_auth_method_configured ? 'Reconfigure' : 'Configure';
						$form .= '</button></div>';
					}

					if ( ($is_auth_method_configured && ! $is_auth_method_selected) or MO2F_IS_ONPREM  ) {
						$form .= '<div class="mo2f_set_2_factor">
	                               <button type="button" id="'.$auth_method_abr.'_set_2_factor" class="mo2f_configure_set_2_factor" onclick="configureOrSet2ndFactor_' . $category . '(\'' . $auth_method_abr . '\', \'select2factor\','.$cloudswitch.','.$allowed.');"';
						$form .= $can_user_configure_2fa_method ? "" : "  ";
						$form .= '>Set as 2-factor</button>
	                              </div>';
					}

				}
				if($is_auth_method_selected && $auth_method == 'miniOrange Authenticator'){
						$form .='<select name="mo2fa_MO_methods" id="mo2fa_MO_methods" class="mo2f_set_2_factor mo2f_configure_switch_2_factor mo2f_kba_ques" style="color: white;font-weight: 700;background: #48b74b;background-size: 16px 16px;border: 1px solid #48b74b;padding: 0px 0px 0px 17px;min-height: 30px;max-width: 9em;max-width: 9em;" onchange="show_3_minorange_methods();">
							      <option value="" selected disabled hidden style="color:white!important;">Switch to >></option>
							      <option value="miniOrangeSoftToken">Soft Token</option>
							      <option value="miniOrangeQRCodeAuthentication">QR Code</option>
							      <option value="miniOrangePushNotification">Push Notification</option>
							  </select></div>
							  <br><br>

							  ';
					}
					$form .= '</div>';
			}
			$form .= '</div></div></td>';
		}

		$form .= '</tr>';
	}


	$form .= '</table>';
     if( $category!="free_plan")
     	if(current_user_can('administrator')){
	     $form .= '<div class="mo2f_premium_footer">
                            <p style="font-size:16px;margin-left: 1%">In addition to these authentication methods, for other features in this plan, <a href="admin.php?page=mo_2fa_upgrade"><i>Click here.</i></a></p>
                 </div>';
     	}
     $configured_auth_method_abr  = str_replace(' ', '',$configured_auth_method);
     $form .= '</div> <input type="hidden" name="miniorange_save_form_auth_methods_nonce"
                   value="'. esc_html(wp_create_nonce( "miniorange-save-form-auth-methods-nonce" )) .'"/>
                <input type="hidden" name="option" value="mo2f_save_' . esc_html($category) . '_auth_methods" />
                <input type="hidden" name="mo2f_configured_2FA_method_' . esc_html($category ). '" id="mo2f_configured_2FA_method_' . esc_html($category) . '" />
                <input type="hidden" name="mo2f_selected_action_' . esc_html($category) . '" id="mo2f_selected_action_' . esc_html($category) . '" />
                </form><script>
                var selected_miniorange_method = "'.esc_html($selected_miniorange_method).'";
                if(selected_miniorange_method)
                	jQuery("<input>").attr({type: "hidden",id: "miniOrangeAuthenticator",value: "'.esc_html($configured_auth_method_abr).'"}).appendTo("form");
                else                	
                	jQuery("<input>").attr({type: "hidden",id: "miniOrangeAuthenticator",value: "miniOrangeSoftToken"}).appendTo("form");
                </script>';

	return $form;
}


function mo2f_get_activated_second_factor( $user ) {
	
	global $Mo2fdbQueries;
	$user_registration_status = $Mo2fdbQueries->get_user_detail( 'mo_2factor_user_registration_status', $user->ID );
	$is_customer_registered   = $Mo2fdbQueries->get_user_detail( 'user_registration_with_miniorange', $user->ID ) == 'SUCCESS' ? true : false;
	$useremail                = $Mo2fdbQueries->get_user_detail( 'mo2f_user_email', $user->ID );

	if ( $user_registration_status == 'MO_2_FACTOR_SUCCESS' ) {
		//checking this option for existing users
		$Mo2fdbQueries->update_user_details( $user->ID, array( 'mobile_registration_status' => true ) );
		$mo2f_second_factor = 'MOBILE AUTHENTICATION';

		return $mo2f_second_factor;
	} else if ( $user_registration_status == 'MO_2_FACTOR_INITIALIZE_TWO_FACTOR' ) {
		return 'NONE';
	} else {
		//for new users
		if ( $user_registration_status == 'MO_2_FACTOR_PLUGIN_SETTINGS' && $is_customer_registered ) {
			$enduser  = new Two_Factor_Setup();
			$userinfo = json_decode( $enduser->mo2f_get_userinfo( $useremail ), true );
			if ( json_last_error() == JSON_ERROR_NONE ) {
				if ( $userinfo['status'] == 'ERROR' ) {
					update_option( 'mo2f_message', Mo2fConstants:: langTranslate( $userinfo['message'] ) );
					$mo2f_second_factor = 'NONE';
				} else if ( $userinfo['status'] == 'SUCCESS' ) {
					$mo2f_second_factor = mo2f_update_and_sync_user_two_factor( $user->ID, $userinfo );
				} else if ( $userinfo['status'] == 'FAILED' ) {
					$mo2f_second_factor = 'NONE';
					update_option( 'mo2f_message', Mo2fConstants:: langTranslate( "ACCOUNT_REMOVED" ) );
				} else {
					$mo2f_second_factor = 'NONE';
				}
			} else {
				update_option( 'mo2f_message', Mo2fConstants:: langTranslate( "INVALID_REQ" ) );
				$mo2f_second_factor = 'NONE';
			}
		} else {
			$mo2f_second_factor = 'NONE';
		}

		return $mo2f_second_factor;
	}
}

function mo2f_update_and_sync_user_two_factor( $user_id, $userinfo ) {
	global $Mo2fdbQueries;
	$mo2f_second_factor = isset( $userinfo['authType'] ) && ! empty( $userinfo['authType'] ) ? $userinfo['authType'] : 'NONE';
	if(MO2F_IS_ONPREM)
	{
		$mo2f_second_factor = $Mo2fdbQueries->get_user_detail( 'mo2f_configured_2FA_method', $user_id );
		$mo2f_second_factor = $mo2f_second_factor ? $mo2f_second_factor : 'NONE';
		return $mo2f_second_factor;
	}

	$Mo2fdbQueries->update_user_details( $user_id, array( 'mo2f_user_email' => $userinfo['email'] ) );
	if ( $mo2f_second_factor == 'OUT OF BAND EMAIL' ) {
		$Mo2fdbQueries->update_user_details( $user_id, array( 'mo2f_EmailVerification_config_status' => true ) );
	} else if ( $mo2f_second_factor == 'SMS' and !MO2F_IS_ONPREM) {
		$phone_num = isset($userinfo['phone'])?sanitize_text_field($userinfo['phone']):'';
		$Mo2fdbQueries->update_user_details( $user_id, array( 'mo2f_OTPOverSMS_config_status' => true ) );
		$_SESSION['user_phone'] = $phone_num;
	} else if ( in_array( $mo2f_second_factor, array(
		'SOFT TOKEN',
		'MOBILE AUTHENTICATION',
		'PUSH NOTIFICATIONS'
	) ) ) {
			if(!MO2F_IS_ONPREM)
				$Mo2fdbQueries->update_user_details( $user_id, array(
					'mo2f_miniOrangeSoftToken_config_status'            => true,
					'mo2f_miniOrangeQRCodeAuthentication_config_status' => true,
					'mo2f_miniOrangePushNotification_config_status'     => true
				) );
	} else if ( $mo2f_second_factor == 'KBA' ) {
		$Mo2fdbQueries->update_user_details( $user_id, array( 'mo2f_SecurityQuestions_config_status' => true ) );
	} else if ( $mo2f_second_factor == 'GOOGLE AUTHENTICATOR' ) {
		$app_type = get_user_meta( $user_id, 'mo2f_external_app_type', true );

		if ( $app_type == 'Google Authenticator' ) {
			$Mo2fdbQueries->update_user_details( $user_id, array(
				'mo2f_GoogleAuthenticator_config_status' => true
			) );
			update_user_meta( $user_id, 'mo2f_external_app_type', 'Google Authenticator' );
		} else if ( $app_type == 'Authy Authenticator' ) {
			$Mo2fdbQueries->update_user_details( $user_id, array(
				'mo2f_AuthyAuthenticator_config_status' => true
			) );
			update_user_meta( $user_id, 'mo2f_external_app_type', 'Authy Authenticator' );
		} else {
			$Mo2fdbQueries->update_user_details( $user_id, array(
				'mo2f_GoogleAuthenticator_config_status' => true
			) );

			update_user_meta( $user_id, 'mo2f_external_app_type', 'Google Authenticator' );
		}
	}

	return $mo2f_second_factor;
}

function display_customer_registration_forms($user){

	global $Mo2fdbQueries;
	$mo2f_current_registration_status = get_option( 'mo_2factor_user_registration_status');
	$mo2f_message              = get_option( 'mo2f_message' );
	?>

	<div id="smsAlertModal" class="modal" role="dialog" data-backdrop="static" data-keyboard="false" >
		<div class="mo2f_modal-dialog" style="margin-left:30%;">
			<!-- Modal content-->
			<div class="modal-content">
				<div class="mo2f_modal-header">
					<h2 class="mo2f_modal-title">You are just one step away from setting up 2FA.</h2><span type="button" id="mo2f_registration_closed" class="modal-span-close" data-dismiss="modal">&times;</span>
				</div>
				<div class="mo2f_modal-body">
					<span style="color:green;cursor: pointer;float:right;" onclick="show_content();">Why Register with miniOrange?</span><br>
				<div id="mo2f_register" style="background-color:#f1f1f1;padding: 1px 4px 1px 14px;display: none;">
					<p>miniOrange Two Factor plugin uses highly secure miniOrange APIs to communicate with the plugin. To keep this communication secure, we ask you to register and assign you API keys specific to your account.			This way your account and users can be only accessed by API keys assigned to you. Also, you can use the same account on multiple applications and your users do not have to maintain multiple accounts or 2-factors.</p>
                </div>
					<?php if ( $mo2f_message ) { ?>
	                    <div style="padding:5px;">
	                        <div class="alert alert-info" style="margin-bottom:0px;padding:3px;">
	                            <p style="font-size:15px;margin-left: 2%;"><?php wp_kses($mo2f_message, array('b'=>array())); ?></p>
	                        </div>
	                    </div>
					<?php }
					if(in_array($mo2f_current_registration_status, array("REGISTRATION_STARTED", "MO_2_FACTOR_OTP_DELIVERED_SUCCESS", "MO_2_FACTOR_OTP_DELIVERED_FAILURE", "MO_2_FACTOR_VERIFY_CUSTOMER")) ){
                    	mo2f_show_registration_screen($user); 
                    }
					?>
				</div>
			</div>
		</div>
	    <form name="f" method="post" action="" class="mo2f_registration_closed_form">
			<input type="hidden" name="mo2f_registration_closed_nonce"
							value="<?php echo esc_html(wp_create_nonce( "mo2f-registration-closed-nonce" )) ?>"/>
	        <input type="hidden" name="option" value="mo2f_registration_closed"/>
	    </form>
    </div>
	
    <script>
		function show_content() {
            jQuery('#mo2f_register').slideToggle();
        }
        jQuery(function () {
            jQuery('#smsAlertModal').modal();
        });

        jQuery('#mo2f_registration_closed').click(function () {
        	jQuery('.mo2f_registration_closed_form').submit();
        });
	</script>

	<?php
	wp_register_script( 'mo2f_bootstrap_js',plugins_url( 'includes/js/bootstrap.min.js', dirname(dirname(__FILE__)) ),[],MO2F_VERSION);
    wp_print_scripts( 'mo2f_bootstrap_js' );
}

function mo2f_show_registration_screen($user){
	global $mo2f_dirName;

	include $mo2f_dirName . 'controllers'.DIRECTORY_SEPARATOR.'account.php';

}

function mo2f_show_2FA_configuration_screen( $user, $selected2FAmethod ) {
	global $mo2f_dirName;
	
	switch ( $selected2FAmethod ) {
		case "Google Authenticator":
			if(MO2F_IS_ONPREM){
				include_once dirname( __FILE__ ) . DIRECTORY_SEPARATOR. 'gaonprem.php';
				$obj = new Google_auth_onpremise();
				$obj->mo_GAuth_get_details();
			}
			else{
				if(!get_user_meta($user->ID, 'mo2f_google_auth', true)){
					Miniorange_Authentication::mo2f_get_GA_parameters($user);
				}
				echo '<div class="mo2f_table_layout mo2f_table_layout1">';
				mo2f_configure_google_authenticator( $user );
				echo '</div>';
			}
			break;
		case "Authy Authenticator":
			echo '<div class="mo2f_table_layout mo2f_table_layout1">';
			mo2f_configure_authy_authenticator( $user );
			echo '</div>';
			break;
		case "Security Questions":
			echo '<div class="mo2f_table_layout mo2f_table_layout1">';
			mo2f_configure_for_mobile_suppport_kba( $user );
			echo '</div>';
			break;
		case "Email Verification":
			echo '<div class="mo2f_table_layout mo2f_table_layout1">';
			mo2f_configure_for_mobile_suppport_kba( $user );
			echo '</div>';
			break;
		case "OTP Over SMS":
			echo '<div class="mo2f_table_layout mo2f_table_layout1">';
			mo2f_configure_otp_over_sms( $user );
			echo '</div>';
			break;
		case "miniOrange Soft Token":
			echo '<div class="mo2f_table_layout mo2f_table_layout1">';
			mo2f_configure_miniorange_authenticator( $user );
			echo '</div>';
			break;
		case "miniOrange QR Code Authentication":
			echo '<div class="mo2f_table_layout mo2f_table_layout1">';
			mo2f_configure_miniorange_authenticator( $user );
			echo '</div>';
			break;
		case "miniOrange Push Notification":
			echo '<div class="mo2f_table_layout mo2f_table_layout1">';
			mo2f_configure_miniorange_authenticator( $user );
			echo '</div>';
			break;
		case "OTP Over Email":
			echo '<div class="mo2f_table_layout mo2f_table_layout1">';
			mo2f_test_otp_over_email($user,$selected2FAmethod);
			echo '</div>';
			break;
		case "OTP Over Telegram":
			echo '<div class="mo2f_table_layout mo2f_table_layout1">';
			mo2f_configure_otp_over_Telegram($user);
			echo '</div>';
			break;
		case "DuoAuthenticator":
		case "Duo Authenticator":
		   	echo '<div class="mo2f_table_layout mo2f_table_layout1">';
			mo2f_configure_duo_authenticator($user);
			echo '</div>';
			break;		
	}

}

function mo2f_show_2FA_test_screen( $user, $selected2FAmethod ) {

	
	switch ( $selected2FAmethod ) {
		case "miniOrange QR Code Authentication":
			mo2f_test_miniorange_qr_code_authentication( $user );
			break;
		case "miniOrange Push Notification":
			mo2f_test_miniorange_push_notification( $user );
			break;
		case "miniOrange Soft Token":
			mo2f_test_miniorange_soft_token( $user );
			break;
		case "Email Verification":
			mo2f_test_email_verification($user);
			break;
		case "OTP Over SMS":
			mo2f_test_otp_over_sms( $user );
			break;
		case "OTP Over Telegram":
			mo2f_test_otp_over_Telegram( $user );
			break;
		case "Security Questions":
			mo2f_test_kba_security_questions( $user );
			break;
		case "OTP Over Email":
			mo2f_test_otp_over_email($user,$selected2FAmethod);
			break;
		case "Duo Authenticator":
		   mo2f_test_duo_authenticator($user);
		   break;
		default:
			mo2f_test_google_authy_authenticator( $user, $selected2FAmethod );
	}

}

function mo2f_method_display_name($user,$mo2f_second_factor){
	
	if ( $mo2f_second_factor == 'GOOGLE AUTHENTICATOR' ) {
		$app_type = get_user_meta( $user->ID, 'mo2f_external_app_type', true );

		if ( $app_type == 'Google Authenticator' ) {
			$selectedMethod = 'Google Authenticator';
		} else if ( $app_type == 'Authy Authenticator' ) {
			$selectedMethod = 'Authy Authenticator';
		} else {
			$selectedMethod = 'Google Authenticator';
			update_user_meta( $user->ID, 'mo2f_external_app_type', $selectedMethod );
		}
	} else {
		$selectedMethod = MO2f_Utility::mo2f_decode_2_factor( $mo2f_second_factor, "servertowpdb" );
	}
	return $selectedMethod;

}

function mo2f_lt( $string ) {
	return __($string ,'miniorange-2-factor-authentication' );
}

function mo2f_rba_description($mo2f_user_email) {?>
	<div id="mo2f_rba_addon">
	    <?php if ( get_option( 'mo2f_rba_installed' ) ) { ?>
	        <a href="<?php echo admin_url(); ?>plugins.php" id="mo2f_activate_rba_addon"
	           class="button button-primary button-large"
	           style="float:right; margin-top:2%;"><?php echo __( 'Activate Plugin', 'miniorange-2-factor-authentication' ); ?></a>
	    <?php } ?>
	    <?php if ( ! get_option( 'mo2f_rba_purchased' ) ) { ?>  
	        <a onclick="mo2f_addonform('wp_2fa_addon_rba')" id="mo2f_purchase_rba_addon"
	           class="button button-primary button-large"
	           style="float:right;"><?php echo __( 'Purchase', 'miniorange-2-factor-authentication' ); ?></a><?php } ?>
	    <div id="mo2f_rba_addon_hide">
	        
	        <br>
	        <div id="mo2f_hide_rba_content">

	            <div class="mo2f_box">
	                <h3><?php echo __( 'Remember Device', 'miniorange-2-factor-authentication' ); ?></h3>
	                <hr>
	                <p id="mo2f_hide_rba_content"><?php echo __( 'With this feature, User would get an option to remember the personal device where Two Factor is not required. Every time the user logs in with the same device it detects                     the saved device so he will directly login without being prompted for the 2nd factor. If user logs in from new device he will be prompted with 2nd                          Factor.', 'miniorange-2-factor-authentication' ); ?>

	                </p>
	            </div>
	            <br><br>
	            <div class="mo2f_box">
	                <h3><?php echo __( 'Limit Number Of Device', 'miniorange-2-factor-authentication' ); ?></h3>
	                <hr>
	                <p><?php echo __( 'With this feature, the admin can restrict the number of devices from which the user can access the website. If the device limit is exceeded the admin can set three actions where it can allow the users to login, deny the access or challenge the user for authentication.', 'miniorange-2-factor-authentication' ); ?>
	                </p>

	            </div>
	            <br><br>
	            <div class="mo2f_box">
	                <h3><?php echo __( 'IP Restriction: Limit users to login from specific IPs', 'miniorange-2-factor-authentication' ); ?></h3>
	                <hr>
	                <p><?php echo __( 'The Admin can enable IP restrictions for the users. It will provide additional security to the accounts and perform different action to the accounts only from the listed IP Ranges. If user tries to access with a restricted IP, Admin can set three action: Allow, challenge or deny. Depending upon the action it will allow the user to login, challenge(prompt) for authentication or deny the access.', 'miniorange-2-factor-authentication' ); ?>
					
	            </div>
				<br>
	        </div>

	    </div>
	    <div id="mo2f_rba_addon_show">
			<?php	$x = apply_filters( 'mo2f_rba', "rba" );?>
		</div>
    </div>
    <form style="display:none;" id="mo2fa_loginform"
          action="<?php echo esc_url(MO_HOST_NAME . '/moas/login'); ?>"
          target="_blank" method="post">
        <input type="email" name="username" value="<?php echo esc_html($mo2f_user_email); ?>"/>
        <input type="text" name="redirectUrl"
               value="<?php echo esc_url(MO_HOST_NAME . '/moas/initializepayment'); ?>"/>
        <input type="text" name="requestOrigin" id="requestOrigin"/>
    </form>
    <script>
        function mo2f_addonform(planType) {
            jQuery('#requestOrigin').val(planType);
            jQuery('#mo2fa_loginform').submit();
        }
    </script>
    <?php
}

function mo2f_personalization_description($mo2f_user_email) {?>
	<div id="mo2f_custom_addon">
        <?php if ( get_option( 'mo2f_personalization_installed' ) ) { ?>
            <a href="<?php echo esc_url(admin_url()); ?>plugins.php" id="mo2f_activate_custom_addon"
                       class="button button-primary button-large"
                       style="float:right; margin-top:2%;"><?php echo __( 'Activate Plugin', 'miniorange-2-factor-authentication' ); ?></a>
                <?php } ?>
        <?php if ( ! get_option( 'mo2f_personalization_purchased' ) ) { ?>  <a
                        onclick="mo2f_addonform('wp_2fa_addon_shortcode')" id="mo2f_purchase_custom_addon"
                        class="button button-primary button-large"
                        style="float:right;"><?php echo __( 'Purchase', 'miniorange-2-factor-authentication' ); ?></a>
                <?php } ?>
        <div id="mo2f_custom_addon_hide">
            
		    
		    <br>
		    <div id="mo2f_hide_custom_content">
		        <div class="mo2f_box">
		            <h3><?php echo __( 'Customize Plugin Icon', 'miniorange-2-factor-authentication' ); ?></h3>
		            <hr>
		            <p>
		                <?php echo __( 'With this feature, you can customize the plugin icon in the dashboard which is useful when you want your custom logo to be displayed to the users.', 'miniorange-2-factor-authentication' ); ?>
		            </p>
		            <br>
		            <h3><?php echo __( 'Customize Plugin Name', 'miniorange-2-factor-authentication' ); ?></h3>
		            <hr>
		            <p>
		                <?php echo __( 'With this feature, you can customize the name of the plugin in the dashboard.', 'miniorange-2-factor-authentication' ); ?>
		            </p>

		        </div>
		        <br>
		        <div class="mo2f_box">
		            <h3><?php echo __( 'Customize UI of Login Pop up\'s', 'miniorange-2-factor-authentication' ); ?></h3>
		            <hr>
		            <p>
		                <?php echo __( 'With this feature, you can customize the login pop-ups during two factor authentication according to the theme of                 your website.', 'miniorange-2-factor-authentication' ); ?>
		            </p>
		        </div>

		        <br>
		        <div class="mo2f_box">
		            <h3><?php echo __( 'Custom Email and SMS Templates', 'miniorange-2-factor-authentication' ); ?></h3>
		            <hr>

		            <p><?php echo __( 'You can change the templates for Email and SMS which user receives during authentication.', 'miniorange-2-factor-authentication' ); ?></p>

		        </div>
		    </div>
		</div>
		 <div id="mo2f_custom_addon_show"><?php $x = apply_filters( 'mo2f_custom', "custom"); ?></div> 
    </div> 
    
    <?php
}

function mo2f_shortcode_description($mo2f_user_email) { ?>
	<div id="mo2f_Shortcode_addon_hide">
        <?php if ( get_option( 'mo2f_shortcode_installed' ) ) { ?>
            <a href="<?php echo esc_url(admin_url()); ?>plugins.php" id="mo2f_activate_shortcode_addon"
                           class="button button-primary button-large" style="float:right; margin-top:2%;"><?php echo __( 'Activate
                        Plugin', 'miniorange-2-factor-authentication' ); ?></a>
        <?php } if ( ! get_option( 'mo2f_shortcode_purchased' ) ) { ?>
                   <a onclick="mo2f_addonform('wp_2fa_addon_personalization')" id="mo2f_purchase_shortcode_addon"
                           class="button button-primary button-large"
                           style="float:right;"><?php echo __( 'Purchase', 'miniorange-2-factor-authentication' ); ?></a>
        <?php } ?>
        
		<div id="shortcode" class="description">
			
            
			<br>
			<div id="mo2f_hide_shortcode_content" class="mo2f_box">
				<h3><?php echo __( 'List of Shortcodes', 'miniorange-2-factor-authentication' ); ?>:</h3>
				<hr>
				<ol style="margin-left:2%">
					<li>
						<b><?php echo __( 'Enable Two Factor: ', 'miniorange-2-factor-authentication' ); ?></b> <?php echo __( 'This shortcode provides an option to turn on/off 2-factor by user.', 'miniorange-2-factor-authentication' ); ?>
					</li>
					<li>
						<b><?php echo __( 'Enable Reconfiguration: ', 'miniorange-2-factor-authentication' ); ?></b> <?php echo __( 'This shortcode provides an option to configure the Google Authenticator and Security Questions by user.', 'miniorange-2-factor-authentication' ); ?>
					</li>
					<li>
						<b><?php echo __( 'Enable Remember Device: ', 'miniorange-2-factor-authentication' ); ?></b> <?php echo __( ' This shortcode provides\'Enable Remember Device\' from your custom login form.', 'miniorange-2-factor-authentication' ); ?>
					</li>
				</ol>
			</div>
			<div id="mo2f_Shortcode_addon_show"><?php $x = apply_filters( 'mo2f_shortcode', "shortcode" ); ?></div>
	    </div>
	    <br>
    </div>
	<form style="display:none;" id="mo2fa_loginform" action="<?php echo esc_url(MO_HOST_NAME . '/moas/login'); ?>" target="_blank" method="post">
        <input type="email" name="username" value="<?php echo esc_html($mo2f_user_email); ?>"/>
        <input type="text" name="redirectUrl"
               value="<?php echo esc_url(MO_HOST_NAME . '/moas/initializepayment'); ?>"/>
        <input type="text" name="requestOrigin" id="requestOrigin"/>
    </form>
    <script>
        function mo2f_addonform(planType) {
            jQuery('#requestOrigin').val(planType);
            jQuery('#mo2fa_loginform').submit();
        }
    </script>
    <?php
}

?>
