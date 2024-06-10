<?php 
// get all plugins list
$all_plugins = get_plugins();
$plugins = array();
$form = "";
$plugins["Default WP login form"] = "Default WP login form";
foreach ($all_plugins as $plugin_name=>$plugin_details){
    $plugins[$plugin_name] = $plugin_details["Name"];
}
unset($plugins['miniorange-2-factor-authentication/miniorange_2_factor_settings.php']);
    
$my_theme = wp_get_theme();
if ( $my_theme->exists() )
	$theme_name = $my_theme["Name"];

?>
<style>
    #mo2f_trial_request_form input[type=text], #mo2f_trial_request_form input[type=email], #mo2f_trial_request_form input[type=tel], #mo2f_trial_login_form 
    {
        width: 300px;
    }

    #mo2f_trial_request_form input[type=number]
    {
        width: 80px;
    }
</style>
<div class="mo2f_table_layout mo2f_table_layout1">
		<h3> Trial Request Form : <div style="float: right;">
			<?php
			echo '<a class="mo_wpns_button mo_wpns_button1 mo2f_offer_contact_us_button" href="'.esc_url($two_fa).'">Back</a>';
			?>
		</div></h3>
		<form method="post" id="mo2f_trial_request_form">
			<input type="hidden" name="option" value="mo2f_trial_request_form" />
			<input type="hidden" name="nonce" value="<?php echo esc_html(wp_create_nonce('mo2f_trial-nonce'))?>">
			<table cellpadding="4" cellspacing="4">
				<tr>
					<td><strong>Email ID : </strong></td>
					<td><input required type="email" name="mo2f_trial_email" value="<?php echo esc_html(get_option('mo2f_email'));?>" placeholder="Email id"  /></td>
				</tr>
				<tr>
					<td><strong>Phone No. : </strong></td>
					<td><input required type="tel" name="mo2f_trial_phone"  id= "mo2f_phone" value="<?php echo esc_html($user_phone); ?>" /></td>
				</tr>
				<tr>
					<td valign=top ><strong>Request a Trial for : </strong></td>
					<td>
							<p style = "margin-top:0px">
							    <input type= 'radio' name= 'mo2f_trial_plan' id="mo2f_sites_for_2fa" onchange="mo2f_display_field(this)" value="All Inclusive" required >All Inclusive (Unlimited Users + Advanced Features)<br>
                        <div id="mo2f_sites_for_2fa_field" name= 'mo2f_trial_plan_field' style="display: none">&emsp;Number of sites on which you intend to enable 2FA  : <input type="number" min="1" name="mo2f_number_of_sites_1" value="1" disabled required/></div>
							</p>
							<p><input type= 'radio' name= 'mo2f_trial_plan' id="mo2f_users_for_2fa" onchange="mo2f_display_field(this)" value="Enterprise" required >Enterprise(Unlimited sites)<br></p>
                        <div id="mo2f_users_for_2fa_field" name= 'mo2f_trial_plan_field'style="display: none">&emsp;Number of users who will use 2FA  : <input type="number" min="5" name="mo2f_number_of_users_2" value="5" disabled required/></div>

                        <p><input type= 'radio' name= 'mo2f_trial_plan' id="mo2f_confused" onchange="mo2f_display_field(this)" value="notSure" required >Need help in choosing the plan?<br>
                            <div id="mo2f_confused_field" name= 'mo2f_trial_plan_field' style="display: none"><table disabled ><td>&emsp;Number of users who will use 2FA  : </td><td><input type="number" min="5" name="mo2f_number_of_users_3" value="5" required/></td>
                                <td>&emsp;Number of sites on which you intend to enable 2FA  : </td><td><input type="number" min="1" name="mo2f_number_of_sites_3"  id= "mo2f_number_of_sites" value="1" required/></td></table></div>
                        </p>
                        <p>
                            Authentication method you prefer to use  : <select name="mo2f_authentication_method" id="mo2f_authentication_method">
                                                                            <option value="TOTP">Time- based OTP</option>
                                                                            <option value="SMS">SMS based Authentication</option>
                                                                            <option value="Email">Email based Authentication</option>
                                                                            <option value="Others">Whatsapp, Telegram and Others</option>
                                                                        </select>

                        </p>
                            <a href="<?php echo esc_url($upgrade_url); ?>" target="_blank">Check out our Plans</a>

					</td>
				</tr>
                
                <tr>
                    <td><strong>Login Form:</strong></td>
                    <td>
                        <select name="mo2f_trial_login_form" id="mo2f_trial_login_form">
                            <?php
                            foreach ($plugins as $plugin_name){ 
                                ?>
                                    <option value="<?php echo esc_html($plugin_name); ?>"><?php echo esc_html($plugin_name); ?></option>
                                <?php 
                            } 
                            ?>
                            <option value="Other">Other Login Form</option>
                        </select>
                        <input type="text" id="mo2f_other_login_form" name="mo2f_other_login_form" value="" placeholder="Name of the login form" >
                    </td>
                </tr>
                <tr>
                    <td><strong>Theme:</strong></td>
                    <td><input required type="text" name="mo2f_trial_theme" value="<?php echo esc_html($theme_name) ?>" placeholder="Wordpress Theme" required/></td>
                </tr>
			</table>
			<div style="padding-top: 10px;">
			     <p ><b><i>NOTE: You will receive an email with your trial license key that allows you to use the premium plugin for 7 days. If you choose to purchase the plugin, you can use the license key you receive to convert the trial version into the fully functional version.
                                         You will not need to reinstall the plugin after you purchase a license.</i></b></p>
				    <input type="submit" name="submit" value="Submit Trial Request" class="mo2f_trial_submit_button"/>

			</div>
		</form>		
	</div>	
<script>
    
    jQuery("#mo2f_phone").intlTelInput();

    function mo2f_display_field(elmt){
        var inputValue = elmt.id;
        var targetBox = jQuery("#" + inputValue + "_field");
        jQuery("div[name='mo2f_trial_plan_field']").not(targetBox).hide();
        jQuery(targetBox).show();
        jQuery(targetBox).children().removeAttr('disabled');
        jQuery("div[name='mo2f_trial_plan_field']").not(targetBox).children().prop('disabled', true);
    }
    
    jQuery(document).ready(function(){
        
        var mo2f_trial_query_sent = "<?php echo esc_html(get_site_option('mo2f_trial_query_sent')) ?>"
        if(mo2f_trial_query_sent == 1){
            jQuery(':input[type="submit"]').prop('disabled', true);
            jQuery(':input[type="submit"]').attr('title','You have already sent a trial request for premium plugin. We will get back to you on your email soon.' );
            jQuery(':input[type="submit"]').css('color', 'white');
            jQuery(':input[type="submit"]').css('box-shadow', 'none');
        }
        jQuery('#mo2f_other_login_form').hide();
        jQuery('#mo2f_trial_login_form').click(function(){
        var other_login_form = jQuery('#mo2f_trial_login_form').val();
        if(other_login_form != 'Other')
            jQuery('#mo2f_other_login_form').hide();
        else 
            jQuery('#mo2f_other_login_form').show();
        });
        
    });
</script>
