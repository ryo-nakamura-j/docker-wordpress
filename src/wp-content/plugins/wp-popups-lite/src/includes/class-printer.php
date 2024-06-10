<?php

/**
 * Popup rendering.
 *
 * @package    WPPopups
 * @author     WPPopups
 * @since 2.0.0
 * @license    GPL-2.0+
 * @copyright  Copyright (c) 2016, WP Popups LLC
 */
class WPPopups_Printer {

	/**
	 * Contains popup data to be referenced later.
	 *
	 * @since 2.0.0
	 * @var array
	 */
	public $popups;

	/**
	* Will hold only the popups that needs to be visible in front end
	* filter by basic rules
	* set on wp_head hook
	 * @var array
	 */
	private $filtered_popups = [];


	/**
	 * Primary class constructor.
	 *
	 * @since 2.0.0
	 */
	public function __construct() {

		$this->popups = [];
		// Get all popups
		$this->popups = wppopups()->popups->get();

		// base scripts
		add_action( 'wp_enqueue_scripts', [ $this, 'assets_css' ] );
		add_action( 'wp_enqueue_scripts', [ $this, 'assets_js' ], -10 );

		// Actions.
		add_action( 'wppopups_popup_output', [ $this, 'background' ], 10, 2 );
		add_action( 'wppopups_popup_output', [ $this, 'head' ], 15, 2 );
		add_action( 'wppopups_popup_output', [ $this, 'content' ], 20, 2 );
		add_action( 'wppopups_popup_output', [ $this, 'foot' ], 25, 2 );
		add_action( 'wp_head', [ $this, 'print_popup_styles' ], 40 );
		// Filters
		add_filter( 'wppopups_content', 'wptexturize' );
		add_filter( 'wppopups_content', 'convert_smilies' );
		add_filter( 'wppopups_content', 'convert_chars' );
		add_filter( 'wppopups_content', 'wpautop' );
		add_filter( 'wppopups_content', 'shortcode_unautop' );
		global $wp_version;
		if ( version_compare( $wp_version, '5.5', '<=' ) && version_compare( $wp_version, '4.4', '>=' ) ) {
			add_filter( 'wppopups_content', 'wp_make_content_images_responsive' );
		}
		add_filter( 'wppopups_content', 'do_shortcode', 12 );
		// Print popups in front end
		add_action( 'wp_footer', [ $this, 'print_popups_frontend' ] );
		// print popup in builder
		add_action( 'wpopopups_builder_popup', [ $this, 'print_popup_builder' ] );
		// Popup shortcodes
		add_shortcode( 'spu-facebook', [ $this, 'facebook_shortcode' ], 10, 2 );
		add_shortcode( 'spu-facebook-page', [ $this, 'facebook_page_shortcode' ], 10, 2 );
		add_shortcode( 'spu-twitter', [ $this, 'twitter_shortcode' ], 10, 2 );
		add_shortcode( 'spu-close', [ $this, 'close_shortcode' ], 10, 2 );
		add_shortcode( 'spu', [ $this, 'popup_link_shortcode' ], 10, 2 );
	}

	/**
	 * Prints popups in frontend footer
	 */
	public function print_popups_frontend() {

		if( is_admin() )
			return;

		$popups = $this->filtered_popups;
		if ( is_array( $popups ) ) {
			echo '<div class="wppopups-whole" style="display: none">';
			foreach ( $popups as $popup ) {
				$this->output( $popup );
			}
			echo '</div>';
		}

	}

	/**
	 * Print popup in admin builder
	 *
	 * @param $popup
	 */
	public function print_popup_builder( $popup ) {
		remove_filter( 'wppopups_content', 'do_shortcode', 12 );
		add_action( 'wppopups_popup_output', [ $this, 'styles' ], 5, 2 );
		echo '<div class="wppopups-whole">';
			$this->output( $popup );
		echo '</div>';
	}
	/**
	* Popup custom css
	*/
	public function print_popup_styles() {

		if ( ! is_array( $this->popups ) ) {
			return;
		}

		$this->filtered_popups = WPPopups_Rules::pass_basic_rules( $this->popups );
		if ( is_array( $this->filtered_popups ) ) {
			foreach ( $this->filtered_popups as $popup ) {
				// Basic data.
				$popup_data = apply_filters( 'wppopups_frontend_popup_data', wp_parse_args( $popup->data, $popup->get_defaults() ), $popup );
				$popup_id   = absint( $popup->id );
				$this->styles( $popup_data, $popup );
			}
		}
	}

	/**
	 * Primary function to render a popup on the frontend.
	 *
	 * @param WPPopups_Popup $popup
	 *
	 * @since 2.0.0
	 *
	 */
	public function output( $popup ) {

		// Basic data.
		$popup_data = apply_filters( 'wppopups_frontend_popup_data', wp_parse_args( $popup->data, $popup->get_defaults() ), $popup );
		$popup_id   = absint( $popup->id );

		// Before output hook.
		do_action( 'wppopups_popup_output_before', $popup_data, $popup );

		// Allow filter to return early if some condition is not met.
		if ( ! apply_filters( 'wppopups_frontend_load', true, $popup_data, null ) ) {
			return;
		}
		// Print the popup
		do_action( 'wppopups_popup_output', $popup_data, $popup );

		// After output hook.
		do_action( 'wppopups_popup_output_after', $popup_data, $popup );

		// Add popup to class property that tracks all popups in a page.
		//$this->popups[ $popup_id ] = $popup_data;
		$this->popups[ $popup_id ] = $popup;

		// Optional debug information if WPPOPUPS_DEBUG is defined.
		wppopups_debug_data( $popup_data );
	}

	/**
	 * Popups Style
	 *
	 * @param array $popup_data
	 * @param WPPopups_Popup $popup
	 */
	public function styles( $popup_data, $popup ) {

		$popup_id = $popup->id;
		printf(
			'<style type="text/css" id="spu-css-%s" class="spu-css">',
			esc_attr( wppopups_sanitize_classes( $popup_id ) )
		);
		?>
		#spu-bg-<?php echo esc_attr( $popup_id ); ?> {
			background-color: <?php echo isset( $popup_data['colors']['overlay_color'] ) ? esc_attr( $popup_data['colors']['overlay_color'] ) : 'transparent'; ?>;
		}

		#spu-<?php echo esc_attr( $popup_id ); ?> .spu-close {
			font-size: <?php echo esc_attr( $popup_data['close']['close_size'] ); ?>px;
			color: <?php echo esc_attr( $popup_data['close']['close_color'] ); ?>;
			text-shadow: 0 1px 0<?php echo esc_attr( $popup_data['close']['close_shadow_color'] ); ?>;
		}

		#spu-<?php echo esc_attr( $popup_id ); ?> .spu-close:hover {
			color: <?php echo esc_attr( $popup_data['close']['close_hover_color'] ); ?>;
		}

		#spu-<?php echo esc_attr( $popup_id ); ?> {
			background-color: <?php echo ( ! empty( $popup_data['colors']['bg_color'] ) ) ? esc_attr( $popup_data['colors']['bg_color'] ) : 'transparent'; ?>;
			max-width: <?php echo ( ! empty( $popup_data['popup_box']['width'] ) ) ? esc_attr( wppopups_sanitize_size($popup_data['popup_box']['width'] )) : '650px'; ?>;
			border-radius: <?php echo ( ! empty( $popup_data['popup_box']['radius'] ) ) ? esc_attr( $popup_data['popup_box']['radius'] ) : '0'; ?>px;
			<?php if ( isset( $popup_data['colors']['bg_img'] ) && !empty($popup_data['colors']['bg_img']) ) : ?>
				background-image: url( <?php echo esc_attr( $popup_data['colors']['bg_img'] );?> );
				background-repeat: <?php echo isset( $popup_data['colors']['bg_img_repeat'] ) ? esc_attr( $popup_data['colors']['bg_img_repeat'] ) : ''; ?>;
				background-size: <?php echo isset( $popup_data['colors']['bg_img_size'] ) ? esc_attr( $popup_data['colors']['bg_img_size'] ) : ''; ?>;
			<?php endif; ?>

		<?php
		if ( isset( $popup_data['popup_box']['auto_height'] ) && 'yes' === $popup_data['popup_box']['auto_height'] ) {
			$height = 'auto';
		} else {
			$height = isset( $popup_data['popup_box']['height'] ) ? wppopups_sanitize_size($popup_data['popup_box']['height']) : '';
		}
		?>
			height: <?php echo esc_attr( $height ); ?>;
		<?php
		if ( isset( $popup_data['shadow']['shadow_type'] ) && 'none' !== $popup_data['shadow']['shadow_type'] ) :
			$shadow_type = 'inset' === $popup_data['shadow']['shadow_type'] ? 'inset' : '';
			$shadow_x_offset = isset( $popup_data['shadow']['shadow_x_offset'] ) ? $popup_data['shadow']['shadow_x_offset'] : '0';
			$shadow_y_offset = isset( $popup_data['shadow']['shadow_y_offset'] ) ? $popup_data['shadow']['shadow_y_offset'] : '0';
			$shadow_blur = isset( $popup_data['shadow']['shadow_blur'] ) ? $popup_data['shadow']['shadow_blur'] : '0';
			$shadow_spread = isset( $popup_data['shadow']['shadow_spread'] ) ? $popup_data['shadow']['shadow_spread'] : '0';
			$shadow_color = isset( $popup_data['shadow']['shadow_color'] ) ? $popup_data['shadow']['shadow_color'] : '';
			?>
			box-shadow: <?php echo esc_attr( $shadow_type ) . ' ' . esc_attr( $shadow_x_offset ) . 'px ' . esc_attr( $shadow_y_offset ) . 'px ' . esc_attr( $shadow_blur ) . 'px ' . esc_attr( $shadow_spread ) . 'px ' . esc_attr( $shadow_color ); ?>;
		<?php endif; ?>
		}

		#spu-<?php echo esc_attr( $popup_id ); ?> .spu-container {
		<?php if ( isset( $popup_data['border']['border_type'] ) && 'none' !== $popup_data['border']['border_type'] ) : ?>
			border: <?php echo esc_attr( $popup_data['border']['border_width'] ) . 'px ' . esc_attr( $popup_data['border']['border_type'] ); ?>;
			border-color: <?php echo ( ! empty( $popup_data['border']['border_color'] ) ) ? esc_attr( $popup_data['border']['border_color'] ) : 'white'; ?>;
			border-radius: <?php echo ( ! empty( $popup_data['border']['border_radius'] ) ) ? esc_attr( $popup_data['border']['border_radius'] ) : '0'; ?>px;
			margin: <?php echo ( ! empty( $popup_data['border']['border_margin'] ) ) ? esc_attr( $popup_data['border']['border_margin'] ) : '0'; ?>px;
		<?php endif; ?>
			padding: <?php echo ( ! empty( $popup_data['popup_box']['padding'] ) ) ? esc_attr( $popup_data['popup_box']['padding'] ) : '0'; ?>px;
			height: calc(100% - <?php echo ( ! empty( $popup_data['border']['border_margin'] ) ) ? abs( esc_attr( $popup_data['border']['border_margin'] ) ) * 2 : '0'; ?>px);
		}
		<?php echo isset( $popup_data['css']['custom_css'] ) ? wp_strip_all_tags( $popup_data['css']['custom_css'] ) : ''; ?>
		<?php do_action( 'wppopups_popups_style', $popup_data, $popup ); ?>
		<?php do_action( "wppopups_popup_{$popup_id}_style", $popup_data, $popup ); ?>
		</style>
		<?php
	}

	/**
	 * Background overlay for popup
	 *
	 * @param array $popup_data
	 * @param WPPopups_Popup $popup
	 */
	public function background( $popup_data, $popup ) {
		// show overlay since 2.0.0.7, so show if not set
		if( ! isset( $popup_data['colors']['show_overlay'] ) || $popup_data['colors']['show_overlay'] == 'yes-color' || $popup_data['colors']['show_overlay'] == 'yes' ) {
			printf('<div class="spu-bg %s" id="spu-bg-%s"></div>',
				esc_attr( wppopups_sanitize_classes( apply_filters( 'wppopups_popup_bg_class', [], $popup ), true ) ),
				esc_attr( $popup->id )
			);
		}
	}

	/**
	 * Popup head area.
	 *
	 * @param array $popup_data
	 * @param WPPopups_Popup $popup
	 */
	public function head( $popup_data, $popup ) {

		$position = isset( $popup_data['position'] ) && isset( $popup_data['position']['position'] ) ? 'spu-position-' . $popup_data['position']['position'] : '';
		$animation = isset( $popup_data['animation'] ) && isset( $popup_data['animation']['animation'] ) ? 'spu-animation-' . $popup_data['animation']['animation'] : '';
		$theme_class = isset( $popup_data['settings']['popup_hidden_class'] ) ? $popup_data['settings']['popup_hidden_class'] : '';
		$custom_class = isset( $popup_data['settings']['popup_class'] ) ? $popup_data['settings']['popup_class'] : '';
		$classes   = [ 'spu-box ', $animation, $theme_class, $custom_class, $position ];
		//remove content , we don't need it here
		unset( $popup_data['content'] );
		printf(
			'<div class="%s" id="spu-%d" data-id="%d" data-parent="%d" data-settings="%s" data-need_ajax="%d">',
			esc_attr( wppopups_sanitize_classes( apply_filters( 'wppopups_popup_class', $classes, $popup, $popup_data ), true ) ),
			esc_attr( $popup->id ),
			esc_attr( $popup->id ),
			esc_attr( $popup->parent ),
			esc_attr( json_encode( $popup_data ) ),
			! empty( $popup->need_ajax ) ? 1 : 0
		);

	}

	/**
	 * Popup content area.
	 *
	 * @param array $popup_data
	 * @param WPPopups_Popup $popup
	 */
	public function content( $popup_data, $popup ) {

		do_action( 'wppopups_popup_container_before', $popup );

		echo '<div class="spu-container ' . esc_attr( wppopups_sanitize_classes( apply_filters( 'wppopups_spu_container_class', '', $popup_data, $popup ) ) ). '">';
		echo '<div class="spu-content">';
		do_action( 'wppopups_popup_content_before', $popup );
		echo apply_filters( 'wppopups_content',
			! empty( $popup_data['content']['popup_content'] ) ?
				$popup_data['content']['popup_content'] : wppopups_welcome_text(),
			$popup_data
		);
		do_action( 'wppopups_popup_content_after', $popup );
		echo '</div>'; //spu-content
		//Close
		if( apply_filters( 'wppopups_display_close_button', true, $popup_data, $popup ) ) {
			$close_position = isset( $popup_data['close']['close_position'] ) ? $popup_data['close']['close_position'] : '';
			echo '<a href="#" class="spu-close spu-close-popup spu-close-' . esc_attr( wppopups_sanitize_classes( $close_position ) ) . '">&times;</a>';
		}
		// Timer
		echo '<span class="spu-timer"></span>';
		// Powered by
		if ( isset( $popup_data['settings']['powered_link'] ) && '1' === $popup_data['settings']['powered_link'] ) {
			$aff_link = ! empty( wppopups_setting( 'aff-link' ) ) ? wppopups_setting( 'aff-link' ) : 'https://timersys.com/popups/';
			printf( '<p class="spu-powered">Powered by <a href="%s" target="_blank" >WP Popups</a></p>',
				esc_url( $aff_link )
			);
		}
		echo '</div>'; //spu-container
		do_action( 'wppopups_popup_container_after', $popup );

	}


	/**
	 * Popup footer area.
	 *
	 * @param array $popup_data
	 * @param WPPopups_Popup $popup
	 */
	public function foot( $popup_data, $popup ) {

		do_action( 'wppopups_popup_footer', $popup );

		echo '</div><!--spu-box-->';
	}

	/**
	 * Load the CSS assets for frontend output.
	 *
	 * @since 2.0.0
	 */
	public function assets_css() {

		if( is_admin() )
			return;

		do_action( 'wppopups_frontend_css', $this->popups );

		wp_enqueue_style(
			'wppopups-base',
			WPPOPUPS_PLUGIN_URL . 'assets/css/wppopups-base.css',
			[],
			WPPOPUPS_VERSION
		);

	}

	/**
	 * Load the JS assets for frontend output.
	 *
	 * @since 2.0.0
	 */
	public function assets_js() {

		if( is_admin() )
			return;

		do_action( 'wppopups_frontend_js', $this->popups );

		$handle_jquery = apply_filters( 'wppopups_handle_jquery', 'jquery' );

		wppopups_wp_hooks();

		$wpml_lang = defined( 'ICL_LANGUAGE_CODE' ) ? ICL_LANGUAGE_CODE : '';
		$es6 = defined( 'WPP_DEBUG' ) || isset( $_GET['WPP_DEBUG'] ) ? 'es6/' : '';

		// Load base JS.
		wp_enqueue_script(
			'wppopups',
			WPPOPUPS_PLUGIN_URL . 'assets/js/' . $es6 . 'wppopups.js',
			[ $handle_jquery, 'wp-hooks' ],
			WPPOPUPS_VERSION,
			true
		);
		//[ $handle_jquery, 'wppopups-validation', 'wppopups-mailcheck', 'wp-hooks' ],

		$ajax_url = admin_url( 'admin-ajax.php' );
		if ( ! empty( $wpml_lang ) ) {
			$ajax_url = add_query_arg( 'lang', $wpml_lang, $ajax_url );
		}

		// old plugin
		wp_register_script( 'spu-facebook', '//connect.facebook.net/' . get_locale() . '/sdk.js#xfbml=1&version=v3.0', [ $handle_jquery ], null, false );
		wp_register_script( 'spu-twitter', '//platform.twitter.com/widgets.js', [ $handle_jquery ], WPPOPUPS_VERSION, false );

		// only load scripts if shortcode in content
		$load_fb = $load_tw = false;
		if ( is_array( $this->popups ) ) {
			foreach ( $this->popups as $popup ) {
				$popup_data = apply_filters( 'wppopups_frontend_popup_data', wp_parse_args( $popup->data, $popup->get_defaults() ), $popup );
				if ( ! empty( $popup_data['content']['popup_content'] ) ) {
					$content = $popup_data['content']['popup_content'];
					if ( has_shortcode( $content, 'spu-facebook-page' ) || has_shortcode( $content, 'spu-facebook' ) ) {
						$load_fb = true;
					}

					if ( has_shortcode( $content, 'spu-twitter' ) ) {
						$load_tw = true;
					}
				}
			}
			if ( $load_fb ) {
				wp_enqueue_script( 'spu-facebook' );
			}
			if ( $load_tw ) {
				wp_enqueue_script( 'spu-twitter' );
			}
		}
		$vars = [
				'is_admin'      => current_user_can( apply_filters( 'wppopups_capabilities_testmode', 'administrator' ) ),
				#	'disable_style' 				=> isset( $this->spu_settings['shortcodes_style'] ) ? esc_attr( $this->spu_settings['shortcodes_style'] ) : '',
				#	'ajax_mode'						=> isset( $this->spu_settings['ajax_mode'] ) ? esc_attr( $this->spu_settings['ajax_mode'] ) :'',
				'ajax_url'      => $ajax_url,
				'pid'           => get_queried_object_id(),
				'is_front_page' => is_front_page(),
				'is_blog_page'  => is_home(),
				'is_category'   => is_category(),
				'site_url'      => site_url(),
				'is_archive'    => is_archive(),
				'is_search'     => is_search(),
				'is_singular'   => is_singular(),
				'is_preview'    => is_page( absint( get_option( 'wppopups_preview_page' ) ) ),
				'facebook'      => $load_fb,
				'twitter'       => $load_tw,
				'val_required'	=> esc_html__( 'This field is required.', 'wp-popups-lite' ),
				'val_url'		=> esc_html__( 'Please enter a valid URL.', 'wp-popups-lite' ),
				'val_email'		=> esc_html__( 'Please enter a valid email address.', 'wp-popups-lite' ),
				'val_number'	=> esc_html__( 'Please enter a valid number.', 'wp-popups-lite' ),
				'val_checklimit'		=> wppopups_setting( 'validation-check-limit', esc_html__( 'You have exceeded the number of allowed selections: {#}.', 'wp-popups-lite' ) ),
				'val_limit_characters'	=> esc_html__( '{count} of {limit} max characters.', 'wp-popups-lite' ),
				'val_limit_words'		=> esc_html__( '{count} of {limit} max words.', 'wp-popups-lite' ),
			];
		if ( function_exists( 'is_shop' ) ) {
		    $vars['woo_is_shop'] = is_shop();
		    $vars['woo_is_order_received'] = is_wc_endpoint_url( 'order-received' );
			$vars['woo_is_product_category'] = is_product_category();
			$vars['woo_is_product_tag'] = is_product_tag();
			$vars['woo_is_product'] = is_product();
			$vars['woo_is_cart'] = is_cart();
			$vars['woo_is_checkout'] = is_checkout();
			$vars['woo_is_account_page'] = is_account_page();
		}
		wp_localize_script( 'wppopups', 'wppopups_vars', $vars );
	}

	/**
	 * [facebook_shortcode description]
	 *
	 * @param $atts
	 * @param $content
	 *
	 * @return string          [description]
	 * @internal param $atts    [description] $atts    [description]
	 * @internal param $ $content [description] $content [description]
	 */
	function facebook_shortcode( $atts, $content ) {

		extract( shortcode_atts( [
			'href'       => apply_filters( 'wppopups_social_fb_href', 'https://www.facebook.com/pages/Timersys/146687622031640' ),
			'layout'     => 'button_count', // standard, box_count, button_count, button
			'show_faces' => 'false', // true
			'share'      => 'false', // true
			'action'     => 'like', // recommend
			'width'      => '',
			'align'      => 'center',
		], $atts ) );

		$layout = strtolower( trim( $layout ) );
		$action = strtolower( trim( $action ) );

		// to avoid problems
		if ( 'standard' != $layout && 'box_count' != $layout && 'button_count' != $layout && 'button' != $layout ) {
			$layout = 'button_count';
		}
		if ( 'like' != $action && 'recommend' != $action ) {
			$action = 'like';
		}
		$align = $this->sanitize_align( $align );

		return '<div class="spu-facebook spu-shortcode" style="text-align:' . $align . '"><div class="fb-like" data-width="' . esc_attr( strtolower( trim( $width ) ) ) . '" data-href="' . esc_url( $href ) . '" data-layout="' . esc_attr( $layout ) . '" data-action="' . esc_attr( $action ) . '" data-show-faces="' . esc_attr( strtolower( trim( $show_faces ) ) ) . '" data-share="' . esc_attr( strtolower( trim( $share ) ) ) . '"></div></div>';

	}

	/**
	 * Shortcode for facebook page
	 *
	 * @param $atts
	 * @param $content
	 *
	 * @return string          [description]
	 * @internal param $atts    [description] $atts    [description]
	 * @internal param $ $content [description] $content [description]
	 */
	function facebook_page_shortcode( $atts, $content ) {

		extract( shortcode_atts( [
			'href'       => apply_filters( 'wppopups_social_fb_href', 'https://www.facebook.com/pages/Timersys/146687622031640' ),
			'name'       => apply_filters( 'wppopups_social_fb_name', 'Timersys' ),
			'show_faces' => 'true', // false
			'hide_cover' => 'false', // true
			'width'      => '500',
			'align'      => 'center',
		], $atts ) );
		$align = $this->sanitize_align( $align );

		return '<div class="spu-facebook-page" style="text-align:' . $align . '"><div class="fb-page" data-href="' . esc_url( $href ) . '" data-width="' . esc_attr( strtolower( trim( $width ) ) ) . '" data-hide-cover="' . esc_attr( strtolower( trim( $hide_cover ) ) ) . '" data-show-facepile="' . esc_attr( strtolower( trim( $show_faces ) ) ) . '" data-show-posts="false"><div class="fb-xfbml-parse-ignore"><blockquote cite="' . esc_attr( $href ) . '"><a href="' . esc_url( $href ) . '">' . esc_attr( $name ) . '</a></blockquote></div></div></div>';

	}

	/**
	 * [twitter_shortcode description]
	 *
	 * @param string $content [description]
	 * @param array $atts [description]
	 *
	 * @return string          [description]
	 */
	function twitter_shortcode( $atts, $content ) {
		extract( shortcode_atts( [
			'user'       => apply_filters( 'wppopups_social_tw_user', 'chifliiiii' ),
			'show_count' => 'true', // false
			'size'       => '', // large
			'lang'       => '',
			'align'      => 'center',
		], $atts ) );
		$align = $this->sanitize_align( $align );

		return '<div class="spu-twitter spu-shortcode' . esc_attr( $user ) . '" style="text-align:' . $align . '"><a href="https://twitter.com/' . esc_attr( $user ) . '" class="twitter-follow-button" data-show-count="' . esc_attr( strtolower( trim( $show_count ) ) ) . '" data-size="' . esc_attr( strtolower( trim( $size ) ) ) . '" data-lang="' . esc_attr( $lang ) . '"></a></div>';

	}

	function close_shortcode( $atts, $content ) {
		extract( shortcode_atts( [
			'class'      => 'button-primary btn-primary',
			'text'       => 'Close',
			'conversion' => false,
		], $atts ) );
		$button_class = ! $conversion || $conversion == 'false' ? 'spu-close-popup ' : 'spu-close-convert ';

		return '<button class="' . $button_class . esc_attr( $class ) . '">' . esc_attr( $text ) . '</button>';
	}

	/**
	 * Popup button
	 *
	 * @param  [type] $atts    [description]
	 * @param  [type] $content [description]
	 *
	 * @return [type]          [description]
	 */
	public function popup_link_shortcode( $atts, $content ) {
		return '<a href="#" class="spu-open-' . esc_attr( $atts['popup'] ) . '">' . wp_kses_post( do_shortcode( $content ) ) . '</a>';
	}

	/**
	 * Check if align in one of the three methods
	 *
	 * @param $align
	 *
	 * @return string
	 */
	private function sanitize_align( $align ) {
		if ( ! in_array( $align, [ 'center', 'left', 'right' ] ) ) {
			return 'center';
		}

		return $align;
	}
}
