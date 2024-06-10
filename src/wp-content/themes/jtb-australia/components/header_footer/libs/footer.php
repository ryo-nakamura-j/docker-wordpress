<?php
require_once( dirname( __FILE__ ) . "/../../tp-footer-def.php");
$footerUi = new TpFooter();
$footerUi->loadConfig();
$footerUi->init("#footerWrapper");


if(is_page(23983)){
  echo do_shortcode('[jtb-widget f="a-roobix-home"]');
}


echo do_shortcode('[jtb-widget f="roobix-footer2"]');

?>
<footer id="footerWrapper" class="footer header_footer" hidden>

<?php

echo do_shortcode('[jtb-widget f="roobix-footer"]');

?>


    <p class="copy" v-html="sectionConfig.copyright"></p>
</footer>




<?php echo $footerUi->sectionConfig['contact_form_mobile']['scripttagpart']; ?>
<?php 
    if( $footerUi->sectionConfig['contact_form']['scripttagpart'] != 
        $footerUi->sectionConfig['contact_form_mobile']['scripttagpart'] ) 
        echo $footerUi->sectionConfig['contact_form']['scripttagpart']; 
?>
<script src="<?php echo APP_ASSETS; ?>js/lib/common.js"></script>
<script src="<?php echo APP_ASSETS; ?>js/lib/smoothscroll.js"></script>
<script src="<?php echo APP_ASSETS; ?>js/lib/biggerlink.js"></script>
<script src="<?php echo APP_ASSETS; ?>js/lib/slick.min.js"></script>