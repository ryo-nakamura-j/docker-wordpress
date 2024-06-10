
	<div class="airport-transfers four grid">
		<h3>Airport Transfers</h3>
		<div class="ribon-red-desktop"></div>
		<div class="row"> 

			<!-- Four grid -->

			<?php
			// check if the repeater field has rows of data
			if( have_rows('airport_transfers') ):
			 
			 	// loop through the rows of data
			    while ( have_rows('airport_transfers') ) : the_row();
			 	$link2 = get_sub_field('box_link') ; 
			 	$texturl = get_sub_field('texturl');
			 	if ($texturl =="hidden"){
			 		continue;
			 	}
			        // display a sub field value
					?>

					  <div class="col-sm-3">
						<div class="thumbnail">

							<?php 
								    if (($texturl != "") && ($texturl != null) && ($texturl != undefined) ){
								    	$link2 = $texturl;
								    }
								?>


							<div class="caption"><a href="<?php  echo $link2; ?>">
								<h4><?php the_sub_field('box_title') ?></h4></a>
							</div>
					  		<a href="<?php  echo $link2;  ?>"><img src="<?php the_sub_field('box_image') ?>" alt=""></a>
						</div>
					</div>
					<?php ;
			    endwhile;
			else :
			    // no rows found
			endif; ?>
		</div>
	</div>