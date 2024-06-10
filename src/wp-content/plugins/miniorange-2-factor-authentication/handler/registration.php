<?php


class RegistrationHandler
{
    function __construct()
    {
        add_filter( 'registration_errors' , array($this, 'mo_wpns_registration_validations' ), 10, 3 );
        if(get_site_option('mo2f_custom_form_name')=='#wordpress-register')
            add_action( 'register_form', array($this, 'mo2f_wp_verification' ) );
    }

    function mo2f_wp_verification()
    {
        global $mainDir;
        $submitSelector = '#wp-submit';
        $formName       = '#registerform';
        $emailField     = '#user_email';
        $authType       = get_site_option('mo2f_custom_auth_type');
        $phoneSelector =  get_site_option('mo2f_custom_phone_selector');
        if(get_site_option('mo2f_customerkey') > 0)
            $isRegistered =   get_site_option('mo2f_customerkey');
        else $isRegistered = 'false';
        $javaScript = 'includes/js/custom-form.js';
        wp_enqueue_style( 'mo2f_intl_tel_style',  $mainDir.'includes/css/phone.css',[],MO2F_VERSION);
        wp_enqueue_script( 'mo2f_intl_tel_script',$mainDir.'includes/js/phone.js',[],MO2F_VERSION);
        wp_register_script('mo2f_otpVerification',$mainDir.$javaScript,[],MO2F_VERSION);
        wp_localize_script('mo2f_otpVerification', 'otpverificationObj',
            array('siteURL'=> admin_url( 'admin-ajax.php'),
                'nonce'=>wp_create_nonce('ajax-nonce'),
                'authType'=>$authType,
                'submitSelector'=>$submitSelector,
                'formname'=>$formName,
                'emailselector'=>$emailField,
                'isRegistered' => $isRegistered,
                'phoneSelector' => $phoneSelector,
                'loaderUrl' 		=> plugin_dir_url(__FILE__).'includes/images/loader.gif',
                'isEnabledShortcode' => get_site_option('enable_form_shortcode')));
        wp_enqueue_script('mo2f_otpVerification');
    }

    function mo_wpns_registration_validations( $errors, $sanitized_user_login, $user_email )
    {

        global $moWpnsUtility;
			if(get_option('mo_wpns_activate_recaptcha_for_registration')){
				if(get_option('mo_wpns_recaptcha_version')=='reCAPTCHA_v3')
					$recaptchaError = $moWpnsUtility->verify_recaptcha_3(sanitize_text_field($_POST['g-recaptcha-response']));
			    else if(get_option('mo_wpns_recaptcha_version')=='reCAPTCHA_v2')
			    	$recaptchaError = $moWpnsUtility->verify_recaptcha(sanitize_text_field($_POST['g-recaptcha-response']));
				if(!empty($recaptchaError->errors))
				$errors = $recaptchaError;
			}
        if(get_site_option('mo_wpns_enable_fake_domain_blocking')){
            if($moWpnsUtility->check_if_valid_email($user_email) && empty($recaptchaError->errors))
                $errors->add( 'blocked_email_error', __( '<strong>ERROR</strong>: Your email address is not allowed to register. Please select different email address.') );
            else if(!empty($recaptchaError->errors))
                $errors = $recaptchaError;

        }
        else{
            $count= get_site_option('number_of_fake_reg');
            if($moWpnsUtility->check_if_valid_email($user_email) && empty($recaptchaError->errors))
            {
                $count = $count + 1;
                update_site_option('number_of_fake_reg' ,$count );
            }
        }
        return $errors;
    }

}
new RegistrationHandler;