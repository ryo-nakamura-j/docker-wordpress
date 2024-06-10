<?php

namespace Webdesignby;

class Recaptcha{
    
    public $plugin_name = "webdesignby_recaptcha";
    
    protected $_config;
    protected $_site_key    = "";
    protected $_secret_key  = "";
    protected $_options_page;
    
    public function __construct( $config = null ) {
        
        if( ! empty($config) ){
            $this->_config = $config;
            if( ! empty($config['site_key'])){
                $this->_site_key = $config['site_key'];
            }
            if( ! empty($config['secret_key'])){
                $this->_secret_key = $config['secret_key'];
            }
        }
        
        if( !empty($this->_secret_key) && !empty($this->_site_key))
        {
            $actions = array(
                'login_enqueue_scripts',
                'login_form',
                'wp_authenticate',
            );

            foreach($actions as $action){
                add_action( $action, array( $this, $action));
            }

            add_filter( 'login_message', array($this, 'login_message') );
        }
        
        add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts') );
        add_action('admin_menu', array($this, 'admin_menu') );
        
    }
    
    public function grecaptcha_js(){
     ?>   
        <script type="text/javascript">
        var recaptcha1;
        var onloadCallback = function() {
            recaptcha1 = grecaptcha.render('g-recaptcha1', {
              'sitekey' : '<?php echo $this->_site_key; ?>',
              'theme' : 'light'
            });

          };
        </script>
     <?php
    }
    
    public function admin_enqueue_scripts(){

        $screen = \get_current_screen();
        $plugin_screen_id = "settings_page_webdesignby-recaptcha";
        if( $screen->id == $plugin_screen_id){
            
             if( ! empty($_POST) ){
                check_admin_referer( 'process' );
                $arr_recaptcha_vars = $_POST['webdesignby_recaptcha'];
                $g_site_key = trim( $arr_recaptcha_vars['g_site_key'] );
                $g_secret_key = trim( $arr_recaptcha_vars['g_secret_key'] );
                $webdesignby_recaptcha = array();
                $this->_site_key = $webdesignby_recaptcha['g_site_key'] = $g_site_key;
                $this->_secret_key = $webdesignby_recaptcha['g_secret_key'] = $g_secret_key;
                update_option('webdesignby_recaptcha', $webdesignby_recaptcha );
                $this->create_options_page();
                $this->_options_page->message = "<div id=\"setting-error-settings_updated\" class=\"updated settings-error\"> 
                            <p><strong>" . __('Settings saved', 'webdesignby-recaptcha') . "</strong></p></div>";
            }
            
            $this->grecaptcha_js();
        }
    }
    
    public function login_enqueue_scripts(){
        
        ?>
        <style type="text/css">
            .wp-login-recaptcha-wrapper{
                margin-bottom:15px;
            }
            form#loginform{
                min-width:302px;
            }
        </style>
        <?php
        $this->grecaptcha_js();
    }
    
    public function login_form(){
        
        ?>
        <div class="wp-login-recaptcha-wrapper">
            <div class="g-recaptcha" id="g-recaptcha1"></div>
        <?php if( ! empty($message)){ echo $message; } ?>
        </div>
          <script src='https://www.google.com/recaptcha/api.js?onload=onloadCallback&render=explicit' async defer></script>
        <?php
    }
    
    public function getRemoteIp(){
        return $_SERVER['REMOTE_ADDR'];
    }
    
    public function admin_error_notice($message){

	$class = "update-nag";
	$message = "your message";
        return "<div class=\"$class\"> <p>$message</p></div>";

    }
    
    public function login_message( $message ){
        
        $g_recaptcha_err = false;
        if( !empty($_GET['g-recaptcha_err']) ){
           $g_recaptcha_err = intval($_GET['g-recaptcha_err']);
        }
        if( $g_recaptcha_err ){
            $message_content = __("Please confirm you are not a robot", "webdesignby-recaptcha") . ".";
            $message = "<div id=\"login_error\"><strong>" . __("ERROR") . ":</strong> " . $message_content . "</div>";
        }

        return $message;
    }

    private function get_recaptcha_check( $g_recaptcha_response ){
    
        $g_recaptcha_check_url = "https://www.google.com/recaptcha/api/siteverify";
        $fields = array(
            "secret" => trim($this->_secret_key),
            "response" => $g_recaptcha_response,
            "remoteip" => $this->getRemoteIp()
        );
        $fields_string = "";
        foreach($fields as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }
        rtrim($fields_string, '&');
        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL, $g_recaptcha_check_url);
        curl_setopt($ch,CURLOPT_POST, count($fields));
        curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        try{
            $result = curl_exec($ch);
            curl_close($ch);
        }catch( Exception $e){
            $message = $e->getMessage();
        }
        $g_recaptcha_check = json_decode($result);
        return $g_recaptcha_check;
        
    }
    
    public function wp_authenticate(){

        if( ! empty($_POST)){
            $g_recaptcha_response = $_POST['g-recaptcha-response'];
            $g_recaptcha_check = $this->get_recaptcha_check( $g_recaptcha_response );
            if( empty($g_recaptcha_check->success) || !($g_recaptcha_check->success) ){
                header('Location: wp-login.php?g-recaptcha_err=1');
                exit();
            }
        }
    }
    
    private function create_options_page(){
        if( empty($this->_options_page) ){
            $this->_options_page = new \Webdesignby\RecaptchaOptionsPage;
        }
    }
    
    public function admin_menu(){
        $this->create_options_page();
        $this->_options_page->add();
    }
    
    public static function uninstall(){
        delete_option('webdesignby_recaptcha');
    }
    
}
