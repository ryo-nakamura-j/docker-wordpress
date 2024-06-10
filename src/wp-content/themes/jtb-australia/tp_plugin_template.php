<?php
/**
 * @package WordPress
 * @subpackage Default_Theme
 *
 * Template Name: Tourplan Plugin Template
 */
$useNewnHeaderFooter = get_field("use_footer_header_redesign") == "new";
if ( $useNewnHeaderFooter )
	get_header("redesign");
else
	get_header(); 
?>
	<div class="container tp-breadcrumb-container">
		<?php include("components/tp-breadcrumb-template.php"); ?>
	</div>

	<?php
	$section = 0;

	$helper = new TpAcfHelper();

	// Initialize data list & helper for Vue
	include("components/tp-plugin-data.php");

	$alternativeTemplateSource = get_field("template_source");

	if ( $alternativeTemplateSource != "None" && isset( $alternativeTemplateSource ) ) {
		require_once("components/tp-customized-layout-def.php");
		$ui = new TpCustomizedLayout( );
		$ui->template_source = $alternativeTemplateSource;
		$ui->init( $dataList );
	}
	else {
		while (have_rows('sections')) : the_row(); 

			$row_layout = get_row_layout();

			if ($row_layout == "divider") { ?>
			<div class="container">
				<div class="row">
					<div class="col-xs-12">
						<?php
						if (get_sub_field('heading')) { ?>
							<h3 class="red-heading"><?php the_sub_field("heading"); ?></h3>
						<?php
						}
						?>
						<div class="ribbon-red-desktop"></div>
					</div>
				</div>
			</div>
			<?php
			}

			else if ($row_layout == "wysiwyg_content") { ?>
			<div class="container">
				<div class="row">
					<div class="col-xs-12">
						<?php the_sub_field('content'); ?>
					</div>
				</div>
			</div>
			<?php
			}

			else if ($row_layout == "toggle_section") { ?>
			<div class="container">
				<div class="row">
					<div class="col-xs-12">
						<!-- Terms and Conditions -->
						<div class="toggle_section">
							<a class="toggle_section_toggle collapsed" data-toggle="collapse" href="#toggle_section_<?php echo $section; ?>">
								<h4><?php the_sub_field('label'); ?></h4>
							</a>
							<div id="toggle_section_<?php echo $section; ?>" class="toggle_section collapse"><?php the_sub_field('content'); ?></div>
						</div>

					</div>
				</div>
			</div>
			<?php
			}

			else if ($row_layout == "single_image") { 
				$image = get_sub_field('image');
				?>
				<div class="container">
					<div class="row section-<?php echo $section; ?>">

						<div class="col-xs-12">
							<div class="ribbon-red-desktop attached"></div>
							<?php image_if_exists($image, "img-responsive center-block fullwidth"); ?>
						</div>
					</div>
				</div>
			<?php 
			} 

			else if ($row_layout == "banner_logo_text") { 
			?> <div class="container"> <?php
				include get_template_directory() . '/partials/banner_logo_text.php';
			?> </div> <?php 
			}

			else if ($row_layout == "tourplan_two_product_rail_pass" ||
				$row_layout == "tourplan_two_supplier_rail_pass" ) { 
				?> <div class="container"> <?php
				require_once("components/tp-supplier-product-def.php");
				$ui = new TpSupplierProductDef();
				$ui->sectionId = $section;
				$ui->loadStandardSearchConfig();
				$ui->init( $dataList );
				?> </div> <?php 
			}

			else if ($row_layout == "tourplan_itinerary") { 
			?>
			<div class="container">
				<div class="row section-<?php echo $section; ?>">
				<?php 
					require_once("components/tp-itinerary-def.php");
					$ui = new TpItinerary();
					$ui->sectionId = $section;
					$ui->template_source = get_sub_field("template_source");
					$ui->loadStandardSearchConfig();
					$ui->init( $dataList );
				?>
				</div>
			</div>
			<?php
			}

			else if ($row_layout == "tourplan_product_search") {
			?>
			<div class="section-<?php echo $section; ?>">
			<?php
				require_once("components/tp-product-search-def.php");
				$ui = new TpProductSearch();
				$ui->sectionId = $section;
				$ui->template_source = get_sub_field("template_source");
				$ui->loadStandardSearchConfig();
				$ui->init( $dataList );
			?>
			</div>
			<?php
			}

			else if ($row_layout == "tourplan_product_page") {
			?>
			<div class="section-<?php echo $section; ?>">
			<?php
				require_once("components/tp-accom-product-detail-def.php");
				$ui = new TpAccomProductDetail();
				$ui->sectionId = $section;
				$ui->template_source = get_sub_field("template_source");
				$ui->loadStandardSearchConfig();
				$ui->init( $dataList );
				?>
			</div>
			<?php
			}

			else if ($row_layout == "tourplan_jtb_tour_page") {
			?>
			<div class="section-<?php echo $section; ?>">
			<?php
				require_once("components/tp-tour-product-detail-def.php");
				$ui = new TpTourProductDetail();
				$ui->sectionId = $section;
				$ui->template_source = get_sub_field("template_source");
				$ui->loadStandardSearchConfig();
				$ui->init( $dataList );
				?>
			</div>
			<?php
			}

			else if ($row_layout == "tourplan_checkout_page") { 
			?>
			<div class="container">
				<div class="row section-<?php echo $section; ?>">
				<?php 
					require_once("components/tp-checkout-def.php");
					$ui = new TpCheckout();
					$ui->sectionId = $section;
					$ui->template_source = get_sub_field("template_source");
					$ui->loadStandardSearchConfig();
					$ui->init( $dataList );
				?>
				</div>
			</div>
			<?php
			}

			else if ($row_layout == "popular_tours_section") {
				?>
			<div class="container">
				<div class="row">
					<div class="col-xs-12">
						<h3>Popular Tours</h3>
						<div class="ribon-red-desktop"></div>
					</div>
				</div>
				<div class="row">
					<?php
$counterjtb = 0;
					if (have_rows('popular_tours')) :
						while (have_rows('popular_tours')) : the_row();
$counterjtb += 1;
if($counterjtb %4 ==1){
	$classjtb = " clearleft ";
}else{$classjtb = "";}
	$imgtemp=get_sub_field('image');
	$imgjtb2 = $imgtemp['url']; 
if(get_sub_field('imagetxt')){
	$imgjtb2 = get_sub_field('imagetxt');
}


						?>

						<div class="col-xs-14 col-sm-3<?php echo $classjtb ; ?>">
							<div class="thumbnail">
								<div class="caption">
									<a href="<?php echo the_sub_field('link'); ?>">
										<h4><?php echo the_sub_field('title'); ?></h4>
									</a>
								</div>
								<a href="<?php echo the_sub_field('link'); ?>">
									<img src="<?php echo  $imgjtb2; ?>" />
								</a>
								<div class="caption">
									<div class="row">
										<div class="col-sm-12 col-md-12">
											<p><?php the_sub_field('caption'); ?></p>
										</div>
									</div>
								</div>
							</div>
						</div>

						<?php
						endwhile;
					endif;
					?>
				</div>
			</div>
			<?php
			}

			else if ($row_layout == "featured_and_popular_section") { 
				?>
			<div class="container">
				<div class="row"> 
					<div class="col-sm-12">
						<h3><?php the_sub_field('section_title'); ?></h3>
						<div class="ribon-red-desktop"></div>
					</div>
					<div class="col-sm-6">
						<div class="row">
							<?php
							// check if the repeater field has rows of data
							if( have_rows('featured_items') ):
							 
							 	// loop through the rows of data
							    while ( have_rows('featured_items') ) : the_row();
							 
							        // display a sub field value
									?>

									  <div class="col-sm-6">
									  	<div class="featured ryokans">
											<div class="thumbnail">
												<div class="title"><a href="<?php the_sub_field('link') ?>">
													<h4><?php the_sub_field('title') ?></h4></a>
												</div>
										  		<a href="<?php the_sub_field('link') ?>"><img src="<?php the_sub_field('image') ?>" alt="..."></a>
												<div class="caption">
													<div class="row">
														<div class="col-sm-12 col-md-12">
														<?php the_sub_field('caption') ?>
														</div>
													</div>
												  </div>
											</div>
										</div>
									</div>
									<?php ;
							    endwhile;
							else :
							    // no rows found
							endif; ?>
						</div>
					</div>
					<div class="col-sm-6">
						<div class="popular-destinations ryokan">
							<h4>Choose from popular destinations!!</h4>
							<div class="row">
							<?php
							// check if the repeater field has rows of data
							if( have_rows('popular_destinations') ):
							 
							 	// loop through the rows of data
							    while ( have_rows('popular_destinations') ) : the_row();
							 
							        // display a sub field value
									?>

								  	<div class="col-sm-6 multi">
								  		<a href="<?php echo tp_search_url() . '/' . get_sub_field('url_parameters'); ?>"><img src="<?php the_sub_field('image') ?>" class="img-responsive" alt=""></a>
										<p class="flying-text"><?php the_sub_field('title') ?></p>
									</div>
									<?php ;
							    endwhile;
							else :
							    // no rows found
							endif; ?>
							</div>
						</div>
					</div>
				</div>
			</div>
			<?php
			}

			else if ($row_layout == "tourplan_multiple_product_single_book") {

				$minDateType = get_sub_field("min_date_type");
				$minDateVal = get_sub_field("min_date_" . $minDateType);
				$maxDateType = get_sub_field("max_date_type");
				$maxDateVal = get_sub_field("max_date_" . $maxDateType);

				$controlConf = array(
					"title" => get_sub_field("title"),
					"section_heading" => get_sub_field("section_heading"),
					"supplierID" => get_sub_field("supplier_id"),
					"service_button" => get_sub_field("service_button"),
					"search_on_load" => get_sub_field("search_on_load"),
					"min_date_type" => $minDateType,
					"min_date_val" => $minDateVal,
					"max_date_type" => $maxDateType,
					"max_date_val" => $maxDateVal
				);
				$productConfs = array();

				if (have_rows("products")) :
					while(have_rows("products")) : the_row();

						$product = array(
							"title" => get_sub_field("title"),
							"name" => get_sub_field("title"),
							"productID" => get_sub_field("product_id"),
							"qtyConfig" => get_sub_field("quantity_configuration"),
							"list" => get_sub_field("service_button"),
							"Category" => get_sub_field("service_button"),
						);

						if (have_rows("age_range_settings") && $product["qtyConfig"] == "paxbased") :

							$age_ranges = array();
							$paxType= "";

							while(have_rows("age_range_settings")) : the_row();
								// $age_ranges[get_row_layout()] = array(
								// 	"title" => get_sub_field("title")
								// );
								$paxType = get_row_layout();
								$defaultQty = get_sub_field("default_qty");
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
			<div class="container">
				<div class="row section-<?php echo $section; ?> multi-product">
					<div class="col-xs-12">
						<div class="tourplan_plugin_section <?php echo get_sub_field('service_button'); ?>">
							<div class="plugin_control"></div>
							<?php include('templates' . DIRECTORY_SEPARATOR . get_sub_field('template_source')); ?>	
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
				
				<script type="text/javascript">
					dataLayer.push({
							  'ecommerce': {
							  	'event':'gtm.dom',
							  	'currencyCode': 'SGD',
							  	'impressions': <?php echo json_encode($productConfs); ?>
							  }
							});
				</script>
			</div>
			<?php
			}

			$section +=1 ;
		endwhile;	
	}

	?>

<?php 
$useRedesignHeaderFooter = get_field("use_footer_header_redesign") != "old";
if ( $useRedesignHeaderFooter )
	get_footer("redesign");
else
	get_footer(); 
?>
