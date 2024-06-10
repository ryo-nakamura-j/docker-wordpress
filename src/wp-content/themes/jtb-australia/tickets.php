<?php
/**
 * @package WordPress
 * @subpackage Default_Theme
 * Template Name: Tickets
 */

get_header(); ?>

<div class="container tickets">
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


	<div class="major-attractions three grid">
		<h3>Major Attractions</h3>
		<div class="ribon-red-desktop"></div>
		<div class="row"> 

			<!-- Three grid -->

			<?php
			// check if the repeater field has rows of data
			if( have_rows('major_attractions') ):
			 
			 	// loop through the rows of data
			    while ( have_rows('major_attractions') ) : the_row();
			 
			        // display a sub field value
					?>

					 <div class="col-sm-4">
						<div class="thumbnail">
							<div class="main-image">
								<a href="<?php the_sub_field('box_link') ?>">
						  			<img class="main-image" src="<?php the_sub_field('box_image') ?>" alt="">
						  		</a>
						  	</div>
					  		<a href="<?php the_sub_field('box_link') ?>">
					  			<img class="logo" src="<?php the_sub_field('box_logo') ?>" alt="">
					  		</a>
						</div>
					</div>
					<?php ;
			    endwhile;
			else :
			    // no rows found
			endif; ?>
		 
			   
			<!-- End of Three Grid -->
		</div>
	</div>
	<div class="theme-park four grid">
		<h3>Theme Park and Event Tickets</h3>
		<div class="ribon-red-desktop"></div>
		<div class="row"> 

			<!-- Four grid -->

			<?php
			// check if the repeater field has rows of data
			if( have_rows('theme_park_and_event_tickets') ):
			 
			 	// loop through the rows of data
			    while ( have_rows('theme_park_and_event_tickets') ) : the_row();
			 
			        // display a sub field value

$url33 =  get_sub_field('box_link') ;
$imgurl33 =  get_sub_field('box_image') ;
if(get_sub_field('texturl') ){
	$url33 = get_sub_field('texturl');
}if(get_sub_field('imgtext') ){
	$imgurl33 = get_sub_field('imgtext');
}
if($url33 == "hidden"){
	continue;
}


					?>

					  <div class="col-sm-3">
						<div class="thumbnail">
							<div class="caption"><a href="<?php echo $url33;   ?>">
								<h4><?php the_sub_field('box_title') ?></h4></a>
							</div>
					  		<a href="<?php echo $url33;  ?>"><img src="<?php echo $imgurl33;  ?>" alt=""></a>
						</div>
					</div>
					<?php ;
			    endwhile;
			else :
			    // no rows found
			endif; ?>
		 
			   
			<!-- End of Four Grid -->
		</div>
	</div>
	<div class="airport-transfers four grid">
		<h3>Airport Transfers</h3>
		<div class="ribon-red-desktop"></div>
		<div class="row"> 

			<!-- Four grid -->

			<?php
			// check if the repeater field has rows of data
			if( have_rows('airport_transfers') ):
			 
			 	// loop through the rows of data
			    while ( have_rows('airport_transfers') ) : the_row();
			 
			        // display a sub field value
					?>

					  <div class="col-sm-3">
						<div class="thumbnail">
							<div class="caption"><a href="<?php the_sub_field('box_link') ?>">
								<h4><?php the_sub_field('box_title') ?></h4></a>
							</div>
					  		<a href="<?php the_sub_field('box_link') ?>"><img src="<?php the_sub_field('box_image') ?>" alt=""></a>
						</div>
					</div>
					<?php ;
			    endwhile;
			else :
			    // no rows found
			endif; ?>
		</div>
	</div>
</div><!-- .container ends -->

<?php get_footer(); ?>
