 var nousers = 1;



function hidelabel2(x){ 
  //$("#natlabid"+x).css({"top": "-18px", "font-size": "0.7em"});

}



/*

document.addEventListener('DOMContentLoaded', function() {
   var elems = document.querySelectorAll('select');
   var options = document.querySelectorAll('option');


   var instances = M.FormSelect.init(elems, options); })
*/


function fbutton(x,y){// x is which scale, y is which option 
  var z = 3;
  if(y==1){ z=5 }
  if(y==2){ z=4 }
  if(y==5){ z=1 }
  if(y==4){ z=2 }
  $("#fbutton"+x).val(z); 
  $("#fbutton"+x+y).attr("src", "https://nx.jtbtravel.com.au/jr-pass-form/feedback/"+y+y+".jpg");
  for(var i = 1 ; i < 6; i++){
    if(i != y){
      $("#fbutton"+x+i).attr("src", "https://nx.jtbtravel.com.au/jr-pass-form/feedback/"+i+".jpg");
    }

  }
}





 
function isEmail(email) {
  var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
  return regex.test(email);
}
function caps(element){
    element.value = element.value.toUpperCase();
}

function othercheck(){
  if ( ($('#other3').val()!="") && ($('input[name="other2"]:checked').val()==null)  ){//@@@ is not checked 
    $('#other2').click();
  }else if ( ($('#other3').val()=="") && ($('input[name="other2"]:checked').val()!=null)  ){ 
    $('#other2').click();
  }
}






function feedback_google(email){

if(!email){
  feedback_google2();
  return;
}

var submitted = false;


$.ajax({ // subscribe 
      //url: "https://docs.google.com/a/jtbap.com/forms/d/1PUqUTddB5SS3TMr2kbjhZok0FQfTkJdvIdATV2sTIcQ/formResponse",
          // url: "https://docs.google.com/a/jtbap.com/forms/d/1PUqUTddB5SS3TMr2kbjhZok0FQfTkJdvIdATV2sTIcQ/formResponse",
          url: "https://docs.google.com/forms/d/1SRMQbdLim8-OPLR9LHOWfXZYtmv-Z0ms8Xdd0XQzf1k/formResponse",
      data:  'entry.1411460999='+email+'&entry.976961624=Retail_web_feedback',
      type: "POST",
      dataType: "xml",
      success: function(data) {
          console.log('Submission successful'); 
          if(!submitted){
            submitted=true;
            feedback_google2();
          }
      },
      error: function(xhr, status, error) {
        if (error.length>0){
          console.log('Submission failed: ' + error); 
        }else{
            console.log('Submission successful'); 
            if(!submitted){
              submitted=true;
              feedback_google2();
            }
        }
      }
  });



}




function feedback_google2(){

var blank = true;
if( document.getElementById('goog').checked ){
	blank = false;
	feedback_google3("Google search");
}if( document.getElementById('travagent').checked ){
	blank = false;
	feedback_google3("Travel Agent");
}if( document.getElementById('friends').checked ){
	blank = false;
	feedback_google3("Friends");
}if( document.getElementById('repeat').checked ){
	blank = false;
	feedback_google3("Repeat client");
}if( document.getElementById('socmedia').checked ){
	blank = false;
	feedback_google3("Social Media");
}if( document.getElementById('newspaper').checked ){
	blank = false;
	feedback_google3("Newspaper");
}if( document.getElementById('travexpo').checked ){
	blank = false;
	feedback_google3("Travel Expo");
}if( document.getElementById('yellowpages').checked ){
	blank = false;
	feedback_google3("Yellow/White Pages");
}

if(blank){
	if( document.getElementById('other2').checked ){
		blank = false;
		feedback_google3("Other");
	}
}

}


function feedback_google3(x){

var data2 = 'entry.308663305='+x;

$.ajax({ // hear about us 
     // url: "https://docs.google.com/a/jtbap.com/forms/d/1JH0gL0rQUTRoLDNqUMF5zVOJHHIN7rnakVJKg8paaUg/formResponse",
      url: "https://docs.google.com/forms/d/1uqhBFCQu1ZjXVeW3hhNgoe8ZIN0-0b_Yne4fqTg6UPY/formResponse",
      data:  data2,
      type: "POST",
      dataType: "xml",
      success: function(data) {
          console.log('Submission successful'); 
      },
      error: function(xhr, status, error) {
        if (error.length>0){
          console.log('Submission failed: ' + error); 
        }else{
            console.log('Submission successful'); 
        }
      }
  });

}




// Google form AJAX
$( document ).ready(function() {

//M.AutoInit()

//$('select').material_select();




  $('#subscribeform2').submit(function(e) {
      e.preventDefault();
      var errors="";

      //if(($('#ftitle1').val()=="")||(  $('#ftitle1').val()=="Title")||(  $('#ftitle1').val()==null)){
       // errors += "<li>Please select your title</li>";
      //}  




/* name validation */

var types2 = "";

if( document.getElementById('other2').checked ){
  if( $('#other3').val() != ""){
    types2 += "other("+$('#other3').val()+")_";
  }else{
    types2 += "other_";
  }
  
}

if(  $('input[name="goog"]:checked').val()!=null){
  types2 += $('input[name="goog"]:checked').val()+"_";
}
if(  $('input[name="travagent"]:checked').val()!=null){
  types2 += $('input[name="travagent"]:checked').val()+"_";
}
if(  $('input[name="friends"]:checked').val()!=null){
  types2 += $('input[name="friends"]:checked').val()+"_";
}
if(  $('input[name="repeat"]:checked').val()!=null){
  types2 += $('input[name="repeat"]:checked').val()+"_";
}
if(  $('input[name="socmedia"]:checked').val()!=null){
  types2 += $('input[name="socmedia"]:checked').val()+"_";
}
if(  $('input[name="newspaper"]:checked').val()!=null){
  types2 += $('input[name="newspaper"]:checked').val()+"_";
}
if(  $('input[name="travexpo"]:checked').val()!=null){
  types2 += $('input[name="travexpo"]:checked').val()+"_";
}
if(  $('input[name="yellowpages"]:checked').val()!=null){
  types2 += $('input[name="yellowpages"]:checked').val()+"_";
}


 
  types2 = types2.substring(0, types2.length - 1);
  $('#hearabout2').val(types2);
 
 
$('#fbutton5964').val(  $.trim($('#expectations').val().replace( "\n" , " - ").replace( "\r" , " - "))  );
//$('#expectations').val(" ");

      if(errors != ""){
        errors='<ul class="red2">'+errors+'</ul>';
        $("#subscribemessage").html(errors);
        $("html, body").animate({ scrollTop: $(document).height() }, 1000);
         
      }else{

var botom_buton = $('#bottom_buttons').html();


      $("#submitbuttonhide").html('<div style="width: 150px;"><svg class="spinner" width="65px" height="65px" viewBox="0 0 66 66" xmlns="http://www.w3.org/2000/svg"><circle class="path" fill="none" stroke-width="6" stroke-linecap="round" cx="33" cy="33" r="30"></circle></svg></div>');
//tp name 
//entry.444155984
//entry.444155984

      //email, then submit to g-Docs
      //feedback review 
      //var urlsyd = "https://docs.google.com/a/jtbap.com/forms/d/10wPt2GBEj-QJvuq9F3NWGMMzaGrW_t0wF_PIZsEFGXE/formResponse";

var urlsyd = "https://docs.google.com/forms/d/1Cp8fYPJ-gpVvBEDZHYN0DMm9cCZwI2K8ovxsjtsFD2s/formResponse";
var jtb_state = $("#jtb_state").val();
if(jtb_state != "SYDNEY"){//always MEL for testing - TO DO 
  urlsyd = "https://docs.google.com/forms/d/1Cp8fYPJ-gpVvBEDZHYN0DMm9cCZwI2K8ovxsjtsFD2s/formResponse";
}

//alert($(this).serialize());
var submitted = false;
$.ajax({
    url: urlsyd,
    data: $(this).serialize(),
    //data:"<entry.1484458029>aa</entry.1484458029>",
    type: "POST", 
    dataType: "xml",
    success: function(data) {
        console.log('Submission successful');
        $("#subscribeform2").toggle();
        $("#msg_37425").html('<h4 class="green-text"><i class="fa fa-envelope" aria-hidden="true"></i> Form submitted - Thank you!</h4><FORM><INPUT TYPE="button" onClick="history.go(0)" VALUE="New Form Submission" class="btnLarge"></FORM>'+botom_buton);
        if(!submitted){
          submitted=true;
          feedback_google($('#jrform').val());
        }
        

//////

    },
    error: function(xhr, status, error) {
      if (error.length>0){
        console.log('Submission failed: ' + error);
          $("#msg_37425").html('Submission failed: ' + error+'<p>Try refresh the page and submit again, or send us an email.</p>');
      }else{
          console.log('Submission successful');
          $("#subscribeform2").toggle();
          $("#msg_37425").html('<h4 class="green-text"><i class="fa fa-envelope" aria-hidden="true"></i> Form submitted - Thank you!</h4><FORM><INPUT TYPE="button" onClick="history.go(0)" VALUE="New Form Submission" class="btnLarge"></FORM>');
            
        if(!submitted){
          submitted=true;
          feedback_google($('#jrform').val());
        }
//if( ! ( ( $('#jrform').val() == "" ) && ($('#hearabout2').val() == "") ) ){



      }
    }
});



// END SUBMITTING


    }


  }); 





 



});





