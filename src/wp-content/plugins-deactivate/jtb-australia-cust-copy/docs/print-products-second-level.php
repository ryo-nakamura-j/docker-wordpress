
<div class="container tickets-second">

	<div class="row">

		<div class="col-sm-12">
			<?php if ( function_exists('yoast_breadcrumb') ) {
			yoast_breadcrumb('<p id="breadcrumbs">','</p>');
			} ?>
		</div>

		<div class="col-sm-12">
			<h1><?php the_title(); ?></h1>
			<?php the_field('content'); ?>
		</div>

		<?php
		// check if the repeater field has rows of data
		if( have_rows('product') ):
		 
		 	// loop through the rows of data
		    while ( have_rows('product') ) : the_row();
		 
		        // display a sub field value
				?>

				<div class="ticket-product">

					<div class="col-sm-12">
						<h3><?php the_sub_field('product_title') ?></h3>
						<div class="ribon-red-desktop"></div>
					</div>

					<div class="col-sm-3">
						<div class="thumbnail">
					  		<a href="<?php
					  		if(get_sub_field('textlink')!=""){
					  			the_sub_field('textlink');
					  		}else{
					  			the_sub_field('product_link');
					  		}
					  		?>"><img src="<?php the_sub_field('product_image') ?>" alt=""></a>
						</div>
					</div>

					<div class="col-sm-9">
						<div class="product-info">
							<?php the_sub_field('product_intro') ?>
						</div>
					</div>
				</div>

				<?php ;
		    endwhile;
			else :
		    // no rows found
		endif; ?>
	</div>
</div><!-- .container ends -->
