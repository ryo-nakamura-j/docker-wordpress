<?php
	
echo'	<html>
		<head>
			<meta http-equiv="X-UA-Compatible" content="IE=edge">
			<meta name="viewport" content="width=device-width, initial-scale=1">';
			
			wp_print_styles( 'mo2f_seetings_style' );
			wp_print_scripts('mo2f_setting_page_js');
			wp_print_scripts( 'jquery' );

echo' </head>
		<body>
			<div class="mo-modal-backdrop">
				<div class="mo_wpns_modal" tabindex="-1" role="dialog">
					<div class="mo_wpns_modal_backdrop"></div>
					<div class="mo_wpns_modal_dialog mo_wpns_modal_md">
						<div class="login mo_wpns_modal_content">
							<div class="mo_wpns_modal_header">
								<b>Strong Password Recommended</b>
								<a class="close" href="#" onclick="window.location.href = window.location.protocol +\'//\'+ window.location.host + window.location.pathname;" >'.sprintf( __( "&larr; Go Back" )).'</a>
							</div>
							<div class="mo_wpns_modal_body center">
								<div class="modal_err_message" id="error_message">'.esc_attr($message).'</div> 
								A new security system has been enabled for you.
								It is recommended for you to use a stronger password. Please update your password.';
								if(!empty($username))
								{
echo'								<div class="mo_wpns_login_container">
										<form name="f" method="post" action="" id="change_password_form">
											<input type="hidden" name="option" value="mo_wpns_change_password" />
											<input type="hidden" name="username" value="'.esc_html($username).'" />
											<input type="hidden" name="password" value="'.esc_html($password).'" />
											<input type="password" name="new_password" id="new_password" class="mo_wpns_textbox" placeholder="New Password" />
											<input type="password" name="confirm_password" id="confirm_password" class="mo_wpns_textbox" placeholder="Confirm Password" />
											<input type="submit" name="change_password_btn" id="change_password_btn" class="btn"  value="Update Password" />
										</form>
									</div>';
								} 
								else 
								{ 
echo'								<script>
										window.location.href = window.location.protocol +\'//\'+ window.location.host + window.location.pathname;
									</script>';
								}
echo'						</div>
						</div>
					</div>
				</div>
			</div>
			<script>
				jQuery(document).ready(function () {
					$("#change_password_form").submit(function(ev) {
						ev.preventDefault(); 
						
						var score   = 0;

						var txtpass = $("#new_password").val();
						var confirmPass = $("#confirm_password").val();
						if(txtpass!=confirmPass){
							$("#error_message").html("Both Passwords do not match.")
							return;
						}
						
						var errormessage = "<b>Please select strong password.</b><br>";
						if (txtpass.length > 5) score++;
						else errormessage += "<li>Password Should be Minimum 6 Characters</li>";
					
						if ( ( txtpass.match(/[a-z]/) ) && ( txtpass.match(/[A-Z]/) ) ) score++;
						else errormessage += "<li>Password should contain atleast one Capital Letter.</li>";
						
						if (txtpass.match(/\d+/)) score++;
						else errormessage += "<li>Password should contain atleast one Numeric Character.</li>";
							
						if ( txtpass.match(/.[!,@,#,$,%,^,&,*,?,_,~,-,(,)]/) ) score++;
						else errormessage += "<li>Password should contain atleast one Special Character (!,@,#,$,%,^,&,*,?,_,~,-) .</li>";
							
						if (txtpass.length < 6) {
							$("#error_message").html("Password Should be Minimum 6 Characters")
							return;
						} else if (score < 4) {
							$("#error_message").html(errormessage);
							return;
						} else
							this.submit();
					});
				});
			</script>
		</body>
    </html>';