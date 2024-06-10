<?php
/**
 * Provides the 'Resources' view for the corresponding tab in the Shortcode Meta Box.
 *
 * @since 2.0
 *
 * @package    post-carousel
 */
?>

<div id="sp-pc-tab-1" class="sp-pc-mbf-tab-content nav-tab-active">

	<?php
	$this->metaboxform->select_layout(
		array(
			'id'      => 'sp_posts_layout',
			'name'    => __( 'Layout', 'post-carousel' ),
			'desc'    => __( 'Select which layout you want to display.', 'post-carousel' ),
			'default' => 'carousel',
		)
	);
	$this->metaboxform->select(
		array(
			'id'      => 'pc_themes',
			'name'    => __( 'Select Theme', 'post-carousel' ),
			'desc'    => __( 'Select which theme you want to display.', 'post-carousel' ),
			'options' => array(
				'carousel_one' => __( 'Theme One', 'post-carousel' ),
				'carousel_two' => __( 'Theme Two', 'post-carousel' ),
			),
			'default' => 'carousel_one',
		)
	);
	$this->metaboxform->select_posts_from(
		array(
			'id'      => 'pc_posts_from',
			'name'    => __( 'Display Posts From', 'post-carousel' ),
			'desc'    => __( 'Select an option to display the posts.', 'post-carousel' ),
			'default' => 'latest',
		)
	);
	$this->metaboxform->number(
		array(
			'id'      => 'pc_number_of_total_posts',
			'name'    => __( 'Total Posts', 'post-carousel' ),
			'desc'    => __( 'Number of Total posts to show. Default value is 12.', 'post-carousel' ),
			'default' => 12,
		)
	);
	$this->metaboxform->number(
		array(
			'id'      => 'pc_number_of_column',
			'name'    => __( 'Post Column(s)', 'post-carousel' ),
			'desc'    => __( 'Set number of posts column for the screen larger than 1100px.', 'post-carousel' ),
			'default' => 4,
		)
	);
	$this->metaboxform->number(
		array(
			'id'      => 'pc_number_of_column_desktop',
			'name'    => __( 'Post Column(s) on Desktop', 'post-carousel' ),
			'desc'    => __( 'Set number of column on desktop for the screen smaller than 1100px.', 'post-carousel' ),
			'default' => 3,
		)
	);
	$this->metaboxform->number(
		array(
			'id'      => 'pc_number_of_column_tablet',
			'name'    => __( 'Post Column(s) on Tablet', 'post-carousel' ),
			'desc'    => __( 'Set number of column on tablet for the screen smaller than 990px.', 'post-carousel' ),
			'default' => 2,
		)
	);
	$this->metaboxform->number(
		array(
			'id'      => 'pc_number_of_column_mobile',
			'name'    => __( 'Post Column(s) on Mobile', 'post-carousel' ),
			'desc'    => __( 'Set number of column on mobile for the screen smaller than 650px.', 'post-carousel' ),
			'default' => 1,
		)
	);
	$this->metaboxform->select(
		array(
			'id'      => 'pc_posts_order_by',
			'name'    => __( 'Order By', 'post-carousel' ),
			'desc'    => __( 'Select an order by option.', 'post-carousel' ),
			'options' => array(
				'title'    => __( 'Title', 'post-carousel' ),
				'date'     => __( 'Date', 'post-carousel' ),
				'modified' => __( 'Modified', 'post-carousel' ),
			),
			'default' => 'date',
		)
	);
	$this->metaboxform->select(
		array(
			'id'      => 'pc_posts_order',
			'name'    => __( 'Order', 'post-carousel' ),
			'desc'    => __( 'Set post order.', 'post-carousel' ),
			'options' => array(
				'ASC'  => __( 'Ascending', 'post-carousel' ),
				'DESC' => __( 'Descending', 'post-carousel' ),
			),
			'default' => 'DESC',
		)
	);

	?>

</div>
