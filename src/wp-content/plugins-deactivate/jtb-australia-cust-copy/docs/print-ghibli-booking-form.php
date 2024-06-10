<div class="container"><?php 
$section = 0;
// id of form page 5122

$post_id2 = get_the_ID();
$counter=0; $post_id = get_the_ID(); wp_reset_query() ; the_post(5122); 
echo "rsetni2";
while (have_rows('sections', 5122)) : the_row(); 
echo "rsetni3";
$row_layout = get_row_layout();

if ($row_layout == "tourplan_multiple_product_single_book") {

	$minDateType = get_sub_field("min_date_type", 5122);
	$minDateVal = get_sub_field("min_date_" . $minDateType, 5122);
	$maxDateType = get_sub_field("max_date_type", 5122);
	$maxDateVal = get_sub_field("max_date_" . $maxDateType, 5122);

	$controlConf = array(
		"title" => get_sub_field("title", 5122),
		"supplierID" => get_sub_field("supplier_id", 5122),
		"service_button" => get_sub_field("service_button", 5122),
		"search_on_load" => get_sub_field("search_on_load", 5122),
		"min_date_type" => $minDateType,
		"min_date_val" => $minDateVal,
		"max_date_type" => $maxDateType,
		"max_date_val" => $maxDateVal
	);
	$productConfs = array();

	if (have_rows("products", 5122)) :
		while(have_rows("products", 5122)) : the_row();

			$product = array(
				"title" => get_sub_field("title", 5122),
				"productID" => get_sub_field("product_id", 5122),
				"qtyConfig" => get_sub_field("quantity_configuration", 5122)
			);

			if (have_rows("age_range_settings", 5122) && $product["qtyConfig"] == "paxbased") :

				$age_ranges = array();
				$paxType= "";

				while(have_rows("age_range_settings", 5122)) : the_row();
					// $age_ranges[get_row_layout()] = array(
					// 	"title" => get_sub_field("title")
					// );
					$paxType = get_row_layout(5122);
					$defaultQty = get_sub_field("default_qty", 5122);
				endwhile;

				// $product['age_ranges'] = $age_ranges;
				$product['paxtype'] = $paxType;
				$product['default_qty'] = $defaultQty;
			endif;

			array_push($productConfs, $product);
		endwhile;
	endif;

	$controlConf['productConfs'] = $productConfs;

	?>
	<div class="row section-<?php echo $section; ?> multi-product">
		<div class="col-xs-12">
			<div class="tourplan_plugin_section <?php echo get_sub_field('service_button', 5122); ?>">
				<div class="plugin_control"></div>
				<?php include('templates/' . get_sub_field('template_source', 5122)); ?>	
			</div>
		</div>
	</div>


	<script class="configs">
							
		$(window).load(function() {
			var pluginControl = $(".section-<?php echo $section; ?> .tourplan_plugin_section");
			_.forEach(pluginControl, function(pc) {
				new TourplanMultiProductController(
					pc,
					<?php echo json_encode($controlConf); ?>);
			});
		});

	</script>
<?php
}

endwhile;

 the_post($post_id2); 
?></div>
@@@