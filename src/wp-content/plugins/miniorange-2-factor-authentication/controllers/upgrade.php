<?php
      	include $mo2f_dirName . 'views'.DIRECTORY_SEPARATOR.'upgrade.php';
		MoWpnsUtility::checkSecurity();
		update_site_option("mo_2fa_pnp",time());
		update_site_option("mo2fa_visit",intval(get_site_option("mo2fa_visit",0))+1);