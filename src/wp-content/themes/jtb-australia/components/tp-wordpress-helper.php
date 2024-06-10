<?php
class TpWordpressHelper {

	public static function wp_get_menu_array($current_menu) {
		if ( !isset($current_menu) && $current_menu == "" )
			return array();
		// https://developer.wordpress.org/reference/functions/wp_get_nav_menu_items/
	    $array_menu = wp_get_nav_menu_items($current_menu);
	    $menu = array();
	    foreach ($array_menu as $m) {
	        if (empty($m->menu_item_parent)) {
	            $tmp = array();
	            $tmp['ID']      	=   $m->ID;
	            $tmp['title']       =   $m->title;
	            $tmp['url']         =   $m->url;
	            $tmp['children']    =   array();
			    foreach ($array_menu as $mS) {
			        if ($mS->menu_item_parent && $mS->menu_item_parent == $m->ID) {
			            $tmpS = array();
			            $tmpS['ID']      	=   $mS->ID;
			            $tmpS['title']       =   $mS->title;
			            $tmpS['url']         =   $mS->url;
			            array_push( $tmp['children'], $tmpS );
			        }
			    }
	            array_push( $menu, $tmp );
	        }
	    }
    	return $menu;
	}
}
?>