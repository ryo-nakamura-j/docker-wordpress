<?php
/**
 * @package WordPress
 * @subpackage Default_Theme
 * Template Name: Tour Product Page
 */

get_header(); ?>

	<section id="content" class="container">
		<div class="row">
			<div class="col-sm-12">
				<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
				<div class="post">
					<div class="entry">
						<?php the_field('content'); ?>
						<div id="myTarget"></div>
					</div>
				</div>
				<?php endwhile; endif; ?>
			</div>

	 		<div class="page-top col-sm-12">
				<div class="ribon-red-desktop"></div>
			</div>
		</div>

		<!-- <div class="row">
			<div class="col-xs-12">
	   			<div class="ribon-red-desktop"></div>
	   		</div>
	   	</div> -->
	   	
	</section>


	<script id="tour-product-template" type="text/x-handlebars-template">
		<?php echo get_template_part('templates/tour', 'product'); ?>
	</script>

	<script id="tour-product-price-panel-template" type="text/x-handlebars-template">
		<?php echo get_template_part('templates/tour', 'product-price-panel'); ?>
	</script>

<?php get_footer(); ?>
