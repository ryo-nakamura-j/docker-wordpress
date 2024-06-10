function mo2fa_viewMethod(evt, selected_method) 
{
    var i, tabcontent, tablinks;
    var is_registered = jQuery('input[name=\'is_registered\']').val();
    var trimmed_method = selected_method.replace(/ /g,'');
    jQuery('#method').val(selected_method);
    tabcontent = document.getElementsByClassName("mo2fa_tabcontent");
    for (i = 0; i < tabcontent.length; i++) {
        tabcontent[i].style.display = "none";
    }
    tablinks = document.getElementsByClassName("mo2fa_tablinks");
    for (i = 0; i < tablinks.length; i++) {
        tablinks[i].className = tablinks[i].className.replace(" active", "");
    }
    mo2fa_viewMethod.calledTimes++;
    jQuery("#mo2fa_count").val(mo2fa_viewMethod.calledTimes);   
    document.getElementById(selected_method).style.display = "block";
    evt.currentTarget.className += " active";
    var same_user = jQuery('input[name=\'same_user\']').val();
    var textbox_id = 'textbox-'+trimmed_method;
    var textbox_element = document.getElementById(textbox_id);
    if(selected_method == 'SecurityQuestions')
        document.getElementById("mo2f_kbaquestion_1").focus(); 
    else if(selected_method == 'OTPOverSMS' && is_registered)
        document.getElementById("textbox-OTPOverSMS").focus(); 
    else if(textbox_element !== null && same_user && (is_registered && ((trimmed_method!="EmailVerification" && trimmed_method !='OTPOverEmail')) || (!is_registered && (trimmed_method=="GoogleAuthenticator" || trimmed_method=="AuthyAuthenticator" || trimmed_method=='SecurityQuestions'))))
        document.getElementById(textbox_id).focus();
    var save_button_id = jQuery('#'+'save-'+trimmed_method);
    var form_id = jQuery('#'+'mo2f_verify_form-'+trimmed_method);
    jQuery(form_id).submit(function(e){
        e.preventDefault();
        jQuery(save_button_id).click();
    });
    var MO2F_IS_ONPREM = jQuery('input[name=\'MO2F_IS_ONPREM\']').val();
    var cloud_methods = ["miniOrangeQRCodeAuthentication", "miniOrangeSoftToken","miniOrangePushNotification","OTPOverSMS","miniOrangeAuthenticator"];
    if(MO2F_IS_ONPREM == 0 && !is_registered)
    {
        jQuery('#wpns_nav_message').empty();
        jQuery('#wpns_nav_message').append("<div id='notice_div' class='mo2fa_overlay_error'><div class='mo2fa_popup_text'>&nbsp; &nbsp; <b>Please register with miniOrange for using this method</b> </div></div>");
        window.onload = nav_popup();
        return;
    }
    for(method of cloud_methods)
    {
        if((selected_method==method && !is_registered) || !MO2F_IS_ONPREM)
        {
            jQuery('#wpns_nav_message').empty();
            jQuery('#wpns_nav_message').append("<div id='notice_div' class='mo2fa_overlay_error'><div class='mo2fa_popup_text'>&nbsp; &nbsp; <b>Please register with miniOrange for using this method</b> </div></div>");
            window.onload = nav_popup();
        }
    }
}

jQuery('#mo2f_qrcode').hide();
jQuery('.mo2f_miniAuthApp').click(function(){
    jQuery('#mo2f_qrcode').show();
    jQuery('#mo2fa_display_mo_methods').hide();
    var method = jQuery('input[name="miniOrangeAuthenticator"]:checked').val();
});

jQuery('.mo2f_miniAuthApp').click(function(){
    var method = jQuery('input[name="miniOrangeAuthenticator"]:checked').val();
});

jQuery('#miniOrangeSoftTokenButton').click(function() {
    jQuery('#method').val('miniOrangeSoftToken');
    jQuery("#save-miniOrangeAuthenticator").click(function(e){
        set_mo_methods('miniOrangeSoftToken');
    });
});
jQuery('#miniOrangeQRCodeAuthenticationButton').click(function() {
    jQuery('#method').val('miniOrangeQRCodeAuthentication');
    jQuery("#save-miniOrangeAuthenticator").click(function(e){
        set_mo_methods('miniOrangeQRCodeAuthentication');
    });
});
jQuery('#miniOrangePushNotificationButton').click(function() {
    jQuery('#method').val('miniOrangePushNotification');
    jQuery("#save-miniOrangeAuthenticator").click(function(e){
        set_mo_methods('miniOrangePushNotification');
    });
});


function set_mo_methods(trimmed_method){
    var textbox_id_element = jQuery('#'+'textbox-'+trimmed_method);
    var code = jQuery('#textbox-miniOrangeAuthenticator').val();
    var nonce = jQuery('input[name=\'mo2f-update-mobile-nonce\']').val();
    var transient_id = jQuery('input[name=\'transient_id\']').val();
    var is_registered = jQuery('input[name=\'is_registered\']').val();
    if(!is_registered)
    {
        jQuery('#wpns_nav_message').empty();
        jQuery('#wpns_nav_message').append("<div id='notice_div' class='mo2fa_overlay_error'><div class='mo2fa_popup_text'>&nbsp; &nbsp; <b>Please register with miniOrange for using this  method</b> </div></div>");
        window.onload = nav_popup();
    }
    else
    {
        var data = {
            'action'                    : 'mo_two_factor_ajax',
            'mo_2f_two_factor_ajax'     : 'mo2f_set_miniorange_methods',
            'nonce'                     :  nonce,
            'code'                      :  code,
            'transient_id'              :  transient_id,
        };
        jQuery.post(ajaxurl, data, function(response) {
            if(response['status'] == "SUCCESS")
            {
                $("#mo2f_configuration_status").val(response['status']);                                
            }
            jQuery('#wpns_nav_message').empty();
            jQuery('#wpns_nav_message').append("<div id='notice_div' class='"+(response['status']=="SUCCESS"?"mo2fa_overlay_success":"mo2fa_overlay_error")+"'><div class='mo2fa_popup_text'>&nbsp; &nbsp; <b>"+response['status']+"</b> : "+response['message']+"</div></div>");
            window.onload = nav_popup();
        });
    }
}
function mo2fa_set_ga(code){
    var nonce = jQuery('input[name=\'mo2f-update-mobile-nonce\']').val();
    var transient_id = jQuery('input[name=\'transient_id\']').val();
    var ga_secret = jQuery('input[name=\'ga_secret\']').val();
    var data = {
        'action'                    : 'mo_two_factor_ajax',
        'mo_2f_two_factor_ajax'     : 'mo2f_set_GA',
        'nonce'                     :  nonce,
        'code'                      :  code,
        'transient_id'              :  transient_id,
        'ga_secret'                 : ga_secret
    };
    jQuery.post(ajaxurl, data, function(response) {
        jQuery('#wpns_nav_message').empty();
        if(response == "SUCCESS"){
            jQuery("#mo2f_configuration_status").val(response);  
            jQuery('#wpns_nav_message').append("<div id='notice_div' class='mo2fa_overlay_success'><div class='mo2fa_popup_text'>&nbsp; &nbsp; <b>SUCCESS</b> : Entered Passcode is correct. Click on Update Profile.</div></div>");
        }else if(response == "UserIdNotFound"){
                jQuery('#wpns_nav_message').append("<div id='notice_div' class='mo2fa_overlay_error'><div class='mo2fa_popup_text'>&nbsp; &nbsp; Error occured validating the user. </div></div>");
        }else{
            jQuery('#wpns_nav_message').append("<div id='notice_div' class='mo2fa_overlay_error'><div class='mo2fa_popup_text'>&nbsp; &nbsp; <b>ERROR</b> : Entered Passcode is incorrect. </div></div>");
        }
        window.onload = nav_popup();
    });
}
var MO2F_IS_ONPREM = jQuery('input[name=\'MO2F_IS_ONPREM\']').val();
if(MO2F_IS_ONPREM == 1){
    jQuery(document).ready(function() {
        jQuery('.mo2f_gauth').qrcode({
            'render': 'image',
            size: 175,
            'text': jQuery('.mo2f_gauth').data('qrcode')
        });
    });
}

jQuery("#save-GoogleAuthenticator").click(function(e){
    var code = jQuery('#textbox-GoogleAuthenticator').val();
    mo2fa_set_ga(code);
});
jQuery("#save-AuthyAuthenticator").click(function(e){
    var code = jQuery('#textbox-AuthyAuthenticator').val();
    mo2fa_set_ga(code);
});
jQuery("#save-OTPOverSMS").click(function(e){
    var nonce = jQuery('input[name=\'mo2f-update-mobile-nonce\']').val();
    var transient_id = jQuery('input[name=\'transient_id\']').val();
    var phone = jQuery("#textbox-OTPOverSMS").val();
    var is_registered = jQuery('input[name=\'is_registered\']').val();
    if(!is_registered){
        jQuery('#wpns_nav_message').empty();
        jQuery('#wpns_nav_message').append("<div id='notice_div' class='mo2fa_overlay_error'><div class='mo2fa_popup_text'>&nbsp; &nbsp; <b>Please register with miniOrange for using this  method</b> </div></div>");
        window.onload = nav_popup();
    }
    else{
        var data = {
            'action'                    : 'mo_two_factor_ajax',
            'mo_2f_two_factor_ajax'     : 'mo2f_set_otp_over_sms',
            'nonce'                     :  nonce,
            'transient_id'              :  transient_id,
            'phone'                     :  phone
        };
        jQuery.post(ajaxurl, data, function(response) {
            jQuery('#wpns_nav_message').empty();
            if(response == "UserIdNotFound"){
                jQuery('#wpns_nav_message').append("<div id='notice_div' class='mo2fa_overlay_error'><div class='mo2fa_popup_text'>&nbsp; &nbsp; Error occured validating the user. </div></div>");
            }else if(response != "ERROR"){
                jQuery('#wpns_nav_message').append("<div id='notice_div' class='mo2fa_overlay_success'><div class='mo2fa_popup_text'>&nbsp; &nbsp; Phone no. has been saved. Click on Update Profile.</div></div>");
            }else{
                jQuery('#wpns_nav_message').append("<div id='notice_div' class='mo2fa_overlay_error'><div class='mo2fa_popup_text'>&nbsp; &nbsp; There was an error saving your phone no. </div></div>");
            }
            window.onload = nav_popup();
        });
    }
})
mo2fa_viewMethod.calledTimes = 0;
document.getElementById("defaultOpen").click();
jQuery("#textbox-OTPOverSMS").intlTelInput();