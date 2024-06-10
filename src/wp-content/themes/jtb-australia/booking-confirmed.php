<?php
/**
 * @package WordPress
 * @subpackage Default_Theme
 * Template Name: Booking Confirmed
 */

get_header(); ?>

	<section id="content" class="container">
		<div class="row">
			<div class="col-sm-12">
				<?php if ( function_exists('yoast_breadcrumb') ) {
				yoast_breadcrumb('<p id="breadcrumbs">','</p>');
				} ?>
			</div>
			<div class="col-sm-12">
				<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
				<div class="post">
					<h1><?php the_title(); ?></h1>
					<div class="ribon-red-desktop"></div>
					<div class="entry">
						<?php the_field('content'); ?>
					</div>
				</div>
				<?php endwhile; endif; ?>
			</div>
		</div>
	</section>

	<script type="text/javascript">
	bokking_id = '<?php echo do_shortcode("[tp-booking-ref]"); ?>';
	<?php
	$totalprice = intval($_SESSION["tpprice"]) /100;
	$revenue = number_format($totalprice,2,'.','');
	$tplist = json_decode($_SESSION['tpshoppingcart']);
	$pricing = 0;
	foreach (json_decode($_SESSION["list_products"]) as $value) {
		$pricing = intval($pricing+$value->price);
	}
	if($pricing == 0) {
		$shiping = 8;
	} else {
		$shiping = intval($totalprice-$pricing);
	}
	?>
	if (bokking_id == '(Errored)') {
	   window.location.replace("<?php echo bloginfo('siteurl');?>");
	} else {
		dataLayer.push({
			'event': 'purchase',
			'eventCategory': 'Ecommerce', 
			'eventAction': 'Purchase',
			'ecommerce': {
				'purchase': {
					'actionField': {
						'id': '<?php echo do_shortcode("[tp-booking-ref]"); ?>',
						'revenue': '<?php echo $revenue; ?>',
						'affilliation': 'JTB SG Store',
						'shipping': '<?php echo $shiping; ?>'
					},
					'products' : <?php echo $_SESSION["list_products"]; ?>
				}
			}
		});
		dataLayer.push({
			'event': 'checkout',
			'eventCategory': 'Ecommerce', 
			'eventAction': 'Checkout',
			'ecommerce': {
				'checkout': {
					'actionField': {'step': 4,'option':'Order Complete'},
					'products': <?php echo $_SESSION["list_products"]; ?>
				}
		  }
		});
	}
	</script>

<?php get_footer(); ?>
