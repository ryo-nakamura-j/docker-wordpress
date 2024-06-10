<?php





//get_header();

?>
<!--
/

*

*

 * @package WordPress

 * @subpackage Default_Theme

 *

 /
-->
<section id="index" class="container">

	<div class="col-xs-12">



		<?php if (have_posts()) : while (have_posts()) : the_post(); ?>







			<div id="post">

				<h1><?php the_title(); ?></h1>



				<div class="entry">

					<?php the_content('<p class="serif">Read the rest of this entry &raquo;</p>'); ?>



					<?php wp_link_pages(array('before' => '<p><strong>Pages:</strong> ', 'after' => '</p>', 'next_or_number' => 'number')); ?>

					

			<div class="navigation">

				<div class="alignleft"><?php previous_post_link('&laquo; %link') ?></div>

				<div class="alignright"><?php next_post_link('%link &raquo;') ?></div>

			</div>

					

				</div>

			</div>







		<?php endwhile; else: ?>



			<p>Sorry, no posts matched your criteria.</p>



		<?php endif; ?>



	</div>

</section>



<?php get_footer(); ?>

