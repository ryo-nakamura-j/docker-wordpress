<?php

function mo2f_configure_otp_over_Telegram( $user ) {
	
    $chat_id = get_user_meta($user->ID,'mo2f_chat_id',true);

    if($chat_id == '')
        $chat_id = get_user_meta($user->ID,'mo2f_temp_chatID',true);


	?>

    <h3><?php echo mo2f_lt( 'Configure OTP over Telegram' ); ?>
    </h3>
    <h4 style="padding:10px; background-color: #a7c5eb"> Remaining Telegram Transactions: <b>Unlimited</b></h4>
    <hr>

    <form name="f" method="post" action="" id="mo2f_verifychatID_form">
        <input type="hidden" name="option" value="mo2f_configure_otp_over_Telegram_send_otp"/>
		<input type="hidden" name="mo2f_configure_otp_over_Telegram_send_otp_nonce"
						value="<?php echo esc_html(wp_create_nonce( "mo2f-configure-otp-over-Telegram-send-otp-nonce" )) ?>"/>

        <h4 class='mo_wpns_not_bold'> 1. Open the telegram app and search for miniorange2fa_bot. Click on start button or send <b>/start</b> message.</h4>
        <div style="display:inline;">
            <h4 class='mo_wpns_not_bold'> 2. Enter the recieved chat id in the below box.
            <h4>Chat ID:
            <input class="mo2f_table_textbox" style="width:200px;" type="text" name="verify_chatID" id="phone"
                   value="<?php echo esc_html($chat_id) ?>" pattern="[0-9]+" 
                   title="<?php echo mo2f_lt( 'Enter Chat ID recieved on your Telegram without any space or dashes' ); ?>"/><br></h4>
            <input type="submit" name="verify" id="verify" class="button button-primary button-large"
                   value="<?php echo mo2f_lt( 'Verify' ); ?>"/>
        </div>
    </form>
    <form name="f" method="post" action="" id="mo2f_validateotp_form">
        <input type="hidden" name="option" value="mo2f_configure_otp_over_Telegram_validate"/>
		<input type="hidden" name="mo2f_configure_otp_over_Telegram_validate_nonce"
						value="<?php echo esc_html(wp_create_nonce( "mo2f-configure-otp-over-Telegram-validate-nonce" )) ?>"/>
        <p><?php echo mo2f_lt( 'Enter One Time Passcode' ); ?></p>
        <input class="mo2f_table_textbox" style="width:200px;" autofocus="true" type="text" name="otp_token"
               placeholder="<?php echo mo2f_lt( 'Enter OTP' ); ?>" style="width:95%;"/>
        <br><br>
        <input type="button" name="back" id="go_back" class="button button-primary button-large"
               value="<?php echo mo2f_lt( 'Back' ); ?>"/>
        <input type="submit" name="validate" id="validate" class="button button-primary button-large"
               value="<?php echo mo2f_lt( 'Validate OTP' ); ?>"/>
    </form><br>
    <form name="f" method="post" action="" id="mo2f_go_back_form">
        <input type="hidden" name="option" value="mo2f_go_back"/>
		<input type="hidden" name="mo2f_go_back_nonce"
						value="<?php echo esc_html(wp_create_nonce( "mo2f-go-back-nonce" )) ?>"/>
    </form>
    <script>
        jQuery('#go_back').click(function () {
            jQuery('#mo2f_go_back_form').submit();
        });
        jQuery('a[href=\"#resendtelegramSMS\"]').click(function (e) {
            jQuery('#mo2f_verifyChatID_form').submit();
        });

    </script>
	<?php
}

?>