<?php
/**
 * @package WordPress
 * @subpackage Default_Theme
 * Template name: Itinerary
 */

get_header(); ?>

	<section id="content" class="itinerary container">
		<div class="row">
			<div class="col-sm-12">
				<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
				<div class="post">
				<h1><?php the_title(); ?></h1>
					<div class="entry">
						<?php the_field('content'); ?>
					</div>
				</div>
				<?php endwhile; endif; ?>
			</div>

	 		<div class="page-top col-sm-12">
				<div class="ribon-red-desktop"></div>
			</div>
		</div>
	</section>


<?php get_footer(); ?>
