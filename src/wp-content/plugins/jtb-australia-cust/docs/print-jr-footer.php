<?php 


//search for: 
// ||||||||| disable suica 


global $post_id2;
global $wp_query;
$post_id = $wp_query->post->ID;

if ($post_id==false){
	$post_id = $post_id2->id;
}

$counterlabel = 0;
if($post_id != 34329 ):


?>

<div class="container">

<div class="row">
<div class="col-xs-12">


<?php




if (($post_id==false)||($post_id==3574)||($post_id==3562)){
	echo '<h3 class="red-heading" id="otherrailpasses">Japan Rail Passes</h3>';
}else{
	echo '<h3 class="red-heading" id="otherrailpasses">Other Rail Passes</h3>';
}

?>


<div class="ribbon-red-desktop"></div>
</div>
</div>

<div class="row">


<div class="col-sm-12">
	<div class="regional-rail-pass">
	<?php // POST 406 - load
	// check if the repeater field has rows of data
	if( have_rows('regional_rail_pass_section', 406) ):

	 	// loop through the rows of data
	    while ( have_rows('regional_rail_pass_section', 406) ) : the_row();

		//if current item == current page - skip
/*

Footer links

3338 - national - https://www.nx.jtbtravel.com.au/japan-rail-pass/
3343 - national - https://www.nx.jtbtravel.com.au/japan-rail-pass/national-rail-pass/
3347 - hokkaido - https://www.nx.jtbtravel.com.au/japan-rail-pass/jr-hokkaido-pass/
3434 - Central - https://www.nx.jtbtravel.com.au/japan-rail-pass/jr-central-pass/
3403 - East - https://www.nx.jtbtravel.com.au/japan-rail-pass/jr-east/
3458 - West - https://www.nx.jtbtravel.com.au/japan-rail-pass/jr-west-pass/
3480 - East and West - https://www.nx.jtbtravel.com.au/japan-rail-pass/jr-east-and-west/
3474 - shikoku - https://www.nx.jtbtravel.com.au/japan-rail-pass/jr-shikoku-pass/
3478 - kyushu - https://www.nx.jtbtravel.com.au/japan-rail-pass/jr-kyushu-pass/

waiting for AP to make 3 buttons for us 
*/

$title = get_sub_field('rail_pass_name', 406);
$titlelower = strtolower(get_sub_field('rail_pass_name', 406));
if ($post_id==3347){
	if (strpos($titlelower, 'hokkaido') !== false) {
		continue;
	}
}else if ($post_id==3474){
	if (strpos($titlelower, 'shikoku') !== false) {
		continue;
	}
}else if ($post_id==3478){
	if (strpos($titlelower, 'kyushu') !== false) {
		continue;
	}
}else if ($post_id==3338 ||$post_id==3343 ){
	if (strpos($titlelower, 'national') !== false) {
		continue;
	}
}else if ($post_id==3434){
	if (strpos($titlelower, 'central') !== false) {
		continue;
	}
}else if ($post_id==3480){
	if ((strpos($titlelower, 'east') !== false)&&(strpos($titlelower, 'west') !== false)) {
		continue;
	}
}else if ($post_id==3403){
	if ((strpos($titlelower, 'east') !== false)&&(strpos($titlelower, 'west') == false)) {
		continue;
	}
}else if ($post_id==3458){
	if ((strpos($titlelower, 'east') == false)&&(strpos($titlelower, 'west') !== false)) {
		continue;
	}
}else if ($post_id==4043 || 1){ // ||||||||| disable suica 
	if (strpos($titlelower, 'suica') !== false) {
		continue;
	}
}else if ($post_id==xxxxx){
	if (strpos($titlelower, 'xxxxx') !== false) {
		continue;
	}
}

$counterlabel += 1;

	        // display a sub field value
			?>

			  <div class="col-sm-4" id="counterlabeljr<?php echo $counterlabel ; ?>">
				<div class="single">
					<div><a href="<?php the_sub_field('rail_pass_link', 406) ?>">
						<h4 style="background:<?php the_sub_field('rail_pass_colour'); ?>; "><?php echo $title; ?></h4></a>
					</div>
			  		<a href="<?php the_sub_field('rail_pass_link', 406) ?>"><img class="img-responsive" src="<?php the_sub_field('rail_pass_map', 406) ?>" alt="..."></a>
				</div>
			</div>
			<?php ;
	    endwhile;
	else :
	    // no rows found
	endif; ?>
	</div>

	<?php 
	if( current_user_can('editor') || current_user_can('administrator') ){
	?><div class='clear'></div>
 	<div class="page-top">
		<p><a class="button" href="https://www.nx.jtbtravel.com.au/wp-admin/post.php?post=406&action=edit">Edit JR Pass footer</a></p>
	</div>
	<?php } ?>

</div>
 

</div>
</div>

<?php

endif;

?>
