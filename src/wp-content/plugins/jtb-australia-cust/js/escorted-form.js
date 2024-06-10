 

function cvf_form_validate(element) {
    $('html, body').animate({scrollTop: $(element).offset().top-100}, 150);
    element.effect("highlight", { color: "#F2DEDE" }, 1500);
    //element.parent().effect('shake');
}


 

jQuery(document).ready(function($) {

 

$('input:radio[name=type0]').change(function() {
	checkChildChecked();
});


jQuery('#agent-jr-send').click(function(e){


//Build and send email.
e.preventDefault();

if($('#date').val() === '') {
    cvf_form_validate($('#date'));
    console.log('Required: date');
}else if($('#tour2').val() === '') {
	cvf_form_validate($('#tour2'));
	console.log('Required: tour2');
}else if($('#name').val() === '') {
    cvf_form_validate($('#name'));
    console.log('Required: name');
}else if($('#email').val() === '') {
    cvf_form_validate($('#email'));
    console.log('Required: email');
}else if(!validateEmail($('#email').val()) ) {
    cvf_form_validate($('#email'));
    console.log('Required: valid_email');
}else if($('#dob').val() === '') {
    cvf_form_validate($('#dob'));
    console.log('Required: dob');
}else if($('#age').val() === '') {
    cvf_form_validate($('#age'));
    console.log('Required: age');
}else if($('#mobile').val() === '') {
    cvf_form_validate($('#mobile'));
    console.log('Required: mobile');
}else if($('#emergency_name').val() === '') {
    cvf_form_validate($('#emergency_name'));
    console.log('Required: emergency_name');
}else if($('#relation').val() === '') {
    cvf_form_validate($('#relation'));
    console.log('Required: relation');
}else if($('#emergency_number').val() === '') {
    cvf_form_validate($('#emergency_number'));
    console.log('Required: emergency_number');
//}else if($('#walk_5k').val() === '') {
}else if (!$('input[name=walk_5k]:checked').length > 0) {
    cvf_form_validate($('#walk_5k'));
    console.log('Required: walk_5k');
}else if (!$('input[name=carry_luggage]:checked').length > 0) {
    cvf_form_validate($('#carry_luggage'));
    console.log('Required: carry_luggage');
}else if (!$('input[name=assistance]:checked').length > 0) {
    cvf_form_validate($('#assistance'));
    console.log('Required: assistance');
} 
else {

 
nocusterrors = true;
var same_passes_check = $("#all_same").val();


if(nocusterrors){
console.log('Required: all ok');
//hide the submit button 
$("#submit_button_hide").html( " " );


jQuery('#jragent_response').html('<div style="width: 150px;"><svg class="spinner" width="65px" height="65px" viewBox="0 0 66 66" xmlns="http://www.w3.org/2000/svg"><circle class="path" fill="none" stroke-width="6" stroke-linecap="round" cx="33" cy="33" r="30"></circle></svg></div>');

 

var form_data = new Array();

 

var name = $('#name').val();
var email = $('#email').val();

 

var email_gdocs_error = "";


var message =  "[[img src='https://www.nx.jtbtravel.com.au/wp-content/uploads/2017/10/admin-section.png' style='position:relative;float:right;display:inline-block;width:100px;height:auto;' /]]";


var desc_2 = block_text_sanitize($('#assistance3').val());
if((desc_2.length > 0 ) && (desc_2.length != " " )){
    desc_2 = " - " + desc_2;
}
var assistance_2 = block_text_sanitize($('input[name=assistance]:checked').val()) + desc_2 ;


	jQuery.ajax({
		url: 'https://www.nx.jtbtravel.com.au/wp-admin/admin-ajax.php',
		type: "POST",
		cache: false,
		data:{ 
action: 'send_email_escorted', 
name: name,
email: email,
date: $('#date').val(),
tour2: $('#tour2').val(),
dob: $('#dob').val(),
age: $('#age').val(),
phone: $('#phone').val() ,
mobile: $('#mobile').val(),
insurer: $('#insurer').val(),
policy: $('#policy').val(),
emergency_name: $('#emergency_name').val(),
relation: $('#relation').val(),
emergency_number: $('#emergency_number').val(),
want_to_see: block_text_sanitize($('#want_to_see').val() ), 
beds_yes_no: block_text_sanitize($('input[name=beds_yes_no]:checked').val()),
meal_requirements: block_text_sanitize($('#meal_requirements').val()),
medical: block_text_sanitize($('#medical').val() ),
walk_5k: block_text_sanitize($('input[name=walk_5k]:checked').val()),
carry_luggage: block_text_sanitize($('input[name=carry_luggage]:checked').val()),
assistance: assistance_2
	 
		    },
		success:function(res){ 
		   jQuery('#jragent_response').html('<h3 class="green-text">Form submitted successfully</h3>');
	 
 
		},error:function(res){
 

				 jQuery('#jragent_response').html('<h3 class="red-text">Error: There was an email error - please try again - ' + res + ' </h3>');
 

		}
	}); //end ajax send

 

}
	 }//end if validation
   });//end page load
});//end


function validateEmail(email) {
    var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(String(email).toLowerCase());
}

 
function block_text_sanitize(comments){
var c2 = "" + comments;
c2 = c2.replace("\r", "\n");
c2 = c2.replace("\n\n", "\n");
c2 = c2.replace("\n\n", "\n");
c2 = c2.replace("\n", " - ");
return c2;
 }