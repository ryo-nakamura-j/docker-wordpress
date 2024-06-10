
<?php

$head_img="https://www.nx.jtbtravel.com.au/wp-content/uploads/2018/01/rugby-world-cup-banner.jpg";

if(is_page(24514)){$head_img="https://www.nx.jtbtravel.com.au/wp-content/uploads/2018/01/sydney-rugby-south-africa.jpg";}
else if(is_page(21900)){$head_img="https://www.nx.jtbtravel.com.au/wp-content/uploads/2018/01/sydney-rugby-whales.jpg";}
else if(is_page(20757)){$head_img="https://www.nx.jtbtravel.com.au/wp-content/uploads/2018/01/rugby-world-cup-japan-jtb.jpg";}
else if(is_page(24069)){$head_img="https://www.nx.jtbtravel.com.au/wp-content/uploads/2018/01/sydney-rugby-japan-jtb.jpg";}
else if(is_page(24038)){$head_img="https://www.nx.jtbtravel.com.au/wp-content/uploads/2018/01/sydney-rugby-flags.jpg";}

if(is_page(24349) || is_page(24514)){
	$logo_img = "https://www.nx.jtbtravel.com.au/wp-content/uploads/2018/01/rugby-world-cup-logo3.png";
}else{
	$logo_img = "https://www.nx.jtbtravel.com.au/wp-content/uploads/2017/12/rugby-world-cup-logo.png";
}

?>


<div class="rwc_header"><img src="<?php echo $logo_img; ?>"   /><img src="<?php echo $head_img; ?>" class="mobile_only"  /><div class="copyright"><?php
if(is_page(24349)){
	echo 'The Webb Ellis Cup is protected by TM © Rugby World Cup Limited 1986. All rights reserved.';
}else{
	echo 'The Rugby World Cup 2019 logo TM © Rugby World Cup Limited 2015. All rights reserved.';
}
?></div></div>

<img src="https://www.nx.jtbtravel.com.au/wp-content/uploads/2019/06/rugby-world-cup-banner-mob.jpg" class="rwc_header_mobile" />
