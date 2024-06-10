<?php

/**

 * @package WordPress

 * @subpackage Default_Theme

 */

?>

<!DOCTYPE html>

<html <?php language_attributes(); ?>>

	<head><link href='https://fonts.googleapis.com/css?family=Roboto:400,700,500,300,900|Signika:400,600,700|Droid+Sans:400,700|Material+Icons' rel='stylesheet' type='text/css'>

		<meta charset="utf-8">

		<meta name="format-detection" content="telephone=no">

		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">



		<?php

			// Author: A+LIVE

			include_once('components/header_footer/config.php');

			include(APP_PATH.'libs/head.php');

		?>



		<title><?php wp_title(''); ?></title>

		<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />

		<link rel="shortcut icon" type="image/x-icon" href="<?php bloginfo('template_directory'); ?>/images/favicon.ico">

  		<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/font-awesome.min.css">

		<!-- Custom Stylesheet -->

		<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/bootstrap.min.css">

		<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/vue2-collapse.css">

		<link rel="stylesheet" href="<?php bloginfo('template_directory');?>/css/bootstrap-datepicker3.min.css" type="text/css"/>

		<link rel="stylesheet" href="<?php bloginfo('template_directory');?>/css/bootstrap-multiselect.css" type="text/css"/>

		<link rel="stylesheet" href="<?php bloginfo('template_directory');?>/css/pikaday.css" type="text/css"/>

		<link rel="stylesheet" href="<?php bloginfo('template_directory');?>/css/jquery.loadmask.css" type="text/css"/>

		<link rel="stylesheet" href="<?php bloginfo('template_directory');?>/css/jquery-ui.min.css" type="text/css"/>

		<link rel="stylesheet" href="<?php bloginfo('template_directory');?>/css/bootstrap-datetimepicker.min.css" type="text/css"/>



		<!-- Google Analytics -->

		<?php echo get_option('tp_google_analytics'); ?>

		<!-- PTEngine Tracking -->

		<?php echo get_option('tp_ptengine_tracking'); ?>



		<?php wp_head(); ?>

    </head>



    <body <?php body_class(); ?> id="top" class='top'>

		<?php echo get_option('tp_body_start'); ?>

		<?php do_action('print_after_body'); ?>

        <!-- Header

    ================================================== -->

        <?php include(APP_PATH.'libs/header.php'); ?>