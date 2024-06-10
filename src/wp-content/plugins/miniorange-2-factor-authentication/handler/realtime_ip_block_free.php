<?php
/**
 * 
 */
class MO2F_realtime_free
{
	
	function __construct()
	{
		add_filter( 'cron_schedules', array($this,'mo_2fa_realtime_blocking_int'));
		add_action( 'mo2f_realtime_ip_block_free_hook', array($this,'mo2f_realtime_ip_block_free') );

	}
	function mo2f_realtime_ip_block_free()
	{
		global $wpnsDbQueries;
		$results = $wpnsDbQueries->get_blocked_ips_realtime();
		
		$ip_addresses = array();
		$mo2f_added_ips = get_site_option('mo2f_added_ips_realtime');
		$sizeofResults = sizeof($results); 
		
		$mo2f_added_ips = explode(',', $mo2f_added_ips);

		for($i = 0;$i<$sizeofResults;$i++)
		{
			if($results[$i]->ip_address != '::1' and $results[$i]->ip_address != '127.0.0.1' and rest_is_ip_address($results[$i]->ip_address))
			{
				if(!in_array($results[$i]->ip_address, $mo2f_added_ips))
				{
					array_push($ip_addresses,$results[$i]->ip_address);
				}
			}
		}
		

		add_to_blacklist($ip_addresses,get_site_option('siteurl'));

	}
	function mo_2fa_realtime_blocking_int()
	{
		$mo2f_cron_hours = 7200;
		$schedules['mo2f_realtime_ipblock_free'] = array(
		'interval' => $mo2f_cron_hours,
		'display'  => esc_html__( 'Cron Activated' ),
		);
	    return $schedules;
	}
}
new MO2F_realtime_free;

?>