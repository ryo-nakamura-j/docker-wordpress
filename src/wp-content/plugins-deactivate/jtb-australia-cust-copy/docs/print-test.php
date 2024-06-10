
<?php
/*
jtb-widget f="test"]
*/

if(is_user_logged_in()){



if (defined('DOING_AJAX') && DOING_AJAX) {  echo "@@@__Ajax-is-working"; }

if(wp_doing_ajax()){
	echo "@@@__Ajax-is-working__2222";
}

	echo "@@@__Ajax-ERROR";


	echo '<p>When TP/ eWay accepting Amex/ Diner’s with surcharge – replace the payment page with this: <span class="red">(only logged in users can see this)</span></p>';
	echo '<img src="https://www.nx.jtbtravel.com.au/wp-content/uploads/2016/12/eway-logo-jtb-credit-cards.jpg" /><br /><br />';
	echo '<img src="https://www.nx.jtbtravel.com.au/wp-content/uploads/2017/10/eway-logo-jtb-credit-cards-jcb.jpg" /><br /><br />';


  
	//function wpdocs_set_html_mail_content_type() {
	//    return 'text/html';
	//}
	//add_filter( 'wp_mail_content_type', 'wpdocs_set_html_mail_content_type' );

   	//wp_mail( "benjamin_g.au@jtbap.com","JTB Australia Payment","Thank you for your payment to JTB Australia. Your payment was successful.<br /><br />Customer Name: Ben Gib<br />Amount: $10.11<br />Invoice Reference: SYRT123<br />Customer Email: benjamin_g.au@jtbap.com<br />State: NSW<br />Contact number: 0434787452 <br /><br /> <img src=\"https://www.nx.jtbtravel.com.au/wp-content/uploads/2017/03/jtb-logo-email.png\" style=\"width:125px;height:auto;\" /><p style=\" font-size: 1.4em;font-weight:bold;padding:0;margin:0;\">JTB Australia Pty Ltd</p><p style=\"padding:0;margin:0;\"><strong>Toll Free: 1300 739 330</strong></p><p style=\"padding:0;margin:0;\">License No: 02361295 IATA No: 02361295 ABN: 99 003 218 728</p>", "From: JTB Australia <sydres@nx.jtbtravel.com.au>"  );
   	
   	//remove_filter( 'wp_mail_content_type', 'wpdocs_set_html_mail_content_type' );





echo "<BR><BR><hr><BR><BR><BR>";




 echo get_delete_post_link( 71); 





}

?>









