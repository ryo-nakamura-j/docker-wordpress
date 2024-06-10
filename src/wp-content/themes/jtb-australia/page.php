<?php
/**
 * @package WordPress
 * @subpackage Default_Theme
 */

get_header(); ?>

	<section id="content" class="container">
		<div class="row">
			<div class="col-sm-12">
				<?php if ( function_exists('yoast_breadcrumb') ) {
				yoast_breadcrumb('<p id="breadcrumbs">','</p>');
				} ?>
			</div>
			<div class="col-sm-12">
				<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
				<div class="post">
					<h1><?php the_title(); ?></h1>
					<div class="ribon-red-desktop"></div>
					<div class="entry">
						<?php the_field('content'); 
					the_content();   ?>
					</div>
				</div>
				<?php endwhile; endif; ?>
			</div>
		</div>
	</section>

<?php get_footer(); ?>
