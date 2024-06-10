<?php

echo'<div class="mo_wpns_divided_layout">
		<div class="mo_wpns_setting_layout">';

echo'	<div>

		<form name="f" method="post" action="" id="manualblockipform" >
		<input type="hidden" name="option" value="mo_wpns_manual_clear" />
		<table>
            <tr>
                <td style="width: 100%">
                    <a class="button button-primary button-large" href="'.esc_url($dashboard_url).'">Back</a>

                    <h2>
                        Login Transactions Report
                    </h2>
                </td>
		        <td>
                    <input type="submit"" class="button button-primary button-large" value="Clear Login Reports" />
                </td>
            </tr>
        </table>
		<br>
	</form>
		</div>
		
			<div class="mo_wpns_setting_layout hidden">	
				<div style="float:right;margin-top:10px">
					<input type="submit" name="printcsv" style="width:100px;" value="Print PDF" class="button button-success button-large">
					<input type="submit" name="printpdf" style="width:100px;" value="Print CSV" class="button button-success button-large">
				</div>
				<h3>Advanced Report</h3>
				
				<form id="mo_wpns_advanced_reports" method="post" action="">
					<input type="hidden" name="option" value="mo_wpns_advanced_reports">
					<table style="width:100%">
					<tr>
					<td width="33%">WordPress Username : <input class="mo_wpns_table_textbox" type="text" name="username" required="" placeholder="Search by username" value=""></td>
					<td width="33%">IP Address :<input class="mo_wpns_table_textbox" type="text" name="ip" required="" placeholder="Search by IP" value=""></td>
					<td width="33%">Status : <select name="status" style="width:100%;">
						  <option value="success" selected="">Success</option>
						  <option value="failed">Failed</option>
						</select>
					</td>
					</tr>
					<tr><td><br></td></tr>
					<tr>
					<td width="33%">User Action : <select name="action" style="width:100%;">
						  <option value="login" selected="">User Login</option>
						  <option value="register">User Registeration</option>
						</select>
					</td>
					<td width="33%">From Date : <input class="mo_wpns_table_textbox" type="date"  name="fromdate"></td>
					<td width="33%">To Date :<input class="mo_wpns_table_textbox" type="date"  name="todate"></td>
					</tr>
					</table>
					<br><input type="submit" name="Search" style="width:100px;" value="Search" class="button button-primary button-large">
				</form>
				<br>
			</div>
			
			<table id="login_reports" class="display" cellspacing="0" width="100%">
		        <thead>
		            <tr>
		                <th>IP Address</th>
						<th>Username</th>
						<th>Status</th>
		                <th>TimeStamp</th>
		            </tr>
		        </thead>
		        <tbody>';
		           
		           showLoginTransactions($logintranscations);

echo'	        </tbody>
		    </table>
		</div>
		<div class="mo_wpns_setting_layout">	
			<div>

		<form name="f" method="post" action="" id="manualblockipforms" >
		<input type="hidden" name="option" value="mo_wpns_manual_errorclear" />
		<table>
            <tr>
                <td style="width: 100%">
                    <h2>
                        Error Report
                    </h2>
                </td>
		        <td>
                    <input type="submit"" class="button button-primary button-large" value=" Clear Error Reports" />
                </td>
            </tr>
        </table>
		<br>
	</form>
		</div>
			<table id="error_reports" class="display" cellspacing="0" width="100%">
		        <thead>
		            <tr>
		                <th>IP Address</th>
						<th>Username</th>
						<th>URL</th>
						<th>Error Type</th>
		                <th>TimeStamp</th>
		            </tr>
		        </thead>
		        <tbody>';
		        
		       showErrorTransactions($errortranscations);

echo'	        </tbody>
		    </table>
		</div>
	</div>
<script>
	jQuery(document).ready(function() {
		$("#login_reports").DataTable({
			"order": [[ 3, "desc" ]]
		});
		$("#error_reports").DataTable({
			"order": [[ 4, "desc" ]]
		});
	} );
</script>';