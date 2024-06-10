 
<?php







$isadmin = false;
$isHidden=false;
if(current_user_can('edit_post')){
	$isadmin=true;
}

echo '<p>The following are our website pages following the site structure.</p>';
echo '<p>Some icons used on our site are <a href="http://material.io/icons/">Google Material Design Icons</a>. Images used are ©JNTO or in the public domain.</p>';

if($isadmin){ // admin notes




/*

List all images URLs
$args = array(
    'post_type' => 'attachment',
    'numberposts' => -1,
    'post_status' => null,
    'post_parent' => null, // any parent
    ); 
$temp = "";
$attachments = get_posts($args);
if ($attachments) {
    foreach ($attachments as $post) {
        setup_postdata($post);
       // the_title() ;
     echo wp_get_attachment_image_src($post->ID)[0] . "<br />" ;//,  false,false , true 
        
    }
}
*/
//wp_mail("benjamin_g.au@jtbap.com","test",$temp);
//wp_mail( string|array $to, string $subject, string $message, string|array $headers = '', string|array $attachments = array() )
// get all media - list of name / url / page?










	echo '<p class="green-text"><strong>Note: Any text in green or red can only be seen if you are logged in to WordPress - hidden from the public.</strong></p>';
	echo '<p class="red-text"><strong>Red text means that the page is hidden from search and the public site-map.</strong></p>'; 


echo '<br /><a href="https://www.nx.jtbtravel.com.au/wp-content/plugins/jtb-australia-cust/docs/cron-job-generate-sitemap.php" class="button" target="_blank">Refresh Sitemap</a><br /><br />'; 

echo '<a href="https://www.nx.jtbtravel.com.au/wp-content/plugins/jtb-australia-cust/docs/cron-job-jr-price-list.php" class="button" target="_blank">Refresh JR Data</a><br /><br />'; 

echo '<a href="https://www.nx.jtbtravel.com.au/wp-content/plugins/jtb-australia-cust/docs/cron-job-jr-price-list-2023.php" class="button" target="_blank">Refresh JR Data - 2023 NEW</a><br /><br />'; 


echo '<a href="https://www.nx.jtbtravel.com.au/wp-admin/post.php?post=22954&action=edit" class="button" target="_blank">Update JR list - what we are selling</a><br /><br />'; 


echo '<a href="https://www.nx.jtbtravel.com.au/wp-admin/post.php?post=35519&action=edit" class="button" target="_blank">Update JR list - what we are selling (2023 JR Nat Prices Clone Agent)</a><br /><br />'; 






}


if ($isadmin){ //generated at docs/cron-job-generate-sitemap.php
	echo get_option("jtbau_sitemap_admin");
}else{
	echo get_option("jtbau_sitemap");
}


if($isadmin){

//PRINT ITINERARY THING
// 360 - 225 images itenerary 
// 720 - 450
?>


<h1>Tour Itinerary Generator</h1>



<br />

<script type="text/javascript">



$(document).ready(function() {

  });


var noDaysState = "0";
var tourTypeState = "0";



function selectDays(x){

	var htmlContent = "";

	if (x==1){
		document.getElementById("chosePasses").innerHTML = htmlContent;
	}
	
}


//on change - check the 2 values
function itemChange(){
	var tourDays = document.getElementById("tourDuration");
	var tourDaysValue = tourDays.options[tourDays.selectedIndex].value;
	var tourType = document.getElementById("choseTour");
	var tourTypeValue = tourType.options[tourType.selectedIndex].value;
	var htmlOut,tourText1,tourText2 = " ";

	if ((tourDaysValue!=="0")&&(tourTypeValue!=="0")){
		var x = parseInt(tourDaysValue);
	}
	else{
		document.getElementById("htmlOutput").innerHTML =  " ";
		return false;
		//if either of the drop downs are blank - clear output and stop here - else print the table ~ 
	}

	if (tourTypeValue=="2"){ //if guided
		tourText1 = '<tr>\n<td>\n<table>\n<tbody>\n<tr>\n<td>\n<ol>\n<li>Day</li>\n<li>';
		tourText2 = '\n</li>\n</ol>\n<table><tbody><tr><td>Tour Comment</td></tr></tbody></table></td>\n</tr>\n<tr>\n<td> \n<p>Information</p>\n</td>\n</tr>\n</tbody>\n</table>\n</td>\n</tr>\n';
	}else{
		tourText1 = '<tr>\n<td>\n<table>\n<tbody>\n<tr>\n<td>\n<ul>\n<li>Day</li>\n<li>';
		tourText2 = '</li>\n<li>B</li>\n<li>L</li>\n<li>D</li>\n<li>Other notes</li>\n</ul>\n</td>\n</tr>\n<tr>\n<td> \n<p>Tour details</p>\n</td>\n</tr>\n</tbody>\n</table>\n<p class="right-side-image"><img class="alignnone" src="/images/empty2.jpg" alt="" /></p>\n</td>\n</tr>\n';
	}


	htmlOut += '<br /><hr /><br /><p>@@@</p>\n\n<table class=" itinerary">\n<tbody>\n\n';

	var c = 0;
	while (c != x){
		htmlOut += tourText1 +(c+1).toString() + tourText2 ;
		c+=1; 
	}

	htmlOut += '</tbody>\n</table>\n\n<p>@@@</p>\n\n';
	
	document.getElementById("htmlOutput").innerHTML = htmlOut;
	return true;
}

</script>


<p>Select the number of days you want for your tour</p>
<select name="tourDuration" id="tourDuration" onchange="itemChange()">
  <option value="0">Select number of days</option>
  <option value="1">1 day</option>
  <option value="2">2 days</option>
  <option value="3">3 days</option>
  <option value="4">4 days</option>
  <option value="5">5 days</option>
  <option value="6">6 days</option>
  <option value="7">7 days</option>
  <option value="8">8 days</option>
  <option value="9">9 days</option>
  <option value="10">10 days</option>
  <option value="11">11 day</option>
  <option value="12">12 days</option>
  <option value="13">13 days</option>
  <option value="14">14 days</option>
  <option value="15">15 days</option>
  <option value="16">16 days</option>
  <option value="17">17 days</option>
  <option value="18">18 days</option>
  <option value="19">19 days</option>
  <option value="20">20 days</option>
  <option value="21">21 day</option>
  <option value="22">22 days</option>
  <option value="23">23 days</option>
  <option value="24">24 days</option>
  <option value="25">25 days</option>
  <option value="26">26 days</option>
  <option value="27">27 days</option>
  <option value="28">28 days</option>
  <option value="29">29 days</option>
  <option value="30">30 days</option>
</select>


<select name="choseTour" id="choseTour"  onchange="itemChange()">
  <option value="0">Select Tour Type</option>
  <option value="1">Fully Escorted Tour</option>
  <option value="2">Guided Tour</option>
</select>



<h3>Output</h3>
<p>Select between the '@' symbols and copy into WordPress, editing the information required.</p>

<div id="htmlOutput">


</div>



<?php

}

?>

