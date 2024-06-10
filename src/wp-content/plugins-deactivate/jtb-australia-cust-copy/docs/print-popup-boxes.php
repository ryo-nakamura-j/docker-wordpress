
<?php // hidden popup boxes and JS ?>

  

<?php // Hidden popup - Flight Search ?>

<div id="myModal-b" class="modal"><!-- Modal content --> <div class="modal-content"> <span class="close">×</span>

<?php
if( (get_the_ID()!=73) && (wp_get_post_parent_id(get_the_ID())!=73) && (wp_get_post_parent_id(get_the_ID())!=20517) ){
  echo do_shortcode('[jtb-widget f="flights"]');
}

?>

</div></div> 




<?php // Hidden popup - Flight Search 
if(get_the_ID()==21607):?>
<div id="myModal-dt" class="modal"><!-- Modal content --> <div class="modal-content"> <span class="close">×</span>
<h2>TOUR ENQUIRY</h2>
<?php
  echo do_shortcode('[jtb-widget f="contact-form"]');
?>
</div></div> 
<?php endif; ?>



<?php // Hidden popup - Hyperdia
if(get_the_ID()==3343||get_the_ID()==3562):?>
<div id="myModal-hyperdia" class="modal"><!-- Modal content --> <div class="modal-content"> <span class="close">×</span>
<h2>Hyperdia Transport Maps and Prices</h2>
<p>You are opening a link to an external non-JTB website</p>
<a href="http://www.hyperdia.com/en/" target="_blank"><button>Ok - continue</button></a> <button class="close" >Close</button>
</div></div> 
<?php endif; ?>


<?php // Hidden popup - Web  Search ?>

<div id="myModal-c" class="modal"><!-- Modal content --> <div class="modal-content"> <span class="close">×</span>

<h2 ><i class="fa fa-search" aria-hidden="true"></i> Search the website</h2>
<p>Search our website for some key-words or phrases.</p>
<form action="https://www.nx.jtbtravel.com.au/" method="get" id="adminbarsearch" class="" _lpchecked="1">
<div class="row">
<div class="col-sm-8 col-xs-12">
<input class="adminbar-input" id="websearchbox" name="s"   type="text" value="" maxlength="150" autocomplete="off">
</div>
<div class="col-sm-4 col-xs-12">
<input type="submit" class="wpcf7-form-control wpcf7-submit btnLarge" value="Search">
</div>
</div>
</form>

</div></div> 


<?php
if (is_page(22779)||is_page(23438)): //if it's the agent booking page - add a JR Pass selection popupbox
// Hidden popup - Web  Search 
//echo box
echo get_option('jr_list_popup');

endif;

if (is_page(35517)):
// Same as above, but 2023 prices V2
echo get_option('jr_list_popup_2023');
endif;

?>

