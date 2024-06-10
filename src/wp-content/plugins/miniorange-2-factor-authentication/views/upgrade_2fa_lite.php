<?php
	global $Mo2fdbQueries;
	$user = wp_get_current_user();
	$is_NC = MoWpnsUtility::get_mo2f_db_option('mo2f_is_NC', 'get_option');

	$is_customer_registered = $Mo2fdbQueries->get_user_detail( 'user_registration_with_miniorange', $user->ID ) == 'SUCCESS' ? true : false;

	$mo2f_feature_set = array(
		"Google Authenticator",
		"Security Questions",
		"Authy Authenticator",
		"Microsoft Authenticator",
		"TOTP Based Authenticator",
		"Email Verification",
		"OTP Over Email",
		"OTP Over SMS",
		"OTP Over Whatsapp (Add-on)",
		"OTP Over Telegram",
		"miniOrange QR Code Authentication",
		"miniOrange Soft Token",
		"miniOrange Push Notification",		
		"OTP Over SMS and Email",
		"Hardware Token",
		"Other Features",
		"2FA for specific User Roles",
		"2FA for specific Users",
		"Choose specific authentications",
		"Force Two Factor for",
		"Email Verification during 2FA Registration",
		"Language Translation Support",
		"Password Less Login",
		"Backup Methods",
		"Multi-Site Support",
		"User role based redirection after Login",
		"Add custom Security Questions (KBA)",
		"Customize name in Google Authenticator",
		"Custom Gateway",		
		"Security Questions as backup",
		"App Specific Password from mobile Apps",
		"Brute Force Protection",
		"IP Blocking",
		"Monitoring",
		"Strong Password",
		"File Protection",
		"Custom SMS Gateway",
		"Solution Infi",			
		"Clickatell",													
		"ClickSend",			
		"Twilio SMS",													
		"SendGrid",
		"Many Other Gateways",
		"Support"
	);

	$mo2f_backup_methods = array(

		"Security Questions",
		"OTP Over Email",
		"Backup Codes"

	);

	$mo2f_feature_set_with_plans_NC = array(

		"Google Authenticator"                                          		=> array( true, true, true, true ),
		"Security Questions"                                          			=> array( true, true, true, true ),
		"Authy Authenticator"                                          			=> array( true, true, true, true ),
		"Microsoft Authenticator"                                          		=> array( true, true, true, true ),
		"TOTP Based Authenticator"												=> array( true, true, true, true ),
		"Email Verification"                                          			=> array( true, true, true, true ),
		"OTP Over Email"                                          				=> array( true, true, true, true ),
		"OTP Over SMS"                                          				=> array( true, true, true, true ),
		"OTP Over Whatsapp (Add-on)"                                          	=> array( false, true, false, false ),
		"OTP Over Telegram"                                          			=> array( false, true, false, false ),
		"miniOrange QR Code Authentication"                                     => array( false, false, true, true ),
		"miniOrange Soft Token"                                          		=> array( false, false, true, true ),
		"miniOrange Push Notification"                                          => array( false, false, true, true ),
		"OTP Over SMS and Email"                                          		=> array( false, false, true, true ),
		"Hardware Token"                                         				=> array( false, false, false, true ),
		"Other Features"                                          				=> array( true, true, true, true ),
		"Language Translation Support"                                          => array( true, true, true, true ),
		"Password Less Login"                            						=> array( true, true, true, true ),
		"Backup Methods"                                          				=> array( true, true, true, true),
		"Multi-Site Support"                                                    => array( true, true, true, true ),
		"User role based redirection after Login"                               => array( true, true, true, true ),
		"Add custom Security Questions (KBA)"                                   => array( true, true, true, true ),
		"Add custom Security Questions (KBA)"                                   => array( true, true, true, true ),
		"Customize name in Google Authenticator"                    			=> array( true, true, true, true ),
		"Custom SMS Gateway"                    								=> array( false, true, true, true ),

		"Brute Force Protection"												=> array( false, false, false, true ),
		"IP Blocking"															=> array( false, false, false, true ),
		"Monitoring"															=> array( false, false, false, true ),
		"Strong Password"														=> array( false, false, false,true ),
		"File Protection"														=> array( false, false, false, true ),
		"2FA for specific User Roles"                                    		=> array( true, true, true, true ),
		"2FA for specific Users"                                         		=> array(  false, true, true, true ),
		"Choose specific authentications"                      			=> array( false, true, true, true ),
		"Force Two Factor for"                        					=> array( true, true, true, true ),
		"Email Verification during 2FA Registration"         => array( false, true, true, true ),
		"Security Questions as backup" 									=> array( false, true, true, true ),
		"App Specific Password from mobile Apps"                       			=> array( false, true, true, true ),
		"Support"                                                               => array(
			array("Basic Support by Email"),
			array("Priority Support by Email"),
			array( "Priority Support by Email", "Priority Support with GoTo meetings" ),
			array( "Priority Support by Email", "Priority Support with GoTo meetings" )
		),

		"Solution Infi"												=> array( false, true, true,true ),
		"Clickatell"												=> array( false, true, true,true ),
		"ClickSend"													=> array( false, true, true,true ),
		"Many Other Gateways"										=> array( false, true, true,true ),
		"Custom Gateway"											=> array( false, true, true,true ),		
		"Twilio SMS"												=> array( false, true, true,true ),
		"SendGrid"													=> array( false, true, true,true ),
	);

		$mo2f_feature_description_set = array(
		"Enter the soft token from the account in your Google Authenticator App to login.",
		"Answer the three security questions you had set, to login.",
		"Enter the soft token from the account in your Authy Authenticator App to login.",
		"Enter the soft token from the account in your Microsoft Authenticator App to login.",
		"Enter the soft token from the account in your TOTP Authenticator App to login.",
		"Accept the verification link sent to your email to login.",
		"You will receive a one time passcode via Email.",
		"You will receive a One Time Passcode via SMS on your Phone",
		"You will receive a One Time Passcode on your Whatsapp account - Supported with twillio",
		"You will receive a One Time Passcode on your Telegram account",
		"Scan the QR code from the account in your miniOrange Authenticator App to login.",
		"Enter the soft token from the account in your miniOrange Authenticator App to login.",
		"Accept a push notification in your miniOrange Authenticator App to login.",		
		"In this method, you receive an sms and an email containing a numeric key which you need to enter.",
		"In this method, you need to connect a usb like token into your computer which generates an alphabetic key.",
		"Other Features",
		"Enable and disable 2fa for users based on roles(Like Administrator, Editor and others). It works for custom roles too.",
		"Enable or disable 2fa for a particular user.",
		"You can choose specific authentication methods for specific user roles",
		"Enforce user to setup 2nd factor during registration",
		"One time Email Verification for Users during 2FA Registration",
		"You can translate the plugin in a language of your choice",
		"After a valid username is entered, the 2FA prompt will be directly displayed",
		"By using backup you can restore the plugin settings",
		"All features including 2FA can be enabled for all subsites",
		"According to user's role the particular user will be redirected to specific location",
		"Custom questions can be added for the Security Questions Method",
		"You can customize the account name in Google Authenticator app on mobile",
		"Have your own gateway? You can use it, no need to purchase SMS then.",
		"Allows for login using security questions in cases where physical access to the mobile isn’t possible",
		"For access wordpress on different moblie apps, app specific passwords can be set",
		"This protects your site from attacks which tries to gain access / login to a site with random usernames and passwords.",
		"Allows you to manually/automatically block any IP address that seems malicious from accessing your website. ",
		"Monitor activity of your users. For ex:- login activity, error report",
		"Enforce users to set a strong password.",
		"Allows you to protect sensitive files through the malware scanner and other security features.",
		"Custom SMS Gateway",
		"Configure and test to add Solution Infi as custom gateway",
		"Configure and test to add Clickatell as custom gateway",
		"Configure and test to add ClickSend as custom gateway",
		"Configure and test to add Twilio SMS as custom gateway",
		"Configure and test to add SendGrid as custom gateway",
		"Not Listed? Configure and test to add it as custom gateway",
		"24/7 support is available.",
	);
	$mo2f_addons_set		=	array(
		"RBA & Trusted Devices Management",
		"Personalization",		                 
		"Short Codes"  
	);
	$mo2f_addons           	= array(
		"RBA & Trusted Devices Management" 	=> array( false, true, true, true ),
		"Personalization"					=> array( false, true, true, true ),
		"Short Codes"						=> array( false, true, true, true )
	);
	$mo2f_addons_plan_name = array(
		"RBA & Trusted Devices Management"		  => "wp_2fa_addon_rba",
		"Personalization"		                  => "wp_2fa_addon_personalization",
		"Short Codes"        		              => "wp_2fa_addon_shortcode"
	);


	$mo2f_addons_with_features = array(
		"Personalization"         		          => array(
			"Custom UI of 2FA popups",
			"Custom Email and SMS Templates",
			"Customize 'powered by' Logo",
			"Customize Plugin Icon",
			"Customize Plugin Name",
			
		),
		"RBA & Trusted Devices Management" 		  => array(
			"Remember Device",
			"Set Device Limit for the users to login",
		 "IP Restriction: Limit users to login from specific IPs"
		),
		"Short Codes"    		                  => array(
			"Option to turn on/off 2-factor by user",
			"Option to configure the Google Authenticator and Security Questions by user",
			"Option to 'Enable Remember Device' from a custom login form",
			"On-Demand ShortCodes for specific fuctionalities ( like for enabling 2FA for specific pages)"
		)
	);
	?>
    <div class="mo2f_licensing_plans" style="border:0px;width: 100%">
	
    <table class="table mo_table-bordered mo_table-striped" style="width: 100%">
            <tbody class="mo_align-center mo-fa-icon">
			<?php for ( $i = 0; $i < count( $mo2f_feature_set ); $i ++ ) { ?>
                <tr>
                    	<?php
						$feature_set = $mo2f_feature_set[ $i ];
                   	
						$f_feature_set_with_plan = $mo2f_feature_set_with_plans_NC[ $feature_set ];
					
					?>
                    <td class="mo2f_padding_style"><?php
                    	if ($feature_set == "Support") {
                    		?>
                    		<div>
                    		<?php
                    	}
                    	else
                    	{
                    		?>
                    		<div style="float: left;">
                    		<?php
                    	}
							
								if ( gettype( $f_feature_set_with_plan[0] ) == "boolean" && ($feature_set != "Other Features" )&& ($feature_set != "Custom SMS Gateway" )) 
								{
									echo mo2f_get_binary_equivalent_2fa_lite( $f_feature_set_with_plan[0] );
								} elseif($feature_set == "Support") {
									echo 'Basic Support by Email';
								}
								
								if ($feature_set == "Other Features" ) 
								{
									?>
									<h3 style="float: left;">&nbsp;Other Features</h3>
									<?php 
								}
								elseif ($feature_set == "Custom SMS Gateway" ) 
								{
									?>
									<h3 style="float: left;">&nbsp;Custom SMS Gateway
									<?php 
								}
								elseif ($feature_set != "Support")
								{
									echo esc_html($feature_set);
								}
								if ($feature_set == "Force Two Factor for" ) {
									echo " administrators";									
								}
								?>
								</div>
								<div>
							<?php
							if ($feature_set == "Backup Methods") {
								echo mo2f_features_on_hover_2fa_lite("Security Questions is available as a backup method");
							}
							elseif ($feature_set == "Force Two Factor for") {
								echo mo2f_features_on_hover_2fa_lite("Enforce administrators to setup 2nd factor during registration");
							}
							elseif ($feature_set != "Other Features" && $feature_set != "Custom SMS Gateway") 
							{
								echo mo2f_features_on_hover_2fa_lite($mo2f_feature_description_set[$i]);
							}
							?></div>
							<?php
								if ($feature_set == "Backup Methods") {
									?>
									<div style="width: 100%;text-align: left;"><br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;1. Security Questions (KBA)</div>
									<?php 
								}

							?>
							<?php
							
						 ?>
							</div>
							
                    </td>
                    <td class="mo2f_black_background"></td>
                    <td  class="mo2f_padding_style"><?php
                    	if ($feature_set == "Support") {
                    		?>
                    		<div>
                    		<?php

                    	}
                    	else
                    	{
                    		?>
                    		<div style="float: left;">
                    		<?php
                    	}
							
								if ( gettype( $f_feature_set_with_plan[1] ) == "boolean" && ($feature_set != "Other Features" )&& ($feature_set != "Custom SMS Gateway" )) {
									echo mo2f_get_binary_equivalent_2fa_lite( $f_feature_set_with_plan[1] );
								} elseif($feature_set == "Support") {
									echo 'Priority Support by Email';
								}
								
								if ($feature_set == "Other Features" ) 
								{
									?>
									<h3 style="float: left;">&nbsp;Other Features</h3>
									<?php 
								}
								elseif ($feature_set == "Custom SMS Gateway" ) 
								{
									?>
									<h3 style="float: left;">&nbsp;Custom SMS Gateway&nbsp;&nbsp;
									<a  style="text-decoration:none;" href="https://login.xecurify.com/moas/login?redirectUrl=https://login.xecurify.com/moas/admin/customer/smsconfig" target="_blank">Test Now</a>
									</h3>
									<?php 
								}
								elseif ($feature_set != "Support")
								{
									echo esc_html($feature_set);
								}
								if ($feature_set == "Force Two Factor for" ) {
									echo " all users";									
								}
								?>
								</div>
								<div>
							<?php
							if ($feature_set == "Backup Methods") {
								echo mo2f_features_on_hover_2fa_lite("Security Questions is available as a backup method");
							}
							
							elseif ($feature_set != "Other Features" && $feature_set != "Custom SMS Gateway") 
							{
								echo mo2f_features_on_hover_2fa_lite($mo2f_feature_description_set[$i]);
							}
							?></div>
							<?php
								if ($feature_set == "Backup Methods") {
									?>
									<div style="width: 100%;text-align: left;"><br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;1. Security Questions (KBA)</div>
									<?php 
								}

							?>
							<?php
							
						 ?>
					</div>
                    </td>
                    <td class="mo2f_black_background"></td>
                    <td class="mo2f_padding_style"><?php
                    if ($feature_set == "Support") {
                    		?>
                    		<div>
                    		<?php
                    	}
                    	else
                    	{
                    		?>
                    		<div style="float: left;">
                    		<?php
                    	}
						
								if ( gettype( $f_feature_set_with_plan[2] ) == "boolean" && ($feature_set != "Other Features" )&& ($feature_set != "Custom SMS Gateway" )) {
									echo mo2f_get_binary_equivalent_2fa_lite( $f_feature_set_with_plan[2] );
								} elseif($feature_set == "Support") {
									echo 'Priority Support by Email, Priority Support with GoTo meetings';
								}
								if ($feature_set == "Other Features") 
								{
									?>
									<h3 style="float: left;">&nbsp;Other Features</h3>
									<?php 
								}
								elseif ($feature_set == "Custom SMS Gateway") 
								{
									?>
									<h3 style="float: left;">&nbsp;Custom SMS Gateway&nbsp;&nbsp;
									<a  style="text-decoration:none;" href="https://login.xecurify.com/moas/login?redirectUrl=https://login.xecurify.com/moas/admin/customer/smsconfig" target="_blank">Test Now</a>
									</h3>
									<?php 
								}
								elseif($feature_set != "Support")
								{
									echo $feature_set;
								}
								if ($feature_set == "Force Two Factor for" ) {
									echo " all users";									
								}
								?>
								</div>
							<div>
							<?php
							if ($feature_set == "Backup Methods") {
								echo mo2f_features_on_hover_2fa_lite("Security Questions, OTP Over Email, Backup Codes are available as a backup method");
							}
							elseif ($feature_set != "Other Features"&& $feature_set != "Custom SMS Gateway") 
							{
								echo mo2f_features_on_hover_2fa_lite($mo2f_feature_description_set[$i]);
							}
							?>
								
							</div>
							<?php
								if ($feature_set == "Backup Methods") {
									?>
									<br><div style="width: 100%;text-align: left;}"> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;1. Security Questions<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;2. OTP Over Email<br>
									 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;3. Backup Codes</div>
									<?php 
								
							?>
							<?php
						}
                    	?></div>
                    </td>
                    <td class="mo2f_black_background"></td>
					<td class="mo2f_padding_style"><?php
                    if ($feature_set == "Support") {
                    		?>
                    		<div>
                    		<?php
                    	}
                    	else
                    	{
                    		?>
                    		<div style="float: left;">
                    		<?php
                    	}
						
								if ( gettype( $f_feature_set_with_plan[3] ) == "boolean" && ($feature_set != "Other Features" )&& ($feature_set != "Custom SMS Gateway" )) {
									echo mo2f_get_binary_equivalent_2fa_lite( $f_feature_set_with_plan[3] );
								} elseif($feature_set == "Support") {
									echo 'Priority Support by Email, Priority Support with GoTo meetings';
								}
								if ($feature_set == "Other Features") 
								{
									?>
									<h3 style="float: left;">&nbsp;Other Features</h3>
									<?php 
								}
								elseif ($feature_set == "Custom SMS Gateway" ) 
								{
									?>
									<h3 style="float: left;">&nbsp;Custom SMS Gateway&nbsp;&nbsp;
									<a  style="text-decoration:none;" href="https://login.xecurify.com/moas/login?redirectUrl=https://login.xecurify.com/moas/admin/customer/smsconfig" target="_blank">Test Now</a>
									</h3>
									<?php 
								}								
								elseif($feature_set != "Support")
								{
									echo esc_html($feature_set);
								}
								if ($feature_set == "Force Two Factor for" ) {
									echo " all users";									
								}
								?>
								</div>
							<div>
							<?php
							if ($feature_set == "Backup Methods") {
								echo mo2f_features_on_hover_2fa_lite("Security Questions, OTP Over Email, Backup Codes are available as a backup method");
							}
							elseif ($feature_set != "Other Features" && $feature_set != "Custom SMS Gateway") 
							{
								echo mo2f_features_on_hover_2fa_lite($mo2f_feature_description_set[$i]);
							}
							?></div>
							<?php
								if ($feature_set == "Backup Methods") {
									?>
									<div style="width: 100%;text-align: left;}"><br> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;1. Security Questions<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;2. OTP Over Email<br>
									 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;3. Backup Codes</div>
									<?php 
								
							?>
							<?php
						}
                    	?></div>
                    </td>
                </tr>
			<?php } ?>
			<tr>
				<th colspan="8" class="mo2f_2fa_lite_plan_title"><h1 class="mo2f_white_color_style">Add-Ons</h1></th>
			</tr>
            <tr>
                <td><b>Standard Lite </b>(Contact Us)</td>
                <td class="mo2f_black_background"></td>
                <td><b>Premium Lite</b></td>
                <td class="mo2f_black_background"></td>	
                <td><b>Premium</b></td>
                <td class="mo2f_black_background"></td>
                <td><b>Enterprise</b></td>
            </tr>
			<?php 
            	

			for ( $i = 0; $i < count( $mo2f_addons_set ); $i ++ ) 
			{ 
				$mo2f_addons_feature_set = $mo2f_addons_set[ $i ];
				$mo2f_feature_set_with_plan = $mo2f_addons[ $mo2f_addons_feature_set ];
				?>
                <tr>
                    <td>
                    	<?php
                    		if (isset($mo2f_feature_set_with_plan[0]) && gettype( $mo2f_feature_set_with_plan[0] ) == "boolean" ) 
                    		{
								echo mo2f_get_binary_equivalent_2fa_lite( $mo2f_feature_set_with_plan[0] );
								
							}
						?>
                    	<div style="float: left;"><?php echo esc_html($mo2f_addons_feature_set); ?></div>
                    </td>

                    <td class="mo2f_black_background"></td>
                     <td>
                    	<?php
                    		if (isset($mo2f_feature_set_with_plan[1]) && gettype( $mo2f_feature_set_with_plan[1] ) == "boolean" ) 
                    		{
								echo mo2f_get_binary_equivalent_2fa_lite( $mo2f_feature_set_with_plan[1] );
								
							}
						?>
                    	<div style="float: left;"><?php echo esc_html($mo2f_addons_feature_set); ?></div>
                    </td>
                    <td class="mo2f_black_background"></td>
                   <td>
                    	<?php
                    		if (isset($mo2f_feature_set_with_plan[2]) && gettype( $mo2f_feature_set_with_plan[2] ) == "boolean" ) 
                    		{
								echo mo2f_get_binary_equivalent_2fa_lite( $mo2f_feature_set_with_plan[2] );
								
							}
						?>
                    	<div style="float: left;"><?php echo esc_html($mo2f_addons_feature_set); ?></div>
                    </td>
                    <td class="mo2f_black_background"></td>
                    <td>
                    	<?php
                    		if (isset($mo2f_feature_set_with_plan[3]) && gettype( $mo2f_feature_set_with_plan[3] ) == "boolean" ) 
                    		{
								echo mo2f_get_binary_equivalent_2fa_lite( $mo2f_feature_set_with_plan[3] );
								
							}
						?>
                    	<div style="float: left;"><?php echo esc_html($mo2f_addons_feature_set); ?></div>
                    </td>
                </tr>
			<?php } ?>

            </tbody>
        </table>
        <hr><br>
        <div style="padding:10px;">
			<?php for ( $i = 0; $i < count( $mo2f_addons_set ); $i ++ ) {
				$f_feature_set_of_addons = $mo2f_addons_with_features[ $mo2f_addons_set[ $i ] ];
				for ( $j = 0; $j < $i + 1; $j ++ ) { ?>*<?php } ?>
                <b><?php echo esc_html($mo2f_addons_set[ $i ]); ?> Features</b>
                <br>
                <ol>
					<?php for ( $k = 0; $k < count( $f_feature_set_of_addons ); $k ++ ) { ?>
                        <li><?php echo esc_html($f_feature_set_of_addons[ $k ]); ?></li>
					<?php } ?>
                </ol>

                <hr><br>
			<?php } ?>
			<b>* Multisite</b>
            <p><?php echo mo2f_lt( 'For your first license 3 subsites will be activated automatically on the same domain. And if you wish to use it for more please contact support ' ); ?></p>
            <hr>
            <br>
            <b>**** SMS Charges</b>
            <p><?php echo mo2f_lt( 'If you wish to choose OTP Over SMS / OTP Over SMS and Email as your authentication method,
                    SMS transaction prices & SMS delivery charges apply and they depend on country. SMS validity is for lifetime.' ); ?></p>
            <hr>
            <br>
            <b>***** Custom SMS Gateways</b>
            <p>
            	<ol>
            		<li>Plivo</li>
            		<li>Solution Infi</li>
            		<li>Clickatell</li>
            		<li>ClickSend</li>
            		<li>Telesign SMS Verify</li>
            		<li>Telize</li>
            		<li>Twilio SMS</li>
            		<li>SendGrid</li>
            		<li>Many Other Gateways</li>
            	</ol>
           	You can test custom gateway <a  style="text-decoration:none;" href="https://login.xecurify.com/moas/login?redirectUrl=https://login.xecurify.com/moas/admin/customer/smsconfig" target="_blank">[HERE]</a>
            </p>
            <hr>
            <br>
            <div>
                <h2>Note</h2>
                <ol class="mo2f_licensing_plans_ol">
                    <li><?php echo mo2f_lt( 'The plugin works with many of the default custom login forms (like Woocommerce / Theme My Login), however if you face any issues with your custom login form, contact us and we will help you with it.' ); ?></li>
                    <li style="color: red"><?php echo mo2f_lt( 'There is license key required to activate the Standard/Premium Plugins. You will have to login with the miniOrange Account you used to make the purchase then enter license key to activate plugin.' ); ?>
                    	
                    </li>
                </ol>
            </div>

            <br>
            <hr>
            
           

            <style>#mo2f_support_table {
                    display: none;
                }

            </style>
        </div>
    </div>

<?php 
function mo2f_create_li_2fa_lite( $mo2f_array ) {
	$html_ol = '<ul>';
	foreach ( $mo2f_array as $element ) {
		$html_ol .= "<li>" . $element . "</li>";
	}
	$html_ol .= '</ul>';

	return $html_ol;
}
function mo2f_get_binary_equivalent_2fa_lite( $mo2f_var ) {
	switch ( $mo2f_var ) {
		case 1:
			return "<div style='color: #2271b1;font-size: x-large;float:left;margin:0px 5px;'>🗸</div>";
		case 0:
			return "<div style='color: red;font-size: x-large;float:left;margin:0px 5px;'>×</div>";
		default:
			return $mo2f_var;
	}
}

function mo2f_features_on_hover_2fa_lite( $mo2f_var ) {

	return '<div class="mo2f_tooltip"style="float: right;"><span class="dashicons dashicons-info mo2f_info_tab"></span><br><span class="mo2f_tooltiptext" style="margin-left: -1089%;">'. $mo2f_var .'</span>';
}

function mo2f_create_li( $mo2f_array ) {
	$html_ol = '<ul>';
	foreach ( $mo2f_array as $element ) {
		$html_ol .= "<li>" . $element . "</li>";
	}
	$html_ol .= '</ul>';

	return $html_ol;
}
