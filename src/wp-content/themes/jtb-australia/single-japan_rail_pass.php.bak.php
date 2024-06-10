<?php
/**
 * @package WordPress
 * @subpackage Default_Theme
 */

get_header(); ?>

<script>
$(document).ready(function(){
   $('.show_hide').showHide({
        speed: 300
    });
});
</script>


<div class="container">
	<div class="row">
		<div class="col-sm-12">
			<?php if ( function_exists('yoast_breadcrumb') ) {
			yoast_breadcrumb('<p id="breadcrumbs">','</p>');
			} ?>
		</div>
	</div>
</div>

<section id="index" class="regional rail-pass container">
	<div class="row">
	<?php
	
	$count = 1;

	// check if the repeater field has rows of data
	if( have_rows('rail_pass_package') ):
	 
	 	// loop through the rows of data
	    while ( have_rows('rail_pass_package') ) : the_row();
	 	$backgroundColor = get_sub_field('theme_colour');

	        // display a sub field value
			?>


		<?php if (have_posts()) : ?>

			<?php while (have_posts()) : the_post(); ?>

				<div class="col-sm-8">
					<?php 
					if( get_sub_field('centre_image') ) { ?>
					<div class="col-sm-8 with-centre-image">
					<?php }
					else {?>
					<div class="col-sm-12">
					<?php }
					?>

						<div class="price-card">
							<h2 class="rail-pass-name" style="background:<?php the_sub_field('theme_colour'); ?>;"><img src="/wp-content/themes/jtb-singapore/images/logo-white.png" /><span><?php the_sub_field('rail_pass_name'); ?></span></h2>
							<div class="price-chart" style="background:<?php echo $backgroundColor; ?>;">
								<div class="price-card-packages row">
				        			<?php
									// check if the repeater field has rows of data
									if( have_rows('package_prices') ):
									 
									 	// loop through the rows of data
									    while ( have_rows('package_prices') ) : the_row();
									 
									        // display a sub field value
											?>
											<div class="price-card-package">
												<h1 class="package-title"><?php the_sub_field('package_title') ?></h1>
						        					<div class="price adult">
						        						<p class="age">Adult (12+)<p>
						        						<p class="amount"><span class="symbol">$</span><?php the_sub_field('package_price_-_adult'); ?></p>
						        						<i class="fa fa-caret-down down-arrow left"></i>
						        					</div>
						        					<div class="price child">
						        						<p class="age">Child (6-11)<p>
						        						<p class="amount"><span class="symbol">$</span><?php the_sub_field('package_price_-_child'); ?></p>
						        						<i class="fa fa-caret-down down-arrow right"></i>
						        					</div>

						        				<a class="buy-now" href="<?php the_sub_field('buy_now_link'); ?>">Buy Now</a>
						        			</div>

					        				<?php ;
									 
									    endwhile;
									else :
									 
									    // no rows found
									endif;
									 
									?>
								</div>
		                	</div>
		                </div>
           			</div>
					<?php if( get_sub_field('centre_image') ): ?>
					<div class="col-sm-3">
	        			<div class="centre-image">
	        				<img src="<?php the_sub_field('centre_image'); ?>" alt="" />
				        </div>
				    </div>
					<?php endif; ?>
					<div class="col-sm-12">
	       			    <div class="pass-description">
	                		<?php the_sub_field('rail_pass_description'); ?>
	                	</div>
	                </div>
           		</div>


	       		<div class="col-sm-4">
	       			<div class="maps">
	                	<img class="map-national-view" src="<?php the_sub_field('map_-_national_view'); ?>" />
	                	<img class="map-regional-view" src="<?php the_sub_field('map_-_regional_view'); ?>" />
	                </div>
	       		</div>

			<?php endwhile; ?>


		<?php else : ?>

			<h2 class="center">Not Found</h2>
			<p class="center">Sorry, but you are looking for something that isn't here.</p>
			<?php get_search_form(); ?>

		<?php endif; ?>

		<div class="col-sm-12">
			<div class="terms-conditions">
				<h3><a class="show_hide" href="#" rel="#slidingDiv-<?php echo $count;?>">Terms and Conditions</a></h3>
				<div id="slidingDiv-<?php echo $count;?>" class="inner">
        			<?php
					// check if the repeater field has rows of data
					if( have_rows('terms_and_conditions') ):
					 
					 	// loop through the rows of data
					    while ( have_rows('terms_and_conditions') ) : the_row();
					 
					        // display a sub field value
							?>
							
							<h4 style="background-color: <?php echo $backgroundColor; ?>;"><?php the_sub_field('header') ?></h4>
	        				<?php the_sub_field('content'); ?>

	        				<?php ;
					 
					    endwhile;
					else :
					 
					    // no rows found
					endif;
					 
					?>
				</div>
			</div>
			<div class="ribon-red-desktop"></div>
		</div>
	<?php

	$count++;
	
	endwhile;
	else :
	 
	    // no rows found
	endif;
	 
	?>
	</div>
</section>

<?php get_footer(); ?>
