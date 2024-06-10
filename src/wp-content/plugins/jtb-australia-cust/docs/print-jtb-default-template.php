<?php
global $wp_query; 
$post_id = $wp_query->post->ID; 
if (get_post_meta($post_id, '_accent_colour_dropdown', true)){
	$accent_col = " deftemplate" . get_post_meta($post_id, '_accent_colour_dropdown', true);
}else{
	$accent_col = "";
}
$productHidden="";
$systempages= array('2877','3392','3394','2875','462','2873','2871','2868','466','464','2879','3675','21891','21942','23492','23498');
if ((get_post_meta($post_id, '_hide_from_search', true)=="1")&&(!in_array($post_id, $systempages))){
	$productHidden = '<div class="col-sm-12"><p class="red-message">This product is currently unavailable. Please see our other offerings and check back at a later date.</p></div>';
}
?>


<section id="content" class="container<?php echo $accent_col; ?>">
	<div class="row">
		<div class="col-sm-12">
			<?php if ( function_exists('yoast_breadcrumb') ) {
			yoast_breadcrumb('<p id="breadcrumbs">','</p>');
			} ?>
		</div>
		<?php echo $productHidden; ?>







<?php 
$logedin = (current_user_can('editor') || current_user_can('administrator') );

  ?>
<!-- Slider -->
<div class="home-content container">
	<section id="home-slider" class="carousel slide carousel-fade tp-wp-carousel" data-ride="carousel">
	  	<!-- Wrapper for slides -->
	  	
		  	<?php $int = 0; $popupdiv=99; ?>
		    <?php if( have_rows('slides') ):
		    echo '<div class="carousel-inner">';
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
					      <a href="<?php the_sub_field('link');?>">
					      	<img class="hidden-xs" src="<?php the_sub_field('slide');?>" alt="slide-<?php echo $count;?>">
					      	<img class="visible-xs-block" src="<?php echo $mobslide; ?>" alt="slide-<?php echo $count; ?>">
					      </a>
					  	</div>
		    <?php 
		    $int++;
		    endwhile;
		    echo'</div>';
		    endif;?>
	
<?php if ($int>1):?>

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

		<?php endif; ?>
	</section>
</div>

<!-- End Slider -->
 








		<div class="col-sm-12">
			<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
			<div class="post">
				<?php //<h1> the_title(); </h1> <div class="ribon-red-desktop"></div> ?>
				
				<div class="entry">

					<?php
					the_content(); 
					the_field('content'); 

					if(is_page(22673)){//if transfers top page
						do_action("print_tickets_top");
					}
					?>
				</div>
			</div>
			<?php endwhile; endif; ?>
		</div>
	</div>
</section>

