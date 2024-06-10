<?php
global $mo2f_dirName;
$setup_dirName = $mo2f_dirName.'views'.DIRECTORY_SEPARATOR.'twofa'.DIRECTORY_SEPARATOR.'link_tracer.php';
 include $setup_dirName;
 include_once $mo2f_dirName.'handler'.DIRECTORY_SEPARATOR.'WAF'.DIRECTORY_SEPARATOR.'database'.DIRECTORY_SEPARATOR.'mo-waf-db-common.php';
 ?>
<div class="mo_wpns_divided_layout_method">
<div class="nav-tab-wrapper">
  <button class="nav-tab" onclick="mo2f_wpns_waf_function(this)" id="mo2f_firewall_attack_dash">Firewall Dashboard</button>
  <button class="nav-tab" onclick="mo2f_wpns_waf_function(this)" id="mo2f_settings_tab">Settings</button>
  <button class="nav-tab" onclick="mo2f_wpns_waf_function(this)" id="mo2f_real_time">Real Time Blocking</button>
  <button class="nav-tab" onclick="mo2f_wpns_waf_function(this)" id="mo2f_rate_limiting">Rate Limiting</button>
  <button class="nav-tab" onclick="mo2f_wpns_waf_function(this)" id="mo2f_waf_report">Report</button>
</div>
</div>
<br>
<div>
<div id="mo2f_firewall_attack_dash_div" class="tabcontent">
  <div class="mo_wpns_divided_layout">
  	<div class="mo_wpns_waf_divided_layout_tab" id ="mo2f_firewall_attack_dash_div">
		<div class="mo_wpns_small_2_layout">
			<div class ="mo_wpns_sub_dashboards_layout">Attacks Blocked<hr><div class="wpns_font_shown" ><?php echo esc_attr($totalAttacks); ?></div></div>
			<div class="mo_wpns_small_3_layout">
				<div class ="mo_wpns_sub_sub_dashboard_layout">Injections<hr class="line"><?php echo esc_attr($sqlC); ?></></div>
				<div class ="mo_wpns_sub_sub_dashboard_layout">RCE<hr class="line"><?php echo esc_attr($rceC); ?></div>
				<div class ="mo_wpns_sub_sub_dashboard_layout">RFI/LFI<hr class="line"><?php echo intval(esc_attr($rfiC)) + intval(esc_attr($lfiC)); ?></div>
				<div class ="mo_wpns_sub_sub_dashboard_layout">XSS<hr class="line"><?php echo esc_attr($xssC); ?></div>
			</div>
		</div>
		<div class="mo_wpns_small_2_layout">
			<div class ="mo_wpns_sub_dashboards_layout">Blocked IPs<hr class="line"><div class="wpns_font_shown"><?php echo esc_attr($totalIPBlocked); ?></div></div>
			<div class="mo_wpns_small_3_layout">
					<div class ="mo_wpns_sub_sub_dashboard_layout">Manual<hr class="line"><?php echo esc_attr($manualBlocks); ?></div>
					<div class ="mo_wpns_sub_sub_dashboard_layout">Real Time<hr class="line"><?php echo esc_attr($realTime); ?></div>
					<div class ="mo_wpns_sub_sub_dashboard_layout">Country Blocked<hr class="line"><?php echo esc_attr($countryBlocked); ?></div>
					<div class ="mo_wpns_sub_sub_dashboard_layout">IP Blocked by WAF<hr class="line"><?php echo esc_attr($IPblockedByWAF); ?></div>
			</div>
		</div>
		</div>
				<div class="mo_wpns_small_layout">
					<h3>Settings</h3>
					<p><i class="mo_wpns_not_bold">
					This contains settings of your <b>Website Application Firewall</b> with settings of <b>SQL Injecton, Cross Site Scripting, Local File Inclusion, Remote File Inclusion, Remote Code Inclusion,</b> etc.<br><br><br><br>
					</i></p>
					<input type="button" name="SettingPage" id="SettingPage" value="Settings" class="button button-primary button-large" />
				
				</div>
				<div class="mo_wpns_small_layout">
					<h3>Real Time Blocking</h3>
					<p><i class="mo_wpns_not_bold">
					Real Time Blocking is <b>blocking IPs in real time</b> by miniOrange IP dataset. If any IP is found malicious then that IP will be added to the <b>miniOrange IP dataset</b> which is <b>maintained in real time.</b> By enabling this feature, if any IP is found malicious on <b>any miniOrange customer's site</b> then that IP will be <b>automatically blocked from your site as well.</b> <br><br>
					</i></p>
					<input type="button" name="RTBPage" id="RTBPage" value="Real Time Blocking" class="button button-primary button-large" />
					
				</div>
				<div class="mo_wpns_small_layout">
					<h3>Rate limiting</h3>
					<p><i class="mo_wpns_not_bold">
					Rate Limiting is used for <b>controlling the amount of incoming requests</b> from a <b>specific IP</b> to a service(Website). In this feature you can decide the <b>number of requests</b> a user can make to your website. If this is not enabled, an attacker can send <b>any number of requests</b> to a service that can lead to a <b>denial of service</b> by which legitimate users of the website will not be able to access the website.
					</i></p>
					<input type="button" name="RLPage" id="RLPage" value="Rate limiting" class="button button-primary button-large" />
					

				</div>
		
</div>
	


</div>

<div id="mo2f_waf_report_div" class="tabcontent">
	<div class="mo_wpns_divided_layout">
		<div class="mo_wpns_setting_layout">
			<h2>Blocked attacks Report</h2>

			<div id="waf_attack_table">
				<table id="waf_report_table" class="display">
				<thead><tr><th>IP Address&emsp;&emsp;</th><th>Type of attack&emsp;&emsp;</th><th>time of blocking&emsp;&emsp;</th><th>Input&emsp;&emsp;</th></tr></thead>
				<tbody>

<?php			
			$mo_wpns_handler 		= new MoWpnsHandler();
			$blockedattacks 		= $mo_wpns_handler->get_blocked_attacks();
			global $mo2f_dirName;
			foreach($blockedattacks as $blockedattack)
			{
echo 			"<tr class='mo_wpns_not_bold'><td>".esc_attr($blockedattack->ip)."</td><td>".esc_attr(retrivefullname($blockedattack->type))."</td>";
			
echo 			"<td>".date("M j, Y, g:i:s a",esc_attr($blockedattack->time))."</td><td>".esc_attr($blockedattack->input)."</td></tr>";
			} 
?>
					</tbody>
					</table>
			</div>	
		
		</div>
	</div>
</div>

<div id="mo2f_real_time_div" class="tabcontent">
	<div class="mo_wpns_divided_layout">
		<div class="mo_wpns_setting_layout">

		<table style="width:100%">
		<tr><th align="left">
		<h3>Real Time IP Blocking  <strong style="color: red"><a href="admin.php?page=mo_2fa_upgrade"> [Premium Feature] </a></strong>:
			<br>
			<p><i class="mo_wpns_not_bold">Blocking those malicious IPs Which has been detected by miniOrange WAF. This feature contains a list of malicious IPs which is mantained in real time. By enabling this option if any attack has been detected on miniOrange WAF on others wbsite then that IP will be blocked from your site also.</i></p>
  		</th><th align="right">
  		<label class='mo_wpns_switch'>
		 <input type=checkbox id='RealTimeIP' name='RealTimeIP' disabled/>
		 <span class='mo_wpns_slider mo_wpns_round'></span>
		</label></th>
		</tr>
		 </h3>
		 </table>
		<input type="checkbox" id='mo2f_realtime_ip_block_free' name = 'mo2f_realtime_ip_block_free'<?php if(get_site_option('mo2f_realtime_ip_block_free')) echo 'checked';?>/>
		Enable blocked IPs data saving on miniOrange server.
		</div>
	</div>
</div>

<div id="mo2f_rate_limiting_div" class="tabcontent">
   <div class="mo_wpns_divided_layout">
     <div class="mo_wpns_setting_layout" id='mo2f_ratelimiting_div'>
		<div id="RL" name="RL">
	    	<table style="width:100%">
			<tr>
			<th align="left">
			<h3>Rate Limiting : <a href='<?php echo esc_url($two_factor_premium_doc['Rate Limiting']);?>' target="_blank"><span class="	dashicons dashicons-text-page" style="font-size:23px;color:#413c69;"></span></a>
				<br>
				<p><i class="mo_wpns_not_bold">This will protect your Website from Dos attack and block request after a limit exceed.</i></p>
	  		</th>
	  		<th align="right">
		  		<label class='mo_wpns_switch'>
				 <input type=checkbox id='rateL' name='rateL' />
				 <span class='mo_wpns_slider mo_wpns_round'></span>
				</label>
			</th>
		
			</h3>
			</tr>
			</table>
		</div>
		
		<div name = 'rateLFD' id ='rateLFD'>
		<table style="width: 100%"> 
		</h3>
		<tr><th align="left">
		<h3>Block user after:</th>
		<th align="center"><input type="number" name="req" id="req" required min="1" style="width: 400px" />
			<i class="mo_wpns_not_bold">Requests/min</i></h3>
		</th>

		<th align="right">
		<h3>action
		<select id="action">
		  <option value="ThrottleIP">Throttle IP</option>
		  <option value="BlockIP">Block IP</option>
		</select>
		</h3>
		</th></tr>
		<tr><th></th>
		<th align="center">
			<br><input type="button" name="saveRateL" id="saveRateL" value="Save" class="button button-primary button-large">
			</th>
		</tr>
		</table>
		</form>
		
		</div>
	</div>
	
		 	<div class="mo_wpns_setting_layout">
		  	<table style="width:100%">
			<tr><th align="left">
			<h3>Rate Limiting for Crawlers<strong style="color: red"><a href="admin.php?page=mo_2fa_upgrade"> [Premium Feature] </a></strong>: 
				<br>
				<p><i class="mo_wpns_not_bold">Web crawlers crawl your Webstie for increasing ranking in the search engine. But sometimes they can make so many request to the server that the service can get damage.By enabling this feature you can provide limit at which a crawler can visit your site.</i></p>
	  		</th><th align="right">
	  		<label class='mo_wpns_switch'>
			 <input type=checkbox id='RateLimitCrawler' name='RateLimitCrawler' disabled />
			 <span class='mo_wpns_slider mo_wpns_round'></span>
			</label>
			</tr></th>
			 </h3>
			 </table>
			 <div name = 'rateLCrawler' id ='rateLCrawler'>
				<table style="width: 100%"> 
				</h3>
				<tr><th align="left">
				<h3>Block Crawler after:</th>
				<th align="center"><input type="number" name="reqCrawler" id="reqCrawler" required min="1" style="width: 400px" />
					<i class="mo_wpns_not_bold">Requests/min</i></h3>
				</th>

				<th align="right">
				<h3>action
				<select id="actionCwawler">
				  <option value="ThrottleIP">Throttle IP</option>
				  <option value="BlockIP">Block IP</option>
				</select>
				</h3>
				</th></tr>
				<tr><th></th>
				<th align="center">
					<br><input type="button" name="saveRateLCrawler" id="saveRateLCrawler" value="Save" class="button button-primary button-large">
					</th>
				</tr>
				</table>
				</form>
		
			</div>
		  </div>

		  <div class="mo_wpns_setting_layout">
		  	<table style="width:100%">
			<tr><th align="left">
			<h3>Fake Web Crawler Protection<strong style="color: red"><a href="admin.php?page=mo_2fa_upgrade"> [Premium Feature] </a></strong>: 
				<br>
				<p><i class="mo_wpns_not_bold">Web Crawlers are used for scaning the Website and indexing it. Google, Bing, etc. are the top crwalers which increase your site's indexing in the seach engine. There are several fake crawlers which can damage your site. By enabling this feature all fake google and bing crawlers will be blocked.  </i></p>
	  		</th><th align="right">
	  		<label class='mo_wpns_switch'>
			 <input type=checkbox id='FakeCrawler' name='FakeCrawler' disabled />
			 <span class='mo_wpns_slider mo_wpns_round'></span>
			</label>
			</tr></th>
			 </h3>
			</th>
			<?php $enable = get_site_option('mo2f_enable_fake_Crawler_protection') ? '' : 'disabled';?>
			<th align="left">
			<h3>Block Fake GoogleBot<strong style="color: red"></strong>: 
				
	  		</th><th align="right">
	  		<label class='mo_wpns_switch' >

			 <input type=checkbox id='FakegoogleC' name='FakegoogleC' disabled/>
			 <span class='mo_wpns_slider mo_wpns_round'></span>
			</label>
			</tr></th>

			<th align="left">
			<h3>Block Fake Facebook Crawler<strong style="color: red"></strong>: 
				
	  		</th><th align="right">
	  		<label class='mo_wpns_switch' >
			 <input type=checkbox id='FakeFBC' name='FakeFBC' disabled />
			 <span class='mo_wpns_slider mo_wpns_round'></span>
			</label>
			</tr></th>

			<th align="left">
			<h3>Block Fake Bing Bot<strong style="color: red"></strong>: 
				
	  		</th><th align="right">
	  		<label class='mo_wpns_switch' >
			 <input type=checkbox id='FakeBingC' name='FakeBingC' disabled />
			 <span class='mo_wpns_slider mo_wpns_round'></span>
			</label>
			</tr></th>

			<th align="left">
			<h3>Block Fake StatusCake<strong style="color: red"></strong>: 
				
	  		</th><th align="right">
	  		<label class='mo_wpns_switch' >
			 <input type=checkbox id='FakeStatusCake' name='FakeStatusCake' disabled />
			 <span class='mo_wpns_slider mo_wpns_round'></span>
			</label>
			</tr></th>

			 </h3>
			</th>

		</tr> </table>
		  </div>

		   <div class="mo_wpns_setting_layout">
		  	<table style="width:100%">
			<tr><th align="left">
			<h3>Whitelist Crawler<strong style="color: red"><a> [Premium Feature] </a></strong>: 
				<br>
				<p><i class="mo_wpns_not_bold">You can whitelist the top crawlers which increase indexing of your website in the search engine. By enabling this feature the whitelisted crawlers will not throttle/block by rate limiting. </i></p>
	  		</th><th align="right">
	  		<label class='mo_wpns_switch'>
			 <input type=checkbox id='WhitelistCrawler' name='WhitelistCrawler' disabled/>
			 <span class='mo_wpns_slider mo_wpns_round'></span>
			</label>
			</tr></th>

			 </h3>
			</th>
			<?php $enable = get_site_option('mo2f_enable_whitelist_crawler') ? '' : 'disabled';?>
			<th align="left">
			<h3>WhiteList GoogleBot<strong style="color: red"></strong>: 
				
	  		</th><th align="right">
	  		<label class='mo_wpns_switch' >

			 <input type=checkbox id='whitelistegoogleC' name='whitelistegoogleC'disabled/>
			 <span class='mo_wpns_slider mo_wpns_round'></span>
			</label>
			</tr></th>

			<th align="left">
			<h3>WhiteList Facebook Crawler<strong style="color: red"></strong>: 
				
	  		</th><th align="right">
	  		<label class='mo_wpns_switch' >
			 <input type=checkbox id='whitelistFBC' name='whitelistFBC' disabled />
			 <span class='mo_wpns_slider mo_wpns_round'></span>
			</label>
			</tr></th>

			<th align="left">
			<h3>WhiteList Bing Bot<strong style="color: red"></strong>: 
				
	  		</th><th align="right">
	  		<label class='mo_wpns_switch' >
			 <input type=checkbox id='whitelistBingC' name='whitelistBingC' disabled/>
			 <span class='mo_wpns_slider mo_wpns_round'></span>
			</label>
			</tr></th>

			<th align="left">
			<h3>WhiteList StatusCake<strong style="color: red"></strong>: 
				
	  		</th><th align="right">
	  		<label class='mo_wpns_switch' >
			 <input type=checkbox id='whitelistStatusCake' name='whitelistStatusCake' disabled />
			 <span class='mo_wpns_slider mo_wpns_round'></span>
			</label>
			</tr></th>


			 </h3>
			</th>

		</tr> </table>
		  </div>
		

		  <div class="mo_wpns_setting_layout">
		  	<table style="width:100%">
			<tr><th align="left">
			<h3>BotNet Protection<strong style="color: red"><a href="admin.php?page=mo_2fa_upgrade"> [Premium Feature] </a></strong>:
				<br>
				<p><i class="mo_wpns_not_bold"> BotNet is a network of robots or army of robots. The BotNet is used for Distributed denial of service attack. The attacker sends too many requests from multiple IPs to a service so that the legitimate traffic can not get the service. By enabling this your Website will be protected from such kind of attacks.  </i>
					</p>
					
				 
	  		</th><th align="right">
	  		<label class='mo_wpns_switch'>
			 <input type=checkbox id='BotNetProtection' name='BotNetProtection' disabled />
			 <span class='mo_wpns_slider mo_wpns_round'></span>
			</label>
			</tr>

		</th>

			 </h3>
			 </table>
		  </div>

		 

	</div>
	
	

</div>

<div id="mo2f_settings_tab_div" class="tabcontent">


<?php
	
	$admin_url = network_admin_url();
	$url = explode('/wp-admin/', $admin_url);
	$url = $url[0].'/htaccess';

	$nameDownload = "Backup.htaccess";

?>
<div class="mo_wpns_divided_layout">
	<div class="mo_wpns_setting_layout" id= 'mo2f_settings_tab_div'>
	<table style="width:100%">
		<tr><th align="left">
		<h3>Website Firewall on Plugin Level : <a href='<?php echo esc_url($two_factor_premium_doc['Plugin level waf']);?>' target="_blank">
  			<span class="	dashicons dashicons-text-page" style="font-size:23px;color:#413c69;"></span></a>
			<br>
			<p><i class="mo_wpns_not_bold">This will activate WAF after the WordPress load. This will block illegitimate requests after making connection to WordPress. This will check Every Request in plugin itself.</i></p>
  		</th><th align="right">
  		<label class='mo_wpns_switch'>
		 <input type=checkbox id='pluginWAF' name='pluginWAF' />
		 <span class='mo_wpns_slider mo_wpns_round'></span>
		</label>
		</tr></th>
		 </h3>
		 <tr><th align="left">
	<h3>Website Firewall on .htaccess Level <strong style="color: #2271b1">[Recommended] </strong>: <a href='<?php echo esc_attr($two_factor_premium_doc['htaccess level waf']);?>' target="_blank">
  			<span class="dashicons dashicons-text-page" style="font-size:23px;color:#413c69;"></span></a>
			<p><i class="mo_wpns_not_bold">This will activate WAF before the WordPress load. This will block illegitimate request before any connection to WordPress. This level doesnot allow illegal requests to before any page gets loaded.</i></p>
		</th><th align="right">
		<label class='mo_wpns_switch'>
		 <input type=checkbox id='htaccessWAF' name='htaccessWAF' />
		 <span class='mo_wpns_slider mo_wpns_round'></span>
		</label>
		 </h3></th></tr>
		 </table>
		 <div id ='htaccessChange' name ='htaccessChange'>
		 <p><i class="mo_wpns_not_bold"> This feature will make changes to .htaccess file, Please confirm before the changes<br>
		 	if you have any issue after this change please use the downloaded version as backup.
		 	Rename the file as '.htaccess' [without name just extension] and use it as backup.  
		 	</i></p> 
<?php
echo	 "<a href='". esc_url($url)."' download='".esc_html($nameDownload)."'>";?>
		 <input type='button' name='CDhtaccess' id='CDhtaccess' value='Confirm & Download' class="button button-primary button-large" />
		 </a>
		 
		 <input type='button' name='cnclDH' id='cnclDH' value='Cancel' class="button button-primary button-large"/>
	</div>
	</div>	
	<div name = 'AttackTypes' id ='AttackTypes'>
	<div class="mo_wpns_setting_layout" id ='mo2f_AttackTypes'>
	
		<table style="width:100%">
			<tr>
				<th align="left"> <h1>Vulnerabilities</h1></th>

				<th align="right"><h1>Enable/disable</h1></th>
				
			</tr>
		</table>
		<hr color = "#2271b1"/>
	<table style="width:100%">
	<tr>

		<th align="left"><h2>	SQL Injection Protection <strong style="color: #2271b1">[Basic Level Protection] </strong>:: 
			
			<p><i class="mo_wpns_not_bold">SQL Injection attacks are used for attack on database. This option will block all illegal requests which tries to access your database. <a href="admin.php?page=mo_2fa_upgrade"><strong style="color: #2271b1">Advance Signatures</strong></a></i></p>
		</th>  
		<th align="right">
			<label class='mo_wpns_switch'>
			<input type="checkbox" name="SQL" id="SQL"/>
		 	<span class='mo_wpns_slider mo_wpns_round'></span>
			</label>
		</th>

		</h2>

	</tr>
		<tr>
		<th align="left"><h2>	Cross Site scripting Protection <strong style="color: #2271b1">[Basic Level Protection] </strong>:: 
			<br>
			<p><i class="mo_wpns_not_bold">cross site scripting is used for script attacks. This will block illegal scripting on website. <a href="admin.php?page=mo_2fa_upgrade"><strong style="color: #2271b1">Advance Signatures</strong></a></i></p>
		</th>
		<th align="right">
			<label class='mo_wpns_switch'>
			<input type="checkbox" name="XSS" id="XSS"/>
		 	<span class='mo_wpns_slider mo_wpns_round'></span>
			</label>
			</th>
		</h2></tr>
			<tr>
		<th align="left"><h2>	Local File Inclusion Protection <strong style="color: #2271b1">[Basic Level Protection] </strong>::  
				<br>
			<p><i class="mo_wpns_not_bold">Local File inclusion is used for making changes to the local files of the server. This option will block Local File Inclusion. <a href="admin.php?page=mo_2fa_upgrade"><strong style="color: #2271b1">Advance Signatures</strong></a></i></p>
		</th>
		<th align="right">
			<label class='mo_wpns_switch'>
			<input type="checkbox" name="LFI" id="LFI"/>
		 	<span class='mo_wpns_slider mo_wpns_round'></span>
			</label>
		</th>
		</h2></tr>
	
		<tr>
		<th align="left"><h2>	Remote File Inclusion Protection <strong style="color: red"><a href="admin.php?page=mo_2fa_upgrade"> [Premium Feature] </a></strong>::  
			<br>
			<p><i class="mo_wpns_not_bold">Remote File Inclusion is used by attackers for adding malicious files from remote server to your server.This option will block Remote File Inclusion Attacks.</i></p>
		</th>
		<th align="right">
			<label class='mo_wpns_switch'>
			<input type="checkbox" name="RFI" id="RFI" disabled />
		 	<span class='mo_wpns_slider mo_wpns_round'></span>
			</label>
		</th>
		</h2></tr>
		
		<tr>
		<th align="left"><h2>	Remote Code Execution Protection <strong style="color: red"><a href="admin.php?page=mo_2fa_upgrade"> [Premium Feature] </a></strong>::
			<br>
			<p><i class="mo_wpns_not_bold">Remote Code Execution is used for executing malicious commands or files in your server.This option will block Remote File Inclusion </i></p>
		</th>  
		<th align="right">
			<label class='mo_wpns_switch'>
			<input type="checkbox" name="RCE" id="RCE" disabled/>
		 	<span class='mo_wpns_slider mo_wpns_round'></span>
			</label>
		</th>
		</h2>
	</tr>
	<tr>
		<th align="left"><h2>	SQL Injection Protection <strong style="color: #2271b1">[Advance Level Protection]</strong> <strong style="color: red"><a href="admin.php?page=mo_2fa_upgrade"> [Premium Feature] </a></strong>::
			<br>
			<p><i class="mo_wpns_not_bold">Advance Level Protection includes advance signatures to detect SQL injection. It is the recommended protection for all websites. </i></p>
		</th>  
		<th align="right">
			<label class='mo_wpns_switch'>
			<input type="checkbox" name="SQLAdvance" id="SQLAdvance" disabled/>
		 	<span class='mo_wpns_slider mo_wpns_round'></span>
			</label>
		</th>
		</h2>
	</tr>
	<tr>
		<th align="left"><h2>	Cross Site scripting Protection<strong style="color: #2271b1"> [Advance Level Protection]</strong> <strong style="color: red"><a href="admin.php?page=mo_2fa_upgrade"> [Premium Feature] </a></strong>::
			<br>
			<p><i class="mo_wpns_not_bold">Advance Level Protection includes advance signatures to detect Cross Site Scripting attacks.</i></p>
		</th>  
		<th align="right">
			<label class='mo_wpns_switch'>
			<input type="checkbox" name="XSSAdvance" id="XSSAdvance" disabled/>
		 	<span class='mo_wpns_slider mo_wpns_round'></span>
			</label>
		</th>
		</h2>
	</tr>
	<tr>
		<th align="left"><h2>	Local File Inclusion Protection Protection<strong style="color: #2271b1"> [Advance Level Protection]</strong> <strong style="color: red"><a href="admin.php?page=mo_2fa_upgrade"> [Premium Feature] </a></strong>::
			<br>
			<p><i class="mo_wpns_not_bold">Advance Level Protection includes advance signatures to detect LFI attacks on your website. Advance protection covers all files of your server to get protected from any kind of LFI attack.</i></p>
		</th>  
		<th align="right">
			<label class='mo_wpns_switch'>
			<input type="checkbox" name="LFIAdvance" id="LFIAdvance" disabled/>
		 	<span class='mo_wpns_slider mo_wpns_round'></span>
			</label>
		</th>
		</h2>
	</tr>
	
		</table>
		
	</div>
	<div class="mo_wpns_setting_layout" id="mo2f_waf_block_after">
		<table style="width: 100%">
		<tr>
		<th align="left"><h2>Block After <strong style="color: #2271b1">[Recommended : 10] </strong>:
			<p><i class="mo_wpns_not_bold">Option for blocking the IP if the limit of the attacks has been exceeds.</i></p>
		</th>  
		<th align="right"><input type ="number" name ="limitAttack" id = "limitAttack" required min="5"/></th>
		<th><h2 align="left"> attacks</h2></th>
		<th align="right"><input type="button" name="saveLimitAttacks" id="saveLimitAttacks" value="Save" class="button button-primary button-large" /></th>
		</h2>
		</tr>
		</table>
	</div>
	</div>
	</div>	
	
	
	</div>
	</div>


<script type="text/javascript">
		document.getElementById('htaccessChange').style.display="none";	
		document.getElementById('rateLFD').style.display="none";
		document.getElementById('rateLCrawler').style.display 		= 'none';
		jQuery('#resultsIPLookup').empty();
				

		var Rate_request 	= "<?php echo esc_html(get_option('Rate_request'));?>";
		var Rate_limiting 	= "<?php echo esc_html(get_option('Rate_limiting'));?>";
		var actionValue		= "<?php echo esc_html(get_option('actionRateL'));?>";
		var WAFEnabled 		= "<?php echo esc_html(get_option('WAFEnabled'));?>";
		if(WAFEnabled == '1')
		{
			if(Rate_limiting == '1')
			{

				jQuery('#rateL').prop("checked",true);
				jQuery('#req').val(Rate_request);
				if(actionValue == 0)
				{
					jQuery('#action').val('ThrottleIP');
				}
				else
				{
					jQuery('#action').val('BlockIP');
				}
				document.getElementById('rateLFD').style.display="block";
					
			}
		}
		jQuery('#rateL').click(function(){
			var rateL 	= 	jQuery("input[name='rateL']:checked").val();
			
				document.getElementById('rateLFD').style.display="none";
				
			var Rate_request 	= "<?php echo esc_html(get_option('Rate_request'));?>";
			var nonce = '<?php echo esc_html(wp_create_nonce("RateLimitingNonce"));?>';
			var actionValue		= "<?php echo esc_html(get_option('actionRateL'));?>";

			jQuery('#req').val(Rate_request);
			if(actionValue == 0)
			{
				jQuery('#action').val('ThrottleIP');
			}
			else
			{
				jQuery('#action').val('BlockIP');
			}

			
			if(Rate_request !='')
			{	

				var data = {
				'action'					: 'wpns_login_security',
				'wpns_loginsecurity_ajax' 	: 'wpns_waf_rate_limiting_form',
				'Requests'					:  Rate_request,
				'nonce'						:  nonce,
				'rateCheck'					:  rateL,
				'actionOnLimitE'			:  actionValue
				};
				jQuery.post(ajaxurl, data, function(response) {
					var response = response.replace(/\s+/g,' ').trim();
					if(response == 'RateEnabled')
					{
                        document.getElementById('rateLFD').style.display="block";
                        success_msg(" Rate Limiting is Enabled.");
					}
					else if(response == 'Ratedisabled')
					{
                        error_msg(" Rate Limiting is disabled.");
					}
					else if(response == 'WAFNotEnabled')
					{
                        error_msg(" Enable WAF (Firewall -> Settings -> Website Firewall on Plugin/.htaccess level) to use Rate Limiting");
						jQuery('#rateL').prop('checked',false);
						document.getElementById('rateLFD').style.display="none";
					}
					else if(response == 'NonceDidNotMatch')
					{
                        error_msg("There was an error in processing the request.");
                        document.getElementById('rateLFD').style.display="none";
					}
					else
					{
                        error_msg("Error: An unknown error has occured");
					}
		
				});
			}
			
			
		});
		jQuery('#saveRateL').click(function(){

			var req  	= 	jQuery('#req').val();
			var rateL 	= 	jQuery("input[name='rateL']:checked").val();
			var Action 	= 	jQuery("#action").val();
			var nonce = '<?php echo esc_html(wp_create_nonce("RateLimitingNonce"));?>';


			if(req !='' && rateL !='' && Action !='')
			{
				var data = {
				'action'					: 'wpns_login_security',
				'wpns_loginsecurity_ajax' 	: 'wpns_waf_rate_limiting_form',
				'Requests'					:  req,
				'nonce'						:  nonce,
				'rateCheck'					:  rateL,
				'actionOnLimitE'			:  Action
				};
				jQuery.post(ajaxurl, data, function(response) {
					var response = response.replace(/\s+/g,' ').trim();
					if(response == 'RateEnabled')
					{
                        success_msg("Rate Limiting is Saved");
					}
					else if(response == 'Ratedisabled')
					{
                        error_msg("Rate Limiting is disabled.");}
					else
					{
                        error_msg(" Limit of attacks should be more than 1.");
					}
		
				});
			}
		
		});	

		var wafE 			= "<?php echo esc_html(get_option('WAFEnabled'));?>";
		var SQL 			= "<?php echo esc_html(get_option('SQLInjection'));?>";
		var XSS 			= "<?php echo esc_html(get_option('XSSAttack'));?>";
		var LFI 			= "<?php echo esc_html(get_option('LFIAttack'));?>";
		var RFI 			= "<?php echo esc_html(get_option('RFIAttack'));?>";
		var RCE 			= "<?php echo esc_html(get_option('RCEAttack'));?>";
		var limitAttack 	= "<?php echo esc_html(get_option('limitAttack'));?>"
		var WAF 			= "<?php echo esc_html(get_option('WAF'));?>";



		if(wafE=='1')
		{	
			
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
			if(limitAttack >1)
			{
				jQuery('#limitAttack').val(limitAttack);
			}
		}
		
		jQuery('#SQL').click(function(){
			var SQL = jQuery("input[name='SQL']:checked").val();
			
			var nonce = '<?php echo esc_html(wp_create_nonce("WAFsettingNonce"));?>';
			if(SQL != '')
			{
				var data = {
				'action'					: 'wpns_login_security',
				'wpns_loginsecurity_ajax' 	: 'wpns_waf_settings_form',
				'optionValue' 				: 'SQL',
				'SQL'						:  SQL,
				'nonce'						:  nonce
				};
				jQuery.post(ajaxurl, data, function(response) {
						var response = response.replace(/\s+/g,' ').trim();
						if(response == 'SQLenable')
						{
                            success_msg(" SQL Injection protection is enabled");
						}
						else
						{
                            error_msg(" SQL Injection protection is disabled.");
						}
			
				});
							
			}


		});


		jQuery('#saveLimitAttacks').click(function(){
			var limitAttack = jQuery("#limitAttack").val();
			var nonce = '<?php echo esc_html(wp_create_nonce("WAFsettingNonce"));?>';
			if(limitAttack != '')
			{
				var data = {
				'action'					: 'wpns_login_security',
				'wpns_loginsecurity_ajax' 	: 'wpns_waf_settings_form',
				'optionValue' 				: 'limitAttack',
				'limitAttack'				:  limitAttack,
				'nonce'						:  nonce
				};
				jQuery.post(ajaxurl, data, function(response) {
						var response = response.replace(/\s+/g,' ').trim();
						if(response == 'limitSaved')
						{
                            success_msg(" Limit of attacks has been saved");
						}
						else
						{
                            error_msg(" Limit of attacks should be more that 1");
						}
			
				});
						
			}


		});
		jQuery("#waf_report_table").DataTable({
				"order": [[ 3, "desc" ]]
			});
		

		jQuery('#XSS').click(function(){
			var XSS = jQuery("input[name='XSS']:checked").val();
			var nonce = '<?php echo esc_html(wp_create_nonce("WAFsettingNonce"));?>';
			if(XSS != '')
			{
				var data = {
				'action'					: 'wpns_login_security',
				'wpns_loginsecurity_ajax' 	: 'wpns_waf_settings_form',
				'optionValue' 				: 'XSS',
				'XSS'						:  XSS,
				'nonce'						:  nonce
				};
				jQuery.post(ajaxurl, data, function(response) {
						var response = response.replace(/\s+/g,' ').trim();
						if(response == 'XSSenable')
						{
                            success_msg("XSS detection is enabled");
						}
						else
						{
                            error_msg(" XSS detection is disabled.");
						}
			
				});
							
			}
			

		});
		jQuery('#LFI').click(function(){
			var LFI = jQuery("input[name='LFI']:checked").val();
			var nonce = '<?php echo esc_html(wp_create_nonce("WAFsettingNonce"));?>';
			if(LFI != '')
			{
				var data = {
				'action'					: 'wpns_login_security',
				'wpns_loginsecurity_ajax' 	: 'wpns_waf_settings_form',
				'optionValue' 				: 'LFI',
				'LFI'						:  LFI,
				'nonce'						:  nonce
				};
				jQuery.post(ajaxurl, data, function(response) {
						var response = response.replace(/\s+/g,' ').trim();
						if(response == 'LFIenable')
						{
                            success_msg("LFI detection is enabled");
						}
						else
						{
                            error_msg("LFI detection is disabled.");
						}
			
				});
							
			}
			
			


		
		});
		
		
		jQuery('#pluginWAF').click(function(){
			pluginWAF = jQuery("input[name='pluginWAF']:checked").val();
			var htaccessWAF = jQuery("input[name='htaccessWAF']:checked").val();
			var nonce = '<?php echo esc_html(wp_create_nonce("WAFsettingNonce"));?>';
			if(pluginWAF != '')
			{

				var data = {
				'action'					: 'wpns_login_security',
				'wpns_loginsecurity_ajax' 	: 'wpns_waf_settings_form',
				'optionValue' 				: 'WAF',
				'pluginWAF'					:  pluginWAF,
				'nonce'						:  nonce
				};

				jQuery.post(ajaxurl, data, function(response) {
						var response = response.replace(/\s+/g,' ').trim();
						
						if(response == "PWAFenabled")
						{
							var SQL ="<?php echo esc_html(get_option('SQLInjection'));?>";
							var XSS ="<?php echo esc_html(get_option('XSSAttack'));?>";
							var LFI ="<?php echo esc_html(get_option('LFIAttack'));?>";
							var RFI ="<?php echo esc_html(get_option('RFIAttack'));?>";
							var RCE ="<?php echo esc_html(get_option('RCEAttack'));?>";
							var limitAttack 	= "<?php echo esc_html(get_option('limitAttack'));?>"

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
							if(limitAttack >1)
							{	
								jQuery('#limitAttack').val(limitAttack);
							}
							success_msg("WAF is enabled on Plugin level.");
						}
						else
						{
							jQuery('#SQL').prop("checked",false);	
							jQuery('#LFI').prop("checked",false);	
							jQuery('#XSS').prop("checked",false);	
						
							error_msg("WAF is disabled on plugin level.");
							
						}
			
				});
							
			}

			if(htaccessWAF=='on' && pluginWAF=='on')
			{
				jQuery('#htaccessWAF').prop("checked",false);
				document.getElementById("htaccessWAF").disabled = false;
				document.getElementById("htaccessChange").style.display = "none";
				
				var nonce = '<?php echo esc_html(wp_create_nonce("WAFsettingNonce"));?>';
				var data = {
				'action'					: 'wpns_login_security',
				'wpns_loginsecurity_ajax' 	: 'wpns_waf_settings_form',
				'optionValue' 				: 'HWAF',
				'nonce'						:  nonce,
				'pluginWAF'					: 'on'
				};
				jQuery.post(ajaxurl, data, function(response) {
						var response = response.replace(/\s+/g,' ').trim();
						if(response == 'HWAFdisabled')
						{
						}
						else
						{
						}
						
			
				});

			}

		});
		jQuery('#htaccessWAF').click(function(){

			var pluginWAF = jQuery("input[name='pluginWAF']:checked").val();
			var htaccessWAF = jQuery("input[name='htaccessWAF']:checked").val();
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
				var nonce = '<?php echo esc_html(wp_create_nonce("WAFsettingNonce"));?>';
				var data = {
				'action'					: 'wpns_login_security',
				'wpns_loginsecurity_ajax' 	: 'wpns_waf_settings_form',
				'optionValue' 				: 'HWAF',
				'htaccessWAF'				:  htaccessWAF,
				'nonce'						:  nonce
				};
				jQuery.post(ajaxurl, data, function(response) {
						var response = response.replace(/\s+/g,' ').trim();
						if(response == 'HWAFdisabled')
						{
							jQuery('#SQL').prop("checked",false);	
							jQuery('#LFI').prop("checked",false);	
							jQuery('#XSS').prop("checked",false);

                            error_msg(" WAF is disabled");
						}
						else
						{
                            error_msg("An error has occured while deactivating WAF.");
						}
					
				});
				
			}
			else
			{
				var nonce = '<?php echo esc_html(wp_create_nonce("WAFsettingNonce"));?>';
				var data = {
				'action'					: 'wpns_login_security',
				'wpns_loginsecurity_ajax' 	: 'wpns_waf_settings_form',
				'optionValue' 				: 'backupHtaccess',
				'htaccessWAF'				:  htaccessWAF,
				'nonce'						:  nonce
				};
				jQuery.post(ajaxurl, data, function(response) {
						var response = response.replace(/\s+/g,' ').trim();
						if(response == 'HWAFEnabled')
						{
                            success_msg("WAF is enabled on htaccess level");
						}
						else if(response =='HWAFEnabledFailed')
						{
                            error_msg("An error has occured while activating WAF.");
						}
						else
						{
							window.scrollTo({ top: 0, behavior: 'smooth' });
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
			}
			jQuery('#htaccessWAF').prop("checked",false);
			document.getElementById("htaccessWAF").disabled = false;
            success_msg(" WAF activation canceled ");

		});
		jQuery('#CDhtaccess').click(function(){

			var pluginWAF = jQuery("input[name='pluginWAF']:checked").val();
			var htaccessWAF = jQuery("input[name='htaccessWAF']:checked").val();

			var nonce = '<?php echo esc_html(wp_create_nonce("WAFsettingNonce"));?>';
				var data = {
				'action'					: 'wpns_login_security',
				'wpns_loginsecurity_ajax' 	: 'wpns_waf_settings_form',
				'optionValue' 				: 'HWAF',
				'htaccessWAF'				:  htaccessWAF,
				'nonce'						:  nonce
				};
				jQuery.post(ajaxurl, data, function(response) {
						var response = response.replace(/\s+/g,' ').trim();
						if(response == 'HWAFEnabled')
						{
							if(htaccessWAF=='on')
							{	
								var SQL ="<?php echo esc_html(get_option('SQLInjection'));?>";
								var XSS ="<?php echo esc_html(get_option('XSSAttack'));?>";
								var LFI ="<?php echo esc_html(get_option('LFIAttack'));?>";
								var RFI ="<?php echo esc_html(get_option('RFIAttack'));?>";
								var RCE ="<?php echo esc_html(get_option('RCEAttack'));?>";
								var limitAttack = "<?php echo esc_html(get_option('limitAttack'));?>"

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
								if(limitAttack >1)
								{	
									jQuery('#limitAttack').val(limitAttack);
								}
                                success_msg("WAF is enabled on htaccess Level");
							}
						}
						else if(response == 'HWAFEnabledFailed')
						{
                            error_msg("An error occured while activating WAF");
								
						}
						else if(response == 'HWAFdisabledFailed')
						{
                            error_msg(" An error occured while deactivating WAF");
							
						}
						else if(response == 'HWAFdisabled')
						{
							jQuery('#SQL').prop("checked",false);
                            jQuery('#LFI').prop("checked",false);
                            jQuery('#XSS').prop("checked",false);

                            error_msg("WAF is disabled on htaccess Level.");
						}
						else
						{
                            error_msg("An error has occured.There might be another WAF exists.");
						}
						
				});
		
			if(htaccessWAF=='on' && pluginWAF=='on')
			{
				jQuery('#pluginWAF').prop("checked",false);
					
			}
			document.getElementById("htaccessChange").style.display = "none";
			document.getElementById("htaccessWAF").disabled = false;

		});

		

jQuery('#RLPage').click(function(){
	document.getElementById("mo2f_rate_limiting").click();
});

jQuery('#mo2f_realtime_ip_block_free').click(function(){
	var mo2f_realtime_ip_block_free 	= 	jQuery("input[name='mo2f_realtime_ip_block_free']:checked").val();
	var nonce = '<?php echo esc_html(wp_create_nonce("mo2f_realtime_ip_block_free"));?>';
	var data = {
		'action'					: 'wpns_login_security',
		'wpns_loginsecurity_ajax' 	: 'wpns_waf_realtime_ip_block_free',
		'mo2f_realtime_ip_block_free': mo2f_realtime_ip_block_free,
		'nonce'						:  nonce,
		};
	jQuery.post(ajaxurl, data, function(response) {
		var response = response.replace(/\s+/g,' ').trim();
		if(response == 'realtime_block_free_enable')
		{
			success_msg("Data saving on miniOrange is enabled.");
		}
		else if(response =='realtime_block_free_disable')
		{
		    error_msg("Data saving on miniOrange is disabled.");
		}
		else
		{
		    error_msg("Unknown error has occured.");
		}
	});
});
jQuery('#SettingPage').click(function(){
	document.getElementById("mo2f_settings_tab").click();
});

jQuery('#RTBPage').click(function(){
	document.getElementById("mo2f_real_time").click();
});
jQuery('#waf_report').click(function(){
	document.getElementById("mo2f_waf_report").click();
});
	

	function mo2f_wpns_waf_function(elmt){
		var tabname = elmt.id;
		var tabarray = ["mo2f_firewall_attack_dash","mo2f_settings_tab","mo2f_rate_limiting","mo2f_real_time","mo2f_waf_report"];
		for (var i = 0; i < tabarray.length; i++) {
			if(tabarray[i] == tabname){
				jQuery("#"+tabarray[i]).addClass("nav-tab-active");
				jQuery("#"+tabarray[i]+"_div").css("display", "block");
			}else{
				jQuery("#"+tabarray[i]).removeClass("nav-tab-active");
				jQuery("#"+tabarray[i]+"_div").css("display", "none");
			}
		}
		
		localStorage.setItem("waf_last_tab", tabname);
	}		

	jQuery('#mo_2fa_waf').addClass('nav-tab-active');

	var tab = localStorage.getItem("waf_last_tab"); 

	if(tab)
		document.getElementById(tab).click();
	else{
		document.getElementById("mo2f_firewall_attack_dash").click();
	}


</script>
