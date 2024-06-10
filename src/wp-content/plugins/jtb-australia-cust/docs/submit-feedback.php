<?php






//wp_mail("ben@pushka.com","test1234","DATA: " . $post_content . " --- " . $data_brochure . " ---  request updates " . $arraydata["_field_request-updates7"][0]     );
$hear_about = "";
if( ($arraydata["_field_hear-about7"] != "") && ($arraydata["_field_hear-about7"] != false) && ($arraydata["_field_hear-about7"] != null ) ){
	$hear_about = $arraydata["_field_hear-about7"];
}
$hear_about = str_replace(" ","%20",$hear_about);

$url = "https://docs.google.com/a/jtbap.com/forms/d/1JH0gL0rQUTRoLDNqUMF5zVOJHHIN7rnakVJKg8paaUg/formResponse";
$stream_options = array(
    'http' => array(
        'method'  => 'GET',
        'header'  => 'Content-Type: text/html' . "\r\n",
        'content' => "" 
    ));
$context  = stream_context_create($stream_options);
$response = file_get_contents($url."?entry.308663305=".$hear_about, null, $context);

if($arraydata["_field_request-updates7"][0] == "Yes"){
$name = ""; 
if( ($arraydata["_field_title7"] != "") && ($arraydata["_field_title7"] != false) && ($arraydata["_field_title7"] != null ) ){
	$name .= $arraydata["_field_title7"]." ";
}
if( ($arraydata["_field_first-name7"] != "") && ($arraydata["_field_first-name7"] != false) && ($arraydata["_field_first-name7"] != null ) ){
	$name .= $arraydata["_field_first-name7"]." ";
}
if( ($arraydata["_field_surname7"] != "") && ($arraydata["_field_surname7"] != false) && ($arraydata["_field_surname7"] != null ) ){
	$name .= $arraydata["_field_surname7"] ;
}
$name = str_replace(" ","%20",$name);
$state = ""; 
if( ($arraydata["_field_your-recipient7"] != "") && ($arraydata["_field_your-recipient7"] != false) && ($arraydata["_field_your-recipient7"] != null ) ){
	$state = $arraydata["_field_your-recipient7"];
	if( ($state == "melres+vic@nx.jtbtravel.com.au") || ($state == "melres+agent@nx.jtbtravel.com.au") ){
		$state="VIC";
	}else{
		$state=strtoupper(substr($state,7,3));//sydres+NSW@
	}
}
$email = ""; 
if( ($arraydata["_field_email7"] != "") && ($arraydata["_field_email7"] != false) && ($arraydata["_field_email7"] != null ) ){
	$email = $arraydata["_field_email7"];
}
$email = str_replace(" ","%20",$email); $email = str_replace("&","%26",$email);
$email = str_replace("+","%2B",$email); $email = str_replace("?","%3F",$email);
$email = str_replace("@","%40",$email); $email = str_replace("$","%24",$email);
$url = "https://docs.google.com/a/jtbap.com/forms/d/1PUqUTddB5SS3TMr2kbjhZok0FQfTkJdvIdATV2sTIcQ/formResponse";
$stream_options = array(
    'http' => array(
        'method'  => 'GET',
        'header'  => 'Content-Type: text/html' . "\r\n",
        'content' => "" 
    ));
$context  = stream_context_create($stream_options);
$response = file_get_contents($url."?entry.1493792574=".$name."&entry.976961624=".$state."&entry.1411460999=".$email, null, $context);
}




?>