<?php

namespace Webdesignby;

if( ! class_exists('Webdesignby\RecaptchaOptionsPage')) {

    class RecaptchaOptionsPage{

        public $page_title = "reCaptcha Settings";
        public $menu_title = "reCaptcha";
        public $capabilites = "manage_options";
        public $page_slug = "webdesignby-recaptcha";
        public $message    = "";

        function add(){
            \add_options_page( $this->page_title, $this->menu_title, $this->capabilites, $this->page_slug, array( $this, 'settings_page' ) );
        }

        function  settings_page () {

                   
                    $opt = get_option('webdesignby_recaptcha');
                    
                    
                    ?>
                    <h1><?php echo __('reCaptcha Settings', 'webdesignby-recaptcha'); ?></h1>
                    <p>Generate a new site key and secret at:<br /><strong><a href="https://www.google.com/recaptcha/admin" target="_blank">https://www.google.com/recaptcha/admin</a></strong></p>
                    <?php
                    if( ! empty($this->message))
                        echo $this->message;
                    ?>
                    <form name="form" action="" method="post">
                    <?php echo wp_nonce_field('process'); ?>
                    <table class="form-table">
                        <tbody>
                            <tr>
                                <th><label for="webdesignby_recaptcha[g_site_key]"><?php echo __('Site Key', 'webdesignby-recaptcha'); ?>:</label></th>
                                <td><input name="webdesignby_recaptcha[g_site_key]" id="g_site_key" type="text" class="regular-text code" value="<?php echo trim($opt['g_site_key']); ?>" /></td>
                            </tr>
                            <tr>
                                <th><label for="webdesignby_recaptcha[g_secret_key]"><?php echo __('Secret Key', 'webdesignby-recaptcha'); ?>:</label></th>
                                <td><input name="webdesignby_recaptcha[g_secret_key]" id="g_secret_key" type="text" class="regular-text code" value="<?php echo trim($opt['g_secret_key']); ?>" /></td>
                            </tr>
                            <?php 
                            if( ! empty( $opt['g_secret_key'] ) && ! empty( $opt['g_site_key'] ) ){
                            ?>
                            <tr>
                                <th><?php echo __('reCAPTCHA Test', 'webdesignby-recaptcha'); ?></th>
                                <td>
                                     <div class="g-recaptcha" id="g-recaptcha1"></div>
                                    <script src='https://www.google.com/recaptcha/api.js?onload=onloadCallback&render=explicit' async defer></script>
                                    <p style="font-weight:bold;">Does everything look ok?</p>
                                    <p style="max-width:300px;font-style:italic;color:#666;">If you see an error such as <strong style="color:#ff0000;">Invalid site key</strong> or <strong style="color:#ff0000;">Invalid domain for site key</strong> you need to <a href="https://www.google.com/recaptcha/admin" target="_blank">regenerate your site key.</a></p>
                                </td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                    <p class="submit">
                        <input type="submit" name="submit" id="submit" class="button button-primary" value="<?php echo __('Save Changes', 'webdesignby-recaptcha'); ?>">
                    </p>
                    </form>
                    <?php
                    
            }

    }

}