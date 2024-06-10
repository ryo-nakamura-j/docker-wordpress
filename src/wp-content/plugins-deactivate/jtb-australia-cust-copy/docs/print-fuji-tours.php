
<form class="formdropdown">





<p><label for="fujiduration">Duration of Mt. Fuji trip:</label><br />
<select name="fujiduration" id="fujiduration">
  <option value="x">Select duration</option>
  <option value="1days">1 Day Tour</option>
  <option value="2days">2 Day Tour</option>
  <option value="long">Longer duration tour</option>
</select></p>












<div id="lvl2"></div>

<div id="lvl3"></div>

<div id="lvl4"></div>

<div id="lvl5"></div>


<input type="hidden" name="data1" id="data1" />
<input type="hidden" name="data2" id="data2" />
<input type="hidden" name="data3" id="data3" />
<input type="hidden" name="data4" id="data4" />

<div id="lvl6" style="display: none;">
	

<p><strong>Long Duration Tours - Mt. Fuji</strong></p>

<p>For longer tours and packages in the Mt. Fuji and Hakone areas, you can take part in a longer tour, or build your own tailor made package with the JR Pass, and other Mt. Fuji day tours.</p>







</div>





</form>


<script type="text/javascript">
	
jQuery(document).ready(function(){ 

//alert("test1");


 }) 


jQuery('.formdropdown').on('change', 'select', function(){

//alert( jQuery( this ).attr('id')  );
 var selectID = jQuery( this ).attr('id'); // id of just-edited item 
 var dropdown = document.getElementById(selectID).value  ; //current selected item val

if(selectID=="fujiduration"){
	jQuery("#data1").val('');jQuery("#data2").val('');jQuery("#data3").val('');jQuery("#data4").val('');
jQuery("#lvl2").html('');jQuery("#lvl3").html('');jQuery("#lvl4").html("");jQuery("#lvl5").html("");
if(dropdown == "x"){return true;}
if(dropdown == "1days"){
	jQuery("#data1").val('1day');
  jQuery("#lvl2").html('<p><label for="fujilunch">Lunch inclusion:</label><br /><select name="fujilunch" id="fujilunch"  ><option value="x">Would you like lunch included?</option><option value="lunch">Lunch included</option><option value="nolunch">No lunch included</option></select></p>'); 
  } else if(dropdown == "2days"){
jQuery("#data1").val('2day');
  		jQuery("#lvl2").html('<p><label for="fuji2daysmeals">Meal options:</label><br /><select name="fuji2daysmeals" id="fuji2daysmeals">  <option value="x">What meals would you like included?</option>  <option value="lunch">one Lunch only</option>  <option value="bld">B/L/D</option></select></p>'); 
  } else if(dropdown == "long"){
  	jQuery("#data1").val('3day');
  //long Fuji selection 
jQuery("#lvl2").html("");jQuery("#lvl3").html("");jQuery("#lvl4").html("");jQuery("#lvl5").html("");
printAll();
  }

//end LVL ONE 
}else if(selectID=='fujilunch'){// if 1-day  /////
	jQuery("#data3").val('');jQuery("#data4").val('');
jQuery("#lvl3").html('');jQuery("#lvl4").html('');
if(dropdown == "x"){jQuery("#data2").val('');return true;}
//lunch or no lunch selected , 1day
jQuery("#data2").val(dropdown);
jQuery("#lvl3").html('<p><label for="fujireturn">Return method:</label><br /><select name="fujireturn" id="fujireturn">  <option value="x">Select return method</option>  <option value="motorcoach">Return to Tokyo by motorcoach</option>  <option value="shinkansen">Return to Tokyo by Shinkansen</option>  <option value="remain">Stay in the Mt. Fuji/ Hakone area after the tour</option></select></p>');


}else if(selectID=='fuji2daysmeals'){ //  2 day ///////
	jQuery("#data2").val('');jQuery("#data3").val('');jQuery("#data4").val('');
jQuery("#lvl3").html('');jQuery("#lvl4").html('');jQuery("#lvl5").html("");
if(dropdown == "x"){jQuery("#data2").val('');return true;}
//lunch or no lunch selected , 2day
jQuery("#data2").val(dropdown);
jQuery("#lvl3").html('<p><label for="fujipickup2day">Pickup location in Tokyo:</label><br /><select name="fujipickup2day" id="fujipickup2day">  <option value="x">Select pickup location</option><option value="asakusa">Asakusa, Ginza, Shiba</option><option value="ikebukuro">Ikebukuro</option><option value="kudanshita">Kudanshita, Akasaka, Roppongi</option><option value="odaiba">Odaiba, Shinagawa</option><option value="shinjuku">Shinjuku, Shibuya</option><option value="keio">Keio Plaza Hotel Tokyo</option><option value="tokyo">Tokyo Ueno Station</option> </select></p>');

}else if(selectID=='fujireturn'){
	//1 day - return method selected 
jQuery("#data4").val('');jQuery("#lvl4").html('');jQuery("#lvl5").html("");
if(dropdown == "x"){jQuery("#data3").val('');return true;}
	//pickup location 
	jQuery("#data3").val(dropdown);
	jQuery("#lvl4").html('<p><label for="fujipickup">Pickup location in Tokyo:</label><br /><select name="fujipickup" id="fujipickup">  <option value="x">Select pickup location</option><option value="asakusa">Asakusa, Ginza, Shiba</option><option value="ikebukuro">Ikebukuro</option><option value="kudanshita">Kudanshita, Akasaka, Roppongi</option><option value="odaiba">Odaiba, Shinagawa</option><option value="shinjuku">Shinjuku, Shibuya</option><!--<option value="keio">Keio Plaza Hotel Tokyo</option><option value="tokyo">Tokyo Ueno Station</option> --></select></p>');
}else if(selectID=='fujipickup'){ //1 day all selected
if(dropdown == "x"){jQuery("#data4").val('');return true;}
jQuery("#data4").val(dropdown);
printAll();
}else if(selectID=='fujipickup2day'){ //2 day all selected
if(dropdown == "x"){jQuery("#data4").val('');return true;}
jQuery("#data4").val(dropdown);
printAll();
}



return true;

/*

// on any drop-down change , run this
//alert('###');
var lvl3data = "";
  
if(dropdown == "x"){
  	jQuery("#lvl2").html('');
jQuery("#lvl3").html('');jQuery("#lvl4").html("");
return true;
}

if(lvl3data){
//after picking lunch 

if(0){ // if 1 day - return method coach/shin/etc

}

}

*/



});


function printAll(){
	var option1="";
var option2="";
var option3="";
var data="";
var buttontext='Click for tour details';
var d1 = jQuery("#data1").val();
var d2 = jQuery("#data2").val();
var d3="";
var d4 = jQuery("#data4").val();

if(d1=="1day"){ //1 DAY
  d3 = jQuery("#data3").val();//if 2 day, blank
  if(d2=="lunch"){//lunch
  	if(d3=="motorcoach"){//lunch

data += '<p>Mt. Fuji 1 Day Tour with lunch, return by motorcoach.</p><a  class="btn button"  href="';

switch(d4) {
  case 'asakusa':
    // code block
    data += 'https://www.nx.jtbtravel.com.au/day-tours/day-tour/?qty=1A&scu=1&productid=10174';
    break;
  case 'ikebukuro':
    // code block
    data += 'https://www.nx.jtbtravel.com.au/day-tours/day-tour/?qty=1A&scu=1&productid=10175';
    break;
  case 'kudanshita':
    // code block
    data += 'https://www.nx.jtbtravel.com.au/day-tours/day-tour/?qty=1A&scu=1&productid=10176';
    break;
  case 'odaiba':
    // code block
    data += 'https://www.nx.jtbtravel.com.au/day-tours/day-tour/?qty=1A&scu=1&productid=10177';
    break;
  case 'shinjuku':
    // code block
    data += 'https://www.nx.jtbtravel.com.au/day-tours/day-tour/?qty=1A&scu=1&productid=10178';
    break;
  case 'keio':
    // code block
    data += '#';
    break;
  case 'tokyo':
    // code block
    data += '#';
    break;
  default:
    // code block
    data += '#';
}

data += '" target="_blank">'+buttontext+'</a>';

  	}//end motorcoach 
  	else 	if(d3=="shinkansen"){//lunch

data += '<p>Mt. Fuji 1 Day Tour with lunch, return by Shinkansen.</p><a  class="btn button"  href="';
 

switch(d4) {
  case 'asakusa':
    // code block
    data += 'https://www.nx.jtbtravel.com.au/day-tours/day-tour/?qty=1A&scu=1&productid=10162';
    break;
  case 'ikebukuro':
    // code block
    data += 'https://www.nx.jtbtravel.com.au/day-tours/day-tour/?qty=1A&scu=1&productid=10164';
    break;
  case 'kudanshita':
    // code block
    data += 'https://www.nx.jtbtravel.com.au/day-tours/day-tour/?qty=1A&scu=1&productid=10165';
    break;
  case 'odaiba':
    // code block
    data += 'https://www.nx.jtbtravel.com.au/day-tours/day-tour/?qty=1A&scu=1&productid=10166';
    break;
  case 'shinjuku':
    // code block
    data += 'https://www.nx.jtbtravel.com.au/day-tours/day-tour/?qty=1A&scu=1&productid=10167';
    break;
  case 'keio':
    // code block
    data += '#';
    break;
  case 'tokyo':
    // code block
    data += '#';
    break;
  default:
    // code block
    data += '#';
}

data += '" target="_blank">'+buttontext+'</a>';

  	}//end shinkansen 
else 	if(d3=="remain"){//lunch

data += '<p>Mt. Fuji 1 Day Tour with lunch, remain in the Mt. Fuji/ Hakone area after the tour.</p><a  class="btn button"  href="';
 


switch(d4) {
  case 'asakusa':
    // code block
    data += 'https://www.nx.jtbtravel.com.au/day-tours/day-tour/?qty=1A&scu=1&productid=10540';
    break;
  case 'ikebukuro':
    // code block
    data += 'https://www.nx.jtbtravel.com.au/day-tours/day-tour/?qty=1A&scu=1&productid=10355';
    break;
  case 'kudanshita':
    // code block
    data += 'https://www.nx.jtbtravel.com.au/day-tours/day-tour/?qty=1A&scu=1&productid=11506';
    break;
  case 'odaiba':
    // code block
    data += 'https://www.nx.jtbtravel.com.au/day-tours/day-tour/?qty=1A&scu=1&productid=10574';
    break;
  case 'shinjuku':
    // code block
    data += 'https://www.nx.jtbtravel.com.au/day-tours/day-tour/?qty=1A&scu=1&productid=10529';
    break;
  case 'keio':
    // code block
    data += '#';
    break;
  case 'tokyo':
    // code block
    data += '#';
    break;
  default:
    // code block
    data += '#';
}

data += '" target="_blank">'+buttontext+'</a>';

  	}//end remain 








  }// END OF 1 DAY LUNCH ZONE 
else if(d2=="nolunch"){// - NO LUNCH /////////////////////////////////
  	if(d3=="motorcoach"){//lunch-no

data += '<p>Mt. Fuji 1 Day Tour without lunch, return by motorcoach.</p><a  class="btn button"  href="';

switch(d4) {
  case 'asakusa':
    // code block
    data += 'https://www.nx.jtbtravel.com.au/day-tours/day-tour/?qty=1A&scu=1&productid=10179';
    break;
  case 'ikebukuro':
    // code block
    data += 'https://www.nx.jtbtravel.com.au/day-tours/day-tour/?qty=1A&scu=1&productid=10180';
    break;
  case 'kudanshita':
    // code block
    data += 'https://www.nx.jtbtravel.com.au/day-tours/day-tour/?qty=1A&scu=1&productid=10181';
    break;
  case 'odaiba':
    // code block
    data += 'https://www.nx.jtbtravel.com.au/day-tours/day-tour/?qty=1A&scu=1&productid=10182';
    break;
  case 'shinjuku':
    // code block
    data += 'https://www.nx.jtbtravel.com.au/day-tours/day-tour/?qty=1A&scu=1&productid=10183';
    break;
  case 'keio':
    // code block
    data += '#';
    break;
  case 'tokyo':
    // code block
    data += '#';
    break;
  default:
    // code block
    data += '#';
}

data += '" target="_blank">'+buttontext+'</a>';

  	}//end motorcoach 
  	else 	if(d3=="shinkansen"){//lunch-no

data += '<p>Mt. Fuji 1 Day Tour without lunch, return by Shinkansen.</p><a  class="btn button"  href="';
 


switch(d4) {
  case 'asakusa':
    // code block
    data += 'https://www.nx.jtbtravel.com.au/day-tours/day-tour/?qty=1A&scu=1&productid=10168';
    break;
  case 'ikebukuro':
    // code block
    data += 'https://www.nx.jtbtravel.com.au/day-tours/day-tour/?qty=1A&scu=1&productid=10169';
    break;
  case 'kudanshita':
    // code block
    data += 'https://www.nx.jtbtravel.com.au/day-tours/day-tour/?qty=1A&scu=1&productid=10170';
    break;
  case 'odaiba':
    // code block
    data += 'https://www.nx.jtbtravel.com.au/day-tours/day-tour/?qty=1A&scu=1&productid=10171';
    break;
  case 'shinjuku':
    // code block
    data += 'https://www.nx.jtbtravel.com.au/day-tours/day-tour/?qty=1A&scu=1&productid=10172';
    break;
  case 'keio':
    // code block
    data += '#';
    break;
  case 'tokyo':
    // code block
    data += '#';
    break;
  default:
    // code block
    data += '#';
}

data += '" target="_blank">'+buttontext+'</a>';

  	}//end shinkansen 
else 	if(d3=="remain"){//lunch

data += '<p>Mt. Fuji 1 Day Tour, remain in the Mt. Fuji/ Hakone area after the tour.<br /><strong>Note: unfortunately at this time there is no option for NO-LUNCH, remaining in the area. This tour includes lunch.</strong></p><a  class="btn button"  href="';
 


switch(d4) {
  case 'asakusa':
    // code block
    data += 'https://www.nx.jtbtravel.com.au/day-tours/day-tour/?qty=1A&scu=1&productid=10540';
    break;
  case 'ikebukuro':
    // code block
    data += 'https://www.nx.jtbtravel.com.au/day-tours/day-tour/?qty=1A&scu=1&productid=10355';
    break;
  case 'kudanshita':
    // code block
    data += 'https://www.nx.jtbtravel.com.au/day-tours/day-tour/?qty=1A&scu=1&productid=11506';
    break;
  case 'odaiba':
    // code block
    data += 'https://www.nx.jtbtravel.com.au/day-tours/day-tour/?qty=1A&scu=1&productid=10574';
    break;
  case 'shinjuku':
    // code block
    data += 'https://www.nx.jtbtravel.com.au/day-tours/day-tour/?qty=1A&scu=1&productid=10529';
    break;
  case 'keio':
    // code block
    data += '#';
    break;
  case 'tokyo':
    // code block
    data += '#';
    break;
  default:
    // code block
    data += '#';
}

data += '" target="_blank">'+buttontext+'</a>';

  	}//end remain 



  }// END OF 1 DAY NO LUNCH ZONE  //////////////////////




} // end 1 day 
else   if(d1=="2day"){//lunch - 2 DAY 
 if(d2 == "lunch"){

data += '<p>Mt. Fuji 2 Day Tour with lunch</p>';

switch(d4) {
  case 'asakusa':
    // code block
    option1 = 'https://www.nx.jtbtravel.com.au/day-tours/day-tour/?qty=1A&scu=1&productid=10796';
    option2 = 'https://www.nx.jtbtravel.com.au/day-tours/day-tour/?qty=1A&scu=1&productid=13408';
    option3 = 'https://www.nx.jtbtravel.com.au/day-tours/day-tour/?qty=1A&scu=1&productid=10429';
    break;
  case 'ikebukuro':
    // code block
    option1 = 'https://www.nx.jtbtravel.com.au/day-tours/day-tour/?qty=1A&scu=1&productid=13403';
    option2 = 'https://www.nx.jtbtravel.com.au/day-tours/day-tour/?qty=1A&scu=1&productid=13406';
    option3 = 'https://www.nx.jtbtravel.com.au/day-tours/day-tour/?qty=1A&scu=1&productid=10717';
    break;
  case 'kudanshita':
    // code block
    option1 = 'https://www.nx.jtbtravel.com.au/day-tours/day-tour/?qty=1A&scu=1&productid=10712';
    option2 = 'https://www.nx.jtbtravel.com.au/day-tours/day-tour/?qty=1A&scu=1&productid=13405';
    option3 = 'https://www.nx.jtbtravel.com.au/day-tours/day-tour/?qty=1A&scu=1&productid=11770';
    break;
  case 'odaiba':
    // code block
    option1 = 'https://www.nx.jtbtravel.com.au/day-tours/day-tour/?qty=1A&scu=1&productid=13404';
    option2 = 'https://www.nx.jtbtravel.com.au/day-tours/day-tour/?qty=1A&scu=1&productid=13409';
    option3 = 'https://www.nx.jtbtravel.com.au/day-tours/day-tour/?qty=1A&scu=1&productid=11771';
    break;
  case 'shinjuku':
    // code block
    option1 = 'https://www.nx.jtbtravel.com.au/day-tours/day-tour/?qty=1A&scu=1&productid=10682';
    option2 = 'https://www.nx.jtbtravel.com.au/day-tours/day-tour/?qty=1A&scu=1&productid=13407';
    option3 = 'https://www.nx.jtbtravel.com.au/day-tours/day-tour/?qty=1A&scu=1&productid=10532';
    break;
  case 'keio':
    // code block
    data += '#';
    break;
  case 'tokyo':
    // code block
    data += '#';
    break;
  default:
    // code block
    data += '#';
}

data += 'Option 1 - <a  class="btn button"  href="'+option1+'" target="_blank">'+buttontext+'</a><br />';
data += 'Option 2 - <a  class="btn button"  href="'+option2+'" target="_blank">'+buttontext+'</a><br />';
data += 'Option 3 - <a  class="btn button"  href="'+option3+'" target="_blank">'+buttontext+'</a><br />';

  	}//end 2 day lunch only
  	else{ // B L D food 2 day 


data += '<p>Mt. Fuji 2 Day Tour with Breakfast, Lunch, Dinner</p>';

switch(d4) {
  case 'asakusa':
    // code block
    option1 = 'https://www.nx.jtbtravel.com.au/day-tours/day-tour/?qty=1A&scu=1&productid=11890';
    option2 = 'https://www.nx.jtbtravel.com.au/day-tours/day-tour/?qty=1A&scu=1&productid=10566';
    option3 = 'https://www.nx.jtbtravel.com.au/day-tours/day-tour/?qty=1A&scu=1&productid=13410';
    break;
  case 'ikebukuro':
    // code block
    option1 = 'https://www.nx.jtbtravel.com.au/day-tours/day-tour/?qty=1A&scu=1&productid=11892';
    option2 = 'https://www.nx.jtbtravel.com.au/day-tours/day-tour/?qty=1A&scu=1&productid=12505';
    option3 = 'https://www.nx.jtbtravel.com.au/day-tours/day-tour/?qty=1A&scu=1&productid=13412';
    break;
  case 'kudanshita':
    // code block
    option1 = 'https://www.nx.jtbtravel.com.au/day-tours/day-tour/?qty=1A&scu=1&productid=10897';
    option2 = 'https://www.nx.jtbtravel.com.au/day-tours/day-tour/?qty=1A&scu=1&productid=12032';
    option3 = 'https://www.nx.jtbtravel.com.au/day-tours/day-tour/?qty=1A&scu=1&productid=11988';
    break;
  case 'odaiba':
    // code block
    option1 = 'https://www.nx.jtbtravel.com.au/day-tours/day-tour/?qty=1A&scu=1&productid=11891';
    option2 = 'https://www.nx.jtbtravel.com.au/day-tours/day-tour/?qty=1A&scu=1&productid=11234';
    option3 = 'https://www.nx.jtbtravel.com.au/day-tours/day-tour/?qty=1A&scu=1&productid=13411';
    break;
  case 'shinjuku':
    // code block
    option1 = 'https://www.nx.jtbtravel.com.au/day-tours/day-tour/?qty=1A&scu=1&productid=10527';
    option2 = 'https://www.nx.jtbtravel.com.au/day-tours/day-tour/?qty=1A&scu=1&productid=10526';
    option3 = 'https://www.nx.jtbtravel.com.au/day-tours/day-tour/?qty=1A&scu=1&productid=13413';
    break;
  case 'keio':
    // code block
    data += '#';
    break;
  case 'tokyo':
    // code block
    data += '#';
    break;
  default:
    // code block
    data += '#';
}

data += 'Option 1 - <a  class="btn button"  href="'+option1+'" target="_blank">'+buttontext+'</a><br />';
data += 'Option 2 - <a  class="btn button"  href="'+option2+'" target="_blank">'+buttontext+'</a><br />';
data += 'Option 3 - <a  class="btn button"  href="'+option3+'" target="_blank">'+buttontext+'</a><br />';

  	} 
}else{ //3 day
//data="Long Duration Tours - Mt. Fuji";
data += jQuery("#lvl6").html();
//jQuery("#lvl5").html(jQuery("#lvl6").html());

}






jQuery("#lvl5").html(data);
return true;

} // END FINAL PRINT FUNCTION 



function contains(x,y){
	if (x.indexOf(y) >= 0){
		return true;
	}return false ;
}


/*

program lvl 3, or 4, 

in an extracted function - add in links / buttons to day tour pages, with TXT descriptions and text for long tour options / tailor-made, 

*/

</script>