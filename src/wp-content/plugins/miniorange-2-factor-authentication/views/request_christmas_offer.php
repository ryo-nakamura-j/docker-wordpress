<div class="mo_wpns_divided_layout">
	<div class="mo_wpns_setting_layout mo2f_christmas_contact_us_layout">
		<h3>  Request For Christmas Offer : <div style="float: right;">
		<?php
			echo '<a class="mo_wpns_button mo_wpns_button1 mo2f_christmas_contact_us_button" href="'.esc_url($two_fa).'">Back</a>';
		 ?>
		</div></h3>
		<form method="post">
			<input type="hidden" name="option" value="mo_2FA_christmas_request_form" />
			<input type="hidden" name="nonce" value="<?php echo esc_html(wp_create_nonce('mo2f-Request-christmas'))?>">
			<table cellpadding="4" cellspacing="4">
                        <tr>
						  	<td><strong>Usecase : </strong></td>
							<td>
							<textarea type="text"  name="mo_2FA_christmas_usecase" style="resize: vertical; width:350px; height:100px;" rows="4" placeholder="Write us about your usecase" required value=""></textarea>
							</td>


						  </tr> 	
                        <tr>
							<td>						
							</td>
							
						</tr>
			    		<tr>
							<td><strong>Email ID : </strong></td>
							<td><input required type="email" name="mo_2FA_christmas_email" placeholder="Email id" value="" /></td>
						</tr>
                        
			    	</table>
			    	<div style="padding-top: 10px;">
			    		<input type="submit" name="submit" value="Submit Request" class="mo_wpns_button mo_wpns_button1 mo2f_christmas_contact_us_button" />
			    	</div>	
		</form>		
	</div>
</div>