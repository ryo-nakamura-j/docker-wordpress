
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

var d2 = document.getElementById("jr_use_date_d").value;
var m2 = document.getElementById("jr_use_date_m").value;
var y2 = document.getElementById("jr_use_date_y").value;

if((d1=="x")||(m1=="x")||(y1=="x")||(d1==null)||(m1==null)||(y1==null)||(d1==undefined)||(m1==undefined)||(y1==undefined)){
	document.getElementById("day5dep").classList.add("red-text");
	document.getElementById("departure_date").value = "";
}else{
	if (date_is_valid_5_days()  ){
		document.getElementById("departure_date").value = d1 + "/" + m1 + "/" + y1  ;
		document.getElementById("day5dep").classList.remove("red-text");
			//date is after 5 days, now cut off on or after OCT 1  @@@OCT
			if(    (y1>2023)  || (y2>2023)   
//only 2023 
// Date of Departure limit  25DEC,   date of activation     29DEC

 || (     (y1==2023)  && (y2==2023) &&  (  (m1=="Dec") && (d1>25) )  )
 || (     (y1==2023)  && (y2==2023) &&  (  (m2=="Dec") && (d2>29) )  )

			){ //|| (m1  ==  "Oct")|| (m1  ==  "Nov")|| (m1  ==  "Dec")  // also day filter x2
				console.log('error after cutoff ');
				document.getElementById("day9dep").classList.add("red-text");
				document.getElementById("departure_date").value = "";
			}else{
				document.getElementById("day9dep").classList.remove("red-text");
			}
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

if((d2=="x")||(m2=="x")||(y2=="x")||(d2==null)||(m2==null)||(y2==null)||(d2==undefined)||(m2==undefined)||(y2==undefined)){
	document.getElementById("jr_use_date").value = "";
}else{
	document.getElementById("jr_use_date").value = d2 + "/" + m2 + "/" + y2  ;
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
	}else if($('#address1').val() === '') {
	    cvf_form_validate($('#address1'));
	    console.log('Required: address1');
	}else if($('#consultant_name').val() === '') {
	    cvf_form_validate($('#consultant_name'));
	    console.log('Required: consultant_name');
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
    }else if((delivery_method === '')||(delivery_method === undefined)||(delivery_method === null)||(delivery_method === false)||(delivery_method === 'undefined')) {
	    cvf_form_validate($('#delivery_method'));
	    console.log('Required: delivery_method');
	}else if(($('#departure_date').val() === '')||($('#departure_date').val() === undefined)||($('#departure_date').val() === null)||($('#departure_date').val() === false)||($('#departure_date').val() === 'undefined')) {
	    cvf_form_validate($('#departure_date'));
	    console.log('Required: departure_date');
	}else if(($('#jr_use_date').val() === '')||($('#jr_use_date').val() === undefined)||($('#jr_use_date').val() === null)||($('#jr_use_date').val() === false)||($('#jr_use_date').val() === 'undefined')) {
	    cvf_form_validate($('#jr_use_date'));
	    console.log('Required: jr_use_date');
	} else if(!date_is_valid_5_days()  ){
		cvf_form_validate($('#departure_date_d'));
	    console.log('Required: departure_date_5_days 2');
	//cust-1

   }else {

 
nocusterrors = true;
var same_passes_check = $("#all_same").val();
for (var i = 0 ; i <= Number_of_customers; i++) {
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
	}else if($( '#nat'+String(i)).val() === '') {
		nocusterrors = false;
	    cvf_form_validate($( '#nat'+String(i)));
	    console.log('Required: nat'+String(i));
	}else if($( '#passes'+String(i)).val() === '') {
		if( (i==0) || (same_passes_check == "all_not_same" ) ){
			nocusterrors = false;
		    cvf_form_validate($( '#passes'+String(i)));
		    console.log('Required: passes'+String(i));
		}
	}else if(   ! ($('#type'+String(i)+'1').is(':checked') || $('#type'+String(i)+'2').is(':checked')) ){
        nocusterrors = false;
	    cvf_form_validate($( '#type'+String(i)+"1"));
	    console.log('Required: type'+String(i)+"1");
      }

	if($("#type"+String(i)+"2").is(":checked")){
		if($( '#dob'+String(i)).val() === '') {
			nocusterrors = false;
		    cvf_form_validate($( '#type'+String(i)+"2"));
		    console.log('Required: child DOB');
		}
	}



	//validate title/ radio buttons ###
}

//var users_eligible = jQuery('#users_eligible').val();
if(!document.getElementById('users_eligible').checked){
        nocusterrors = false;
	    cvf_form_validate($("#users_eligible"));
	    console.log('Required: users_eligible');
}

if(!document.getElementById('names_double_check').checked){
        nocusterrors = false;
	    cvf_form_validate($("#names_double_check"));
	    console.log('Required: names_double_check');
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
var address1 =  jQuery('#address1').val();
var consultant_name =  jQuery('#consultant_name').val();
var suburb =  jQuery('#suburb').val();
var state =  jQuery('#state').val();
var post_code =  jQuery('#post_code').val();
var email =  jQuery('#email').val();

var licence_no =  jQuery('#licence_no').val();
var address2 =  jQuery('#address2').val();

var departure_date = jQuery('#departure_date').val();
var jr_use_date = jQuery('#jr_use_date').val();


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


message += "11__22b11__33Sales ID:11__22/b11__3311__22br /11__33@@#SALES_ID#@@11__22br /11__33";

message += "11__22h211__33Agent JR Pass booking:11__22/h211__33";
message += "11__22b11__33Consultant name:11__22/b11__3311__22br /11__33"+consultant_name.toUpperCase()+"11__22br /11__33";
message += "11__22b11__33Agent Name:11__22/b11__3311__22br /11__33"+agent_name.toUpperCase()+"11__22br /11__33";
message += "11__22b11__33License No:11__22/b11__3311__22br /11__33"+licence_no.toUpperCase()+"11__22br /11__33";
message += "11__22b11__33Email:11__22/b11__3311__22br /11__33"+email+"11__22br /11__33";
message += "11__22b11__33Agent Contact tel:11__22/b11__3311__22br /11__33"+agent_tel+"11__22br /11__33";
message += "11__22b11__33Agency address (Australia only):11__22/b11__3311__22br /11__33"+address1.toUpperCase()+"11__22br /11__33";
if(address2!=""){ message += address2.toUpperCase()+"11__22br /11__33"; }
message += "11__22b11__33Suburb:11__22/b11__3311__22br /11__33"+suburb.toUpperCase()+"11__22br /11__33";
message += "11__22b11__33State:11__22/b11__3311__22br /11__33"+state.toUpperCase()+"11__22br /11__33";
message += "11__22b11__33Post Code:11__22/b11__3311__22br /11__33"+post_code+"11__22br /11__33";

message += "11__22h211__33Booking Details11__22/h211__33";

message += "11__22b11__33Order transaction time:11__22/b11__3311__22br /11__33@@#DATE_TIME#@@11__22br /11__33";
message += "11__22b11__33Delivery method:11__22/b11__3311__22br /11__33"+delivery_method+"11__22br /11__33";
message += "11__22b11__33Date of departure from Australia:11__22/b11__3311__22br /11__33"+departure_date+"11__22br /11__33";
message += "11__22b11__33Intended date of JR Pass use:11__22/b11__3311__22br /11__33"+jr_use_date+"11__22br /11__33";

//message += "11__22b11__33Add $49 Data SIM:11__22/b11__3311__22br /11__33"+sim_card+"11__22br /11__33";



message += "11__22b11__33Ensure users are eligible:11__22/b11__3311__22br /11__33Yes11__22br /11__33";
message += "11__22b11__33Ensure names are as passport:11__22/b11__3311__22br /11__33Yes11__22br /11__33";


message += "11__22b11__33Comments:11__22/b11__3311__22br /11__33"+comments+"11__22br /11__33";


message += "11__22h211__33Customer Details11__22/h211__33";



for (var i = 0 ; i <= Number_of_customers; i++) {
	message += "11__22h311__33Person "+String(parseInt(i+1))+":11__22/h311__33";
	message += "11__22b11__33Full Name:11__22/b11__3311__22br /11__33" + $('#title'+String(i)).val() + " " + $('#name'+String(i)).val().toUpperCase() + " " + $('#midname'+String(i)).val().toUpperCase() + " " + $('#lastname'+String(i)).val().toUpperCase()  +  "11__22br /11__33";
	message += "11__22b11__33Title:11__22/b11__3311__22br /11__33"+$('#title'+String(i)).val()+"11__22br /11__33";
	message += "11__22b11__33First Name:11__22/b11__3311__22br /11__33"+$('#name'+String(i)).val().toUpperCase()+"11__22br /11__33";
	message += "11__22b11__33Middle Name:11__22/b11__3311__22br /11__33"+$('#midname'+String(i)).val().toUpperCase()+"11__22br /11__33";
	message += "11__22b11__33Last Name:11__22/b11__3311__22br /11__33"+$('#lastname'+String(i)).val().toUpperCase()+"11__22br /11__33";
	message += "11__22b11__33Nationality:11__22/b11__3311__22br /11__33"+$('#nat'+String(i)).val().toUpperCase()+"11__22br /11__33";
	message += "11__22b11__33Pass Type:11__22/b11__3311__22br /11__33"+$('input[name="type'+String(i)+'"]:checked').val()+"11__22br /11__33";

	if($("#type"+String(i)+"2").is(":checked")){
		message += "11__22b11__33Child DOB:11__22/b11__3311__22br /11__33"+$( '#dob'+String(i)).val()+"11__22br /11__33";
	}

var same_passes = $("#all_same").val();
if(same_passes=="all_same" && (i!=0) ){
	message += "11__22br /11__3311__22b11__33JR Passes Selected:11__22/b11__3311__22br /11__33All passes same as person 1.11__22br /11__3311__22br /11__33";
}else{
	message += "11__22br /11__3311__22b11__33JR Passes Selected:11__22/b11__3311__22br /11__33"+processPassesReadableEmail($('#passes'+String(i)).val())+"11__22br /11__3311__22br /11__33";

}


}//end loop

var wpfunction = "send_email";
if(window.location.hash.substr(1) == "testzz"){
	wpfunction = "send_email_test2";
}

	jQuery.ajax({
		url: 'https://www.nx.jtbtravel.com.au/wp-admin/admin-ajax.php',
		type: "POST",
		cache: false,
		data:{ 
		  action: wpfunction, 
		  name: consultant_name,
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


//start google save
//var gsh_data = "entry.1457614519="+jQuery.parseJSON(JSON.stringify(res)) +"&" + //generate_google_data();  //JSON.parse(res)
var field1 = jQuery("#agent_name").val().toUpperCase();
var field2 = jQuery("#consultant_name").val().toUpperCase();
var field3 = jQuery("#agent_tel").val().toUpperCase();
var field4 = jQuery("#address1").val().toUpperCase();//addy 1 
var field5 = jQuery("#address2").val().toUpperCase();
var field6 = jQuery("#suburb").val().toUpperCase();
var field7 = jQuery("#state").val().toUpperCase();
var field8 = jQuery("#post_code").val().toUpperCase();

jQuery.ajax({
    url: "https://docs.google.com/forms/u/0/d/e/1FAIpQLScXSVNyRlta5pwoTHUVPwr93slTKxDgphZf6mzjXjNOOEdSVg/formResponse",
    //data: $(this).serialize(),
    //data: gsh_data,
	data: { //fields import from html form 
"entry.441041617": field1,
"entry.2093888220": field2,
"entry.1844225605": field3,
"entry.2132684829": field4,
"entry.560745814": field5,
"entry.1873859788": field6,
"entry.322042682": field7,
"entry.1774943951": field8
	},
    type: "POST",
    dataType: "xml",
    success: function(data) {
    	//await sleep(750);
        console.log('Submission successful');

	jQuery('#jragent_response').html('<h3 class="green-text">JR Pass order submitted successfully</h3><FORM><INPUT TYPE="button" onClick="history.go(0)" VALUE="New JR Pass Booking" class="btnLarge"></FORM>');
	$("#contact_form").hide( "fast" );

    },
    error: function(xhr, status, error) {
    	if (error.length>0){
    		console.log('Submission failed: ' + error);

            jQuery('#jragent_response').html('<h3 class="red-text">Error: There was a google database error - please try again - '+ error +' </h3>');
            $("#submit_button_hide").html( '<button type="submit" id="agent-jr-send" class="submit wpcf7-form-control wpcf7-submit btnLarge">Submit JR Pass Order</button>' );
    	}else{
    		//await sleep(750);
            console.log('Submission successful');
	jQuery('#jragent_response').html('<h3 class="green-text">JR Pass order submitted successfully</h3><FORM><INPUT TYPE="button" onClick="history.go(0)" VALUE="New JR Pass Booking" class="btnLarge"></FORM>');
	$("#contact_form").hide( "fast" );
    	}
    }
});
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



function generate_google_data(){



var Number_of_customers = parseInt($('#numberOfPeople').val());//@@@@@
if((Number_of_customers==undefined)||(isNaN(Number_of_customers))||(Number_of_customers==null)||(Number_of_customers=="0")){
	Number_of_customers=0;
}

var sim_card = "No";
//if(document.getElementById('sim_card').checked){
  //      sim_card = "Yes";
//}



var agent_name =  jQuery('#agent_name').val();
var agent_tel =  jQuery('#agent_tel').val();
var address1 =  jQuery('#address1').val();
var consultant_name =  jQuery('#consultant_name').val();
var state =  jQuery('#state').val();
var suburb = jQuery('#suburb').val();
var post_code =  jQuery('#post_code').val();
var email =  jQuery('#email').val();

var licence_no =  jQuery('#licence_no').val();
var address2 =  jQuery('#address2').val();

var departure_date = jQuery('#departure_date').val();
var jr_use_date = jQuery('#jr_use_date').val();

var comments = jQuery('#comments').val();


comments = comments.replace("\r", "\n");
comments = comments.replace("\n\n", "\n");
comments = comments.replace("\n\n", "\n");
comments = comments.replace("\n", " - ");

var xxxx = "xxxx";

var email_gdocs_error = "";


var sim_card = "No";
//if($('#sim_card'  ).is(":checked")){
  //      sim_card = "Yes";
//}


var nearest_branch = $('input[name="nearest_branch"]:checked').val();
var delivery_method = $('input[name="delivery_method"]:checked').val();

	//start google save
	//data-string-get-format
	//to-do remove & symbol, = symbol
	//entry.1457614519
	//entry.1457614519=test123
	var gsh_data="entry.1484458029="+String(agent_name.toUpperCase());//Agent Name
	gsh_data=gsh_data+"&entry.522123169="+String(licence_no.toUpperCase());//License No
	gsh_data=gsh_data+"&entry.1968756785="+String(agent_tel);//Agent Contact tel
	gsh_data=gsh_data+"&entry.997379294="+String(consultant_name.toUpperCase());//Consultant name
	gsh_data=gsh_data+"&entry.1682905874="+String(email);//E-mail address
	gsh_data=gsh_data+"&entry.1324372657="+String(address1.toUpperCase());//Agency address
	if(address2!=""){
	gsh_data=gsh_data+"; "+String(address2.toUpperCase());//Agency address2
	}
	gsh_data=gsh_data+"&entry.1053576936="+String(state.toUpperCase());//State
	gsh_data=gsh_data+"&entry.1337512550="+String(suburb.toUpperCase());//State
	gsh_data=gsh_data+"&entry.1754451011="+String(post_code);//Post Code
	//gsh_data=gsh_data+"&="+String();//Nearest JTB branch
	gsh_data=gsh_data+"&entry.1570014780="+String(delivery_method);//Delivery method
	gsh_data=gsh_data+"&entry.1180778698="+String(departure_date);//Departure Date


	gsh_data=gsh_data+"&entry.1591743473="+String(jr_use_date);//Date of First Use
//gsh_data=gsh_data+"&entry.556273524="+String(sim_card);//SIM Card
gsh_data=gsh_data+"&entry.842904639=Yes";//eligible confirm
	gsh_data=gsh_data+"&entry.616473624=Yes";//name confirm


		gsh_data=gsh_data+"&entry.248790125="+String(comments);//comments
 

	var cust_details = new Array("entry.2142454256","entry.1518023627","entry.682641894","entry.1606414213","entry.1634704866","entry.2030717374","entry.430801779","entry.131994499","entry.912169756","entry.352507096"  );
	var cust_JR_Passes = new Array("entry.1727451678","entry.1478062621","entry.1414832344","entry.512581323","entry.936939475","entry.1360171315","entry.1975793694","entry.1355381080","entry.453746953","entry.1466170721" );
	var cust_details_11 = "";
	var cust_JR_Passes_11 = "";

for (var i = 0 ; i <= Number_of_customers; i++) {
		//name, nationality, type

	var childDOB = "";

	if($("#type"+String(i)+"2").is(":checked")){
		childDOB = " [Child-DOB] "+$( '#dob'+String(i)).val()+" ";
	}


	if(i<10){
		gsh_data=gsh_data+"&"+cust_details[i]+"=[T] "+$('#title'+String(i)).val() +" [FN] "+$('#name'+String(i)).val().toUpperCase() +" [MN] "+$('#midname'+String(i)).val().toUpperCase() +" [LN] "+$('#lastname'+String(i)).val().toUpperCase() +" [AND] "+$('#nat'+String(i)).val().toUpperCase() + " - "+$('input[name="type'+String(i)+'"]:checked').val() + childDOB;
	}else{
		cust_details_11 = cust_details_11 + "[Person " +String(parseInt(i+1))+ "] "+cust_details[i]+" [T] "+$('#title'+String(i)).val() +" [FN] "+$('#name'+String(i)).val().toUpperCase() +" [MN] "+$('#midname'+String(i)).val().toUpperCase() +" [LN] "+$('#lastname'+String(i)).val().toUpperCase() +" [AND] "+$('#nat'+String(i)).val().toUpperCase() + " - "+$('input[name="type'+String(i)+'"]:checked').val() + " " + childDOB + " ";
	}
		
	var same_passes = $("#all_same").val();
	if(same_passes=="all_same" && (i!=0) ){
		if(i<10){
			gsh_data=gsh_data+"&"+cust_JR_Passes[i]+"=All passes same as Person_1";
		}else{
			cust_JR_Passes_11 = "All passes same as Person_1";
		}
	}else{
		if(i<10){
			gsh_data=gsh_data+"&"+cust_JR_Passes[i]+"="+processPassesReadableGoogle($('#passes'+String(i)).val());
		}else{
			cust_JR_Passes_11 = cust_JR_Passes_11 +  "[Person " +String(parseInt(i+1))+ "] "+processPassesReadableGoogle($('#passes'+String(i)).val())+" ";
		}
		
	}

}//end loop

	gsh_data=gsh_data+"&entry.1533728143="+String(cust_details_11);//11
	gsh_data=gsh_data+"&entry.2091682587="+String(cust_JR_Passes_11);//11

	return gsh_data;
}

//@@@
function checkChildChecked(){

var Number_of_customers = parseInt($('#numberOfPeople').val());//@@@@@@
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

	$("#personbox"+String(id)).html('<!--n--><div class="personbox"><div class="closebox" id="closebox'+id+'" onclick="close2();"></div><!--n--><!--n--><h3>Person '+idplusone+'</h3><p  class="red-text"><i class="material-icons" >error_outline</i> Note: Customer name must be entered as it appears on their passport, including any middle names.</p><!--n--><!--n--><div class="col-xs-12 col-sm-12"><!--n--><div class="row form-group" ><!--n-->    <select name="title'+id+'" id="title'+id+'" required ><!--n-->      <option value="" disabled selected>Title</option><!--n-->      <option value="MR">Mr</option><!--n-->      <option value="MRS">Mrs</option><!--n-->      <option value="MS">Ms</option><!--n-->    </select> <span class="required">*</span><!--n--></div></div><!--n--><!--n--><!--n--><!--n--><div class="col-xs-12 col-sm-12"><!--n--><div class="row form-group"><!--n--><label for="name'+id+'">First Name <span class="required">*</span></label><input name="name'+id+'" id="name'+id+'" required><!--n--></div><!--n--></div><!--n--><!--n--><div class="col-xs-12 col-sm-12"><!--n--><div class="row form-group"><!--n--><label for="midname'+id+'">Middle Name</label><input name="midname'+id+'" id="midname'+id+'" required><!--n--></div><!--n--></div><!--n--><!--n--><div class="col-xs-12 col-sm-12"><!--n--><div class="row form-group"><!--n--><label for="lastname'+id+'">Last Name <span class="required">*</span></label><input name="lastname'+id+'" id="lastname'+id+'" required><!--n--></div><!--n--></div><!--n--><!--n--><div class="col-xs-12 col-sm-12"><!--n--><div class="row form-group"><!--n--><label for="nat'+id+'">Nationality <span class="required">*</span></label><input name="nat'+id+'" id="nat'+id+'" required><!--n--></div><!--n--></div><!--n--><!--n--><div class="col-xs-12 col-sm-6"><!--n--><div class="row form-group"><!--n--> <label for="type'+id+'1">Pass type <span class="required">*</span></label><!--n--><br /><br /><p><input name="type'+id+'" type="radio" id="type'+id+'1" value="Adult" class="radio" /> <label for="type'+id+'">Adult (12 yrs +)</label>  <!--n-->  </p><!--n--></div><!--n--></div><!--n--><div class="col-xs-12 col-sm-6"><!--n--><div class="row form-group"><br /><br /><!--n--> <p><input name="type'+id+'" type="radio" id="type'+id+'2" value="Child"   class="radio"  /> <label for="type'+id+'2">Child (6-11 yrs)</label>   <!--n--> </p><!--n--></div><!--n--></div><!--n--><div class="dob'+id+'container" id="dob'+id+'container"></div><!--n--><input type="hidden" name="passes'+id+'" val="" id="passes'+id+'"><!--n--><!--n--><div class="col-xs-12 col-sm-12"><!--n--><div  id="jrbutton'+id+'" class="row form-group"><!--n--><button onclick="selectPasses('+id+');"  class="wpcf7-form-control wpcf7-submit btnLarge">Select JR Passes</button><!--n--></div><!--n--></div><!--n--><!--n--><div class="col-xs-12 col-sm-12"><!--n--><div class="row form-group"><!--n--><div class="jrdisplay" id="jrdisplay'+id+'"><p><span>JR Passes Selected</span><br /><br /><!--n--><span id="jrdisplay'+id+'"></span><!--n--></p><!--n--></div><!--n--></div><!--n--></div><!--n--><!--n--><!--n--></div>');
$('input:radio[name=type'+id+']').change(function() {
	checkChildChecked();
}); 
  var same = $("#all_same").val();
  if (same=="all_same"){// if all pass same, hide all buttons etc.
	hide_all_buttons()
  }


}











 







/*

BACKUP OF NEW PERSON CODE

<div class="personbox">
<h3>Person '+idplusone+'</h3>
<p  class="red-text"><i class="material-icons" >error_outline</i> Name must be entered as it appears on your passport, including any middle names.</p>

<div class="col-xs-12 col-sm-12">
<div class="row form-group" >
    <select name="title'+id+'" id="title'+id+'" required >
      <option value="" disabled selected>Title</option>
      <option value="MR">Mr</option>
      <option value="MRS">Mrs</option>
      <option value="MS">Ms</option>
    </select> <span class="required">*</span>
</div></div>

<div class="col-xs-12 col-sm-12">
<div class="row form-group">
<label for="name'+id+'">First Name <span class="required">*</span></label><input name="name'+id+'" id="name'+id+'" required>
</div>
</div>

<div class="col-xs-12 col-sm-12">
<div class="row form-group">
<label for="midname'+id+'">Middle Name</label><input name="midname'+id+'" id="midname'+id+'" required>
</div>
</div>

<div class="col-xs-12 col-sm-12">
<div class="row form-group">
<label for="lastname'+id+'">Last Name <span class="required">*</span></label><input name="lastname'+id+'" id="lastname'+id+'" required>
</div>
</div>

<div class="col-xs-12 col-sm-12">
<div class="row form-group">
<label for="nat'+id+'">Nationality <span class="required">*</span></label><input name="nat'+id+'" id="nat'+id+'" required>
</div>
</div>

<div class="col-xs-12 col-sm-6">
<div class="row form-group">
 <label for="type'+id+'1">Pass type <span class="required">*</span></label>
<br /><br /><p><input name="type'+id+'" type="radio" id="type'+id+'1" value="Adult" class="radio" /> <label for="type'+id+'">Adult (12 yrs +)</label>  
  </p>
</div>
</div>
<div class="col-xs-12 col-sm-6">
<div class="row form-group"><br /><br />
 <p><input name="type'+id+'" type="radio" id="type'+id+'2" value="Child"   class="radio"  /> <label for="type'+id+'2">Child (6-11 yrs)</label>   
 </p>
</div>
</div>

<input type="hidden" name="passes'+id+'" val="" id="passes'+id+'">

<div class="col-xs-12 col-sm-12">
<div class="row form-group">
<button onclick="selectPasses('+id+');"  class="wpcf7-form-control wpcf7-submit btnLarge" >Select JR Passes</button>
</div>
</div>

<div class="col-xs-12 col-sm-12">
<div class="row form-group">
<div class="jrdisplay" id="jrdisplay'+id+'"><p><span>JR Passes Selected</span><br /><br />
<span id="jrdisplay'+id+'"></span>
</p>
</div>
</div>
</div>

</div>


*/






//original
/*
function checkdate5days(){
var d1 = document.getElementById("departure_date_d").value;
var m1 = document.getElementById("departure_date_m").value;
var y1 = document.getElementById("departure_date_y").value;
var d = new Date() ; 
var today = new Date( (d.getFullYear() + "-" + (   d.getMonth() +1 ) + "-" + d.getDate() )  ).getTime() / 1000;
var input = new Date( (y1 + "-" + m1 + "-" + d1 )  ).getTime() / 1000;

//seconds in a day 86400
//5 days add to today, 
//if date less than, eror
today = today + (5 * 86400 ) -50000;


console.log('time: input ' + input);
console.log('time: today ' + today);

if(input < today){
	return false;
}else{
	return true;
}

}
*/