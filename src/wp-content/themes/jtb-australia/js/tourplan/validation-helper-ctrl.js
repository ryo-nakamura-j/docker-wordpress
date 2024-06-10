
// params - validationParamsList:
// [{
//	containerJq: jquery_element_object, 
//	format: formatRex
//	isRequired: true/false
//	validator: e.g. ValidationHelper.prototype.ValidateDropdown
// }, ...]
ValidationHelperCtrl = function() { 
	var ctrl = {
		DEVAULT_TEXTAREA_EMPTY: '0', // NV_VEGG need to always have numbers.
		ValidateAll: ValidateAll,
		ValidationFeedback: ValidationFeedback,
		ValidateDropdown: ValidateDropdown,
		ValidateTextArea: ValidateTextArea,
		ValidateDatePicker: ValidateDatePicker
	}; 
	return ctrl;

	function ValidateAll( validationParamsList ) {
		var rlt = true;
		_.forEach( validationParamsList, function(p) {
			switch( p.validator ) {
				case ctrl.ValidateDropdown:
				case ctrl.ValidateDatePicker:
					if ( !p.validator( p.containerJq, p.isRequired ) )
						rlt = false;
					break;
				case ctrl.ValidateTextArea:
					if ( !p.validator( p.containerJq, p.format, p.isRequired ) )
						rlt = false;
					break;
				default:
					console.log( "Invalid Validator - " + JSON.stringify(p) );
			}
		})
		return rlt;
	}
	function ValidationFeedback( containerJq, isValid ) {
		if ( isValid )
			containerJq.removeClass('has-error');
		else
			containerJq.addClass('has-error');
	}
	function ValidateDropdown( containerJq, isRequired ) {
		var rlt = true;
		if ( isRequired ) {
			_.forEach(containerJq.find('select'), function(sub) {
				if (_.isEmpty( $(sub).val()) ) {
					rlt = false;
				};
			});
		}
		ctrl.ValidationFeedback(containerJq, rlt)
		return rlt;
	}
	function ValidateTextArea( containerJq, format, isRequired ) {
		var rlt = true;
		var val = containerJq.find('textarea').val();
		// We have to always check regex. 
		// Beause plane text are fine with empty value,
		// but some fields like NV_VEGG is not gonna work with empty value
		// there will be a not selected error, similar to dropdowns

		// We will use a default value if the val is empty. This is a jtb requirement.
		if ( !isRequired && _.isEmpty(val) )
			val = ctrl.DEVAULT_TEXTAREA_EMPTY;

		if ( (isRequired && _.isEmpty(val) )
			|| ( !_.isEmpty(format) && format != ' ' && !val.match(format) ) )
			rlt = false;
		ctrl.ValidationFeedback(containerJq, rlt)
		return rlt;
	}
	function ValidateDatePicker( containerJq, isRequired ) {
		var rlt = true;
		if ( isRequired ) {
			_.forEach(containerJq.find('input'), function(sub) {
				if (_.isEmpty( $(sub).val()) ) {
					rlt = false;
				};
			});
		}
		ctrl.ValidationFeedback(containerJq, rlt)
		return rlt;
	}
}
ValidationHelper = ValidationHelperCtrl();