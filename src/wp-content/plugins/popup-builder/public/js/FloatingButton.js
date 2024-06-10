function SGPBFloatingButton() {

}

SGPBFloatingButton.prototype.adminInit = function()
{
	var that = this;
	document.getElementById('sgpb-floating-btn-wrapper').addEventListener('change', function(){
		that.livePreview();
	});
	jQuery('#sgpb-floating-button-text').on('keyup keypress', function() {
		setTimeout(function() {

			that.livePreview();
		}, 100);
	});

	jQuery('#sgpb-enable-floating-button').on('click', function() {
		if (!jQuery(this).is(':checked')) {
			that.removeButton();
		}
		var buttonStyle = jQuery('#sgpb-floating-button-style').val();
		that.reorderPositions(buttonStyle);
		that.reorderOtherOptions(buttonStyle);
	});
	jQuery('.js-floating-button-position').click(function() {
		setTimeout(function() {
			that.livePreview();
		}, 100);
	});

	jQuery('.js-floating-button-style').click(function(e) {
		var buttonStyle = jQuery(this).attr('data-sgvalue');
		setTimeout(function(){
			that.livePreview();
			that.reorderOtherOptions(buttonStyle);
		}, 100);
	});

	jQuery('#sgpb-floating-btn-wrapper .sgpb-color-picker').each(function() {
		var currentColorPicker = jQuery(this);
		currentColorPicker.wpColorPicker({
			change: function() {
				setTimeout(function(){

					that.livePreview();
				}, 100);
			}
		});
	});
};

SGPBFloatingButton.prototype.livePreview = function()
{
	var buttonIsEnabled = document.getElementById('sgpb-enable-floating-button').checked;
	if (buttonIsEnabled) {
		this.createButton();
	}

	return true;
};

SGPBFloatingButton.prototype.createButton = function()
{
	/* we remove already existed button and create new */
	this.removeButton();
	var buttonStyle = document.getElementById('sgpb-floating-button-style').value;
	var buttonPosition = document.getElementById('sgpb-floating-button-position').value;

	this.hideShowUnrelatedOptions(buttonStyle);

	var positionTop = document.getElementById('sgpb-floating-button-position-top').value;
	var positionRight = document.getElementById('sgpb-floating-button-position-right').value;
	var textColor = document.getElementById('sgpb-floating-button-text-color').value;
	var bgColor = document.getElementById('sgpb-floating-button-bg-color').value;
	var borderColor = document.getElementById('sgpb-floating-button-border-color').value;
	var borderRadius = document.getElementById('sgpb-floating-button-border-radius').value;
	var borderSize = document.getElementById('sgpb-floating-button-border-size').value;
	var fontSize = document.getElementById('sgpb-floating-button-font-size').value;
	var text = document.getElementById('sgpb-floating-button-text').value;
	var button = document.createElement('div');
	button.innerHTML = '<span class="sgpb-'+buttonStyle+'-floating-button-text">'+text+'</span>';
	button.id = 'sgpb-floating-button';
	button.style.fontSize = fontSize+'px';
	button.style.borderWidth = borderSize+'px';
	button.style.borderRadius = borderRadius+'px';
	button.style.borderColor = borderColor;
	button.style.backgroundColor = bgColor;
	button.style.color = textColor;
	button.style.zIndex = '99999999';
	button.style.position = 'fixed';
	button.style.textAlign = 'center';
	button.style.padding = '10px';
	if (buttonPosition.includes('right')) {
		button.style.right = '0';
		if (buttonPosition.includes('center')) {
			button.style.top = positionTop+'%';
			button.style.transform = 'rotate(-90deg)';
			button.style.transformOrigin = 'bottom right';
		}
	}
	if (buttonPosition.includes('bottom')) {
		button.style.bottom = '0';
		if (buttonPosition.includes('center')) {
			button.style.right = positionRight+'%';
		}
	}
	if (buttonPosition.includes('left')) {
		button.style.left = '0';
		if (buttonPosition.includes('center')) {
			button.style.top = positionTop+'%';
			button.style.transform = 'rotate(90deg)';
			button.style.transformOrigin = 'left bottom';
		}
	}
	if (buttonPosition.includes('top')) {
		button.style.top = '0';
		if (buttonPosition.includes('center')) {
			button.style.right = positionRight+'%';
		}
	}
	if (buttonStyle === 'corner') {
		if (buttonPosition === 'top-left') {
			button.style.left = '-220px';
			button.style.top = '-40px';
			button.style.transform = 'rotate(140deg)';
			button.style.transformOrigin = 'right center';
		}
		if (buttonPosition === 'bottom-left') {
			button.style.left = '-115px';
			button.style.bottom = '-145px';
			button.style.transform = 'rotate(45deg)';
			button.style.transformOrigin = 'right center';
		}
		if (buttonPosition === 'top-right') {
			button.style.right = '62px';
			button.style.top = '-145px';
			button.style.transform = 'rotate(-140deg)';
			button.style.transformOrigin = 'right';
		}
		if (buttonPosition === 'bottom-right') {
			button.style.right = '-65px';
			button.style.bottom = '-30px';
			button.style.transform = 'rotate(-45deg)';
			button.style.transformOrigin = 'right';
		}
		button.style.width = '160px';
		button.style.height = '160px';
		button.style.display = 'inline-grid';
	}
	button.className = 'sgpb-'+buttonStyle+'-'+buttonPosition;
	document.getElementsByTagName('body')[0].appendChild(button);
};

SGPBFloatingButton.prototype.removeButton = function()
{
	var button = document.getElementById('sgpb-floating-button');
	if (button !== null) {
		button.parentNode.removeChild(button);
	}
};

SGPBFloatingButton.prototype.hideShowUnrelatedOptions = function(buttonStyle)
{
	var that = this;
	jQuery('#sgpb-floating-button-style').on('select2:select', function (e) {
		var data = e.params.data;
		/* data.id = 'corner/basic' */
		that.reorderPositions(data.id);
		that.reorderOtherOptions(data.id);
	});
};

SGPBFloatingButton.prototype.reorderPositions = function(buttonStyle)
{
	var positionDropdown = jQuery('#sgpb-floating-button-position');
	if (!positionDropdown.length) {
		return false;
	}
	/* these positions will be removed or appended */
	var dynamicPositions = [
		{'id': 'top-center', 'text': 'Top center'},
		{'id': 'bottom-center', 'text': 'Bottom center'},
		{'id': 'right-center', 'text': 'Right center'},
		{'id': 'left-center', 'text': 'Left center'}
	];
	/* we need the loop and checking button style in it, to know if we should add or remove the related options */
	for (var i in dynamicPositions) {
		if (buttonStyle === 'corner') {
			positionDropdown.find("option[value='" + dynamicPositions[i].id + "']").remove();
		}
		if (buttonStyle === 'basic') {
			/* check if we've already had this option (select2 documentation method) */
			if (positionDropdown.find("option[value='" + dynamicPositions[i].id + "']").length) {
				positionDropdown.val(dynamicPositions[i].id).trigger('change');
			}
			else {
				var newOption = new Option(dynamicPositions[i].text, dynamicPositions[i].id, false, false);
				positionDropdown.append(newOption).trigger('change');
			}
		}
	}

	return false;
};

SGPBFloatingButton.prototype.reorderOtherOptions = function(buttonStyle)
{
	jQuery('.sgpb-basic-button-style-options-wrapper-js').removeClass('sgpb-hide');
	if (buttonStyle === 'corner') {
		jQuery('.sgpb-basic-button-style-options-wrapper-js').addClass('sgpb-hide');
	}
};

jQuery(document).ready(function() {
	if (document.getElementById('sgpb-enable-floating-button') === null) {
		return false;
	}
	var sgpbFloatingBtn = new SGPBFloatingButton();
	sgpbFloatingBtn.livePreview();
	sgpbFloatingBtn.adminInit();
});
