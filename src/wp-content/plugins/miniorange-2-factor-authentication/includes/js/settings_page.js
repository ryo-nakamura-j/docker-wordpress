jQuery(document).ready(function () {
	
    $ = jQuery;

	//show and hide instructions
    $("#auth_help").click(function () {
        $("#auth_troubleshoot").toggle();
    });
	$("#conn_help").click(function () {
        $("#conn_troubleshoot").toggle();
    });
	
	$("#conn_help_user_mapping").click(function () {
        $("#conn_user_mapping_troubleshoot").toggle();
    });
	
	//show and hide attribute mapping instructions
    $("#toggle_am_content").click(function () {
        $("#show_am_content").toggle();
    });

	 //Instructions
    $("#mo_wpns_help_curl_title").click(function () {
        $("#mo_wpns_help_curl_desc").slideToggle(600);
    });
	
    $("#mo_wpns_issue_in_scanning_QR").click(function () {
        $("#mo_wpns_issue_in_scanning_QR_solution").slideToggle(600);
    });
	
    $("#mo_wpns_help_get_back_to_account").click(function () {
        $("#mo_wpns_help_get_back_to_account_solution").slideToggle(600);
    });
	
    $("#mo_wpns_help_multisite").click(function () {
        $("#mo_wpns_help_multisite_solution").slideToggle(600);
    });
	
    $("#mo_wpns_help_adv_user_ver_title").click(function () {
        $("#mo_wpns_help_adv_user_ver_desc").slideToggle(600);
    });
    $("#mo_wpns_help_forgot_password").click(function () {
        $("#mo_wpns_help_forgot_password_solution").slideToggle(600);
    });
    $("#mo_wpns_help_MFA_propmted").click(function () {
        $("#mo_wpns_help_MFA_propmted_solution").slideToggle(600);
    });
	
    $("#mo_wpns_help_redirect_back").click(function () {
        $("#mo_wpns_help_redirect_back_solution").slideToggle(600);
    });
     $("#mo_wpns_help_alternet_login").click(function () {
        $("#mo_wpns_help_alternet_login_solution").slideToggle(600);
    });
     $("#mo_wpns_help_lost_ability").click(function () {
        $("#mo_wpns_help_lost_ability_solution").slideToggle(600);
    });
     $("#mo_wpns_help_translate").click(function () {
        $("#mo_wpns_help_translate_solution").slideToggle(600);
    });
     $("#mo_wpns_help_particular_use_role").click(function () {
        $("#mo_wpns_help_particular_use_role_solution").slideToggle(600);
    });
     $("#mo_wpns_help_enforce_MFA").click(function () {
        $("#mo_wpns_help_enforce_MFA_solution").slideToggle(600);
    });
     $("#mo_wpns_help_reset_MFA").click(function () {
        $("#mo_wpns_help_reset_MFA_solution").slideToggle(600);
    });
  
    
    $(".backup_codes_dismiss").click(function(){
        ajaxCall("dismisscodeswarning",".backupcodes-notice",true);
    });

    $(".smtpsetup").click(function(){
         ajaxCall("dissmissSMTP",".smtpsetup-notice",true);
    });

    $(".whitelist_self").click(function(){
        ajaxCall("whitelistself",".whitelistself-notice",true);
    });

    $(".sms_low_dismiss").click(function(){
        ajaxCall("dismissSms",".low_sms-notice",true);
    });

    $(".sms_low_dismiss_always").click(function(){
        ajaxCall("dismissSms_always",".low_sms-notice",true);
    });

    $(".email_low_dismiss").click(function(){
        ajaxCall("dismissEmail",".low_email-notice",true);
    });
    
    $(".email_low_dismiss_always").click(function(){
        ajaxCall("dismissEmail_always",".low_email-notice",true);
    });

    $(".new_plugin_dismiss").click(function(){
        ajaxCall("dismissplugin",".plugin_warning_hide-notice",true);
    });
    
   
    $(".dismiss_brute_force_notice").click(function(){
        ajaxCall("dismissbruteforce",".plugin_warning_hide-notice",true);
    });

    $(".dismiss_google_recaptcha_notice").click(function(){
        ajaxCall("dismissrecaptcha",".plugin_warning_hide-notice",true);
    });
    $(".dismiss_firewall_notice").click(function(){
        ajaxCall("dismissfirewall",".plugin_warning_hide-notice",true);
    });
    
     $(".plugin_warning_never_show_again").click(function(){
        ajaxCall("plugin_warning_never_show_again",".plugin_warning_hide-notice",true);
    });

    $(".mo2f_banner_never_show_again").click(function(){
        ajaxCall("mo2f_banner_never_show_again",".mo2f_offer_main_div",true);
    });

    $(".wpns_premium_option :input").attr("disabled",true);

    $("#setuptwofa_redirect").click(function(e){
        localStorage.setItem("last_tab", "setup_2fa");
    });
});


function ajaxCall(option,element,hide)
{
    jQuery.ajax({
            url: "",
            type: "GET",
            data: "option="+option,
            crossDomain: !0,
            dataType: "json",
            contentType: "application/json; charset=utf-8",
            success: function(o) {
                if (hide!=undefined)
                    jQuery(element).slideUp();
            },
            error: function(o, e, n) {}
        });
}

function success_msg(msg){ 
jQuery('#wpns_nav_message').empty();
jQuery('#wpns_nav_message').append("<div id='notice_div' class='overlay_success'><div class='popup_text'>&nbsp&nbsp"+msg+"</div></div>");
window.onload =  nav_popup();
}

function error_msg(msg){
jQuery('#wpns_nav_message').empty();
jQuery('#wpns_nav_message').append("<div id='notice_div' class='overlay_error'><div class='popup_text'>&nbsp&nbsp"+msg+"</div></div>");
window.onload =  nav_popup();
}

function nav_popup() {
  if(document.getElementById("notice_div") !== null){
      document.getElementById("notice_div").style.width = "40%";
      setTimeout(function(){ jQuery('#notice_div').fadeOut('slow'); }, 3000);
  }
}

// OFFER TIMER
var countDownDate = new Date("Jan 15, 2022 23:59:59").getTime();

var x = setInterval(function() {

  var now = new Date().getTime();
  var distance = countDownDate - now;

  var days = Math.floor(distance / (1000 * 60 * 60 * 24));
  var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
  var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
  var seconds = Math.floor((distance % (1000 * 60)) / 1000);
 if(document.getElementById("mo2f_offer_timer")!= null){
  document.getElementById("mo2f_offer_timer").innerHTML = days + "d " + hours + "h "
  + minutes + "m " + seconds + "s ";
}

  if (distance < 0) {
    clearInterval(x);
    if(document.getElementById("mo2f_offer_timer")!= null){
        document.getElementById("mo2f_offer_timer").innerHTML = "EXPIRED";
    }
  }
}, 1000);
// -----OFFER TIMER