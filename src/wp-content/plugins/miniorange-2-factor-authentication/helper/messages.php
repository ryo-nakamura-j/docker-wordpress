<?php
	
	class MoWpnsMessages
	{
		// ip-blocking messages
		const INVALID_IP						= "The IP address you entered is not valid or the IP Range is not valid.";
		const INVALID_RANGE 					= "IP Range is not valid, please enter a valid range";
		const IP_ALREADY_BLOCKED				= "IP Address is already Blocked";
		const IP_PERMANENTLY_BLOCKED			= "IP Address is blocked permanently.";
		const IP_ALREADY_WHITELISTED			= "IP Address is already Whitelisted.";
		const IP_IN_WHITELISTED					= "IP Address is Whitelisted. Please remove it from the whitelisted list.";
		const IP_UNBLOCKED						= "IP has been unblocked successfully";
		const IP_WHITELISTED					= "IP has been whitelisted successfully";
		const IP_UNWHITELISTED					= "IP has been removed from the whitelisted list successfully";

		//login-security messages
		const BRUTE_FORCE_ENABLED				= "Brute force protection is enabled.";
		const BRUTE_FORCE_DISABLED				= "Brute force protection is disabled.";
		const DOS_ENABLED						= "DOS protection enabled.";
		const DOS_DISABLED						= "DOS protection disabled.";
		const TWOFA_ENABLED						= "Two Factor protection has been enabled.";
		const TWOFA_DISABLED					= "Two Factor protection has been disabled.";
		const RBA_ENABLED						= "Mobile Authentication and Risk based access is Enabled.";						
		const RBA_DISABLED						= "Risk based access is Disabled.";
		const RECAPTCHA_ENABLED					= "Google reCAPTCHA configuration is enabled.";
		const RECAPTCHA_DISABLED				= "Google reCAPTCHA configuration is disabled.";
		const STRONG_PASS_ENABLED				= "Strong Password has been enabled for your users.";
		const STRONG_PASS_DISABLED				= "Strong Password has been disabled for your users.";		

		//notification messages
		const NOTIFY_ON_IP_BLOCKED				= "Email notification is enabled for Admin.";
		const DONOT_NOTIFY_ON_IP_BLOCKED		= "Email notification is disabled for Admin.";
		const NOTIFY_ON_UNUSUAL_ACTIVITY		= "Email notification is enabled for user for unusual activities.";
		const DONOT_NOTIFY_ON_UNUSUAL_ACTIVITY  = "Email notification is disabled for user for unusual activities.";
		const NONCE_ERROR						= "Nonce Error.";
		const TWO_FA_ON_LOGIN_PROMPT_ENABLED		= "2FA prompt on the WP Login Page Enabled.";
		const TWO_FA_ON_LOGIN_PROMPT_DISABLED		= "2FA prompt on the WP Login Page Disabled.";
		const TWO_FA_PROMPT_LOGIN_PAGE			= 'Please disable Login with 2nd factor only to enable 2FA prompt on login page.';

		//registration security
		const DOMAIN_BLOCKING_ENABLED			= "Blocking fake user registrations is Enabled.";
		const DOMAIN_BLOCKING_DISABLED			= "Blocking fake user registration is disabled";
		const ENFORCE_STRONG_PASSWORD			= "Strong password enforcement is Enabled.";
		const ENFORCE_STRONG_PASS_DISABLED		= "Strong password enforcement is Disabled.";
		const ENABLE_ADVANCED_USER_VERIFY		= "Advanced user verification is Enabled.";
		const DISABLE_ADVANCED_USER_VERIFY		= "Advanced user verification is Disable.";
		const ENABLE_SOCIAL_LOGIN				= "Social Login Integration is Enabled.";
		const DISABLE_SOCIAL_LOGIN				= "Social Login Integration is Disabled.";

		//Advanced security
		const HTACCESS_ENABLED					= "htaccess security has been enabled";
		const HTACCESS_DISABLED					= "htaccess security has been disabled";
		const USER_AGENT_BLOCK_ENABLED			= "User Agent has block been enabled";
		const USER_AGENT_BLOCK_DISABLED			= "User Agent has block been disabled";
		const INVALID_IP_FORMAT 				= "Please enter Valid IP Range.";
		//content protection
		const CONTENT_PROTECTION_ENABLED		= "Your configuration for Content Protection has been saved.";
		const CONTENT_SPAM_BLOCKING				= "Protection for Comment SPAM has been enabled.";
		const CONTENT_RECAPTCHA					= "reCAPTCHA has been enabled for Comments.";
		const CONTENT_SPAM_BLOCKING_DISABLED	= "Protection for Comment SPAM has been disabled.";
		const CONTENT_RECAPTCHA_DISABLED		= "reCAPTCHA has been disabled for Comments.";

		//support form 
		const SUPPORT_FORM_VALUES				= "Please submit your query along with email.";
		const SUPPORT_FORM_SENT					= "Thanks for getting in touch! We shall get back to you shortly.";
		const TRIAL_REQUEST_SENT			    = "Thanks for getting in touch! We shall provide you the trial plugin on your email shortly.";
		const TRIAL_REQUEST_ALREADY_SENT		= "You have already sent a trial request for premium plugin. We will get back to you on your email soon.";
		const SUPPORT_FORM_ERROR				= "Your query could not be submitted. Please try again.";
        // request demo form
        const DEMO_FORM_ERROR					= "Please fill out all the fields.";
        //feedback Form
		const DEACTIVATE_PLUGIN                 		= "Plugin deactivated successfully";

		//common messages
		const UNKNOWN_ERROR						= "Error processing your request. Please try again.";
		const CONFIG_SAVED						= "Configuration saved successfully.";
		const REQUIRED_FIELDS					= "Please enter all the required fields";
		CONST SELECT_A_PLAN                     = "Please select a plan";
		const RESET_PASS						= "You password has been reset successfully and sent to your registered email. Please check your mailbox.";
		const TEMPLATE_SAVED					= "Email template saved.";
		const GET_BACKUP_CODES					= "<div class='mo2f-custom-notice notice notice-warning backupcodes-notice'><p><p class='notice-message'><b>Please download backup codes using the 'Get backup codes' button to avoid getting locked out. Backup codes will be emailed as well as downloaded.</b></p><button class='backup_codes_dismiss notice-button'><i>NEVER SHOW AGAIN</i></button></p></div>";
		
		const CLOUD2FA_SINGLEUSER					= "<div class='mo2f-custom-notice notice notice-warning whitelistself-notice'><p><p class='notice-message'>The current solution is cloud which supports 2-factor for only one user. Either upgrade your plan or contact your administrator.</p></p></div>";

		//registration messages
		const PASS_LENGTH						= "Choose a password with minimum length 6.";
		const ERR_OTP_EMAIL						= "There was an error in sending email. Please click on Resend OTP to try again.";
		const OTP_SENT							= 'A passcode is sent to {{method}}. Please enter the otp below.';
		const REG_SUCCESS						= 'Your account has been retrieved successfully.';
		const ACCOUNT_EXISTS					= 'You already have an account with miniOrange. Please enter a valid password.';
		const INVALID_UP						= 'Invalid username or password.';
		const ACCOUT_NOTEXISTS					= 'Account does not exist. Please create one to use Two-factor Authentication';
		
		const INVALID_CRED						= 'Invalid username or password. Please try again.';
		const REQUIRED_OTP 						= 'Please enter a value in OTP field.';
		const INVALID_OTP 						= 'Invalid one time passcode. Please enter a valid passcode.';
		const INVALID_PHONE						= 'Please enter a valid phone number.';
		const INVALID_INPUT						= 'Please enter a valid value in the input fields.';
		const PASS_MISMATCH						= 'Password and Confirm Password do not match.';
        const CRON_DB_BACKUP_ENABLE			    = 'Scheduled Database Backup enabled';
		const CRON_DB_BACKUP_DISABLE			= 'Scheduled Database Backup disabled';
		const CRON_FILE_BACKUP_ENABLE			= 'Scheduled File Backup enabled';
		const CRON_FILE_BACKUP_DISABLE			= 'Scheduled File Backup disabled';	
		const BACKUP_CREATED					= 'Backup created successfully';
		const WARNING  							= 'Please select folder for backup';
        const INVALID_EMAIL  					= 'Please enter valid Email ID';
        const EMAIL_SAVED 						= 'Email ID saved successfully';
        const INVALID_HOURS 					= 'For scheduled backup, please enter number of hours greater than 1.';
        const ALL_ENABLED						= "All Website security features are available.";
        const ALL_DISABLED						= 'All Website security features are disabled.';
        const TWO_FACTOR_ENABLE					= 'Two-factor is enabled. Configure it in the Two-Factor tab.';
        const TWO_FACTOR_DISABLE				= 'Two-factor is disabled.';
        const WAF_ENABLE						= 'WAF features are now available. Configure it in the Firewall tab.';
        const WAF_DISABLE						= 'WAF is disabled.';
        const LOGIN_ENABLE						= 'Login security and spam protection features are available. Configure it in the Login and Spam tab.';
        const LOGIN_DISABLE						= 'Login security and spam protection features are disabled.';
        const BACKUP_ENABLE 					= 'Encrypted backup features are available. Configure it in the Encrypted Backup tab.';
        const BACKUP_DISABLE 					= 'Encrypted Backup features are disabled.';
        const DELETE_FILE 						= 'Someone has deleted the backup by going to directory please refreash the page';
        const NOT_ADMIN							= 'You are not a admin. Only admin can download';
        const MALWARE_ENABLE					= 'Malware scan features and modes are available. Configure it in the Malware Scan tab.';
        const MALWARE_DISABLE 					= 'Malware scan features are disabled.';
        const ADV_BLOCK_ENABLE					= 'Advanced blocking features are available. Configure it in the Advanced blocking tab.';
        const ADV_BLOCK_DISABLE					= 'Advanced blocking features are disabled.';
        const REPORT_ENABLE						= 'Login and error reports are available in the Reports tab.';
        const REPORT_DISABLE					= 'Login and error reports are disabled.';
        const NOTIF_ENABLE						= 'Notification options are available. Configure it in the Notification tab.';
        const NOTIF_DISABLE						= 'Notifications are disabled.';

        const WHITELIST_SELF					= "<div class='mo2f-custom-notice notice notice-warning whitelistself-notice MOWrn'><p><p class='notice-message'>It looks like you have not whitelisted your IP. Whitelist your IP as you can get blocked from your site.</p><button class='whitelist_self notice-button'><i>WhiteList</i></button></p></div>";
        const ADMIN_IP_WHITELISTED				= "<div class='mo2f-custom-notice notice notice-warning MOWrn'>
                                                       <p class='notice-message'>Your IP has been whitelisted. In the IP Blocking settings, you can remove your IP address from the whitelist if you want to do so.</p>
                                                   </div>";
        
        const NEW_PLUGIN_THEME_CHECK			= "<div class='mo2f-custom-notice notice notice-warning plugin_warning_hide-notice MOWrn'><p><p class='notice-message'>We detected a change in plugins/themes folder. Kindly scan for better security.</p><a class='notice-button' href='admin.php?page=mo_2fa_malwarescan' style='margin-right: 15px;'>SCAN</a><button class='new_plugin_dismiss notice-button' style='margin-right: 15px;'><i>DISMISS</i></button><button class='plugin_warning_never_show_again notice-button'><i>NEVER SHOW AGAIN</i></button></p></div>";

        const CREATE_BACKUP						= "<div class='mo2f-custom-notice notice notice-warning plugin_warning_hide-notice MOWrn'><p><p class='notice-message'>It looks like you have not created a single backup of your website. Make the backup and secure your site.</p><a class='notice-button' href='admin.php?page=mo_2fa_backup' style='margin-right: 15px;'>Take Backup</a><button class='dismiss_website_backup_notice notice-button' style='margin-right: 15px;'><i>DISMISS</i></button><button class='plugin_warning_never_show_again notice-button'><i>NEVER SHOW AGAIN</i></button></p></div>";
        
        const BRUTE_FORCE_NOTICE				= "<div class='mo2f-custom-notice notice notice-warning plugin_warning_hide-notice MOWrn'><p><p class='notice-message'>It looks like your login protection is too weak. Enable brute force feature and safe your website from brute force attacker</p><a class='notice-button' href='admin.php?page=mo_2fa_login_and_spam' style='margin-right: 15px;'>Brute Force</a><button class='dismiss_brute_force_notice notice-button' style='margin-right: 15px;'><i>DISMISS</i></button><button class='plugin_warning_never_show_again notice-button'><i>NEVER SHOW AGAIN</i></button></p></div>";

        const GOOGLE_RECAPTCHA_NOTICE			= "<div class='mo2f-custom-notice notice notice-warning plugin_warning_hide-notice MOWrn'><p><p class='notice-message'>It looks like your login protection is too weak. Enable Google reCAPTCHA and increase your website login security</p><a class='notice-button' href='admin.php?page=mo_2fa_login_and_spam' style='margin-right: 15px;'>Google_reCAPTCHA</a><button class='dismiss_google_recaptcha_notice notice-button' style='margin-right: 15px;'><i>DISMISS</i></button><button class='plugin_warning_never_show_again notice-button'><i>NEVER SHOW AGAIN</i></button></p></div>";

        const WAF_NOTICE			            = "<div class='mo2f-custom-notice notice notice-warning plugin_warning_hide-notice MOWrn'><p><p class='notice-message'>Your website is on risk. Turn on firewall and make secure your website from crawler</p><a class='notice-button' href='admin.php?page=mo_2fa_waf' style='margin-right: 15px;'>Firewall</a><button class='dismiss_firewall_notice notice-button' style='margin-right: 15px;'><i>DISMISS</i></button><button class='plugin_warning_never_show_again notice-button'><i>NEVER SHOW AGAIN</i></button></p></div>";

        const LOW_SMS_TRANSACTIONS				= "<div class='mo2f-custom-notice notice notice-warning low_sms-notice MOWrn'><p><p class='notice-message'><img style='width:15px;' src='".MO2F_PLUGIN_URL.'/includes/images/miniorange_icon.png'."'>&nbsp&nbspYou have left very few SMS transaction. We advise you to recharge or change 2FA method before you have no SMS left.</p><a class='notice-button' href='".MoWpnsConstants::rechargeLink."' target='_blank' style='margin-right: 15px;'>RECHARGE</a><a class='notice-button' href='admin.php?page=mo_2fa_two_fa' id='setuptwofa_redirect' style='margin-right: 15px;'>SET UP ANOTHER 2FA</a><button class='sms_low_dismiss notice-button' style='margin-right: 15px;'><i>DISMISS</i></button><button class='sms_low_dismiss_always notice-button'><i>NEVER SHOW AGAIN</i></button></p></div>";

        const LOW_EMAIL_TRANSACTIONS			= "<div class='mo2f-custom-notice notice notice-warning low_email-notice MOWrn'><p><p class='notice-message'><img style='width:15px;' src='".MO2F_PLUGIN_URL.'/includes/images/miniorange_icon.png'."'>&nbsp&nbspYou have left very few Email transaction. We advise you to recharge or change 2FA method before you have no Email left.</p><a class='notice-button' href='".MoWpnsConstants::rechargeLink."' target='_blank' style='margin-right: 15px;'>RECHARGE</a><a class='notice-button' href='admin.php?page=mo_2fa_two_fa'id='setuptwofa_redirect' style='margin-right: 15px;'>SET UP ANOTHER 2FA</a><button class='email_low_dismiss notice-button' style='margin-right: 15px;'><i>DISMISS</i></button><button class='email_low_dismiss_always notice-button'><i>NEVER SHOW AGAIN</i></button></p></div>";


        const FREE_TRIAL_MESSAGE_TRIAL_PAGE				= "
        <div class='notice notice-warning mo2f-notice-warning trial-notice MOWrn' id='mo2f_is-dismissible'>
        <form id='MO2F_FREE_TRIAL_MESSAGE_TRIAL_PAGE' method='post' action=''>
        <p>
        <img style='width:15px;' src='".MO2F_PLUGIN_URL.'includes/images/miniorange_icon.png'."'>&nbsp&nbspInterested in the Trial of<b> 2 Factor Authentication Premium Plugins?</b> Click on the button below to get trial for <strong>7 days</strong>.
        (<em>No credit card required</em>)
        </p>
        <p style='height:25px; padding: 10px;'>
        <a class='button button-primary notice-button' href='admin.php?page=mo_2fa_trial' id='mo2f_trial_redirect'>Get Trial</a>
        <input type='hidden' name='mo2f_dismiss_trial' value='mo2f_dismiss_trial'/>
        <button type='submit' class='mo2f-trial-dismiss notice-button'><i>DISMISS</i></button>
        </p>
        </form>
        </div>
        ";

        const FREE_TRIAL_MESSAGE_ACCOUNT_PAGE				= "
        <div class='notice notice-warning mo2f-notice-warning trial-notice mo2f-custom-notice MOWrn'>
        <form id='MO2F_FREE_TRIAL_MESSAGE_TRIAL_PAGE' method='post' action=''>
        <p>
        <img style='width:15px;' src='".MO2F_PLUGIN_URL.'includes/images/miniorange_icon.png'."'>&nbsp&nbspInterested in the Trial of<b> 2 Factor Authentication Premium Plugins?</b> Click on the button below to get trial for <strong>7 days</strong>.
        (<em>No credit card required</em>)
        </p>
        <p style='height:25px; padding: 10px;'>
        <a class='button button-primary notice-button' href='admin.php?page=mo_2fa_account' id='mo2f_trial_redirect'>Get Trial</a>
        <input type='hidden' name='mo2f_dismiss_trial' value='mo2f_dismiss_trial'/>
        <button type='submit' class='mo2f-trial-dismiss notice-button'><i>DISMISS</i></button>
        </p>
        </form>
        </div>
        ";

		const NOTIFYING_USER_FOR_REMOVING_NETWORK_SECURITY    ='
		<div class="notice notice-warning mo2f-notice-warning MOWrn mo2f-banner">
		<form id="mo2f-notification-form" class="mo2f-notification-form" method="post" action=" ">
		<div class="mo-logo"><img width="50" height="50" src="'.MO2F_PLUGIN_URL.'includes'.DIRECTORY_SEPARATOR.'images'.DIRECTORY_SEPARATOR.'miniorange_logo.png'.'">
		</div>
		<div class="mo2f-notification-text"><b>Warning!</b> Website security features such as Firewall, Malware scan, and Limit login attempts will be shifted to the <a href="https://wordpress.org/plugins/miniorange-limit-login-attempts/" class="mo-limit-login-link"><u><b>Limit Login Attempts plugin</b></u></a> in the next update. Please note that all <b>2FA features will continue to work</b> in this plugin.</div>
		<input type="hidden" name="mo2f_remove_network_security" value="1" />
		<div class="mo2f-acknowledge-button">
		<button class="button button-secondary" type="submit" id="mo2f-acknowledge-button">Dismiss</a>
		</div>
		</form>
		</div>
';

         public static $notification_array = array('malware_notification_option' => MoWpnsMessages::NEW_PLUGIN_THEME_CHECK ,
												'backup_notification_option'  => MoWpnsMessages::CREATE_BACKUP,
												'bruteforce_notification_option' => MoWpnsMessages::BRUTE_FORCE_NOTICE,
												'recaptcha_notification_option' => MoWpnsMessages::GOOGLE_RECAPTCHA_NOTICE,
												'waf_notification_option' => MoWpnsMessages::WAF_NOTICE
												);
		public static function showMessage($message , $data=array())
		{
			$message = constant( "self::".$message );
		    foreach($data as $key => $value)
		    {
		        $message = str_replace("{{" . $key . "}}", $value , $message);
		    }
		    return $message;
		}
	}