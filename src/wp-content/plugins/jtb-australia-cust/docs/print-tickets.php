

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
								<?php $link2 = get_sub_field('box_link') ; 
								    if ((get_sub_field('texturl') != "") && (get_sub_field('texturl') != null) && (get_sub_field('texturl') != undefined) ){
								    	$link2 = get_sub_field('texturl');
								    }
								?>
								<a href="<?php echo $link2 ; ?>">
						  			<img class="main-image" src="<?php the_sub_field('box_image') ?>" alt="">
						  		</a>
						  	</div>
					  		<a href="<?php  echo $link2 ; ?>">
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
			$fistingrid=0;
			if( have_rows('theme_park_and_event_tickets') ):
			 
			 	// loop through the rows of data
			    while ( have_rows('theme_park_and_event_tickets') ) : the_row();
			 	
			        // display a sub field value
						if(( get_sub_field('texturl')=="hidden")||( get_sub_field('texturl')=="hide")){
							continue;
						}
						$fistingrid+=1;
					?>

					  <div class="col-sm-3<?php if ($fistingrid%4==1){echo ' clearleft';} ?>">
						<div class="thumbnail">

						 <?php $link2 = get_sub_field('box_link') ; 
								    if ((get_sub_field('texturl') != "") && (get_sub_field('texturl') != null) && (get_sub_field('texturl') != undefined) ){
								    	$link2 = get_sub_field('texturl');
								    }
								?>


							<div class="caption"><a href="<?php echo  $link2 ; ?>">
								<h4><?php the_sub_field('box_title') ?></h4></a>
							</div>
					  		<a href="<?php echo  $link2 ; ?>" alt=""></a>
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

								 <?php $link2 = get_sub_field('box_link') ; 
								    if ((get_sub_field('texturl') != "") && (get_sub_field('texturl') != null) && (get_sub_field('texturl') != undefined) ){
								    	$link2 = get_sub_field('texturl');
								    }
								?>


							<div class="caption"><a href="<?php echo $link2;  ?>">
								<h4><?php the_sub_field('box_title') ?></h4></a>
							</div>
					  		<a href="<?php echo $link2;  ?>"><img src="<?php the_sub_field('box_image') ?>" alt=""></a>
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

