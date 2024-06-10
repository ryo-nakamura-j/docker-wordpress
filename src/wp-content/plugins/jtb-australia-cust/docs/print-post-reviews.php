<?php

if (in_category("reviews"))://if category reviews, custom template
?>
<div class="container reviewsdetailpage">
<?php
if(have_posts()) :
while(have_posts()) :
?>
<h1><?php the_title(); ?></h1>

<nav id="categorynavigation">
<?php previous_post_link('%link', '<div class="left"><img src="https://www.nx.jtbtravel.com.au/wp-content/uploads/svg/ic_arrow_forward_black_24px-back.svg" /> %title </div>', 1); ?>
<?php next_post_link('%link', '<div class="right"> %title  <img src="https://www.nx.jtbtravel.com.au/wp-content/uploads/svg/ic_arrow_forward_black_24px.svg" /></div>', 1); ?>
</nav><!-- #nav-single -->

<?php
the_post_thumbnail();
the_post();
the_content();
?>

<p class="postmetadata">
<?php 

echo "<p class='taggedpost'>";
echo "<img src='https://www.nx.jtbtravel.com.au/wp-content/uploads/svg/ic_date_range_black_24px.svg' /> ";
the_date("M. Y","<i>","</i>");
the_tags( "<img src='https://www.nx.jtbtravel.com.au/wp-content/uploads/svg/ic_label_outline_black_24px.svg' /> <i>", ", ", "</i> " );
echo "</p>";
?>
</p>

<?php
endwhile;
?>

<p><a href="https://www.nx.jtbtravel.com.au/reviews/"><img src='https://www.nx.jtbtravel.com.au/wp-content/uploads/svg/ic_arrow_forward_black_24px-back.svg' class="back-arrow-reviews"/>Back to all reviews</a></p>

<?php
endif;
?>
</div>
<?php



else:




?>

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

<?php 

endif;

?>