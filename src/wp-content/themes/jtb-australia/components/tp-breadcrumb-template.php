<!-- tp-breadcrumb -->
<?php if (function_exists('yoast_breadcrumb')) { ?>
<div class="row">
	<div class="col-xs-12">
		<?php yoast_breadcrumb('<p id="breadcrumbs">', '</p>'); ?>
	</div>
</div>
<?php } ?>