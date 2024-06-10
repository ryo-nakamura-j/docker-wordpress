
<div class="row">

<?php  $counter=0; $post_id = get_the_ID(); wp_reset_query() ; the_post(30364); 

//JR EAST ONLY !!!
//jtb-widget f="jr-flowchart-4-jr-east-only"]
//jtb-widget f="jr-flowchart-5-jr-east-only"]
//jtb-widget f="jr-flowchart-5"]
?>

<div class="col-sm-12">
	<div class="jrpass-bookingflow thumbnail">
	<?php  
	// check if the repeater field has rows of data

		if( have_rows('gallery', 30364) ):

	 	// loop through the rows of data
	    while ( have_rows('gallery', 30364) ) : the_row();
		//if current item == current page - skip





	if( have_rows('3img', 30364) ):

	 	// loop through the rows of data
	    while ( have_rows('3img', 30364) ) : the_row();
		//if current item == current page - skip



$counter +=1;
	?>

	  <div class="col-sm-3">
		<div class="single">
		<img class="img-responsive" src="<?php echo get_sub_field('img3', 30364)['url']; ?>" alt="..."><?php if ($counter<4){ ?><i class="fa fa fa-arrow-right icon" aria-hidden="true" ></i><i class="fa fa-arrow-down iconmob" aria-hidden="true"></i><?php } ?>

			<div> 
				<p ><?php echo the_sub_field('caption', 30364) ?></p> 
			</div>
	  		
		</div>
	</div>
	<?php ;
	    endwhile;
	else :
	    // no rows found
	endif;

	  endwhile;
	endif;
	 ?>
	</div>

	<?php 
	if( current_user_can('editor') || current_user_can('administrator') ){
	?><div class='clear'></div>
 	<div class="page-top">
		<p><a class="button" target="_blank" href="https://www.nx.jtbtravel.com.au/wp-admin/post.php?post=30364&action=edit">Edit chart</a></p>
	</div>
	<?php } ?>

</div>

<?php  wp_reset_query() ; ?>

</div>
 