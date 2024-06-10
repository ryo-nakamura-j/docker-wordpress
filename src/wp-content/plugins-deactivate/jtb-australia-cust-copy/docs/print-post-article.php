<?php

if (in_category("blog"))://if category reviews, custom template
?>
<div class="container reviewsdetailpage">
<?php
if(have_posts()) :
while(have_posts()) :
?>
<br />
<nav id="categorynavigation">
<?php previous_post_link('%link', '<div class="left"><img src="https://www.nx.jtbtravel.com.au/wp-content/uploads/svg/ic_arrow_forward_black_24px-back.svg" /> %title </div>', 1); ?>
<?php next_post_link('%link', '<div class="right"> %title  <img src="https://www.nx.jtbtravel.com.au/wp-content/uploads/svg/ic_arrow_forward_black_24px.svg" /></div>', 1); ?>
</nav><!-- #nav-single -->


<h1 id="blogtitle"><?php the_title(); ?></h1>




<?php
$authors=['Aude','Beth','Carly','Jacquie','Joshua','Tak','Michelle','Yuna'];
$authors_image_bios=[
['Aude','https://www.nx.jtbtravel.com.au/wp-content/uploads/2020/04/Aude.jpg',''],
['Beth','',''],
['Carly','',''],
['Jacquie','https://www.nx.jtbtravel.com.au/wp-content/uploads/2020/04/Jacquie.jpg',''],
['Joshua','https://www.nx.jtbtravel.com.au/wp-content/uploads/2020/04/Joshua.jpg',''],
['Tak','https://www.nx.jtbtravel.com.au/wp-content/uploads/2020/04/tak.jpg',''],
['Michelle','',''],
['Yuna','https://www.nx.jtbtravel.com.au/wp-content/uploads/2021/01/yuna.jpg','']


];
$author = "";$author_url="";
$authorimagebio = "";
$authorimagebiotext = "";
$tagdata = "";
$tt = get_the_tags(get_the_id());
foreach ($tt as $tag ) {
	if(    in_array($tag->name,$authors) ){
		$author = "$tag->name";$author_url=get_tag_link( $tag->term_id );
		foreach ($authors_image_bios as $key  ) {
			if($key[0] == $tag->name){
				if($key[1]){
					$authorimagebio .= '<a href="'.$author_url.'" >';
					$authorimagebio .= '<img src="'.$key[1].'" class="authorbiopic" />';
					$authorimagebio .= '</a>';
				}
				if($key[2]){
					$authorimagebiotext .= ' - '.$key[2]; 
				}
			}
		}


		//bioauthor
		continue;
	}
	$tagdata .= "<img src='https://www.nx.jtbtravel.com.au/wp-content/uploads/svg/ic_label_outline_black_24px.svg' /> <i>";
	$tagdata .= '<a href="'.get_tag_link( $tag->term_id ).'">'.$tag->name.'</a>';
	$tagdata .= '</i> ';
}
if($author){
	echo '<div class="authorbiojtb">'.$authorimagebio.'<img src="https://www.nx.jtbtravel.com.au/wp-content/uploads/svg/edit-24px2.svg" class="icon24"> <a href="'.$author_url.'"> Post by '.$author.'</a>'.$authorimagebiotext;
}

	?>


<div class="socialbuttonjtb<?php if($author){echo ' social-padding2'; } ?>">
Share:
<a href="https://www.facebook.com/sharer/sharer.php?u=<?php
echo str_replace(" ","%20",str_replace(":","%3A",get_permalink() ));
 ?>" target="_blank"><img src="https://www.nx.jtbtravel.com.au/wp-content/uploads/2020/04/fb-share.png" class="socialbuttonjtb"></a>

<a href="https://twitter.com/intent/tweet?text=<?php
echo  str_replace("/","-",str_replace(" ","%20",str_replace(":","%3A",get_the_title() )));
echo "%20-%20";
echo str_replace(" ","%20",str_replace(":","%3A",get_permalink() ));
 ?>" target="_blank"><img src="https://www.nx.jtbtravel.com.au/wp-content/uploads/2020/04/tweet.png" class="socialbuttonjtb"></a>
</div>


<?php

if($author){
echo '</div>';
}




the_post_thumbnail();
the_post();
the_content();
?>

<p class="postmetadata">
<?php 

echo "<p class='taggedpost'>";
echo "<img src='https://www.nx.jtbtravel.com.au/wp-content/uploads/svg/ic_date_range_black_24px.svg' /> ";
the_date("M. Y","<i>","</i>");
//the_tags( "<img src='https://www.nx.jtbtravel.com.au/wp-content/uploads/svg/ic_label_outline_black_24px.svg' /> <i>", ", ", "</i> " );
//the_tags( "<img src='https://www.nx.jtbtravel.com.au/wp-content/uploads/svg/ic_label_outline_black_24px.svg' /> <i>", ", ", "</i> " );





echo $tagdata; 

if($author){
	echo '<img src="https://www.nx.jtbtravel.com.au/wp-content/uploads/svg/edit-24px2.svg" class="icon24"> <a href="'.$author_url.'">'.$author.'</a>';
}


echo "</p>";
?>
</p>

<?php
endwhile;
?>

<p><br /><a href="https://www.nx.jtbtravel.com.au/blog/"><img src='https://www.nx.jtbtravel.com.au/wp-content/uploads/svg/ic_arrow_forward_black_24px-back.svg' class="back-arrow-reviews"/>Back to all articles</a></p>


<?php

echo '<br />';

post_carousel_id('32313');


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