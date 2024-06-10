<?php

function mo2f_configure_duo_authenticator( $user ) {
	global $Mo2fdbQueries;
	$mo2f_user_phone = $Mo2fdbQueries->get_user_detail( 'mo2f_user_phone', $user->ID );
	$user_phone      = $mo2f_user_phone ? $mo2f_user_phone : get_option( 'user_phone_temp' );
   
	?>

    <?php if(isset($_POST['option']) && sanitize_text_field(wp_unslash($_POST['option'])) == 'duo_mobile_send_push_notification_inside_plugin'){
            mo2f_setup_duo_authenticator(); //4
          }elseif(get_user_meta($user->ID,'user_not_enroll')){ 
            mo2f_inside_plugin_go_for_user_enroll_on_duo($user);// 3    //initialize_duo_mobile_registration($user);
          }elseif(get_site_option('duo_credentials_save_successfully') ) { 
           mo2f_download_instruction_for_duo_mobile_app(); //2
          }else{ 
            if(current_user_can('administrator'))
             mo2f_save_duo_configuration_credentials(); //1
            else
              mo2f_non_admin_notice();
        ?>   
       
    <?php  
    } 

}

function mo2f_setup_duo_authenticator(){

  ?>
   <h3><?php echo mo2f_lt( 'Test Duo Authenticator' ); ?></h3>
    <hr>
    <div>
        <br>
        <br>
        <center>
            <h3><?php echo mo2f_lt( 'Duo push notification is sent to your mobile phone.' ); ?>
                <br>
                <?php echo mo2f_lt( 'We are waiting for your approval...' ); ?></h3>
            <img src="<?php echo esc_url(plugins_url( 'includes/images/ajax-loader-login.gif', dirname(dirname(dirname(__FILE__))))); ?>"/>
        </center>

        <input type="button" name="back" id="go_back" class="button button-primary button-large"
               value="<?php echo mo2f_lt( 'Back' ); ?>"
               style="margin-top:100px;margin-left:10px;"/>
    </div>

    <form name="f" method="post" action="" id="mo2f_go_back_form">
        <input type="hidden" name="option" value="mo2f_go_back"/>
        <input type="hidden" name="mo2f_go_back_nonce"
               value="<?php echo esc_html(wp_create_nonce( "mo2f-go-back-nonce" )) ?>"/>
    </form>
    <form name="f" method="post" id="duo_mobile_register_form" action="">
        <input type="hidden" name="option" value="mo2f_configure_duo_authenticator_validate_nonce"/>
        <input type="hidden" name="mo2f_configure_duo_authenticator_validate_nonce"
               value="<?php echo esc_html(wp_create_nonce( "mo2f-configure-duo-authenticator-validate-nonce" )) ?>"/>
    </form>
    <form name="f" method="post" id="mo2f_duo_authenticator_error_form" action="">
        <input type="hidden" name="option" value="mo2f_duo_authenticator_error"/>

        <input type="hidden" name="mo2f_duo_authentcator_error_nonce"
               value="<?php echo esc_html(wp_create_nonce( "mo2f-duo-authenticator-error-nonce" )) ?>"/>
    </form>

    <script>
        jQuery('#go_back').click(function () {
            jQuery('#mo2f_go_back_form').submit();
        });

        var timeout;



            pollMobileValidation();
            function pollMobileValidation() {
                var nonce = "<?php echo esc_html(wp_create_nonce( 'miniorange-2-factor-duo-nonce' )); ?>";
                var data={
                'action':'mo2f_duo_authenticator_ajax',
                'call_type':'check_duo_push_auth_status',
                'nonce': nonce,
               
            }; 

            jQuery.post(ajaxurl, data, function(response){
                        
                        if (response == 'SUCCESS') {
                            jQuery('#duo_mobile_register_form').submit();
                        } else if (response == 'ERROR' || response == 'FAILED' || response == 'DENIED') {
                            jQuery('#mo2f_duo_authenticator_error_form').submit();
                        } else {
                            timeout = setTimeout(pollMobileValidation, 3000);
                        }
                    
                });
            
            }

    </script>
 <?php   
}

function mo2f_inside_plugin_go_for_user_enroll_on_duo($user){
  $regis = get_user_meta($user->ID,'user_not_enroll_on_duo_before');
  $regis = isset($regis[0]) ? $regis[0]:'https://plugins.miniorange.com/2-factor-authentication-for-wordpress';
?>
  <div style = " background-color: #d9eff6;">
    <p style = "font-size: 17px;">
        <?php echo mo2f_lt( 'Register push notification as Two Factor Authentication using the below link.');?> 
        <?php echo mo2f_lt( 'After registration if you have not received  authenticate requestyet, please click on ');?><b><?php echo mo2f_lt( 'Send Me Push Notification.');?></b> 
    </p>
   </div>
   <br>
     <p style = " font-size: 17px;"><b>Step : 1 </b></p>
   <div style = " background-color: #d9eff6;" > 
    <p style = " font-size: 17px;">
    <b> <a href="<?php echo esc_url($regis) ;?>" target="_blank">Click Here</a></b> <?php echo mo2f_lt( 'to configure DUO Push Notification. Once done with registration click on ');?><b><?php echo mo2f_lt( 'Send Me Push Notification Button.');?></b>  
  </p>
  </div> 
  <br>
 <form name="f" method="post" id="duo_mobile_send_push_notification_inside_plugin" action="" >
        <input type="hidden" name="option" value="duo_mobile_send_push_notification_inside_plugin" />
        <input type="hidden" name="duo_mobile_send_push_notification_inside_plugin_nonce"
                value="<?php echo esc_html(wp_create_nonce( "mo2f-send-duo-push-notification-inside-plugin-nonce" )) ?>"/>
        <p style = " font-size: 17px;"><b>Step : 2 </b></p>
         <input type="submit" name="validate" id="validate" class="button button-primary button-large"
               value="<?php echo mo2f_lt( 'Send Me Push Notification' ); ?>"/>
        <br><br><br>
        <input type="button" name="back" id="go_back_form" class="button button-primary button-large" value="<?php echo mo2f_lt('Back');?>" />
         <?php if(current_user_can('administrator')){ ?>
        <input type="button" name="back" id="reset_duo_configuration" class="button button-primary button-large" value="<?php echo mo2f_lt('Reset Duo Configuration');?>" />
         <?php } ?>
 </form>
  <form name="f" method="post" action="" id="mo2f_go_back_form">
                <input type="hidden" name="option" value="mo2f_go_back" />
                <input type="hidden" name="mo2f_go_back_nonce" value="<?php echo esc_html(wp_create_nonce( "mo2f-go-back-nonce" )) ?>"/>
 </form>
  <form name="f" method="post" action="" id="mo2f_reset_duo_configuration">
                        <input type="hidden" name="option" value="mo2f_reset_duo_configuration" />
                         <input type="hidden" name="mo2f_duo_reset_configuration_nonce"
               value="<?php echo esc_html(wp_create_nonce( "mo2f-duo-reset-configuration-nonce" )) ?>"/>
  </form>   
            <script>
                jQuery('#go_back_form').click(function() {
                    jQuery('#mo2f_go_back_form').submit();
                });
                 jQuery('#reset_duo_configuration').click(function() {
                    jQuery('#mo2f_reset_duo_configuration').submit();
                });
                jQuery("#mo2f_configurePhone").empty();
                jQuery("#mo2f_app_div").hide();
            </script>

<?php    
}


function go_for_user_enroll_on_duo($user,$session_id){
  $regis = get_user_meta($user->ID,'user_not_enroll_on_duo_before');
  $regis = isset($regis[0]) ? $regis[0]:'https://plugins.miniorange.com/2-factor-authentication-for-wordpress';
?>
  <div style = " background-color: #d9eff6;">
    <p style = "font-size: 17px;">
        <?php echo mo2f_lt( 'Register push notification as Two Factor Authentication using the below link.');?> 
        <?php echo mo2f_lt( 'After registration if you have not received  authenticate requestyet, please click on ');?><b><?php echo mo2f_lt( 'Send Me Push Notification.');?></b> 
    </p>
   </div>
   <br>
    <p style = " font-size: 17px;"><b>Step : A </b></p>
   <div style = " background-color: #d9eff6;" > 
    <p style = " font-size: 17px;">
    <a href="<?php echo esc_url($regis);?>" target="_blank">Click Here</a> <?php echo mo2f_lt( 'to configure DUO Push Notification. Once done with registration click on ');?><b><?php echo mo2f_lt( 'Send Me Push Notification.');?></b>  
  </p>
  </div> 
 
 <form name="f" method="post" id="duo_mobile_send_push_notification_for_inline_form" action="" >
        <input type="hidden" name="option" value="duo_mobile_send_push_notification_for_inline_form" />
        <input type="hidden" name="session_id" value="<?php echo esc_html($session_id) ?>" />
        <input type="hidden" name="duo_mobile_send_push_notification_inline_form_nonce"
                value="<?php echo esc_html(wp_create_nonce( "mo2f-send-duo-push-notification-inline-nonce" )) ?>"/>
         <p style = " font-size: 17px;"><b>Step : B </b></p>
         <input type="submit" name="validate" id="validate" class="button button-primary button-large"
               value="<?php echo mo2f_lt( 'Send Me Push Notification' ); ?>"/>
        <br><br><br>
         <input type="button" name="back" id="go_back_form" class="button button-primary button-large" value="<?php echo mo2f_lt('Back');?>" />
 </form>
  <form name="f" method="post" action="" id="mo2f_go_back_form">
                <input type="hidden" name="option" value="mo2f_go_back" />
                <input type="hidden" name="mo2f_go_back_nonce" value="<?php echo esc_html(wp_create_nonce( "mo2f-go-back-nonce" )) ?>"/>
 </form>
            <script>
                jQuery('#go_back_form').click(function() {
                    jQuery('#mo2f_go_back_form').submit();
                });
                jQuery("#mo2f_configurePhone").empty();
                jQuery("#mo2f_app_div").hide();
            </script>

<?php    
}

function mo2f_non_admin_notice(){
  
?>
  <div style = " background-color: #d9eff6;">
    <p style = "font-size: 25px;">
        <?php echo mo2f_lt( 'Please contact your administrator, To configure duo push notification, your administrator needs to enter duo credentials first after that you can configure. Click BACK button to configure other method.');?> 
        
    </p>
   </div>
 
 <form name="f" method="post" id="duo_notice_for_non_admin" action="" >
        <input type="hidden" name="option" value="duo_notice_for_non_admin" />
        <input type="hidden" name="duo_notice_for_non_admin_nonce"
                value="<?php echo esc_html(wp_create_nonce( "duo-notice-for-non-admin-nonce" )) ?>"/>
         <input type="button" name="back" id="go_back_form" class="button button-primary button-large" value="<?php echo mo2f_lt('Back');?>" />
 </form>
  <form name="f" method="post" action="" id="mo2f_go_back_form">
                <input type="hidden" name="option" value="mo2f_go_back" />
                <input type="hidden" name="mo2f_go_back_nonce" value="<?php echo esc_html(wp_create_nonce( "mo2f-go-back-nonce" )) ?>"/>
 </form>
            <script>
                jQuery('#go_back_form').click(function() {
                    jQuery('#mo2f_go_back_form').submit();
                });
                jQuery("#mo2f_configurePhone").empty();
                jQuery("#mo2f_app_div").hide();
            </script>

<?php    
}


function mo2f_download_instruction_for_duo_mobile_app(){ 
    
  ?>  
   
        <form name="f" method="post" id="duo_mobile_register_form" action="">
        <input type="hidden" name="option" value="mo2f_configure_duo_authenticator_abc"/>
        <input type="hidden" name="mo2f_configure_duo_authenticator_nonce"
               value="<?php echo esc_html(wp_create_nonce( "mo2f-configure-duo-authenticator-nonce" )) ?>"/>
        <a class="mo_app_link" data-toggle="collapse" href="#mo2f_sub_header_app" aria-expanded="false">
            <h3 class="mo2f_authn_header"><?php echo mo2f_lt('Step-1 : Download the Duo');?> <span style="color: #F78701;"> <?php echo mo2f_lt('Authenticator');?></span> <?php echo mo2f_lt('App');?>
        </h3>
        </a>
        <hr class="mo_hr">
        <div class="mo2f_collapse in" id="mo2f_sub_header_app">
            <table width="100%;" id="mo2f_inline_table">
                <tr id="mo2f_inline_table">
                    <td style="padding:10px;">
                        <h4 id="user_phone_id"><?php echo mo2f_lt('iPhone Users');?></h4>
                        <hr>
                        <ol>
                            <li>
                                <?php echo mo2f_lt( 'Go to App Store');?>
                            </li>
                            <li>
                                <?php echo mo2f_lt( 'Search for');?> <b><?php echo mo2f_lt('Duo Authenticator');?></b>
                            </li>
                            <li>
                                <?php echo mo2f_lt( 'Download and install ');?><span style="color: #F78701;"><?php echo mo2f_lt('Duo');?><b> <?php echo mo2f_lt('Authenticator');?></b></span>
                                <?php echo mo2f_lt( 'app ');?>(<b><?php echo mo2f_lt('NOT Duo');?></b>)
                            </li>
                        </ol>
                        <br>
                        <a style="margin-left:10%" target="_blank" href="https://apps.apple.com/app/id1482362759"><img src="<?php echo esc_url(plugins_url( 'includes/images/appstore.png' , dirname(dirname(dirname(__FILE__)))));?>" style="width:120px; height:45px; margin-left:6px;">
                        </a>
                    </td>
                    <td style="padding:10px;">
                        <h4 id="user_phone_id"><?php echo mo2f_lt('Android Users');?></h4>
                        <hr>
                        <ol>
                            <li>
                                <?php echo mo2f_lt( 'Go to Google Play Store.');?>
                            </li>
                            <li>
                                <?php echo mo2f_lt( 'Search for ');?><b><?php echo mo2f_lt('Duo Authenticator.');?></b>
                            </li>
                            <li>
                                <?php echo mo2f_lt( 'Download and install');?> <span style="color: #F78701;"><b><?php echo mo2f_lt('Authenticator');?></b></span>
                                <?php echo mo2f_lt( 'app');?> (<b><?php echo mo2f_lt('NOT Duo');?> </b>)
                            </li>
                        </ol>
                        <br>
                        <a style="margin-left:10%" target="_blank" href="https://play.google.com/store/apps/details?id=com.miniorange.android.authenticator&hl=en"><img src="<?php echo esc_url(plugins_url( 'includes/images/playStore.png' , dirname(dirname(dirname(__FILE__))) ));?>" style="width:120px; height:=45px; margin-left:6px;"></a>
                    </td>
                </tr>
            </table>

            <input type="button" name="back" id="mo2f_inline_back_btn" class="button button-primary button-large" value="<?php echo __('Back', 'miniorange-2-factor-authentication'); ?>" />
                                   
            <input type="submit" name="submit" id="mo2f_plugin_configure_btn" class="button button-primary button-large" value="<?php echo __('Configure your phone', 'miniorange-2-factor-authentication'); ?>" />
        </div>
    </form>
    <form name="f" method="post" action="" id="mo2f_go_back_form">
        <input type="hidden" name="option" value="mo2f_go_back" />
        <input type="hidden" name="mo2f_go_back_nonce" value="<?php echo esc_html(wp_create_nonce( "mo2f-go-back-nonce" )) ?>"/>
    </form>
            <script>
                jQuery('#mo2f_inline_back_btn').click(function() {
                    jQuery('#mo2f_go_back_form').submit();
                });
                
               
            </script>
   
   

    <?php 
}

function mo2f_inline_download_instruction_for_duo_mobile_app($mobile_registration_status = false){ 
    
  ?>  
   <div id="mo2f_app_div" class="mo_margin_left">
        <a class="mo_app_link" data-toggle="collapse" href="#mo2f_sub_header_app" aria-expanded="false">
            <h3 class="mo2f_authn_header"><?php echo mo2f_lt('Step-1 : Download the Duo');?> <span style="color: #F78701;"> <?php echo mo2f_lt('Authenticator');?></span> <?php echo mo2f_lt('App');?>
        </h3>
        </a>
        <hr class="mo_hr">
        <div class="mo2f_collapse in" id="mo2f_sub_header_app">
            <table width="100%;" id="mo2f_inline_table">
                <tr id="mo2f_inline_table">
                    <td style="padding:10px;">
                        <h4 id="user_phone_id"><?php echo mo2f_lt('iPhone Users');?></h4>
                        <hr>
                        <ol>
                            <li>
                                <?php echo mo2f_lt( 'Go to App Store');?>
                            </li>
                            <li>
                                <?php echo mo2f_lt( 'Search for');?> <b><?php echo mo2f_lt('Duo Authenticator');?></b>
                            </li>
                            <li>
                                <?php echo mo2f_lt( 'Download and install ');?><span style="color: #F78701;"><?php echo mo2f_lt('Duo');?><b> <?php echo mo2f_lt('Authenticator');?></b></span>
                                <?php echo mo2f_lt( 'app ');?>(<b><?php echo mo2f_lt('NOT Duo');?></b>)
                            </li>
                        </ol>
                        <br>
                        <a style="margin-left:10%" target="_blank" href="https://apps.apple.com/app/id1482362759"><img src="<?php echo esc_url(plugins_url( 'includes/images/appstore.png' , dirname(dirname(dirname(__FILE__)))));?>" style="width:120px; height:45px; margin-left:6px;">
                        </a>
                    </td>
                    <td style="padding:10px;">
                        <h4 id="user_phone_id"><?php echo mo2f_lt('Android Users');?></h4>
                        <hr>
                        <ol>
                            <li>
                                <?php echo mo2f_lt( 'Go to Google Play Store.');?>
                            </li>
                            <li>
                                <?php echo mo2f_lt( 'Search for ');?><b><?php echo mo2f_lt('Duo Authenticator.');?></b>
                            </li>
                            <li>
                                <?php echo mo2f_lt( 'Download and install');?> <span style="color: #F78701;"><b><?php echo mo2f_lt('Authenticator');?></b></span>
                                <?php echo mo2f_lt( 'app');?> (<b><?php echo mo2f_lt('NOT Duo');?> </b>)
                            </li>
                        </ol>
                        <br>
                        <a style="margin-left:10%" target="_blank" href="https://play.google.com/store/apps/details?id=com.miniorange.android.authenticator&hl=en"><img src="<?php echo esc_url(plugins_url( 'includes/images/playStore.png' , dirname(dirname(dirname(__FILE__)))));?>" style="width:120px; height:=45px; margin-left:6px;"></a>
                    </td>
                </tr>
            </table>
        </div>
    </div>
   <?php
    if( $mobile_registration_status) { ?>
                 <script>
                    jQuery("#mo2f_app_div").hide();
                </script>
            <?php } else{ ?>
                
                 <script>
                    jQuery("#mo2f_app_div").show();
                </script>
          <?php } ?> 

    <?php 
}

function mo2f_save_duo_configuration_credentials(){

?>
<h3><?php echo mo2f_lt( 'Please enter required details' ); ?>
     </h3> 
     <p  style = "font-size: 17px;">
        <?php echo mo2f_lt( '1. If you do not have an account in duo, please'); ?>  <a href="https://signup.duo.com/" target="_blank">Click Here </a><?php echo mo2f_lt( 'to create an account.'); ?> 

     </p>
     <p  style = "font-size: 17px;">
        <?php echo mo2f_lt( '2. Follow these steps( ') ?> <a href=" https://duo.com/docs/authapi#first-steps" target="_blank">Click Here </a> <?php echo mo2f_lt( ') to create AUTH API application on duo side. After creating auth API, you will get the all credentials which you need to enter below.'); ?> 

     </p>
   <br>
   <div> 
    <form name="f" method="post" action="" id="mo2f_save_duo_configration">
        <input type="hidden" name="option" value="mo2f_configure_duo_authenticator"/>
        <input type="hidden" name="mo2f_configure_duo_authenticator_nonce"
                        value="<?php echo esc_html(wp_create_nonce( "mo2f-configure-duo-authenticator" )) ?>"/>
        <p><?php echo mo2f_lt( 'Integration key' ); ?> 
        &nbsp &nbsp <input class="mo2f_table_textbox" style="width:400px;" autofocus="true" type="text" name="ikey"
               placeholder="<?php echo mo2f_lt( 'Integration key' ); ?>" style="width:95%;"/>
       
        </p>
        <br><br>
        <p><?php echo mo2f_lt( 'Secret Key' ); ?> 
        &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp<input class="mo2f_table_textbox" style="width:400px;" autofocus="true" type="text" name="skey"
               placeholder="<?php echo mo2f_lt( 'Secret key' ); ?>" style="width:95%;"/>
       
        </p>
        <br><br>
        <p><?php echo mo2f_lt( 'API Hostname' ); ?> 
        &nbsp &nbsp <input class="mo2f_table_textbox" style="width:400px;" autofocus="true" type="text" name="apihostname"
               placeholder="<?php echo mo2f_lt( 'API Hostname' ); ?>" style="width:95%;"/>
        
        </p>
        <br><br>
        <input type="button" name="back" id="go_back" class="button button-primary button-large"
               value="<?php echo mo2f_lt( 'Back' ); ?>"/>
        <input type="submit" name="validate" id="validate" class="button button-primary button-large"
               value="<?php echo mo2f_lt( 'Save' ); ?>"/>
    </form><br>
    <form name="f" method="post" action="" id="mo2f_go_back_form">
        <input type="hidden" name="option" value="mo2f_go_back"/>
        <input type="hidden" name="mo2f_go_back_nonce"
                        value="<?php echo esc_html(wp_create_nonce( "mo2f-go-back-nonce" )) ?>"/>
    </form>
            <script>
                jQuery('#go_back').click(function() {
                    jQuery('#mo2f_go_back_form').submit();
                });
            </script>
             
    

  
  </div>  

    <script>
        jQuery("#phone").intlTelInput();
        jQuery('#go_back').click(function () {
            jQuery('#mo2f_go_back_form').submit();
        });
        jQuery('a[href=\"#resendsmslink\"]').click(function (e) {
            jQuery('#mo2f_verifyphone_form').submit();
        });

    </script>

    <?php 


 }

?>