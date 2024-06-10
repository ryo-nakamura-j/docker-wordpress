
jQuery( document ).ready(function() {
/*
 jQuery("#header_message_jtb_head").hide();
        jQuery("#header_message_jtb_foot").hide();
    alert("yes");   


$(document).on('click', "div.menu-btn", function() {
        jQuery("#header_message_jtb_head").hide();
        jQuery("#header_message_jtb_foot").hide();
    alert("yes");       
});



$(document).on('click', "div.is-active", function() {
        jQuery("#header_message_jtb_head").show();
        jQuery("#header_message_jtb_foot").show();
    alert("yes");       
});

 




$(".burger-icon").on('click', function(event){
    event.stopPropagation();
    event.stopImmediatePropagation();
    	jQuery("#header_message_jtb_head").hide();
    	jQuery("#header_message_jtb_foot").hide();
    alert("yes");
});


$(".is-active").on('click', function(event){
    event.stopPropagation();
    event.stopImmediatePropagation();
    	jQuery("#header_message_jtb_head").show();
    	jQuery("#header_message_jtb_foot").show();
    alert("yes");
});
 
*/




 jQuery(".menu-btn").click(toggle_head_menu());

 
});


function toggle_head_menu(){
        jQuery("#header_message_jtb_head").toggle();
        jQuery("#header_message_jtb_foot").toggle();
}