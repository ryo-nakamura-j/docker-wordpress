<?php
/**
 * @package WordPress
 * @subpackage Default_Theme
 * Template Name: Day Tours Search Page
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
	</div>
	<!-- End Slider -->

	<div class="container">

	    <div class="row">
	    	<div class="col-xs-12">
				<h3>Tour Search Result</h3>
				<div class="ribon-red-desktop"></div>
			</div>
		</div>
    	<div id="srTarget"></div>

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


	<script id="day-tours-results-template" type="text/x-handlebars-template">
		<?php echo get_template_part('templates/tour', 'result'); ?>
	</script>

<?php get_footer(); ?>
