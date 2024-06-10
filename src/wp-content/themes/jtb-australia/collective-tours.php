<?php
/**
 * @package WordPress
 * @subpackage Default_Theme
 * Template Name: Collective Tours
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
<div class="collective container">
	<section id="home-slider" class="carousel slide carousel-fade tp-wp-carousel" data-ride="carousel">
	  	<!-- Wrapper for slides -->
	  	<div class="carousel-inner">
		  	<?php $int = 0; ?>
		    <?php if( have_rows('slides') ):
		        while ( have_rows('slides') ) : the_row();?>
				    <div class="item <?php if($int == 0): echo " active"; endif;?>">
		        	    <a href="<?php the_sub_field('link'); ?>">
							<img class="hidden-xs" src="<?php the_sub_field('slide');?>" alt="slide-<?php echo $count;?>">
				      		<img class="visible-xs-block" src="<?php the_sub_field('slide_mobile'); ?>" alt="slide-<?php echo $count; ?>">
				  		</a>
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
	        	 	<img class="hidden-xs" src="<?php the_sub_field('slide');?>" alt="slide-<?php echo $count;?> thumbnail">
	        	 	<img class="visible-xs-block" src="<?php the_sub_field('slide_mobile');?>" alt="slide-<?php echo $count;?> thumbnail">
	        	 </li>
		    <?php 
		    $count++;
		    endwhile;
	        endif;?>
		</ol>
	</section>
</div>
<!-- End Slider --> 

<div class="col-xs-12 col-md-12 container">
	<div class="row">
		&nbsp;
	</div>
</div>

<div class="collective three grid container">
	<?php

		$collectiveToursList = array();
		while ( have_rows('fully-escorted-tours') ) {
			the_row();
			$rlt = array();
			$rlt['box_title'] = get_sub_field('box_title');
			$rlt['box_link'] = get_sub_field('box_link');
			$rlt['box_image'] = get_sub_field('box_image');
			$rlt['box_copy'] = get_sub_field('box_copy');
			array_push( $collectiveToursList, $rlt );
		}
		// if the count is odd, fill an empty item in
		for ( $j = 0; $j < (4 - sizeof( $collectiveToursList ) % 2); $j++ ) {
			array_push( $collectiveToursList, array() );
		}
	?>
	<?php if( sizeof( $collectiveToursList ) > 0 ) { ?>
	<?php for ( $i = 0; $i < sizeof( $collectiveToursList); $i = $i + 4 ) { ?>
	<div class="col-xs-12 col-md-12">
		<div class="row">
		<?php for ( $ii = 0; $ii < 4; $ii = $ii + 2 ) { ?>
			<div class="col-xs-12 col-md-6">
				<div class="row">
					<?php 
						for ( $iii = 0; $iii < 2; $iii++ ) { 
							$item = $collectiveToursList[$i + $ii + $iii]; ?>
					<div class="col-xs-12 col-sm-6">
						<?php if ( !empty( $item ) ) { ?>
						<div class="thumbnail">
							<div class="caption">
								<a href="<?php echo $item['box_link'] ?>">
									<h4><?php echo $item['box_title'] ?></h4>
								</a>
							</div>
							<a href="<?php echo $item['box_link'] ?>">
								<img src="<?php echo $item['box_image'] ?>" alt="...">
							</a>
							<div class="caption">
								<div class="row">
									<div class="col-sm-12 col-md-12">
										<?php echo $item['box_copy'] ?>
									</div>
								</div>
							</div>
						</div>
						<?php } ?>
					</div>
					<?php } ?>
				</div>
			</div>
		<?php } ?>
		</div>
	</div>
	<?php } ?>
	<?php } ?>
</div>


<div class="col-xs-12 col-md-12 container">
	<div class="row">
		&nbsp;
	</div>
</div>

<?php get_footer(); ?>
