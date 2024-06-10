<?php
const INITIAL_BACKOFF_SECONDS = 1;
const MAX_BACKOFF_SECONDS = 32;
const BACKOFF_FACTOR = 2;
const RATE_LIMIT_HTTP_CODE = 429;



function ping($skey, $ikey, $host)
    {
        $method = "GET";
        $endpoint = "/auth/v2/ping";
        $params = [];

        return jsonApiCall($method, $endpoint, $params,$skey,$ikey,$host);
    
    }

function check($skey, $ikey, $host)
    {
        $method = "GET";
        $endpoint = "/auth/v2/check";
        $params = [];

        return jsonApiCall($method, $endpoint, $params,$skey,$ikey,$host);
    } 


function delete($skey,$ikey,$host,$user_id)
    {
        $method = "DELETE";
        $endpoint = "/admin/v1/users/".$user_id;
        $params = [];

        return jsonApiCall($method, $endpoint, $params,$skey,$ikey,$host);
    }


function enroll($username = null, $valid_secs = null)
    {
         $ikey = get_site_option('mo2f_d_integration_key');
         $skey = get_site_option('mo2f_d_secret_key');
         $host = get_site_option('mo2f_d_api_hostname');
        assert(is_string($username) || is_null($username));
        assert(is_int($valid_secs) || is_null($valid_secs));


        $method = "POST";
        $endpoint = "/auth/v2/enroll";
        $params = [];

        if ($username) {
            $params["username"] = $username;
        }
        if ($valid_secs) {
            $params["valid_secs"] = $valid_secs;
        }

        return jsonApiCall($method, $endpoint, $params,$skey,$ikey,$host);
    } 

  function enroll_status($user_id, $activation_code,$skey,$ikey,$host)
    {
        assert(is_string($user_id));
        assert(is_string($activation_code));

        $method = "POST";
        $endpoint = "/auth/v2/enroll_status";
        $params = [
            "user_id" => $user_id,
            "activation_code" => $activation_code,
        ];

        return jsonApiCall($method, $endpoint, $params,$skey,$ikey,$host);
    }
          
 
 function preauth(
        $user_identifier,
        $username,
        $skey,
        $ikey,
        $host,
        $ipaddr = null,
        $trusted_device_token = null
        
    ) {
        
        assert(is_string($ipaddr) || is_null($ipaddr));
        assert(is_string($trusted_device_token) || is_null($trusted_device_token));
        

        $method = "POST";
        $endpoint = "/auth/v2/preauth";
        $params = [];

        if ($username) {
            $params["username"] = $user_identifier;
        } else {
            $params["user_id"] = $user_identifier;
        }
        if ($ipaddr) {
            $params["ipaddr"] = $ipaddr;
        }
        if ($trusted_device_token) {
            $params["trusted_device_token"] = $trusted_device_token;
        }

        return jsonApiCall($method, $endpoint, $params,$skey,$ikey,$host);
}


function mo2f_duo_auth(
        $user_identifier,
        $factor,
        $factor_params,
        $skey,
        $ikey,
        $host,
        $username = true,
        $ipaddr = null,
        $async = false,
        $timeout = 60
    ) {
        assert(is_string($user_identifier));
        assert(
            is_string($factor) &&
            in_array($factor, ["auto", "push", "passcode", "sms", "phone"], true)
        );
        assert(is_array($factor_params));
        assert(is_string($ipaddr) || is_null($ipaddr));
        assert(is_bool($async));
        assert(is_bool($username));

        $method = "POST";
        $endpoint = "/auth/v2/auth";
        $params = [];

        if ($username) {
            $params["username"] = $user_identifier;
        } else {
            $params["user_id"] = $user_identifier;
        }
        if ($ipaddr) {
            $params["ipaddr"] = $ipaddr;
        }
        if ($async) {
            $params["async"] = "1";
        }

        $params["factor"] = $factor;

        if ($factor === "push") {
            assert(array_key_exists("device", $factor_params) && is_string($factor_params["device"]));
            $params["device"] = $factor_params["device"];

            if (array_key_exists("type", $factor_params)) {
                $params["type"] = $factor_params["type"];
            }
            if (array_key_exists("display_username", $factor_params)) {
                $params["display_username"] = $factor_params["display_username"];
            }
            if (array_key_exists("pushinfo", $factor_params)) {
                $params["pushinfo"] = $factor_params["pushinfo"];
            }
        } elseif ($factor === "passcode") {
            assert(array_key_exists("passcode", $factor_params) && is_string($factor_params["passcode"]));
            $params["passcode"] = $factor_params["passcode"];
        } elseif ($factor === "phone") {
            assert(array_key_exists("device", $factor_params) && is_string($factor_params["device"]));
            $params["device"] = $factor_params["device"];
        } elseif ($factor === "sms") {
            assert(array_key_exists("device", $factor_params) && is_string($factor_params["device"]));
            $params["device"] = $factor_params["device"];
        } elseif ($factor === "auto") {
            assert(array_key_exists("device", $factor_params) && is_string($factor_params["device"]));
            $params["device"] = $factor_params["device"];
        }

       
         $options = [
            "timeout" => $timeout,
        ];
        $requester_timeout = array_key_exists("timeout", $options) ? $options["timeout"] : null;
        if (!$requester_timeout || $requester_timeout < $timeout) {
            setRequesterOption("timeout", $timeout);
        }

        try {
            $result = jsonApiCall($method, $endpoint, $params,$skey,$ikey,$host);
        } finally {
           
            if ($requester_timeout) {
                setRequesterOption("timeout", $requester_timeout);
            } else {
                unset($options["timeout"]);
            }
        }

        return $result;
    }
function setRequesterOption($option, $value)
    {
        $options[$option] = $value;
        return $options;
    }

function mo2f_sleep($seconds)
{
    usleep($seconds * 1000000);
}


function options($options)
    {

        $ch =  curl_init();
        assert(is_array($options));

       
        $possible_options = [
            CURLOPT_TIMEOUT => "timeout",
            CURLOPT_CAINFO => "ca",
            CURLOPT_USERAGENT => "user_agent",
            CURLOPT_PROXY => "proxy_url",
            CURLOPT_PROXYPORT => "proxy_port",
        ];

        $curl_options = array_filter($possible_options, function ($option) use ($options) {
            return array_key_exists($option, $options);
        });

        foreach ($curl_options as $key => $value) {
            $curl_options[$key] = $options[$value];
        }

        
        $curl_options[CURLOPT_RETURNTRANSFER] = 1;
        $curl_options[CURLOPT_FOLLOWLOCATION] = 1;
        $curl_options[CURLOPT_SSL_VERIFYPEER] = true;
        $curl_options[CURLOPT_SSL_VERIFYHOST] = 2;

        curl_setopt_array($ch, $curl_options);
    }

    function execute($url, $method, $headers, $body = null)
    {
        $ch =  curl_init();
        assert(is_string($url));
        assert(is_string($method));
        assert(is_array($headers));
        assert(is_string($body) || is_null($body));

        $headers = array_map(function ($key, $value) {
            return sprintf("%s: %s", $key, $value);
        }, array_keys($headers), array_values($headers));


        $args = array(
            'method' => $method,
            'timeout' => '5',
            'redirection' => '5',
            'httpversion' => '1.0',
            'blocking' => true,
            'headers' => $headers
        );

        if ($method === "POST") {
            $args['body']=$body;
        }
        
        
        $result=wp_remote_post($url,$args);
        
        if(is_wp_error($result)){
            return [
                "response" => '',
                "success" => '',
                "http_status_code" => ''
            ];
        } 
        
        $status_code=wp_remote_retrieve_response_code($result);
      
        $http_status_code = null;
        $success = true;
        if ($result === false) {
            
            $result = json_encode(
                [
                    'stat' => 'FAIL',
                    'code' => $errno,
                    'message' => $error,
                ]
            );
            $success = false;
        } else {
            $http_status_code = isset($status_code)?$status_code:'404';
        }

        return [
            "response" => $result['body'],
            "success" => $success,
            "http_status_code" => $http_status_code
        ];
    }

function jsonApiCall($method, $path, $params,$skey,$ikey,$host)
{
    assert(is_string($method));
    assert(is_string($path));
    assert(is_array($params));

    $result = apiCall($method, $path, $params,$skey,$ikey,$host);
    
    $result["response"] = json_decode($result["response"], true);
    return $result;
}

 

function urlEncodeParameters($params)
{
    assert(is_array($params));

    ksort($params);
    $args = array_map(function ($key, $value) {
        return sprintf("%s=%s", rawurlencode($key), rawurlencode($value));
    }, array_keys($params), array_values($params));
    return implode("&", $args);
}
function canonicalize($method, $host, $path, $params, $now)
{
    assert(is_string($method));
    assert(is_string($host));
    assert(is_string($path));
    assert(is_array($params));
    assert(is_string($now));

    $args = urlEncodeParameters($params);
    $canon = array($now, strtoupper($method), strtolower($host), $path, $args);

    $canon = implode("\n", $canon);

    return $canon;
}
function sign($msg, $key)
{
    assert(is_string($msg));
    assert(is_string($key));

    return hash_hmac("sha1", $msg, $key);
}

function signParameters($method, $host, $path, $params, $skey, $ikey, $now)
{
    assert(is_string($method));
    assert(is_string($host));
    assert(is_string($path));
    assert(is_array($params));
    assert(is_string($skey));
    assert(is_string($ikey));
    assert(is_string($now));

    $canon = canonicalize($method, $host, $path, $params, $now);

    $signature = sign($canon, $skey);
    $auth = sprintf("%s:%s", $ikey, $signature);
    $b64auth = base64_encode($auth);

    return sprintf("Basic %s", $b64auth);
}


function makeRequest($method, $uri, $body, $headers,$host)
{

    assert(is_string($method));
    assert(is_string($uri));
    assert(is_string($body) || is_null($body));
    assert(is_array($headers));
    $url = "https://" . $host . $uri;
    
    $options = [
            "timeout" => 10,
        ];
        
    options($options);
    $backoff_seconds = INITIAL_BACKOFF_SECONDS;
    while (true) {
        $result = execute($url, $method, $headers, $body);
        
        if ($result["http_status_code"] != RATE_LIMIT_HTTP_CODE || $backoff_seconds > MAX_BACKOFF_SECONDS) {
            return $result;
        }

        mo2f_sleep($backoff_seconds + (rand(0, 1000) / 1000.0));
        $backoff_seconds *= BACKOFF_FACTOR;
    }
}
function apiCall($method, $path, $params,$skey,$ikey,$host)
{
    assert(is_string($method));
    assert(is_string($path));
    assert(is_array($params));

    $now = date(DateTime::RFC2822);

    $headers = [];
    $headers["Date"] = $now;
    $headers["Host"] = $host;
    $headers["Authorization"] = signParameters(
        $method,
        $host,
        $path,
        $params,
        $skey,
        $ikey,
        $now
    );

    if (in_array($method, ["POST", "PUT"], true)) {
        
        $body = http_build_query($params);
        $headers["Content-Type"] = "application/x-www-form-urlencoded";
        $headers["Content-Length"] = strval(strlen($body));
        $uri = $path;
    } else {
        $body = null;
        $uri = $path . (!empty($params) ? "?" . urlEncodeParameters($params) : "");
    }
  
    return makeRequest($method, $uri, $body, $headers,$host);
}