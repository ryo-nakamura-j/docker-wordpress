<?php
/**
 * Provides the 'Resources' view for the corresponding tab in the Shortcode Meta Box.
 *
 * @since 2.0
 *
 * @package    post-carousel
 */
?>

<div id="sp-pc-tab-2" class="sp-pc-mbf-tab-content">
	<?php
	$this->metaboxform->checkbox(
		array(
			'id'      => 'pc_auto_play',
			'name'    => __( 'AutoPlay', 'post-carousel' ),
			'desc'    => __( 'Check to on autoplay carousel.', 'post-carousel' ),
			'default' => 'on',
		)
	);
	$this->metaboxform->number(
		array(
			'id'      => 'pc_auto_play_speed',
			'name'    => __( 'AutoPlay Speed', 'post-carousel' ),
			'desc'    => __( 'Set autoplay speed.', 'post-carousel' ),
			'after'   => __( '(Millisecond)', 'post-carousel' ),
			'default' => 3000,
		)
	);
	$this->metaboxform->checkbox(
		array(
			'id'      => 'pc_pause_on_hover',
			'name'    => __( 'Pause on Hover', 'post-carousel' ),
			'desc'    => __( 'Check to activate pause on hover.', 'post-carousel' ),
			'default' => 'on',
		)
	);
	$this->metaboxform->checkbox(
		array(
			'id'      => 'pc_show_navigation',
			'name'    => __( 'Navigation', 'post-carousel' ),
			'desc'    => __( 'Check to show navigation arrows.', 'post-carousel' ),
			'default' => 'on',
		)
	);
	$this->metaboxform->color(
		array(
			'id'      => 'pc_nav_arrow_color',
			'type'    => 'color',
			'name'    => __( 'Navigation Color	', 'post-carousel' ),
			'desc'    => __( 'Pick a color for navigation arrows.', 'post-carousel' ),
			'default' => '#ffffff',
		)
	);
	$this->metaboxform->color(
		array(
			'id'      => 'pc_nav_arrow_bg',
			'type'    => 'color',
			'name'    => __( 'Navigation Background	', 'post-carousel' ),
			'desc'    => __( 'Pick a color for navigation arrows background.', 'post-carousel' ),
			'default' => '#e96443',
		)
	);
	$this->metaboxform->checkbox(
		array(
			'id'      => 'pc_show_pagination_dots',
			'name'    => __( 'Pagination Dots', 'post-carousel' ),
			'desc'    => __( 'Check to show pagination dots.', 'post-carousel' ),
			'default' => 'on',
		)
	);
	$this->metaboxform->color(
		array(
			'id'      => 'pc_pagination_color',
			'type'    => 'color',
			'name'    => __( 'Pagination Color	', 'post-carousel' ),
			'desc'    => __( 'Pick a color for pagination dots.', 'post-carousel' ),
			'default' => '#cccccc',
		)
	);
	$this->metaboxform->color(
		array(
			'id'      => 'pc_pagination_active_color',
			'type'    => 'color',
			'name'    => __( 'Pagination Active Color	', 'post-carousel' ),
			'desc'    => __( 'Pick a color for pagination active dots.', 'post-carousel' ),
			'default' => '#333333',
		)
	);
	$this->metaboxform->number(
		array(
			'id'      => 'pc_scroll_speed',
			'name'    => __( 'Pagination Speed', 'post-carousel' ),
			'desc'    => __( 'Set pagination/slide scroll speed.', 'post-carousel' ),
			'after'   => __( '(Millisecond).', 'post-carousel' ),
			'default' => 450,
		)
	);
	$this->metaboxform->checkbox(
		array(
			'id'      => 'pc_touch_swipe',
			'name'    => __( 'Touch Swipe', 'post-carousel' ),
			'desc'    => __( 'Check to on touch swipe.', 'post-carousel' ),
			'default' => 'on',
		)
	);
	$this->metaboxform->checkbox(
		array(
			'id'      => 'pc_mouse_draggable',
			'name'    => __( 'Mouse Draggable', 'post-carousel' ),
			'desc'    => __( 'Check to on mouse draggable.', 'post-carousel' ),
			'default' => 'on',
		)
	);
	$this->metaboxform->checkbox(
		array(
			'id'      => 'pc_rtl',
			'name'    => __( 'RTL Mode', 'post-carousel' ),
			'desc'    => __( 'Check and Set a RTL language from admin settings to make the rtl option work.', 'post-carousel' ),
			'default' => 'off',
		)
	);
	?>
</div>
