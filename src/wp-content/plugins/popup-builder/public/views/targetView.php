<?php
namespace sgpb;

$targetData = $popupTypeObj->getOptionValue('sgpb-target');
$popupTargetData = ConditionBuilder::createTargetConditionBuilder($targetData);
$type = (!empty($_GET['sgpb_type'])) ? sanitize_text_field($_GET['sgpb_type']) : $popupTypeObj->getOptionValue('sgpb-type');
$allowed_html = AdminHelper::allowed_html_tags();

?>

<div class="popup-conditions-wrapper popup-conditions-target" data-condition-type="target">
	<?php
	$creator = new ConditionCreator($popupTargetData);
	echo wp_kses($creator->render(), $allowed_html);
	?>
</div>

<input type="hidden" name="sgpb-type" value="<?php echo esc_attr($type); ?>">
<input id="sgpb-is-preview" type="hidden" name="sgpb-is-preview" value="0" autocomplete="off">
<input id="sgpb-is-active" type="hidden" name="sgpb-is-active" value="<?php echo esc_attr($popupTypeObj->getOptionValue('sgpb-is-active')); ?>" autocomplete="off">
