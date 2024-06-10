
<div class="container">
	<div class="row">
		<div class="col-sm-12">
			<?php if ( function_exists('yoast_breadcrumb') ) {
			yoast_breadcrumb('<p id="breadcrumbs">','</p>');
			} ?>
		</div><?php /*
		<div class="col-sm-12">
			<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
				<div class="post">
					<h1><?php the_title(); ?></h1>
				</div>
			<?php endwhile; endif; ?>
		</div> */ ?>
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
						      <img src="<?php the_sub_field('slide');?>" alt="slide-<?php echo $count;?>">				 
						  	</div>
			    <?php 
			    $int++;
			    endwhile;
			    endif;?>
			</div>

			<!-- Controls -->
		    <ol class="carousel-indicators">
		    	<?php if ($int>1){ $count = 0; ?>
		        <?php if( have_rows('slides') ):
		        while ( have_rows('slides') ) : the_row();?>
		        	 <li data-target="#home-slider" data-slide-to="<?php echo $count;?>" class="<?php if($count == 0): echo "active"; endif;?>">
		        	 	<img src="<?php the_sub_field('slide');?>" alt="slide-<?php echo $count;?> thumbnail">
		        	 </li>
			    <?php 
			    $count++;
			    endwhile;
		        endif;
		        }?>
			</ol>
		</section>
	</div>
	<!-- End Slider --> 

<div class="collective container">
<?php the_content(); ?>
</div>







<?php 
if( have_rows('tour_block') ):
while ( have_rows('tour_block') ) : the_row();
?>
	<div class="collective three grid container">
	
	<?php /* <h3>the_sub_field('category_title');</h3>  */?>
	 
	<div class="row marginboth"> <div class="tours2 marginboth"> 
		<!-- Three grid -->
		<?php
		// check if the repeater field has rows of data
		if( have_rows('tour_item') ):  ?>
			
					
		<?php
		 	// loop through the rows of data
		    while ( have_rows('tour_item') ) : the_row();
		    ?>
		    <div class="col-xs-12 col-md-12">
				<div class="row">
				<?php
		        // display a sub field value
				$urltemp= get_sub_field('text_url');
				if ($urltemp == ""){
					$urltemp = get_sub_field('url');
				}
				if($urltemp ==""){
					$urltemp = "/";
				}
				?>
				<div class="col-xs-12 col-sm-3">
				<?php
						$imgtemp=get_sub_field('image');
						$img11 = $imgtemp['url'];
						if ($img11 == ''){
							$img11 = 'https://www.nx.jtbtravel.com.au/wp-content/uploads/2016/11/blank-tour-jtb-image.jpg';
							}
						$alt11 = $imgtemp['alt'];
						if($alt11 == ''){
							$alt11 = 'JTB Australia Tour';
						} 
						?>
						<a href="<?php echo $urltemp; ?>"><img src="<?php echo $img11; ?>" alt="<?php echo $alt11; ?>" <?php if ($alt11 != 'JTB Australia Tour'){echo'title="'.$alt11.'"';} ?> ></a>
				</div>
				
				<div class="col-xs-12 col-sm-9">
					<a href="<?php echo $urltemp; ?>">
					<h3><?php the_sub_field('title') ?></h3></a>
					<p>
					<?php the_sub_field('description') ?>
					</p>
				</div> 
				
					</div><div class="col-xs-12 col-sm-12"></div>

	</div>
				<?php 
		// else :
		    // no rows found
				endwhile;
				echo "</div>";
		endif; ?>
		<!-- End of Three Grid -->

	<div class="collective three grid container"><div class="row">
			<div class="page-top col-sm-12">
			<p><img src="<?php bloginfo('template_directory'); ?>/images/top-btn.png" alt="gotop">&nbsp;<a href="#top" style="text-decoration: underline;">Page Top</a></p>
		</div>
	</div></div>
	<?php 


endwhile;
endif;
?>


