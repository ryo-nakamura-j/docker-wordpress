
<?php
	// set viewport by user agent.
	require_once 'ua.class.php';
	$ua = new UserAgent();
	if($ua->set() === 'tablet') :
		// set width when you use the tablet
		$width = '1024px';
?>
<meta content="width=<?php echo $width; ?>" name="viewport">
<?php else: ?>
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0">
<?php endif; ?>
<meta name="site-title" value="<?php echo bloginfo('name'); ?>">

<?php 
include(APP_PATH.'libs/function.php');
include(APP_PATH.'libs/argument.php');  
?>

<!--font-->
<link href="https://fonts.googleapis.com/css?family=Signika:400,600,700" rel="stylesheet">

<!--css-->
<link href="<?php echo APP_ASSETS; ?>css/style.css" rel="stylesheet">
<link href="<?php echo APP_ASSETS; ?>css/custom.css" rel="stylesheet">
<!--/css-->


<!-- Favicons ==================================================-->
<link rel="icon" href="<?php echo APP_ASSETS; ?>img/common/icon/favicon.ico" type="image/vnd.microsoft.icon">

<!--[if lt IE 9]>
<script src="//cdn.jsdelivr.net/html5shiv/3.7.2/html5shiv.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/respond.js/1.4.2/respond.min.js"></script>
<![endif]-->

