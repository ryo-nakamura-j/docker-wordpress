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

// JZ Tourplan Addition
var checkForm = function(productID) {
	// console.log(productID);
	var adultSelect = document.getElementById(productID + '-adult');
	var childSelect = document.getElementById(productID + '-child');
	var productSubmit = document.getElementById(productID + '-submit');
	if ((parseInt(adultSelect.value)  == 0) && (parseInt(childSelect.value)) == 0) {
		productSubmit.disabled = true;
	} else {
		productSubmit.disabled = false;
	}
}
// JZ Tourplan Addition End
</script>


<div class="container">
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
			</div>
			<?php endwhile; endif; ?>
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
							<h2 class="rail-pass-name" style="background:<?php the_sub_field('theme_colour'); ?>;">
								<img src="<?php echo get_template_directory_uri(); ?>/images/logo-white.png" />
							
								<span><?php the_sub_field('rail_pass_name'); ?></span>
							</h2>
							<div class="price-chart" style="background:<?php echo $backgroundColor; ?>;">
								<div class="price-card-packages row">
				        			<?php
									// check if the repeater field has rows of data
									if( have_rows('package_prices') ):
									 
									 	// loop through the rows of data
									    while ( have_rows('package_prices') ) : the_row();
									 
									        // display a sub field value
											?>

											<div class="price-card-package <?php echo str_replace(' ', '-', get_sub_field('code')); ?>">
												<span class="wp-price" style="display:none"><?php 
													$adult = get_sub_field('package_price_-_adult'); $child = get_sub_field('package_price_-_child'); $currency = get_sub_field('currency');
													$prices = array();
													if (!empty($adult)) { $prices['adult'] = $adult; }
													if (!empty($child)) { $prices['child'] = $child; }
													if (!empty($currency)) { $prices['currency'] = $currency; }
													echo json_encode($prices);
												?></span>
												<h1 class="package-title"><?php the_sub_field('package_title') ?></h1>
												<div class="price-cards"></div>
								        		<!-- </form> -->
											 
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
					<div class="col-sm-4">
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

<script id="rail-pass-template" type="text/x-handlebars-template">
	<?php include("templates/rail-pass-template.php"); ?>
</script>

<?php get_footer(); ?>
