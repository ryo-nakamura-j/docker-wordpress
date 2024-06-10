<?php
$pagename = str_replace(array('/', '.php'), '', parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
$pagename = $pagename ? $pagename : 'default';

switch ($pagename) {
	case '':
		if(!isset($titlepage)) $titlepage = 'About us title';
		if(!isset($desPage)) $desPage = '';
		if(!isset($keyPage)) $keyPage = '';
		if(!isset($txtH1)) $txtH1 = 'H1 content for about us';
	break;
	default:
		if(!isset($titlepage)) $titlepage = 'Default';
		if(!isset($desPage)) $desPage = '';
		if(!isset($keyPage)) $keyPage = '';
		if(!isset($txtH1)) $txtH1 = 'H1 Default';
}


?>
