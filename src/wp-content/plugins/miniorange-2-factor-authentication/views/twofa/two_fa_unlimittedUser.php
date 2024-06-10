<?php
$setup_dirName = dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.'twofa'.DIRECTORY_SEPARATOR.'link_tracer.php';
$settings_tab_tooltip_array = array ('Disable this to temporarily disable 2FA prompt for all users','If you disable this checkbox, A separate screen would be presented to users for 2FA','If you disable this checkbox, user enrollment (forcing users to setup 2FA after initial login) will not be done','Selecting the below roles will enable 2-Factor for all users associated with that role.','Plugin debug log file is very helpful to debug the issue in case you face.', 'If you enable this checkbox then you will have a choice to login using any of the methods that is already configured','Users can either have a grace period to configure 2FA (users who don\'t have 2fa setup after grace period, will be enforced to setup 2FA)');

include $setup_dirName;
function miniorange_2_factor_user_roles($current_user) {
    include dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.'twofa'.DIRECTORY_SEPARATOR.'link_tracer.php';
	global $wp_roles;
	if (!isset($wp_roles))
		$wp_roles = new WP_Roles();
     $upgrade_url    = add_query_arg(array('page' => 'mo_2fa_upgrade'), sanitize_url($_SERVER['REQUEST_URI']));?>

	<div><span style="font-size:16px;">Roles<div style="float:right;">Custom Redirection URL <a href="<?php echo esc_url($upgrade_url); ?>" style="color: red">[ PREMIUM ]</a>&nbsp;&nbsp;&nbsp;
   </span></a>
    </div></span><br /><br />
    <?php 
    if(is_multisite()){
        $first_role=array('superadmin'=>'Superadmin');
        $wp_roles->role_names = array_merge($first_role,$wp_roles->role_names);
    }
	 foreach($wp_roles->role_names as $id => $name) {
		$setting = get_site_option('mo2fa_'.$id);
		?>
        <div>
            <input type="checkbox" name="role" value="<?php echo 'mo2fa_'.esc_html($id); ?>"
				<?php
				
                    if(get_site_option('mo2fa_'.$id))
                        echo 'checked' ;
                    else
                        echo 'unchecked'; 
				?>/>
			<?php
			echo esc_html($name);
			?>
            <input type="text" class="mo2f_table_textbox" style="width:50% !important;float:right;" id="<?php echo 'mo2fa_'.esc_html($id); ?>_login_url" value="<?php echo esc_url(site_url()); ?>"
				<?php
				echo 'disabled' ;
				?>
            />
            
        </div>
        <br/>
		<?php
	}
	print '</div>';
}
$user = wp_get_current_user();
$configured_2FA_method = $Mo2fdbQueries->get_user_detail( 'mo2f_configured_2FA_method', $user->ID );
$configured_meth = array();
$configured_meth = array('Email Verification','Google Authenticator','Security Questions','Authy Authenticator','OTP Over Email','OTP Over SMS');
$method_exisits = in_array($configured_2FA_method, $configured_meth);
$imagepath=plugins_url( '/includes/images/', dirname(dirname(__FILE__ )));

?>
<?php
if(current_user_can('administrator')){
	?>
    <div id="disable_two_factor_tour">


        <h2>Enable 2FA for Users<?php mo2f_setting_tooltip_array($settings_tab_tooltip_array[0]); ?>
            <a href='<?php echo esc_url($two_factor_premium_doc['Enable/disable 2-factor Authentication']);?>' target="_blank">
            <span class="dashicons dashicons-text-page" title="More Information" style="font-size:19px;color:#4a47a3;float: right;"></span>

        </a></h2>

        <div>
            <form name="f" method="post" action="" >
                <input type="hidden" id="mo2f_nonce_enable_2FA" name="mo2f_nonce_enable_2FA"
                       value="<?php echo esc_html(wp_create_nonce( "mo2f-nonce-enable-2FA" )) ?>"/>
                <label class="mo_wpns_switch" style="float: right">
                <input type="checkbox" onChange="mo_toggle_twofa()" style="padding-top: 50px;" id="mo2f_enable_2faa"
                       name="mo2f_enable_2fa"
                       value="<?php MoWpnsUtility::get_mo2f_db_option('mo2f_activate_plugin', 'get_option') ?>"<?php  checked( MoWpnsUtility::get_mo2f_db_option('mo2f_activate_plugin', 'get_option') == 1 );?>/>
                <span class="mo_wpns_slider"></span>
                </label>
                <p>If you enable this checkbox, Two-Factor will be invoked for any user during login
                </p>
            </form>
             <form name="f" method="post" action="" id="mo2f_enable_debuglog_form_id">
             <input type="hidden" id="mo2f_nonce_enable_debug_log" name="mo2f_nonce_enable_debug_log"
                       value="<?php echo esc_html(wp_create_nonce( "mo2f-nonce-enable-debug-log" )) ?>"/>
                <h3>
                   </br> <hr>
                <?php
                echo mo2f_lt( 'Enable plugin log ' );
                mo2f_setting_tooltip_array($settings_tab_tooltip_array[4]);
                ?>
                </h3>
                </hr>
                <p><i>If you enable this checkbox, the plugin log will be enable.</i>
                <label class="mo_wpns_switch" style="float: right;">
                <input type="checkbox" onChange="mo2f_debug_log()" style="padding-top: 50px;" id="mo2f_debug_log_id"
                       name="mo2f_enable_debug_log"
                       value="<?php MoWpnsUtility::get_mo2f_db_option('mo2f_enable_debug_log', 'site_option') ?>"<?php  checked( MoWpnsUtility::get_mo2f_db_option('mo2f_enable_debug_log', 'site_option') == 1 );?>/>
                <span class="mo_wpns_slider"></span>
                </label>
                </p>

           </form>
        </div>
       </br>
    <?php if(MoWpnsUtility::get_mo2f_db_option('mo2f_enable_debug_log', 'site_option') == 1) { ?>
        <form name="f" method="post" action="" id="mo2f_download_log_file">
                <input type="submit" class="button button-primary" value="Download log file"
                   id="mo2f_debug_form"  name= "mo2f_debug_form">
                 <input type="button" class="button button-primary" value="Delete log file"
                   id="mo2f_debug_delete_form"  name= "mo2f_debug_delete_form">
                <input type="hidden" id="mo2f_download_log" name="mo2f_nonce_download_log"
                   value="<?php echo esc_html(wp_create_nonce( "mo2f-nonce-download-log" )) ?>"/>
                <input type="hidden" id="mo2f_download_log" name="option"
                   value="log_file_download"/>
        </form>
         <form name="f" method="post" action="" id="mo2f_delete_log_file">
               <input type="hidden" id="mo2f_delete_log" name="mo2f_nonce_delete_log"
                   value="<?php echo esc_html(wp_create_nonce( "mo2f-nonce-delete-log" )) ?>"/>
                <input type="hidden" id="mo2f_delete_logs" name="option"
                   value="log_file_delete"/>
           </form>
      <?php } ?>
     </br><hr>
     </br>
        <h2>2FA Prompt on Wordpress Login Page
       <a class=" btn-link" data-toggle="collapse" id="showpreviewwploginpage" href="#previewwploginpage" aria-expanded="false"><?php echo __('See preview','miniorange-2-factor-authentication');?></a>
           <?php mo2f_setting_tooltip_array($settings_tab_tooltip_array[1]); ?>
   </h2>
        <div class="mo2f_collapse" id="previewwploginpage" style="height:300px;">
                    <center><br>
                    <img style="height:300px;" src="<?php echo esc_url($imagepath).'2fa-on-login-page.png';?>" >
                    </center>
                 </div>
        <div>
            <form name="f" method="post" action="" >
                <input type="hidden" id="mo2f_nonce_enable_2FA_prompt_on_login" name="mo2f_nonce_enable_2FA_prompt_on_login"
                       value="<?php echo esc_html(wp_create_nonce( "mo2f-enable-2FA-on-login-page-option-nonce" )) ?>"/>

                <label class="mo_wpns_switch" style="float: right">
                <input type="checkbox" onChange="mo_toggle_twofa_prompt_on_login()" style="padding-top: 20px;" id="mo2f_enable_2faa_prompt_on_login"
                       name="mo2f_enable_2fa_prompt_on_login"
                       value="<?php MoWpnsUtility::get_mo2f_db_option('mo2f_enable_2fa_prompt_on_login_page', 'site_option') ?>"<?php  checked( MoWpnsUtility::get_mo2f_db_option('mo2f_enable_2fa_prompt_on_login_page', 'site_option') == 1 );?>/>

                <span class="mo_wpns_slider"></span>
                </label>
                <p>If you enable this checkbox, an OTP input field will be shown on the login page itself <br><i> (Supported for Google Authenticator and miniOrange Soft Token only)</i>
                </p>
            </form>
        </div>
     </br><hr>
        <h2>On the Fly 2FA Configuration
        <?php mo2f_setting_tooltip_array($settings_tab_tooltip_array[2]); ?>
        </h2>

        <div>
            <form name="f" method="post" action="" >
                <input type="hidden" id="mo2f_nonce_enable_inline" name="mo2f_nonce_enable_inline"
                       value="<?php echo esc_html(wp_create_nonce( "mo2f-nonce-enable-inline" )) ?>"/>
                <label class="mo_wpns_switch" style="float: right;">
                <input type="checkbox" onChange="mo_toggle_inline()" style="padding-top: 50px;float: right;" id="mo2f_inline_registration"
                       name="mo2f_inline_registration"
                       value="<?php MoWpnsUtility::get_mo2f_db_option('mo2f_inline_registration', 'site_option') ?>" <?php  checked( MoWpnsUtility::get_mo2f_db_option('mo2f_inline_registration', 'site_option') == 1 );?>/>
                <span class="mo_wpns_slider"></span>
                </label>
                <p>Force 2FA Setup by users after Initial login</p>
            </form>
        </div>
     </br><hr>
 <h2>Should users be given a grace period or should they be directly enforced for 2FA setup?
        <?php mo2f_setting_tooltip_array($settings_tab_tooltip_array[6]); ?>
    </h2>
    <div>
    <form name="f" method="post" action="">
    
           
            <input type="hidden" id="mo2f_nonce_enable_grace_period" name="mo2f_nonce_enable_grace_period" value="<?php echo esc_html(wp_create_nonce( "mo2f-nonce-enable-grace-period" )) ?>"/>
            <input type="radio" name="mo2f_grace_period" id="mo2f_no_grace_period" value="off" 	<?php checked( MoWpnsUtility::get_mo2f_db_option('mo2f_grace_period','site_option')=="off"); ?>/>
            <span> Users should be directly enforced for 2FA setup </span><br><br>

            <input type="radio" name="mo2f_grace_period" id="mo2f_use_grace_period" value="on" 	<?php checked( MoWpnsUtility::get_mo2f_db_option('mo2f_grace_period','site_option')=="on"); ?>/>
            <span> Give users a grace period to configure 2FA (Users will be enforced to setup 2FA after grace period expiry).&nbsp;&nbsp;&nbsp; </span>
            
            
            </br>
           
            <div id="mo2f_grace_period_show" style="display: <?php echo(get_option("mo2f_grace_period")=="on")?"block":"none";?>;" >
                  </br>
                  <span style="font-size:15px;"><b>Grace Period:</b></span>
                  
                  <input type="number" id="mo2f_grace_period" name= "mo2f_grace_period_value" value="<?php echo esc_html(get_option('mo2f_grace_period_value'));?>" min=1 max=10>
                     
                  <input type="radio" name="mo2f_grace_period_type" id="mo2f_grace_hour" value="hours" <?php  checked( MoWpnsUtility::get_mo2f_db_option('mo2f_grace_period_type','site_option')=="hours"); ?>/> hours
                     
                  <input type="radio" name="mo2f_grace_period_type" id="mo2f_grace_day" value= "days" 	<?php checked( MoWpnsUtility::get_mo2f_db_option('mo2f_grace_period_type','site_option')=="days"); ?>/> days
                
                  
                </br>
    
            </div>
           </br>     
            <input type="button" style="float: left;" id="mo2f_save_grace_period"  name="submit" value="Save Settings" class="button button-primary button-large "/>
    
    </form>
    </div>
                    
    </br></br></br><hr>

    

<script>
jQuery(document).ready(function($){
    jQuery(function(){
           

        jQuery("#mo2f_use_grace_period").click(function()
        {
            if(jQuery(this).is(':checked'))
            {
                jQuery("#mo2f_grace_period_show").show();
                
               
                jQuery("#mo2f_grace_period").focus();
            }
        });
            jQuery("#mo2f_no_grace_period").click(function()
            {
                 if(jQuery(this).is(':checked'))
                 {
                      jQuery("#mo2f_grace_period_show").hide();
                 }
            });
          jQuery("#mo2f_grace_hour").click(function(){
            jQuery("#mo2f_grace_period").focus();
          });
          jQuery("#mo2f_grace_day").click(function(){
            jQuery("#mo2f_grace_period").focus();
          });
       
    });
});
</script>
     <h2>Enable the login with all configured methods
        <?php mo2f_setting_tooltip_array($settings_tab_tooltip_array[5]); ?>
        </h2>   
        <div>
            <form name="f" method="post" action="" >
                <label class="mo_wpns_switch" style="float: right;">
                <input type="checkbox" onChange="mo_toggle_configured_methods()" style="padding-top: 50px;float: right;" id="mo2f_nonce_enable_configured_methods"
                       name="mo2f_nonce_enable_configured_methods"
                       value="<?php MoWpnsUtility::get_mo2f_db_option('mo2f_nonce_enable_configured_methods', 'site_option') ?>" <?php  checked( MoWpnsUtility::get_mo2f_db_option('mo2f_nonce_enable_configured_methods', 'site_option') == true);?>/>
                <span class="mo_wpns_slider"></span>
                </label>
                <p>It will help the user to login with any of the configured methods</p>
            </form>
        </div>
        </br><hr>
    <script type="text/javascript">

        jQuery('#mo2f_debug_delete_form').click(function(){

         var data =  {
                'action'                        : 'mo_two_factor_ajax',
                'mo_2f_two_factor_ajax'         : 'mo2f_delete_log_file',
                'mo2f_nonce_delete_log'         :  jQuery('#mo2f_delete_log').val(),

            };
            jQuery.post(ajaxurl, data, function(response) {
                var response = response.replace(/\s+/g,' ').trim();
                if (response == "true"){
                    success_msg("Log file deleted.");
                }else{
                    error_msg("Log file is not available.");
                }
            });
        });

          jQuery('#mo2f_save_grace_period').click(function(){

                var data =  {
                            'action'                        : 'mo_two_factor_ajax',
                            'mo_2f_two_factor_ajax'         : 'mo2f_grace_period_save',
                            'mo2f_grace_period_nonce'       :  jQuery('#mo2f_nonce_enable_grace_period').val(),
                            'mo2f_graceperiod_use'          :  jQuery('#mo2f_use_grace_period').is(":checked"),
                            'mo2f_graceperiod_no'           :  jQuery('#mo2f_no_grace_period').is(":checked"),
                            'mo2f_graceperiod_hour'         :  jQuery('#mo2f_grace_hour').is(":checked"),
                            'mo2f_graceperiod_day'          :  jQuery('#mo2f_grace_day').is(":checked"),
                            'mo2f_graceperiod_value'        :  jQuery('#mo2f_grace_period').val(),
                          };
                jQuery.post(ajaxurl, data, function(response) {
                    var response = response.replace(/\s+/g,' ').trim();
                    if (response == "true"){
                        success_msg("Grace period saved successfully");
                    }else if(response == 'invalid_input'){
                        error_msg("Please enter valid input");
                    }else{
                        error_msg("Error while saving the settings");
                    }
                });
          });

        function mo_toggle_twofa(){
            var data =  {
                'action'                        : 'mo_two_factor_ajax',
                'mo_2f_two_factor_ajax'         : 'mo2f_enable_disable_twofactor',
                'mo2f_nonce_enable_2FA'         :  jQuery('#mo2f_nonce_enable_2FA').val(),
                'mo2f_enable_2fa'               :  jQuery('#mo2f_enable_2faa').is(":checked"),
            };
            jQuery.post(ajaxurl, data, function(response) {
                var response = response.replace(/\s+/g,' ').trim();
                if (response == "true"){
                    success_msg("Two factor is now enabled.");
                }else if(response == "error"){
                    error_msg("Error occured while saving the settings.");
                }else{
                    error_msg("Two factor is now disabled.");
                }
            });

        }
        function mo2f_debug_log(){
            var data =  {
                'action'                        : 'mo_two_factor_ajax',
                'mo_2f_two_factor_ajax'         : 'mo2f_enable_disable_debug_log',
                'mo2f_nonce_enable_debug_log'   :  jQuery('#mo2f_nonce_enable_debug_log').val(),
                'mo2f_enable_debug_log'         :  jQuery('#mo2f_debug_log_id').is(":checked"),
            };
            jQuery.post(ajaxurl, data, function(response) {
                var response = response.replace(/\s+/g,' ').trim();
                if (response == "true"){
                    success_msg("Plugin log is now enabled.");

                }else{
                    error_msg("Plugin log is now disabled.");

                }
            });

        }


        function mo_toggle_twofa_prompt_on_login(){
            var data =  {
                'action'                        : 'mo_two_factor_ajax',
                'mo_2f_two_factor_ajax'         : 'mo2f_enable_disable_twofactor_prompt_on_login',
                'mo2f_nonce_enable_2FA_prompt_on_login'         :  jQuery('#mo2f_nonce_enable_2FA_prompt_on_login').val(),
                'mo2f_enable_2fa_prompt_on_login'               :  jQuery('#mo2f_enable_2faa_prompt_on_login').is(":checked"),
            };
            jQuery.post(ajaxurl, data, function(response) {
                var response = response.replace(/\s+/g,' ').trim();
                if (response == "true"){
                    success_msg("Two factor prompt on login is now enabled.");
                }else if(response == "false_method_onprem"){
                    error_msg("This field is supported only for Google Authenticator and miniOrange softToken.");
                    jQuery("#mo2f_enable_2faa_prompt_on_login").prop("checked",false);
                }else if(response == 'false_method_cloud'){
                    error_msg("This field is supported only for Google/Authy Authenticator and miniOrange softToken.");
                    jQuery("#mo2f_enable_2faa_prompt_on_login").prop("checked",false);
                }else{
                    error_msg("Two factor prompt on login is now disabled.");
                }
            });

        }
        function mo_toggle_inline(){
            var data =  {
                'action'                        : 'mo_two_factor_ajax',
                'mo_2f_two_factor_ajax'         : 'mo2f_enable_disable_inline',
                'mo2f_nonce_enable_inline'      :  jQuery('#mo2f_nonce_enable_inline').val(),
                'mo2f_inline_registration'            :  jQuery('#mo2f_inline_registration').is(":checked"),
            };
            jQuery.post(ajaxurl, data, function(response) {
                var response = response.replace(/\s+/g,' ').trim();
                if (response == "true"){
                    success_msg('User enrollment is now enabled.');
                }
                else if (response == "error"){
                    error_msg('Unknown error occured. Please try again!');
                }
                else{
                    error_msg('User Enrollment is now disabled.');
                }
            });

        }
        function mo_toggle_configured_methods(){
            var nonce = '<?php echo esc_html(wp_create_nonce("WAFsettingNonce_configurd_methods"));?>';
            var data =  {
                'action'                                    : 'mo_two_factor_ajax',
                'mo_2f_two_factor_ajax'                     : 'mo2f_enable_disable_configurd_methods',
                'nonce'                                     :  nonce,
                'mo2f_nonce_enable_configured_methods'      :  jQuery('#mo2f_nonce_enable_configured_methods').is(":checked")
            };
            jQuery.post(ajaxurl, data, function(response) {
                var response = response.replace(/\s+/g,' ').trim();
                if (response == "true"){
                    success_msg('Login with the configured method is enabled');
                }
                else if (response == "error"){
                    error_msg('Unknown error occured. Please try again!');
                }
                else{
                    error_msg('Login with the configured method is disabled');
                }
            });

        }
         jQuery('#previewwploginpage').hide();
         jQuery('#showpreviewwploginpage').on('click', function() {
           if ( jQuery("#previewwploginpage").is(":visible") ) {
              jQuery('#previewwploginpage').hide();
          } else if ( jQuery("#previewwploginpage").is(":hidden") ) {
              jQuery('#previewwploginpage').show();
          }
         });
    </script>
	<?php
}


if(current_user_can('administrator'))
{
	?>

        <input type="hidden" name="option" value="" />
        <span>
                        <h2>Select User Roles to enable 2-Factor for <b  style="font-size: 70%;color: red;">(Upto 3 users in Free version)</b>
                        <?php mo2f_setting_tooltip_array($settings_tab_tooltip_array[3]); ?>
                        <a href= '<?php echo esc_url($two_factor_premium_doc['Enable 2FA Role Based']);?>' target="_blank">
                        <span class="dashicons dashicons-text-page" title="More Information" style="font-size:19px;color:#4a47a3;float: right;"></span>
                        </a></h2>
        </br>
        <span>

	                    <?php
	                    echo esc_html(miniorange_2_factor_user_roles($current_user));
	                    ?>
                        <br>
                        </span>
                        <input type="submit" style="float: left;" id="save_role_2FA"  name="submit" value="Save Settings" class="button button-primary button-large" />
                        <br>
        </span>
        <br><br>



    <script>
        jQuery("#save_role_2FA").click(function(){
            var enabledrole = [];
            $.each($("input[name='role']:checked"), function(){
                enabledrole.push($(this).val());
            });
            var mo2fa_administrator_login_url   =   $('#mo2fa_administrator_login_url').val();
            var nonce = '<?php echo esc_html(wp_create_nonce("unlimittedUserNonce"));?>';
            var data =  {
                'action'                        : 'mo_two_factor_ajax',
                'mo_2f_two_factor_ajax'         : 'mo2f_role_based_2_factor',
                'nonce'                         :  nonce,
                'enabledrole'                   :  enabledrole,
                'mo2fa_administrator_login_url' :  mo2fa_administrator_login_url
            };
            jQuery.post(ajaxurl, data, function(response) {
                var response = response.replace(/\s+/g,' ').trim();
                if (response == "true"){
                    success_msg("Settings are saved.");
                }
                else
                {
                    jQuery('#mo2f_confirmcloud').css('display', 'none');
                    jQuery( "#singleUser" ).prop( "checked", false );
                    jQuery('#single_user').css('display', 'none');

                    error_msg("<b>You are not authorized to perform this action</b>. Only <b>"+response+"</b> is allowed. For more details contact miniOrange.");
                }
            });
        });
    </script>

	<?php
}
?>
</div>
<?php
    if(!MO2F_IS_ONPREM && current_user_can('administrator')){
	?>
    

    <script type="text/javascript">

        function reconfigKBA(){
            var data = {
                'action'                    : 'mo_two_factor_ajax',
                'mo_2f_two_factor_ajax'     : 'mo2f_shift_to_onprem',
            };
            jQuery.post(ajaxurl, data, function(response) {

                if(response == 'true'){
                    jQuery('#mo2f_go_back_form').submit();
                    jQuery('#mo2f_configured_2FA_method_free_plan').val('SecurityQuestions');
                    jQuery('#mo2f_selected_action_free_plan').val('configure2factor');
                    jQuery('#mo2f_save_free_plan_auth_methods_form').submit();
                    mo2f_wpns_openTab2fa(setup_2fa);
                }
                else
                {
                    jQuery('#afterMigrate').css('display', 'none');
                    jQuery( "#unlimittedUser" ).prop( "checked", false );
                    jQuery('#ConfirmOnPrem').css('display', 'none');
                    jQuery('#onpremisediv').css('display','inline');
                    error_msg("<b>You are not authorized to perform this action</b>. Only <b>"+response+"</b> is allowed. For more details contact miniOrange.");

                   }
            });
        }
        function reconfigGA(){

            var data = {
                'action'                    : 'mo_two_factor_ajax',
                'mo_2f_two_factor_ajax'     : 'mo2f_shift_to_onprem',
            };
            jQuery.post(ajaxurl, data, function(response) {

                if(response == 'true'){
                    jQuery('#mo2f_go_back_form').submit();
                    jQuery('#mo2f_configured_2FA_method_free_plan').val('GoogleAuthenticator');
                    jQuery('#mo2f_selected_action_free_plan').val('configure2factor');
                    jQuery('#mo2f_save_free_plan_auth_methods_form').submit();
                    mo2f_wpns_openTab2fa(setup_2fa);
                }
                else
                {
                    jQuery('#afterMigrate').css('display', 'none');
                    jQuery( "#unlimittedUser" ).prop( "checked", false );
                    jQuery('#ConfirmOnPrem').css('display', 'none');
                    jQuery('#onpremisediv').css('display','inline');
                    error_msg("<b>You are not authorized to perform this action</b>. Only <b>"+response+"</b> is allowed. For more details contact miniOrange.");

                }
            });
        }

        function emailVerification(){
            jQuery('#reconfigTable').hide();
            jQuery('#Emailreconfig').show();
            jQuery('#reconfig').hide();
            jQuery('#msg').hide();
        }
    </script>

    <script type="text/javascript">

        jQuery('#closeConfirmOnPrem').click(function(){
            document.getElementById('unlimittedUser').checked = false;
            close_modal();
        });
        jQuery('#ConfirmOnPremButton').click(function(){
            jQuery('#ConfirmOnPrem').hide();
            var enableOnPremise = jQuery("input[name='unlimittedUser']:checked").val();
            var nonce = '<?php echo esc_html(wp_create_nonce("unlimittedUserNonce"));?>';
            var data = {
                'action'					: 'mo_two_factor_ajax',
                'mo_2f_two_factor_ajax' 	: 'mo2f_unlimitted_user',
                'nonce' :  nonce,
                'enableOnPremise' :  enableOnPremise
            };
            jQuery.post(ajaxurl, data, function(response) {
                var response = response.replace(/\s+/g,' ').trim();
                if(response =='OnPremiseActive')
                {
                    success_msg("Congratulations! Now you can use 2-factor Authentication for your administrators for free. ");
                    jQuery('#onpremisediv').hide();
                    jQuery('#afterMigrate').show();
                }
                else if(response =='OnPremiseDeactive')
                {
                    error_msg("Cloud Solution deactivated");
                }
                else
                {
                    error_msg("An Unknown Error has occured.");
                }
            });

        });

        jQuery('#emailBack').click(function(){
            jQuery('#reconfigTable').show();
            jQuery('#Emailreconfig').hide();
            jQuery('#msg').show();
            jQuery('#reconfig').show();
        });
        jQuery('#save_email').click(function(){
            var email   = jQuery('#emalEntered').val();
            var nonce   = '<?php echo esc_html(wp_create_nonce('EmailVerificationSaveNonce'));?>';
            var user_id = '<?php echo esc_html(get_current_user_id());?>';

            if(email != '')
            {
                var data = {
                    'action'                    : 'mo_two_factor_ajax',
                    'mo_2f_two_factor_ajax'     : 'mo2f_save_email_verification',
                    'nonce'                     : nonce,
                    'email'                     : email,
                    'user_id'                   : user_id
                };
                jQuery.post(ajaxurl, data, function(response) {

                    var response = response.replace(/\s+/g,' ').trim();
                    if(response=="settingsSaved")
                    {
                        jQuery('#mo2f_configured_2FA_method_free_plan').val('EmailVerification');
                        jQuery('#mo2f_selected_action_free_plan').val('select2factor');
                        jQuery('#mo2f_save_free_plan_auth_methods_form').submit();
                    }
                    else if(response == "NonceDidNotMatch")
                    {
                    error_msg(" There were some issues. Please try again.");
                    }
                    else
                    {
					error_msg("Please enter a valid Email.");
                    }
                });
            }
        });
        jQuery('#closeConfirmOnPrem').click(function(){
            close_modal();
            window.location.reload();
        });

        jQuery('#unlimittedUser').click(function(){
            jQuery('#ConfirmOnPrem').css('display', 'block');
            jQuery('.modal-content').css('width', '35%');

        });


    </script>
    <script type="text/javascript">

    </script>

	<?php
}

 function mo2f_setting_tooltip_array($mo2f_addon_feature){
    echo '<div class="mo2f_tooltip_addon">
            <span class="dashicons dashicons-info mo2f_info_tab"></span>
            <span class="mo2f_tooltiptext_addon" >'. esc_html($mo2f_addon_feature) .'
            </span>
        </div>';
    }
?>