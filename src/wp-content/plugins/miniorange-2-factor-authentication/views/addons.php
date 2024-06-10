<?php

	if (get_option('mo_2factor_user_registration_status') == 'MO_2_FACTOR_PLUGIN_SETTINGS') {
		$is_customer_registered = 'MO_2_FACTOR_PLUGIN_SETTINGS';

	}

	$mo2f_feature_description_set_addon = array(
	"This will allow you to set a time limit on the user's session. After that time, the user would be logged out.",
	"Sharing passwords will not work. Only one user will be able to login from one account.",
	"Admin can set the number of allowed deivces per user to login.",
	"This will allow you to logout a Wordpress user who was inactive for a period of time.",
	"Set a fixed time per user session and force log out after that time irrespective of user activity.",
	"Admins can decide the number of active sessions for a particular account. Limiting active sessions prevents friends and family share and access website at the same time.",
	"Users login with Email without worrying for passwords. It only works with 2fa.",
	"You can login with your phone number, OTP will send on your mobile phone, you can skip password for login.",
	"You can login with your username, you can skip password for login.",
	);
?>
<div id="mo_addon_features_only" style="margin-top: 3%;">	

			<div id="mo2f_payment_option" style="margin-top: -2%;width: 93.5%;margin-left: 0%;border: none;box-shadow: none;background: none; padding: 5px 20px 30px 20px;">
       	<div>
       	<div>
			
			<center>
				<div style="width: 92%;background-color: white;padding: 10px;border-top: 4px solid #2271b1">
					<div style="float: left;">
			    <?php echo '<a class="button button-primary button-large" href="'.esc_url($two_fa).'">Back</a>';?>
				</div>
					<h1 style="margin-right: 8%;">AddOns</h1>
				</div>
			</center>
		</div>
           <div class="mo_2fa_container">
           <div class="mo_2fa_card-deck">
        <div class="mo_2fa_card mo_2fa_animation" style="border-top: 4px solid #2271b1;
    background-color: white;width: 30%;margin: 10px;">
                <div class="mo_2fa_Card-header">
                <h3>Learning Management System / Online Courses</h3>
                </div>
                <hr>
                <div class="mo_2fa_card-body" style="padding-bottom: 0%;">
	                <h4>
						<span>Session Handling</span>
						<?php echo mo2f_addon_features_on_hover($mo2f_feature_description_set_addon[0]);?>

						<br>
						<span>Prevent Account Sharing</span>
						<?php echo mo2f_addon_features_on_hover($mo2f_feature_description_set_addon[1]);?>
						<br>
						<span>Restrict no of device per user</span>
						<?php echo mo2f_addon_features_on_hover($mo2f_feature_description_set_addon[2]);?>
					</h4>
                </div>
                <hr>
                <div style="text-align: center;">
						<b><a href="mailto:2fasupport@xecurify.com" class = "button button-primary button-large "><i>Contact Us</i></a></b>
		    	</div><br>
		    	<?php echo mo2f_addon_contact_us();?>
        </div>
            <div class="mo_2fa_card mo_2fa_animation" style="border-top: 4px solid #2271b1;
    background-color: white;width: 30%;margin: 10px;">
                <div class="mo_2fa_Card-header">
                <h3>User Session Control</h3>
                </div>
                <hr>
                <div class="mo_2fa_card-body" style="padding-bottom: 0%;">
                
				<h4>
						<span>Idle Session Control</span>
						<?php echo mo2f_addon_features_on_hover($mo2f_feature_description_set_addon[3]);?>
						<br>
						<span>User Session Timeout</span>
						<?php echo mo2f_addon_features_on_hover($mo2f_feature_description_set_addon[4]);?>
						<br>
						<span>Limit Simultaneous Session</span>
						<?php echo mo2f_addon_features_on_hover($mo2f_feature_description_set_addon[5]);?>
					</h4>
                </div>
				<hr>
                <div style="text-align: center;">
						<b><a href="mailto:2fasupport@xecurify.com" class = "button button-primary button-large "><i>Contact Us</i></a></b>
		    	</div><br>
		    	<?php echo mo2f_addon_contact_us();?>

                </div>
        <div class="mo_2fa_card mo_2fa_animation" style="border-top: 4px solid #2271b1;background-color: white;width: 30%;margin: 10px;">
                <div class="mo_2fa_Card-header">
                <h3>Password-Less Login</h3>
                </div>
                <hr>
                <div class="mo_2fa_card-body" style="padding-bottom: 0%;">
				<h4>
						<span>Login with email or</span>
						<?php echo mo2f_addon_features_on_hover($mo2f_feature_description_set_addon[6]);?>
						<br>
						<span>Login with Phone or</span>
						<?php echo mo2f_addon_features_on_hover($mo2f_feature_description_set_addon[7]);?>
						<br>
						<span>Login with username only</span>
						<?php echo mo2f_addon_features_on_hover($mo2f_feature_description_set_addon[8]);?>
					</h4>
                </div><hr>
                <div style="text-align: center;">
						<b><a href="mailto:2fasupport@xecurify.com" class = "button button-primary button-large "><i>Contact Us</i></a></b>
		    	</div><br>
		    	<?php echo mo2f_addon_contact_us();?>
        </div>
        <div class="mo_2fa_card mo_2fa_animation" style="border-top: 4px solid #2271b1;background-color: white;width: 30%;margin: 10px;">
                <div class="mo_2fa_Card-header">
                <h3>WooCommerce</h3>
                </div>
                <hr>
                <div class="mo_2fa_card-body" style="padding-bottom: 0%;">
				<h4>
						<span>OTP on Login Page</span>
						<br>
						<span>OTP on Registration</span>
						<br><br>
					</h4>
                </div><hr>
                <div style="text-align: center;">
						<b><a href="mailto:2fasupport@xecurify.com" class = "button button-primary button-large "><i>Contact Us</i></a></b>
		    	</div><br>
		    	<?php echo mo2f_addon_contact_us();?>
        </div>
        <div class="mo_2fa_card mo_2fa_animation" style="border-top: 4px solid #2271b1;background-color: white;width: 30%;margin: 10px;">
                <div class="mo_2fa_Card-header">
                <h3>User Registration</h3>
                </div>
                <hr>
                <div class="mo_2fa_card-body" style="padding-bottom: 0%;">
				<h4>
						<span>OTP on Login Page</span>
						<br>
						<span>OTP on Registration</span>
						<br><br>
					</h4>
                </div><hr>
                <div style="text-align: center;">
						<b><a href="mailto:2fasupport@xecurify.com" class = "button button-primary button-large "><i>Contact Us</i></a></b>
		    	</div><br>
		    	<?php echo mo2f_addon_contact_us();?>
        </div>
        <div class="mo_2fa_card mo_2fa_animation" style="border-top: 4px solid #2271b1;background-color: white;width: 30%;margin: 10px;">
                <div class="mo_2fa_Card-header">
                <h3>Ultimate Member</h3>
                </div>
                <hr>
                <div class="mo_2fa_card-body" style="padding-bottom: 0%;">
				<h4>
						<span>OTP on Login Page</span>
						<br>
						<span>OTP on Registration</span>
						<br>
						<span>OTP on Reset Password</span>
					</h4>
                </div><hr>
                <div style="text-align: center;">
						<b><a href="mailto:2fasupport@xecurify.com" class = "button button-primary button-large "><i>Contact Us</i></a></b>
		    	</div><br>
		    	<?php echo mo2f_addon_contact_us();?>
        </div>

          <div class="mo_2fa_card mo_2fa_animation" style="border-top: 4px solid #2271b1;
    background-color: white;width: 30%;margin: 10px">
                <div class="mo_2fa_Card-header">
                <h3>Personalization Add-on Features</h3>
                </div>
                <hr>
                <h1 class="mo_wpns_upgrade_pade_pricing" style="color: #2271b1">$199</h1>
                <div class="mo_2fa_card-body" style="padding-bottom: 0%;">
                	<h3>Features</h3>
                <h4>
					Custom UI of 2FA popups<br>
					Customize 'powered by' Logo<br>
					Custom Email and SMS Templates<br>
					Customize Plugin Icon and Plugin Name<br><br>
				</h4>
                </div>
					<hr>
                <div style="text-align: center;">
						<?php	if( isset($is_customer_registered) ) 
						{
							?>
	                         <button class="button button-primary button-large " onclick="mo2f_upgradeform('wp_2fa_addon_personalization', 'addon_plan')" >Purchase</button>
			                <?php 
			            }else
			            { ?>
							<button class="button button-primary button-large " onclick="mo2f_register_and_upgradeform('wp_2fa_addon_personalization', 'addon_plan')" >Purchase</button>
			                <?php } 
			                ?>
		    		</div>
		    		<br>
            </div>
          <div class="mo_2fa_card mo_2fa_animation" style="border-top: 4px solid #2271b1;
    background-color: white;width: 30%;margin: 10px">
                <div class="mo_2fa_Card-header">
                <h3>Short Codes Add-on Features</h3>
                </div>
                <hr>
                <h1 class="mo_wpns_upgrade_pade_pricing" style="color: #2271b1">$99</h1>
                <div class="mo_2fa_card-body" style="padding-bottom: 0%;">
                <h3>Features</h3>
                <h4>
							Turn on/off 2-factor by user<br>
							Configure Security Questions by user<br>
							Remember Device from custom forms<br>
							Configure Google Authenticator by user<br>
							On-Demand ShortCodes for specific fuctionalities
						
					</h4>
				
                </div><hr>
                <div style="text-align: center;">
						<?php	if( isset($is_customer_registered) ) {
							?>
	                         <button class="button button-primary button-large " onclick="mo2f_upgradeform('wp_2fa_addon_shortcode', 'addon_plan')" >Purchase</button>
			                <?php 
			            }else
			            { ?>
							<button class="button button-primary button-large " onclick="mo2f_register_and_upgradeform('wp_2fa_addon_shortcode', 'addon_plan')" >Purchase</button>
			                <?php } 
			                ?>
		    		</div>
		    		<br>
                </div>
          </div>
          </div>
     </div>
 </div>

</div>
<form class="mo2f_display_none_forms" id="mo2fa_loginform"
                  action="<?php echo esc_url(MO_HOST_NAME . '/moas/login'); ?>"
                  target="_blank" method="post">
                <input type="email" name="username" value="<?php echo esc_html(get_option( 'mo2f_email' )); ?>"/>
                <input type="text" name="redirectUrl"
                       value="<?php echo esc_url(MO_HOST_NAME) . '/moas/initializepayment'; ?>"/>
                <input type="text" name="requestOrigin" id="requestOrigin"/>
            </form>

            <form class="mo2f_display_none_forms" id="mo2fa_register_to_upgrade_form"
                   method="post">
                <input type="hidden" name="requestOrigin" />
                <input type="hidden" name="mo2fa_register_to_upgrade_nonce"
                       value="<?php echo esc_html(wp_create_nonce( 'miniorange-2-factor-user-reg-to-upgrade-nonce' )); ?>"/>
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
</script>
<?php
function mo2f_addon_features_on_hover($mo2f_addon_feature)
{
	return 	'<div class="mo2f_tooltip_addon">
			<span class="dashicons dashicons-info mo2f_info_tab"></span>
			<span class="mo2f_tooltiptext_addon" >'. esc_html($mo2f_addon_feature) .'
			</span>
		</div>';
}
function mo2f_addon_contact_us()
{
	return 	'<div>
		    		<b>Contact us at <a href="mailto:2fasupport@xecurify.com">2fasupport@xecurify.com</a> or <a href="mailto:info@xecurify.com">info@xecurify.com</a></b>
		    	</div><br>';
}
?>