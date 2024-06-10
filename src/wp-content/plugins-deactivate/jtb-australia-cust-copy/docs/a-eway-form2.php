<?php
if(is_user_logged_in()){
	$filename2 = 'a-eway2.php';
}else{
	$filename2 = 'a-eway2.php';
}

?>
<form method="POST" action="<?php echo plugin_dir_url( __FILE__ ) .$filename2; ?>" class="search_page">
<h2>TEST SANDBOX FORM</h2>

<div class="row"><div class="col-xs-12 col-sm-6">

	<label>First Name <span class="required">*</span></label><input type="text" name="name" class="wpcf7-text" required> 
</div>
 <div class="col-xs-12 col-sm-6">

	<label>Last Name <span class="required">*</span></label><input type="text" name="lastname" class="wpcf7-text" required> 
</div>
</div>



<div class="row"><div class="col-xs-12 col-sm-6">

	<label>Reference Number <span class="required">*</span></label><input type="text" name="refno" class="wpcf7-text" required>
</div>
 <div class="col-xs-12 col-sm-6">

	<label>Email Address <span class="required">*</span></label><input type="text" name="email" class="wpcf7-text" required>
</div>
</div>

 

<div class="row"><div class="col-xs-12 col-sm-6">

	<label>Contact Number <span class="required">*</span></label><input type="text" name="phoneno" class="wpcf7-text" required> 
</div>
 <div class="col-xs-12 col-sm-6">

	<label>State of Residence <span class="required">*</span></label>
	<p class="padbottom">
	<select name="your-recipient7" class="wpcf7-form-control wpcf7-select wpcf7-validates-as-required" aria-required="true" aria-invalid="false" required><option value="x">---</option><option value="NSW">NSW</option><option value="VIC">VIC</option><option value="ACT">ACT</option><option value="NT">NT</option><option value="QLD">QLD</option><option value="SA">SA</option><option value="TAS">TAS</option><option value="WA">WA</option><option value="TravelAgents">Travel Agents</option></select></p>
</div>
</div>




<div class="row"><div class="col-xs-12 col-sm-6">

	<label>Payment Amount <span class="required">*</span></label><input type="text" name="amount" class="wpcf7-text paymentfield" required>
	 
</div>
 <div class="col-xs-12 col-sm-6">

	<p class="pink-text padtop">A credit card surcharge will be automatically added to your payment amount. Visa, Mastercard - 1% (American Express and Diners Club currently unavailable)</p>
</div>
</div>
<div class="row padtop"><div class="col-xs-12 col-sm-12">

	<input type="hidden" name="x" value="x" >
	<input type="submit" name="submit" class="wpcf7-submit btnLarge ewayoffline clear">
</div>
</div>


</form>



