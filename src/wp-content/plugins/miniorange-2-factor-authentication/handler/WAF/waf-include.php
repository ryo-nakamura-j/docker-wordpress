<?php
    $dir                = dirname(__FILE__);
    $dir                = str_replace('\\', "/", $dir);
    $dir                = explode('WAF', $dir);
    $dir                = $dir[0]; 
    $sqlInjectionFile   = $dir.DIRECTORY_SEPARATOR.'signature/APSQLI.php';
    $xssFile            = $dir.DIRECTORY_SEPARATOR.'signature/APXSS.php';
    $lfiFile            = $dir.DIRECTORY_SEPARATOR.'signature/APLFI.php';
    $configfilepath     = explode('wp-content', $dir);
    $configfile         = $configfilepath[0].DIRECTORY_SEPARATOR.'wp-includes/mo-waf-config.php';
    $missingFile        = 0;

    if(file_exists($configfile))
    {
        include_once($configfile);
    }
    else
    {
         $missingFile   = 1;
    }
    include_once($sqlInjectionFile);
    include_once($xssFile);
    include_once($lfiFile);

    function mo2f_is_crawler()
    {
        
        $user_agent = isset($_SERVER['HTTP_USER_AGENT'])?filter_var($_SERVER['HTTP_USER_AGENT'], FILTER_SANITIZE_STRING):'';
        $Botsign = array('bot','apache','crawler','elinks','http', 'java', 'spider','link','fetcher','scanner','grabber','collector','capture','seo','.com');
        foreach ($Botsign as $key => $value) 
        {
            if(isset($user_agent) && preg_match('/'.$value.'/', $user_agent)) 
            {
                return true;
            }
        }   
        return false;
    }
    function mo2f_is_fake_bot($ipaddress)
    {
        $bing   = false;
        $fb     = false;
        $google = false;
        $status = false;

            $bing = mo2f_is_fake_bing_bot($ipaddress); 
            $fb = mo2f_is_fake_FB_crawler($ipaddress);
            $google = mo2f_is_fake_googlebot($ipaddress);
            $status = mo2f_is_fake_statusCake($ipaddress);
        
        return $google or $fb or $bing or $status;

    }
    function mo2f_is_fake_statusCake($ipaddress)
    {
        $user_agent = sanitize_text_field($_SERVER['HTTP_USER_AGENT']);
        if(preg_match('/StatusCake/', $user_agent))
        {
            return mo2f_is_fake_status($ipaddress);
        }
        return false; 
    }
    function mo2f_is_fake_googlebot($ipaddress)
    {

        $user_agent = isset($_SERVER['HTTP_USER_AGENT'])?filter_var($_SERVER['HTTP_USER_AGENT'], FILTER_SANITIZE_STRING):'';
        if(preg_match('/Googlebot/', $user_agent))
        {
            return mo2f_is_fake_google('Googlebot',$user_agent,$ipaddress);
        }
        return false;
    }
    function mo2f_is_fake_FB_crawler($ipaddress)
    {
        $user_agent = sanitize_text_field($_SERVER['HTTP_USER_AGENT']);
        if(preg_match('/Facebot/', $user_agent))
        {
            return mo2f_is_fake_fb('Facebot',$user_agent,$ipaddress);
        }
        else if(preg_match('/facebookexternalhit/', $user_agent))
        {
            return mo2f_is_fake_fb('facebookexternalhit',$user_agent,$ipaddress);       
        }
        return false;
    }
    function mo2f_is_fake_bing_bot($ipaddress)
    {
        $user_agent = sanitize_text_field($_SERVER['HTTP_USER_AGENT']);
        if(preg_match('/bingbot/', $user_agent))
        {
            return mo2f_is_fake_bing('bingbot',$user_agent,$ipaddress);
        }
        return false;
    }
    function mo2f_is_fake_status($ipaddress)
    {
        $dir     = dirname(dirname(__FILE__));
        $IPList  = $dir.DIRECTORY_SEPARATOR.'WAF'.DIRECTORY_SEPARATOR.'Includes'.DIRECTORY_SEPARATOR.'CrawlerIPs.php';
        include($IPList);
        $currentIP = '';
        if(strlen(inet_pton($ipaddress)) == 16)
        {
            $currentIP  = mo2f_ipaddress_to_ipnumber(trim($ipaddress));
        }
        else
        {
            $currentIP  = ip2long(trim($ipaddress));
        }
        foreach ($statusCake as $index => $IP)
        {
            $ip = ip2long(trim($IP));
            if($ip == $currentIP)
            {
                return false;
            }
        }
        return true;
    }
    
    function mo2f_is_fake_google($crawler,$user_agent,$ipaddress)
    {
        $dir     = dirname(dirname(__FILE__));
        $IPList  = $dir.DIRECTORY_SEPARATOR.'WAF'.DIRECTORY_SEPARATOR.'Includes'.DIRECTORY_SEPARATOR.'CrawlerIPs.php';
        include($IPList);
        if(strlen(inet_pton($ipaddress)) == 4)
        {
            foreach ($googleBot as $index => $range) 
            {
                if(mo2f_check_current_IP_in_range($range,$ipaddress))
                {
                    return false;
                }          
            }
        }
        else if(strlen(inet_pton($ipaddress)) == 16)
        {
            foreach ($googleBotV6 as $index => $range) 
            {
                if(mo2f_check_current_IP_in_rangeV6($range,$ipaddress))
                {
                    return false;
                }          
            }
        }
        return true;      
    }

    function mo2f_is_fake_bing($crawler,$user_agent,$ipaddress)
    {
        $dir     = dirname(dirname(__FILE__));
        $IPList  = $dir.DIRECTORY_SEPARATOR.'WAF'.DIRECTORY_SEPARATOR.'Includes'.DIRECTORY_SEPARATOR.'CrawlerIPs.php';
        include($IPList);
        if(strlen(inet_pton($ipaddress)) == 4)
        {
            foreach ($bingBot as $index => $range) 
            {
                if(mo2f_check_current_IP_in_range($range,$ipaddress))
                {
                    return false;
                }          
            }
        }
        return true;
    }
    
    function mo2f_is_fake_fb($crawler,$user_agent,$ipaddress)
    {
        $dir     = dirname(dirname(__FILE__));
        $IPList  = $dir.DIRECTORY_SEPARATOR.'WAF'.DIRECTORY_SEPARATOR.'Includes'.DIRECTORY_SEPARATOR.'CrawlerIPs.php';
        include($IPList);
        if(strlen(inet_pton($ipaddress)) == 4)
        {
            foreach ($facebookBot as $index => $range) 
            {
                if(mo2f_check_current_IP_in_range($range,$ipaddress))
                {
                    return false;
                }          
            }
        }
        else if(strlen(inet_pton($ipaddress)) == 16)
        {
            foreach ($facebookBotV6 as $index => $range) 
            {
                if(mo2f_check_current_IP_in_rangeV6($range,$ipaddress))
                {
                    return false;
                }          
            }
        }
        return true;
    }

    function mo2f_check_current_IP_in_rangeV6($range , $ipaddress)
    {
        $rangearray = explode(" - ",$range);
        if(sizeof($rangearray)==2)
        {
            $lowip      = mo2f_ipaddress_to_ipnumber(trim($rangearray[0]));
            $highip     = mo2f_ipaddress_to_ipnumber(trim($rangearray[1]));
            $currentIP  = mo2f_ipaddress_to_ipnumber(trim($ipaddress));
           
            if($currentIP>=$lowip && $currentIP<=$highip)
            {
                return true;
            }
            return false;
        }
        return false;

    }

    function mo2f_ipaddress_to_ipnumber($ipaddress) 
    {
        $pton = @inet_pton($ipaddress);
        if (!$pton) { return false; }
        $number = '';
        foreach (unpack('C*', $pton) as $byte) {
            $number .= str_pad(decbin($byte), 8, '0', STR_PAD_LEFT);
        }
        return base_convert(ltrim($number, '0'), 2, 10);
    }

    function mo2f_check_current_IP_in_range($range, $ipaddress)
    {
        $rangearray = explode(" - ",$range);
        if(sizeof($rangearray)==2)
        {
            $lowip  = ip2long(trim($rangearray[0]));
            $highip = ip2long(trim($rangearray[1]));
            if(ip2long(trim($ipaddress))>=$lowip && ip2long(trim($ipaddress))<=$highip)
            {
                return true;
            }
            return false;
        }
        return false;
    }
?>