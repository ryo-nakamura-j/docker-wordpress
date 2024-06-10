<?php

	$dir =dirname(__FILE__);
	$dir = str_replace('\\', "/", $dir);
	$sqlInjectionFile 	= $dir.'/signature/APSQLI.php';
	$xssFile			= $dir.'/signature/APXSS.php';
	$lfiFile 			= $dir.'/signature/APLFI.php';
	$configfilepath 	= explode('wp-content', $dir);
	$configfile 		= $configfilepath[0].'/wp-includes/mo-waf-config.php';

	$missingFile		= 0;

	if(file_exists($configfile))
	{
		include($configfile);
	}
	else
	{
		 $missingFile	= 1;
	}
	include_once($sqlInjectionFile);
	include_once($xssFile);
	include_once($lfiFile);
	
	
	global $wpdb,$moWpnsUtility;
	$ipaddress = $moWpnsUtility->get_client_ip();
	$query 		= $wpdb->prepare('select * from '.$wpdb->base_prefix.'mo2f_network_blocked_ips where ip_address="%s";',array($ipaddress));
	$results	= $wpdb->get_results($query);
	
	if(sizeof($results)!=0)
    {
    	$query 		= $wpdb->prepare('select * from '.$wpdb->base_prefix.'mo2f_network_whitelisted_ips where ip_address="%s";',array($ipaddress));
		$results1	= $wpdb->get_results($query);
    	if(sizeof($results1)!=0)
    	{
    		//IP whitelisted 
    	}
    	else
    	{
    	 header('HTTP/1.1 403 Forbidden');
	     include_once("mo-block.html");
	     exit;
    	}
    }
	$dir_name =  dirname(__FILE__);
	$dir_name1 = explode('wp-content', $dir_name);
	$dir_name = $dir_name1[0];
	$filepath = str_replace('\\', '/', $dir_name1[0]);
	$fileName = $filepath.'/wp-includes/mo-waf-config.php';

	if($missingFile==1)
	{
	   	if(!file_exists($fileName))
		{
			$file 		= fopen($fileName, "a+");
			$string 	= "<?php".PHP_EOL;
			$string	.= '$SQL = '.get_option("SQLInjection").';'.PHP_EOL;
			$string .= '$XSS = '.get_option("XSSAttack").';'.PHP_EOL;
			$string .= '$RFI = '.get_option("RFIAttack").';'.PHP_EOL;
			$string .= '$LFI = '.get_option("LFIAttack").';'.PHP_EOL;
			$string .= '$RCE = '.get_option("RCEAttack").';'.PHP_EOL;
			$string .= '$RateLimiting = '.get_option("Rate_limiting").';'.PHP_EOL;
			$string .= '$RequestsPMin = '.get_option("Rate_request").';'.PHP_EOL;

			if(get_option('actionRateL') == 0)
				$string .= '$actionRateL = "ThrottleIP";'.PHP_EOL;
			else
				$string .= '$actionRateL = "BlockIP";'.PHP_EOL;
		
			$string .= '?>'.PHP_EOL;
			fwrite($file, $string);
			fclose($file);
			
		 }
		
	}
	include_once($fileName);


    if($RateLimiting == 1)
    {
    	$time 		= 60;
    	$reqLimit	= $RequestsPMin;
		
	    $query = $wpdb->prepare("delete from ".$wpdb->base_prefix."wpns_ip_rate_details where time< %d",array((time()-$time)));
	    $results = $wpdb->get_results($query);

	    $query = $wpdb->prepare("insert into ".$wpdb->base_prefix."wpns_ip_rate_details values('%s',%d);",array($ipaddress,time()));
		$results = $wpdb->get_results($query);
	   
	    $query = $wpdb->prepare("select count(*) as count from ".$wpdb->base_prefix."wpns_ip_rate_details where ip='%s';",array($ipaddress));
		$results = $wpdb->get_results($query);

	    if($results[0]->count>=$reqLimit)
	    {
	    	$action = $actionRateL;
			if($action == 'ThrottleIP')
			{			
				$query 			= $wpdb->prepare("select time from ".$wpdb->base_prefix."wpns_attack_logs where ip ='%s' ORDER BY time DESC LIMIT 1;",array($ipaddress));
			    $results 		= $wpdb->get_results($query);
			    $current_time 	= time();
			    if($results[0]->time < $current_time-60)
			    {
			    	$query 			= $wpdb->prepare("insert into ".$wpdb->base_prefix."wpns_attack_logs values('%s','Rate Limit',%d,'".MoWpnsConstants::RATE_LIMIT_EXCEEDED."');",array($ipaddress,time()));
	    			$results 		= $wpdb->get_results($query);
			    }
	    		header('HTTP/1.1 403 Forbidden');
	    		include_once("mo-error.html");
	    		exit;
	    	}
	    	else
	    	{
	    		$query 			= $wpdb->prepare("select time from ".$wpdb->base_prefix."wpns_attack_logs where ip ='%s' ORDER BY time DESC LIMIT 1;",array($ipaddress));
			    $results 		= $wpdb->get_results($query);
			    $current_time 	= time();
			    if($results[0]->time < $current_time-60)
			    {
			    	$query 			= $wpdb->prepare("insert into ".$wpdb->base_prefix."wpns_attack_logs values('%s','Rate Limit',%d,'".MoWpnsConstants::RATE_LIMIT_EXCEEDED."');",array($ipaddress,time()));
	    			$results 		= $wpdb->get_results($query);
			    }
			    $query 		= $wpdb->prepare('select * from '.$wpdb->base_prefix.'mo2f_network_whitelisted_ips where ip_address="%s";',array($ipaddress));
				$results1	= $wpdb->get_results($query);
		    	if(sizeof($results1)!=0)
		    	{
		    		//IP whitelisted 
		    	}
		    	else
		    	{
		    		$query = $wpdb->prepare("insert into ".$wpdb->base_prefix."mo2f_network_blocked_ips values(NULL,'%s','Rate limit exceed',NULL,".current_time( 'timestamp' ).");",array($ipaddress));
		    		$results =$wpdb->get_results($query);
	    		}
	    		header('HTTP/1.1 403 Forbidden');
	    		include_once("mo-error.html");
	    		exit;
	    	}
	 	}
    }
    $attack = array();
    if($SQL==1)
    {
    	array_push($attack,"SQL");
    }
    if($XSS==1)
    {
    	array_push($attack,"XSS");
    }
    if($LFI==1)
    {
    	array_push($attack,"LFI");
    }
    
    $attackC 		= $attack;
    $ParanoiaLevel 	= 1;
    $annomalyS 		= 0;
    $SQLScore		= 0;
    $XSSScore		= 0;
    $limitAttack 	= get_option('limitAttack');


    foreach ($attackC as $key1 => $value1) {
    	for($lev=1;$lev<=$ParanoiaLevel;$lev++)
    	{
    		if(isset($regex[$value1][$lev]))
		    {	
		    	for($i=0;$i<sizeof($regex[$value1][$lev]);$i++)
			    {
			    	foreach ($_REQUEST as $key => $value) {
						if($regex[$value1][$lev][$i] != "")
				    	{	
							if(strpos($regex[$value1][$lev][$i], '/') == false)
					    	{	
					    		if(is_string($value))
						    	{
						    		
					    		if(preg_match('/'.$regex[$value1][$lev][$i].'/', $value))
						    	{	
						    		$scoreValue = 0;
						    	
						    		$annomalyMS = $score[$value1][$lev][$i];
                                    if(strcmp($annomalyMS,"CRITICAL")==0)
                                    {
                                        $scoreValue = 5;
                                    }

                                    elseif(strcmp($annomalyMS,"WARNING")==0)
                                    {
                                        $scoreValue = 3;
                                    }
                                    elseif(strcmp($annomalyMS,"ERROR")==0)
                                    {
                                        $scoreValue = 4;
                                    }
                                    elseif(strcmp($annomalyMS,"NOTICE")==0)
                                    {
                                        $scoreValue =2;
                                    }
                                    
						    		if($value1 == "SQL")
						    		{
						    			$SQLScore += $scoreValue;
						    		
						    		}
						    		elseif ($value1 == "XSS")
						    		{
						    			$XSSScore += $scoreValue;
						    		}
						    		else
						    		{
						    			$annomalyS += $scoreValue;
						    		}
						    		if($annomalyS>=5 || $SQLScore>=10 || $XSSScore >=10)
						    		{
						    			$value = htmlspecialchars($value);
						    			$query = $wpdb->prepare('insert into '.$wpdb->base_prefix.'wpns_attack_logs values ("%s","%s",%d,"%s");',array($ipaddress,$value1,time(),$value));
						    			$results = $wpdb->get_results($query);
						    			$query = $wpdb->prepare("select count(*) as count from ".$wpdb->base_prefix."wpns_attack_logs where ip='%s' and input != '".MoWpnsConstants::RATE_LIMIT_EXCEEDED."';",array($ipaddress));
						    			$results = $wpdb->get_results($query);
						    			if($results[0]->count>$limitAttack)
						    			{
						    				$query 		= $wpdb->prepare('select * from '.$wpdb->base_prefix.'mo2f_network_whitelisted_ips where ip_address="%s";',array($ipaddress));
											$results	= $wpdb->get_results($query);
									    	if(sizeof($results)!=0)
									    	{
									    		//IP whitelisted 
									    	}
									    	else
									    	{
						    					$query = $wpdb->prepare("insert into ".$wpdb->base_prefix."mo2f_network_blocked_ips values(NULL,'%s','attack limit exceed',NULL,%d);",array($ipaddress,current_time('timestamp')));
	    										$results =$wpdb->get_results($query);
	    									}
	  						    		}
						    			header('HTTP/1.1 403 Forbidden');
	    								include_once("mo-error.html");
	    								exit;
						    		}
						    		
						    		}
						    	}
					    	}
					    	else if (strpos($regex[$value1][$lev][$i], '#') == false) {
					    		if(is_string($value))
						    	{
						    		
					    		if(preg_match('#'.$regex[$value1][$lev][$i].'#', $value))
						    	{
						    		$scoreValue = 0;
						    		$annomalyMS = $score[$value1][$lev][$i];
                                    if(strcmp($annomalyMS,"CRITICAL")==0)
                                    {
                                        $scoreValue = 5;
                                    }

                                    elseif(strcmp($annomalyMS,"WARNING")==0)
                                    {
                                        $scoreValue = 3;
                                    }
                                    elseif(strcmp($annomalyMS,"ERROR")==0)
                                    {
                                        $scoreValue = 4;
                                    }
                                    elseif(strcmp($annomalyMS,"NOTICE")==0)
                                    {
                                        $scoreValue =2;
                                    }


						    		if($value1 == "SQL")
						    		{
						    			$SQLScore += $scoreValue;
						    		
						    		}
						    		elseif ($value1 == "XSS")
						    		{
						    			$XSSScore += $scoreValue;
						    		}
						    		else
						    		{
						    			$annomalyS += $scoreValue;
						    		}
						    		if($annomalyS>=5 || $SQLScore>=10 || $XSSScore >=10)
						    		{
						    			$value = htmlspecialchars($value);
						    			$query = $wpdb->prepare('insert into '.$wpdb->base_prefix.'wpns_attack_logs values ("%s","%s",%d,"%s");',array($ipaddress,$value1,time(),$value));
						    			$results = $wpdb->get_results($query);
						    			$query = $wpdb->prepare("select count(*) as count from ".$wpdb->base_prefix."wpns_attack_logs where ip='%s' and input != '".MoWpnsConstants::RATE_LIMIT_EXCEEDED."';",array($ipaddress));
						    			$results = $wpdb->get_results($query);

						    			if($results[0]->count>$limitAttack)
						    			{
						    				$query 		= $wpdb->prepare('select * from '.$wpdb->base_prefix.'mo2f_network_whitelisted_ips where ip_address="%s";',array($ipaddress));
											$results	= $wpdb->get_results($query);
									    	if(sizeof($results)!=0)
									    	{
									    		//IP whitelisted 
									    	}
									    	else
									    	{
						    					$query =$wpdb->prepare("insert into ".$wpdb->base_prefix."mo2f_network_blocked_ips values(NULL,'%s','attack limit exceed',NULL,%d);",array($ipaddress,current_time( 'timestamp' )));
	    										$results =$wpdb->get_results($query);
	    									}
	  						    		}
						    			header('HTTP/1.1 403 Forbidden');
	    								include_once("mo-error.html");
	    								exit;
						    		}
						    		}
						    	}
						    }

						    elseif (strpos($regex[$value1][$lev][$i], '@') == false) {
						    	if(is_string($value))
						    	{
						    		
						    	if(preg_match('@'.$regex[$value1][$lev][$i].'@', $value))
						    	{
						    		$scoreValue = 0;
						    		$annomalyMS = $score[$value1][$lev][$i];
                                    if(strcmp($annomalyMS,"CRITICAL")==0)
                                    {
                                        $scoreValue = 5;
                                    }

                                    elseif(strcmp($annomalyMS,"WARNING")==0)
                                    {
                                        $scoreValue = 3;
                                    }
                                    elseif(strcmp($annomalyMS,"ERROR")==0)
                                    {
                                        $scoreValue = 4;
                                    }
                                    elseif(strcmp($annomalyMS,"NOTICE")==0)
                                    {
                                        $scoreValue =2;
                                    }


						    		if($value1 == "SQL")
						    		{
						    			$SQLScore += $scoreValue;
						    		
						    		}
						    		elseif ($value1 == "XSS")
						    		{
						    			$XSSScore += $scoreValue;
						    		}
						    		else
						    		{
						    			$annomalyS += $scoreValue;
						    		}
						    		if($annomalyS>=5 || $SQLScore>=10 || $XSSScore >=10)
						    		{	
						    			$value = htmlspecialchars($value);
						    			$query = $wpdb->prepare('insert into '.$wpdb->base_prefix.'wpns_attack_logs values ("%s","%s",%d,"%s");',array($ipaddress,$value1,time(),$value));
						    			$results = $wpdb->get_results($query);
						    			$query = $wpdb->prepare("select count(*) as count from ".$wpdb->base_prefix."wpns_attack_logs where ip='%s' and input != '".MoWpnsConstants::RATE_LIMIT_EXCEEDED."';",array($ipaddress));
						    			$results = $wpdb->get_results($query);

						    			if($results[0]->count>$limitAttack)
						    			{
						    				$query 		= $wpdb->prepare('select * from '.$wpdb->base_prefix.'mo2f_network_whitelisted_ips where ip_address="%s";',$ipaddress);
											$results	= $wpdb->get_results($query);
									    	if(sizeof($results)!=0)
									    	{
									    		//IP whitelisted 
									    	}
									    	else
									    	{
						    					$query = $wpdb->prepare("insert into ".$wpdb->base_prefix."mo2f_network_blocked_ips values(NULL,'%s','attack limit exceed',NULL,%d);",array($ipaddress,current_time( 'timestamp' )));
	    										$results =$wpdb->get_results($query);
	    									}
	  						    		}
						    			header('HTTP/1.1 403 Forbidden');
	    								include_once("mo-error.html");
	    								exit;
						    		}
						    	}
						    	}

						    }
					    	
					    }
				    }
				    
				}
			}
		
		}
     }


		
	

?>