
<?php
require_once( '/home/jtbtrave/public_html/wp-load.php' ); 

//do_shortcode('[jtb-widget f="ghibli-cal"]');


?>
 <div class="clear"></div>




<?php 

//20190110-20190131-1
//20190201-20190322-2

//30203 id
$date_data="";
$counter=0; $post_id = get_the_ID(); wp_reset_query() ; the_post(30203);  
$counterlvl2 =0;
$pricelist=[];
while ( have_rows('gallery', 30203) ) : the_row();
  $counter+=1;
  if($counter==1){
    while ( have_rows('3img', 30203) ) : the_row();
      $counterlvl2++;
      if($counterlvl2==1){continue;}

        if(get_sub_field('caption', 30203)){
          $date_data .= get_sub_field('caption', 30203) . "@";
        } 

    endwhile;
  }else if($counter==2){
       break;
  }
  
endwhile;
$pricelist[]= "";$pricelist[]= "";$pricelist[]= "";$pricelist[]= "";
$pricelist[]= "";$pricelist[]= "";$pricelist[]= "";$pricelist[]= "";
echo '<input type="hidden" name="dates" id="dateslistjtb" value="'.$date_data.'" />';

?>


<div class="row" id="ghib-data-2">
<div class="col-sm-12 col-md-12  ">


<table class="usj-cal-key">
<tbody>
<tr>
<td class="normal one">Available</td>
<td class="normal four">Not available</td>
</tr>
</tbody>
</table>




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

<div class="calendar-wrapper col-xs-4 ">
  <div id="divCal4"></div>
</div>
<div class="calendar-wrapper col-xs-4 ">
  <div id="divCal5"></div>
</div>
<div class="calendar-wrapper col-xs-4">
  <div id="divCal6"></div>
</div><div class="clear"></div>

</div> 
 



</div>
<div class="clear"></div>
<br /> 


<!--<script src="https://nx.jtbtravel.com.au/wp-content/plugins/jtb-australia-cust/js/ghibli-cal.js"></script> -->
