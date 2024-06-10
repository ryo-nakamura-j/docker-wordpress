


<div id="flightbox">
<div id="contact_form" >


<div id="result"></div>
 

<h2>USJ Express Pass Request Form</h2>
<div class="clear"></div>
  



<div class="col-xs-12 col-sm-6">
<div class="row form-group">


<div class="col-xs-12 col-sm-6">
<div class="row form-group">
  <label for="consultant_title">Lead Title <span class="required">*</span> </label>
<select name="consultant_title" id="consultant_title" onchange="changename();"  class="wpcf7-form-control wpcf7-select" aria-invalid="false"><option value="x">---</option><option value="mr">Mr</option><option value="mrs">Mrs</option><option value="Miss">Miss</option><option value="Ms">Ms</option></select>
</div>
</div>

<div class="col-xs-12 col-sm-6">
<div class="row form-group">
<label for="consultant_name">Lead Name <span class="required">*</span></label><br />
<input name='consultant_name' id='consultant_name' onkeyup="changename();" required>
  </div>
</div>


</div>
</div>


<div class="col-xs-12 col-sm-6">
<div class="row form-group">
<label for="consultant_name2">Lead Surname <span class="required">*</span></label><br />
<input name='consultant_name2' id='consultant_name2' required  onkeyup="changename();"  >
<input type="hidden" name='full_name_one' id='full_name_one'  >
</div>
</div>

<div class="clear"></div>


<div class="col-xs-12 col-sm-6">
<div class="row form-group">
<label for="email">E-mail Address <span class="required">*</span></label><br />
<input name='email' id='email' required>
</div>
</div>





<div class="col-xs-12 col-sm-6">
<div class="row form-group">
<label for="agent_tel">Phone <span class="required">*</span></label><br />
<input name='agent_tel' id='agent_tel' required>
</div>
</div>
<div class="clear"></div>
<div class="col-xs-12 col-sm-6">
<div class="row form-group">
<label for="departure_date">Your Date Of Departure From Australia <span class="required">*</span> </label>
<select name="departure_date_d" id="departure_date_d" onchange="changedate()" class="wpcf7-form-control wpcf7-select" aria-invalid="false"><option value="x">---</option><option value="01">01</option><option value="02">02</option><option value="03">03</option><option value="04">04</option><option value="05">05</option><option value="06">06</option><option value="07">07</option><option value="08">08</option><option value="09">09</option><option value="10">10</option><option value="11">11</option><option value="12">12</option><option value="13">13</option><option value="14">14</option><option value="15">15</option><option value="16">16</option><option value="17">17</option><option value="18">18</option><option value="19">19</option><option value="20">20</option><option value="21">21</option><option value="22">22</option><option value="23">23</option><option value="24">24</option><option value="25">25</option><option value="26">26</option><option value="27">27</option><option value="28">28</option><option value="29">29</option><option value="30">30</option><option value="31">31</option></select>

<select name="departure_date_m" id="departure_date_m" onchange="changedate()" class="wpcf7-form-control wpcf7-select" aria-invalid="false"><option value="x">---</option><option value="Jan">Jan</option><option value="Feb">Feb</option><option value="Mar">Mar</option><option value="Apr">Apr</option><option value="May">May</option><option value="Jun">Jun</option><option value="Jul">Jul</option><option value="Aug">Aug</option><option value="Sep">Sep</option><option value="Oct">Oct</option><option value="Nov">Nov</option><option value="Dec">Dec</option></select>

<select name="departure_date_y" id="departure_date_y" onchange="changedate()" class="wpcf7-form-control wpcf7-select" aria-invalid="false"><option value="x">---</option>
  <?php
echo '<option  value="'.date('Y').'">'.date('Y').'</option>';
echo '<option   value="'.((int)date('Y')+1).'">'.((int)date('Y')+1).'</option>';
echo '<option   value="'.((int)date('Y')+2).'">'.((int)date('Y')+2).'</option>';
  ?> 
</select>
 
<input  type='hidden' name='departure_date' id='departure_date' required>
</div>
</div>


<div class="clear"></div>
 



<div class="col-xs-12 col-sm-6">
<div class="row    form-group">
<label for="email">Billing address (Australia only) <span class="required">*</span></label><input name='address1' id='address1' required class="thin_address_bar">
<input name='address2' id='address2' class="thin_address_bar">

<div class="clear"></div><br />
 <label for="suburb">Suburb <span class="required">*</span></label><br />
 <input required="" name="suburb" id="suburb" class="thin_address_bar"> 
<div class="clear"></div>
  
</div>
</div>



<div class="col-xs-12 col-sm-6">
<div class="row    form-group">


<label for="agent_tel">State <span class="required">*</span> </label>  
<select name="state" id="state_field_2" class="wpcf7-form-control wpcf7-select width100 clearleft"><option value="">State of Residence*</option><option value="NSW">NSW</option><option value="VIC">VIC</option><option value="ACT">ACT</option><option value="NT">NT</option><option value="QLD">QLD</option><option value="SA">SA</option><option value="TAS">TAS</option><option value="WA">WA</option><option value="TravelAgents">Travel Agents</option></select>
<div class="clear"></div><br /><br />
 <label for="post_code">Post Code <span class="required">*</span></label><br />
 <input required name='post_code' id='post_code'  class="thin_address_bar">
</div>
</div>



 





<br /> 
<div class="clear"></div>











<hr />

<h2>Select your USJ Express Pass Type</h2><!--#1-->

<div class="col-xs-12 col-sm-6">
<div class="row form-group" id="passes2usj" >




<div class="  form-group nomargin">
<h4>Park Entry Date<span class="required">*</span></h4>

<?php /*onchange="changedate2()"*/ ?>
<select name="jr_use_date_d" id="jr_use_date_d" onchange="changedate();"  class="wpcf7-form-control wpcf7-select" aria-invalid="false"><option value="x">---</option><option value="01">01</option><option value="02">02</option><option value="03">03</option><option value="04">04</option><option value="05">05</option><option value="06">06</option><option value="07">07</option><option value="08">08</option><option value="09">09</option><option value="10">10</option><option value="11">11</option><option value="12">12</option><option value="13">13</option><option value="14">14</option><option value="15">15</option><option value="16">16</option><option value="17">17</option><option value="18">18</option><option value="19">19</option><option value="20">20</option><option value="21">21</option><option value="22">22</option><option value="23">23</option><option value="24">24</option><option value="25">25</option><option value="26">26</option><option value="27">27</option><option value="28">28</option><option value="29">29</option><option value="30">30</option><option value="31">31</option></select>
<?php /*onchange="changedate2()"*/ ?>

<select name="jr_use_date_m" id="jr_use_date_m" onchange="changedate();" class="wpcf7-form-control wpcf7-select" aria-invalid="false"><option value="x">---</option><option value="Jan">Jan</option><option value="Feb">Feb</option><option value="Mar">Mar</option><option value="Apr">Apr</option><option value="May">May</option><option value="Jun">Jun</option><option value="Jul">Jul</option><option value="Aug">Aug</option><option value="Sep">Sep</option><option value="Oct">Oct</option><option value="Nov">Nov</option><option value="Dec">Dec</option></select>
<?php /*onchange="changedate2()"*/ ?>
<input type="hidden" name="hiddenxdata" id="hidden_date_data" value='<?php echo get_option("jtbau_usj_date"); ?>' />
<select name="jr_use_date_y" id="jr_use_date_y" onchange="changedate();" class="wpcf7-form-control wpcf7-select" aria-invalid="false">
  <option value="x" onchange="changedate()" >---</option>
  <?php
echo '<option   value="'.date('Y').'">'.date('Y').'</option>';
echo '<option   value="'.((int)date('Y')+1).'">'.((int)date('Y')+1).'</option>';
echo '<option   value="'.((int)date('Y')+2).'">'.((int)date('Y')+2).'</option>';
  ?> 
</select>

<br /><br />
<p id="date_limit_text3">*Tickets are only available for request up until <?php 
echo get_option("jtbau_usj_date");
if(current_user_can('edit_posts')){
  echo ' - <small><a class="red-text" target="_blank" href="https://www.nx.jtbtravel.com.au/wp-admin/post.php?post=791&action=edit#acf-group_5822553bf0549">edit_URL_admin_only</a></small>';
}
?>  </p>




 
<input  type='hidden' name='jr_use_date' id='jr_use_date' required>
</div> 

<h4>Entry Ticket</h4>

<input type="radio" class="checkbox-usj" name="no_days"  id="no_days1"  value="1">1 Day Ticket <br>
<div id="day1data"></div>

<input type="radio" class="checkbox-usj" name="no_days"  id="no_days2"  value="2">2 Day Fixed Dated Ticket <br>
<div id="day2data"></div>


<br />





<p class="inputwidth">Express passes must be booked in conjunction with admission tickets with JTB.<br />
If you have already purchased or inquired about your entry ticket, please input the reference number in the below field.</p>

 
<label for="refno2">JTB reference number (SYRT/MERTXXXX)</label><input name='lastname0' id='refno2'  >
 


</div></div>


<div class="col-xs-12 col-sm-6">
<div class="row form-group">



<?php /*onchange="changedate2()"*/ ?>
<div id="pass_3_1">

<?php
/* special = list of USJ Passes @@@  
Special Title
Special
*/
//if( have_rows('four_grid_boxes', 374) ):
$pas_no = 0;
$capt="";
$sys_msg=0; 
$sys_data = [];
while ( have_rows('gallery') ) : the_row();
    $set_no = 0;
    while ( have_rows('3img') ) : the_row();
      if($sys_msg==0){ 
        array_push($sys_data, get_sub_field('caption')); 
        continue;
      }

      if(get_sub_field('caption')!=""){
          $capt = get_sub_field('caption');
      }else{continue;}

      if($set_no == 0){
        if($pas_no != 0){
          echo "<br />";
        }
        echo "<h4>".$capt."</h4>";
        $set_no++;
      }else{
        $pas_no++;  
        $pass_name_nospace =  str_replace(" ","-",$capt); 
      ?>
        <input type="radio" class="checkbox-usj" name="pass_3_1"  id="pass_2_<?php echo $pas_no; ?>"  value="<?php echo $pass_name_nospace ; ?>"> <?php echo $capt ; ?> <br>
      <?php
      // name="pass<?php echo $pas_no; ?->"
      }

    endwhile; 
    $sys_msg=1;
endwhile; 

//print hidden fields with dynamic content to insert into HTML above 


echo '<input type="hidden" name="day1data1" id="day1data1" value="'.$sys_data[4].'" /><input type="hidden" name="day2data2" id="day2data2" value="'.$sys_data[5].'" />';
echo '<input type="hidden" name="availabledates" id="availabledates" value="'.$sys_data[2].'" />';
echo '<input type="hidden" name="availabledates2" id="availabledates2" value="'.    substr( $sys_data[2] , 6)  . '/'  . substr( $sys_data[2] , 4,2) . '/' . substr( $sys_data[2] , 0,4).'" />';
//2019 01 10
//0 - 4   ///    5 - 6    ///   7 - 8
 



echo "<br />";
for($c=6;$c<  count($sys_data) ;$c++){
  echo  $sys_data[$c].'<br />';
}

//echo   strtotime( "20190110" ); - convert 2 dates to unix, if after that - error message and remove the hidden date text therefore can't submit. 


if(current_user_can('edit_post')){
  echo '<p class="yellow-message">You are logged in as admin - to update prices and labels on this form - <a class="red-text" target="_blank" href="https://www.nx.jtbtravel.com.au/wp-admin/post.php?post=791&amp;action=edit#acf-group_5822553bf0549">edit this wordpress page</a>.</p>';
}


//echo $pas_no - into hidden field 

?>
<input  type='hidden' name='nopasses2' id='nopasses2' value='<?php echo $pas_no; ?>' >
</div>






</div>
</div>

<div class="clear"></div>

 




<hr />
<h2>Traveler Information</h2>
<p>*Please include all passengers who would like to request an Express Pass</p>

<button id="wppopup1" class="hidden"></button>
<input type="hidden" name="numberOfPeople" val="0" id="numberOfPeople">
<input type="hidden" name="personid" val="" id="personid">

<div class="personbox"><div class="personbox">
<h3>Person 1</h3>

<div id="full_name_one_display"></div>

<div class="col-xs-12 col-sm-12">
<div class="row form-group nomargin ">
<label for="age0">Age <span class="required  ">*</span></label><br />
<select name="age0" id="age0" required >
      <option value="" disabled selected> - - - </option>
      <option value="1">1</option>
      <option value="2">2</option>
      <option value="3">3</option>
      <option value="4">4</option>
      <option value="5">5</option>
      <option value="6">6</option>
      <option value="7">7</option>
      <option value="8">8</option>
      <option value="9">9</option>
      <option value="10">10</option>
      <option value="11">11</option>
      <option value="12+">12+</option> 
    </select> 
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




<button id="personboxadd" class="wpcf7-form-control wpcf7-submit btnLarge" onclick="addPerson();" >Add a person</button>

  


<div class="clear"></div>







<hr />
<h2>Terms and Conditions</h2>

* One ticket per person is required.<br />
* Tickets are strictly non changeable and non refundable. <br />
* A separate admission ticket is required to enter the park.<br />
* All Express passes have timed entries for rides, and can only use each ride once on the pass.<br />
* Nominated times may change; the best available time will be selected at the time of ticketing.<br />
* Attractions may be closed on certain days for maintenance.<br />
* Operation of attractions is subject to change without prior notice or liability.<br />
* Not applicable if the attraction is separately ticketed for special operations or hours.<br />
* Price may vary for any specific entrance date.<br />
* Ride and height requirements for all attractions is valid as of 20th October 2016<br />
* express pass prices are inclusive of JTB booking and handling fee.<br />
* Credit card fees will apply (Visa & Master card 1% / AMEX & Diners 3%)



<div class="clear"></div>

<hr />

<br />

<!-- <input class="hidden" type="checkbox" name="sim_card" value="sim_card" id="sim_card">-->
 


<div class="col-xs-12 col-sm-6">
<div class="row submit">
 
<div class="g-recaptcha" data-sitekey="6LcQZXUUAAAAABA2YunV8pNdSdjKtTJy5XQgve_m"></div>

<br />

<div id="submit_button_hide">
<button type="submit" id="agent-jr-send" class="submit wpcf7-form-control wpcf7-submit btnLarge">Submit USJ Express Pass Order</button>
</div>

</div>
</div>



</div></div>
<div id="jragent_response" class="col-xs-12 col-sm-6">
<br />
</div>




