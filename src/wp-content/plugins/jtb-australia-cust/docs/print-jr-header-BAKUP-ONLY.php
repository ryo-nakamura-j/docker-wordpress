<?php

/* 
this is replacing a file in the template - edited tp_plugin_template 

do_action('jr_links');
$section = 0;
while (have_rows('sections')) : the_row(); 

else if ($row_layout == "tourplan_two_product_rail_pass") { 
	//include get_template_directory() . '/partials/rail_pass.php';
	do_action('print_jr_header');
}

JR PARTIAL 

*/



global $jrheader;
global $jrheadercount;
$jrheadercount += 1;

global $post_id2;
$post_id2->id = $wp_query->post->ID;

$upper_map = get_sub_field('upper_map');
$lower_map = get_sub_field('lower_map');
$side_image = get_sub_field('side_image');
$group_colour = get_sub_field('group_colour');

$searchConf = array(
		'srb' => get_sub_field('service_button'),
		'supplierid' => get_sub_field('supplier_id')
	);
$productConfs = array();
$numProducts = count(get_sub_field('products'));
?>

<div id ="jranchor<?php echo $jrheadercount; ?>" class="row section-<?php echo $section; ?> rail-product">
<div class="col-xs-12"><br /></div>

<div class="col-xs-12 col-sm-7 col-md-8">
	<div class="row">
		<div class="col-xs-12 <?php echo ($numProducts <= 2 ? 'col-md-8' : 'col-md-12'); ?>">
			<!-- Plugin section -->
			<h2 class="rail_pass_heading" style="background:<?php echo $group_colour ?>"><i class="fa fa-train"></i> <?php the_sub_field('group_title'); ?></h2>
			<div class="row">
				<div class="rail_pass_inner clearfix" style="background:<?php the_sub_field('group_colour'); ?>">
					<div class="tourplan_plugin_section">
					<?php

					$jrheader .= '<div class="col-xs-12 col-md-4 col-lg-4"><a style=" background: '.$group_colour.'; " href="#jranchor'.$jrheadercount.'">'.get_sub_field('group_title').'</a></div>';


					if (have_rows('products')) {
						while (have_rows('products')) : the_row(); 
							$product = array(
								'productid' => get_sub_field('product_id'),
								'configs' => array()
								);

							if (have_rows('rail_configuration')) {
								while (have_rows('rail_configuration')) : the_row();
									$product['configs'][get_sub_field('config_setting')] = get_sub_field('config_value');
								endwhile;
							}
							array_push($productConfs, $product);

							$bsClasses = "col-xs-12 ";
							if ($numProducts == 1) {
								$bsClasses .= "col-md-offset-3 col-md-6";
							} else {
								$bsClasses .= "col-md-" . (12/$numProducts);
							}

							?>

							<div class="col-xs-12 <?php echo $bsClasses; ?>">
								<div class="row">
									<div class="col-xs-12 rail-product-<?php the_sub_field('product_id'); ?>">
										<h3 class="rail_pass_title"><?php echo get_sub_field('product_title'); ?></h3>
										<div class="plugin_control"></div>
									</div>
								</div>
							</div>

						<?php
						endwhile;
					}
					?>

					<?php include(get_template_directory() . '/templates/' . get_sub_field('template_source')); ?>
					<script class="configs">
						
						$(window).load(function() {
							var pluginControl = $(".section-<?php echo $section; ?> .tourplan_plugin_section");
							_.forEach(pluginControl, function(pc) {
								new TourplanRailPassControlGroup(
									pc, 
									<?php echo json_encode($searchConf); ?>, 
									<?php echo json_encode($productConfs); ?>);
							});
						});

					</script>
					</div>
				</div>
			</div>
		</div>
		<?php if ($numProducts <= 2) { ?>
		<div class="col-xs-12 col-md-4">
			<!-- Image Section -->
			<?php image_if_exists($side_image, "img-responsive fullwidth"); ?>
		</div>
		<?php } ?>
	</div>
	<div class="row">
		<div class="col-xs-12">
			<!-- Description Section -->
			<div class="description">
				<?php the_sub_field('description'); ?>
			</div>
		</div>
	</div>
</div>

<div class="col-xs-12 col-sm-5 col-md-4">
	<div class="row">
		<div class="col-xs-12">
			<!-- Upper Map -->
			<div class="upper-map">
				<?php image_if_exists($upper_map, "img-responsive fullwidth"); ?>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-xs-12">
			<!-- Lower Map -->
			<div class="lower-map">
				<?php image_if_exists($lower_map, "img-responsive fullwidth"); ?>
			</div>
		</div>
	</div>
</div>
<div class="col-xs-12">
	<!-- Terms and Conditions -->
	<div class="terms_and_conditions_section">
		<a class="toggle_section_toggle collapsed" data-toggle="collapse" href="#terms_and_conditions_<?php echo $section; ?>">
		<h4>
			Terms & Conditions
		</h4>
		</a>
		<div id="terms_and_conditions_<?php echo $section; ?>" class=" terms_and_conditions collapse">
			<?php 
			// the_sub_field('terms_and_conditions'); 

			if (have_rows('terms_and_conditions')) {

				while (have_rows('terms_and_conditions')) : the_row(); ?>

				<h4 style="background:<?php echo $group_colour; ?>"><?php the_sub_field('header'); ?></h4>
				<?php the_sub_field('content'); ?>

				<?php
				endwhile;

			}

			?>
		</div>
	</div>

</div>
</div>