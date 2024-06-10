<?php

require_once( '/home/jtbtrave/public_html/wp-load.php' );

$jr_id_list=array();
$jr_id_list_reverse=array();
$jr_id_text_list="";
$jr_calc_3_passes="";//112@@@119@@@121
$counter=0;   wp_reset_query() ; the_post(22954); 

// check if the repeater field has rows of data
if( have_rows('jr_pass_type', 22954) ):
// loop through the rows of data
while ( have_rows('jr_pass_type', 22954) ) : the_row();
//if current item == current page - skip

if( have_rows('tour_item', 22954) ):
while ( have_rows('tour_item', 22954) ) : the_row();

if(get_sub_field('id_number', 22954) && get_sub_field('title', 22954)  ){
  $jr_id_list[(string)get_sub_field('title', 22954)]=get_sub_field('id_number', 22954);
  $jr_id_list_reverse[(string)get_sub_field('id_number', 22954)]=get_sub_field('title', 22954);
  $jr_id_text_list .= (string)get_sub_field('id_number', 22954) . "%2C";
}

//$jr .= get_sub_field('id_number', 22954)."\n";
endwhile;
endif;
//array_push($jr_list , "");
endwhile;
endif;

$jr_name_price=array();
$jr_prices = file_get_contents ( 'https://agent.nx.jtbtravel.com.au/retail-live/rates?ids='.$jr_id_text_list.'&date=' .date ( 'Y-m-d'));
foreach (json_decode($jr_prices) as  $value) {
	$prices1 =$value->adult ;
	$prices2 = $value->child; 
	$tmp1=substr($prices1, -2);
	$tmp2=substr($prices2, -2);
	$prices1 =substr($prices1, 0,-2);
	$prices2 = substr($prices2, 0,-2);
	if($tmp1!="00"){
		$prices1 = $prices1.".".$tmp1;
	}
	if($tmp2!="00"){
		$prices2 = $prices2.".".$tmp2;
	}
	$jr_name_price[(string)$jr_id_list_reverse[$value->productid]]="<br /><span class='unbold'>Adult: $" .$prices1 ." Child: $".$prices2."</span>";

//load 3 pass prices for JR Calc field
  if($value->productid == 112){//7-day @@@ - # $ % 
    $jr_calc_3_passes .= "@@@".$prices1 . "#@@@";
  }if($value->productid == 119){//7-day @@@ - # $ % 
    $jr_calc_3_passes .= "@@@".$prices1 . "$@@@";
  }if($value->productid == 121){//7-day @@@ - # $ % 
    $jr_calc_3_passes .= "@@@".$prices1 . "%@@@";
  }

	//echo "ID: ".$value->productid . " Adult: " .$value->adult ." Child: ".$value->child .'<br />';
} 





/* Save all JR Prices: */
/* Save jr pass list popup-box */

echo "@@@";
 
$jr_list_popup_text="";
$jr_list=array();
$counter=0;  wp_reset_query() ; the_post(22954); 

// check if the repeater field has rows of data
if( have_rows('jr_pass_type', 22954) ):
// loop through the rows of data
while ( have_rows('jr_pass_type', 22954) ) : the_row();
//if current item == current page - skip

array_push($jr_list , get_sub_field('category_title', 22954)); 

if( have_rows('tour_item', 22954) ):
while ( have_rows('tour_item', 22954) ) : the_row();
array_push($jr_list ,  get_sub_field('title', 22954));
//$jr .= get_sub_field('id_number', 22954)."\n";
endwhile;
endif;
array_push($jr_list , "");
endwhile;
endif;


for ($i=1; $i < 2; $i++) {
  $new=true; 
  $jr_list_popup_text .=  '<input id="ignoretitle" type="hidden" value=""><input id="temp2" type="hidden" value=""><input id="temp3" type="hidden" value=""><input id="temp4" type="hidden" value=""><input id="allsamepass" type="hidden" value=""><div id="myModal-'.(string)$i.'" class="modal"><div class="modal-content"> <span class="close"><i class="material-icons">close</i></span><h3>JR PASS SELECTION <span class="red2">*</span></h3><p>The following prices listed are the retail prices.</p><input type="hidden" id="personid" value="" ><input type="hidden" id="allpassessame2" value="no" >';
//echo $category;
  $c=0;
  //echo"<pre>".$jr."</pre>";
  while(!empty($jr_list)){ 
    //echo '<br>'.$jr.'<br>';
    if (empty($jr_list)){
      break;
    }
    if($new){
      $category = array_shift($jr_list);
      $jr=$category;
      $new=false;
      if($c==0){
        $jr_list_popup_text .=  '<div class="p50perc">';
      }else if($c==3){
        $jr_list_popup_text .=  '</div><div class="p50perc">';
      }
      $c +=1;
      $jr_list_popup_text .=  '<span class="close pushupclose"><span>Close popup box</span> <i class="material-icons">close</i></span><h4>'.$category.'</h4>';
    }
    $temp = array_shift($jr_list);
    $passname = $temp;
    $jr=$temp;
    if(($temp=="")||($temp==" ")){
      $new=true;
      continue;
    }
    $temp = str_replace(" ", "_" , $category . "_" . $temp);
    $temp = $temp . $jr_name_price[$passname];
    $temp = str_replace(": $", "_" ,   $temp);
    $temp = str_replace(": $", "_" ,   $temp);
    $temp = str_replace("<br /><span class='unbold'>", "_" ,   $temp);
    $temp = str_replace("</span>", "" ,   $temp);
    $temp = str_replace(" ", "_" ,   $temp);
    
    $jr_list_popup_text .=  '<p><input type="checkbox" name="'.$temp.'" value="'.$temp.'" id="'.$temp.'" /><label for="'.$temp.'">'.$passname.$jr_name_price[$passname].'</label><span class="'.$temp.' cont_buttons"></span></p>';

  }
  $jr_list_popup_text .=  '</div><div style="clear:both;width:100%;"></div><hr style="border: 1px solid #ccc;" /> ';
  $jr_list_popup_text .=  '</div></div>';
}

 
update_option( 'jr_list_popup', $jr_list_popup_text ); 
//add_option('jr_list_3_calc',"@@@");
update_option( 'jr_list_3_calc', $jr_calc_3_passes ); 







//////////////////////////////////////////////////////////////

// CLONE 2023 new prices 













?>