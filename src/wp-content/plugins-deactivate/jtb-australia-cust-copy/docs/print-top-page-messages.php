<?php if (!empty($_GET['newsletter']) && $_GET['newsletter'] == 'emailadded' ){ 
echo '<div class="home-content container top-mar-25"><p class="header-message green-message">Thank you, you will be added to the email newsletter.</p></div>'; 
}   
if (!empty($_GET['tpdisabled']) && $_GET['tpdisabled'] == 'yes' ){ 
echo '<div class="home-content container top-mar-25"><p class="header-message yellow-message">You were redirected here from a website booking page - our site is not currently taking bookings temporarily - the functionality will return shortly - feel free to email thorugh booking enquiries.</p></div>'; 
}  
  
if(($tpCloseTemp != true)&&($todaysDate<$WarningMessageExpireDate)){ 
  echo $tempWarningMessage; 
}   
if ($tpCloseTemp){  
echo '<div class="home-content container top-mar-25"><p class="header-message red-message">Our site booking and payment system is currently unavailable - the functionality will return shortly - feel free to email thorugh booking enquiries.</p></div>'; 
}  

/*
if(is_front_page()){
    echo '<div class="home-content container top-mar-25"><p class="header-message red-message">Due to fluctuations in the exchange rate, quotes requested may differ from the listed price.</p></div>';
}*/
//is_home ()




/*

if(   $post->post_parent == 3338    ){
    echo '<div class="home-content container top-mar-25"><p class="header-message yellow-message">By using this page you agree with the <a href="https://www.nx.jtbtravel.com.au/japan-rail-pass/agreement/">JR Pass Agreement</a></p></div>';
}

*/


?>

