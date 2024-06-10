<?php
global $mo2f_dirName;

echo '

                <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
       <div class="mo2f_table_layout_support">		
            <div class="mo2f-support-form-flex">
				<div style="display flex;  flex-direction: column; align-items:center;justify-content:center;padding 5px;">
					<h2 style="margin-left:5px; font-weight:bold">Contact Us</h2>
					<p style="margin:7px;">Need any help? We are available 24/7, Just send us a query so we can help you.</p>
				</div>
			</div>
			
			<div class="mo2f-form-div">
			<form name="f" method="post" action="">
			<input type="hidden" name="option" value="mo_wpns_send_query"/>
			<table class="mo_wpns_settings_table">
				<tr><td>
					<input type="email" class="mo2f-support-input" id="query_email" name="query_email" value="'.esc_attr($email).'" placeholder="Enter your email" required />
					</td>
				</tr>
				<tr><td>
					<input type="phone" class="mo2f-support-input" name="query_phone" id="query_phone" value="'.esc_attr($phone).'" placeholder="Enter your phone"/>
					</td>
				</tr>
				<tr>
					<td>
						<textarea id="query" name="query" class="mo2f-support-input" style="resize: vertical;width:100%" cols="52" rows="4" placeholder="Write your query here"></textarea>
					</td>
				</tr>
			</table>
			<div id="mo_2fa_plugin_configuration">
			<input type="hidden" name="mo_2fa_plugin_configuration" value="mo2f_send_plugin_configuration"/>
						<input type="checkbox" id="mo2f_send_configuration"
							   name="mo2f_send_configuration" 
							   value="1" checked
						<h3>Send plugin Configuration</h3>
<br />
</div>
			<input type="submit" name="send_query" id="send_query" value="Submit Query" class="button button-primary button-large mo2f_send_query"/>
			<br>
			<a href="https://wordpress.org/support/plugin/miniorange-2-factor-authentication/" target="_blank" class="mo2f_raise_support_ticket button button-primary button-large" value="Raise a Support Ticket on WordPress"><i style="margin-left: -8px;" class="fab fa-wordpress"></i> Raise a Support Ticket on WordPress</a>

		</form>		
			</div>
		</div>
		';?>
	<div class='mo2f_firebase_adv_container'>
		<div class="mo2f_firebase_adv_img">
				<div><img src="<?php echo esc_url(dirname(plugin_dir_url(__FILE__)).'/includes/images/firebase.png') ?>" height="40px" width="40px"></div>
				<div style="margin-left:5px"><span class='mo2f_firebase_adv_text'><h1>Firebase</h1></span></div>
		</div>
		<div class="mo2f_firebase_adv_content">
			<b>Use Firebase as your custom SMS gateway with <a href="https://firebase.google.com/pricing"target="_blank">10K free transactions</a> to send One Time Passcodes (OTP) for 2 Factor Authentication</b>
		</div>
		<div >
			<a href="mailto:2fasupport@xecurify.com?subject = Feedback&body = Message"  target="_blank" class="mo2f_firebase_adv_button">Contact us</a>
		</div>
	<div>
		<script>
			function moSharingSizeValidate(e){
				var t=parseInt(e.value.trim());t>60?e.value=60:10>t&&(e.value=10)
			}
			function moSharingSpaceValidate(e){
				var t=parseInt(e.value.trim());t>50?e.value=50:0>t&&(e.value=0)
			}
			function moLoginSizeValidate(e){
				var t=parseInt(e.value.trim());t>60?e.value=60:20>t&&(e.value=20)
			}
			function moLoginSpaceValidate(e){
				var t=parseInt(e.value.trim());t>60?e.value=60:0>t&&(e.value=0)
			}
			function moLoginWidthValidate(e){
				var t=parseInt(e.value.trim());t>1000?e.value=1000:140>t&&(e.value=140)
			}
			function moLoginHeightValidate(e){
				var t=parseInt(e.value.trim());t>50?e.value=50:35>t&&(e.value=35)
			}
			jQuery("#mo2f-show-feedback-form").click(function(){
				jQuery(".mp2f-form-div").slideToggle(
					function(){
						if(jQuery("#mo2f-show-feedback-form").hasClass('dashicons-arrow-up-alt2'))
							jQuery("#mo2f-show-feedback-form").addClass('dashicons-arrow-down-alt2').removeClass('dashicons-arrow-up-alt2');
						else
							jQuery("#mo2f-show-feedback-form").addClass('dashicons-arrow-up-alt2').removeClass('dashicons-arrow-down-alt2');				
						}
				);
			});


		</script>