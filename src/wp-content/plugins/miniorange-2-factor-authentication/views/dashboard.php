<?php
global $moWpnsUtility,$mo2f_dirName;
include_once $mo2f_dirName . 'views'.DIRECTORY_SEPARATOR.'navbar.php';
add_action('admin_footer','mo_2fa_dashboard_switch');
$two_fa_toggle = get_site_option("mo2f_toggle");
$two_fa_on= get_site_option("mo_2f_switch_2fa")?"checked":"";
$all_on= get_site_option("mo_2f_switch_all")?"checked":"";
$waf_on= get_site_option("mo_2f_switch_waf")?"checked":"";
$login_spam_on= get_site_option("mo_2f_switch_loginspam")?"checked":"";
$malware_on= get_site_option("mo_2f_switch_malware")?"checked":"";
$adv_block_on= get_site_option("mo_2f_switch_adv_block")?"checked":"";
echo '<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
	<div class="mo2f_table_layout_method">
		
		<div class="mo_wpns_dashboard_layout" >
			<center>
				<div class ="mo_wpns_inside_dashboard_layout "><p style="font-weight: bold;">Failed Login</p><p class ="wpns_font_size mo_wpns_dashboard_text" >'.esc_attr($wpns_attacks_blocked).'</p>
				<a class="mo_wpns_button_info_tab" onclick="clear_Local_storage()" style="color:white;" href="admin.php?page=mo_2fa_reports&tab=default&view">Details</a>
				
				</div>


				<div class ="mo_wpns_inside_dashboard_layout"><p style="font-weight: bold;">Attacks Blocked </p><p class ="wpns_font_size mo_wpns_dashboard_text">'.esc_attr($totalAttacks).'</p><a class="mo_wpns_button_info_tab" style="color:white;" onclick="clear_Local_storage()" href="admin.php?page=mo_2fa_waf">Details</a></div>


				<div class ="mo_wpns_inside_dashboard_layout"><p style="font-weight: bold;">Blocked IPs</p><p class ="wpns_font_size mo_wpns_dashboard_text">'.esc_attr($wpns_count_ips_blocked).'</p><a class="mo_wpns_button_info_tab" style="color:white;" onclick="clear_Local_storage()" href="admin.php?page=mo_2fa_advancedblocking">Details</a></div>

				<div class ="mo_wpns_inside_dashboard_layout"><p style="font-weight: bold;">Infected Files</p><p class ="wpns_font_size mo_wpns_dashboard_text" >'.esc_attr($total_malicious).'</p><a class="mo_wpns_button_info_tab" style="color:white;" onclick="clear_Local_storage()" href="admin.php?page=mo_2fa_malwarescan">Details</a></div>

				<div class ="mo_wpns_inside_dashboard_layout"><p style="font-weight: bold;">White-listed IPs</p><p class ="wpns_font_size mo_wpns_dashboard_text">'.esc_attr($wpns_count_ips_whitelisted).'</p><a class="mo_wpns_button_info_tab" style="color:white;" onclick="clear_Local_storage()" href="admin.php?page=mo_2fa_advancedblocking">Details</a></div>
				
				
			</center>
		</div>

	
			<div style="padding: 0px 0px 0px 5px;text-align:center" >
			<form name="tab_all" id="tab_all" method="post">
			<h3 style="text-align:center;margin-right:4.5%;">Enable All
			<label class="mo_wpns_switch">
			<input type="hidden" name="option" value="tab_all_switch"/>
			<input type=checkbox id="switch_all" name="switch_val" value="1" '.esc_html($all_on).' />
			<span class="mo_wpns_slider mo_wpns_round"></span>
			</label>
			</h3>
			</form>
			</div>
		<div style="display:flex; flex-direction:column	">	
		 <div style="display:flex;justify-content:center">
			<div class="mo_wpns_small_layout">
				<form name="tab_2fa" id="tab_2fa" method="post">
				<h3>Two Factor Authentication ';
				if($two_fa_toggle){
					echo ' <label class="mo_wpns_switch" style="float: right">
					<input type="hidden" name="option" value="tab_2fa_switch"/>
					<input type=checkbox id="switch_2fa" name="switch_val" value="1" '.esc_html($two_fa_on).' />
					<span class="mo_wpns_slider mo_wpns_round"></span>
					</label>';
				}else{
					echo ' <b style="color:#3dd23d;">(Enabled)</b>';
				}
			echo ' </h3>
				</form>
				<br>
				<div style="text-align:justify;">
				Two Factor Authentication adds an extra security layer for verification that involve <b>google authenticator, other application based authentication,  Soft Token, Push Notification, USB based Hardware token, Security Questions, One time passcodes (OTP) over SMS, OTP over Email </b> etc.
				</div>
				<br>
				<a class="button button-primary button-large" href="'.esc_url($two_fa).'">Settings</a>


			</div>
			<div class="mo_wpns_small_layout">
				<form name="tab_waf" id="tab_waf" method="post">
				<h3 align="center">Web Application Firewall (WAF)
				<label class="mo_wpns_switch" style="float: right">
				<input type="hidden" name="option" value="tab_waf_switch"/>
				<input type=checkbox id="switch_WAF" name="switch_val" value="1" '.esc_html($waf_on).' />
				<span class="mo_wpns_slider mo_wpns_round"></span>
				</label>
				</h3>
				</form>
				<br>
				<div style="text-align:justify;">
				Web Application Firewall protects your website from several website attacks such as <b>SQL Injection(SQLI), Cross Site Scripting(XSS), Remote File Inclusion</b> and many more cyber attacks.It also protects your website from <b>critical attacks</b> such as <b>Dos and DDos attacks.</b><br>
				</div>
				<br><br>
				<a class="button button-primary button-large" href="'.esc_url($waf).'">Settings</a>
			</div>
			
			<div class="mo_wpns_small_layout">
				<form name="tab_login" id="tab_login" method="post">
				<h3 align="center">Login and Spam
				<label class="mo_wpns_switch" style="float: right">
				<input type="hidden" name="option" value="tab_login_switch"/>
				 <input type=checkbox id="switch_login_spam" name="switch_val" value="1" ' .esc_html($login_spam_on). ' />
				 <span class="mo_wpns_slider mo_wpns_round"></span>
				</label>
				</h3>
				</form>
				<br>
				<div style="text-align:justify;">
				Firewall protects the whole website.
				If you just want to prevent your login page from <b> password guessing attacks</b> by humans or by bots.
				 We have features such as <b> Brute Force,Enforcing Strong Password,Custom Login Page URL,Recaptcha </b> etc.
				 <br><br>
				 </div>
				<a class="button button-primary button-large" href="'.esc_url($login_and_spam).'">Settings</a>
			</div>
			</div>
			<div style="display:flex;justify-content:center">
			<div class="mo_wpns_small_layout">
				<form name="tab_malware" id="tab_malware" method="post">
				<h3>Malware Scan
				<label class="mo_wpns_switch" style="float: right">
				<input type="hidden" name="option" value="tab_malware_switch"/>
				 <input type=checkbox id="switch_malware" name="switch_val" value="1" ' .esc_html($malware_on). ' />
				 <span class="mo_wpns_slider mo_wpns_round"></span>
				</label>
				</h3>
				</form>
				<br>
				<div style="text-align:justify;">
				 A malware scanner / detector or virus scanner is a <b>software that detects the malware</b> into the system. It detects different kinds of malware and categories based on the <b>strength of vulnerability or harmfulness.</b>
				 <br><br>
				 </div>
				<a class="button button-primary button-large" href="'.esc_url($scan_url).'">Settings</a>
			</div>
		
			<div class="mo_wpns_small_layout">
				<form name="tab_adv_block" id="tab_adv_block" method="post">
				<h3>IP Blocking
				<label class="mo_wpns_switch" style="float: right">
				<input type="hidden" name="option" value="tab_block_switch"/>
				 <input type=checkbox id="switch_adv_block" name="switch_val" value="1" ' .esc_html($adv_block_on). '/>
				 <span class="mo_wpns_slider mo_wpns_round"></span>
				</label>
				</h3>
				</form>
				<br>
				<div style="text-align:justify;">
				In IP blocking we have features like <b> Country Blocking, IP range Blocking , Browser blocking </b> and other options you can set up specifically according to your needs 
				<br><br><br>
				</div>
				<a class="button button-primary button-large" href="'.esc_url($advance_block).'">Settings</a>
			</div>
			</div>
			<div style="display:flex;justify-content:center">
		    <div class="mo_wpns_small_layout">
		    	<form name="tab_report" id="tab_report" method="post">
				<h3>Reports
				
				</h3>
				</form>
				<br>
				<div style="text-align:justify;">
                Track users <b>login activity</b> on your website. You can also <b>track 404 error</b> so that if anyone tries to access it too many times you can take action.<br>
                </div><br>
                <a class="button button-primary button-large" href="'.esc_url($reports_url).'">Settings</a>
			</div>

			<div class="mo_wpns_small_layout">
				<form name="tab_notif" id="tab_notif" method="post">
				<h3>Notification
				
				</h3>
				</form>
				<br>
				<div style="text-align:justify;">
				Get <b>Notified realtime</b> about any <b>IP getting Blocked.</b> With that, also get informed about any <b>unusual activities</b> detected by miniOrange.<br><br>
				</div><br>
				<a class="button button-primary button-large" href="'.esc_url($notif_url).'">Settings</a>

			</div>
			</div>
			</div>
		
	</div>
	<script>
	function clear_Local_storage(){
	localStorage.clear(); 
	}
	</script>';

function mo_2fa_dashboard_switch(){
	if ( ('admin.php' != basename( $_SERVER['PHP_SELF'] )) || (sanitize_text_field($_GET['page']) != 'mo_2fa_dashboard') ) {
        return;
    }
?>
	<script>
		jQuery('#mo_2fa_dashboard').addClass('nav-tab-active');


		jQuery(document).ready(function(){
			jQuery("#switch_2fa").click(function(){
				jQuery("#tab_2fa").submit();
			});

			jQuery("#switch_all").click(function(){
				jQuery("#tab_all").submit();
			});

			jQuery("#switch_WAF").click(function(){
				jQuery("#tab_waf").submit();
			});

			jQuery("#switch_login_spam").click(function(){
				jQuery("#tab_login").submit();
			});

			jQuery("#switch_backup").click(function(){
				jQuery("#tab_backup").submit();
			});

			jQuery("#switch_malware").click(function(){
				jQuery("#tab_malware").submit();
			});

			jQuery("#switch_adv_block").click(function(){
				jQuery("#tab_adv_block").submit();
			});

			jQuery("#switch_reports").click(function(){
				jQuery("#tab_report").submit();
			});

		});
	</script>
<?php
}
?>