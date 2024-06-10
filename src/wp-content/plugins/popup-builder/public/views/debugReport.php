<?php
global $SGPB_DEBUG_POPUP_BUILDER_DETAILS;
$SGPB_DEBUG_POPUP_BUILDER_DETAILS = json_encode($SGPB_DEBUG_POPUP_BUILDER_DETAILS);

echo wp_kses('<script>var SGPB_DEBUG_POPUP_BUILDER_DETAILS = '.$SGPB_DEBUG_POPUP_BUILDER_DETAILS.';</script>', \sgpb\AdminHelper::allowed_html_tags());
?>

<script type="text/javascript">
function debugModeInit()
{
	if (typeof SGPB_DEBUG_POPUP_BUILDER_DETAILS == 'undefined') {
		return false;
	}

	console.log('%c POPUP BUILDER AVAILABLE DEBUG DETAILS', 'background: #eeeeee; color: #000000');

	for (var popupId in SGPB_DEBUG_POPUP_BUILDER_DETAILS) {
		console.groupCollapsed('Details for the '+ popupId +' popup:');

		var debugParamas = SGPB_DEBUG_POPUP_BUILDER_DETAILS[popupId];
		for (var i in debugParamas) {
			if (jQuery.isEmptyObject(debugParamas[i])) {
				continue;
			}

			var paramName = debugParamas[i];

			console.info('%c '+i.toUpperCase() +': ', 'background: #ccc; color: #000000');

			for (var x in paramName) {
				if (typeof paramName[x]['name'] != 'undefined') {
					console.log('%c Option name ------- ' + paramName[x]['name'], 'background: #eeeeee; color: #000000');
				}
				if (typeof paramName[x]['operator'] != 'undefined') {
					console.log('%c Option operator --- ' + paramName[x]['operator'], 'background: #eeeeee; color: #000000');
				}
				if (typeof paramName[x]['value'] != 'undefined') {
					if (typeof paramName[x]['value'] == 'string') {
						/* when empty string we need to set it 0 (delay) */
						if (paramName[x]['value'] == '') {
							paramName[x]['value'] = 0;
						}
						console.log('%c Option value ------ ' + paramName[x]['value'], 'background: #eeeeee; color: #000000');
					}
					else {
						console.log('%c Option value ------ ' + Object.values(paramName[x]['value']), 'background: #eeeeee; color: #000000');
					}
				}
				if (i == 'options') {
					// all other options here
					for (var option in paramName[x]) {
						console.log('%c '+option+ ' - ' + paramName[x][option], 'background: #eeeeee; color: #000000');
					}
				}
				console.log('<->');
			}
		}
		console.groupEnd();
	}
};

jQuery(document).ready(function()
{
	debugModeInit();
});

</script>
