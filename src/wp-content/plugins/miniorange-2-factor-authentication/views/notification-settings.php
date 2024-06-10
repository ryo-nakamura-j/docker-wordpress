<?php 

echo'<div class="mo_wpns_divided_layout">	
		<div class="mo_wpns_setting_layout">';

$email= get_option("admin_email_address_status")?get_option("admin_email_address"):'';
$dash_url    =MoWpnsUtility::get_mo2f_db_option('mo_wpns_2fa_with_network_security', 'get_option')? esc_url($dashboard_url) :$two_fa  ;
echo'		

			<h3>Email Notifications<span style="float:right"><a class="button button-primary button-large" href="'. esc_url($dash_url).'">Back</a></span></h3>
             <p>If you want to get notification over email, Please enter email address below!</p>
             <form id="mo_wpns_get_manual_email" method="post" action="">
              <input type="hidden" name="option" value="mo_wpns_get_manual_email">
              Enter your E-mail :<input type= "email" name="admin_email_address" placeholder="miniorange@gmail.com" value="'.esc_html($email).'">
              <input type="submit" name="submit" style="width:100px" value="Save" class="button button-primary button-large"/>
             </form>
             <br>
			<form id="mo_wpns_enable_ip_blocked_email_to_admin" method="post" action="">
				<input type="hidden" name="option" value="mo_wpns_enable_ip_blocked_email_to_admin">
				<input type="checkbox" name="enable_ip_blocked_email_to_admin" '.esc_html($notify_admin_on_ip_block).' onchange="document.getElementById(\'mo_wpns_enable_ip_blocked_email_to_admin\').submit();"'; if(!get_option("admin_email_address_status")|| get_option("admin_email_address") ==''){echo "disabled";} 
			echo '>Notify Administrator if IP address is blocked.
				<a style="cursor:pointer" id="custom_admin_template_expand">Customize Email Template</a>
			</form>
			<form id="custom_admin_template_form" method="post" class="hidden">
				<input type="hidden" name="option" value="custom_admin_template">
				<br><br>';

				wp_editor($template1, $template_type1, $ip_blocking_template); 
				submit_button( 'Save Template' );

echo'		</form>
			<br>
			<form id="mo_wpns_enable_unusual_activity_email_to_user" method="post" action="">
				<input type="hidden" name="option" value="mo_wpns_enable_unusual_activity_email_to_user">
				<input type="checkbox" name="enable_unusual_activity_email_to_user" '.esc_html($notify_admin_unusual_activity).' onchange="document.getElementById(\'mo_wpns_enable_unusual_activity_email_to_user\').submit();"';if(!get_option("admin_email_address_status") || get_option("admin_email_address") ==''){echo "disabled";} 
		echo '		> Notify users for unusual activity with their account.
				<a style="cursor:pointer" id="custom_user_template_expand">Customize Email Template</a>
			</form>
			<form id="custom_user_template_form" method="post" class="hidden">
				<input type="hidden" name="option" value="custom_user_template">
				<br><br>';

				wp_editor($template2, $template_type2, $user_activity_template); 
				submit_button( 'Save Template' );

echo'		</form>
			<br>
		</div>
		<div class="mo_wpns_setting_layout">
		<table>
		<tr>
		   <th><p><b>This will give the Notification of new release via Mail to Enable or Disable the Email notification please turn ON or OFF  </b></p>
		   </th>
		   <th >
		   <label class="mo_wpns_switch" style="align:right;">
			<input type="checkbox" name="S_mail" id="S_mail">
		 	<span class="mo_wpns_slider mo_wpns_round"></span>
			</label>
			</th>
			</tr>
			</table>
			</div>
			</form>
			<br>

		<div class="mo_wpns_setting_layout" style="align:right;">
		<table>
		<tr>
		   <th><p><b>This will give the alert for the IP matching via Mail to Enable or Disable the Email notification please turn ON or OFF  </b></p>
		   </th>
		   <th>
		   <label class="mo_wpns_switch">
			<input type="checkbox" name="Smail" id="Smail" />
		 	<span class="mo_wpns_slider mo_wpns_round"></span>
			</label>
			</th>
			</tr>
			</table>
		</div>
				</div>
	<script>
		jQuery(document).ready(function(){
			$("#custom_admin_template_expand").click(function() {
				$("#custom_admin_template_form").slideToggle();
			});
			$("#custom_user_template_expand").click(function() {
				$("#custom_user_template_form").slideToggle();
			});
		});
	</script>';
	?>
	<script>
	var S_mail = "<?php echo get_site_option('mo2f_mail_notify_new_release');?>";
         if(S_mail == 'on')
			{
				jQuery('#S_mail').prop("checked",true);	
			}
		jQuery("#S_mail").click(function()
		{
			
			var S_mail = jQuery("input[name='S_mail']:checked").val();

			var nonce = '<?php echo esc_html(wp_create_nonce("WAFsettingNonce"));?>';
				if(S_mail != '')
				{
					var data = {
								'action'					: 'wpns_login_security',
								'wpns_loginsecurity_ajax' 	: 'waf_settings_mail_form_notify',
								'optionValue' 				: 'S_mail',
								'S_mail'                     :  S_mail,
								'nonce'						:  nonce
							};
						jQuery.post(ajaxurl, data, function(response) 
						{
							var response = response.replace(/\s+/g,' ').trim();	
			    		});
				}
		});
	</script>
	<script>
		var Smail = "<?php echo get_site_option('mo2f_mail_notify');?>";
         if(Smail == 'on')
			{
				jQuery('#Smail').prop("checked",true);	
			}
		   else
		   {
		   	jQuery('#Smail').prop("checked",false);
		   }
		jQuery("#Smail").click(function()
		{	
			var Smail = jQuery("input[name='Smail']:checked").val();
			var nonce = '<?php echo esc_html(wp_create_nonce("WAFsettingNonce"));?>';
				if(Smail != '')
				{
					var data = {
								'action'					: 'wpns_login_security',
								'wpns_loginsecurity_ajax' 	: 'waf_settings_IP_mail_form',
								'optionValue' 				: 'Smail',
								'Smail'                     :  Smail,
								'nonce'						:  nonce
							};
						jQuery.post(ajaxurl, data, function(response) 
						{
							var response = response.replace(/\s+/g,' ').trim();
							
			    		});
				}
	});
		
	</script>