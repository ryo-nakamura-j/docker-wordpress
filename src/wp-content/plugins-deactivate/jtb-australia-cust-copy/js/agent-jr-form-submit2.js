
function cvf_form_validate(element) {
    $('html, body').animate({scrollTop: $(element).offset().top-100}, 150);
    element.effect("highlight", { color: "#F2DEDE" }, 1500);
    //element.parent().effect('shake');
}


jQuery(document).ready(function($) {



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



jQuery('#agent-jr-send').click(function(e){

var Number_of_customers = parseInt($('#numberOfPeople').val());//@@@@@@
if((Number_of_customers==undefined)||(isNaN(Number_of_customers))||(Number_of_customers==null)||(Number_of_customers=="0")){
	Number_of_customers=0;
}


var nearest_branch = $('input[name="nearest_branch"]:checked').val();
var delivery_method = $('input[name="delivery_method"]:checked').val();

	if($('#agent_name').val() === '') {
        cvf_form_validate($('#agent_name'));
    }else if($('#agent_tel').val() === '') {
    	cvf_form_validate($('#agent_tel'));
	}else if($('#address1').val() === '') {
	    cvf_form_validate($('#address1'));
	}else if($('#consultant_name').val() === '') {
	    cvf_form_validate($('#consultant_name'));
	}else if($('#state').val() === '') {
	    cvf_form_validate($('#state'));
	}else if($('#post_code').val() === '') {
	    cvf_form_validate($('#post_code'));
	}else if($('#email').val() === '') {
        cvf_form_validate($('#email'));
    }else if((delivery_method === '')||(delivery_method === undefined)||(delivery_method === null)||(delivery_method === false)||(delivery_method === 'undefined')) {
	    cvf_form_validate($('#delivery_method'));
	}else if(($('#departure_date').val() === '')||($('#departure_date').val() === undefined)||($('#departure_date').val() === null)||($('#departure_date').val() === false)||($('#departure_date').val() === 'undefined')) {
	    cvf_form_validate($('#departure_date'));
	}else if(($('#jr_use_date').val() === '')||($('#jr_use_date').val() === undefined)||($('#jr_use_date').val() === null)||($('#jr_use_date').val() === false)||($('#jr_use_date').val() === 'undefined')) {
	    cvf_form_validate($('#jr_use_date'));
	}
	//cust-1

   else {

 
nocusterrors = true;
for (var i = 0 ; i <= Number_of_customers; i++) {
	if(($('#title'+i).val() === '')||($('#title'+i).val() === "Title")||($('#title'+i).val() === null)) {
		nocusterrors = false;
	    cvf_form_validate($( '#title'+i));
	}else if($( '#name'+String(i)).val() === '') {
		nocusterrors = false;
	    cvf_form_validate($( '#name'+String(i)));
	}else if($( '#lastname'+String(i)).val() === '') {
		nocusterrors = false;
	    cvf_form_validate($( '#lastname'+String(i)));
	}else if($( '#nat'+String(i)).val() === '') {
		nocusterrors = false;
	    cvf_form_validate($( '#nat'+String(i)));
	}else if($( '#passes'+String(i)).val() === '') {
		nocusterrors = false;
	    cvf_form_validate($( '#passes'+String(i)));
	}else if(  $('input[name="type'+String(i)+'"]:checked').val()==null){
        nocusterrors = false;
	    cvf_form_validate($( '#type'+String(i)));
      }
	//validate title/ radio buttons
}





if(nocusterrors){


jQuery('#jragent_response').html('<div style="width: 150px;"><svg class="spinner" width="65px" height="65px" viewBox="0 0 66 66" xmlns="http://www.w3.org/2000/svg"><circle class="path" fill="none" stroke-width="6" stroke-linecap="round" cx="33" cy="33" r="30"></circle></svg></div>');


//Build and send email.
e.preventDefault();

var agent_name =  jQuery('#agent_name').val();
var agent_tel =  jQuery('#agent_tel').val();
var address1 =  jQuery('#address1').val();
var consultant_name =  jQuery('#consultant_name').val();
var state =  jQuery('#state').val();
var post_code =  jQuery('#post_code').val();
var email =  jQuery('#email').val();

var licence_no =  jQuery('#licence_no').val();
var address2 =  jQuery('#address2').val();

var departure_date = jQuery('#departure_date').val();
var jr_use_date = jQuery('#jr_use_date').val();


var departure_date = jQuery('#departure_date').val();
var jr_use_date = jQuery('#jr_use_date').val();

var xxxx = "xxxx";

var email_gdocs_error = "";

var message =  "[[img src='https://www.nx.jtbtravel.com.au/wp-content/themes/jtb-australia/images/logo.png' style='position:relative;float:right;display:inline-block;width:100px;height:auto;' /]]";
message += "[[h2]]Agent JR Pass booking:[[/h2]]";
message += "[[b]]Consultant name:[[/b]][[br /]]"+consultant_name.toUpperCase()+"[[br /]]";
message += "[[b]]Agent Name:[[/b]][[br /]]"+agent_name.toUpperCase()+"[[br /]]";
message += "[[b]]License No:[[/b]][[br /]]"+licence_no.toUpperCase()+"[[br /]]";
message += "[[b]]Email:[[/b]][[br /]]"+email+"[[br /]]";
message += "[[b]]Agent Contact tel:[[/b]][[br /]]"+agent_tel+"[[br /]]";
message += "[[b]]Agency address (Australia only):[[/b]][[br /]]"+address1.toUpperCase()+"[[br /]]";
if(address2!=""){ message += address2.toUpperCase()+"[[br /]]"; }
message += "[[b]]State:[[/b]][[br /]]"+state.toUpperCase()+"[[br /]]";
message += "[[b]]Post Code:[[/b]][[br /]]"+post_code+"[[br /]]";

message += "[[h2]]Booking Details[[/h2]]";
message += "[[b]]Sales ID:[[/b]][[br /]]@@#SALES_ID#@@[[br /]]";
message += "[[b]]Order transaction time:[[/b]][[br /]]@@#DATE_TIME#@@[[br /]]";
message += "[[b]]Delivery method:[[/b]][[br /]]"+delivery_method+"[[br /]]";
message += "[[b]]Date of departure from Australia:[[/b]][[br /]]"+departure_date+"[[br /]]";
message += "[[b]]Intended date of JR Pass use:[[/b]][[br /]]"+jr_use_date+"[[br /]]";




message += "[[h2]]Customer Details[[/h2]]";



for (var i = 0 ; i <= Number_of_customers; i++) {
	message += "[[h3]]Person "+String(parseInt(i+1))+":[[/h3]]";
	message += "[[b]]Title:[[/b]][[br /]]"+$('#title'+String(i)).val()+"[[br /]]";
	message += "[[b]]First Name:[[/b]][[br /]]"+$('#name'+String(i)).val().toUpperCase()+"[[br /]]";
	message += "[[b]]Middle Name:[[/b]][[br /]]"+$('#midname'+String(i)).val().toUpperCase()+"[[br /]]";
	message += "[[b]]Last Name:[[/b]][[br /]]"+$('#lastname'+String(i)).val().toUpperCase()+"[[br /]]";
	message += "[[b]]Nationality:[[/b]][[br /]]"+$('#nat'+String(i)).val().toUpperCase()+"[[br /]]";
	message += "[[b]]Pass Type:[[/b]][[br /]]"+$('input[name="type'+String(i)+'"]:checked').val()+"[[br /]]";
var same_passes = $("#all_same").val();
if(same_passes=="all_same" && (i!=0) ){
	message += "[[br /]][[b]]JR Passes Selected:[[/b]][[br /]]All passes same as person 1.[[br /]][[br /]]";
}else{
	message += "[[br /]][[b]]JR Passes Selected:[[/b]][[br /]]"+processPassesReadableEmail($('#passes'+String(i)).val())+"[[br /]][[br /]]";

}


}


	jQuery.ajax({
		url: 'https://www.nx.jtbtravel.com.au/wp-admin/admin-ajax.php',
		type: "POST",
		cache: false,
		data:{ 
		  action: 'send_email2', 
		  name: 'bbb',
		  email: 'ben@pushka.com',
		  message: message,
		    },
		success:function(res){ 
		   //jQuery('#jragent_response').html('<h3 class="green-text">JR Pass order submitted successfully</h3>');
		   await sleep(250);
		},error:function(res){
			//jQuery('#jragent_response').html('<h3 class="red-text">There was a submit error - please try again.</h3>');
			email_gdocs_error = email_gdocs_error + "There was an email error - please try again."
		}
	}); //end ajax send


	//start google save
//data-string-get-format
//to-do remove & symbol, = symbol
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
gsh_data=gsh_data+"&entry.1754451011="+String(post_code);//Post Code
//gsh_data=gsh_data+"&="+String();//Nearest JTB branch
gsh_data=gsh_data+"&entry.1570014780="+String(delivery_method);//Delivery method
gsh_data=gsh_data+"&entry.1180778698="+String(departure_date);//Departure Date
gsh_data=gsh_data+"&entry.1591743473="+String(jr_use_date);//Date of First Use

var cust_details = new Array("entry.2142454256","entry.1518023627","entry.682641894","entry.1606414213","entry.1634704866","entry.2030717374","entry.430801779","entry.131994499","entry.912169756","entry.352507096"  );
var cust_JR_Passes = new Array("entry.1727451678","entry.1478062621","entry.1414832344","entry.512581323","entry.936939475","entry.1360171315","entry.1975793694","entry.1355381080","entry.453746953","entry.1466170721" );
var cust_details_11 = "";
var cust_JR_Passes_11 = "";

for (var i = 0 ; i <= Number_of_customers; i++) {
	//name, nationality, type
if(i<10){
	gsh_data=gsh_data+"&"+cust_details[i]+"=[t] "+$('#title'+String(i)).val() +" [fn] "+$('#name'+String(i)).val().toUpperCase() +" [mn] "+$('#midname'+String(i)).val().toUpperCase() +" [ln] "+$('#lastname'+String(i)).val().toUpperCase() +" - "+$('#nat'+String(i)).val().toUpperCase() + " - "+$('input[name="type'+String(i)+'"]:checked').val();
}else{
	cust_details_11 = cust_details_11 + "[Person " +String(parseInt(i+1))+ "] "+cust_details[i]+" [T] "+$('#title'+String(i)).val() +" [FN] "+$('#name'+String(i)).val().toUpperCase() +" [MN] "+$('#midname'+String(i)).val().toUpperCase() +" [LN] "+$('#lastname'+String(i)).val().toUpperCase() +" [AND] "+$('#nat'+String(i)).val().toUpperCase() + " - "+$('input[name="type'+String(i)+'"]:checked').val() + " ";
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

 


    jQuery.ajax({
        url: "https://docs.google.com/a/jtbap.com/forms/d/1FC624XHDT7yG8PFnZkcvfXAqo4z4DiAjubzX3Xg8_FU/formResponse", 
        data: gsh_data,
        type: "POST",
        dataType: "xml",
        success: function(data) {
            console.log('Submission successful');
            //$("#subscribeform").toggle();
            //$("#subscribemessage").html('<h4 class="green-text"><i class="fa fa-envelope" aria-hidden="true"></i> Thank you for subscribing with JTB!</h4>');
        },
        error: function(xhr, status, error) {
        	if (error.length>0){
        		await sleep(750);
        		console.log('Submission failed: ' + error);
	            
	            //$("#subscribemessage").html
	            jQuery('#jragent_response').html('<h3 class="red-text">Error: ' + email_gdocs_error + ' - There was a google database error - please try again - '+ error +' </h3>');


        	}else{
        		await sleep(750);
	            console.log('Submission successful');
	            //$("#subscribeform").toggle();
	            //$("#subscribemessage").html('<h4 class="green-text"><i class="fa fa-envelope" aria-hidden="true"></i> You have subscribed to our newsletter</h4><p>Thank you</p>');
if(email_gdocs_error == ""){
	jQuery('#jragent_response').html('<h3 class="green-text">JR Pass order submitted successfully</h3><FORM><INPUT TYPE="button" onClick="history.go(0)" VALUE="New JR Pass Booking" class="btnLarge"></FORM>');
	$("#contact_form").hide( "fast" );
}else{
	jQuery('#jragent_response').html('<h3 class="red-text">Error: ' + error + ' - ' + email_gdocs_error + '</h3>');
}

        	}
        }
    });

	//end google save



}
	 }//end if validation
   });//end page load
});//end




function processPassesReadable(p){
  return "<p><span>JR Passes Selected</span><br />" + p.replace(/,/g, "<br />").replace(/_/g, " ") + "</p>";
}

function processPassesReadableEmail(p){
  return   p.replace(/,/g, "[[br /]]").replace(/_/g, " ")  ;
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
	document.getElementById("myModal-1").style.display = "block";
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

function addPerson(){
	//add one to the people-count.
	var id = parseInt($('#numberOfPeople').val()); 
	if((id==undefined)||(isNaN(id))||(id==null)||(id=="0")){
		id=0;
	}
	if(id==19){
		return false;
	}
	if(parseInt(id)>0){
		$("#closebox"+String(id)).hide( "fast" );
	}
	id = id+1;
	var idplusone = id+1;
	$('#numberOfPeople').val(id);

	$("#personbox"+String(id)).html('<!--n--><div class="personbox"><div class="closebox" id="closebox'+id+'" onclick="close2();"></div><!--n--><!--n--><h3>Person '+idplusone+'</h3><p  class="red-text"><i class="material-icons" >error_outline</i> Name must be entered as it appears on your passport, including any middle names.</p><!--n--><!--n--><div class="col-xs-12 col-sm-12"><!--n--><div class="row form-group" ><!--n-->    <select name="title'+id+'" id="title'+id+'" required ><!--n-->      <option value="" disabled selected>Title</option><!--n-->      <option value="MR">Mr</option><!--n-->      <option value="MRS">Mrs</option><!--n-->      <option value="MS">Ms</option><!--n-->    </select> <span class="required">*</span><!--n--></div></div><!--n--><!--n--><!--n--><!--n--><div class="col-xs-12 col-sm-12"><!--n--><div class="row form-group"><!--n--><label for="name'+id+'">First Name <span class="required">*</span></label><input name="name'+id+'" id="name'+id+'" required><!--n--></div><!--n--></div><!--n--><!--n--><div class="col-xs-12 col-sm-12"><!--n--><div class="row form-group"><!--n--><label for="midname'+id+'">Middle Name</label><input name="midname'+id+'" id="midname'+id+'" required><!--n--></div><!--n--></div><!--n--><!--n--><div class="col-xs-12 col-sm-12"><!--n--><div class="row form-group"><!--n--><label for="lastname'+id+'">Last Name <span class="required">*</span></label><input name="lastname'+id+'" id="lastname'+id+'" required><!--n--></div><!--n--></div><!--n--><!--n--><div class="col-xs-12 col-sm-12"><!--n--><div class="row form-group"><!--n--><label for="nat'+id+'">Nationality <span class="required">*</span></label><input name="nat'+id+'" id="nat'+id+'" required><!--n--></div><!--n--></div><!--n--><!--n--><div class="col-xs-12 col-sm-6"><!--n--><div class="row form-group"><!--n--> <label for="type'+id+'1">Pass type <span class="required">*</span></label><!--n--><br /><br /><p><input name="type'+id+'" type="radio" id="type'+id+'1" value="Adult" class="radio" /> <label for="type'+id+'">Adult (12 yrs +)</label>  <!--n-->  </p><!--n--></div><!--n--></div><!--n--><div class="col-xs-12 col-sm-6"><!--n--><div class="row form-group"><br /><br /><!--n--> <p><input name="type'+id+'" type="radio" id="type'+id+'2" value="Child"   class="radio"  /> <label for="type'+id+'2">Child (6-11 yrs)</label>   <!--n--> </p><!--n--></div><!--n--></div><!--n--><!--n--><input type="hidden" name="passes'+id+'" val="" id="passes'+id+'"><!--n--><!--n--><div class="col-xs-12 col-sm-12"><!--n--><div  id="jrbutton'+id+'" class="row form-group"><!--n--><button onclick="selectPasses('+id+');"  class="wpcf7-form-control wpcf7-submit btnLarge">Select JR Passes</button><!--n--></div><!--n--></div><!--n--><!--n--><div class="col-xs-12 col-sm-12"><!--n--><div class="row form-group"><!--n--><div class="jrdisplay" id="jrdisplay'+id+'"><p><span>JR Passes Selected</span><br /><br /><!--n--><span id="jrdisplay'+id+'"></span><!--n--></p><!--n--></div><!--n--></div><!--n--></div><!--n--><!--n--><!--n--></div>');

  var same = $("#all_same").val();
  if (same=="all_same"){// if all pass same, hide all buttons etc.
	hide_all_buttons()
  }


}








