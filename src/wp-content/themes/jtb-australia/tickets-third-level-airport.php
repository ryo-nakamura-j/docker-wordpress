<?php
/**
 * @package WordPress
 * @subpackage Default_Theme
 * Template Name: Airport Transfers - Third Level
 */

get_header(); ?>

<div class="container tickets-third airport">

	<div class="row">

		<div class="col-sm-12">
			<?php if ( function_exists('yoast_breadcrumb') ) {
			yoast_breadcrumb('<p id="breadcrumbs">','</p>');
			} ?>
		</div>

		<div class="col-sm-12">
			<div class="banner">
				<div class="ribon-red-desktop"></div>
				<h2><?php the_title(); ?></h2>
			</div>
		</div>

		<div class="col-sm-12 top-section">
			<?php the_field('top_section_short_description') ?>
		</div>

		<div class="col-sm-12">
			<div class="ticket-information">
				<div class="ribon-red-desktop"></div>
				<h3><i class="fa fa-ticket"></i> Transfer Information</h3>
				<div id="product_content" class="tpproduct-tickets">
				    <div id="suppliersection">
				    </div>
				    <div id="products_section">
				            <div id="productavailabilitysection">
				            </div>
				            <div id="productssection" class="productssection-tickets">
				            </div>
				    </div>

				</div>
			</div>
		</div>		
		<div class="col-sm-12">
			<div class="main-central-image">
				<?php the_field('bottom_section_full_description') ?>
			</div>
		</div>		

		<div class="col-sm-12">
			<div class="ribon-red-desktop"></div>
		</div>
	</div>
</div><!-- .container ends -->

<?php get_footer(); ?>
