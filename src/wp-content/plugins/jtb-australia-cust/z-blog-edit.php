<?php

function body_message_print(){
	global $jtbau;
	if ( current_user_can('manage_network_plugins') ){
		echo plugins_url();
	}else{
		echo '<p>testing log</p>';
	}
    
}

?>
