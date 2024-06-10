<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}  // if direct access

/**
 * Check if this is a pro version
 *
 * @return boolean
 */
function sp_pc_is_pro() {

	if ( file_exists( SP_PC_PATH . '/includes/pro/loader.php' ) ) {
		return true;
	}

	return false;
}

/**
 * Shortcode converter function
 */
function post_carousel_id( $id ) {
	echo do_shortcode( '[post-carousel id="' . $id . '"]' );
}

/**
 * Functions
 */
class SP_Post_Carousel_Functions {

	/**
	 * SP_Post_Carousel_Functions single instance of the class
	 *
	 * @var null
	 * @since 2.0
	 */
	protected static $_instance = null;

	/**
	 * Main SP_PC Instance
	 *
	 * @since 2.0
	 * @static
	 * @see sp_pc()
	 * @return self Main instance
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * Initialize the class
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		add_filter( 'admin_footer_text', array( $this, 'admin_footer' ), 1, 2 );
	}

	/**
	 * Admin Menu
	 */
	function admin_menu() {
		add_submenu_page(
			'edit.php?post_type=sp_pc_shortcodes', __( 'Post Carousel Help', 'post-carousel' ), __( 'Help', 'post-carousel' ), 'manage_options', 'help', array(
				$this,
				'help_page_callback',
			)
		);
	}

	/**
	 * Help Page Callback
	 */
	public function help_page_callback() {
		?>
		<div class="wrap about-wrap sp-pc-help">
			<h1><?php _e( 'Welcome to Post Carousel!', 'post-carousel' ); ?></h1>
			<p class="about-text">
			<?php
			_e(
				'Thank you for installing Post Carousel! You\'re now running the most popular Post Carousel plugin.
This video will help you get started with the plugin.', 'post-carousel'
			);
			?>
									</p>
			<div class="wp-badge"></div>

			<hr>

			<div class="headline-feature feature-video">
				<iframe width="560" height="315" src="https://www.youtube.com/embed/TrgLOLCfxz8" frameborder="0" allowfullscreen></iframe>
			</div>

			<hr>

			<div class="feature-section three-col">
				<div class="col">
					<div class="sp-pc-feature sp-pc-text-center">
						<i class="sp-pc-font-icon sp-pc-icon-lifebuoy"></i>
						<h3>Need any Assistance?</h3>
						<p>Our Expert Support Team is always ready to help you out promptly.</p>
						<a href="https://shapedplugin.com/support-forum/" target="_blank" class="button button-primary">Contact Support</a>
					</div>
				</div>
				<div class="col">
					<div class="sp-pc-feature sp-pc-text-center">
						<i class="sp-pc-font-icon sp-pc-icon-doc-text-inv"></i>
						<h3>Looking for Documentation?</h3>
						<p>We have detailed documentation on every aspects of Post Carousel.</p>
						<a href="https://shapedplugin.com/docs/post-carousel/" target="_blank" class="button button-primary">Documentation</a>
					</div>
				</div>
				<div class="col">
					<div class="sp-pc-feature sp-pc-text-center">
						<i class="sp-pc-font-icon sp-pc-icon-bug"></i>
						<h3>Found any Bugs?</h3>
						<p>Report any bug that you found, Get a instant solutions from developer.</p>
						<a href="https://shapedplugin.com/support-forum/" target="_blank" class="button button-primary">Report</a>
					</div>
				</div>
			</div>

			<hr>

			<div class="sp-pc-pro-features">
				<h2 class="sp-pc-text-center">Upgrade to Post Carousel Pro!</h2>
				<p class="sp-pc-text-center sp-pc-pro-subtitle">We've added 100+ extra features in our Premium Version of this plugin. Let’s see some amazing features.</p>
				<div class="feature-section three-col">
					<div class="col">
						<div class="sp-pc-feature">
							<h3><span class="dashicons dashicons-yes"></span>Advanced Shortcode Generator</h3>
							<p>Understanding long-shortcodes is very painful with attributes. Post Carousel PRO comes with built-in Advanced Shortcode Generator to easily control the look and function of the Carousel. It's easy enough!</p>
						</div>
					</div>
					<div class="col">
						<div class="sp-pc-feature">
							<h3><span class="dashicons dashicons-yes"></span>4+ Post Layouts</h3>
							<p>You can select from 5 beautiful layouts: Carousel, Grid, Masonry, Filter-Grid, Filter-Masonry. Creating your own customized layout is super easy. Showcase your posts how you want and use multi-creative ideas.</p>
						</div>
					</div>
					<div class="col">
						<div class="sp-pc-feature">
							<h3><span class="dashicons dashicons-yes"></span>10+ Amazing Theme Styles</h3>
							<p>Post Carousel Pro comes with 10+ amazing pre-defined theme styles. You can choose any theme styles or more from several theme styles to fit your requirements. You can customize on your own way.</p>
						</div>
					</div>
					<div class="col">
						<div class="sp-pc-feature">
							<h3><span class="dashicons dashicons-yes"></span>840+ Google Fonts</h3>
							<p>With Premium version, You can add your desired font in the slider from 840+ Google Fonts. You can easily customize the Font family, size, transform, letter spacing, color, and line-height for each and every content of the product.</p>
						</div>
					</div>
					<div class="col">
						<div class="sp-pc-feature">
							<h3><span class="dashicons dashicons-yes"></span>Slide any Post Type</h3>
							<p>You can display posts from multiple post types (posts, products, portfolio etc.). With Post Carousel Pro, you can show multiple post types as post carousel, grid, filter or masonry layout into a page or post.</p>
						</div>
					</div>
					<div class="col">
						<div class="sp-pc-feature">
							<h3><span class="dashicons dashicons-yes"></span>Display any Taxonomy</h3>
							<p>Do you want to display some featured category to your visitors? Post Carousel Pro will help you to show any taxonomy (even specific posts, most viewed & liked) in output, without writing any line of code.</p>
						</div>
					</div>
					<div class="col">
						<div class="sp-pc-feature">
							<h3><span class="dashicons dashicons-yes"></span>Tailor-made Responsivity</h3>
							<p>Post Carousel Pro is 100% responsive and using intuitive breakpoints settings that you can customize the number of slides displayed on a desktop, tablet, and mobile. Control on your carousel at any resolution!</p>
						</div>
					</div>
					<div class="col">
						<div class="sp-pc-feature">
							<h3><span class="dashicons dashicons-yes"></span>Lightbox Options for Images</h3>
							<p>Another important premium feature is Lightbox functionality for images. You can add Lightbox icon color, hover color and overlay color etc. from settings easily. Lightbox feature can help you to zoom in your images smoothly.</p>
						</div>
					</div>
					<div class="col">
						<div class="sp-pc-feature">
							<h3><span class="dashicons dashicons-yes"></span>Image Re-size Options</h3>
							<p>You can change the default size of your post images on the settings panel. New uploaded images will be resized or cropped equally to the specified dimensions what you set. You need upload bigger images to re-size in your chosen dimension.</p>
						</div>
					</div>
					<div class="col">
						<div class="sp-pc-feature">
							<h3><span class="dashicons dashicons-yes"></span>GrayScale Effects</h3>
							<p>Post Carousel Pro is compatible with most browsers, you can choose to display the post images on a Grayscale version and a bit of transparency and choose if on hover the post image will have the original colors or not.</p>
						</div>
					</div>
					<div class="col">
						<div class="sp-pc-feature">
							<h3><span class="dashicons dashicons-yes"></span>6 Read More Buttons</h3>
							<p>You can simply select the read more button from 6 (six) button styles. You can customize the Read More Text, color, border color, background, hover color and background hover color etc. You can also change the read more button alignment.</p>
						</div>
					</div>
					<div class="col">
						<div class="sp-pc-feature">
							<h3><span class="dashicons dashicons-yes"></span>Unlimited Color & Styling</h3>
							<p>You can change anything you want from advanced settings panel easily. It needs just few clicks only. There are plenty of amazing useful styling options which can help you to showcase your posts on your own way.</p>
						</div>
					</div>
					<div class="col">
						<div class="sp-pc-feature">
							<h3><span class="dashicons dashicons-yes"></span>Visual Composer & Widget Ready!</h3>
							<p>To include a carousel or grid inside a widget area is as simple as including any other widget! Post Carousel Pro comes ready with a widget. An extra component will also be added to Visual Composer.</p>
						</div>
					</div>
					<div class="col">
						<div class="sp-pc-feature">
							<h3><span class="dashicons dashicons-yes"></span>Unlimited Post Meta & Icons</h3>
							<p>Post Meta is an important part to show for any post. With the Pro, you can display the following meta with icons author, date, category, tags, comments, like or favorite button, views count and custom meta key options.</p>
						</div>
					</div>
					<div class="col">
						<div class="sp-pc-feature">
							<h3><span class="dashicons dashicons-yes"></span>Social Sharing Button</h3>
							<p>Allow your visitors to share your content via Facebook, Twitter, Google+, Linkedin, Pinterest e-mail easily, without installing another plugin. Post Carousel PRO is fully integrated with social sharing button which can take your valuable content next level.</p>
						</div>
					</div>
					<div class="col">
						<div class="sp-pc-feature">
							<h3><span class="dashicons dashicons-yes"></span>Ticker Mode Carousel</h3>
							<p>This is an another amazing feature of Post Carousel Pro. It slides with infinite loop, with no Pause. You can set the speed and if the slider pauses on hover. You can enable or disable this option easily from the beginning of carousel settings.</p>
						</div>
					</div>
					<div class="col">
						<div class="sp-pc-feature">
							<h3><span class="dashicons dashicons-yes"></span>Navigation Styles & Positions</h3>
							<p>You can select your desired arrow style to fit your needs from 6 six different arrow styles. This plugin has 8+ different navigational arrow positions Top (right, center, top) bottom (left, center, right) vertically (center, inner center, inner center on hover etc.).</p>
						</div>
					</div>
					<div class="col">
						<div class="sp-pc-feature">
							<h3><span class="dashicons dashicons-yes"></span>24/7 Fast & Friendly Support</h3>
							<p>A fully dedicated 24/7 One to one expert support forum is ready to help you instantly whenever you face with any issues to configure or use the plugin. We don't offer one-time support, we care for you day-by-day.</p>
						</div>
					</div>
				</div>
			</div>

			<div class="sp-pc-upgrade-sticky-footer sp-pc-text-center">
				<p><a href="https://shapedplugin.com/demo/post-carousel-pro/" target="_blank" class="button button-primary">Live Demo</a> <a href="https://shapedplugin.com/plugin/post-carousel-pro/" target="_blank" class="button button-primary">Upgrade Now</a></p>
			</div>

		</div>
		<?php
	}

	/**
	 * Review Text
	 *
	 * @param $text
	 *
	 * @return string
	 */
	public function admin_footer( $text ) {
		if ( 'sp_pc_shortcodes' == get_post_type() ) {
			$url  = 'https://wordpress.org/support/plugin/post-carousel/reviews/?filter=5#new-post';
			$text = sprintf( __( 'If you like <strong>Post Carousel</strong> please leave us a <a href="%s" target="_blank">&#9733;&#9733;&#9733;&#9733;&#9733;</a> rating. Your Review is very important to us as it helps us to grow more. ', 'post-carousel' ), $url );
		}

		return $text;
	}


}

new SP_Post_Carousel_Functions();
