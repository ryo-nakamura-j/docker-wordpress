<div>
	<div class="mo2f_table_divide_border">
		<h2>2. Session Control / Session Management<a class="mo2fa-addons-preview-alignment" onclick="mo2f_login_session_control()">&nbsp;&nbsp;See Preview</a>
			<br>
			<hr>
			<p><i class="mo_wpns_not_bold">This will help you limit the number of simultaneous sessions for your users. You can decide to allow access to the new session after limit is reached and destroy all other sessions or block access to the new session when the limit is reached.</i></p>
		 
		</h2>
		
    </div>

    <div id="mo2f_login_session_control" style="display: none;">
	   <div class="mo2f_table_layout" style="background-color: aliceblue; border:none;">
	             <span><h3> Limit Simultaneous Sessions</h3>
                 </span>
                 <hr>
	      
	             <input type="checkbox" id="mo2f_device_restriction" name="mo2f_device_restriction"  value="1" <?php echo"disabled";?>/> Enable '<b>Session Restriction</b>' option.
				 <br><br><br>
				   Enter the maximum simultaneous sessions allowed: <input type="number" class="mo2f_table_textbox" min="1" style="width:10%; margin-left: 1%; margin-right: 1%;" name="mo2fa_simultaneous_session_allowed" value=""<?php echo"disabled";?>/> 
				   <br><br>
            <b>Allow access</b> will allow user to login but terminate all other active session when the limit reached. <b>Disable access</b> will not all users to login when the limit is reached.
            <br><br>
			
			<input type="radio" name="mo2f_enable_simultaneous_session" value="1" <?php echo"disabled";?> />
             Allow access  
            <span style="margin-left:50px"></span>
            <input type="radio" name="mo2f_enable_simultaneous_session" value="0" <?php echo "disabled";?> />
            Disable access
            <br><br>
			<div class="mo2f_advanced_options_note" style="background-color: #bfe5e9;padding:12px"><b>Note:</b><?php echo __(' All other sessions would be destroyed except for the current session after saving the settings', 'miniorange-2-factor-authentication');?>.</div>
			<br><br>	
			<label >
							<input  type="submit" value="save settings" <?php echo 'disabled'; ?> class="button button-primary button-large">
						</label>
        </div>
    </div>

			
</div>
 
 <script type="text/javascript">
    function mo2f_login_session_control()
    {
        jQuery('#mo2f_login_session_control').toggle();
    }
    function mo2f_idle_session_control()
    {
        jQuery('#mo2f_idle_session_control').toggle();
    }
	function mo2f_set_time_session_control()
	{
		jQuery('#mo2f_set_time_session_control').toggle();
	}
   
</script>