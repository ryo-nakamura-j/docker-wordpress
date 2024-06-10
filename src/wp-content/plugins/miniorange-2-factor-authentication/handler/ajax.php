<?php

class AjaxHandler
{
	function __construct()
	{
		add_action( 'admin_init'  , array( $this, 'mo_wpns_2fa_actions' ) );
	}

	function mo_wpns_2fa_actions()
	{
		global $moWpnsUtility,$mo2f_dirName;

		if (current_user_can( 'manage_options' ) && isset( $_REQUEST['option'] ))
		{ 
			switch(sanitize_text_field($_REQUEST['option']))
			{
				case "iplookup":
					$this->lookupIP(sanitize_text_field($_GET['ip']));	break;
				
				case "dissmissSMTP":
					$this->handle_smtp();			break;
				case "whitelistself":
					$this->whitelist_self();		break;
				
				case "dismissplugin":
					$this->wpns_plugin_notice();	break;
				
				case "dismissbackup":
				     $this->wpns_dismiss_backup_notice();     break;

			    case "dismissbruteforce":
			     	 $this->wpns_dismiss_bruteforce_notice(); break;

			    case "dismissrecaptcha":
			     	 $this-> wpns_dismiss_recaptcha_notice(); break;

			    case "dismissfirewall":
			         $this->wpns_dismiss_firewall_notice();   break;

			    case "plugin_warning_never_show_again":
			          $this->wpns_plugin_warning_never_show_again(); 
			          break;

                  case "mo2f_banner_never_show_again":
                      $this->wpns_mo2f_banner_never_show_again();
                      break;
				 
				 case "dismissSms":
					$this->wpns_sms_notice();  				break;

				case "dismissEmail":
					$this->wpns_email_notice();  			break;

				case "dismissSms_always":
					$this->wpns_sms_notice_always();  		break;

				case "dismissEmail_always":
					$this->wpns_email_notice_always();  	break;
					
			    case "dismisscodeswarning":
					$this->mo2f_backup_codes_dismiss(); 	break;


			}
		}
	}
	
	private function lookupIP($ip)
	{
        $result=wp_remote_get("http://www.geoplugin.net/json.gp?ip=".$ip);

		if( !is_wp_error( $result ) ) {
			$result=wp_remote_retrieve_body( $result);
		}

		$hostname 	= gethostbyaddr($result["geoplugin_request"]);
		try{
            $timeoffset	= timezone_offset_get(new DateTimeZone($result["geoplugin_timezone"]),new DateTime('now'));
            $timeoffset = $timeoffset/3600;

        }catch(Exception $e){
            $result["geoplugin_timezone"]="";
            $timeoffset="";
        }

		$ipLookUpTemplate  = MoWpnsConstants::IP_LOOKUP_TEMPLATE;
		if($result['geoplugin_request']==$ip) {

            $ipLookUpTemplate = str_replace("{{status}}", $result["geoplugin_status"], $ipLookUpTemplate);
            $ipLookUpTemplate = str_replace("{{ip}}", $result["geoplugin_request"], $ipLookUpTemplate);
            $ipLookUpTemplate = str_replace("{{region}}", $result["geoplugin_region"], $ipLookUpTemplate);
            $ipLookUpTemplate = str_replace("{{country}}", $result["geoplugin_countryName"], $ipLookUpTemplate);
            $ipLookUpTemplate = str_replace("{{city}}", $result["geoplugin_city"], $ipLookUpTemplate);
            $ipLookUpTemplate = str_replace("{{continent}}", $result["geoplugin_continentName"], $ipLookUpTemplate);
            $ipLookUpTemplate = str_replace("{{latitude}}", $result["geoplugin_latitude"], $ipLookUpTemplate);
            $ipLookUpTemplate = str_replace("{{longitude}}", $result["geoplugin_longitude"], $ipLookUpTemplate);
            $ipLookUpTemplate = str_replace("{{timezone}}", $result["geoplugin_timezone"], $ipLookUpTemplate);
            $ipLookUpTemplate = str_replace("{{curreny_code}}", $result["geoplugin_currencyCode"], $ipLookUpTemplate);
            $ipLookUpTemplate = str_replace("{{curreny_symbol}}", $result["geoplugin_currencySymbol"], $ipLookUpTemplate);
            $ipLookUpTemplate = str_replace("{{per_dollar_value}}", $result["geoplugin_currencyConverter"], $ipLookUpTemplate);
            $ipLookUpTemplate = str_replace("{{hostname}}", $hostname, $ipLookUpTemplate);
            $ipLookUpTemplate = str_replace("{{offset}}", $timeoffset, $ipLookUpTemplate);

            $result['ipDetails'] = $ipLookUpTemplate;
        }else{
            $result["ipDetails"]["status"]="ERROR";
        }

		wp_send_json( $result );

    }
       private function whitelist_self()
	{
		global $moWpnsUtility;
		$moPluginsUtility = new MoWpnsHandler();
		$moPluginsUtility->whitelist_ip($moWpnsUtility->get_client_ip());
		wp_send_json('success');
	}

    private function wpns_plugin_notice()
	{

		update_site_option('malware_notification_option', 1);
        update_site_option('notice_dismiss_time',time());
		wp_send_json('success');
	}

	function wpns_dismiss_backup_notice(){
       update_site_option('backup_notification_option', 1);
       update_site_option('notice_dismiss_time',time());
       wp_send_json('success');
	}

	function wpns_dismiss_bruteforce_notice(){
      update_site_option('bruteforce_notification_option', 1);
       update_site_option('notice_dismiss_time',time());
       wp_send_json('success');
	}

	function wpns_dismiss_recaptcha_notice(){
      update_site_option('recaptcha_notification_option', 1);
       update_site_option('notice_dismiss_time',time());
       wp_send_json('success');
	}
	
	function wpns_plugin_warning_never_show_again(){
	 update_site_option('plugin_warning_never_show_again', 1);
	 wp_send_json('success');
	}

	function wpns_mo2f_banner_never_show_again(){
	 update_site_option('mo2f_banner_never_show_again', 1);
	 wp_send_json('success');
	}


	function wpns_dismiss_firewall_notice(){
       update_site_option('waf_notification_option', 1);
       update_site_option('notice_dismiss_time',time());
       wp_send_json('success');
	}
	private function wpns_sms_notice()
	{
		update_site_option('mo2f_wpns_sms_dismiss', time());
		wp_send_json('success');
	}
	private function wpns_email_notice()
	{
		update_site_option('mo2f_wpns_email_dismiss', time());
		wp_send_json('success');
	}
	private function wpns_sms_notice_always()
	{
		update_site_option('mo2f_wpns_donot_show_low_sms_notice', 1);
		wp_send_json('success');
	}
	private function wpns_email_notice_always()
	{
		update_site_option('mo2f_wpns_donot_show_low_email_notice', 1);
		wp_send_json('success');
	}
	private function mo2f_backup_codes_dismiss()
	{
		$user_id = get_current_user_id();
		update_user_meta($user_id, 'donot_show_backup_code_notice' , 1);
		wp_send_json('success');
	}

    

}new AjaxHandler;
