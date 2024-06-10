<?php
global $mo2f_dirName;
$setup_dirName = $mo2f_dirName.'views'.DIRECTORY_SEPARATOR.'twofa'.DIRECTORY_SEPARATOR.'link_tracer.php';
 include $setup_dirName;
 if(get_option("mo_wpns_recaptcha_version") == "reCAPTCHA_v3"){ 
 	$site_key = get_option("mo_wpns_recaptcha_site_key_v3"); 
 	$secret_key = get_option('mo_wpns_recaptcha_secret_key_v3');
}else { 
  	$site_key = get_option("mo_wpns_recaptcha_site_key"); 
  	$secret_key = get_option('mo_wpns_recaptcha_secret_key'); 
} 

add_action( 'admin_footer', 'login_security_ajax' );
echo '
		<div id="wpns_message" style=" padding-top:8px"></div>
		<div>
		<div class="mo_wpns_setting_layout" id ="mo2f_bruteforce">';


echo ' 		<h3>Brute Force Protection ( Login Protection )<a href='.esc_url($two_factor_premium_doc['Brute Force Protection']).' target="_blank"><span class="dashicons dashicons-text-page" style="font-size:23px;color:#413c69;float: right;"></span></a></h3>
			<div class="mo_wpns_subheading">This protects your site from attacks which tries to gain access / login to a site with random usernames and passwords.</div>
			
				<input id="mo_bf_button" type="checkbox" name="enable_brute_force_protection" '.esc_html($brute_force_enabled).'> Enable Brute force protection
			<br>';

			 
				
echo'			<form id="mo_wpns_enable_brute_force_form" method="post" action="">
					<input type="hidden" name="option" value="mo_wpns_brute_force_configuration">
					<table class="mo_wpns_settings_table">
						<tr>
							<td style="width:40%">Allowed login attempts before blocking an IP  : </td>
							<td><input class="mo_wpns_table_textbox" type="number" id="allwed_login_attempts" name="allwed_login_attempts" required placeholder="Enter no of login attempts" value="'.esc_html($allwed_login_attempts).'" /></td>
							<td></td>
						</tr>
						<tr>
							<td>Time period for which IP should be blocked  : </td>
							<td>
								<select id="time_of_blocking_type" name="time_of_blocking_type" style="width:100%;">
								  <option value="permanent" '.($time_of_blocking_type=="permanent" ? "selected" : "").'>Permanently</option>
								  <option value="months" '.($time_of_blocking_type=="months" ? "selected" : "").'>Months</option>
								  <option value="days" '.($time_of_blocking_type=="days" ? "selected" : "").'>Days</option>
								  <option value="hours" '.($time_of_blocking_type=="hours" ? "selected" : "").'>Hours</option>
								</select>
							</td>
							<td><input class="mo_wpns_table_textbox '.($time_of_blocking_type=="permanent" ? "hidden" : "").' type="number" id="time_of_blocking_val" name="time_of_blocking_val" value="'.esc_html($time_of_blocking_val).'" placeholder="How many?" /></td>
						</tr>
						<tr>
							<td>Show remaining login attempts to user : </td>
							<td><input  type="checkbox"  id="rem_attempt" name="show_remaining_attempts" '.esc_html($remaining_attempts).' ></td>
							<td></td>
						</tr>
						<tr>
							<td></td>
							<td><br>
							<input type="hidden" id="brute_nonce" value ="'. esc_html(wp_create_nonce("wpns-brute-force")).'" />
							<input type="button" style="width:100px;" value="Save" class="button button-primary button-large" id="mo_bf_save_button">
							</td>
							<td></td>
						</tr>
					</table>
				</form>';
			
		
echo'	
       </div>
		<div class="mo_wpns_setting_layout" id="mo2f_google_recaptcha">		
			<h3>Google reCAPTCHA<a href='.esc_url($two_factor_premium_doc['Google reCAPTCHA']).' target="_blank"><span class="dashicons dashicons-text-page" style="font-size:23px;color:#413c69;float: right;"></span></a></h3>
			<div class="mo_wpns_subheading">Google reCAPTCHA protects your website from spam and abuse. reCAPTCHA uses an advanced risk analysis engine and adaptive CAPTCHAs to keep automated software from engaging in abusive activities on your site. It does this while letting your valid users pass through with ease.</div>

			<form id="mo_wpns_activate_recaptcha" method="post" action="">
				<input type="hidden" name="option" value="mo_wpns_activate_recaptcha">

			</form>';
			
echo'			<form id="mo_wpns_recaptcha_settings" method="post" action="">
                    <div style="padding: 5px;">
                        <input id="enable_captcha" type="checkbox" name="enable_captcha" '.esc_html($google_recaptcha).'>
                         Enable reCAPTCHA</div>
                    <p>Select your preferred version of the reCAPTCHA:</p>
                    <div style="padding: 5px;">
                        <input type="radio" name="gcaptchatype" value="reCAPTCHA_v3"/>version 3</div>
                    <div style="padding: 5px;">
                        <input type="radio" name="gcaptchatype" value="reCAPTCHA_v2"/>version 2</div>
                        ';
				
                
echo'           <p>Before you can use reCAPTCHA, you need to register your domain/website 
                <a href="'.esc_url($captcha_url_v2).'"  target="blank" title="guide">here</a>.</p><br>
                <p>Enter Site key and Secret key that you get after registration.</p>
				
					
					<table class="mo_wpns_settings_table">
						<tr>
							<td style="width:30%">Site key  : </td>
							<td style="width:30%"><input id="captcha_site_key" class="mo_wpns_table_textbox" type="text" name="mo_wpns_recaptcha_site_key" required placeholder="site key" 
							  value ="'.esc_html($site_key).'"/></td>
							<td style="width:20%"></td>
						</tr>
						<tr>
							<td>Secret key  : </td>
							<td><input id="captcha_secret_key" class="mo_wpns_table_textbox" type="text" name="mo_wpns_recaptcha_secret_key" required placeholder="secret key"  value ="'.esc_html($secret_key).'"/></td>
						</tr>
						<tr>
							<td style="vertical-align:top;">Enable reCAPTCHA for :</td>
							<td><input id="login_captcha" type="checkbox" name="mo_wpns_activate_recaptcha_for_login" '.esc_html($captcha_login).'> Login form
							<input id="reg_captcha" style="margin-left:10px" type="checkbox" name="mo_wpns_activate_recaptcha_for_registration" '.esc_html($captcha_reg).' > Registration form</td>
						</tr>
					</table><br/>
					<input type="hidden" id="captcha_nonce" value = "'.esc_html(wp_create_nonce("wpns-captcha")).'">
					<input id="captcha_button" type="button" value="Save Settings" class="button button-primary button-large" />
					<input type="button" value="Test reCAPTCHA Configuration" onclick="testcaptchaConfiguration()" class="button button-primary button-large" />
					
				</form> </div>';?>
			 <script>
                var recaptcha_version ="<?php echo esc_html(get_option('mo_wpns_recaptcha_version'));?>";
                if(recaptcha_version=='reCAPTCHA_v3')
                    jQuery('input:radio[name="gcaptchatype"]').filter('[value="reCAPTCHA_v3"]').attr('checked', true);
                else if(recaptcha_version=='reCAPTCHA_v2')
  	                jQuery('input:radio[name="gcaptchatype"]').filter('[value="reCAPTCHA_v2"]').attr('checked', true);
  	            jQuery('input:radio[name="gcaptchatype"]').change(function(){
  	            	var captcha_version=jQuery("input[name='gcaptchatype']:checked").val();
  	            	
  	            	if(captcha_version=='reCAPTCHA_v3'){
  	            	 jQuery("#captcha_site_key").val("<?php echo esc_html(get_option('mo_wpns_recaptcha_site_key_v3')); ?>");
  	            	 jQuery("#captcha_secret_key").val("<?php echo esc_html(get_option('mo_wpns_recaptcha_secret_key_v3')); ?>");
  	            	}
  	            	else if(captcha_version=='reCAPTCHA_v2') {
  	            		
                       jQuery("#captcha_site_key").val("<?php echo esc_html(get_option('mo_wpns_recaptcha_site_key')); ?>");
                       jQuery("#captcha_secret_key").val("<?php echo esc_html(get_option('mo_wpns_recaptcha_secret_key')); ?>");
  	            	}
  	            })
             </script> 

             
			<?php	

echo		'<br>
		</div>
		
		<div class="mo_wpns_setting_layout" id="mo2f_enforce_strong_password_div">		
			<h3>Enforce Strong Passwords <a href='.esc_url($two_factor_premium_doc['Enforce Strong Passwords']).' target="_blank"><span class="dashicons dashicons-text-page" style="font-size:23px;color:#413c69;float: right;"></span></a></h3>
            <span style="color:red">To enforce strong password you need to have miniOrange Password Policy Manager plugin installed.</span><br>
            <div class="mo2fa-ppm-ad">
                <div class="mo2fa-ppm-logo"></div>
                <div>
                    <div style="padding:1% 0%; margin: 0%;width: 150px;"><h2 style="margin: 1%; width: max-content;"><a href="' . esc_url( network_admin_url('plugin-install.php?tab=plugin-information&plugin=password-policy-manager&TB_embed=true&width=660&height=550' ) ) . '" class="thickbox" title="More info about miniOrange\'s Password Policy Manager Plugin">Password Policy Manager | Password Manager</a></h2>
                        <div class="wporg-ratings" aria-label="5 out of 5 stars" data-title-template="5 out of 5 stars" data-rating="5" style="color:#ffb900;"><span class="dashicons dashicons-star-filled"></span><span class="dashicons dashicons-star-filled"></span><span class="dashicons dashicons-star-filled"></span><span class="dashicons dashicons-star-filled"></span><span class="dashicons dashicons-star-filled"></span>
                        <span><a href="https://wordpress.org/support/plugin/password-policy-manager/reviews" target="_blank">(3)</a></span></div>
                        </div><p style="margin: 0%; font-size: 110%;">Provides a secure way of handling the strong password and password security for all roles like administrator, subscriber, author and custom roles.</p>

                    </div>
                </div>
            </div>
		</div>';

		?>
		<?php
	echo'
      <script>

		function testcaptchaConfiguration(){
			    var gradioVal = jQuery("input[name=gcaptchatype]:checked").val();
                if(gradioVal=="reCAPTCHA_v3"){
			    var myWindow = window.open("'.esc_html($test_recaptcha_url_v3).'", "Test Google reCAPTCHA_v3 Configuration", "width=600, height=600");}
			    else if(gradioVal=="reCAPTCHA_v2"){
			    var myWindow = window.open("'.esc_url($test_recaptcha_url).'", "Test Google reCAPTCHA_v2 Configuration", "width=600, height=600");}
        }   			
		
	    </script>';


	    ?>
	    <?php		

			
echo'		<br>
		</div>
	</div>';?>
	
	<script>
		jQuery(document).ready(function(){
			$("#time_of_blocking_type").change(function() {
				if($(this).val()=="permanent")
					$("#time_of_blocking_val").addClass("hidden");
				else
					$("#time_of_blocking_val").removeClass("hidden");	
			});
		});	

		function mo_enable_disable_bf(){
			jQuery.ajax({
				type : "POST",
				data : {
					option: "mo_wpns_enable_brute_force",
					status: "'.$brute_force_enabled.'",
				},
				success: function(data){
				}  
			 });
		}
		</script><?php

		function login_security_ajax(){
			if ( ('admin.php' != basename( $_SERVER['PHP_SELF'] )) || (sanitize_text_field($_GET['page']) != 'mo_2fa_login_and_spam') ) {
				return;
            }
		?>
				<script>
					jQuery(document).ready(function(){
						jQuery("#mo_bf_save_button").click(function(){
						var data =  {
					'action'				  : 'wpns_login_security',
					'wpns_loginsecurity_ajax' : 'wpns_bruteforce_form', 
					'bf_enabled/disabled'     : jQuery("#mo_bf_button").is(":checked"),
					'allwed_login_attempts'   : jQuery("#allwed_login_attempts").val(),
					'time_of_blocking_type'   : jQuery("#time_of_blocking_type").val(),
					'time_of_blocking_val'    : jQuery("#time_of_blocking_val").val(),
					'show_remaining_attempts' : jQuery("#rem_attempt").is(':checked'),
					'nonce' 				  : jQuery("#brute_nonce").val(),	
				};
				jQuery.post(ajaxurl, data, function(response) {
				
				jQuery("#wpns_message").empty();
				jQuery("#wpns_message").hide();
				jQuery('#wpns_message').show();
				if (response == "empty"){
				jQuery('#wpns_message').append("<div id='notice_div' class='overlay_error'><div class='popup_text'>&nbsp; &nbsp; Please fill out all the fields</div></div>");
				window.onload = nav_popup();				
			}
				else if(response == "true"){
					jQuery('#wpns_message').append("<div id='notice_div' class='overlay_success'><div class='popup_text'>&nbsp; &nbsp;Brute force is enabled and configuration has been saved</div></div>");
					window.onload = nav_popup();				
				}
				else if(response == "false"){
					jQuery('#wpns_message').append("<div id='notice_div' class='overlay_error'><div class='popup_text'>&nbsp; &nbsp; Brute force is disabled</div></div>");
							    window.onload = nav_popup();				}
				else if(response == "ERROR" ){ 
				jQuery('#wpns_message').append("<div id='notice_div' class='overlay_error'><div class='popup_text'>&nbsp; &nbsp; ERROR</div></div>");
				window.onload = nav_popup();				
				}
				});
					});

					
			});
jQuery(document).ready(function(){
						jQuery("#captcha_button").click(function(){
							var data = {
					'action'                 :'wpns_login_security',  
					'wpns_loginsecurity_ajax':'wpns_save_captcha',
					'site_key'  			 : jQuery("#captcha_site_key").val(),
					'secret_key'			 : jQuery("#captcha_secret_key").val(), 
					'version'                : jQuery("input[name='gcaptchatype']:checked").val(),
					'enable_captcha'		 : jQuery("#enable_captcha").is(':checked'),
					'login_form'			 : jQuery("#login_captcha").is(':checked'),
					'registeration_form'	 : jQuery("#reg_captcha").is(':checked'),
					'nonce'		           	 : jQuery("#captcha_nonce").val(),
				}
				jQuery.post(ajaxurl, data, function(response) {
					
				jQuery("#wpns_message").empty();
				jQuery("#wpns_message").hide();
				jQuery('#wpns_message').show();
				if (response == "empty"){
				jQuery('#wpns_message').append("<div id='notice_div' class='overlay_error'><div class='popup_text'>&nbsp; &nbsp; Please fill out all the fields</div></div>");
				window.onload = nav_popup();				}
				if (response == "version_select"){
				jQuery('#wpns_message').append("<div id='notice_div' class='overlay_error'><div class='popup_text'>&nbsp; &nbsp; Please select a version for the reCAPTCHA</div></div>");
				window.onload = nav_popup();				}
				else if(response == "true"){
					jQuery('#loginURL').empty();
					jQuery('#loginURL').hide();
					jQuery('#loginURL').show();
					jQuery('#loginURL').append(data.input_url);
					jQuery('#wpns_message').append("<div id='notice_div' class='overlay_success'><div class='popup_text'>&nbsp; &nbsp; CAPTCHA is enabled.</div></div>");
					window.onload = nav_popup();					}
				else if(response == "false"){
					if(!jQuery("input[name='gcaptchatype']:checked").val())
					{
						jQuery('#loginURL').empty();
						jQuery('#loginURL').hide();
						jQuery('#loginURL').show();
						jQuery('#loginURL').append('wp-login.php');
						jQuery('#wpns_message').append("<div id='notice_div' class='overlay_error'><div class='popup_text'>&nbsp; &nbsp; Select a version.</div></div>");
						window.onload = nav_popup();
					}
					else{

					jQuery('#loginURL').empty();
					jQuery('#loginURL').hide();
					jQuery('#loginURL').show();
					jQuery('#loginURL').append('wp-login.php');
					jQuery('#wpns_message').append("<div id='notice_div' class='overlay_error'><div class='popup_text'>&nbsp; &nbsp; CAPTCHA is disabled.</div></div>");
					window.onload = nav_popup();}				}
				else if(response == "ERROR" ){ 
				jQuery('#wpns_message').append("<div id='notice_div' class='overlay_error'><div class='popup_text'>&nbsp; &nbsp; ERROR</div></div>");
				window.onload = nav_popup();				
				}
				});
						});
					});
					jQuery(document).ready(function(){
						jQuery("#strong_password").click(function(){
							var data = {
					'action'                 :'wpns_login_security',  
					'wpns_loginsecurity_ajax':'save_strong_password',
					'enable_strong_pass'	 :jQuery("#strong_password_check").is(':checked'),
					'accounts_strong_pass'	 :jQuery("#mo2f_enforce_strong_passswords_for_accounts").val(),
					'nonce'					 :jQuery("#str_pass").val(), 
				}
				jQuery.post(ajaxurl, data, function(response) {
				if(response == "true"){
                    success_msg("Strong password is enabled.");
				}else if(response == "false") {
                    error_msg("Strong Password is disabled.");
				}else if(response == "ERROR" ){
                    error_msg("There was an error in procession your request");
				}
				});
						});
					});

				</script>


			<?php }