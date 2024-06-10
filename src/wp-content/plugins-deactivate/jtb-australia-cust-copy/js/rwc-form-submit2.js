
function cvf_form_validate(element) {
    $('html, body').animate({scrollTop: $(element).offset().top-100}, 150);
    element.effect("highlight", { color: "#F2DEDE" }, 1500);
    //element.parent().effect('shake');
}


function check_aus_res(){

var Number_of_customers = parseInt($('#numberOfPeople').val());//@@@@@
if((Number_of_customers==undefined)||(isNaN(Number_of_customers))||(Number_of_customers==null)||(Number_of_customers=="0")){
	Number_of_customers=0;
}

for (var i = 0 ; i <= Number_of_customers; i++) {

if(( ! document.getElementById('res'+String(i)+"0").checked ) &&
	(  document.getElementById('res'+String(i)+"1").checked ) ){
   document.getElementById('res_msg_'+String(i) ).innerHTML = '<span class="red-text">You must be an Australian resident to book.</span>';
}else{
	document.getElementById('res_msg_'+String(i) ).innerHTML = ' ';
}

}

}


jQuery(document).ready(function($) {

 


jQuery('#agent-jr-send').click(function(e){

var Number_of_customers = parseInt($('#numberOfPeople').val());//@@@@@
if((Number_of_customers==undefined)||(isNaN(Number_of_customers))||(Number_of_customers==null)||(Number_of_customers=="0")){
	Number_of_customers=0;
}




 
nocusterrors = true;

if($( '#email0').val() === '') {
	nocusterrors = false;
    cvf_form_validate($( '#email0'));
    console.log('Required: Email' );
}

if($( '#phone0').val() === '') {
	nocusterrors = false;
    cvf_form_validate($( '#phone0'));
    console.log('Required: Phone' );
}
if($( '#address0').val() === '') {
	nocusterrors = false;
    cvf_form_validate($( '#address0'));
    console.log('Required: Phone' );
}


if(!$("#names_double_check").is(":checked")){
	nocusterrors = false;
    cvf_form_validate($( '#names_double_check'));
    console.log('Required: Check T/C' );
}if(!$("#names_double_check2").is(":checked")){
	nocusterrors = false;
    cvf_form_validate($( '#names_double_check2'));
    console.log('Required: Check T/C Name Confirm' );
}


for (var i = 0 ; i <= Number_of_customers; i++) {

	if(($('#title'+i).val() === '')||($('#title'+i).val() === "Title")||($('#title'+i).val() === null)) {
		nocusterrors = false;
	    cvf_form_validate($( '#title'+i));
	    console.log('Required: title'+i);
	}else if(($( '#name'+String(i)).val() === '')||($( '#name'+String(i)).val() === null)) {
		nocusterrors = false;
	    cvf_form_validate($( '#name'+String(i)));
	    console.log('Required: name'+String(i));
	}else if(($( '#lastname'+String(i)).val() === '')||($( '#lastname'+String(i)).val() === null)) {
		nocusterrors = false;
	    cvf_form_validate($( '#lastname'+String(i)));
	    console.log('Required: lastname'+String(i));
	}else if(($( '#age'+String(i)).val() === '')||($( '#age'+String(i)).val() === null)) {//age
		nocusterrors = false;
	    cvf_form_validate($( '#age'+String(i)));
	    console.log('Required: age'+String(i));
	}else if(($( '#dob'+String(i)).val() === '')||($( '#dob'+String(i)).val() === null)) {//age
		nocusterrors = false;
	    cvf_form_validate($( '#dob'+String(i)));
	    console.log('Required: dob'+String(i));
	}else if(($( '#pass'+String(i)).val() === '')||($( '#pass'+String(i)).val() === null)) { //passport 
		nocusterrors = false;
	    cvf_form_validate($( '#pass'+String(i)));
	    console.log('Required: pass'+String(i));
	}else if( ! document.getElementById('res'+String(i)+"0").checked ){
		nocusterrors = false;
	    cvf_form_validate($( '#res'+String(i)+"0"));
	    console.log('Required: Aus-resident'+String(i));
	}
}


 


if(nocusterrors){
console.log('Required: all ok');
//hide the submit button 
$("#submit_button_hide").html( " " );


jQuery('#jragent_response').html('<div style="width: 150px;"><svg class="spinner" width="65px" height="65px" viewBox="0 0 66 66" xmlns="http://www.w3.org/2000/svg"><circle class="path" fill="none" stroke-width="6" stroke-linecap="round" cx="33" cy="33" r="30"></circle></svg></div>');


//Build and send email.
e.preventDefault();




//email number address

var title0="";
var name0="";
var midname0="";
var lastname0="";
var age0="";
var dob0="";
var res0="";
var pass0="";
 


for (var i = 0 ; i <= Number_of_customers; i++) {


title0 += $('#title'+String(i)).val()+"##@@##";
name0 += $('#name'+String(i)).val().toUpperCase()+"##@@##";
midname0 += $('#midname'+String(i)).val().toUpperCase()+"##@@##";
lastname0 += $('#lastname'+String(i)).val().toUpperCase()+"##@@##";
dob0 += $('#dob'+String(i)).val().toUpperCase()+"##@@##";

age0 += $('#age'+String(i)).val().toUpperCase()+"##@@##";
pass0 += $('#pass'+String(i)).val().toUpperCase()+"##@@##";

if(document.getElementById('res'+String(i)+"0").checked){
	res0 += "Yes##@@##";
}else{
	res0 += "No##@@##";
}




}//end loop


	jQuery.ajax({
		url: 'https://www.nx.jtbtravel.com.au/wp-admin/admin-ajax.php',
		type: "POST",
		cache: false,
		data:{ 
		  action: 'send_email_rwc2',   
email0: $('#email0').val() , 
phone0: $('#phone0').val() , 
address0: $('#address0').val() , //only person 1  

title0: title0, //list of vals 
name0: name0, //##@@##
midname0: midname0,
lastname0: lastname0,
age0: age0,
dob0: dob0,
res0: res0,
pass0: pass0 

		    },
		success:function(res){ 
		   jQuery('#jragent_response').html('<h3 class="green-text">T/C Form submitted successfully </h3>');
		   //await sleep(250);

	   //jQuery('#jragent_response').html('<h3 class="green-text">JR Pass order submitted successfully</h3>');
		   //await sleep(250);


//start google save

//var gsh_data = "entry.1457614519="+JSON.parse(res)+"&" + generate_google_data(); 
//var gsh_data =   generate_google_data(); 

// no.

var gsh_data = generate_google_data();
jQuery.ajax({
    url: "https://docs.google.com/a/jtbap.com/forms/d/1qMsu6nuBsiuFm5jRHL2FvPVORJsIiH0WDk6DiLS8hlQ/formResponse",
    //data: $(this).serialize(),
    data: gsh_data,
    type: "POST",
    dataType: "xml",
    success: function(data) {
    	//await sleep(750);
        console.log('Submission successful');

	jQuery('#jragent_response').html('<h3 class="green-text">Rugby World Cup 2019™ T/C form submitted successfully</h3><FORM><INPUT TYPE="button" onClick="history.go(0)" VALUE="New Form Submission" class="btnLarge"></FORM>');
	$("#contact_form").hide( "fast" );


    },
    error: function(xhr, status, error) {
    	if (error.length>0){
    		console.log('Submission failed: ' + error);
            //alert('Submission failed: ' + error+'<p>Try refresh the page, or contact us using the contact form</p>');
            //$("#subscribemessage").html
            jQuery('#jragent_response').html('<h3 class="red-text">Error: There was a google database error - please try again - '+ error +' </h3>');
            $("#submit_button_hide").html( '<button type="submit" id="agent-jr-send" class="submit wpcf7-form-control wpcf7-submit btnLarge">Submit form</button>' );

    	}else{
    		//await sleep(750);
            console.log('Submission successful');

	jQuery('#jragent_response').html('<h3 class="green-text">Rugby World Cup 2019™ T/C form submitted successfully</h3><FORM><INPUT TYPE="button" onClick="history.go(0)" VALUE="New Form Submission" class="btnLarge"></FORM>');
	$("#contact_form").hide( "fast" );


    	}
    }
});

//end google save



		},error:function(res){
			//jQuery('#jragent_response').html('<h3 class="red-text">There was a submit error - please try again.</h3>');


				 jQuery('#jragent_response').html('<h3 class="red-text">Error: There was an email error - please try again - ' + res + ' </h3>');

			$("#submit_button_hide").html( '<button type="submit" id="agent-jr-send" class="submit wpcf7-form-control wpcf7-submit btnLarge">Submit form</button>' );


		}
	}); //end ajax send

}
	// }//end if validation
   });//end page load
});//end



function close2(){
	var id = parseInt($('#numberOfPeople').val()); 
	if((id==undefined)||(isNaN(id))||(id==null)||(id=="0")){
		id=0;
	}
	if(id==0){
		return false;
	}
	$("#personboxzz"+String(id)).html(" ");
	id = parseInt(id)-1;
	$('#numberOfPeople').val(id);
	$("#closebox"+String(id)).show( "fast" );
}



function hide_add_person(){
	$("#personboxzzadd").hide( "fast" );
	$("#personboxzzadd22").hide( "fast" );
}

function addPerson(){
	//add one to the people-count.
	var id = parseInt($('#numberOfPeople').val()); 
	if((id==undefined)||(isNaN(id))||(id==null)||(id=="0")){
		id=0;
	}
	if(id==11){
		return false;
	}
	if(parseInt(id)>0){
		$("#closebox"+String(id)).hide( "fast" );
	}
	if(parseInt(id)==0){
		$("#personboxzzadd22").hide( "fast" );
	}
	
	
	id = id+1;
	var idplusone = id+1;
	$('#numberOfPeople').val(id);

	$("#personboxzz"+String(id)).html('<div class="clear"></div><!--\n--><!--\n--><hr /><div class="personboxzz"><!--\n--><div class="closebox" id="closebox'+id+'" onclick="close2();"></div><!--\n--><!--\n--><!--\n--><div class="col-xs-12 col-sm-12"><!--\n--><div class="row form-group" ><!--\n-->	<h4 class="float-right">Passenger '+idplusone+'</h4> <!--\n-->	<label for="name'+id+'">Title <span class="required">*</span><br /></label><!--\n-->    <select name="title'+id+'" id="title'+id+'" required class="display-block"><!--\n-->      <option value="" disabled selected> </option><!--\n-->      <option value="MR">Mr</option><!--\n-->      <option value="MRS">Mrs</option><!--\n-->      <option value="MS">Ms</option><!--\n-->    </select> <!--\n--></div></div><!--\n--><!--\n--><!--\n--><div class="col-xs-8 col-sm-4 clear-left"><!--\n--><div class="row form-group"><!--\n--><label for="name'+id+'">First Name <span class="required">*</span><br /></label><input name="name'+id+'" id="name'+id+'" required><!--\n--></div><!--\n--></div><div class="col-xs-8 col-sm-4  "><!--\n--><div class="row form-group"><!--\n--><label for="midname'+id+'">Middle Name</label><br /><input name="midname'+id+'" id="midname'+id+'"><!--\n--></div><!--\n--></div><div class="col-xs-8 col-sm-4 clear-right"><!--\n--><div class="row form-group"><!--\n-->	<label for="lastname'+id+'">Last Name <span class="required">*</span></label><br /><input name="lastname'+id+'" id="lastname'+id+'" required><!--\n--></div><!--\n--></div><!--\n--><!--\n--><!--\n--><div class="clear"></div><!--\n--><p class="red-text"><i>IMPORTANT NOTE: Please ensure names are spelt correctly as per passports</i></p><br /><!--\n--><div class="clear"></div><!--\n--><!--\n--><!--\n--><!--\n--><!--\n--><div class="col-xs-12 col-sm-6 clear-left"><!--\n--><div class="row form-group"><!--\n--><label for="dob'+id+'">Date Of Birth<span class="required">*</span></label><br /><input name="dob'+id+'" id="dob'+id+'"><!--\n--><!--\n--></div><!--\n--></div><div class="col-xs-12 col-sm-6 clear-right"><!--\n--><div class="row form-group"><!--\n-->	<label for="age'+id+'">Age <span class="required">*</span></label><br /><input name="age'+id+'" id="age'+id+'" required><!--\n--><!--\n--></div><!--\n--></div><!--\n--><!--\n--><!--\n--><!--\n--><!--\n--> <!--\n--><div class="col-xs-12 col-sm-6 clear-left"><!--\n--><div class="row form-group"><!--\n--><label for="pass'+id+'">Passport Number <span class="required">*</span></label><br /><input name="pass'+id+'" id="pass'+id+'" required><!--\n--></div><!--\n--></div><div class="col-xs-12 col-sm-6 clear-right"><!--\n--><div class="row form-group"><!--\n-->  <p><strong>Australian Resident <span class="required">*</span></strong></p><!--\n--><span class="same_pass checkbox_form"><!--\n-->  <input required type="radio" name="res'+id+'" value="yes" id="res'+id+'0"  onclick="check_aus_res();"  > yes</span><!--\n-->   <span class="same_pass checkbox_form"><input required type="radio" name="res'+id+'" value="no" id="res'+id+'1"  onclick="check_aus_res();"  > no</span><!--\n-->  <div id="res_msg_'+id+'"></div><!--\n--></div><!--\n--></div><!--\n--><!--\n--><!--\n--><!--\n--><div class="clear"></div><!--\n--><p class="red-text"><i>IMPORTANT NOTE: Passenger details must be filled out for every single person on your booking (including children and infants)</i></p> <!--\n--><div class="clear"></div> <!--\n--><!--\n--><!--\n--></div> <!--\n--><!--\n--><!--\n--><!--\n--><div class="clear"></div>');

}




 

function generate_google_data(){

var Number_of_customers = parseInt($('#numberOfPeople').val());//@@@@@
if((Number_of_customers==undefined)||(isNaN(Number_of_customers))||(Number_of_customers==null)||(Number_of_customers=="0")){
	Number_of_customers=0;
}
 

var agent_tel =  jQuery('#agent_tel').val();
var address1 =  jQuery('#address0').val(); 
var email =  jQuery('#email').val();
 


var xxxx = "xxxx";

var email_gdocs_error = "";

	//start google save
	//data-string-get-format
	//to-do remove & symbol, = symbol
	//entry.1457614519
	//entry.1457614519=test123
 gsh_data = "";

 
gsh_data=gsh_data+"entry.1094454662="+String(  $('#title0').val().toUpperCase()  + " " +  $('#name0').val().toUpperCase()  );//name
gsh_data=gsh_data+"&"+"entry.501187419="+String(   $('#midname0').val().toUpperCase()  );//name
gsh_data=gsh_data+"&"+"entry.934549982="+String(   $('#lastname0').val().toUpperCase()  );//name

gsh_data=gsh_data+"&"+"entry.1695225315="+String(   $('#age0').val().toUpperCase()  );//age

gsh_data=gsh_data+"&"+"entry.422403499="+String(   $('#email0').val().toUpperCase()  );//email

gsh_data=gsh_data+"&"+"entry.2125174606="+String(   $('#address0').val().toUpperCase()  );//address

gsh_data=gsh_data+"&"+"entry.552770110="+String(   $('#phone0').val().toUpperCase()  );//no

gsh_data=gsh_data+"&"+"entry.375848981="+String(   $('#pass0').val().toUpperCase()  );//pport

gsh_data=gsh_data+"&"+"entry.480452270="+String(   "YES"  );//tc

if(document.getElementById('res00').checked){
	gsh_data=gsh_data+"&"+"entry.1946242846="+String(   "YES"  );//au res
	}else{
		gsh_data=gsh_data+"&"+"entry.1946242846="+String(   "NO" );//au res
	}/*
	if($("#res0").is(":checked")){
		gsh_data=gsh_data+"&"+"entry.1946242846="+String(   "YES"  );//au res
	}else{
		gsh_data=gsh_data+"&"+"entry.1946242846="+String(   "NO" );//au res
	}*/


var cust_details = new Array();

cust_details[1] = new Array("entry.1842251295","entry.1871565009","entry.1007754544","entry.984199645","entry.450427489" ,"entry.561517893"   );
cust_details[2] = new Array("entry.934505157","entry.928820747","entry.636794425","entry.1807248063","entry.243294099" ,"entry.262253996"   );
cust_details[3] = new Array("entry.318648365","entry.2086596998","entry.78093022","entry.147265497","entry.1016496380" ,"entry.2063244833"   );
cust_details[4] = new Array("entry.1964396377","entry.1615759393","entry.245481953","entry.1239819588","entry.1819693731" ,"entry.311093589"   );

var other_first = "";
var other_mid = "";
var other_last = "";
var other_age = "";
var other_pass = "";
var other_aures = "";

	
	//var cust_details_11 = "";
 
for (var i = 1 ; i <= Number_of_customers; i++) {
		//name, nationality, type - change 10 to 5, and combine overflow

//single entry 0, 1, 2, 3, 4
if(i<5){


gsh_data=gsh_data+"&"+cust_details[i][0]+"="+String(  $('#title'+String(i)).val().toUpperCase()  + " " +  $('#name'+String(i)).val().toUpperCase()  );//name
gsh_data=gsh_data+"&"+cust_details[i][1]+"="+String(   $('#midname'+String(i)).val().toUpperCase()  );//name
gsh_data=gsh_data+"&"+cust_details[i][2]+"="+String(   $('#lastname'+String(i)).val().toUpperCase()  );//name

gsh_data=gsh_data+"&"+cust_details[i][3]+"="+String(   $('#age'+String(i)).val().toUpperCase()  );//age

gsh_data=gsh_data+"&"+cust_details[i][4]+"="+String(   $('#pass'+String(i)).val().toUpperCase()  );//pport


if(document.getElementById('res'+String(i)+"0").checked){
	gsh_data=gsh_data+"&"+cust_details[i][5]+"="+String(   "YES"  );//au res
}else{
	gsh_data=gsh_data+"&"+cust_details[i][5]+"="+String(   "NO" );//au res
}


}else{


other_first += "[Person " +String(i+1) + "]: " + $('#title'+String(i)).val().toUpperCase()  + " " +  $('#name'+String(i)).val().toUpperCase() + " " ;
other_mid += "[Person " +String(i+1) + "]: " +  $('#midname'+String(i)).val().toUpperCase() + " " ;
other_last += "[Person " +String(i+1) + "]: " +  $('#lastname'+String(i)).val().toUpperCase()+ " " ;
other_age += "[Person " +String(i+1) + "]: " + $('#age'+String(i)).val().toUpperCase() + " " ;
other_pass += "[Person " +String(i+1) + "]: " + $('#pass'+String(i)).val().toUpperCase() + " " ;


if(document.getElementById('res'+String(i)+"0").checked){
	other_aures += "[Person " +String(i+1) + "]: " +   "YES" + " "  ;//au res
}else{
	other_aures += "[Person " +String(i+1) + "]: " +    "NO" + " " ;//au res
}


}


}//end loop
 

//OTHER

gsh_data=gsh_data+"&"+"entry.1003290958="+String(  other_first  );//name
gsh_data=gsh_data+"&"+"entry.1862622084="+String(  other_mid  );//name
gsh_data=gsh_data+"&"+"entry.1354127728="+String(   other_last  );//name

gsh_data=gsh_data+"&"+"entry.29037324="+String(  other_age  );//age

gsh_data=gsh_data+"&"+"entry.682426355="+String(  other_pass  );//pport

gsh_data=gsh_data+"&"+"entry.1946635608="+String(  other_aures  );//au res


	return gsh_data;
}


