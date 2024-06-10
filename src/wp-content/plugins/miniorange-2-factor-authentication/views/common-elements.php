<?php
    
	//Function to show Error message if user is not registered
    //not needed any more
	function is_customer_valid()
	{
		global $moWpnsUtility;
		$url 	=	add_query_arg( array('page' => 'mo_2fa_account'), sanitize_url($_SERVER['REQUEST_URI']) );
		if (!$moWpnsUtility->icr())
			echo '<div class="warning_div">Please <a href="'.esc_url($url).'">Register or Login with miniOrange</a> to configure the miniOrange 2-Factor Plugin.</div>';
	}


	//Function to show Login Transactions
	function showLoginTransactions($usertranscations)
	{
		 foreach($usertranscations as $usertranscation)
        {
        		echo "<tr><td>".esc_attr($usertranscation->ip_address)."</td><td>".esc_attr($usertranscation->username)."</td><td>";
				if($usertranscation->status==MoWpnsConstants::FAILED || $usertranscation->status==MoWpnsConstants::PAST_FAILED)
					echo "<span style=color:red>".esc_attr(MoWpnsConstants::FAILED)."</span>";
				elseif($usertranscation->status==MoWpnsConstants::SUCCESS)
					echo "<span style=color:green>".esc_attr(MoWpnsConstants::SUCCESS)."</span>";
				else
					echo "N/A";
				echo "</td><td>".date("M j, Y, g:i:s a",esc_attr($usertranscation->created_timestamp))."</td></tr>";
		}
	}


	//Function to show 404 and 403 Reports
	function showErrorTransactions($usertransactions)
	{
		foreach($usertransactions as $usertranscation)
        {
    		echo "<tr><td>".esc_attr($usertranscation->ip_address)."</td><td>".esc_attr($usertranscation->username)."</td>";
			echo "<td>".esc_url($usertranscation->url)."</td><td>".esc_attr($usertranscation->type)."</td>";
			echo "</td><td>".date("M j, Y, g:i:s a",esc_attr($usertranscation->created_timestamp))."</td></tr>";
		}
	}
	//Function to show user details
	function mo2f_show_user_details($users)
	{   global $Mo2fdbQueries;
		
		if(is_array($users))
		{
				foreach($users as $user)
				{   
					if(get_site_option('mo2fa_'.$user->roles[0]))
					{
						$mo2f_method_selected=$Mo2fdbQueries->get_user_detail('mo2f_configured_2FA_method',$user->ID);
						$mo2f_user_registration_status=$Mo2fdbQueries->get_user_detail("mo_2factor_user_registration_status",$user->ID);
						$mo2f_reset_user='';
						
						$mo2f_unblock='';
						
				
						echo '<tr><td>'.esc_attr($user->user_login).
						'</td><td>'.esc_attr($user->user_email).
						'</td><td>'.esc_attr($user->roles[0]).
						'</td><td>'.
						'<span>';
                        echo (empty($mo2f_method_selected))?"None":esc_attr( $mo2f_method_selected);
                        echo '</span>';
						
						echo '</td><td>';
						if($mo2f_user_registration_status=='MO_2_FACTOR_INITIALIZE_TWO_FACTOR' || $mo2f_user_registration_status=='MO_2_FACTOR_PLUGIN_SETTINGS')
						{?>
							<form action="users.php?page=reset&action=reset_edit&amp;user=<?php echo esc_attr($user->ID) ?>" method="post" name="reset2fa" id="reset2fa">
							
							<input type="submit" name="mo2f_reset_2fa" id="mo2f_reset_2fa" value="Reset 2FA" class="button button-primary button-large " />
							</form>
							<?php
						}
						
						echo '</td> </tr>';
					} 
					else
					{
						continue;
					}
				}
	    }
	}
	//Function to show google recaptcha v3 upon login

    function show_google_recaptcha_form_v3_login()
    {
    	$site_k=get_option('mo_wpns_recaptcha_site_key_v3');
		
		wp_register_script( 'mo2f_recaptcha','https://www.google.com/recaptcha/api.js?render='.get_option("mo_wpns_recaptcha_site_key_v3"),[],MO2F_VERSION);
		wp_enqueue_script('mo2f_recaptcha');
		echo'
            <div class="g-recaptcha-response" data-sitekey="'.esc_html($site_k).'"></div>
            <input type="hidden"  name="g-recaptcha-response" id="g-recaptcha-response">
        ';?>
	    
	    <script>
       
        grecaptcha.ready(function() {

        	var sitek = "<?php echo esc_html($site_k);?>";
        	grecaptcha.execute(  sitek, {action:"homepage"}).
            then(function(token) {
                document.getElementById("g-recaptcha-response").value=token;
          });
        });
      
        </script>
        <?php
	}
 
    
   	//Function to show google recaptcha v2 form
   	function show_google_recaptcha_form_v2_login()
   	{
		wp_register_script( 'mo2f_catpcha_js',esc_url(MoWpnsConstants::RECAPTCHA_URL),[],MO2F_VERSION);
		wp_enqueue_script( 'mo2f_catpcha_js' );
		echo '<div class="g-recaptcha" data-sitekey="'.esc_html(get_option("mo_wpns_recaptcha_site_key")).'"></div>';
		echo '<style>#login{ width:349px;padding:2% 0 0; }.g-recaptcha{margin-bottom:5%;}#registerform{padding-bottom:20px;}</style>';
	}


    function show_google_recaptcha_form_v2()
    {
    	wp_register_style('mo2f_admin_css',site_url().'/wp-admin/load-styles.php?c=1&amp;dir=ltr&amp;load%5B%5D=dashicons,admin-bar,common,forms,admin-menu,dashboard,list-tables,edit,revisions,media,themes,about,nav-menus,widgets,site-icon,&amp;load%5B%5D=l10n,buttons,wp-auth-check&amp;ver=4.5.2"/>',[],MO2F_VERSION);
		wp_register_style( 'mo2f_style_settings',plugins_url( 'includes/css/style_settings.css', dirname(__FILE__)),[],MO2F_VERSION);
		
		wp_print_styles('mo2f_admin_css');
    	wp_print_styles( 'mo2f_style_settings' );

		wp_register_script( 'mo2f_catpcha_js',esc_url(MoWpnsConstants::RECAPTCHA_URL),[],MO2F_VERSION);
		wp_enqueue_script( 'mo2f_catpcha_js' );

		echo '<div style="font-family:\'Open Sans\',sans-serif;margin:0px auto;width:303px;text-align:center;">
				<br><br><h2>Test google reCAPTCHA keys</h2>
				<form method="post">
					<div class="g-recaptcha" data-sitekey="'.esc_html(get_option('mo_wpns_recaptcha_site_key')).'"></div>
					<br><input class="mo2f_test_captcha_button" type="submit" value="Test Keys" class="button button-primary button-large">
				</form>
			</div>';
		exit();
	}


	//Function to show google recaptcha v3 form

	function show_google_recaptcha_form_v3()
	{
			$site_k=get_option('mo_wpns_recaptcha_site_key_v3');
			
			wp_register_style('mo2f_admin_css',site_url().'/wp-admin/load-styles.php?c=1&amp;dir=ltr&amp;load%5B%5D=dashicons,admin-bar,common,forms,admin-menu,dashboard,list-tables,edit,revisions,media,themes,about,nav-menus,widgets,site-icon,&amp;load%5B%5D=l10n,buttons,wp-auth-check&amp;ver=4.5.2"/>',[],MO2F_VERSION);
			wp_register_style( 'mo2f_style_settings',plugins_url( 'includes/css/style_settings.css', dirname(__FILE__)),[],MO2F_VERSION);
			
			wp_print_styles('mo2f_admin_css');
			wp_print_styles( 'mo2f_style_settings' );
			
			wp_register_script( 'mo2f_recaptcha','https://www.google.com/recaptcha/api.js?render='.get_option("mo_wpns_recaptcha_site_key_v3"),[],MO2F_VERSION);
			wp_enqueue_script('mo2f_recaptcha');
			echo'
			    <div style="font-family:\'Open Sans\',sans-serif;margin:0px auto;width:303px;text-align:center;">
				<br><br><h2>Test google reCAPTCHA keys</h2>
                <form id="f1" method="post">
                    <div class="g-recaptcha-response" data-sitekey="' . esc_html($site_k) . '"></div>
                    <input type="hidden" name="g-recaptcha-response" id="g-recaptcha-response">
                    <br><input class="mo2f_test_captcha_button" type="submit" value="Test Keys" class="button button-primary button-large">
                </form>
                </div>
            </div>';
			?>
	    
	    <script>
       
        grecaptcha.ready(function() {        	
        	var sitek = ""+"<?php echo esc_html(get_option("mo_wpns_recaptcha_site_key_v3"));?>";
        	grecaptcha.execute(sitek, {action:"homepage"}).
            then(function(token) {
                document.getElementById("g-recaptcha-response").value=token;
          });
        });
      
        </script>
        <?php
		exit();
	}
