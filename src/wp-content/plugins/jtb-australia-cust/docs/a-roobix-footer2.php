

<div id="mob_footer_2" class="footer header_footer" >
<div id="roobix_footer_mob">
<?php
//menu
wp_nav_menu(   array(    'menu'              => "Secondary Menu"  ) ); 
?>
<div class="clear"></div>
<div class="phone"> Phone: <span class="num">1300 739 330</span> </div>
</div>
</div>

<?php if (is_page(21601)||is_page(30922)): ?>
<div id="daytourfooterouter" class="footer header_footer">
	
<div id="daytourfooter"   class="footer__inner01 clearfix"> 

<a href="https://www.nx.jtbtravel.com.au/day-tours/mt-fuji-tours/"><img alt="Japan Day Tours" title="Japan Day Tours" class="one" src="https://www.nx.jtbtravel.com.au/wp-content/uploads/2020/09/mt-fuji-tours.jpg" /></a>
<a href="https://www.nx.jtbtravel.com.au/japan-tours/tokyo-sumo-tournament-from-hamamatsucho/"><img alt="Sumo Tournament" title="Sumo Tournament" class="two" src="https://www.nx.jtbtravel.com.au/wp-content/uploads/2020/09/sumo-tickets.jpg" /></a>

</div>

</div>
 
<?php endif; ?>





<div id="stay_touch" class="footer header_footer" >


  <?php  $current_user = wp_get_current_user(); 
    if( $current_user->user_email == "benjamin_g.au@jtbap.com"){
        ?>
<div class="header_message_jtb" id="header_message_jtb_foot">
<div class="collective container">
@@@ - test jtb
</div></div>
        <?php
    } ?>














<div   class="footer__inner01 clearfix"> 

<h2>Stay in Touch!</h2>
<p>Subscribe to our mailing list to receive exclusive offers.</p>

 
<form id="subscribeform" target="" onsubmit="" action="">

<ol style="padding-left: 0">


<div dir="ltr" class="ss-item  ss-text"><div class="ss-form-entry">
<input type="text"  name="entry.1493792574" value=""  placeholder="Full Name" class="ss-q-short" id="1493792574" dir="auto" aria-label="First Name  " title="">
<div class="error-message"></div>
</div></div>


<div dir="ltr" class="ss-item  ss-text"><div class="ss-form-entry">

<input type="text" name="entry.1411460999"  placeholder="Email Address" value="" class="ss-q-short" id="entry_1306134107" dir="auto" aria-label="Email Address  " title="">
<div class="error-message"></div>
</div></div>


 

<div dir="ltr" class="ss-item  ss-select"><div class="ss-form-entry">

<select name="entry.976961624" id="entry_959658973" aria-label="State  "><option value="">State of Residence</option>
<option value="NSW - Sydney">NSW - Sydney</option> <option value="NSW">NSW - Other</option>  <option value="VIC">VIC </option>  <option value="QLD - Brisbane">QLD - Brisbane</option> <option value="QLD - Gold Coast">QLD - Gold Coast</option> <option value="QLD">QLD - Other</option> <option value="WA">WA</option> <option value="ACT">ACT</option> <option value="TAS">TAS</option> <option value="SA">SA</option> <option value="NT">NT</option>  <option value="Outside Australia">Travel Agents</option> <option value="Outside Australia">Outside Australia</option> </select></div></div>

<input type="hidden" name="draftResponse" value="[,,&quot;5728838363640238249&quot;]
">
<input type="hidden" name="pageHistory" value="0">
<input type="hidden" name="fbzx" value="5728838363640238249">
<input name="pageNumber" type="hidden" value="0" />
<input name="backupCache" type="hidden" />
<!--<input type="submit" name="submit" value="Submit" id="ss-submit">-->
<input type="submit" name="submit" value="Submit" id="ss-submit" class="wpcf7-form-control wpcf7-submit btnLarge jfk-button jfk-button-action ">


</ol>

</form>

<div id="subscribemessage"></div>








</div>
</div>
<?php

//add this to theme - components - head-foot, sections FOOTER.php 
//echo do_shortcode('[jtb-widget f="roobix-footer2"]');


?>