<?php
/**
 * @package WordPress
 * @subpackage Default_Theme
 * Template name: Japan Rail Pass
 * http://jtb.metadev.nz/en/japan-rail-pass/
 */

get_header(); ?>

<script>
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

<div class="japan rail-pass container">
	<div class="row">
		<div class="col-sm-12">
			<h3>National Rail Pass</h3>
			<div class="ribon-red-desktop"></div>
			<?php the_field('introduction', 374); ?>
		</div>
		<?php
		$count = 1;

		// check if the repeater field has rows of data
		if( have_rows('national_pass_package', 374) ):
		 
		 	// loop through the rows of data
		    while ( have_rows('national_pass_package', 374) ) : the_row();
		 	$backgroundColor = get_sub_field('theme_colour', 374);

		        // display a sub field value
				?>

				<div class="col-sm-6 outer">
					<div class="price-card">
						<h2 class="rail-pass-name" style="background:<?php the_sub_field('theme_colour', 374); ?>; color:<?php the_sub_field('header_color', 374); ?>;"><span><?php the_sub_field('rail_pass_name', 374); ?></span><i class="fa fa-train"></i></h2>
						<div class="price-chart" style="background:<?php echo $backgroundColor; ?>;">
							<div class="price-card-packages">
		        				<?php
								// check if the repeater field has rows of data
								if( have_rows('package_prices', 374) ):
								 
							 	// loop through the rows of data
							    while ( have_rows('package_prices', 374) ) : the_row();
							 
						        // display a sub field value
								?>
								<div class="price-card-package <?php echo str_replace(' ', '-', get_sub_field('code')); ?>">
									<span class="wp-price" style="display:none"><?php 
										$adult = get_sub_field('package_price_-_adult'); $child = get_sub_field('package_price_-_child'); $currency = get_sub_field('currency');
										$prices = array();
										if (!empty($adult)) { $prices['adult'] = $adult; }
										if (!empty($child)) { $prices['child'] = $child; }
										if (!empty($currency)) { $prices['currency'] = $currency; };
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

		<?php
		
		endwhile;
		else :
		 
		    // no rows found
		endif;
		?>
	 	<div class="page-top col-sm-12">
			<p><a class="more-info" href="<?php the_field('national_rail_pass_more_info_link', 406) ?>"><span class="red">►</span>&nbsp;More Information</a></p>
		</div>
		<div class="col-sm-12">
			<div class="national rail-pass description">
				<div class="upper-box">
					<?php the_field('upper_content_box', 374) ?>
				</div>
				<div class="lower-box">
					<?php the_field('lower_content_box', 374) ?>
				</div>
			</div>
		</div>	
	</div>
</div>

<div class="container">
	<div class="row">
		<div class="col-sm-12">
			<div class="japan rail-pass">
				<h3>Regional Rail Pass</h3>
				<div class="ribon-red-desktop"></div>
				<?php the_field('regional_rail_pass_intro', 406); ?>
			</div>
		</div>
		<div class="col-sm-12">
			<div class="regional-rail-pass">
			<?php
			// check if the repeater field has rows of data
			if( have_rows('regional_rail_pass_section', 406) ):
			 
			 	// loop through the rows of data
			    while ( have_rows('regional_rail_pass_section', 406) ) : the_row();
			 
			        // display a sub field value
					?>

					  <div class="col-sm-4">
						<div class="single">
							<div><a href="<?php the_sub_field('rail_pass_link', 406) ?>">
								<h4 style="background:<?php the_sub_field('rail_pass_colour'); ?>; "><?php the_sub_field('rail_pass_name', 406) ?></h4></a>
							</div>
					  		<a href="<?php the_sub_field('rail_pass_link', 406) ?>"><img class="img-responsive" src="<?php the_sub_field('rail_pass_map', 406) ?>" alt="..."></a>
						</div>
					</div>
					<?php ;
			    endwhile;
			else :
			    // no rows found
			endif; ?>
			</div>
		 	<div class="page-top">
				<p><a class="more-info" href="<?php the_field('other_rail_passes_link', 406) ?>"><i class="fa fa-train"></i>&nbsp;Other Rail Passes</a></p>
			</div>
		</div>
	</div>
	<div class="row"> 
		<div class="col-sm-12">
			<h3>WHAT'S A JR PASS?</h3>
			<div class="ribon-red-desktop"></div>
				<?php the_field('whats_a_jr_pass_content', 406) ?>
		</div>
		<div class="col-sm-12">		
			<h3>WHY TAKE A JR PASS?</h3>
			<div class="ribon-red-desktop"></div>
				<?php the_field('why_take_a_jr_pass_content', 406) ?>
		</div>
		<div class="page-top col-sm-12">
			<p><img src="<?php bloginfo('template_directory'); ?>/images/top-btn.png" alt="gotop">&nbsp;<a href="#top" style="text-decoration: underline;">Page Top</a></p>
		</div>
		<div class="page-top col-sm-12">
			<div class="ribon-red-desktop"></div>
		</div>
	</div>
</div>


<script id="rail-pass-template" type="text/x-handlebars-template">
	<?php include("templates/rail-pass-template.php"); ?>
</script>

<?php get_footer(); ?>
