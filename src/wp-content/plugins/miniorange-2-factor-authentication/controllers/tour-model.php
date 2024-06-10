<?php

    global $mo2f_dirName;
    $current_user   = wp_get_current_user();
    $email          = get_option("mo2f_email");
    $phone          = get_option("mo_wpns_admin_phone");
    $display        = get_option('mo2f_tour_started') == 2?'block':'none';
    $networkSEnable = MoWpnsUtility::get_mo2f_db_option('mo_wpns_2fa_with_network_security', 'get_option');
    if(empty($email))
        $email      = $current_user->user_email;
	$counter = 0;
    $enableTour = $networkSEnable == 1 ? '' : 'disabled';
   $tour_body = '<p class="modal-body-para">Hey, Thank you for installing <b style="color: #E85700">miniOrange 2-Factor plugin</b>.</p>
                <p class="modal-body-para">Two-factor will provide extra layer of security to your users account which will help to protect your users accounts from any outside attack.</p>';
        if($networkSEnable != 1)
        {    
            $tour_body .= '<div style="margin-left:37.5%;margin-right:37.5%;">';
        }
        
        $tour_body .= '<table style="width: 100%; text-align: center; table-layout: fixed; font-size: medium;">
                <div class="mo2f_hiddenradio">
                <tr>

                    <td style="border: 1px solid black;" id="2fa">
                        <label>
                        <input type="radio" name="mo2f_two_factor" value="2fa" checked style="display:none">
                        <img src="'.plugin_dir_url(dirname(__FILE__)) . 'includes/images/google-authenticator.png">
                        </label>
                    </td>';
    if($networkSEnable == 1)
    {              
        $tour_body .=   '<td style="border: 1px solid black;" id="waf">
                            <label >
                                <input type="radio" name="mo2f_two_factor" value="waf" style="display:none" '.$enableTour.'>  
                                <img src="'.plugin_dir_url(dirname(__FILE__)) . 'includes/images/mo-waf-logo.png">
                            </label>
                        </td>
                      
                        <td style="border: 1px solid black;" id="login">
                            <label >
                                <input type="radio" name="mo2f_two_factor" value="login" style="display:none"'.$enableTour.'>          
                                <img src="'.plugin_dir_url(dirname(__FILE__)) . 'includes/images/login-protection-logo.png">
                            </label>
                        </td>
                        
                        <td style="border: 1px solid black;" id="backup">
                            <label>
                                <input type="radio" name="mo2f_two_factor" value="backup" style="display:none"'.$enableTour.'>
                                <img src="'.plugin_dir_url(dirname(__FILE__)) . 'includes/images/database-backup-logo.png">
                            </label>
                        </td>
                        
                        <td style="border: 1px solid black;" id="malware">
                            <label >
                                <input type="radio" name="mo2f_two_factor" value="malware" style="display:none"'.$enableTour.'> 
                                <img src="'.plugin_dir_url(dirname(__FILE__)) . 'includes/images/malware-scanner-logo.png">
                            </label>
                        </td>';
    }
    $tour_body .= '</tr>
                    <tr>
                    <th>
                        Two-factor authentication
                    </th>';
                    
    if($networkSEnable == 1)
    {                

    $tour_body .=  '<th>
                        Web Application Firewall(WAF)
                    </th>
                    
                    <th>
                        Login Protection
                    </th>
                    
                    <th>
                        Database Backup
                    </th>
                    
                    <th>
                        Malware scanner
                    </th>';
    }
     
    $tour_body .= '</tr>
                </div>
                </table>';
        if($networkSEnable != 1)
        {    
            $tour_body .= '</div>';
        }

    $waf_arr_ecc = '<div class="modal-body-div-c modal-body-div-d"><div id="div-show-hide-1" style="width: 98%; overflow: ; height: ;line-height: 1.5;"><b><u>Data theft and manipulation</u>:</b> Data manipulation can lead to alter, delete, destroy data. The manipulated data may or may not be regained. It includes very sensitive data such as user details, credit/debit card or bank details. It is very necessary to fix the existing data vulnerability issues, data leaks, change weak passwords and provide high end security to stop data breach and manipulation.<div class="modal-waf-dinner"><span class="modal-waf-sinner">Prevent SQL-Injection attacks:</span> SQL-Injection is web security vulnerability through SQL queries executed to modify, delete and destroy data. </div></div><div style="width: 2%; font-size: xx-large;"><a id="show-hide-1" onclick="open_hide(this);">-</a></div></div><div class="modal-body-div-c modal-body-div-d"><div id="div-show-hide-2" style="width: 98%; overflow: hidden; height: 50px;line-height: 1.5;"><b><u>Web Scraping</u>:</b> Web scraping is a used to extract large amount of data from websites and saved on local computer. The web scraping involves fetching and extracting data from it.  It can be used to web indexing, web mining, data mining, research, tracking online presence and reputation, etc. Media scraping, price scraping are also some scraping techniques which are used to degrade/destroy media files and change the price of products.<div class="modal-waf-dinner"><span class="modal-waf-sinner">Prevent Cross-site scripting(XSS) attacks:</span> Cross site scripts used to web scraping and data extraction.</div></div><div style="width: 2%; font-size: xx-large;"><a id="show-hide-2" onclick="open_hide(this);">+</a></div></div><div class="modal-body-div-c modal-body-div-d"><div id="div-show-hide-3" style="width: 98%; overflow: hidden; height: 50px;line-height: 1.5;"><b><u>File manipualtion</u>:</b> The file manipuaiton used to alter, delete, execution of files on the sever. It leads to spoil site, spread malicious content which will harm to the business. <div class="modal-waf-dinner"><span class="modal-waf-sinner">Prevent Remote File Inclusion attacks:</span> Remote file inclusion used to include local file into the server. RFI is type of vulnerability which can lead to add malicious file through a script on server.</div><div class="modal-waf-dinner"><span class="modal-waf-sinner">Prevent Local File Inclusion attacks:</span> Local file inclusion used to access local file available on the server. LFI can be achieved by uploading malicious file to the server.</div></div><div style="width: 2%; font-size: xx-large;"><a id="show-hide-3" onclick="open_hide(this);">+</a></div></div><div class="modal-body-div-c modal-body-div-d"><div id="div-show-hide-4" style="width: 98%; overflow: hidden; height: 50px;line-height: 1.5;"><b><u>Content modification</u>:</b> Cross-site scripting used to change or modify data shown on website. Content modification affects a lot on business due to irrelevent content, malicious links which leads to spoil the trust of clients and reputation of organizations.<div class="modal-waf-dinner"><span class="modal-waf-sinner">Prevent SQL-Injection attacks:</span> SQL-Injection attack can change data in database. The data used to view content such as statistical data, charts, graphs, etc. It may mislead to business.</div><div class="modal-waf-dinner"><span class="modal-waf-sinner">Prevent Cross-site scripting(XSS) attacks:</span> Cross site script can add malicious links, change content of site. </div></div><div style="width: 2%; font-size: xx-large;"><a id="show-hide-4" onclick="open_hide(this);">+</a></div></div>';

    $waf_arr_busi = '<div class="modal-body-div-c modal-body-div-d"><div id="div-show-hide-1" style="width: 98%; overflow: ; height: ;line-height: 1.5;"><b><u>Data theft and manipulation</u>:</b> Data manipulation can lead to alter, delete, destroy data. The manipulated data may or may not be regained. It may include company privileged data, admin users data which will affect on the business. It is very necessary to fix the existing data vulnerability issues, data leaks, change weak passwords and provide high end security to stop data breach and manipulation.<div class="modal-waf-dinner"><span class="modal-waf-sinner">Prevent SQL-Injection attacks:</span> SQL-Injection is web security vulnerability through SQL queries executed to modify, delete and destroy data. </div></div><div style="width: 2%; font-size: xx-large;"><a id="show-hide-1" onclick="open_hide(this);">-</a></div></div><div class="modal-body-div-c modal-body-div-d"><div id="div-show-hide-2" style="width: 98%; overflow: hidden; height: 50px;line-height: 1.5;"><b><u>Web Scraping</u>:</b> Web scraping is a used to extract large amount of data from websites and saved on local computer. The web scraping involves fetching and extracting data from it. It can be used to web indexing, web mining, data mining, research, tracking online presence and reputation, etc. Price scraping is part of web scraping which is used to change prices of the products which affects a lot on business.<div class="modal-waf-dinner"><span class="modal-waf-sinner">Prevent Cross-site scripting(XSS) attacks:</span> Cross site scripts used to web scraping and data extraction.</div></div><div style="width: 2%; font-size: xx-large;"><a id="show-hide-2" onclick="open_hide(this);">+</a></div></div><div class="modal-body-div-c modal-body-div-d"><div id="div-show-hide-3" style="width: 98%; overflow: hidden; height: 50px;line-height: 1.5;"><b><u>File manipualtion</u>:</b> The file manipuaiton used to alter, delete, execution of files on the sever. It leads to spoil site, spread malicious content which will harm to the business.<div class="modal-waf-dinner"><span class="modal-waf-sinner">Prevent Remote File Inclusion attacks:</span> Remote file inclusion used to include local file into the server. RFI is type of vulnerability which can lead to add malicious file through a script on server.</div><div class="modal-waf-dinner"><span class="modal-waf-sinner">Prevent Local File Inclusion attacks:</span> Local file inclusion used to access local file available on the server. LFI can be achieved by uploading malicious file to the server.</div></div><div style="width: 2%; font-size: xx-large;"><a id="show-hide-3" onclick="open_hide(this);">+</a></div></div><div class="modal-body-div-c modal-body-div-d"><div id="div-show-hide-4" style="width: 98%; overflow: hidden; height: 50px;line-height: 1.5;"><b><u>Content modification</u>:</b> Cross-site scripting used to change or modify data shown on website. Content modification affects a lot on business due to irrelevent content, malicious links which leads to spoil the trust of clients and reputation of organizations.<div class="modal-waf-dinner"><span class="modal-waf-sinner">Prevent SQL-Injection attacks:</span> SQL-Injection attack can change data in database. The data used to view content such as statistical data, charts, graphs, etc. It may mislead to business.</div><div class="modal-waf-dinner"><span class="modal-waf-sinner">Prevent Cross-site scripting(XSS) attacks:</span> Cross site script can add malicious links, change content of site. </div></div><div style="width: 2%; font-size: xx-large;"><a id="show-hide-4" onclick="open_hide(this);">+</a></div></div>';

    $waf_arr_blog = '<div class="modal-body-div-c modal-body-div-d"><div id="div-show-hide-1" style="width: 98%; overflow: ; height: ;line-height: 1.5;"><b><u>Data theft and manipulation</u>:</b> Data manipulation can lead to alter, delete, destroy data. The manipulated data may or may not be regained. It is very necessary to fix the existing data vulnerability issues, data leaks, change weak passwords and provide high end security to stop data breach and manipulation. The data may include user details, privileged data, privileged blogs, etc.<div class="modal-waf-dinner"><span class="modal-waf-sinner">Prevent SQL-Injection attacks:</span> SQL-Injection is web security vulnerability through SQL queries executed to modify, delete and destroy data. </div></div><div style="width: 2%; font-size: xx-large;"><a id="show-hide-1" onclick="open_hide(this);">-</a></div></div><div class="modal-body-div-c modal-body-div-d"><div id="div-show-hide-2" style="width: 98%; overflow: hidden; height: 50px;line-height: 1.5;"><b><u>Web Scraping</u>:</b> Web scraping is a used to extract large amount of data from websites and saved on local computer. The web scraping involves fetching and extracting data from it. It can be used to web indexing, web mining, data mining, research, tracking online presence and reputation, etc. Media scraping can be done in the blog/news site which alter, degrade or destroy media files.<div class="modal-waf-dinner"><span class="modal-waf-sinner">Prevent Cross-site scripting(XSS) attacks:</span> Cross site scripts used to web scraping and data extraction.</div></div><div style="width: 2%; font-size: xx-large;"><a id="show-hide-2" onclick="open_hide(this);">+</a></div></div><div class="modal-body-div-c modal-body-div-d"><div id="div-show-hide-3" style="width: 98%; overflow: hidden; height: 50px;line-height: 1.5;"><b><u>File manipualtion</u>:</b> The file manipuaiton used to alter, delete, execution of files on the sever. It leads to spoil site, spread malicious content which will harm to the business.<div class="modal-waf-dinner"><span class="modal-waf-sinner">Prevent Remote File Inclusion attacks:</span> Remote file inclusion used to include local file into the server. RFI is type of vulnerability which can lead to add malicious file through a script on server.</div><div class="modal-waf-dinner"><span class="modal-waf-sinner">Prevent Local File Inclusion attacks:</span> Local file inclusion used to access local file available on the server. LFI can be achieved by uploading malicious file to the server.</div></div><div style="width: 2%; font-size: xx-large;"><a id="show-hide-3" onclick="open_hide(this);">+</a></div></div><div class="modal-body-div-c modal-body-div-d"><div id="div-show-hide-4" style="width: 98%; overflow: hidden; height: 50px;line-height: 1.5;"><b><u>Content modification</u>:</b> Cross-site scripting used to change or modify data shown on website. Content modification affects a lot on business due to irrelevent content, malicious links which leads to spoil the trust of clients and reputation of organizations. Content modification may mislead to blog/news site subscriber. It can be used to spread fake/malicious imformation.<div class="modal-waf-dinner"><span class="modal-waf-sinner">Prevent SQL-Injection attacks:</span> SQL-Injection attack can change data in database. The data used to view content such as statistical data, charts, graphs, etc. It may mislead to business.</div><div class="modal-waf-dinner"><span class="modal-waf-sinner">Prevent Cross-site scripting(XSS) attacks:</span> Cross site script can add malicious links, change content of site. </div></div><div style="width: 2%; font-size: xx-large;"><a id="show-hide-4" onclick="open_hide(this);">+</a></div></div>';

    $waf_arr_other = '<div class="modal-body-div-c modal-body-div-d"><div id="div-show-hide-1" style="width: 98%; overflow: ; height: ;line-height: 1.5;"><b><u>Data theft and manipulation</u>:</b> Data manipulation can lead to alter, delete, destroy data. The manipulated data may or may not be regained. It is very necessary to fix the existing data vulnerability issues, data leaks, change weak passwords and provide high end security to stop data breach and manipulation.<div class="modal-waf-dinner"><span class="modal-waf-sinner">Prevent SQL-Injection attacks:</span> SQL-Injection is web security vulnerability through SQL queries executed to modify, delete and destroy data. </div></div><div style="width: 2%; font-size: xx-large;"><a id="show-hide-1" onclick="open_hide(this);">-</a></div></div><div class="modal-body-div-c modal-body-div-d"><div id="div-show-hide-2" style="width: 98%; overflow: hidden; height: 50px;line-height: 1.5;"><b><u>Web Scraping</u>:</b> Web scraping is a used to extract large amount of data from websites and saved on local computer. The web scraping involves fetching and extracting data from it. It can be used to web indexing, web mining, data mining, research, tracking online presence and reputation, etc.<div class="modal-waf-dinner"><span class="modal-waf-sinner">Prevent Cross-site scripting(XSS) attacks:</span> Cross site scripts used to web scraping and data extraction.</div></div><div style="width: 2%; font-size: xx-large;"><a id="show-hide-2" onclick="open_hide(this);">+</a></div></div><div class="modal-body-div-c modal-body-div-d"><div id="div-show-hide-3" style="width: 98%; overflow: hidden; height: 50px;line-height: 1.5;"><b><u>File manipualtion</u>:</b> The file manipuaiton used to alter, delete, execution of files on the sever. It leads to spoil site, spread malicious content which will harm to the business.<div class="modal-waf-dinner"><span class="modal-waf-sinner">Prevent Remote File Inclusion attacks:</span> Remote file inclusion used to include local file into the server. RFI is type of vulnerability which can lead to add malicious file through a script on server.</div><div class="modal-waf-dinner"><span class="modal-waf-sinner">Prevent Local File Inclusion attacks:</span> Local file inclusion used to access local file available on the server. LFI can be achieved by uploading malicious file to the server.</div></div><div style="width: 2%; font-size: xx-large;"><a id="show-hide-3" onclick="open_hide(this);">+</a></div></div><div class="modal-body-div-c modal-body-div-d"><div id="div-show-hide-4" style="width: 98%; overflow: hidden; height: 50px;line-height: 1.5;"><b><u>Content modification</u>:</b> Cross-site scripting used to change or modify data shown on website. Content modification affects a lot on business due to irrelevent content, malicious links which leads to spoil the trust of clients and reputation of organizations.<div class="modal-waf-dinner"><span class="modal-waf-sinner">Prevent SQL-Injection attacks:</span> SQL-Injection attack can change data in database. The data used to view content such as statistical data, charts, graphs, etc. It may mislead to business. </div><div class="modal-waf-dinner"><span class="modal-waf-sinner">Prevent Cross-site scripting(XSS) attacks:</span> Cross site script can add malicious links, change content of site. </div></div><div style="width: 2%; font-size: xx-large;"><a id="show-hide-4" onclick="open_hide(this);">+</a></div></div>';

    $waf_body_ecommerce = '<div class="modal-body-div">
    	<p class="modal-body-para" style="margin: 0;">Ecommerce sites are always under attack to spoil organizations reputaion, data theft and manipualation, file manipualation, web scraping, etc. WAF controls the access of web applications using rules designed to recognize and restrict suspicious activities.</p>';
    $waf_body_business = '<div class="modal-body-div">
    	<p class="modal-body-para" style="margin: 0;">Business sites are always under attack to spoil organizations reputaion, data theft and manipualation, file manipualation, web scraping, etc. WAF controls the access of web applications using rules designed to recognize and restrict suspicious activities.</p>';
    $waf_body_blog = '<div class="modal-body-div">
    	<p class="modal-body-para" style="margin: 0;">Blogs/News sites are attacked to spoil organizations reputaion, data theft and manipualation, file manipualation, web scraping, etc. WAF controls the access of web applications using rules designed to recognize and restrict suspicious activities.</p>';
    $waf_body_other = '<div class="modal-body-div">
    	<p class="modal-body-para" style="margin: 0;">Other sites such as employment, portfolio, multilingual, etc are attacked to spoil organizations reputaion, data theft and manipualation, file manipualation, web scraping, etc. WAF controls the access of web applications using rules designed to recognize and restrict suspicious activities.</p>';

    $waf_body = '<div style="width:100%; display:inline-flex; margin-left: 20px;">
	    	<div style="width:50%;font-size: medium;">1: <b>Data theft and manipulation</b></div>
	    	<div style="width:50%;font-size: medium;">2: <b>Web Scraping</b></div></div>
    	<div style="width:100%; display:inline-flex; margin-left: 20px;">
    		<div style="width:50%;font-size: medium;">3: <b>File manipulation</b></div>
    		<div style="width:50%;font-size: medium;">4: <b>Content modification</b></div></div>
    </div>';

    $registration_security_ecommerce = '<div class="modal-body-div-c modal-body-div-d"><div id="div-show-hide-13" style="width: 98%; overflow: hidden; height: 50px;line-height: 1.5;"><b><u>Registratoin security:</u></b> Ecommerce sites need to prevent fake registrations. It helps to keep site safe from suspicious user.';
    $registration_security_business = '<div class="modal-body-div-c modal-body-div-d"><div id="div-show-hide-13" style="width: 98%; overflow: hidden; height: 50px;line-height: 1.5;"><b><u>Registratoin security:</u></b> Business sites need to prevent fake registrations. It helps to keep site safe from suspicious user.';

    $registration_security_other = '<div class="modal-body-div-c modal-body-div-d"><div id="div-show-hide-13" style="width: 98%; overflow: hidden; height: 50px;line-height: 1.5;"><b><u>Registratoin security:</u></b> The sites such as employment, social networking, etc. need to prevent fake user registrations. The user will spam other users and share private data of others.';

    $registration_security = '<div style="margin:1em;"><ul>
    		<li>
    			<p class="modal-body-para" style="margin: 0;">1. Temporary/Fake email ban</p>
    		</li>
    		<li>
    			<p class="modal-body-para" style="margin: 0;">2. OTP Verification during registrations</p>
    		</li>
    		<li>
    			<p class="modal-body-para" style="margin: 0;">3. Social login</p>
    		</li>
    	</ul>
    </div></div><div style="width: 2%; font-size: xx-large;"><a id="show-hide-13" onclick="open_hide(this);">+</a></div></div>';

    $login_security_ecommerce = '<div class="modal-body-div"><p class="modal-body-para">Ecommerce sites should have strong login security to prevent unauthorised access.</p></div>';
    $login_security_business = '<div class="modal-body-div"><p class="modal-body-para">Business sites should have strong login security to prevent unauthorised access.</p></div>';
    $login_security_blog = '<div class="modal-body-div"><p class="modal-body-para">Blogs/News sites should have strong login security to prevent unauthorised access.</p></div>';
    $login_security_other = '<div class="modal-body-div"><p class="modal-body-para">Other sites such as employment, portfolio, etc. should have strong login security to prevent unauthorised access.</p></div>';

    $login_security_body = '<div class="modal-body-div-c modal-body-div-d"><div id="div-show-hide-10" style="width: 98%; overflow:; height: ;line-height: 1.5;"><b><u>Limit Login:</u></b> Password guessing/Brute force attack can be controlled/prevented by limiting the login attempts. Paasword guessing can be protected by:
	<div style="margin:1em;">
    	<ul>
    		<li>
    			<p class="modal-body-para">1. Limit Login attempts: 10 (<b>Recommended</b>)</p>
    		</li>
    		<li>
    			<p class="modal-body-para">2. Enforce Strong passwords</p>
    		</li>
    	</ul>
    </div></div><div style="width: 2%; font-size: xx-large;"><a id="show-hide-10" onclick="open_hide(this);">-</a></div></div>

    <div class="modal-body-div-c modal-body-div-d"><div id="div-show-hide-11" style="width: 98%; overflow: hidden; height: 50px;line-height: 1.5;"><b><u>2-Factor:</u></b> 2-factor authentication is important to prevent unauthorised login. 2-factor authentication can be enabled on top of login credentials at the time of login. Google authenticator, Authy authenticator, Lastpass authenticator applications can be used to configure 2FA. miniOrange supports 15+ 2FA methods, it includes OTP over SMS, Email, Security Questions, QR code authentication, push notification, Hardware Token, etc. 2FA can enabled to:
    <div style="margin:1em;">
    	<ul>
    		<li>
    			<p class="modal-body-para">1. Enable 2FA for employees (<b>Recommended</b>)</p>
    		</li>
    		<li>
    			<p class="modal-body-para">2. Enable 2FA for users</p>
    		</li>
    		<li>
    			<p class="modal-body-para">3. Backup methods in case of emergency login</p>
    		</li>
    	</ul>
    </div></div><div style="width: 2%; font-size: xx-large;"><a id="show-hide-11" onclick="open_hide(this);">+</a></div></div>
    <div class="modal-body-div-c modal-body-div-d"><div id="div-show-hide-12" style="width: 98%; overflow: hidden; height: 50px;line-height: 1.5;"><b><u>Remember device:</u></b> Remember device allows user to login from trusted devices. It keeps safe from unauthorised users who tries to gain access. It improves your login security by allowing you to login from limited and trusted deivces.
    <div style="margin:1em;">
    	<ul>
    		<li>
    			<p class="modal-body-para">1. Allows multiple trusted devices</p>
    		</li>
    		<li>
    			<p class="modal-body-para">2. Limit no. of trusted devices</p>
    		</li>
    		<li>
    			<p class="modal-body-para">3. Block IP\'s of unauthorised users</p>
    		</li>
    	</ul>
    </div></div><div style="width: 2%; font-size: xx-large;"><a id="show-hide-12" onclick="open_hide(this);">+</a></div></div>';


   $media_body = '<div class="modal-body-div"><p class="modal-body-para">Ecommerce sites are often attacked to degrade and destroy media files such as images of product, audios and videos about products. You need to secure your media data. Media protection is important because the users of ecommerce site buy product by viewing the media of products. The broad word media scraping can be used for these types attacks.</p>
    <div class="modal-body-div modal-body-para">Here is our <a target="_blank" href="https://wordpress.org/plugins/prevent-file-access/">Media protection</a> plugin.</div>
    <div class="modal-body-div">The Media protection prevents media files listed below:</div>
    </div>
    <div style="margin:1em;">
    	<ul>
    		<li>
    			<p class="modal-body-para">1. It protects all type of image files, audio, video and gif files.</p>
    		</li>
    		<li>
    			<p class="modal-body-para">2. It also prevents access to documents such as pdf, doc, docx, txt, xls, xlsx, etc.</p>
    		</li>
    	</ul>
    </div>';

    $media_body_1 = '<div class="modal-body-div"><p class="modal-body-para">Some sites such as Photo Gallery or other site which contains media files are often attacked to degrade and destroy media files. You need to secure your media data. Media protection is important because the these sites are totally based on media files.</p>
    <div class="modal-body-div modal-body-para">Here is our <a target="_blank" href="https://wordpress.org/plugins/prevent-file-access/">Media protection</a> plugin.</div>
    <div class="modal-body-div">The Media protection prevents media files listed below:</div>
    </div>
    <div style="margin:1em;">
        <ul>
            <li>
                <p class="modal-body-para">1. It protects all type of image files, audio, video and gif files.</p>
            </li>
            <li>
                <p class="modal-body-para">2. It also prevents access to documents such as pdf, doc, docx, txt, xls, xlsx, etc.</p>
            </li>
        </ul>
    </div>';

    $backup_ecommerce = '<div class="modal-body-div"><p class="modal-body-para">The data is backbone of any e-commerce site. The ecommerce sites are attacked to steal data, data manipulation and files manipulation. You can take a backup of your data and files securely.</p></div>';
    $backup_business = '<div class="modal-body-div"><p class="modal-body-para">The data is backbone of any Business site. The business sites are attacked to steal data, data manipulation and files manipulation. You can take a backup of your data and files securely.</p></div>';
    $backup_blog = '<div class="modal-body-div"><p class="modal-body-para">The data is backbone of any Blog/News site. These sites are attacked to steal data, data manipulation and files manipulation. You can take a backup of your data and files securely.</p></div>';
    $backup_blog = '<div class="modal-body-div"><p class="modal-body-para">The data is backbone of any site such as social networking, employment, etc. There are several attacks happens on these sites to steal data, data manipulation and files manipulation. You can take a backup of your data and files securely.</p></div>';


    $backup_body = '<div class="modal-body-div">
    	<ul>
    		<li>
    			<p class="modal-body-para"><b>1. <u>Scheduled backup:</u></b> Scheduled backup used to create automatic backup after scheduled time. It allows you to relax because you know that all of your information is being backed up, and you are limiting what you may lose. Using this feature you can set a time interval to create a continuous backup. ';
    $backup_body_1 = '<div class="modal-waf-dinner"><span class="modal-waf-sinner">Set scheduled backup time 1/2 hr for ecommerce site.</span></div>';
    $backup_body_2 = '<div class="modal-waf-dinner"><span class="modal-waf-sinner">You can set automatic backup after 12 hrs time interval.</span></div>';
    $backup_body_3 = '<div class="modal-waf-dinner"><span class="modal-waf-sinner">You can set automatic backup after 4-5 hrs time interval.</span></div>';
    $backup_body_4 = '</p>
    		</li>
    		<li>
    			<p class="modal-body-para"><b>2. <u>Manual backup:</u></b> Manual backup can be taken manually whenever user wants. It is important when you made any crucial changes and don\'t want to loose data then you can immediately take a backup and keep it securely.</p>
    		</li>
    		<li>
    			<p class="modal-body-para"><b>3. <u>Password protected encrypted backup:</u></b> Password protected encrypted backup is very secure way to take a backup. If anyone gets it still no one can decode it because of its highly secure nature.</p>
    		</li>
    		<li>
    			<p class="modal-body-para"><b>4. <u>Easy restoring and recovering:</u></b> Simple one-click restore using installer files created for your website. During backup, we create an installer.php file. At the time of restore and recovery, you just need to upload the zip file of backup and installer.php and run the installer file and follow instructions to set up your website.</p>
    		</li>
    	</ul>
    </div>';

    $comment_ecommerce = '<div class="modal-body-div"><p class="modal-body-para">Ecommerce websites are attacked to spread unwanted or irrelevant content by submitting automated posts, comments, contact forms, etc. A spam message, content or comment includes backlinks or excessive links that redirect to illegal websites or websites containing inappropriate content. Automated scripts and botnets are used to spread such spam comments and posts to spoil the organizations reputation or product reputation. </p></div>';
    $comment_business = '<div class="modal-body-div"><p class="modal-body-para">Business websites are used to spread unwanted or irrelevant content by submitting automated posts, comments, contact forms, etc. A spam message, content or comment includes backlinks or excessive links that redirect to illegal websites or websites containing inappropriate content. Automated scripts and botnets are used to spread such spam comments and posts to spoil the organizations reputation or product reputation. </p></div>';
    $comment_blog = '<div class="modal-body-div"><p class="modal-body-para">Blog/News websites are used to spread unwanted or irrelevant content by submitting automated posts, comments, contact forms, etc. A spam message, content or comment includes backlinks or excessive links that redirect to illegal websites or websites containing inappropriate content. Automated scripts and botnets are used to spread such spam comments and posts to spoil the organizations reputation or product reputation. </p></div>';
    $comment_other = '<div class="modal-body-div"><p class="modal-body-para">Other websites such as employment, multilingual, etc. are used to spread unwanted or irrelevant content by submitting automated posts, comments, contact forms, etc. A spam message, content or comment includes backlinks or excessive links that redirect to illegal websites or websites containing inappropriate content. Automated scripts and botnets are used to spread such spam comments and posts to spoil the organizations reputation or product reputation. </p></div>';

    $comment_spam = '<div style="margin:1em;">
    	<ul>
    		<li>
    			<p class="modal-body-para"><b>1. <u>Comment protection:</u></b> Spam comments generated by automated scripts can be prevented using captcha. Honeypot is another widely used technique to catch bots and block them.</p>
    		</li>
    		<li>
    			<p class="modal-body-para"><b>2. <u>Post protection:</u></b> Automated scripts or botnets can prevented by using captcha. There are three forms of captcha availble text, math and google recaptcha. Honeypot is also another way to detect spam posts. Malware scanner can also used to scan the malware in the posts.</p>
    		</li>
    	</ul>
    </div>';


    $scanner = '<div class="modal-body-div"><p class="modal-body-para">Malware scanner detects the malicious code in the files. Compares the wordpress, plugins and theme files with Wordpress repo files. Detects changes present in any wordpress, theme and plugin files. It also checks outdated/vulnerable plugins, themes. It is also useful to detect malware in posts and comments.</p></div>
    <div style="margin:1em;">
    	<ul>
    		<li>
    			<p class="modal-body-para"><b>1. <u>Malware scan:</u></b> Malware scan scans the wordpress core files, plugins and theme files to check malware. It detects the trojans, backdoors, viruses, worms, etc. in the files. </p>
    		</li>
    		<li>
    			<p class="modal-body-para"><b>2. <u>Wordpress version and file compare:</u></b> Checks WordPress, plugins and themes version with Wordpress.org repository and compare WordPress core, plugins and themes files with the repository to detect any file changes. Detect any changes in the files present in the WordPress plugins and themes folder.</p>
    		</li>
    		<li>
    			<p class="modal-body-para"><b>3. <u>Check vulnerable plugins and themes:</u></b> Checks vulnerable plugins and themes which contains malicious code. You can remove it to enhance your site security.</p>
    		</li>
    	</ul>
    </div>';

    $support_modal = '<div>

        <div style="width: unset; float: unset; margin: 10px 20%;" class="mo_wpns_support_layout_model">
            
            <img src="'.dirname(plugin_dir_url(__FILE__)).'/includes/images/support3.png">
            <h1>Support</h1>
            <p>Need any help? We are available any time, Just send us a query so we can help you.</p>
                <form name="f" method="post" action="">
                    <input type="hidden" name="option" value="mo_wpns_send_query"/>
                    <table class="mo_wpns_settings_table">
                        <tr><td>
                            <input type="email" class="mo_wpns_table_textbox" id="query_email" name="query_email" value="'.$email.'" placeholder="Enter your email" required />
                            </td>
                        </tr>
                        <tr><td>
                            <input type="text" class="mo_wpns_table_textbox" name="query_phone" id="query_phone" value="'.$phone.'" placeholder="Enter your phone"/>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <textarea id="query" name="query" class="mo_wpns_settings_textarea" style="resize: vertical;width:100%" cols="52" rows="7" placeholder="Write your query here"></textarea>
                            </td>
                        </tr>
                    </table>
                    <input type="submit" name="send_query" id="send_query" value="Submit Query" class="mo_wpns_button  mo2f_submit_query" />
                </form>
                <br />          
        </div>
        </div>
        <script>
            function moSharingSizeValidate(e){
                var t=parseInt(e.value.trim());t>60?e.value=60:10>t&&(e.value=10)
            }
            function moSharingSpaceValidate(e){
                var t=parseInt(e.value.trim());t>50?e.value=50:0>t&&(e.value=0)
            }
            function moLoginSizeValidate(e){
                var t=parseInt(e.value.trim());t>60?e.value=60:20>t&&(e.value=20)
            }
            function moLoginSpaceValidate(e){
                var t=parseInt(e.value.trim());t>60?e.value=60:0>t&&(e.value=0)
            }
            function moLoginWidthValidate(e){
                var t=parseInt(e.value.trim());t>1000?e.value=1000:140>t&&(e.value=140)
            }
            function moLoginHeightValidate(e){
                var t=parseInt(e.value.trim());t>50?e.value=50:35>t&&(e.value=35)
            }
        </script>';


    $ecommerce_site = array(array('Web Application Firewall(WAF)', $waf_body_ecommerce.$waf_body.$waf_arr_ecc), array('Login Security', $login_security_ecommerce.$login_security_body.$registration_security_ecommerce.$registration_security), array('Media Protection', $media_body), array('Encrypted Backup', $backup_ecommerce.$backup_body.$backup_body_1.$backup_body_4), array('Comment and Spam Protection', $comment_ecommerce.$comment_spam), array('Malware Scanner', $scanner), array('Support', $support_modal));

    $business_site = array(array('Web Application Firewall(WAF)', $waf_body_business.$waf_body.$waf_arr_busi), array('Encrypted Backup', $backup_business.$backup_body.$backup_body_2.$backup_body_4), array('Comment and Spam Protection', $comment_business.$comment_spam), array('Login Security', $login_security_business.$login_security_body.$registration_security_business.$registration_security), array('Malware Scanner', $scanner), array('Support', $support_modal));

    $blog_site = array(array('Web Application Firewall(WAF)', $waf_body_blog.$waf_body.$waf_arr_blog), array('Comment and Spam Protection', $comment_blog.$comment_spam), array('Login Security', $login_security_blog.$login_security_body), array('Encrypted Backup', $backup_blog.$backup_body.$backup_body_3.$backup_body_4), array('Malware Scanner', $scanner), array('Support', $support_modal));

    $other_site = array(array('Web Application Firewall(WAF)', $waf_body_other.$waf_body.$waf_arr_other), array('Comment and Spam Protection', $comment_other.$comment_spam), array('Login Security', $login_security_other.$login_security_body), array('Encrypted Backup', $backup_blog.$backup_body.$backup_body_4), array('Media Protection', $media_body_1), array('Malware Scanner', $scanner), array('Support', $support_modal));

    $main_pointer = array('Main' => array('Let\'s get Started', $tour_body), 'Ecommerce' => $ecommerce_site, 'Business' => $business_site, 'Blogs/News' => $blog_site, 'Other' => $other_site);

    include $mo2f_dirName . 'views'.DIRECTORY_SEPARATOR.'tour-model.php';
