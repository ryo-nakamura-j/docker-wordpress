mo2f_has_dm_class = jQuery('.dm_signin_form');
if(mo2f_has_dm_class.length){
 jQuery('form[name="login_1_form"]').removeAttr('action');
 jQuery('form[name="login_1_form"]').removeAttr('method');
 jQuery('form[name="login_1_form"]').attr('data-ajax','2');
 jQuery('form[name="login_1_form"]').prepend( "<div id='mo2f_msg'></div>" );
 jQuery('input[name="save"]').removeAttr('onclick');

  jQuery('#ncore_username0').keypress(function (e) {
    if (e.which == 13) {//Enter key pressed   
    e.preventDefault();
       mo2f_dmajax();
    }
  });
  jQuery('#ncore_password0').keypress(function (e) {
    if (e.which == 13) {//Enter key pressed   
    e.preventDefault();
       mo2f_dmajax();
    }
  });

  jQuery('input[name="save"]' ).click(function(e){
      e.preventDefault();
      mo2f_dmajax();
  });

  function mo2f_dmajax(){
     jQuery('#ncore_ajax_wait').attr('class','ncore_waiting');
      if(typeof jQuery('#miniorange_rba_attribures').val() != 'undefined'){
      jQuery('#miniorange_rba_attribures').val(JSON.stringify(rbaAttributes.attributes));
      var rba = jQuery('#miniorange_rba_attribures').val();
    }
    else
      var rba = "";
    var data = {
      'action'            : 'mo2f_ajax',
      'mo2f_ajax_option'  : 'mo2f_ajax_login',
      'username'          : jQuery('#ncore_username0').val(),
      'password'          : jQuery('#ncore_password0').val(),
      'mo_softtoken'      : jQuery('#mo2f_2fa_code').val(),
      'nonce'             : jQuery("input[name=miniorange_login_nonce]").val(),
      'miniorange_rba_attribures' : rba,
      };
      jQuery.post(my_ajax_object.ajax_url, data, function(response) {
        if ( typeof response.data === "undefined") {
                  jQuery("html").html(response);
              }
              else if ( response.data.notice ) {
          jQuery("#mo2f_msg").append( response.data.notice ).fadeIn();
          jQuery('#ncore_ajax_wait').removeAttr('class');
        }
              else if(response.data.reload)
                location.reload( true );
              else           
                  location.href = response.data.redirect;  
    });
  }
}