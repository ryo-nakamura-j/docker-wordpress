<?php
// for rewrite URL
error_reporting(0);
function getArrUrl($var)
{
	$nvar = Array();
	$na = explode("/", $var);
	for($i=0; $i<count($na)-1;$i+=4)
	{
		$nvar["$na[$i]"] = $na[$i+1];
	}
	return $nvar;
}
//$args = getArrUrl($_GET['args']);
if (isset($_GET['args']))
$args = getArrUrl($_GET['args']);

/*how to use
URL: http://abc.com/sp/blog/title-of-single
$title = $_GET['args'];
 
or http://abc.com/sp/blog/page/2
$paged = $args['page'];

*/

//end for rewrite URL

function cutString($str,$len, $moreStr = "...")
{		
	$mystr = "";
	$str = strip_tags($str);
	$str = preg_replace('/\r\n|\n|\r/','',$str);
	if(mb_strlen($str) > $len)
	{
		$newstr = mb_substr($str,0,$len);			
		$mystr = $newstr.$moreStr;
		
	}
	else $mystr = $str;
	
	return $mystr;			
}


//get image from content
function get_first_image($cnt, $noimg = true){
	$first_img = '';
	$output = preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $cnt, $matches);
	for($i=0;$i<=10;$i++){
        $first_img = $matches[1][$i];
        $ext = substr( $first_img, -3);
        if($ext == 'jpg' or $ext == 'png'){
            return $first_img;
            break;
        }
    }
	 if(empty($first_img) || $first_img == "") {
		if($noimg) $first_img = APP_ASSETS . "img/common/other/img_nophoto.jpg";
		else return false;
	 }  
	 return $first_img;
}

//get image from content 
function catch_that_image($noimg = true) {
  global $post, $posts;
  $first_img = '';
  ob_start();
  ob_end_clean();
  $output = preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $post->post_content, $matches);
  $first_img = $matches[1][0];

  if(empty($first_img) || $first_img == "") {
    if($noimg) $first_img = APP_ASSETS . "img/common/other/img_nophoto.jpg";
	 else return false;
  }  
  return $first_img;
  
}

function curPageURL() {
  $pageURL = 'http';
  if (isset($_SERVER["HTTPS"])) {
  	if ($_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
  }
  $pageURL .= "://";
  if ($_SERVER["SERVER_PORT"] != "80") {
   $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
  } else {
   $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
  }
  return $pageURL;
}
$current_url = curPageURL();

?>