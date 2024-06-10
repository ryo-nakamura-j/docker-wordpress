<?php 
function fetch_methods($current_user = null){
    $methods = array("SMS","SOFT TOKEN","MOBILE AUTHENTICATION","PUSH NOTIFICATIONS","GOOGLE AUTHENTICATOR","KBA","OTP_OVER_EMAIL","OTP OVER TELEGRAM");
    if(!is_null($current_user) && ($current_user->roles[0] != 'administrator') && !mo2f_is_customer_registered()){
        $methods = array("GOOGLE AUTHENTICATOR","KBA","OTP_OVER_EMAIL","OTP OVER TELEGRAM");
    }
    if(get_site_option('duo_credentials_save_successfully'))
        array_push($methods,"DUO");
    return $methods;
}

function prompt_user_to_select_2factor_mthod_inline($current_user_id, $login_status, $login_message,$redirect_to,$session_id,$qrCode){

    global $Mo2fdbQueries;
    $current_user = get_userdata($current_user_id);
    $current_selected_method = $Mo2fdbQueries->get_user_detail( 'mo2f_configured_2FA_method',$current_user_id);

    if($current_selected_method == 'MOBILE AUTHENTICATION' || $current_selected_method == 'SOFT TOKEN' || $current_selected_method == 'PUSH NOTIFICATIONS'){
        if(get_option( 'mo_2factor_admin_registration_status' ) == 'MO_2_FACTOR_CUSTOMER_REGISTERED_SUCCESS')
            prompt_user_for_miniorange_app_setup($current_user_id, $login_status, $login_message,$qrCode,$current_selected_method,$redirect_to,$session_id);
        else
            prompt_user_for_miniorange_register($current_user_id, $login_status, $login_message,$redirect_to,$session_id);
    }else if($current_selected_method == 'SMS' || $current_selected_method == 'PHONE VERIFICATION' || $current_selected_method == 'SMS AND EMAIL'){
        if(get_option( 'mo_2factor_admin_registration_status' ) == 'MO_2_FACTOR_CUSTOMER_REGISTERED_SUCCESS')
            prompt_user_for_phone_setup($current_user_id, $login_status, $login_message,$current_selected_method,$redirect_to,$session_id);
        else
            prompt_user_for_miniorange_register($current_user_id, $login_status, $login_message,$redirect_to,$session_id);
    }else if($current_selected_method == 'OTP Over Telegram' or $current_selected_method == 'OTP OVER TELEGRAM')
    {
        $current_selected_method = 'OTP Over Telegram';
        prompt_user_for_phone_setup($current_user_id, $login_status, $login_message,$current_selected_method,$redirect_to,$session_id);
    }
    else if($current_selected_method == 'Duo Authenticator'){
        prompt_user_for_duo_authenticator_setup($current_user_id, $login_status, $login_message,$redirect_to,$session_id);
    }
    else if($current_selected_method == 'GOOGLE AUTHENTICATOR' ){
        prompt_user_for_google_authenticator_setup($current_user_id, $login_status, $login_message,$redirect_to,$session_id);
    }else if($current_selected_method == 'AUTHY 2-FACTOR AUTHENTICATION'){
        prompt_user_for_authy_authenticator_setup($current_user_id, $login_status, $login_message,$redirect_to,$session_id);
    }else if($current_selected_method == 'KBA' ){
        prompt_user_for_kba_setup($current_user_id, $login_status, $login_message,$redirect_to,$session_id);
    }else if($current_selected_method == 'OUT OF BAND EMAIL' ){
        $status = $Mo2fdbQueries->get_user_detail( 'mo_2factor_user_registration_status',$current_user_id);
        if(( $status == 'MO_2_FACTOR_PLUGIN_SETTINGS' && get_site_option('mo2f_remember_device')!=1)||(get_site_option( 'mo2f_disable_kba' ) &&$login_status == 'MO_2_FACTOR_SETUP_SUCCESS')){
            if(!MO2F_IS_ONPREM)
            {
                $current_user = get_userdata($current_user_id);
                $email = $current_user->user_email;
                $tempEmail  = get_user_meta($current_user->ID,'mo2f_email_miniOrange',true);
                if(isset($tempEmail) and $tempEmail != '')
                    $email = $tempEmail;
                create_user_in_miniOrange($current_user_id,$email,$current_selected_method);
            }
            $Mo2fdbQueries->update_user_details( $current_user_id, array('mo_2factor_user_registration_status' =>'MO_2_FACTOR_PLUGIN_SETTINGS') );
            $pass2fa= new Miniorange_Password_2Factor_Login();
            $pass2fa->mo2fa_pass2login($redirect_to, $session_id);
            }
        prompt_user_for_setup_success($current_user_id, $login_status, $login_message,$redirect_to,$session_id);
    }else{
        $current_user = get_userdata($current_user_id);
        if(isset($current_user->roles[0]))
        $current_user_role=$current_user->roles[0];
        $opt=fetch_methods($current_user);
    ?>  
        <html>
            <head>
                <meta charset="utf-8"/>
                <meta http-equiv="X-UA-Compatible" content="IE=edge">
                <meta name="viewport" content="width=device-width, initial-scale=1">
                <?php
                    mo2f_inline_css_and_js();
                ?>
            </head>
            <body>
                <div class="mo2f_modal1" tabindex="-1" role="dialog" id="myModal51">
                    <div class="mo2f-modal-backdrop"></div>
                    <div class="mo_customer_validation-modal-dialog mo_customer_validation-modal-md">
                        <div class="login mo_customer_validation-modal-content">
                            <div class="mo2f_modal-header">
                                <h3 class="mo2f_modal-title"><button type="button" class="mo2f_close" data-dismiss="modal" aria-label="Close" title="<?php echo __('Back to login','miniorange-2-factor-authentication');?>" onclick="mologinback();"><span aria-hidden="true">&times;</span></button>

                                <?php echo __('New security system has been enabled', 'miniorange-2-factor-authentication'); ?></h3>
                            </div>
                            <div class="mo2f_modal-body">
                                <?php echo __('<b> Configure a Two-Factor method to protect your account</b>', 'miniorange-2-factor-authentication');
                                if(isset($login_message) && !empty($login_message)) { 
                                    echo '<br><br>';
                                
                                 ?>
                                    
                                    <div  id="otpMessage">
                                        <p class="mo2fa_display_message_frontend" style="text-align: left !important;"><?php echo __($login_message, 'miniorange-2-factor-authentication'); ?></p>
                                    </div>
                                <?php }else
                                        echo '<br>';
                                 ?>
                                 
                                 <br>
                                <span class="<?php if( !(in_array("GOOGLE AUTHENTICATOR", $opt))  ){ echo "mo2f_td_hide"; }else { echo "mo2f_td_show"; }?>">
                                    <label title="<?php echo __('You have to enter 6 digits code generated by Authenticator App to login. Supported in Smartphones only.', 'miniorange-2-factor-authentication'); ?>">
                                    <input type="radio"  name="mo2f_selected_2factor_method"  value="GOOGLE AUTHENTICATOR"  />
                                    <?php echo __('Google / Authy / Microsoft Authenticator<br> &nbsp;&nbsp;&nbsp; &nbsp;
                                    (Any TOTP Based Authenticatior App)', 'miniorange-2-factor-authentication'); ?>
                                </label>
                                <br>
                                </span>
                                <span class="<?php if( !(in_array("OUT OF BAND EMAIL", $opt))  ){ echo "mo2f_td_hide"; }else { echo "mo2f_td_show"; } ?>" >
                                    <label title="<?php echo __('You will receive an email with link. You have to click the ACCEPT or DENY link to verify your email. Supported in Desktops, Laptops, Smartphones.', 'miniorange-2-factor-authentication'); ?>">
                                                <input type="radio"  name="mo2f_selected_2factor_method"  value="OUT OF BAND EMAIL"  />
                                                <?php echo __('Email Verification', 'miniorange-2-factor-authentication'); ?>
                                    </label>
                                    <br>
                                </span> 
                                <span class="<?php if( !(in_array("SMS", $opt))  ){ echo "mo2f_td_hide"; }else { echo "mo2f_td_show"; } ?>" >
                                        <label title="<?php echo __('You will receive a one time passcode via SMS on your phone. You have to enter the otp on your screen to login. Supported in Smartphones, Feature Phones.', 'miniorange-2-factor-authentication'); ?>">
                                            <input type="radio"  name="mo2f_selected_2factor_method"  value="SMS"  />
                                            <?php echo __('OTP Over SMS', 'miniorange-2-factor-authentication'); ?>
                                        </label>
                                    <br>
                                </span>
                                <span class="<?php if(  !(in_array("PHONE VERIFICATION", $opt))  ){ echo "mo2f_td_hide"; }else { echo "mo2f_td_show"; } ?>">
                                        <label title="<?php echo __('You will receive a phone call telling a one time passcode. You have to enter the one time passcode to login. Supported in Landlines, Smartphones, Feature phones.', 'miniorange-2-factor-authentication'); ?>">
                                            <input type="radio"  name="mo2f_selected_2factor_method"  value="PHONE VERIFICATION"  />
                                            <?php echo __('Phone Call Verification', 'miniorange-2-factor-authentication'); ?>
                                        </label>
                                    <br>
                                </span>
                                <span class="<?php if(  !(in_array("SOFT TOKEN", $opt))  ){ echo "mo2f_td_hide"; }else { echo "mo2f_td_show"; } ?>" >
                                        <label title="<?php echo __('You have to enter 6 digits code generated by miniOrange Authenticator App like Google Authenticator code to login. Supported in Smartphones only.', 'miniorange-2-factor-authentication'); ?>" >
                                            <input type="radio"  name="mo2f_selected_2factor_method"  value="SOFT TOKEN"  />
                                            <?php echo __('Soft Token', 'miniorange-2-factor-authentication'); ?>
                                        </label>
                                    <br>
                                </span>
                                <span class="<?php if(  !(in_array("OTP OVER TELEGRAM", $opt))  ){ echo "mo2f_td_hide"; }else { echo "mo2f_td_show"; } ?>" >
                                        <label title="<?php echo __('You will get an OTP on your TELEGRAM app from miniOrange Bot.', 'miniorange-2-factor-authentication'); ?>" >
                                            <input type="radio"  name="mo2f_selected_2factor_method"  value="OTP OVER TELEGRAM"  />
                                            <?php echo __('OTP Over TELEGRAM', 'miniorange-2-factor-authentication'); ?>
                                        </label>
                                    <br>
                                </span>
                                <span class="<?php if(  !(in_array("OTP OVER WHATSAPP", $opt))  ){ echo "mo2f_td_hide"; }else { echo "mo2f_td_show"; } ?>" >
                                        <label title="<?php echo __('You will get an OTP on your WHATSAPP app from miniOrange Bot.', 'miniorange-2-factor-authentication'); ?>" >
                                            <input type="radio"  name="mo2f_selected_2factor_method"  value="OTP OVER WHATSAPP"  />
                                            <?php echo __('OTP Over WHATSAPP', 'miniorange-2-factor-authentication'); ?>
                                        </label>
                                    <br>
                                </span>
                                <span class="<?php if(  !(in_array("MOBILE AUTHENTICATION", $opt))  ){ echo "mo2f_td_hide"; }else { echo "mo2f_td_show"; }?>">
                                        <label title="<?php echo __('You have to scan the QR Code from your phone using miniOrange Authenticator App to login. Supported in Smartphones only.', 'miniorange-2-factor-authentication'); ?>">
                                            <input type="radio"  name="mo2f_selected_2factor_method"  value="MOBILE AUTHENTICATION"  />
                                            <?php echo __('QR Code Authentication', 'miniorange-2-factor-authentication'); ?>
                                        </label>
                                    <br>
                                </span>
                                <span class="<?php if(  !(in_array("PUSH NOTIFICATIONS", $opt))  ){ echo "mo2f_td_hide"; }else { echo "mo2f_td_show"; } ?>" >
                                        <label title="<?php echo __('You will receive a push notification on your phone. You have to ACCEPT or DENY it to login. Supported in Smartphones only.', 'miniorange-2-factor-authentication'); ?>">
                                            <input type="radio"  name="mo2f_selected_2factor_method"  value="PUSH NOTIFICATIONS"  />
                                            <?php echo __('Push Notification', 'miniorange-2-factor-authentication'); ?>
                                        </label>
                                        <br>    
                                </span>
                                <span class="<?php if( !(in_array("AUTHY 2-FACTOR AUTHENTICATION", $opt))  ){ echo "mo2f_td_hide"; }else { echo "mo2f_td_show"; }?>">
                                            <label title="<?php echo __('You have to enter 6 digits code generated by Authy 2-Factor Authentication App to login. Supported in Smartphones only.', 'miniorange-2-factor-authentication'); ?>">
                                                <input type="radio"  name="mo2f_selected_2factor_method"  value="AUTHY 2-FACTOR AUTHENTICATION"  />
                                                <?php echo __('Authy 2-Factor Authentication', 'miniorange-2-factor-authentication'); ?>
                                            </label>
                                            <br>
                                </span>
                                <span class="<?php if( !(in_array("KBA", $opt))  ){ echo "mo2f_td_hide"; }else { echo "mo2f_td_show"; }?>">
                                    <label title="<?php echo __('You have to answers some knowledge based security questions which are only known to you to authenticate yourself. Supported in Desktops,Laptops,Smartphones.', 'miniorange-2-factor-authentication'); ?>" >
                                    <input type="radio"  name="mo2f_selected_2factor_method"  value="KBA"  />
                                                <?php echo __('Security Questions ( KBA )', 'miniorange-2-factor-authentication'); ?>
                                            </label>
                                            <br>
                                </span>
                                <span class="<?php if( !(in_array("SMS AND EMAIL", $opt))  ){ echo "mo2f_td_hide"; }else { echo "mo2f_td_show"; }?>">
                                    <label title="<?php echo __('You will receive a one time passcode via SMS on your phone and your email. You have to enter the otp on your screen to login. Supported in Smartphones, Feature Phones.', 'miniorange-2-factor-authentication'); ?>" >
                                    <input type="radio"  name="mo2f_selected_2factor_method"  value="SMS AND EMAIL"  />
                                                <?php echo __('OTP Over SMS and Email', 'miniorange-2-factor-authentication'); ?>
                                            </label>
                                            <br>
                                </span>
                                <span class="<?php if( !(in_array("OTP_OVER_EMAIL", $opt))  ){ echo "mo2f_td_hide"; }else { echo "mo2f_td_show"; }?>">
                                    <label title="<?php echo __('You will receive a one time passcode on your email. You have to enter the otp on your screen to login. Supported in Smartphones, Feature Phones.', 'miniorange-2-factor-authentication'); ?>" >
                                    <input type="radio"  name="mo2f_selected_2factor_method"  value="OTP OVER EMAIL"  />
                                                <?php echo __('OTP Over Email', 'miniorange-2-factor-authentication'); ?>
                                            </label>
                                             <br>
                                </span>
                                 <span class="<?php if(  !(in_array("DUO", $opt))  ){ echo "mo2f_td_hide"; }else { echo "mo2f_td_show"; } ?>" >
                                        <label title="<?php echo __('You will receive a push notification on your phone. You have to ACCEPT or DENY it to login. Supported in Smartphones only.', 'miniorange-2-factor-authentication'); ?>">
                                            <input type="radio"  name="mo2f_selected_2factor_method"  value=" DUO PUSH NOTIFICATIONS"  />
                                            <?php echo __('Duo Push Notification', 'miniorange-2-factor-authentication'); ?>
                                        </label>
                                        <br>    
                                </span>
                                
                                <?php 

                                $object= new Miniorange_Password_2Factor_Login();

                                if(get_site_option('mo2f_grace_period')=="on" && (!$object->mo2f_is_grace_period_expired($current_user) || $object->mo2f_is_new_user($current_user)))
                                { ?><br>
                                <?php
                                    update_site_option('mo2f_user_login_status_'.$current_user->ID,1);

                                   ?>
                                           <a href="#skiptwofactor" style="color:#F4D03F ;font-weight:bold;margin-left:35%;"><?php echo __('Skip Two Factor', 'miniorange-2-factor-authentication'); ?></a>
                                           <br>
                                           <?php }?>
                                
                                <?php mo2f_customize_logo() ?>
                            </div>
                        </div>
                    </div>
                </div>
                <form name="f" id="mo2f_backto_mo_loginform" method="post" action="<?php echo esc_url(wp_login_url()); ?>" style="display:none;">
                    <input type="hidden" name="miniorange_mobile_validation_failed_nonce" value="<?php echo esc_html(wp_create_nonce('miniorange-2-factor-mobile-validation-failed-nonce')); ?>" />
                    <input type="hidden" name="redirect_to" value="<?php echo esc_url($redirect_to); ?>"/>
                    <input type="hidden" name="session_id" value="<?php echo esc_html($session_id); ?>"/>
                </form>
                <form name="f" method="post" action="" id="mo2f_select_2fa_methods_form" style="display:none;">
                    <input type="hidden" name="mo2f_selected_2factor_method" />
                    <input type="hidden" name="miniorange_inline_save_2factor_method_nonce" value="<?php echo esc_html(wp_create_nonce('miniorange-2-factor-inline-save-2factor-method-nonce')); ?>" />
                    <input type="hidden" name="option" value="miniorange_inline_save_2factor_method" />
                    <input type="hidden" name="redirect_to" value="<?php echo esc_url($redirect_to); ?>"/>
                    <input type="hidden" name="session_id" value="<?php echo esc_html($session_id); ?>"/>
                </form>

                <form name="f" id="mo2f_skip_loginform" method="post" action="" style="display:none;">
                    <input type="hidden" name="option" value="mo2f_skip_2fa_setup" />
                    <input type="hidden" name="miniorange_skip_2fa_nonce" value="<?php echo esc_html(wp_create_nonce('miniorange-2-factor-skip-nonce')); ?>" />
                    <input type="hidden" name="redirect_to" value="<?php echo esc_url($redirect_to); ?>"/>
                    <input type="hidden" name="session_id" value="<?php echo esc_html($session_id); ?>"/>
                </form>
            
            <script>
                function mologinback(){
                    jQuery('#mo2f_backto_mo_loginform').submit();
                }
                jQuery('input:radio[name=mo2f_selected_2factor_method]').click(function() {
                    var selectedMethod = jQuery(this).val();
                    document.getElementById("mo2f_select_2fa_methods_form").elements[0].value = selectedMethod;
                    jQuery('#mo2f_select_2fa_methods_form').submit();
                });
                jQuery('a[href="#skiptwofactor"]').click(function(e) {
                    
                jQuery('#mo2f_skip_loginform').submit();
            });
            </script>
            </body>
        </html>
<?php 
    } 
}

function create_user_in_miniOrange($current_user_id,$email,$currentMethod)
{
    
    global $Mo2fdbQueries;
    $mo2f_user_email = $Mo2fdbQueries->get_user_detail( 'mo2f_user_email', $current_user_id );
    if(isset($mo2f_user_email) and $mo2f_user_email != '')
        $email = $mo2f_user_email;

    $current_user = get_userdata($current_user_id);
	if($current_user_id == get_option('mo2f_miniorange_admin'))
		$email = get_option('mo2f_email');

        $enduser    = new Two_Factor_Setup();
        $check_user = json_decode( $enduser->mo_check_user_already_exist( $email ), true );
        
        if(json_last_error() == JSON_ERROR_NONE){

            if($check_user['status'] == 'ERROR'){
                return Mo2fConstants:: langTranslate( $check_user['message']);

            }
            else if(strcasecmp($check_user['status' ], 'USER_FOUND') == 0){
                                        
                $Mo2fdbQueries->update_user_details( $current_user_id, array(
                'user_registration_with_miniorange' =>'SUCCESS',
                'mo2f_user_email' =>$email,
                'mo_2factor_user_registration_status' =>'MO_2_FACTOR_INITIALIZE_TWO_FACTOR'
                ) );
                update_site_option(base64_encode("totalUsersCloud"),get_site_option(base64_encode("totalUsersCloud"))+1);
                    
                $mo2fa_login_status = 'MO_2_FACTOR_PROMPT_USER_FOR_2FA_METHODS';
            }
            else if(strcasecmp($check_user['status'], 'USER_NOT_FOUND') == 0){

                $content = json_decode($enduser->mo_create_user($current_user,$email), true);
                if(json_last_error() == JSON_ERROR_NONE) {
                    if(strcasecmp($content['status'], 'SUCCESS') == 0) {
                    update_site_option(base64_encode("totalUsersCloud"),get_site_option(base64_encode("totalUsersCloud"))+1);
                    $Mo2fdbQueries->update_user_details( $current_user_id, array(
                    'user_registration_with_miniorange' =>'SUCCESS',
                    'mo2f_user_email' =>$email,
                    'mo_2factor_user_registration_status' =>'MO_2_FACTOR_INITIALIZE_TWO_FACTOR'
                    ) );
                    
                        $mo2fa_login_message = '';
                        $mo2fa_login_status = 'MO_2_FACTOR_PROMPT_USER_FOR_2FA_METHODS';
                    }
                }
                    

            }
            else if(strcasecmp($check_user['status'], 'USER_FOUND_UNDER_DIFFERENT_CUSTOMER') == 0){
                   $mo2fa_login_message = __('The email associated with your account is already registered. Please contact your admin to change the email.','miniorange-2-factor-authentication');
                   $mo2fa_login_status = 'MO_2_FACTOR_PROMPT_FOR_RELOGIN';
                   mo2f_inline_email_form($email,$current_user_id);
                   exit;
            }

        }

}

function mo2f_inline_email_form($email,$current_user_id)
{
    ?>
     <html>
            <head>
                <meta charset="utf-8"/>
                <meta http-equiv="X-UA-Compatible" content="IE=edge">
                <meta name="viewport" content="width=device-width, initial-scale=1">
                <?php
                    mo2f_inline_css_and_js();
                ?>
            </head>
            <body>
                <div class="mo2f_modal" tabindex="-1" role="dialog" id="myModal5">
                    <div class="mo2f-modal-backdrop"></div>
                    <div class="mo_customer_validation-modal-dialog mo_customer_validation-modal-md">
                        <div class="login mo_customer_validation-modal-content">
                            <div class="mo2f_modal-header">
                                <h3 class="mo2f_modal-title"><button type="button" class="mo2f_close" data-dismiss="modal" aria-label="Close" title="<?php echo __('Back to login','miniorange-2-factor-authentication');?>" onclick="mologinback();"><span aria-hidden="true">&times;</span></button>
                                <?php echo __('Email already registered.', 'miniorange-2-factor-authentication'); ?></h3>
                            </div>
                            <div class="mo2f_modal-body">
                                <form action="" method="post" name="f">
                                    <p>The Email assoicated with your account is already registered in miniOrange. Please use a different email address or contact miniOrange.
                                    </p><br>
                                    <i><b>Enter your Email:&nbsp;&nbsp;&nbsp; </b> <input type ='email' id='emailInlineCloud' name='emailInlineCloud' size= '40' required value="<?php echo $email;?>"/></i>
                                    <br>
                                    <p id="emailalredyused" style="color: red;" hidden>This email is already associated with miniOrange.</p>
                                    <br>
                                    <input type="hidden" name="miniorange_emailChange_nonce" value="<?php echo esc_html(wp_create_nonce('miniorange-2-factor-email-change-nonce')); ?>" />
                                    <input type="text" name="current_user_id" hidden id="current_user_id" value="<?php echo $current_user_id;?>" />
                                    <button type="submit" class="button button-primary button-large" style ="margin-left: 165px;" id="save_entered_email_inlinecloud">Save</button>
                                    <input type="hidden" name="redirect_to" value="<?php echo esc_url($redirect_to); ?>"/>
                                    <input type="hidden" name="session_id" value="<?php echo esc_html($session_id); ?>"/>
                                </form>
                                    <br>
                                <?php mo2f_customize_logo() ?>
                            </div>
                        </div>
                    </div>
                </div>
                <form name="f" id="mo2f_backto_mo_loginform" method="post" action="<?php echo wp_login_url(); ?>" style="display:none;">
                    <input type="hidden" name="miniorange_mobile_validation_failed_nonce" value="<?php echo esc_html(('miniorange-2-factor-mobile-validation-failed-nonce')); ?>" />
                </form>
                <form name="f" method="post" action="" id="mo2f_select_2fa_methods_form" style="display:none;">
                    <input type="hidden" name="mo2f_selected_2factor_method" />
                    <input type="hidden" name="miniorange_inline_save_2factor_method_nonce" value="<?php echo esc_html(wp_create_nonce('miniorange-2-factor-inline-save-2factor-method-nonce')); ?>" />
                    <input type="hidden" name="option" value="miniorange_inline_save_2factor_method" />
                    <input type="hidden" name="redirect_to" value="<?php echo esc_url($redirect_to); ?>"/>
                    <input type="hidden" name="session_id" value="<?php echo esc_html($session_id); ?>"/>
                </form>
                <?php if(get_site_option('mo2f_skip_inline_option')&& !get_site_option('mo2f_enable_emailchange')){ ?>
                <form name="f" id="mo2f_skip_loginform" method="post" action="" style="display:none;">
                    <input type="hidden" name="miniorange_skip_2fa" value="<?php echo esc_html(wp_create_nonce('miniorange-2-factor-skip-nonce')); ?>" />
                    <input type="hidden" name="redirect_to" value="<?php echo esc_url($redirect_to); ?>"/>
                    <input type="hidden" name="session_id" value="<?php echo esc_html($session_id); ?>"/>
                </form>
                            <?php } ?>
            
            <script type="text/javascript">
                jQuery('#save_entered_email_inlinecloud1').click(function(){
                    var email = jQuery('#emailInlineCloud').val();
                    var nonce = '<?php echo esc_html(wp_create_nonce("checkuserinminiOrangeNonce"));?>';
                    var data = {
                                'action'                    : 'mo_two_factor_ajax',
                                'mo_2f_two_factor_ajax'     : 'mo2f_check_user_exist_miniOrange',
                                'email'                     : email,
                                'nonce' :  nonce
                                
                            };

                    var ajaxurl = '<?php echo esc_url(admin_url('')); ?>';
                                       

                    jQuery.post(ajaxurl, data, function(response) {
                        
                        if(response == 'alreadyExist')
                        {
                            jQuery('#emailalredyused').show();
                        }
                        else if(response =='USERCANBECREATED')
                        {
                            document.getElementById("mo2f_select_2fa_methods_form").elements[0].value = selectedMethod;
                            jQuery('#mo2f_select_2fa_methods_form').submit();
                        }
                     });
                   
                });
                

            </script>
            </body>

       <?php
}
function prompt_user_for_miniorange_app_setup($current_user_id, $login_status, $login_message,$qrCode,$currentMethod,$redirect_to,$session_id){
   
    global $Mo2fdbQueries;
    if(isset($qrCode)){
		$qrCodedata = $qrCode['mo2f-login-qrCode'];
		$showqrCode = $qrCode['mo2f_show_qr_code'];
	}
    $current_user = get_userdata($current_user_id);
    $email = $current_user->user_email;

    $opt=fetch_methods($current_user);
    
    $mobile_registration_status = $Mo2fdbQueries->get_user_detail( 'mobile_registration_status',$current_user_id);
    ?>
    <html>
        <head>  <meta charset="utf-8"/>
            <meta http-equiv="X-UA-Compatible" content="IE=edge">
            <meta name="viewport" content="width=device-width, initial-scale=1">
            <?php
             mo2f_inline_css_and_js();
            ?>
        </head>
        <body>
            <div class="mo2f_modal" tabindex="-1" role="dialog" id="myModal5">
                <div class="mo2f-modal-backdrop"></div>
                <div class="mo2f_modal-dialog mo2f_modal-lg" >
                    <div class="login mo_customer_validation-modal-content">
                        <div class="mo2f_modal-header">
                            <h4 class="mo2f_modal-title"><button type="button" class="mo2f_close" data-dismiss="modal" aria-label="Close" title="<?php echo __('Back to login', 'miniorange-2-factor-authentication'); ?>" onclick="mologinback();"><span aria-hidden="true">&times;</span></button>
                            <?php echo __('Setup miniOrange', 'miniorange-2-factor-authentication'); ?> <b><?php echo __('Authenticator', 'miniorange-2-factor-authentication'); ?></b> <?php echo __('App', 'miniorange-2-factor-authentication'); ?></h4>
                        </div>
                        <div class="mo2f_modal-body">
                            <?php if(isset($login_message) && !empty($login_message)) {  ?>
                                
                                <div  id="otpMessage">
                                    <p class="mo2fa_display_message_frontend" style="text-align: left !important;"><?php echo __($login_message, 'miniorange-2-factor-authentication'); ?></p>
                                </div>
                            <?php } ?>
                            <div style="margin-right:7px;"><?php download_instruction_for_mobile_app($current_user_id,$mobile_registration_status); ?></div>
                            <div class="mo_margin_left">
                                <h3><?php echo __('Step-2 : Scan QR code', 'miniorange-2-factor-authentication'); ?></h3><hr class="mo_hr">
                                <div id="mo2f_configurePhone"><h4><?php echo __('Please click on \'Configure your phone\' button below to see QR Code.', 'miniorange-2-factor-authentication'); ?></h4>
                                    <center>
                                    <?php if (sizeof($opt) > 1) { ?>
                                        <input type="button" name="back" id="mo2f_inline_back_btn" class="miniorange_button" value="<?php echo __('Back', 'miniorange-2-factor-authentication'); ?>" />
                                    <?php } ?>
                                        <input type="button" name="submit" onclick="moconfigureapp();" class="miniorange_button" value="<?php echo __('Configure your phone', 'miniorange-2-factor-authentication'); ?>" />
                                    </center>
                                </div>
                                <?php 
                                    if(isset($showqrCode) && $showqrCode == 'MO_2_FACTOR_SHOW_QR_CODE' && isset($_POST['miniorange_inline_show_qrcode_nonce']) && wp_verify_nonce( $_POST['miniorange_inline_show_qrcode_nonce'], 'miniorange-2-factor-inline-show-qrcode-nonce' )){
                                        initialize_inline_mobile_registration($current_user,$session_id,$qrCodedata); ?>
                                <?php } ?>
                                
                            <?php mo2f_customize_logo() ?>
                            </div>
                            <br>
                            <br>
                        </div>
                    </div>
                </div>
            </div>
            <form name="f" id="mo2f_backto_mo_loginform" method="post" action="<?php echo wp_login_url(); ?>" style="display:none;">
                <input type="hidden" name="miniorange_mobile_validation_failed_nonce" value="<?php echo esc_html(wp_create_nonce('miniorange-2-factor-mobile-validation-failed-nonce')); ?>" />
                <input type="hidden" name="redirect_to" value="<?php echo esc_url($redirect_to); ?>"/>
                <input type="hidden" name="session_id" value="<?php echo esc_html($session_id); ?>"/>
            </form>
            <form name="f" method="post" action="" id="mo2f_inline_configureapp_form" style="display:none;">
                <input type="hidden" name="option" value="miniorange_inline_show_mobile_config"/>
                <input type="hidden" name="redirect_to" value="<?php echo esc_url($redirect_to); ?>"/>
                <input type="hidden" name="session_id" value="<?php echo esc_html($session_id); ?>"/>
                <input type="hidden" name="miniorange_inline_show_qrcode_nonce" value="<?php echo esc_html(wp_create_nonce('miniorange-2-factor-inline-show-qrcode-nonce')); ?>" />
            </form>
            <form name="f" method="post" id="mo2f_inline_mobile_register_form" action="" style="display:none;">
                <input type="hidden" name="option" value="miniorange_inline_complete_mobile"/>
                <input type="hidden" name="redirect_to" value="<?php echo esc_url($redirect_to); ?>"/>
                <input type="hidden" name="session_id" value="<?php echo esc_html($session_id); ?>"/>
                <input type="hidden" name="mo_auth_inline_mobile_registration_complete_nonce" value="<?php echo esc_html(wp_create_nonce('miniorange-2-factor-inline-mobile-registration-complete-nonce')); ?>" />
            </form>
            <?php if (sizeof($opt) > 1) { ?>
                <form name="f" method="post" action="" id="mo2f_goto_two_factor_form">
                    <input type="hidden" name="option" value="miniorange_back_inline"/>
                    <input type="hidden" name="miniorange_inline_two_factor_setup" value="<?php echo esc_html(wp_create_nonce('miniorange-2-factor-inline-setup-nonce')); ?>" />
                    <input type="hidden" name="redirect_to" value="<?php echo esc_url($redirect_to); ?>"/>
                    <input type="hidden" name="session_id" value="<?php echo esc_html($session_id); ?>"/>
                </form>
            <?php } ?>
        <script>
            function mologinback(){
                jQuery('#mo2f_backto_mo_loginform').submit();
            }
            function moconfigureapp(){
                jQuery('#mo2f_inline_configureapp_form').submit();
            }
            jQuery('#mo2f_inline_back_btn').click(function() {  
                    jQuery('#mo2f_goto_two_factor_form').submit();
            });
            <?php 
                if(isset($showqrCode) && $showqrCode == 'MO_2_FACTOR_SHOW_QR_CODE' && isset($_POST['miniorange_inline_show_qrcode_nonce']) && wp_verify_nonce( sanitize_text_field($_POST['miniorange_inline_show_qrcode_nonce']), 'miniorange-2-factor-inline-show-qrcode-nonce' )){
            ?>
            <?php } ?>
        </script>
        </body>
    </html>
<?php 
}
function prompt_user_for_duo_authenticator_setup($current_user_id, $login_status, $login_message,$redirect_to,$session_id){
    global $Mo2fdbQueries;
    $current_user = get_userdata($current_user_id);
    $email = $current_user->user_email;
    $opt=fetch_methods($current_user); 
    $mobile_registration_status = $Mo2fdbQueries->get_user_detail( 'mobile_registration_status',$current_user_id);
    
?>
    <html>
        <head>  <meta charset="utf-8"/>
            <meta http-equiv="X-UA-Compatible" content="IE=edge">
            <meta name="viewport" content="width=device-width, initial-scale=1">
            <?php
             mo2f_inline_css_and_js();
            ?>
        </head>
        <body>
            <div class="mo2f_modal" tabindex="-1" role="dialog" id="myModal5">
                <div class="mo2f-modal-backdrop"></div>
                <div class="mo2f_modal-dialog mo2f_modal-lg" >
                    <div class="login mo_customer_validation-modal-content">
                        <div class="mo2f_modal-header">
                            <h4 class="mo2f_modal-title"><button type="button" class="mo2f_close" data-dismiss="modal" aria-label="Close" title="<?php echo __('Back to login', 'miniorange-2-factor-authentication'); ?>" onclick="mologinback();"><span aria-hidden="true">&times;</span></button>
                            <?php echo __('Setup Duo', 'miniorange-2-factor-authentication'); ?> <b><?php echo __('Authenticator', 'miniorange-2-factor-authentication'); ?></b> <?php echo __('App', 'miniorange-2-factor-authentication'); ?></h4>
                        </div>
                        <div class="mo2f_modal-body">
                            <?php if(isset($login_message) && !empty($login_message)) {  ?>
                                
                                <div  id="otpMessage">
                                    <p class="mo2fa_display_message_frontend" style="text-align: left !important;"><?php echo __(esc_html($login_message), 'miniorange-2-factor-authentication'); ?></p>
                                </div>
                            <?php } ?>
                            <div style="margin-right:7px;"><?php mo2f_inline_download_instruction_for_duo_mobile_app($mobile_registration_status);

                            ?></div>
                            <div class="mo_margin_left">
                                <h3><?php echo __('Step-2 : Setup Duo Push Notification', 'miniorange-2-factor-authentication'); ?></h3><hr class="mo_hr">
                                <div id="mo2f_configurePhone"><h4><?php echo __('Please click on \'Configure your phone\' button below to setup duo push notification.', 'miniorange-2-factor-authentication'); ?></h4>
                                    <center>
                                    <?php if (sizeof($opt) > 1) { ?>
                                        <input type="button" name="back" id="mo2f_inline_back_btn" class="miniorange_button" value="<?php echo __('Back', 'miniorange-2-factor-authentication'); ?>" />
                                    <?php } ?>
                                        <input type="button" name="submit" onclick="moconfigureapp();" class="miniorange_button" value="<?php echo __('Configure your phone', 'miniorange-2-factor-authentication'); ?>" />
                                    </center>
                                </div>
                                <?php 

                                if(isset($_POST['option']) && sanitize_text_field($_POST['option']) =='miniorange_inline_duo_auth_mobile_complete'){
                                         go_for_user_enroll_on_duo($current_user,$session_id);
                                         ?>
                                <?php }else if(isset($_POST['option']) && sanitize_text_field($_POST['option']) == 'duo_mobile_send_push_notification_for_inline_form') {

                                    initialize_inline_duo_auth_registration($current_user,$session_id);
                                    ?>

                                  <?php }?>   
                                
                            <?php mo2f_customize_logo() ?>
                            </div>
                            <br>
                            <br>
                        </div>
                    </div>
                </div>
            </div>
            <form name="f" id="mo2f_backto_mo_loginform" method="post" action="<?php echo esc_url(wp_login_url()); ?>" style="display:none;">
                <input type="hidden" name="miniorange_mobile_validation_failed_nonce" value="<?php echo esc_html(wp_create_nonce('miniorange-2-factor-mobile-validation-failed-nonce')); ?>" />
                <input type="hidden" name="redirect_to" value="<?php echo esc_url($redirect_to); ?>"/>
                <input type="hidden" name="session_id" value="<?php echo esc_html($session_id); ?>"/>
            </form>
            <form name="f" method="post" action="" id="mo2f_inline_configureapp_form" style="display:none;">
                <input type="hidden" name="option" value="miniorange_inline_show_mobile_config"/>
                <input type="hidden" name="redirect_to" value="<?php echo esc_url($redirect_to); ?>"/>
                <input type="hidden" name="session_id" value="<?php echo esc_html($session_id); ?>"/>
                <input type="hidden" name="miniorange_inline_show_qrcode_nonce" value="<?php echo esc_html(wp_create_nonce('miniorange-2-factor-inline-show-qrcode-nonce')); ?>" />
            </form>
            <form name="f" method="post" id="mo2f_inline_duo_auth_register_form" action="" style="display:none;">
                <input type="hidden" name="option" value="miniorange_inline_duo_auth_mobile_complete"/>
                <input type="hidden" name="redirect_to" value="<?php echo esc_url($redirect_to); ?>"/>
                <input type="hidden" name="session_id" value="<?php echo esc_html($session_id); ?>"/>
                <input type="hidden" name="mo_auth_inline_duo_auth_mobile_registration_complete_nonce" value="<?php echo esc_html(wp_create_nonce('miniorange-2-factor-inline-duo_auth-registration-complete-nonce')); ?>" />
            </form>
            <?php if (sizeof($opt) > 1) { ?>
                <form name="f" method="post" action="" id="mo2f_goto_two_factor_form">
                    <input type="hidden" name="option" value="miniorange_back_inline"/>
                    <input type="hidden" name="miniorange_inline_two_factor_setup" value="<?php echo esc_html(wp_create_nonce('miniorange-2-factor-inline-setup-nonce')); ?>" />
                    <input type="hidden" name="redirect_to" value="<?php echo esc_url($redirect_to); ?>"/>
                    <input type="hidden" name="session_id" value="<?php echo esc_html($session_id); ?>"/>
                </form>
            <?php } ?>
        <script>
            function mologinback(){
                jQuery('#mo2f_backto_mo_loginform').submit();
            }
            function moconfigureapp(){
                jQuery('#mo2f_inline_duo_auth_register_form').submit();
            }
            jQuery('#mo2f_inline_back_btn').click(function() {  
                    jQuery('#mo2f_goto_two_factor_form').submit();
            });
            <?php 
                if(isset($showqrCode) && $showqrCode == 'MO_2_FACTOR_SHOW_QR_CODE' && isset($_POST['miniorange_inline_show_qrcode_nonce']) && wp_verify_nonce( sanitize_text_field($_POST['miniorange_inline_show_qrcode_nonce']), 'miniorange-2-factor-inline-show-qrcode-nonce' )){
            ?>
            <?php } ?>
        </script>
        </body>
    </html>
<?php     
}

function prompt_user_for_google_authenticator_setup($current_user_id, $login_status, $login_message,$redirect_to,$session_id){
    $ga_secret = MO2f_Utility::mo2f_get_transient($session_id, 'secret_ga');
    $data = MO2f_Utility::mo2f_get_transient($session_id, 'ga_qrCode');
    global $Mo2fdbQueries;
    if(empty($data)){
        $user = get_user_by('ID',$current_user_id);
        if(!MO2F_IS_ONPREM){
            if(!get_user_meta($user->ID, 'mo2f_google_auth', true)){
                Miniorange_Authentication::mo2f_get_GA_parameters($user);
            }
            $mo2f_google_auth = get_user_meta($user->ID, 'mo2f_google_auth', true);
            $data = isset($mo2f_google_auth['ga_qrCode']) ? $mo2f_google_auth['ga_qrCode'] : null;
            $ga_secret = isset($mo2f_google_auth['ga_secret']) ? $mo2f_google_auth['ga_secret'] : null;
            MO2f_Utility::mo2f_set_transient($session_id, 'secret_ga', $mo2f_google_auth['ga_secret']);
            MO2f_Utility::mo2f_set_transient($session_id, 'ga_qrCode', $mo2f_google_auth['ga_qrCode']);
        }else{
             include_once dirname(dirname(dirname( __FILE__ ))) .DIRECTORY_SEPARATOR . 'handler'.DIRECTORY_SEPARATOR . 'twofa'. DIRECTORY_SEPARATOR . 'gaonprem.php';
            $gauth_obj = new Google_auth_onpremise();
            $email = $Mo2fdbQueries->get_user_detail('mo2f_user_email',$user->ID);
            $onpremise_secret              = $gauth_obj->createSecret();
            $issuer                        = get_site_option( 'mo2f_GA_account_name', 'miniOrangeAu' );
            $url                           = $gauth_obj->geturl( $onpremise_secret, $issuer, $email );
            $data = $url;
            MO2f_Utility::mo2f_set_transient($session_id, 'secret_ga', $onpremise_secret);
            MO2f_Utility::mo2f_set_transient($session_id, 'ga_qrCode', $url);

        }
    }
    wp_register_script('mo2f_qr_code_js',plugins_url( "/includes/jquery-qrcode/jquery-qrcode.js", dirname(dirname(__FILE__ ))) );
    wp_register_script('mo2f_qr_code_minjs',plugins_url( "/includes/jquery-qrcode/jquery-qrcode.min.js", dirname(dirname(__FILE__ ))) );
	?>
    <html>
        <head>  <meta charset="utf-8"/>
            <meta http-equiv="X-UA-Compatible" content="IE=edge">
            <meta name="viewport" content="width=device-width, initial-scale=1">
            <?php
                mo2f_inline_css_and_js();
            ?>
        </head>
    <style>
* {
    box-sizing: border-box;
}
[class*="mcol-"] {
    float: left;
    padding: 15px;
}
/* For desktop: */
.mcol-1 {width: 50%;}
.mcol-2 {width: 50%;}
@media only screen and (max-width: 768px) {
    /* For mobile phones: */
    [class*="mcol-"] {
        width: 100%;
    }
}
</style>
        <body>
            <div class="mo2f_modal" tabindex="-1" role="dialog" id="myModal5">
                <div class="mo2f-modal-backdrop"></div>
                <div class="mo2f_modal-dialog mo2f_modal-lg" >
                    <div class="login mo_customer_validation-modal-content">
                        <div class="mo2f_modal-header">
                            <h4 class="mo2f_modal-title" style="color:black;"><button type="button" class="mo2f_close" data-dismiss="modal" aria-label="Close" title="<?php echo __('Back to login','miniorange-2-factor-authentication');?>" onclick="mologinback();"><span aria-hidden="true">&times;</span></button>
                            <?php echo __('Setup Authenticator', 'miniorange-2-factor-authentication'); ?></h4>
                        </div>
                        <div class="mo2f_modal-body">
                            <?php

                            $current_user = get_userdata($current_user_id);
                            $opt=fetch_methods($current_user);
                            ?>
                            <?php if(isset($login_message) && !empty($login_message)) {  ?>
                                <div  id="otpMessage"
                                <?php if(get_user_meta($current_user_id, 'mo2f_is_error', true)) { ?>style="background-color:#FADBD8; color:#E74C3C;?>"<?php update_user_meta($current_user_id, 'mo2f_is_error', false);} ?>
                                >
                                    <p class="mo2fa_display_message_frontend" style="text-align: left !important;"><?php echo __(esc_html($login_message), 'miniorange-2-factor-authentication'); ?></p>
                                </div>
                                <?php if(isset($login_message)) {?> <br/> <?php } ?>
                            <?php } ?>
                                    <div class="mcol-1">
                                        <div id="mo2f_choose_app_tour">
                                            <label for="authenticator_type"><b>Choose an Authenticator app:</b></label>

                                            <select id="authenticator_type">
                                                <option value="google_authenticator">Google Authenticator</option>
                                                <option value="msft_authenticator">Microsoft Authenticator</option>
                                                <option value="authy_authenticator">Authy Authenticator</option>
                                                <option value="last_pass_auth">LastPass Authenticator</option>
                                                <option value="free_otp_auth">FreeOTP Authenticator</option>
                                                <option value="duo_auth">Duo Mobile Authenticator</option>
                                            </select>
                                            <div id="links_to_apps_tour" style="background-color:white;padding:5px;">
                                                <span id="links_to_apps">
                                                 <p style="background-color:#e8e4e4;padding:5px;">Get the App - <a href="https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2" target="_blank"><b><?php echo mo2f_lt( "Android Play Store" ); ?></b></a>, &nbsp;
                                                        <a href="http://itunes.apple.com/us/app/google-authenticator/id388497605" target="_blank"><b><?php echo mo2f_lt( "iOS App Store" ); ?>.</b>&nbsp;</p></a>

                                                </span>
                                            </div>
                                        </div>
                                         <div style="font-size: 18px !important;"><?php echo __('Scan the QR code from the Authenticator App.', 'miniorange-2-factor-authentication'); ?></div>
                                            <ol>
                                                <li><?php echo __('In the app, tap on Menu and select "Set up account"', 'miniorange-2-factor-authentication'); ?></li>
                                                <li><?php echo __('Select "Scan a barcode". Use your phone\'s camera to scan this barcode.', 'miniorange-2-factor-authentication'); ?></li>
                                                <br>
                                                    <?php if(MO2F_IS_ONPREM){ ?>
                                                         <div class="mo2f_gauth" data-qrcode="<?php echo $data;?>" style="float:left;margin-left:10%;"></div>
                                                          <?php
                                                         
                                                    } else{ ?>
                                                        <div style="margin-left: 14%;">
                                                            <div class="mo2f_gauth_column_cloud mo2f_gauth_left" >
                                                                <div id="displayQrCode"><?php echo '<img id="displayGAQrCodeTour" style="line-height: 0;background:white;" src="data:image/jpg;base64,' . $data . '" />'; ?></div>
                                                            </div>
                                                        </div>
                                                    <?php }
                                                    ?>
                                                <div style="margin-top: 55%"><a href="#mo2f_scanbarcode_a" aria-expanded="false" style="color:#21618C;"><b><?php echo __('Can\'t scan the barcode?', 'miniorange-2-factor-authentication'); ?></b></a></div>

                                            </ol>
                                            <div  id="mo2f_scanbarcode_a" hidden>
                                                <ol >
                                                    <li><?php echo __('Tap Menu and select "Set up account."', 'miniorange-2-factor-authentication'); ?></li>
                                                    <li><?php echo __('Select "Enter provided key"', 'miniorange-2-factor-authentication'); ?></li>
                                                    <li><?php echo __('In "Enter account name" type your full email address.', 'miniorange-2-factor-authentication'); ?></li>
                                                    <li class="mo2f_list"><?php echo __('In "Enter your key" type your secret key:', 'miniorange-2-factor-authentication'); ?></li>
                                                        <div style="padding: 10px; background-color: #f9edbe;width: 20em;text-align: center;" >
                                                            <div style="font-size: 14px; font-weight: bold;line-height: 1.5;" >
                                                            <?php echo esc_html($ga_secret) ?>
                                                            </div>
                                                            <div style="font-size: 80%;color: #666666;">
                                                            <?php echo __('Spaces don\'t matter.', 'miniorange-2-factor-authentication'); ?>
                                                            </div>
                                                        </div>
                                                    <li class="mo2f_list"><?php echo __('Key type: make sure "Time-based" is selected.', 'miniorange-2-factor-authentication'); ?></li>
                                                    <li class="mo2f_list"><?php echo __('Tap Add.', 'miniorange-2-factor-authentication'); ?></li>
                                                </ol>
                                            </div>
                                        </div>
                                        <div class="mcol-2">
                                            <div style="font-size: 18px !important;"><b><?php echo __('Verify and Save', 'miniorange-2-factor-authentication'); ?> </b> </div><br />
                                            <div style="font-size: 15px !important;"><?php echo __('Once you have scanned the barcode, enter the 6-digit verification code generated by the Authenticator app', 'miniorange-2-factor-authentication'); ?></div><br />
                                            <form name="" method="post" id="mo2f_inline_verify_ga_code_form">
                                                <span><b><?php echo __('Code:', 'miniorange-2-factor-authentication'); ?> </b>
                                                <br />
                                                <input type="hidden" name="option" value="miniorange_inline_ga_validate">
                                                <input class="mo2f_IR_GA_token" style="margin-left:36.5%;"  autofocus="true" required="true" pattern="[0-9]{4,8}" type="text" id="google_auth_code" name="google_auth_code" placeholder="<?php echo __('Enter OTP', 'miniorange-2-factor-authentication'); ?>" /></span><br/>
                                                <div class="center">
                                                <input type="submit" name="validate" id="validate" class="miniorange_button" value="<?php echo __('Verify and Save', 'miniorange-2-factor-authentication'); ?>" />
                                                </div>
                                                <input type="hidden" name="redirect_to" value="<?php echo esc_url($redirect_to); ?>"/>
                                                <input type="hidden" name="session_id" value="<?php echo esc_html($session_id); ?>"/>
                                                <input type="hidden" name="mo2f_inline_validate_ga_nonce" value="<?php echo esc_html(wp_create_nonce('miniorange-2-factor-inline-google-auth-nonce')); ?>" />
                                            </form>
                                             <form name="f" method="post" action="" id="mo2f_goto_two_factor_form" class="center">
                                                <input type="submit" name="back" id="mo2f_inline_back_btn" class="miniorange_button" value="<?php echo mo2f_lt('Back');?>" />
                                                <input type="hidden" name="option" value="miniorange_back_inline"/>
                                                 <input type="hidden" name="redirect_to" value="<?php echo esc_url($redirect_to); ?>"/>
                                                 <input type="hidden" name="session_id" value="<?php echo esc_html($session_id); ?>"/>
                                                <input type="hidden" name="miniorange_inline_two_factor_setup" value="<?php echo esc_html(wp_create_nonce('miniorange-2-factor-inline-setup-nonce')); ?>" />
                                            </form>
                                        </div>
                                <br>
                            <br>
                            <?php mo2f_customize_logo() ?>
                        </div>
                    </div>
                </div>
            </div>
            <form name="f" id="mo2f_backto_mo_loginform" method="post" action="<?php echo wp_login_url(); ?>" style="display:none;">
                <input type="hidden" name="miniorange_mobile_validation_failed_nonce" value="<?php echo esc_html(wp_create_nonce('miniorange-2-factor-mobile-validation-failed-nonce')); ?>" />
                <input type="hidden" name="redirect_to" value="<?php echo esc_url($redirect_to); ?>"/>
                <input type="hidden" name="session_id" value="<?php echo esc_html($session_id); ?>"/>
            </form>
            <form name="f" method="post" id="mo2f_inline_app_type_ga_form" action="" style="display:none;">
                <input type="hidden" name="google_phone_type" />
                <input type="hidden" name="redirect_to" value="<?php echo esc_url($redirect_to); ?>"/>
                <input type="hidden" name="session_id" value="<?php echo esc_html($session_id); ?>"/>
                <input type="hidden" name="mo2f_inline_ga_phone_type_nonce" value="<?php echo esc_html(wp_create_nonce('miniorange-2-factor-inline-ga-phone-type-nonce')); ?>" />
            </form>
        
        <script>
            jQuery('#authenticator_type').change(function(){
                var auth_type = jQuery(this).val();
                if(auth_type == 'google_authenticator'){
                    jQuery('#links_to_apps').html('<p style="background-color:#e8e4e4;padding:5px;">' +
                        'Get the App - <a href="https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2" target="_blank"><b><?php echo mo2f_lt( "Android Play Store" ); ?></b></a>, &nbsp;' +
                        '<a href="http://itunes.apple.com/us/app/google-authenticator/id388497605" target="_blank"><b><?php echo mo2f_lt( "iOS App Store" ); ?>.</b>&nbsp;</p>');
                    jQuery('#mo2f_change_app_name').show();
                    jQuery('#links_to_apps').show();
                }else if(auth_type == 'msft_authenticator'){
                    jQuery('#links_to_apps').html('<p style="background-color:#e8e4e4;padding:5px;">' +
                        'Get the App - <a href="https://play.google.com/store/apps/details?id=com.azure.authenticator" target="_blank"><b><?php echo mo2f_lt( "Android Play Store" ); ?></b></a>, &nbsp;' +
                        '<a href="https://apps.apple.com/us/app/microsoft-authenticator/id983156458" target="_blank"><b><?php echo mo2f_lt( "iOS App Store" ); ?>.</b>&nbsp;</p>');
                    jQuery('#links_to_apps').show();
                }else if(auth_type == 'free_otp_auth'){
                    jQuery('#links_to_apps').html('<p style="background-color:#e8e4e4;padding:5px;">' +
                        'Get the App - <a href="https://play.google.com/store/apps/details?id=org.fedorahosted.freeotp" target="_blank"><b><?php echo mo2f_lt( "Android Play Store" ); ?></b></a>, &nbsp;' +
                        '<a href="https://apps.apple.com/us/app/freeotp-authenticator/id872559395" target="_blank"><b><?php echo mo2f_lt( "iOS App Store" ); ?>.</b>&nbsp;</p>');
                    jQuery('#links_to_apps').show();
                }else if(auth_type == 'duo_auth'){
                    jQuery('#links_to_apps').html('<p style="background-color:#e8e4e4;padding:5px;">' +
                        'Get the App - <a href="https://play.google.com/store/apps/details?id=com.duosecurity.duomobile" target="_blank"><b><?php echo mo2f_lt( "Android Play Store" ); ?></b></a>, &nbsp;' +
                        '<a href="https://apps.apple.com/in/app/duo-mobile/id422663827" target="_blank"><b><?php echo mo2f_lt( "iOS App Store" ); ?>.</b>&nbsp;</p>');
                    jQuery('#links_to_apps').show();
                }else if(auth_type == 'authy_authenticator'){
                    jQuery('#links_to_apps').html('<p style="background-color:#e8e4e4;padding:5px;">' +
                        'Get the App - <a href="https://play.google.com/store/apps/details?id=com.authy.authy" target="_blank"><b><?php echo mo2f_lt( "Android Play Store" ); ?></b></a>, &nbsp;' +
                        '<a href="https://itunes.apple.com/in/app/authy/id494168017" target="_blank"><b><?php echo mo2f_lt( "iOS App Store" ); ?>.</b>&nbsp;</p>');
                    jQuery('#links_to_apps').show();
                }else{
                    jQuery('#links_to_apps').html('<p style="background-color:#e8e4e4;padding:5px;">' +
                        'Get the App - <a href="https://play.google.com/store/apps/details?id=com.lastpass.authenticator" target="_blank"><b><?php echo mo2f_lt( "Android Play Store" ); ?></b></a>, &nbsp;' +
                        '<a href="https://itunes.apple.com/in/app/lastpass-authenticator/id1079110004" target="_blank"><b><?php echo mo2f_lt( "iOS App Store" ); ?>.</b>&nbsp;</p>');
                    jQuery('#mo2f_change_app_name').show();
                    jQuery('#links_to_apps').show();
                }
            });
            function mologinback(){
                jQuery('#mo2f_backto_mo_loginform').submit();
            }
            jQuery('input:radio[name=mo2f_inline_app_type_radio]').click(function() {
                var selectedPhone = jQuery(this).val();
                document.getElementById("mo2f_inline_app_type_ga_form").elements[0].value = selectedPhone;
                jQuery('#mo2f_inline_app_type_ga_form').submit();
            });
            jQuery('a[href="#mo2f_scanbarcode_a"]').click(function(){
                jQuery("#mo2f_scanbarcode_a").toggle();
            });
            jQuery(document).ready(function() {
                jQuery('.mo2f_gauth').qrcode({
                    'render': 'image',
                    size: 175,
                    'text': jQuery('.mo2f_gauth').data('qrcode')
                });
            });
            </script>
            </body>
    <?php
        echo '<head>';
            wp_print_scripts( 'mo2f_qr_code_js' );
            wp_print_scripts( 'mo2f_qr_code_minjs' );
        echo '</head>';
     }

function mo2f_inline_css_and_js(){

    wp_register_style( 'mo2f_bootstrap',plugins_url('includes/css/bootstrap.min.css', dirname(dirname(__FILE__))));
    wp_register_style('mo2f_front_end_login',plugins_url('includes/css/front_end_login.css',dirname(dirname( __FILE__))));
    wp_register_style('mo2f_style_setting',plugins_url('includes/css/style_settings.css', dirname(dirname(__FILE__))));
    wp_register_style('mo2f_hide-login',plugins_url('includes/css/hide-login.css',dirname(dirname( __FILE__))));

    wp_print_styles( 'mo2f_bootstrap');
    wp_print_styles( 'mo2f_front_end_login');
    wp_print_styles( 'mo2f_style_setting');
    wp_print_styles( 'mo2f_hide-login');

    wp_register_script('mo2f_bootstrap_js',plugins_url('includes/js/bootstrap.min.js',dirname(dirname( __FILE__))));
    wp_print_scripts( 'jquery');
    wp_print_scripts( 'mo2f_bootstrap_js' );
}


function initialize_inline_mobile_registration($current_user,$session_id,$qrCode){
        $data = $qrCode;
        
        $mo2f_login_transaction_id = MO2f_Utility::mo2f_get_transient($session_id, 'mo2f_transactionId' );

        $url = MO_HOST_NAME;
        $opt=fetch_methods($current_user);  
        ?>
            <p><?php echo __('Open your miniOrange', 'miniorange-2-factor-authentication'); ?><b> <?php echo __('Authenticator', 'miniorange-2-factor-authentication'); ?></b> <?php echo __('app and click on', 'miniorange-2-factor-authentication'); ?> <b><?php echo __('Configure button', 'miniorange-2-factor-authentication'); ?> </b> <?php echo __('to scan the QR Code. Your phone should have internet connectivity to scan QR code.', 'miniorange-2-factor-authentication'); ?> </p>
            <div class="red" style="color:#E74C3C;">
            <p><?php echo __('I am not able to scan the QR code,', 'miniorange-2-factor-authentication'); ?> <a  data-toggle="mo2f_collapse" href="#mo2f_scanqrcode" aria-expanded="false"  style="color:#3498DB;"><?php echo __('click here ', 'miniorange-2-factor-authentication'); ?></a></p></div>
            <div class="mo2f_collapse" id="mo2f_scanqrcode" style="margin-left:5px;">
                <?php echo __('Follow these instructions below and try again.', 'miniorange-2-factor-authentication'); ?>
                <ol>
                    <li><?php echo __('Make sure your desktop screen has enough brightness.', 'miniorange-2-factor-authentication'); ?></li>
                    <li><?php echo __('Open your app and click on Configure button to scan QR Code again.', 'miniorange-2-factor-authentication'); ?></li>
                    <li><?php echo __('If you get cross mark on QR Code then click on \'Refresh QR Code\' link.', 'miniorange-2-factor-authentication'); ?></li>
                </ol>
            </div>
            <table class="mo2f_settings_table">
                <a href="#mo2f_refreshQRCode" style="color:#3498DB;"><?php echo __('Click here to Refresh QR Code.', 'miniorange-2-factor-authentication'); ?></a>
                <div id="displayInlineQrCode" style="margin-left:36%;"><?php echo '<img style="width:200px;" src="data:image/jpg;base64,' . esc_html($data) . '" />'; ?>
                </div>
            </table>
            <center>
                <?php 
                if (sizeof($opt) > 1) { ?>
                    <input type="button" name="back" id="mo2f_inline_back_btn" class="miniorange_button" value="<?php echo __('Back', 'miniorange-2-factor-authentication'); ?>" />
                <?php }
                ?>
            </center>
            <script>
                jQuery('a[href="#mo2f_refreshQRCode"]').click(function(e) { 
                    jQuery('#mo2f_inline_configureapp_form').submit();
                });
                    jQuery("#mo2f_configurePhone").empty();
                    jQuery("#mo2f_app_div").hide();
                    var timeout;
                    pollInlineMobileRegistration();
                    function pollInlineMobileRegistration()
                    {
                        var transId = "<?php echo esc_html($mo2f_login_transaction_id);  ?>";
                        var jsonString = "{\"txId\":\""+ transId + "\"}";
                        var postUrl = "<?php echo esc_html($url);  ?>" + "/moas/api/auth/registration-status";
                        jQuery.ajax({
                            url: postUrl,
                            type : "POST",
                            dataType : "json",
                            data : jsonString,
                            contentType : "application/json; charset=utf-8",
                            success : function(result) {
                                var status = JSON.parse(JSON.stringify(result)).status;
                                if (status == 'SUCCESS') {
                                    var content = "<br/><div id='success'><img style='width:165px;margin-top:-1%;margin-left:2%;' src='" + "<?php echo plugins_url( 'includes/images/right.png' , dirname(dirname(__FILE__ )));?>" + "' /></div>";
                                    jQuery("#displayInlineQrCode").empty();
                                    jQuery("#displayInlineQrCode").append(content);
                                    setTimeout(function(){jQuery("#mo2f_inline_mobile_register_form").submit();}, 1000);
                                } else if (status == 'ERROR' || status == 'FAILED') {
                                    var content = "<br/><div id='error'><img style='width:165px;margin-top:-1%;margin-left:2%;' src='" + "<?php echo plugins_url( 'includes/images/wrong.png' , __FILE__ );?>" + "' /></div>";
                                    jQuery("#displayInlineQrCode").empty();
                                    jQuery("#displayInlineQrCode").append(content);
                                    jQuery("#messages").empty();
                                    jQuery("#messages").append("<div class='error mo2f_error_container'> <p class='mo2f_msgs'>An Error occured processing your request. Please try again to configure your phone.</p></div>");
                                } else {
                                    timeout = setTimeout(pollInlineMobileRegistration, 3000);
                                }
                            }
                        });
                    }   
            </script>
    <?php
    }

function initialize_inline_duo_auth_registration($current_user,$session_id_encrypt){
 
    $user_id = MO2f_Utility::mo2f_get_transient($session_id_encrypt, 'mo2f_current_user_id'); 
    update_user_meta($user_id,'current_user_email',$current_user->user_email);


   $opt=fetch_methods($current_user);  
        ?>
            <h3><?php echo mo2f_lt( 'Test Duo Authenticator' ); ?></h3>
    <hr>
    <div>
        <br>
        <br>
        <center>
            <h3><?php echo mo2f_lt( 'Duo push notification is sent to your mobile phone.' ); ?>
                <br>
                <?php echo mo2f_lt( 'We are waiting for your approval...' ); ?></h3>
            <img src="<?php echo plugins_url( 'includes/images/ajax-loader-login.gif', dirname(dirname(__FILE__)) ); ?>"/>
        </center>

        <input type="button" name="back" id="go_back" class="button button-primary button-large"
               value="<?php echo mo2f_lt( 'Back' ); ?>"
               style="margin-top:100px;margin-left:10px;"/>
    </div>

    <form name="f" method="post" action="" id="mo2f_go_back_form">
        <input type="hidden" name="option" value="mo2f_go_back"/>
        <input type="hidden" name="mo2f_go_back_nonce"
               value="<?php echo esc_html(wp_create_nonce( "mo2f-go-back-nonce" )) ?>"/>
    </form>
    <form name="f" method="post" id="mo2f_inline_duo_authenticator_success_form" action="">
        <input type="hidden" name="option" value="mo2f_inline_duo_authenticator_success_form"/>
        <input type="hidden" name="session_id" value="<?php echo esc_html($session_id_encrypt); ?>"/>
        <input type="hidden" name="mo2f_duo_authenticator_success_nonce"
               value="<?php echo esc_html(wp_create_nonce( "mo2f-duo-authenticator-success-nonce" )) ?>"/>
    </form>
    <form name="f" method="post" id="mo2f_duo_authenticator_error_form" action="">
        <input type="hidden" name="option" value="mo2f_inline_duo_authenticator_error"/>
        <input type="hidden" name="session_id" value="<?php echo esc_html($session_id_encrypt); ?>"/>
        <input type="hidden" name="mo2f_inline_duo_authentcator_error_nonce"
               value="<?php echo esc_html(wp_create_nonce( "mo2f-inline-duo-authenticator-error-nonce" )) ?>"/>
    </form>

    <script>
        jQuery('#go_back').click(function () {
            jQuery('#mo2f_go_back_form').submit();
        });
        jQuery("#mo2f_configurePhone").empty();
        jQuery("#mo2f_app_div").hide();
        var timeout;



            pollMobileValidation();
            function pollMobileValidation() {
                var ajax_url = "<?php echo esc_url(admin_url('admin-ajax.php')); ?>";
                var nonce = "<?php echo esc_html(wp_create_nonce( 'miniorange-2-factor-duo-nonce' )); ?>";
                var session_id_encrypt = "<?php echo esc_html($session_id_encrypt); ?>";

                var data={
                'action':'mo2f_duo_ajax_request',
                'call_type':'check_duo_push_auth_status',
                'session_id_encrypt': session_id_encrypt,
                'nonce': nonce,
               
            }; 

            jQuery.post(ajax_url, data, function(response){
                        
                        if (response == 'SUCCESS') {
                            jQuery('#mo2f_inline_duo_authenticator_success_form').submit();
                        } else if (response == 'ERROR' || response == 'FAILED' || response == 'DENIED') {

                            jQuery('#mo2f_duo_authenticator_error_form').submit();
                        } else {
                            timeout = setTimeout(pollMobileValidation, 3000);
                        }
                    
                });
            
            }

    </script>

    <?php
    }    
function prompt_user_for_kba_setup($current_user_id, $login_status, $login_message,$redirect_to,$session_id){
    $current_user = get_userdata($current_user_id);
    $opt=fetch_methods($current_user);

?>
    <html>
        <head>  <meta charset="utf-8"/>
            <meta http-equiv="X-UA-Compatible" content="IE=edge">
            <meta name="viewport" content="width=device-width, initial-scale=1">
            <?php
               mo2f_inline_css_and_js(); 
            ?>
            <style>
                .mo2f_kba_ques, .mo2f_table_textbox{
                    background: whitesmoke none repeat scroll 0% 0%;
                }
            </style>
        </head>
        <body>
            <div class="mo2f_modal" tabindex="-1" role="dialog" id="myModal5">
                <div class="mo2f-modal-backdrop"></div>
                <div class="mo2f_modal-dialog mo2f_modal-lg">
                    <div class="login mo_customer_validation-modal-content">
                        <div class="mo2f_modal-header">
                            <h4 class="mo2f_modal-title"><button type="button" class="mo2f_close" data-dismiss="modal" aria-label="Close" title="<?php echo __('Back to login','miniorange-2-factor-authentication');?>" onclick="mologinback();"><span aria-hidden="true">&times;</span></button>
                            <?php echo __('Setup Security Question (KBA)', 'miniorange-2-factor-authentication'); ?></h4>
                        </div>
                        <div class="mo2f_modal-body">
                            <?php if(isset($login_message) && !empty($login_message)) {   ?>
                                <div  id="otpMessage">
                                    <p class="mo2fa_display_message_frontend" style="text-align: left !important;"><?php echo esc_html($login_message); ?></p>
                                </div>
                            <?php } ?>
                            <form name="f" method="post" action="" >
                                <?php mo2f_configure_kba_questions(); ?>
                                <br />
                                <div class ="row">
                                    <div class="col-md-4" style="margin: 0 auto;width: 100px;">
                                        <input type="submit" name="validate" class="miniorange_button" style="width: 30%;background-color:#ff4168;" value="<?php echo __('Save', 'miniorange-2-factor-authentication'); ?>" />
                                        <button type="button" class="miniorange_button" style="width: 30%;background-color:#ff4168;" onclick="mobackinline();">Back</button>

                                    </div>
                                </div>
                                <input type="hidden" name="option" value="mo2f_inline_kba_option" />
                                <input type="hidden" name="mo2f_inline_save_kba_nonce" value="<?php echo esc_html(wp_create_nonce('miniorange-2-factor-inline-save-kba-nonce')); ?>" />
                                <input type="hidden" name="redirect_to" value="<?php echo esc_url($redirect_to); ?>"/>
                                <input type="hidden" name="session_id" value="<?php echo esc_html($session_id); ?>"/>
                            </form>
                            <?php if (sizeof($opt) > 1) { ?>
                                    <form name="f" method="post" action="" id="mo2f_goto_two_factor_form" class="mo2f_display_none_forms">
                                        <div class ="row">
                                            <div class="col-md-4" style="margin: 0 auto;width: 100px;">
                                            <input type="hidden" name="option" value="miniorange_back_inline"/>
                                            </div>
                                        </div>
                                        <input type="hidden" name="miniorange_inline_two_factor_setup" value="<?php echo esc_html(wp_create_nonce('miniorange-2-factor-inline-setup-nonce')); ?>" />
                                        <input type="hidden" name="redirect_to" value="<?php echo esc_url($redirect_to); ?>"/>
                                        <input type="hidden" name="session_id" value="<?php echo esc_html($session_id); ?>"/>
                                    </form>
                            <?php } ?>

                            <?php mo2f_customize_logo() ?>
                        </div>
                    </div>
                </div>
            </div>
            <form name="f" id="mo2f_backto_mo_loginform" method="post" action="<?php echo wp_login_url(); ?>" style="display:none;">
                <input type="hidden" name="miniorange_mobile_validation_failed_nonce" value="<?php echo esc_html(wp_create_nonce('miniorange-2-factor-mobile-validation-failed-nonce')); ?>" />
                <input type="hidden" name="redirect_to" value="<?php echo esc_url($redirect_to); ?>"/>
                <input type="hidden" name="session_id" value="<?php echo esc_html($session_id); ?>"/>
            </form>
        
        <script>
 function mologinback(){
                jQuery('#mo2f_backto_mo_loginform').submit();
            }            

	function mobackinline(){
                jQuery('#mo2f_goto_two_factor_form').submit();
            }
        </script>
        </body>
    </html>
<?php 
}function prompt_user_for_miniorange_register($current_user_id, $login_status, $login_message,$redirect_to,$session_id){
    $current_user = get_userdata($current_user_id);
    $opt=fetch_methods($current_user);
?>
    <html>
        <head>  <meta charset="utf-8"/>
            <meta http-equiv="X-UA-Compatible" content="IE=edge">
            <meta name="viewport" content="width=device-width, initial-scale=1">
            <?php
               mo2f_inline_css_and_js(); 
            ?>
            <style>
                .mo2f_kba_ques, .mo2f_table_textbox{
                    background: whitesmoke none repeat scroll 0% 0%;
                }
            </style>
        </head>
        <body>
            <div class="mo2f_modal" tabindex="-1" role="dialog" id="myModal5">
                <div class="mo2f-modal-backdrop"></div>
                <div class="mo2f_modal-dialog mo2f_modal-lg">
                    <div class="login mo_customer_validation-modal-content">
                        <div class="mo2f_modal-header">
                            <h3 class="mo2f_modal-title" style="color:black;"><button type="button" class="mo2f_close" data-dismiss="modal" aria-label="Close" title="<?php echo __('Back to login','miniorange-2-factor-authentication');?>" onclick="mologinback();"><span aria-hidden="true">&times;</span></button>
                           <b> <?php echo __('Connect with miniOrange', 'miniorange-2-factor-authentication'); ?></b></h3>
                        </div>
                        <div class="mo2f_modal-body">
                            <?php if(isset($login_message) && !empty($login_message)){ ?>
                                    <div  id="otpMessage">
                                        <p class="mo2fa_display_message_frontend" style="text-align: left !important;"  ><?php echo wp_kses($login_message, array('b'=>array())); ?></p>
                                    </div> 
                                <?php } ?>
                            <form name="mo2f_inline_register_form" id="mo2f_inline_register_form" method="post" action="">
                                <input type="hidden" name="option" value="miniorange_inline_register" />
                                <input type="hidden" name="redirect_to" value="<?php echo esc_url($redirect_to); ?>"/>
                                <input type="hidden" name="session_id" value="<?php echo esc_html($session_id); ?>"/>
                                <p>This method requires you to have an account with miniOrange.</p>
                                <table class="mo_wpns_settings_table">
                                    <tr>
                                    <td><b><font color="#FF0000">*</font>Email:</b></td>
                                    <td><input class="mo_wpns_table_textbox" type="email" name="email"
                                    required placeholder="person@example.com"/></td>
                                    </tr>
                                    <tr>
                                        <td><b><font color="#FF0000">*</font>Password:</b></td>
                                        <td><input class="mo_wpns_table_textbox" required type="password"
                                    name="password" placeholder="Choose your password (Min. length 6)" /></td>
                                    </tr>
                                    <tr>
                                        <td><b><font color="#FF0000">*</font>Confirm Password:</b></td>
                                        <td><input class="mo_wpns_table_textbox" required type="password"
                                    name="confirmPassword" placeholder="Confirm your password" /></td>
                                    </tr>
                                    <tr>
                                        <td>&nbsp;</td>
                                        <td><br><input type="submit" name="submit" value="Create Account" 
                                    class="miniorange_button" />
                                    <a href="#mo2f_account_exist">Already have an account?</a>
                                    </tr>
                                </table>
                            </form>
                <form name="f" id="mo2f_inline_login_form" method="post" action="" hidden>
                    <p><b>It seems you already have an account with miniOrange. Please enter your miniOrange email and password.<br></b><a target="_blank" href="https://login.xecurify.com/moas/idp/resetpassword"> Click here if you forgot your password?</a></p>
                    <input type="hidden" name="option" value="miniorange_inline_login"/>
                    <input type="hidden" name="redirect_to" value="<?php echo esc_url($redirect_to); ?>"/>
                    <input type="hidden" name="session_id" value="<?php echo esc_html($session_id); ?>"/>
                    <table class="mo_wpns_settings_table">
                        <tr>
                        <td><b><font color="#FF0000">*</font>Email:</b></td>
                        <td><input class="mo_wpns_table_textbox" type="email" name="email"
                        required placeholder="person@example.com"
                        /></td>
                        </tr>
                        <tr>
                        <td><b><font color="#FF0000">*</font>Password:</b></td>
                        <td><input class="mo_wpns_table_textbox" required type="password"
                        name="password" placeholder="Enter your miniOrange password" /></td>
                        </tr>
                        <tr>
                        <td>&nbsp;</td>
                        <td><input type="submit" class="miniorange_button" />
                            <input type="button" id="cancel_link" class="miniorange_button" value="<?php echo __('Go Back to Registration', 'miniorange-2-factor-authentication'); ?>" />
                        </tr>
                    </table>
                </form>
                            <br>
                    <input type="button" name="back" id="mo2f_inline_back_btn" class="miniorange_button" value="<?php echo __('<< Back to Menu', 'miniorange-2-factor-authentication'); ?>" />
                            <?php mo2f_customize_logo() ?>
                        </div>
                    </div>
                </div>
            </div>
            <form name="f" method="post" action="" id="mo2f_goto_two_factor_form" >              
                <input type="hidden" name="option" value="miniorange_back_inline"/>
                <input type="hidden" name="miniorange_inline_two_factor_setup" value="<?php echo esc_html(wp_create_nonce('miniorange-2-factor-inline-setup-nonce')); ?>" />
                <input type="hidden" name="redirect_to" value="<?php echo esc_url($redirect_to); ?>"/>
                <input type="hidden" name="session_id" value="<?php echo esc_html($session_id); ?>"/>
            </form>
            <form name="f" id="mo2f_backto_mo_loginform" method="post" action="<?php echo esc_url(wp_login_url()); ?>" style="display:none;">
                <input type="hidden" name="miniorange_mobile_validation_failed_nonce" value="<?php echo esc_html(wp_create_nonce('miniorange-2-factor-mobile-validation-failed-nonce')); ?>" />
                <input type="hidden" name="redirect_to" value="<?php echo esc_url($redirect_to); ?>"/>
                <input type="hidden" name="session_id" value="<?php echo esc_html($session_id); ?>"/>
            </form>
        
        <script>
            jQuery('#mo2f_inline_back_btn').click(function() {  
                    jQuery('#mo2f_goto_two_factor_form').submit();
            });
            jQuery('a[href=\"#mo2f_account_exist\"]').click(function (e) {
                    jQuery('#mo2f_inline_login_form').show();
                    jQuery('#mo2f_inline_register_form').hide();
            });
            jQuery('#cancel_link').click(function(){                               
                     jQuery('#mo2f_inline_register_form').show();
                    jQuery('#mo2f_inline_login_form').hide();
                });     
            function mologinback(){
                jQuery('#mo2f_backto_mo_loginform').submit();
            }
        </script>
        </body>
    </html>
<?php 
}
function prompt_user_for_setup_success($id, $login_status, $login_message,$redirect_to,$session_id){
    global $Mo2fdbQueries;
?>
    <html>
        <head>  <meta charset="utf-8"/>
            <meta http-equiv="X-UA-Compatible" content="IE=edge">
            <meta name="viewport" content="width=device-width, initial-scale=1">
            <?php
               mo2f_inline_css_and_js();
            ?>
            <style>
                .mo2f_kba_ques, .mo2f_table_textbox{
                    background: whitesmoke none repeat scroll 0% 0%;
                }
            </style>
        </head>
        <body>
            <div class="mo2f_modal" tabindex="-1" role="dialog" id="myModal5">
                <div class="mo2f-modal-backdrop"></div>
                <div class="mo2f_modal-dialog mo2f_modal-lg">
                    <div class="login mo_customer_validation-modal-content">
                        <div class="mo2f_modal-header">
                            <h4 class="mo2f_modal-title"><button type="button" class="mo2f_close" data-dismiss="modal" aria-label="Close" title="<?php echo __('Back to login', 'miniorange-2-factor-authentication'); ?>" onclick="mologinback();"><span aria-hidden="true">&times;</span></button>
                            <?php echo __('Two Factor Setup Complete', 'miniorange-2-factor-authentication'); ?></h4>
                        </div>
                        <div class="mo2f_modal-body center">
                            <?php
                            global $Mo2fdbQueries;
                                $mo2f_second_factor = $Mo2fdbQueries->get_user_detail( 'mo2f_configured_2FA_method',$id);
                                if($mo2f_second_factor == 'OUT OF BAND EMAIL'){
                                    $mo2f_second_factor = 'Email Verification';
                                }else if($mo2f_second_factor == 'SMS'){
                                    $mo2f_second_factor = 'OTP over SMS';
                                }else if($mo2f_second_factor == 'OTP_OVER_EMAIL'){
                                    $mo2f_second_factor = 'OTP_OVER_EMAIL';
                                }else if($mo2f_second_factor == 'PHONE VERIFICATION'){
                                    $mo2f_second_factor = 'Phone Call Verification';
                                }else if($mo2f_second_factor == 'SOFT TOKEN'){
                                    $mo2f_second_factor = 'Soft Token';
                                }else if($mo2f_second_factor == 'MOBILE AUTHENTICATION'){
                                    $mo2f_second_factor = 'QR Code Authentication';
                                }else if($mo2f_second_factor == 'PUSH NOTIFICATIONS'){
                                    $mo2f_second_factor = 'Push Notification';
                                }else if($mo2f_second_factor == 'GOOGLE AUTHENTICATOR'){
                                    if(get_user_meta($id,'mo2f_external_app_type',true) == 'GOOGLE AUTHENTICATOR'){
                                        $mo2f_second_factor = 'Google Authenticator';
                                    }else{
                                        $mo2f_second_factor = 'Authy 2-Factor Authentication';
                                    }   
                                }else if($mo2f_second_factor == 'KBA'){
                                    $mo2f_second_factor = 'Security Questions (KBA)';
                                }
                                $Mo2fdbQueries->get_user_detail( 'mo2f_configured_2FA_method',$id);
                                $status = $Mo2fdbQueries->get_user_detail( 'mo_2factor_user_registration_status',$id);

                        if(get_site_option( 'mo2f_disable_kba' )!=1){
                            if($status != 'MO_2_FACTOR_PLUGIN_SETTINGS'){
                            ?><div id="validation_msg" style="color:red;text-align:left !important;"></div>
                                <div id="mo2f_show_kba_reg" class="mo2f_inline_padding" style="text-align:left !important;" >
                                <?php if(isset($login_message) && !empty($login_message)){ ?>
                                    <div  id="otpMessage">
                                        <p class="mo2fa_display_message_frontend" style="text-align: left !important;"  ><?php echo wp_kses($login_message, array('b'=>array())); ?></p>
                                    </div> 
                                <?php } ?>
                                <h4> <?php echo __('Please set your security questions as an alternate login or backup method.', 'miniorange-2-factor-authentication'); ?></h4>
                                <form name="f" method="post" action="" >
                                    <?php mo2f_configure_kba_questions(); ?>
                                    <br>
                                    <center>
                                        <input type="submit" name="validate" class="miniorange_button" value="<?php echo __('Save', 'miniorange-2-factor-authentication'); ?>" /> 
                                    </center>
                                    <input type="hidden" name="mo2f_inline_kba_option" />
                                    <input type="hidden" name="mo2f_inline_save_kba_nonce" value="<?php echo esc_html(wp_create_nonce('miniorange-2-factor-inline-save-kba-nonce')); ?>" />
                                    <input type="hidden" name="mo2f_inline_kba_status" value="<?php echo esc_html($login_status); ?>" />
                                    <input type="hidden" name="redirect_to" value="<?php echo esc_url($redirect_to); ?>"/>
                                    <input type="hidden" name="session_id" value="<?php echo esc_html($session_id); ?>"/>
                                </form>
                                </div>
                            <?php }
                        }else{
                            $mo2fa_login_status = 'MO_2_FACTOR_SETUP_SUCCESS';
                            $Mo2fdbQueries->update_user_details( $id, array('mo_2factor_user_registration_status' =>'MO_2_FACTOR_PLUGIN_SETTINGS') );
                            $status = 'MO_2_FACTOR_PLUGIN_SETTINGS';
                        }
                    if($status == 'MO_2_FACTOR_PLUGIN_SETTINGS'){
                        if(get_site_option('mo2f_remember_device')!=1)
                        {
                            $pass2fa= new Miniorange_Password_2Factor_Login();
                            $pass2fa->mo2fa_pass2login(site_url(), $session_id);
                            ?>
                                <center>
                                <p style="font-size:17px;"><?php echo __('You have successfully set up ', 'miniorange-2-factor-authentication'); ?><b style="color:#28B463;"><?php echo $mo2f_second_factor; ?> </b><?php echo __('as your Two Factor method.', 'miniorange-2-factor-authentication'); ?><br><br>
                                <?php echo __('From now, when you login, you will be prompted for', 'miniorange-2-factor-authentication'); ?>  <span style="color:#28B463;"><?php echo __($mo2f_second_factor, 'miniorange-2-factor-authentication'); ?></span>  <?php echo __('as your 2nd factor method of authentication.', 'miniorange-2-factor-authentication'); ?>
                                </p>
                                </center>
                                <br>
                                <center>
                                <p style="font-size:16px;"><a href="#" onclick="mologinback();"style="color:#CB4335;"><b><?php echo __('Click Here', 'miniorange-2-factor-authentication'); ?></b></a> <?php echo __('to sign-in to your account.', 'miniorange-2-factor-authentication'); ?>
                                <br>
                                </center>
                            <?php 
                        }else{
                                $redirect_to = isset($_POST[ 'redirect_to' ]) ? sanitize_url($_POST[ 'redirect_to' ]) : null;
                                $mo_enable_rem = new Miniorange_Password_2Factor_Login();
                                mo2f_collect_device_attributes_handler($session_id,$redirect_to);
                        }
                    }
                            mo2f_customize_logo() ?>
                        </div>
                    </div>
                </div>
            </div>
            <form name="f" id="mo2f_backto_mo_loginform" method="post" action="<?php echo esc_url(wp_login_url()); ?>" style="display:none;">
                <input type="hidden" name="miniorange_mobile_validation_failed_nonce" value="<?php echo esc_html(wp_create_nonce('miniorange-2-factor-mobile-validation-failed-nonce')); ?>" />
                <input type="hidden" name="redirect_to" value="<?php echo esc_url($redirect_to); ?>"/>
                <input type="hidden" name="session_id" value="<?php echo esc_html($session_id); ?>"/>
            </form>
        
        <script>
            function mologinback(){
                jQuery('#mo2f_backto_mo_loginform').submit();
            }
        </script>
        </body>
    </html>
    <?php
    }

function prompt_user_for_phone_setup($current_user_id, $login_status, $login_message,$currentMethod,$redirect_to,$session_id){
$current_user = get_userdata($current_user_id); 
                            $opt=fetch_methods($current_user);  
    global $Mo2fdbQueries;
    $current_selected_method = $Mo2fdbQueries->get_user_detail( 'mo2f_configured_2FA_method',$current_user_id);
    $current_user = get_userdata($current_user_id);
    $email = $current_user->user_email;
?>
    <html>
        <head>  <meta charset="utf-8"/>
            <meta http-equiv="X-UA-Compatible" content="IE=edge">
            <meta name="viewport" content="width=device-width, initial-scale=1">
            <?php
                mo2f_inline_css_and_js();
                
                wp_register_script('mo2f_bootstrap_js', plugins_url('includes/js/bootstrap.min.js',dirname(dirname(__FILE__))));
                wp_register_script('mo2f_phone_js', plugins_url('includes/js/phone.js',dirname(dirname( __FILE__))));
                wp_print_scripts( 'mo2f_bootstrap_js' );
                wp_print_scripts( 'mo2f_phone_js');

                wp_register_style('mo2f_phone',plugins_url('includes/css/phone.css', dirname(dirname(__FILE__))));
                wp_print_styles( 'mo2f_phone' );
            ?>
        </head>
        <body>
            <div class="mo2f_modal" tabindex="-1" role="dialog" id="myModal5">
                <div class="mo2f-modal-backdrop"></div>
                <div class="mo_customer_validation-modal-dialog mo_customer_validation-modal-md" >
                    <div class="login mo_customer_validation-modal-content">
                        <div class="mo2f_modal-header">
                            <h4 class="mo2f_modal-title"><button type="button" class="mo2f_close" data-dismiss="modal" aria-label="Close" title="<?php echo __('Back to login','miniorange-2-factor-authentication');?>" onclick="mologinback();"><span aria-hidden="true">&times;</span></button>
                            <?php
                            if($current_selected_method == 'SMS AND EMAIL'){?>
                            <?php   echo __('Verify Your Phone and Email', 'miniorange-2-factor-authentication'); ?></h4>
                            <?php }
                            else if($current_selected_method == 'OTP Over Telegram')
                            {
                                echo __('Verify Your Telegram Details', 'miniorange-2-factor-authentication');   
                            }
                            else if($current_selected_method == 'OTP OVER EMAIL'){
                            ?>
                            <?php echo __('Verify Your EMAIL', 'miniorange-2-factor-authentication'); ?></h4>
                            <?php }
                            else{
                            ?>
                            <?php   echo __('Verify Your Phone', 'miniorange-2-factor-authentication'); ?></h3>
                            <?php } ?>
                        </div>
                        <div class="mo2f_modal-body">
                            <?php if(isset($login_message) && !empty($login_message)) {  ?>
                                <div  id="otpMessage" 
                                <?php if(get_user_meta($current_user_id, 'mo2f_is_error', true)) { ?>style="background-color:#FADBD8; color:#E74C3C;?>"<?php update_user_meta($current_user_id, 'mo2f_is_error', false);} ?>
                                >
                                    <p class="mo2fa_display_message_frontend" style="text-align: left !important; "> <?php echo wp_kses($login_message, array('b'=>array())); ?></p>
                                </div>
                                <?php if(isset($login_message)) {?> <br/> <?php } ?>
                            <?php } ?>
                            <div class="mo2f_row">
                                <form name="f" method="post" action="" id="mo2f_inline_verifyphone_form">
                                    <p>
                                    <?php
                                    if($current_selected_method == 'SMS AND EMAIL'){?>
                                    <?php echo __('Enter your phone number. An One Time Passcode(OTP) wll be sent to this number and your email address.', 'miniorange-2-factor-authentication'); ?></p>
                                    <?php 
                                    }else if($current_selected_method == 'OTP OVER EMAIL'){
                                        //no message
                                    }else if($current_selected_method == 'OTP Over Telegram')
                                    {
                                        echo __('1. Open the telegram app and search for miniorange2fa_bot. Click on start button or send <b>/start</b> message', 'miniorange-2-factor-authentication');
                                        echo "<br><br><br>";
                                        echo __('2. Enter the recieved Chat ID here below::', 'miniorange-2-factor-authentication');
                                        $chat_id = get_user_meta($current_user_id,'mo2f_chat_id',true);

                                        if($chat_id == '')
                                            $chat_id = get_user_meta($current_user_id,'mo2f_temp_chatID',true);

                                        ?>
                                         <input  type="text" name="verify_chatID" id="chatID"
                                        value="<?php echo esc_html($chat_id); ?>" pattern="[\+]?[0-9]{1,4}\s?[0-9]{7,12}" required="true" title="<?php echo __('Enter chat ID without any space or dashes', 'miniorange-2-factor-authentication'); ?>" /><br />

                                        <?php
                                        echo "<br>";
                                        
                                    }
                                    else{
                                    ?>
                                    <?php echo __('Enter your phone number', 'miniorange-2-factor-authentication'); ?></h4>
                                    <?php } 
                                    if(!($current_selected_method == 'OTP OVER EMAIL') and $current_selected_method !='OTP Over Telegram'and $current_selected_method !='OTP Over Whatsapp'){
                                    ?>  
                                    <input class="mo2f_table_textbox"  type="text" name="verify_phone" id="phone"
                                        value="<?php echo get_user_meta($current_user_id,'mo2f_user_phone',true); ?>" pattern="[\+]?[0-9]{1,4}\s?[0-9]{7,12}" required="true" title="<?php echo __('Enter phone number without any space or dashes', 'miniorange-2-factor-authentication'); ?>" /><br />
                                    <?php } ?>
                                    <?php
                                    $email = $Mo2fdbQueries->get_user_detail( 'mo2f_user_email',$current_user_id);
                                    if($current_selected_method == 'SMS AND EMAIL' ||$current_selected_method == 'OTP OVER EMAIL' ){?>
                                        <input class="mo2f_IR_phone"  type="text" name="verify_email" id="email"
                                        value="<?php echo esc_html($email) ; ?>"  title="<?php echo __('Enter your email', 'miniorange-2-factor-authentication'); ?>" style="width: 250px;" disabled /><br />
                                    <?php } ?>  
                                    <input type="submit" name="verify" class="miniorange_button" value="<?php echo __('Send OTP', 'miniorange-2-factor-authentication'); ?>" />
                                    <input type="hidden"  name="option" value="miniorange_inline_complete_otp_over_sms"/>
                                    <input type="hidden" name="miniorange_inline_verify_phone_nonce" value="<?php echo esc_html(wp_create_nonce('miniorange-2-factor-inline-verify-phone-nonce')); ?>" />
                                    <input type="hidden" name="redirect_to" value="<?php echo esc_url($redirect_to); ?>"/>
                                    <input type="hidden" name="session_id" value="<?php echo esc_html($session_id); ?>"/>
                                </form>
                            </div>  
                            <form name="f" method="post" action="" id="mo2f_inline_validateotp_form" >
                                <p>
                                <?php
                                    if($current_selected_method == 'SMS AND EMAIL'){?>
                                <h4><?php echo __('Enter One Time Passcode', 'miniorange-2-factor-authentication'); ?></h4>
                                    <?php }
                                    else{
                                    ?>
                                    <?php echo mo2f_lt('Please enter the One Time Passcode sent to your phone.');?></p>
                                <?php } ?>
                                <input class="mo2f_IR_phone_OTP"  required="true" pattern="[0-9]{4,8}" autofocus="true" type="text" name="otp_token" placeholder="<?php echo __('Enter the code', 'miniorange-2-factor-authentication'); ?>" id="otp_token"/><br>
                                <?php if ($current_selected_method == 'PHONE VERIFICATION'){ ?>
                                    <span style="color:#1F618D;"><?php echo mo2f_lt('Didn\'t get code?');?></span> &nbsp;
                                    <a href="#resendsmslink" style="color:#F4D03F ;font-weight:bold;"><?php echo __('CALL AGAIN', 'miniorange-2-factor-authentication'); ?></a>
                                <?php } else if($current_selected_method != 'OTP Over Telegram'){
                                    ?>
                                    <span style="color:#1F618D;"><?php echo mo2f_lt('Didn\'t get code?');?></span> &nbsp;
                                    <a href="#resendsmslink" style="color:#F4D03F ;font-weight:bold;"><?php echo __('RESEND IT', 'miniorange-2-factor-authentication'); ?></a>
                                <?php } ?>
                                <br /><br />
                                <input type="submit" name="validate" class="miniorange_button" value="<?php echo __('Verify Code', 'miniorange-2-factor-authentication'); ?>" />
                                <?php if (sizeof($opt) > 1) { ?>

                                    <input type="hidden" name="option" value="miniorange_back_inline"/>
                                    <input type="button" name="back" id="mo2f_inline_back_btn" class="miniorange_button" value="<?php echo __('Back', 'miniorange-2-factor-authentication'); ?>" />
                                <?php } ?>
                                <input type="hidden" name="redirect_to" value="<?php echo esc_url($redirect_to); ?>"/>
                                <input type="hidden" name="session_id" value="<?php echo esc_html($session_id); ?>"/>
                                <input type="hidden" name="option" value="miniorange_inline_complete_otp"/>
                                <input type="hidden" name="miniorange_inline_validate_otp_nonce" value="<?php echo esc_html(wp_create_nonce('miniorange-2-factor-inline-validate-otp-nonce')); ?>" />
                            </form>
                            <?php mo2f_customize_logo() ?>
                        </div>
                    </div>
                </div>
            </div>
            <form name="f" id="mo2f_backto_mo_loginform" method="post" action="<?php echo wp_login_url(); ?>" style="display:none;">
                <input type="hidden" name="miniorange_mobile_validation_failed_nonce" value="<?php echo esc_html(wp_create_nonce('miniorange-2-factor-mobile-validation-failed-nonce')); ?>" />
                <input type="hidden" name="redirect_to" value="<?php echo esc_url($redirect_to); ?>"/>
                <input type="hidden" name="session_id" value="<?php echo esc_html($session_id); ?>"/>
            </form>
            <form name="f" method="post" action="" id="mo2fa_inline_resend_otp_form" style="display:none;">
                <input type="hidden" name="miniorange_inline_resend_otp_nonce" value="<?php echo esc_html(wp_create_nonce('miniorange-2-factor-inline-resend-otp-nonce')); ?>" />
                <input type="hidden" name="redirect_to" value="<?php echo esc_url($redirect_to); ?>"/>
                <input type="hidden" name="session_id" value="<?php echo esc_html($session_id); ?>"/>
            </form>
            <?php if (sizeof($opt) > 1) { ?>
            <form name="f" method="post" action="" id="mo2f_goto_two_factor_form" >              
                <input type="hidden" name="option" value="miniorange_back_inline"/>
                <input type="hidden" name="miniorange_inline_two_factor_setup" value="<?php echo esc_html(wp_create_nonce('miniorange-2-factor-inline-setup-nonce')); ?>" />
                <input type="hidden" name="redirect_to" value="<?php echo esc_url($redirect_to); ?>"/>
                <input type="hidden" name="session_id" value="<?php echo esc_html($session_id); ?>"/>
            </form>
            <?php } ?>
        <script>
            jQuery("#phone").intlTelInput();
            function mologinback(){
                jQuery('#mo2f_backto_mo_loginform').submit();
            }
            jQuery('#mo2f_inline_back_btn').click(function() {  
                    jQuery('#mo2f_goto_two_factor_form').submit();
            });
            jQuery('a[href="#resendsmslink"]').click(function(e) {
                jQuery('#mo2fa_inline_resend_otp_form').submit();
            });
        </script>
        </body>
        
    </html>
<?php 
}