<?php
/** Copyright (C) 2015  miniOrange

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>
* @package 		miniOrange OAuth
* @license		http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
*
**/


// need to have different classes here for each ipblocking, whitelisting, htaccess and transaction related functions
class MoWpnsHandler
{

	function mo_wpns_is_ip_blocked($ipAddress)
	{
		global $wpnsDbQueries;
		if(empty($ipAddress))
			return false;
		
		$user_count = $wpnsDbQueries->get_ip_blocked_count($ipAddress);
		
		if($user_count)
			$user_count = intval($user_count);
		if($user_count>0)
			return true;
		
		return false;
	}
	function get_blocked_attacks_count($attackName)
	{
		global $wpnsDbQueries;
		$attackCount = $wpnsDbQueries->get_blocked_attack_count($attackName);
		if($attackCount)
			$attackCount =  intval($attackCount);
		return $attackCount;
	}
	function get_blocked_countries()
	{
		$countrycodes 	= get_option('mo_wpns_countrycodes');
		$countries 		= explode(';', $countrycodes);
		return sizeof($countries)-1;
	}
	function get_blocked_ip_waf()
	{
		global $wpnsDbQueries;
		$ip_count = $wpnsDbQueries->get_total_blocked_ips_waf();
		if($ip_count)
			$ip_count = intval($ip_count);

		return $ip_count;
	}
	function get_manual_blocked_ip_count()
	{
		global $wpnsDbQueries;
		$ip_count = $wpnsDbQueries->get_total_manual_blocked_ips();
		if($ip_count)
			$ip_count = intval($ip_count);

		return $ip_count;
	}
	function get_blocked_attacks()
	{
		global $wpnsDbQueries;
		return $wpnsDbQueries->get_blocked_attack_list();	
	}
	function get_blocked_ips()
	{
		global $wpnsDbQueries;
		return $wpnsDbQueries->get_blocked_ip_list();
	}
	function get_blocked_sqli()
	{
		global $wpnsDbQueries;
		return $wpnsDbQueries->get_blocked_sqli_list();
	}
	function get_blocked_rfi()
	{
		global $wpnsDbQueries;
		return $wpnsDbQueries->get_blocked_rfi_list();	
	}
	function get_blocked_lfi()
	{
		global $wpnsDbQueries;
		return $wpnsDbQueries->get_blocked_lfi_list();
	}
	function get_blocked_rce()
	{
		global $wpnsDbQueries;
		return $wpnsDbQueries->get_blocked_rce_list();
	}
	function get_blocked_xss()
	{
		global $wpnsDbQueries;
		return $wpnsDbQueries->get_blocked_xss_list();	
	}
	
	function mo_wpns_block_ip($ipAddress, $reason, $permenently)
	{
		global $wpnsDbQueries;
		if(empty($ipAddress))
			return;
		if($this->mo_wpns_is_ip_blocked($ipAddress))
			return;
		$blocked_for_time = null;
		if(!$permenently && get_option('mo2f_time_of_blocking_type'))
		{
			$blocking_type = get_option('mo2f_time_of_blocking_type');
			$time_of_blocking_val = 3;
			if(get_option('mo2f_time_of_blocking_val'))
				$time_of_blocking_val = get_option('mo2f_time_of_blocking_val');
			if($blocking_type=="months")
				$blocked_for_time = current_time( 'timestamp' )+$time_of_blocking_val * 30 * 24 * 60 * 60;
			else if($blocking_type=="days")
				$blocked_for_time = current_time( 'timestamp' )+$time_of_blocking_val * 24 * 60 * 60;
			else if($blocking_type=="hours")
				$blocked_for_time = current_time( 'timestamp' )+$time_of_blocking_val * 60 * 60;
		}
		
		if(get_option('mo_wpns_enable_htaccess_blocking'))
		{
			$base = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
			$f = fopen($base.DIRECTORY_SEPARATOR.".htaccess", "a");
			fwrite($f, "\ndeny from ".trim($ipAddress));
			fclose($f);
		}
		
		$wpnsDbQueries->insert_blocked_ip($ipAddress, $reason,$blocked_for_time);
		
		//send notification
		global $moWpnsUtility;
		if(MoWpnsUtility::get_mo2f_db_option('mo_wpns_enable_ip_blocked_email_to_admin', 'get_option'))
			$moWpnsUtility->sendIpBlockedNotification($ipAddress,MoWpnsConstants::LOGIN_ATTEMPTS_EXCEEDED);
			
	}
	
	function unblock_ip_entry($entryid)
	{
		global $wpnsDbQueries;
		$myrows = $wpnsDbQueries->get_blocked_ip($entryid);
		if(count($myrows)>0)
			if(get_option('mo_wpns_enable_htaccess_blocking'))
			{
				$ip_address = $myrows[0]->ip_address;
				$base = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
				$hpath = $base.DIRECTORY_SEPARATOR.".htaccess";
				$contents = file_get_contents($hpath);
				if (strpos($contents, "\ndeny from ".trim($ip_address)) !== false)
				{
					$contents = str_replace("\ndeny from ".trim($ip_address), '', $contents);
					file_put_contents($hpath, $contents);
				}
			}
		
		$wpnsDbQueries->delete_blocked_ip($entryid);
	}
	
	function remove_htaccess_ips()
	{
		global $wpnsDbQueries;
		$myrows = $wpnsDbQueries->get_blocked_ip_list();
		$base = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
		$hpath = $base.DIRECTORY_SEPARATOR.".htaccess";
		$contents = file_get_contents($hpath);
		$changed = 0;
		foreach($myrows as $row)
		{
			$ip_address = $row->ip_address;
			if (strpos($contents, "\ndeny from ".trim($ip_address)) !== false) 
			{
				$contents = str_replace("\ndeny from ".trim($ip_address), '', $contents);
				$changed = 1;
			}
		}
		if($changed==1)
			file_put_contents($hpath, $contents);
	}
	
	function add_htaccess_ips()
	{
		global $wpnsDbQueries;
		$myrows = $wpnsDbQueries->get_blocked_ip_list();
		$base = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
		$hpath = $base.DIRECTORY_SEPARATOR.".htaccess";
		$contents = file_get_contents($hpath);
		$f = fopen($hpath, "a");
		foreach($myrows as $row)
		{
			$ip_address = $row->ip_address;
			if (strpos($contents, "\ndeny from ".trim($ip_address)) === false)
				fwrite($f, "\ndeny from ".trim($ip_address));
		}
		fclose($f);
	}
	
	
	function is_whitelisted($ipAddress)
	{
		global $wpnsDbQueries;
		$count = $wpnsDbQueries->get_whitelisted_ip_count($ipAddress);

		if(empty($ipAddress))
			return false;
		if($count)
			$count = intval($count);

		if($count>0)
			return true;
		return false;
	}
	
	function whitelist_ip($ipAddress)
	{
		global $wpnsDbQueries;	
		if(get_option('mo_wpns_enable_htaccess_blocking'))
		{
			$base = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
			$hpath = $base.DIRECTORY_SEPARATOR.".htaccess";
			$contents = file_get_contents($hpath);
			if (strpos($contents, "\ndeny from ".trim($ipAddress)) !== false)
			{
				$contents = str_replace("\ndeny from ".trim($ipAddress), '', $contents);
				file_put_contents($hpath, $contents);
			}
		}
		
		if(empty($ipAddress))
			return;
		if($this->is_whitelisted($ipAddress))
			return;

		$wpnsDbQueries->insert_whitelisted_ip($ipAddress);
	}

	function update_htaccess_configuration()
	{
		$base = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
		$htaccesspath = $base.DIRECTORY_SEPARATOR.".htaccess";
		if(!file_exists($htaccesspath)){
			$f = fopen($base.DIRECTORY_SEPARATOR.".htaccess", "a");
			fwrite($f, "# BEGIN WordPress\r\n<IfModule mod_rewrite.c>\r\nRewriteEngine On\r\nRewriteBase /\r\nRewriteRule ^index\.php$ - [L]\r\nRewriteCond %{REQUEST_FILENAME} !-f\r\nRewriteCond %{REQUEST_FILENAME} !-d\r\nRewriteRule . /index.php [L]\r\n</IfModule>\r\n# END WordPress");
			fclose($f);
		}
		$this->change_wp_config_protection($base);
		$this->change_content_protection($base);
		$this->mo2f_change_htaccess_file($htaccesspath);
	}

	function mo2f_change_htaccess_file($htaccesspath){
		$contents = file_get_contents($htaccesspath);
		if (strpos($contents, "\r\n<files ~ \"^.*\.([Hh][Tt][Aa])\">\r\norder allow,deny\r\ndeny from all\r\nsatisfy all\r\n</files>") !== false) {
			if(!get_option('mo2f_htaccess_file')){
				$contents = str_replace("\r\n<files ~ \"^.*\.([Hh][Tt][Aa])\">\r\norder allow,deny\r\ndeny from all\r\nsatisfy all\r\n</files>", '', $contents);
				file_put_contents($htaccesspath, $contents);
			}
		} else {
			if(get_option('mo2f_htaccess_file')){
				$f = fopen($htaccesspath, "a");
				fwrite($f, "\r\n<files ~ \"^.*\.([Hh][Tt][Aa])\">\r\norder allow,deny\r\ndeny from all\r\nsatisfy all\r\n</files>");
				fclose($f);
			}
		}	
	}

	function change_wp_config_protection($base)
	{
		$htaccesspath = $base.DIRECTORY_SEPARATOR.".htaccess";
		$contents = file_get_contents($htaccesspath);
		if (strpos($contents, "\n<files wp-config.php>\norder allow,deny\ndeny from all\n</files>") !== false) 
		{
			if(!get_option('mo2f_protect_wp_config'))
			{
				$contents = str_replace("\n<files wp-config.php>\norder allow,deny\ndeny from all\n</files>", '', $contents);
				file_put_contents($htaccesspath, $contents);
			}
		} 
		else
		{
			if(get_option('mo2f_protect_wp_config'))
			{
				$f = fopen($base.DIRECTORY_SEPARATOR.".htaccess", "a");
				fwrite($f, "\n<files wp-config.php>\norder allow,deny\ndeny from all\n</files>");
				fclose($f);
			}
		}
	}
	
	function change_content_protection($base)
	{
		$htaccesspath = $base.DIRECTORY_SEPARATOR.".htaccess";
		$contents = file_get_contents($htaccesspath);
		if (strpos($contents, "\nOptions All -Indexes") !== false)
		{
			if(!get_option('mo2f_prevent_directory_browsing'))
			{
				$contents = str_replace("\nOptions All -Indexes", '', $contents);
				file_put_contents($htaccesspath, $contents);
			}
		} 
		else
		{
			if(get_option('mo2f_prevent_directory_browsing'))
			{
				$f = fopen($base.DIRECTORY_SEPARATOR.".htaccess", "a");
				fwrite($f, "\nOptions All -Indexes");
				fclose($f);
			}
		}
	}
	
	function remove_whitelist_entry($entryid)
	{
		global $wpnsDbQueries;
		$wpnsDbQueries->delete_whitelisted_ip($entryid);
	}
	
	function get_whitelisted_ips()
	{
		global $wpnsDbQueries;
		return $wpnsDbQueries->get_whitelisted_ips_list();
	}
	
	function is_email_sent_to_user($username, $ipAddress)
	{
		global $wpnsDbQueries;
		if(empty($ipAddress))
			return false;
		$sent_count = $wpnsDbQueries->get_email_audit_count($ipAddress,$username);
		if($sent_count)
			$sent_count = intval($sent_count);
		if($sent_count>0)
			return true;
		return false;
	}
	
	function audit_email_notification_sent_to_user($username, $ipAddress, $reason)
	{
		if(empty($ipAddress) || empty($username))
			return;
		global $wpnsDbQueries;
		$wpnsDbQueries->insert_email_audit($ipAddress,$username,$reason);
	}
	
	function add_transactions($ipAddress, $username, $type, $status, $url=null)
	{
		global $wpnsDbQueries;
		$wpnsDbQueries->insert_transaction_audit($ipAddress, $username, $type, $status, $url);
	}

	function get_login_transaction_report()
	{
		global $wpnsDbQueries;
		return $wpnsDbQueries->get_login_transaction_report();
	}
	
	function get_error_transaction_report()
	{
		global $wpnsDbQueries;
		return $wpnsDbQueries->get_error_transaction_report();
	}


	function get_all_transactions()
	{
		global $wpnsDbQueries;
		return $wpnsDbQueries->get_transasction_list();
	}
	
	function move_failed_transactions_to_past_failed($ipAddress)
	{
		global $wpnsDbQueries;
		$wpnsDbQueries->update_transaction_table(array('status'=>MoWpnsConstants::FAILED,'ip_address'=>$ipAddress),
			array('status'=>MoWpnsConstants::PAST_FAILED));
	}
	
	function remove_failed_transactions($ipAddress)
	{
		global $wpnsDbQueries;
		$wpnsDbQueries->delete_transaction($ipAddress);	
	}
	
	function get_failed_attempts_count($ipAddress)
	{
		global $wpnsDbQueries;
		$count = $wpnsDbQueries->get_failed_transaction_count($ipAddress);
		if($count)
		{
			$count = intval($count);
			return $count;
		}
		return 0;
	}
	
	function is_ip_blocked_in_anyway($userIp)
	{
		$isBlocked = false;
		if($this->mo_wpns_is_ip_blocked($userIp))
			$isBlocked = true;
		else if($this->is_ip_range_blocked($userIp))
			$isBlocked = true;
		else if($this->is_browser_blocked())
			$isBlocked = true;
		else if($this->is_country_blocked($userIp))
			$isBlocked = true;
		else if($this->is_referer_blocked())
			$isBlocked = true;

		return $isBlocked;
	}

	function is_ip_range_blocked($userIp)
	{
		if(empty($userIp))
			return false;
		$range_count = 0;
		if(is_numeric(get_option('mo_wpns_iprange_count')))
			$range_count = intval(get_option('mo_wpns_iprange_count'));
		for($i = 1 ; $i <= $range_count ; $i++){ 
			$blockedrange  = get_option('mo_wpns_iprange_range_'.$i);
			$rangearray = explode("-",$blockedrange);
			if(sizeof($rangearray)==2){
				$lowip = ip2long(trim($rangearray[0]));
				$highip = ip2long(trim($rangearray[1]));
				if(ip2long($userIp)>=$lowip && ip2long($userIp)<=$highip){
					$mo_wpns_config = new MoWpnsHandler();
					$mo_wpns_config->mo_wpns_block_ip($userIp, MoWpnsConstants::IP_RANGE_BLOCKING, true);
					return true;
				}
			}
		}
		return false;
	}
	
	
	function is_browser_blocked()
	{
		global $moWpnsUtility;
		if(get_option( 'mo_wpns_enable_user_agent_blocking'))
		{			
			$current_browser = $moWpnsUtility->getCurrentBrowser();
			if(get_option('mo_wpns_block_chrome') && $current_browser=='chrome')
				return true;
			else if(get_option('mo_wpns_block_firefox') && $current_browser=='firefox')
				return true;
			else if(get_option('mo_wpns_block_ie') && $current_browser=='ie')
				return true;
			else if(get_option('mo_wpns_block_opera') && $current_browser=='opera')
				return true;
			else if(get_option('mo_wpns_block_safari')&& $current_browser=='safari')
				return true;
			else if(get_option('mo_wpns_block_edge') && $current_browser=='edge')
				return true;
		}
		return false;
	}
	
	function is_country_blocked($userIp)
	{                   
		$countrycodes = get_option('mo_wpns_countrycodes');
		if($countrycodes && !empty($countrycodes)){
			$ip_data=wp_remote_get("http://www.geoplugin.net/json.gp?ip=".$userIp);
			if( !is_wp_error( $ip_data ) ) {
				$ip_data = json_decode(wp_remote_retrieve_body( $ip_data), true);
			}
			
			$ip_data_countryname = is_object($ip_data) ? $ip_data->geoplugin_countryName : (isset($ip_data['geoplugin_countryName']) ? $ip_data['geoplugin_countryName'] : null);
			
			if($ip_data  && $ip_data_countryname != null){
				$country_code = is_object($ip_data) ? $ip_data->geoplugin_countryCode : (isset($ip_data['geoplugin_countryCode']) ? $ip_data['geoplugin_countryCode'] : null);
				if(!empty($country_code)){
					$countrycodes = get_option('mo_wpns_countrycodes');
					$codes = explode(";", $countrycodes);
					foreach($codes as $code){
						if(!empty($code) && strcasecmp($code,$country_code)==0)
						return true;
					}
				}
			}
		}
		return false;
	}

	function lockedOutlink(){
		if(MO2F_IS_ONPREM){
			return MoWpnsConstants::OnPremiseLockedOut;
		}else{
			return MoWpnsConstants::CloudLockedOut;
		}
	}

	function is_referer_blocked()
	{
		if(isset($_SERVER['HTTP_REFERER']) && !empty($_SERVER['HTTP_REFERER']) && get_option('mo_wpns_referrers')){
			$userreferer = sanitize_text_field($_SERVER['HTTP_REFERER']);
			$referrers = explode(";",get_option('mo_wpns_referrers'));
			foreach($referrers as $referrer){
				if(!empty($referrer) && strpos(strtolower($userreferer), strtolower($referrer)) !== false){
					return true;
				}
			}
		}
		return false;
	}
	
} ?>