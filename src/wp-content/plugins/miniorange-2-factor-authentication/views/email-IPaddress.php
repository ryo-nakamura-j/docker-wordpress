<?php
function mo_IP_template()
{
global $moWpnsUtility,$imagePath;
$IPaddress = $moWpnsUtility->get_client_ip(); 
$IPaddress = sanitize_text_field( $IPaddress );
$result=wp_remote_get("http://www.geoplugin.net/json.gp?ip=".$IPaddress);

$mo2f_cityName='-';
$mo2f_Country='-';

if( !is_wp_error( $result ) ) {
	try{
		$result=wp_remote_retrieve_body( $result);
		$mo2f_cityName=isset($result["geoplugin_city"])?$result["geoplugin_city"]:'-';
		$mo2f_Country=isset($result["geoplugin_countryName"])?$result["geoplugin_countryName"]:'-';
	}catch(Exception $e){

	}
}


$ipLookUpTemplate  = MoWpnsConstants::IP_LOOKUP_TEMPLATE; 
$hostname = get_site_url();
$t= date("Y-m-d");
return '<!DOCTYPE html>
<html>
<head>

	<title></title>
</head>
<body style=background-color:#f6f4f4>
<style>
	.mo_2fa_description
	{

		/*min-height: 400px;*/
		/*width: 29%;*/
		margin: 3%;
		/*float: left;*/
		text-align: left;
		color: black;
		padding: 19px 12px;
		margin-top: -9px;
		width :91%;
		margin-left:3%;
		font-size:19px;
		border: 4px solid #2271b1;

	}
	.mo_2fa_feature
	{
		width: 78%;
		/*margin: 2%;*/
		float: left;
		background-color: white;
		/*border: 1px solid gray;*/
		min-height: 400px;	
    	overflow: hidden;
	}
	.mo_2fa_email_template_details
	{
		width: 40%;
		margin: 1%;
		float: left;
		background-color: white;
		border-top: 5px solid #2271b1;
		min-height: 320px;
		text-align: center;
		overflow: hidden;
		margin-top:47px;
		font-size:23px;
	}
	.mo_2fa_email_template_details:hover
	{
		box-shadow: 0 0px 0px 0 #9894f6, 0 6px 10px 0 #837fea;
		border-top: 4px solid black;
    	margin-top: -0.5%;
	}
	.mo_2fa_email_feature_details
	{
		width: 29%;
		margin: 2.16%;
		margin-bottom: 5%;
		float: left;
		background-color: #FF4500;
		text-align: center;
		min-height: 250px;
		overflow: hidden;
		color: #100505;
    	font-family: cursive;
    	border-radius: 15px;
		box-shadow: 0 0px 0px 0 #b5b2f6, 0 6px 10px 0 #bcbaf4;

	}
	.mo_2fa_email_feature_details:hover
	{
		color: #110d8b;
		box-shadow: 0 0px 0px 0 #9894f6, 0 6px 10px 0 #837fea;
	}
	.mo_2fa_ef_button:hover
	{
		box-shadow: 0 0px 0px 0 #ffa792, 0 6px 10px 0 #cb8473;
	}
	.mo_2fa_feature_block
	{
		/*width: 91%;*/
	    margin-left: 3%;
	    display: flex;
	    color:white;
	}
	.mo_2fa_ef_h2
	{
		color: #ad2100;
		font-family: cursive;
	}
	.mo_2fa_ef_h1
	{
		color: #100505;
		

	}
	.mo_2fa_ef_button
	{
		font-size: x-large;
	    background-color:#2271b1;
	    color: white;
	    padding: 17px 127px;
	    font-family: cursive;
	    margin-left: -42px;
	}
	.mo_2fa_ef_read_more
	{
		color: #2271b1;
    	border: 2px solid #2271b1;
	    padding: 17px 27px;
	    font-family: cursive;
	}
	.mo_2fa_ef_read_more:hover
	{
		
		/*font-size: x-large;*/
	    background-color: #2271b1;
	    color: white;
	    border: 1px solid white;
	    padding: 17px 27px;
	    font-family: cursive;
	}
    .mo_2fa_ef_hr
    {
		border: 2px solid #100505;
	    margin: 0px 7%;
    }
    .myDiv {
 
  		/*min-height: 300px;*/
		background-color: #18272a;
		/*width: 29%;*/
		/*float: left;*/
		text-align: center;
		color: white;
		padding: 2px 2px;
		font-size:18px;
		margin-top:14px;
}
</style>
<div style="border: 2px solid black;">
			<center><img src="'.$imagePath.'includes/images/40290_shield.png" alt="miniOrange 2FA" width="350" height="175"></center>
			<div class="mo_2fa_description" ><center><h2> Dear Customer</h2></center>
				<h2>A new login to your account has been made from this IP Address '.esc_attr($IPaddress).'.  If you recently logged in and recognize the logged in location,you may disregard this email.  If you did not recently log in, you should immediately 	change your password . Passwords should be unique and not used for any other sites or services.If not MFA enabled To further protect your account, consider configuring a multi-factor authentication method <a style="color: #000080"href="https://plugins.miniorange.com/2-factor-authentication-for-wordpress">See 2FA methods</a>.
				</h2>
			</div>

 			<div>
 			<center><h2 style="color: black; font-size:40px"> Your Account Sign in with New Location </h2></center>
 			<center> <table style="text-align: left;margin-top: -120;color:blue"> 
 						<tr>
 								<th><h2> IP ADDRESS </h2></th>
 								<th><h2>::   '.esc_attr($IPaddress).' </h2></th>
 						</tr>
 						<tr>
 								<th><h2> WEBSITE   </h2></th>
 								<th><h2>::   '.esc_attr($hostname).' </h2></th>
 						</tr>
 						<tr>
 								<th><h2>LOGIN DATE  </h2> </th>
 								<th><h2>::   '.esc_attr($t).'</h2> </th>
 						</tr>
 						<tr>
 								<th><h2>LOGIN LOCATION</h2> </th>
 								<th><h2>::    '.esc_attr($mo2f_cityName).'/'.esc_attr($mo2f_Country).'</h2> </th>
 			
 						</tr>
 						</table>
 			</center>
 			</div>

			<div>
					<br><br>
				<center><a class="mo_2fa_ef_button" href="https://plugins.miniorange.com/2-factor-authentication-for-wordpress">Feature Details</a></center>
			</div>
	
			<div class="mo_2fa_feature_block" style="margin-left: 14%;">
				<div class="mo_2fa_email_template_details">
						<i  class="dashicons dashicons-admin-site" style="font-size:50px;color: black;margin-top: 6%"></i>
						<div style="min-height: 150px;">
							<h2 style="color: black;">Website</h2>
							<p style="color: black;padding: 0px 27px;text-align: justify;">miniOrange provides easy to use 2-factor authentication for secure login to your WordPress site.</p>
						</div>
						<div>
								<br><br>
							<center>
									<a class="mo_2fa_ef_read_more"href="https://plugins.miniorange.com/">Read More</a>
							</center>
						</div>
				</div>
				<div class="mo_2fa_email_template_details">
						<i class="fa fa-headphones" style="font-size:50px;color: black;margin-top: 6%"></i>
						<div style="min-height: 150px;">
								<h2 style="color: black;">Documentation</h2>
								<p style="color: black;padding: 0px 27px;text-align: justify;">miniOrange Two-Factor Authentication in which you have to provide two factors to gain the access</p>
						</div>
						<div>
							<br><br>
						<center>
								<a class="mo_2fa_ef_read_more" href="https://developers.miniorange.com/docs/security/wordpress/wp-security">Read More</a>
						</center>

						</div>
				</div>
			</div>
				<div class="mo_2fa_feature_block" style="margin-left: 14%;">
					<div class="mo_2fa_email_template_details">
							<i class="fa fa-file-text" style="font-size:50px;color: black;margin-top: 6%"></i>
						<div style="min-height: 150px;">
							<h2 style="color: black;">Support</h2>
							<p style="color: black;padding: 0px 27px;text-align: justify;">You are not going to hit a ridiculously long phone menu when you call us or contact us.</p>
						</div>
						<div>
						<br><br>
							<center>
							<a class="mo_2fa_ef_read_more" href="https://www.miniorange.com/contact">Read More</a>
							</center>
						</div>
					</div>
					<div class="mo_2fa_email_template_details">
							<i class="fa fa-shield" style="font-size:50px;color: black;margin-top: 6%"></i>
						<div style="min-height: 150px;">
							<h2 style="color: black;">Security site</h2>
							<p style="color: black;padding: 0px 27px;text-align: justify;">miniOrange combines Web Application Firewall (WAF),Malware Scanner, Encrypted Database and File backup</p>
						</div>
						<div>
							<br><br>
							<center>
								<a class="mo_2fa_ef_read_more" href="https://security.miniorange.com/">Read More</a>
							</center>
						</div>
					</div>
				</div>
				<div class="myDiv">
		   <h2 style="margin-bottom: -36px;"><b>You are welcome to use our New Features</b></h2>.
			<h2 style="margin-bottom: -36px;"  > Thank you</h2><br>
			<p style="margin-top: 26px;">If you need any help we are just a mail away <p> <br>
			      <p style="margin-top: -47px;"> Contact us at :-  <b>info@xecurify.com /2fasupport@xecurify.com<b></p><br>
			      <p style="margin-top: -10px;"> If you want to disable this notification please turn of the toggle of email from Notification TAB
			      		</p>
			
		</div>
	</div>
</body>
</html>';
}

?>