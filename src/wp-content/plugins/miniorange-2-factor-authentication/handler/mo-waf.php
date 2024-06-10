<?php

    $dir =dirname(__FILE__);
    $dir = str_replace('\\', "/", $dir);
    $sqlInjectionFile   = $dir.'/signature/APSQLI.php';
    $xssFile            = $dir.'/signature/APXSS.php';
    $lfiFile            = $dir.'/signature/APLFI.php';
    $configfilepath     = explode('wp-content', $dir);
    $configfile         = $configfilepath[0].'/wp-includes/mo-waf-config.php';

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


    global $dbcon;
    $dbcon = new mysqli($dbD['DB_HOST'],$dbD['DB_USER'],$dbD['DB_PASSWORD']);
    if(!$dbcon)
    {
        echo "database connection error";
        exit;

    }

    if(mysqli_select_db($dbcon,$dbD['DB_NAME']))
    {
        $query  = 'SELECT * FROM '.$prefix.'options WHERE `option_name`="WAF";';
        $results1 = mysqli_query($dbcon,$query);
        $row = mysqli_fetch_array($results1);
        if(isset($row['option_value']) && $row['option_value']=='HtaccessLevel'){
            global $wpdb,$moWpnsUtility;
	        $ipaddress = $moWpnsUtility->get_client_ip();
            $ipaddress = filter_var($ipaddress, FILTER_VALIDATE_IP) ? $ipaddress : 'UNKNOWN';
            
            $query =  'select * from '.$prefix.'mo2f_network_blocked_ips where ip_address="'.$ipaddress.'";';
            $results = mysqli_query($dbcon,$query);
            if($results)
            {
                $row = mysqli_fetch_array($results);
                $query = 'select * from '.$prefix.'mo2f_network_whitelisted_ips where ip_address="'.$ipaddress.'";';
                $results = mysqli_query($dbcon,$query);
                if($results)
                {
                    $row1 = mysqli_fetch_array($results);
                    if(!is_null($row1['ip_address']))
                    {

                    }
                    else if(!is_null($row['ip_address']))
                    {
                        header('HTTP/1.1 403 Forbidden');
                        include_once("mo-block.html");
                        exit;
                    }
                }
                else if(!is_null($row['ip_address']))
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
                    $file   = fopen($fileName, "a+");
                    $string = "<?php".PHP_EOL;
                    $query  = 'select option_value from '.$prefix.'options where option_name = "SQLInjection";' ;
                    $results = mysqli_query($dbcon,$query);
                    if($results)
                    {
                        $row = mysqli_fetch_array($results);
                        $string .= '$SQL='.$row["option_value"].';'.PHP_EOL;
                    }
                    $query  = 'select option_value from '.$prefix.'options where option_name = "XSSAttack";' ;
                    $results = mysqli_query($dbcon,$query);
                    if($results)
                    {
                        $row = mysqli_fetch_array($results);
                        $string .= '$XSS='.$row["option_value"].';'.PHP_EOL;
                    }
                    $query  = 'select option_value from '.$prefix.'options where option_name = "RFIAttack";' ;
                    $results = mysqli_query($dbcon,$query);
                    if($results)
                    {
                        $row = mysqli_fetch_array($results);
                        $string .= '$RFI='.$row["option_value"].';'.PHP_EOL;
                    }
                    $query  = 'select option_value from '.$prefix.'options where option_name = "LFIAttack";' ;
                    $results = mysqli_query($dbcon,$query);
                    if($results)
                    {
                        $row = mysqli_fetch_array($results);
                        $string .= '$LFI='.$row["option_value"].';'.PHP_EOL;
                    }
                    $query  = 'select option_value from '.$prefix.'options where option_name = "RCEAttack";' ;
                    $results = mysqli_query($dbcon,$query);
                    if($results)
                    {
                        $row = mysqli_fetch_array($results);
                        $string .= '$RCE='.$row["option_value"].';'.PHP_EOL;
                    }
                    $query  = 'select option_value from '.$prefix.'options where option_name = "Rate_limiting";' ;
                    $results = mysqli_query($dbcon,$query);
                    if($results)
                    {
                        $row = mysqli_fetch_array($results);
                        if($row["option_value"]!='')
                            $string .= '$RateLimiting='.$row["option_value"].';'.PHP_EOL;
                        else
                            $string .= '$RateLimiting=0;'.PHP_EOL;
                    }
                    $query  = 'select option_value from '.$prefix.'options where option_name = "Rate_request";' ;
                    $results = mysqli_query($dbcon,$query);
                    if($results)
                    {
                        $row = mysqli_fetch_array($results);
                        if($row["option_value"]!='')
                            $string .= '$RequestsPMin='.$row["option_value"].';'.PHP_EOL;
                        else
                            $string .= '$RequestsPMin=120;'.PHP_EOL;
                    }

                    $query  = 'select option_value from '.$prefix.'options where option_name = "actionRateL";' ;
                    $results = mysqli_query($dbcon,$query);
                    if($results)
                    {

                        $row = mysqli_fetch_array($results);
                        if($row["option_value"] == 1)
                            $string .= '$actionRateL="ThrottleIP";'.PHP_EOL;
                        else
                            $string .= '$actionRateL="BlockIP";'.PHP_EOL;
                    }
                    $string .= '?>'.PHP_EOL;
                    fwrite($file, $string);
                    fclose($file);

                }

            }

            include_once($fileName);
            if($RateLimiting == 1)
            {
                 
                  
                    $time       = 60;
                    $reqLimit   = $RequestsPMin;
                    $query = "delete from ".$prefix."wpns_ip_rate_details where time<".(time()-$time);
                    $results = mysqli_query($dbcon,$query);

                    $query = "insert into ".$prefix."wpns_ip_rate_details values('".$ipaddress."',".time().");";
                    $results = mysqli_query($dbcon,$query);

                    $query = "select count(*) from ".$prefix."wpns_ip_rate_details where ip='".$ipaddress."';";
                    $results = mysqli_query($dbcon,$query);

                    if($results)
                    {
                        $row = mysqli_fetch_array($results);
                        if($row['count(*)']>=$reqLimit)
                        {
                            $action = $actionRateL;
                            if($action == 'ThrottleIP')
                            {
                                $query = "select time from ".$prefix."wpns_attack_logs where ip ='".$ipaddress."' ORDER BY time DESC LIMIT 1;";
                                $results = mysqli_query($dbcon,$query);
                                $results = mysqli_fetch_array($results);
                                $current_time = time();
                                if($current_time>$results['time']+60)
                                {
                                    $query = "insert into ".$prefix."wpns_attack_logs values('".$ipaddress."','Rate Limit',".time().",'".MoWpnsConstants::RATE_LIMIT_EXCEEDED."');";
                                    $results = mysqli_query($dbcon,$query);
                                }
                                header('HTTP/1.1 403 Forbidden');
                                include_once("mo-error.html");
                                exit;
                            }
                            else
                            {
                                $query = "select time from ".$prefix."wpns_attack_logs where ip ='".$ipaddress."' ORDER BY time DESC LIMIT 1;";
                                $results = mysqli_query($dbcon,$query);
                                $results = mysqli_fetch_array($results);
                                $current_time = time();
                                if($current_time>$results['time']+60)
                                {
                                    $query = "insert into ".$prefix."wpns_attack_logs values('".$ipaddress."','Rate Limit',".time().",'".MoWpnsConstants::RATE_LIMIT_EXCEEDED."');";
                                    $results = mysqli_query($dbcon,$query);
                                }
                                $query = 'select * from '.$prefix.'mo2f_network_whitelisted_ips where ip_address="'.$ipaddress.'";';
                                $results = mysqli_query($dbcon,$query);
                                if($results)
                                {
                                    $row1 = mysqli_fetch_array($results);
                                    if(!is_null($row1['ip_address']))
                                    {

                                    }
                                    else
                                    {
                                        $query ="insert into ".$prefix."mo2f_network_blocked_ips values(NULL,'".$ipaddress."','Rate limit exceed',NULL,".time().");";
                                        $results = mysqli_query($dbcon,$query);
                                    }
                                    header('HTTP/1.1 403 Forbidden');
                                    include_once("mo-error.html");
                                    exit;
                                }
                            }
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
            


            $attackC = $attack;
            $ParanoiaLevel =  1;
            $annomalyS = 0;
            $SQLScore = 0;
            $XSSScore = 0;
            $query = 'select option_value from '.$prefix.'options where option_name ="limitAttack";';
            $results = mysqli_query($dbcon,$query);
            $rows   =  mysqli_fetch_array($results);

            $limitAttack = intval($rows['option_value']);


            foreach ($attackC as $key1 => $value1) {
                for($lev=1;$lev<=$ParanoiaLevel;$lev++)
                {
                    if(isset($regex[$value1][$lev]))
                    {   $ooo = 0;
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
                                                    $query  = 'insert into '.$prefix.'wpns_attack_logs values ("'.$ipaddress.'","'.$value1.'",'.time().',"'.$value.'");';
                                                    $results = mysqli_query($dbcon,$query);
                                                    $query  = "select count(*) from ".$prefix."wpns_attack_logs where ip='".$ipaddress."' and input != '".MoWpnsConstants::RATE_LIMIT_EXCEEDED."';";
                                                    $results = mysqli_query($dbcon,$query);
                                                    $rows   =  mysqli_fetch_array($results);
                                                    if($rows['count(*)']>$limitAttack)
                                                    {
                                                        $query = 'select * from '.$prefix.'mo2f_network_whitelisted_ips where ip_address="'.$ipaddress.'";';
                                                        $results = mysqli_query($dbcon,$query);
                                                        if($results)
                                                        {
                                                            $row1 = mysqli_fetch_array($results);
                                                            if(!is_null($row1['ip_address']))
                                                            {
                                                                //IP WHiTELISTED
                                                            }
                                                            else
                                                            {
                                                                $query ="insert into ".$prefix."mo2f_network_blocked_ips values(NULL,'".$ipaddress."','attack limit exceed',NULL,".time().");";
                                                                $results = mysqli_query($dbcon,$query);
                                                            }
                                                        }
                                                    }


                                                    header('HTTP/1.1 403 Forbidden');
                                                    include_once("mo-error.html");
                                                    exit;
                                                }

                                            }}
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
                                                    $query  = 'insert into '.$prefix.'wpns_attack_logs values ("'.$ipaddress.'","'.$value1.'",'.time().',"'.$value.'");';
                                                    $results = mysqli_query($dbcon,$query);
                                                    $query  = "select count(*) from ".$prefix."wpns_attack_logs where ip='".$ipaddress."' and input != '".MoWpnsConstants::RATE_LIMIT_EXCEEDED."';";
                                                    $results = mysqli_query($dbcon,$query);
                                                    $rows   =  mysqli_fetch_array($results);
                                                    if($rows['count(*)']>$limitAttack)
                                                    {
                                                        $query = 'select * from '.$prefix.'mo2f_network_whitelisted_ips where ip_address="'.$ipaddress.'";';
                                                        $results = mysqli_query($dbcon,$query);
                                                        if($results)
                                                        {
                                                            $row1 = mysqli_fetch_array($results);
                                                            if(!is_null($row1['ip_address']))
                                                            {
                                                                //IP WHiTELISTED
                                                            }
                                                            else
                                                            {
                                                                $query ="insert into ".$prefix."mo2f_network_blocked_ips values(NULL,'".$ipaddress."','attack limit exceed',NULL,".time().");";
                                                                $results = mysqli_query($dbcon,$query);
                                                            }
                                                        }
                                                    }


                                                    header('HTTP/1.1 403 Forbidden');
                                                    include_once("mo-error.html");
                                                    exit;
                                                }
                                            }}
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
                                                    $query  = 'insert into '.$prefix.'wpns_attack_logs values ("'.$ipaddress.'","'.$value1.'",'.time().',"'.$value.'");';
                                                    $results = mysqli_query($dbcon,$query);
                                                    $query  = "select count(*) from ".$prefix."wpns_attack_logs where ip='".$ipaddress."' and input != '".MoWpnsConstants::RATE_LIMIT_EXCEEDED."';";
                                                    $results = mysqli_query($dbcon,$query);
                                                    $rows   =  mysqli_fetch_array($results);
                                                    if($rows['count(*)']>$limitAttack)
                                                    {
                                                        $query = 'select * from '.$prefix.'mo2f_network_whitelisted_ips where ip_address="'.$ipaddress.'";';
                                                        $results = mysqli_query($dbcon,$query);
                                                        if($results)
                                                        {
                                                            $row1 = mysqli_fetch_array($results);
                                                            if(!is_null($row1['ip_address']))
                                                            {
                                                                //IP WHiTELISTED
                                                            }
                                                            else
                                                            {
                                                                $query ="insert into ".$prefix."mo2f_network_blocked_ips values(NULL,'".$ipaddress."','attack limit exceed',NULL,".time().");";
                                                                $results = mysqli_query($dbcon,$query);
                                                            }
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
        }
    }

    $dbcon->close();
?>
