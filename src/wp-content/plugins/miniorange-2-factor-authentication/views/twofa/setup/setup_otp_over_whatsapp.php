<?php

function mo2f_configure_otp_over_Whatsapp( $user ) {
	
    $whatsapp_id = get_user_meta($user->ID,'mo2f_whatsapp_id',true);
    $whatsapp_number = get_user_meta($user->ID,'mo2f_whatsapp_num',true);
    if($whatsapp_id == '')
        $whatsapp_id = get_user_meta($user->ID,'mo2f_temp_whatsappID',true);
    if($whatsapp_number == '')
        $whatsapp_number = get_user_meta($user->ID,'mo2f_temp_whatsapp_num',true);


	?>

    <h3>
        <?php echo mo2f_lt( 'Configure OTP over Whatsapp <p style="text-align: right;"> Note: The Free API is only for personal use. </p>' ); ?>
        
    </h3>
    <h4> Remaining Whatsapp Transaction: <b><?php echo get_site_option('cmVtYWluaW5nV2hhdHNhcHB0cmFuc2FjdGlvbnM='); ?></b></h4> 
    <hr>

    <form name="f" method="post" action="" id="mo2f_verifywhatsappID_form">
        <input type="hidden" name="option" value="mo2f_configure_otp_over_Whatsapp_send_otp"/>
		<input type="hidden" name="mo2f_configure_otp_over_Whatsapp_send_otp_nonce"
						value="<?php echo wp_create_nonce( "mo2f-configure-otp-over-Whatsapp-send-otp-nonce" ) ?>"/>

        <h4 class='mo_wpns_not_bold'> 1. Add the given phone number (+34 644 17 94 64) in your phone with any name of your choice.  <br><br> 2. Open the Whatsapp app in your phone and send the below text to the given phone number. <b>Message:</b> I allow callmebot to send me messages</h4>
        <div style="display:inline;">
            
            <h4 class='mo_wpns_not_bold'> 3. Enter the recieved API Key and your phone number in the below box.</h4>
            <table>
           <tr>
            <th>
            API Key:
            </th>
            <th>
            <input class="mo2f_table_textbox" style="width:200px;" type="text" name="verify_whatsappID" required id="phone"
                   value="<?php echo esc_html($whatsapp_id) ?>" pattern="[0-9]+"
                   title="<?php echo mo2f_lt( 'Enter API Key recieved on your Whatsapp without any space or dashes' ); ?>"/><br>
            </th>
        </tr>
        <tr>
            <th>
            Phone Number(with Country code):
            </th>
            <th>
            <input class="mo2f_table_textbox" style="width:200px;" type="text" required name="verify_whatsappNum" id="phone"
                   value="<?php echo $whatsapp_number ?>" pattern="[\+]?[0-9]{1,4}\s?[0-9]{7,12}"
                   title="<?php echo mo2f_lt( 'Enter your Whatsapp Number with your country code.' ); ?>"/><br>
            </th>
            </tr>
            </table>  
            <input type="submit" name="verify" id="verify" class="button button-primary button-large"
                   value="<?php echo mo2f_lt( 'Verify' ); ?>"/>
        </div>
    </form>
    <form name="f" method="post" action="" id="mo2f_validateotp_form">
        <input type="hidden" name="option" value="mo2f_configure_otp_over_Whatsapp_validate"/>
		<input type="hidden" name="mo2f_configure_otp_over_Whatsapp_validate_nonce"
						value="<?php echo wp_create_nonce( "mo2f-configure-otp-over-Whatsapp-validate-nonce" ) ?>"/>
        <p><?php echo mo2f_lt( 'Enter One Time Passcode' ); ?></p>
        <input class="mo2f_table_textbox" style="width:200px;" autofocus="true" type="text" name="otp_token"
               placeholder="<?php echo mo2f_lt( 'Enter OTP' ); ?>" style="width:95%;"/>
        <a href="#resendWhatsappSMS"><?php echo mo2f_lt( 'Resend OTP ?' ); ?></a>
        <br><br>
        <input type="button" name="back" id="go_back" class="button button-primary button-large"
               value="<?php echo mo2f_lt( 'Back' ); ?>"/>
        <input type="submit" name="validate" id="validate" class="button button-primary button-large"
               value="<?php echo mo2f_lt( 'Validate OTP' ); ?>"/>
    </form><br>
    <form name="f" method="post" action="" id="mo2f_go_back_form">
        <input type="hidden" name="option" value="mo2f_go_back"/>
		<input type="hidden" name="mo2f_go_back_nonce"
						value="<?php echo wp_create_nonce( "mo2f-go-back-nonce" ) ?>"/>
    </form>
    <script>
        jQuery('#go_back').click(function () {
            jQuery('#mo2f_go_back_form').submit();
        });
        jQuery('a[href=\"#resendWhatsappSMS\"]').click(function (e) {
            jQuery('#mo2f_verifyChatID_form').submit();
        });

    </script>
	<?php
}

?>