
<div class="row">

<?php  $counter=0; $post_id = get_the_ID(); wp_reset_query() ; the_post(35622); ?>

<div class="col-sm-12">
	<div class="jrpass-bookingflow thumbnail">
	<?php  
	// check if the repeater field has rows of data
	if( have_rows('regional_rail_pass_section', 35622) ):

	 	// loop through the rows of data
	    while ( have_rows('regional_rail_pass_section', 35622) ) : the_row();
		//if current item == current page - skip 


$title = get_sub_field('rail_pass_name', 35622);
$titlelower = strtolower(get_sub_field('rail_pass_name', 35622));
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
}else if ($post_id==99999){
	if (strpos($titlelower, 'xxxxxxx') !== false) {
		continue;
	}
}else if ($post_id==99999){
	if (strpos($titlelower, 'xxxxxxx') !== false) {
		continue;
	}
}

$counter +=1;
	?>

	  <div class="col-sm-3">
		<div class="single">
		<img class="img-responsive" src="<?php the_sub_field('rail_pass_map', 35622) ?>" alt="..."><?php if ($counter<4){ ?><i class="fa fa fa-arrow-right icon" aria-hidden="true" ></i><i class="fa fa-arrow-down iconmob" aria-hidden="true"></i><?php } ?>

			<div> 
				<p ><?php echo $title ;
 
 

//Passes are issued within 24 hours of receiving your order and will be available for collection the following business day from JTB Sydney or Melbourne offices.
				?></p> 
				
			</div>
	  		
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
		<p><a class="button"  target="_blank" href="https://www.nx.jtbtravel.com.au/wp-admin/post.php?post=35622&action=edit">Edit chart</a></p>
	</div>
	<?php } ?>

</div>

<?php  wp_reset_query() ; ?>

</div>

<?php
/*
top links anchor
3434 - Central - https://www.nx.jtbtravel.com.au/japan-rail-pass/jr-central-pass/
3403 - East - https://www.nx.jtbtravel.com.au/japan-rail-pass/jr-east/
3478 - kyushu - https://www.nx.jtbtravel.com.au/japan-rail-pass/jr-kyushu-pass/
3458 - West - https://www.nx.jtbtravel.com.au/japan-rail-pass/jr-west-pass/

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
*/
?>