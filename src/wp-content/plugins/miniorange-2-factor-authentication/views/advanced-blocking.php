<div class="mo_wpns_divided_layout_tab">
<div class="nav-tab-wrapper">
  <button class="nav-tab" onclick="mo2f_wpns_block_function(this)" id="mo2f_block_list">IP Black list</button>
  <button class="nav-tab" onclick="mo2f_wpns_block_function(this)" id="mo2f_adv_block">Advanced Blocking</button>

</div>
</div>
<?php
global $mo2f_dirName;
$setup_dirName = $mo2f_dirName.'views'.DIRECTORY_SEPARATOR.'twofa'.DIRECTORY_SEPARATOR.'link_tracer.php';
include $setup_dirName;
?>
<div id="mo2f_block_list_div" class="tabcontent">
 
 <div class="mo_wpns_divided_layout">
		<div class="mo_wpns_setting_layout" id="mo2f_manual_ip_blocking">
					<h2>Manual IP Blocking <a href='<?php echo esc_url($two_factor_premium_doc['Manual IP Blocking']);?>' target="_blank"><span class="dashicons dashicons-text-page" style="font-size:30px;color:#413c69;float: right;"></span></a></h2>
					
					<h4 class="mo_wpns_setting_layout_inside">Manually block an IP address here:&emsp;&emsp;
					<input type="text" name="ManuallyBlockIP" id="ManuallyBlockIP" required placeholder='IP address'pattern="((^|\.)((25[0-5])|(2[0-4]\d)|(1\d\d)|([1-9]?\d))){4}" style="width: 35%; height: 41px" />&emsp;&emsp;
					<input type="button" name="BlockIP" id="BlockIP" value="Manual Block IP" class="button button-primary button-large" />
					</h4>

					<h3 class="mo_wpns_setting_layout_inside"><b>Blocked IPs</b>
					</h3>
					<h4 class="mo_wpns_setting_layout_inside">&emsp;&emsp;&emsp;

			<div id="blockIPtable">
				<table id="blockedips_table" class="display">
				<thead><tr><th>IP Address&emsp;&emsp;</th><th>Reason&emsp;&emsp;</th><th>Blocked Until&emsp;&emsp;</th><th>Blocked Date&emsp;&emsp;</th><th>Action&emsp;&emsp;</th></tr></thead>
				<tbody>
					
<?php			
			$mo_wpns_handler 	= new MoWpnsHandler();
			$blockedips 		= $mo_wpns_handler->get_blocked_ips();
			$whitelisted_ips 	= $mo_wpns_handler->get_whitelisted_ips();
			$disabled = '';
			global $mo2f_dirName;
			foreach($blockedips as $blockedip)
			{
echo 			"<tr class='mo_wpns_not_bold'><td>".esc_attr($blockedip->ip_address)."</td><td>".esc_attr($blockedip->reason)."</td><td>";
				if(empty($blockedip->blocked_for_time)) 
echo 				"<span class=redtext>Permanently</span>"; 
				else 
echo 				date("M j, Y, g:i:s a",esc_attr($blockedip->blocked_for_time));
echo 			"</td><td>".date("M j, Y, g:i:s a",esc_attr($blockedip->created_timestamp))."</td><td><a ".esc_html($disabled)." onclick=unblockip('".esc_attr($blockedip->id)."')>Unblock IP</a></td></tr>";
			} 
?>
					</tbody>
					</table>
			</div>	
				</h4>
		</div>
		<div class="mo_wpns_setting_layout"  id="mo2f_ip_whitelisting">
					<h2>IP Whitelisting<a href="https://developers.miniorange.com/docs/security/wordpress/wp-security/IP-blocking-whitelisting-lookup#wp-ip-whitelisting" target="_blank"><span class="dashicons dashicons-text-page" style="font-size:30px;color:#413c69;float: right;"></span></a></h2>
					<h4 class="mo_wpns_setting_layout_inside">Add new IP address to whitelist:&emsp;&emsp;
					<input type="text" name="IPWhitelist" id="IPWhitelist" required placeholder='IP address'pattern="((^|\.)((25[0-5])|(2[0-4]\d)|(1\d\d)|([1-9]?\d))){4}" style="width: 40%; height: 41px"/>&emsp;&emsp;
					<input type="button" name="WhiteListIP" id="WhiteListIP" value="Whitelist IP" class="button button-primary button-large" />
	
					</h4>
					<h3 class="mo_wpns_setting_layout_inside">Whitelist IPs
					</h3>
					<h4 class="mo_wpns_setting_layout_inside">&emsp;&emsp;&emsp;

			<div id="WhiteListIPtable">
				<table id="whitelistedips_table" class="display">
				<thead><tr><th>IP Address</th><th>Whitelisted Date</th><th>Remove from Whitelist</th></tr></thead>
				<tbody>
<?php
					foreach($whitelisted_ips as $whitelisted_ip)
					{
						echo "<tr class='mo_wpns_not_bold'><td>".esc_attr($whitelisted_ip->ip_address)."</td><td>".date("M j, Y, g:i:s a",$whitelisted_ip->created_timestamp)."</td><td><a ".esc_html($disabled)." onclick=removefromwhitelist('".esc_html($whitelisted_ip->id)."')>Remove</a></td></tr>";
					} 

echo'			</tbody>
			</table>';
?>
			</div>
				</h4>
		</div>			



		<div class="mo_wpns_setting_layout" id="mo2f_ip_lookup">
					<h2>IP LookUp<a href='<?php echo esc_url($two_factor_premium_doc['IP LookUp']);?>' target="_blank"><span class="dashicons dashicons-text-page" style="font-size:30px;color:#413c69;float: right;"></span></a></h2>
					<h4 class="mo_wpns_setting_layout_inside">Enter IP address you Want to check:&emsp;&emsp;
					<input type="text" name="ipAddresslookup" id="ipAddresslookup" required placeholder='IP address'pattern="((^|\.)((25[0-5])|(2[0-4]\d)|(1\d\d)|([1-9]?\d))){4}" style="width: 40%; height: 41px"/>&emsp;&emsp;
					<input type="button" name="LookupIP" id="LookupIP" value="LookUp IP" class="button button-primary button-large" />
					</h4>
					<div class="ip_lookup_desc" hidden ></div>
					
					<div id="resultsIPLookup">
					</div>
		</div>		
</div>
</div>


<?php
echo '<div id="mo2f_adv_block_div" class="tabcontent">';
echo'<div class="mo_wpns_divided_layout">
		<div class="mo_wpns_setting_layout"  id= "mo2f_ip_range_blocking">';

echo'		<h2>IP Address Range Blocking<a href='.esc_url($two_factor_premium_doc['IP Address Range Blocking']).' target="_blank"><span class="dashicons dashicons-text-page" style="font-size:23px;color:#413c69;float: right;"></span></a></h2>
			You can block range of IP addresses here  ( Examples: 192.168.0.100 - 192.168.0.190 )
			<form name="f" method="post" action="" id="iprangeblockingform" >
				<input type="hidden" name="option" value="mo_wpns_block_ip_range" />

			<br>
			<table id="iprangetable">		
';			
		for($i = 1 ; $i <= $range_count ; $i++)
		{
echo '<tr><td>Start IP	<input style="width :30%" type ="text" class="mo_wpns_table_textbox" name="start_'.intval(esc_html($i)).'" value ="'.esc_html($start[$i]).'" placeholder=" e.g 192.168.0.100" />End IP	<input style="width :30%" type ="text" placeholder=" e.g 192.168.0.190" class="mo_wpns_table_textbox" value="'.esc_html($end[$i]).'"  name="end_'.intval(esc_html($i)).'"/></td></tr>';
}		
echo '
		</table>
		<a style="cursor:pointer" id="add_ran">Add IP Range</a>
			';

			echo'	<br><input type="submit" class="button button-primary button-large" value="Block IP range" />
				
			</form>
		</div>
		
		<div class="mo_wpns_setting_layout" id="mo2f_htaccess_blocking">
			<h3>htaccess level blocking</h3>
			<p>It help you secure your website from unintended user with htaccess website security protection which blocks user request on server(apache) level before reaching your WordPress and saves lots of load on server.</p>

			<form id="mo_wpns_enable_htaccess_blocking" method="post" action="">
				<input type="hidden" name="option" value="mo_wpns_enable_htaccess_blocking">
				<b style="padding-right:10px;">Enable htaccess level security</b>
				<label class="mo_wpns_switch_small">
				<input type="checkbox" name="mo_wpns_enable_htaccess_blocking" '.esc_html($htaccess_block).' onchange="document.getElementById(\'mo_wpns_enable_htaccess_blocking\').submit();">
				<span class="mo_wpns_slider_small mo_wpns_round_small"></span>
				</label>
			</form>
			<br>
		</div>
		
		
		<div class="mo_wpns_setting_layout" id="mo2f_browser_blocking">
			<h3>Browser Blocking<a href='.esc_url($two_factor_premium_doc['Browser Blocking']).' target="_blank"><span class="dashicons dashicons-text-page" style="font-size:30px;color:#413c69;float: right;"></span></a></h3>
			<!-- <div class="mo_wpns_subheading">This protects your site from robots and other automated scripts.</div> -->
			<form id="mo_wpns_enable_user_agent_blocking" method="post" action="">
				<input type="hidden" name="option" value="mo_wpns_enable_user_agent_blocking">
				<b style="padding-right:10px;">Enable Browser Blocking</b>
				<label class="mo_wpns_switch_small">
				<input type="checkbox" name="mo_wpns_enable_user_agent_blocking" '.esc_html($user_agent).' onchange="document.getElementById(\'mo_wpns_enable_user_agent_blocking\').submit();">
				<span class="mo_wpns_slider_small mo_wpns_round_small"></span>
				</label>
			</form><br>
			<div style="margin-bottom:10px">Select browsers below to block</div>
			<form id="mo_wpns_browser_blocking" method="post" action="">
				<input type="hidden" name="option" value="mo_wpns_browser_blocking">
				<table style="width:100%">
				<tr>
					<td width="33%"><input type="checkbox" name="mo_wpns_block_chrome" '.esc_html($block_chrome).' > Google Chrome '.($current_browser=='chrome' ? wp_kses_post(MoWpnsConstants::CURRENT_BROWSER) : "").'</td>
					<td width="33%"><input type="checkbox" name="mo_wpns_block_firefox" '.esc_html($block_firefox).' > Firefox '.($current_browser=='firefox' ? wp_kses_post(MoWpnsConstants::CURRENT_BROWSER) : "").'</td>
					<td width="33%"><input type="checkbox" name="mo_wpns_block_ie" '.esc_html($block_ie).' > Internet Explorer '.($current_browser=='ie' ? wp_kses_post(MoWpnsConstants::CURRENT_BROWSER) : "").'</td>
				</tr>
				<tr>
					<td width="33%"><input type="checkbox" name="mo_wpns_block_safari" '.esc_html($block_safari).' > Safari '.($current_browser=='safari' ? wp_kses_post(MoWpnsConstants::CURRENT_BROWSER) : "").'</td>
					<td width="33%"><input type="checkbox" name="mo_wpns_block_opera" '.esc_html($block_opera).' > Opera '.($current_browser=='opera' ? wp_kses_post(MoWpnsConstants::CURRENT_BROWSER) : "").'</td>
					<td width="33%"><input type="checkbox" name="mo_wpns_block_edge" '.esc_html($block_edge).' > Microsoft Edge '.($current_browser=='edge' ? wp_kses_post(MoWpnsConstants::CURRENT_BROWSER) : "").'</td>
				</tr>
				</table>
				<br>
				<input type="submit" class="button button-primary button-large" value="Save Configuration" />
			</form>
			<br>
		</div> 

		<div class="mo_wpns_setting_layout">
		<h3>Block HTTP Referer\'s</h3>
			An "HTTP Referer" is anything online that drives visitors to your website which includes search engines, weblogs link lists, emails etc.<br>
			Examples : google.com
			<form name="f" method="post" action="" id="referrerblockingform" >
				<input type="hidden" name="option" value="mo_wpns_block_referrer" />
				<table id="referrerblockingtable">';

					$count=1;
					foreach($referrers as $referrer)
					{
						echo '<tr><td style="width:300px"><input style="padding:0px 10px" class="mo_wpns_table_textbox" type="text" name="referrer_'.intval(esc_html($count)).'"
						 value="'.esc_url($referrer).'"  placeholder=" e.g  google.com" /></td></tr>';
						$count++;
					}

echo'			</table>
				<a style="cursor:pointer" id="add_referer">Add More Referer\'s</a><br><br>
				<input type="submit" class="button button-primary button-large" value="Block Referers" />
			</form>
			<br>
		</div> 
		
		<div class="mo_wpns_setting_layout" id="mo2f_country_blocking">
			<h2>Country Blocking</h2>
			<b>Select countries from below which you want to block.</b><br><br>
			<form name="f" method="post" action="" id="countryblockingform" >
				<input type="hidden" name="option" value="mo_wpns_block_countries" />
				<table id="countryblockingtable" style="width:100%">';
						
						foreach($country as $key => $value)
							echo '<tr class="one-third"><td><input type="checkbox" name="'.esc_html($key).'"/ >'.esc_html($value).'</td></tr>';

echo'			</table><br>
				<input type="submit" class="button button-primary button-large" value="Save" />
			</form>
		</div>
	</div>
	</div>
	<script>		
		jQuery( document ).ready(function() {
			var countrycodes = "'.esc_html($codes).'";
			var countrycodesarray = countrycodes.split(";");
			for (i = 0; i < countrycodesarray.length; i++) { 
				if(countrycodesarray[i]!="")
					$("#countryblockingform :input[name=\'"+countrycodesarray[i]+"\']").prop("checked", true);
			}

			$("#add_range").click(function() {
				var last_index_name = $("#iprangeblockingtable tr:last .mo_wpns_table_textbox").attr("name");
				var splittedArray = last_index_name.split("_");
				var last_index = parseInt(splittedArray[splittedArray.length-1])+1;

				var new_row   = \'<tr><td><input style="padding:0px 10px" class="mo_wpns_table_textbox" type="text" name="range_\'+last_index+\'" value=""   placeholder=" e.g 192.168.0.100 - 192.168.0.190" /></td></tr>\';
				$("#iprangeblockingtable tr:last").after(new_row);
			});

			$("#add_ran").click(function() {
				var last_index_name = $("#iprangetable tr:last .mo_wpns_table_textbox").attr("name");
				
				var splittedArray = last_index_name.split("_");
				var last_index = parseInt(splittedArray[splittedArray.length-1])+1;
				var new_row = \'<tr><td>Start IP<input style="width :30%" type ="text" class="mo_wpns_table_textbox" name="start_\'+last_index+\'" value="" placeholder=" e.g 192.168.0.100" >&nbsp;&nbsp;End IP	<input style="width :30%" type ="text" placeholder=" e.g 192.168.0.190" class="mo_wpns_table_textbox" value="" name="end_\'+last_index+\'"></td></tr>\';
				$("#iprangetable tr:last").after(new_row);
			
			});

			$("#add_referer").click(function() {
				var last_index_name = $("#referrerblockingtable tr:last .mo_wpns_table_textbox").attr("name");
				var splittedArray = last_index_name.split("_");
				var last_index = parseInt(splittedArray[splittedArray.length-1])+1;
				var new_row = \'<tr><td><input style="padding:10px 0px" class="mo_wpns_table_textbox" type="text" name="referrer_\'+last_index+\'" value=""   placeholder=" e.g  google.com" /></td></tr>\';
				$("#referrerblockingtable tr:last").after(new_row);
			});
	
		});
	</script>';

	?>
<script type="text/javascript">

function mo2f_wpns_block_function(elmt){
		var tabname = elmt.id;
		var tabarray = ["mo2f_block_list","mo2f_adv_block"];
		for (var i = 0; i < tabarray.length; i++) {
			if(tabarray[i] == tabname){
				jQuery("#"+tabarray[i]).addClass("nav-tab-active");
				jQuery("#"+tabarray[i]+"_div").css("display", "block");
			}else{
				jQuery("#"+tabarray[i]).removeClass("nav-tab-active");
				jQuery("#"+tabarray[i]+"_div").css("display", "none");
			}
		}
		localStorage.setItem("ip_last_tab", tabname);
	}
	
	jQuery('#adv_block_tab').addClass('nav-tab-active');

	var tab = localStorage.getItem("ip_last_tab"); 

	if(tab)
		document.getElementById(tab).click();
	else{
		document.getElementById("mo2f_block_list").click();
	}


jQuery('#BlockIP').click(function(){

	var ip 	= jQuery('#ManuallyBlockIP').val();

	var nonce = '<?php echo esc_html(wp_create_nonce("manualIPBlockingNonce"));?>';
	if(ip != '')
	{
		var data = {
		'action'					: 'wpns_login_security',
		'wpns_loginsecurity_ajax' 	: 'wpns_ManualIPBlock_form', 
		'IP'						:  ip,
		'nonce'						:  nonce,
		'option'					: 'mo_wpns_manual_block_ip'
		};
		jQuery.post(ajaxurl, data, function(response) {
				var response = response.replace(/\s+/g,' ').trim();
				if(response == 'empty IP')
				{
                    error_msg("IP can not be blank.");
				}
				else if(response == 'already blocked')
				{
                    error_msg("IP is already blocked.");
				}
				else if(response == "INVALID_IP_FORMAT")
				{
                    error_msg("IP does not match required format.");
				}
				else if(response == "IP_IN_WHITELISTED")
				{
                    error_msg("IP is whitelisted can not be blocked.");

				}
				else
				{
					refreshblocktable(response);
                    success_msg("IP Blocked Sucessfully.");
				}
		
		});
					
	}

});

jQuery('#WhiteListIP').click(function(){

	var ip 	= jQuery('#IPWhitelist').val();

	var nonce = '<?php echo esc_html(wp_create_nonce("IPWhiteListingNonce"));?>';
	if(ip != '')
	{
		var data = {
		'action'					: 'wpns_login_security',
		'wpns_loginsecurity_ajax' 	: 'wpns_WhitelistIP_form', 
		'IP'						:  ip,
		'nonce'						:  nonce,
		'option'					: 'mo_wpns_whitelist_ip'
		};
		jQuery.post(ajaxurl, data, function(response) {
				
				var response = response.replace(/\s+/g,' ').trim();
				if(response == 'EMPTY IP')
				{
                    error_msg("IP can not be empty.");
				}
				else if(response == 'INVALID_IP')
				{
                    error_msg(" IP does not match required format.");
				}
				else if(response == 'IP_ALREADY_WHITELISTED')
				{
                    error_msg("IP is already whitelisted.");
				}
				else
				{
                    refreshWhiteListTable(response);
                    success_msg("IP whitelisted Sucessfully.");
				}
		});
					
	}

});

jQuery("#blockedips_table").DataTable({
				"order": [[ 3, "desc" ]]
			});
jQuery("#whitelistedips_table").DataTable({
				"order": [[ 1, "desc" ]]
			});

jQuery('#LookupIP').click(function(){
			jQuery('#resultsIPLookup').empty();
			var ipAddress 	= jQuery('#ipAddresslookup').val();
			var nonce 		= '<?php echo esc_html(wp_create_nonce("IPLookUPNonce"));?>';
			jQuery("#resultsIPLookup").empty();
			jQuery("#resultsIPLookup").append("<img src='<?php if(isset($img_loader_url))echo esc_url($img_loader_url);?>'>");
			jQuery("#resultsIPLookup").slideDown(400);
			var data = {
				'action'					: 'wpns_login_security',
				'wpns_loginsecurity_ajax' 	: 'wpns_ip_lookup',
				'nonce'						:  nonce,
				'IP'						:  ipAddress
				};
				jQuery.post(ajaxurl, data, function(response) {
					if(response == 'INVALID_IP_FORMAT')
					{
						jQuery("#resultsIPLookup").empty();
                        error_msg("IP did not match required format.");
					}
					else if(response == 'INVALID_IP')
					{
						jQuery("#resultsIPLookup").empty();
                        error_msg("IP entered is invalid.");
					}
					else if(response.geoplugin_status == 404)
					{
						jQuery("#resultsIPLookup").empty();
                        success_msg(" IP details not found.");
					}
					else if (response.geoplugin_status == 200 ||response.geoplugin_status == 206) {
						   jQuery('#resultsIPLookup').empty();
				           jQuery('#resultsIPLookup').append(response.ipDetails);
				    }
					
				});
		});

function unblockip(id) {
  var nonce = '<?php echo esc_html(wp_create_nonce("manualIPBlockingNonce"));?>';
	if(id != '')
	{
		var data = {
		'action'					: 'wpns_login_security',
		'wpns_loginsecurity_ajax' 	: 'wpns_ManualIPBlock_form', 
		'id'						:  id,
		'nonce'						:  nonce,
		'option'					: 'mo_wpns_unblock_ip'
		};
		jQuery.post(ajaxurl, data, function(response) {
			var response = response.replace(/\s+/g,' ').trim();
			if(response=="UNKNOWN_ERROR")
			{
                error_msg(" Unknow Error occured while unblocking IP.");
			}
			else
			{
                refreshblocktable(response);
                success_msg("IP unblocked Sucessfully.");
			}
		});
					
	}
}
function removefromwhitelist(id)
{
	var nonce = '<?php echo esc_html(wp_create_nonce("IPWhiteListingNonce"));?>';
	if(id != '')
	{
		var data = {
		'action'					: 'wpns_login_security',
		'wpns_loginsecurity_ajax' 	: 'wpns_WhitelistIP_form', 
		'id'						:  id,
		'nonce'						:  nonce,
		'option'					: 'mo_wpns_remove_whitelist'
		};
		jQuery.post(ajaxurl, data, function(response) {
				var response = response.replace(/\s+/g,' ').trim();
				if(response == 'UNKNOWN_ERROR')
				{
                    error_msg(" Unknow Error occured while removing IP from Whitelist.");
				}
				else
				{
                    refreshWhiteListTable(response);
                    success_msg("IP removed from Whitelist.");
                }
		});
					
	}
}
function refreshblocktable(html)
{
	 jQuery('#blockIPtable').html(html);
}

function refreshWhiteListTable(html)
{
	 
	 jQuery('#WhiteListIPtable').html(html);	
}

</script>