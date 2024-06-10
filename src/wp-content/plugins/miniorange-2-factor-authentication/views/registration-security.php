<?php

echo'<div>	
		<div class="mo_wpns_setting_layout" id="mo2f_block_registration">';

echo'		<h3>Block Registerations from fake users</h3>
			<div class="mo_wpns_subheading">
				Disallow Disposable / Fake / Temporary email addresses
			</div>
			
			<form id="mo_wpns_enable_fake_domain_blocking" method="post" action="">
				<input type="hidden" name="option" value="mo_wpns_enable_fake_domain_blocking">
				<input type="checkbox" name="mo_wpns_enable_fake_domain_blocking" '.esc_html($domain_blocking).' onchange="document.getElementById(\'mo_wpns_enable_fake_domain_blocking\').submit();"> Enable blocking registrations from fake users.
			</form>
		</div>
		
		<div class="mo_wpns_setting_layout">	
			<h3>Advanced User Verification</h3>
			<div class="mo_wpns_subheading">Verify identity of user by sending One Time Password ( OTP ) on his phone number or email address.</div>
			<p>Contact us using the plugin support form on the right or mail us directly on <a href="mailto:2fasupport@xecurify.com">2fasupport@xecurify.com</a> or <a href="mailto:info@xecurify.com">info@xecurify.com</a> 
			';

			if($user_verify)
				mo2f_user_verify();
			
echo'		
		</div>
		
		<div class="mo_wpns_setting_layout">	
			<h3>Social Login Integration</h3>
			<div class="mo_wpns_subheading">Allow your user to login and auto-register with their favourite social network like Google, Twitter, Facebook, Vkontakte, LinkedIn, Instagram, Amazon, Salesforce, Windows Live.</div>
			
			<form id="mo_wpns_social_integration" method="post" action="">
				<input type="hidden" name="option" value="mo_wpns_social_integration">
				<input type="checkbox" name="mo_wpns_enable_social_integration" '.esc_html($social_login).' onchange="document.getElementById(\'mo_wpns_social_integration\').submit();"> Enable login and registrations with social networks.<br>
			    
			</form>';
			
			if($social_login)
				mo2f_social_login();
				
echo'	</div>
	</div>';