<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-T6JTF6F"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->

<script>
$( window ).load(function() { 
$( "table.itinerary tbody tr td table tbody tr:first-child" ).addClass('classone');
$( "table.itinerary ul li:nth-child(1)" ).addClass('one'); 
$( "table.itinerary ul li:nth-child(2)" ).addClass('two'); 
$( "table.itinerary ul li:nth-child(3)" ).addClass('three'); 
$( "table.itinerary ul li:nth-child(4)" ).addClass('four'); 
$( "table.itinerary ul li:nth-child(5)" ).addClass('five'); 
$( "table.itinerary ul li:nth-child(6)" ).addClass('six'); 
$( "table.itinerary ul li:nth-child(7)" ).addClass('seven'); 
$( "table.itinerary ol li:nth-child(1)" ).addClass('one'); 
$( "table.itinerary ol li:nth-child(2)" ).addClass('two'); 
$( "table.itinerary ol li:nth-child(3)" ).addClass('three'); 
$( "table.itinerary ol li:nth-child(4)" ).addClass('four'); 
$( "table.itinerary ol li:nth-child(5)" ).addClass('five'); 
$( "table.itinerary ol li:nth-child(6)" ).addClass('six'); 
$( "table.itinerary ol li:nth-child(7)" ).addClass('seven'); 
 }); 
//$.noConflict();

</script>



<?php 
//if test website - print test banner

if( !  ( strpos($_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'], 'www.nx.jtbtravel.com.au') !== false)   ){
	echo '<div style="position: fixed;top: 35px;left: 5px ;width:120px;height: auto;opacity: 0.6;"><img src="https://www.nx.jtbtravel.com.au/wp-content/uploads/2018/01/jtb-test.png" style="width: 100%;height: auto;"></div>';
} 


// IE 9 and below - booking/ loading errors
// 732 738 739 742

echo '<a href="/" id="head_a_home"> </a>';


global $post;
if ( (is_page(3338)) || ($post->post_parent==3338) || (is_page(3360)) || ($post->post_parent==3360)  || ($post->post_parent==732) ||  ($post->post_parent==738) ||  ($post->post_parent==739)  || ($post->post_parent==742) || (is_page(3350)) || ($post->post_parent==3350)  ){
	echo '<!--[if lt IE 10]><p class="red-message onlyshowie9">You are currently using an out-dated browser which is not receiving security updates from Microsoft. Please use Google Chrome Browser or Firefox to complete bookings or call/ email us.</p><![endif]-->';
}


if (isset($_GET['booking'])){if ($_GET['booking']=='closed'){
	echo '<section class="container"><p class="mobileonly2x red-message">The booking system on our website has been closed - so you were redirected from the checkout page.</p></section>';
}}
if (isset($_GET['payment'])){if ($_GET['payment']=='canceled'){
	echo '<section class="container"><p class="mobileonly2x yellow-message">You have canceled your payment.</p></section>';
}}
 if (isset($_GET['report'])){ if ($_GET['report']=='logout'){
	echo '<section class="container"><p class="green-message">You have logged out of the JTB Report page.</p></section>';
}}
if (isset($_GET['er'])){if ($_GET['er']=='suica'){
	echo '<section class="container"><p class="mobileonly2x red-message">We currently don\'t have stock of SUICA cards - sorry for the inconvenience</p></section>';
}}


if (isset($_GET['shop'])){if ($_GET['shop']=='yes'){
	echo '<section class="container"><p class="mobileonly2x blue-message">Continue adding products to the shopping cart, link in the top right <i class="material-icons">shopping_cart</i> </p></section>';
}}


$bookclosesettings= get_option('close_bookings_jtb');
date_default_timezone_set('Australia/Sydney');
$nowTime = strtotime(date('m/d/Y h:i:s a', time()));


$customTPmessage = $bookclosesettings['customTPmessage'];
$customTPmessageColour = $bookclosesettings['customTPmessageColour'];
//$customTPmessageEndDate = $bookclosesettings['customTPmessageEndDate'];
$customTPmessageEndUNIX = date($bookclosesettings['customTPmessageEndDate']);
$customTPmessageEnd = strtotime(substr($customTPmessageEndUNIX,0,10)." ".substr($customTPmessageEndUNIX,11,2).":".substr($customTPmessageEndUNIX,-2));


$customMessage = $bookclosesettings['customMessage'];
$customMessageColour = $bookclosesettings['customMessageColour'];
$customMessageEndUNIX = date($bookclosesettings['customMessageEndDate']);
$customMessageEnd = strtotime(substr($customMessageEndUNIX,0,10)." ".substr($customMessageEndUNIX,11,2).":".substr($customMessageEndUNIX,-2));


$bookCloseUNIX = date($bookclosesettings['autoStartBookingClose']);
$bookClose = strtotime(substr($bookCloseUNIX,0,10)." ".substr($bookCloseUNIX,11,2).":".substr($bookCloseUNIX,-2));

$bookOpenUNIX = date($bookclosesettings['autoFINISHBookingClose']);
$bookOpen = strtotime(substr($bookOpenUNIX,0,10)." ".substr($bookOpenUNIX,11,2).":".substr($bookOpenUNIX,-2));
$bookWarningStartUNIX = date($bookclosesettings['startShowingWarning']);
$bookWarningStart = strtotime(substr($bookWarningStartUNIX,0,10)." ".substr($bookWarningStartUNIX,11,2).":".substr($bookWarningStartUNIX,-2));

$warnMessage=$bookclosesettings['warningMessage'];
$closeMessage=$bookclosesettings['closeBookingsMessage'];
if($bookclosesettings['closeBookingsNow']==1){
	echo '<section class="mobileonly2x container"><p class="mobileonly2x red-message">'.$closeMessage.'</p></section>';
}else if($bookclosesettings['warningMessageShow']==1){
	echo '<section class="mobileonly2x container"><p class="mobileonly2x yellow-message">'.$warnMessage.'</p></section>';
}else if(($nowTime<$bookOpen)&&($nowTime>$bookClose)){
	echo '<section class="mobileonly2x container"><p class="mobileonly2x red-message">'.$closeMessage.'</p></section>';
}else if(($nowTime<$bookClose)&&($nowTime>$bookWarningStart)){
	echo '<section class="mobileonly2x container"><p class="mobileonly2x yellow-message">'.$warnMessage.'</p></section>';
}

if($nowTime<$customMessageEnd){
	echo '<section class="mobileonly2x container"><p class="mobileonly2x '.$customMessageColour.'-message">'.$customMessage.'</p></section>';
}

if($nowTime<$customTPmessageEnd){
	if ( $post->post_parent==3338){
		echo '<section class="mobileonly2x container"><p class="mobileonly2x '.$customTPmessageColour.'-message">'.$customTPmessage.'</p></section>'; 
	}
}





//do_action("print_flight_home_data");


?>
