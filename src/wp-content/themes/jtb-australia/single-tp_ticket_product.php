<?php
/**
 * @package WordPress
 * @subpackage Default_Theme
 */

get_header(); ?>

<div class="container">

	<?php if (function_exists('yoast_breadcrumb')) { ?>
	<div class="row">
		<div class="col-xs-12">
			<?php yoast_breadcrumb('<p id="breadcrumbs">', '</p>'); ?>
		</div>
	</div>
	<?php
	}

	while (have_rows('sections')) : the_row(); ?>
	<div class="row">
		<div class="col-xs-12"><div class="ribbon-red-desktop"></div></div>

		<?php 
		$row_layout = get_row_layout();

		if ($row_layout == "single_image") { 
			$image = get_sub_field('image'); ?>

		<div class="col-xs-12">
			<img src="<?php echo $image['url']; ?>" class="img-responsive center-block fullwidth" />
		</div>

		<?php 
		} 
		else if ($row_layout == "banner_logo_text") { 
			$banner_image = get_sub_field('banner_image');
			$logo_image = get_sub_field('logo_image'); ?>

		<div class="col-xs-12">
			<img src="<?php echo $banner_image['url']; ?>" class="img-responsive center-block fullwidth" />
		</div>

		<div class="col-xs-12 col-sm-6 col-md-4">
			<img src="<?php echo $logo_image['url']; ?>" class="img-responsive center-block fullwidth" />
		</div>

		<div class="col-xs-12 col-sm-6 col-md-8">
			<p><?php the_sub_field('text'); ?></p>
		</div>

		<?php
		}

		else if ($row_layout == "tourplan_non-accommodation_product") { ?>

		<div class="col-xs-12">
			<h3><i class="fa fa-ticket"></i> Ticket Information</h3>
			<div class="tourplan_plugin_section">

				<!-- <div class="row"> -->
				<div class="plugin_control">
					<!-- Need to place loading spinner here so it exists while loading -->
				</div>
				<!-- </div> -->

				<?php
				$enableSearch = get_sub_field('enable_search');

				if (have_rows('control_type')) {
					while (have_rows('control_type')) : the_row();
						$searchConf = array(
							'enableSearch' => $enableSearch,
							'srb' => get_sub_field('service_button'),
							'supplierid' => get_sub_field('supplier_id')
							);

						$productConf = array(
							'productid' => get_sub_field('product_id')
							);

						while (have_rows($configurations)) : the_row();
							$productConf[get_sub_field('product_config_setting')] = get_sub_field('value');
						endwhile;
					endwhile;
				}
				include('templates/' . get_sub_field('template_source'));
				?>

			</div>

			<script class="configs">
				var searchConfigs = <?php echo json_encode($searchConf); ?>;
				var productConfigs = <?php echo json_encode(array($productConf)); ?>;


				$(window).load(function() {
					var pluginControl = $(".tourplan_plugin_section");
					_.forEach(pluginControl, function(pc) {
						new TourplanNonAccomProductControlGroup(pc);
					})
				});
			</script>
		</div>

		<?php
		}

		else if ($row_layout == "tourplan_two_product_rail_pass") { ?>

		<div class="col-xs-12 col-lg-10">
			<div class="row">
				<div class="col-xs-12 col-md-5">
					<!-- Plugin section -->
				</div>
				<div class="col-xs-12 col-md-5">
					<!-- Image Section -->
				</div>
			</div>
			<div class="row">
				<div class="col-xs-12">
					<!-- Description Section -->
					<?php the_sub_field('description'); ?>
				</div>
			</div>
		</div>

		<div class="col-xs-12 col-lg-2">
		<div class="row">
			<div class="col-xs-12">
				<!-- Upper Map -->
			</div>
		</div>
		<div class="row">
			<div class="col-xs-12">
				<!-- Lower Map -->
			</div>
		</div>
		</div>

		<?php
		}
		?>

	</div>
	<?php
	endwhile;	
	?>
</div><!-- .container ends -->

<?php get_footer(); ?>
