<?php
/**
 * @package WordPress
 * @subpackage Default_Theme
 * Template Name: Ryokan & Hotels - Search
 */

get_header(); ?>

	<div class="container">
		<div class="row">
			<div class="col-sm-12">
				<?php if ( function_exists('yoast_breadcrumb') ) {
				yoast_breadcrumb('<p id="breadcrumbs">','</p>');
				} ?>
			</div>
		</div>

		<div class="ryokan-hotel search row">
			<div class="col-sm-12">
				<div id="searchparamssection"></div>
				<div id="sortresultssection"></div>
				<div id="searchresultssection"></div>
			</div>
		</div>
	
		<div class="row">
	 		<div class="page-top col-sm-12">
				<p><img src="<?php bloginfo('template_directory'); ?>/images/top-btn.png" alt="gotop">&nbsp;<a href="#top" style="text-decoration: underline;">Page Top</a></p>
				<div class="ribon-red-desktop"></div>
			</div>
		</div>
	</div>

<script>

$( init );

function init() {

	$('.destinationsSection').remove();
  // $('.basicSearchPanel').append( $('.basicSearchPanel > .destinationsSection') );
}

</script>

<?php get_footer(); ?>
