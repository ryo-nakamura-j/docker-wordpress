<?php

include_once('two_fa_short_call.php');

class TwoFACustomRegFormShortcode

{
    public function __construct()
    {
        add_action('woocommerce_created_customer',array($this,'wc_post_registration'), 1, 3);
    }

    public function mo_enqueue_shortcode()
    {
        add_action("wp_ajax_mo_shortcode",array( $this, 'mo_shortcode' ));
        add_action("wp_ajax_nopriv_mo_shortcode",array($this,'mo_shortcode'));
        add_action("wp_ajax_mo_ajax_register",array( $this, 'mo_ajax_register' ));
        add_action("wp_ajax_nopriv_mo_ajax_register",array($this,'mo_ajax_register'));
    }

    public function mo_shortcode()
    {
        switch(sanitize_text_field($_POST['mo_action']))
        {
            case "challenge":
                $email = sanitize_text_field($_POST['email']);
                $phone = sanitize_text_field($_POST['phone']);
                $authTypeSend = sanitize_text_field($_POST['authTypeSend']);

                TwoFACustomRegFormAPI::challenge($phone,$email,$authTypeSend);
            break;

            case "validate":
                $otp = sanitize_text_field($_POST['otp']);
                $txId = sanitize_text_field($_POST['txId']);
                TwoFACustomRegFormAPI::validate($txId,$otp);
            break;
        }
    }

    public function mo_ajax_register(){
        switch (sanitize_text_field($_POST['mo_action'])) {
            case 'send_otp_over_email':
                $email = isset($_POST['email'])? sanitize_email($_POST['email']): "";
                $phone = isset($_POST['phone'])? sanitize_text_field($_POST['phone']): "";
               
                $authTypeSend = sanitize_text_field($_POST['authTypeSend']);
                TwoFACustomRegFormAPI :: challenge($phone,$email,$authTypeSend);
                # code...
                break;
            case 'send_otp_over_sms' :
                $email = isset($_POST['email'])? sanitize_email($_POST['email']): "";
                $phone = isset($_POST['phone'])? sanitize_text_field($_POST['phone']): "";
               
                $authTypeSend = sanitize_text_field($_POST['authTypeSend']);
                TwoFACustomRegFormAPI :: challenge($phone,$email,$authTypeSend);
                break;

            default:
                $otp = sanitize_text_field($_POST['otp']);
                $txId = sanitize_text_field($_POST['txId']);
                TwoFACustomRegFormAPI :: validate($txId,$otp);
                # code...
                break;
        }
    }

    function wc_post_registration( $user_id, $new_customer_data, $password_generated) {
        if ( isset( $_POST['phone'] ))
            update_user_meta($user_id, 'billing_phone', sanitize_text_field($_POST['phone']));
    }




}


