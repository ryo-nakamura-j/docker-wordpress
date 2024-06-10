<?php
/**
 * @package WordPress
 * @subpackage Default_Theme
 * Template name: National Rail Pass - Archive
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

	<div class="home-content four grid container">
		<h3>National Rail Pass</h3>
		<div class="ribon-red-desktop"></div>
		<div class="row"> 
		<div class="col-sm-12">
			<?php the_field('national_rail_pass_description'); ?>
		</div>
			<!-- Four grid -->

			<?php
			// check if the repeater field has rows of data
			if( have_rows('four_grid_boxes', 374) ):
			 
			 	// loop through the rows of data
			    while ( have_rows('four_grid_boxes', 374) ) : the_row();
			 
			        // display a sub field value
					?>

					  <div class="col-sm-3 col-xs-12">
						<div class="thumbnail">
							<div class="caption"><a href="<?php the_sub_field('box_link') ?>">
								<h4><?php the_sub_field('box_title') ?></h4></a>
							</div>
					  		<a href="<?php the_sub_field('box_link') ?>"><img src="<?php the_sub_field('box_image') ?>" alt="..."></a>
						</div>
					</div>
					<?php ;
			    endwhile;
			else :
			    // no rows found
			endif; ?>
		 
			   
			<!-- End of Three Grid -->
		</div>
		<div class="row">
	 		<div class="page-top col-sm-12">
				<p><img src="<?php bloginfo('template_directory'); ?>/images/top-btn.png" alt="gotop">&nbsp;<a href="#top" style="text-decoration: underline;">Page Top</a></p>
			</div>
		</div>
	</div>
	<div class="home-content three grid container">
		<h3>Japan Information</h3>
		<div class="ribon-red-desktop"></div>
		<div class="row"> 

			<!-- Three grid -->

			<?php
			// check if the repeater field has rows of data
			if( have_rows('three_grid_boxes', 374) ):
			 
			 	// loop through the rows of data
			    while ( have_rows('three_grid_boxes', 374) ) : the_row();
			 
			        // display a sub field value
					?>

					  <div class="col-sm-4 col-xs-12">
						<div class="thumbnail">
							<div class="caption"><a href="<?php the_sub_field('box_link') ?>">
								<h4><?php the_sub_field('box_title') ?></h4></a>
							</div>
					  		<a href="<?php the_sub_field('box_link') ?>"><img src="<?php the_sub_field('box_image') ?>" alt="..."></a>
							<div class="caption">
								<div class="row">
									<div class="col-sm-12 col-md-12">
									<?php the_sub_field('box_copy') ?>

									</div>
								</div>
							  </div>
						</div>
					</div>
					<?php ;
			    endwhile;
			else :
			    // no rows found
			endif; ?>
		 
			   
			<!-- End of Three Grid -->
		</div>
		<div class="row">
	 		<div class="page-top col-sm-12">
				<p><img src="<?php bloginfo('template_directory'); ?>/images/top-btn.png" alt="gotop">&nbsp;<a href="#top" style="text-decoration: underline;">Page Top</a></p>
			</div>
		</div>
	</div>

	<div class="home-content multi grid container">
		<h3>Featured Destinations</h3>
		<div class="ribon-red-desktop"></div>
		<div class="multi row one"> 

			<!-- Multi grid -->

			<?php
			// check if the repeater field has rows of data
			if( have_rows('multi_grid_boxes_row_1', 374) ):
			 
			 	// loop through the rows of data
			    while ( have_rows('multi_grid_boxes_row_1', 374) ) : the_row();
			 
			        // display a sub field value
					?>

				  	<div class="col-sm-4 multi">
						<a href="<?php the_sub_field('box_link') ?>"><img src="<?php the_sub_field('box_image') ?>" class="img-responsive" alt=""></a>
						<p class="flying-text"><?php the_sub_field('box_title') ?></p>
					</div>
					<?php ;
			    endwhile;
			else :
			    // no rows found
			endif; ?>
			
		</div>


		<div class="multi row two"> 

			<?php
			// check if the repeater field has rows of data
			if( have_rows('multi_grid_boxes_row_2', 374) ):
			 
			 	// loop through the rows of data
			    while ( have_rows('multi_grid_boxes_row_2', 374) ) : the_row();
			 
			        // display a sub field value
					?>

				  	<div class="col-sm-3 multi">
						<a href="<?php the_sub_field('box_link') ?>"><img src="<?php the_sub_field('box_image') ?>" class="img-responsive" alt=""></a>
						<p class="flying-text"><?php the_sub_field('box_title') ?></p>
					</div>
					<?php ;
			    endwhile;
			else :
			    // no rows found
			endif; ?>

		</div>
		

		<div class="multi row three"> 

			<?php
			// check if the repeater field has rows of data
			if( have_rows('multi_grid_boxes_row_3', 374) ):
			 
			 	// loop through the rows of data
			    while ( have_rows('multi_grid_boxes_row_3', 374) ) : the_row();
			 
			        // display a sub field value
					?>

				  	<div class="col-sm-2 multi">
						<a href="<?php the_sub_field('box_link') ?>"><img src="<?php the_sub_field('box_image') ?>" class="img-responsive" alt=""></a>
						<p class="flying-text"><?php the_sub_field('box_title') ?></p>
					</div>
					<?php ;
			    endwhile;
			else :
			    // no rows found
			endif; ?>
		 
			   
			<!-- End of Multi Grid -->
		</div>
		<div class="row">
	 		<div class="page-top col-sm-12">
				<p><img src="<?php bloginfo('template_directory'); ?>/images/top-btn.png" alt="gotop">&nbsp;<a href="#top" style="text-decoration: underline;">Page Top</a></p>
			</div>
		</div>
		<div class="row">
	 		<div class="page-top col-sm-12">
				<div class="ribon-red-desktop"></div>
			</div>
		</div>
	</div>

<?php get_footer(); ?>
