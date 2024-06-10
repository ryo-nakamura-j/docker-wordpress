<?php 
function mo2f_get_method_to_configure($user){
    if(isset($_POST['mo2f_method']) && !empty($_POST['mo2f_method'])){
        $mo2f_method = sanitize_text_field($_POST['mo2f_method']);
    }
    else{
        $mo2f_method = get_user_meta( $user->ID, 'mo2f_2FA_method_to_configure', true );
        $mo2f_method = str_replace(' ', '', $mo2f_method);
    }
    return $mo2f_method;
}
function mo2f_configure_miniorange_authenticator($user){
    $mo2f_method = isset($_POST['mo2f_configured_2FA_method_free_plan'])? sanitize_text_field($_POST['mo2f_configured_2FA_method_free_plan']) : (isset($_POST['mo2f_configured_2FA_method_free_plan'])? sanitize_text_field($_POST['mo2f_method']) : 'miniOrangeSoftToken');
    if(isset($_POST) && isset($_POST['mo2f_session_id'])){
        $session_id_encrypt = sanitize_text_field($_POST['mo2f_session_id']);
    }else{
        $session_id_encrypt             = MO2f_Utility::random_str(20);
    }
    ?>
    <div id="mo2f_width">
        <?php $mobile_reg_status = get_user_meta($user->ID,'mobile_registration_status',true);
        if(!$mobile_reg_status) {
            download_instruction_for_mobile_app($mobile_reg_status);
        } ?>
    </div>
    <div>
        <h3><?php echo mo2f_lt('Step-2 : Select method');?></h3>
        <form name="f" method="post" action="">
        <div id='mo2fa_select_miniorange_method'>
        <input type="submit" name="mo2f_method" id="miniOrangeSoftTokenButton" class="button button-primary button-large" value="Soft Token" />
        <input type="submit" name="mo2f_method" id="miniOrangeQRCodeAuthenticationButton" class="button button-primary button-large" value="QR Code Authentication" />
        <input type="submit" name="mo2f_method" id="miniOrangePushNotificationButton" class="button button-primary button-large" value="Push Notification" />
        </div>
            <input type="hidden" name="option" value="mo_auth_refresh_mobile_qrcode" />
            <input type="hidden" name="mo2f_method" id="mo2f_method_mo" value="<?php echo esc_html($mo2f_method); ?>">
            <input type="hidden" name="mo2f_session_id" id="mo2f_session_id" value="<?php echo esc_html($session_id_encrypt); ?>">
            <input type="hidden" name="mo_auth_refresh_mobile_qrcode_nonce"	value="<?php echo esc_html(wp_create_nonce( "mo-auth-refresh-mobile-qrcode-nonce" )) ?>"/>
             <input type="button" style="float: right;" name="back" id="go_backlogin" class="button button-primary button-large" value="Back" />
            </form>
            <?php 
            $mo2f_show_qr_code = MO2f_Utility::mo2f_get_transient($session_id_encrypt, 'mo2f_show_qr_code');
            if(isset($mo2f_show_qr_code) && $mo2f_show_qr_code=='MO_2_FACTOR_SHOW_QR_CODE' && isset($_POST[ 'option']) && sanitize_text_field($_POST[ 'option']) =='mo_auth_refresh_mobile_qrcode' ){ 
                initialize_mobile_registration($user,$session_id_encrypt); 
                if($mobile_reg_status) { ?>
                    <script>
                        jQuery("#mo2f_app_div").show();
                    </script>
                <?php } else{ ?>
                    <script>
                        jQuery("#mo2f_app_div").hide();
                    </script>
                <?php } 
            } else{ ?>
                <br>
             
                <form name="f" method="post" action="" id="mo2f_go_back_form">
                    <input type="hidden" name="option" value="mo2f_go_back" />
                    <input type="hidden" name="mo2f_go_back_nonce" value="<?php echo esc_html(wp_create_nonce( "mo2f-go-back-nonce" )) ?>"/>
                </form>
                <script>
                jQuery('#miniOrangeSoftTokenButton').click(function() {
                    jQuery('#mo2f_method_mo').val('miniOrangeSoftToken');
                });
                jQuery('#miniOrangeQRCodeAuthenticationButton').click(function() {
                    jQuery('#mo2f_method_mo').val('miniOrangeQRCodeAuthentication');
                });
                jQuery('#miniOrangePushNotificationButton').click(function() {
                    jQuery('#mo2f_method_mo').val('miniOrangePushNotification');
                });
                jQuery('#go_backlogin').click(function() {
                  
                    jQuery('#mo2f_go_back_form').submit();
                });
                </script>
            <?php } ?>
        </div>
        <?php 
    } 

function download_instruction_for_mobile_app( $mobile_reg_status){ ?>
    <div id="mo2f_app_div" class="mo_margin_left">
            <span style="display: flex;"><div  style="width: -webkit-fill-available;"><h3 class="mo2f_authn_header"><?php echo mo2f_lt('Step-1 : Download the miniOrange Authenticator App');?></h3></div><div>
           </div></span>
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
                                <?php echo mo2f_lt( 'Search for');?> <b><?php echo mo2f_lt('miniOrange');?></b>
                            </li>
                            <li>
                                <?php echo mo2f_lt( 'Download and install ');?><a style="color: #F78701; text-decoration:blink" href="https://apps.apple.com/app/id1482362759"><b> <?php echo mo2f_lt('miniOrange Authenticator');?></b></a></span>
                                <?php echo mo2f_lt( 'app ');?>(<b><?php echo mo2f_lt('NOT MOAuth');?></b>)
                            </li>
                        </ol>
                        <br>
                        <a style="margin-left:10%" target="_blank" href="https://apps.apple.com/app/id1482362759"><img src="<?php echo esc_url(plugins_url( 'includes/images/appstore.png' , dirname(dirname(dirname(__FILE__)))) );?>" style="width:120px; height:45px; margin-left:-2.5em;">
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
                                <?php echo mo2f_lt( 'Search for ');?><b><?php echo mo2f_lt('miniOrange.');?></b>
                            </li>
                            <li>
                                <?php echo mo2f_lt( 'Download and install');?><a style="color: #F78701; text-decoration:blink" href="https://play.google.com/store/apps/details?id=com.miniorange.android.authenticator&hl=en"><b><?php echo mo2f_lt(' miniOrange Authenticator');?></b></a>
                                <?php echo mo2f_lt( 'app');?> (<b><?php echo mo2f_lt('NOT MOAuth');?> </b>)
                            </li>
                        </ol>
                        <br>
                        <a style="margin-left:10%" target="_blank" href="https://play.google.com/store/apps/details?id=com.miniorange.android.authenticator&hl=en"><img src="<?php echo esc_url(plugins_url( 'includes/images/playStore.png' , dirname(dirname(dirname(__FILE__)))) );?>" style="width:120px; height:=45px; margin-left:-3.7em;"></a>
                    </td>
                </tr>
            </table>
        </div>
    </div>
    <?php 
} 
function initialize_mobile_registration($user,$session_id_encrypt = null) { 
    $mo2f_method = mo2f_get_method_to_configure($user);
    $data = MO2f_Utility::mo2f_get_transient($session_id_encrypt, 'mo2f_qrCode');
    ?>
    <div style="padding: 20px;">
        <p>
            <?php echo mo2f_lt( 'Open your ');?><b> <?php echo mo2f_lt('miniOrange Authenticator');?></b> app and
            <?php echo mo2f_lt( 'click on');?> <b> <?php echo mo2f_lt('Add Account');?></b>
            <?php echo mo2f_lt( 'to scan the QR Code. Your phone should have internet connectivity to scan QR code.');?>
        </p>
        <br>
        <div>
            <b>
                <h3>
                <?php  echo mo2f_lt( 'I am not able to scan the QR code, what should I do?');?>
                </h3>
            </b>
        </div>
        <div class="mo2f_collapse" id="mo2f_scanqrcode">
            <?php echo mo2f_lt( 'Follow these instructions below and try again.');?>
            <ol>
                <li>
                    <?php echo mo2f_lt( 'Make sure your desktop screen has enough brightness.');?>
                </li>
                <li>
                    <?php echo mo2f_lt( 'Open your app and click on Configure button to scan QR Code again.');?>
                </li>
                <li>
                    <?php echo mo2f_lt( 'If you get a cross mark on QR Code then click on \'Refresh QR Code\' link.');?>
                </li>
            </ol>
        </div>
        <br><br>
           <a href="#refreshQRCode">
                <?php echo mo2f_lt( 'Click here to Refresh QR Code.');?>
            </a>
            <br><br>
            <div id="displayQrCode" >
            <br>
            <?php echo '<img style="width:200px;" src="data:image/jpg;base64,' . esc_html($data) . '" />'; ?>
            </div>

        <table class="mo2f_settings_table" style="display: none;">
            <tr>                
                <th>
                    Select Authentication method :
                </th>
            </tr>
            <tr>
                <td>
                    <input type='radio' hidden  value='miniOrange Soft Token' name='miniOrangeAuthenticator' id='miniOrangeSoftToken' />
                    <label class="mo2f_miniAuthApp" for='miniOrangeSoftToken'>Soft Token</label>  
                </td>
                <td rowspan="3">
                </td>
            </tr>
            <tr>
                <td>                   
                    <input type='radio' hidden value='miniOrange QR Code Authentication' name='miniOrangeAuthenticator' id='miniOrangeQRCodeAuthentication'  />
                    <label class="mo2f_miniAuthApp" for='miniOrangeQRCodeAuthentication'>QR Code Authentication</label>               
                </td>
            </tr> 
            <tr>
                <td>
                    <input type='radio' hidden value='miniOrange Push Notification' name='miniOrangeAuthenticator' id='miniOrangePushNotification'  />
                    <label class="mo2f_miniAuthApp" for='miniOrangePushNotification'>Push Notification</label>  
                </td>
            </tr>
        </table>
        <br>
        <div id="mobile_registered">

            <form name="f" method="post" id="mobile_register_form" action="" class="mo2f_display_none_forms">
                <input type="hidden" name="mo2f_method" id="mo2f_method" value="miniOrangeSoftToken" />
                <input type="hidden" name="option" value="mo2f_configure_miniorange_authenticator_validate" />
				<input type="hidden" name="mo2f_configure_miniorange_authenticator_validate_nonce"
						value="<?php echo esc_html(wp_create_nonce( "mo2f-configure-miniorange-authenticator-validate-nonce" )) ?>"/>
            </form>
        </div>
        <form name="f" method="post" action="" id="mo2f_go_back_form">
                    <input type="hidden" name="option" value="mo2f_go_back" />
                    <input type="hidden" name="mo2f_go_back_nonce" value="<?php echo esc_html(wp_create_nonce( "mo2f-go-back-nonce" )) ?>"/>
                </form>
        <form name="f" method="post" id="mo2f_refresh_qr_form" action="" class="mo2f_display_none_forms">
            <input type="hidden" name="option" value="mo_auth_refresh_mobile_qrcode" />            
            <input type="hidden" name="mo2f_session_id" id="mo2f_session_id" value="<?php echo esc_html($session_id_encrypt); ?>">         
            <input type="hidden" name="mo2f_method" id="mo2f_ref_method"value="<?php echo esc_html($_POST['mo2f_method']) ;?>" />
			<input type="hidden" name="mo_auth_refresh_mobile_qrcode_nonce"
						value="<?php echo esc_html(wp_create_nonce( "mo-auth-refresh-mobile-qrcode-nonce" )) ?>"/>

        </form>
        
        <br>
        <br>
    </div>
    <script>
        jQuery('#go_backlogin').click(function () {
            jQuery('#mo2f_go_back_form').submit();
        });
        var method = "<?php echo esc_html($mo2f_method);?>";
        jQuery("#"+method).prop('checked',true);
        var method = jQuery('input[name="miniOrangeAuthenticator"]:checked').val();
        jQuery("#mo2f_method").val(method);        
        jQuery("#mo2f_ref_method").val(method);
        
        jQuery('a[href=\"#refreshQRCode\"]').click(function(e) {
            jQuery('#mo2f_refresh_qr_form').submit();
        });
        jQuery("#mo2fa_select_miniorange_method").hide();
         jQuery('input[type=radio][name=miniOrangeAuthenticator]').change(function() {
            jQuery("#mo2f_method").val(this.value);                
            jQuery("#mo2f_ref_method").val(this.value);
        });
        var timeout;
        pollMobileRegistration();
        pollMobileRegistration();

        function pollMobileRegistration() {
            var transId = "<?php echo esc_html(MO2f_Utility::mo2f_get_transient($session_id_encrypt, 'mo2f_transactionId'));  ?>";
            var jsonString = "{\"txId\":\"" + transId + "\"}";
            var postUrl = "<?php echo esc_url(MO_HOST_NAME);  ?>" + "/moas/api/auth/registration-status";
            jQuery.ajax({
                url: postUrl,
                type: "POST",
                dataType: "json",
                data: jsonString,
                contentType: "application/json; charset=utf-8",
                success: function(result) {
                    var status = JSON.parse(JSON.stringify(result)).status;
                    if (status == 'SUCCESS') {
                        var content = "<br><div id='success'><img style='width:165px;margin-top:-1%;margin-left:2%;' src='" + "<?php echo esc_url(plugins_url( 'includes/images/right.png' , dirname(dirname(dirname(__FILE__)))) );?>" + "' /></div>";
                        jQuery("#displayQrCode").empty();
                        jQuery("#displayQrCode").append(content);
                        setTimeout(function() {
                            jQuery("#mobile_register_form").submit();
                        }, 1000);
                    } else if (status == 'ERROR' || status == 'FAILED') {
                        var content = "<br><div id='error'><img style='width:165px;margin-top:-1%;margin-left:2%;' src='" + "<?php echo esc_url(plugins_url( 'includes/images/wrong.png' , dirname(dirname(dirname(__FILE__)))));?>" + "' /></div>";
                        jQuery("#displayQrCode").empty();
                        jQuery("#displayQrCode").append(content);
                        jQuery("#messages").empty();

                        jQuery("#messages").append("<div class='error mo2f_error_container'> <p class='mo2f_msgs'>An Error occured processing your request. Please try again to configure your phone.</p></div>");
                    } else {
                        timeout = setTimeout(pollMobileRegistration, 3000);
                    }
                }
            });
        }
        jQuery('html,body').animate({
            scrollTop: jQuery(document).height()
        }, 800);
    </script>
    <?php 
} ?>