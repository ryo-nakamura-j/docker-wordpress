<?php
namespace sgpb;
$eventsData = $popupTypeObj->getOptionValue('sgpb-events');
$popupTargetData = ConditionBuilder::createEventsConditionBuilder($eventsData);
$allowed_html = AdminHelper::allowed_html_tags();
?>

<div class="popup-conditions-wrapper popup-special-conditions-wrapper popup-conditions-events sgpb-wrapper" data-condition-type="events">
	<?php
		$creator = new ConditionCreator($popupTargetData);
		echo wp_kses($creator->render(), $allowed_html);
	?>
</div>
