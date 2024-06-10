<?php
namespace WPDM\__;

class Messages {

    public $template = "blank";

    public static function fullPage($title, $msg, $type = 'error'){
        include Template::locate("message.php", __DIR__.'/views');
        die();
    }

    public static function message($msg, $die = 0, $style = 'embed'){
        if(is_array($msg)) {
	        $title = sanitize_title($msg['title']);
	        $type = sanitize_html_class($msg['type']);
	        $_message = sanitize_title($msg['message']);
            if($style === 'modal')
                $message = "<script>WPDM.bootAlert('{$title}', '<div class=\'text-{$type}\'>{$_message}</div>')</script>";
            else if($style === 'notify')
                $message = "<script>WPDM.notify('<strong>{$msg['title']}</strong><br/>{$_message}', '{$msg['type']}', 'top-right')</script>";
            else
                $message = "<div class='w3eden'><div class='alert alert-{$msg['type']}' data-title='{$msg['title']}'>{$_message}</div></div>";
        }
        else {
	        $msg = sanitize_title($msg);
            if($style === 'mpdal')
                $message = "<script>WPDM.bootAlert('Attention Please!', '{$msg}')</script>";
            else if($style === 'notify')
                $message = "<script>WPDM.notify('{$msg}', 'info', 'top-right')</script>";
            else
                $message = $msg;
        }
        if($die==-1) return $message;
        if($die==0)
            echo wp_kses_post($message);
        if($die==1) {
            wp_die($message);
        }
        return true;
    }

    public static function error($msg, $die = 0, $style = 'embed'){
        if(!is_array($msg)) {
            $message = $msg;
            $msg = array();
            $msg['message'] = $message;
        }
        if(!isset($msg['title'])) $msg['title'] = 'Error!';
        $msg['type'] = 'danger';
        $msg['icon'] = 'exclamation-triangle';
        return self::Message($msg, $die, $style);
    }

    public static function warning($msg, $die = 0, $style = 'embed'){
        if(!is_array($msg)) {
            $message = $msg;
            $msg = array();
            $msg['message'] = $message;
        }
        if(!isset($msg['title'])) $msg['title'] = 'Warning!';
        $msg['type'] = 'warning';
        $msg['icon'] = 'exclamation-circle';
        return self::Message($msg, $die, $style);
    }

    public static function info($msg, $die = 0, $style = 'embed'){
        if(!is_array($msg)) {
            $message = $msg;
            $msg = array();
            $msg['message'] = $message;
        }
        if(!isset($msg['title'])) $msg['title'] = 'Attention!';
        $msg['type'] = 'info';
        $msg['icon'] = 'info-circle';
        return self::Message($msg, $die, $style);
    }

    public static function success($msg, $die = 0, $style = 'embed'){
        if(!is_array($msg)) {
            $message = $msg;
            $msg = array();
            $msg['message'] = $message;
        }
        if(!isset($msg['title'])) $msg['title'] = 'Awesome!';
        $msg['type'] = 'success';
        $msg['icon'] = 'check-circle';
        return self::Message($msg, $die, $style);
    }

    public static function decode_html($html){
        $html = htmlspecialchars_decode($html);
        $html = html_entity_decode($html, ENT_QUOTES);
        $html = stripslashes_deep($html);
        return $html;
    }

    public static function download_limit_exceeded($ID = null){
        $message = get_option("__wpdm_download_limit_exceeded");
        $message = self::decode_html($message);
        $message = wpdm_escs($message);
        $message = trim($message) !== '' ? $message : __( "Download Limit Exceeded!", "download-manager" );
        return $message;
    }

    public static function login_required($ID = null){
        $message = get_option("wpdm_login_msg");
        $message = self::decode_html($message);
        $message = wpdm_escs($message);
        $message = trim($message) !== '' ? $message : WPDM()->user->login->modalLoginFormBtn(['class' => 'btn btn-danger', 'label' => '<i class="fas fa-lock mr-3"></i>'.__( "Login", "download-manager" )]);
        return $message;
    }

    public static function permission_denied($ID = null){
        $message = get_option("__wpdm_permission_denied_msg");
        $message = self::decode_html($message);
        $message = wpdm_escs($message);
        $message = trim($message) !== '' ? $message : WPDM()->ui->button('<i class="fas fa-lock mr-3"></i>'.__( "Access Denied", "download-manager" ), ['class' => 'btn btn-danger']);
        return $message;
    }
}
