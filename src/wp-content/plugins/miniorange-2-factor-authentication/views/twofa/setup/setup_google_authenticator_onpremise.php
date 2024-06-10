<?php

function mo2f_configure_google_authenticator_setupWizard($secret,$url,$otpcode, $session_id_encrypt)
{
    $gauth_name = sanitize_text_field($_SERVER['SERVER_NAME']);
    echo "<b><h3>1. Please scan the QR code below in your Authenticator App</h3></b>
           <table id='mo2f-ga-supported_methods'>
           <tbody>
           <tr><td><li>Google Authenticator</li> </td>
           <td> <li>Microsoft Authenticator</li> </td></tr>
           <tr><td colspan='2'> <li> Authy Authenticator and other popular TOTP Authenticators</li></td></tr>
           
           
</tbody>
            </table>
    ";
    ?>
          <div style="margin-left:40px;">
            <ol>
                <li><?php echo mo2f_lt( 'In the app, tap on Menu and select "Set up account".' ); ?></li>
                <li><?php echo mo2f_lt( 'Select "Scan a barcode".' ); ?></li>
                <br><br>
            <form name="f"  id="login_settings_appname_form" method="post" action="">
                <input type="hidden" name="option" value="mo2f_google_appname" />
                <input type="hidden" name="mo2f_google_appname_nonce"
                value="<?php echo esc_html(wp_create_nonce( "mo2f-google-appname-nonce" )) ?>"/>
                <div style="margin-left: 14%;">
                    <div class="mo2f_gauth_column mo2f_gauth_left" >

                        <div class="mo2f_gauth" style="background: white;" data-qrcode="<?php echo esc_html($url);?>" ></div>
                    </div>
                </div>

               
            </form>

            </ol>

            <div><a data-toggle="collapse" href="#mo2f_scanbarcode_a"
                    aria-expanded="false"><b><?php echo mo2f_lt( 'Can\'t scan the barcode? ' ); ?></b></a>
            </div>
            <div class="mo2f_collapse"  id="mo2f_scanbarcode_a" style="background: white; display: none;">
                <ol class="mo2f_ol">
                    <li><?php echo mo2f_lt( 'Tap on Menu and select' ); ?>
                        <b> <?php echo mo2f_lt( ' Set up account ' ); ?></b>.
                    </li>
                    <li><?php echo mo2f_lt( 'Select' ); ?>
                        <b> <?php echo mo2f_lt( ' Enter provided key ' ); ?></b>.
                    </li>
                    <li><?php echo mo2f_lt( 'For the' ); ?>
                        <b> <?php echo mo2f_lt( ' Enter account name ' ); ?></b>
                        <?php echo mo2f_lt( 'field, type your preferred account name' ); ?>.
                    </li>
                    <li><?php echo mo2f_lt( 'For the' ); ?>
                        <b> <?php echo mo2f_lt( ' Enter your key ' ); ?></b>
                        <?php echo mo2f_lt( 'field, type the below secret key' ); ?>:
                    </li>

                    <div class="mo2f_google_authy_secret_outer_div">
                        <div class="mo2f_google_authy_secret_inner_div">
                            <?php echo esc_attr($secret); ?>
                        </div>
                        <div class="mo2f_google_authy_secret">
                            <?php echo mo2f_lt( 'Spaces do not matter' ); ?>.
                        </div>
                    </div>
                    <li><?php echo mo2f_lt( 'Key type: make sure' ); ?>
                        <b> <?php echo mo2f_lt( ' Time-based ' ); ?></b>
                        <?php echo mo2f_lt( ' is selected' ); ?>.
                    </li>

                    <li><?php echo mo2f_lt( 'Tap Add.' ); ?></li>
                </ol>
            </div>
        </div>

        <div id="mo2f_entergoogle_auth_code">
            
            <b><h3>2. Enter the code generated in your Authenticator app <input style="padding: 5px" class ='mo_input_text_box_size' type="text" id="mo2f_google_auth_code" name="mo2f_google_auth_code" placeholder="Enter OTP" /> </h3></b>
            <input type="hidden" name="mo2f_session_id" id="mo2f_session_id" value="<?php echo esc_html($session_id_encrypt) ?>">
                        
        </div>
        <script type="text/javascript">
            jQuery('a[href="#mo2f_scanbarcode_a"]').click(function(e){

                var element = document.getElementById('mo2f_scanbarcode_a');
                if(element.style.display === 'none')
                    element.style.display = 'block';
                
                else
                    element.style.display = "none";
            });
            jQuery(document).ready(function() {
                jQuery('.mo2f_gauth').qrcode({
                    'render': 'image',
                    size: 175,
                    'text': jQuery('.mo2f_gauth').data('qrcode')
                });
            });
            
        </script>
    <?php   
}
function mo2f_configure_google_authenticator_onprem( $secret,$url,$otpcode, $session_id_encrypt ) {
	$h_size               = 'h3';
	$gauth_name= get_option('mo2f_google_appname');
	$gauth_name = $gauth_name ? $gauth_name : 'miniOrangeAu';
	
	?>
    <table class="mo2f_configure_ga">
        <tr>
            <td class="mo2f_google_authy_step2">
				<?php echo '<' . esc_html($h_size) . '>' . mo2f_lt( 'Step-1: Set up Google/Authy/LastPass Authenticator' ) . '<span style="float:right">
                        <a href="https://developers.miniorange.com/docs/security/wordpress/wp-security/google-authenticator" target="_blank"><span class="dashicons dashicons-text-page" style="font-size:26px;color:#413c69;float: right;"></span></a>

                        <a href="https://www.youtube.com/watch?v=vVGXjedIaGs" target="_blank"><span class="dashicons dashicons-video-alt3" style="font-size:30px;color:red;float: right;    margin-right: 16px;margin-top: -3px;"></span></a>
                     </span></' . esc_html($h_size) . '>'; ?>
                <hr>

                  

	                <div style="line-height: 5; background: white; margin-left:40px;" id = "mo2f_choose_app_tour">
                        <label for="authenticator_type"><b>1. Choose an Authenticator app:</b></label>

                        <select id="authenticator_type">
                          <option value="google_authenticator">Google Authenticator</option>
                          <option value="msft_authenticator">Microsoft Authenticator</option>
                          <option value="authy_authenticator">Authy Authenticator</option>
                          <option value="last_pass_auth">LastPass Authenticator</option>
                          <option value="free_otp_auth">FreeOTP Authenticator</option>
                          <option value="duo_auth">Duo Mobile Authenticator</option>
                        </select>
                    </div>

                <div id="links_to_apps_tour" style="background-color:white;padding:5px;margin-left:40px;">
                <span id="links_to_apps"></span>
                </div>

                <h4><span id="step_number"></span><?php echo mo2f_lt( 'Scan the QR code from the Authenticator App.' ); ?></h4>
                <div style="margin-left:40px;">
                    <ol>
                        <li><?php echo mo2f_lt( 'In the app, tap on Menu and select "Set up account".' ); ?></li>
                        <li><?php echo mo2f_lt( 'Select "Scan a QR code".' ); ?></li>

                    <form name="f"  id="login_settings_appname_form" method="post" action="">
                        <input type="hidden" name="option" value="mo2f_google_appname" />
                        <input type="hidden" name="mo2f_google_appname_nonce"
                        value="<?php echo esc_html(wp_create_nonce( "mo2f-google-appname-nonce" )) ?>"/>
                        <div class="mo2f_ga_qr_container">
                        <div>
                            <div class="mo2f_gauth_column mo2f_gauth_left" >
                                <div class="mo2f_gauth" id= "displayGAQrCodeTour" style="background: white;" data-qrcode="<?php echo esc_html($url);?>" ></div>
                            </div>
                        </div>
                        <br>
			            <div>
                            
                            <input type="text" class="mo2f_table_textbox" style="" id= "mo2f_change_app_name" name="mo2f_google_auth_appname" placeholder="Enter the app name" value="<?php echo esc_html($gauth_name);?>"  />
                            <br><br>
                            <input type="submit" name="submit" value="Save App Name" class="button button-primary button-large" style="" />
                            
                        </div>
                        <br>
                        </div>
                    </form>

                    </ol>

                    <div><a data-toggle="collapse" href="#mo2f_scanbarcode_a"
                            aria-expanded="false"><b><?php echo mo2f_lt( 'Can\'t scan the QR code? ' ); ?></b></a>
                    </div>
                    <div class="mo2f_collapse"  id="mo2f_scanbarcode_a" style="background: white;">
                        <ol class="mo2f_ol">
                            <li><?php echo mo2f_lt( 'Tap on Menu and select' ); ?>
                                <b> <?php echo mo2f_lt( ' Set up account ' ); ?></b>.
                            </li>
                            <li><?php echo mo2f_lt( 'Select' ); ?>
                                <b> <?php echo mo2f_lt( ' Enter provided key ' ); ?></b>.
                            </li>
                            <li><?php echo mo2f_lt( 'For the' ); ?>
                                <b> <?php echo mo2f_lt( ' Enter account name ' ); ?></b>
								<?php echo mo2f_lt( 'field, type your preferred account name' ); ?>.
                            </li>
                            <li><?php echo mo2f_lt( 'For the' ); ?>
                                <b> <?php echo mo2f_lt( ' Enter your key ' ); ?></b>
								<?php echo mo2f_lt( 'field, type the below secret key' ); ?>:
                            </li>

                            <div class="mo2f_google_authy_secret_outer_div">
                                <div class="mo2f_google_authy_secret_inner_div">
									<?php echo $secret; ?>
                                </div>
                                <div class="mo2f_google_authy_secret">
									<?php echo mo2f_lt( 'Spaces do not matter' ); ?>.
                                </div>
                            </div>
                            <li><?php echo mo2f_lt( 'Key type: make sure' ); ?>
                                <b> <?php echo mo2f_lt( ' Time-based ' ); ?></b>
								<?php echo mo2f_lt( ' is selected' ); ?>.
                            </li>

                            <li><?php echo mo2f_lt( 'Tap Add.' ); ?></li>
                        </ol>
                    </div>
                <br>
                </div>

            </td>
            <td class="mo2f_vertical_line" ></td>
            <td class="mo2f_google_authy_step3">
                <h4><?php echo '<' . esc_html($h_size) . '>' . mo2f_lt( 'Step-2: Verify and Save' ) . '</' . esc_html($h_size) . '>';; ?></h4>
                <hr>
                <div style="display: block;">
                    <div><?php echo mo2f_lt( 'After you have scanned the QR code and created an account, enter the verification code from the scanned account here.' ); ?></div>
                    <br>
                    <form name="f" method="post" action="">
						<span><b><?php echo mo2f_lt( 'Code:' ); ?> </b>&nbsp;
						<input class="mo2f_table_textbox" style="width:200px;" id="EnterOTPGATour" autofocus="true" required="true"
                               type="text" name="google_token" placeholder="<?php echo mo2f_lt( 'Enter OTP' ); ?>"
                               style="width:95%;"/></span><br><br>
                        
                        <input type="hidden" name="option" value="mo2f_configure_google_authenticator_validate"/>
                        <input type="hidden" name="mo2f_session_id" value="<?php echo esc_html($session_id_encrypt) ?>">
						<input type="hidden" name="mo2f_configure_google_authenticator_validate_nonce"
						value="<?php echo esc_html(wp_create_nonce( "mo2f-configure-google-authenticator-validate-nonce" )) ?>"/>
                        <input type="submit" name="validate" id="SaveOTPGATour" class="button button-primary button-large"
                               style="float:left;" value="<?php echo mo2f_lt( 'Verify and Save' ); ?>"/>
                    </form>
                    <form name="f" method="post" action="" id="mo2f_go_back_form">
                                        <input type="hidden" name="option" value="mo2f_go_back"/>
                                        <input style="margin-left: 5px;" type="submit" name="back" id="go_back" class="button button-primary button-large"
                                               value="<?php echo mo2f_lt( 'Back' ); ?>"/>
											   <input type="hidden" name="mo2f_go_back_nonce"
						value="<?php echo esc_html(wp_create_nonce( "mo2f-go-back-nonce" )) ?>"/>
                                    </form>
                </div><br><br>
                <div>
                    <h3 style="color: red">Current Server Time: <span id="mo2f_server_time">--</span></h3>
                </div>
            </td>
        </tr>
    </table>
<?php
$q = sanitize_text_field($_SERVER['REQUEST_TIME'])*1000;
    ?>
    <script>
        var d = new Date(<?php echo esc_html($q) ?>);
        var server_time = d.toLocaleTimeString();
        document.getElementById("mo2f_server_time").innerHTML = server_time;
    </script>
    <?php                       
                echo '<head>';
                echo '</head>';
                echo '<script>';
                echo 'jQuery(document).ready(function() {';
                echo "jQuery('.mo2f_gauth').qrcode({
                                'render': 'image',
                                size: 175,
                                'text': jQuery('.mo2f_gauth').data('qrcode')
                            });";
                echo '});';         
                echo '</script>';

                ?>
    <script>
        jQuery(document).ready(function(){

            jQuery(this).scrollTop(0);
                jQuery('#links_to_apps').html('<p style="background-color:#e8e4e4;padding:5px;margin-left:40px;width:65%">' +
                    'Get the App - <a href="https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2" target="_blank"><b><?php echo mo2f_lt( "Android Play Store" ); ?></b></a>, &nbsp;' +
                    '<a href="http://itunes.apple.com/us/app/google-authenticator/id388497605" target="_blank"><b><?php echo mo2f_lt( "iOS App Store" ); ?>.</b>&nbsp;</p>');
                jQuery('#mo2f_change_app_name').show();
                jQuery('#links_to_apps').show();
        });

        jQuery('input[type=radio][name=mo2f_app_type_radio]').change(function () {
            jQuery('#mo2f_configure_google_authy_form1').submit();
        });

        jQuery('#authenticator_type').change(function(){
                var auth_type = jQuery(this).val();
                if(auth_type == 'google_authenticator'){
                    jQuery('#links_to_apps').html('<p style="background-color:#e8e4e4;padding:5px;margin-left:40px;width:65%">' +
                        'Get the App - <a href="https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2" target="_blank"><b><?php echo mo2f_lt( "Android Play Store" ); ?></b></a>, &nbsp;' +
                        '<a href="http://itunes.apple.com/us/app/google-authenticator/id388497605" target="_blank"><b><?php echo mo2f_lt( "iOS App Store" ); ?>.</b>&nbsp;</p>');
                    jQuery('#mo2f_change_app_name').show();
                    jQuery('#links_to_apps').show();
                }else if(auth_type == 'msft_authenticator'){
                    jQuery('#links_to_apps').html('<p style="background-color:#e8e4e4;padding:5px;margin-left:40px;width:65%">' +
                        'Get the App - <a href="https://play.google.com/store/apps/details?id=com.azure.authenticator" target="_blank"><b><?php echo mo2f_lt( "Android Play Store" ); ?></b></a>, &nbsp;' +
                        '<a href="https://apps.apple.com/us/app/microsoft-authenticator/id983156458" target="_blank"><b><?php echo mo2f_lt( "iOS App Store" ); ?>.</b>&nbsp;</p>');
                    jQuery('#links_to_apps').show();
                }else if(auth_type == 'free_otp_auth'){
                    jQuery('#links_to_apps').html('<p style="background-color:#e8e4e4;padding:5px;margin-left:40px;width:65%">' +
                        'Get the App - <a href="https://play.google.com/store/apps/details?id=org.fedorahosted.freeotp" target="_blank"><b><?php echo mo2f_lt( "Android Play Store" ); ?></b></a>, &nbsp;' +
                        '<a href="https://apps.apple.com/us/app/freeotp-authenticator/id872559395" target="_blank"><b><?php echo mo2f_lt( "iOS App Store" ); ?>.</b>&nbsp;</p>');
                    jQuery('#links_to_apps').show();
                }else if(auth_type == 'duo_auth'){
                    jQuery('#links_to_apps').html('<p style="background-color:#e8e4e4;padding:5px;margin-left:40px;width:65%">' +
                        'Get the App - <a href="https://play.google.com/store/apps/details?id=com.duosecurity.duomobile" target="_blank"><b><?php echo mo2f_lt( "Android Play Store" ); ?></b></a>, &nbsp;' +
                        '<a href="https://apps.apple.com/in/app/duo-mobile/id422663827" target="_blank"><b><?php echo mo2f_lt( "iOS App Store" ); ?>.</b>&nbsp;</p>');
                    jQuery('#links_to_apps').show();
                }else if(auth_type == 'authy_authenticator'){
                    jQuery('#links_to_apps').html('<p style="background-color:#e8e4e4;padding:5px;margin-left:40px;width:65%">' +
                        'Get the App - <a href="https://play.google.com/store/apps/details?id=com.authy.authy" target="_blank"><b><?php echo mo2f_lt( "Android Play Store" ); ?></b></a>, &nbsp;' +
                        '<a href="https://itunes.apple.com/in/app/authy/id494168017" target="_blank"><b><?php echo mo2f_lt( "iOS App Store" ); ?>.</b>&nbsp;</p>');
                    jQuery('#links_to_apps').show();
                }else{
                    jQuery('#links_to_apps').html('<p style="background-color:#e8e4e4;padding:5px;margin-left:40px;width:65%">' +
                        'Get the App - <a href="https://play.google.com/store/apps/details?id=com.lastpass.authenticator" target="_blank"><b><?php echo mo2f_lt( "Android Play Store" ); ?></b></a>, &nbsp;' +
                        '<a href="https://itunes.apple.com/in/app/lastpass-authenticator/id1079110004" target="_blank"><b><?php echo mo2f_lt( "iOS App Store" ); ?>.</b>&nbsp;</p>');
                    jQuery('#mo2f_change_app_name').show();
                    jQuery('#links_to_apps').show();
                }
            });

    </script>
	<?php
}

?>