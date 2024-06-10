<?php

class Flamingo_Inbound_Message {

	const post_type = 'flamingo_inbound';
	const spam_status = 'flamingo-spam';
	const channel_taxonomy = 'flamingo_inbound_channel';

	public static $found_items = 0;

	public $id;
	public $channel;
	public $date;
	public $subject;
	public $from;
	public $from_name;
	public $from_email;
	public $fields;
	public $meta;
	public $akismet;
	public $spam;
	public $consent;

	public static function register_post_type() {
		register_post_type( self::post_type, array(
			'labels' => array(
				'name' => __( 'Flamingo Inbound Messages', 'flamingo' ),
				'singular_name' => __( 'Flamingo Inbound Message', 'flamingo' ),
			),
			'rewrite' => false,
			'query_var' => false,
		) );

		register_post_status( self::spam_status, array(
			'label' => __( 'Spam', 'flamingo' ),
			'public' => false,
			'exclude_from_search' => true,
			'show_in_admin_all_list' => false,
			'show_in_admin_status_list' => true,
		) );

		register_taxonomy( self::channel_taxonomy, self::post_type, array(
			'labels' => array(
				'name' => __( 'Flamingo Inbound Message Channels', 'flamingo' ),
				'singular_name' => __( 'Flamingo Inbound Message Channel', 'flamingo' ),
			),
			'public' => false,
			'hierarchical' => true,
			'rewrite' => false,
			'query_var' => false,
		) );
	}

	public static function find( $args = '' ) {
		$defaults = array(
			'posts_per_page' => 10,
			'offset' => 0,
			'orderby' => 'ID',
			'order' => 'ASC',
			'meta_key' => '',
			'meta_value' => '',
			'post_status' => 'any',
			'tax_query' => array(),
			'channel' => '',
			'channel_id' => 0,
		);

		$args = wp_parse_args( $args, $defaults );

		$args['post_type'] = self::post_type;

		if ( ! empty( $args['channel_id'] ) ) {
			$args['tax_query'][] = array(
				'taxonomy' => self::channel_taxonomy,
				'terms' => absint( $args['channel_id'] ),
				'field' => 'term_id',
			);
		}

		if ( ! empty( $args['channel'] ) ) {
			$args['tax_query'][] = array(
				'taxonomy' => self::channel_taxonomy,
				'terms' => $args['channel'],
				'field' => 'slug',
			);
		}

		$q = new WP_Query();
		$posts = $q->query( $args );

		self::$found_items = $q->found_posts;

		$objs = array();

		foreach ( (array) $posts as $post ) {
			$objs[] = new self( $post );
		}

		return $objs;
	}

	public static function count( $args = '' ) {
		$args = wp_parse_args( $args, array(
			'offset' => 0,
			'channel' => '',
			'channel_id' => 0,
			'post_status' => 'publish',
		) );

		self::find( $args );

		return absint( self::$found_items );
	}

	public static function add( $args = '' ) {
		$defaults = array(
			'channel' => '',
			'subject' => '',
			'from' => '',
			'from_name' => '',
			'from_email' => '',
			'fields' => array(),
			'meta' => array(),
			'akismet' => array(),
			'spam' => false,
			'consent' => array(),
		);

		$args = apply_filters( 'flamingo_add_inbound',
			wp_parse_args( $args, $defaults ) );

		$obj = new self();

		$obj->channel = $args['channel'];
		$obj->subject = $args['subject'];
		$obj->from = $args['from'];
		$obj->from_name = $args['from_name'];
		$obj->from_email = $args['from_email'];
		$obj->fields = $args['fields'];
		$obj->meta = $args['meta'];
		$obj->akismet = $args['akismet'];
		$obj->consent = $args['consent'];

		if ( $args['spam'] ) {
			$obj->spam = true;
		} else {
			$obj->spam = isset( $obj->akismet['spam'] ) && $obj->akismet['spam'];
		}

		$obj->save();

		return $obj;
	}

	public function __construct( $post = null ) {
		if ( ! empty( $post ) && ( $post = get_post( $post ) ) ) {
			$this->id = $post->ID;

			$this->date = get_the_time(
				__( 'Y/m/d g:i:s A', 'flamingo' ), $this->id );
			$this->subject = get_post_meta( $post->ID, '_subject', true );
			$this->from = get_post_meta( $post->ID, '_from', true );
			$this->from_name = get_post_meta( $post->ID, '_from_name', true );
			$this->from_email = get_post_meta( $post->ID, '_from_email', true );
			$this->fields = get_post_meta( $post->ID, '_fields', true );

			if ( ! empty( $this->fields ) ) {
				foreach ( (array) $this->fields as $key => $value ) {
					$meta_key = sanitize_key( '_field_' . $key );

					if ( metadata_exists( 'post', $post->ID, $meta_key ) ) {
						$value = get_post_meta( $post->ID, $meta_key, true );
						$this->fields[$key] = $value;
					}
				}
			}

			$this->meta = get_post_meta( $post->ID, '_meta', true );
			$this->akismet = get_post_meta( $post->ID, '_akismet', true );
			$this->consent = get_post_meta( $post->ID, '_consent', true );

			$terms = wp_get_object_terms( $this->id, self::channel_taxonomy );

			if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
				$this->channel = $terms[0]->slug;
			}

			if ( self::spam_status == get_post_status( $post ) ) {
				$this->spam = true;
			} else {
				$this->spam = isset( $this->akismet['spam'] ) && $this->akismet['spam'];
			}
		}
	}


	public function save() {
		if ( ! empty( $this->subject ) ) {
			$post_title = $this->subject;
		} else {
			$post_title = __( '(No Title)', 'flamingo' );
		}

		$fields = flamingo_array_flatten( $this->fields );
		$fields = array_filter( array_map( 'trim', $fields ) );

		$post_content = implode( "\n", $fields );

		$post_status = $this->spam ? self::spam_status : 'publish';

		$postarr = array(
			'ID' => absint( $this->id ),
			'post_type' => self::post_type,
			'post_status' => $post_status,
			'post_title' => $post_title,
			'post_content' => $post_content,
		);

		$post_id = false;// wp_insert_post( $postarr );

		if ( !$post_id ) {
			$this->id = $post_id;
			update_post_meta( $post_id, '_subject', $this->subject );
			update_post_meta( $post_id, '_from', $this->from );
			update_post_meta( $post_id, '_from_name', $this->from_name );
			update_post_meta( $post_id, '_from_email', $this->from_email );

			$data_brochure = "";
			$arraydata=[];
			foreach ( $this->fields as $key => $value ) {
				$meta_key = sanitize_key( '_field_' . $key );
				update_post_meta( $post_id, $meta_key, $value );
				$this->fields[$key] = null;
				$data_brochure .= $meta_key . " - " . $value . "\n\n";
				$arraydata[$meta_key] = $value; 
			}


			update_post_meta( $post_id, '_fields', $this->fields );
			update_post_meta( $post_id, '_meta', $this->meta );
			update_post_meta( $post_id, '_akismet', $this->akismet );
			update_post_meta( $post_id, '_consent', $this->consent );


			foreach ( $this->meta as $key => $value ) {
 				$meta_key = sanitize_key( '_field_' . $key );
				$arraydata[$meta_key] = $value; 
				$data_brochure .= $meta_key . " - " . $value . "\n\n";
			}




			if ( term_exists( $this->channel, self::channel_taxonomy ) ) {
				wp_set_object_terms( $this->id, $this->channel,
					self::channel_taxonomy );
			}

if( ($arraydata["_field_post_id"]=="23199") ||  ($arraydata["_field_post_id"]=="4608") ){








//wp_mail("ben@pushka.com","test1234","DATA: " . $post_content . " --- " . $data_brochure . " ---  request updates " . $arraydata["_field_request-updates7"][0]     );
$hear_about = "";
if( ($arraydata["_field_hear-about7"] != "") && ($arraydata["_field_hear-about7"] != false) && ($arraydata["_field_hear-about7"] != null ) ){
	$hear_about = $arraydata["_field_hear-about7"];
}
$hear_about = str_replace(" ","%20",$hear_about);

$url = "https://docs.google.com/forms/d/1uqhBFCQu1ZjXVeW3hhNgoe8ZIN0-0b_Yne4fqTg6UPY/formResponse";

// hear about us 
     // url: "https://docs.google.com/a/jtbap.com/forms/d/1JH0gL0rQUTRoLDNqUMF5zVOJHHIN7rnakVJKg8paaUg/formResponse",
      


$stream_options = array(
    'http' => array(
        'method'  => 'GET',
        'header'  => 'Content-Type: text/html' . "\r\n",
        'content' => "" 
    ));
$context  = stream_context_create($stream_options);
$response = file_get_contents($url."?entry.308663305=".$hear_about, null, $context);

if($arraydata["_field_request-updates7"][0] == "Yes"){
$name = ""; 
if( ($arraydata["_field_title7"] != "") && ($arraydata["_field_title7"] != false) && ($arraydata["_field_title7"] != null ) ){
	$name .= $arraydata["_field_title7"]." ";
}
if( ($arraydata["_field_first-name7"] != "") && ($arraydata["_field_first-name7"] != false) && ($arraydata["_field_first-name7"] != null ) ){
	$name .= $arraydata["_field_first-name7"]." ";
}
if( ($arraydata["_field_surname7"] != "") && ($arraydata["_field_surname7"] != false) && ($arraydata["_field_surname7"] != null ) ){
	$name .= $arraydata["_field_surname7"] ;
}
$name = str_replace(" ","%20",$name);
$state = ""; 
if( ($arraydata["_field_your-recipient7"] != "") && ($arraydata["_field_your-recipient7"] != false) && ($arraydata["_field_your-recipient7"] != null ) ){
	$state = $arraydata["_field_your-recipient7"];
	if( ($state == "melres+vic@nx.jtbtravel.com.au") || ($state == "melres+agent@nx.jtbtravel.com.au") ){
		$state="VIC";
	}else{
		$state=strtoupper(substr($state,7,3));//sydres+NSW@
	}
}
$email = ""; 
if( ($arraydata["_field_email7"] != "") && ($arraydata["_field_email7"] != false) && ($arraydata["_field_email7"] != null ) ){
	$email = $arraydata["_field_email7"];
}
$email = str_replace(" ","%20",$email); $email = str_replace("&","%26",$email);
$email = str_replace("+","%2B",$email); $email = str_replace("?","%3F",$email);
$email = str_replace("@","%40",$email); $email = str_replace("$","%24",$email);
$url =  "https://docs.google.com/forms/d/1SRMQbdLim8-OPLR9LHOWfXZYtmv-Z0ms8Xdd0XQzf1k/formResponse";

// subscribe 
      //url: "https://docs.google.com/a/jtbap.com/forms/d/1PUqUTddB5SS3TMr2kbjhZok0FQfTkJdvIdATV2sTIcQ/formResponse",
          // url: "https://docs.google.com/a/jtbap.com/forms/d/1PUqUTddB5SS3TMr2kbjhZok0FQfTkJdvIdATV2sTIcQ/formResponse",
      


$stream_options = array(
    'http' => array(
        'method'  => 'GET',
        'header'  => 'Content-Type: text/html' . "\r\n",
        'content' => "" 
    ));
$context  = stream_context_create($stream_options);
$response = file_get_contents($url."?entry.1493792574=".$name."&entry.976961624=".$state."&entry.1411460999=".$email, null, $context);
}








}

	}

	return  1;//$post_id;
}
//end


public function do_post_request($url, $data, $optional_headers = null){
  $params = array('http' => array(
              'method' => 'POST',
              'content' => $data,
              'dataType' => 'xml'
            ));
  if ($optional_headers !== null) {
    $params['http']['header'] = $optional_headers;
  }
  $ctx = stream_context_create($params);
  $fp = @fopen($url, 'rb', false, $ctx);
  if (!$fp) {
    return false;//throw new Exception("Problem with $url, $php_errormsg");
  }
  $response = @stream_get_contents($fp);
  if ($response === false) {
    return false;//throw new Exception("Problem reading data from $url, $php_errormsg");
  }
  return $response;
}



	public function trash() {
		if ( empty( $this->id ) ) {
			return;
		}

		if ( ! EMPTY_TRASH_DAYS ) {
			return $this->delete();
		}

		$post = wp_trash_post( $this->id );

		return (bool) $post;
	}

	public function untrash() {
		if ( empty( $this->id ) ) {
			return;
		}

		$post = wp_untrash_post( $this->id );

		return (bool) $post;
	}

	public function delete() {
		if ( empty( $this->id ) ) {
			return;
		}

		if ( $post = wp_delete_post( $this->id, true ) ) {
			$this->id = 0;
		}

		return (bool) $post;
	}

	public function spam() {
		if ( $this->spam ) {
			return;
		}

		$this->akismet_submit_spam();
		$this->spam = true;

		return $this->save();
	}

	public function akismet_submit_spam() {
		if ( empty( $this->id ) || empty( $this->akismet ) ) {
			return;
		}

		if ( isset( $this->akismet['spam'] ) && $this->akismet['spam'] ) {
			return;
		}

		if ( empty( $this->akismet['comment'] ) ) {
			return;
		}

		if ( flamingo_akismet_submit_spam( $this->akismet['comment'] ) ) {
			$this->akismet['spam'] = true;
			update_post_meta( $this->id, '_akismet', $this->akismet );
			return true;
		}
	}

	public function unspam() {
		if ( ! $this->spam ) {
			return;
		}

		$this->akismet_submit_ham();
		$this->spam = false;

		return $this->save();
	}

	public function akismet_submit_ham() {
		if ( empty( $this->id ) || empty( $this->akismet ) ) {
			return;
		}

		if ( isset( $this->akismet['spam'] ) && ! $this->akismet['spam'] ) {
			return;
		}

		if ( empty( $this->akismet['comment'] ) ) {
			return;
		}

		if ( flamingo_akismet_submit_ham( $this->akismet['comment'] ) ) {
			$this->akismet['spam'] = false;
			update_post_meta( $this->id, '_akismet', $this->akismet );
			return true;
		}
	}
}
