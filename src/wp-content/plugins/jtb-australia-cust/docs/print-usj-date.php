<?php

//item # 3 - ID 791
//20190110
$date_data="";

if( ! is_page(791) ){

$counter=0; $post_id = get_the_ID(); wp_reset_query() ; the_post(791);  
$counterlvl2 =0;


while ( have_rows('gallery', 791) ) : the_row();
	$counter+=1;
	if ($counter == 2){
		break;
	}
	while ( have_rows('3img', 791) ) : the_row();
		$counterlvl2++;
		if($counterlvl2 ==3 ){
			if(get_sub_field('caption', 791)){
				$date_data = get_sub_field('caption', 791);
				break;
			} 
		}
	endwhile;
endwhile;

if($date_data == "custom_value_in_plugin"){

$text22 = '19th of October, 3rd of September and 1st of September 2019, depending on the pass; <a href="https://www.nx.jtbtravel.com.au/tickets/universal-studios-japan/express-pass/#tab2link" target="_blank">Click here for details</a>';
//custom_value_in_plugin
	echo $text22;

// $text22 =  str_replace('target="_blank"', '', $text22 ) ;

if(!get_option('jtbau_usj_date')){
    update_option('jtbau_usj_date',  $text22 );
}

if(get_option('jtbau_usj_date') !=$text22){
	update_option('jtbau_usj_date',  $text22 );
}
if(current_user_can('edit_posts')){
	echo ' - <small><a class="red-text" target="_blank" href="https://www.nx.jtbtravel.com.au/wp-admin/post.php?post=791&action=edit#acf-group_5822553bf0549">edit_URL_admin_only</a></small>';
}

wp_reset_query() ;



}else{



$day = ["1st","2nd","3rd","4th","5th","6th","7th","8th","9th","10th","11th","12th","13th","14th","15th","16th","17th","18th","19th","20th","21st","22nd","23rd","24th","25th","26th","27th","28th","29th","30th","31st"];

$month = ["JANUARY","FEBRUARY","MARCH","APRIL","MAY","JUNE","JULY","AUGUST","SEPTEMBER","OCTOBER","NOVEMBER","DECEMBER"];

 
echo   $day[(int)substr($date_data,6,2)-1] ." of ". $month[(int)substr($date_data,4,2)-1] . " ".substr($date_data,0,4);
 


if(current_user_can('edit_posts')){
	echo ' - <small><a class="red-text" target="_blank" href="https://www.nx.jtbtravel.com.au/wp-admin/post.php?post=791&action=edit#acf-group_5822553bf0549">edit_URL_admin_only</a></small>';
}

if(!get_option('jtbau_usj_date')){
    update_option('jtbau_usj_date', $day[(int)substr($date_data,6,2)-1] ." of ". $month[(int)substr($date_data,4,2)-1] . " ".substr($date_data,0,4) );
}

if(get_option('jtbau_usj_date') !=  $day[(int)substr($date_data,6,2)-1] ." of ". $month[(int)substr($date_data,4,2)-1] . " ".substr($date_data,0,4) ){
	update_option('jtbau_usj_date', $day[(int)substr($date_data,6,2)-1] ." of ". $month[(int)substr($date_data,4,2)-1] . " ".substr($date_data,0,4) );
}


wp_reset_query() ;

}//end - custom message in code.

}else{
	//echo value cache from global var. - don't mess up the loop on system page . 
	//echo "6TH OF JANUARY 2019";
	echo get_option("jtbau_usj_date");
if(current_user_can('edit_posts')){
	echo ' - <small><a class="red-text" target="_blank" href="https://www.nx.jtbtravel.com.au/wp-admin/post.php?post=791&action=edit#acf-group_5822553bf0549">edit_URL_admin_only</a></small>';
}
}






?>
