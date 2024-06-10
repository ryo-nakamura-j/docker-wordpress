<?php
class Mo2f_ajax_dashboard
{
	function __construct(){
		add_action( 'admin_init'  , array( $this, 'mo2f_switch_functions' ) );
	}

	public function mo2f_switch_functions(){
		if(isset($_POST) && isset($_POST['option'])){
			$tab_count= get_site_option('mo2f_tab_count', 0);
			if($tab_count == 5)
				update_site_option('mo_2f_switch_all', 1);
			elseif($tab_count == 0)
				update_site_option('mo_2f_switch_all', 0);
			$santizied_post=isset($_POST['switch_val'])? sanitize_text_field($_POST['switch_val']):null;
			switch(sanitize_text_field($_POST['option']))
			{
				case "tab_all_switch":
					$this->mo2f_handle_all_enable($santizied_post);
					break;
				case "tab_2fa_switch":
					$this->mo2f_handle_2fa_enable($santizied_post);
					break;
				case "tab_waf_switch":
					$this->mo2f_handle_waf_enable($santizied_post);
					break;
				case "tab_login_switch":
					$this->mo2f_handle_login_enable($santizied_post);
					break;
				case "tab_malware_switch":
					$this->mo2f_handle_malware_enable($santizied_post);
					break;
				case "tab_block_switch":
					$this->mo2f_handle_block_enable($santizied_post);
					break;
				
			}
		}
	}

	public function mo2f_handle_all_enable($POSTED){
		$this->mo2f_handle_waf_enable($POSTED);
		$this->mo2f_handle_login_enable($POSTED);
		$this->mo2f_handle_malware_enable($POSTED);
		$this->mo2f_handle_block_enable($POSTED);
		if($POSTED){
			update_option('mo_2f_switch_all',1);
			update_site_option('mo2f_tab_count', 5);
			do_action('wpns_show_message',MoWpnsMessages::showMessage('ALL_ENABLED'),'SUCCESS');
		}
		else{
			update_option('mo_2f_switch_all', 0);
			update_site_option('mo2f_tab_count', 0);
			do_action('wpns_show_message',MoWpnsMessages::showMessage('ALL_DISABLED'),'ERROR');
		}
	}

	public function mo2f_handle_2fa_enable($POSTED){
		global $Mo2fdbQueries;
		$user= wp_get_current_user();
		$user_id= $user->user_ID;
		if($POSTED){
			$Mo2fdbQueries->update_user_deails($user_id, array('mo_2factor_user_registration_status', 'MO_2_FACTOR_PLUGIN_SETTINGS'));
			if(sanitize_text_field($_POST['tab_2fa_switch']))
				do_action('wpns_show_message',MoWpnsMessages::showMessage('TWO_FACTOR_ENABLE'),'SUCCESS');
		}
		else{
			$Mo2fdbQueries->update_user_deails($user_id, array('mo_2factor_user_registration_status', 0));
			if(sanitize_text_field($_POST['tab_2fa_switch']))
				do_action('wpns_show_message',MoWpnsMessages::showMessage('TWO_FACTOR_DISABLE'),'ERROR');
		}
	}

	public function mo2f_handle_waf_enable($POSTED){
		if($POSTED){
			update_site_option('mo_2f_switch_waf', 1);
			update_site_option('mo2f_tab_count', get_site_option('mo2f_tab_count')+1);
			if (isset($_POST['option'] )) 
			{
				if(sanitize_text_field($_POST['option']) == 'tab_waf_switch')
				{
					do_action('wpns_show_message',MoWpnsMessages::showMessage('WAF_ENABLE'),'SUCCESS');
				}
			}
		}
		else{
			update_site_option('mo_2f_switch_waf', 0);
			update_site_option('mo2f_tab_count', get_site_option('mo2f_tab_count')-1);
			update_option('WAFEnabled', 0);
			update_option('WAF','wafDisable');
			update_site_option('Rate_limiting', 0);
			$dir_name 	=  dirname(dirname(dirname(dirname(dirname(__FILE__)))));
			$dir_name1  =  $dir_name.DIRECTORY_SEPARATOR.'.htaccess';
			$filePath 	= $dir_name.DIRECTORY_SEPARATOR.'mo-check.php';
			$filePath 	= str_replace('\\', '/', $filePath);
		 	$file 		=  file_get_contents($dir_name1);
		 	$cont 	 = PHP_EOL.'# BEGIN miniOrange WAF'.PHP_EOL;
		 	$cont 	.= 'php_value auto_prepend_file '.$filePath.PHP_EOL;
		 	$cont 	.= '# END miniOrange WAF'.PHP_EOL;
		 	$file =str_replace($cont,'',$file);
			file_put_contents($dir_name1, $file);
			if(sanitize_text_field($_POST['option']) == 'tab_waf_switch')
				do_action('wpns_show_message',MoWpnsMessages::showMessage('WAF_DISABLE'),'ERROR');
		}
	}

	public function mo2f_handle_login_enable($POSTED){
		if($POSTED){
			update_site_option('mo_2f_switch_loginspam', 1);
			update_site_option('mo2f_tab_count', get_site_option('mo2f_tab_count')+1);
			if (isset($_POST['option'] )) 
			{
			if(sanitize_text_field($_POST['option']) == 'tab_login_switch')
				do_action('wpns_show_message',MoWpnsMessages::showMessage('LOGIN_ENABLE'),'SUCCESS');
			}
		}
		else{
			update_site_option('mo_2f_switch_loginspam', 0);
			update_site_option('mo2f_tab_count', get_site_option('mo2f_tab_count')-1);
			update_site_option('mo2f_enable_brute_force', false);
			update_site_option('mo_wpns_activate_recaptcha', false);
			update_site_option('mo_wpns_activate_recaptcha_for_login', false);
			update_site_option('mo_wpns_activate_recaptcha_for_woocommerce_login', false);
			update_site_option('mo_wpns_activate_recaptcha_for_registration', false);
			update_site_option('mo_wpns_activate_recaptcha_for_woocommerce_registration', false);
			update_site_option('mo2f_enforce_strong_passswords', 0);
			update_site_option('mo_wpns_enable_fake_domain_blocking', false);
			update_site_option('mo_wpns_enable_advanced_user_verification', false);
			update_site_option('mo_wpns_enable_social_integration', false);
			update_site_option('mo2f_protect_wp_config', 0);
			update_site_option('mo2f_prevent_directory_browsing', 0);
			update_site_option('mo2f_disable_file_editing', 0);
			update_site_option('mo_wpns_enable_comment_spam_blocking', false);
			update_site_option('mo_wpns_enable_comment_recaptcha', false);
			update_site_option('mo2f_htaccess_file', 0);
			if(sanitize_text_field($_POST['option']) == 'tab_login_switch')
				do_action('wpns_show_message',MoWpnsMessages::showMessage('LOGIN_DISABLE'),'ERROR');
		}
	}

	public function mo2f_handle_malware_enable($POSTED){
		if($POSTED){
			update_site_option('mo_2f_switch_malware', 1);
			update_site_option('mo2f_tab_count', get_site_option('mo2f_tab_count')+1);
			if (isset($_POST['option'] )) 
			{
			if(sanitize_text_field($_POST['option']) == 'tab_malware_switch')
				do_action('wpns_show_message',MoWpnsMessages::showMessage('MALWARE_ENABLE'),'SUCCESS');
			}
		}else{
			update_site_option('mo_2f_switch_malware', 0);
			update_site_option('mo2f_tab_count', get_site_option('mo2f_tab_count')-1);
			if(sanitize_text_field($_POST['option']) == 'tab_malware_switch')
				do_action('wpns_show_message',MoWpnsMessages::showMessage('MALWARE_DISABLE'),'ERROR');
		}
	}

	public function mo2f_handle_block_enable($POSTED){
		if($POSTED){
			update_site_option('mo_2f_switch_adv_block', 1);
			update_site_option('mo2f_tab_count', get_site_option('mo2f_tab_count')+1);
			if (isset($_POST['option'] )) 
			{
			if(sanitize_text_field($_POST['option']) == 'tab_block_switch')
				do_action('wpns_show_message',MoWpnsMessages::showMessage('ADV_BLOCK_ENABLE'),'SUCCESS');
			}
		}
		else{
			update_site_option('mo_2f_switch_adv_block', 0);
			update_site_option('mo2f_tab_count', get_site_option('mo2f_tab_count')-1);
			update_site_option('mo_wpns_iprange_count', 0);
			update_site_option('mo_wpns_enable_htaccess_blocking', 0);
			update_site_option('mo_wpns_enable_user_agent_blocking', 0);
			update_site_option('mo_wpns_referrers', false);
			update_site_option('mo_wpns_countrycodes', false);
			if(sanitize_text_field($_POST['option']) == 'tab_block_switch')
				do_action('wpns_show_message',MoWpnsMessages::showMessage('ADV_BLOCK_DISABLE'),'ERROR');
		}
	}


}
new Mo2f_ajax_dashboard();
?>