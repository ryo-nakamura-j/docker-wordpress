<?php
global $Mo2fdbQueries;
    $roles = get_editable_roles();
 
    $mo_2factor_user_registration_status = $Mo2fdbQueries->get_user_detail( 'mo_2factor_user_registration_status', $user->ID );
    ?>
<?php if ( !MoWpnsUtility::get_mo2f_db_option('mo2f_is_NC', 'get_option') && MoWpnsUtility::get_mo2f_db_option('mo2f_is_NC', 'get_option') ) { ?>
    <div class="mo2f_advanced_options_EC" style="width: 85%;border: 0px;">
			<?php echo get_standard_premium_options( $user ); ?>
        </div>
	<?php } else {

		$mo2f_active_tab = '2factor_setup';
		?>
    <div class="mo_wpns_setting_layout">
        <div class="mo2f_advanced_options_EC">

            <div id="mo2f_login_options">
                <a href="#standard_premium_options" style="float:right">Show Standard/Premium
                    Features</a></h3>

                <form name="f" id="login_settings_form" method="post" action="">
                    <input type="hidden" name="option" value="mo_auth_login_settings_save"/>
					<input type="hidden" name="mo_auth_login_settings_save_nonce"
						value="<?php echo esc_html(wp_create_nonce( "mo-auth-login-settings-save-nonce" )) ?>"/>
                    <div class="row">
                        <h3 style="padding:10px;"><?php echo mo2f_lt( 'Select Login Screen Options' ); ?>

                    </div>
                    <hr>
                    <br>


                    <div style="margin-left: 2%;">
                        <input type="radio" name="mo2f_login_option" value="1"
							<?php checked( MoWpnsUtility::get_mo2f_db_option('mo2f_login_option', 'get_option') );
							if ( $mo_2factor_user_registration_status == 'MO_2_FACTOR_PLUGIN_SETTINGS' or MO2F_IS_ONPREM) {
							} else {
								echo 'disabled';
							} ?> />
						<?php echo mo2f_lt( 'Login with password + 2nd Factor ' ); ?>
                        <i>(<?php echo mo2f_lt( 'Default & Recommended' ); ?>)&nbsp;&nbsp;</i>

                        <br><br>

                        <div style="margin-left:6%;">
                            <input type="checkbox" id="mo2f_remember_device" name="mo2f_remember_device"
                                   value="1" <?php checked( get_option( 'mo2f_remember_device' ) == 1 );
							if ( $mo_2factor_user_registration_status == 'MO_2_FACTOR_PLUGIN_SETTINGS' and MO2F_IS_ONPREM!=1)  {
							} else {
								echo 'disabled';
							} ?> />Enable
                            '<b><?php echo mo2f_lt( 'Remember device' ); ?></b>' <?php echo mo2f_lt( 'option ' ); ?><br>

                            <div class="mo2f_advanced_options_note"><p style="padding:5px;">
                                    <i><?php echo mo2f_lt( ' Checking this option will display an option ' ); ?>
                                        '<b><?php echo mo2f_lt( 'Remember this device' ); ?></b>'<?php echo mo2f_lt( 'on 2nd factor screen. In the next login from the same device, user will bypass 2nd factor, i.e. user will be logged in through username + password only.' ); ?>
                                    </i></p></div>
                        </div>

                        <br>

                        <input type="radio" name="mo2f_login_option" value="0"
							<?php checked( ! MoWpnsUtility::get_mo2f_db_option('mo2f_login_option', 'get_option') );
							if ( $mo_2factor_user_registration_status == 'MO_2_FACTOR_PLUGIN_SETTINGS' or MO2F_IS_ONPREM) {
							} else {
								echo 'disabled';
							} ?> />
						<?php echo mo2f_lt( 'Login with 2nd Factor only ' ); ?>
                        <i>(<?php echo mo2f_lt( 'No password required.' ); ?>)</i> &nbsp;<a 
                        data-toggle="collapse"
                        id="showLoginwith2ndFactoronly"
                        href="#Loginwith2ndFactoronly"
                        aria-expanded="false"><?php echo mo2f_lt( 'See preview' ); ?></a>
                        <br>
                        <div class="mo2f_collapse" id="Loginwith2ndFactoronly" style="height:300px; ">
                            <center><br>
                                <img style="height:300px;"
                                     src="https://login.xecurify.com/moas/images/help/login-help-1.png">
                            </center>
                        </div>
                        <br>
                        <br>
                        <div class="mo2f_advanced_options_note"><p style="padding:5px;">
                                <i><?php echo mo2f_lt( 'Checking this option will add login with your phone button below default login form. Click above link to see the preview.' ); ?></i>
                            </p></div>
                        <div id="loginphonediv" hidden><br>
                            <input type="checkbox" id="mo2f_login_with_username_and_2factor"
                                   name="mo2f_login_with_username_and_2factor"
                                   value="1" <?php checked( get_option( 'mo2f_enable_login_with_2nd_factor' ) == 1 );
							if ( $mo_2factor_user_registration_status == 'MO_2_FACTOR_PLUGIN_SETTINGS' or MO2F_IS_ONPREM ){
							} else {
								echo 'disabled';
							} ?> />
							<?php echo mo2f_lt( '	I want to hide default login form.' ); ?> &nbsp;<a
                                    class=""
                                    data-toggle="collapse"
                                    href="#hideDefaultLoginForm"
                                    id = 'showhideDefaultLoginForm'
                                    aria-expanded="false"><?php echo mo2f_lt( 'See preview' ); ?></a>
                            <br>
                            <div class="mo2f_collapse" id="showhideDefaultLoginForm" style="height:300px;">
                                <center><br>
                                    <img style="height:300px;"
                                         src="https://login.xecurify.com/moas/images/help/login-help-3.png">
                                </center>
                            </div>
                            
                            <br>
                            <br>
                            <div class="mo2f_advanced_options_note"><p style="padding:5px;">
                                    <i><?php echo mo2f_lt( 'Checking this option will hide default login form and just show login with your phone. Click above link to see the preview.' ); ?></i>
                                </p></div>
                        </div>
                        <br>
                    </div>
                    <div>
                        <h3 style="padding:10px;"><?php echo mo2f_lt( 'Backup Methods' ); ?></h3></div>
                    <hr>
                    <br>
                    <div style="margin-left: 2%">
                        <input type="checkbox" id="mo2f_forgotphone" name="mo2f_forgotphone"
                               value="1" <?php checked( MoWpnsUtility::get_mo2f_db_option('mo2f_enable_forgotphone', 'get_option') == 1 );
						if ( $mo_2factor_user_registration_status == 'MO_2_FACTOR_PLUGIN_SETTINGS' ) {
						} else {
							echo 'disabled';
						} ?> />
						<?php echo mo2f_lt( 'Enable Forgot Phone.' ); ?>

                        <div class="mo2f_advanced_options_note"><p style="padding:5px;">
                                <i><?php echo mo2f_lt( 'This option will provide you an alternate way of logging in to your site in case you are unable to login with your primary authentication method.' ); ?></i>
                            </p></div>
                        <br>

                    </div>
                    <div>
                        <h3 style="padding:10px;">XML-RPC <?php echo mo2f_lt( 'Settings' ); ?></h3></div>
                    <hr>
                    <br>
                    <div style="margin-left: 2%">
                        <input type="checkbox" id="mo2f_enable_xmlrpc" name="mo2f_enable_xmlrpc"
                               value="1" <?php checked( MoWpnsUtility::get_mo2f_db_option('mo2f_enable_xmlrpc', 'get_option') == 1 );
						if ( $mo_2factor_user_registration_status == 'MO_2_FACTOR_PLUGIN_SETTINGS' ) {
						} else {
							echo 'disabled';
						} ?> />
						<?php echo mo2f_lt( 'Enable XML-RPC Login.' ); ?>
                        <div class="mo2f_advanced_options_note"><p style="padding:5px;">
                                <i><?php echo mo2f_lt( 'Enabling this option will decrease your overall login security. Users will be able to login through external applications which support XML-RPC without authenticating from miniOrange. ' ); ?>
                                    <b><?php echo mo2f_lt( 'Please keep it unchecked.' ); ?></b></i></p></div>


                    <div style="padding:10px;">
                    <br><br>
                        <center>
                        <?php
						if ( $mo_2factor_user_registration_status == 'MO_2_FACTOR_PLUGIN_SETTINGS' or MO2F_IS_ONPREM) {
                            ?>
                            <input type="submit" name="submit" value="<?php echo mo2f_lt( 'Save Settings' ); ?>"
                               class="button button-primary button-large">
                            <?php
						} else {
                            ?>
                            <input type="submit" name="submit" value="<?php echo mo2f_lt( 'Save Settings' ); ?>"
                               class="mo_wpns_button" disabled style="background-color: #2271b1;padding: 11px 28px; text-align: center; text-decoration: none; display: inline-block; font-size: 16px; margin: 4px 2px;">
                            <?php
						} ?> 
                        </center>
                    </div>
                </div>
                    <br></form>
                <hr>
            </div>
         </div>
			<?php echo get_standard_premium_options( $user ); ?>
        </div>
        <?php
    }
        ?>

        <script>

        if (jQuery("input[name=mo2f_login_option]:radio:checked").val() == 0) {
            jQuery('#loginphonediv').show();
        }
        jQuery("input[name=mo2f_login_option]:radio").change(function () {
            if (this.value == 1) {
                jQuery('#loginphonediv').hide();
            } else {
                jQuery('#loginphonediv').show();
            }
        });

        
        jQuery('#Loginwith2ndFactoronly').hide();
        jQuery('#showLoginwith2ndFactoronly').click(function(){
                    jQuery('#Loginwith2ndFactoronly').slideToggle(700);    
        });        
        jQuery('#Loginwith2ndFactoronlyStandard').hide();
        jQuery('#showLoginwith2ndFactoronlyStandard').click(function(){
            jQuery('#Loginwith2ndFactoronlyStandard').slideToggle(700);    
        });
        jQuery('#LoginWithUsernameOnlyStandard').hide();
        jQuery('#showLoginWithUsernameOnlyStandard').click(function(){
            jQuery('#LoginWithUsernameOnlyStandard').slideToggle(700);    
        });
        jQuery('#Loginwith2ndFactoronlyPremium').hide();
        jQuery('#showLoginwith2ndFactoronlyPremium').click(function(){
            jQuery('#Loginwith2ndFactoronlyPremium').slideToggle(700);    
        });
        jQuery('#LoginWithUsernameOnlyPremium').hide();
        jQuery('#showLoginWithUsernameOnlyPremium').click(function(){
            jQuery('#LoginWithUsernameOnlyPremium').slideToggle(700);    
        });                
        jQuery('#showhideDefaultLoginForm').hide();
        jQuery('#showhideDefaultLoginForm').click(function(){
            jQuery('#showhideDefaultLoginForm').slideToggle(700);    
        });
        
       
        function show_backup_options() {
            jQuery("#backup_options").slideToggle(700);
            jQuery("#login_options").hide();
            jQuery("#customizations").hide();
            jQuery("#customizations_prem").hide();
            jQuery("#backup_options_prem").hide();
            jQuery("#inline_registration_options").hide();
        }

        function show_customizations() {
            jQuery("#login_options").hide();
            jQuery("#inline_registration_options").hide();
            jQuery("#backup_options").hide();
            jQuery("#customizations_prem").hide();
            jQuery("#backup_options_prem").hide();
            jQuery("#customizations").slideToggle(700);

        }

        jQuery("#backup_options_prem").hide();

        function show_backup_options_prem() {
            jQuery("#backup_options_prem").slideToggle(700);
            jQuery("#login_options").hide();
            jQuery("#customizations").hide();
            jQuery("#customizations_prem").hide();
            jQuery("#inline_registration_options").hide();
            jQuery("#backup_options").hide();
        }

        jQuery("#login_options").hide();

        function show_login_options() {
            jQuery("#inline_registration_options").hide();
            jQuery("#customizations").hide();
            jQuery("#backup_options").hide();
            jQuery("#backup_options_prem").hide();
            jQuery("#customizations_prem").hide();
            jQuery("#login_options").slideToggle(700);
        }

        jQuery("#inline_registration_options").hide();

        function show_inline_registration_options() {
            jQuery("#login_options").hide();
            jQuery("#customizations").hide();
            jQuery("#backup_options").hide();
            jQuery("#backup_options_prem").hide();
            jQuery("#customizations_prem").hide();
            jQuery("#inline_registration_options").slideToggle(700);

        }

        jQuery("#customizations_prem").hide();

        function show_customizations_prem() {
            jQuery("#inline_registration_options").hide();
            jQuery("#login_options").hide();
            jQuery("#customizations").hide();
            jQuery("#backup_options").hide();
            jQuery("#backup_options_prem").hide();
            jQuery("#customizations_prem").slideToggle(700);

        }

        function showLoginOptions() {
            jQuery("#mo2f_login_options").show();
        }

        function showLoginOptions() {
            jQuery("#mo2f_login_options").show();
        }


    </script>
<?php
function get_standard_premium_options( $user ) {
    $is_NC = MoWpnsUtility::get_mo2f_db_option('mo2f_is_NC', 'get_option');

    ?>
    <div >
        <div id="standard_premium_options" style="text-align: center;">
            <p style="font-size:22px;color:darkorange;padding:10px;"><?php echo mo2f_lt( 'Features in the Standard Plan' ); ?></p>

        </div>

        <hr>
        <?php if ( $is_NC ) { ?>
            <div>
                <a class="mo2f_view_backup_options" onclick="show_backup_options()">
                    <img src="<?php echo plugins_url( 'includes/images/right-arrow.png', dirname(dirname(__FILE__ ))); ?>"
                         class="mo2f_advanced_options_images"/>

                    <p class="mo2f_heading_style"><?php echo mo2f_lt( 'Backup Options' ); ?></p>
                </a>

            </div>
            <div id="backup_options" style="margin-left: 5%;">

                <div class="mo2f_advanced_options_note"><p style="padding:5px;">
                        <i><?php echo mo2f_lt( 'Use these backup options to login to your site in case your 
                                phone is lost / not accessible or if you are not able to login using your primary 
                                authentication method.' ); ?></i></p></div>

                <ol class="mo2f_ol">
                    <li><?php echo mo2f_lt( 'KBA (Security Questions)' ); ?></li>
                </ol>

            </div>
        <?php } ?>

        <div>
            <a class="mo2f_view_customizations" onclick="show_customizations()">
             

                <p class="mo2f_heading_style"><?php echo mo2f_lt( 'Customizations' ); ?></p>
            </a>
        </div>


        <div id="customizations" style="margin-left: 5%;">

            <p style="font-size:15px;font-weight:bold">1. <?php echo mo2f_lt( 'Login Screen Options' ); ?></p>
            <div>
                <ul style="margin-left:4%" class="mo2f_ol">
                    <li><?php echo mo2f_lt( 'Login with Wordpress username/password and 2nd Factor' ); ?> <a
                                class="" data-toggle="collapse" id="showLoginwith2ndFactoronlyStandard" href="#Loginwith2ndFactoronlyStandard"
                                aria-expanded="false">[ <?php echo mo2f_lt( 'See Preview' ); ?>
                            ]</a>
                            <div class="mo2f_collapse" id="Loginwith2ndFactoronlyStandard" style="height:300px;">
                            <center><br>
                                <img style="height:300px;"
                                     src="https://login.xecurify.com/moas/images/help/login-help-1.png">
                            </center>
                        </div>
                        
                    </li><br>
                    <li><?php echo mo2f_lt( 'Login with Wordpress username and 2nd Factor only' ); ?> <a
                                class="" data-toggle="collapse" id="showLoginWithUsernameOnlyStandard" href="#LoginWithUsernameOnlyStandard"
                                aria-expanded="false">[ <?php echo mo2f_lt( 'See Preview' ); ?>
                            ]</a>
                        <br>
                        <div class="mo2f_collapse" id="LoginWithUsernameOnlyStandard" style="height:300px;">
                            <center><br>
                                <img style="height:300px;"
                                     src="https://login.xecurify.com/moas/images/help/login-help-3.png">
                            </center>
                        </div>
                        <br>
                    </li>
                </ul>


            </div>
            <br>
            <p style="font-size:15px;font-weight:bold">2. <?php echo mo2f_lt( 'Custom Redirect URLs' ); ?></p>
            <p style="margin-left:4%"><?php echo mo2f_lt( 'Enable Custom Relay state URL\'s (based on user roles in Wordpress) to which the users
                will get redirected to, after the 2-factor authentication' ); ?>'.</p>


            <br>
            <p style="font-size:15px;font-weight:bold">3. <?php echo mo2f_lt( 'Custom Security Questions (KBA)' ); ?></p>
            <div id="mo2f_customKBAQuestions1">
                <p style="margin-left:4%"><?php echo mo2f_lt( 'Add up to 16 Custom Security Questions for Knowledge based authentication (KBA).
                    You also have the option to select how many standard and custom questions should be shown to the
                    users' ); ?>.</p>

            </div>
            <br>
            <p style="font-size:15px;font-weight:bold">
                4. <?php echo mo2f_lt( 'Custom account name in Google Authenticator App' ); ?></p>
            <div id="mo2f_editGoogleAuthenticatorAccountName1">

                <p style="margin-left:4%"><?php echo mo2f_lt( 'Customize the Account name in the Google Authenticator App' ); ?>
                    .</p>

            </div>
            <br>
        </div>
        <div id="standard_premium_options" style="text-align: center;">
            <p style="font-size:22px;color:darkorange;padding:10px;"><?php echo mo2f_lt( 'Features in the Premium Plan' ); ?></p>

        </div>
        <hr>
        <div>
            <a class="mo2f_view_customizations_prem" onclick="show_customizations_prem()">
                

                <p class="mo2f_heading_style"><?php echo mo2f_lt( 'Customizations' ); ?></p>
            </a>
        </div>


        <div id="customizations_prem" style="margin-left: 5%;">

            <p style="font-size:15px;font-weight:bold">1. <?php echo mo2f_lt( 'Login Screen Options' ); ?></p>
            <div>
                <ul style="margin-left:4%" class="mo2f_ol">
                    <li><?php echo mo2f_lt( 'Login with Wordpress username/password and 2nd Factor' ); ?> <a
                                 data-toggle="collapse" id="showLoginwith2ndFactoronlyPremium" href="#Loginwith2ndFactoronlyPremium"
                                aria-expanded="false">[ <?php echo mo2f_lt( 'See Preview' ); ?>
                            ]</a>
                        <div class="mo2f_collapse" id="Loginwith2ndFactoronlyPremium" style="height:300px;">
                            <center><br>
                                <img style="height:300px;"
                                     src="https://login.xecurify.com/moas/images/help/login-help-1.png">
                            </center>

                        </div>
                        <br></li>
                    <li><?php echo mo2f_lt( 'Login with Wordpress username and 2nd Factor only' ); ?> <a
                                 data-toggle="collapse" id="showLoginWithUsernameOnlyPremium" href="#LoginWithUsernameOnlyPremium"
                                aria-expanded="false">[ <?php echo mo2f_lt( 'See Preview' ); ?>
                            ]</a>
                        <br>
                        <div class="mo2f_collapse" id="LoginWithUsernameOnlyPremium" style="height:300px;">
                            <center><br>
                                <img style="height:300px;"
                                     src="https://login.xecurify.com/moas/images/help/login-help-3.png">
                            </center>
                        </div>
                        <br>
                    </li>
                </ul>


            </div>
            <br>
            <p style="font-size:15px;font-weight:bold">2. <?php echo mo2f_lt( 'Custom Redirect URLs' ); ?></p>
            <p style="margin-left:4%"><?php echo mo2f_lt( 'Enable Custom Relay state URL\'s (based on user roles in Wordpress) to which the users
                will get redirected to, after the 2-factor authentication' ); ?>'.</p>


            <br>
            <p style="font-size:15px;font-weight:bold">3. <?php echo mo2f_lt( 'Custom Security Questions (KBA)' ); ?></p>
            <div id="mo2f_customKBAQuestions1">
                <p style="margin-left:4%"><?php echo mo2f_lt( 'Add up to 16 Custom Security Questions for Knowledge based authentication (KBA).
                    You also have the option to select how many standard and custom questions should be shown to the
                    users' ); ?>.</p>

            </div>
            <br>
            <p style="font-size:15px;font-weight:bold">
                4. <?php echo mo2f_lt( 'Custom account name in Google Authenticator App' ); ?></p>
            <div id="mo2f_editGoogleAuthenticatorAccountName1">

                <p style="margin-left:4%"><?php echo mo2f_lt( 'Customize the Account name in the Google Authenticator App' ); ?>
                    .</p>

            </div>
            <br>
        </div>
        <div>
            <a class="mo2f_view_backup_options_prem" onclick="show_backup_options_prem()">
                

                <p class="mo2f_heading_style"><?php echo mo2f_lt( 'Backup Options' ); ?></p>
            </a>

        </div>
        <div id="backup_options_prem" style="margin-left: 5%;">

            <div class="mo2f_advanced_options_note"><p style="padding:5px;">
                    <i><?php echo mo2f_lt( 'Use these backup options to login to your site in case your 
                                phone is lost / not accessible or if you are not able to login using your primary 
                                authentication method.' ); ?></i></p></div>

            <ol class="mo2f_ol">
                <li><?php echo mo2f_lt( 'KBA (Security Questions)' ); ?></li>
                <li><?php echo mo2f_lt( 'OTP Over Email' ); ?></li>
                <li><?php echo mo2f_lt( 'Backup Codes' ); ?></li>
            </ol>

        </div>


        <div>
            <a class="mo2f_view_inline_registration_options" onclick="show_inline_registration_options()">
                
                <p class="mo2f_heading_style"><?php echo mo2f_lt( 'Inline Registration Options' ); ?></p>
            </a>
        </div>


        <div id="inline_registration_options" style="margin-left: 5%;">

            <div class="mo2f_advanced_options_note"><p style="padding:5px;">
                    <i><?php echo mo2f_lt( 'Inline Registration is the registration process the users go through the first time they
                                setup 2FA.' ); ?><br>
                        <?php echo mo2f_lt( 'If Inline Registration is enabled by the admin for the users, the next time
                                the users login to the website, they will be prompted to set up the 2FA of their choice by
                                creating an account with miniOrange.' ); ?>


                    </i></p></div>


            <p style="font-size:15px;font-weight:bold"><?php echo mo2f_lt( 'Features' ) ?>:</p>
            <ol style="margin-left: 5%" class="mo2f_ol">
                <li><?php echo mo2f_lt( 'Invoke 2FA Registration & Setup for Users during first-time login (Inline Registration)' ); ?>
                </li>

                <li><?php echo mo2f_lt( 'Verify Email address of User during Inline Registration' ); ?></li>
                <li><?php echo mo2f_lt( 'Remove Knowledge Based Authentication(KBA) setup during inline registration' ); ?></li>
                <li><?php echo mo2f_lt( 'Enable 2FA for specific Roles' ); ?></li>
                <li><?php echo mo2f_lt( 'Enable specific 2FA methods to Users during Inline Registration' ); ?>:
                    <ul style="padding-top:10px;">
                        <li style="margin-left: 5%;">
                            1. <?php echo mo2f_lt( 'Show specific 2FA methods to All Users' ); ?></li>
                        <li style="margin-left: 5%;">
                            2. <?php echo mo2f_lt( 'Show specific 2FA methods to Users based on their roles' ); ?></li>
                    </ul>
                </li>
            </ol>
        </div>


        <div>
            <a class="mo2f_view_login_options" onclick="show_login_options()">                
                <p class="mo2f_heading_style"><?php echo mo2f_lt( 'User Login Options' ); ?></p>
            </a>
        </div>

        <div id="login_options" style="margin-left: 5%;">

            <div class="mo2f_advanced_options_note"><p style="padding:5px;">
                    <i><?php echo mo2f_lt( 'These are the options customizable for your users.' ); ?>


                    </i></p></div>

            <ol style="margin-left: 5%" class="mo2f_ol">
                <li><?php echo mo2f_lt( 'Enable 2FA during login for specific users on your site' ); ?>.</li>

                <li><?php echo mo2f_lt( 'Enable login from external apps that support XML-RPC. (eg. Wordpress App)' ); ?>
                    <br>
                    <div class="mo2f_advanced_options_note"><p style="padding:5px;">
                            <i><?php echo mo2f_lt( 'Use the Password generated in the 2FA plugin to login to your Wordpress Site from
                                        any application that supports XML-RPC.' ); ?>


                            </i></p></div>


                <li><?php echo mo2f_lt( 'Enable KBA (Security Questions) as 2FA for Users logging in to the site from mobile
                phones.' ); ?>
                </li>


            </ol>
            <br>
        </div>
    </div>
    <?php
}