<?php
if(is_user_logged_in()){
	$filename2 = 'a-eway-inbound.php';
}else{
	$filename2 = 'a-eway-inbound.php';
}

?>
<form method="POST" action="<?php echo plugin_dir_url( __FILE__ ) .$filename2; ?>" class="search_page">


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

	<label>Contact Number</label><input type="text" name="phoneno" class="wpcf7-text"  > 
</div>
</div>

 

<div class="row"><div class="col-xs-12 col-sm-6">

	<label>Branch <span class="required">*</span></label>
	<p class="padbottom">
	<select name="branch" class="wpcf7-form-control wpcf7-select wpcf7-validates-as-required" aria-required="true" aria-invalid="false" required><option value="x">---</option><option value="syd">SYD CS</option><option value="mel">MEL CS</option><option value="ool">OOL CS</option><option value="cns">CNS CS</option></select></p>
</div>
 <div class="col-xs-12 col-sm-6">

	
</div>
</div>




<div class="row"><div class="col-xs-12 col-sm-6">

	<label>Card company <span class="required">*</span></label>
	<p class="padbottom">
	<select name="cardcompany" class="wpcf7-form-control wpcf7-select wpcf7-validates-as-required" aria-required="true" aria-invalid="false" required><option value="x">---</option><option value="visa">VISA</option><option value="mc">Master Card</option><option value="amex">AMEX - American Express</option><option value="diners">Diners Club</option><option value="jcb">JCB</option><option value="nocharge">No CC Charge</option></select></p>
</div>
 <div class="col-xs-12 col-sm-6">

	<label>Card issuer country <span class="required">*</span></label>
	<p class="padbottom">
	<select name="issuer" class="wpcf7-form-control wpcf7-select wpcf7-validates-as-required" aria-required="true" aria-invalid="false" required><option value="x">---</option><option value="au">Australia</option><option value="notau">Other countries</option></select></p>
</div>
</div>





<div class="row"><div class="col-xs-12 col-sm-6">

	<label>Payment Amount <span class="required">*</span></label><input type="text" name="amount" class="wpcf7-text paymentfield" required>
	 
</div>
 <div class="col-xs-12 col-sm-6">

	<p class="pink-text padtop"> </p>
</div>
</div>
<div class="row padtop"><div class="col-xs-12 col-sm-12">

	<input type="hidden" name="x" value="x" >
	<input type="submit" name="submit" class="wpcf7-submit btnLarge ewayoffline clear">
</div>
</div>


</form>



