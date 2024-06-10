<?php
/**
 * @package WordPress
 * @subpackage Default_Theme
 */

get_header();
if(in_category(158)){ //blog articles 
	do_action("print_post_template");
}else{
	do_action("print_post_reviews");
}

get_footer(); ?>