
<p>@@@</p>






<script>

/*

  username: 'user',
  password: 'pass',
  
    xhrFields: {
    withCredentials: true,
  },
  


var api = "https://pa-jtbsyd.nx.tourplan.net/HostConnect_Test/test.html"

$.ajax({
  type: 'POST',
  dataType: 'text',
  url: api,
  crossDomain: true,

})
  .done(function (data) {
    console.log('done @@@');
  })
  .fail(function (xhr, textStatus, errorThrown) {
    alert(xhr.responseText);
    alert(textStatus);
  });
  
    */
  
/*

$url2 = "https://clienttest.nx.tourplan.net/TourplanNX_PA-JTBSYD_Test";


$.support.cors = true;
$.ajax({
    url: 'https://pa-jtbsyd.nx.tourplan.net/HostConnect_Test/test.html',
    type: 'POST',
    crossDomain: true,
    data: '',
    dataType: 'text/xml',
    username: 'rpa',
    password: 's3tRtbTDQ@',
    success: function (result) {
        alert(result);
    },
    error: function (jqXHR, tranStatus, errorThrown) {
        alert(
            'Status: ' + jqXHR.status + ' ' + jqXHR.statusText + '. ' +
            'Response: ' + jqXHR.responseText
        );
    }
});

*/

</script>




<?php


/*

nx_exe/:1023 Access to XMLHttpRequest at 'https://rpa:vmnMG7mBG2F4uSrbTDQ%40@clienttest.nx.tourplan.net/RetailEngine_PA-JTBSYD_Test/api' from origin 'https://www.nx.jtbtravel.com.au' has been blocked by CORS policy: No 'Access-Control-Allow-Origin' header is present on the requested resource.

Access to XMLHttpRequest at 'https://rpa:vmnMG7mBG2F4uSrbTDQ%40@clienttest.nx.tourplan.net/RetailEngine_PA-JTBSYD_Test/api' from origin 'https://www.jtbtravel.com.au' has been blocked by CORS policy: No 'Access-Control-Allow-Origin' header is present on the requested resource.

'<?xml version="1.0"?>' +
'<!DOCTYPE Request SYSTEM "hostConnect_3_00_000.dtd">' +
'<Request>' + 
'	<ListBookingsRequest>'+
'		<AgentID>rpa</AgentID>'+
'		<Password>vmnMG7mBG2F4uSrbTDQ@</Password>'+
'		<TravelDateFrom>2024-03-01</TravelDateFrom>'+
'		<TravelDateTo>2024-04-01</TravelDateTo>'+
'	</ListBookingsRequest>'+
'</Request>'
*/


function debug_to_console($data) {
$output = $data;    if (is_array($output)){$output = implode(',', $output);}
echo "<script>console.log('cons_log: ".$output."');</script>";}

function email7($txt7){ if(!$txt7){return false;}
if (!wp_mail( 'benjamin_g.au@jtbap.com', 'RPA_Info', $txt7 )){
echo '<h2>EMAIL SEND ERROR</h2>'  ; return false;}echo 'email-sent'; return true; }
/*
TP_NX login info
rpa
@8sSa3M4b3R#%2k^!VM^RQ

booking
SYAD397205
2024-03-31



*/

//Tourplan Appengine URL
//https://clienttest.nx.tourplan.net/RetailEngine_PA-JTBSYD_Test/api

/*





*/


$data = <<<XML
<?xml version="1.0"?>
<!DOCTYPE Request SYSTEM "hostConnect_3_00_000.dtd">

<Request>
	<ListBookingsRequest>
		<AgentID>INME03</AgentID>
		<Password>INME03</Password>
		<TravelDateFrom>2023-03-01</TravelDateFrom>
		<TravelDateTo>2025-04-01</TravelDateTo>
	</ListBookingsRequest>
</Request>


XML;
//rpa s3tRtbTDQ@

$data2 = <<<XML
<?xml version="1.0"?>
<!DOCTYPE Request SYSTEM "hostConnect_3_00_000.dtd">
<Request>
	<AgentInfoRequest>
		<AgentID>rpa</AgentID>
		<Password>s3tRtbTDQ@</Password>
		<ReturnAccountInfo>Y</ReturnAccountInfo>
	</AgentInfoRequest>
</Request>
XML;

$data2 = <<<XML

<?xml version="1.0"?>
 
<!DOCTYPE Request SYSTEM "hostConnect_5_04_004.dtd">
 
<Request>
	<AddServiceRequest>
		<AgentID>INME03</AgentID>
		<Password>INME03</Password>
		<NewBookingInfo>
			<Name>Test JTB Rail Pass Booking2</Name>
			<QB>B</QB>
			<TourplanBookingStatus>PE</TourplanBookingStatus>
		</NewBookingInfo>
		<Opt>JAPJRJRAE03JRTSH6</Opt>
		<RateId>Default</RateId>
		<DateFrom>2024-06-30</DateFrom>
		<RoomConfigs>
			<RoomConfig>
			<Adults>1</Adults>
			<Children>0</Children>
			<Infants>0</Infants>
			<RoomType>DB</RoomType>
			<PaxList>
				<PaxDetails>
					<Title>MS</Title>
					<Forename>PaulTest</Forename>
					<Surname>JacksonTest</Surname>
					<PaxType>A</PaxType>
				</PaxDetails>
			</PaxList>
			</RoomConfig>
		</RoomConfigs>
		<SCUqty>1</SCUqty>
		<Consult>Stephen</Consult>
	</AddServiceRequest>
</Request>
XML;


/*

error

<?xml version="1.0"?>

<!DOCTYPE Request SYSTEM "hostConnect_5_04_004.dtd">

<Request>
  <AgentInfoRequest>
    <AgentID>rpa</AgentID>
    <Password>s3tRtbTDQ@</Password>
    <ReturnAccountInfo>Y</ReturnAccountInfo>
  </AgentInfoRequest>
</Request>



 

<?xml version="1.0"?>

<!DOCTYPE Request SYSTEM "hostConnect_5_04_004.dtd">


<Request>
	<AddServiceRequest>
		<AgentID>INME03</AgentID>
		<Password>INME03</Password>
		<NewBookingInfo>
			<Name>Test JTB Rail Pass Booking2</Name>
			<QB>B</QB>
			<TourplanBookingStatus>PE</TourplanBookingStatus>
		</NewBookingInfo>
		<Opt>JAPJRJRAE03JRTSH6</Opt>
		<RateId>Default</RateId>
		<DateFrom>2024-06-30</DateFrom>
		<RoomConfigs>
			<RoomConfig>
			<Adults>1</Adults>
			<Children>0</Children>
			<Infants>0</Infants>
			<RoomType>DB</RoomType>
			<PaxList>
				<PaxDetails>
					<Title>MS</Title>
					<Forename>PaulTest</Forename>
					<Surname>JacksonTest</Surname>
					<PaxType>A</PaxType>
				</PaxDetails>
			</PaxList>
			</RoomConfig>
		</RoomConfigs>
		<SCUqty>1</SCUqty>
		<Consult>Stephen</Consult>
	</AddServiceRequest>
</Request>
*/






debug_to_console("Test API - RPA / TP NX");
//<Name>Test JTB Rail Pass Booking</Name>

$msg2 = "@@@ test";
$url = "https://pa-jtbsyd.nx.tourplan.net/HostConnect_Test/api/hostConnectApi?testpage=Y";
//$url = "https://pa-jtbsyd.nx.tourplan.net/HostConnect_Test/test.html";

$xml = simplexml_load_string($data);
$xml = $data;

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, TRUE);   //is it optional?
curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
curl_setopt($ch, CURLOPT_TIMEOUT, 80);
//curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_VERBOSE, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: text/xml',
        'Accept: text/xml'
    ));
$reply2 = curl_exec($ch);
curl_close($ch);



 debug_to_console( $reply2 );

echo $reply2;

echo "<hr />";


//############

/*


 
$xml = $data;


$send_context = stream_context_create(array(
    'http' => array(
    'method' => 'POST',
    'header' => 'Content-Type: text/xml',
    'content' => $xml
    )
));

debug_to_console( file_get_contents($url, false, $send_context) );



  $contents = file_get_contents($url, true, $send_context,0,null);

 debug_to_console( $contents);

 debug_to_console( "@@@");



function sendXmlOverPost($url, $xml) {
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);

	// For xml, change the content-type.
	curl_setopt ($ch, CURLOPT_HTTPHEADER, Array("Content-Type: text/xml"));

	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);

	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // ask for results to be returned

	// Send to remote and return data to caller.
	$result = curl_exec($ch);
	curl_close($ch);
	return $result;
}


debug_to_console( sendXmlOverPost($url,$data) );




 
$msg2 .= '\n\n' . $resp;


*/

/*
// SEND EMAIL - pause
if(!email7($msg2)){
 debug_to_console('email send error');
}
*/


//, string|string[] $headers = ”, 
//string|string[] $attachments = array() ): bool

?>

<p>@@@</p>






<?php 




?>
