<?php
/**
 * @package WordPress
 * @subpackage Default_Theme
 * Template Name: Test Template
 */

get_header(); ?>

<div class="container">

<?php

if (have_rows('control_type')) {
	while (have_rows('control_type')) : the_row();

		$srb = get_sub_field('service_button');
		$supplierid = get_sub_field('supplier_id');

		if (get_row_layout() == 'static_non-accommodation_product') {

			if (have_rows('product_group')) {

				while (have_rows('product_group')) : the_row();
					if (have_rows('products')) { ?>
						<div class="tourplan_plugin_section">

							<div class="plugin_control"></div>

							<?php
							
							$searchConf = array(
								'enableSearch' => get_field('enable_search') ? true : false,
								'srb' => get_field('service_button'),
								'supplierid' => get_field('supplier_id')
							);

							$productList = array();
							while (have_rows('products')) : the_row();
								$productArr = array(
									'productid' => get_sub_field('product_id'),
									'productname' => get_sub_field('product_name')
								);
								if (have_rows('configuration')) {
									while (have_rows('configuration')) : the_row();
									$productArr[get_sub_field('product_config_setting')] = get_sub_field('value');
									endwhile;
								}
								array_push($productList, $productArr);
							endwhile;

							include('templates/' . get_field('template_source'));
							?>
						</div>

						<script class="configs">
							var searchConfigs = <?php echo json_encode($searchConf); ?>;
							var productConfigs = <?php echo json_encode($productList); ?>;
						</script>
						<?php
					}
				endwhile;
			}
		}

		else if (get_row_layout() == 'generic_non-accommodation_product') { ?>
			<div class="tourplan_plugin_section">
				<div class="plugin_control"></div>
				
				<?php include('templates/' . get_field('template_source')); ?>

				<script class="configs">
					var searchConfigs = <?php echo json_encode(
						array_merge(
							$_GET, 
							array(
								'enableSearch' => get_field('enable_search') ? true : false, 
								'srb' => get_field('service_button')
							)
						)
					); ?>;
					var productConfigs = <?php echo json_encode(array($_GET)); ?>;
				</script>

			</div>
		<?php
		}

	endwhile;

}

?>



</div>

<script>
	$(window).load(function() {
		var pluginControl = $(".tourplan_plugin_section");
		_.forEach(pluginControl, function(pc) {
			new TourplanNonAccomProductControlGroup(pc);
		})
	});
</script>

<?php get_footer(); ?>