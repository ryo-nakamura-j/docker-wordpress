<?php
/**
 * @package WordPress
 * @subpackage Default_Theme
 * Template Name: Ryokan & Hotels - Landing Page
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
	<div class="ryokan-hotel landing container">
		<div class="row">
			<div class="col-sm-3">
				<section class="search">
					<h3>Ryokan &amp; Hotel Search</h3>
					<div id="searchparamssection" class="quicksearch"></div>
				</section>
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

	<div class="ryokan-hotel landing container">
		<div class="row"> 
			<div class="col-sm-12">
				<h3>Ryokan -Japanese style inns-</h3>
				<div class="ribon-red-desktop"></div>
			</div>
			<div class="col-sm-6">
				<div class="row">
					<?php
					// check if the repeater field has rows of data
					if( have_rows('featured_ryokans') ):
					 
					 	// loop through the rows of data
					    while ( have_rows('featured_ryokans') ) : the_row();
					 
					        // display a sub field value
							?>

							  <div class="col-sm-6">
							  	<div class="featured ryokans">
									<div class="thumbnail">
										<div class="title"><a href="<?php the_sub_field('link') ?>">
											<h4><?php the_sub_field('title') ?></h4></a>
										</div>
								  		<a href="<?php the_sub_field('link') ?>"><img src="<?php the_sub_field('image') ?>" alt="..."></a>
										<div class="caption">
											<div class="row">
												<div class="col-sm-12 col-md-12">
												<?php the_sub_field('caption') ?>
												</div>
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
				</div>
			</div>
			<div class="col-sm-6">
				<div class="popular-destinations ryokan">
					<h4>Choose from popular destinations!!</h4>
					<div class="row">
					<?php
					// check if the repeater field has rows of data
					if( have_rows('popular_destinations_ryokan') ):
					 
					 	// loop through the rows of data
					    while ( have_rows('popular_destinations_ryokan') ) : the_row();
					 
					        // display a sub field value
							?>

						  	<div class="col-sm-6 multi">
						  		<a href="<?php echo site_url() . '/' . get_option('tp_search_url') . '/' . get_sub_field('url_parameters'); ?>"><img src="<?php the_sub_field('image') ?>" class="img-responsive" alt=""></a>
								<p class="flying-text"><?php the_sub_field('title') ?></p>
							</div>
							<?php ;
					    endwhile;
					else :
					    // no rows found
					endif; ?>
					</div>
				</div>
			</div>
		</div>

		<div class="row"> 
			<div class="col-sm-12">
				<h3>Hotels</h3>
				<div class="ribon-red-desktop"></div>
			</div>
			<div class="col-sm-6">
				<div class="row">
					<?php
					// check if the repeater field has rows of data
					if( have_rows('featured_hotels') ):
					 
					 	// loop through the rows of data
					    while ( have_rows('featured_hotels') ) : the_row();
					 
					        // display a sub field value
							?>

							  <div class="col-sm-6">
							  	<div class="featured hotels">
									<div class="thumbnail">
										<div class="title"><a href="<?php the_sub_field('link') ?>">
											<h4><?php the_sub_field('title') ?></h4></a>
										</div>
								  		<a href="<?php the_sub_field('link') ?>"><img src="<?php the_sub_field('image') ?>" alt="..."></a>
										<div class="caption">
											<div class="row">
												<div class="col-sm-12 col-md-12">
												<?php the_sub_field('caption') ?>
												</div>
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
				</div>
			</div>
			<div class="col-sm-6">
				<div class="popular-destinations hotels">
					<h4>Choose from popular destinations!!</h4>
					<div class="row">
					<?php
					// check if the repeater field has rows of data
					if( have_rows('popular_destinations_hotels') ):
					 
					 	// loop through the rows of data
					    while ( have_rows('popular_destinations_hotels') ) : the_row();
					 
					        // display a sub field value
							?>

						  	<div class="col-sm-6 multi">
						  		<a href="<?php echo site_url() . '/' . get_option('tp_search_url') . '/' . get_sub_field('url_parameters'); ?>"><img src="<?php the_sub_field('image') ?>" class="img-responsive" alt=""></a>
								<p class="flying-text"><?php the_sub_field('title') ?></p>
							</div>
							<?php ;
					    endwhile;
					else :
					    // no rows found
					endif; ?>
					</div>
				</div>
			</div>
		</div>
	
		<div class="row">
	 		<div class="page-top col-sm-12">
				<p><img src="<?php bloginfo('template_directory'); ?>/images/top-btn.png" alt="gotop">&nbsp;<a href="#top" style="text-decoration: underline;">Page Top</a></p>
				<div class="ribon-red-desktop"></div>
			</div>
		</div>
	</div>


<?php get_footer(); ?>
