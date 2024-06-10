
<div class="mobile-content">
	<div class="round-social ">
		<a class="fa fa-search solo" href="#" target="_blank"></a>
		<a class="fa fa-facebook solo" href="#" target="_blank"></a>
		<a class="fa fa-twitter solo" href="#" target="_blank"></a>
	</div>
	<div class="home-full-width-button">
		<a href="/japan-rail-pass/">
			<div class="button-search">
				<i class="fa fa-search"></i>
				<strong>&nbsp;&nbsp;JR Pass&nbsp;</strong>
				<i class="fa fa-angle-right" style="float: right; padding-right: 15px; padding-top: 5px;"></i>
			</div>
		</a>
		<img src="<?php bloginfo('template_directory'); ?>/images/home-res-train.jpg" class="img-responsive" alt="jtb">
	</div>
	<div class="home-full-width-button">
		<a href="/collective-tours/"><div class="button-search"><i class="fa fa-search"></i><strong>&nbsp;&nbsp;Ryokan &amp; Hotel&nbsp;</strong><i class="fa fa-angle-right" style="float: right; padding-right: 15px; padding-top: 5px;"></i></div></a>
		<img src="<?php bloginfo('template_directory'); ?>/images/home-res-cherry-blossom.jpg" class="img-responsive" alt="jtb">
	</div>
	<div class="home-full-width-button">
		<a href="/collective-tours/"><div class="button-search"><i class="fa fa-search"></i><strong>&nbsp;&nbsp;CollectiveTours&nbsp;</strong><i class="fa fa-angle-right" style="float: right; padding-right: 15px; padding-top: 5px;"></i></div></a>
		<img src="<?php bloginfo('template_directory'); ?>/images/home-res-train.jpg" class="img-responsive" alt="jtb">
	</div>
	<div class="home-full-width-button">
		<a href="/day-tours/"><div class="button-search"><i class="fa fa-search"></i><strong>&nbsp;&nbsp;Day Tours&nbsp;</strong><i class="fa fa-angle-right" style="float: right; padding-right: 15px; padding-top: 5px;"></i></div></a>
		<img src="<?php bloginfo('template_directory'); ?>/images/home-res-sunflower.jpg" class="img-responsive" alt="jtb">
	</div>
	<div class="home-full-width-button tickets">
		<a href="/tickets/"><div class="button-search"><i class="fa fa-search"></i><strong>&nbsp;&nbsp;Tickets&nbsp;</strong><i class="fa fa-angle-right" style="float: right; padding-right: 15px; padding-top: 5px;"></i></div></a>
		<img src="<?php bloginfo('template_directory'); ?>/images/home-res-city-night.jpg" class="img-responsive" alt="jtb">
	</div>

	<div class="ribon-red-top"></div>

<!-- 		<div class="footer-mobile">	
	    <div class="row">
			<div class="col-md-12 col-sm-12 col-xs-12">
			<p align="center" style="margin-top: 5px; padding-right: 10px;">Copyright 2015 JTB AUSTRALIA. All rights reserve</p>
			</div>
		</div>
		<div class="walking-link-mobile">
			<a href="#">About Us</a>
			<a href="#">Customer Support</a>
		 	<a href="#">FAQ</a>
		</div>

		<div>
			<i class="fa fa-phone-square"></i><p>1300 739 330</p>
		</div>
		<ul class="social">
			<li><a class="fa fa-twitter solo" href="#" target="_blank"><span>Twitter</span></a></li>
			<li><a class="fa fa-facebook solo" href="#" target="_blank"><span>Facebook</span></a></li>
		</ul>
	</div>	 -->			
</div>


<!-- End of Mobile Content -->


<!-- div class="container">
	<div class="row">
		<div class="col-sm-12 rss-banner round-social-small">
			<a class="fa fa-rss solo" href="http://www.japan-guide.com/e/e2011.html" target="_blank"></a>
			<a class="rss-desc" href="http://www.japan-guide.com/e/e2011.html" target="_blank">Cherry Blossom Forecast 2015. The cherry blossoms are expected to open roughly according to their average schedule...</a>
		</div>
	</div>
</div -->

<!-- Slider -->
<div class="home-content container">
	<section id="home-slider" class="carousel slide carousel-fade tp-wp-carousel" data-ride="carousel">
	  	<!-- Wrapper for slides -->
	  	<div class="carousel-inner">
		  	<?php $int = 0; $popupdiv=99; ?>
		    <?php if( have_rows('slides') ):
		        while ( have_rows('slides') ) : the_row();?>
		                    
					    <div id="sliderdiv<?php 
					    if(get_sub_field('link')=="#"){
					    		echo $popupdiv; $popupdiv++;
					    	}
					    	else{ 
					    		echo $int;
					    	} 
					    	$mobslide=get_sub_field('slide_mobile');
					    	if($mobslide==""){
					    		$mobslide = get_sub_field('slide');
					    	}
					    	?>" class="item <?php if($int == 0): echo " active"; endif;?>">
<?php
$targ_blank="";
if(get_sub_field('link')=="https://issuu.com/wattention/docs/sydney5.12_final_small"){
$targ_blank=' target="_blank" ';
}

?>
					      <a href="<?php the_sub_field('link');?>" <?php echo $targ_blank; ?> >
					      	<img class="hidden-xs" src="<?php the_sub_field('slide');?>" alt="slide-<?php echo $count;?>">
					      	<img class="visible-xs-block" src="<?php echo $mobslide; ?>" alt="slide-<?php echo $count; ?>">
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
						<?php
					    	$mobslide=get_sub_field('slide_mobile');
					    	if($mobslide==""){
					    		$mobslide = get_sub_field('slide');
					    	}
					    ?>
	        	 	<img class="hidden-xs" src="<?php the_sub_field('slide');?>" alt="slide-<?php echo $count;?> thumbnail">
	        	 	<img class="visible-xs-block" src="<?php echo $mobslide; ?>" alt="slide-<?php echo $count;?> thumbnail">
	        	 </li>
		    <?php 
		    $count++;
		    endwhile;
	        endif;?>
		</ol>
	</section>
</div>
<!-- End Slider -->

<div class="home-content container">
	<div class="row"> 
		<div class="slider-caption col-sm-12">
			<?php the_field( "slideshow_caption" ); ?>
		</div>
	</div>
</div>

<div class="home-content four grid container">
	<h3>Hot Deals</h3>
	<div class="ribon-red-desktop"></div>
	<div class="row"> 

		<!-- Four grid -->

		<?php
		$count=0;
		$fistingrid=0;
		// check if the repeater field has rows of data
		if( have_rows('four_grid_boxes') ):
		 
		 	// loop through the rows of data
		    while ( have_rows('four_grid_boxes') ) : the_row();

// display a sub field value

$link2 = "";
$newtab2 = "";
$title2 = get_sub_field('box_title');

if($title2=="Worldwide Flights"){
	$link2="#";
}else if((get_sub_field('box_link')=="")&&($title2=="Ski")){
	$link2='http://japanski.com.au/';
	$newtab2=' target="_blank" ';
}else{
	$link2=get_sub_field('box_link');
}

$hiderwc=true;
global $current_user;
get_currentuserinfo();
if ((!get_post_meta( 24349, '_hide_from_search', true ))|| ($current_user->user_email == "benjamin_g.au@jtbap.com")){
  //wp_enqueue_style ( 'rwc-hide-css', plugin_dir_url( __FILE__ ) .'css/z_rwc.css',array(),9997 );
	//show data. 
  $hiderwc=false;
}

if($link2=="hidden" || ($hiderwc && $link2=="https://www.nx.jtbtravel.com.au/rugby-world-cup-2019/") ){
	continue;
}

 $fistingrid+=1;


				?>

				  <div class="col-sm-3 col-xs-12<?php if ($fistingrid%4==1){echo ' clearleft';} if($link2=="https://www.nx.jtbtravel.com.au/#2" || $link2=="https://www.nx.jtbtravel.com.au/rugby-world-cup-2019/"){echo " rwc-hotdeal ";} ?>">
					<div <?php if(get_sub_field('external-link')=="#"){echo 'id="hotdealpopup'.$count.'"';$count++;} ?> class="thumbnail">
						<div <?php if(get_sub_field('external-link')=="#"){echo 'id="hotdealpopuplink'.$count.'"';$count++;} ?> class="caption"><a href="<?php echo $link2;	?>" <?php echo $newtab2; if($link2=="#"){echo 'id="hotdealpopup0"';} ?>>
							<h4><?php echo $title2; ?></h4></a>
						</div>
				  		<a href="<?php  echo $link2; ?>" <?php echo $newtab2; if($link2=="#"){echo 'id="hotdealpopuplink0"';} ?>><img src="<?php the_sub_field('box_image') ?>" alt="<?php echo $title2; ?>"></a>
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
	<h3>Featured Products</h3>
	<div class="ribon-red-desktop"></div>
	<div class="row"> 

		<!-- Three grid -->

		<?php
		// check if the repeater field has rows of data
		if( have_rows('three_grid_boxes') ):
		 
		 	// loop through the rows of data
		    while ( have_rows('three_grid_boxes') ) : the_row();
		 
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

		<!-- Three grid -->

		<?php
		// check if the repeater field has rows of data
		if( have_rows('prow2') ):
		 
		 	// loop through the rows of data
		    while ( have_rows('prow2') ) : the_row();
		 
		        // display a sub field value
				?>

				  <div class="col-sm-4 col-xs-12">
					<div class="thumbnail">
						<div class="caption"><a href="<?php the_sub_field('box_link') ?>">
							<h4><?php the_sub_field('ptitle1') ?></h4></a>
						</div>
				  		<a href="<?php the_sub_field('plink1') ?>"><img src="<?php the_sub_field('pimage1') ?>" alt="..."></a>
						<!-- <div class="caption">
							<div class="row">
								<div class="col-sm-12 col-md-12">
								<?php the_sub_field('box_copy') ?>

								</div>
							</div>
						  </div> -->
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




		<!-- Four grid -->

		<?php
		// check if the repeater field has rows of data
		if( have_rows('r3') ):
		 
		 	// loop through the rows of data
		    while ( have_rows('r3') ) : the_row();
		 
		        // display a sub field value
				?>

				  <div class="col-sm-3 col-xs-12">
					<div class="thumbnail">
						<div class="caption"><a href="<?php the_sub_field('l1') ?>">
							<h4><?php the_sub_field('t1') ?></h4></a>
						</div>
				  		<a href="<?php the_sub_field('l1') ?>"><img src="<?php the_sub_field('i1') ?>" alt="..."></a>
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





	<h3>Featured Destinations</h3>
	<div class="ribon-red-desktop"></div>
	<div class="multi row one"> 

		<!-- Multi grid -->

		<?php
		// check if the repeater field has rows of data
		if( have_rows('multi_grid_boxes_row_1') ):
		 
		 	// loop through the rows of data
		    while ( have_rows('multi_grid_boxes_row_1') ) : the_row();
		 
		        // display a sub field value
				?>

			  	<div class="col-sm-4 multi">
					<a href="<?php the_sub_field('box_link') ?>"><img src="<?php the_sub_field('box_image') ?>" class="img-responsive fullwidth" alt=""></a>
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
		if( have_rows('multi_grid_boxes_row_2') ):
		 
		 	// loop through the rows of data
		    while ( have_rows('multi_grid_boxes_row_2') ) : the_row();
		 
		        // display a sub field value
				?>

			  	<div class="col-sm-3 multi">
					<a href="<?php the_sub_field('box_link') ?>"><img src="<?php the_sub_field('box_image') ?>" class="img-responsive fullwidth" alt=""></a>
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
		if( have_rows('multi_grid_boxes_row_3') ):
		
		 	// loop through the rows of data
		    while ( have_rows('multi_grid_boxes_row_3') ) : the_row();
		
		        // display a sub field value 

				?>

			  	<div class="col-sm-2 multi">
					<a href="<?php if (get_sub_field('box_link')==""){ echo 'http://discovery-japan.com/';	}else{the_sub_field('box_link');} ?>" <?php if(get_sub_field('box_link')==""){echo ' target="_blank" '; } ?> ><img src="<?php the_sub_field('box_image') ?>" class="img-responsive fullwidth" alt=""></a>
					<p class="flying-text"><?php the_sub_field('box_title') ?></p>
				</div>
				<?php ;
		    endwhile;
		else :
		    // no rows found
		endif; ?>

<!-- 	<div class="row">
		<div class="col-xs-12"> -->
	 
		   
		<!-- End of Multi Grid -->


	</div>
	<div class="row">
 		<div class="page-top col-sm-12">
			<p><img src="<?php bloginfo('template_directory'); ?>/images/top-btn.png" alt="gotop">&nbsp;<a href="#top" style="text-decoration: underline;">Page Top</a></p>
		</div>
	</div>
	<div class="ribon-red-desktop"></div>
	
	<div class="row  ">
		<?php
		// check if the repeater field has rows of data
		if( have_rows('advertising_banner') ):
		 
		 	// loop through the rows of data
		    while ( have_rows('advertising_banner') ) : the_row();

				?>

			  	<div class="col-sm-3">
					<a href="<?php the_sub_field('link');?>" target="_blank" ><img src="<?php the_sub_field('image') ?>" class="img-responsive" alt=""></a>
				</div>
				<?php ;
		    endwhile;
		else :
		    // no rows found
		endif; ?>
	</div>
	<div class="row">
 		<div class="page-top col-sm-12">
			<p><img src="<?php bloginfo('template_directory'); ?>/images/top-btn.png" alt="gotop">&nbsp;<a href="#top" style="text-decoration: underline;">Page Top</a></p>
		</div>
	</div>
</div>
