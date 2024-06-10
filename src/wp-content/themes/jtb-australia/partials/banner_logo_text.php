<?php
$banner_image = get_sub_field('banner_image');
$logo_image = get_sub_field('logo_image'); ?>

<div class="row section-<?php echo $section; ?>">

	<div class="col-xs-12">
		<div class="ribbon-red-desktop attached"></div>
		<?php image_if_exists($banner_image, "img-responsive center-block fullwidth"); ?>
	</div>

	<div class="col-xs-12 col-sm-6 col-md-4">
		<?php image_if_exists($logo_image, "img-responsive center-block fullwidth"); ?>
	</div>

	<div class="col-xs-12 col-sm-6 col-md-8">
		<p><?php the_sub_field('text'); ?></p>
	</div>

</div>
