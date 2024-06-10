<?php
	$save_waf_url 	    = add_query_arg( array('option'=>'saveWAF'), 	admin_url() );
	$save_Hwaf_url     	= add_query_arg( array('option'=>'saveHWAF'), 	admin_url() );
	$save_waf_sql     	= add_query_arg( array('option'=>'savesql'), 	admin_url() );
	$save_waf_xss     	= add_query_arg( array('option'=>'savexss'),	admin_url() );
	$save_waf_lfi    	= add_query_arg( array('option'=>'savelfi'), 	admin_url() );
	$save_waf_rfi    	= add_query_arg( array('option'=>'saverfi'), 	admin_url() );
	$save_waf_rce		= add_query_arg( array('option'=>'saverce'), 	admin_url() );
	$save_rateL_details	= add_query_arg( array('option'=>'saveRateL'), 	admin_url() );
	$disable_rateL		= add_query_arg( array('option'=>'disableRL'), 	admin_url() );
	$backup_htaccess	= add_query_arg( array('option'=>'BuHtaccess'), admin_url() );
	$save_limit_attack	= add_query_arg( array('option'=>'limitAttack'),admin_url() );

	$admin_url = admin_url();
	$url = explode('/wp-admin/', $admin_url);
	$url = $url[0].'/htaccess';

	$nameDownload = "htaccessBackup.htaccess";

	
		
?>
<div class="mo_wpns_divided_layout">
	<div class="mo_wpns_setting_layout">
	<table style="width:100%">
		<tr><th align="left">
		<h3>Website Firewall on Plugin Level:
			<br>
			<p><i class="mo_wpns_not_bold">This will activate WAF on plugin level. The Firewall will work after WordPress get loaded. This will check Every Request before the load of plugin.</i></p>
  		</th><th align="right">
  		<label class='mo_wpns_switch'>
		 <input type=checkbox id='pluginWAF' name='pluginWAF' />
		 <span class='mo_wpns_slider mo_wpns_round'></span>
		</label>
		</tr></th>
		 </h3>
		 <tr><th align="left">
	<h3>Website Firewall on .htaccess Level:
		<br>
			<p><i class="mo_wpns_not_bold">This will activate WAF on htaccess level. The Firewall will work before wordpress load. It will make changes to your .htaccess file.<strong> It is the recommended type</strong></i></p>
		</th><th align="right">
		<label class='mo_wpns_switch'>
		 <input type=checkbox id='htaccessWAF' name='htaccessWAF' />
		 <span class='mo_wpns_slider mo_wpns_round'></span>
		</label>
		 </h3></th></tr>
		 </table>
		 <div id ='htaccessChange' name ='htaccessChange'>
		 <h4> This feature will make changes to .htaccess file, Please confirm before the changes<br>
		 	if you have any issue after this change please use the downloaded version as backup.
		 	Rename the file as '.htaccess' [without name just extension] and use it as backup.  
		 	</h4> 
<?php
echo	 "<a href='". esc_url($url)."' download='".esc_html($nameDownload)."'>";?>
		 <input type='button' name='CDhtaccess' id='CDhtaccess' value='Confirm & Download'/>
		 </a>
		 
		 <input type='button' name='cnclDH' id='cnclDH' value='Cancel'/>
		 		

	</div>
	</div>	

	<div name = 'AttackTypes' id ='AttackTypes'>
	<div class="mo_wpns_setting_layout">
	
		<table style="width:100%">
	<tr>
		<th align="left"><h2>	SQL Injection Protection ::
			<br>
			<p><i class="mo_wpns_not_bold">SQL Injection attacks are used for attack on database. This option will block all illegal request which tries to access to database.</i></p>
		</th>  
		<th align="right">
			<label class='mo_wpns_switch'>
			<input type="checkbox" name="SQL" id="SQL"/>
		 	<span class='mo_wpns_slider mo_wpns_round'></span>
			</label>
		</th>

		</h2>

	</tr>
		<br><tr>
		<th align="left"><h2>	XSS Protection :: 
			<br>
			<p><i class="mo_wpns_not_bold">XSS is used for script attacks. This will block illegal scripting on website.</i></p>
		</th>
		<th align="right">
			<label class='mo_wpns_switch'>
			<input type="checkbox" name="XSS" id="XSS"/>
		 	<span class='mo_wpns_slider mo_wpns_round'></span>
			</label>
			</th>
		</h2></tr>
		<br><tr>
		<th align="left"><h2>	Remote File Inclusion Protection ::  
			<br>
			<p><i class="mo_wpns_not_bold">This option will block Remote File Inclusion</i></p>
		</th>
		<th align="right">
			<label class='mo_wpns_switch'>
			<input type="checkbox" name="RFI" id="RFI"/>
		 	<span class='mo_wpns_slider mo_wpns_round'></span>
			</label>
		</th>
		</h2></tr>
		<br><tr>
		<th align="left"><h2>	Local File Inclusion Protection ::  
				<br>
			<p><i class="mo_wpns_not_bold">This option will block Local File Inclusion</i></p>
		</th>
		<th align="right">
			<label class='mo_wpns_switch'>
			<input type="checkbox" name="LFI" id="LFI"/>
		 	<span class='mo_wpns_slider mo_wpns_round'></span>
			</label>
		</th>
		</h2></tr>
		<br>
		<tr>
		<th align="left"><h2>	Remote Code Execution Protection ::
			<br>
			<p><i class="mo_wpns_not_bold">This option will block Remote File Inclusion</i></p>
		</th>  
		<th align="right">
			<label class='mo_wpns_switch'>
			<input type="checkbox" name="RCE" id="RCE"/>
		 	<span class='mo_wpns_slider mo_wpns_round'></span>
			</label>
		</th>
		</h2>
	</tr>
		<tr>
		<th align="left"><h2> limit of attacks::</th>  
		<th>
			
			<input type ="number" name ="limitAttack" id = "limitAttack" required min="5"/>
		 	
		 	<input type="button" name="saveLimitAttacks" id="saveLimitAttacks" value="save limit"/>
		</th>
		</h2>
		</tr>
	
		</table>
		<br>
	</div>
	</div>
	</div>	
	
	



<script type="text/javascript">
		document.getElementById('AttackTypes').style.display = "none";
		document.getElementById('htaccessChange').style.display="none";		

		var wafE 			= "<?php echo esc_html(get_option('WAFEnabled'));?>";
		var WAF 			= "<?php echo esc_html(get_option('WAF'));?>";
		var SQL 			= "<?php echo esc_html(get_option('SQLInjection'));?>";
		var XSS 			= "<?php echo esc_html(get_option('XSSAttack'));?>";
		var LFI 			= "<?php echo esc_html(get_option('LFIAttack'));?>";
		var RFI 			= "<?php echo esc_html(get_option('RFIAttack'));?>";
		var RCE 			= "<?php echo esc_html(get_option('RCEAttack'));?>";

		if(wafE=='1')
		{	
			document.getElementById('AttackTypes').style.display="block";
	
			if(WAF == 'PluginLevel')
			{
				jQuery('#pluginWAF').prop("checked",true);
			}
			else if(WAF == 'HtaccessLevel')
			{
				jQuery('#htaccessWAF').prop("checked",true);
			}
			if(SQL == '1')
			{
				jQuery('#SQL').prop("checked",true);	
			}
			if(XSS == '1')
			{
				jQuery('#XSS').prop("checked",true);	
			}
			if(LFI == '1')
			{
				jQuery('#LFI').prop("checked",true);	
			}
			if(RFI == '1')
			{
				jQuery('#RFI').prop("checked",true);	
			}
			if(RCE == '1')
			{
				jQuery('#RCE').prop("checked",true);
			}
		}
		
		jQuery('#SQL').click(function(){
			var SQL = jQuery("input[name='SQL']:checked").val();
			var url =  '<?php echo esc_html($save_waf_sql); ?>';
			jQuery.ajax({
					url:url,
					method: "post",
					data : {'SQL':SQL},
					success: function(response)
					{
						
					}	

			});
			


		});


		jQuery('#saveLimitAttacks').click(function(){
			var limitAttack = jQuery("#limitAttack").val();
			var url 		= '<?php echo esc_html($save_limit_attack); ?>';
			if(limitAttack != '')
			{
				jQuery.ajax({
					url:url,
					method: "post",
					data : {'limitAttack':limitAttack},
					success: function(response)
					{
															
					}	

				});
			}


		});

		jQuery('#RCE').click(function(){
			var RCE = jQuery("input[name='RCE']:checked").val();
			var url =  '<?php echo esc_html($save_waf_rce); ?>';
			jQuery.ajax({
					url:url,
					method: "post",
					data : {'RCE':RCE},
					success: function(response)
					{
					}	

			});
			


		});

		jQuery('#XSS').click(function(){
			var XSS = jQuery("input[name='XSS']:checked").val();
			var url =  '<?php echo esc_html($save_waf_xss); ?>';
			jQuery.ajax({
					url:url,
					method: "post",
					data : {'XSS':XSS},
					success: function(response)
					{

						
					}	

			});
			


		});
		jQuery('#LFI').click(function(){
			var LFI = jQuery("input[name='LFI']:checked").val();
			var url =  '<?php echo esc_html($save_waf_lfi); ?>';
			jQuery.ajax({
					url:url,
					method: "post",
					data : {'LFI':LFI},
					success: function(response)
					{
						
					}	

			});
			


		});
		jQuery('#RFI').click(function(){
			var RFI = jQuery("input[name='RFI']:checked").val();
			var url =  '<?php echo esc_html($save_waf_rfi); ?>';
			jQuery.ajax({
					url:url,
					method: "post",
					data : {'RFI':RFI},
					success: function(response)
					{
						
					}	

			});
			


		});
		
		jQuery('#pluginWAF').click(function(){
			var pluginWAF = jQuery("input[name='pluginWAF']:checked").val();
			var htaccessWAF = jQuery("input[name='htaccessWAF']:checked").val();
			var url =  '<?php echo esc_url($save_waf_url); ?>';
			jQuery.ajax({
					url:url,
					method: "post",
					data : {'pluginWAF':pluginWAF},
					success: function(response)
					{
						if(pluginWAF == 'on')
						{
							document.getElementById('AttackTypes').style.display="block";
							var SQL ="<?php echo esc_html(get_option('SQLInjection'));?>";
							var XSS ="<?php echo esc_html(get_option('XSSAttack'));?>";
							var LFI ="<?php echo esc_html(get_option('LFIAttack'));?>";
							var RFI ="<?php echo esc_html(get_option('RFIAttack'));?>";
							var RCE ="<?php echo esc_html(get_option('RCEAttack'));?>";
							if(SQL == '1')
							{
								jQuery('#SQL').prop("checked",true);	
							}
							if(XSS == '1')
							{
								jQuery('#XSS').prop("checked",true);	
							}
							if(LFI == '1')
							{
								jQuery('#LFI').prop("checked",true);	
							}
							if(RFI == '1')
							{
								jQuery('#RFI').prop("checked",true);	
							}
							if(RCE == '1')
							{
								jQuery('#RCE').prop("checked",true);	
							}
						}
						else
						{
							document.getElementById('AttackTypes').style.display="none";
						}
					}	

			});
			var url =  '<?php echo esc_html($save_Hwaf_url); ?>';
			if(htaccessWAF=='on' && pluginWAF=='on')
			{
				
				jQuery('#htaccessWAF').prop("checked",false);
				document.getElementById("htaccessWAF").disabled = false;
				document.getElementById("htaccessChange").style.display = "none";

			
			}

			

		});
		jQuery('#htaccessWAF').click(function(){

			var pluginWAF = jQuery("input[name='pluginWAF']:checked").val();
			var htaccessWAF = jQuery("input[name='htaccessWAF']:checked").val();
			var url =  '<?php echo esc_url($save_Hwaf_url); ?>';
			if(htaccessWAF =='on')
			{
				document.getElementById("htaccessChange").style.display ="block";
				document.getElementById("htaccessWAF").disabled = true;
			}
			else
			{
				document.getElementById("htaccessChange").style.display ="none";	
			}

			if(htaccessWAF != 'on')
			{
				jQuery.ajax({
						url:url,
						method:"post",
						data:{'htaccessWAF':htaccessWAF},
						success: function(response)
						{
							document.getElementById('AttackTypes').style.display="none";
							
						}

				});
			}
			else
			{
				var url = '<?php echo esc_html($backup_htaccess); ?>';
				jQuery.ajax({
						url:url,
						method:"post",
						data:{'htaccessWAF':htaccessWAF},
						success: function(response)
						{
							document.getElementById('AttackTypes').style.display="none";
							
						}

				});	
			}

			
			
		});
		jQuery('#cnclDH').click(function(){
			var pluginWAF = jQuery("input[name='pluginWAF']:checked").val();
			document.getElementById("htaccessChange").style.display = "none";
			if(pluginWAF == 'on')
			{
				jQuery('#pluginWAF').prop("checked",true);
				document.getElementById('AttackTypes').style.display = "block";
	
				
			}
			jQuery('#htaccessWAF').prop("checked",false);
			document.getElementById("htaccessWAF").disabled = false;

		});
		jQuery('#CDhtaccess').click(function(){

			var url =  '<?php echo esc_url($save_Hwaf_url); ?>';
			var pluginWAF = jQuery("input[name='pluginWAF']:checked").val();
			var htaccessWAF = jQuery("input[name='htaccessWAF']:checked").val();
		
			jQuery.ajax({
					url:url,
					method:"post",
					data:{'htaccessWAF':htaccessWAF},
					success: function(response)
					{
						if(htaccessWAF=='on')
						{	
							document.getElementById('AttackTypes').style.display="block";
							var XSS ="<?php echo esc_html(get_option('XSSAttack'));?>";
							var SQL ="<?php echo esc_html(get_option('SQLInjection'));?>";
							var LFI ="<?php echo esc_html(get_option('LFIAttack'));?>";
							var RFI ="<?php echo esc_html(get_option('RFIAttack'));?>";
							var RCE ="<?php echo esc_html(get_option('RCEAttack'));?>";
							if(SQL == '1')
							{
								jQuery('#SQL').prop("checked",true);	
							}
							if(XSS == '1')
							{
								jQuery('#XSS').prop("checked",true);	
							}
							if(LFI == '1')
							{
								jQuery('#LFI').prop("checked",true);	
							}
							if(RFI == '1')
							{
								jQuery('#RFI').prop("checked",true);	
							}
							if(RCE == '1')
							{
								jQuery('#RCE').prop("checked",true);	
							}

						}
						else
						{
							document.getElementById('AttackTypes').style.display="none";
						}
					}

			});
			if(htaccessWAF=='on' && pluginWAF=='on')
			{
				jQuery('#pluginWAF').prop("checked",false);	
			}
			document.getElementById("htaccessChange").style.display = "none";
			document.getElementById("htaccessWAF").disabled = false;

		});

</script>
