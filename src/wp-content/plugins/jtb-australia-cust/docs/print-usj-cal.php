<?php 

//20190110-20190131-1
//20190201-20190322-2

/*2023 hide 3 of the calendars, in the rubix css file and hiding divs, no border, no min height 
#divCal4,#divCal5,#divCal6{display:none;}
.noborder2{border:none !important ;height:5px !important ;min-height:5px !important ;}
*/
/*2023 hide 3 of the USJ calendars */



/*
ID number for 3 pages 
$a1day = 20511;
$a15day = 34804;
$a2day = 30123;

price date data pages
1day = 30146
1.5day = 34828
2day = 34826

*/


//30146 id
$date_data="";
$counter=0; $post_id = get_the_ID();



$editdata = 30146;
if($post_id == 34804){
	$editdata = 34828;
}else if($post_id == 30123){
	$editdata = 34826;
}


/*
Old way - there was only 1 cal on one page with price list and calendar list.
backup bottom of this page 

*/


if ( ($post_id == 34804) || ($post_id == 30123) ){
	



 wp_reset_query() ; the_post(30146);  
$counterlvl2 =0;
$pricelist=[];
while ( have_rows('gallery', 30146) ) : the_row();
  $counter+=1;
  if($counter==1){
    while ( have_rows('3img', 30146) ) : the_row();
      $counterlvl2++;
      if($counterlvl2==1){continue;}

        if(get_sub_field('caption', 30146)){
          $date_data .= get_sub_field('caption', 30146) . "@";
        } 

    endwhile;
  }
  if($counter==2){
	  break;
    $counterlvl2=0;
    while ( have_rows('3img', $editdata) ) : the_row();
      $counterlvl2++;
      if($counterlvl2==1){continue;}

        if(get_sub_field('caption', $editdata)){
          $pricelist[]= get_sub_field('caption', $editdata) ;
        } 

    endwhile;
  }
    if($counter==3){
      break;
  }
endwhile;




$counter=0;

 wp_reset_query() ; the_post($editdata);  
$counterlvl2 =0;
$pricelist=[];
while ( have_rows('gallery', $editdata) ) : the_row();
  $counter+=1;
  if($counter==1){
	  continue; 
  }
  if($counter==2){
    $counterlvl2=0;
    while ( have_rows('3img', $editdata) ) : the_row();
      $counterlvl2++;
      if($counterlvl2==1){continue;}

        if(get_sub_field('caption', $editdata)){
          $pricelist[]= get_sub_field('caption', $editdata) ;
        } 

    endwhile;
  }
    if($counter==3){
      break;
  }
endwhile;


}else{
	
	
 wp_reset_query() ; the_post($editdata);  
$counterlvl2 =0;
$pricelist=[];
while ( have_rows('gallery', $editdata) ) : the_row();
  $counter+=1;
  if($counter==1){
    while ( have_rows('3img', $editdata) ) : the_row();
      $counterlvl2++;
      if($counterlvl2==1){continue;}

        if(get_sub_field('caption', $editdata)){
          $date_data .= get_sub_field('caption', $editdata) . "@";
        } 

    endwhile;
  }
  if($counter==2){
    $counterlvl2=0;
    while ( have_rows('3img', $editdata) ) : the_row();
      $counterlvl2++;
      if($counterlvl2==1){continue;}

        if(get_sub_field('caption', $editdata)){
          $pricelist[]= get_sub_field('caption', $editdata) ;
        } 

    endwhile;
  }
    if($counter==3){
      break;
  }
endwhile;

}





/* end of calendar section data load */

$pricelist[]= "";$pricelist[]= "";$pricelist[]= "";$pricelist[]= "";
$pricelist[]= "";$pricelist[]= "";$pricelist[]= "";$pricelist[]= "";
$pricelist[]= "";$pricelist[]= "";
echo '<input type="hidden" name="dates" id="dateslistjtb" value="'.$date_data.'" />';

?>


<div class="row">
<div class="col-sm-12 col-md-8  ">

<div class="calendar-wrapper  col-xs-4 ">
  <div id="divCal"></div>
</div>

<div class="calendar-wrapper col-xs-4">
  <div id="divCal2"></div>
</div>

<div class="calendar-wrapper col-xs-4 ">
  <div id="divCal3"></div>
</div>

<div class="clear"></div>

<div class="calendar-wrapper noborder2 col-xs-4 ">
  <div id="divCal4"></div>
</div>
<div class="calendar-wrapper  noborder2 col-xs-4 ">
  <div id="divCal5"></div>
</div>
<div class="calendar-wrapper  noborder2 col-xs-4">
  <div id="divCal6"></div>
</div><div class="clear"></div>

</div> 
<div class="col-sm-12 col-md-4  ">

 <table class="usj-cal-key">
<tbody>
<tr>
<td><strong> </strong></td>
<td><strong>Adult</strong></td>
<td><strong>Child (4-11 yrs)</strong></td>
</tr>
<tr>
<td class="one">Price</td>
<td>$<?php echo $pricelist[0]; ?></td>
<td>$<?php echo $pricelist[1]; ?></td>
</tr>
<tr>
<td class="two">Price</td>
<td>$<?php echo $pricelist[2]; ?></td>
<td>$<?php echo $pricelist[3]; ?></td>
</tr>
<tr>
<td class="three">Price</td>
<td>$<?php echo $pricelist[4]; ?></td>
<td>$<?php echo $pricelist[5]; ?></td>
</tr>
<tr>
<td class="four">Price</td>
<td>$<?php echo $pricelist[6]; ?></td>
<td>$<?php echo $pricelist[7]; ?></td>
</tr>
<tr>
<td class="five">Price</td>
<td>$<?php echo $pricelist[8]; ?></td>
<td>$<?php echo $pricelist[9]; ?></td>
</tr>
</tbody>
</table>

</div></div>

<?php
if( current_user_can('editor') || current_user_can('administrator') ){
?>
<br />
<a href="https://www.nx.jtbtravel.com.au/wp-admin/post.php?post=<?php echo $editdata; ?>&action=edit#acf-group_5822553bf0549" target="_blank" class="button" >EDIT</a>
<?php
}

/*backup of old cal data load code - now have 3 pages, same cal, dif prices */

/*

 wp_reset_query() ; the_post($editdata);  
$counterlvl2 =0;
$pricelist=[];
while ( have_rows('gallery', $editdata) ) : the_row();
  $counter+=1;
  if($counter==1){
    while ( have_rows('3img', $editdata) ) : the_row();
      $counterlvl2++;
      if($counterlvl2==1){continue;}

        if(get_sub_field('caption', $editdata)){
          $date_data .= get_sub_field('caption', $editdata) . "@";
        } 

    endwhile;
  }
  if($counter==2){
    $counterlvl2=0;
    while ( have_rows('3img', $editdata) ) : the_row();
      $counterlvl2++;
      if($counterlvl2==1){continue;}

        if(get_sub_field('caption', $editdata)){
          $pricelist[]= get_sub_field('caption', $editdata) ;
        } 

    endwhile;
  }
    if($counter==3){
      break;
  }
endwhile;

*/


?>


