
<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

<section id="content" class="container hawaiipages">
	<div class="row<?php
if ( wp_get_post_parent_id( get_the_ID() ) ){
	if (wp_get_post_parent_id( get_the_ID() ) == 3795){
		echo ' koreatemplate'; //orange
	}elseif(wp_get_post_parent_id( get_the_ID() ) ==3797){
		echo ' hawaiitemplate';//pink
	}elseif( get_the_ID() ==3789){
		echo ' cookingclasstemplate';//green
	}elseif( wp_get_post_parent_id() ==71){
		echo ' tickettemplate';//orange
	}
}
?>">
		<div class="col-sm-12">
			<?php if ( function_exists('yoast_breadcrumb') ) {
			yoast_breadcrumb('<p id="breadcrumbs">','</p>');
			} ?>
		</div>

		<div class="col-sm-12">
			<div class="post">
				<div class="entry about-tc-menu">
					<?php 
					echo get_field( "page-title" );
					?>
				</div>
			</div>
		</div>

<div class="marginbottom">
<?php
$counter=0;
while ( have_rows('3image-set') ) : the_row();
$imgtemp=get_sub_field('img-3');
$img11 = $imgtemp['url'];
if ($img11 == ''){
	$img11 = 'https://www.nx.jtbtravel.com.au/wp-content/uploads/2016/11/blank-tour-jtb-image.jpg';
	}
$alt11 = $imgtemp['alt'];
if($alt11 == ''){
	$alt11 = 'JTB Australia Tour';
} 
$counter +=1;
?>

<div class="col-sm-4 imageset<?php echo $counter; ?>">
	<div class="post">
		<div class="entry">
			<img src="<?php echo $img11; ?>" alt="<?php echo $alt11; ?>" <?php if ($alt11 != 'JTB Australia Tour'){echo'title="'.$alt11.'"';} ?>></a>
		</div>
	</div>
</div>
<?php endwhile; ?>
</div>

		<div class="col-sm-12">
        <?php wp_reset_query(); the_content();   ?>
				
		</div>

	</div>
</section>

<?php endwhile; endif; ?>