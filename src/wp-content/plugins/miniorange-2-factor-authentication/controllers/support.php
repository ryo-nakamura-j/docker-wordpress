<?php

	global $mo2f_dirName;
	
	if(current_user_can( 'manage_options' )  && isset($_POST['option']))
	{


		switch(sanitize_text_field(wp_unslash($_POST['option'])))
		{
			case "mo_wpns_send_query":
				wpns_handle_support_form(sanitize_email($_POST['query_email']),sanitize_text_field($_POST['query'])
				,sanitize_text_field($_POST['query_phone']));		break;
		}
	}

	$current_user 	= wp_get_current_user();
	$email 			= get_option("mo2f_email");
	$phone 			= get_option("mo_wpns_admin_phone");

	
	if(empty($email))
		$email 		= $current_user->user_email;

	include $mo2f_dirName . 'views'.DIRECTORY_SEPARATOR.'support.php';


	/* SUPPORT FORM RELATED FUNCTIONS */

	//Function to handle support form submit
	function wpns_handle_support_form($email,$query,$phone)
	{

            $send_configuration = (isset($_POST['mo2f_send_configuration'])?$_POST['mo2f_send_configuration']:0);
            if(empty($email) || empty($query)){
			do_action('wpns_show_message',MoWpnsMessages::showMessage('SUPPORT_FORM_VALUES'),'ERROR');
			return;
        }
        
        $contact_us = new MocURL();

        if($send_configuration)       
            $query = $query.MoWpnsUtility::mo_2fa_send_configuration(true);
        else
            $query = $query.MoWpnsUtility::mo_2fa_send_configuration();

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                do_action('wpns_show_message',MoWpnsMessages::showMessage('SUPPORT_FORM_ERROR'),'ERROR');
            } else {
                $submited = json_decode($contact_us->submit_contact_us( $email, $phone, $query),true);
            }
                if(json_last_error() == JSON_ERROR_NONE && $submited){
                        do_action('wpns_show_message',MoWpnsMessages::showMessage('SUPPORT_FORM_SENT'),'SUCCESS');
                }else{
                        do_action('wpns_show_message',MoWpnsMessages::showMessage('SUPPORT_FORM_ERROR'),'ERROR');
                }
    }
