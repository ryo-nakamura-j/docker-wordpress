
//test js - loading the fields etc.




$( document ).ready(function() {


jQuery('#rwc-transport-submit').click(function(e){
//Build and send email.
e.preventDefault();


if($('#rwcname').val().length <2) {
    cvf_form_validate($('#rwcname'));
    console.log('Required: name');
}else if($('#rwcemail').val().length <6) {
    cvf_form_validate($('#rwcemail'));
    console.log('Required: email');
}else if(! $('#rwcemail').val().includes("@")) {
    cvf_form_validate($('#rwcemail'));
    console.log('Required: email format issue');
}else if(  $('#rwc-state').val() == 'x') {
    cvf_form_validate($('#rwc-state'));
    console.log('Required: state');
}else if(( $('#travel_opt').html() == "")||( $('#travel_opt').html() == " -- Choose your match ticket -- ")||( $('#travel_opt').html() == null)||( $('#travel_opt').html() == false)){
    cvf_form_validate($('#rwc-match'));
    console.log('Required: Choose your match ticket');
}else{
	//no errors 


var message = '[br /][br /][b]Name[/b][br /]';
message+= $('#rwcname').val();
message+= '[br /][br /][b]Email[/b][br /]';
message+= $('#rwcemail').val();
message+= '[br /][br /][b]How many tickets would you like?[/b][br /]';
message+= $('#rwcnumtix').val();
message+= '[br /][br /][b]Match[/b][br /]';
message+= $( "select#rwc-match" ).val();
message+= '[br /][br /]';
//message+= $('#rwc-match').val();



	jQuery.ajax({
		url: 'https://www.nx.jtbtravel.com.au/wp-admin/admin-ajax.php',
		type: "POST",
		cache: false,
		data:{ 
action: 'send_email_rwc_travel', 
message: message,
email: $('#rwcemail').val(),
name: $('#rwcname').val(),
state: $( "select#rwc-match" ).val()
 
	 
		    },
		success:function(res){ 
		   jQuery('#rwc_response').html('<h3 class="green-text">Form submitted successfully</h3>');
	 	jQuery('#rwc-transport-package').html('');
	 	jQuery('#rwc-transport-submit').hide();
	 	
 
		},error:function(res){
 

				 jQuery('#rwc_response').html('<h3 class="red-text">Error: There was an email error - please try again - ' + res + ' </h3>');
 

		}
	}); //end ajax send



}//end of no-errors - send email using f(x)



});//click submit button

});//on-doc-load


//adjust prices in HTML and JS @@@ - and array X3

function rwc_select(){
var rwc_selected = $( "select#rwc-match" ).val();
var item_ids = [
"Match-24-Australia-v-Uruguay-from-411",
"Match-2-Australia-v-Fiji-from-481",
"Match-33-Australia-v-Georgia-from-481",
"Match-17-Australia-v-Wales-from-770",
"Match-4-New-Zealand-v-South-Africa-from-929",
"Match-44-QF-4-W-Pool-A-v-RU-Pool-B-from-929",
"Match-43-QF-3-W-Pool-D-v-RU-Pool-C-from-929",
"Match-42-QF-2-W-Pool-B-v-RU-Pool-A-from-929",
"Match-41-QF-1-W-Pool-C-v-RU-Pool-D-from-929",
"Match-46-SF2-W-QF3-v-W-QF4-from-1627",
"Match-45-SF1-W-QF1-v-W-QF2-from-1627",
"Match-47-Bronze-Final-from-780",
"Match-48-Final-from-3023",
"x"
];





var item_names = [
"Match 24 Australia v Uruguay",
"Match 2 Australia v Fiji",
"Match 33 Australia v Georgia",
"Match 17 Australia v Wales",
"Match 4 New Zealand v South Africa",
"Match 44 QF 4 W Pool A v RU Pool B",
"Match 43 QF 3 W Pool D v RU Pool C",
"Match 42 QF 2 W Pool B v RU Pool A",
"Match 41 QF 1 W Pool C v RU Pool D",
"Match 46 SF2 W QF3 v W QF4",
"Match 45 SF1 W QF1 v W QF2",
"Match 47 Bronze Final",
"Match 48 Final",
" -- Choose your match ticket -- "
];




var from_price = [
"411",
"481",
"481",
"770",
"929",
"929",
"929",
"929",
"929",
"1627",
"1627",
"780",
"3023",
" -- Choose your match ticket -- "
];



var selected_id_no = item_ids.findIndex(function(element) {
  return element == rwc_selected;
});

$("#package-no").val(selected_id_no);


//alert(selected_id_no + " - "+item_names[selected_id_no]);
//selected_id_no - id of the item 
//item_names[selected_id_no] - full name of the item 


if(( from_price[parseInt(selected_id_no)] == "x")||( from_price[parseInt(selected_id_no)] == " -- Choose your match ticket -- ")||( from_price[parseInt(selected_id_no)] == "")){
	$("#travel_opt").html(" -- Choose your match ticket -- ");
}else{
	$("#travel_opt").html("From $" + from_price[parseInt(selected_id_no)] + " per person" );

}


//var travel_options_pre = '<h2>Transport</h2>';


/*
if( (selected_id_no == 0) || (selected_id_no == 2) || (selected_id_no == 6) || (selected_id_no == 8)     ){
	$("#total_pack").val(  $("input[name='travel_option']:checked").val()   );
	$("#total_pack_price").val(  $("#total_pack").val().replace(/(.*?)_/, '')  );
$("#total_pack_price_only").val(  $("#total_pack").val().replace(/(.*?)_/, '')  );
	$("#total_print").html( '<h3><small>Total price estimate:</small> $'+$("#total_pack_price").val() +"</h3>") ;
	ready_to_book();

}else{
 
		$("#total_pack").val( "0");
		$("#total_pack_price").val( "0");
		$("#total_pack_price_only").val( "0");
		$("#total_print").html(""); 
		$("#rwc-extras").html("");
}*/


/*$('input:radio[name=travel_option]').change(function() {
    update_ticket();
});*/

}


function update_ticket(){
	return true;
	if(  ($("input[name='travel_option']:checked").val() != "") && ($("input[name='travel_option']:checked").val() != null) && ($("input[name='travel_option']:checked").val()) != false  ){
		$("#total_pack").val(  $("input[name='travel_option']:checked").val()   );
		$("#total_pack_price").val( $("#total_pack").val().replace(/(.*?)_/, '')  );
		$("#total_pack_price_only").val( $("#total_pack").val().replace(/(.*?)_/, '')  );
		$("#total_print").html( '<h3><small>Total price estimate:</small> $'+$("#total_pack_price").val()  +'</h3>') ;
		ready_to_book();
	}else{
		$("#total_pack").val( "0");
		$("#total_pack_price").val( "0");
		$("#total_pack_price_only").val( "0");
		$("#total_print").html("");	
		$("#rwc-extras").html("");	
	}
}



function ready_to_book(){

}

function update_price_extras(){

}




 

function cvf_form_validate(element) {
    $('html, body').animate({scrollTop: $(element).offset().top-100}, 150);
    element.effect("highlight", { color: "#F2DEDE" }, 1500);
    //element.parent().effect('shake');
}


