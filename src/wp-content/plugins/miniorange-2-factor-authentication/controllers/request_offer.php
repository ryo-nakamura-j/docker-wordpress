<?php 

			
	if(current_user_can( 'manage_options' )  && isset($_POST['option']) )
	{
		switch(sanitize_text_field(wp_unslash($_POST['option'])))
		{
			case "mo_2FA_offer_request_form":
				wpns_handle_offer_request_form($_POST); break;
		}
	}
	
	include $mo2f_dirName . 'views'.DIRECTORY_SEPARATOR.'request_offer.php';
	
	function wpns_handle_offer_request_form($post){
		$nonce 		= sanitize_text_field($post['nonce']);
		$usecase 	= isset($post['mo_2FA_offer_usecase'])? sanitize_text_field($post['mo_2FA_offer_usecase']): NULL;
		$email   	= isset($post['mo_2FA_offer_email'])? sanitize_text_field($post['mo_2FA_offer_email']) : NULL;
		if ( ! wp_verify_nonce( $nonce, 'mo2f-Request-offer' ) ){
	   			return;
	   		}
		if(empty($usecase) || empty($email)  )
		{
			do_action('wpns_show_message',MoWpnsMessages::showMessage('DEMO_FORM_ERROR'),'SUCCESS');
			return;
		}
		else{

			$query = 'REQUEST FOR SPECIAL OFFERS';
			$query .= ' =>';
			$query .= ' : ';
			$query .= $usecase;
			$contact_us = new MocURL();
			$submited = json_decode($contact_us->submit_contact_us($email, '', $query),true);

			if(json_last_error() == JSON_ERROR_NONE && $submited) 
				{
					do_action('wpns_show_message',MoWpnsMessages::showMessage('SUPPORT_FORM_SENT'),'SUCCESS');
					return;
				}
			else{			
				do_action('wpns_show_message',MoWpnsMessages::showMessage('SUPPORT_FORM_ERROR'),'ERROR');
				}
			}
	}
?>