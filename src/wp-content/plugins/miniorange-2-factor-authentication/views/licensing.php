<?php

echo'	<div class="mo_wpns_table_layout">
		<table class="mo_wpns_local_pricing_table">
		<h2>Licensing Plans
		<span style="float:right"><input type="button" name="ok_btn" id="ok_btn" class="button button-primary button-large" value="OK, Got It" onclick="window.location.href=\''. esc_url($default_url) .'\'" /></span>
		</h2><hr>
		<tr style="vertical-align:top;">
	
				<td>
				<div class="mo_wpns_local_thumbnail mo_wpns_local_pricing_paid_tab" >
				
				<h3 class="mo_wpns_local_pricing_header">Do it yourself</h3>
				<p></p>
				
				
				<hr>
				<p class="mo_wpns_pricing_text" >'.esc_attr($basic_plan_price).'<br>+ <br>
				<span style="font-size:12px">( Additional Discounts available for <br>multiple instances and years)</span><br></p>
				<p><a class="button button-primary button-large" onclick="upgradeform(\'wp_security_pro_basic_plan\')">Click here to upgrade</a></p>
				<hr>
				<p class="mo_wpns_pricing_text" >';
					foreach($basic_plan_features as $feature)
						echo esc_attr($feature) . '<br/><br/>';			
echo'				<hr>
				</p>

				
				<p class="mo_wpns_pricing_text" >Basic Support by Email</p>
				</div></td>
				<td>
				<div class="mo_wpns_local_thumbnail mo_wpns_local_pricing_free_tab" >
				<h3 class="mo_wpns_local_pricing_header">Premium</h3>
				<p></p>
				
				 
				<hr>
				<p class="mo_wpns_pricing_text">'.esc_attr($premium_plan_price).'<br>
				( $60 per hour )<br>
				<span style="font-size:12px">( Additional Discounts available for <br>multiple instances and years)</span><br></p>
				<p><a class="button button-primary button-large" onclick="upgradeform(\'wp_security_pro_premium_plan\')">Click here to upgrade</a></p>
				<hr>
				
				<p class="mo_wpns_pricing_text">';
					foreach($premium_plan_features as $feature)
						echo esc_attr($feature) . '<br/><br/>';				
echo'				<hr>
				</p>
				
				
				
				<p class="mo_wpns_pricing_text">Premium Support Plans Available</p>
				
				</div></td>
			
		</tr>	
		</table>
		<form style="display:none;" id="loginform" action="'.esc_html($form_action).'" 
		target="_blank" method="post">
		<input type="email" name="username" value="'.esc_html($admin_email).'" />
		<input type="text" name="redirectUrl" value="'.esc_url($redirect_url).'" />
		<input type="text" name="requestOrigin" id="requestOrigin"  />
		</form>
		<script>
			function upgradeform(planType){
				$("#requestOrigin").val(planType);
				$("#loginform").submit();
			}
		</script>
		<br>
		<h3>* Steps to upgrade to premium plugin -</h3>
		<p>1. You will be redirected to miniOrange Login Console. Enter your password with which you created an account with us. After that you will be redirected to payment page.</p>
		<p>2. Enter you card details and complete the payment. On successful payment completion, you will see the link to download the premium plugin.</p>
		<p>3. Once you download the premium plugin, just unzip it and replace the folder with existing plugin. </p>
		<b>Note: Do not delete the plugin from the Wordpress Admin Panel and upload the plugin using zip. Your saved settings will get lost.</b>
		<p>4. From this point on, do not update the plugin from the Wordpress store. We will notify you when we upload a new version of the plugin.</p>
		
		<h3>** End to End Integration - We will setup a conference and do end to end configuration for you. We provide services to do the configuration on your behalf. </h3>
		
		<h3>10 Days Return Policy -</h3>
		<p>At miniOrange, we want to ensure you are 100% happy with your purchase. If you feel that the premium plugin you purchased is not the best fit for your requirements or you’ve attempted to resolve any feature issues with our support team, which couldn\'t get resolved. We will refund the whole amount within 10 days of the purchase. Please email us at <a href="mailto:info@xecurify.com">info@xecurify.com</a> for any queries regarding the return policy.<br><br>
		If you have any doubts regarding the licensing plans, you can mail us at <a href="mailto:info@xecurify.com">info@xecurify.com</a> or submit a query using the support form.</p>	

	</div>';