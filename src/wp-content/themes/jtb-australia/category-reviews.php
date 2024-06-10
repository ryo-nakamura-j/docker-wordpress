<?php


get_header();

?>


<div class="container reviewspostlist">

<?php $post = $posts[0]; // Hack. Set $post so that the_date() works. ?>
	<?php /* If this is a category archive */ if (is_category()) { ?>
		<h2 class="review-title">What our customers have said:</h2>
	<?php /* If this is a tag archive */ } elseif( is_tag() ) { ?>
		<h2>Posts Tagged &#8216;<?php single_tag_title(); ?>&#8217;</h2>
	<?php /* If this is a daily archive */ } elseif (is_day()) { ?>
		<h2>Archive for <?php the_time('F jS, Y'); ?>:</h2>
	<?php /* If this is a monthly archive */ } elseif (is_month()) { ?>
		<h2>Archive for <?php the_time('F, Y'); ?>:</h2>
	<?php /* If this is a yearly archive */ } elseif (is_year()) { ?>
		<h2>Archive for <?php the_time('Y'); ?>:</h2>
	<?php /* If this is an author archive */ } elseif (is_author()) { ?>
		<h2>Author Archive</h2>
	<?php /* If this is a paged archive */ } elseif (isset($_GET['paged']) && !empty($_GET['paged'])) { ?>
		<h2>Blog Archives</h2>
<?php } ?>


<div id="categorynavigation">
<?php posts_nav_link(" ","<div class='left'><img src='https://www.nx.jtbtravel.com.au/wp-content/uploads/svg/ic_arrow_forward_black_24px-back.svg' /> Previous page</div>","<div class='right'>Next Page <img src='https://www.nx.jtbtravel.com.au/wp-content/uploads/svg/ic_arrow_forward_black_24px.svg' /></div>"); ?>

</div>


<?php if(have_posts()) : ?><?php while(have_posts()) : the_post(); ?>

<div class="post">
<h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>


	<div class="entry">
	<?php the_content(); ?>

        <p class="postmetadata">
        <?php 
        
        echo "<p class='taggedpost'>";
        if (get_the_date()) { 
                the_date("F Y","<img src='https://www.nx.jtbtravel.com.au/wp-content/uploads/svg/ic_date_range_black_24px.svg' /><i>","</i>");}
        if (get_the_tags()){the_tags( "<img src='https://www.nx.jtbtravel.com.au/wp-content/uploads/svg/ic_label_outline_black_24px.svg' /> <i>", ", ", "</i> " );
                echo "</p>";}
        ?>
        </p>

	</div>

</div>



<?php endwhile; ?>

<div id="categorynavigation">
<?php posts_nav_link(" ","<div class='left'><img src='https://www.nx.jtbtravel.com.au/wp-content/uploads/svg/ic_arrow_forward_black_24px-back.svg' /> Previous page</div>","<div class='right'>Next Page <img src='https://www.nx.jtbtravel.com.au/wp-content/uploads/svg/ic_arrow_forward_black_24px.svg' /></div>"); ?>

</div>

</div></div>


<?php endif; ?>

</div>


<?php

get_footer();

?>