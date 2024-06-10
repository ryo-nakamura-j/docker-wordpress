<?php
/**
 * @package WordPress
 * @subpackage Default_Theme
 * Template Name: Day Tours - Landing Page
 */

get_header(); ?>


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
	
	<!-- Slider -->
	<div class="day-tours landing container">
		<div class="row">
			<div class="col-xs-12 col-sm-3">
				<div id="tourSearchPanel"></div>
			</div>
			<div class="col-sm-9">
				<section id="home-slider" class="carousel slide carousel-fade" data-ride="carousel">
				  	<!-- Wrapper for slides -->
				  	<div class="carousel-inner">
					  	<?php $int = 0; ?>
					    <?php if( have_rows('slides') ):
					        while ( have_rows('slides') ) : the_row();?>
					                    
								    <div class="item <?php if($int == 0): echo " active"; endif;?>">
								      <a href="<?php the_sub_field('link');?>"><img src="<?php the_sub_field('slide');?>" alt="slide-<?php echo $count;?>"></a>
								  	</div>
					    <?php 
					    $int++;
					    endwhile;
					    endif;?>
					</div>

					<!-- Controls -->
				    <ol class="carousel-indicators">
				    	<?php $count = 0; ?>
				        <?php if( have_rows('slides') ):
				        while ( have_rows('slides') ) : the_row();?>
				        	 <li data-target="#home-slider" data-slide-to="<?php echo $count;?>" class="<?php if($count == 0): echo "active"; endif;?>">
				        	 	<img src="<?php the_sub_field('slide');?>" alt="slide-<?php echo $count;?> thumbnail">
				        	 </li>
					    <?php 
					    $count++;
					    endwhile;
				        endif;?>
					</ol>
				</section>
			</div>
		</div>
	<!-- End Slider -->

		<div class="row">
			<div class="col-xs-12">
				<h3>Popular Tours</h3>
				<div class="ribon-red-desktop"></div>
			</div>
			<div class="col-xs-12">
				<div class="row">
					<?php

					if (have_rows('popular_tours')) :
						while (have_rows('popular_tours')) : the_row();

						?>

						<div class="col-xs-14 col-sm-3">
							<div class="thumbnail">
								<div class="caption">
									<a href="<?php echo the_sub_field('url_parameters'); ?>">
										<h4><?php echo the_sub_field('title'); ?></h4>
									</a>
								</div>
								<a href="<?php echo the_sub_field('url_parameters'); ?>">
									<img src="<?php echo the_sub_field('image'); ?>">
								</a>
								<div class="caption">
									<div class="row">
										<div class="col-sm-12 col-md-12">
											<p><?php the_sub_field('caption'); ?></p>
										</div>
									</div>
								</div>
							</div>
						</div>

						<?php
						endwhile;
					endif;
					?>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-xs-12">
	   			<div class="ribon-red-desktop"></div>
	   		</div>
	   	</div>

	</div>

	

	<script id="day-tours-search-template" type="text/x-handlebars-template">
		<?php echo get_template_part('templates/tour', 'search'); ?>
	</script>

	<script id="day-tours-search-settings" type="application/json">
		<?php echo get_template_part('templates/tour', 'search-settings'); ?>
	</script>


<?php get_footer(); ?>
