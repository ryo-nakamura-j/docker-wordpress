<?php
class Mo2f_OnPremRedirect {

	function OnpremValidateRedirect($authType, $otpToken,$current_user =null){
		switch($authType){

			case "GOOGLE AUTHENTICATOR" :$content = $this->mo2f_google_authenticator_onpremise($otpToken,$current_user);
				return $content;
				break;
			case "KBA": $content = $this->mo2f_kba_onpremise();
				return $content;
				break;
			case "OUT OF BAND EMAIL":
				break;
			case "EMAIL":
			case "OTP OVER EMAIL":
			case "OTP_OVER_EMAIL":
				return $this->mo2f_otp_over_email($otpToken,$current_user);	
				

		}

	}

	function mo2f_kba_onpremise(){
		$session_id_encrypt = isset( $_POST['session_id'] ) ? sanitize_text_field($_POST['session_id']) : null;
		if(isset($_POST['validate'])){
			$user_id = wp_get_current_user()->ID;
		}
		else{
			$user_id = MO2f_Utility::mo2f_get_transient( $session_id_encrypt, 'mo2f_current_user_id' );
		}
			$redirect_to = isset( $_POST['redirect_to'] ) ? sanitize_url( $_POST['redirect_to']) : null;
			$kba_ans_1 = sanitize_text_field( $_POST['mo2f_answer_1'] );
			$kba_ans_2 = sanitize_text_field( $_POST['mo2f_answer_2'] );
			$questions_challenged = get_user_meta($user_id ,'kba_questions_user');
			$questions_challenged = $questions_challenged[0];	
			$all_ques_ans = (get_user_meta($user_id , 'mo2f_kba_challenge'));
			$all_ques_ans = $all_ques_ans[0];
			$ans_1 = $all_ques_ans[$questions_challenged[0]['question']];
			$ans_2 = $all_ques_ans[$questions_challenged[1]['question']];
			$check_trust_device = isset( $_POST['mo2f_trust_device'] ) ? sanitize_text_field($_POST['mo2f_trust_device']) : 'false';
			$mo2f_rba_status = MO2f_Utility::mo2f_retrieve_user_temp_values( 'mo2f_rba_status',$session_id_encrypt );

			$pass2fa = new Miniorange_Password_2Factor_Login;
			$twofa_Settings = new Miniorange_Authentication;
			if(!strcmp(md5($kba_ans_1),$ans_1 ) && !strcmp(md5($kba_ans_2), $ans_2) ){
					$arr = array('status' => 'SUCCESS','message'=>'Successfully validated.');
					$content = json_encode($arr);
					delete_user_meta( $user_id, 'test_2FA' );
					return $content; 
			}
			else {
					$arr = array('status' => 'FAILED','message'=>'TEST FAILED.');
					$content = json_encode($arr);
					return $content;		
		}

	}
			
	function OnpremSendRedirect($useremail,$authType,$currentuser){

		switch($authType){

			case "Email Verification":
			case "OUT OF BAND EMAIL":
			 $content = $this->mo2f_pass2login_push_email_onpremise($currentuser);
				return $content;
			case "EMAIL":
				
			case "OTP Over Email": $content = $this->OnpremOTPOverEMail($currentuser,$useremail);
				return $content;
			case "KBA": $content = $this->OnpremSecurityQuestions($currentuser);
						return $content;

		}

	}

	function OnpremSecurityQuestions($user){
		$question_answers = get_user_meta($user->ID , 'mo2f_kba_challenge');
		$challenge_questions = array_keys($question_answers[0]);
		$random_keys = array_rand($challenge_questions,2);
		$challenge_ques1 = array('question'=>$challenge_questions[$random_keys[0]]);
		$challenge_ques2 = array('question'=>$challenge_questions[$random_keys[1]]);
		$questions =  array($challenge_ques1,$challenge_ques2);
		update_user_meta( $user->ID, 'kba_questions_user', $questions );
		$response=json_encode(array('txId'=>rand(100,10000000),'status'=>'SUCCESS','message'=>'Please answer the following security questions.','questions'=>$questions));
		return $response;

	}

	function mo2f_google_authenticator_onpremise($otpToken,$current_user=null){
		include_once dirname(dirname( __FILE__ )) . DIRECTORY_SEPARATOR. 'handler'.DIRECTORY_SEPARATOR. 'twofa' . DIRECTORY_SEPARATOR . 'gaonprem.php';
		$gauth_obj= new Google_auth_onpremise();
		$session_id_encrypt = isset( $_POST['session_id'] ) ? sanitize_text_field($_POST['session_id']) : null;
		if(is_user_logged_in()){
			$user = wp_get_current_user();
			$user_id = $user->ID;
		}else if(isset($current_user) && !empty($current_user->ID)){
			$user_id = $current_user->ID;
		}else{
			$user_id = MO2f_Utility::mo2f_get_transient($session_id_encrypt, 'mo2f_current_user_id');
		}
		$secret= $gauth_obj->mo_GAuth_get_secret($user_id);
		$content=$gauth_obj->verifyCode($secret, $otpToken);
		return $content;
	}
	function OnpremOTPOverEMail($current_user,$useremail=false)
	{
		return $this->OnpremSendOTPEMail($current_user,'mo2f_otp_email_code','mo2f_otp_email_time',$useremail);
	}
	function OnpremSendOTPEMail($current_user,$tokenName,$timeName,$email=null)
	{
		$count_threshold = 5;
		global $Mo2fdbQueries,$imagePath;
		if(!isset($current_user) or is_null($current_user))
		{
			if(is_user_logged_in()){
				$current_user = wp_get_current_user();
			}else{
				$current_user = unserialize($_SESSION['mo2f_current_user']);
			}
		}

		if(is_null($email) or empty($email) or $email == '' or !isset($email) )
		{	
			$email = get_user_meta($current_user->ID,'tempEmail',true);
				
			if($email == '' or empty($email))
			{
				$email      = $Mo2fdbQueries->get_user_detail( 'mo2f_user_email', $current_user->ID );
			}

		}
		if(is_null($email) or empty($email) or $email == '' or !isset($email) )
			$email = $current_user->user_email;
		
		delete_user_meta($current_user->ID,'tempEmail');
		$subject 	= '2-Factor Authentication';
		$headers 	= array('Content-Type: text/html; charset=UTF-8');
		$otpToken 	= '';
		for($i=1;$i<7;$i++)
		{
			$otpToken 	.= rand(0,9);
		}
		update_user_meta($current_user->ID,$tokenName,$otpToken);
		update_user_meta($current_user->ID,$timeName,time());
		update_user_meta($current_user->ID,'tempRegEmail',$email);
		$message =  '<table cellpadding="25" style="margin:0px auto">
		<tbody>
		<tr>
		<td>
		<table cellpadding="24" width="584px" style="margin:0 auto;max-width:584px;background-color:#f6f4f4;border:1px solid #a8adad">
		<tbody>
		<tr>
		<td><img src="'.$imagePath.'includes/images/xecurify-logo.png" alt="Xecurify"  style="color:#5fb336;text-decoration:none;display:block;width:auto;height:auto;max-height:35px" class="CToWUd"></td>
		</tr>
		</tbody>
		</table>
		<table cellpadding="24" style="background:#fff;border:1px solid #a8adad;width:584px;border-top:none;color:#4d4b48;font-family:Arial,Helvetica,sans-serif;font-size:13px;line-height:18px">
		<tbody>
		<tr>
		<td>
		<p style="margin-top:0;margin-bottom:20px">Dear Customers,</p>
		<p style="margin-top:0;margin-bottom:10px">You initiated a transaction <b>WordPress 2 Factor Authentication Plugin</b>:</p>
		<p style="margin-top:0;margin-bottom:10px">Your one time passcode is '.$otpToken.'.
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
			if(get_site_option('cmVtYWluaW5nT1RQ') == $count_threshold)
				Miniorange_Authentication::low_otp_alert("email");
			update_site_option( 'mo2f_message', 'A OTP has been sent to you on' .'<b> ' . $email . '</b>. ' .  Mo2fConstants::langTranslate("ACCEPT_LINK_TO_VERIFY_EMAIL"));
			$arr = array('status' => 'SUCCESS','message'=>'Successfully validated.' ,'txId' => '' );
		
		}else{
			$arr = array('status' => 'FAILED','message'=>'TEST FAILED.');
			update_site_option( 'mo2f_message',  Mo2fConstants::langTranslate("ERROR_DURING_PROCESS_EMAIL"));
		}
		$content = json_encode($arr);
		return $content;
		
	}

	function mo2f_otp_over_email($otpToken,$current_user)
	{	
		return $this->mo2f_otp_email_verify($otpToken,$current_user,'mo2f_otp_email_code','mo2f_otp_email_time');	
	}
	function mo2f_otp_email_verify($otpToken,$current_user,$dtoken,$dtime)
	{
		global $Mo2fdbQueries;
		if(is_null($current_user))
		{
			$current_user = wp_get_current_user();
		}

		if(isset($otpToken) and !empty($otpToken) and !is_null($current_user))
		{
			$user_id 	 	= $current_user->ID;
			



			$valid_token 	= get_user_meta($user_id,$dtoken,true);


			$cd = get_user_meta($user_id,"mo2f_email_check_code",true);

			
			$time 		 	= get_user_meta($user_id,$dtime,true);
			$accepted_time	= time()-300;
			

			if($accepted_time>$time)
			{
				
				delete_user_meta( $user_id, $dtoken );
				delete_user_meta( $user_id, $dtime );
				delete_user_meta( $user_id,'tempRegEmail');
			
				$arr = array('status' => 'FAILED','message'=>'OTP Expire.');
			}	

			else if($valid_token == $otpToken)
			{
				$arr = array('status' => 'SUCCESS','message'=>'Successfully validated.');
				delete_user_meta( $user_id, $dtoken );
				if($dtoken == 'mo2f_email_check_code' or $dtoken == 'mo2f_otp_email_code')
				{
					$tempRegEmail = get_user_meta($user_id,'tempRegEmail',true);
					if($tempRegEmail != '' or !is_null($tempRegEmail) or !$tempRegEmail)
						$Mo2fdbQueries->update_user_details($user_id,array(
							'mo2f_configured_2FA_method' => 'OTP Over Email',
							'mo_2factor_user_registration_status' =>'MO_2_FACTOR_PLUGIN_SETTINGS',
							'mo2f_user_email'=> $tempRegEmail));
				}
				delete_user_meta( $user_id,'tempRegEmail');	
			}
			else
			{
				$arr = array('status' => 'FAILED','message'=>'TEST FAILED.');
			}
			
			$content = json_encode($arr);
			return $content;
	
		}		
	}


	function mo2f_pass2login_push_email_onpremise($current_user, $redirect_to=null)
	{
		
		global $Mo2fdbQueries;
		
		$email = get_user_meta($current_user->ID,'tempEmail',true);     
		
        
        if(empty($email))
        	$email = $Mo2fdbQueries->get_user_detail( 'mo2f_user_email', $current_user->ID );

		$subject 	= "2-Factor Authentication(Email verification)";
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
		$otpTokenH =  hash('sha512',$otpToken);
		$otpTokenDH = hash('sha512', $otpTokenD);
		update_user_meta($current_user->ID,'mo2f_EV_txid',$txid);	
		$userID = hash('sha512',$current_user->ID);
		update_site_option($userID,$otpTokenH);
		update_site_option($txid,3);
		$userIDd = $userID . 'D';
		update_site_option($userIDd,$otpTokenDH);

		$message = $this->getEmailTemplate($userID, $otpTokenH,$otpTokenDH,$txid,$email);
		$result = wp_mail($email,$subject,$message,$headers);

		$response=array("txId"=>$txid);
		$hidden_user_email = MO2f_Utility::mo2f_get_hidden_email( $email );
		if($result)
		{
			$response['status']='SUCCESS';
			$time = "time".$txid;
			$currentTimeInMillis = round(microtime(true) * 1000);
			update_site_option($time,$currentTimeInMillis);
		}
		else
		{
			$response['status']='FAILED';
		}

		return json_encode($response);
	}

	function getEmailTemplate($userID, $otpTokenH,$otpTokenDH,$txid,$email){
		global $imagePath;
		$url =  get_site_option('siteurl').'/wp-login.php?';
		$message =  '<table cellpadding="25" style="margin:0px auto">
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
		return $message;
	}
}
