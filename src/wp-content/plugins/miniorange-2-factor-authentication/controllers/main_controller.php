<?php

	global $moWpnsUtility,$mo2f_dirName;

	$controller = $mo2f_dirName . 'controllers'.DIRECTORY_SEPARATOR;

include_once $controller 	 . 'navbar.php';
if(current_user_can('administrator'))
{

	include_once $controller 	 . 'newtork_security_features.php';    
        
		if( isset( $_GET[ 'page' ])) 
		{
			switch(sanitize_text_field($_GET['page']))
			{
				case 'mo_2fa_dashboard':
					include_once $controller . 'dashboard.php';			    break;
				case 'mo_2fa_login_and_spam':
					include_once $controller . 'login-spam.php';				break;
				case 'default':
					include_once $controller . 'login-security.php';			break;
				case 'mo_2fa_account':
					include_once $controller . 'account.php';				break;		
			
				case 'mo_2fa_upgrade':
					include_once $controller . 'upgrade.php';                break;
				case 'mo_2fa_waf':
					include_once $controller . 'waf.php';		    		break;
				case 'mo_2fa_blockedips':
					include_once $controller . 'ip-blocking.php';			break;
				case 'mo_2fa_advancedblocking':
					include_once $controller . 'advanced-blocking.php';		break;
				case 'mo_2fa_notifications':
					include_once $controller . 'notification-settings.php';	break;
                case 'mo_2fa_all_users':
					include_once $controller . 'all_users.php';        break;
			
				case 'mo_2fa_reports':
					include_once $controller . 'reports.php';				break;
				case 'mo_2fa_licensing':
					include_once $controller . 'licensing.php';				break;
				case 'mo_2fa_troubleshooting':
					include_once $controller . 'troubleshooting.php';		break;
				case 'mo_2fa_addons':
					include_once $controller . 'addons.php';					break;
				case 'mo_2fa_malwarescan':
					include_once $controller . 'malware_scanner'.DIRECTORY_SEPARATOR.'scan_malware.php';			break;
				case 'mo_2fa_two_fa':
					include_once $controller .'twofa'.DIRECTORY_SEPARATOR. 'two_fa.php';					break;
				case 'mo_2fa_request_demo':
					include_once $controller . 'request_demo.php';			break;	
				case 'mo_2fa_request_offer':
					include_once $controller . 'request_offer.php';          break;
				case 'mo_2fa_trial':
					include_once $controller . 'trial.php';				    break;

			}
		}
	}
	else
	{
		if( isset( $_GET[ 'page' ])) 
		{
			switch(sanitize_text_field($_GET['page']))
			{
				case 'mo_2fa_two_fa':
					include_once $controller .'twofa'.DIRECTORY_SEPARATOR. 'two_fa.php';					break;	
			
			}

		}

	}
	if (isset( $_GET[ 'page' ])) {


	if (MoWpnsUtility::get_mo2f_db_option('mo_wpns_2fa_with_network_security', 'get_option') && current_user_can('administrator'))
	{
		include_once $controller . 'feedback_footer.php';
	}
}
?>

