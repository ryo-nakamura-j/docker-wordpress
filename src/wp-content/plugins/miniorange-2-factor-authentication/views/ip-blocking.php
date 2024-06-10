<?php

echo'<div class="mo_wpns_divided_layout">
		<div class="mo_wpns_setting_layout">';

echo'	<h2>IP LookUP<a href="https://developers.miniorange.com/docs/security/wordpress/wp-security/IP-blocking-whitelisting-lookup#wp-ip-lookup" target="_blank"><span class="dashicons dashicons-text-page" style="font-size:30px;color:#413c69;float: right;"></span></a></h2>
			<form name="f" method="post" action="" id="iplookup">
				<input type="hidden" name="option" value="mo_wpns_ip_lookup" />
				<table style="width:100%;">
					<tr>
						<td>Enter IP Address : </td>
						<td style="padding:0px 10px"><input class="mo_wpns_table_textbox" id="lookupip" type="text" name="lookupip"
							required placeholder="IP address" value=""  pattern="((^|\.)((25[0-5])|(2[0-4]\d)|(1\d\d)|([1-9]?\d))){4}" /></td>
						<td><input type="submit" id="lookup-button" class="button button-primary button-large" value="Lookup IP" /></td>
					</tr>
					<tr><td colspan="3"><div class="ip_lookup_desc" hidden ></div><td></tr>
				</table>
			</form>
		</div>';


echo'	<div class="mo_wpns_setting_layout">';

echo'		<h2>Manual Block IP\'s</h2>
			<form name="f" method="post" action="" id="manualblockipform" >
				<input type="hidden" name="option" value="mo_wpns_manual_block_ip" />
				<table><tr><td>You can manually block an IP address here: </td>
				<td style="padding:0px 10px"><input class="mo_wpns_table_textbox" type="text" name="ip"
					required placeholder="IP address" value="" pattern="((^|\.)((25[0-5])|(2[0-4]\d)|(1\d\d)|([1-9]?\d))){4}" /></td>
				<td><input type="submit" class="button button-primary button-large" value="Manual Block IP" /></td></tr></table>
			</form>
			<h2>Blocked IP\'s</h2>
			<table id="blockedips_table" class="display">
				<thead><tr><th width="14%">IP Address</th><th width="25%">Reason</th><th width="24%">Blocked Until</th><th width="25%">Blocked Date</th><th width="20%">Action</th></tr></thead>
				<tbody>';
					
					foreach($blockedips as $blockedip)
					{
echo 					"<tr><td>".esc_attr($blockedip->ip_address)."</td><td>".esc_attr($blockedip->reason)."</td><td>";
						if(empty($blockedip->blocked_for_time)) 
echo 						"<span class=redtext>Permanently</span>"; 
						else 
echo 						date("M j, Y, g:i:s a",esc_attr($blockedip->blocked_for_time));
echo 					"</td><td>".date("M j, Y, g:i:s a",esc_attr($blockedip->created_timestamp))."</td><td><a onclick=unblockip('".esc_attr($blockedip->id)."')>Unblock IP</a></td></tr>";
					} 

echo'			</tbody>
			</table>
		</div>
		<form class="hidden" id="unblockipform" method="POST">
			<input type="hidden" name="option" value="mo_wpns_unblock_ip" />
			<input type="hidden" name="entryid" value="" id="unblockipvalue" />
		</form>
		
		<div class="mo_wpns_setting_layout">
			<h2>Whitelist IP\'s</h2>
			<form name="f" method="post" action="" id="whitelistipform">
				<input type="hidden" name="option" value="mo_wpns_whitelist_ip" />
				<table><tr><td>Add new IP address to whitelist : </td>
				<td style="padding:0px 10px"><input class="mo_wpns_table_textbox" type="text" name="ip"
					required placeholder="IP address" value=""  pattern="((^|\.)((25[0-5])|(2[0-4]\d)|(1\d\d)|([1-9]?\d))){4}" /></td>
				<td><input type="submit" class="button button-primary button-large" value="Whitelist IP" /></td></tr></table>
			</form>
			<h2>Whitelisted IP\'s</h2>
			<table id="whitelistedips_table" class="display">
				<thead><tr><th width="30%">IP Address</th><th width="40%">Whitelisted Date</th><th width="30%">Remove from Whitelist</th></tr></thead>
				<tbody>';

					foreach($whitelisted_ips as $whitelisted_ip)
					{
						echo "<tr><td>".esc_attr($whitelisted_ip->ip_address)."</td><td>".date("M j, Y, g:i:s a",esc_attr($whitelisted_ip->created_timestamp))."</td><td><a onclick=removefromwhitelist('".esc_attr($whitelisted_ip->id)."')>Remove</a></td></tr>";
					} 

echo'			</tbody>
			</table>
		</div>
		<form class="hidden" id="removefromwhitelistform" method="POST">
			<input type="hidden" name="option" value="mo_wpns_remove_whitelist" />
			<input type="hidden" name="entryid" value="" id="removefromwhitelistentry" />
		</form>

	</div>
	<script>
		function unblockip(entryid){
			$("#unblockipvalue").val(entryid);
			$("#unblockipform").submit();
		}
		function removefromwhitelist(entryid){
			$("#removefromwhitelistentry").val(entryid);
			$("#removefromwhitelistform").submit();
		}
		jQuery(document).ready(function() {
			$("#whitelistedips_table").DataTable({
				"order": [[ 1, "desc" ]]
			});
			$("#blockedips_table").DataTable({
				"order": [[ 3, "desc" ]]
			});
			$("#iplookup").on("submit",function (e){
				$(".ip_lookup_desc").empty();
			    $(".ip_lookup_desc").append("<img src='.esc_url($img_loader_url).'>");
			    $(".ip_lookup_desc").slideDown(400);
			    var inputs 	= $("#lookupip").val();
			    $.ajax({
			        url: "'.esc_url_raw($page_url).'",
			        type: "GET",
			        data: "option=iplookup&ip=" + inputs,
			        crossDomain: !0,
			        dataType: "json",
			        contentType: "application/json; charset=utf-8",
			        success: function(o) {
			            			            
				        if (o.geoplugin_status == 200 ||o.geoplugin_status == 206) {
				           $(".ip_lookup_desc").empty();
				           $(".ip_lookup_desc").append(o.ipDetails);
				        }
			        },
			        error: function(o, e, n) {}
			    });
			    e.preventDefault();
			});
		} );
	</script>';