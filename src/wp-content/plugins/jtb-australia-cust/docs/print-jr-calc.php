
<?php


/*


Something I can't copy from the original system is that they don't need to type in every combination of locations - some location pairs are built up from other location pairs...

EG - 

Kyoto -> Tokyo

is this:

Kyoto -> Osaka
plus
Osaka -> Tokyo

So I need to add a list of from-to prices 
AND
a list of location pairs that are equivilant to multiple location pairs above - adding all the prices together

Tokyo
Aomori
Beppu
Fukuoka
Hakodate
Hakone
Himeji
Hiroshima
Kagoshima
Kamakura
Kanazawa
Kansai Airport
Kinosaki Onsen
Kochi
Kyoto
Nagano
Nagasaki
Nagoya
Nara
Narita Airport
Nikko
Okayama
Osaka
Sapporo
Sendai
Takayama


*/

?>
<div id="jr_calc">

<div id="calcblock">
<input type="hidden" name="total1" id="total1" value="0" />
<input type="hidden" name="total2" id="total2" value="0" />
<br /><br />

</div>

<button id="addcalc" onclick="addtokyo();" style="display: none;">Add price</button>
<h3>Add Trip:</h3>
<div id="selection">
</div><div id="selection2">
</div><div id="selection3">
</div><div style="clear: both;"></div>

<?php
// Generate 3 pass prices
//801% - 626$ - 393#
$passes = get_option( 'jr_list_3_calc');
$passes = str_replace( "@@@@@@" , "@@@", $passes ) ;
$pass_array = explode("@@@", $passes);
$p7 = "";$p14="";$p21="";
foreach ($pass_array as  $value) {
	if($value[strlen($value)-1 ] == "#"){
		$p7 = substr($value,0,strlen($value)-1);
	}	if($value[strlen($value)-1 ] == "$"){
		$p14 = substr($value,0,strlen($value)-1);
	}	if($value[strlen($value)-1 ] == "%"){
		$p21 = substr($value,0,strlen($value)-1);
	}
}


/* load data from WP page data section */
$exchange_rate=0;

/*
 wp_reset_query() ; the_post(3343); 

// check if the repeater field has rows of data
if( have_rows('jr_calc_1', 3343) ):
// loop through the rows of data
while ( have_rows('jr_calc_1', 3343) ) : the_row();

if(get_sub_field('calc2', 3343)    ){
   $exchange_rate = get_sub_field('calc2', 3343) ;
   break;
}

endwhile;
endif;

wp_reset_query() ;
*/

$exchange_rate = 68;
$exchange_rate = ( 1 / $exchange_rate );

?>
<div id="running_total"></div>
<div id="jr_passes">
	<div id="a7day">JR National Pass - 7 day - $<?php echo $p7; ?><span id="a7d2"></span></div>
	<div id="a14day">JR National Pass - 14 day - $<?php echo $p14; ?><span id="a14d2"></span></div>
	<div id="a21day">JR National Pass - 21 day - $<?php echo $p21; ?><span id="a21d2"></span></div>
	<input type="hidden" name="a7d1" id="a7d1" value="<?php echo $p7; ?>" />
	<input type="hidden" name="a14d1" id="a14d1" value="<?php echo $p14; ?>" />
	<input type="hidden" name="a21d1" id="a21d1" value="<?php echo $p21; ?>" />

	<input type="hidden" name="exchange_rate" id="exchange_rate" value="<?php echo $exchange_rate; ?>" />
</div>
</div>

<?php 

if( current_user_can('editor') || current_user_can('administrator') ){
?><div class='clear'></div>
	<div class="page-top">
		<?php
		//<p>*you can only see this if logged in as admin -  the exchange rate is stored in a field inside this page's edit page in WordPress.</p>
		//<p>EDIT - loading the exchange was breaking the JR price loading </p>
	
	//<p><a class="button" target="_blank" href="https://www.nx.jtbtravel.com.au/wp-admin/post.php?post=3343&action=edit#acf-group_5df061714072c">Edit data</a></p>
	?>
</div>
<?php } ?>

