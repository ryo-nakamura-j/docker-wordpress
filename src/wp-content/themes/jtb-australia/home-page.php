<?php
/**
 * @package WordPress
 * @subpackage Default_Theme
 * Template Name: Home page
 */

get_header(); ?>
	<?php 
	require_once("components/tp-main-slider-def.php");
	require_once("components/tp-caption-def.php");
	require_once("components/tp-grid-container-def.php");
	?>
	<?php /*include("components/tp-main-slider-template-mobile.php");*/ ?>
	<?php 
	$ui = new TpMainSlider();
	$ui->id = "home-slider";
	$ui->class = "home-content";
	$ui->slideList = array();
	while ( have_rows('slides') ) {
		the_row();
		$slide = new TpMainSlide();
		$slide->link = get_sub_field("link");
		$slide->slide = get_sub_field("slide");
		$slide->slide_mobile = get_sub_field("slide_mobile");
		array_push( $ui->slideList, $slide );
	} 
	$ui->calculatePremotion( array( 'national-rail-pass', 'japan-rail-pass') );
	$ui->init();
	?>

	<?php 
	$ui = new TpCaption();
	$ui->id = "tp-home-page-caption-1";
	$ui->class = "home-content";
	$ui->content = get_field( "slideshow_caption" );
	$ui->init();
	?>

	<?php 
	$ui = new TpGridContainer();
	$ui->title = "Special"; // Label
	$ui->class = "home-content four grid";
	$ui->classRow = "col-sm-3";
	$ui->boxList = array();
	while ( have_rows('four_grid_boxes') ) {
		the_row();
		$box = new TpGridBox();
		$box->box_title = get_sub_field("box_title");
		$box->box_link = get_sub_field("box_link");
		$box->box_image = get_sub_field("box_image");
		array_push( $ui->boxList, $box );
	} 
	$ui->init();
	?>

	<?php
		$sabreSearchConfigs = get_field("sabre_search");
		if ( $sabreSearchConfigs["enabled"] ) {
			include "partials/sabre_search.php";
			$sabreModule = new SabreSearchModule($sabreSearchConfigs);
			// $sabreModule->dumpConfigs();
			$sabreModule->renderControl();
		}
	?>

	<?php 
	$ui = new TpGridContainer();
	$ui->title = "Japan Information"; // Label
	$ui->class = "home-content three grid";
	$ui->classRow = "col-sm-4";
	$ui->boxList = array();
	while ( have_rows('three_grid_boxes') ) {
		the_row();
		$box = new TpGridBox();
		$box->box_title = get_sub_field("box_title");
		$box->box_link = get_sub_field("box_link");
		$box->box_image = get_sub_field("box_image");
		$box->box_copy = get_sub_field("box_copy");
		array_push( $ui->boxList, $box );
	} 
	$ui->init();
	?>

	<?php 
	$ui = new TpGridContainer();
	$ui->title = "Featured Products"; // Label
	$ui->class = "home-content multi grid";
	$ui->classRow = "col-sm-4";
	$ui->showFooter = false;
	$ui->style = $ui->STYLE_FLYING_TEXT;
	$ui->boxList = array();
	while ( have_rows('multi_grid_boxes_row_1') ) {
		the_row();
		$box = new TpGridBox();
		$box->box_title = get_sub_field("box_title");
		$box->box_link = get_sub_field("box_link");
		$box->box_image = get_sub_field("box_image");
		array_push( $ui->boxList, $box );
	} 
	$ui->init();
	?>

	<?php 
	$ui = new TpGridContainer();
	$ui->class = "home-content multi grid";
	$ui->classRow = "col-sm-3";
	$ui->showFooter = false;
	$ui->showHeader = false;
	$ui->style = $ui->STYLE_FLYING_TEXT;
	$ui->boxList = array();
	while ( have_rows('multi_grid_boxes_row_2') ) {
		the_row();
		$box = new TpGridBox();
		$box->box_title = get_sub_field("box_title");
		$box->box_link = get_sub_field("box_link");
		$box->box_image = get_sub_field("box_image");
		array_push( $ui->boxList, $box );
	} 
	$ui->init();
	?>

	<?php 
	$ui = new TpGridContainer();
	$ui->class = "home-content multi grid";
	$ui->classRow = "col-sm-2";
	$ui->showFooter = false;
	$ui->showHeader = false;
	$ui->style = $ui->STYLE_FLYING_TEXT;
	$ui->boxList = array();
	while ( have_rows('multi_grid_boxes_row_3') ) {
		the_row();
		$box = new TpGridBox();
		$box->box_title = get_sub_field("box_title");
		$box->box_link = get_sub_field("box_link");
		$box->box_image = get_sub_field("box_image");
		array_push( $ui->boxList, $box );
	} 
	$ui->init();
	?>

	<?php 
	$ui = new TpGridContainer();
	$ui->class = "home-content multi grid";
	$ui->classRow = "col-sm-3";
	$ui->style = $ui->STYLE_IMAGE_RESPONSIVE;
	$ui->boxList = array();
	while ( have_rows('advertising_banner') ) {
		the_row();
		$box = new TpGridBox();
		$box->box_link = get_sub_field("link");
		$box->box_image = get_sub_field("image");
		array_push( $ui->boxList, $box );
	} 
	$ui->init();
	?>

<?php get_footer(); ?>
