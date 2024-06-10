<?php
	global $dbcon,$prefix;
    include_once('mo-waf-db-common.php');
    function mo_wpns_log_attack($ipaddress,$value1,$value)
    {
        global $prefix,$dbcon;
        $value      = htmlspecialchars($value);
        $query      = 'insert into '.$prefix.'wpns_attack_logs values ("'.$ipaddress.'","'.$value1.'",'.time().',"'.$value.'");';
        $results    = mysqli_query($dbcon,$query);
        $query      = "select count(*) from ".$prefix."wpns_attack_logs where ip='".$ipaddress."' and input != 'RLE';";
        $results    = mysqli_query($dbcon,$query);
        $rows       = mysqli_fetch_array($results);
        return $rows['count(*)'];
    }
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
            $file   = fopen($fileName, "a+");
            $string = "<?php".PHP_EOL;

            $sqlInjection = mo_wpns_get_option_value("SQLInjection");
            $string .= '$SQL='.$sqlInjection.';'.PHP_EOL;

            $XSSAttack = mo_wpns_get_option_value("XSSAttack");
            $string .= '$XSS='.$XSSAttack.';'.PHP_EOL;
            
            $RFIAttack = mo_wpns_get_option_value("RFIAttack");
            $string .= '$RFI='.$RFIAttack.';'.PHP_EOL;

            $LFIAttack = mo_wpns_get_option_value("LFIAttack");
            $string .= '$LFI='.$LFIAttack.';'.PHP_EOL;
            
            $RCEAttack = mo_wpns_get_option_value("RCEAttack");
            $string .= '$RCE='.$RCEAttack.';'.PHP_EOL;

            $Rate_limiting = mo_wpns_get_option_value("Rate_limiting");
            if($Rate_limiting!='')
                $string .= '$RateLimiting='.$Rate_limiting.';'.PHP_EOL;
            else
                $string .= '$RateLimiting=0;'.PHP_EOL;

            $Rate_request = mo_wpns_get_option_value("Rate_request");
            if($Rate_request!='')
                $string .= '$RequestsPMin='.$Rate_request.';'.PHP_EOL;
            else
                $string .= '$RequestsPMin=0;'.PHP_EOL;

            $actionRateL = mo_wpns_get_option_value("actionRateL");
            if($actionRateL==1)
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
    function mo_wpns_is_ip_whitelisted($ipaddress)
    {   
        global $dbcon,$prefix;
        $query      = 'select * from '.$prefix.'mo2f_network_whitelisted_ips where ip_address="'.$ipaddress.'";';
        $results    = mysqli_query($dbcon,$query);
        if($results)
        {
            $row = mysqli_fetch_array($results);
            if(is_null($row))
            {
                return false;
            }
            else
            {
                return true;
            }
        }
        return false;   
    }
    function mo_wpns_is_ip_blocked($ipaddress)
    {
        global $dbcon,$prefix;
        $query =  'select * from '.$prefix.'mo2f_network_blocked_ips where ip_address="'.$ipaddress.'";';
        $results = mysqli_query($dbcon,$query);
        if($results)
        {
            $row = mysqli_fetch_array($results);
            if(is_null($row))
            {
                return false;
            }
            else
            {
                return true;
            }
        }
        return false;       
    }
    function mo_wpns_block_ip($ipaddress,$reason)
    {
        global $dbcon, $prefix;
        $query ="insert into ".$prefix."mo2f_network_blocked_ips values(NULL,'".$ipaddress."','".$reason."',NULL,".time().");";
        $results = mysqli_query($dbcon,$query);
    }
    function mo_wpns_dbconnection()
    {
        global $dbcon,$prefix;
        $dir = dirname(__FILE__);
        $dir = str_replace('\\', "/", $dir);
        $dir_name = explode('wp-content', $dir);    
        $file = file_get_contents($dir_name[0].'wp-config.php');
        $content =  explode("\n", $file);
        $len = sizeof($content);
        $Ismultisite    = 0;
        $dbD = array('DB_NAME' =>'' ,'DB_USER' => '' ,'DB_PASSWORD' =>'','DB_HOST' =>'','DB_CHARSET' =>'','DB_COLLATE' =>'' );
        
        $prefix = 'wp_';

        for($i=0;$i<$len;$i++)
        {   

            if(preg_match("/define/", $content[$i])) 
            {
                 $cont = explode(",", $content[$i]);
                 $string = str_replace(array('define(',' ','\''), '', $cont[0]);
                 switch ($string) {
                    case "DB_NAME":
                        $res = str_replace(array('\'',')',';',' '), '', $cont[1]);
                        $res = preg_replace('/\s/', '', $res);
                        $dbD['DB_NAME'] = $res;
                        break;
                    case 'DB_USER':
                        $res = str_replace(array('\'',')',';',' '), '', $cont[1]);
                        $res = preg_replace('/\s/', '', $res);
                        $dbD['DB_USER'] = $res;
                        break;
                    case "DB_PASSWORD":
                        $res = str_replace(array('\'',')',';',' '), '', $cont[1]);
                        $res = preg_replace('/\s/', '', $res);
                        $dbD['DB_PASSWORD'] = $res;
                        break;
                    case 'DB_HOST':
                        $res = str_replace(array('\'',')',';',' '), '', $cont[1]);
                        $res = preg_replace('/\s/', '', $res);
                        $dbD['DB_HOST'] = $res;
                        break;
                    case "DB_CHARSET":
                        $res = str_replace(array('\'',')',';',' '), '', $cont[1]);
                        $res = preg_replace('/\s/', '', $res);
                        $dbD['DB_CHARSET'] = $res;
                        break;
                    case 'DB_COLLATE':
                        $res = str_replace(array('\'',')',';',' '), '', $cont[1]);
                        $res = preg_replace('/\s/', '', $res);
                        $dbD['DB_COLLATE'] = $res;
                        break;
                    default:
                    
                        break;
                 }
            }
            if(preg_match('/\$table_prefix/', $content[$i]))
            {
                $cont = explode("'", $content[$i]);

                $prefix = $cont['1'];
            }
        }
        $dbcon = new mysqli($dbD['DB_HOST'],$dbD['DB_USER'],$dbD['DB_PASSWORD']);
        if(!$dbcon)
        {
            echo "database connection error";
            exit;
        }
        $connection = mysqli_select_db($dbcon,$dbD['DB_NAME']);
        return $connection;
    }
    function mo_wpns_get_option_value($option)
    {   
        global $dbcon,$prefix;
        $query          = 'select option_value from '.$prefix.'options where option_name ="'.$option.'";';
        $results        = mysqli_query($dbcon,$query);
        if($results)
        {
            $rows           = mysqli_fetch_array($results);
            if(isset($rows)&&(!is_null($rows['option_value'])))
            {
                $option_value   = intval($rows['option_value']);  
                return $option_value;
            }
        }
        return '';
    }
    
    function mo_wpns_getRLEattack($ipaddress)
    {
        global $dbcon,$prefix;
        $query = "select time from ".$prefix."wpns_attack_logs where ip ='".$ipaddress."' and type = 'RLE' ORDER BY time DESC LIMIT 1;";
        $results = mysqli_query($dbcon,$query);
        if($results)
        {
            $results = mysqli_fetch_array($results);
            return $results['time'];
        }
        return 0;
    }
    function mo_wpns_CheckRate($ipaddress)
    {
        global $dbcon,$prefix;
        $time       = 60;
        mo_wpns_clearRate($time);
        mo_wpns_insertRate($ipaddress);
        $query = "select count(*) from ".$prefix."wpns_ip_rate_details where ip='".$ipaddress."';";
        $results = mysqli_query($dbcon,$query);

        if($results)
        {
            $row = mysqli_fetch_array($results);
            return $row['count(*)'];
        }
        return 0;
    }
    function mo_wpns_clearRate($time)
    {
        global $dbcon,$prefix;
        $query = "delete from ".$prefix."wpns_ip_rate_details where time<".(time()-$time);
        $results = mysqli_query($dbcon,$query);
    }
    function mo_wpns_insertRate($ipaddress)
    {
        global $dbcon,$prefix;
        $query = "insert into ".$prefix."wpns_ip_rate_details values('".$ipaddress."',".time().");";
        $results = mysqli_query($dbcon,$query);
    }
    
?>