<?php
$setup_dirName = dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.'twofa'.DIRECTORY_SEPARATOR.'link_tracer.php';
include $setup_dirName;
global $is_register;

?>
<div class="mo2f_table_divide_border">
    <form id="settings_from_addon" method="post" action="">
        <input type="hidden" name="option" value="mo_auth_addon_settings_save"/>
        <h2><?php echo mo2f_lt( '1. Remember Device' ); ?>
            
            
            <a class="mo2fa-addons-preview-alignment" onclick="mo2f_rba_functionality()">See Preview</a>
            <a href='<?php echo esc_url($two_factor_premium_doc['Remember Device']);?>'target="_blank">
                <span class="dashicons dashicons-text-page" style="font-size:19px;color:#413c69;float: right;"></span>
            </a>


        </h2>
         <hr>
        <p id="rba_description" >
            It helps you to remember the device where you will not be asked to authenticate the 2-factor if you login from the remembered device. 
        </p>
         <div id="mo2f_hide_login_form" style="display: none;">
            <div class="mo2f_table_layout" style="background-color: aliceblue; border:none;">
            <h2>Device Profile Settings</h2>
            <hr>
            <br>
                <input type="checkbox" id="mo2f_remember_device" name="mo2f_remember_device" value="1" <?php checked( get_option( 'mo2f_remember_device' ) == 1 );echo 'disabled';?> /><?php echo mo2f_lt( 'Enable' ); ?>'<b><?php echo mo2f_lt( 'Remember device' ); ?></b>' <?php echo mo2f_lt( 'option ' ); ?> <br><span style="color:red;">&emsp;(<?php echo mo2f_lt( 'Applicable only for ' ); ?><i><?php echo mo2f_lt( 'Login with password + 2nd Factor.The option is available in Login Settings tab.' ); ?>)</i></span><br><br>
                <div style="margin-left:30px;">
                   <input type="radio" name="1" <?php echo 'disabled';?><?php  checked(true);?>><?php echo " Give users an option to enable";?><b><?php echo" 'Remember Device'";?></b>
                   <br><br>
                   <input type="radio" name="1" <?php echo 'disabled';?>><?php echo"Silently enable ";?><b><?php echo"'Remember Device'";?></b>
                </div>
                <br>
                <div>
                      <?php echo __('Remember Device for', 'miniorange-2-factor-authentication');?> <input type="number" class="mo2f_table_textbox" style="width:10%; margin-left: 1%; margin-right: 1%;" name="mo2fa_device_expiry" <?php if($is_register){}else{ echo 'disabled';} ?> /> <?php echo __('days', 'miniorange-2-factor-authentication');?> .
                      <br><br>
                      <?php echo __('Allow', 'miniorange-2-factor-authentication');?> <input type="number" class="mo2f_table_textbox" style="width:10%; margin-left: 1%; margin-right: 1%;" name="mo2fa_device_limit" <?php if($is_register){}else{ echo 'disabled';} ?> /><?php echo __('devices for users to remember', 'miniorange-2-factor-authentication');?> .
                      <br><br>
                     <?php echo __('Action on exceeding device limit:', 'miniorange-2-factor-authentication');?>
                     &emsp;
                     <input type="radio" name="mo2f_rba_login_limit" value="1" <?php echo 'disabled';?> <?php checked(true);?>>
                     Ask for '<b>Two Factor</b>'  &emsp;
                     <input type="radio" name="mo2f_rba_login_limit" value="0"  <?php echo 'disabled';?>>
                     Deny Access 
                </div>
                <br>
                <div class="mo2f_advanced_options_note" style="background-color: #bfe5e9;padding:12px"><b>Note:</b><?php echo __('Checking this option will enable', 'miniorange-2-factor-authentication');?> '<b>Remember Device</b>'.<?php echo __('In the login from the same device, user will bypass 2nd factor i.e user will be logged in through username + password only', 'miniorange-2-factor-authentication');?>  .</div>
              

                   <br>
                <div style="margin-top: 10px;">
                    <button style="box-shadow: none;" class="button button-primary button-large" id="set_remember_device_button" target="_blank"><?php echo mo2f_lt( 'Save Settings' ); ?></button>
                 </div>
            <script type="text/javascript">
              document.getElementById("set_remember_device_button").disabled = true;
            </script>
            </form>
            <br>

         </div> 
</div>

</div>

<script>
    if(document.getElementById("rbaConfiguration_deviceExceedActionCHALLENGE2") !== null)
        document.getElementById("rbaConfiguration_deviceExceedActionCHALLENGE2").disabled = true;
    if(document.getElementById("rbaConfiguration_deviceExceedActionCHALLENGE1") !== null)
        document.getElementById("rbaConfiguration_deviceExceedActionCHALLENGE1").disabled = true;
    if(document.getElementById("rbaConfiguration_deviceExceedActionDENY1") !== null)
        document.getElementById("rbaConfiguration_deviceExceedActionDENY1").disabled = true;
    jQuery('#mo2f_hide_rba_content').hide();
    jQuery('#mo2f_activate_rba_addon').hide();
    function mo2f_rba_functionality() {
        <?php global $current_user;
        $current_user = wp_get_current_user();
        global $dbQueries,$Mo2fdbQueries;
        $upgrade_url    = add_query_arg(array('page' => 'mo_2fa_upgrade'), sanitize_url($_SERVER['REQUEST_URI'])); ?>
        jQuery('#mo2f_hide_login_form').toggle();
    }
    
</script>
				