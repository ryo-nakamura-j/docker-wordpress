
function cvf_form_validate(element) {
    $('html, body').animate({scrollTop: $(element).offset().top-100}, 150);
    element.effect("highlight", { color: "#F2DEDE" }, 1500);
    //element.parent().effect('shake');
}



function addWorkDays(startDate, days) {
    // Get the day of the week as a number (0 = Sunday, 1 = Monday, .... 6 = Saturday)
    var dow = startDate.getDay();
    var daysToAdd = parseInt(days);
    // If the current day is Sunday add one day
    if (dow == 0)
        daysToAdd++;
    // If the start date plus the additional days falls on or after the closest Saturday calculate weekends
    if (dow + daysToAdd >= 6) {
        //Subtract days in current working week from work days
        var remainingWorkDays = daysToAdd - (5 - dow);
        //Add current working week's weekend
        daysToAdd += 2;
        if (remainingWorkDays > 5) {
            //Add two days for each working week by calculating how many weeks are included
            daysToAdd += 2 * Math.floor(remainingWorkDays / 5);
            //Exclude final weekend if remainingWorkDays resolves to an exact number of weeks
            if (remainingWorkDays % 5 == 0)
                daysToAdd -= 2;
        }
    }
    startDate.setDate(startDate.getDate() + daysToAdd);
    return startDate;
}

function changename(){
	//full_name_one
	var title= document.getElementById("consultant_title").value ;
	var first= document.getElementById("consultant_name").value ;
	var last= document.getElementById("consultant_name2").value ;
if((title=="x")||(title=="X")||(title==null)||(title==undefined)||(title==false)||
	(first=="")||(first==null)||(first==undefined)||(first==false)||
	(last=="")||(last==null)||(last==undefined)||(last==false)){
	document.getElementById("full_name_one").value =  "";
	document.getElementById("full_name_one_display").innerHTML  =  "";
}else{
	document.getElementById("full_name_one").value = title.toUpperCase()+" "+first.toUpperCase()+" "+last.toUpperCase();
	document.getElementById("full_name_one_display").innerHTML  = "<p><b>Name:</b> "+title.toUpperCase()+" "+first.toUpperCase()+" "+last.toUpperCase()+"</p>";
}




}// end changename()



function changedate(){

//@@@@@

var temp_date_data = document.getElementById("hidden_date_data").value;



var d1 = document.getElementById("departure_date_d").value;
var m1 = document.getElementById("departure_date_m").value;
var y1 = document.getElementById("departure_date_y").value;

var d2 = document.getElementById("jr_use_date_d").value;
var m2 = document.getElementById("jr_use_date_m").value;
var y2 = document.getElementById("jr_use_date_y").value;
const monthNames = ["B" ,"Jan", "Feb", "Mar", "Apr", "May", "Jun",
  "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"
];
var date_before = document.getElementById("availabledates").value;
var date_before2 = ""+date_before.substring(0, 4)+monthNames[parseInt(date_before.substring(4,6))]+date_before.substring(6, 8);
var date_using = ""+y2+m2+d2;

//20191201
var date_before_unix = Math.round(new Date(date_before2+", 00:00:00").getTime()/1000);
var date_using_unix = Math.round(new Date(date_using+", 00:00:00").getTime()/1000);

if(temp_date_data.length>30){
date_before_unix = Math.round(new Date("01/01/2999, 00:00:00").getTime()/1000);
}

//if date is before today- error
var ts = Math.round((new Date()).getTime() / 1000) - 86400; //today 


if((d1=="x")||(m1=="x")||(y1=="x")||(d1==null)||(m1==null)||(y1==null)||(d1==undefined)||(m1==undefined)||(y1==undefined)){
	//document.getElementById("day5dep").classList.add("red-text");
	document.getElementById("departure_date").value = "";
	$('#date_limit_text3').removeClass('red-text');
}else{
	if (date_is_valid_5_days() || 1 ){ // the or 1 disables 5 days before dep. kate asked to delete
		document.getElementById("departure_date").value = d1 + "/" + m1 + "/" + y1  ;
		//document.getElementById("day5dep").classList.remove("red-text");
	}else{
		//document.getElementById("day5dep").classList.add("red-text");
		document.getElementById("departure_date").value = "";
		$('#date_limit_text3').removeClass('red-text');
	}
}

if((d2=="x")||(m2=="x")||(y2=="x")||(d2==null)||(m2==null)||(y2==null)||(d2==undefined)||(m2==undefined)||(y2==undefined)){
	document.getElementById("jr_use_date").value = "";
	$('#date_limit_text3').removeClass('red-text');
}else{

		if((date_using_unix > date_before_unix) ){
			document.getElementById("jr_use_date").value = ""; 
			$('#date_limit_text3').addClass('red-text');
		}else{

			if(date_using_unix < ts){//if date before today - error
				document.getElementById("departure_date").value = "";
				$('#date_limit_text3').addClass('red-text');
			}else{
				document.getElementById("jr_use_date").value = d2 + "/" + m2 + "/" + y2  ;
				$('#date_limit_text3').removeClass('red-text');
			}


		}


	//document.getElementById("jr_use_date").value = d2 + "/" + m2 + "/" + y2  ;
}

 

}


function add_leading_zero(x){
x = x.toString()
if (x.length <2){
	x = "0" + x;
}
return x;
}

function add_day_to_date(x){
	//in - string date 
	//out - date + 1 day
	//20190425
	//20190426 (taking into account months etc.)
var date2 = x.toString();
var tomorrow = new Date(date2.substring(0, 4) + "-" +  date2.substring(4, 6) + "-" + date2.substring(6, 8) );
tomorrow.setDate(tomorrow.getDate() + 1);
var zd = add_leading_zero(tomorrow.getDate().toString());
var zm =add_leading_zero( (   tomorrow.getMonth() +1  ).toString());
var zy = tomorrow.getFullYear().toString();
return zy + zm + zd;
}


function date_is_valid_5_days(){
// if date is not ok - return false.
// BDAY = 5 business days after today
// if departure date is less than BDAY - return false

var holidays = new Array(

20180425,//anzac
20180611,//queen
20180928,//grand final
20181106,//cup day
20181225,//xmas
20181226,//xmas box

20190101,//nye
20190128,//au day
20190311,//labour
20190419,//easter
20190422,//easter
20190425,//anzac
20190610,//queen
20191105,//cup day
20191225,//xmas
20191226,//xmas box
//Grand Final Day 2019 is TBD.
29881131 //end - ask for update - July 2019. 
);


 

var d = new Date() ; 
var zd = add_leading_zero(d.getDate().toString());
var zm =add_leading_zero( (   d.getMonth() +1  ).toString());
var zm_minus_one =  add_leading_zero((   d.getMonth()   ).toString());

var today_date = new Date(d.getFullYear().toString(), zm_minus_one, zd); 
//month is zero based (+1 to get actual month)

var today_plus_five =  addWorkDays(today_date,5);

var d5 = add_leading_zero(today_plus_five.getDate().toString());
var m5 = add_leading_zero((today_plus_five.getMonth() +1).toString());
var y5 = today_plus_five.getFullYear().toString();

var today =  d.getFullYear().toString() +  zm.toString()  +  zd.toString()   ;//.getTime() / 1000;
var today_plus_five_text = y5.toString()+m5.toString()+d5.toString() ;
 
var   monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun",  "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"
];

var d1 = document.getElementById("departure_date_d").value;
var m1 = document.getElementById("departure_date_m").value;
m1 = add_leading_zero((monthNames.indexOf(m1)+1).toString());

var y1 = document.getElementById("departure_date_y").value;

var input_date = y1.toString() + m1.toString() + d1.toString();


//for each in array holidays
for (var i = holidays.length - 1; i >= 0; i--) {
	if(( parseInt(holidays[i]) <= parseInt(today_plus_five_text) ) && 
			( parseInt(holidays[i]) >= parseInt(today) )){
		today_plus_five_text = add_day_to_date(today_plus_five_text);
	}
}


console.log('Departure - today: ' + today + ' - form input: ' + input_date + ' - today + 5 business days: ' + today_plus_five_text );
 
if(parseInt(input_date) < parseInt(today_plus_five_text) ){
	return false;
}else{
	return true;
}

}



function init_data_insert(){
$("#day1data" ).html($('#day1data1').val());
$("#day2data" ).html($('#day2data2').val());
}



jQuery(document).ready(function($) {

init_data_insert();

$("#myModal-1 :checkbox").change(function(e){
  //var id=$('#personid').val();
  var jrp = $('#myModal-1 input:checkbox:checked').map(function() {
      return this.value ;
  }).get();
  var id=$('#personid').val();
  $('#passes'+id).val(jrp);
  $("#jrdisplay"+id).html(processPassesReadable("" + jrp));
});

  
$("#all_same").change(function(e){
  var same = $("#all_same").val();
  if (same=="all_not_same"){//if set to each person dif passes, hide all buttons etc.
  	$("#all_same").val("all_same");
  	hide_all_buttons();
  }else{
  	$("#all_same").val("all_not_same");
  	show_all_buttons();
  }
 
});


$('input:radio[name=type0]').change(function() {
	checkChildChecked();
});


jQuery('#agent-jr-send').click(function(e){

var Number_of_customers = parseInt($('#numberOfPeople').val()); 
if((Number_of_customers==undefined)||(isNaN(Number_of_customers))||(Number_of_customers==null)||(Number_of_customers=="0")){
	Number_of_customers=0;
}


var nearest_branch = $('input[name="nearest_branch"]:checked').val();
var delivery_method = $('input[name="delivery_method"]:checked').val();

var nopasses = $('#nopasses2').val();
var passlist  = "";
var numberofdays2  = "";

var radios = document.getElementsByName('pass_3_1');
var radios2 = document.getElementsByName('no_days');

 
for (var i = 0, length = radios.length; i < length; i++){
 if (radios[i].checked)
 {
  passlist = radios[i].value;
  break;
 }
}

for (var i = 0, length = radios2.length; i < length; i++){
 if (radios2[i].checked)
 {
  numberofdays2 = radios2[i].value;
  break;
 }
}

//numberofdays2

/*
for (var i = 0 ; i <= nopasses; i++) {
	
	if($("#pass_2_"+ (i+1)).prop("checked")) {

		passlist += $("#pass_2_"+ (i+1) ).val() + "11__22br /11__33";
	}

}*/

	 if($('#agent_tel').val() === '') {
    	cvf_form_validate($('#agent_tel'));
    	console.log('Required: agent_tel');
	}else if($('#address1').val() === '') {
	    cvf_form_validate($('#address1'));
	    console.log('Required: address1');
	}else if($('#full_name_one').val() === '') {
	    cvf_form_validate($('#full_name_one'));
	    console.log('Required: name_1');
	}else if($('#suburb').val() === '') {
	    cvf_form_validate($('#suburb'));
	    console.log('Required: suburb');
	}else if($('#state').val() === '') {
	    cvf_form_validate($('#state'));
	    console.log('Required: state');
	}else if($('#post_code').val() === '') {
	    cvf_form_validate($('#post_code'));
	    console.log('Required: post_code');
	}else if($('#email').val() === '') {
        cvf_form_validate($('#email'));
        console.log('Required: email');
    }else if($('#no_days').val() === '') {
        cvf_form_validate($('#no_days'));
        console.log('Required: number of days for the pass');
    }else if(($('#departure_date').val() === '')||($('#departure_date').val() === undefined)||($('#departure_date').val() === null)||($('#departure_date').val() === false)||($('#departure_date').val() === 'undefined')) {
	    cvf_form_validate($('#departure_date'));
	    console.log('Required: departure_date');
	}else if(($('#jr_use_date').val() === '')||($('#jr_use_date').val() === undefined)||($('#jr_use_date').val() === null)||($('#jr_use_date').val() === false)||($('#jr_use_date').val() === 'undefined')) {
	    cvf_form_validate($('#jr_use_date_d'));
	    console.log('Required: jr_use_date');
	}else if(passlist.length == 0 ){
		cvf_form_validate($('#passes2usj'));
	    console.log('Required: USJ Pass Selection ');
   }else if(numberofdays2.length == 0 ){
		cvf_form_validate($('#passes2usj'));
	    console.log('Required: USJ Pass Selection ');
   }else{

 
nocusterrors = true;
//var same_passes_check = $("#all_same").val();
for (var i = 0 ; i <= Number_of_customers; i++) {

if(i != 0){
		if(($('#title'+i).val() === '')||($('#title'+i).val() === "Title")||($('#title'+i).val() === null)) {
		nocusterrors = false;
	    cvf_form_validate($( '#title'+i));
	    console.log('Required: title'+i);
	}else if($( '#name'+String(i)).val() === '') {
		nocusterrors = false;
	    cvf_form_validate($( '#name'+String(i)));
	    console.log('Required: name'+String(i));
	}else if($( '#lastname'+String(i)).val() === '') {
		nocusterrors = false;
	    cvf_form_validate($( '#lastname'+String(i)));
	    console.log('Required: lastname'+String(i));
	}
}



	if(($( '#age'+String(i)).val() === '')||($( '#age'+String(i)).val() === null)||($( '#age'+String(i)).val() === false)) {
		 
			nocusterrors = false;
		    cvf_form_validate($( '#age'+String(i)));
		    console.log('Required: age'+String(i));
	}

	//validate title/ radio buttons ###
}



if(nocusterrors){
console.log('Required: all ok');
//hide the submit button 
$("#submit_button_hide").css('visibility', 'hidden'); 


jQuery('#jragent_response').html('<div style="width: 150px;"><svg class="spinner" width="65px" height="65px" viewBox="0 0 66 66" xmlns="http://www.w3.org/2000/svg"><circle class="path" fill="none" stroke-width="6" stroke-linecap="round" cx="33" cy="33" r="30"></circle></svg></div>');


//Build and send email.
e.preventDefault();

var agent_tel =  jQuery('#agent_tel').val();
var address1 =  jQuery('#address1').val();
var consultant_name =  jQuery('#full_name_one').val()  ;
var suburb =  jQuery('#suburb').val();
var state =  jQuery('#state_field_2 option:selected').text();
var post_code =  jQuery('#post_code').val();
var email =  jQuery('#email').val();

var tix_booked2 = jQuery('#refno2').val(); 
 


var address2 =  jQuery('#address2').val();

var departure_date = jQuery('#departure_date').val();
var jr_use_date = jQuery('#jr_use_date').val();


var email_gdocs_error = "";


var message =  "11__22img src='https://www.nx.jtbtravel.com.au/wp-content/uploads/2017/10/admin-section.png' 11__44'position:relative;float:right;display:inline-block;width:100px;height:auto;' /11__33";


message += "11__22h211__33USJ Express Pass Request Form:11__22/h211__33";
message += "11__22b11__33Name:11__22/b11__3311__22br /11__33"+consultant_name.toUpperCase()+"11__22br /11__33";
message += "11__22b11__33Email:11__22/b11__3311__22br /11__33"+email+"11__22br /11__33";
message += "11__22b11__33Phone:11__22/b11__3311__22br /11__33"+agent_tel+"11__22br /11__33";
message += "11__22b11__33Address (Australia only):11__22/b11__3311__22br /11__33"+address1.toUpperCase()+"11__22br /11__33";
if(address2!=""){ message += address2.toUpperCase()+"11__22br /11__33"; }
message += "11__22b11__33Suburb:11__22/b11__3311__22br /11__33"+suburb.toUpperCase()+"11__22br /11__33";
message += "11__22b11__33State:11__22/b11__3311__22br /11__33"+state.toUpperCase()+"11__22br /11__33";
message += "11__22b11__33Post Code:11__22/b11__3311__22br /11__33"+post_code+"11__22br /11__33";

message += "11__22h211__33Request Details11__22/h211__33";

message += "11__22b11__33Order transaction time:11__22/b11__3311__22br /11__33@@#DATE_TIME#@@11__22br /11__33";
message += "11__22b11__33Date of departure from Australia:11__22/b11__3311__22br /11__33"+departure_date+"11__22br /11__33";
message += "11__22b11__33Intended date of entry:11__22/b11__3311__22br /11__33"+jr_use_date+"11__22br /11__33";
message +=  "11__22b11__33Pass: 11__22/b11__33" +passlist + "11__22br /11__3311__22b11__33Number of days: 11__22/b11__33"+numberofdays2 +"11__22br /11__33" ;
if( tix_booked2.length >0 ){
   message +=  "11__22b11__33Booked entry ticket?11__22/b11__33 - " + tix_booked2 + "11__22br /11__33" ;
}

message += "11__22b11__33Passholder Details11__22/b11__3311__22br /11__33";


for (var i = 0 ; i <= Number_of_customers; i++) {

 //no name needed - just ages ~ kate  - actually add names again 
 if(i==0){
 	message += consultant_name;
 }else{
	message += $('#title'+String(i)).val().toUpperCase() + " " + $('#name'+String(i)).val().toUpperCase() + " " + $('#lastname'+String(i)).val().toUpperCase(); 
 }	
	message += " - "+$('#age'+String(i)).val().toUpperCase()+"11__22br /11__33";

}//end loop
var state2 = state.toUpperCase();
var wpfunction = "send_email_usj";
if(window.location.hash.substr(1) == "testzz" && 0 ){
	wpfunction = "send_email_usj2";
}
//var recap = jQuery('#g-recaptcha-response').val(); 
	jQuery.ajax({
		url: 'https://www.nx.jtbtravel.com.au/wp-admin/admin-ajax.php',
		type: "POST",
		cache: false,
		data:{ 
		  action: wpfunction, 
		  name: consultant_name,
		  email: email,
		  message: message ,
		  recap: grecaptcha.getResponse(),
		  state2: state2
		    },
		success:function(res){ 

if( res== "no-recap0"){
jQuery('#jragent_response').html('<h3 class="red-text">Please complete the recaptcha field</h3>');
$("#submit_button_hide").css('visibility', 'visible'); 

}else if(res == "wrong-recap0"){
jQuery('#jragent_response').html('<h3 class="red-text">Recaptcha error</h3>');
$("#submit_button_hide").css('visibility', 'visible'); 

}else {
  jQuery('#jragent_response').html('<h3 class="green-text">USJ Express Pass order submitted successfully</h3><br />' + res );

}
		   //await sleep(250);
  
		},error:function(res){
			//jQuery('#jragent_response').html('<h3 class="red-text">There was a submit error - please try again.</h3>');

				 jQuery('#jragent_response').html('<h3 class="red-text">Error: There was an email error - please try again - ' + res + ' </h3>');

			$("#submit_button_hide").html( '<button type="submit" id="agent-jr-send" class="submit wpcf7-form-control wpcf7-submit btnLarge">Submit JR Pass Order</button>' );


		}
	}); //end ajax send

 

}
	 }//end if validation
   });//end page load
});//end


 


function booked_entry(){
var radios = document.getElementsByName('booked_tix');
for (var i = 0, length = radios.length; i < length; i++){
 if (radios[i].checked){ 
  if(radios[i].value == "yes"){
  	return true;
  }else{
  	return false;
  }
 }
}
}




function processPassesReadable(p){
  return "<p><span>JR Passes Selected</span><br />" + p.replace(/,/g, "<br />").replace(/_/g, " ") + "</p>";
}

function processPassesReadableEmail(p){
  return   p.replace(/,/g, "11__22br /11__33").replace(/_/g, " ")  ;
}

function processPassesReadableGoogle(p){
  return   p.replace(/,/g, " --- ").replace(/_/g, " ")  ;
}

function selectPasses(id){
	$('#personid').val(id);
	$("#myModal-1 :checkbox").attr('checked', false); //uncheck all
	var jrlist = $('#passes'+id).val();
	var list = jrlist.split(",");
	for (var i = list.length - 1; i >= 0; i--) {
	  $("#"+list[i]).click();
	}
	document.getElementById("myModal-1").style.display = "block";;
}

function close2(){
	var id = parseInt($('#numberOfPeople').val()); 
	if((id==undefined)||(isNaN(id))||(id==null)||(id=="0")){
		id=0;
	}
	if(id==0){
		return false;
	}
	$("#personbox"+String(id)).html(" ");
	id = parseInt(id)-1;
	$('#numberOfPeople').val(id);
	$("#closebox"+String(id)).show( "fast" );
}


function hide_all_buttons(){
	//hide all pass buttons for non person-0 people.
	var id = parseInt($('#numberOfPeople').val()); 
	for (var i = 1 ; i <= id; i++) {
		$("#jrdisplay"+String(i)).hide( "fast" );
		$("#jrbutton"+String(i)).hide( "fast" );
	}
}

function show_all_buttons(){
	//show all buttons other than person-0
	var id = parseInt($('#numberOfPeople').val()); 
	for (var i = 1 ; i <= id; i++) {
		$("#jrdisplay"+String(i)).show( "fast" );
		$("#jrbutton"+String(i)).show( "fast" );
	}
}



 

//@@@
function checkChildChecked(){

var Number_of_customers = parseInt($('#numberOfPeople').val());//@ 
if((Number_of_customers==undefined)||(isNaN(Number_of_customers))||(Number_of_customers==null)||(Number_of_customers=="0")){
	Number_of_customers=0;
}


for (var i = 0 ; i <= Number_of_customers; i++) {
		//name, nationality, type
	if(i<10){
		if($("#type"+i+"2").is(":checked")){
			$("#dob"+i+"container").html('<div class="col-xs-12 col-sm-6"><div class="row form-group"><p><input name="dob'+i+'" type="text" id="dob'+i+'"    /> <label for="dob'+i+'">Child Date of Birth</label></p></div></div>');
		}else{
			$("#dob"+i+"container").html(' ');
		}
	}//else{ }  - no validation for all in 1 text-box
		
}
//<div class="dob0container" id="dob0container"></div> - add to new-person
} //end function


function addPerson(){
	//add one to the people-count.
	var id = parseInt($('#numberOfPeople').val()); 
	if((id==undefined)||(isNaN(id))||(id==null)||(id=="0")){
		id=0;
	}
	if(id==39){
		return false;
	}
	if(parseInt(id)>0){
		$("#closebox"+String(id)).hide( "fast" );
	}
	id = id+1;
	var idplusone = id+1;
	$('#numberOfPeople').val(id);

 

	$("#personbox"+String(id)).html('<div class="personbox"><div class="closebox" id="closebox'+id+'" onclick="close2();"></div><!--n--><h3>Person '+(id+1)+'</h3><!--n--><!--n--><!--n--><div class="col-xs-12 col-sm-12"><!--n--><div class="row form-group" ><!--n--> <div class="col-xs-12 col-sm-12"><!--n--><div class="row form-group" ><!--n-->    <select name="title'+id+'" id="title'+id+'" required ><!--n-->      <option value="" disabled selected>Title</option><!--n-->      <option value="MR">Mr</option><!--n-->      <option value="MRS">Mrs</option><!--n-->      <option value="MS">Ms</option><!--n-->    </select> <span class="required">*</span><!--n--></div></div><!--n--><!--n--><!--n--><!--n--><div class="col-xs-12 col-sm-12"><!--n--><div class="row form-group"><!--n--><label for="name'+id+'">First Name <span class="required">*</span></label><input name="name'+id+'" id="name'+id+'" required><!--n--></div><!--n--></div><!--n--> <!--n--><div class="col-xs-12 col-sm-12"><!--n--><div class="row form-group"><!--n--><label for="lastname'+id+'">Last Name <span class="required">*</span></label><input name="lastname'+id+'" id="lastname'+id+'" required><!--n--></div><!--n--></div><!--n-->  <!--n--><div class="col-xs-12 col-sm-12"><!--n--><div class="row form-group nomargin "><!--n--><label for="age'+id+'">Age <span class="required">*</span></label><br /><!--n--><select name="age'+id+'" id="age'+id+'" required ><!--n-->      <option value="" disabled selected> - - - </option><!--n-->      <option value="1">1</option><!--n-->      <option value="2">2</option><!--n-->      <option value="3">3</option><!--n-->      <option value="4">4</option><!--n-->      <option value="5">5</option><!--n-->      <option value="6">6</option><!--n-->      <option value="7">7</option><!--n-->      <option value="8">8</option><!--n-->      <option value="9">9</option><!--n-->      <option value="10">10</option><!--n-->      <option value="11">11</option><!--n-->      <option value="12+">12+</option> <!--n-->    </select> <!--n--></div><!--n--></div><!--n--> <!--n--><!--n-->   <!--n--><!--n--><!--n--><!--n--></div>');
	
$('input:radio[name=type'+id+']').change(function() {
	checkChildChecked();
}); 
  var same = $("#all_same").val();
  if (same=="all_same"){// if all pass same, hide all buttons etc.
	hide_all_buttons()
  }


}










