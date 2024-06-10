<?php

use Jaybizzle\CrawlerDetect\CrawlerDetect;

/**
 * Rules class.
 *
 * @package    WPPopups
 * @author     WPPopups
 * @license    GPL-2.0+
 * @copyright  Copyright (c) 2016, WP Popups LLC
 */
class WPPopups_Rules {
	private static $post_id;
	private static $detect;
	private static $referrer;
	private static $query_string;
	private static $is_category;
	private static $is_singular;
	private static $is_archive;
	private static $is_search;
	private static $current_url;
	private static $is_front_page;
	private static $is_blog_page;
	private static $woo_is_account_page;
	private static $woo_is_checkout;
	private static $woo_is_cart;
	private static $woo_is_product;
	private static $woo_is_product_category;
	private static $woo_is_product_tag;
	private static $woo_is_order_received;
	private static $woo_is_shop;

	private static $rules_to_check;

	/**
	 * WPPopups_Rules constructor.
	 */
	public function __construct() {
		add_action( 'wp_ajax_nopriv_wppopups_rules', [ $this, 'check_rules' ], 1 );
		add_action( 'wp_ajax_wppopups_rules', [ $this, 'check_rules' ], 1 );

		// User
		add_filter( 'wppopups_rules_rule_match_user_type', [ self::class, 'rule_match_user_type' ] );
		add_filter( 'wppopups_rules_rule_match_logged_user', [ self::class, 'rule_match_logged_user' ] );
		add_filter( 'wppopups_rules_rule_match_left_comment', [ self::class, 'rule_match_left_comment' ] );
		add_filter( 'wppopups_rules_rule_match_search_engine', [ self::class, 'rule_match_search_engine' ] );
		add_filter( 'wppopups_rules_rule_match_same_site', [ self::class, 'rule_match_same_site' ] );

		// Post
		add_filter( 'wppopups_rules_rule_match_post_type', [ self::class, 'rule_match_post_type' ] );
		add_filter( 'wppopups_rules_rule_match_post_id', [ self::class, 'rule_match_post' ] );
		add_filter( 'wppopups_rules_rule_match_post', [ self::class, 'rule_match_post' ] );
		add_filter( 'wppopups_rules_rule_match_post_category', [ self::class, 'rule_match_post_category' ] );
		add_filter( 'wppopups_rules_rule_match_post_format', [ self::class, 'rule_match_post_format' ] );
		add_filter( 'wppopups_rules_rule_match_post_status', [ self::class, 'rule_match_post_status' ] );
		add_filter( 'wppopups_rules_rule_match_taxonomy', [ self::class, 'rule_match_taxonomy' ] );

		// Page
		add_filter( 'wppopups_rules_rule_match_page', [ self::class, 'rule_match_post' ] );
		add_filter( 'wppopups_rules_rule_match_page_type', [ self::class, 'rule_match_page_type' ] );
		add_filter( 'wppopups_rules_rule_match_page_parent', [ self::class, 'rule_match_page_parent' ] );
		add_filter( 'wppopups_rules_rule_match_page_template', [ self::class, 'rule_match_page_template' ] );

		//Other
		add_filter( 'wppopups_rules_rule_match_custom_url', [ self::class, 'rule_match_custom_url' ] );
		add_filter( 'wppopups_rules_rule_match_keyword_url', [ self::class, 'rule_match_keyword_url' ] );
		add_filter( 'wppopups_rules_rule_match_cookie', [ self::class, 'rule_match_cookie' ] );
		add_filter( 'wppopups_rules_rule_match_mobiles', [ self::class, 'rule_match_mobiles' ] );
		add_filter( 'wppopups_rules_rule_match_tablets', [ self::class, 'rule_match_tablets' ] );
		add_filter( 'wppopups_rules_rule_match_desktop', [ self::class, 'rule_match_desktop' ] );
		add_filter( 'wppopups_rules_rule_match_referrer', [ self::class, 'rule_match_referrer' ] );
		add_filter( 'wppopups_rules_rule_match_crawlers', [ self::class, 'rule_match_crawlers' ] );
		add_filter( 'wppopups_rules_rule_match_query_string', [ self::class, 'rule_match_query_string' ] );
		add_filter( 'wppopups_rules_rule_match_browser', [ self::class, 'rule_match_browser' ] );
		add_filter( 'wppopups_rules_rule_match_language', [ self::class, 'rule_match_language' ] );

		// Buddypress
		add_filter( 'wppopups_rules_rule_match_bp_is_buddypress', [ self::class, 'rule_match_buddypress' ] );
		add_filter( 'wppopups_rules_rule_match_bp_user_page', [ self::class, 'rule_match_buddypress' ] );
		add_filter( 'wppopups_rules_rule_match_bp_profile_page', [ self::class, 'rule_match_buddypress' ] );
		add_filter( 'wppopups_rules_rule_match_bp_group_page', [ self::class, 'rule_match_buddypress' ] );
		add_filter( 'wppopups_rules_rule_match_bp_friends_page', [ self::class, 'rule_match_buddypress' ] );
		add_filter( 'wppopups_rules_rule_match_bp_messages_page', [ self::class, 'rule_match_buddypress' ] );
		add_filter( 'wppopups_rules_rule_match_bp_activation_page', [ self::class, 'rule_match_buddypress' ] );
		add_filter( 'wppopups_rules_rule_match_bp_register_page', [ self::class, 'rule_match_buddypress' ] );
		add_filter( 'wppopups_rules_rule_match_bp_directory_page', [ self::class, 'rule_match_buddypress' ] );


		// Woocommerce
		add_filter( 'wppopups_rules_rule_match_woo_is_shop', [ self::class, 'rule_match_woocommerce' ] );
		add_filter( 'wppopups_rules_rule_match_woo_is_product_category', [ self::class, 'rule_match_woocommerce' ] );
		add_filter( 'wppopups_rules_rule_match_woo_is_product_tag', [ self::class, 'rule_match_woocommerce' ] );
		add_filter( 'wppopups_rules_rule_match_woo_is_product', [ self::class, 'rule_match_woocommerce' ] );
		add_filter( 'wppopups_rules_rule_match_woo_is_cart', [ self::class, 'rule_match_woocommerce' ] );
		add_filter( 'wppopups_rules_rule_match_woo_is_checkout', [ self::class, 'rule_match_woocommerce' ] );
		add_filter( 'wppopups_rules_rule_match_woo_is_account_page', [ self::class, 'rule_match_woocommerce' ] );
		add_filter( 'wppopups_rules_rule_match_woo_is_order_received', [ self::class, 'rule_match_woocommerce' ] );


		// only run on ajax mode and when our ajax is running
		if ( isset( $_POST['action'] ) && 'wppopups_rules' == $_POST['action'] && defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			$this->init_ajax();
		}


		// rules that work with cache plugins, because are inmutable
		self::$rules_to_check = apply_filters(
			'wppopups_basic_rules',
			[
				'post_type',
				'post_id',
				'post',
				'post_category',
				'post_status',
				'post_format',
				'taxonomy',
				'page',
				'page_type',
				'page_parent',
				'page_template',
				'custom_url',
				'keyword_url',
				'visited_n_pages',
				'woo_is_shop',
				'woo_is_order_received',
				'woo_is_product_category',
				'woo_is_product_tag',
				'woo_is_product',
				'woo_is_cart',
				'woo_is_checkout',
				'woo_is_account_page',
			]
		);
	}

	public static function init_ajax() {

		self::$detect = new Mobile_Detect;

		if ( isset( $_POST['pid'] ) ) {
			self::$post_id = absint( $_POST['pid'] );
		}
		if ( ! empty( $_POST['referrer'] ) ) {
			self::$referrer = sanitize_text_field( $_POST['referrer'] );
		}
		if ( ! empty( $_POST['query_string'] ) ) {
			self::$query_string = sanitize_text_field( $_POST['query_string'] );
		}
		if ( ! empty( $_POST['is_category'] ) ) {
			self::$is_category = true;
		}
		if ( ! empty( $_POST['is_singular'] ) ) {
			self::$is_singular = true;
		}
		if ( ! empty( $_POST['is_archive'] ) ) {
			self::$is_archive = true;
		}
		if ( ! empty( $_POST['is_search'] ) ) {
			self::$is_search = true;
		}
		if ( ! empty( $_POST['url'] ) ) {
			self::$current_url = $_POST['url'];
		}
		if ( ! empty( $_POST['is_front_page'] ) ) {
			self::$is_front_page = $_POST['is_front_page'];
		}
		if ( ! empty( $_POST['is_blog_page'] ) ) {
			self::$is_blog_page = $_POST['is_blog_page'];
		}
		if ( ! empty( $_POST['woo_is_shop'] ) ) {
			self::$woo_is_shop = $_POST['woo_is_shop'];
		}
		if ( ! empty( $_POST['woo_is_order_received'] ) ) {
			self::$woo_is_order_received = $_POST['woo_is_order_received'];
		}
		if ( ! empty( $_POST['woo_is_product_category'] ) ) {
			self::$woo_is_product_category = $_POST['woo_is_product_category'];
		}
		if ( ! empty( $_POST['woo_is_product_tag'] ) ) {
			self::$woo_is_product_tag = $_POST['woo_is_product_tag'];
		}
		if ( ! empty( $_POST['woo_is_product'] ) ) {
			self::$woo_is_product = $_POST['woo_is_product'];
		}
		if ( ! empty( $_POST['woo_is_cart'] ) ) {
			self::$woo_is_cart = $_POST['woo_is_cart'];
		}
		if ( ! empty( $_POST['woo_is_checkout'] ) ) {
			self::$woo_is_checkout = $_POST['woo_is_checkout'];
		}
		if ( ! empty( $_POST['woo_is_account_page'] ) ) {
			self::$woo_is_account_page = $_POST['woo_is_account_page'];
		}
	}


	/**
	 * Check rules y group
	 * @param  array  $rules
	 * @param  Object &$popup
	 * @return mixed
	 */
	private static function check_group( $rules = [], &$popup = null ) {

		// check for rules groups to see if any groups matchs
		foreach( $rules as $group_id => $group ) {
			// start of as true, this way, any rule that doesn't match will cause this varaible to false
			$match_group = true;

			// If the group is not an array
			if( ! is_array( $group ) )
				continue;
			
			// Loop a single group
			foreach( $group as $rule_id => $rule ) {
				if( ! isset( $rule['rule'] ) )
					continue;

				// set to empty if not set
				if( ! isset( $rule['value'] ) )
					$rule['value'] = '';

				// If rule is not a basic rule continue
				if( ! empty( $popup ) && ! in_array( $rule['rule'], self::$rules_to_check ) ) {
					// Mark the popup as having other rules, so we know it needs further proccesing with ajax
					$popup->need_ajax = true;
					continue;
				}

				// Check a rule
				$match = apply_filters( 'wppopups_rules_rule_match_' . $rule['rule'], $rule );

				// if one rule fails we don't need to check the rest of the rules in the group
				if( ! $match ) {
					$match_group = false;
					break;
				}
			}

			// If a group is true, then all the condition is true
			if( $match_group )
				return true;
		}

		return false;
	}

	/**
	 * Function that will check against rules and return the ids of popups to be removed from DOM
	 */
	public function check_rules() {
		$popups = $_POST['popups'];
		$remove = [];
		if ( $popups ) {
			foreach ( $popups as $pop ) {
				// if not rules continue
				if ( empty( $pop['rules'] ) || ! empty( $pop['parent'] ) )
					continue;

				$rules = json_decode( stripslashes( $pop['rules'] ), true );
				$global_rules = isset( $pop['global_rules'] ) ? json_decode( stripslashes( $pop['global_rules'] ), true ) : [];

				// if not valid array		
				if( ! is_array( $rules ) )
					continue;

				// by default remove popup
				$remove[ absint( $pop['id'] ) ] = absint( $pop['id'] );

				$preview = absint( $_POST['is_preview'] );
				if ( $preview ) {
					parse_str(
						str_replace( '?', '', trim( $_POST['query_string'] ) ),
						$preview_id
					);
				}

				// if we are in preview don't check rules unless it's the popup in question
				if ( $preview && ! empty( $preview_id['popup_id'] ) ) {
					unset( $remove[ absint( $preview_id['popup_id'] ) ] );
					continue;
				}

				$match = self::check_group( $rules );
				
				// if we have a match in groups, check the global rules
				if( $match && ! empty( $global_rules ) )
					$match = self::check_group( $global_rules );

				// if we are only use global rules also make it work
				if( empty( $rules ) && ! empty( $global_rules ) )
					$match = self::check_group( $global_rules );

				if( $match )
					unset( $remove[ absint( $pop['id'] ) ] );
			}
		}
		echo json_encode( [ 'success' => apply_filters( 'wppopups_remove_popups_from_loop', array_values( $remove ), $popups ) ] );
		die();
	}

	/**
	 * This is the first run of rules we run to avoid loading all popups in all pages
	 * This is run before popups it's printed and will only take in consideration rules
	 * than can be run even with a cache plugin
	 *
	 * @param array $popups
	 *
	 * @return array
	 */
	public static function pass_basic_rules( array $popups ) {
		// don't check in preview
		if ( isset( $_GET['wppopups_preview'] ) && ! empty( $_GET['popup_id'] ) ) {
			$id = absint( $_GET['popup_id'] );

			return array_filter(
				$popups,
				function ( $popup ) use ( $id ) {
					return $popup->id == $id;
				}
			);
		}
		//init default values
		self::$post_id       = get_queried_object_id();
		self::$is_category   = is_category();
		self::$is_singular   = is_singular();
		self::$is_archive    = is_archive();
		self::$is_search     = is_search();
		self::$current_url   = wppopups_get_current_url();
		self::$is_front_page = is_front_page();
		self::$is_blog_page  = is_home();
		// woocommerce init
		if ( function_exists( 'is_shop' ) ) {
			self::$woo_is_account_page     = is_account_page();
			self::$woo_is_checkout         = is_checkout();
			self::$woo_is_cart             = is_cart();
			self::$woo_is_product          = is_product();
			self::$woo_is_product_category = is_product_category();
			self::$woo_is_product_tag      = is_product_tag();
			self::$woo_is_order_received   = is_wc_endpoint_url( 'order-received' );
			self::$woo_is_shop             = is_shop();
		}

		if ( ! empty( $popups ) ) {
			$filtered_popups = [];
			$examined        = 0;
			foreach ( $popups as $i => &$popup ) {
				$rules = $popup->get_data( 'rules' );
				$global_rules = $popup->get_data( 'global_rules' );

				// If Ab test add to group and continue
				if ( ! empty( $popup->parent ) && $popup->parent !== 0 ) {
					// but check first that parent exists and it's published
					$add = false;
					foreach ( $popups as $j => $p_popup ) {
						if( $popup->parent === $p_popup->id ) {
							$add = true;
						}
					}
					if ( $add ) {
						$popups[ $i ]->need_ajax = true;
						$filtered_popups[ $i ]   = $popups[ $i ];
						continue;
					}
				}
				// if ab parent popup also needs ajax
				if ( ! empty( $popup->childs ) ) {
					$popups[ $i ]->need_ajax = true;
					$filtered_popups[ $i ]   = $popups[ $i ];
					continue;
				}

				// if not valid array
				if ( empty( $rules ) )
					continue;

				$match = self::check_group( $rules, $popup );
				
				// if we have a match in groups, check the global rules
				if( $match && ! empty( $global_rules ) )
					$match = self::check_group( $global_rules, $popup );

				// if we are only use global rules also make it work
				if( empty( $rules ) && ! empty( $global_rules ) )
					$match = self::check_group( $global_rules, $popup );

				if( $match )
					$filtered_popups[ $i ] = $popups[ $i ];
			}

			// if we don't have any filtered popup, return all of them, if none where excluded
			//return 0 === $examined && empty( $filtered_popups ) ? $popups : $filtered_popups;
			
			return empty( $filtered_popups ) ? [] : $filtered_popups;
		}

		return $popups;
	}

	/**
	 * Rule operators
	 *
	 * @param string $rule
	 *
	 * @return array
	 */
	public static function operators( $rule = 'page_type' ) {

		switch ( $rule ) {
			default:
				$operators = [
					'==' => 'is equal to',
					'!=' => 'not equal to',
				];
				break;
		}

		return apply_filters( 'wppopups/rules/operators', $operators, $rule );
	}

	/**
	 * @param string $rule
	 *
	 * @return string
	 */
	public static function field_type( $rule = 'page_type' ) {

		switch ( $rule ) {
			case 'visited_n_pages':
				$type = 'number';
				break;
			case 'referrer':
			case 'keyword_url':
			case 'custom_url':
			case 'query_string':
			case 'post_id':
			case 'language':
				$type = 'text';
				break;
			default:
				$type = 'select';
				break;
		}

		return apply_filters( 'wppopups/rules/field_type', $type, $rule );
	}

	/**
	 * Rules main options
	 * @return array
	 */
	public static function options() {
		$rules = [
			__( 'User', 'wp-popups-lite' )  => [
				'user_type'     => __( 'User role', 'wp-popups-lite' ),
				'logged_user'   => __( 'User is logged', 'wp-popups-lite' ),
				'left_comment'  => __( 'User never left a comment', 'wp-popups-lite' ),
				'search_engine' => __( 'User came via a search engine', 'wp-popups-lite' ),
				'same_site'     => __( 'User did not arrive via another page on your site', 'wp-popups-lite' ),
			],
			__( 'Post', 'wp-popups-lite' )  => [
				'post'          => __( 'Post', 'wp-popups-lite' ),
				'post_id'       => __( 'Post ID', 'wp-popups-lite' ),
				'post_type'     => __( 'Post Type', 'wp-popups-lite' ),
				'post_category' => __( 'Post Category', 'wp-popups-lite' ),
				'post_format'   => __( 'Post Format', 'wp-popups-lite' ),
				'post_status'   => __( 'Post Status', 'wp-popups-lite' ),
				'taxonomy'      => __( 'Post Taxonomy', 'wp-popups-lite' ),
			],
			__( 'Page', 'wp-popups-lite' )  => [
				'page'          => __( 'Page', 'wp-popups-lite' ),
				'page_type'     => __( 'Page Type', 'wp-popups-lite' ),
				'page_parent'   => __( 'Page Parent', 'wp-popups-lite' ),
				'page_template' => __( 'Page Template', 'wp-popups-lite' ),
			],
			__( 'Other', 'wp-popups-lite' ) => [
				'referrer'     => __( 'Referrer', 'wp-popups-lite' ),
				'query_string' => __( 'Query String', 'wp-popups-lite' ),
				'keyword_url'  => __( 'Url contains keyword', 'wp-popups-lite' ),
				'custom_url'   => __( 'Custom Url', 'wp-popups-lite' ),
				'mobiles'      => __( 'Mobile Phone', 'wp-popups-lite' ),
				'tablets'      => __( 'Tablet', 'wp-popups-lite' ),
				'desktop'      => __( 'Desktop', 'wp-popups-lite' ),
				'crawlers'     => __( 'Bots/Crawlers', 'wp-popups-lite' ),
				'browser'      => __( 'Browser', 'wp-popups-lite' ),
			],
		];
		// WPML or Polylang
		if ( function_exists( 'icl_object_id' ) || function_exists( 'pll_current_language' ) ) {
			$rules[ __( 'Other', 'wp-popups-lite' ) ]['language'] = __( 'Language', 'wp-popups-lite' );
		}
		// Buddypress
		if ( function_exists( 'bp_is_user_profile' ) ) {
			$rules[ __( 'Buddypress', 'wp-popups-lite' ) ] = apply_filters( 'wppopups_bp_rules_options',
				[
					'bp_is_buddypress'   => __( 'Is BuddyPress page ?', 'wp-popups-lite' ),
					'bp_user_page'       => __( 'Is the User page ?', 'wp-popups-lite' ),
					'bp_profile_page'    => __( 'Is the Profile page ?', 'wp-popups-lite' ),
					'bp_group_page'      => __( 'Is the Groups page ?', 'wp-popups-lite' ),
					'bp_messages_page'   => __( 'Is the Messages page ?', 'wp-popups-lite' ),
					'bp_friends_page'    => __( 'Is the Friends page ?', 'wp-popups-lite' ),
					'bp_activation_page' => __( 'Is the Activation page ?', 'wp-popups-lite' ),
					'bp_register_page'   => __( 'Is the Register page ?', 'wp-popups-lite' ),
					'bp_directory_page'  => __( 'Is the Directory page ?', 'wp-popups-lite' ),
				]
			);
		}
		// Woocommerce
		if ( function_exists( 'is_woocommerce' ) ) {
			$rules[ __( 'WooCommerce', 'wp-popups-lite' ) ] = apply_filters( 'wppopups_woo_rules_options',
				[
					'woo_is_shop'             => __( 'Is Main shop page ?', 'wp-popups-lite' ),
					'woo_is_product_category' => __( 'Is product Category page ?', 'wp-popups-lite' ),
					'woo_is_product_tag'      => __( 'Is product tag page ?', 'wp-popups-lite' ),
					'woo_is_product'          => __( 'Is single product page ?', 'wp-popups-lite' ),
					'woo_is_cart'             => __( 'Is the cart page ?', 'wp-popups-lite' ),
					'woo_is_checkout'         => __( 'Is the checkout page ?', 'wp-popups-lite' ),
					'woo_is_order_received'   => __( 'Is the order confirmation page ?', 'wp-popups-lite' ),
					'woo_is_account_page'     => __( 'Is the customer account page ?', 'wp-popups-lite' ),
				]
			);
		}

		return apply_filters( 'wppopups/rules/options', $rules );
	}

	/**
	 * @param string $rule
	 *
	 * @return mixed
	 */
	public static function values( $rule = 'page_type' ) {
		$choices = [];
		switch ( $rule ) {
			case 'post_type':
				$choices = wppopups_get_post_types();
				break;

			case 'page':
			case 'page_parent':
				$args = [
					'numberposts'            => - 1,
					'post_type'              => 'page',
					'orderby'                => 'menu_order title',
					'order'                  => 'ASC',
					'post_status'            => 'publish',
					'suppress_filters'       => false,
					'update_post_meta_cache' => false,
				];

				$posts = get_posts( apply_filters( 'wppopups_rules/page_args', $args ) );

				if ( $posts ) {
					// sort into hierachial order!
					if ( is_post_type_hierarchical( 'page' ) ) {
						$posts = get_page_children( 0, $posts );
					}

					foreach ( $posts as $page ) {
						$title     = '';
						$ancestors = get_ancestors( $page->ID, 'page' );
						if ( $ancestors ) {
							foreach ( $ancestors as $a ) {
								$title .= '- ';
							}
						}

						$title                .= apply_filters( 'the_title', $page->post_title, $page->ID );
						$choices[ $page->ID ] = $title;

					}
				}

				break;

			case 'page_type':
				$choices = [
					'all_pages'     => __( 'All Pages', 'wp-popups-lite' ),
					'front_page'    => __( 'Front Page', 'wp-popups-lite' ),
					'posts_page'    => __( 'Posts Page', 'wp-popups-lite' ),
					'category_page' => __( 'Category Page', 'wp-popups-lite' ),
					'search_page'   => __( 'Search Page', 'wp-popups-lite' ),
					'archive_page'  => __( 'Archives Page', 'wp-popups-lite' ),
					'top_level'     => __( 'Top Level Page (parent of 0)', 'wp-popups-lite' ),
					'parent'        => __( 'Parent Page (has children)', 'wp-popups-lite' ),
					'child'         => __( 'Child Page (has parent)', 'wp-popups-lite' ),
				];

				break;

			case 'page_template':
				$choices = [
					'default' => __( 'Default Template', 'wp-popups-lite' ),
				];

				$templates = get_page_templates();
				foreach ( $templates as $k => $v ) {
					$choices[ $v ] = $k;
				}

				break;

			case 'post':
				$post_types = wppopups_get_post_types();
				if ( $post_types ) {
					foreach ( $post_types as $post_type ) {
						$args  = [
							'numberposts'            => - 1,
							'post_type'              => $post_type,
							'post_status'            => 'publish',
							'suppress_filters'       => false,
							'update_post_meta_cache' => false,
							'update_post_term_cache' => false,
						];
						$posts = get_posts( apply_filters( 'wppopups_rules/post_args', $args ) );

						if ( $posts ) {
							$choices[ $post_type ] = [];

							foreach ( $posts as $post ) {
								$title                              = apply_filters( 'the_title', $post->post_title, $post->ID );
								$choices[ $post_type ][ $post->ID ] = $title;

							}
						}
					}
				}

				break;

			case 'post_category':
				$categories = get_terms( 'category', [ 'get' => 'all', 'fields' => 'id=>name' ] );
				$choices    = apply_filters( 'wppopups_rules/categories', $categories );

				break;

			case 'post_format':
				$choices = get_post_format_strings();

				break;

			case 'post_status':
				$choices = get_post_stati();

				break;

			case 'user_type':
				global $wp_roles;

				$choices = $wp_roles->get_names();

				if ( is_multisite() ) {
					$choices['super_admin'] = __( 'Super Admin' );
				}

				break;

			case 'taxonomy':
				$choices = wppopups_get_taxonomies();
				break;

			case 'browser':
				$choices = wppopups_get_browsers();
				break;

			default:
				$choices = [ 'true' => __( 'True', 'wp-popups-lite' ) ];

				break;


		}


		// allow custom rules rules
		return apply_filters( 'wppopups_rules/rule_values/' . $rule, $choices );

	}

	/**
	 * Empty rules
	 * @return array
	 */
	public static function defaults() {
		return [
			'group_0' => [
				'rule_0' => [
					'rule'     => 'page_type',
					'operator' => '==',
					'value'    => 'all_pages',
				],
			],
		];
	}

	/*
	*  rule_match_post
	*
	* @since 2.0.0
	*/
	public static function rule_match_post( $rule ) {

		if ( ! self::$post_id || ! self::$is_singular ) {
			return $rule['operator'] == "==" ? false : true;
		}

		$post_id = self::$post_id;

		// in case multiple ids are passed
		$ids = array_map( 'trim', explode( ',', $rule['value'] ) );

		if ( $rule['operator'] == "==" ) {
			return in_array( $post_id, $ids );
		} elseif ( $rule['operator'] == "!=" ) {
			return ! in_array( $post_id, $ids );
		}

	}

	/**
	 * [rule_match_logged_user description]
	 *
	 * @param array $rule rule to compare
	 *
	 * @return boolean true if match
	 */
	public static function rule_match_logged_user( $rule ) {

		if ( $rule['operator'] == "==" ) {
			return is_user_logged_in();
		}

		return ! is_user_logged_in();
	}

	/**
	 * [rule_match_browser description]
	 *
	 * @param array $rule rule to compare
	 *
	 * @return boolean true if match
	 */
	public static function rule_match_browser( $rule ) {
		include_once 'libraries/Browser.php';

		$detect = new Browser();

		if ( $rule['operator'] == "==" ) {
			return $detect->getBrowser() == $rule['value'];
		}

		return $detect->getBrowser() != $rule['value'];

	}

	/**
	 * [rule_match_mobiles description]
	 *
	 * @param array $rule rule to compare
	 *
	 * @return boolean true if match
	 */
	public static function rule_match_mobiles( $rule ) {

		if ( $rule['operator'] == "==" ) {
			return self::$detect->isMobile();
		}

		return ! self::$detect->isMobile();
	}

	/**
	 * [rule_match_tablets description]
	 *
	 * @param array $rule rule to compare
	 *
	 * @return boolean true if match
	 */
	public static function rule_match_tablets( $rule ) {

		if ( $rule['operator'] == "==" ) {
			return self::$detect->isTablet();
		}

		return ! self::$detect->isTablet();
	}

	/**
	 * [rule_match_desktop description]
	 *
	 * @param array $rule rule to compare
	 *
	 * @return boolean true if match
	 */
	public static function rule_match_desktop( $rule ) {

		if ( $rule['operator'] == "==" ) {
			return ( ! self::$detect->isTablet() && ! self::$detect->isMobile() );
		}

		return ( self::$detect->isTablet() || self::$detect->isMobile() );

	}

	/**
	 * [rule_match_left_comment description]
	 *
	 * @param array $rule rule to compare
	 *
	 * @return boolean true if match
	 */
	public static function rule_match_left_comment( $rule ) {

		if ( $rule['operator'] == "==" ) {
			return ! empty( $_COOKIE[ 'comment_author_' . COOKIEHASH ] );
		}

		return empty( $_COOKIE[ 'comment_author_' . COOKIEHASH ] );
	}

	/**
	 * [rule_match_search_engine description]
	 *
	 * @param array $rule rule to compare
	 *
	 * @return boolean true if match
	 */
	public static function rule_match_search_engine( $rule ) {

		$ref = self::$referrer;

		$SE = apply_filters( 'wppopups_rules_search_engines', [
			'/?s=',
			'/search?',
			'.google.',
			'web.info.com',
			'search.',
			'del.icio.us/search',
			'soso.com',
			'/search/',
			'.yahoo.',
			'.bing.',
		] );
		foreach ( $SE as $url ) {
			if ( strpos( $ref, $url ) !== false ) {
				return $rule['operator'] == "==" ? true : false;
			}
		}

		return $rule['operator'] == "==" ? false : true;

	}

	/**
	 * Check for user referrer
	 *
	 * @param array $rule rule to compare
	 *
	 * @return boolean true if match
	 */
	public static function rule_match_referrer( $rule ) {

		$ref = self::$referrer;

		if ( strpos( $ref, $rule['value'] ) !== false ) {
			return $rule['operator'] == "==" ? true : false;
		}

		return $rule['operator'] == "==" ? false : true;

	}

	/**
	 * Check for custom url
	 *
	 * @param array $rule rule to compare
	 *
	 * @return boolean true if match
	 */
	public static function rule_match_custom_url( $rule ) {

		$wide_search = strpos( $rule['value'], '*' ) !== false ? true : false;
		$current_url = trim( self::$current_url, '/' );
		if ( $wide_search ) {
			if ( strpos( $current_url, trim( $rule['value'], '*' ) ) === 0 ) {
				return ( $rule['operator'] == "==" );
			}

			return ! ( $rule['operator'] == "==" );
		}

		if ( $rule['operator'] == "==" ) {
			return ( $current_url === trim( $rule['value'], '/' ) );
		}

		return ! ( $current_url === trim( $rule['value'], '/' ) );

	}


	/**
	 * Check for keyword url
	 *
	 * @param array $rule rule to compare
	 *
	 * @return boolean true if match
	 */
	public static function rule_match_keyword_url( $rule ) {

		$search_url = str_replace( site_url(), '', self::$current_url );

		if ( strlen( $search_url ) > 0 && strpos( $search_url, trim( $rule['value'] ) ) !== false ) {
			return ( $rule['operator'] == "==" );
		}

		return ! ( $rule['operator'] == "==" );
	}


	/**
	 * Check for crawlers / bots
	 *
	 * @param array $rule rule to compare
	 *
	 * @return boolean true if match
	 */
	public static function rule_match_crawlers( $rule ) {

		$detect = new CrawlerDetect;

		if ( $rule['operator'] == "==" ) {
			return $detect->isCrawler();
		}

		return ! $detect->isCrawler();

	}

	/**
	 * Check for query string to see if matchs all given ones
	 *
	 * @param array $rule rule to compare
	 *
	 * @return boolean true if match
	 */
	public static function rule_match_query_string( $rule ) {


		$found = strpos( self::$query_string, str_replace( '?', '', $rule['value'] ) ) > - 1 ? true : false;

		if ( $rule['operator'] == "==" ) {
			return $found;
		}

		return ! $found;

	}

	/**
	 * [rule_match_same_site description]
	 *
	 * @param array $rule rule to compare
	 *
	 * @return boolean true if match
	 */
	public static function rule_match_same_site( $rule ) {

		$ref = self::$referrer;

		$internal = str_replace( [ 'http://', 'https://' ], '', home_url() );

		if ( $rule['operator'] == "==" ) {
			return ! preg_match( '~' . $internal . '~i', $ref );
		}

		return preg_match( '~' . $internal . '~i', $ref );


	}

	/*
	*  rule_match_post_type
	*
	* @since 2.0.0
	*/

	public static function rule_match_post_type( $rule ) {

		if ( ! self::$post_id || ! self::$is_singular ) {
			return $rule['operator'] == "==" ? false : true;
		}

		$post_type = get_post_type( self::$post_id );

		if ( $rule['operator'] == "==" ) {
			return ( $post_type === $rule['value'] );
		}

		return ( $post_type !== $rule['value'] );
	}

	/*
	*  rule_match_page_type
	*
	* @since 2.0.0
	*/

	public static function rule_match_page_type( $rule ) {


		if ( $rule['value'] == 'front_page' ) {

			if ( $rule['operator'] == "==" ) {
				return self::$is_front_page;
			}

			return ! self::$is_front_page;


		} elseif ( $rule['value'] == 'category_page' ) {
			if ( $rule['operator'] == "==" ) {
				return self::$is_category;
			}

			return ! self::$is_category;

		} elseif ( $rule['value'] == 'archive_page' ) {
			if ( $rule['operator'] == "==" ) {
				return self::$is_archive;
			}

			return ! self::$is_archive;
		} elseif ( $rule['value'] == 'search_page' ) {
			if ( $rule['operator'] == "==" ) {
				return self::$is_search;
			}

			return ! self::$is_search;
		} elseif ( $rule['value'] == 'posts_page' ) {

			if ( $rule['operator'] == "==" ) {
				return self::$is_blog_page;
			}

			return ! self::$is_blog_page;

		} else {
			$post        = get_post( self::$post_id );
			$post_parent = isset( $post->post_parent ) ? $post->post_parent : '';
			$post_type   = get_post_type( self::$post_id );
			if ( $rule['value'] == 'top_level' ) {
				if ( $rule['operator'] == "==" ) {
					return ( $post_parent == 0 );
				}

				return ( $post_parent != 0 );
			} elseif ( $rule['value'] == 'parent' ) {

				$children = get_pages( [
					'post_type' => $post_type,
					'child_of'  => self::$post_id,
				] );

				if ( $rule['operator'] == "==" ) {
					return ( count( $children ) > 0 );
				}

				return ( count( $children ) == 0 );
			} elseif ( $rule['value'] == 'child' ) {
				if ( $rule['operator'] == "==" ) {
					return ( $post_parent != 0 );
				}

				return ( $post_parent == 0 );

			}
		}

		return true;

	}


	/*
	*  rule_match_page_parent
	*
	* @since 2.0.0
	*/

	public static function rule_match_page_parent( $rule ) {

		// validation
		if ( ! self::$post_id || ! self::$is_singular ) {
			return $rule['operator'] == "==" ? false : true;
		}

		// vars
		$post = get_post( self::$post_id );

		$post_parent = $post->post_parent;

		if ( $rule['operator'] == "==" ) {
			return ( $post_parent == $rule['value'] );
		}

		return ( $post_parent != $rule['value'] );
	}


	/*
	*  rule_match_page_template
	*
	* @since 2.0.0
	*/

	public static function rule_match_page_template( $rule ) {

		if ( ! self::$post_id || ! self::$is_singular ) {
			return $rule['operator'] == "==" ? false : true;
		}

		$page_template = get_post_meta( self::$post_id, '_wp_page_template', true );

		if ( ! $page_template ) {
			if ( 'page' == get_post_type( self::$post_id ) ) {
				$page_template = "default";
			}
		}

		if ( $rule['operator'] == "==" ) {
			return ( $page_template === $rule['value'] );
		}

		return ( $page_template !== $rule['value'] );

	}


	/*
	*  rule_match_post_category
	*
	* @since 2.0.0
	*/

	public static function rule_match_post_category( $rule ) {

		if ( ! self::$post_id ) {
			return $rule['operator'] == "==" ? false : true;
		}

		// are we in archive page or single post
		if ( self::$is_category ) {
			if ( $rule['operator'] == "==" ) {
				return ( self::$post_id == $rule['value'] );
			}

			return ( self::$post_id != $rule['value'] );
		} else {
			// post type
			$post_type = get_post_type( self::$post_id );
			// vars
			$taxonomies = get_object_taxonomies( $post_type );
			$terms      = [];
			$all_terms  = get_the_terms( self::$post_id, 'category' );
			if ( $all_terms ) {
				foreach ( $all_terms as $all_term ) {

					$terms[] = $all_term->term_id;

					// If the term has parents
					$parents = get_ancestors( $all_term->term_id, 'category' );

					if ( $parents ) {
						foreach ( $parents as $parent_id ) {
							if ( ! in_array( $parent_id, $terms ) ) {
								$terms[] = $parent_id;
							}
						}
					}
				}
			}


			// no terms at all?
			if ( empty( $terms ) ) {
				// If no terms, this is a new post and should be treated as if it has the "Uncategorized" (1) category ticked
				if ( is_array( $taxonomies ) && in_array( 'category', $taxonomies ) ) {
					$terms[] = '1';
				}
			}


			if ( $rule['operator'] == "==" ) {
				return ( is_array( $terms ) && in_array( $rule['value'], $terms ) );
			}

			return ! ( is_array( $terms ) && in_array( $rule['value'], $terms ) );
		}

	}


	/*
	*  rule_match_user_type
	*
	* @since 2.0.0
	*/

	public static function rule_match_user_type( $rule ) {
		$user = wp_get_current_user();

		if ( $rule['value'] == 'super_admin' ) {
			if ( $rule['operator'] == "==" ) {
				return is_super_admin( $user->ID );
			}

			return ! is_super_admin( $user->ID );
		}
		if ( $rule['operator'] == "==" ) {
			return in_array( $rule['value'], $user->roles );
		}

		return ! in_array( $rule['value'], $user->roles );

	}

	/*
	*  rule_match_post_format
	*
	* @since 2.0.0
	*/

	public static function rule_match_post_format( $rule ) {

		if ( ! self::$post_id || ! self::$is_singular ) {
			return $rule['operator'] == "==" ? false : true;
		}

		$post_type   = get_post_type( self::$post_id );
		$post_format = '';
		// does post_type support 'post-format'
		if ( post_type_supports( $post_type, 'post-formats' ) ) {
			$post_format = get_post_format( self::$post_id );

			if ( $post_format === false ) {
				$post_format = 'standard';
			}

		}


		if ( $rule['operator'] == "==" ) {
			return ( $post_format === $rule['value'] );
		}

		return ( $post_format !== $rule['value'] );

	}


	/*
	*  rule_match_post_status
	*
	* @since 2.0.0
	*/

	public static function rule_match_post_status( $rule ) {

		if ( ! self::$post_id || ! self::$is_singular ) {
			return $rule['operator'] == "==" ? false : true;
		}

		// vars
		$post_status = get_post_status( self::$post_id );

		// auto-draft = draft
		if ( $post_status == 'auto-draft' ) {
			$post_status = 'draft';
		}

		// match
		if ( $rule['operator'] == "==" ) {
			return ( $post_status === $rule['value'] );
		}

		return ( $post_status !== $rule['value'] );

	}

	/*
	*  rule_match_taxonomy
	*
	* @since 2.0.0
	*/

	public static function rule_match_taxonomy( $rule ) {

		if ( ! self::$post_id ) {
			return $rule['operator'] == "==" ? false : true;
		}
		// are we in archive page or single post
		if ( self::$is_archive ) {
			if ( $rule['operator'] == "==" ) {
				return ( self::$post_id == $rule['value'] );
			}

			return ( self::$post_id != $rule['value'] );
		} else {
			// post type
			$post_type = get_post_type( self::$post_id );

			$terms = [];
			// vars
			$taxonomies = get_object_taxonomies( $post_type );

			if ( is_array( $taxonomies ) ) {
				foreach ( $taxonomies as $tax ) {
					$all_terms = get_the_terms( self::$post_id, $tax );
					if ( $all_terms ) {
						foreach ( $all_terms as $all_term ) {
							$terms[] = $all_term->term_id;
						}
					}
				}
			}

			// no terms at all?
			if ( empty( $terms ) ) {
				// If no terms, this is a new post and should be treated as if it has the "Uncategorized" (1) category ticked
				if ( is_array( $taxonomies ) && in_array( 'category', $taxonomies ) ) {
					$terms[] = '1';
				}

			}

			if ( $rule['operator'] == "==" ) {
				return ( is_array( $terms ) && in_array( $rule['value'], $terms ) );
			}

			return ! ( is_array( $terms ) && in_array( $rule['value'], $terms ) );
		}
	}

	/**
	 * Match cookies
	 *
	 * @param $rule
	 *
	 * @return bool
	 */
	public static function rule_match_cookie( $rule ) {

		if ( $rule['operator'] == "==" ) {
			return isset( $_COOKIE[ $rule['value'] ] );
		}

		return ! isset( $_COOKIE[ $rule['value'] ] );
	}

	/**
	 * Check for language WPML or Polylang
	 *
	 * @param $rule
	 *
	 * @return bool
	 */
	public static function rule_match_language( $rule ) {
		$lang = '';
		// polylang
		if ( function_exists( 'pll_current_language' ) ) {
			$lang = pll_current_language();
		}
		// wpml
		if ( function_exists( 'icl_object_id' ) ) {
			$lang = isset( $_GET['lang'] ) ? $_GET['lang'] : ICL_LANGUAGE_CODE;
		}
		// match
		if ( '==' === $rule['operator'] ) {
			return ( $lang === $rule['value'] );
		}

		return ( $lang !== $rule['value'] );
	}

	/**
	 * Rule checker for buddypress templates
	 *
	 * @param $rule
	 *
	 * @return bool
	 */
	public static function rule_match_buddypress( $rule ) {
		if ( ! function_exists( 'is_buddypress' ) ) {
			return '==' === $rule['operator'] ? false : true;
		}
		switch ( $rule['rule'] ) {
			case 'bp_is_buddypress':
				return '==' === $rule['operator'] ? is_buddypress() : ! is_buddypress();
				break;
			case 'bp_user_page':
				return '==' === $rule['operator'] ? bp_user_page() : ! bp_user_page();
				break;
			case 'bp_profile_page':
				return '==' === $rule['operator'] ? bp_profile_page() : ! bp_profile_page();
				break;
			case 'bp_group_page':
				return '==' === $rule['operator'] ? bp_group_page() : ! bp_group_page();
				break;
			case 'bp_messages_page':
				return '==' === $rule['operator'] ? bp_messages_page() : ! bp_messages_page();
				break;
			case 'bp_friends_page':
				return '==' === $rule['operator'] ? bp_friends_page() : ! bp_friends_page();
				break;
			case 'bp_activation_page':
				return '==' === $rule['operator'] ? bp_activation_page() : ! bp_activation_page();
				break;
			case 'bp_register_page':
				return '==' === $rule['operator'] ? bp_register_page() : ! bp_register_page();
				break;
			case 'bp_directory_page':
				return '==' === $rule['operator'] ? bp_directory_page() : ! bp_directory_page();
				break;
		}
	}

	/**
	 * Match woo template tags
	 *
	 * @param $rule
	 *
	 * @return bool
	 */
	public static function rule_match_woocommerce( $rule ) {
		if ( ! function_exists( 'is_woocommerce' ) ) {
			return '==' === $rule['operator'] ? false : true;
		}

		switch ( $rule['rule'] ) {
			case 'woo_is_shop':
				return '==' === $rule['operator'] ? self::$woo_is_shop : ! self::$woo_is_shop;
				break;
			case 'woo_is_product_category':
				return '==' === $rule['operator'] ? self::$woo_is_product_category : ! self::$woo_is_product_category;
				break;
			case 'woo_is_product_tag':
				return '==' === $rule['operator'] ? self::$woo_is_product_tag : ! self::$woo_is_product_tag;
				break;
			case 'woo_is_product':
				return '==' === $rule['operator'] ? self::$woo_is_product : ! self::$woo_is_product;
				break;
			case 'woo_is_cart':
				return '==' === $rule['operator'] ? self::$woo_is_cart : ! self::$woo_is_cart;
				break;
			case 'woo_is_checkout':
				return '==' === $rule['operator'] ? self::$woo_is_checkout : ! self::$woo_is_checkout;
				break;
			case 'woo_is_order_received':
				return '==' === $rule['operator'] ? self::$woo_is_order_received : ! self::$woo_is_order_received;
				break;
			case 'woo_is_account_page':
				return '==' === $rule['operator'] ? self::$woo_is_account_page : ! self::$woo_is_account_page;
				break;
		}
	}
}

new WPPopups_Rules();
