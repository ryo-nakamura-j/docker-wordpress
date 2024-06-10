<?php
namespace sgpb;
$metaboxes = apply_filters('sgpbAdditionalMetaboxes', array());
?>

<div class="sgpb sgpb-options">
	<?php foreach ( $metaboxes as $key => $metabox ) {
		if ( $key == 'allMetaboxesView' ) {
			continue;
		}
	?>
	<div class="sgpb-options-menu"
	     id="<?php echo esc_attr($key); ?>">
		<h3 class="sgpb-options-menu-header"><?php echo wp_kses($metabox['displayName'], 'post'); ?></h3>
		<span class="sgpb-options-menu-header__sub"><?php  echo esc_html($metabox['short_description']); ?></span>
	</div>

	<div class="sgpb-options-content">
		<div id="options-<?php echo esc_attr($key); ?>" class="sgpb-metabox sgpb-metabox-options ">
			<p class="sgpb-header-h1 sgpb-margin-top-20 sgpb-margin-bottom-50"><?php echo wp_kses($metabox['displayName'], 'post'); ?></p>
			<?php require_once( $metabox['filePath'] ); ?>
		</div>
	</div>
	<?php }; ?>
</div>
<script type="text/javascript">
	let hash = window.location.hash.replace(/^#/,'');
	if (hash) {
		jQuery('#'+hash).addClass('sgpb-options-menu-active');
	} else {
		jQuery('.sgpb-options-menu').first().addClass('sgpb-options-menu-active')
	}
	jQuery(document).ready(function () {
		setTimeout(function () {
			calcHeight();
		});
		jQuery('.sgpb-options-content, .sgpb-options-menu').click(function(){
			setTimeout(function(){
				calcHeight();
			}, 500);
		});
		jQuery('.sgpb-options-menu').click(function () {
			if (jQuery(this).hasClass('sgpb-options-menu-active')) {
				return;
			}
			const findActive = jQuery('.sgpb-options-menu-active');
			findActive.removeClass('sgpb-options-menu-active');
			jQuery(this).addClass('sgpb-options-menu-active');
			jQuery([document.documentElement, document.body]).animate({
				scrollTop: jQuery('#allMetaboxesView').offset().top
			}, 500);
			location.hash = jQuery(this).attr('id');
		});
		jQuery(document.body).on( 'click.postboxes', function() {
			calcHeight();
		});
		function calcHeight() {
			let minHeightShouldBe = 0;
			if (!jQuery( '.postbox' ).hasClass('closed')) {
				minHeightShouldBe = parseInt(jQuery('.sgpb-options-menu-active').next().height())+100;
			}
			jQuery('#allMetaboxesView').css('min-height', minHeightShouldBe+'px');
		}
	});
</script>
