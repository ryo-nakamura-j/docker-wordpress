<?php
class TpAcfHelper {
	var $postId;

	function __construct( $postId = "" ) {
		if ( isset($postId) && $postId != "" )
			$this->postId = $postId;
	}

	public function getField( $fieldName ) {
		return isset($this->postId) ? 
			get_field( $fieldName, $this->postId ) : get_field( $fieldName);
	}

	public function getFieldList( $fieldNameList ) {
		$rlt = array();
		foreach ( $fieldNameList as $name) {
			$rlt[ $name ] = isset($this->postId) ? 
				get_field( $name, $this->postId ) : get_field( $name );
		}
		return $rlt;
	}

	public function getSubFieldList( $fieldNameList ) {
		$rlt = array();
		foreach ( $fieldNameList as $name) {
			$rlt[ $name ] = get_sub_field( $name );
		}
		return $rlt;
	}

	public function getListOfKeyValue( $rowName, $keyField, $valueField ) {
		$rlt = array();
		while( isset($this->postId) ? 
			have_rows( $rowName, $this->postId) : have_rows( $rowName ) ) { 
			the_row();
        	$key = get_sub_field( $keyField );
        	$rlt[$key] = get_sub_field( $valueField ); 
    	}
    	return $rlt;
	}

	public function getValueMap( $valueNameList ) {
		$rlt = array();
		foreach( $valueNameList as $v ) {
			$rlt[$v] = get_sub_field($v);
		}
		return $rlt;
	}

	public function getValueMapList( $rowName, $valueNameList, $needPostId = false, $isFromGroup = false ) {
		$rlt = array();
		while( ( $needPostId && isset($this->postId) ) ? have_rows( $rowName, $this->postId ) : have_rows( $rowName ) ){
			the_row();
			array_push( $rlt, $this->getValueMap( $valueNameList ) );
		}
		if ( $isFromGroup && count($rlt) > 0 )
			return $rlt[0];
		else
			return $rlt;
	}

	public function getKeyValueWithLayout( $rowName, $keyName, $subRowName, $layoutCallbackMap ) {
		$rlt = array();
		while( isset($this->postId) ? 
			have_rows( $rowName, $this->postId ) : have_rows( $rowName ) ) {
			the_row();
			$key = get_sub_field( $keyName );
			while( have_rows( $subRowName ) ){
				the_row();
				$v = array();
				$layout = get_row_layout();
				foreach( $layoutCallbackMap as $layoutKey => $callBackObject ) {
					// Call different callbacks according to layout
					if ( $layout == $layoutKey ) {
						$func = $callBackObject['func'];
						$params = $callBackObject['params'];
						$v = call_user_func_array( array($this, $func), $params );
					}
				}
			}
			$rlt[$key] = $v;
		}
		return $rlt;
	}
}

class TpAcf {
	const HEADER_POST_ID = 'acf-header';
	const FOOTER_POST_ID = 'acf-footer';

	function __construct() { }

	public static function getCurHeaderPostId( $ext = null ) {
		if ( $ext != "" && $ext != null )
			return self::HEADER_POST_ID . '-' . $ext;
		else
			return self::HEADER_POST_ID;
	}

	public static function getCurFooterPostId( $ext = null ) {
		if ( $ext != "" && $ext != null )
			return self::FOOTER_POST_ID . '-' . $ext;
		else
			return self::FOOTER_POST_ID;
	}

	public function init() {
		// 1. customize ACF path
		add_filter('acf/settings/path', 'my_acf_settings_path');
		function my_acf_settings_path( $path ) {
		    // update path
		    $path = get_stylesheet_directory() . '/acf/';
		    // return
		    return $path;
		}

		// 2. customize ACF dir
		add_filter('acf/settings/dir', 'my_acf_settings_dir');
		function my_acf_settings_dir( $dir ) {
		    // update path
		    $dir = get_stylesheet_directory_uri() . '/acf/';
		    // return
		    return $dir;
		}

		// 3. Hide ACF field group menu item
		// add_filter('acf/settings/show_admin', '__return_false');

		// 4. Include ACF
		include_once( get_stylesheet_directory() . '/acf/acf.php' );

		// 5. Add ACF Option Pages
		if( function_exists('acf_add_options_page') ) {
		    acf_add_options_page( array(
		      'page_title' => 'ACF Header',
		      'post_id' => self::getCurHeaderPostId("")
		    ));
		    acf_add_options_page( array(
		      'page_title' => 'ACF Footer',
		      'post_id' => self::getCurFooterPostId("")
		    ));
		    $exts = tp_language_exts();
			foreach ( $exts as $ex ) {
				if ( $ex != "" && $ex != null ) {
				    acf_add_options_page( array(
				      'page_title' => 'ACF Header ' . strtoupper($ex),
				      'post_id' => self::getCurHeaderPostId($ex),
				    ));
				    acf_add_options_page( array(
				      'page_title' => 'ACF Footer ' . strtoupper($ex),
				      'post_id' => self::getCurFooterPostId($ex),
				    ));
				}
			}
		}
	}
}
?>