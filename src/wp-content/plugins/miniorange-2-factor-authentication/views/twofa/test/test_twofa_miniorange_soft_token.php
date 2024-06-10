<?php function mo2f_test_miniorange_soft_token( $user ) {?>
        <div style="width:100%;">
            <h3><?php echo mo2f_lt( 'Test Soft Token' ); ?></h3>
            <hr>
            <p><?php echo mo2f_lt( 'Open your' ); ?>
                <b><?php echo mo2f_lt( 'miniOrange Authenticator App ' ); ?></b> <?php echo mo2f_lt( 'and ' ); ?>
                <?php echo mo2f_lt( 'enter the' ); ?>
                <b><?php echo mo2f_lt( 'one time passcode' ); ?></b> <?php echo mo2f_lt( 'shown in the App under your account.' ); ?>
            </p>
            <form name="f" method="post" action="" id="mo2f_test_token_form">
                <input type="hidden" name="option" value="mo2f_validate_soft_token"/>
                <input type="hidden" name="mo2f_validate_soft_token_nonce"
                                value="<?php echo esc_html(wp_create_nonce( "mo2f-validate-soft-token-nonce" )) ?>"/>
                <input class="mo2f_table_textbox" style="width:200px;" autofocus="true" type="text" name="otp_token" required
                       placeholder="<?php echo mo2f_lt( 'Enter OTP' ); ?>" style="width:95%;"/>

                <br><br>
                    <input type="button" name="back" id="go_back" class="button button-primary button-large"
                           value="<?php echo mo2f_lt( 'Back' ); ?>"/>
                <input type="submit" name="validate" id="validate" class="button button-primary button-large"
                       value="<?php echo mo2f_lt( 'Validate OTP' ); ?>"/>

            </form>

            <form name="f" method="post" action="" id="mo2f_go_back_form">
                <input type="hidden" name="option" value="mo2f_go_back"/>
                <input type="hidden" name="mo2f_go_back_nonce"
                                value="<?php echo esc_html(wp_create_nonce( "mo2f-go-back-nonce" )) ?>"/>
            </form>
        </div>
    <script>
        jQuery('#go_back').click(function () {
            jQuery('#mo2f_go_back_form').submit();
        });
    </script>
<?php }

?>