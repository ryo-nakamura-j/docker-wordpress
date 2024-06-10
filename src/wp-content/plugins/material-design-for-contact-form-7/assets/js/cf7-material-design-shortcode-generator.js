(function($) {
	$(document).ready(function() {
		// === Initial state === //
		var state = JSON.parse(window.cf7md_html.shortcode_generator_state);
		state.active = "list";

		// ==================================== //
		// FUNCTIONS DIRECTLY RELATED TO STATE
		// ==================================== //

		/**
		 * Reset State
		 */
		function resetState() {
			state = JSON.parse(window.cf7md_html.shortcode_generator_state);
			state.active = "list";
			$("#cf7md-scg-list .mdc-list-item").removeClass(
				"mdc-ripple-upgraded--background-active mdc-ripple-upgraded--background-bounded-active-fill"
			);
			handleStateChange(true);
		}

		/**
		 * Handle state change
		 */
		function handleStateChange(mapToDom) {
			if (typeof mapToDom === "undefined") mapToDom = true;
			if (mapToDom) {
				mapStateToDom();
			}
			switchPanel(state.active || "list");
			toggleBackBtn();
			updateShortcode();
		}

		/**
		 * Change an attribute's value on state
		 */
		function setAttributeValue(shortcode, attrIndex, newValue) {
			state.shortcodes[shortcode].attributes[attrIndex].value = newValue;
			handleStateChange(false);
		}

		/**
		 * Map State To DOM
		 * Updates the values of the inputs based on state
		 */
		function mapStateToDom() {
			$.each(state.shortcodes, function(i, sc) {
				var $panel = $('.cf7md-scg--panel[data-panel="' + sc.type + '"]');
				$.each(sc.attributes, function(j, att) {
					var $input = $panel.find('[data-attr-index="' + j + '"]');
					var value = att.hasOwnProperty("value") ? att.value : att.default;
					if ($input.is('[type="checkbox"], [type="radio"]')) {
						$input.each(function(index, el) {
							$(el).prop("checked", $(el).val() === value);
						});
					} else {
						$input.val(value);
					}
					// Also update sliders
					if ($input.hasClass("cf7md-layout-slider-input")) {
						var slider = $input
							.closest(".cf7md-item")
							.find(".cf7md-layout-slider")
							.get(0);
						if (slider.noUiSlider) {
							slider.noUiSlider.set(parseInt(value, 10));
						}
					}
				});
			});
		}

		/**
		 * Switch Panel
		 */
		function switchPanel() {
			var $panels = $(".cf7md-scg--panel");
			if (state.active === $panels.filter(":visible").data("panel")) return;
			var $newActivePanel = $panels.filter(
				'[data-panel="' + state.active + '"]'
			);
			$(".cf7md-scg--body").toggleClass(
				"has-field-panels-active",
				state.active !== "list"
			);
			$panels.hide();
			$newActivePanel.show();
			$newActivePanel.find(".cf7md-scg--panel-body").scrollTop(0);
			updatePanelBodyHeight($newActivePanel);
		}

		/**
		 * Toggle back button
		 */
		function toggleBackBtn() {
			if (state.active === "list") {
				$(".cf7md-modal-back-btn").hide();
			} else {
				$(".cf7md-modal-back-btn").show();
			}
		}

		/**
		 * Build the shortcode and update the DOM
		 */
		function updateShortcode() {
			var sc = buildShortcode();
			var $panel = getActivePanel();
			var $textarea = $panel.find(".cf7md-scg--shortcode");
			//$textarea.html('<code><pre>' + sc + '</pre></code>');
			$textarea.html(sc);
		}

		// === Get active panel by state === //
		function getActivePanel() {
			return $('.cf7md-scg--panel[data-panel="' + state.active + '"]');
		}

		// ==================================== //
		// SET UP EVENTS AND DOM
		// ==================================== //

		// === Panel body dynamic height === //
		function updatePanelBodyHeight($panel) {
			var $panelBody = $panel.find(".cf7md-scg--panel-body");
			var $footer = $panel.find(".cf7md-scg--footer");
			var availableHeight = $(".cf7md-scg--field-panels").outerHeight();
			$panelBody.css({
				height: availableHeight - $footer.outerHeight() + "px",
				paddingBottom: "0"
			});
		}
		$(window).resize(function() {
			updatePanelBodyHeight(
				$('.cf7md-scg--panel[data-panel="' + state.active + '"]')
			);
		});

		// === List items === //
		var $list = $("#cf7md-scg-list");
		var $listItems = $list.find(".mdc-list-item");
		$listItems.filter(":not(.cf7md-list-item__locked)").click(function(e) {
			e.preventDefault();
			state.active = $(this).data("open-panel");
			handleStateChange();
		});

		// === Panel fields === //
		var $fields = $(".cf7md-scg--fields").find("input, select, textarea");
		$fields.on("change", function(e) {
			fieldUpdate($(this));
		});
		$fields
			.not('select, [type="checkbox"], [type="radio"]')
			.on("input", function(e) {
				fieldUpdate($(this));
			});
		function fieldUpdate($this) {
			// Update the state
			var $panel = $this.closest(".cf7md-scg--panel");
			var attrIndex = $this.data("attr-index");
			var value = $this.val();
			if (
				$this.is('[type="checkbox"], [type="radio"]') &&
				!$this.prop("checked")
			) {
				value = $this.val() === "1" ? "0" : "";
			}
			setAttributeValue($panel.data("panel"), attrIndex, value);
		}

		// === Modal === //
		var $modalBg = $(".cf7md-modal-backdrop");
		var $modal = $(".cf7md-modal");
		var $modalCloseBtn = $(".cf7md-modal-close-btn");
		var $modalBackBtn = $(".cf7md-modal-back-btn");

		$modalBg.add($modalCloseBtn).click(function(e) {
			e.preventDefault();
			closeModal();
		});

		$modalBackBtn.click(function(e) {
			e.preventDefault();
			resetState();
		});

		function positionModal() {
			if (!$modal.is(":visible")) return;
			// positioning here rather than a CSS transform
			// prevents the blurry text
			var $windowHeight = $(window).outerHeight();
			var $windowWidth = $(window).outerWidth();
			var $modalHeight = $modal.outerHeight();
			var $modalWidth = $modal.outerWidth();
			$modal.css({
				top: ($windowHeight - $modalHeight) / 2 + "px",
				left: ($windowWidth - $modalWidth) / 2 + "px"
			});
		}
		$(window).resize(positionModal);

		function closeModal() {
			$modalBg.removeClass("is-visible");
			$modal.removeClass("is-visible");
			$("body").css("overflow", "");
			resetState();
		}

		function openModal() {
			$modalBg.addClass("is-visible");
			$modal.addClass("is-visible");
			positionModal();
			$(".cf7md-scg--list-panel").scrollTop(0);
			$("body").css("overflow", "hidden");
			//resetState();
		}

		// === Shortcode pre tag === //
		/* This might be more annoying than helpful
    $('.cf7md-scg--shortcode').on('click', function(){
      selectText($(this).find('pre').get(0));
    }); */

		// === Shortcode copy button === //
		if (typeof ClipboardJS === "function") {
			var copybutton = new ClipboardJS(".cf7md-scg--copy-btn", {
				text: function(trigger) {
					return buildShortcode();
				}
			});
			if (!ClipboardJS.isSupported()) {
				$(".cf7md-scg--copy-btn").hide();
			}
			copybutton.on("success", function(e) {
				setTimeout(function() {
					closeModal();
				}, 300);
				e.clearSelection();
			});
		}

		// === Shortcode insert button === //
		$(".cf7md-scg--insert-btn").click(function(e) {
			e.preventDefault();
			var content = buildShortcode();
			setTimeout(function() {
				// this part is copied from wpcf7
				$("textarea#wpcf7-form").each(function() {
					this.focus();

					if (document.selection) {
						// IE
						var selection = document.selection.createRange();
						selection.text = content;
					} else if (this.selectionEnd || 0 === this.selectionEnd) {
						var val = $(this).val();
						var end = this.selectionEnd;
						$(this).val(
							val.substring(0, end) + content + val.substring(end, val.length)
						);
						this.selectionStart = end + content.length;
						this.selectionEnd = end + content.length;
					} else {
						$(this).val($(this).val() + content);
					}

					this.focus();
				});
				closeModal();
			}, 300);
		});

		// === Layout range slider === //
		var $sliders = $(".cf7md-layout-slider");
		$sliders.each(function() {
			var $slider = $(this);
			var $item = $slider.closest(".cf7md-item");
			var slider = $slider.get(0);
			var max = Number($slider.data("max"));
			$slider.css("width", (max / 12) * 100 + "%");

			noUiSlider.create(slider, {
				start: [max],
				animate: false,
				connect: [true, false],
				//tooltips: [true],
				step: 1,
				range: {
					min: 0,
					max: max
				},
				pips: {
					mode: "steps",
					density: 100 / max
				}
			});

			// On slider update...
			slider.noUiSlider.on("update", function(values) {
				var val = parseInt(values[0], 10);
				// Restrict zero value
				if (val === 0) {
					slider.noUiSlider.set(1);
					val = 1;
				}
				// Update native input
				var $input = $item.find("input.cf7md-layout-slider-input");
				if (parseInt($input.val(), 10) !== val) {
					$input.val(val);
					$input.change();
				}

				// Update help text
				var fraction = "all";
				switch ((val / max).toFixed(2)) {
					case "0.25": // 3/12, 2/8, 1/4
						fraction = "one quarter";
						break;
					case "0.17": // 2/12
						fraction = "one sixth";
						break;
					case "0.33": // 4/12
						fraction = "one third";
						break;
					case "0.50": // 6/12, 4/8, 2/4
						fraction = "half";
						break;
					case "0.67": // 8/12
						fraction = "two thirds";
						break;
					case "0.75": // 9/12, 6/8, 3/4
						fraction = "three quarters";
						break;
					case "0.83": // 10/12
						fraction = "five sixths";
						break;
					case "1.00":
						fraction = "all";
						break;
					default:
						var numerator = {
							1: "one",
							2: "two",
							3: "three",
							4: "four",
							5: "five",
							6: "six",
							7: "seven",
							8: "eight",
							9: "nine",
							10: "ten",
							11: "eleven",
							12: "twelve"
						};
						var denominator = { 4: "quarter", 8: "eighth", 12: "twelfth" };
						fraction = numerator[val] + " " + denominator[max];
						fraction += val === 1 ? "" : "s";
						break;
				}
				$item.find(".cf7md-layout-slider-value").text(fraction);
			});

			// Make pips clickable
			var $pips = $(slider)
				.find(".noUi-value")
				.each(function() {
					var value = Number(this.getAttribute("data-value"));
					if (value !== 0) {
						$(this)
							.addClass("is-clickable")
							.click(function() {
								slider.noUiSlider.set(value);
							});
					}
				});
		});

		// === Shortcode generator button === //
		if ($("#tag-generator-list").length) {
			var $wrap = $("<div></div>");
			var $btn = $('<a href="#"></a>');
			var $tagList = $("#tag-generator-list");
			$btn.attr("id", "cf7md-shortcode-generator-btn");
			$btn.addClass("cf7md-shortcode-generator-btn");
			$btn.addClass(
				"mdc-button mdc-button--raised mdc-button--primary mdc-ripple-surface"
			);
			$btn.attr("data-mdc-auto-init", "MDCRipple");
			$btn.text("Material Design");
			$wrap.addClass("cf7md-admin");
			$wrap.css({
				float: "right",
				margin: "2px 1% 5px 5px",
				transform: "translateZ(0)"
			});
			$wrap.append($btn);
			$tagList.prepend($wrap);

			// (safari often doesn't re-paint)
			$tagList.css("display", "block");
			setTimeout(function() {
				$tagList.css("display", "");
			}, 10);

			$btn.click(function(e) {
				e.preventDefault();
				openModal();
			});
		}

		// ==================================== //
		// OTHER FUNCTIONS
		// ==================================== //

		// === Shortcode builder === //
		function buildShortcode() {
			var sc = state.shortcodes[state.active];

			if (typeof sc === "undefined") return;

			var output = "[" + sc.type;

			// Build the attributes
			$.each(sc.attributes, function(i, att) {
				if (att.renderer === "html") return true;

				if (
					att.hasOwnProperty("required") ||
					(att.hasOwnProperty("value") && att.value !== att.default)
				) {
					var value = typeof att.value === "undefined" ? "" : att.value;
					output += " " + att.name + '="' + value + '"';
				}
			});

			// Close the opening tag
			if (sc.hasOwnProperty("selfClosing")) {
				output += " /]";
				return output;
			} else {
				output += "]";
			}

			// Build the 'replace me' text
			if (sc.hasOwnProperty("replace")) {
				output += "\n{{" + sc.replace + "}}";
			} else {
				output +=
					"\n{{A single " +
					formatArray(sc.wraps, "or") +
					" form tag goes here}}";
			}

			// End
			output += "\n[/" + sc.type + "]";
			return output;
		}

		// === Utilities == //

		// Format array from [a,b,c] to 'a, b and c'
		function formatArray(arr, joinWord) {
			var outStr = "";
			if (arr.length === 1) {
				outStr = arr[0];
			} else if (arr.length === 2) {
				outStr = arr.join(" " + joinWord + " ");
			} else if (arr.length > 2) {
				outStr =
					arr.slice(0, -1).join(", ") + " " + joinWord + " " + arr.slice(-1);
			}
			return outStr;
		}

		// Select text
		function selectText(node) {
			if (document.body.createTextRange) {
				const range = document.body.createTextRange();
				range.moveToElementText(node);
				range.select();
			} else if (window.getSelection) {
				const selection = window.getSelection();
				const range = document.createRange();
				range.selectNodeContents(node);
				selection.removeAllRanges();
				selection.addRange(range);
			}
		}
	});
})(jQuery);
