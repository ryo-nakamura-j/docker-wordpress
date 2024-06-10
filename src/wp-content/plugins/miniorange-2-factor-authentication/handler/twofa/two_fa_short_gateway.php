<?php

global $mo2f_dirName;

require_once $mo2f_dirName.'helper'.DIRECTORY_SEPARATOR.'mo_twofa_sessions.php';

if(! defined( 'ABSPATH' )) exit;
define('MO2F_DEFAULT_APIKEY',"fFd2XcvTGDemZvbw1bcUesNJWEqKbbUq");
define('MO2F_FAIL_MODE', false);
define('MO2F_SESSION_TYPE', "TRANSIENT");

class TwoFAMOGateway
{
    public static function mo_send_otp_token($authType, $phone, $email)
    {
        if(MO2F_TEST_MODE)
        {
            return ['message'=>'OTP Sent Successfully','status'=>'SUCCESS','txId'=> rand(1000,9999)];
        }
        else
        {
            $customerKey = get_site_option('mo2f_customerKey');
            $apiKey      = get_site_option('mo2f_api_key');
            TwoFAMoSessions::addSessionVar('mo2f_transactionId',true);
            TwoFAMoSessions::addSessionVar('sent_on',time());

            if($authType == 'EMAIL')
            {
                $cmVtYWluaW5nT1RQ = MoWpnsUtility::get_mo2f_db_option('cmVtYWluaW5nT1RQ', 'site_option');
                if($cmVtYWluaW5nT1RQ>0)
                update_site_option("cmVtYWluaW5nT1RQ",$cmVtYWluaW5nT1RQ-1);
                $content = (new Customer_Cloud_Setup)->send_otp_token($email,$authType,$customerKey,$apiKey);
            }

            else
            {
                $mo2f_sms = get_site_option('cmVtYWluaW5nT1RQVHJhbnNhY3Rpb25z');
                if($mo2f_sms>0)
                update_site_option('cmVtYWluaW5nT1RQVHJhbnNhY3Rpb25z',$mo2f_sms-1);
            
                $content = (new Customer_Cloud_Setup)->send_otp_token($phone,$authType,$customerKey,$apiKey);
            }
            return json_decode($content,TRUE);
        }
    }

    public static function mo_validate_otp_token($authType,$txId, $otp_token)
    {
        if(MO2F_TEST_MODE)
        {
            TwoFAMoSessions::unsetSession('mo2f_transactionId');
            return MO2F_FAIL_MODE ? ['status'=>"FAILED","message"=>"OTP is Invalid"]:['status'=>"SUCCESS","message"=>"Successfully Validated"];
        }
        else
        {
            $content = "";
            if(TwoFAMoSessions :: getSessionVar('mo2f_transactionId'))
            {
                $customerKey = get_site_option('mo2f_customerKey');
                $apiKey      = get_site_option('mo2f_api_key');
                $content = (new Customer_Cloud_Setup)->validate_otp_token($authType,null,$txId,$otp_token,$customerKey,$apiKey);
                $content = json_decode($content, TRUE);
                if($content["status"] == "SUCCESS")
                {
                    TwoFAMoSessions :: unsetSession('mo2f_transactionId');
                }
            }
            return $content;
        }
    }
}