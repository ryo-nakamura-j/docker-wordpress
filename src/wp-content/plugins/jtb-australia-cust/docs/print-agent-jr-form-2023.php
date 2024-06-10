
<?php
if(  current_user_can( 'edit_post' )  || 1 ) {


//INFO

/*
The JR selector popup is split between different files

popup boxes, and model divs
print-popup-boxes.php

the list of popups are stored in one WP variable, when JR data is refreshed 
echo get_option('jr_list_popup');

docs/cron-job-jr-price-list.php


*/



?>

<div id="flightbox">
<div id="contact_form">


<div id="result"></div>

<hr />

<h2>Agency Details:</h2><!--agent 2023.php-->

<span class="required">* Required information</span>

<div class="clear"></div>

<div class="col-xs-12 col-sm-6">
<div class="row form-group">
<label for="agent_name">Agency Name <span class="required">*</span></label><input name='agent_name' id='agent_name' required>
</div>
</div>
<div class="col-xs-12 col-sm-6">
<div class="row form-group">
<label for="licence_no">License No</label><input name='licence_no' id='licence_no'>
</div>
</div><div class="clear"></div>

 
<div class="col-xs-12 col-sm-6">
<div class="row form-group">
<label for="agent_tel">Agent Contact Tel <span class="required">*</span></label><input name='agent_tel' id='agent_tel' required>
</div>
</div>

<div class="col-xs-12 col-sm-6">
<div class="row form-group">
<label for="consultant_name">Consultant Full Name <span class="required">*</span></label><input name='consultant_name' id='consultant_name' required>
</div>
</div><div class="clear"></div>

<div class="col-xs-12 col-sm-6">
<div class="row form-group">
<label for="email">E-mail Address <span class="required">*</span></label><input name='email' id='email' required>
</div>
</div><div class="clear"></div>


<div class="col-xs-12 col-sm-6">

<div class="row address_border_left  form-group">


<label for="email">Agency Address (Australia only) <span class="required">*</span></label><input name='address1' id='address1' required class="thin_address_bar">
<input name='address2' id='address2' class="thin_address_bar">
<div class="clear"></div>
<div class="percent50"><label for="suburb">Suburb <span class="required">*</span></label><input required="" name="suburb" id="suburb" class="thin_address_bar"></div>
<div class="clear"></div>
<div class="percent50"><label for="state">State <span class="required">*</span></label><input required name='state' id='state'  class="thin_address_bar"></div>
<div class="percent50"><label for="post_code">Post Code <span class="required">*</span></label><input required name='post_code' id='post_code'  class="thin_address_bar"></div>


</div>


</div>


 

<div class="clear"></div>
<hr />

<h2>Delivery Method: <span class="required"><small>*</small></span></h2>







<div class="col-xs-12 col-sm-6">
<div class="row form-group">
<div class="clear"></div>

<?php /*

 <span class="same_pass"> <input type="radio" name="delivery_method" value="Pick_up_SYD" class="agent_form_radio"  ><p>PICKUP JTB SYDNEY OFFICE</p></span>
 
<ul><li><span class="required">Only available to travelers departing within 7 days.</span></li>
<li>Address: Level 18, 456 Kent Street (Town Hall House)
</li></ul>




 <span class="same_pass"> <input type="radio" name="delivery_method" value="Pick_up_MEL" class="agent_form_radio"  ><p>PICKUP JTB MELBOURNE OFFICE</p></span>
 
<ul><li><span class="required">Only available to travelers departing within 7 days.</span></li>
<li>Address: 6/31 Queen St, Melbourne VIC 3000
</li></ul>

*/ ?>

<p><span class="required">Office pickup is not currently available</span></p>

 <span class="same_pass"> <input type="radio" name="delivery_method" value="Dx_mail" class="agent_form_radio" checked ><p>AUSTRALIA POST EXPRESS POST ( FREE )</p></span>

<ul><li>Allow up to 5 working days for delivery.</li>
<li>Delivery directly to clients is not available.</li>
<li>JTB is not liable for lost or misplaced passes<br /></li>
<li>
*There is no signature on delivery option
<!-- <br />
* Toll Courier requires a signature upon delivery. Please provide a street address where you will be during normal business hours. If unable to sign for the delivery, please select Toll DX Express Mail  option. -->
</li>
</ul>

<!--


 <span class="same_pass"> <input type="radio" name="delivery_method" value="Courier_AUD_21" class="agent_form_radio"  ><p>TOLL COURIER (A$21.00)</p></span>

<ul><li>Allow up to 5 working days for delivery. </li>
<li>Signature required upon delivery. </li>
<li>Deliveries cannot be made to a PO box and PARCEL COLLECT/ PARCEL LOCKER address. </li>
<li>Delivery directly to clients is not available.</li>
<li>Period of 21Dec - 03Jan Courier is not available, may be expecting some delay for Express Post for this period).</li>
<li>If you are ordering JR East Passes only, please select the pick-up option. The passes will be emailed to you as electronic vouchers (JR East Pass only).</li>
<li>We highly recommend the use of a courier. </li></ul>



  

-->




</div>
</div>

<div class="clear"></div>
 
<hr />

<h2>Eligibility:</h2>


<ul><li>Adult 12yrs +</li>
<li>Child (6-11 years): Age at the time of exchanging order is issued.</li>
<li>Child 5 and under is free of charge with NO seat. This is limited to one child under 6 per parent.<br /><br /></li>

<li><strong>Non Japanese Nationals: </strong> Must be visiting Japan under the entry status of "Temporary visitor" according to Japan Immigration Law. When entering Japan for sightseeing, entry personnel will stamp the passport as "Temporary visitor" Only a person who has a passport bearing this stamp can use a JAPAN RAIL PASS. Please note that according to strict interpretation of the Japanese Immigration Law, “Temporary visitor" status differs even from other types of stays that are also for only short time periods.</li>
<li>If you use an automated gate, no stamp will be applied to your passport. Either use a manned automated gate or ask a clerk to apply the stamp to your passport.<br /><br /></li>
<li><strong>Japanese Nationals: </strong> Who have both their valid Japanese passport and written proof obtained from the embassy or legation of Japan in the foreign country where they live, that they have been living in that country for 10 or more consecutive years. 


<?php 
//echo apply_filters( 'the_content',' [jtb-widget f="jr-doc-buttons"] ');
echo do_shortcode('[jtb-widget f="jr-doc-buttons"]'); 
?>

<br /><br />

</li>
<li>It is Japan Rail’s strict rule that the JR passes have to be sold at the price printed on the pass and that no discounting is allowed under any circumstances. Japan Rail is closely monitoring the selling prices at all times and if any proof of discounting is found, the given agents will not be able to sell the passes any more.<br /></</li>
</ul>


<div class="clear"></div>
 







<hr />

<h2>Conditions:</h2>


<ul>
</li><li>After purchase and voucher issuance, a re-issue fee of $60 per change will apply for any name correction (This applies to corrections only. Completely different names will be treated as a cancellation), plus any delivery and credit card surcharges will apply ($8.50 Toll Express Mail<!--, $21 Courier-->). You must send the original exchange coupon back to the office of purchase.</li><li>
The downgrading of a JR pass (same name), after purchase will also attract a $60 re issue fee plus any delivery fees.</li><li>
Neither an exchange coupon nor a Japan Rail Pass can be reissued if lost or stolen.</li><li>
A refund can be made with original Exchange Order within one year of the date of issue <strong>provided you return the original exchange coupon to the office of purchase</strong>. Upon purchase you understand and accept our cancellation conditions, being a 20% cancellation fee. 10% of the booking will be charged as a cancelation fee, 10% will be held in credit for future travel of up to 12 months. In the event that this deposit is not used within 12 months from the date of your original cancellation it will be forfeit and is strictly non-refundable.
</li><li>
You will need to send the original exchange coupon back to the office of purchase for a refund.</li><li>
Any changes made to date, type or length of a pass will be treated as a cancellation and cancellation fees will apply.</li> 
</ul>


<p><a href="https://www.nx.jtbtravel.com.au/terms-and-conditions/">JTB Full Terms and Conditions</a></p>



<hr />

<h2>Traveller Details:</h2>
<br />

<h4><span class="same_pass"><input type="checkbox" name="all_same" value="all_not_same" id="all_same"></span>All customers are booking the same JR Passes.</h4>
<br />


<div class="col-xs-12 col-sm-6">
<div class="row form-group">
<label for="departure_date">Date of departure from Australia <span class="required">*</span></label>



<select name="departure_date_d" id="departure_date_d" onchange="changedate()" class="wpcf7-form-control wpcf7-select" aria-invalid="false"><option value="x">---</option><option value="01">01</option><option value="02">02</option><option value="03">03</option><option value="04">04</option><option value="05">05</option><option value="06">06</option><option value="07">07</option><option value="08">08</option><option value="09">09</option><option value="10">10</option><option value="11">11</option><option value="12">12</option><option value="13">13</option><option value="14">14</option><option value="15">15</option><option value="16">16</option><option value="17">17</option><option value="18">18</option><option value="19">19</option><option value="20">20</option><option value="21">21</option><option value="22">22</option><option value="23">23</option><option value="24">24</option><option value="25">25</option><option value="26">26</option><option value="27">27</option><option value="28">28</option><option value="29">29</option><option value="30">30</option><option value="31">31</option></select>


<select name="departure_date_m" id="departure_date_m" onchange="changedate()" class="wpcf7-form-control wpcf7-select" aria-invalid="false"><option value="x">---</option><option value="Jan">Jan</option><option value="Feb">Feb</option><option value="Mar">Mar</option><option value="Apr">Apr</option><option value="May">May</option><option value="Jun">Jun</option><option value="Jul">Jul</option><option value="Aug">Aug</option><option value="Sep">Sep</option><option value="Oct">Oct</option><option value="Nov">Nov</option><option value="Dec">Dec</option></select>



<select name="departure_date_y" id="departure_date_y" onchange="changedate()" class="wpcf7-form-control wpcf7-select" aria-invalid="false"><option value="x">---</option>
  <?php

echo '<option value="'.date('Y').'">'.date('Y').'</option>';
echo '<option value="'.((int)date('Y')+1).'">'.((int)date('Y')+1).'</option>';
echo '<option value="'.((int)date('Y')+2).'">'.((int)date('Y')+2).'</option>';

  ?> 
</select>
<br />
<span id="day5dep">You must book 5 business days before departure.</span>
<span id="day9dep"><br /><br />This order form is for DEPARTURE from Australia on or after 26DEC23 - for earlier bookings use <a href="https://www.nx.jtbtravel.com.au/agent-jr-pass-booking">this form</a>.</span>



<input  type='hidden' name='departure_date' id='departure_date' required>
</div>
</div>
<div class="col-xs-12 col-sm-6">
<div class="row form-group">
<label for="jr_use_date">Intended date of JR Pass use <span class="required">*</span></label>

<select name="jr_use_date_d" id="jr_use_date_d" onchange="changedate()" class="wpcf7-form-control wpcf7-select" aria-invalid="false"><option value="x">---</option><option value="01">01</option><option value="02">02</option><option value="03">03</option><option value="04">04</option><option value="05">05</option><option value="06">06</option><option value="07">07</option><option value="08">08</option><option value="09">09</option><option value="10">10</option><option value="11">11</option><option value="12">12</option><option value="13">13</option><option value="14">14</option><option value="15">15</option><option value="16">16</option><option value="17">17</option><option value="18">18</option><option value="19">19</option><option value="20">20</option><option value="21">21</option><option value="22">22</option><option value="23">23</option><option value="24">24</option><option value="25">25</option><option value="26">26</option><option value="27">27</option><option value="28">28</option><option value="29">29</option><option value="30">30</option><option value="31">31</option></select>


<select name="jr_use_date_m" id="jr_use_date_m" onchange="changedate()" class="wpcf7-form-control wpcf7-select" aria-invalid="false"><option value="x">---</option><option value="Jan">Jan</option><option value="Feb">Feb</option><option value="Mar">Mar</option><option value="Apr">Apr</option><option value="May">May</option><option value="Jun">Jun</option><option value="Jul">Jul</option><option value="Aug">Aug</option><option value="Sep">Sep</option><option value="Oct">Oct</option><option value="Nov">Nov</option><option value="Dec">Dec</option></select>



<select name="jr_use_date_y" id="jr_use_date_y" onchange="changedate()" class="wpcf7-form-control wpcf7-select" aria-invalid="false"><option value="x">---</option>
  <?php

echo '<option value="'.date('Y').'">'.date('Y').'</option>';
echo '<option value="'.((int)date('Y')+1).'">'.((int)date('Y')+1).'</option>';
echo '<option value="'.((int)date('Y')+2).'">'.((int)date('Y')+2).'</option>';

  ?> 
</select>



<input required  type='hidden' name='jr_use_date' id='jr_use_date'>
</div>
</div>





<button id="wppopup1" class="hidden"></button>
<input type="hidden" name="numberOfPeople" val="0" id="numberOfPeople">
<input type="hidden" name="personid" val="" id="personid">



<div class="personbox"><div class="personbox">

<p  class="red-text"><i class="material-icons" >error_outline</i> Note: Customer name must be entered as it appears on their passport, including any middle names.</p>

<div class="col-xs-12 col-sm-12">
<div class="row form-group" >
    <select name="title0" id="title0" required >
      <option value="" disabled selected>Title</option>
      <option value="MR">Mr</option>
      <option value="MRS">Mrs</option>
      <option value="MS">Ms</option>
    </select> <span class="required">*</span>
</div></div>



<div class="col-xs-12 col-sm-12">
<div class="row form-group">
<label for="name0">First Name <span class="required">*</span></label><input name='name0' id='name0' required>
</div>
</div>

<div class="col-xs-12 col-sm-12">
<div class="row form-group">
<label for="midname0">Middle Name</label><input name='midname0' id='midname0'>
</div>
</div>

<div class="col-xs-12 col-sm-12">
<div class="row form-group">
<label for="lastname0">Last Name <span class="required">*</span></label><input name='lastname0' id='lastname0' required>
</div>
</div>

<div class="col-xs-12 col-sm-12">
<div class="row form-group">
<label for="nat0">Nationality <span class="required">*</span></label><input name='nat0' id='nat0' required>
</div>
</div>

<div class="col-xs-12 col-sm-6">
<div class="row form-group">
 <label for="type01">Pass type <span class="required">*</span></label>
<br /><br /><p><input name="type0" type="radio" id="type01" value="Adult" class="radio" /> <label for="type0">Adult (12 yrs +)</label>  
  </p>
</div>
</div>
<div class="col-xs-12 col-sm-6">
<div class="row form-group"><br /><br />
 <p><input name="type0" type="radio" id="type02" value="Child"   class="radio"  /> <label for="type02">Child (6-11 yrs)</label>   
 </p>
</div>
</div>

<div class="dob0container" id="dob0container"></div>

<input type="hidden" name="passes0" val="" id="passes0">

<div class="col-xs-12 col-sm-12">
<div id="jrbutton0" class="row form-group"> <!--id="wppopup1"-->
<button onclick="selectPasses(0);" class="wpcf7-form-control wpcf7-submit btnLarge">Select JR Passes</button>
<p>JR Pass Prices are listed at the bottom of the page</p>
</div>
</div>

<div style="clear:both;"></div>

<div style="width: 100%;  display: inline-block;">
<div class="row form-group">
<div class="jrdisplay" id="jrdisplay0"><p><span>JR Passes Selected</span><br /><br />
<span id="jrdisplay0"></span>
</p>
</div>
</div>
</div>




</div></div>


<div class="personbox" id="personbox1"></div><div class="personbox" id="personbox2"></div><div class="personbox" id="personbox3"></div><div class="personbox" id="personbox4"></div><div class="personbox" id="personbox5"></div><div class="personbox" id="personbox6"></div><div class="personbox" id="personbox7"></div><div class="personbox" id="personbox8"></div><div class="personbox" id="personbox9"></div><div class="personbox" id="personbox10"></div><div class="personbox" id="personbox11"></div><div class="personbox" id="personbox12"></div><div class="personbox" id="personbox13"></div><div class="personbox" id="personbox14"></div><div class="personbox" id="personbox15"></div><div class="personbox" id="personbox16"></div><div class="personbox" id="personbox17"></div><div class="personbox" id="personbox18"></div><div class="personbox" id="personbox19"></div><div class="personbox" id="personbox20"></div>

<div class="personbox" id="personbox21"></div>
<div class="personbox" id="personbox22"></div>
<div class="personbox" id="personbox23"></div>
<div class="personbox" id="personbox24"></div>
<div class="personbox" id="personbox25"></div>
<div class="personbox" id="personbox26"></div>
<div class="personbox" id="personbox27"></div>
<div class="personbox" id="personbox28"></div>
<div class="personbox" id="personbox29"></div>

<div class="personbox" id="personbox30"></div>
<div class="personbox" id="personbox31"></div>
<div class="personbox" id="personbox32"></div>
<div class="personbox" id="personbox33"></div>
<div class="personbox" id="personbox34"></div>
<div class="personbox" id="personbox35"></div>
<div class="personbox" id="personbox36"></div>
<div class="personbox" id="personbox37"></div>
<div class="personbox" id="personbox38"></div>
<div class="personbox" id="personbox39"></div>
<div class="personbox" id="personbox40"></div>


<p>JR Pass Prices are listed at the bottom of the page</p>

<button id="personboxadd" class="wpcf7-form-control wpcf7-submit btnLarge" onclick="addPerson();" >Add a person</button>

  


 






<div class="clear"></div>

<hr />


<br />

<!-- <input class="hidden" type="checkbox" name="sim_card" value="sim_card" id="sim_card">-->



<!--
<h4><span class="same_pass"><input type="checkbox" name="sim_card" value="sim_card" id="sim_card"></span> Would you also like to add on a Japan Data Sim 4GB / $49 to your booking?</h4>
<br /> 
-->


<h4><span class="same_pass"><input required type="checkbox" name="users_eligible" value="users_eligible" id="users_eligible"></span> I have confirmed that all JR pass users are eligible <span class="required">*</span></h4>
<br />

<h4><span class="same_pass"><input required type="checkbox" name="names_double_check" value="names_double_check" id="names_double_check"></span> I confirm that all names entered are exactly as per passport, including any middle names <span class="required">*</span></h4>
<br />


<div class="col-xs-12 col-sm-6">
<div class="row">



<strong>Additional comments:</strong><br />
<textarea name="comments" id="comments"></textarea>



</div>
</div>
<div class="col-xs-12 col-sm-6">
<div class="row submit">
<label>&nbsp;</label>

<div class="g-recaptcha" data-sitekey="6LcQZXUUAAAAABA2YunV8pNdSdjKtTJy5XQgve_m"></div>
<br />

<div id="submit_button_hide">
<button type="submit" id="agent-jr-send" class="submit wpcf7-form-control wpcf7-submit btnLarge">Submit JR Pass Order</button>
</div>

</div>
</div>



</div></div>
<div id="jragent_response" class="col-xs-12 col-sm-6">

</div>






<!--
<h4>Note:</h4>
<p>Delivery and handling fee charge to be added as follows</p>
<p>Delivery to PO box is not accepted, if delivered by courier</p>

<ul>
<li>Courier with signature required A$20.00</li>
<li>Delivery by DX Toll/Express Post, no extra charge</li>
<li>Delivery direct to clients is not available<br />
<small>(Please Note -- Period of 21Dec - 03Jan Courier is not available, may be expecting some delay for Express Post for this period.)</small></li>
<li>We highly recommend the use of a courier.</li>
<li>JTB is not liable for lost or misplaced passes sent Express post. </li>
<li>If you are ordering only JR East Passes, please select the Pick-up option. The passes will be emailed to you as electronic vouchers. (JR East Pass only)</li>
<li>It is Japan Rail’s strict rule that the JR passes have to be sold at the price printed on the pass and that no discounting is allowed under any circumstances.  Japan Rail is closely monitoring the selling prices at all times and if any proof of discounting is found, the given agents will not be able to sell the passes any more.</li>
</ul> 



<h2>Choose JR Pass</h2>

<!-- list of all JR Passes --/>

<ul>
<li>Rail passes are processed the next business day and will be ready for collection from our office after 1pm. If you wish your passes to be delivered to your designated address, please allow us up to 5 working days for delivery . If you are not collecting your passes, and are leaving within 5 working days, please contact JTB for delivery options.</li>
<li><strong>Please select a pass you need</strong></li>
</ul> 


<ul>
<li>Child (6-11 years): Age at the time of exchanging order is issued.</li>
<li>Child 5 and under is free of charge with NO seat. </li>
</ul> 


<p><strong>If you are a tourist visiting Japan from abroad for sightseeing:</strong></p>

<p>You must be visiting Japan under the entry status of "Temporary visitor" according to Japan Immigration Law. When you enter Japan for sightseeing, entry personnel will stamp the passport as "Temporary visitor" Only a person who has a passport bearing this stamp can use a JAPAN RAIL PASS. Please note that according to strict interpretation of the Japanese Immigration Law, “Temporary visitor" status differs even from other types of stays that are also for only short time periods.</p>



<p>Important Notice from JR Group Company: JR passes will not be available to Japanese passport holders from 1st of April 2017.</p>
-->




<?php
}
?>

