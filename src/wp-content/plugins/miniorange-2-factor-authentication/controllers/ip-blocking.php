<?php 
	
	global $moWpnsUtility,$mo2f_dirName;
	$mo_wpns_handler 	= new MoWpnsHandler();

	if(current_user_can( 'manage_options' )  && isset($_POST['option']))
	{
		
		switch(sanitize_text_field($_POST['option']))
		{	
			case "mo_wpns_manual_block_ip":
				wpns_handle_manual_block_ip(sanitize_text_field($_POST['IP']));			break;
			case "mo_wpns_unblock_ip":
				wpns_handle_unblock_ip(sanitize_text_field($_POST['id']));			break;
			case "mo_wpns_whitelist_ip":
				wpns_handle_whitelist_ip(sanitize_text_field($_POST['IP']));				break;
			case "mo_wpns_remove_whitelist":
				wpns_handle_remove_whitelist(sanitize_text_field($_POST['id'] ));	break;
		}
	}

	$blockedips 		= $mo_wpns_handler->get_blocked_ips();
	$whitelisted_ips 	= $mo_wpns_handler->get_whitelisted_ips();
	$path 			= dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'images'.DIRECTORY_SEPARATOR.'loader.gif';
	$path 			= explode('plugins', $path);
	$img_loader_url	= plugins_url().$path[1];

	$page_url			= "";
	$license_url		= add_query_arg( array('page' => 'mo_2fa_upgrade'), sanitize_url($_SERVER['REQUEST_URI'] ));


	function wpns_handle_manual_block_ip($ip)
	{
		global $moWpnsUtility;	
		if( $moWpnsUtility->check_empty_or_null( $ip) )
		{
			echo("empty IP");
			exit;
		} 
		if(!preg_match('/^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}\z/',$ip))
		{
			echo("INVALID_IP_FORMAT");
			exit;
		}
		else
		{
			

			$ipAddress 		= filter_var($ip, FILTER_VALIDATE_IP) ? $ip : 'INVALID_IP_FORMAT';
			$mo_wpns_config = new MoWpnsHandler();
			$isWhitelisted 	= $mo_wpns_config->is_whitelisted($ipAddress);
			if(!$isWhitelisted)
			{
				if($mo_wpns_config->mo_wpns_is_ip_blocked($ipAddress)){
					echo("already blocked");	
					exit;
				} else{
					$mo_wpns_config->mo_wpns_block_ip($ipAddress, MoWpnsConstants::BLOCKED_BY_ADMIN, true);
					?>
					<table id="blockedips_table1" class="display">
				<thead><tr><th>IP Address&emsp;&emsp;</th><th>Reason&emsp;&emsp;</th><th>Blocked Until&emsp;&emsp;</th><th>Blocked Date&emsp;&emsp;</th><th>Action&emsp;&emsp;</th></tr></thead>
				<tbody>
<?php					
				$mo_wpns_handler 	= new MoWpnsHandler();
				$blockedips 		= $mo_wpns_handler->get_blocked_ips();
				$whitelisted_ips 	= $mo_wpns_handler->get_whitelisted_ips();
				global $mo2f_dirName;
				foreach($blockedips as $blockedip)
				{
	echo 			"<tr class='mo_wpns_not_bold'><td>".esc_attr($blockedip->ip_address)."</td><td>".esc_attr($blockedip->reason)."</td><td>";
					if(empty($blockedip->blocked_for_time)) 
	echo 				"<span class=redtext>Permanently</span>"; 
					else 
	echo 				date("M j, Y, g:i:s a", esc_attr($blockedip->blocked_for_time));
	echo 			"</td><td>".date("M j, Y, g:i:s a",esc_attr($blockedip->created_timestamp))."</td><td><a  onclick=unblockip('".esc_html($blockedip->id)."')>Unblock IP</a></td></tr>";
				} 
	?>
					</tbody>
					</table>
					<script type="text/javascript">
						jQuery("#blockedips_table1").DataTable({
						"order": [[ 3, "desc" ]]
						});
					</script>
					<?php
					exit;
				}
			}
			else
			{
				echo("IP_IN_WHITELISTED");
				exit;
			}
		}
	}


	function wpns_handle_unblock_ip($entryID)
	{
		global $moWpnsUtility;
		
		if( $moWpnsUtility->check_empty_or_null($entryID))
		{
			echo("UNKNOWN_ERROR");
			exit;
		}
		else
		{
			$entryid 		= sanitize_text_field($entryID);
			$mo_wpns_config = new MoWpnsHandler();
			$mo_wpns_config->unblock_ip_entry($entryid);
					?>
				<table id="blockedips_table1" class="display">
				<thead><tr><th>IP Address&emsp;&emsp;</th><th>Reason&emsp;&emsp;</th><th>Blocked Until&emsp;&emsp;</th><th>Blocked Date&emsp;&emsp;</th><th>Action&emsp;&emsp;</th></tr></thead>
				<tbody>
<?php					
				$mo_wpns_handler 	= new MoWpnsHandler();
				$blockedips 		= $mo_wpns_handler->get_blocked_ips();
				$whitelisted_ips 	= $mo_wpns_handler->get_whitelisted_ips();
				global $mo2f_dirName;
				foreach($blockedips as $blockedip)
				{
	echo 			"<tr class='mo_wpns_not_bold'><td>". esc_attr($blockedip->ip_address)."</td><td>".esc_attr($blockedip->reason)."</td><td>";
					if(empty($blockedip->blocked_for_time)) 
	echo 				"<span class=redtext>Permanently</span>"; 
					else 
	echo 				date("M j, Y, g:i:s a",esc_attr($blockedip->blocked_for_time));
	echo 			"</td><td>".date("M j, Y, g:i:s a",esc_attr($blockedip->created_timestamp))."</td><td><a onclick=unblockip('".esc_attr($blockedip->id)."')>Unblock IP</a></td></tr>";
				} 
	?>
					</tbody>
					</table>
					<script type="text/javascript">
						jQuery("#blockedips_table1").DataTable({
						"order": [[ 3, "desc" ]]
						});
					</script>
					<?php
			
			exit;
		}
	}


	function wpns_handle_whitelist_ip($ip)
	{
		global $moWpnsUtility;
		if( $moWpnsUtility->check_empty_or_null($ip))
		{
			echo("EMPTY IP");
			exit;
		}
		if(!preg_match('/^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}\z/',$ip))
		{
				echo("INVALID_IP");
				exit;
		}
		else
		{
			$ipAddress = (filter_var($ip, FILTER_VALIDATE_IP)) ?  $ipAddress : 'INVALID_IP';
			$mo_wpns_config = new MoWpnsHandler();
			if($mo_wpns_config->is_whitelisted($ipAddress))
			{
				echo("IP_ALREADY_WHITELISTED");
				exit;
			}
			else
			{
				$mo_wpns_config->whitelist_ip($ip);
				$mo_wpns_handler 	= new MoWpnsHandler();
				$whitelisted_ips 	= $mo_wpns_handler->get_whitelisted_ips();
					
			?>
				<table id="whitelistedips_table1" class="display">
				<thead><tr><th >IP Address</th><th >Whitelisted Date</th><th >Remove from Whitelist</th></tr></thead>
				<tbody>
				<?php
					foreach($whitelisted_ips as $whitelisted_ip)
					{
						echo "<tr class='mo_wpns_not_bold'><td>".esc_html($whitelisted_ip->ip_address)."</td><td>".date("M j, Y, g:i:s a",esc_html($whitelisted_ip->created_timestamp))."</td><td><a  onclick=removefromwhitelist('".esc_attr($whitelisted_ip->id)."')>Remove</a></td></tr>";
					} 

	
				?>
				</tbody>
				</table>
			<script type="text/javascript">
				jQuery("#whitelistedips_table1").DataTable({
				"order": [[ 1, "desc" ]]
				});
			</script>

	<?php
			exit;
			}
		}
	}


	function wpns_handle_remove_whitelist($entryID)
	{
		global $moWpnsUtility;
		if( $moWpnsUtility->check_empty_or_null($entryID))
		{
			//change Message
			echo("UNKNOWN_ERROR");
			exit;
		}
		else
		{
			$entryid = sanitize_text_field($entryID);
			$mo_wpns_config = new MoWpnsHandler();
			$mo_wpns_config->remove_whitelist_entry($entryid);
			//structures
				$mo_wpns_handler 	= new MoWpnsHandler();
				$whitelisted_ips 	= $mo_wpns_handler->get_whitelisted_ips();
					
			?>
				<table id="whitelistedips_table1" class="display">
				<thead><tr><th >IP Address</th><th >Whitelisted Date</th><th >Remove from Whitelist</th></tr></thead>
				<tbody>
			<?php
					foreach($whitelisted_ips as $whitelisted_ip)
					{
						echo "<tr class='mo_wpns_not_bold'><td>".esc_html($whitelisted_ip->ip_address)."</td><td>".date("M j, Y, g:i:s a",esc_html($whitelisted_ip->created_timestamp))."</td><td><a onclick=removefromwhitelist('".esc_attr($whitelisted_ip->id)."')>Remove</a></td></tr>";
					} 

	
			?>
				</tbody>
				</table>
			<script type="text/javascript">
				jQuery("#whitelistedips_table1").DataTable({
				"order": [[ 1, "desc" ]]
				});
			</script>

		<?php
			exit;
		}
	}

	