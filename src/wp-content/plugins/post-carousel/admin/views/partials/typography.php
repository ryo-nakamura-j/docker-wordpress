<?php
/**
 * This file is to display typography.
 *
 *  @since 2.1.3
 * @package post-carousel
 */
?>
<div id="sp-pc-tab-4" class="sp-pc-mbf-tab-content sp-pc-mbf-tab-typography">
	<div class="sp-pcpro-notice">These Typography (840+ Google Fonts) options are available in the <b><a href="https://shapedplugin.com/plugin/post-carousel-pro" target="_blank">Pro Version</a></b>.</div>
	<?php
	$this->metaboxform->typography_type(
		array(
			'id'   => 'pc_carousel_title_font',
			'name' => __( 'Carousel Title Font', 'post-carousel' ),
			'desc' => __( 'Set carousel title font properties.', 'post-carousel' ),
		)
	);
	$this->metaboxform->typography_type(
		array(
			'id'   => 'pc_post_title_font',
			'name' => __( 'Post Title Font', 'post-carousel' ),
			'desc' => __( 'Set post title font properties.', 'post-carousel' ),
		)
	);
	$this->metaboxform->typography_type(
		array(
			'id'   => 'pc_sticky_post_font',
			'name' => __( 'Sticky Post Ribbon Font', 'post-carousel' ),
			'desc' => __( 'Set sticky post ribbon font properties.', 'post-carousel' ),
		)
	);
	$this->metaboxform->typography_type(
		array(
			'id'   => 'pc_post_content_font',
			'name' => __( 'Post Content Font', 'post-carousel' ),
			'desc' => __( 'Set post content font properties.', 'post-carousel' ),
		)
	);
	$this->metaboxform->typography_type(
		array(
			'id'   => 'pc_post_meta_font',
			'name' => __( 'Post Meta Font', 'post-carousel' ),
			'desc' => __( 'Set post meta font properties.', 'post-carousel' ),
		)
	);
	$this->metaboxform->typography_type(
		array(
			'id'   => 'pc_post_meta_font',
			'name' => __( 'Post Content ReadMore Font', 'post-carousel' ),
			'desc' => __( 'Set post content readmore font properties.', 'post-carousel' ),
		)
	);
	$this->metaboxform->typography_type(
		array(
			'id'   => 'pc_filter_font',
			'name' => __( 'Filter Font', 'post-carousel' ),
			'desc' => __( 'Set filter font properties.', 'post-carousel' ),
		)
	);
	$this->metaboxform->typography_type(
		array(
			'id'   => 'pc_loadmore_font',
			'name' => __( 'Load More Font', 'post-carousel' ),
			'desc' => __( 'Set load more font properties..', 'post-carousel' ),
		)
	);
	?>
</div>
