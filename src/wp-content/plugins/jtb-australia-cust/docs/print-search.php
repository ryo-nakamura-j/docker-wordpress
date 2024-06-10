
<div class="home-content four grid container search_page">

<?php 
if (have_posts()) : 
$search_term = "";
if(isset($_GET['s']) ){
	$search_term .= "" . $_GET['s'] ;
}
?>

	<h2 class="pagetitle"><i class="fa fa-search" aria-hidden="true"></i> Search Results - <small id="search_term" class="blue-text" ><?php echo $search_term; ?></small></h2>

<div class="ribon-red-desktop"></div>
<form action="https://www.nx.jtbtravel.com.au/" method="get" id="adminbarsearch" class="" _lpchecked="1">
<div class="row">
<div class="col-sm-8 col-xs-12">
<input class="adminbar-input" name="s"   type="text" value="" maxlength="150" autocomplete="off">
</div>
<div class="col-sm-4 col-xs-12">
<input type="submit" class="wpcf7-form-control wpcf7-submit btnLarge" value="Search">
</div>
</div>
</form>


	<div class="navigation">
		<div class="alignleft"><?php previous_posts_link('&laquo; Previous Page') ?></div><div class="alignright"><?php next_posts_link('Next Page &raquo;') ?></div></div><?php $count=0;
	while (have_posts()) : the_post(); 
	if ($count%4==0){echo '<div class="row">';}
	?><div class="col-sm-3 col-xs-12"><div class="thumbnail"><div class="caption"><div <?php post_class() ?>><h3 id="post-<?php the_ID(); ?>"><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h3>
		<small>Parent page: <?php if (wp_get_post_parent_id(get_the_ID())){$parentid = wp_get_post_parent_id(get_the_ID());  ?><a href="<?php echo get_permalink($parentid); ?>" rel="bookmark"  ><?php echo get_the_title($parentid);}else{echo '<a href="/">Home</a>';} ?></a><?php the_excerpt(); ?></small><?php edit_post_link('Edit', '<br />', ''); ?></div></div></div></div><?php

	if ($count%4==3){echo '</div>';}
	$count++;
	endwhile; ?><div class="navigation"><div class="alignleft"><?php previous_posts_link('&laquo; Previous Page') ?></div><div class="alignright"><?php next_posts_link('Next Page &raquo;') ?></div></div>

<?php else : ?>

	<h2 class="center">No posts found. Try a different search?</h2>
	<?php get_search_form(); ?>

<?php endif; ?>

</div>