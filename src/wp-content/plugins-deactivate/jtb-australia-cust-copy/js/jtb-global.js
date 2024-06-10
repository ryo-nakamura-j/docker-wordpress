
// Google form AJAX
jQuery( document ).ready(function() {





  jQuery('#subscribeform').submit(function(e) {
      e.preventDefault();
      e.stopPropagation();
    //  update_name_jtb2();
      //validate 
      if((jQuery('#1493792574').val()=="")||(jQuery('#1493792574').val()==null)||(jQuery('#1493792574').val()==undefined)||(jQuery('#1493792574').val()==false)||
        (jQuery('#entry_959658973').val()=="")||(jQuery('#entry_959658973').val()==null)||(jQuery('#entry_959658973').val()==undefined)||(jQuery('#entry_959658973').val()==false)||
        (jQuery('#entry_1306134107').val()=="")||(jQuery('#entry_1306134107').val()==null)||(jQuery('#entry_1306134107').val()==undefined)||(jQuery('#entry_1306134107').val()==false) ){
              console.log('please fill in the form');
                jQuery("#subscribemessage").html('<p class="red-text">Please fill in the form</p>');
                return false;die;exit;
      }

      jQuery.ajax({ //old
         // url: "https://docs.google.com/a/jtbap.com/forms/d/1PUqUTddB5SS3TMr2kbjhZok0FQfTkJdvIdATV2sTIcQ/formResponse",
          url: "https://docs.google.com/forms/d/1SRMQbdLim8-OPLR9LHOWfXZYtmv-Z0ms8Xdd0XQzf1k/formResponse",
          // url: "https://docs.google.com/forms/d/1SRMQbdLim8-OPLR9LHOWfXZYtmv-Z0ms8Xdd0XQzf1k/formResponse",
          //https://docs.google.com/forms/d/15ycGKEe2XkRPqJtATUJxKlCe_V0tOuiapksBl-OGs9o/edit
          //https://docs.google.com/forms/d/e/1FAIpQLSd2qhFSy6EZ3G8CiD3j6zs4IAOQczQsadk1pg1XitrtqC5m2A/viewform
          data: jQuery(this).serialize(),
          type: "POST",
          dataType: "xml",
          success: function(data) {
              console.log('Submission successful');
              jQuery("#subscribeform").toggle();
              jQuery("#subscribemessage").html('<h4 class="green-text"><i class="fa fa-envelope" aria-hidden="true"></i> Thank you for subscribing with JTB!</h4>');
          },
          error: function(xhr, status, error) {
            if (error.length>0){
              console.log('Submission failed: ' + error);
                jQuery("#subscribemessage").html('Submission failed: ' + error+'<p>Try refresh the page, or contact us using the contact form</p>');
            }else{
                console.log('Submission successful');
                jQuery("#subscribeform").toggle();
                jQuery("#subscribemessage").html('<h4 class="green-text"><i class="material-icons">done_outline</i> You have subscribed to our newsletter</h4><p>Thank you</p>');
            }
          }
      });
      return false;
  });





$(".close").click(function() {
  $(".modal").hide();
  return true;

});
 
$(".modal").click(function(event) {
 if( (event.target.id=="myModal-a") || (event.target.id=="myModal-b") || (event.target.id=="myModal-c") || (event.target.id=="myModal-d") || (event.target.id=="myModal-dt") || (event.target.id=="myModal-hyperdia") ){
  $(".modal").hide();
  return true;
 }
});




    jQuery(".tabsanchor111").click(function() {
        jQuery("#tabbutton1").click();
        scrollToAnchor('tabsanchor');
    });

    jQuery(".tabsanchor222").click(function() {
        jQuery("#tabbutton2").click();
        scrollToAnchor('tabsanchor');
    });

    jQuery(".tour23button").click(function() {
        jQuery("#tabbutton2").click();
        scrollToAnchor('tabsanchor');
    });
	
	 jQuery(".tour24button").click(function() {
        jQuery("#tabbutton1").click();
        scrollToAnchor('tabsanchor');
    });
	
    jQuery(".tabsanchor333").click(function() {
        jQuery("#tabbutton3").click();
        scrollToAnchor('tabsanchor');
    });

    jQuery(".tabsanchor444").click(function() {
        jQuery("#tabbutton4").click();
        scrollToAnchor('tabsanchor');
    });

    jQuery(".tabsanchor555").click(function() {
        jQuery("#tabbutton5").click();
        scrollToAnchor('tabsanchor');
    });

    jQuery(".tabsanchor666").click(function() {
        jQuery("#tabbutton6").click();
        scrollToAnchor('tabsanchor');
    });

    jQuery(".tabsanchor777").click(function() {
        jQuery("#tabbutton7").click();
        scrollToAnchor('tabsanchor');
    });

  var url2 =  jQuery(location).attr('href');
  var hash = url2.substring(url2.indexOf("#")+1);
  if (hash=='tab2link'){
    jQuery("#tabbutton2").click();
    scrollToAnchor('tabsanchor');
  }
  if (hash=='tab3link'){
    jQuery("#tabbutton3").click();
    scrollToAnchor('tabsanchor');
  }
    if (hash=='tab4link'){
    jQuery("#tabbutton4").click();
    scrollToAnchor('tabsanchor');
  }
    if (hash=='tab1link'){
    jQuery("#tabbutton1").click();
    scrollToAnchor('tabsanchor');
  }
    if (hash=='tab5link'){
    jQuery("#tabbutton5").click();
    scrollToAnchor('tabsanchor');
  }
      if (hash=='tab6link'){
    jQuery("#tabbutton6").click();
    scrollToAnchor('tabsanchor');
  }
      if (hash=='tab7link'){
    jQuery("#tabbutton7").click();
    scrollToAnchor('tabsanchor');
  }
      if (hash=='tab8link'){
    jQuery("#tabbutton8").click();
    scrollToAnchor('tabsanchor');
  }

    if (hash=='tab2'){
    jQuery("#tabbutton2").click();
  }
  if (hash=='tab3'){
    jQuery("#tabbutton3").click();
  }
    if (hash=='tab4'){
    jQuery("#tabbutton4").click();
  }
    if (hash=='tab1'){
    jQuery("#tabbutton1").click();
  }
    if (hash=='tab5'){
    jQuery("#tabbutton5").click();
  }
      if (hash=='tab6'){
    jQuery("#tabbutton6").click();
  }
      if (hash=='tab7'){
    jQuery("#tabbutton7").click();
  }
      if (hash=='tab8'){
    jQuery("#tabbutton8").click();
  }



if((hash.indexOf('wpcf7')>-1)&&(hash.indexOf('-o1')>-1)){
    jQuery(".enquireclass:first").click(); 
    scrollToAnchor('tabsanchor'); 
}



if((hash.indexOf('wpcf7')>-1)&&(hash.indexOf('-o2')>-1)){
    jQuery(".quoteclass:first").click(); 
    scrollToAnchor('tabsanchor'); 
}
 






	jQuery('.wpcf7-response-output').addClass('col-xs-12');
	jQuery('.wpcf7-response-output').addClass('col-sm-12');
	return false;

 


});


function scrollToAnchor(aid){
    var aTag = jQuery("a[name='"+ aid +"']");
    jQuery('html,body').animate({scrollTop: aTag.offset().top},'slow');
}

function update_name_jtb2(){
  var name = jQuery('#1419603528').val();
  var last = jQuery('#667586719').val();
  if((name == "")||(name == null)||(name == undefined)||(name == false)||
    (last == "")||(last == null)||(last == undefined)||(last == false)){
      jQuery('#1493792574').val("");
  }else{
    jQuery('#1493792574').val(name+" "+last);
  }
}


function hyperdiashow(){
  document.getElementById("myModal-hyperdia").style.display = "block";
}
 


 function submit_newsletter_sub(){
 
     // update_name_jtb2();
      //validate  
      if( 
        (jQuery('#entry_959658973').val()=="")||(jQuery('#entry_959658973').val()==null)||(jQuery('#entry_959658973').val()==undefined)||(jQuery('#entry_959658973').val()==false) ){
              console.log('please fill in the form111');
                jQuery("#subscribemessage").html('<p class="red-text">Please fill in the form</p>');
                return false;die;exit;
      }


            if((jQuery('#1493792574').val()=="")||(jQuery('#1493792574').val()==null)||(jQuery('#1493792574').val()==undefined)||(jQuery('#1493792574').val()==false)  ){
              console.log('please fill in the form222');
                jQuery("#subscribemessage").html('<p class="red-text">Please fill in the form</p>');
                return false;die;exit;
      }


            if(   (jQuery('#entry_1306134107').val()=="")||(jQuery('#entry_1306134107').val()==null)||(jQuery('#entry_1306134107').val()==undefined)||(jQuery('#entry_1306134107').val()==false) ){
              console.log('please fill in the form333');
                jQuery("#subscribemessage").html('<p class="red-text">Please fill in the form</p>');
                return false;die;exit;
      }

 
      jQuery.ajax({ //old
         // url: "https://docs.google.com/a/jtbap.com/forms/d/1PUqUTddB5SS3TMr2kbjhZok0FQfTkJdvIdATV2sTIcQ/formResponse",
          url: "https://docs.google.com/forms/d/1SRMQbdLim8-OPLR9LHOWfXZYtmv-Z0ms8Xdd0XQzf1k/formResponse",
          // url: "https://docs.google.com/forms/d/1SRMQbdLim8-OPLR9LHOWfXZYtmv-Z0ms8Xdd0XQzf1k/formResponse",
          //https://docs.google.com/forms/d/15ycGKEe2XkRPqJtATUJxKlCe_V0tOuiapksBl-OGs9o/edit
          //https://docs.google.com/forms/d/e/1FAIpQLSd2qhFSy6EZ3G8CiD3j6zs4IAOQczQsadk1pg1XitrtqC5m2A/viewform
          data: jQuery(this).serialize(),
          type: "POST",
          dataType: "xml",
          success: function(data) {
              console.log('Submission successful');
              jQuery("#subscribeform").toggle();
              jQuery("#subscribemessage").html('<h4 class="green-text"><i class="fa fa-envelope" aria-hidden="true"></i> Thank you for subscribing with JTB!</h4>');
          },
          error: function(xhr, status, error) {
            if (error.length>0){
              console.log('Submission failed: ' + error);
                jQuery("#subscribemessage").html('Submission failed: ' + error+'<p>Try refresh the page, or contact us using the contact form</p>');
            }else{
                console.log('Submission successful');
                jQuery("#subscribeform").toggle();
                jQuery("#subscribemessage").html('<h4 class="green-text"><i class="fa fa-envelope" aria-hidden="true"></i> You have subscribed to our newsletter</h4><p>Thank you</p>');
            }
          }
      });


 




 }