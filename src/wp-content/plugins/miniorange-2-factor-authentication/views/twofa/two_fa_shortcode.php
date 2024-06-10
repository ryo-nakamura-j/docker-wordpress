<?php 
$setup_dirName = dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.'twofa'.DIRECTORY_SEPARATOR.'link_tracer.php';
        global $current_user;
        $current_user = wp_get_current_user();
        ?>
        
        <div class="mo2f_table_divide_border">
            
            
            <div id="mo2f_hide_shortcode_content" >
                <h2>4. Shortcode
                <a href='<?php echo esc_url($two_factor_premium_doc['Shortcode']);?>' target="_blank"><span class="dashicons dashicons-text-page" style="font-size:19px;color:#413c69;float: right;"></span></a> <a class="mo2fa-addons-preview-alignment" onclick="mo2f_login_with_shortcode()">&nbsp;&nbsp;See Preview</a>
                </h2><hr>
                <h3><?php echo __( 'List of Shortcodes', 'miniorange-2-factor-authentication' ); ?></h3>
                <ol style="margin-left:2%">
                    <li>
                        <b><?php echo __( 'Enable Two Factor: ', 'miniorange-2-factor-authentication' ); ?></b> <?php echo __( 'This shortcode provides an option to turn on/off 2-factor by user.', 'miniorange-2-factor-authentication' ); ?>
                    </li>
                    <li>
                        <b><?php echo __( 'Enable Reconfiguration: ', 'miniorange-2-factor-authentication' ); ?></b> <?php echo __( 'This shortcode provides an option to configure the Google Authenticator and Security Questions by user.', 'miniorange-2-factor-authentication' ); ?>
                    </li>
                    <li>
                        <b><?php echo __( 'Enable Remember Device: ', 'miniorange-2-factor-authentication' ); ?></b> <?php echo __( ' This shortcode provides \'Enable Remember Device\' from your custom login form.', 'miniorange-2-factor-authentication' ); ?>
                    </li>
                </ol>
            </div>
         <div id="mo2f_login_with_shortcode" style="display: none;">
        
            
           <br>
            <div>
              
                <div class="mo2f_table_layout" style="background-color: aliceblue; border:none;">
                <table class="display" cellspacing="0" width="100%" style="border:1px ridge #e6e6ff;">
                    <thead>
                    <tr>
                        <th class="shortcode_table" ><h3>Shortcode</h3></th>
                        <th class="shortcode_table" ><h3>Description</h3></th>
                    </tr>
                    
                  </thead>
                  <tbody>
                    <tr>
                        
                        <td class="mo2f_shortcode_table"><b style="font-size:16px;color: #0085ba;">[miniorange_enable2fa]</b></td>
                        
                        <td class="mo2f_shortcode_table"><?php echo mo2f_lt(' Add this shortcode to provide the option to turn on/off 2-factor by user.');?></td>
                    </tr>
                    
                    <tr>
                        <td class="mo2f_shortcode_table"><b style="font-size:16px;color: #0085ba;">[mo2f_enable_reconfigure]</b></td>
                        <td class="mo2f_shortcode_table"><?php echo mo2f_lt('Add this shortcode to provide the option to configure the Google Authenticator and Security Questions by user.');?></td>
                    </tr>
                    
                    <tr>
                        <td class="mo2f_shortcode_table"><b style="font-size:16px;color: #0085ba;">[mo2f_enable_rba_shortcode]</b></td>
                        <td class="mo2f_shortcode_table"><?php echo mo2f_lt(' Add this shortcode to \'Enable Remember Device\' from your custom login form.');?></td>
                    </tr>
                    
                  
                   </tbody>
                </table>

              
                <br>
                
                <form name="f" id="custom_login_form" method="post" action="">
                   <b> <?php echo mo2f_lt('Enter the id of your custom login form to use \'Enable Remember Device\' on the login page:');?></b>
                   <br> <br><input type="text" class="mo2f_table_textbox" id="mo2f_rba_loginform_id"
                           name="mo2f_rba_loginform_id" <?php echo 'disabled';
                    ?> value="<?php echo get_option('mo2f_rba_loginform_id') ?>"/>
                    <br><br>
                    <input type="hidden" name="option" value="custom_login_form_save"/>
                    <input type="submit" name="submit" value="Save Settings" style="background-color: #2271b1; color: white;" class="button button-primary button-large" 
                    <?php  echo 'disabled';
                     ?> />
                </form>
           </div>
            </div>
        </div>
        


<div style="padding-top: 10px; padding-bottom: 10px">
    <h3><?php echo mo2f_lt( '5. IP Restriction: Limit users to login from specific IPs' ); ?> <a class="mo2fa-addons-preview-alignment" onclick="mo2f_rba_functionality1()">&nbsp;&nbsp;See Preview</a> </h3>
    <hr>
    <p><?php echo mo2f_lt( 'The Admin can enable IP restrictions for the users. It will provide additional security to the accounts and perform different action to the accounts only from the listed IP Ranges. If user tries to access with a restricted IP, Admin can set three action: Allow, challenge or deny. Depending upon the action it will allow the user to login, challenge(prompt) for authentication or deny the access.' ); ?>
        <!--  // started second division --></p>
    
    <div id="mo2f_hide_login_form1" style="display: none;">
         <br>
         <div>
                <div class="mo2f_table_layout" style="background-color: aliceblue; border:none;">
                     <h2>IP Blocking Configuration </h2>
          
        
                      <hr>
                       <br>
                      <label class="mo_wpns_switch">
                           <input type="checkbox" id="pluginWAF" name="pluginWAF" <?php  echo 'disabled'; ?>>
                           <span class="mo_wpns_slider mo_wpns_round"></span>
                      </label>&nbsp;&nbsp;&nbsp;
                      <span class="checkbox_text text_fonts"  id="Allow_User_to_Register_Device" style="font-weight: 500;">Allow All IPs</span>
                      <br><br>
                      <div class="col-md-7 top-buffer">
                            <span class="input_field_fonts" style="font-weight: 500;">Action if IP Address is not in the given list:</span>

                      </div>

                      <div class="radio col-md-5 col-xs-offset-1">
                            <br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            <input type="radio" name="rbaConfiguration.deviceExceedAction" id="rbaConfiguration_deviceExceedActionCHALLENGE2" value="CHALLENGE" class="radio spacing" <?php echo 'disabled';?> ><label for="rbaConfiguration_deviceExceedActionCHALLENGE" style="font-weight: 500;" class="radio spacing">Allow</label>
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            <input type="radio" name="rbaConfiguration.deviceExceedAction" id="rbaConfiguration_deviceExceedActionCHALLENGE1" value="CHALLENGE" class="radio spacing" <?php echo 'disabled';?>><label for="rbaConfiguration_deviceExceedActionCHALLENGE" style="font-weight: 500;" class="radio spacing">Challenge</label>
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            <input type="radio" name="rbaConfiguration.deviceExceedAction" id="rbaConfiguration_deviceExceedActionDENY1" checked="checked" value="DENY" class="radio spacing" <?php echo 'disabled';?>><label for="rbaConfiguration_deviceExceedActionDENY" style="font-weight: 500;" class="radio spacing">Deny</label>
                            <br><br>

                      </div>
      
                      <input type="text" name="allowedDeviceRegistrations" maxlength="2"  id="allowedDeviceRegistrations" class="form-control" title="Please enter Numbers only" pattern="\d*" placeholder="Enter Start IP" style="background-color: white;" <?php echo 'disabled';?>>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                      <input type="text" name="allowedDeviceRegistrations" maxlength="2"  id="allowedDeviceRegistrations" class="form-control" title="Please enter Numbers only" pattern="\d*" placeholder="Enter End IP" style="background-color: white;" <?php echo 'disabled';?>> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <label class="mo_wpns_switch">
                            <input type="checkbox" id="pluginWAF" name="pluginWAF" <?php  echo 'disabled'; ?>>
                            <span class="mo_wpns_slider mo_wpns_round"></span>
                        </label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <button type="button" style="    background-color: forestgreen;" id="add_ip" class="btn btn-success addipbutton pull-right">
                            <i class="glyphicon-white glyphicon-plus">+</i>
                        </button><br><br>
       
    
                        <div style="margin-top: 10px;">
                            <a style="box-shadow: none;"
                            class="button button-primary button-large"
                            target="_blank" <?php echo 'disabled' ;  ?>><?php echo mo2f_lt( 'Restrict IP' ); ?></a>
                        </div>
               </div>
          </div>
     </div>
</div>
</div>
        <script type="text/javascript">
    function mo2f_login_with_shortcode()
    {
        jQuery('#mo2f_login_with_shortcode').toggle();
    }
   function mo2f_rba_functionality1() {
        jQuery('#mo2f_hide_login_form1').toggle();
    }
</script>
<style>
   .display .shortcode_table{
        border:1px ridge #e6e6ff;
        text-align:center; 
        padding-left:2px;

    }
   .display .mo2f_shortcode_table{
   
  border:1px ridge #e6e6ff;
  text-align:left;
  padding:7px;
}
    </style>
