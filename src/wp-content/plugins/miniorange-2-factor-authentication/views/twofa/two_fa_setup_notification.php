<?php

function mo2f_display_test_2fa_notification( $user ) {
    
	global $Mo2fdbQueries, $mo2f_dirName;
	$mo2f_configured_2FA_method = $Mo2fdbQueries->get_user_detail( 'mo2f_configured_2FA_method', $user->ID );
        
    if(MO2F_IS_ONPREM)
    {
        if($mo2f_configured_2FA_method == 'Google Authenticator'){
            $currentTimeSlice = floor(time() / 30);
            $code_array = array();
            include_once $mo2f_dirName . DIRECTORY_SEPARATOR. 'handler'.DIRECTORY_SEPARATOR. 'twofa' . DIRECTORY_SEPARATOR . 'gaonprem.php';
            $gauth_obj= new Google_auth_onpremise();
            $secret= $gauth_obj->mo_GAuth_get_secret($user->ID);
            for ($i = -3; $i <= 3; ++$i) {
                $calculatedCode = $gauth_obj->getCode($secret, $currentTimeSlice + $i);
                array_push($code_array, $calculatedCode);
            }
        }

    }

    wp_print_scripts( 'jquery' );
?>
    <div id="twoFAtestAlertModal" class="modal" role="dialog">
        <div class="mo2f_modal-dialog">
            <!-- Modal content-->
            <div class="modal-content" style="width:660px !important;">
                <center>
                <div class="modal-header">
                    <h2 class="mo2f_modal-title" style="color: #2271b1;">2FA Setup Successful.</h2>
                    <span type="button" id="test-methods" class="modal-span-close" data-dismiss="modal">&times;</span>
                </div>
                <div class="mo2f_modal-body">
                    <p style="font-size:14px;"><b><?php echo esc_attr($mo2f_configured_2FA_method); ?> </b> has been set as your 2-factor authentication method.
                    <br>
                    <?php if($mo2f_configured_2FA_method == 'Google Authenticator' && MO2F_IS_ONPREM){ ?>
                        <p><b>Current valid OTPs for Google Authenticator</b></p>
                        <table cellspacing="10">
                            <tr><td><?php echo esc_attr($code_array[0]); ?></td><td><?php echo esc_attr($code_array[1]); ?></td><td><?php echo esc_attr($code_array[2]); ?></td><td><?php echo esc_attr($code_array[3]); ?></td><td><?php echo esc_attr($code_array[4]); ?></td></tr>
                        </table>
                    <?php } ?>
                    <br>Please test the login flow once with 2nd factor in another browser or in an incognito window of the same browser to ensure you don't get locked out of your site.</p>
                </div>
                <div class="mo2f_modal-footer">
                    <button type="button" id="test-methods-button" class="button button-primary button-large" data-dismiss="modal">Test it!</button>
                </div>
                    <br>
            </center>
            </div>
        </div>
    </div>

    <script>
        jQuery('#twoFAtestAlertModal').css('display', 'block');
        jQuery('#test-methods').click(function(){
            jQuery('#twoFAtestAlertModal').css('display', 'none');
        });
        jQuery('#test-methods-button').click(function(){
            jQuery('#twoFAtestAlertModal').css('display', 'none');
            testAuthenticationMethod('<?php echo esc_html($mo2f_configured_2FA_method); ?>');
        });
    </script>
<?php }
?>