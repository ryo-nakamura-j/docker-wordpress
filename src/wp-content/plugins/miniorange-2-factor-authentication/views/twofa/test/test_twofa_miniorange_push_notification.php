<?php function mo2f_test_miniorange_push_notification( $user ) {  ?>

        <h3><?php echo mo2f_lt( 'Test Push Notification' ); ?></h3>
        <hr>
    <div>
        <br><br>
        <center>
            <h4><?php echo mo2f_lt( 'A Push Notification has been sent to your phone.' ); ?>
                <br><?php echo mo2f_lt( 'We are waiting for your approval...' ); ?>
            </h4>
            <img src="<?php echo esc_url(plugins_url( 'includes/images/ajax-loader-login.gif', dirname(dirname(dirname(__FILE__))))); ?>"/>
        </center>
            <input type="button" name="back" id="go_back" class="button button-primary button-large"
                   value="<?php echo mo2f_lt( 'Back' ); ?>" style="margin-top:100px;margin-left:10px;"/>
        <br><br>
    </div>

    <form name="f" method="post" action="" id="mo2f_go_back_form">
        <input type="hidden" name="option" value="mo2f_go_back"/>
		<input type="hidden" name="mo2f_go_back_nonce"
						value="<?php echo esc_html(wp_create_nonce( "mo2f-go-back-nonce" )) ?>"/>
    </form>
    <form name="f" method="post" id="mo2f_push_success_form" action="">
        <input type="hidden" name="option" value="mo2f_out_of_band_success"/>
		<input type="hidden" name="mo2f_out_of_band_success_nonce"
						value="<?php echo esc_html(wp_create_nonce( "mo2f-out-of-band-success-nonce" )) ?>"/>
    </form>
    <form name="f" method="post" id="mo2f_push_error_form" action="">
        <input type="hidden" name="option" value="mo2f_out_of_band_error"/>
		<input type="hidden" name="mo2f_out_of_band_error_nonce"
						value="<?php echo esc_html(wp_create_nonce( "mo2f-out-of-band-error-nonce" )) ?>"/>
    </form>
   
    <script>
        jQuery('#go_back').click(function () {
            jQuery('#mo2f_go_back_form').submit();
        });

        var timeout;
        pollMobileValidation();

        function pollMobileValidation() {
            var transId = "<?php echo esc_html(get_user_meta($user->ID, 'mo2f_transactionId', true));  ?>";
            var jsonString = "{\"txId\":\"" + transId + "\"}";
            var postUrl = "<?php echo esc_url(MO_HOST_NAME);  ?>" + "/moas/api/auth/auth-status";

            jQuery.ajax({
                url: postUrl,
                type: "POST",
                dataType: "json",
                data: jsonString,
                contentType: "application/json; charset=utf-8",
                success: function (result) {
                    var status = JSON.parse(JSON.stringify(result)).status;
                    if (status == 'SUCCESS') {
                        jQuery('#mo2f_push_success_form').submit();
                    } else if (status == 'ERROR' || status == 'FAILED' || status == 'DENIED') {
                        jQuery('#mo2f_push_error_form').submit();
                    } else {
                        timeout = setTimeout(pollMobileValidation, 3000);
                    }
                }
            });
        }

    </script>

<?php } ?>
