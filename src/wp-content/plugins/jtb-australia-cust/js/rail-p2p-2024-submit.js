
function cvf_form_validate(element) {
    $('html, body').animate({scrollTop: $(element).offset().top-100}, 150);
    element.effect("highlight", { color: "#F2DEDE" }, 1500);
    //element.parent().effect('shake');
}

/*
2023 Agent switch off Oct 1st - 
search for @@@OCT

<span id="day5dep">You must book 5 business days before departure.</span>
<span id="day9dep">Booking unavailable for dates OCT-01 and beyond.</span>
message is on the Html 
*/


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



function changedate(){

var d1 = document.getElementById("departure_date_d").value;
var m1 = document.getElementById("departure_date_m").value;
var y1 = document.getElementById("departure_date_y").value;



if((d1=="x")||(m1=="x")||(y1=="x")||(d1==null)||(m1==null)||(y1==null)||(d1==undefined)||(m1==undefined)||(y1==undefined)){
	document.getElementById("day5dep").classList.add("red-text");
	document.getElementById("departure_date").value = "";
}else{
	if (date_is_valid_5_days()  ){
		document.getElementById("departure_date").value = d1 + "/" + m1 + "/" + y1  ;
		document.getElementById("day5dep").classList.remove("red-text");
			//date is after 5 days, now cut off on or after OCT 1  @@@OCT
			//if(      
//only AFTER 2023 DEC 26 (or including)
// Date of Departure limit  25DEC,   date of activation     29DEC

//no limit for dates 

				//document.getElementById("day9dep").classList.remove("red-text");
			
	}else{
		document.getElementById("day5dep").classList.add("red-text");
		document.getElementById("departure_date").value = "";
	}
}

/*
2023 Agent switch off Oct 1st - 
search for @@@OCT 
<span id="day5dep">You must book 5 business days before departure.</span>
<span id="day9dep">Booking unavailable for dates OCT-01 and beyond.</span>
*/



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


//Grand Final Day 2019 is TBD.


20230101,//nye
20230126,//au day
20230313,//labour
20230407,//easter
20230408,//easter
20230409,//easter
20230410,//easter

20230425,//anzac
20230612,//queen
20230929,//AFL
20231107,//cup day
20231225,//xmas
20231226,//xmas box

20240101,//nye
20240126,//au day
20240311,//labour
20240329,//easter
20240330,//easter
20240331,//easter
20240401,//easter
20240425,//anzac
20240610,//queen
20240929,//afl ???
20240905,//cup day
20241225,//xmas
20241226,//xmas box

20250101,//nye
20250127,//au day
20250310,//labour
20250418,//easter
20250419,//easter
20250420,//easter
20250421,//easter
20250425,//anzac
20250609,//queen
20250929,//afl ???
20251104,//cup day
20251225,//xmas
20251226,//xmas box

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






jQuery(document).ready(function($) {


$(".close").click(function() {
	if (document.getElementById("myModal-1")) { document.getElementById("myModal-1").style.display = "none"; }
 
	return true;

 
});

$("#state").change(function(e){
//&&&&&&&
if($('#state').val() == "Travel Agents"){
	$('#agentmessagered').css("visibility", "visible");
}else{
	$('#agentmessagered').css("visibility", "hidden");
}

});



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
	//checkChildChecked();
});


jQuery('#agent-jr-send').click(function(e){

var Number_of_customers = parseInt($('#numberOfPeople').val());//@@@@@
if((Number_of_customers==undefined)||(isNaN(Number_of_customers))||(Number_of_customers==null)||(Number_of_customers=="0")){
	Number_of_customers=0;
}


var nearest_branch = $('input[name="nearest_branch"]:checked').val();
var delivery_method = $('input[name="delivery_method"]:checked').val();

	if($('#agent_name').val() === '') {
        cvf_form_validate($('#agent_name'));
        console.log('Required: agent_name');
    }else if($('#agent_tel').val() === '') {
    	cvf_form_validate($('#agent_tel'));
    	console.log('Required: agent_tel');
	}else if($('#consultant_name').val() === '') {
	    cvf_form_validate($('#consultant_name'));
	    console.log('Required: consultant_name');
	}else if($('#state').val() === '') {
	    cvf_form_validate($('#state'));
	    console.log('Required: state');
	}else if($('#email').val() === '') {
        cvf_form_validate($('#email'));
        console.log('Required: email');
    }else if(($('#departure_date').val() === '')||($('#departure_date').val() === undefined)||($('#departure_date').val() === null)||($('#departure_date').val() === false)||($('#departure_date').val() === 'undefined')) {
	    cvf_form_validate($('#departure_date'));
	    console.log('Required: departure_date');
	} else if(!date_is_valid_5_days()  ){
		cvf_form_validate($('#departure_date_d'));
	    console.log('Required: departure_date_5_days 2');
	//cust-1

   }else {

 
nocusterrors = true;
var same_passes_check = $("#all_same").val();
for (var i = 0 ; i <= Number_of_customers; i++) {
	 if($( '#name'+String(i)).val() === '') {
		nocusterrors = false;
	    cvf_form_validate($( '#name'+String(i)));
	    console.log('Required: name'+String(i));
	}else if($( '#lastname'+String(i)).val() === '') {
		nocusterrors = false;
	    cvf_form_validate($( '#lastname'+String(i)));
	    console.log('Required: lastname'+String(i));
	}else if($( '#nat'+String(i)).val() === '') {
		nocusterrors = false;
	    cvf_form_validate($( '#nat'+String(i)));
	    console.log('Required: nat'+String(i));
	}else if(   ! ($('#type'+String(i)+'1').is(':checked') || $('#type'+String(i)+'2').is(':checked')) ){
        nocusterrors = false;
	    cvf_form_validate($( '#type'+String(i)+"1"));
	    console.log('Required: type'+String(i)+"1");
      }


	//validate title/ radio buttons ###
}


if(nocusterrors){
console.log('Required: all ok');
//hide the submit button 
//$("#submit_button_hide").html( " " );
$("#submit_button_hide").css('visibility', 'hidden'); 

jQuery('#jragent_response').html('<div style="width: 150px;"><svg class="spinner" width="65px" height="65px" viewBox="0 0 66 66" xmlns="http://www.w3.org/2000/svg"><circle class="path" fill="none" stroke-width="6" stroke-linecap="round" cx="33" cy="33" r="30"></circle></svg></div>');


//Build and send email.
e.preventDefault();

var agent_name =  jQuery('#agent_name').val();
var agent_tel =  jQuery('#agent_tel').val();
var consultant_name =  jQuery('#consultant_name').val();
var state =  jQuery('#state').val();
var email =  jQuery('#email').val();

var departure_date = jQuery('#departure_date').val();


var comments = jQuery('#comments').val();


comments = comments.replace("\r", "\n");
comments = comments.replace("\n\n", "\n");
comments = comments.replace("\n\n", "\n");
comments = comments.replace("\n", " - ");

var sim_card = "No";
//if($('#sim_card'  ).is(":checked")){
  //      sim_card = "Yes";
//}



var xxxx = "xxxx";

var email_gdocs_error = "";


var message =  "11__22img src='https://www.nx.jtbtravel.com.au/wp-content/uploads/2017/10/admin-section.png' 11__44'position:relative;float:right;display:inline-block;width:100px;height:auto;' /11__33";


message += "11__22b11__33Email ID:11__22/b11__3311__22br /11__33@@#SALES_ID#@@11__22br /11__33";

message += "11__22h211__33Sector Tickets:11__22/h211__33";
message += "11__22b11__33Name:11__22/b11__3311__22br /11__33"+agent_name.toUpperCase()+"11__22br /11__33";
message += "11__22b11__33Number of Travellers:11__22/b11__3311__22br /11__33"+consultant_name.toUpperCase()+"11__22br /11__33";
message += "11__22b11__33Email:11__22/b11__3311__22br /11__33"+email+"11__22br /11__33";
message += "11__22b11__33Phone:11__22/b11__3311__22br /11__33"+agent_tel+"11__22br /11__33";
message += "11__22b11__33State:11__22/b11__3311__22br /11__33"+state.toUpperCase()+"11__22br /11__33";


//message += "11__22h211__33Booking Details11__22/h211__33";

message += "11__22b11__33Order transaction time:11__22/b11__3311__22br /11__33@@#DATE_TIME#@@11__22br /11__33";
message += "11__22b11__33Date of departure from Australia:11__22/b11__3311__22br /11__33"+departure_date+"11__22br /11__33";

//message += "11__22b11__33Add $49 Data SIM:11__22/b11__3311__22br /11__33"+sim_card+"11__22br /11__33";





message += "11__22b11__33Comments:11__22/b11__3311__22br /11__33"+comments+"11__22br /11__33";


message += "11__22h211__33Trip Details11__22/h211__33";



for (var i = 0 ; i <= Number_of_customers; i++) {
	message += "11__22h311__33Trip "+String(parseInt(i+1))+":11__22/h311__33";
	message += "11__22b11__33From:11__22/b11__3311__22br /11__33"+$('#name'+String(i)).val().toUpperCase()+"11__22br /11__33";
	message += "11__22b11__33To:11__22/b11__3311__22br /11__33"+$('#lastname'+String(i)).val().toUpperCase()+"11__22br /11__33";
	message += "11__22b11__33Date / Time:11__22/b11__3311__22br /11__33"+$('#nat'+String(i)).val().toUpperCase()+"11__22br /11__33";
	var fromtooption = "Arrival";
	if( $('input[name="type'+String(i)+'"]:checked').val() == "Adult"){
		fromtooption = "Detarture";
	}
	message += "11__22b11__33Pass Type:11__22/b11__3311__22br /11__33"+fromtooption+"11__22br /11__33";

	// change adult to dep ? 




}//end loop

var wpfunction = "send_email_sector";
if(window.location.hash.substr(1) == "testzz"){
	//wpfunction = "send_email_test2";
}

	jQuery.ajax({
		url: 'https://www.nx.jtbtravel.com.au/wp-admin/admin-ajax.php',
		type: "POST",
		cache: false,
		data:{ 
		  action: wpfunction, 
		  name: agent_name,
		  email: email,
		  message: message,
		  recap: grecaptcha.getResponse(),
		  agency: agent_name
		    },
		success:function(res){
		   //jQuery('#jragent_response').html('<h3 class="green-text">JR Pass order submitted successfully</h3>');
		   //await sleep(250); //#####


if (res == "no-recap0"){
	jQuery('#jragent_response').html('<h3 class="red-text">Error: no recaptcha checked</h3>');
	$("#submit_button_hide").css('visibility', 'visible'); 

}else if (res == "wrong-recap0"){
	jQuery('#jragent_response').html('<h3 class="red-text">Error: recaptcha error</h3>');
	$("#submit_button_hide").css('visibility', 'visible'); 
}else{

jQuery('#jragent_response').html('<h3 class="green-text">Email sent</h3>');


/*
2023 - Google save Version 2 - we now use microsoft so the old form is lost 

view
https://docs.google.com/forms/d/e/1FAIpQLScXSVNyRlta5pwoTHUVPwr93slTKxDgphZf6mzjXjNOOEdSVg/viewform

edit
https://docs.google.com/forms/d/1Y9dW_-eoMnKpMZlDmbR1J9ZFM0tXLvsxbu7eFa2Sznk/edit

submit
https://docs.google.com/forms/u/0/d/e/1FAIpQLScXSVNyRlta5pwoTHUVPwr93slTKxDgphZf6mzjXjNOOEdSVg/formResponse

outbjtb GMAIL account 


Agency / Corp Name
441041617

Name
2093888220

Mobile
1844225605

Address 1
2132684829

Address 2
560745814

Suburb
1873859788

State
322042682

Postcode
1774943951


*/



//end google save


}






},error:function(res){
			//jQuery('#jragent_response').html('<h3 class="red-text">There was a submit error - please try again.</h3>');


				 jQuery('#jragent_response').html('<h3 class="red-text">Error: There was an email error - please try again - ' + res +  ' @@@ '+  JSON.stringify(res)  +' @@@   </h3>');
				
				
			$("#submit_button_hide").html( '<button type="submit" id="agent-jr-send" class="submit wpcf7-form-control wpcf7-submit btnLarge">Submit JR Pass Order</button>' );




		}
	}); //end ajax send








}
	 }//end if validation
   });//end page load
});//end




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

var Number_of_customers = parseInt($('#numberOfPeople').val());//@@@@@@
if((Number_of_customers==undefined)||(isNaN(Number_of_customers))||(Number_of_customers==null)||(Number_of_customers=="0")){
	Number_of_customers=0;
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

	$("#personbox"+String(id)).html('<!--n--><div class="personbox"><div class="closebox" id="closebox'+id+'" onclick="close2();"></div><!--n--><!--n--><h3>Trip '+idplusone+'</h3><!--n--><!--n--><!--n--><!--n--><div class="col-xs-12 col-sm-12"><!--n--><div class="row form-group"><!--n--><label for="name'+id+'">From - Station Name <span class="required">*</span></label><input name="name'+id+'" id="name'+id+'" required><!--n--></div><!--n--></div><!--n--><!--n--><!--n--><!--n--><div class="col-xs-12 col-sm-12"><!--n--><div class="row form-group"><!--n--><label for="lastname'+id+'">To - Station Name <span class="required">*</span></label><input name="lastname'+id+'" id="lastname'+id+'" required><!--n--></div><!--n--></div><!--n--><!--n--><div class="col-xs-12 col-sm-12"><!--n--><div class="row form-group"><!--n--><label for="nat'+id+'">Date / Time <span class="required">*</span></label><input name="nat'+id+'" id="nat'+id+'" required><!--n--></div><!--n--></div><!--n--><!--n--><div class="col-xs-12 col-sm-6"><!--n--><div class="row form-group"><!--n--><p><input name="type'+id+'" type="radio" id="type'+id+'1" value="Adult" class="radio" /> <label for="type'+id+'">Departure</label><!--n--><br /><br /><!--n--><input name="type'+id+'" type="radio" id="type'+id+'2" value="Child"   class="radio"  /> <label for="type'+id+'2">Arrival</label><!--n-->  </p><!--n--></div><!--n--></div><!--n--><!--n--><!--n--><input type="hidden" name="passes'+id+'" val="" id="passes'+id+'"><!--n--><span id="jrdisplay'+id+'"></span><!--n--><!--n--><!--n--><!--n--></div>');
$('input:radio[name=type'+id+']').change(function() {
	//checkChildChecked();
}); 
  var same = $("#all_same").val();
  if (same=="all_same"){// if all pass same, hide all buttons etc.
	hide_all_buttons()
  }


}




