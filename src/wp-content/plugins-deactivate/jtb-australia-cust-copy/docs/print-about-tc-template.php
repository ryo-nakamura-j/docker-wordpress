
<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

<section id="content" class="container about-tc-template">
	<div class="row">
		<div class="col-sm-12">
			<?php if ( function_exists('yoast_breadcrumb') ) {
			yoast_breadcrumb('<p id="breadcrumbs">','</p>');
			} ?>
			<h1><?php the_title(); ?></h1>
			<div class="ribon-red-desktop"></div>
			<?php if (the_field( "text3" )){ ?>
			<div class="post">
				<div class="entry">
					<?php 
					echo get_field( "text3" );
					?>
				</div>
			</div>
			<?php } ?>
		</div>

		<div class="col-sm-3">
			<div class="post">
				<div class="entry about-tc-menu">
					<?php 
					echo get_field( "text2" );
					?>
				</div>
			</div>
		</div>

		<div class="col-sm-9">
			<div class="post">
				<div class="entry">
					<?php the_content(); ?>
				</div>
			</div>
		</div>

	</div>
</section>

<?php endwhile; endif; ?>