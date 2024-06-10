<?php

global $post;

if(get_post_meta($post->ID, "_hide_from_search", true)!="1"){
	if (($post->post_parent == 3609)||($post->post_parent == 3795)||($post->post_parent == 3797)) { //escort, korea, hawaii
	   //do_shortcode('[contact-form-7 id="5190" title="Contact Escorted Tours"]');
	   echo apply_filters( 'the_content','[contact-form-7 id="5190" title="Contact Escorted Tours"]');
	}else if (($post->post_parent == 3720)||($post->post_parent == 3718)||($post->post_parent == 3722)) {//drive, ini, special int
		echo apply_filters('the_content', '[contact-form-7 id="5215" title="Contact Drive Independent"]');
	}else if (($post->post_parent == 3724)||($post->post_parent == 3716)) {
		echo apply_filters('the_content', '[contact-form-7 id="5200" title="Contact Guided Tours"]');
	}else if (($post->post_parent == 24893)|| is_page(24893)) {//cruise 
		echo apply_filters('the_content', '[contact-form-7 id="25092" title="Contact Cruise"]');
	}else if ( ($post->post_parent == 33966)||  ($post->post_parent == 33865)||  is_page(33951) ) {//VT - virtual tour  
		echo apply_filters('the_content', '[contact-form-7 id="33954" title="Contact Virtual Tours"]');
	}else if (($post->post_parent == 25334)|| is_page(25334)||($post->post_parent == 28818)|| is_page(28818)) {//kathy tours - quilt okina
		echo apply_filters('the_content', '[contact-form-7 id="25353" title="Tours MICE - clone of escort default"]');
	}else{//else, escorted
		echo apply_filters('the_content', '[contact-form-7 id="5190" title="Contact Escorted Tours"]');
	}
}else{
	echo '<div class="col-sm-12"><p class="red-message">This product is currently unavailable. Please see our other offerings and check back at a later date.</p></div>';
}

?>