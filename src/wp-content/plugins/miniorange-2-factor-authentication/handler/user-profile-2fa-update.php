<?php
if(isset($_POST['method']))
    $method = sanitize_text_field($_POST['method']);
else 
    return;
global $Mo2fdbQueries;
$email = $Mo2fdbQueries->get_user_detail('mo2f_user_email',$user);
$method = MO2f_Utility::mo2f_decode_2_factor($method,'wpdb');
$email = sanitize_email($email);
$enduser  = new Two_Factor_Setup();
if(isset($_POST['verify_phone']))
    $phone = strlen($_POST['verify_phone']>4)?sanitize_text_field($_POST['verify_phone']) : null;
else
    $phone = null;
$response = json_decode( $enduser->mo2f_update_userinfo( $email,MO2f_Utility::mo2f_decode_2_factor($method,'server') , $phone, null, null ), true );
if($response['status']!= 'SUCCESS')
    return;
$id = get_current_user_id();
$method = MO2f_Utility::mo2f_decode_2_factor($method,'wpdb');
switch ($method) {
    case "miniOrange QR Code Authentication":
    case "miniOrange Push Notification":
    case "miniOrange Soft Token":
    if($id != $user){
        send_reconfiguration_on_email($email,$user,$method);
    }else if(sanitize_text_field($_POST['mo2f_configuration_status'])!='SUCCESS')    
    return; 
    delete_user_meta( $user, 'configure_2FA' );
    update_user_meta($user,'mo2f_2FA_method_to_configure',$method);
    $Mo2fdbQueries->update_user_details($user, array(
        'mobile_registration_status' =>true,
        'mo2f_miniOrangeQRCodeAuthentication_config_status' => true,
        'mo2f_miniOrangeSoftToken_config_status'            => true,
        'mo2f_miniOrangePushNotification_config_status'     => true,
        "mo2f_configured_2FA_method" => $method,
        'user_registration_with_miniorange' => 'SUCCESS',
        'mo2f_2factor_enable_2fa_byusers'=> '1',
        'mo_2factor_user_registration_status' =>'MO_2_FACTOR_PLUGIN_SETTINGS',
    ) );
    break;
    case "Google Authenticator":
    if($id!= $user){
        send_reconfiguration_on_email($email,$user,$method);
    }else if(sanitize_text_field($_POST['mo2f_configuration_status'])!='SUCCESS')    
    return;
    $Mo2fdbQueries->update_user_details( $user, array(
        'mo2f_GoogleAuthenticator_config_status' => true,
        'mo2f_configured_2FA_method' => 'Google Authenticator',
        'mo2f_AuthyAuthenticator_config_status' => false,
        'user_registration_with_miniorange' => 'SUCCESS',
        'mo_2factor_user_registration_status'   => 'MO_2_FACTOR_PLUGIN_SETTINGS',
        'mo2f_2factor_enable_2fa_byusers'     => 1,
        'mo2f_user_email' => $email
    ) );
    if(!MO2F_IS_ONPREM){
        update_user_meta( $user ,'mo2f_external_app_type', "Google Authenticator" );
    }
    break;
    case "Authy Authenticator":
    if($id!= $user){
        send_reconfiguration_on_email($email,$user,$method);
    }else if(sanitize_text_field($_POST['mo2f_configuration_status'])!='SUCCESS')    
    return;
    $Mo2fdbQueries->update_user_details( $user, array(
        'mo2f_GoogleAuthenticator_config_status' => false,
        'mo2f_configured_2FA_method' => 'Authy Authenticator',
        'mo2f_AuthyAuthenticator_config_status' => true,
        'user_registration_with_miniorange' => 'SUCCESS',
        'mo_2factor_user_registration_status'   => 'MO_2_FACTOR_PLUGIN_SETTINGS',
        'mo2f_2factor_enable_2fa_byusers'     => 1,
        'mo2f_user_email' => $email
    ) );
    if(!MO2F_IS_ONPREM){
        update_user_meta( $user ,'mo2f_external_app_type', "Authy Authenticator" );
    }
    break;
    case "OTP Over SMS":
    $Mo2fdbQueries->update_user_details($user, array(
        "mo2f_configured_2FA_method" => 'OTP Over SMS',
        'mo2f_OTPOverSMS_config_status' => true,
        'user_registration_with_miniorange' => 'SUCCESS',
        'mo2f_2factor_enable_2fa_byusers'=> '1',
        'mo_2factor_user_registration_status' =>'MO_2_FACTOR_PLUGIN_SETTINGS',
    ) );
    break;
    case "Security Questions":
    $obj = new Miniorange_Authentication();
    $kba_q1 = sanitize_text_field($_POST['mo2f_kbaquestion_1']);
    $kba_a1 = sanitize_text_field( $_POST['mo2f_kba_ans1'] );
    $kba_q2 = sanitize_text_field($_POST['mo2f_kbaquestion_2']);
    $kba_a2 = sanitize_text_field( $_POST['mo2f_kba_ans2'] );
    $kba_q3 = sanitize_text_field( $_POST['mo2f_kbaquestion_3'] );
    $kba_a3 = sanitize_text_field( $_POST['mo2f_kba_ans3'] );
    
    $kba_q1 = addcslashes( stripslashes( $kba_q1 ), '"\\' );
    $kba_q2 = addcslashes( stripslashes( $kba_q2 ), '"\\' );
    $kba_q3 = addcslashes( stripslashes( $kba_q3 ), '"\\' );

    $kba_a1 = addcslashes( stripslashes( $kba_a1 ), '"\\' );
    $kba_a2 = addcslashes( stripslashes( $kba_a2 ), '"\\' );
    $kba_a3 = addcslashes( stripslashes( $kba_a3 ), '"\\' );
    if ( MO2f_Utility::mo2f_check_empty_or_null( $kba_q1 ) || MO2f_Utility::mo2f_check_empty_or_null( $kba_a1 ) || MO2f_Utility::mo2f_check_empty_or_null( $kba_q2 ) || MO2f_Utility::mo2f_check_empty_or_null( $kba_a2) || MO2f_Utility::mo2f_check_empty_or_null( $kba_q3) || MO2f_Utility::mo2f_check_empty_or_null( $kba_a3) ) {
        update_option( 'mo2f_message', Mo2fConstants:: langTranslate( "INVALID_ENTRY" ) );
        return;
    }

    if ( strcasecmp( $kba_q1, $kba_q2 ) == 0 || strcasecmp( $kba_q2, $kba_q3 ) == 0 || strcasecmp( $kba_q3, $kba_q1 ) == 0 ) {
        update_option( 'mo2f_message', 'The questions you select must be unique.' );
        return;
    }
    $kba_registration = new Two_Factor_Setup();
    $kba_reg_reponse  = json_decode( $kba_registration->register_kba_details( $email, $kba_q1, $kba_a1, $kba_q2, $kba_a2, $kba_q3, $kba_a3, $user ), true );

    if ( json_last_error() == JSON_ERROR_NONE ) {
        if ( $response['status'] == 'SUCCESS' ) {
            $Mo2fdbQueries->update_user_details( $user, array( 
                'mo2f_configured_2FA_method' => 'Security Questions' ,
                'user_registration_with_miniorange' => 'SUCCESS',
                'mo2f_SecurityQuestions_config_status' => true,
                'mo2f_2factor_enable_2fa_byusers'=> '1',
                'mo_2factor_user_registration_status' =>'MO_2_FACTOR_PLUGIN_SETTINGS',
            ), true);

        }else {
            update_option( 'mo2f_message', Mo2fConstants:: langTranslate( "ERROR_DURING_PROCESS" ) );
            $obj->mo_auth_show_error_message();

        }
    } 

    break;
    case "OTP Over Email":
    $Mo2fdbQueries->update_user_details($user, array(
        "mo2f_configured_2FA_method" => 'OTP Over Email',
        'mo2f_OTPOverEmail_config_status' => true,
        'mo2f_user_email'            => $email,
        'mo2f_2factor_enable_2fa_byusers'=> '1',
        'mo_2factor_user_registration_status' =>'MO_2_FACTOR_PLUGIN_SETTINGS',
        'user_registration_with_miniorange' => 'SUCCESS',
    ) );
    delete_user_meta( $user, 'configure_2FA');
    delete_user_meta( $user, 'test_2FA');
    break;
    case "Email Verification":
    $Mo2fdbQueries->update_user_details($user, array(
        "mo2f_configured_2FA_method" => 'Email Verification',
        'mo2f_user_email'            => $email,
        'user_registration_with_miniorange' => 'SUCCESS',
        'mo2f_2factor_enable_2fa_byusers'=> '1',
        'mo_2factor_user_registration_status' =>'MO_2_FACTOR_PLUGIN_SETTINGS',
        'mo2f_EmailVerification_config_status' => true
    ) );
    break;
}
if(isset($_POST['mo2fa_count']) && sanitize_text_field($_POST['mo2fa_count']) != '1')
    update_option('mo2fa_userProfile_method',$method);
function send_reconfiguration_on_email($email,$user,$method){
    global $mo2f_dirName,$imagePath;
    $method = MO2f_Utility::mo2f_decode_2_factor($method,'server');
    $reconfiguraion_method = hash('sha512',$method);
    update_site_option($reconfiguraion_method,$method);
    $txid = bin2hex(openssl_random_pseudo_bytes(32));
    update_site_option($txid, get_current_user_id());
    update_user_meta($user,'mo2f_EV_txid',$txid);
    $subject    = '2fa-reconfiguration : Scan QR';
    $headers    = array('Content-Type: text/html; charset=UTF-8');
    update_option('mo2fa_reconfiguration_via_email',json_encode(array($user,$email,$method)));
    $path = plugins_url(DIRECTORY_SEPARATOR. 'views'.DIRECTORY_SEPARATOR. 'qr_over_email.php',dirname( __FILE__ )).'?email='.$email.'&amp;user_id='.$user_id;
    $url =  get_site_option('siteurl').'/wp-login.php?';
    $path = $url.'&amp;reconfigureMethod='.$reconfiguraion_method.'&amp;transactionId='.$txid;
    $message = '
    <table>
    <tbody>
    <tr>
    <td>
    <table cellpadding="24" width="584px" style="margin:0 auto;max-width:584px;background-color:#f6f4f4;border:1px solid #a8adad">
    <tbody>
    <tr>
    <td><img src="'.$imagePath.'includes/images/xecurify-logo.png" alt="Xecurify" style="color:#5fb336;text-decoration:none;display:block;width:auto;height:auto;max-height:35px" class="CToWUd"></td>
    </tr>
    </tbody>
    </table>
    <table cellpadding="24" style="background:#fff;border:1px solid #a8adad;width:584px;border-top:none;color:#4d4b48;font-family:Arial,Helvetica,sans-serif;font-size:13px;line-height:18px">
    <tbody>
    <tr>
    <td>
    <input type="hidden" name="user_id" id="user_id" value="'.esc_attr($user_id).'">
    <input type="hidden" name="email" id="email" value="'.esc_attr($email).'">
    <p style="margin-top:0;margin-bottom:20px">Dear Customer,</p>
    <p style="margin-top:0;margin-bottom:10px">Please scan the QR code from given link to set <b>2FA method</b>:</p>
    <p><a href="'.esc_url($path).'" > Click to reconfigure 2nd factor</a></p>
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
        update_site_option( 'mo2f_message', 'A OTP has been sent to you on' .'<b> ' . esc_html($email) . '</b>. ' .  Mo2fConstants::langTranslate("ACCEPT_LINK_TO_VERIFY_EMAIL"));
        $arr = array('status' => 'SUCCESS','message'=>'Successfully validated.' ,'txId' => '' );
        
    }else{
        $arr = array('status' => 'FAILED','message'=>'TEST FAILED.');
        update_site_option( 'mo2f_message',  Mo2fConstants::langTranslate("ERROR_DURING_PROCESS_EMAIL"));
    }
    $content = json_encode($arr);
}
?>