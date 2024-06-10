<?php
?>

<div>

<div>
    <h2>Custom Login Forms</h2>
    <p>We support most of the login forms present on the wordpress. And our plugin is tested with almost all the forms like Woocommerce, Ultimate Member, Restrict Content Pro and so on.</p>
</div>
<div>
    <div>
        <table class="customloginform" style="width: 95%">
            <tr>
                <th style="width: 65%">
                    Custom Login form
                </th>
                <th style="width: 22%">
                    Show 2FA prompt on Custom login

                </th>
                <th style="width: 13%">
                    Documents
                </th>
            </tr>
            <tr>
                <td>
                    <?php echo '<img style="width:30px; height:30px;display: inline;" src="'.esc_url(dirname(plugin_dir_url(dirname(__FILE__)))).'/includes/images/woocommerce.png">';?><h3 style="margin-left: 15px; font-size: large; display: inline; float: inherit; padding-right: 50px;">Woocommerce</h3>
                </td>
                <td style="align-items: right;">
                    <form id="woocommerce_login_prompt_form" method="post">
                        <div align="center">
                            <input  type="checkbox" name="woocommerce_login_prompt"  onchange="document.getElementById('woocommerce_login_prompt_form').submit();" <?php if(get_site_option('mo2f_woocommerce_login_prompt')){?> checked <?php } ?> <?php if(!MoWpnsUtility::get_mo2f_db_option('mo2f_enable_2fa_prompt_on_login_page', 'site_option')){?> checked <?php } ?>/>
                        </div>
                        <input type="hidden" name="option" value="woocommerce_disable_login_prompt">

                    </form>
                </td>
                <td>
                    <div style="text-align: center;">
                        <a href='<?php echo esc_url($two_factor_premium_doc['Woocommerce']);?>' target="blank"><span class="dashicons dashicons-text-page mo2f_doc_icon_style mo2f-custom-guide"></span></a>
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <?php echo '<img style="width:30px; height:30px;display: inline;" src="'.esc_url(dirname(plugin_dir_url(dirname(__FILE__)))).'/includes/images/ultimate_member.png">';?><h3 style="margin-left: 15px; font-size: large; display: inline; float: inherit;">Ultimate Member</h3>
                </td>
                <td style="text-align: center;">
                    <input type="checkbox" name=""  checked>
                </td>
                <td>
                    <div style="text-align: center;">
                        <a href='<?php echo $two_factor_premium_doc['Ultimate Member'];?>' target="blank"><span class="dashicons dashicons-text-page mo2f_doc_icon_style mo2f-custom-guide" ></span></a>
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <?php echo '<img style="width:30px; height:30px;display: inline;" src="'.esc_url(dirname(plugin_dir_url(dirname(__FILE__)))).'/includes/images/restrict_content_pro.png">';?><h3 style="margin-left: 15px; font-size: large; display: inline; float: inherit;">Restrict Content Pro</h3>
                </td>
                <td style="text-align: center;">
                    <input type="checkbox" name="" checked>
                </td>
                <td>
                    <div style="text-align: center;">
                        <a href='<?php echo $two_factor_premium_doc['Restrict Content Pro'];?>' target="blank"><span class="dashicons dashicons-text-page mo2f_doc_icon_style mo2f-custom-guide" ></span></a>
                    </div>
                </td>
            </tr>
            <tr>
                <td >
                    <?php echo '<img style="width:30px; height:30px;display: inline;" src="'.esc_url(dirname(plugin_dir_url(dirname(__FILE__)))).'/includes/images/theme_my_login.png">';?><h3 style="margin-left: 15px; font-size: large; display: inline; float: inherit;">Theme My Login</h3>
                </td>
                <td style="text-align: center;">
                    <input type="checkbox" name="" checked>
                </td>
                <td>
                    <div style="text-align: center;">
                        <a href='<?php echo $two_factor_premium_doc['Theme My Login'];?>' target="blank"><span class="dashicons dashicons-text-page mo2f_doc_icon_style mo2f-custom-guide" ></span></a>
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <?php echo '<img style="width:30px; height:30px;display: inline;" src="'.esc_url(dirname(plugin_dir_url(dirname(__FILE__)))).'/includes/images/user_registration.png">';?><h3 style="margin-left: 15px; font-size: large; display: inline; float: inherit;">User Registration</h3>
                </td>
                <td style="text-align: center;">
                    <input type="checkbox" name="" checked>
                </td>
                <td>
                    <div style="text-align: center;">
                        <a href='<?php echo $two_factor_premium_doc['User Registration'];?>' target="blank"><span class="dashicons dashicons-text-page mo2f_doc_icon_style mo2f-custom-guide" ></span></a>
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <?php echo '<img style="width:30px; height:30px;display: inline;" src="'.esc_url(dirname(plugin_dir_url(dirname(__FILE__)))).'/includes/images/Custom_Login_Page_Customizer_LoginPress.png">';?><h3 style="margin-left: 15px; font-size: large; display: inline; float: inherit;">LoginPress | Custom Login Page Customizer</h3>
                </td>
                <td style="text-align: center;">
                    <input type="checkbox" name="" checked>
                </td>
                <td>
                    <div style="text-align: center;">
                        <a href='<?php echo $two_factor_premium_doc['LoginPress'];?>' target="blank"><span class="dashicons dashicons-text-page mo2f_doc_icon_style mo2f-custom-guide" ></span></a>
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <?php echo '<img style="width:30px; height:30px;display: inline;float: left;" src="'.esc_url(dirname(plugin_dir_url(dirname(__FILE__)))).'/includes/images/Admin_Custom_Login.png">';?><h3 style="margin-left: 15px; font-size: large; display: inline; float: inherit;">Admin Custom Login</h3>
                </td>
                <td style="text-align: center;">
                    <input type="checkbox" name="" checked>
                </td>
                <td>
                    <div style="text-align: center;">
                        <a href='<?php echo $two_factor_premium_doc['Admin Custom Login'];?>' target="blank"><span class="dashicons dashicons-text-page mo2f_doc_icon_style mo2f-custom-guide" ></span></a>
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <?php echo '<img style="width:30px; height:30px;display: inline;float: left;" src="'.esc_url(dirname(plugin_dir_url(dirname(__FILE__)))).'/includes/images/RegistrationMagic_Custom_Registration_Forms_and_User_Login.png">';?><h3 style="margin-left: 15px; font-size: large; display: inline; float: inherit;">RegistrationMagic – Custom Registration Forms and User Login</h3>
                </td>
                <td style="text-align: center; ">
                    <input type="checkbox" name="" checked>
                </td>
                <td>
                <div style="text-align: center;">
                        <a href='<?php echo $two_factor_premium_doc['RegistrationMagic'];?>' target="blank"><span class="dashicons dashicons-text-page mo2f_doc_icon_style mo2f-custom-guide" ></span></a>
                      </div>
                </td>
            </tr>

             <tr>
                <td>
                    <?php echo '<img style="width:30px; height:30px;display: inline;float: left;" src="'.dirname(plugin_dir_url(dirname(__FILE__))).'/includes/images/buddypress.png">';?><h3 style="margin-left: 15px; font-size: large; display: inline; float: inherit;">BuddyPress</h3>
                </td>
                <td style="text-align: center;">
                    <input type="checkbox" name="" checked>
                </td>
                <td>
                    <div style="text-align: center;">
                        <a href='<?php echo $two_factor_premium_doc['BuddyPress'];?>' target="blank"><span class="dashicons dashicons-text-page mo2f_doc_icon_style mo2f-custom-guide" ></span></a>
                    </div>  
                </td>
            </tr>

             <tr>
                <td>
                    <?php echo '<img style="width:30px; height:30px;display: inline;float: left;" src="'.dirname(plugin_dir_url(dirname(__FILE__))).'/includes/images/profile-builder.png">';?><h3 style="margin-left: 15px; font-size: large; display: inline; float: inherit;">Profile Builder</h3>
                </td>
                <td style="text-align: center;">
                    <input type="checkbox" name="" checked>
                </td>
                <td>
                    <div style="text-align: center;">
                        <a href='<?php echo $two_factor_premium_doc['Profile Builder'];?>' target="blank"><span class="dashicons dashicons-text-page mo2f_doc_icon_style mo2f-custom-guide" ></span></a>
                    </div>  
                </td>
            </tr>

             <tr>
                <td>
                    <?php echo '<img style="width:30px; height:30px;display: inline;float: left;" src="'.dirname(plugin_dir_url(dirname(__FILE__))).'/includes/images/elementor-pro.png">';?><h3 style="margin-left: 15px; font-size: large; display: inline; float: inherit;">Elementor Pro</h3>
                </td>
                <td style="text-align: center;">
                    <input type="checkbox" name="" checked>
                </td>
                <td>
                    <div style="text-align: center;">
                        <a href='<?php echo $two_factor_premium_doc['Elementor Pro'];?>' target="blank"><span class="dashicons dashicons-text-page mo2f_doc_icon_style mo2f-custom-guide" ></span></a>
                    </div>
                </td>
            </tr>

             <tr>
                <td>
                    <?php echo '<img style="width:30px; height:30px;display: inline;float: left;" src="'.dirname(plugin_dir_url(dirname(__FILE__))).'/includes/images/login-with-ajax.png">';?><h3 style="margin-left: 15px; font-size: large; display: inline; float: inherit;">Login with Ajax</h3>
                </td>
                <td style="text-align: center;">
                    <input type="checkbox" name="" checked>
                </td>
                <td>
                    <div style="text-align: center;">
                        <a href='<?php echo $two_factor_premium_doc['Login with Ajax'];?>' target="blank"><span class="dashicons dashicons-text-page mo2f_doc_icon_style mo2f-custom-guide" ></span></a>
                    </div>
                </td>
            </tr>

        </table>
        <div style="text-align: center">
        <b style="color: red; " >**If you want to enable/disable 2FA prompt on other Custom login pages please Contact us.</b>
        <br>
        <b style="color: red;" >**This feature will only work when you enable 2FA prompt on wordpress login page.</li></b>

        <p style="font-size:15px">If there is any custom login form where Two Factor is not initiated for you, please reach out to us by dropping a query in the <b>Support</b> section.</p>
        </div>
    </div>

    <hr>

    <form name="form_custom_form_config" method="post" action="" id="mo2f_custom_form_config">
        <h3> <?php echo 'Custom Registration Forms';?> </h3>
        <?php
        $isRegistered = get_site_option('mo2f_customerkey')? get_site_option('mo2f_customerkey') : 'false';
        if($isRegistered=='false')
        {
            ?><br>
            <div style="padding: 10px;border: red 1px solid">
                <a href="admin.php?page=mo_2fa_account"> Register/Login</a> with miniOrange to Use the Shortcode
            </div>
            <?php
        }
        ?>

        <div style="padding: 20px;border: 1px #DCDCDC solid">
            <h3>Step 1 : Select Authentication Method</h3>
            <hr>
            <table>
                <tbody>
                <tr>
                    <td>
                        <input type="checkbox" name="mo2f_method_phone" id="mo2f_method_phone" value="phone" <?php if(get_site_option('mo2f_custom_auth_type')=='phone' or get_site_option('mo2f_custom_auth_type')=='both') echo 'checked';?>>
                        <label for="mo2f_method_phone"> Verify Phone Number </label>
                    </td>
                    <td>
                        <input type="checkbox" name="mo2f_method_email" id="mo2f_method_email" value="email" <?php if(get_site_option('mo2f_custom_auth_type')=='email' or get_site_option('mo2f_custom_auth_type')=='both') echo 'checked';?>>
                        <label for="mo2f_method_email"> Verify Email Address </label>
                    </td>
                </tr>
                </tbody>
            </table>

            <table>
                <h3>Step 2 : Select Form</h3>
                <tbody>
                <tr>
                    <td>
                        <select id="regFormList" name="regFormList">
                            <?php
                            //$formsArray = array("formName"=>array("Woo Commerce","BB Press"),"formSelector"=>array(".woocommerce-form-register",".bbp-login-form"),"emailSelector"=>array("#reg_email","#user_email"),"submitSelector"=>array(".user-submit",".woocommerce-form-register__submit"));

                            $defaultWordpress = array(
                                "formName"=>"Wordpress Registration",
                                "formSelector"=>"#wordpress-register",
                                "emailSelector"=>"#wordpress-register",
                                "submitSelector"=>"#wordpress-register"
                            );

                            $wcForm = array("formName"=>"Woo Commerce",
                                "formSelector"=>".woocommerce-form-register",
                                "emailSelector"=>"#reg_email",
                                "submitSelector"=>".woocommerce-form-register__submit");

                            $bbForm = array("formName"=>"BB Press",
                                "formSelector"=>".bbp-login-form",
                                "emailSelector"=>"#user_email",
                                "submitSelector"=>".user-submit");

                            $loginPressForm = array(
                                "formName"=>"Login Press",
                                "formSelector"=>"#registerform",
                                "emailSelector"=>"#user_email",
                                "submitSelector"=>"#wp-submit"
                            );

                            $userRegForm = array(
                                "formName"=>"User Registration",
                                "formSelector"=>".user-registration-form",
                                "emailSelector"=>"#username",
                                "submitSelector"=>".user-registration-Button"
                            );

                            $customForm = array(
                                "formName"=>"Custom Form",
                                "formSelector"=>"",
                                "emailSelector"=>"",
                                "submitSelector"=>""
                            );

                            $formsArray = array("forms"=>array($defaultWordpress,$wcForm,$bbForm,$loginPressForm,$userRegForm,$customForm));

                            for ($i= 0 ; $i < sizeof($formsArray["forms"]) ; $i++)
                            {
                                $formName = $formsArray["forms"];
                                echo '<option value='.strtolower(str_replace(" ","", esc_html($formName[$i]["formName"]))).'>'.esc_html($formName[$i]["formName"]).'</option>';
                                ?>
                                <?php
                            }
                            ?>
                        </select>
                    </td>
                </tr>
                </tbody>
            </table>
            <div id="selector_div">
            <h4 id="enterMessage" name="enterMessage" style="display: none;padding:8px; color: white; background-color: teal">Enter Selectors for your Form</h4>
            <div id="formDiv">
                <h4>Form Selector<span style="color: red;font-size: 14px">*</span></h4>
                <input type="text" value="<?php echo esc_html(get_site_option('mo2f_custom_form_name'));?>" style="width: 100%" name="mo2f_shortcode_form_selector" id="mo2f_shortcode_form_selector" placeholder="Example #form_id" <?php if($is_any_of_woo_bb) { echo 'disabled';}?> >
            </div>
            <div id="emailDiv">
                <h4>Email Field Selector <span style="color: red;font-size: 14px">*</span></h4>
                <input type="text" style="width: 100%" value="<?php echo esc_html(get_site_option('mo2f_custom_email_selector'));?>" name="mo2f_shortcode_email_selector" id="mo2f_shortcode_email_selector" placeholder="example #email_field_id" <?php if($is_any_of_woo_bb) { echo 'disabled';}?> >
            </div>
            <div id="phoneDiv">
                <h4>Phone Field Selector <span style="color: red;font-size: 14px">*</span></h4>
                <input type="text" style="width: 100%" value="<?php echo esc_html(get_site_option('mo2f_custom_phone_selector'));?>" name="mo2f_shortcode_phone_selector" id="mo2f_shortcode_phone_selector" placeholder="example #phone_field_id" >
            </div>
            <div id="submitDiv">
                <h4>Submit Button Selector <span style="color: red;font-size: 14px">*</span></h4>
                <input type="text" style="width: 100%" value="<?php echo esc_html(get_site_option('mo2f_custom_submit_selector'));?>" name="mo2f_shortcode_submit_selector" id="mo2f_shortcode_submit_selector" placeholder="example #submit_button_id" <?php if($is_any_of_woo_bb) { echo 'disabled';}?> >
                <p style="color:red;">* Required</p>
            </div>
            </div>
            <br>
            <input type="checkbox" id="use_shortcode_config" name="use_shortcode_config" value="yes" <?php if (get_option('enable_form_shortcode'))echo 'checked';?>>
            <label for="use_shortcode_config">Enable Shortcode</label>
            <h4> <?php echo 'Enables/Disables Phone Number and Email Verification for custom Registration Forms where You have added the Shortcode'?></h4>
            <br>
            <input type="button" style="float: right" class="button button-primary" value="Save Settings"
                   id="mo2f_form_config_save"  name= "mo2f_form_config_save">
            <input type="hidden" id="mo2f_nonce_save_form_settings" name="mo2f_nonce_save_form_settings"
                   value="<?php echo esc_html(wp_create_nonce( "mo2f-nonce-save-form-settings" )) ?>"/>
            <br>
        </div>
        <h2> Step 3 : Copy Shortcode </h2>
        <p style="color: red">*Add this on the page where you have your registration form to enable OTP verification for the same.</p>
        <div style="padding: 10px;border: 1px #DCDCDC solid">
            <h4 class="shortcode_form" style="font-family: monospace">[mo2f_enable_register]</h4>
        </div>


    </form>
    <script>
        jQuery(document).ready(function () {


            let formArray = <?php echo json_encode($formName) ;?>

                let $mo = jQuery;
            $mo('#mo2f_shortcode_form_selector').prop('disabled',true)
            $mo('#mo2f_shortcode_submit_selector').prop('disabled',true)
            $mo('#mo2f_shortcode_email_selector').prop('disabled',true)
            let customForm = false;
            is_registered   = '<?php echo esc_html($is_registered); ?>';

            $mo('#phoneDiv').css('display','none')

            $mo("#mo2f_method_phone").change(function() {
                let checked = $mo('#mo2f_method_phone').is(':checked')
                if(checked)
                {
                    $mo('#phoneDiv').css('display','inherit')
                }
                else
                {
                    $mo('#phoneDiv').css('display','none')
                }
            });

            if(!is_registered)
            {
                $mo('#use_shortcode_config').prop('checked',false)
                $mo('#use_shortcode_config').prop('disabled',true)
            }

            $mo('#mo2f_shortcode_form_selector').val(formArray[0]["formSelector"])
            $mo('#mo2f_shortcode_submit_selector').val(formArray[0]["submitSelector"])
            $mo('#mo2f_shortcode_email_selector').val(formArray[0]["emailSelector"])

            $mo("#regFormList").change(function(){

                let index = $mo("#regFormList").prop('selectedIndex')
                if(index<5)
                {
                    $mo('#selector_div').css('display','none')
                }
                else
                {
                    $mo('#mo2f_shortcode_email_selector').prop('disabled',false);
                    $mo('#mo2f_shortcode_form_selector').prop('disabled',false);
                    $mo('#mo2f_shortcode_phone_selector').prop('disabled',false);
                    $mo('#mo2f_shortcode_submit_selector').prop('disabled',false);

                    $mo('#selector_div').css('display','inherit')
                    jQuery("#mo2f_shortcode_form_selector").focus();

                }

                $mo('#mo2f_shortcode_form_selector').val(formArray[index]["formSelector"])
                $mo('#mo2f_shortcode_submit_selector').val(formArray[index]["submitSelector"])
                $mo('#mo2f_shortcode_email_selector').val(formArray[index]["emailSelector"])
                if(index===0)
                {
                    $mo('#mo2f_shortcode_phone_selector').val("#wp-register");
                }
            });

            $mo('#custom_auto').click(function()
            {
                customForm = true;
                $mo('#formDiv').css('display','inherit')
                $mo('#submitDiv').css('display','inherit')
                $mo('#emailDiv').css('display','inherit')
                $mo('#mo2f_shortcode_form_selector').val('<?php echo esc_html(get_site_option('mo2f_custom_form_name'))?>');
                $mo('#mo2f_shortcode_submit_selector').val('<?php echo esc_html(get_site_option('mo2f_custom_submit_selector'));?>');
                $mo('#mo2f_shortcode_email_selector').val('<?php echo esc_html(get_site_option('mo2f_custom_email_selector'));?>');
            });

            $mo('#mo2f_form_config_save').click(function () {
                is_registered   = '<?php echo esc_html($is_registered); ?>';
                if(!is_registered)
                    error_msg("Please Register/Login with miniOrange");
                else
                {

                    let sms,email,authType,enableShortcode
                    enableShortcode = $mo('#use_shortcode_config').is(':checked');
                    sms             = $mo('#mo2f_method_phone').is(':checked');
                    email           = $mo('#mo2f_method_email').is(':checked');
                    email_selector  = $mo('#mo2f_shortcode_email_selector').val();
                    phone_selector  = $mo('#mo2f_shortcode_phone_selector').val();
                    form_selector   = $mo('#mo2f_shortcode_form_selector').val();
                    submit_selector = $mo('#mo2f_shortcode_submit_selector').val();
                    authType        = (email === true && sms === true) ? 'both' : (email === false && sms=== true) ? 'phone' : 'email'
                    error          = "";
                    if(authType === 'both' || authType === 'email')
                        if(email_selector === ''){
                            error = "Add email selector to use OTP Over Email";
                        }
                    if(authType === 'both' || authType === 'phone')
                        if(phone_selector === ''){
                            error = "Add phone selector to use OTP Over SMS";
                        }

                    if(!validate_selector(email_selector))
                        error = "NOTE: Choosing your Selector<br>Element\'s ID Selector looks like #element_id <br> Element\'s name Selector looks like input[name=element_name]";
                    if(error != ""){
                        error_msg(error);
                    }
                    else{
                        let data =  {
                            'action'                        : 'mo_two_factor_ajax',
                            'mo_2f_two_factor_ajax'         : 'mo2f_save_custom_form_settings',
                            'mo2f_nonce_save_form_settings' :  $mo('#mo2f_nonce_save_form_settings').val(),
                            'submit_selector'               :  submit_selector,
                            'form_selector'                 :  form_selector,
                            'email_selector'                :  email_selector,
                            'phone_selector'                :  phone_selector,
                            'authType'                      :  authType,
                            'customForm'                    :  customForm,
                            'enableShortcode'               :  enableShortcode
                        };
                        jQuery.post(ajaxurl, data, function(response) {
                            if(response.saved === false)
                            {
                                error_msg('One or more fields are empty.');
                            }
                            else if(response == "error")
                            {
                                error_msg("Error occured while saving the settings.");
                            }
                            else if(response.saved === true)
                            {
                                success_msg("Selectors Saved Successfully.");
                            }
                        });
                    }
                }
            });
        });

        function validate_selector(selector){
            let is_valid = false
            if(/^#/.test(selector))
                is_valid = true
            if(/^\./.test(selector))
                is_valid = true
            if(/^input\[name=/.test(selector))
                is_valid = true

            return is_valid;
        }



    </script>

</div>

</div>

