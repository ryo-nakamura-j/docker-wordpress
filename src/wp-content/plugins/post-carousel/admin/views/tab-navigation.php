<?php
/**
 * This file display meta box tab
 *
 * @package post-carousel
 */

$current_screen        = get_current_screen();
$the_current_post_type = $current_screen->post_type;
if ( $the_current_post_type == 'sp_pc_shortcodes' ) {
	?>
	<div class="sp-pc-metabox-framework">
		<div class="sp-pc-mbf-banner">
			<div class="sp-pc-mbf-logo"><img src="<?php echo SP_PC_URL; ?>admin/assets/images/post-carousel.png" alt="Post Carousel"></div>
			<div class="sp-pc-mbf-short-links">
				<a href="https://shapedplugin.com/docs/post-carousel/" target="_blank"><i class="sp-pc-font-icon sp-pc-icon-doc-text-inv"></i> Docs</a>
				<a href="https://shapedplugin.com/support-forum/" target="_blank"><i class="sp-pc-font-icon sp-pc-icon-lifebuoy"></i> Support</a>
			</div>
		</div>
		<div class="sp-pc-mbf text-center">

			<div class="sp-pc-col-lg-3">
				<div class="sp-pc-mbf-shortcode">
					<h2 class="sp-pc-mbf-shortcode-title"><?php _e( 'Shortcode', 'post-carousel' ); ?> </h2>
					<p>
					<?php
					_e( 'Copy and paste this shortcode into your posts or pages:', 'post-carousel' );
						global $post;
					?>
						</p>
					<div class="pc-sc-code selectable" >[post-carousel <?php echo 'id="' . $post->ID . '"'; ?>]</div>
				</div>


			</div>
			<div class="sp-pc-col-lg-3">
				<div class="sp-pc-mbf-shortcode">
					<h2 class="sp-pc-mbf-shortcode-title"><?php _e( 'Template Include', 'post-carousel' ); ?> </h2>

					<p><?php _e( 'Paste the PHP code into your template file:', 'post-carousel' ); ?></p>
					<div class="pc-sc-code selectable">
						&lt;?php
						post_carousel_id('<?php echo $post->ID; ?>');
						?&gt;</div>
				</div>
			</div>
			<div class="sp-pc-col-lg-3">
				<div class="sp-pc-mbf-shortcode">
					<h2 class="sp-pc-mbf-shortcode-title"><?php _e( 'Post or Page editor', 'post-carousel' ); ?> </h2>

					<p><?php _e( 'Insert it into an existing post or page with the icon:', 'post-carousel' ); ?></p>
					<img class="back-image"
						 src="<?php echo SP_PC_URL . 'admin/assets/images/pc-tiny-mce.png'; ?>"
						 alt="">
				</div>
			</div>

		</div>
		<div class="sp-pc-shortcode-divider"></div>

		<div class="sp-pc-mbf-nav nav-tab-wrapper current">
			<a class="nav-tab nav-tab-active" data-tab="sp-pc-tab-1"><i class="sp-pc-font-icon sp-pc-icon-wrench"></i>General Settings</a>
			<a class="nav-tab" data-tab="sp-pc-tab-2"><i class="sp-pc-font-icon sp-pc-icon-sliders"></i>Carousel Settings</a>
			<a class="nav-tab" data-tab="sp-pc-tab-3"><i class="sp-pc-font-icon sp-pc-icon-brush"></i>Stylization</a>
			<a class="nav-tab" data-tab="sp-pc-tab-4"><i class="sp-pc-font-icon sp-pc-icon-font"></i>Typography</a>
			<a class="nav-tab sp-pc-upgrade-to-pro" data-tab="sp-pc-tab-5"><i class="sp-pc-font-icon sp-pc-icon-rocket"></i>Upgrade to Pro</a>
		</div>

		<?php
		include_once 'partials/general-settings.php';
		include_once 'partials/carousel-settings.php';
		include_once 'partials/stylization.php';
		include_once 'partials/typography.php';
		include_once 'partials/upgrade-to-pro.php';
		?>
	</div>
	<?php
}
