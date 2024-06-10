<?php
	
	include_once('mo-waf-db-common.php');
	function mo_wpns_setting_file()
	{
		global $prefix,$dbcon;
        $dir_name    = dirname(__FILE__);
        $dir_name1   = explode('wp-content', $dir_name);
        $dir_name    = $dir_name1[0];
        $filepath    = str_replace('\\', '/', $dir_name1[0]);
        $fileName    = $filepath.'/wp-includes/mo-waf-config.php';
        $missingFile = 0;
        if(!file_exists($fileName))
        {
            $missingFile = 1;
        }
        if($missingFile==1)
        {
        	$file 	= fopen($fileName, "a+");
			$string = "<?php".PHP_EOL;
			$string	.= '$SQL='.get_option("SQLInjection").';'.PHP_EOL;
			$string .= '$XSS='.get_option("XSSAttack").';'.PHP_EOL;
			$string .= '$RFI='.get_option("RFIAttack").';'.PHP_EOL;
			$string .= '$LFI='.get_option("LFIAttack").';'.PHP_EOL;
			$string .= '$RCE='.get_option("RCEAttack").';'.PHP_EOL;
			$string .= '$RateLimiting='.get_option("Rate_limiting").';'.PHP_EOL;
			$string .= '$RequestsPMin='.get_option("Rate_request").';'.PHP_EOL;

			if(get_option('actionRateL') == 0)
				$string .= '$actionRateL="ThrottleIP";'.PHP_EOL;
			else
				$string .= '$actionRateL="BlockIP";'.PHP_EOL;
		
			$string .= '?>'.PHP_EOL;
			fwrite($file, $string);
			fclose($file);
			return $fileName;
        }
        return "notMissing";

	}
	
	function mo_wpns_getRLEAttack($ipaddress)
	{
		global $wpdb;
		$query 	 = $wpdb->prepare("select time from ".$wpdb->base_prefix."wpns_attack_logs where ip ='%s' ORDER BY time DESC LIMIT 1;",array($ipaddress));
		$results = $wpdb->get_results($query);
		return $results[0]->time;
	}
	function mo_wpns_log_attack($ipaddress,$value1,$value)
    {
        global $wpdb;
        $value      = htmlspecialchars($value);
        $query      = $wpdb->prepare('insert into '.$wpdb->base_prefix.'wpns_attack_logs values ("%s","%s",%d,"%s");',array($ipaddress,$value1,time(),$value));
        $results 	= $wpdb->get_results($query);
		$query      = $wpdb->prepare("select count(*) as count from ".$wpdb->base_prefix."wpns_attack_logs where ip='%s' and input != 'RLE';",array($ipaddress));
        $results 	= $wpdb->get_results($query);
        return $results[0]->count;
    }
  	

	function mo_wpns_CheckRate($ipaddress)
	{
		global $wpdb;
		$time 		= 60;
		mo_wpns_clearRate($time);
        mo_wpns_insertRate($ipaddress);
	    $query = $wpdb->prepare("select count(*) as count from ".$wpdb->base_prefix."wpns_ip_rate_details where ip='%s';",array($ipaddress));
		$results = $wpdb->get_results($query);

	    if(isset($results[0]->count))
	    {
	    	return $results[0]->count;
	    }
	    return 0;
	    
	}
	function mo_wpns_clearRate($time)
	{
		global $wpdb;
		$query = $wpdb->prepare("delete from ".$wpdb->base_prefix."wpns_ip_rate_details where time< %d",array(time()-$time));
	    $results = $wpdb->get_results($query);
	}
	function mo_wpns_insertRate($ipaddress)
	{
		global $wpdb;
		$query = $wpdb->prepare("insert into ".$wpdb->base_prefix."wpns_ip_rate_details values('%s',%d);",array($ipaddress,time()));
		$results = $wpdb->get_results($query);
	}

?>