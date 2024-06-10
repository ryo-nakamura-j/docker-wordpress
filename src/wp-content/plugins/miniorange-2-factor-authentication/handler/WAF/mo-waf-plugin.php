<?php

    $dir        = dirname(__FILE__);
    $dir        = str_replace('\\', "/", $dir);
    $dir        = explode('handler', $dir);
    $wafInclude = $dir[0].'/handler/WAF/waf-include.php';
    $pluginU    = $dir[0].'helper/pluginUtility.php';
    $wafDB      = $dir[0].'/handler/WAF/database/mo-waf-plugin-db.php';
    $errorPage  = $dir[0].'handler/mo-error.html';
    $blockPage  = $dir[0].'handler/mo-block.html';

    include_once($wafInclude);
    include_once($pluginU);
    include_once($wafDB);


    global $wpdb,$mowpnshandle,$moWpnsUtility;
    $mowpnshandle   = new MoWpnsHandler();
	$ipaddress = $moWpnsUtility->get_client_ip();
    $ipaddress      = sanitize_text_field($ipaddress);
    if($mowpnshandle->mo_wpns_is_ip_blocked($ipaddress))
    {
        if(!$mowpnshandle->is_whitelisted($ipaddress))
        {
            header('HTTP/1.1 403 Forbidden');
            include_once($blockPage);
             exit;
        }
    }
    $fileName = mo_wpns_setting_file();
    if($fileName != "notMissing")
    {
        include_once($fileName);
    }
    if(isset($RateLimiting) and $RateLimiting == 1)
    {
        
        if(!mo2f_is_crawler())
        {
            mo_wpns_apply_RateLimiting($RequestsPMin,$actionRateL,$ipaddress,$errorPage);
        }
    }
    if(isset($RateLimitingCrawler))
    {
        if($RateLimitingCrawler == 1)
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
    }
    if(mo2f_is_crawler())
    {
            if(mo2f_is_fake_bot($ipaddress))
            {
                header('HTTP/1.1 403 Forbidden');
                include_once($errorPage);
                exit;
            }
        
    }
    $attack = array();
            if(isset($SQL) )
            {
                if($SQL==1)
                array_push($attack,"SQL");
            }
            if(isset($XSS) )
            {
                if( $XSS==1)
                array_push($attack,"XSS");
            }
            if(isset($LFI))
            {
                if($LFI==1)
                array_push($attack,"LFI");
            }
    
    $attackC        = $attack;
    $ParanoiaLevel  = 1;
    $annomalyS      = 0;
    $SQLScore       = 0;
    $XSSScore       = 0;
    $limitAttack    = get_option('limitAttack');


    foreach ($attackC as $key1 => $value1)
    {
        for($lev=1;$lev<=$ParanoiaLevel;$lev++)
        {
            if(isset($regex[$value1][$lev]))
            {   $ooo = 0;
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
                                            if(!$mowpnshandle->is_whitelisted($ipaddress))
                                            {
                                                if(!$mowpnshandle->mo_wpns_is_ip_blocked($ipaddress))
                                                    $mowpnshandle->mo_wpns_block_ip($ipaddress,'Attack limit Exceeded',true);         //Attack Limit Exceed
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

    function mo_wpns_apply_RateLimiting($reqLimit,$action,$ipaddress,$errorPage)
    {
        global $wpdb,$mowpnshandle;
        $rate = mo_wpns_CheckRate($ipaddress);
        if($rate>=$reqLimit)
        {
            $lastAttack     = mo_wpns_getRLEAttack($ipaddress)+60;
            $current_time   = time();
            if($lastAttack < $current_time-60)
            {
                mo_wpns_log_attack($ipaddress,'RLE','RLE');
            }
            if($action != 'ThrottleIP')
            {   
                if(!$mowpnshandle->is_whitelisted($ipaddress))
                {
                    $mowpnshandle->mo_wpns_block_ip($ipaddress,'RLE',true);
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
        global $wpdb,$mowpnshandle;
        $user_agent = isset($_SERVER['HTTP_USER_AGENT'])?sanitize_text_field($_SERVER['HTTP_USER_AGENT']):'';
        if(isset($RateLimitingCrawler))
        {
            if($RateLimitingCrawler=='1')
            {
                if(isset($RequestsPMinCrawler))
                {
                    $reqLimit   = $RequestsPMinCrawler;
                    $rate       = mo_wpns_CheckRate($ipaddress);
                    if($rate>=$reqLimit)
                    {
                        $action         = $actionRateLCrawler;
                        $lastAttack     = mo_wpns_getRLEattack($ipaddress)+60;
                        $current_time   = time();
                        if($current_time>$lastAttack && sanitize_text_field($_SERVER['HTTP_USER_AGENT'])!='')
                        {
                            mo_wpns_log_attack($ipaddress,'RLECrawler',$user_agent);
                        }
                        if($action != 'ThrottleIP')
                        {
                           if(!$mowpnshandle->is_whitelisted($ipaddress))
                            {
                                if(!$mowpnshandle->mo_wpns_is_ip_blocked($ipaddress))
                                {
                                    $mowpnshandle->mo_wpns_block_ip($ipaddress,'RLECrawler',true);
                                }
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



?>