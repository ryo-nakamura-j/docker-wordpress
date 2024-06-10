
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

--

clone agent form for the point to point rail order form 

user data 

loop "trips"

from x 
to x 
(radio - depart time OR arrive time aprox/request)
time x 

delete / new 





*/



?>

<div id="flightbox">
<div id="contact_form">


<div id="result"></div>


<span class="required">* Required information</span>

<div class="clear"></div>

<div class="col-xs-12 col-sm-6">
<div class="row form-group">
<label for="agent_name">Name <span class="required">*</span></label><input name='agent_name' id='agent_name' required>
</div>
</div>
<div class="col-xs-12 col-sm-6">
<div class="row form-group">
<label for="email">Email <span class="required">*</span></label><input name='email' id='email' required>
</div>
</div><div class="clear"></div>

 
<div class="col-xs-12 col-sm-6">
<div class="row form-group">
<label for="agent_tel">Phone <span class="required">*</span></label><input name='agent_tel' id='agent_tel' required>
</div>
</div>

<div class="col-xs-12 col-sm-6">
<div class="row form-group">
<label for="consultant_name">Number of Travellers <span class="required">*</span></label><input name='consultant_name' id='consultant_name' required>
</div>
</div><div class="clear"></div>





<div class="clear"></div>
 




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



<input  type='hidden' name='departure_date' id='departure_date' required>
</div>
</div>
<div class="col-xs-12 col-sm-6">
<div class="row form-group">
<select name="your-recipient7" id="state" class="wpcf7-form-control wpcf7-select wpcf7-validates-as-required mdc-select" aria-required="true" aria-invalid="false"><option value="">State of Residence*</option><option value="NSW">NSW</option><option value="VIC">VIC</option><option value="ACT">ACT</option><option value="NT">NT</option><option value="QLD">QLD</option><option value="SA">SA</option><option value="TAS">TAS</option><option value="WA">WA</option><option value="Travel Agents">Travel Agents</option></select>
<p id="agentmessagered"   style="visibility:hidden  ;color:#c60e0e;"><strong>Travel Agents - please do not use this form - please email
melres.au@jtbap.com instead</strong></p>
</div></div>






<button id="wppopup1" class="hidden"></button>
<input type="hidden" name="numberOfPeople" val="0" id="numberOfPeople">
<input type="hidden" name="personid" val="" id="personid">



<div class="personbox"><div class="personbox">

<h3>Trip 1</h3>

<div class="col-xs-12 col-sm-12">
<div class="row form-group">
<label for="name0">From - Station Name <span class="required">*</span></label><input name='name0' id='name0' required>
</div>
</div>


<div class="col-xs-12 col-sm-12">
<div class="row form-group">
<label for="lastname0">To - Station name <span class="required">*</span></label><input name='lastname0' id='lastname0' required>
</div>
</div>

<div class="col-xs-12 col-sm-12">
<div class="row form-group">
<label for="nat0">Date / Time <span class="required">*</span></label><input name='nat0' id='nat0' required>
</div>
</div>

<div class="col-xs-12 col-sm-6">
<div class="row form-group">
<p><input name="type0" type="radio" id="type01" value="Adult" class="radio" /> <label for="type0">Detarture</label>  <br /><br />
<input name="type0" type="radio" id="type02" value="Child"   class="radio"  /> <label for="type02">Arrival</label>  
  </p>
</div>
</div>



<input type="hidden" name="passes0" val="" id="passes0">

<span id="jrdisplay0"></span>





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



<button id="personboxadd" class="wpcf7-form-control wpcf7-submit btnLarge" onclick="addPerson();" >Add a trip</button>

  




<br />
<div class="clear"></div>
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

