<?php
$is_registered = empty(get_option('mo2f_customerkey'))?false:true;
$role = $user->roles;
$roles = ( array ) $user->roles;
$flag  = 0;
foreach ( $roles as $role ) {
	if(get_option('mo2fa_'.$role)=='1')
		$flag=1;
}
if(!current_user_can( 'administrator', $user->ID) || (!MO2F_IS_ONPREM && !$is_registered) || $flag==0)
	return;
else if(!MO2F_IS_ONPREM && !$is_registered)
	return;
$cloud_methods = array("miniOrange QR Code Authentication", "miniOrange Soft Token","miniOrange Push Notification","OTP Over SMS");
$id = get_current_user_id();
$available_methods =  MoWpnsUtility::get_mo2f_db_option('mo2f_is_NC', 'get_option')?get_site_option('mo2fa_free_plan_new_user_methods'):get_site_option('mo2fa_free_plan_existing_user_methods');
if(!$available_methods)
	return;
$transient_id             = MO2f_Utility::random_str(20);

MO2f_Utility::mo2f_set_transient($transient_id, 'mo2f_user_id', $user->ID);
$same_user = $user->ID == $id?true:false;
global $Mo2fdbQueries;
$current_method = $Mo2fdbQueries->get_user_detail('mo2f_configured_2FA_method',$user->ID);
if($current_method == "miniOrange QR Code Authentication" || $current_method == "miniOrange Soft Token" || $current_method == "miniOrange Push Notification")
	$current_method = "miniOrange Authenticator";
$twofactor_transactions = new Mo2fDB;
$exceeded = $twofactor_transactions->check_alluser_limit_exceeded($user->ID);
if($exceeded){
	return;
}
$user_column_exists = $Mo2fdbQueries->check_if_user_column_exists( $user->ID );
$email=$Mo2fdbQueries->get_user_detail( 'mo2f_user_email', $user->ID );
if($email == ''){
	$Mo2fdbQueries->update_user_details($user->ID,array('mo2f_user_email'=>$user->user_email));
}
$email = !empty($Mo2fdbQueries->get_user_detail( 'mo2f_user_email', $user->ID ))?$Mo2fdbQueries->get_user_detail( 'mo2f_user_email', $user->ID ):$user->user_email;
$pass_2fa_login_session = new Miniorange_Password_2Factor_Login();
if(!$user_column_exists){
	$Mo2fdbQueries->insert_user( $user->ID );
	$content = $pass_2fa_login_session->create_user_in_miniOrange($user->ID,$email,'SOFT TOKEN');
}
$registerMobile = new Two_Factor_Setup();
$content = $registerMobile->register_mobile($email);
update_user_meta($user->ID,'registered_mobile',$content);
$two_factor_methods_descriptions = array(
	"miniOrange QR Code Authentication" => "Scan the QR code from the account in your miniOrange Authenticator App to login.",
	"miniOrange Authenticator" 			=> "Scan the QR code from the account in your miniOrange Authenticator App to login.",
	"miniOrange Soft Token"             => "Enter the soft token from the account in your miniOrange Authenticator App to login.",
	"miniOrange Push Notification"      => "Accept a push notification in your miniOrange Authenticator App to login.",
	"Google Authenticator"              => "Enter the soft token from the account in your Google Authenticator App to login.",
	"Security Questions"                => "Answer the three security questions you had set, to login.",
	"OTP Over SMS"                      => "Enter the One Time Passcode sent to your phone to login.",
	"Authy Authenticator"               => "Enter the soft token from the account in your Authy Authenticator App to login.",
	"OTP Over Email"                    => "Enter the One Time Passcode sent to your email to login.",
	"Email Verification"                => "Accept the verification link sent to your email to login.",
	"OTP Over SMS and Email"            => "Enter the One Time Passcode sent to your phone and email to login.",
	"Hardware Token"                    => "Enter the One Time Passcode on your Hardware Token to login."
);
global $mainDir;
wp_enqueue_style( 'mo2f_user-profile_style',  $mainDir.'/includes/css/user-profile.css',[],MO2F_VERSION);
?>
<h3><?php esc_html_e( 'Set 2-Factor Authentication', 'miniorange 2-factor-authentication' ); ?></h3>
<table class="form-table" id="mo2fa_form-table-user-profile">
	<tr>
		<th style="text-align: left;">
			<?php echo mo2f_lt( '2-Factor Options' ); ?>
		</th>
		<td>
			<form name="f" method="post" action="" id="mo2f_update_2fa">
				<div class="mo2fa_tab">
					<?php foreach ( $two_factor_methods_descriptions as $method => $description ){ 
						if(in_array($method, $available_methods)){
							$trimmed_method = str_replace(' ','',$method);?>
							<button class="mo2fa_tablinks" type="button"
							<?php if((!empty($current_method) && MO2f_Utility::is_same_method($method,$current_method)) || (empty($current_method) && MO2f_Utility::is_same_method($method,'miniOrange Authenticator')) ){?>
								id="defaultOpen" 
							<?php }?>
							onclick='mo2fa_viewMethod(event, "<?php echo esc_attr( $trimmed_method );?>")'><?php echo esc_attr( $method );?>
						</button>
					<?php }}?>
				</div>
			</form>
			<?php foreach ( $two_factor_methods_descriptions as $method => $description ){ 
				if(in_array($method, $available_methods)){
					$trimmed_method = str_replace(' ','',$method);?>
					<div id="<?php echo esc_attr( $trimmed_method );?>" class="mo2fa_tabcontent">
						<p><?php echo esc_attr( $description );?></p>
						<p><?php methods_on_user_profile($method,$user,$transient_id);?></p>
					</div>
				<?php }}?>
			</td>
		</tr>
	</table>
	<div id="wpns_nav_message"></div>
	<input type="hidden" name="MO2F_IS_ONPREM" value="<?php echo esc_attr(MO2F_IS_ONPREM);?>">
	<input type="hidden" name="same_user" value="<?php echo esc_attr($same_user); ?>">
	<input type="hidden" name="is_registered" value="<?php echo esc_attr($is_registered); ?>">
	<input type="hidden" name="mo2f-update-mobile-nonce" value="<?php echo esc_html(wp_create_nonce("mo2f-update-mobile-nonce"));?>">
	<input type="hidden" name="mo2fa_count" id="mo2fa_count" value="1">
	<input type="hidden" name="transient_id" value="<?php echo esc_attr($transient_id) ;?>">
	<input type="hidden" name='method' id="method" value="NONE">
	<input type="hidden" name='mo2f_configuration_status' id="mo2f_configuration_status" value="Configuration">
	<?php
	wp_enqueue_script( 'user-profile-2fa-script', $mainDir.'/includes/js/user-profile-twofa.js',[],MO2F_VERSION);

	function methods_on_user_profile($method,$user,$transient_id){
		global $Mo2fdbQueries,$mainDir;
		$email=$Mo2fdbQueries->get_user_detail( 'mo2f_user_email', $user->ID );
		$pass_2fa_login_session = new Miniorange_Password_2Factor_Login();
		$trimmed_method = str_replace(' ','',$method);
		$is_registered = get_option('mo2f_customerkey');
		$id = get_current_user_id();
		if($email == ''){
			$Mo2fdbQueries->update_user_details($user->ID,array('mo2f_user_email'=>$user->user_email));
		}
		$email = !empty($Mo2fdbQueries->get_user_detail( 'mo2f_user_email', $user->ID ))?$Mo2fdbQueries->get_user_detail( 'mo2f_user_email', $user->ID ):$user->user_email;
		switch($method){
			case "miniOrange Authenticator":
			if(!$is_registered){
				$message = "Please register with miniOrange for using this method.";
				echo mo2f_lt( $message );
			}
			else{
				?>
				<div id="mo2fa_display_mo_methods">
					<h4 class="mo2fa_select_method">
						Select Authentication method :
					</h4>
					<input type="button" name="mo2f_method" id="miniOrangeSoftTokenButton" class="mo2f_miniAuthApp" value="Soft Token" />
					<input type="button" name="mo2f_method" id="miniOrangeQRCodeAuthenticationButton" class="mo2f_miniAuthApp" value="QR Code Authentication" />
					<input type="button" name="mo2f_method" id="miniOrangePushNotificationButton" class="mo2f_miniAuthApp" value="Push Notification" />
				</div>
				<?php
				if($id == $user->ID)
				{
					$content = get_user_meta($user->ID,'registered_mobile',true);
					$response = json_decode($content, true);
					$message = '';
					
					if(json_last_error() == JSON_ERROR_NONE) {
						if($response['status'] == 'ERROR'){
							$mo_qr_details['message'] = Mo2fConstants::langTranslate($response['message']);
							delete_user_meta( $user->ID, 'miniorageqr' );
						}else{
							if($response['status'] == 'IN_PROGRESS'){

								$mo_qr_details['message'] = '';
								$mo_qr_details['mo2f-login-qrCode']=$response['qrCode'];
								update_user_meta($user->ID,'miniorageqr',$mo_qr_details);
							}else{
								$mo_qr_details['message'] = __('An error occured while processing your request. Please Try again.','miniorange-2-factor-authentication');
								delete_user_meta( $user->ID, 'miniorageqr' );
							}
						}
					}
					?>
					
					<div class="mcol-2" id='mo2f_qrcode'>
						<table class="mo2f_settings_table">
							<br><?php 
							echo (isset($mo_qr_details['mo2f-login-qrCode'])?'<img style="width:165px;" src="data:image/jpg;base64,' .$mo_qr_details['mo2f-login-qrCode']. '" />':'Please register with miniOrange for using this method') ; 
							?>
						</table>
						<?php
						if(isset($mo_qr_details['mo2f-login-qrCode'])){
							?>
							<form name="f" method="post" action="" id="<?php echo 'mo2f_verify_form-'.mo2f_lt($trimmed_method); ?>">

								<table id="mo2f_setup_mo_methods">
									<td class="bg-none"><?php echo mo2f_lt( 'Enter Code:' )?></td> 
									<td><input type="tel" class="mo2f_table_textbox" style="margin-left: 1%; margin-right: 1%;  width:200px;" name="mo_qr_auth_code" id="<?php echo 'textbox-'.mo2f_lt($trimmed_method); ?>" value="" pattern="[0-9]{4,8}" title="<?php echo mo2f_lt('Enter OTP:'); ?>"/></td>
									<td><a id="<?php echo 'save-'.mo2f_lt($trimmed_method); ?>" name="save_qr" class="button button1" ><?php echo mo2f_lt( 'Verify and Save' ); ?></a></td>
								</table>
								
							</form>
						<?php } ?>
					</div>

					<?php
				}
				else{
					$message= "Link to reconfigure 2nd factor will be sent to ".$email;
					echo mo2f_lt( $message ); 
				}
			}
			break;
			case "Authy Authenticator":
			case "Google Authenticator":
			if($user->ID == $id){
				if(MO2F_IS_ONPREM){
					include_once dirname( __FILE__ ) . DIRECTORY_SEPARATOR.'twofa'. DIRECTORY_SEPARATOR. 'gaonprem.php';
					$gauth_obj= new Google_auth_onpremise();

					$ga_secret              = $gauth_obj->createSecret();
					$issuer                        = get_site_option( 'mo2f_GA_account_name', 'miniOrangeAu' );
					$url                           = $gauth_obj->geturl( $ga_secret, $issuer, $email );
					$mo2f_google_auth              = array('ga_qrCode'=> $url,'ga_secret'=>$ga_secret);
					update_user_meta( $user->ID, 'mo2f_google_auth', json_encode( $mo2f_google_auth ) );
					$otpcode = $gauth_obj->getCode($ga_secret);
					$data = isset($mo2f_google_auth) ? $mo2f_google_auth['ga_qrCode'] : null;
					wp_enqueue_script( 'mo_wpns_qrcode_script', $mainDir.'/includes/jquery-qrcode/jquery-qrcode.js',[],MO2F_VERSION );
					wp_enqueue_script( 'mo_wpns_min_qrcode_script', $mainDir.'/includes/jquery-qrcode/jquery-qrcode.min.js',[],MO2F_VERSION);
					echo '<div class="mo2f_gauth_column mo2f_gauth_left" >';
					echo '<div class="mo2f_gauth"  data-qrcode='.esc_html($data).'></div>';
					echo '</div>';
				}else{
					if(!get_user_meta($user->ID, 'mo2f_google_auth', true)){
						Miniorange_Authentication::mo2f_get_GA_parameters($user);
					}
					$mo2f_google_auth = get_user_meta($user->ID, 'mo2f_google_auth', true);
					$data = isset($mo2f_google_auth['ga_qrCode']) ? $mo2f_google_auth['ga_qrCode'] : null;
					$ga_secret = isset($mo2f_google_auth['ga_secret']) ? $mo2f_google_auth['ga_secret'] : null;
					echo '<br><div id="displayQrCode">
					<img id="mo2f_gauth" style="line-height: 0;background:white;" src="data:image/jpg;base64,' . esc_html($data) . '" />
					</div>';
				}
				?>
				
				<div class="mcol-2">
					<br>
					<form name="f" method="post" action="" id="<?php echo 'mo2f_verify_form-'.mo2f_lt($trimmed_method); ?>">

						<table id="mo2f_setup_ga">
							<td class="bg-none"><?php echo mo2f_lt( 'Enter Code:' )?></td> 
							<td><input type="tel" class="mo2f_table_textbox" style="margin-left: 1%; margin-right: 1%;  width:200px;" name="google_auth_code" id="<?php echo 'textbox-'.mo2f_lt($trimmed_method); ?>" value="" pattern="[0-9]{4,8}" title="<?php echo mo2f_lt('Enter OTP:'); ?>"/></td>
							<td><a id="<?php echo 'save-'.mo2f_lt($trimmed_method); ?>" name="save_GA" class="button button1" ><?php echo mo2f_lt( 'Verify and Save' ); ?></a></td>
						</table>

						<input type="hidden" name="ga_secret" value="<?php echo esc_html($ga_secret);?>">
					</form>

				</div>
				<?php
			}else{
				$message= "Link to reconfigure 2nd factor will be sent to ".$email;
				echo mo2f_lt( $message ); 
			}
			break;
			case "OTP Over SMS":
			if(!$is_registered){
				$message = "Please register with miniOrange for using this method.";
				echo mo2f_lt( $message ); 
			}
			else{
				$mo2f_user_phone = $Mo2fdbQueries->get_user_detail( 'mo2f_user_phone', $user->ID );
				$user_phone      = $mo2f_user_phone ? $mo2f_user_phone : get_option( 'user_phone_temp' );
				?>
				<form name="f" method="post" action="" id="<?php echo esc_html('mo2f_verify_form-'.mo2f_lt($trimmed_method)); ?>">

					<table id="mo2f_setup_sms">
						<td class="bg-none"><?php echo mo2f_lt( 'Authentication codes will be sent to ' )?></td> 
						<td><input type="text" class="mo2f_table_textbox" style="margin-left: 1%; margin-right: 1%;  width:200px;" name="verify_phone" id="<?php echo 'textbox-'.mo2f_lt($trimmed_method); ?>" value="<?php echo esc_html($user_phone) ?>" pattern="[\+]?[0-9]{1,4}\s?[0-9]{7,12}" required="true" title="<?php echo mo2f_lt( 'Enter phone number without any space or dashes' ); ?>"/></td>
						<td><a id="<?php echo 'save-'.mo2f_lt($trimmed_method); ?>" name="save" class="button button1" ><?php echo mo2f_lt( 'Save' ); ?></a></td>
					</table>
					
				</form>
				<?php
			}
			break;
			case "Security Questions":
			mo2f_configure_kba_questions($user);
			break;
			case "OTP Over Email":
			case "Email Verification":
			if(!$Mo2fdbQueries->check_if_user_column_exists($user->ID)){
				$content = $pass_2fa_login_session->create_user_in_miniOrange($user->ID,$email,$method);
			}
			$email = ($email=='')?$user->user_email:$email;
			$message = "Authentication codes will be sent to ".$email;
			echo mo2f_lt( $message ); 
			break;
			$Mo2fdbQueries->delete_user_login_sessions($user->ID);
		}
	}
?>