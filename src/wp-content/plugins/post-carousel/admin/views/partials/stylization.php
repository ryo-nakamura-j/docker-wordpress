<?php
/**
 * Provides the 'Resources' view for the corresponding tab in the Shortcode Meta Box.
 *
 * @since 2.0
 *
 * @package    post-carousel
 */
?>

<div id="sp-pc-tab-3" class="sp-pc-mbf-tab-content">
	<?php
	$this->metaboxform->checkbox(
		array(
			'id'      => 'pc_carousel_title',
			'name'    => __( 'Carousel Title', 'post-carousel' ),
			'desc'    => __( 'Check to display the shortcode name as carousel title.', 'post-carousel' ),
			'default' => 'off',
		)
	);
	$this->metaboxform->color(
		array(
			'id'      => 'pc_carousel_title_color',
			'type'    => 'color',
			'name'    => __( 'Carousel Title Color', 'post-carousel' ),
			'desc'    => __( 'Set carousel title color.', 'post-carousel' ),
			'default' => '#333333',
		)
	);
	$this->metaboxform->checkbox(
		array(
			'id'      => 'pc_post_title',
			'name'    => __( 'Post Title', 'post-carousel' ),
			'desc'    => __( 'Check to show post title.', 'post-carousel' ),
			'default' => 'on',
		)
	);
	$this->metaboxform->color(
		array(
			'id'      => 'pc_post_title_color',
			'type'    => 'color',
			'name'    => __( 'Post Title Color', 'post-carousel' ),
			'desc'    => __( 'Set post title color.', 'post-carousel' ),
			'default' => '#333333',
		)
	);
	$this->metaboxform->color(
		array(
			'id'      => 'pc_post_title_hover_color',
			'type'    => 'color',
			'name'    => __( 'Post Title Hover Color', 'post-carousel' ),
			'desc'    => __( 'Set post title hover color.', 'post-carousel' ),
			'default' => '#e44646',
		)
	);
	$this->metaboxform->select(
		array(
			'id'      => 'pc_post_content',
			'name'    => __( 'Post Content', 'post-carousel' ),
			'desc'    => __( 'Select post content option.', 'post-carousel' ),
			'options' => array(
				'content_with_limit' => __( 'Content with limit', 'post-carousel' ),
				'full_content'       => __( 'Full Content', 'post-carousel' ),
				'hide'               => __( 'Hide', 'post-carousel' ),
			),
			'default' => 'content_with_limit',
		)
	);
	$this->metaboxform->color(
		array(
			'id'      => 'pc_post_content_color',
			'type'    => 'color',
			'name'    => __( 'Post Content Color', 'post-carousel' ),
			'desc'    => __( 'Set post content color.', 'post-carousel' ),
			'default' => '#333333',
		)
	);

	$this->metaboxform->checkbox(
		array(
			'id'      => 'pc_post_author',
			'name'    => __( 'Post Author Name', 'post-carousel' ),
			'desc'    => __( 'Check to show post author name.', 'post-carousel' ),
			'default' => 'on',
		)
	);
	$this->metaboxform->checkbox(
		array(
			'id'      => 'pc_post_date',
			'name'    => __( 'Post Date', 'post-carousel' ),
			'desc'    => __( 'Check to show post date.', 'post-carousel' ),
			'default' => 'on',
		)
	);
	$this->metaboxform->color(
		array(
			'id'      => 'pc_post_meta_color',
			'type'    => 'color',
			'name'    => __( 'Post Meta Color', 'post-carousel' ),
			'desc'    => __( 'Set post meta color.', 'post-carousel' ),
			'default' => '#333333',
		)
	);
	$this->metaboxform->color(
		array(
			'id'      => 'pc_post_meta_hover_color',
			'type'    => 'color',
			'name'    => __( 'Post Meta Hover Color', 'post-carousel' ),
			'desc'    => __( 'Set post meta hover color.', 'post-carousel' ),
			'default' => '#e44646',
		)
	);
	?>
</div>
