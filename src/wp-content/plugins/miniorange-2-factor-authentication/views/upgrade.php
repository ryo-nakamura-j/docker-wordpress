<?php
	global $Mo2fdbQueries,$mainDir;
	$user = wp_get_current_user();
	$is_NC = MoWpnsUtility::get_mo2f_db_option('mo2f_is_NC', 'get_option');
	$is_customer_registered = get_option('mo_2factor_user_registration_status') == 'MO_2_FACTOR_PLUGIN_SETTINGS';

	$mo2f_feature_set = array(
		"Roles Based and User Based 2fa",
		"Role based Authentication Methods",
		"Force Two Factor",
		"Verification during 2FA Registration",
		"Language Translation Support",
		"Password Less Login",
		"Backup Methods",
		"Role based redirection",
		"Custom SMS Gateway",
		"App Specific Password from mobile Apps",
		"Brute Force Protection",
		"IP Blocking",
		"Monitoring",
		"Strong Password",
		"File Protection"
	);

	$mo2f_addons_set		=	array(
		"RBA & Trusted Devices Management",
		"Personalization",		                 
		"Short Codes"  
	);
	$mo2f_addons           	= array(
		"RBA & Trusted Devices Management" 	=> array( true, true,  false, true ),
		"Personalization"					=> array( true, true,  false, true ),
		"Short Codes"						=> array( true, true,  false, true )
	);
	$mo2f_addons_description_set	=array(
		"Remember Device, Set Device Limit for the users to login, IP Restriction: Limit users to login from specific IPs.",
		"Custom UI of 2FA popups Custom Email and SMS Templates, Customize 'powered by' Logo, Customize Plugin Icon, Customize Plugin Name",
		"Option to turn on/off 2-factor by user, Option to configure the Google Authenticator and Security Questions by user, Option to 'Enable Remember Device' from a custom login form, On-Demand ShortCodes for specific fuctionalities ( like for enabling 2FA for specific pages)",
	);
if (sanitize_text_field($_GET['page']) == 'mo_2fa_upgrade') {
	?><br><br><?php
}
echo '
<a class="mo2f_back_button" style="font-size: 16px; color: #000;" href="'.esc_url($two_fa).'"><span class="dashicons dashicons-arrow-left-alt" style="vertical-align: bottom;"></span> Back To Plugin Configuration</a>';
?>
<br><br>

<?php
	wp_register_style('mo2f_upgrade_css',$mainDir.'/includes/css/upgrade.css',[],MO2F_VERSION );
	wp_register_style('mo2f_font_awesome','https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css',[],MO2F_VERSION);
	wp_enqueue_style('mo2f_upgrade_css' );
	wp_enqueue_style('mo2f_font_awesome' );
?>

	<?php 
	if( get_option("mo_wpns_2fa_with_network_security"))
		{
			?>
			<div class="mo_upgrade_toggle">
				<p class="mo_upgrade_toggle_2fa">
				<input type="radio" name="sitetype" value="Recharge" id="mo2f_2fa_plans" onclick="show_2fa_plans();" style="display: none;">
				<label for="mo2f_2fa_plans" class="mo2f_upgrade_toggle_lable" id="mo_2fa_lite_licensing_plans_title" style="display: none;">&nbsp;&nbsp;&nbsp;2-Factor Authentication</label>
				<label for="mo2f_2fa_plans" class="mo2f_upgrade_toggle_lable mo2f_active_plan" id="mo_2fa_lite_licensing_plans_title1" style="display: block;">&nbsp;&nbsp;&nbsp;2-Factor Authentication</label>
				<input type="radio" name="sitetype" value="Recharge" id="mo2f_ns_plans" onclick="mo_ns_show_plans();" style="display: none;">
				<label for="mo2f_ns_plans" class="mo2f_upgrade_toggle_lable" id="mo2f_ns_licensing_plans_title">Website Security</label>
				<label for="mo2f_ns_plans" class="mo2f_upgrade_toggle_lable mo2f_active_plan" id="mo_ns_licensing_plans_title1" style="display: none;">Website Security</label>
				</p>
			</div>
			<?php
		}
?>
<span class="cd-switch"></span>



<div class="mo2f-pp-row"  id="mo2f_twofa_plans">
		
		

		<div class="mo2f-text-center mo2f-upper-row">
				   <div class="mo2f-cloud-onprem-solution">

							<div class="mo2f-col-md-6 mo2f-cloud-solution-ribbon">
									<div class="mo2f-ribbon-title">Cloud Solution Plans</div>
									<p>Synchronize and use same 2FA method across multiple websites.</p> 
							</div>

							<div class="mo2f-col-md-6 mo2f-on-prem-ribbon">
									<div class="mo2f-ribbon-title">On Premise Solution Plans</div>
									<p>Users' data is stored on your organization's premises.</p>
							</div>

					</div>
					<div class="mo2f-cloud-solution-ribbon-rpn">
						<div class="mo2f-ribbon-title">Cloud Solution Plans</div>
                            <p>Synchronize and use same 2FA method across multiple websites.</p>
                    </div>
		</div>

<div class="mo2f-text-center  mo2f-reg-plans-2fa mo2f-single-site mo2f-single-site-rot" id="mo2fa-plan-name">
			<div id="mo2f-hover-2fa-1" class="mo2f-col-md-4 mo2f-hover-2fa mo2f-hover-2fa1">
                <h3>BASIC </h3>
			    
                <div class="mo2f-price-list" class="mo2f-list-border" id="mo2f-list-margin">
				<div id="mo2f-basic-plan-div" class="mo2f_price_list_border">
                    <li class="mo2f-choose-plan-2fa mo2f-text-left">
                        <i class="fas fa-arrow-circle-right mo2f-text-success"></i><span>Install On Unlimited Websites</span>
                    </li>

				
                   
                    <li class="mo2f-choose-plan-2fa mo2f-text-left">
						<div class="mo2fa_15_tooltip_methodlist">
							<i class="fas fa-arrow-circle-right mo2f-text-success"></i>
							<span>TOTP Based Methods (2FA code via mobile app)</span> 
							      <i class="fa fa-info-circle fa-xs" aria-hidden="true"></i>
							      <span class="mo2fa_methodlist">
                              
											Google Authenticator <br>
											Authy Authenticator <br>
											Microsoft Authenticator <br>
											LastPass Authenticator<br>
											FreeOTP Authenticator<br>
											Duo Mobile Authenticator <br>
                              
                              
                            </span>
						</div>
					</li> 
					



                  
                    <li class="mo2f-choose-plan-2fa mo2f-text-left"><i class="fas fa-arrow-circle-right mo2f-text-success"></i><span >2FA Code Over Email/Email Verification (Charges Apply)</span></li>
                    <li class="mo2f-choose-plan-2fa mo2f-text-left"><i class="fas fa-arrow-circle-right mo2f-text-success"></i><span >2FA Code Via SMS (Charges Apply) </span></li>
                    <li class="mo2f-choose-plan-2fa mo2f-text-left"><i class="fas fa-arrow-circle-right mo2f-text-success"></i><span >Enforce 2FA For Users</span></li>
                    <li class="mo2f-choose-plan-2fa mo2f-text-left"><i class="fas fa-arrow-circle-right mo2f-text-success"></i><span >Redirection To Custom Url After 2FA</span></li>
                    <li class="mo2f-choose-plan-2fa mo2f-text-left"><i class="fas fa-arrow-circle-right mo2f-text-success"></i><span >Role-Based 2FA</span></li>
                    <li class="mo2f-choose-plan-2fa mo2f-text-left"><i class="fas fa-arrow-circle-right mo2f-text-success"></i><span >Backup Login Method</span></li>

                </div>
	          </div>
				<p class="mo2f-mt"><span class="mo2f-display-1"><span>$</span><span class="mo_basic_price">30</span><sub class="mo2f-year">/year</sub><br></span></p>
                 
                      
                       <div class="mo2f-container-dropdown mo2f-discount-price">
                           <div class="mo2f-select-dropdown">
						   <select class="mo2f-dropdown-width mo2f-inst-btn2" id="mo_basic_select" onchange="mo2f_change_instance_value('mo_enterprise_select','mo_basic_select',this);">
                                   <option value="1" data-price="30"> 5 USERS </option>
                                    <option value="2" data-price="49">10 USERS </option>
                                    <option value="3" data-price="69">25 USERS </option>
                                   <option value="4" data-price="99">50 USERS</option>
                                   <option value="5" data-price="199">100 USERS</option>
                                   <option value="6" data-price="349">500 USERS</option>
                                   <option value="7" data-price="499">1000 USERS</option>
                                   <option value="8" data-price="799">5000 USERS</option>
                                   <option value="9" data-price="999">10000 USERS</option>
                                   <option value="10" data-price="1449">20000 USERS</option>
                                  
               
                               </select>
                            </div>
                        </div>

						<center>
									<div id="mo2fa_custom_my_plan_2fa_mo">
												<?php	if( isset($is_customer_registered) && $is_customer_registered) { ?>
												<a onclick="mo2f_upgradeform('wp_2fa_premium_plan','2fa_plan')" target="blank" class="mo2f-license-btn mo2f-license-btn-2fa">UPGRADE NOW</a>
												<?php }else{ ?>
												<a onclick="mo2f_register_and_upgradeform('wp_2fa_premium_plan','2fa_plan')" target="blank"class="mo2f-license-btn mo2f-license-btn-2fa">UPGRADE NOW</a>
												<?php }?>
									</div>

								 </center>		
           
            </div>

     

                   
<div  class="mo2f-col-md-4 mo2f-hover-2fa" style="background: rgb(255, 255, 255);">
                <h3>ENTERPRISE</h3>
				
                <div class="mo2f-price-list" id="mo2f-list-margin">
				<div id="mo2f-enterprise-plan-div" class="mo2f_price_list_border">   
      



                <li  id="mo2f-enterprise-first-element" class="mo2f-choose-plan-2fa mo2f-text-left mo2f-basic-color"><i class="fas fa-arrow-circle-right  mo2f-basic-color"></i><span>Everything In Basic</span> </li>
                            
                     



                    <li class="mo2f-choose-plan-2fa mo2f-text-left">
                        <i class="fas fa-arrow-circle-right mo2f-text-success"></i><span>White labelling (logo, text and color)</span>
                    </li>
                    <li class="mo2f-choose-plan-2fa mo2f-text-left">
                        <i class="fas fa-arrow-circle-right mo2f-text-success"></i><span>Passwordless Login</span> 
                    </li>
                    <li class="mo2f-choose-plan-2fa mo2f-text-left">
                        <i class="fas fa-arrow-circle-right mo2f-text-success"></i><span>Configurable 2FA Code Length And Expiration Time.</span></li>
                    <li class="mo2f-choose-plan-2fa mo2f-text-left">
                            <i class="fas fa-arrow-circle-right mo2f-text-success"></i><span>Remember Device(skip 2FA for trusted devices.)</span></li>
                    <li class="mo2f-choose-plan-2fa mo2f-text-left">
                        <i class="fas fa-arrow-circle-right mo2f-text-success"></i><span>Custom SMS Gateway</span></li>
                    <li class="mo2f-choose-plan-2fa mo2f-text-left">
                            <i class="fas fa-arrow-circle-right mo2f-text-success"></i><span>QR Code Authentication, OTP Over call, Push Notification, Yubikey Hardware Token</span></li>
                </div>
				</div>
				<p class="mo2f-mt"><span class="mo2f-display-1"><span>$</span><span class="mo_enterprise_price">59</span><sub class="mo2f-year">/year</sub><br></span></p>
                        
                        <div class="mo2f-container-dropdown mo2f-discount-price">
                            <div class="mo2f-select-dropdown">
							<select class="mo2f-dropdown-width mo2f-inst-btn2" id="mo_enterprise_select" onchange="mo2f_change_instance_value('mo_enterprise_select','mo_basic_select',this);">
                                    <option value="1" selected="" data-price="59"> 5 USERS </option>
                                     <option value="2" data-price="78"> 10 USERS </option>
                                      <option value="3" data-price="98"> 25 USERS </option>
                                    <option value="4" data-price="128"> 50 USERS</option>
                                    <option value="5" data-price="228"> 100 USERS</option>
                                    <option value="6" data-price="378"> 500 USERS</option>
                                    <option value="7" data-price="528"> 1000 USERS</option>
                                    <option value="8" data-price="878"> 5000 USERS</option>
                                    <option value="9" data-price="1028"> 10000 USERS</option>
                                    <option value="10" data-price="1478"> 20000 USERS</option>
                                 
                                </select>
                                </div>
                                </div>

								<center>
									<div id="mo2fa_custom_my_plan_2fa_mo">
												<?php	if( isset($is_customer_registered) && $is_customer_registered) { ?>
												<a onclick="mo2f_upgradeform('wp_2fa_enterprise_plan','2fa_plan')" target="blank" class="mo2f-license-btn mo2f-license-btn-2fa">UPGRADE NOW</a>
												<?php }else{ ?>
												<a onclick="mo2f_register_and_upgradeform('wp_2fa_enterprise_plan','2fa_plan')" target="blank"class="mo2f-license-btn mo2f-license-btn-2fa">UPGRADE NOW</a>
												<?php }?>
									</div>

								 </center>			
				</div>
			
				<div class="mo2f-on-prem-ribbon-rpn"><div class="mo2f-ribbon-title">On Premise Solution Plans</div>
                            <p>Users' data is stored on your organization's premises.</p>
                </div>
           
			<div id="mo2f-hover-2fa-2" class="mo2f-col-md-4 mo2f-hover-2fa mo2f-hover-2fa2">

                <h3>PREMIUM LITE</h3>
				
                <div class="mo2f-price-list" id="mo2f-list-margin">
				<div id="mo2f-premium-lite-plan-div" class="mo2f_price_list_border">
                    <li class="mo2f-choose-plan-2fa mo2f-text-left">
                        <i class="fas fa-arrow-circle-right mo2f-text-success"></i><span>Unlimited Users And Single-Site Compatible</span>
                        </li>
                
                    <li class="mo2f-choose-plan-2fa mo2f-text-left">
						<div class="mo2fa_15_tooltip_methodlist">
							<i class="fas fa-arrow-circle-right mo2f-text-success"></i>
							<span>TOTP Based Methods (2FA code via mobile app)</span> 
							      <i class="fa fa-info-circle fa-xs" aria-hidden="true"></i>
							      <span class="mo2fa_methodlist">
                              
											Google Authenticator <br>
											Authy Authenticator <br>
											Microsoft Authenticator <br>
											LastPass Authenticator<br>
											FreeOTP Authenticator<br>
											Duo Mobile Authenticator <br>
                              
                              
                            </span>
						</div>
					</li>


                    <li class="mo2f-choose-plan-2fa mo2f-text-left"><i class="fas fa-arrow-circle-right mo2f-text-success"></i><span>2FA Code Over Email/Email Verification (Unlimited Transcations)</span></li>
                    <li class="mo2f-choose-plan-2fa mo2f-text-left"><i class="fas fa-arrow-circle-right mo2f-text-success"></i><span>2FA Code Via SMS (Charges Apply)</span></li>
                    <li class="mo2f-choose-plan-2fa mo2f-text-left"><i class="fas fa-arrow-circle-right mo2f-text-success"></i><span>Enforce 2FA For Users</span></li>
                    <li class="mo2f-choose-plan-2fa mo2f-text-left"><i class="fas fa-arrow-circle-right mo2f-text-success"></i><span>Redirection To Custom Url After 2FA</span></li>
                    <li class="mo2f-choose-plan-2fa mo2f-text-left"><i class="fas fa-arrow-circle-right mo2f-text-success"></i><span>Role-Based 2FA</span></li>
                    <li class="mo2f-choose-plan-2fa mo2f-text-left"><i class="fas fa-arrow-circle-right mo2f-text-success"></i><span>Backup Login Method</span></li>


                  
                    
                   
                </div>
					</div>
				<p class="mo2f-mt"><span class="mo2f-display-1 "><span>$</span><span class="mo_premium_lite_price">99</span><sub class="mo2f-year">/year</sub></span><br></p>
                 
                    <div class="mo2f-container-dropdown mo2f-discount-price">
                        <div class="mo2f-select-dropdown">
						<select class="mo2f-dropdown-width mo2f-inst-btn2" id="mo_premium_lite_select" onchange="mo2f_change_instance_value('mo_premium_select','mo_premium_lite_select',this);">
                                <option value="1" data-price="99"> 1 SITE </option>
                                <option value="2" data-price="179"> 2 SITES</option>
                                <option value="3" data-price="299"> 5 SITES</option>
                                <option value="4" data-price="449"> 10 SITES</option>
                                <option value="5" data-price="599"> 25 SITES</option>
                            </select>

                            </div>
                            </div>
							<center>
									<div id="mo2fa_custom_my_plan_2fa_mo">
												<?php	if( isset($is_customer_registered) && $is_customer_registered) { ?>
												<a onclick="mo2f_upgradeform('wp_security_two_factor_premium_lite_plan','2fa_plan')" target="blank" class="mo2f-license-btn mo2f-license-btn-2fa">UPGRADE NOW</a>
												<?php }else{ ?>
												<a onclick="mo2f_register_and_upgradeform('wp_security_two_factor_premium_lite_plan','2fa_plan')" target="blank"class="mo2f-license-btn mo2f-license-btn-2fa">UPGRADE NOW</a>
												<?php }?>
									</div>

								 </center>
               
				</div>
				
		
			<div class="mo2f-col-md-4 mo2f-hover-2fa mo2f-incl-plan-2fa">
           
               
		   <div class="mo2f-product">
			   <div class="mo2f-price-tag">
				   <p class="mo2f-price">Best Value</p>
			   </div>
	 
		   <h3 >ALL-INCLUSIVE</h3>
		 
		   <div class="mo2f-price-list mo2f-text-left mo2f-price-list-incl" id="mo2f-list-margin">
		   <div id="mo2f-all-inclusive-plan-div" class="mo2f_price_list_border">
			   <li id="mo2f-all-inclusive-first-element" class="mo2f-choose-plan-2fa mo2f-text-left mo2f-premium-color"><i class="fas fa-arrow-circle-right mo2f-premium-color"></i><span>Everything In Premium Lite </span>
					 
				   </li>

			   
			   <li class="mo2f-choose-plan-2fa">
				   <i class="fas fa-arrow-circle-right mo2f-text-success"></i><span>2FA Code Via Telegram</span>
			   </li>
			   <li class="mo2f-choose-plan-2fa">
				   <i class="fas fa-arrow-circle-right mo2f-text-success"></i><span>Remember Device (Skip 2FA for trusted device)</span>
			   </li>
			   <li class="mo2f-choose-plan-2fa">
				   <i class="fas fa-arrow-circle-right mo2f-text-success"></i><span>White labelling (logo, text and color)</span>
			   </li>
			   <li class="mo2f-choose-plan-2fa">
				   <i class="fas fa-arrow-circle-right mo2f-text-success"></i><span>Page Restriction (2FA to access specific pages)</span>
			   </li>
			   <li class="mo2f-choose-plan-2fa">
				   <i class="fas fa-arrow-circle-right mo2f-text-success"></i><span>Concurrent Session Restriction</span>
			   </li>
			   <li class="mo2f-choose-plan-2fa">
				   <i class="fas fa-arrow-circle-right mo2f-text-success"></i><span>WebAuthn (Login using device credentials)</span>
			   </li>
			   <li class="mo2f-choose-plan-2fa">
				   <i class="fas fa-arrow-circle-right mo2f-text-success"></i><span>Skip 2FA For Specified IPs.</span>
			   </li>
		   </div>
						</div>
		   <p class="mo2f-mt"><span class="mo2f-display-1"><span>$</span><span class="mo_premium_price">199</span><sub class="mo2f-year">/year</sub></span><br></p>

		
			   <div class="mo2f-container-dropdown mo2f-discount-price">
				   <div class="mo2f-select-dropdown">
				   <select class="mo2f-dropdown-width mo2f-inst-btn2" id="mo_premium_select" onchange="mo2f_change_instance_value('mo_premium_select','mo_premium_lite_select',this);">
                                    <option value="1" data-price="199"> 1 SITE </option>
                                    <option value="2" data-price="299"> 2 SITES</option>
                                    <option value="3" data-price="499"> 5 SITES</option>
                                    <option value="4" data-price="799"> 10 SITES</option>
                                    <option value="5" data-price="1099"> 25 SITES</option>
                                </select>

					   </div>
					   </div>
					   <center>
									<div id="mo2fa_custom_my_plan_2fa_mo">
												<?php	if( isset($is_customer_registered) && $is_customer_registered) { ?>
												<a onclick="mo2f_upgradeform('wp_security_two_factor_all_inclusive_plan','2fa_plan')" target="blank" class="mo2f-license-btn mo2f-license-btn-2fa">UPGRADE NOW</a>
												<?php }else{ ?>
												<a onclick="mo2f_register_and_upgradeform('wp_security_two_factor_all_inclusive_plan','2fa_plan')" target="blank"class="mo2f-license-btn mo2f-license-btn-2fa">UPGRADE NOW</a>
												<?php }?>
									</div>

								 </center>
		   </div> 
		
		
   </div>
	        </div>

			<br>
			<br><br>
				</div>
				
<script> 
document.getElementById("mo2f-enterprise-first-element").addEventListener("mouseover", mouseOver);

document.getElementById("mo2f-enterprise-first-element").addEventListener("mouseout", mouseOut);

function mouseOver() {
  document.getElementById("mo2f-hover-2fa-1").style.background = "linear-gradient(145deg, #fafbfe,#ffe9cc,#e3ecfe)";
}
function mouseOut() {
  document.getElementById("mo2f-hover-2fa-1").style.background = "#fff";
}



document.getElementById("mo2f-all-inclusive-first-element").addEventListener("mouseover", premium_mouseOver);

document.getElementById("mo2f-all-inclusive-first-element").addEventListener("mouseout", premium_mouseOut);

function premium_mouseOver() {
  document.getElementById("mo2f-hover-2fa-2").style.background = "linear-gradient(145deg,#fafbfe,#d5f6d3 ,#d4f1d3,#e3ecfe)";
}
function premium_mouseOut() {
  document.getElementById("mo2f-hover-2fa-2").style.background = "#fff";
}


	</script>


	
			
    
    

	     
	        
	
	
<div id="mo2fa_compare">
	<center>
	<div class=""><a href="#mo2fa_more_deails" onclick="mo2fa_show_details()"><button class="mo2fa_upgrade_my_plan mo2fa_compare1">Click here to Compare Features</button></a></div>
	<div><a href="#mo2fa_details" onclick="mo2fa_show_details()"><button  style="display: none;" class="mo2fa_upgrade_my_plan mo2fa_compare1">Click here to Hide Comparison</button></a></div>
	</center>
</div>

<div id="mo2fa_ns_features_only" style="display: none; margin-left:3%">

	<div class="mo_wpns_upgrade_security_title" >
		<div class="mo_wpns_upgrade_page_title_name">
			<h1 class="mo_wpns_upgrade_page_header">
		WAF</h1><hr class="mo_wpns_upgrade_page_hr"></div>
		
	<div class="mo_wpns_upgrade_page_ns_background">
			<center>
			<h4 class="mo_wpns_upgrade_page_starting_price">Starting From</h4>
			<h1 class="mo_wpns_upgrade_pade_pricing">$50</h1>
			
				<?php echo esc_html(mo2f_waf_yearly_standard_pricing()); ?>
				
				
			</center>
	
	<div style="text-align: center;">
	<?php	
	if(isset($is_customer_registered) && $is_customer_registered) {
			?>
	            <button
	                        class="button button-primary button-large mo_wpns_upgrade_page_button"
	                        onclick="mo2f_upgradeform('wp_security_waf_plan','2fa_plan')">Upgrade</button>
	        <?php }
	        else{ ?>
				<button
	                        class="button button-primary button-large mo_wpns_upgrade_page_button"
	                        onclick="mo2f_register_and_upgradeform('wp_security_waf_plan','2fa_plan')">Upgrade</button>
	        <?php } 
	        ?>
			
	</div>
			<div><center><b>
		<ul>
			<li>Realtime IP Blocking</li>
			<li>Live Traffic and Audit</li>
			<li>IP Blocking and Whitelisting</li>
			<li>OWASP TOP 10 Firewall Rules</li>
			<li>Standard Rate Limiting/ DOS Protection</li>
			<li><a onclick="wpns_pricing()">Know more</a></li>
		</ul>
		</b></center></div>
	</div>
	</div>
	<div class="mo_wpns_upgrade_page_space_in_div"></div>
	<div class="mo_wpns_upgrade_security_title" >
		<div class="mo_wpns_upgrade_page_title_name">
			<h1 class="mo_wpns_upgrade_page_header">
		Login and Spam</h1>
		<hr class="mo_wpns_upgrade_page_hr">
	</div>
		
		<div class="mo_wpns_upgrade_page_ns_background">
			<center>
			<h4 class="mo_wpns_upgrade_page_starting_price">Starting From</h4>
			<h1 class="mo_wpns_upgrade_pade_pricing">$15</h1>
			
				<?php echo esc_html(mo2f_login_yearly_standard_pricing()); ?>
				
				
			</center>
			
		<div style="text-align: center;">
		<?php if( isset($is_customer_registered)&& $is_customer_registered ) {
			?>
	            <button class="button button-primary button-large mo_wpns_upgrade_page_button"
	                        onclick="mo2f_upgradeform('wp_security_login_and_spam_plan','2fa_plan')">Upgrade</button>
	        <?php }else{ ?>

	           <button class="button button-primary button-large mo_wpns_upgrade_page_button"
	                    onclick="mo2f_register_and_upgradeform('wp_security_login_and_spam_plan','2fa_plan')">Upgrade</button>
	        <?php } 
	        ?>
		</div>
			<div><center><b>
				<ul>
					<li>Limit login Attempts</li>
					<li>CAPTCHA on login</li>
					<li>Blocking time period</li>
					<li>Enforce Strong Password</li>
					<li>SPAM Content and Comment Protection</li>
					<li><a onclick="wpns_pricing()">Know more</a></li>
				</ul>
			</b></center></div>
		</div>
		
		
	</div>
	<div class="mo_wpns_upgrade_page_space_in_div"></div>
	<div class="mo_wpns_upgrade_security_title" >
		<div class="mo_wpns_upgrade_page_title_name">
			<h1 class="mo_wpns_upgrade_page_header">
		Malware Scanner</h1><hr class="mo_wpns_upgrade_page_hr"></div>
		
			<div class="mo_wpns_upgrade_page_ns_background">
			<center>
			<h4 class="mo_wpns_upgrade_page_starting_price">Starting From</h4>
			<h1 class="mo_wpns_upgrade_pade_pricing">$15</h1>
			
				<?php echo esc_html(mo2f_scanner_yearly_standard_pricing()); ?>
				
				
			</center>
			<div style="text-align: center;">
			<?php if( isset($is_customer_registered) && $is_customer_registered) {
			?>
                <button
                            class="button button-primary button-large mo_wpns_upgrade_page_button"
                            onclick="mo2f_upgradeform('wp_security_malware_plan','2fa_plan')">Upgrade</button>
            <?php }else{ ?>

               <button
                            class="button button-primary button-large mo_wpns_upgrade_page_button"
                            onclick="mo2f_register_and_upgradeform('wp_security_malware_plan','2fa_plan')">Upgrade</button>
            <?php } 
            ?>
		</div>
			<div><center><b>
				<ul>
					<li>Malware Detection</li>
					<li>Blacklisted Domains</li>
					<li>Action On Malicious Files</li>
					<li>Repository Version Comparison</li>
					<li>Detect any changes in the files</li>
					<li><a onclick="wpns_pricing()">Know more</a></li>
				</ul>
			</b></center></div>
	</div>
	</div>
	<div class="mo_wpns_upgrade_page_space_in_div"></div>
	<div class="mo_wpns_upgrade_security_title" >
		<div class="mo_wpns_upgrade_page_title_name">
			<h1 class="mo_wpns_upgrade_page_header">
		Encrypted Backup</h1><hr class="mo_wpns_upgrade_page_hr"></div>
		
	<div class="mo_wpns_upgrade_page_ns_background">

		<center>
			<h4 class="mo_wpns_upgrade_page_starting_price">Starting From</h4>
			<h1 class="mo_wpns_upgrade_pade_pricing">$30</h1>
			
				<?php echo esc_html(mo2f_backup_yearly_standard_pricing()); ?>
				
				
			</center>
			<div style="text-align: center;">
	<?php	if( isset($is_customer_registered) && $is_customer_registered) {
		?>
            <button
                        class="button button-primary button-large mo_wpns_upgrade_page_button"
                        onclick="mo2f_upgradeform('wp_security_backup_plan','2fa_plan')">Upgrade</button>
        <?php }else{ ?>
			<button
                        class="button button-primary button-large mo_wpns_upgrade_page_button"
                        onclick="mo2f_register_and_upgradeform('wp_security_backup_plan' ,'2fa_plan')">Upgrade</button>
        <?php } 
        ?>
		
		</div>
			<div><center><b>
				<ul>
					<li>Schedule Backup</li>
					<li>Encrypted Backup</li>
					<li>Files/Database Backup</li>
					<li>Restore and Migration</li>
					<li>Password Protected Zip files</li>
					<li><a onclick="wpns_pricing()">Know more</a></li>
				</ul>
			</b></center></div>
	</div></div>
</div>
<center>
	<br>
	<div id="mo2fa_more_deails" style="display:none;">
<div class="mo2fa_table-scrollbar"></br></br>
<table class="table mo2fa_table_features table-striped">
	<caption class="pricing_head_mo_2fa" id="mo2fa_details"><h1>Feature Details</h1></caption>
  <thead>
    <tr class="mo2fa_main_category_header" style="font-size: 20px;">
      <th scope="col">Features</th>
     
	  <th scope="col" class="mo2fa_plugins"><center>Basic</center></th> 
      <th scope="col" class="mo2fa_plugins"><center>Enterprise</center></th> 
      <th scope="col" class="mo2fa_plugins"><center>Premium Lite</center></th>
	  <th scope="col" class="mo2fa_plugins"><center>All Inclusive</center></th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <th scope="row">Unlimited Sites</th>
   
      <td><center><i class="fas fa-check"></i></center></td>      
      <td><center><i class="fas fa-check"></i></center></td>
	  <td><center><i class="fas fa-times mo2fa_hide"></i></center></td>
      <td><center><i class="fas fa-times mo2fa_hide"></i></center></td>      
    </tr>
   
    <tr>
     <th scope="row">Unlimited Users</th>
	
      <td><center><i class="fas fa-times mo2fa_hide"></i></center></td>
      <td><center><i class="fas fa-times mo2fa_hide"></i></center></td>
	  <td><center><i class="fas fa-check"></i></center></td>
	 <td><center><i class="fas fa-check"></i></center></td>

    </tr>
   <tr class="mo2fa_bg_category_main">
     <th scope="row">Authentication Methods</th>
	 <td></td>   
	 <td></td>
      <td></td>
      <td></td>
    </tr>
    <tr>
    <th scope="row" class="mo2fa_category_feature">
		TOTP Based Authenticator
	    <br>
		<div class="mo2fa-top-auth-unbold"> Google Authenticator<br>Microsoft Authenticator<br>Authy Authenticator<br>Last Pass Authenticator<br>Duo Authenticator</div>
	</th>

	<td><center><i class="fas fa-check"></i></center></td>
	<td><center><i class="fas fa-check"></i></center></td>
      <td><center><i class="fas fa-check"></i></center></td>
      <td><center><i class="fas fa-check"></i></center></td>

    </tr>

	<tr>
     <th scope="row" class="mo2fa_category_feature">Security Questions</th>
	 <td><center><i class="fas fa-check"></i></center></td>
	 <td><center><i class="fas fa-check"></i></center></td>
      <td><center><i class="fas fa-check"></i></center></td>
      <td><center><i class="fas fa-check"></i></center></td>

    </tr>
	<tr>
    <th scope="row" class="mo2fa_category_feature">Email Verification</th>
	<td><center><i class="fas fa-check"></i></center></td>
	<td><center><i class="fas fa-check"></i></center></td>
      <td><center><i class="fas fa-check"></i></center></td>
      <td><center><i class="fas fa-check"></i></center></td>

    </tr> 

	

	<tr>
  <th scope="row" class="mo2fa_category_feature">OTP Over Email</th>
    <td><center><a href="<?php echo esc_url(MoWpnsConstants::SMS_EMAIL_TRANSACTION) ?>" target="_blank">Charges Apply</a></center></td>
    <td><center><a href="<?php echo esc_url(MoWpnsConstants::SMS_EMAIL_TRANSACTION) ?>" target="_blank">Charges Apply</a></center></td>
    <td><center>Unlimited</center></td>
    <td><center>Unlimited</center></td>
  </tr>


	<tr>
  <th scope="row" class="mo2fa_category_feature">OTP Over SMS</th>
   
  <td><center><a href="<?php echo esc_url(MoWpnsConstants::SMS_EMAIL_TRANSACTION) ?>" target="_blank">Charges Apply</a></center></td>
  <td><center><a href="<?php echo esc_url(MoWpnsConstants::SMS_EMAIL_TRANSACTION) ?>" target="_blank">Charges Apply</a></center></td>
  <td><center><a href="<?php echo esc_url(MoWpnsConstants::SMS_EMAIL_TRANSACTION) ?>" target="_blank">Charges Apply</a></center></td>
  <td><center><a href="<?php echo esc_url(MoWpnsConstants::SMS_EMAIL_TRANSACTION) ?>" target="_blank">Charges Apply</a></center></td>
  

  </tr>

   
 
 

    <th scope="row" class="mo2fa_category_feature">miniOrange Soft Token</th>
	
      <td><center><i class="fas fa-check"></i></center></td>
      <td><center><i class="fas fa-check"></i></center></td>
	  <td><center><i class="fas fa-times mo2fa_hide"></i></center></td>
	<td><center><i class="fas fa-times mo2fa_hide"></i></center></td>
    </tr>
    <tr>
    <th scope="row" class="mo2fa_category_feature">miniOrange Push Notification</th>
	
      <td><center><i class="fas fa-check"></i></center></td>
      <td><center><i class="fas fa-check"></i></center></td>
	  <td><center><i class="fas fa-times mo2fa_hide"></i></center></td>
	<td><center><i class="fas fa-times mo2fa_hide"></i></center></td>
    </tr>
	<th scope="row" class="mo2fa_category_feature">miniOrange QR code authentication</th>
	
	<td><center><i class="fas fa-check"></i></center></td>
	<td><center><i class="fas fa-check"></i></center></td>
	<td><center><i class="fas fa-times mo2fa_hide"></i></center></td>
  <td><center><i class="fas fa-times mo2fa_hide"></i></center></td>
  </tr>
  
<tr>
  <th scope="row" class="mo2fa_category_feature">OTP Over SMS and Email</th>
  <td><center><a href="<?php echo esc_url(MoWpnsConstants::SMS_EMAIL_TRANSACTION) ?>" target="_blank">Charges Apply</a></center></td>
    <td><center><a href="<?php echo esc_url(MoWpnsConstants::SMS_EMAIL_TRANSACTION) ?>" target="_blank">Charges Apply</a></center></td>
    <td><center><a href="<?php echo esc_url(MoWpnsConstants::SMS_EMAIL_TRANSACTION) ?>" target="_blank">Charges Apply</a></center></td>
    <td><center><a href="<?php echo esc_url(MoWpnsConstants::SMS_EMAIL_TRANSACTION) ?>" target="_blank">Charges Apply</a></center></td>
  
  
  </tr>


<tr>
  <th scope="row" class="mo2fa_category_feature">Yubikey Hardware Token</th>
  <td></td>
    <td><center><i class="fas fa-check"></i></center></td>  
    <td></td>
    <td></td>
   

  </tr>
<tr>
	<th scope="row" class="mo2fa_category_feature">OTP Over Whatsapp (Add-on)</th>
	
	<td><center></center></td>
	<td><center><i class="fas fa-times mo2fa_hide"></i></center></td>
	<td><center><i class="fas fa-times mo2fa_hide"></i></center></td>
	<td><center><i class="fas fa-check"></i></center></td>
</tr>
<tr>
	<th scope="row" class="mo2fa_category_feature">OTP Over Telegram</th>

	<td><center></center></td>  
	<td><center></center></td>
	<td><center><i class="fas fa-times mo2fa_hide"></i></center></td>
	<td><center><i class="fas fa-check"></i></center></td>
    </tr>
     <tr class="mo2fa_bg_category_main">
     <th scope="row">Backup Login Methods</th>
	 <td></td>   
	 <td></td>
      <td></td>   
      <td></td>   
    </tr>
    <tr>
    <th scope="row" class="mo2fa_category_feature">Security Questions (KBA)</th>
	<td><center><i class="fas fa-check"></i></center></td>
	<td><center><i class="fas fa-check"></i></center></td>
      <td><center><i class="fas fa-check"></i></center></td>
      <td><center><i class="fas fa-check"></i></center></td>
    </tr>
    <tr>
    <th scope="row" class="mo2fa_category_feature">OTP Over Email</th>
	
      <td><center><i class="fas fa-times mo2fa_hide"></i></center></td>
	  <td><center><i class="fas fa-check"></i></center></td>
      <td><center><i class="fas fa-times mo2fa_hide"></i></center></td>
	  
	<td><center><i class="fas fa-check"></i></center></td>
    </tr>
    <tr>
		<th scope="row" class="mo2fa_category_feature">Backup Codes</th>
		<td><center><i class="fas fa-times mo2fa_hide"></i></center></td>
		<td><center><i class="fas fa-check"></i></center></td>
		<td><center><i class="fas fa-check"></i></center></td>
		<td><center><i class="fas fa-check"></i></center></td>
		
    </tr>
    <tr class="mo2fa_bg_category_main">
     <th scope="row">Password Policy</th>
	 <td></td>   
	 <td></td>
      <td></td>   
      <td></td>   

    </tr>
   <tr>
    <th scope="row" class="mo2fa_category_feature">Passwordless Login</th>
	<td><center></center></td>
	<td><center><i class="fas fa-check"></i></center></td>
      <td><center></center></td>
      <td><center><i class="fas fa-check"></i></center></td>
    </tr> 
    
    <tr>
     <th scope="row" class="mo2fa_category_feature">Custom Gateway</th>
	 <td><center></center></td>
	 <td><center><i class="fas fa-check"></i></center></td>
      <td><center></center></td>
      <td><center><i class="fas fa-check"></i></center></td>

    </tr>
  <tr class="mo2fa_bg_category_main">
     <th scope="row">Add-Ons</th>
	 <td></td>   
	 <td></td>
      <td></td>   
      <td></td>   

    </tr>
     <tr>
     <th scope="row" class="mo2fa_category_feature">Remember Device Add-on</br><p class="mo2fa_description">You can save your device using the Remember device addon and you will get a two-factor authentication </br>prompt to check your identity if you try to login from different devices.</p></th>

	 <td><center></center></td>
      <td><center><i class="fas fa-check"></i></center></td>
      <td><center></center></td>
	  <td><center><i class="fas fa-check"></i></center></td>
    </tr>
    <tr>
     <th scope="row" class="mo2fa_category_feature">Personalization Add-on<p class="mo2fa_description">You'll get many more customization options in Personalization, such as </br>ustom Email and SMS Template, Custom Login Popup, Custom Security Questions, and many more.</p></th>
	 
	 <td><center></center></td>
      <td><center><i class="fas fa-check"></i></center></td>
      <td><center></center></td>
	  <td><center><i class="fas fa-check"></i></center></td>
    </tr>
     <tr>
     <th scope="row" class="mo2fa_category_feature">Short Codes Add-on<p class="mo2fa_description">Shortcode Add-ons mostly include Allow 2fa shortcode (you can use this this to add 2fa on any page), </br>Reconfigure 2fa add-on (you can use this add-on to reconfigure your 2fa if you have lost your 2fa verification ability), remember device shortcode.</p></th>

	 <td><center></center></td>
      <td><center><i class="fas fa-check"></i></center></td>
      <td><center></center></td>
	  <td><center><i class="fas fa-check"></i></center></td>
    </tr>
   <tr>
     <th scope="row" class="mo2fa_category_feature">Session Management<p class="mo2fa_description">Session Management prevents account sharing and limits number of simultaneous sessions. It also supports session control, login limit, idle session logout feature.</th>
	
	
	 <td><center><i class="fas fa-times mo2fa_hide"></i></center></td>
      <td><center><i class="fas fa-times mo2fa_hide"></i></center></td>
      <td><center><i class="fas fa-times mo2fa_hide"></i></center></td>
	  <td><center><i class="fas fa-check"></i></center></td>
    </tr>

	<tr>
   <th scope="row" class="mo2fa_category_feature">Page Restriction Add-On</th>
   <td></td>
   <td></td>
    <td></td>
    <td><center><i class="fas fa-check"></i></center></td>
   
   
    
  </tr>
    <tr class="mo2fa_bg_category_main">
     <th scope="row">Advance WordPress Login Settings</th>
	 <td></td> 
	 <td></td>
      <td></td>   
      <td></td>   
	  
    </tr>
     <tr>
     <th scope="row" class="mo2fa_category_feature">Force Two Factor for Users</th>
	 <td><center><i class="fas fa-check"></i></center></td>
	 <td><center><i class="fas fa-check"></i></center></td>
	 <td><center><i class="fas fa-check"></i></center></td>
	 <td><center><i class="fas fa-check"></i></center></td>
    </tr>
    <tr>
		<th scope="row" class="mo2fa_category_feature">Role Based and User Based Authentication settings</th>
		<td><center><i class="fas fa-check"></i></center></td>
		<td><center><i class="fas fa-check"></i></center></td>
		<td><center><i class="fas fa-check"></i></center></td>
		<td><center><i class="fas fa-check"></i></center></td>
    </tr>
    <tr>
		<th scope="row" class="mo2fa_category_feature">Email Verification during Two-Factor Registration</th>
		<td><center><i class="fas fa-times mo2fa_hide"></i></center></td>
		<td><center><i class="fas fa-check"></i></center></td>
		<td><center><i class="fas fa-check"></i></center></td>
		<td><center><i class="fas fa-check"></i></center></td>

		
    </tr>
	<tr>
		<th scope="row" class="mo2fa_category_feature">Custom Redirect URL</th>
		<td><center><i class="fas fa-times mo2fa_hide"></i></center></td>
		<td><center><i class="fas fa-check"></i></center></td>
		<td><center><i class="fas fa-check"></i></center></td>
		<td><center><i class="fas fa-check"></i></center></td>

		
    </tr><tr>
		<th scope="row" class="mo2fa_category_feature">Inline Registration (Set up 2FA after first login)</th>
		<td><center><i class="fas fa-check"></i></center></td>
		<td><center><i class="fas fa-check"></i></center></td>
		<td><center><i class="fas fa-check"></i></center></td>
		<td><center><i class="fas fa-check"></i></center></td>
		
    </tr><tr>
		<th scope="row" class="mo2fa_category_feature">Mobile Support</th>
		<td><center><i class="fas fa-times mo2fa_hide"></i></center></td>
		<td><center><i class="fas fa-check"></i></center></td>
		<td><center><i class="fas fa-check"></i></center></td>
		<td><center><i class="fas fa-check"></i></center></td>
		
		
    </tr><tr>
     <th scope="row" class="mo2fa_category_feature">Privacy Policy Settings</th>
	 <td><center><i class="fas fa-times mo2fa_hide"></i></center></td>
	 <td><center><i class="fas fa-check"></i></center></td>
	 <td><center><i class="fas fa-check"></i></center></td>
	 <td><center><i class="fas fa-check"></i></center></td>
	 
	 
    </tr><tr>
		<th scope="row" class="mo2fa_category_feature">XML-RPC </th>
		<td><center><i class="fas fa-times mo2fa_hide"></i></center></td>
		<td><center><i class="fas fa-check"></i></center></td>
		<td><center><i class="fas fa-check"></i></center></td>
		<td><center><i class="fas fa-check"></i></center></td>
		
		
    </tr>
     <tr class="mo2fa_bg_category_main">
     <th scope="row">Advance Security Features</th>
	 <td></td>
	 <td></td>
      <td></td>   
      <td></td>   
   
    </tr>
     <tr>
     <th scope="row" class="mo2fa_category_feature">Brute Force Protection</th>
	 <td><center><i class="fas fa-times mo2fa_hide"></i></center></td>
	
	 <td><center><i class="fas fa-check"></i></center></td>
	
	 <td><center><i class="fas fa-times mo2fa_hide"></i></center></td>
	 <td><center><i class="fas fa-check"></i></center></td>
	 
    </tr>
    <tr>
		<th scope="row" class="mo2fa_category_feature">IP Blocking </th>
	
		
		
		<td><center><i class="fas fa-times mo2fa_hide"></i></center></td>
		<td><center><i class="fas fa-check"></i></center></td>
		<td><center><i class="fas fa-times mo2fa_hide"></i></center></td>
		<td><center><i class="fas fa-check"></i></center></td>
		
    </tr>
	<tr>
		<th scope="row" class="mo2fa_category_feature">Monitoring</th>
	
	
		
		<td><center><i class="fas fa-times mo2fa_hide"></i></center></td>
		<td><center><i class="fas fa-check"></i></center></td>
		<td><center><i class="fas fa-times mo2fa_hide"></i></center></td>
		<td><center><i class="fas fa-check"></i></center></td>
		
    </tr> <tr>
		<th scope="row" class="mo2fa_category_feature">File Protection</th>

		<td><center><i class="fas fa-times mo2fa_hide"></i></center></td>
		<td><center><i class="fas fa-check"></i></center></td>
		<td><center><i class="fas fa-times mo2fa_hide"></i></center></td>
		<td><center><i class="fas fa-check"></i></center></td>

    </tr>
   <tr>
     <th scope="row" class="mo2fa_category_feature">Country Blocking </th>
	
	 <td><center><i class="fas fa-times mo2fa_hide"></i></center></td>
	 <td><center><i class="fas fa-check"></i></center></td>
	 <td><center><i class="fas fa-times mo2fa_hide"></i></center></td>
	 <td><center><i class="fas fa-check"></i></center></td>
	 
    </tr>
    <tr>
		<th scope="row" class="mo2fa_category_feature">HTACCESS Level Blocking </th>
	
		<td><center><i class="fas fa-times mo2fa_hide"></i></center></td>
		<td><center><i class="fas fa-check"></i></center></td>
		<td><center><i class="fas fa-times mo2fa_hide"></i></center></td>
		<td><center><i class="fas fa-check"></i></center></td>

    </tr>
    <tr>
     <th scope="row" class="mo2fa_category_feature">Browser Blocking </th>
	
	 <td><center><i class="fas fa-times mo2fa_hide"></i></center></td>
      <td><center><i class="fas fa-check"></i></center></td>
      <td><center><i class="fas fa-times mo2fa_hide"></i></center></td>
	  <td><center><i class="fas fa-check"></i></center></td>

    </tr>
   <tr>
     <th scope="row" class="mo2fa_category_feature">Block Global Blacklisted Email Domains</th>

	 <td><center><i class="fas fa-times mo2fa_hide"></i></center></td>
	 <td><center><i class="fas fa-check"></i></center></td>
	 <td><center><i class="fas fa-times mo2fa_hide"></i></center></td>
	 <td><center><i class="fas fa-check"></i></center></td>

    </tr>
 <tr>
     <th scope="row" class="mo2fa_category_feature">Manual Block Email Domains</th>
	
	 <td><center><i class="fas fa-times mo2fa_hide"></i></center></td>
	 <td><center><i class="fas fa-check"></i></center></td>
	 <td><center><i class="fas fa-times mo2fa_hide"></i></center></td>
	 <td><center><i class="fas fa-check"></i></center></td>

    </tr>
   <tr>
     <th scope="row" class="mo2fa_category_feature">DB Backup</th>
	
	 <td><center><i class="fas fa-times mo2fa_hide"></i></center></td>
	 <td><center><i class="fas fa-check"></i></center></td>
	 <td><center><i class="fas fa-times mo2fa_hide"></i></center></td>
	 <td><center><i class="fas fa-check"></i></center></td>

    </tr>

	</tr>
     <tr class="mo2fa_bg_category_main">
     <th scope="row">Additional Features</th>
	 <td></td>
	 <td></td>
      <td></td>   
      <td></td>   
   
    </tr>


<tr>
     <th scope="row" class="mo2fa_category_feature">Multi-Site Support</th>
	 <td><center><i class="fas fa-check"></i></center></td>
	
      <td><center><i class="fas fa-check"></i></center></td>
	  <td><center></center></td>
      <td><center><i class="fas fa-check"></i></center></td>
    </tr>
	<tr>
     <th scope="row" class="mo2fa_category_feature">Language Translation Support</th>
	
	 <td><center></center></td>
      <td><center></center></td>
      <td><center></center></td>
	  <td><center><i class="fas fa-check"></i></center></td>

    </tr><tr>
     <th scope="row" class="mo2fa_category_feature">Get online support with GoTo/Zoom meeting</th>
	 <td><center><i class="fas fa-check"></i></center></td>
	 <td><center><i class="fas fa-check"></i></center></td>
      <td><center><i class="fas fa-check"></i></center></td>
      <td><center><i class="fas fa-check"></i></center></td>

    </tr>
  </tbody>
</table>
</div>
</div>
</center>
<div class="mo2f_table_layout" style="width: 90%;margin-left:3%">
	<div>
		<h2><?php echo mo2f_lt('Steps to upgrade to the Premium Plan :');?></h2>
		<ol class="mo2f_licensing_plans_ol">
			<li><?php echo mo2f_lt( 'Click on <b>Proceed</b>/<b>Upgrade</b> button of your preferred plan above.' ); ?></li>
			<li><?php echo mo2f_lt( ' You will be redirected to the miniOrange Console. Enter your miniOrange username and password, after which you will be redirected to the payment page.' ); ?></li>

			<li><?php echo mo2f_lt( 'Select the number of users/sites you wish to upgrade for, and any add-ons if you wish to purchase, and make the payment.' ); ?></li>
			<li><?php echo mo2f_lt( 'After making the payment, you can find the Premium Lite/Premium/Enterprise plugin to download from the <b>License</b> tab in the left navigation bar of the miniOrange Console.' ); ?></li>
			<li><?php echo mo2f_lt( 'Download the paid plugin from the <b>Releases and Downloads</b> tab through miniOrange Console .' ); ?></li>
			<li><?php echo mo2f_lt( 'Deactivate and delete the free plugin from <b>WordPress dashboard</b> and install the paid plugin downloaded.' ); ?></li>
			<li><?php echo mo2f_lt( 'Login to the paid plugin with the miniOrange account you used to make the payment, after this your users will be able to set up 2FA.' ); ?></li>
		</ol>
	</div>
	<hr>
	<div>
		<h2><?php echo mo2f_lt('Note :');?></h2>
		<ol class="mo2f_licensing_plans_ol">
		<li><?php echo mo2f_lt( 'Purchasing licenses for <b>unlimited users will grant you upto 2000 users.</b> If you want to purchase more users, please contact us or drop an email at <a href="mailto:2fasupport@xecurify.com">2fasupport@xecurify.com.</a>' ); ?></li>
			<li><?php echo mo2f_lt( 'The plugin works with many of the default custom login forms (like Woocommerce/Theme My Login/Login With Ajax/User Pro/Elementor), however if you face any issues with your custom login form, contact us and we will help you with it.' ); ?></li>
			<li><?php echo mo2f_lt( 'The <b>license key </b>is required to activate the <b>Premium Lite/Premium</b> Plugins. You will have to login with the miniOrange Account you used to make the purchase then enter license key to activate plugin.' ); ?>

		</li>
	</ol>
</div>
<hr>
<br>
<div>
	<?php echo mo2f_lt( '<b class="mo2fa_note">Refund Policy : </b>At miniOrange, we want to ensure you are 100% happy with your purchase. If the premium plugin you purchased is not working as advertised and you\'ve attempted to resolve any issues with our support team, which couldn\'t get resolved then we will refund the whole amount within 10 days of the purchase. ' ); ?>
</div>
<br>
<hr>
<br>
<div><?php echo mo2f_lt( '<b class="mo2fa_note">SMS Charges : </b>If you wish to choose OTP Over SMS/OTP Over SMS and Email as your authentication method,
	SMS transaction prices & SMS delivery charges apply and they depend on country. SMS validity is for lifetime.' ); ?>
</div>
<br>
<hr>
<br>
<div>
		<?php echo mo2f_lt( '<b class="mo2fa_note">Multisite : </b>For your first license 3 subsites will be activated automatically on the same domain. And if you wish to use it for more please contact support ' ); ?>
</div>
<br>
<hr>
<br>
<div>
	<?php echo mo2f_lt( '<b class="mo2fa_note">Privacy Policy : </b><a		href="https://www.miniorange.com/2-factor-authentication-for-wordpress-gdpr" target="blank">Click Here</a>
		to read our Privacy Policy.' ); ?>
</div>
<br>
<hr>
<br>
<div>
	<?php echo mo2f_lt( '<b class="mo2fa_note">Contact Us : </b>If you have any doubts regarding the licensing plans, you can mail us at <a		href="mailto:info@xecurify.com"><i>info@xecurify.com</i></a>
		or submit a query using the support form.' ); ?>
</div>
</div>
</center>
<div id="mo2f_payment_option" class="mo2f_table_layout" style="width: 90%;margin-left:3%">
	<div>
		<h3>Supported Payment Methods</h3><hr>
		<div class="mo_2fa_container">
			<div class="mo_2fa_card-deck">
				<div class="mo_2fa_card mo_2fa_animation">
					<div class="mo_2fa_Card-header">
						<?php 
						echo'<img src="'.esc_url(dirname(plugin_dir_url(__FILE__))).'/includes/images/card.png" class="mo2fa_card">';?>
					</div>
					<hr class="mo2fa_hr">
					<div class="mo_2fa_card-body">
						<p class="mo2fa_payment_p">If payment is done through Credit Card/Intenational debit card, the license would be created automatically once payment is completed. </p>
						<p class="mo2fa_payment_p"><i><b>For guide 
							<?php echo'<a href='.esc_url(MoWpnsConstants::FAQ_PAYMENT_URL).' target="blank">Click Here.</a>';?></b></i></p>

						</div>
					</div>
					<div class="mo_2fa_card mo_2fa_animation">
						<div class="mo_2fa_Card-header">
							<?php 
							echo'<img src="'.esc_url(dirname(plugin_dir_url(__FILE__))).'/includes/images/paypal.png" class="mo2fa_card">';?>
						</div>
						<hr class="mo2fa_hr">
						<div class="mo_2fa_card-body">
							<?php echo'<p class="mo2fa_payment_p">Use the following PayPal id for payment via PayPal.</p><p><i><b style="color:#1261d8"><a href="mailto:'.esc_html(MoWpnsConstants::SUPPORT_EMAIL).'">info@xecurify.com</a></b></i>';?>

						</div>
					</div>
					<div class="mo_2fa_card mo_2fa_animation">
						<div class="mo_2fa_Card-header">
							<?php 
							echo'<img src="'.esc_url(dirname(plugin_dir_url(__FILE__))).'/includes/images/bank-transfer.png" class="mo2fa_card mo2fa_bank_transfer">';?>

						</div>
						<hr class="mo2fa_hr">
						<div class="mo_2fa_card-body">
							<?php echo'<p class="mo2fa_payment_p">If you want to use Bank Transfer for payment then contact us at <i><b style="color:#1261d8"><a href="mailto:'.esc_html(MoWpnsConstants::SUPPORT_EMAIL).'">info@xecurify.com</a></b></i> so that we can provide you bank details. </i></p>';?>
						</div>
					</div>
				</div>
			</div>
			<div class="mo_2fa_mo-supportnote">
				<p class="mo2fa_payment_p"><b>Note :</b> Once you have paid through PayPal/Bank Transfer, please inform us at <i><b style="color:#1261d8"><a href="mailto:<?php echo esc_html(MoWpnsConstants::SUPPORT_EMAIL); ?>">info@xecurify.com</a></b></i>, so that we can confirm and update your License.</p>
			</div>
		</div>
	</div>


	<?php
function mo2f_waf_yearly_standard_pricing() {
	?>
    <p class="mo2f_pricing_text mo_wpns_upgrade_page_starting_price"
       id="mo2f_yearly_sub"><?php echo __( 'Yearly subscription fees', 'miniorange-2-factor-authentication' ); ?><br>

	<select id="mo2f_yearly" class="form-control mo2fa_form_control1">
		<option> <?php echo mo2f_lt( '1 site - $50 per year' ); ?> </option>
		<option> <?php echo mo2f_lt( 'Upto 5 sites - $100 per year' ); ?> </option>
		<option> <?php echo mo2f_lt( 'Upto 10 sites - $150 per year' ); ?> </option>

	</select>
</p>

	<?php
}
function mo2f_login_yearly_standard_pricing() {
	?>
    <p class="mo2f_pricing_text mo_wpns_upgrade_page_starting_price"
       id="mo2f_yearly_sub"><?php echo __( 'Yearly subscription fees', 'miniorange-2-factor-authentication' ); ?><br>

	<select id="mo2f_yearly" class="form-control mo2fa_form_control1">
		<option> <?php echo mo2f_lt( '1 site - $15 per year' ); ?> </option>
		<option> <?php echo mo2f_lt( 'Upto 5 sites - $35 per year' ); ?> </option>
		<option> <?php echo mo2f_lt( 'Upto 10 sites - $60 per year' ); ?> </option>

	</select>
</p>

	<?php
}
function mo2f_backup_yearly_standard_pricing() {
	?>
    <p class="mo2f_pricing_text mo_wpns_upgrade_page_starting_price"
       id="mo2f_yearly_sub"><?php echo __( 'Yearly subscription fees', 'miniorange-2-factor-authentication' ); ?><br>

	<select id="mo2f_yearly" class="form-control mo2fa_form_control1">
		<option> <?php echo mo2f_lt( '1 site - $30 per year' ); ?> </option>
		<option> <?php echo mo2f_lt( 'Upto 5 sites - $50 per year' ); ?> </option>
		<option> <?php echo mo2f_lt( 'Upto 10 sites - $70 per year' ); ?> </option>

	</select>
</p>

	<?php
}
function mo2f_scanner_yearly_standard_pricing() {
	?>
    <p class="mo2f_pricing_text mo_wpns_upgrade_page_starting_price" 
       id="mo2f_yearly_sub"><?php echo __( 'Yearly subscription fees', 'miniorange-2-factor-authentication' ); ?><br>

	<select id="mo2f_yearly" class="form-control mo2fa_form_control1">
		<option> <?php echo mo2f_lt( '1 site - $15 per year' ); ?> </option>
		<option> <?php echo mo2f_lt( 'Upto 5 sites - $35 per year' ); ?> </option>
		<option> <?php echo mo2f_lt( 'Upto 10 sites - $60 per year' ); ?> </option>

	</select>
</p>

	<?php
}

function mo2f_get_binary_equivalent_2fa_lite( $mo2f_var ) {
	switch ( $mo2f_var ) {
		case 1:
			return "<div style='color: #20b2aa;font-size: x-large;float:left;margin:0px 5px;'>ðŸ—¸</div>";
		case 0:
			return "<div style='color: red;font-size: x-large;float:left;margin:0px 5px;'>Ã—</div>";
		default:
			return $mo2f_var;
	}
}

function mo2f_feature_on_hover_2fa_upgrade( $mo2f_var ) {

	return '<div class="mo2f_tooltip" style="float: right;width: 6%;"><span class="dashicons dashicons-info mo2f_info_tab"></span><span class="mo2f_tooltiptext" style="margin-left:-232px;margin-top: 9px;">'. $mo2f_var .'</span></div>';
}

?>
<form class="mo2f_display_none_forms" id="mo2fa_loginform"
                  action="<?php echo MO_HOST_NAME . '/moas/login'; ?>"
                  target="_blank" method="post">
                <input type="email" name="username" value="<?php echo get_option( 'mo2f_email' ); ?>"/>
                <input type="text" name="redirectUrl"
                       value="<?php echo MO_HOST_NAME . '/moas/initializepayment'; ?>"/>
                <input type="text" name="requestOrigin" id="requestOrigin"/>
            </form>

            <form class="mo2f_display_none_forms" id="mo2fa_register_to_upgrade_form"
                   method="post">
                <input type="hidden" name="requestOrigin" />
                <input type="hidden" name="mo2fa_register_to_upgrade_nonce"
                       value="<?php echo wp_create_nonce( 'miniorange-2-factor-user-reg-to-upgrade-nonce' ); ?>"/>
            </form>
    <script type="text/javascript">

		function mo2f_upgradeform(planType,planname) 
		{
            jQuery('#requestOrigin').val(planType);
            jQuery('#mo2fa_loginform').submit();
            var data =  {
								'action'				  : 'wpns_login_security',
								'wpns_loginsecurity_ajax' : 'update_plan', 
								'planname'				  : planname,
								'planType'				  : planType,
					}
					jQuery.post(ajaxurl, data, function(response) {
					});
        }
        function mo2f_register_and_upgradeform(planType, planname) 
        {
                    jQuery('#requestOrigin').val(planType);
                    jQuery('input[name="requestOrigin"]').val(planType);
                    jQuery('#mo2fa_register_to_upgrade_form').submit();

                    var data =  {
								'action'				  : 'wpns_login_security',
								'wpns_loginsecurity_ajax' : 'wpns_all_plans', 
								'planname'				  : planname,
						'planType'				  : planType,
					}
					jQuery.post(ajaxurl, data, function(response) {
					});
        }
    	function show_2fa_plans()
    	{
    		document.getElementById('mo2fa_ns_features_only').style.display = "none";
    		document.getElementById('mo2f_twofa_plans').style.display = "block";
    		document.getElementById('mo_2fa_lite_licensing_plans_title').style.display = "none";
    		document.getElementById('mo_2fa_lite_licensing_plans_title1').style.display = "block";
    		document.getElementById('mo2f_ns_licensing_plans_title').style.display = "block";
    		document.getElementById('mo_ns_licensing_plans_title1').style.display = "none";
    		document.getElementById('mo2fa_compare').style.display = "block";
    	}
    	function mo_ns_show_plans()
    	{
    		document.getElementById('mo2fa_ns_features_only').style.display = "block";
    		document.getElementById('mo2f_twofa_plans').style.display = "none";
    		document.getElementById('mo_2fa_lite_licensing_plans_title').style.display = "block";
    		document.getElementById('mo_2fa_lite_licensing_plans_title1').style.display = "none";
    		document.getElementById('mo2f_ns_licensing_plans_title').style.display = "none";
    		document.getElementById('mo_ns_licensing_plans_title1').style.display = "block";
    		document.getElementById('mo2fa_compare').style.display = "none";
			
			if(document.getElementById('mo2fa_more_deails').style.display!="none")
			{   
		        jQuery('#mo2fa_more_deails').toggle();
			    jQuery('.mo2fa_compare1').toggle();


			}
			
			
    	}

    	function wpns_pricing()
		{
			window.open("https://security.miniorange.com/pricing/");
		}

		function mo2fa_show_details()
		{
			jQuery('#mo2fa_more_deails').toggle();
			jQuery('.mo2fa_more_details_p1').toggle();
			jQuery('.mo2fa_more_details_p').toggle();
			jQuery('.mo2fa_compare1').toggle();
		}


		var multisite = !1;

       function mo2f_change_instance_value(e, r, o, s = !1) {
				let p = 0,
					u = 0,
					n = 0;
				if (s) p = jQuery(o).find(":selected").val(), jQuery("#number_of_subsites_premium,#number_of_subsites_all_inclusive").not(o).val(p);
				else if (u = jQuery(o).find(":selected").val(), jQuery("#" + r ).not(o).val(u), "" != e) {
					document.getElementById(e).value = u;
					var c = jQuery("#" + e).find(":selected").data("price");
					jQuery("." + e.replace("select", "price")).text(c)
				}
				multisite && (n = jQuery("#number_of_subsites_premium").find(":selected").data("price"));
				var l = jQuery("#" + r).find(":selected").data("price"),
					a = jQuery("#" + r).find(":selected").val();
				jQuery("." + r.replace("select", "price")).text(parseInt(l) + parseInt(n) * parseInt(a));
        }
        	
                    
		
	 

    </script>
