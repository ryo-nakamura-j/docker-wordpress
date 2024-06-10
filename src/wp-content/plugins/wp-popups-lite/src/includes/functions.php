<?php

use Hummingbird\WP_Hummingbird;

/**
 * Retrieve a value from options
 *
 * @param $key
 * @param bool $default
 * @param string $option
 *
 * @return bool|mixed
 */
function wppopups_setting( $key, $default = false, $option = 'wppopups_settings' ) {

	$key     = wppopups_sanitize_key( $key );
	$options = get_option( $option, false );
	$value   = is_array( $options ) && isset( $options[ $key ] ) && ( $options[ $key ] === '0' || ! empty( $options[ $key ] ) ) ? $options[ $key ] : $default;

	return $value;
}

/**
 * Sanitize key, primarily used for looking up options.
 *
 * @param string $key
 *
 * @return string
 */
function wppopups_sanitize_key( $key = '' ) {

	return preg_replace( '/[^a-zA-Z0-9_\-\.\:\/]/', '', $key );
}


/**
 * Check if popup provided contains the specified field type.
 *
 * @since 1.0.5
 *
 * @param array|string $type
 * @param array|object $popup
 * @param bool         $multiple
 *
 * @return bool
 */
function wppopups_has_field_type( $type, $popup, $multiple = false ) {

	$popup_data = '';
	$field     = false;
	$type      = (array) $type;

	if ( $multiple ) {
		foreach ( $popup as $single_popup ) {
			$field = wppopups_has_field_type( $type, $single_popup );
			if ( $field ) {
				break;
			}
		}

		return $field;
	}

	if ( is_object( $popup ) && ! empty( $popup->post_content ) ) {
		$popup_data = wppopups_decode( $popup->post_content );
	} elseif ( is_array( $popup ) ) {
		$popup_data = $popup;
	}

	if ( empty( $popup_data['fields'] ) ) {
		return false;
	}

	foreach ( $popup_data['fields'] as $single_field ) {
		if ( in_array( $single_field['type'], $type, true ) ) {
			$field = true;
			break;
		}
	}

	return $field;
}

/**
 * Check if popup provided contains a field which a specific setting.
 *
 * @since 1.4.5
 *
 * @param string $setting
 * @param object|array  $popup
 * @param bool   $multiple
 *
 * @return bool
 */
function wppopups_has_field_setting( $setting, $popup, $multiple = false ) {

	$popup_data = '';
	$field     = false;

	if ( $multiple ) {
		foreach ( $popup as $single_popup ) {
			$field = wppopups_has_field_setting( $setting, $single_popup );
			if ( $field ) {
				break;
			}
		}

		return $field;
	}

	if ( is_object( $popup ) && ! empty( $popup->post_content ) ) {
		$popup_data = wppopups_decode( $popup->post_content );
	} elseif ( is_array( $popup ) ) {
		$popup_data = $popup;
	}

	if ( empty( $popup_data['fields'] ) ) {
		return false;
	}

	foreach ( $popup_data['fields'] as $single_field ) {

		if ( ! empty( $single_field[ $setting ] ) ) {
			$field = true;
			break;
		}
	}

	return $field;
}


/**
 * Perpopups json_decode and unslash.
 *
 * @param string $data
 *
 * @return array|bool
 * @since 2.0.0
 *
 */
function wppopups_decode( $data ) {

	if ( ! $data || empty( $data ) || is_array( $data ) ) {
		return false;
	}

	return wp_unslash( json_decode( $data, true ) );
}

/**
 * Check permissions for currently logged in user.
 *
 * @return bool
 * @since 2.0.0
 *
 */
function wppopups_current_user_can() {

	$capability = wppopups_get_manage_capability();

	return apply_filters( 'wppopups/current_user_can', current_user_can( $capability ), $capability );
}

/**
 * Get the default capability to manage everything for WPPopups.
 *
 * @return string
 * @since 2.0.0
 *
 */
function wppopups_get_manage_capability() {
	return apply_filters( 'wppopups/manage_capability', 'manage_options' );
}

/**
 * Return current url
 *
 * @return string
 */
function wppopups_get_current_url() {
	return ( isset( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] == 'on' ? 'https' : 'http' ) . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
}

/**
 * Perform json_encode and wp_slash.
 *
 * @param mixed $data *
 *
 * @return string
 */
function wppopups_encode( $data = false ) {

	if ( empty( $data ) ) {
		return false;
	}

	return wp_slash( wp_json_encode( $data ) );
}

/**
 * Debug mode bool.
 *
 * @return bool
 */
function wppopups_debug() {

	$debug = false;

	if ( ( defined( 'WPPOPUPS_DEBUG' ) && true === WPPOPUPS_DEBUG ) && is_super_admin() ) {
		$debug = true;
	}

	$debug_option = get_option( 'wppopups_debug' );

	if ( $debug_option ) {
		$current_user = wp_get_current_user();
		if ( $current_user->user_login === $debug_option ) {
			$debug = true;
		}
	}

	return apply_filters( 'wppopups_debug', $debug );
}

/**
 * Sanitizes string of CSS classes.
 *
 * @param array|string $classes
 * @param bool $convert True will convert strings to array and vice versa.
 *
 * @return string|array
 * @since 2.0.0
 *
 */
function wppopups_sanitize_classes( $classes, $convert = false ) {

	$array = is_array( $classes );
	$css   = [];

	if ( ! empty( $classes ) ) {
		if ( ! $array ) {
			$classes = explode( ' ', trim( $classes ) );
		}
		foreach ( $classes as $class ) {
			if ( ! empty( $class ) ) {

				if ( strpos( $class, ' ' ) !== false ) {
					$css[] = wppopups_sanitize_classes( $class, false );
				} else {
					$css[] = sanitize_html_class( $class );
				}
			}
		}
	}
	if ( $array ) {
		return $convert ? implode( ' ', $css ) : $css;
	} else {
		return $convert ? $css : implode( ' ', $css );
	}
}


/**
 * Sanitizes hex color.
 *
 * @param string $color
 *
 * @return string
 */
function wppopups_sanitize_hex_color( $color ) {
	$color = sanitize_hex_color( $color );

	if ( empty( $color ) ) {
		return '';
	}

	return $color;
}


/**
 * Sanitize error message, primarily used during popup frontend output.
 *
 * @since 1.3.7
 *
 * @param string $error
 *
 * @return string
 */
function wppopups_sanitize_error( $error = '' ) {

	$allow = array(
		'a'      => array(
			'href'  => array(),
			'title' => array(),
		),
		'br'     => array(),
		'em'     => array(),
		'strong' => array(),
		'p'      => array(),
	);

	return wp_kses( $error, $allow );
}

/**
 * Sanitize a string, that can be a multiline.
 *
 * @uses wppopups_sanitize_text_deeply()
 *
 * @since 1.4.1
 *
 * @param string $string String to deeply sanitize.
 *
 * @return string Sanitized string, or empty string if not a string provided.
 */
function wppopups_sanitize_textarea_field( $string ) {

	return wppopups_sanitize_text_deeply( $string, true );
}

/**
 * Deeply sanitize the string, preserve newlines if needed.
 * Prevent maliciously prepared strings from containing HTML tags.
 *
 * @since 1.6.0
 *
 * @param string $string        String to deeply sanitize.
 * @param bool   $keep_newlines Whether to keep newlines. Default: false.
 *
 * @return string Sanitized string, or empty string if not a string provided.
 */
function wppopups_sanitize_text_deeply( $string, $keep_newlines = false ) {

	if ( is_object( $string ) || is_array( $string ) ) {
		return '';
	}

	$string        = (string) $string;
	$keep_newlines = (bool) $keep_newlines;

	$new_value = _sanitize_text_fields( $string, $keep_newlines );

	if ( strlen( $new_value ) !== strlen( $string ) ) {
		$new_value = wppopups_sanitize_text_deeply( $new_value, $keep_newlines );
	}

	return $new_value;
}


/**
 * Sanitizes hex color.
 *
 * @param string $color
 *
 * @return string
 */
function wppopups_hex2rgba( $color = '', $opacity = false ) {

	if ( empty( $color ) ) {
		return '';
	}

	if ( strpos( $color, '#' ) === false ) {
		return $color;
	}

	$rgb   = [];
	$color = ltrim( $color, '#' );

	switch ( strlen( $color ) ) {
		case 6:
			$rgb = array_map( 'hexdec', str_split( $color, 2 ) );
			break;

		case 3:
			$acolor = str_split( $color, 1 );

			$rgb[] = hexdec( str_repeat( $acolor[0], 2 ) );
			$rgb[] = hexdec( str_repeat( $acolor[1], 2 ) );
			$rgb[] = hexdec( str_repeat( $acolor[2], 2 ) );
			break;

		default:
			return $color;
	}

	if ( $opacity ) {
		$opacity = absint( $opacity ) > 1 ? 1.0 : round( abs( $opacity ), 1 );

		$rgb_output = 'rgba(' . implode( ',', $rgb ) . ', ' . $opacity . ')';
	} else {
		$rgb_output = 'rgb(' . implode( ',', $rgb ) . ')';
	}

	return apply_filters( 'wppopups/hex2rgba', $rgb_output, $color, $opacity );
}

/**
 * Return welcome popup text
 *
 * @return string
 */
function wppopups_welcome_text() {
	return esc_html( __( 'Welcome to popups plugin', 'wp-popups-lite' ) );
}


/**
 * Helper function to display debug data.
 *
 * @param mixed $data
 * @param bool $echo
 *
 * @return string
 */
function wppopups_debug_data( $data, $echo = true ) {

	if ( wppopups_debug() ) {

		$output = '<textarea style="background:#fff;margin: 20px 0;width:100%;height:500px;font-size:12px;font-family: Consolas,Monaco,monospace;direction: ltr;unicode-bidi: embed;line-height: 1.4;padding: 4px 6px 1px;" readonly>';

		$output .= "=================== WPPOPUPS DEBUG ===================\n\n";

		if ( is_array( $data ) || is_object( $data ) ) {
			$output .= ( print_r( $data, true ) );
		} else {
			$output .= $data;
		}

		$output .= '</textarea>';

		if ( $echo ) {
			echo $output;
		} else {
			return $output;
		}
	}
}

/**
 * Retrieve site post types
 * @return array
 */
function wppopups_get_post_types() {

	// get all custom post types
	$post_types = get_post_types();

	$includes = apply_filters( 'wppopups_include_post_types', [] ); // phpcs:ignore
	$excludes = apply_filters( 'wppopups_exclude_post_types', [
		'spucpt',
		'wppopups',
		'wppopups_log',
		'geot_cpt',
		'geotr_cpt',
		'geotl_cpt',
		'acf',
		'revision',
		'nav_menu_item',
		'custom_css',
		'customize_changeset',
		'oembed_cache',
		'wpvqgr_quiz_trivia',
		'wpvqgr_quiz_perso',
		'wpvqgr_user',
		'popup',
		'popup_theme',
		'page_rating',
		'sa_slider',
		'faq',
		'opanda-item',
		'amn_smtp',
	] ); // phpcs:ignore

	if ( is_array( $includes ) ) {
		foreach ( $includes as $p ) {
			if ( post_type_exists( $p ) ) {
				$post_types[ $p ] = $p;
			}
		}
	}
	if ( is_array( $excludes ) ) {
		foreach ( $excludes as $p ) {
			unset( $post_types[ $p ] );
		}
	}

	return apply_filters( 'wppopups_get_post_types', $post_types );

}


/**
 * Retrieve site taxonomies with his terms
 * @return array
 */
function wppopups_get_taxonomies() {
	// get all custom post types
	$post_types = get_post_types();

	$includes = apply_filters( 'wppopups_include_taxonomies', [] );
	$excludes = apply_filters( 'wppopups_exclude_taxonomies', [ 'wppopups_log_type', 'nav_menu' ] );

	if ( $post_types ) {
		foreach ( $post_types as $post_type ) {
			$post_type_object = get_post_type_object( $post_type );
			$taxonomies       = get_object_taxonomies( $post_type );


			if ( is_array( $includes ) ) {
				foreach ( $includes as $tax ) {
					if ( taxonomy_exists( $tax ) ) {
						$taxonomies[ $tax ] = $tax;
					}
				}
			}

			if ( is_array( $excludes ) ) {
				foreach ( $excludes as $tax ) {
					unset( $taxonomies[ $tax ] );
				}
			}


			if ( $taxonomies ) {
				foreach ( $taxonomies as $taxonomy ) {

					$terms = get_terms( $taxonomy, [ 'hide_empty' => true ] );

					if ( $terms ) {
						foreach ( $terms as $term ) {
							$choices[ $post_type_object->label . ': ' . $taxonomy ][ $term->term_id ] = $term->name;
						}
					}
				}
			}
		}
	}


	return apply_filters( 'wppopups_get_taxonomies', $choices );
}


/**
 * Retrieve browsers
 * @return array
 */
function wppopups_get_browsers() {

	$browsers = [
		Browser::BROWSER_OPERA        => Browser::BROWSER_OPERA,
		Browser::BROWSER_WEBTV        => Browser::BROWSER_WEBTV,
		Browser::BROWSER_NETPOSITIVE  => Browser::BROWSER_NETPOSITIVE,
		Browser::BROWSER_EDGE         => Browser::BROWSER_EDGE,
		Browser::BROWSER_IE           => Browser::BROWSER_IE,
		Browser::BROWSER_POCKET_IE    => Browser::BROWSER_POCKET_IE,
		Browser::BROWSER_GALEON       => Browser::BROWSER_GALEON,
		Browser::BROWSER_KONQUEROR    => Browser::BROWSER_KONQUEROR,
		Browser::BROWSER_ICAB         => Browser::BROWSER_ICAB,
		Browser::BROWSER_OMNIWEB      => Browser::BROWSER_OMNIWEB,
		Browser::BROWSER_PHOENIX      => Browser::BROWSER_PHOENIX,
		Browser::BROWSER_FIREBIRD     => Browser::BROWSER_FIREBIRD,
		Browser::BROWSER_UCBROWSER    => Browser::BROWSER_UCBROWSER,
		Browser::BROWSER_FIREFOX      => Browser::BROWSER_FIREFOX,
		Browser::BROWSER_MOZILLA      => Browser::BROWSER_MOZILLA,
		Browser::BROWSER_PALEMOON     => Browser::BROWSER_PALEMOON,
		Browser::BROWSER_CURL         => Browser::BROWSER_CURL,
		Browser::BROWSER_WGET         => Browser::BROWSER_WGET,
		Browser::BROWSER_AMAYA        => Browser::BROWSER_AMAYA,
		Browser::BROWSER_LYNX         => Browser::BROWSER_LYNX,
		Browser::BROWSER_SAFARI       => Browser::BROWSER_SAFARI,
		Browser::BROWSER_PLAYSTATION  => Browser::BROWSER_PLAYSTATION,
		Browser::BROWSER_IPHONE       => Browser::BROWSER_IPHONE,
		Browser::BROWSER_IPOD         => Browser::BROWSER_IPOD,
		Browser::BROWSER_ANDROID      => Browser::BROWSER_ANDROID,
		Browser::BROWSER_CHROME       => Browser::BROWSER_CHROME,
		Browser::BROWSER_GOOGLEBOT    => Browser::BROWSER_GOOGLEBOT,
		Browser::BROWSER_SLURP        => Browser::BROWSER_SLURP,
		Browser::BROWSER_W3CVALIDATOR => Browser::BROWSER_W3CVALIDATOR,
		Browser::BROWSER_BLACKBERRY   => Browser::BROWSER_BLACKBERRY,
	];

	return apply_filters( 'wppopups_get_browsers', $browsers );
}



/**
 * Build and return either a taxonomy or post type object that is
 * nested to accommodate any hierarchy.
 *
 * @since 1.3.9
 * @since 1.5.0 Return array only. Empty array of no data.
 *
 * @param array $args Object arguments to pass to data retrieval function.
 * @param bool  $flat Preserve hierarchy or not. False by default - preserve it.
 *
 * @return array
 */
function wppopups_get_hierarchical_object( $args = array(), $flat = false ) {

	if ( empty( $args['taxonomy'] ) && empty( $args['post_type'] ) ) {
		return array();
	}

	$children   = array();
	$parents    = array();
	$ref_parent = '';
	$ref_name   = '';

	if ( ! empty( $args['post_type'] ) ) {

		$defaults   = array(
			'posts_per_page' => - 1,
			'orderby'        => 'title',
			'order'          => 'ASC',
		);
		$args       = wp_parse_args( $args, $defaults );
		$items      = get_posts( $args );
		$ref_parent = 'post_parent';
		$ref_id     = 'ID';
		$ref_name   = 'post_title';

	} elseif ( ! empty( $args['taxonomy'] ) ) {

		$defaults   = array(
			'hide_empty' => false,
		);
		$args       = wp_parse_args( $args, $defaults );
		$items      = get_terms( $args );
		$ref_parent = 'parent';
		$ref_id     = 'term_id';
		$ref_name   = 'name';
	}

	if ( empty( $items ) || is_wp_error( $items ) ) {
		return array();
	}

	foreach ( $items as $item ) {
		if ( $item->{$ref_parent} ) {
			$children[ $item->{$ref_id} ]     = $item;
			$children[ $item->{$ref_id} ]->ID = (int) $item->{$ref_id};
		} else {
			$parents[ $item->{$ref_id} ]     = $item;
			$parents[ $item->{$ref_id} ]->ID = (int) $item->{$ref_id};
		}
	}

	$children_count = count( $children );
	while ( $children_count >= 1 ) {
		foreach ( $children as $child ) {
			_wppopups_get_hierarchical_object_search( $child, $parents, $children, $ref_parent );
			// $children is modified by reference, so we need to recount to make sure we met the limits.
			$children_count = count( $children );
		}
	}

	if ( $flat ) {
		$parents_flat = array();
		_wppopups_get_hierarchical_object_flatten( $parents, $parents_flat, $ref_name );

		return $parents_flat;
	}

	return $parents;
}

/**
 * Search a given array and find the parent of the provided object.
 *
 * @since 1.3.9
 *
 * @param object $child      Current child.
 * @param array  $parents    Parents list.
 * @param array  $children   Children list.
 * @param string $ref_parent Parent reference.
 */
function _wppopups_get_hierarchical_object_search( $child, &$parents, &$children, $ref_parent ) {

	foreach ( $parents as $id => $parent ) {

		if ( $parent->ID === $child->{$ref_parent} ) {

			if ( empty( $parent->children ) ) {
				$parents[ $id ]->children = array(
					$child->ID => $child,
				);
			} else {
				$parents[ $id ]->children[ $child->ID ] = $child;
			}

			unset( $children[ $child->ID ] );

		} elseif ( ! empty( $parent->children ) && is_array( $parent->children ) ) {

			_wppopups_get_hierarchical_object_search( $child, $parent->children, $children, $ref_parent );
		}
	}
}

/**
 * Flatten a hierarchical object.
 *
 * @since 1.3.9
 *
 * @param array  $array    Array to process.
 * @param array  $output   Processed output.
 * @param string $ref_name Name reference.
 * @param int    $level    Nesting level.
 */
function _wppopups_get_hierarchical_object_flatten( $array, &$output, $ref_name = 'name', $level = 0 ) {

	foreach ( $array as $key => $item ) {

		$indicator           = apply_filters( 'wppopups_hierarchical_object_indicator', '&mdash;' );
		$item->{$ref_name}   = str_repeat( $indicator, $level ) . ' ' . $item->{$ref_name};
		$item->depth         = $level + 1;
		$output[ $item->ID ] = $item;

		if ( ! empty( $item->children ) ) {

			_wppopups_get_hierarchical_object_flatten( $item->children, $output, $ref_name, $level + 1 );
			unset( $output[ $item->ID ]->children );
		}
	}
}


/**
 * Return field choice properties for field configured with dynamic choices.
 *
 * @since 1.4.5
 *
 * @param array $field     Field settings.
 * @param int   $popup_id   Popup ID.
 * @param array $popup_data Popup data and settings.
 *
 * @return false|array
 */
function wppopups_get_field_dynamic_choices( $field, $popup_id, $popup_data = array() ) {

	if ( empty( $field['dynamic_choices'] ) ) {
		return false;
	}

	$choices = array();

	if ( 'post_type' === $field['dynamic_choices'] ) {

		if ( empty( $field['dynamic_post_type'] ) ) {
			return false;
		}

		$posts = wppopups_get_hierarchical_object(
			apply_filters(
				'wppopups_dynamic_choice_post_type_args',
				array(
					'post_type'      => $field['dynamic_post_type'],
					'posts_per_page' => -1,
					'orderby'        => 'title',
					'order'          => 'ASC',
				),
				$field,
				$popup_id
			),
			true
		);

		foreach ( $posts as $post ) {
			$choices[] = array(
				'value' => $post->ID,
				'label' => $post->post_title,
				'depth' => isset( $post->depth ) ? absint( $post->depth ) : 1,
			);
		}
	} elseif ( 'taxonomy' === $field['dynamic_choices'] ) {

		if ( empty( $field['dynamic_taxonomy'] ) ) {
			return false;
		}

		$terms = wppopups_get_hierarchical_object(
			apply_filters(
				'wppopups_dynamic_choice_taxonomy_args',
				array(
					'taxonomy'   => $field['dynamic_taxonomy'],
					'hide_empty' => false,
				),
				$field,
				$popup_data
			),
			true
		);

		foreach ( $terms as $term ) {
			$choices[] = array(
				'value' => $term->term_id,
				'label' => $term->name,
				'depth' => isset( $term->depth ) ? absint( $term->depth ) : 1,
			);
		}
	}

	return $choices;
}


/**
 * Insert an array into another array before/after a certain key.
 *
 * @param array $array The initial array.
 * @param array $pairs The array to insert.
 * @param string $key The certain key.
 * @param string $position Where to insert the array - before or after the key.
 *
 * @return array
 * @link https://gist.github.com/scribu/588429
 *
 * @since 2.0.0
 */
function wppopups_array_insert( $array, $pairs, $key, $position = 'after' ) {

	$key_pos = array_search( $key, array_keys( $array ), true );
	if ( 'after' === $position ) {
		$key_pos ++;
	}

	if ( false !== $key_pos ) {
		$result = array_slice( $array, 0, $key_pos );
		$result = array_merge( $result, $pairs );
		$result = array_merge( $result, array_slice( $array, $key_pos ) );
	} else {
		$result = array_merge( $array, $pairs );
	}

	return $result;
}

/**
 * Get current post id, to let retrieve from url in case is not set yet
 * changed to grab just to make it clear for me Im not using native wp
 * @return mixed
 */
function wppopups_grab_post_id() {
	global $post;
	// in case geotargeting it's enabled on site
	add_filter( 'geot/cancel_posts_where', '__return_true' );
	$actual_url = get_current_url();
	$id         = isset( $post->ID ) ? $post->ID : url_to_postid( $actual_url );
	remove_filter( 'geot/cancel_posts_where', '__return_true' );

	return $id;
}

/**
 * Helper function to determine if viewing an WP Popups builder page.
 *
 * @return boolean
 */
function wppopups_is_builder_page() {

	if ( ! is_admin() || empty( $_REQUEST['page'] ) || 'wppopups-builder' !== $_REQUEST['page'] ) {
		return false;
	}

	return true;
}

/**
 * Log helper.
 *
 * @param string $title Title of a log message.
 * @param mixed $message Content of a log message.
 * @param array $args Expected keys: popup_id, meta, parent.
 *
 * @since 2.0.0
 *
 */
function wppopups_log( $title = '', $message = '', $args = [] ) {

	// Require log title.
	if ( empty( $title ) ) {
		return;
	}

	// Force logging everything when in debug mode.
	if ( ! wppopups_debug() ) {

		/**
		 * Compare error levels to determine if we should log.
		 * Current supported levels:
		 * - Errors (error)
		 * - Providers (provider)
		 */
		$type   = ! empty( $args['type'] ) ? (array) $args['type'] : [ 'error' ];
		$levels = array_intersect( $type, get_option( 'wppopups_logging', [] ) );
		if ( empty( $levels ) ) {
			return;
		}
	}

	// Meta.
	if ( ! empty( $args['popup_id'] ) ) {
		$meta = [
			'popup' => absint( $args['popup_id'] ),
		];
	} elseif ( ! empty( $args['meta'] ) ) {
		$meta = $args['meta'];
	} else {
		$meta = '';
	}

	// Parent element.
	$parent = ! empty( $args['parent'] ) ? $args['parent'] : 0;

	// Make arrays and objects look nice.
	if ( is_array( $message ) || is_object( $message ) ) {
		$message = '<pre>' . print_r( $message, true ) . '</pre>'; // phpcs:ignore
	}

	// Create log entry.
	wppopups()->logs->add( $title, $message, $parent, $parent, $meta );
}

/**
 * Get the default capability to manage everything for WPPopups.
 *
 * @return string
 * @since 2.0.0
 *
 */
function wppopups_get_capability_manage_options() {
	return apply_filters( 'wppopups_manage_cap', 'manage_options' );
}

/**
 * Object to array.
 *
 * @param object $object
 *
 * @return mixed
 * @since 2.0.0
 *
 */
function wppopups_object_to_array( $object ) {

	if ( ! is_object( $object ) && ! is_array( $object ) ) {
		return $object;
	}

	if ( is_object( $object ) ) {
		$object = get_object_vars( $object );
	}

	return array_map( 'wppopups_object_to_array', $object );
}


/**
 * Load Template.
 *
 * @param string $template_name
 * @param array $args
 * @param string $template_path
 *
 * @return include
 * @since 2.0.0
 *
 */
function wppopups_load_template( $template_name, $args = [], $template_path = '' ) {

	if ( ! empty( $args ) && is_array( $args ) ) {
		extract( $args );
	}

	if ( $template_path != '' ) {
		$located = trailingslashit( $template_path ) . $template_name;
	} else {
		$located = WPPOPUPS_PLUGIN_DIR . $template_name;
	}

	// Allow 3rd party plugin filter template file from their plugin.
	$located = apply_filters( 'wppopups_load_template_located', $located, $args );

	if ( ! file_exists( $located ) ) {
		printf( __( 'File %s is not exists', 'wp-popups-lite' ), $located );

		return;
	}

	do_action( 'wppopups_load_template_before', $located, $args );

	include $located;

	do_action( 'wppopups_load_template_after', $located, $args );
}

/**
 * Check if size has a valid unit
 *
 * @param $size
 *
 * @return string
 */
function wppopups_sanitize_size( $size ) {
	if ( strpos( $size, '%' ) === false &&
	     strpos( $size, 'px' ) === false &&
	     strpos( $size, 'em' ) === false &&
	     strpos( $size, 'vh' ) === false &&
	     strpos( $size, 'vw' ) === false &&
	     strpos( $size, 'vmin' ) === false &&
	     strpos( $size, 'vmax' ) === false
	) {
		$size = $size . 'px';
	}

	return $size;
}

/**
 * Return the box options from legacy popups
 *
 * @param int $id spucpt id
 *
 * @return array metadata values
 * @since  2.0
 */
function wppopups_legacy_box_options( $id ) {
	$defaults = [
		'css'                     => [
			'show_overlay'       => 'yes-color',
			'bgopacity'          => '0.5',
			'overlay_color'      => '#000',
			'overlay_blur'       => '2',
			'background_color'   => '#eeeeee',
			'background_opacity' => '1',
			'width'              => '600px',
			'padding'            => '25',
			'radius'             => '0',
			'color'              => '#333',
			'shadow_color'       => '#666',
			'shadow_type'        => 'outset',
			'shadow_x_offset'    => '0',
			'shadow_y_offset'    => '0',
			'shadow_blur'        => '10',
			'shadow_spread'      => '1',
			'border_color'       => '#eee',
			'border_width'       => '8',
			'border_radius'      => '0',
			'border_type'        => 'none',
			'close_color'        => '#666',
			'close_hover_color'  => '#000',
			'close_size'         => '30px',
			'close_position'     => 'top_right',
			'close_shadow_color' => '#fff',
			'position'           => 'centered',
		],
		'trigger'                 => 'seconds',
		'trigger_number'          => '5',
		'animation'               => 'fade',
		'duration-convert-cookie' => '999',
		'type-convert-cookie'     => 'd',
		'duration-close-cookie'   => '30',
		'type-close-cookie'       => 'd',
		'name-convert-cookie'     => 'spu_conversion',
		'name-close-cookie'       => 'spu_closing',
		'auto_hide'               => 0,
		'test_mode'               => 0,
		'conversion_close'        => '1',
		'powered_link'            => '0',
	];

	$opts = apply_filters( 'spu/metaboxes/box_options', get_post_meta( $id, 'spu_options', true ), $id );

	$opts = wp_parse_args( $opts, apply_filters( 'spu/metaboxes/default_options', $defaults ) );
	// we added new rules as we can't merge recursively, so manual check them
	foreach ( $defaults['css'] as $key => $value ) {
		if ( ! isset( $opts['css'][ $key ] ) ) {
			$opts['css'][ $key ] = $value;
		}
	}

	return $opts;
}

/**
 * Return the box rules for legacy popups
 *
 * @param int $id spucpt id
 *
 * @return array metadata values
 * @since  2.0
 */
function wppopups_legacy_box_rules( $id ) {
	$defaults = [
		// group_0
		[

			// rule_0
			[
				'param'    => 'page_type',
				'operator' => '==',
				'value'    => 'all_pages',
				'order_no' => 0,
				'group_no' => 0,
			],
		],
	];

	$rules = get_post_meta( $id, 'spu_rules', true );

	if ( empty( $rules ) ) {

		return apply_filters( 'spu/metaboxes/default_rules', $defaults );

	} else {

		return $rules;

	}

}

/**
 * Return the a/b parent for legacy popups
 *
 * @param INT $id PopupId
 *
 * @return INT $parent parentID
 */
function wppopups_legacy_box_abgroup( $id ) {

	$abgroup = get_post_meta( $id, 'spu_ab_group', true );

	return apply_filters( 'spu/metaboxes/abgroup', $abgroup );
}

/**
 * Clear all Cache
 * @return mixed
 * @since  2.0
 */
function wppopups_clear_caches() {

	// WP Rocket
	if ( function_exists( 'rocket_clean_domain' ) ) {
		rocket_clean_domain();
	}
	// W3 Total Cache : w3tc
	if ( function_exists( 'w3tc_pgcache_flush' ) ) {
		w3tc_pgcache_flush();
	}
	// WP Super Cache : wp-super-cache
	if ( function_exists( 'wp_cache_clear_cache' ) ) {
		wp_cache_clear_cache();
	}
	// WP Fastest Cache
	if ( function_exists( 'wpfc_clear_all_cache' ) ) {
		wpfc_clear_all_cache( true );
	}
	// WPEngine
	if ( class_exists( 'WpeCommon' ) && method_exists( 'WpeCommon', 'purge_memcached' ) ) {
		WpeCommon::purge_memcached();
		WpeCommon::purge_varnish_cache();
	}
	// SG Optimizer by Siteground
	if ( function_exists( 'sg_cachepress_purge_cache' ) ) {
		sg_cachepress_purge_cache();
	}
	// LiteSpeed
	if ( class_exists( 'LiteSpeed_Cache_API' ) && method_exists( 'LiteSpeed_Cache_API', 'purge_all' ) ) {
		LiteSpeed_Cache_API::purge_all();
	}
	// Cache Enabler
	if ( class_exists( 'Cache_Enabler' ) && method_exists( 'Cache_Enabler', 'clear_total_cache' ) ) {
		Cache_Enabler::clear_total_cache();
	}
	// Pagely
	if ( class_exists( 'PagelyCachePurge' ) && method_exists( 'PagelyCachePurge', 'purgeAll' ) ) {
		PagelyCachePurge::purgeAll();
	}
	// Autoptimize
	if ( class_exists( 'autoptimizeCache' ) && method_exists( 'autoptimizeCache', 'clearall' ) ) {
		autoptimizeCache::clearall();
	}
	//comet cache (formerly zencache)
	if ( class_exists( 'comet_cache' ) && method_exists( 'comet_cache', 'clear' ) ) {
		comet_cache::clear();
	}
	// Hummingbird Cache
	if ( class_exists( '\Hummingbird\WP_Hummingbird' ) &&
	     method_exists( '\Hummingbird\WP_Hummingbird', 'flush_cache' )
	) {
		WP_Hummingbird::flush_cache();
	}

	return true;
}

/**
 * Check wordpress version and include asset if needed
 */
function wppopups_wp_hooks() {
	// include an unmodified $wp_version
	include( ABSPATH . WPINC . '/version.php' );
	if ( version_compare( $wp_version, '5.0.0', '<=' ) ) {

		$es6 = defined( 'WPP_DEBUG' ) || isset( $_GET['WPP_DEBUG'] ) ? 'es6/' : '';
		
		wp_enqueue_script(
			'wp-hooks',
			WPPOPUPS_PLUGIN_URL . 'assets/js/' . $es6 . 'hooks.min.js',
			[ 'jquery' ],
			WPPOPUPS_VERSION,
			false
		);
	}
}



/**
 * Get options for all providers.
 *
 * @since 2.0.0
 *
 * @param string $provider Define a single provider to get options for this one only.
 *
 * @return array
 */
function wppopups_get_providers_options( $provider = '' ) {

	$options  = get_option( 'wppopups_providers', array() );
	$provider = sanitize_key( $provider );
	$data     = $options;

	if ( ! empty( $provider ) && isset( $options[ $provider ] ) ) {
		$data = $options[ $provider ];
	}

	return (array) apply_filters( 'wppopups_get_providers_options', $data, $provider );
}

/**
 * Get an array of all the active provider addons.
 *
 * @since 2.0.0
 *
 * @return array
 */
function wppopups_get_providers_available() {
	return (array) apply_filters( 'wppopups_providers_available', array() );
}

/**
 * Get an array of all the active addons.
 *
 * @since 2.0.0
 *
 * @return array
 */
function wppopups_get_addons_available() {
	return (array) apply_filters( 'wppopups_addons_available', array() );
}

/**
 * Get an array of all the active provider addons.
 *
 * @since 2.0.0
 *
 * @return array
 */
function wppopups_get_connections_available() {
	return (array) apply_filters( 'wppopups_connections_available', array() );
}

/**
 * Default Optin name field placeholder
 * @return mixed
 */
function wppopups_default_optin_name_text() {
	return esc_html__('Your Full Name', 'wppopups-pro' );
}

/**
 * Default optin email field placeholder
 * @return mixed
 */
function wppopups_default_optin_email_text() {
	return esc_html__('Your Email', 'wppopups-pro' );
}

/**
 *  * Default Optin submit button text
 * @return mixed
 */
function wppopups_default_optin_submit_text() {
	return esc_html__('Submit', 'wppopups-pro' );
}

/**
 *  * Default Optin submit button processing text
 * @return mixed
 */
function wppopups_default_optin_submit_processing_text() {
	return esc_html__('Sending...', 'wppopups-pro' );
}

/**
 * Default GDPR text
 * @return mixed
 */
function wppopups_default_optin_gdpr_text() {
	return esc_html__('I agree to the privacy policy and terms', 'wppopups-pro' );
}

/**
 * Default submit color based on theme
 *
 * @param $popup_data
 *
 * @return array [ bg, hover, border]
 */
function wppopups_default_optin_submit_color( $popup_data ) {
	$theme = isset( $popup_data['popup_hidden_class'] ) ? $popup_data['popup_hidden_class'] : 'default';

	switch ( $theme ) {
		case 'spu-theme-coupon' :
			return [ '#E45757', '#7D3630', '#7D3630', '#FFFFFF' ];
			break;
		default:
			return [ '#E45757', '#7D3630', '#7D3630', '#FFFFFF' ];
			break;
	}
}

/**
 * Grab user IP from different possible sources
 * Credits to GeotargetingWP
 * @return string
 */
function wppopups_get_ip() {
	$ip = isset( $_SERVER['REMOTE_ADDR'] ) ? $_SERVER['REMOTE_ADDR'] : '1.1.1.1';
	// cloudflare
	$ip = isset( $_SERVER['HTTP_CF_CONNECTING_IP'] ) ? $_SERVER['HTTP_CF_CONNECTING_IP'] : $ip;
	// reblaze
	$ip = isset( $_SERVER['X-Real-IP'] ) ? $_SERVER['X-Real-IP'] : $ip;
	// Sucuri
	$ip = isset( $_SERVER['HTTP_X_SUCURI_CLIENTIP'] ) ? $_SERVER['HTTP_X_SUCURI_CLIENTIP'] : $ip;
	// Ezoic
	$ip = isset( $_SERVER['X-FORWARDED-FOR'] ) ? $_SERVER['X-FORWARDED-FOR'] : $ip;
	// akamai
	$ip = isset( $_SERVER['True-Client-IP'] ) ? $_SERVER['True-Client-IP'] : $ip;
	// Clouways
	$ip = isset( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $ip;
	// get varnish first ip
	$ip = strstr( $ip, ',') === false ? $ip : strstr( $ip, ',', true);

	return sanitize_text_field( apply_filters( 'wppopups_get_ip', $ip ) );
}


/**
 * Return information about pages if the popup has multiple pages.
 *
 * @since 1.3.7
 *
 * @param mixed $popup
 *
 * @return mixed false or an array
 */
function wppopups_get_pagebreak_details( $popup = false ) {

	$popup_data = '';
	$details   = array();
	$pages     = 1;

	if ( is_object( $popup ) && ! empty( $popup->post_content ) ) {
		$popup_data = wppopus_decode( $popup->post_content );
	} elseif ( is_array( $popup ) ) {
		$popup_data = $popup;
	}

	if ( empty( $popup_data['fields'] ) ) {
		return false;
	}

	foreach ( $popup_data['fields'] as $field ) {
		if ( isset($field['type']) && 'pagebreak' === $field['type'] ) {
			if ( empty( $field['position'] ) ) {
				$pages ++;
				$details['total']   = $pages;
				$details['pages'][] = $field;
			} elseif ( 'top' === $field['position'] ) {
				$details['top'] = $field;
			} elseif ( 'bottom' === $field['position'] ) {
				$details['bottom'] = $field;
			}
		}
	}

	if ( ! empty( $details ) ) {
		if ( empty( $details['top'] ) ) {
			$details['top'] = [];
		}
		if ( empty( $details['bottom'] ) ) {
			$details['bottom'] = [];
		}
		$details['current'] = 1;

		return $details;
	}

	return false;
}



/**
 * Format, sanitize, and return/echo HTML element ID, classes, attributes,
 * and data attributes.
 *
 * @since 1.3.7
 *
 * @param string $id
 * @param array  $class
 * @param array  $datas
 * @param array  $atts
 * @param bool   $echo
 *
 * @return string
 */
function wppopups_html_attributes( $id = '', $class = array(), $datas = array(), $atts = array(), $echo = false ) {

	$id    = trim( $id );
	$parts = array();

	if ( ! empty( $id ) ) {
		$id = sanitize_html_class( $id );
		if ( ! empty( $id ) ) {
			$parts[] = 'id="' . $id . '"';
		}
	}

	if ( ! empty( $class ) ) {
		$class = wppopups_sanitize_classes( $class, true );
		if ( ! empty( $class ) ) {
			$parts[] = 'class="' . $class . '"';
		}
	}

	if ( ! empty( $datas ) ) {
		foreach ( $datas as $data => $val ) {
			$parts[] = 'data-' . sanitize_html_class( $data ) . '="' . esc_attr( $val ) . '"';
		}
	}

	if ( ! empty( $atts ) ) {
		foreach ( $atts as $att => $val ) {
			if ( '0' == $val || ! empty( $val ) ) {
				if ( '[' === $att[0] ) {
					// Handle special case for bound attributes in AMP.
					$escaped_att = '[' . sanitize_html_class( trim( $att, '[]' ) ) . ']';
				} else {
					$escaped_att = sanitize_html_class( $att );
				}
				$parts[] = $escaped_att . '="' . esc_attr( $val ) . '"';
			}
		}
	}

	$output = implode( ' ', $parts );

	if ( $echo ) {
		echo trim( $output ); // phpcs:ignore
	} else {
		return trim( $output );
	}
}


/**
 * Get a suffix for assets, `.min` if debug is disabled.
 *
 * @since 1.4.1
 *
 * @return string
 */
function wppopups_get_min_suffix() {
	return wppopups_debug() ? '' : '.min';
}


/**
 * Determine if we should show the "Show Values" toggle for checkbox, radio, or
 * select fields in form builder. Legacy.
 *
 * @since 1.5.0
 *
 * @return bool
 */
function wppopups_show_fields_options_setting() {

	return apply_filters( 'wppopups_fields_show_options_setting', false );
}


/**
 * Get the required label text, with a filter.
 *
 * @since 1.4.4
 *
 * @return string
 */
function wppopups_get_required_label() {
	return apply_filters( 'wppopups_required_label', esc_html__( 'This field is required.', 'wp-popups-lite' ) );
}

/**
 * Get the required field label HTML, with a filter.
 *
 * @since 1.4.8
 *
 * @return string
 */
function wppopups_get_field_required_label() {

	$label_html = apply_filters_deprecated(
		'wppopups_field_required_label',
		array( ' <span class="wppopups-required-label">*</span>' ),
		'1.4.8 of WPPopups plugin',
		'wppopups_get_field_required_label'
	);

	return apply_filters( 'wppopups_get_field_required_label', $label_html );
}

/**
 * @param $popups
 */
function wppopups_get_ids( $popups ) {
	$ids = [];
	if ( is_array( $popups ) ) {
		foreach ( $popups as $p ) {
			$ids[ $p->id ] = $p->title;
		}
	}
	return $ids;
}
