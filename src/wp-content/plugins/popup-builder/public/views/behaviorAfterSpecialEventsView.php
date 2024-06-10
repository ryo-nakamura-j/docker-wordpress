<?php
namespace sgpb;
$data = $popupTypeObj->getOptionValue('sgpb-behavior-after-special-events');
$builder = ConditionBuilder::createBehaviorAfterSpecialEventsConditionBuilder($data);
?>

<div class="popup-conditions-wrapper popup-special-conditions-wrapper behavior-after-special-events-wrapper" data-condition-type="behavior-after-special-events">
	<?php
		$creator = new ConditionCreator($builder);
		echo wp_kses($creator->render(), AdminHelper::allowed_html_tags());
	?>
</div>
