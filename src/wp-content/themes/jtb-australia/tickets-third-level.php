<?php
/**
 * @package WordPress
 * @subpackage Default_Theme
 * Template Name: Products - Third Level
 */

get_header(); ?>

<div class="container tickets-third">

	<div class="row">

		<div class="col-sm-12">
			<?php if ( function_exists('yoast_breadcrumb') ) {
			yoast_breadcrumb('<p id="breadcrumbs">','</p>');
			} ?>
		</div>

		<div class="col-sm-12">
			<div class="banner">
				<div class="ribon-red-desktop"></div>
				<img src="<?php the_field('banner') ?>" alt="" />
			</div>
		</div>

		<div class="col-sm-12 top-section">
			<div class="col-sm-4">
				<div class="left">
					<img src="<?php the_field('top_section_left_image') ?>" alt="" />
				</div>
			</div>
			<div class="col-sm-8">
				<div class="right">
					<?php the_field('top_section_right_content') ?>
				</div>
			</div>
		</div>

		<div class="col-sm-12">
			<div class="ticket-information">
				<div class="ribon-red-desktop"></div>
				<h3><i class="fa fa-ticket"></i> Ticket Information</h3>
				<div id="product_content" class="tpproduct-tickets">
				 	<div id="searchSection"></div>
				 	<div class="productWrapper">
				 		<div id="productSection"><img src=""/></div>
				 	</div>
				</div>
			</div>
		</div>		
		<div class="col-sm-12">
			<div class="main-central-image">
				<div class="ribon-red-desktop"></div>
				<img src="<?php the_field('main_central_image') ?>" alt="" />
			</div>
		</div>		

		<div class="col-sm-12">
			<div class="ribon-red-desktop"></div>
		</div>
	</div>
</div><!-- .container ends -->

<script id="ticket-search-template" type="text/x-handlebars-template">
	<?php include("templates/ticket-search.php"); ?>
</script>

<script id="ticket-product-template" type="text/x-handlebars-template">
	<?php include("templates/ticket-product.php"); ?>
</script>

<?php get_footer(); ?>
