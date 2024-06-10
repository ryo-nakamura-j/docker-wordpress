<?php function mo2f_test_google_authy_authenticator( $user, $method ) {

	 ?>
        <h3><?php echo mo2f_lt( 'Test ' ) . mo2f_lt( $method ); ?></h3>
        <hr>
    <p><?php echo mo2f_lt( 'Enter the verification code from the configured account in your ' ) . mo2f_lt( $method )
	              . mo2f_lt( ' app.' ); ?></p>

    <form name="f" method="post" action="">
        <input type="hidden" name="option" value="mo2f_validate_google_authy_test"/>
		<input type="hidden" name="mo2f_validate_google_authy_test_nonce"
						value="<?php echo esc_html(wp_create_nonce( "mo2f-validate-google-authy-test-nonce" )) ?>"/>

        <input class="mo2f_table_textbox" style="width:200px;" autofocus="true" type="text" name="otp_token" required
               placeholder="<?php echo mo2f_lt( 'Enter OTP' ); ?>" style="width:95%;"/>
        <br><br>
            <input type="button" name="back" id="go_back" class="button button-primary button-large"
                   value="<?php echo mo2f_lt( 'Back' ); ?>"/>
        <input type="submit" name="validate" id="validate" class="button button-primary button-large"
               value="<?php echo mo2f_lt( 'Submit' ); ?>"/>

    </form>
    <form name="f" method="post" action="" id="mo2f_go_back_form">
        <input type="hidden" name="option" value="mo2f_go_back"/>
		<input type="hidden" name="mo2f_go_back_nonce"
						value="<?php echo esc_html(wp_create_nonce( "mo2f-go-back-nonce" )) ?>"/>
    </form>
    <script>
        jQuery('#go_back').click(function () {
            jQuery('#mo2f_go_back_form').submit();
        });
    </script>

	<?php
} ?>