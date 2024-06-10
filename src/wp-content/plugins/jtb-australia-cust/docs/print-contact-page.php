
	<section id="content" class="container">
		<div class="row">
			<div class="col-sm-12">
				<?php if ( function_exists('yoast_breadcrumb') ) {
				yoast_breadcrumb('<p id="breadcrumbs">','</p>');
				} ?>
			</div>




			<div class="col-sm-12">

<h1 class="red-heading">Contact Us</h1>
<div class="ribbon-red-desktop"></div>
<div class="col-xs-12 col-md-12">
		<div class="row">




			<div class="col-xs-12 col-md-3">
				<div class="row">

<!-- Right side content-->
<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
<div class="post">
	<?php /* <h1><?php the_title(); ?></h1>
	<div class="ribon-red-desktop"></div> */ ?>
	<div class="col-sm-12 col-md-12">
	<div class="entry">
		<?php the_content(); ?>
	</div>
	</div>
</div>
<?php endwhile; endif; ?>

				</div>
			</div>
			<div class="col-xs-12 col-md-9">
				<div class="row">


<?php 
echo do_shortcode( '[contact-form-7 id="21531" title="Contact Us Page"]' );
?>


				</div>
			</div>


		



		</div>
</div>







			</div>
		</div>
	</section>
