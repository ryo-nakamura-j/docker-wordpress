



<form id='rwc-transport-package'>

<h2 style="padding-top: 0;margin-top: 0; ">Your details</h2>

<p><strong>Name</strong><br />
<input type="text" name="rwcname" id="rwcname" /></p>

<p><strong>Email address</strong><br />
<input type="text" name="rwcemail" id="rwcemail" /></p>

<p><strong>How many tickets would you like?</strong><br />
<input type="text" name="rwcnumtix" id="rwcnumtix" /></p>

<br />
<select name="rwc-state" id="rwc-state">
<option value="x"> -- State -- </option>
<option value="NSW">NSW</option><option value="VIC">VIC</option><option value="ACT">ACT</option><option value="NT">NT</option><option value="QLD">QLD</option><option value="SA">SA</option><option value="TAS">TAS</option><option value="WA">WA</option><option value="Travel_Agents">Travel Agents</option>
</select>



<h2>Game Selection</h2>

<?php 
/*
//adjust prices in HTML and JS @@@ - and array X3
*/



/*



<option value="Match-2-Australia-v-Fiji-from-481">Match 2 Australia v Fiji</option>
<option value="Match-4-New-Zealand-v-South-Africa-from-929">Match 4 New Zealand v South Africa</option>
<option value="Match-42-QF-2-W-Pool-B-v-RU-Pool-A-from-929">Match 42 QF 2 W Pool B v RU Pool A</option>
<option value="Match-44-QF-4-W-Pool-A-v-RU-Pool-B-from-929">Match 44 QF 4 W Pool A v RU Pool B</option>

*/


?>

<select name="rwc-match" id="rwc-match" onchange="rwc_select()">
<option value="x"> -- Choose your match ticket -- </option><!--
<option value="Match-24-Australia-v-Uruguay-from-411">Match 24 Australia v Uruguay</option>
<option value="Match-33-Australia-v-Georgia-from-481">Match 33 Australia v Georgia</option>
<option value="Match-17-Australia-v-Wales-from-770">Match 17 Australia v Wales</option>
<option value="Match-43-QF-3-W-Pool-D-v-RU-Pool-C-from-929">Match 43 QF 3 W Pool D v RU Pool C</option>
<option value="Match-41-QF-1-W-Pool-C-v-RU-Pool-D-from-929">Match 41 QF 1 W Pool C v RU Pool D</option>
<option value="Match-46-SF2-W-QF3-v-W-QF4-from-1627">Match 46 SF2 W QF3 v W QF4</option>
<option value="Match-45-SF1-W-QF1-v-W-QF2-from-1627">Match 45 SF1 W QF1 v W QF2</option> -->
<option value="Match-47-Bronze-Final-from-780">Match 47 Bronze Final - NEW ZEALAND V WALES</option>
</select>

<br /><br />

<p><strong>Note</strong>: Match 48 Final tickets are sold out - Match 47 Bronze Final still available.</p>




<br /><br />
<div id="travel_opt"></div>





<input type="hidden" name="total_pack" id="total_pack" value="0">
<input type="hidden" name="total_pack_price_only" id="total_pack_price_only" value="0">
<input type="hidden" name="total_pack_price" id="total_pack_price" value="0">

<input type="hidden" name="total_extras" id="total_extras" value="0">
<input type="hidden" name="package-no" id="package-no" value="0">



<br />
<div id="total_print"></div>

<br />


</form>

<button id="rwc-transport-submit" class="button btnLarge btn">Sumbit request</button>


<div id="rwc_response"></div>


<br /><br />
Your request will be sent to our sales team, and we will contact you shortly.




<br /><br />



