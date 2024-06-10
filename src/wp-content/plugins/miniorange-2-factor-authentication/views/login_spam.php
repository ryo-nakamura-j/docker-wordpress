 
<div class="nav-tab-wrapper">
	<button class="nav-tab" onclick="mo2f_wpns_login_span_openTab(this)" id="mo2f_login_sec">Login Security</button>
    <button class="nav-tab" onclick="mo2f_wpns_login_span_openTab(this)" id="mo2f_reg_sec">Registration Security</button>
    <button class="nav-tab" onclick="mo2f_wpns_login_span_openTab(this)" id="mo2f_spam_content">Content & Spam</button>
</div>
<br>
	<div class="tabcontent" id="mo2f_login_sec_div">
		<div class="mo_wpns_divided_layout">
			<table style="width:100%;">
				<tr>
					<td>
						<?php include_once $mo2f_dirName . 'controllers'.DIRECTORY_SEPARATOR.'login-security.php'; ?>
					</td>
				</tr>
			</table>
		</div>
	</div>
<div class="tabcontent" id="mo2f_reg_sec_div">
	<div class="mo_wpns_divided_layout">
		<table style="width:100%;">
			<tr>
				<td>
					<?php include_once $mo2f_dirName . 'controllers'.DIRECTORY_SEPARATOR.'registration-security.php'; ?>
				</td>
			</tr>
		</table>
	</div>
</div>
<div class="tabcontent" id="mo2f_spam_content_div">
	<div class="mo_wpns_divided_layout">
		<table style="width:100%;">
			<tr>
				<td>
					<?php include_once $mo2f_dirName . 'controllers'.DIRECTORY_SEPARATOR.'content-protection.php'; ?>
				</td>
			</tr>
		</table>
	</div>
</div>
<script>

	function mo2f_wpns_login_span_openTab(elmt){
		var tabname = elmt.id;
		var tabarray = ["mo2f_login_sec","mo2f_reg_sec","mo2f_spam_content"];
		for (var i = 0; i < tabarray.length; i++) {
			if(tabarray[i] == tabname){
				jQuery("#"+tabarray[i]).addClass("nav-tab-active");
				jQuery("#"+tabarray[i]+"_div").css("display", "block");
			}else{
				jQuery("#"+tabarray[i]).removeClass("nav-tab-active");
				jQuery("#"+tabarray[i]+"_div").css("display", "none");
			}
		}
		
		localStorage.setItem("login_spam_last_tab", tabname);
	}	

	jQuery('#login_spam_tab').addClass('nav-tab-active');

	var tab = localStorage.getItem("login_spam_last_tab"); 

	if(tab)
		document.getElementById(tab).click();
	else{
		document.getElementById("mo2f_login_sec").click();
	}

</script>