<?php
	$dir        = dirname(__FILE__);
    $dir        = str_replace('\\', "/", $dir);
    $dir        = explode('WAF', $dir);
    $wafInclude = $dir[0].'WAF/waf-include.php';
    $wafdb      = $dir[0].'WAF/database/mo-waf-db.php';
    $errorPage  = $dir[0].'mo-error.html';
    $blockPage  = $dir[0].'mo-block.html';

    include_once($wafInclude);
    include_once($wafdb);

    global $dbcon,$prefix,$moWpnsUtility;	
    $connection = mo_wpns_dbconnection();
    if($connection)
	{
        $wafLevel = mo_wpns_get_option_value('WAF');
        if($wafLevel=='HtaccessLevel')
        {
	        if((isset($_SERVER['REMOTE_ADDR']) && is_string($_SERVER['REMOTE_ADDR']) && !empty($_SERVER['REMOTE_ADDR']) && mo2f_isValidIP($_SERVER['REMOTE_ADDR'])))
            {
                $ipaddress = get_unique_ip($_SERVER['REMOTE_ADDR']);
            }else{
                $ipaddress = 'UNKNOWN';
            }
            if(mo_wpns_is_ip_blocked($ipaddress))
            {
                if(!mo_wpns_is_ip_whitelisted($ipaddress))
                {
                    header('HTTP/1.1 403 Forbidden');
                    include_once($blockPage);
                    exit;
                }
            }
            $fileName = mo_wpns_setting_file();

            if($fileName != 'notMissing')
            {
                include_once($fileName);
            }
            if(isset($RateLimiting) && $RateLimiting == 1)
            {
                if(!mo2f_is_crawler())
                {
                    if(isset($RequestsPMin) && isset($actionRateL))
                        mo_wpns_applyRateLimiting($RequestsPMin,$actionRateL,$ipaddress,$errorPage);
                }
            }
            if(isset($RateLimitingCrawler) && $RateLimitingCrawler == 1)
            {
                if(mo2f_is_crawler())
                {
                    if(mo2f_is_fake_googlebot($ipaddress))
                    {
                        header('HTTP/1.1 403 Forbidden');
                        include_once($errorPage);
                         exit;
                    }
                    if($RateLimitingCrawler == '1')
                    {
                        mo_wpns_applyRateLimitingCrawler($ipaddress,$fileName,$errorPage); 
                    }

                }
            }
            $attack = array();
            if(isset($SQL) && $SQL==1)
            {
                array_push($attack,"SQL");
            }
            if(isset($XSS) && $XSS==1)
            {
                array_push($attack,"XSS");
            }
            if(isset($LFI) && $LFI==1)
            {
                array_push($attack,"LFI");
            }
			
            $attackC        = $attack;
            $ParanoiaLevel  = 1;
            $annomalyS      = 0;
            $SQLScore       = 0;
            $XSSScore       = 0;
            $limitAttack    = mo_wpns_get_option_value("limitAttack");

            foreach ($attackC as $key1 => $value1)
            {
                for($lev=1;$lev<=$ParanoiaLevel;$lev++)
                {
                    if(isset($regex[$value1][$lev]))
                    {	$ooo = 0;
                        for($i=0;$i<sizeof($regex[$value1][$lev]);$i++)
                        {
                            foreach ($_REQUEST as $key => $value) {

                                if($regex[$value1][$lev][$i] != "")
                                {
                                    if(is_string($value))
                                    {
                                        if(preg_match($regex[$value1][$lev][$i], $value))
                                        {
                                           
                                            if($value1 == "SQL")
                                            {
                                                $SQLScore += $score[$value1][$lev][$i];
                                            }
                                            elseif ($value1 == "XSS")
                                            {
                                                $XSSScore += $score[$value1][$lev][$i];
                                            }
                                            else
                                            {
                                                $annomalyS += $score[$value1][$lev][$i];
                                            }

                                            if($annomalyS>=5 || $SQLScore>=10 || $XSSScore >=10)
                                            {
                                                $attackCount = mo_wpns_log_attack($ipaddress,$value1,$value);
                                                if($attackCount>$limitAttack)
                                                {
                                                    if(!mo_wpns_is_ip_whitelisted($ipaddress))
                                                    {
                                                        mo_wpns_block_ip($ipaddress,'Attack limit Exceeded');         //Attack Limit Exceed
                                                    }
                                                }

                                                header('HTTP/1.1 403 Forbidden');
                                                include_once($errorPage);
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
    }

    function get_unique_ip($IP){
		$IP = explode(',',$IP);
		if(is_array($IP))
			return filter_var($IP[0], FILTER_VALIDATE_IP);
		return filter_var($IP, FILTER_VALIDATE_IP);
	}

    function mo2f_isValidIP($IP){
		$new_ip = explode(',',$IP);
		if(is_array($new_ip))
			$IP = $new_ip[0];
		return filter_var(get_unique_ip($IP), FILTER_VALIDATE_IP) !== false;
		}
	
    function mo_wpns_applyRateLimiting($reqLimit,$action,$ipaddress,$errorPage)
    {
        global $dbcon, $prefix;
        $rate = mo_wpns_CheckRate($ipaddress);
        if($rate>$reqLimit)
        {
            $lastAttack     = mo_wpns_getRLEattack($ipaddress)+60;
            $current_time   = time();
            if($current_time > $lastAttack)
            {
                mo_wpns_log_attack($ipaddress,'RLE','RLE');
            }
            if($action != 'ThrottleIP')
            {
               if(!mo_wpns_is_ip_whitelisted($ipaddress))
                {
                    mo_wpns_block_ip($ipaddress,'RLE');     //Rate Limit Exceed
                }
            }
            header('HTTP/1.1 403 Forbidden');
            include_once($errorPage);
             exit;        
        }
    }
    
    function mo_wpns_applyRateLimitingCrawler($ipaddress,$filename,$errorPage)
    {
        if(file_exists($filename))
        {
            include($filename);
        }
        global $dbcon,$prefix;

        $user_agent = isset($_SERVER['HTTP_USER_AGENT'])?filter_var($_SERVER['HTTP_USER_AGENT'], FILTER_SANITIZE_STRING):'';
        if(isset($RateLimitingCrawler))
        {
            if(isset($RateLimitingCrawler) && $RateLimitingCrawler=='1')
            {
                if(isset($RequestsPMinCrawler) && isset($actionRateLCrawler) )
                {
                    $reqLimit   = $RequestsPMinCrawler;
                    $rate       = mo_wpns_CheckRate($ipaddress);
                    if($rate>=$reqLimit)
                    {
                        $action         = $actionRateLCrawler;
                        $lastAttack     = mo_wpns_getRLEattack($ipaddress)+60;
                        $current_time   = time();
                        if($current_time>$lastAttack)
                        {
                            mo_wpns_log_attack($ipaddress,'RLECrawler',$user_agent);
                        }
                        if($action != 'ThrottleIP')
                        {
                           if(!mo_wpns_is_ip_whitelisted($ipaddress))
                            {
                                mo_wpns_block_ip($ipaddress,'RLECrawler');      //Rate Limit Exceed for Crawler
                            }
                        }
                        header('HTTP/1.1 403 Forbidden');
                        include_once($errorPage);
                         exit;
                    } 
                }
            } 
        }
    }

	
	$dbcon->close();
?>