<?php

include_once('two_fa_short_gateway.php');

class TwoFACustomRegFormAPI
{
    public function __construct()
    {

    }

    public static function challenge($phone_number,$email,$authTypeSend)
    {


        if($authTypeSend == 'email')
        {
            $auierpyasdcRy  = MoWpnsUtility::get_mo2f_db_option('cmVtYWluaW5nT1RQ', 'site_option');
            $cmVtYWluaW5nT1RQ 	= $auierpyasdcRy? $auierpyasdcRy : 0;
            if($cmVtYWluaW5nT1RQ > 0)
            {
                $response = TwoFAMOGateway:: mo_send_otp_token('EMAIL', '', $email);
                update_site_option("cmVtYWluaW5nT1RQ",$cmVtYWluaW5nT1RQ-1);
            }
            else
            {
                $response = ['status'=>'ERROR','message'=>'Email Transaction Limit Exceeded'];
                wp_send_json($response);
            }
        }
        else
        {
            $response = TwoFAMOGateway::  mo_send_otp_token('SMS', $phone_number, $email);
        }
        wp_send_json($response);

    }

    public static function validate($txId, $otp)
    {
        wp_send_json(TwoFAMOGateway :: mo_validate_otp_token('OTP',$txId, $otp));
    }
}