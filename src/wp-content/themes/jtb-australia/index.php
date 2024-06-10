<?php
/**
 * @package WordPress
 * @subpackage Default_Theme
 */

get_header(); ?>

<section id="index" class="container">
	<div class="col-xs-12">

		<?php if (have_posts()) : ?>
			
			<div id="posts">
			<?php while (have_posts()) : the_post(); ?>
				<div class="entry">
                	<h1><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h1>
					<div class="date"><small><?php the_time('F jS, Y') ?> <!-- by <?php the_author() ?> --></small></div>
					<?php the_content('Read the rest of this entry &raquo;'); ?>
				</div><!-- entry -->
			<?php endwhile; ?>
			</div><!-- post -->
			
			<div class="navigation">
				<div class="alignleft-page"><?php next_posts_link('&laquo; Older Entries') ?></div>
				<div class="alignright-page"><?php previous_posts_link('Newer Entries &raquo;') ?></div>
			</div>

		<?php else : ?>

			<h2 class="center">Not Found</h2>
			<p class="center">Sorry, but you are looking for something that isn't here.</p>
			<?php get_search_form(); ?>

		<?php endif; ?>

	</div>
</section>

<?php get_footer(); ?>
