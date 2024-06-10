<?php
/**
 * @package WordPress
 * @subpackage Default_Theme
 */

get_header(); ?>
<div class="container">
	<div class="row">
		<div class="col-sm-12">
			<?php if ( function_exists('yoast_breadcrumb') ) {
			yoast_breadcrumb('<p id="breadcrumbs">','</p>');
			} ?>
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
								<div class="price-card-package">

									<!-- JZ Tourplan Addition -->
									<form action="<?php echo site_url('book'); ?>" method="GET">
										<input type="hidden" value="<?php the_sub_field('code'); ?>" name="code" />
									<!-- JZ Tourplan Addition End -->

									<h1 class="package-title"><?php the_sub_field('package_title') ?></h1>
					        		<div class="price adult">
					        			<p class="age">Adult (12+)<p>
					        			<p class="amount"><span class="symbol">$</span><?php the_sub_field('package_price_-_adult'); ?></p>

		        						<!-- JZ Tourplan Addition -->
		        						<select class="passenger-counter" id="<?php the_sub_field('code'); ?>-adult" name="adults" onchange="checkForm('<?php the_sub_field('code'); ?>');">
	        							<?php
	        								for ($i = 0; $i <= 12; $i++) { ?>
	        									<option value="<?php echo $i; ?>">
	        										<?php echo $i; ?>
	        									</option>
	        								<?php
	        								}
		        						?>	
		        						</select>
		        						<!-- JZ Tourplan Addition End -->

					        			<i class="fa fa-caret-down down-arrow left"></i>
					        		</div>
		        					<div class="price child">
		        						<p class="age">Child (6-11)<p>
		        						<p class="amount"><span class="symbol">$</span><?php the_sub_field('package_price_-_child'); ?></p>

		        						<!-- JZ Tourplan Addition -->
		        						<select class="passenger-counter" id="<?php the_sub_field('code'); ?>-child" name="children" onchange="checkForm('<?php the_sub_field('code'); ?>');">
	        							<?php
	        								for ($i = 0; $i <= 12; $i++) { ?>
	        									<option value="<?php echo $i; ?>">
	        										<?php echo $i; ?>
	        									</option>
	        								<?php
	        								}
		        						?>	
		        						</select>
		        						<!-- JZ Tourplan Addition End -->

		        						<i class="fa fa-caret-down down-arrow right"></i>
		        					</div>

					        		<!-- JZ Tourplan Addition -->
				        				<input type="submit" id="<?php the_sub_field('code'); ?>-submit" class="buy-now" value="Buy Now" disabled/>
					        		</form>
					        		<!-- JZ Tourplan Addition End -->
								 
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
<?php get_footer(); ?>
