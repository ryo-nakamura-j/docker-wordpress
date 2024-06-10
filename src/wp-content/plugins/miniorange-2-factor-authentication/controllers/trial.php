<?php

	if(current_user_can( 'manage_options' )  && isset($_POST['option']) )
	{
		switch(sanitize_text_field($_POST['option']))
		{
			case "mo2f_trial_request_form":
				mo2f_handle_trial_request_form($_POST); break;
		}
	}
	global $mo2f_dirName;
	$current_user = wp_get_current_user();
	$email = isset($current_user->user_email)?$current_user->user_email:null;
	$url = get_site_url();
	$user_phone = $Mo2fdbQueries->get_user_detail( 'mo2f_user_phone', $current_user->ID );


	include $mo2f_dirName . 'views'.DIRECTORY_SEPARATOR.'trial.php';

	function mo2f_handle_trial_request_form($post){
		$nonce 	 	= isset($post['nonce'])?sanitize_text_field($post['nonce']):NULL;
		if ( ! wp_verify_nonce( $nonce, 'mo2f_trial-nonce' ) ){
	   			return;
        }

		$email   	= isset($post['mo2f_trial_email'])? sanitize_email($post['mo2f_trial_email']) : NULL;
		$phone   	= isset($post['mo2f_trial_phone'])? sanitize_text_field($post['mo2f_trial_phone']) : ( $user_phone ? $user_phone : NULL );
		$trial_plan  = isset($post['mo2f_trial_plan'])? sanitize_text_field($post['mo2f_trial_plan']): NULL;
		$authentication_method  = isset($post['mo2f_authentication_method'])? sanitize_text_field($post['mo2f_authentication_method']): NULL;
		$login_form_name  = isset($post['mo2f_trial_login_form'])? sanitize_text_field($post['mo2f_trial_login_form']): NULL;
		$theme_name  = isset($post['mo2f_trial_theme'])? sanitize_text_field($post['mo2f_trial_theme']): NULL;

		if($login_form_name == "Other"){
			$login_form_name = isset($post['mo2f_other_login_form'])? sanitize_text_field($post['mo2f_other_login_form']): NULL;
		}

		for($i = 1; $i <= 3; $i++) {
			if(isset( $post[ 'mo2f_number_of_users_' . $i ] ) && !empty($post[ 'mo2f_number_of_users_' . $i ]) || isset( $post[ 'mo2f_number_of_sites_' . $i ]) && !empty($post[ 'mo2f_number_of_sites_' . $i ])){
				
				$number_of_users = isset( $post[ 'mo2f_number_of_users_' . $i ] ) ? intval( $post[ 'mo2f_number_of_users_' . $i ] ) : NULL;
				$number_of_sites = isset( $post[ 'mo2f_number_of_sites_' . $i ] ) ? intval( $post[ 'mo2f_number_of_sites_' . $i ] ) : NULL;
				break;
			}
		}
  
		if(get_site_option('mo2f_trial_query_sent')){
            do_action('wpns_show_message',MoWpnsMessages::showMessage('TRIAL_REQUEST_ALREADY_SENT'),'ERROR');
            return;
        }

		if(empty($email) || empty($phone)   || empty($trial_plan) || empty($login_form_name) || empty($theme_name))
		{
			do_action('wpns_show_message',MoWpnsMessages::showMessage('REQUIRED_FIELDS'),'ERROR');
			return;
		}
		if(!preg_match("/^[\+][0-9]{1,4}\s?[0-9]{7,12}$/", $phone)){
		    do_action('wpns_show_message',MoWpnsMessages::showMessage('INVALID_PHONE'),'ERROR');
            return;
		}
		if(!is_null($number_of_users) && ($number_of_users <= 0 || !is_int($number_of_users)) || !is_null($number_of_sites) && ($number_of_sites <= 0 || !is_int($number_of_sites))) {
			do_action('wpns_show_message',MoWpnsMessages::showMessage('INVALID_INPUT'),'ERROR');
			return;
		}
		else{
			$email = filter_var( $email,FILTER_VALIDATE_EMAIL );
			$phone = preg_replace('/[^0-9]/', '', $phone);
			$trial_plan = sanitize_text_field($trial_plan);
			$query = 'REQUEST FOR TRIAL';
			$query .= ' [ Plan Name => ';
			$query .= $trial_plan;
			$query .= ' | Email => ';
			$query .= get_option('mo2f_email');
			$query .= ' | Users/Sites => ';
			$query .= ($number_of_users ?: 'NA') . '/' . ($number_of_sites  ?: 'NA');
			$query .= ' | Method => ' . $authentication_method;
			$query .= ' | Form/Theme=> ' . $login_form_name . '/' . $theme_name . ' ]';
			$current_user = wp_get_current_user();


            $url          = MoWpnsConstants::HOST_NAME . "/moas/rest/customer/contact-us";
            global $mowafutility;
            $query = '[WordPress 2 Factor Authentication Plugin: OV3 - '.MO2F_VERSION.']: ' . $query;

            $fields = array(
                        'firstName' => $current_user->user_firstname,
                        'lastName'  => $current_user->user_lastname,
                        'company'   => sanitize_text_field($_SERVER['SERVER_NAME']),
                        'email'     => $email,
                        'ccEmail'   => '2fasupport@xecurify.com',
                        'phone'     => $phone,
                        'query'     => $query
                    );
            $field_string = json_encode( $fields );

            $mo2fApi= new Mo2f_Api();
            $response = $mo2fApi->make_curl_call($url, $field_string);

			$submitted = $response;

			if(json_last_error() == JSON_ERROR_NONE && $submitted)
            {
                update_site_option('mo2f_trial_query_sent', true);
                do_action('wpns_show_message',MoWpnsMessages::showMessage('TRIAL_REQUEST_SENT'),'SUCCESS');
                return;
            }
            else{
                do_action('wpns_show_message',MoWpnsMessages::showMessage('SUPPORT_FORM_ERROR'),'ERROR');
            }

        }
	}

?>