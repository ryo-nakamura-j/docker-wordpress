<?php

class WPNS_unlimittedUser
{
	function __construct(){

        add_action( 'admin_init'  , array( $this, 'mo_two_fa_unlimittedUser_ajax' ));
	}

	function mo_two_fa_unlimittedUser_ajax(){	 
        add_action( 'wp_ajax_wpns_two_fa_unlimittedUser', array($this,'wpns_two_fa_unlimittedUser') );
	}

		function wpns_two_fa_unlimittedUser(){		
			switch(sanitize_post($_POST['wpns_unlimittedUser_ajax']))
			{				
				case 'save':
					$this->wpns_handle_save();	break;					
			}   
		}
function wpns_handle_save()
{
	               
  			   		if ( !wp_verify_nonce($_POST['nonce'],'unlimittedUserNonce') ){
    			   			wp_send_json('ERROR');
    			   			return;
                        }
                        global $wp_roles;
		                if (!isset($wp_roles))
			             $wp_roles = new WP_Roles();
                        foreach($wp_roles->role_names as $id => $name) {
                        	update_option('mo2fa_'.$id, 0);
                        }
                        $enabledrole = sanitize_text_field($_POST['enabledrole']);
                         foreach($enabledrole as $role){
   							 update_option($role, 1);   						
  						}
  						update_option('mo2fa_author_login_url', 		sanitize_url( $_POST['mo2fa_author_login_url']));
  						update_option('mo2fa_subscriber_login_url',		sanitize_url($_POST['mo2fa_subscriber_login_url']));
  						update_option('mo2fa_contributor_login_url',	sanitize_url($_POST['mo2fa_contributor_login_url']));
  						update_option('mo2fa_editor_login_url',			sanitize_url($_POST['mo2fa_editor_login_url']));
  						update_option('mo2fa_administrator_login_url',	sanitize_url($_POST['mo2fa_administrator_login_url']));
                        wp_send_json('true');
                        return;
 }
}new WPNS_unlimittedUser();
?>