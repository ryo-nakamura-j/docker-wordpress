<?php /*





plugin etc. 

footer switch - 
/wp-content/themes/jtb-australia/components/header_footer/libs

footer - 

add


<?php
require_once( dirname( __FILE__ ) . "/../../tp-footer-def.php");
$footerUi = new TpFooter();
$footerUi->loadConfig();
$footerUi->init("#footerWrapper");
echo do_shortcode('[jtb-widget f="roobix-footer2"]');
?>
<footer id="footerWrapper" class="footer header_footer" hidden>
<?php
echo do_shortcode('[jtb-widget f="roobix-footer"]');
?>
    <p class="copy" v-html="sectionConfig.copyright"></p>
</footer>








TICKETS PAGE


$url33 =  get_sub_field('box_link') ;
$imgurl33 =  get_sub_field('box_image') ;
if(get_sub_field('texturl') ){
	$url33 = get_sub_field('texturl');
}if(get_sub_field('imgtext') ){
	$imgurl33 = get_sub_field('imgtext');
}if($url33 == "hidden"){
	continue;
}
?>
<div class="col-sm-3">
<div class="thumbnail">
	<div class="caption"><a href="<?php echo $url33;   ?>">
		<h4><?php the_sub_field('box_title') ?></h4></a>
	</div>
		<a href="<?php echo $url33;  ?>"><img src="<?php echo $imgurl33;  ?>" alt=""></a>







*/ ?>