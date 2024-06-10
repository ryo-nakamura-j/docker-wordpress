<?php
global $mo2f_dirName;
$security_features_nonce = wp_create_nonce('mo_2fa_security_features_nonce');

	$user = wp_get_current_user();
	$userID = wp_get_current_user()->ID;
	$onprem_admin = get_option('mo2f_onprem_admin');
	$roles = ( array ) $user->roles;
	$is_onprem = MO2F_IS_ONPREM;
        $flag  = 0;
  		foreach ( $roles as $role ) {
            if(get_option('mo2fa_'.$role)=='1')
            	$flag=1;
        }
    if(get_transient('ip_whitelisted') && current_user_can('administrator')){
        echo MoWpnsMessages::showMessage('ADMIN_IP_WHITELISTED');
    }
	if(!$safe)
	{
		if (MoWpnsUtility::get_mo2f_db_option('mo_wpns_2fa_with_network_security', 'site_option') && current_user_can('administrator')) 
		{
			echo MoWpnsMessages::showMessage('WHITELIST_SELF');		
		}
	}


	if((!get_user_meta($userID, 'mo_backup_code_generated', true) || ($backup_codes_remaining == 5 && !get_user_meta($userID, 'mo_backup_code_downloaded', true))) && $mo2f_two_fa_method != '' && !get_user_meta($userID, 'donot_show_backup_code_notice', true)){
		echo MoWpnsMessages::showMessage('GET_BACKUP_CODES');
	}
?>
<?php
if( isset( $_GET[ 'page' ]) && sanitize_text_field($_GET['page']) != 'mo_2fa_upgrade') 
	{	
			echo'<div style="display:flex; flex-direction:column; margin-left:-20px;">
			<div class="wrap mo2f-header">';
				
				$date1 = "2022-01-10";
				$dateTimestamp1 = strtotime($date1);

				$date2 = date("Y-m-d");
				$dateTimestamp2 = strtotime($date2);

				if($dateTimestamp2<=$dateTimestamp1 && ($userID == $onprem_admin) && !get_site_option("mo2f_banner_never_show_again"))
				{
					echo'<div class="mo2f_offer_main_div">

					

					<div class="mo2f_offer_first_section">
                        <p class="mo2f_offer_christmas">CHRISTMAS</p>
                        <h3 class= "mo2fa_hr_line"><span>&</span></h3>
                        <p class="mo2f_offer_cyber">NEW YEAR&nbsp;<spn style="color:white;">SALE</span></p>
                    </div>

					<div class="mo2f_offer_middle_section">
						<p class="mo2f_offer_get_upto"><span style="font-size: 30px;">GET UPTO <span style="color: white;font-size: larger; font-weight:bold">50%</span> OFF ON PREMIUM PLUGINS</p><br>
						<p class="mo2f_offer_valid">Offer valid for limited period only!</p>
					</div>

					<div id="mo2f_offer_last_section" class="mo2f_offer_last_section"><button class="mo2f_banner_never_show_again mo2f_close">CLOSE <span class=" mo2f_cross">X</span></button><a class="mo2f_offer_contact_us" href="'.esc_url($request_offer_url).'">Contact Us</a></p></div>

					</div><br><br>';
				}
				echo' <div class="mo2f-admin-options"> <div> <img width="50" height="50" src="'.esc_url($logo_url).'"></div>';
				
				if(!current_user_can('administrator')){
					echo' <div><h3 style="padding:0">miniOrange 2 Factor Authentication</h3></div>';
				}

				if(current_user_can('administrator')){
					echo'
						<a class="add-new-h2"  href="'.esc_url($profile_url).'">My Account</a>
						<a class="add-new-h2"  href="'.esc_url($help_url).'">FAQs</a>
						<a class="add-new-h2"  href="'.esc_url($addons_url).'">AddOns Plans</a>
						<a class="add-new-h2" 
							style="background-color:#ffcc44"
							id ="mo_2fa_upgrade_tour" href="'.esc_url($upgrade_url).'">See Plans and Pricing</a>
						
						</div>';

							if(get_site_option("mo_wpns_2fa_with_network_security") || get_site_option("mo2f_is_old_customer"))	
							{  
								update_site_option("mo2f_is_old_customer",1);

								echo'	<form id="mo_wpns_2fa_with_network_security" method="post" action="">
								<div class="mo2f-security-toggle"> 

								
								<input type="hidden" name="mo_security_features_nonce" value="'.esc_html($security_features_nonce).'"/>

									<input type="hidden" name="option" value="mo_wpns_2fa_with_network_security">
									<div>2FA + Website Security
									<span>
										<label class="mo_wpns_switch">
										<input type="checkbox" name="mo_wpns_2fa_with_network_security" '.esc_html($network_security_features).'  onchange="document.getElementById(\'mo_wpns_2fa_with_network_security\').submit();"> 
										<span class="mo_wpns_slider mo_wpns_round"></span>
										</label>
									</span>
									</div>
									
									
									</div>
									</form>';
							}
				}
					
					echo'</div></div>';
					echo '<div id = "wpns_nav_message"></div>';
				
?>


		<?php 
		if(MoWpnsUtility::get_mo2f_db_option('mo_wpns_2fa_with_network_security', 'get_option') && current_user_can('administrator')){ ?>
			<?php if(sanitize_text_field($_GET['page']) != 'mo_2fa_troubleshooting' && sanitize_text_field($_GET['page']) != 'mo_2fa_addons' && sanitize_text_field($_GET['page']) != 'mo_2fa_account'){ ?>
				<div class="nav-tab-wrapper">
					<?php
								echo '<a id="mo_2fa_dashboard"  class="nav-tab" href="'.esc_url($dashboard_url).'" >Dashboard</a>';
								
								echo '<a id="mo_2fa_2fa" class="nav-tab" href="'.esc_url($two_fa).'" >Two Factor</a>';	
						
								echo '<a id="mo_2fa_waf" class="nav-tab"  href="'.esc_url($waf).'" >Firewall</a>';
				
								echo '<a id="login_spam_tab" class="nav-tab"  href="'.esc_url($login_and_spam).'" >Login and Spam</a>';
							
							
							
								echo '<a id="malware_tab" class="nav-tab"  href="'.esc_url($scan_url).'">Malware Scan</a>';
							
								echo '<a id="adv_block_tab" class="nav-tab"  href="'.esc_url($advance_block).'">IP Blocking</a>';
						?>
				</div>
<?php 
		}
	}
}
?>

