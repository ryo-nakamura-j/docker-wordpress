// This closure gives access to jQuery as $
// Don't delete it
(function($) {

	// Do stuff
	$(document).ready(function(){


		// === Running ID === //
		window.cf7mdId = 0;


		// === Form formatting === //
		$('.cf7md-form').each(function(){
			var $this = $(this);

			// Remove empty p tags inside, before and after
			$this.siblings('p')
				.add($this.find('p'))
				.filter(function () { return $.trim(this.innerHTML) == "" }).remove();

			// Add `required` attribute to all required fields
			$('.cf7md-text, .cf7md-textarea').find('.wpcf7-validates-as-required').each(function(){
				$(this).attr('required', 'required');
			});
		});
		

		// === Generic item formatting === //
		$('.cf7md-item').each(function(){
			var $this = $(this),
				$span = $this.find('.wpcf7-form-control-wrap');

			// Remove breaks
			$this.find('br').remove();
			// Add md class to span
			$span.addClass('mdc-form-field cf');
		});
		

		// === Prepare text inputs and textareas for MD Init === //
		$('.cf7md-text, .cf7md-textarea').each(function() {
			var $this = $(this),
				$input = $this.find('input, textarea'),
				$span = $this.find('.wpcf7-form-control-wrap'),
				$tpl = $this.find('.cf7md-text-html').find('> div'),
				$label = $tpl.find('label');

			// Move input
			$input.detach().prependTo($tpl);
			// Insert template
			$tpl.detach().appendTo($span);
			// Add md class to input
			$input.addClass('mdc-textfield__input');
			// Add 'for' to label
			$label.attr('for', $input.attr('name'));
			// Add autosize to textareas
			if($this.hasClass('cf7md-textarea-autosize')){
				$input.attr('rows', '2');
				autosize($input);
			}
		});


		// === Make html5 date inputs play nice with our text fields === //
		$('.cf7md-text [type="date"]').each(function(){
			var $this = $(this);
			$this.change(function(){
				if($this.val() !== '') {
					$this.addClass('cf7md-has-input');
				} else {
					$this.removeClass('cf7md-has-input');
				}
			});
		});


		// === Prepare selects === //
		$('.cf7md-select').each(function() {
			var $this = $(this),
				$select = $this.find('select'),
				label = $this.find('.cf7md-select-label').text(),
				$option1 = $select.children('option').first();

			if($select.hasClass('wpcf7-validates-as-required')) {
				label += '*';
			}
			
			if($this.hasClass('cf7md-select--multi')) {
				// Add class and size for multi
				$select.addClass('mdc-multi-select mdc-list');
				$select.attr('size', '5');
			} else {
				// Add class for standard select
				$select.addClass('mdc-select');
				// Add blank option as label
				if($option1.attr('value') === '') {
					$option1.text(label);
				} else {
					$option1.before('<option default selected value="">' + label + '</option>');
				}
			}
		});


		// === Prepare checkboxes === //
		$('.cf7md-checkbox, .cf7md-radio').each(function() {
			var $this = $(this),
				type = $this.hasClass('cf7md-radio') ? 'radio' : 'checkbox',
				$items = $this.find('.wpcf7-list-item'),
				tpl = $this.find('.cf7md-' + type + '-html').html();

			$items.each(function(){
				var $item = $(this),
					$checkbox = $item.find('input'),
					$label = $item.find('.wpcf7-list-item-label'),
					$labelWrap = $label.parents('label'),
					label = $label.text(),
					$html = $(tpl).clone(),
					$wrap = $('<div class="mdc-' + type + '"></div>'),
					mdcCheckbox = $wrap[0],
					id = 'cf7md' + ++cf7mdId;
				
				// Add classes and ID
				$checkbox.addClass('mdc-' + type + '__native-control').attr('id', id);
				$item.addClass('cf7md-' + type + '-item mdc-form-field cf');
				// Rearrange markup
				$item.append($wrap);
				$label.remove();
				$labelWrap.remove();
				$checkbox.detach().appendTo($wrap);
				$wrap.append($html);
				$wrap.after('<label class="cf7md-' + type + '-label" for="' + id + '">' + label + '</label>');
				// Instantiate mdc checkbox js
				// We're not doing this right now because it's harder to customize colours for
				// and doesn't add all that much pizazz
				//mdc.checkbox.MDCCheckbox.attachTo(mdcCheckbox);
			});
		});


		// === Prepare switches === //
		$('.cf7md-switch').each(function() {
			var $this = $(this),
				type = 'switch',
				$items = $this.find('.wpcf7-list-item'),
				tpl = $this.find('.cf7md-switch-html').html();

			$items.each(function(){
				var $item = $(this),
					$checkbox = $item.find('input'),
					$label = $item.find('.wpcf7-list-item-label'),
					$labelWrap = $label.parents('label'),
					label = $label.text(),
					$html = $(tpl).clone(),
					$switch_wrap = $html.find('.mdc-switch'),
					id = 'cf7md' + ++cf7mdId,
					$newlabel = $('<label for="' + id + '" class="cf7md-switch-label">' + label + '</label>');
				
				// Add classes and ID
				$checkbox.addClass('mdc-' + type + '__native-control').attr('id', id);
				$item.addClass('cf7md-' + type + '-item cf');
				// Rearrange markup
				$item.append($html);
				$checkbox.detach().prependTo($switch_wrap);
				$newlabel.appendTo($html);
				$label.remove();
			});
		});


		// === Prepare acceptance === //
		$('.cf7md-accept').each(function() {
			var $this = $(this),
				$form = $this.closest('#cf7md-form'),
				$span = $this.find('.wpcf7-form-control-wrap'),
				$control = $span.find('.wpcf7-form-control'),
				$checkbox = $this.find('input'),
				$tpl = $this.find('.cf7md-checkbox-html').find('> div'),
				$mdWrap = $('<div class="cf7md-accept--inner"></div>'),
				$wrap = $('<div class="mdc-checkbox"></div>'),
				$label = $this.find('.cf7md-accept-label'),
				$cf7Label = $span.find('.wpcf7-list-item-label'),
				id = 'cf7md' + ++cf7mdId;

			// Use the cf7 label over the shortcode label if it exists
			if($cf7Label.length && $cf7Label.html().length) {
				$label.html($cf7Label.html());
				$cf7Label.detach();
			}

			// Add class to md wrap
			$mdWrap.addClass('mdc-form-field');			
			// Update checkbox input
			$checkbox.addClass('mdc-checkbox__native-control').attr('id', id);
			// Insert tpl
			$span.append($mdWrap);
			$mdWrap.append($wrap);
			// Move checkbox into wrap
			$checkbox.detach().appendTo($wrap);
			// Move tpl into wrap
			$wrap.append($tpl);
			// Add `for` to label
			$label.attr('for', id);
			// Move label
			$label.detach().insertAfter($wrap);
			// Hide original control
			if($this.hasClass('cf7md-is-wpcf7v5')) {
				$control.hide();
			}

			// Watch and toggle submit enabled/disabled
			$checkbox.click(function(){
				toggleSubmit($form);
			});
			toggleSubmit($form);
		});

		// Acceptance toggle function
		function toggleSubmit($form) {
			// v5+ logic
			if($form.find('.cf7md-accept').hasClass('cf7md-is-wpcf7v5')) {
			
				var $acceptances = $form.find('.wpcf7-acceptance'),
					$submit = $form.find('.cf7md-submit-btn'),
					$formTag = $form.closest('form.wpcf7-form');

				// Straight outta wpcf7
				if($formTag.hasClass('wpcf7-acceptance-as-validation')){
					return;
				}
				
				$submit.removeAttr( 'disabled' );

				$acceptances.each(function(i, span) {
					var $span = $(span),
						$input = $span.closest('.wpcf7-form-control-wrap').find('input');

					// Straight outta wpcf7
					if ( ! $span.hasClass( 'optional' ) ) {
						if ( $span.hasClass( 'invert' ) && $input.is( ':checked' )
						|| ! $span.hasClass( 'invert' ) && ! $input.is( ':checked' ) ) {
							$submit.attr( 'disabled', 'disabled' );
							return false;
						}
					}
				});

			} else {
				
				// Pre wpcf7 v5 logic
				var $acceptances = $form.find('input:checkbox.wpcf7-acceptance'),
					$submit = $form.find('.cf7md-submit-btn');

				// Logic taken from wpcf7
				$submit.removeAttr( 'disabled' );
				$acceptances.each(function(i, node) {
					$this = $(node);
					if ( $this.hasClass( 'wpcf7-invert' ) && $this.is( ':checked' )
						|| ! $this.hasClass( 'wpcf7-invert' ) && ! $this.is( ':checked' ) ) {
						$submit.attr( 'disabled', 'disabled' );
					}
				});
			}
		}


		// === File fields === //
		$('.cf7md-file').each(function() {
			var $this = $(this),
				$file = $this.find('[type="file"]'),
				$value = $this.find('.cf7md-file--value'),
				$btn = $this.find('.cf7md-file--btn'),
				$label = $this.find('.cf7md-label--static'),
				$wrap = $this.find('.cf7md-file--label'),
				$error = $this.find('.wpcf7-not-valid-tip');

			// Move the error in the DOM
			$error.detach().insertAfter($wrap);
			// Position value
			$value.css({
				paddingLeft: $btn.outerWidth() + 12 + 'px',
				top: $btn.outerHeight() / 2 + 'px'
			});
			// Update the value on load
			if($file.val()) {
				fileName = $file.val().split('\\').pop();
				$value.text(fileName);
			}
			// Update the value on change
			$file.on('change', function(e) {
				if(e.target.value) {
					fileName = e.target.value.split('\\').pop();
					$value.text(fileName);
				}
			});
		});


		// === Prepare quiz === //
		// TODO: Inputs are being duplicated every refresh
		function cf7mdQuiz(refresh) {
			$('.cf7md-quiz').each(function(){
				var $this = $(this),
					$input = $this.find('.wpcf7-quiz'),
					$newInput = $input.clone(),
					$span = $this.find('.wpcf7-form-control-wrap'),
					$tpl = $this.find('.mdc-textfield'),
					$label = $tpl.find('label'),
					$question = $this.find('.wpcf7-quiz-label'),
					$cf7label = $span.find('> label');
				
				// Update label
				$label.attr('for', $input.attr('name'));
				$label.text($question.text());
				$question.hide();
				// Move input and question
				$input.detach().prependTo($tpl);
				$question.detach().prependTo($tpl);
				// Insert template
				$tpl.detach().appendTo($span);
				// Add md class to input
				$input.addClass('mdc-textfield__input');
				// Remove empty cf7 label
				$cf7label.hide();
			});
		}
		cf7mdQuiz(false);
		// Update the quiz label when the form refreshes
		$(window).on('wpcf7submit', function(e) {
			cf7mdQuiz(true);
		});


		// === Prepare submit buttons for MD Init === //
		$('.cf7md-submit').each(function() {
			var $this = $(this),
				$form = $this.closest('#cf7md-form'),
				$inputs = $this.find('input, button');

			$inputs.each(function() {
				var $input = $(this),
					$val = $input.is('input') ? $input.attr('value') : $input.text(),
					$svg = $this.find('svg'),
					$btn = $('<button>' + $val + '</button>');

				// Copy atts from input to button
				$btn.addClass($input[0].className);
				$btn.attr('id', $input.attr('id'));
				$btn.attr('type', $input.attr('type'));
				// @todo Copy events to button -> https://stackoverflow.com/a/16944385/1466282
				// Add data init for ripple
				$btn.attr('data-mdc-auto-init', 'MDCRipple');
				// Add md classes
				$btn.addClass('cf7md-submit-btn mdc-button mdc-button--raised mdc-button--primary mdc-ripple-surface');
				// Replace input with button
				$input.replaceWith($btn);

				if($input.attr('type') === 'submit') {
					// maybe disable
					toggleSubmit($form);

					// Add click handler to button
					$btn.click(function(){
						// Move svg into spinner
						var $spinner = $(this).parents('.cf7md-submit').find('.ajax-loader');
						$svg.detach().appendTo($spinner);
					});
				}

			});
		});


		// === Update textfields that loaded with a default value === //
		setTimeout(function(){
			$('.mdc-textfield').each(function(){
				var $label = $(this).find('.mdc-textfield__label'),
					$field = $(this).find('.mdc-textfield__input'),
					val = $field.val();
				if(val){
					$label.addClass('mdc-textfield__label--float-above');
				}
			});
		}, 200);


		// === Mutation observer for things like conditional fields for CF7 === //

		// Run this when a field group is toggled
		function conditionalFieldGroupToggled(event) {
			
			// Update file inputs
			$('.cf7md-file').each(function() {
				var $this = $(this),
					$file = $this.find('[type="file"]'),
					$value = $this.find('.cf7md-file--value'),
					$btn = $this.find('.cf7md-file--btn');

				// Position value
				$value.css({
					paddingLeft: $btn.outerWidth() + 12 + 'px',
					top: $btn.outerHeight() / 2 + 'px'
				});

			});

		}
		
		// Feature detection for mutation observers - https://gist.github.com/stucox/5231211
		var MutationObserver = (function () {
			var prefixes = ['WebKit', 'Moz', 'O', 'Ms', '']
			for(var i=0; i < prefixes.length; i++) {
				if(prefixes[i] + 'MutationObserver' in window) {
				return window[prefixes[i] + 'MutationObserver'];
				}
			}
			return false;
		}());

		// Setup mutation observers
		if(MutationObserver) {
			var groups = $('[data-class="wpcf7cf_group"]')
			groups.each(function(i, element) {
				var observer = new MutationObserver(conditionalFieldGroupToggled);
				
				observer.observe(element, {
					attributes: true, 
					attributeFilter: ['class'],
					childList: false, 
					characterData: false
				});
			})
		}


		// Handle hiding the customize preview link
		if (typeof ajax_object !== 'undefined') {
			$('.cf7md-hide-customize-message').click(function(e){
				e.preventDefault();
				$(this).closest('.cf7md-admin-customize-message').hide();
				$.post(ajax_object.ajax_url, { action: 'cf7md_close_customize_link' }, function(response) {
					console.log('Close link response: ', response)
				});
			});
		}

		// === Initialize components === //
		window.mdc.autoInit();
		
	});

}(jQuery));