<?php
/*
Add this instead of the social sections in the header and footer
do_action("print_social_buttons");
*/



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
if(($bookclosesettings['closeBookingsNow']==1) && ( ! is_page(22779) ) ){
	echo '<div class="hearderwarning"><p class="mobileonly red-message">'.$closeMessage.'</p></div>';
}else if($bookclosesettings['warningMessageShow']==1){
	echo '<div class="hearderwarning"><p class="mobileonly yellow-message">'.$warnMessage.'</p></div>';
}else if(($nowTime<$bookOpen)&&($nowTime>$bookClose)){
	echo '<div class="hearderwarning"><p class="mobileonly red-message">'.$closeMessage.'</p></div>';
}else if(($nowTime<$bookClose)&&($nowTime>$bookWarningStart)){
	echo '<div class="hearderwarning"><p class="mobileonly yellow-message">'.$warnMessage.'</p></div>';
}
$cMessageStyle="";
if(strlen($customMessage)>99){
	$cMessageStyle=" style='max-width: 100%;width: 100%;' ";
}

if($nowTime<$customMessageEnd){
	echo '<div class="hearderwarning"><p class="mobileonly '.$customMessageColour.'-message" '.$cMessageStyle.'>'.$customMessage.'</p></div>';
}


global $post;
if($nowTime<$customTPmessageEnd){
	if ( $post->post_parent==3338){
		echo '<div class="hearderwarning"><p class="mobileonly '.$customTPmessageColour.'-message" '.$cMessageStyle.'>'.$customTPmessage.'</p></div>';
	}
}


if ($_GET['er']=='suica'){
	echo '<div class="hearderwarning"><p class="mobileonly red-message">We currently don\'t have stock of SUICA cards - sorry for the inconvenience</p></div>';
}

?>

<div id="social" class="round-social-grey hidden-xs">


<a href="#" id="websearchpopup" class="sprite gplusbutton fa fa-websearch" title="Search the website" alt="Search the website"></a>

<?php
if (strlen(get_option('tp_facebook_url')) > 0) {
	echo "<a href=\"" . get_option('tp_facebook_url') . "\" class=\"sprite facebook fa fa-facebook\" title=\"Visit our Facebook page\"></a>";
}
?>


<?php
if (strlen(get_option('tp_twitter_url')) > 0) {
	echo "<a href=\"https://twitter.com/JTBAust\" class=\"sprite twitter fa fa-twitter\" title=\"Visit our twitter page\" alt=\"Visit our twitter page\"></a>";
} 
?>

<a href="https://www.instagram.com/jtbaustralia/" class="sprite instagram fa fa-instagram" title="Visit our Instagram page" alt="Visit our Instagram page"></a>

</div>


