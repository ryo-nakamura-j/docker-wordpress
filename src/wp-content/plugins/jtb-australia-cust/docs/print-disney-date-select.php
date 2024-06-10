<?php /*
<script type="text/javascript">
$( document ).ready(function() {
	$(".disubmit").click(function() {
	   //EditBanner(bannerID);
	   var sevendays = <?php 
			$date = strtotime(date("Y/m/d"));
			$date = strtotime("+7 day", $date);
			echo date("Ymd", $date);
	    ?>;

	   var day = $( '#dis_day' ).val() + "";
	   if (day.length<2){
	   	day = "0"+day  
	   }
	   var month = $( '#dis_month' ).val() + "";
	   if (month.length<2){
	   	month = "0"+month  
	   }
	   var year = $( '#dis_year' ).val() + "";

	   if(Number("" + year + month + day )<sevendays){
	   	 alert('Please select a date that is at least 7 days from today');
	   	 return false;
	   }

	   window.location.href = $( this ).attr('data-href') + '&date='+year+'-'+month+'-'+day  ;
	   
	});
});


</script>



<div class="left">
  <span>
    <strong>Date of 1st day: </strong>
  </span>
</div>
<div class="left">
  <div class="explore-date-picker explore-validate-date"  >
     <span>
    <select class="day" id="dis_day"   name="dis_day"><option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option><option value="5">5</option><option value="6">6</option><option value="7">7</option><option value="8">8</option><option value="9">9</option><option value="10">10</option><option value="11">11</option><option value="12">12</option><option value="13">13</option><option value="14">14</option><option value="15">15</option><option value="16">16</option><option value="17">17</option><option value="18">18</option><option value="19">19</option><option value="20">20</option><option value="21">21</option><option value="22">22</option><option value="23">23</option><option value="24">24</option><option value="25">25</option><option value="26">26</option><option value="27">27</option><option value="28">28</option><option value="29">29</option><option value="30">30</option><option value="31">31</option></select>
    <select class="month" id="dis_month"  name="dis_month"><option value="1">Jan</option><option value="2">Feb</option><option value="3">Mar</option><option value="4">Apr</option><option value="5">May</option><option value="6">Jun</option><option value="7">Jul</option><option value="8">Aug</option><option value="9">Sep</option><option value="10">Oct</option><option value="11">Nov</option><option value="12">Dec</option></select>
    <select class="year"  id="dis_year" name="dis_year"><option value="2017">2017</option><option value="2018">2018</option></select>
    <input class="date-selector" type="hidden">
     </span>
  </div>
</div>

*/ ?>