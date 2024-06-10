

// Deprecated?
TourplanNonAccomProductControlGroup = function(rootElement) {
	_.extend(this, new TourplanCommonControl(rootElement, window.searchConfigs, null));

	// this.pluginControl = $(this.rootElement).find('.plugin_control')[0];
	this.customDatePicker = (!Modernizr.touch || 
		!Modernizr.inputtypes.date ||
		$("body").hasClass("desktop"));

	/// Render main template
	$(this.pluginControl).html(this.templates.controlTemplate());

	this.searchElement = $(this.pluginControl).children('.searchControl')[0];
	this.resultElement = $(this.pluginControl).children('.resultControl')[0];
	
	var initDate = _.isEmpty(this.searchConfigs.searchdate) ? 
		new moment().add(getServiceButtonConfig(this.searchConfigs.srb, "searchDateOffset", 0), "days") :
		new moment(this.searchConfigs.searchdate, 'YYYY-MM-DD');

 	var tmpTemplates = this.templates;

	var resultElement = $(this.resultElement);

	var tmpObj = this;

	this.productControls = _.map(
		window.productConfigs, 
		function(x) { 
			var newElement = $('<div class="non-accommodation_product_result ' + tmpObj.searchConfigs.srb + '"></div>');
			$(resultElement).append(newElement);
			return new TourplanNonAccomProductControl(x, newElement, tmpTemplates.resultTemplate);
		}
	);

	if (this.searchConfigs.enableSearch) {

		$(this.searchElement).html(this.templates.searchTemplate());

		var defaultQty = _.isEmpty(this.searchConfigs.searchqty) ? 
			getServiceButtonConfig(this.searchConfigs.srb, 'defaultQty') :
			this.searchConfigs.searchqty;
		var defaultAdult = defaultQty.match(/([0-9]*A)/g);
		var defaultChild = defaultQty.match(/([0-9]*C)/g);
		var defaultInfant= defaultQty.match(/([0-9]*I)/g);

		if (defaultAdult) {
			$(this.rootElement).find('select.adultQty').val(defaultAdult[0]);
		}
		if (defaultChild) {
			$(this.rootElement).find('select.childQty').val(defaultChild[0]);
		}
		if (defaultInfant) {
			$(this.rootElement).find('select.infantQty').val(defaultInfant[0]);
		}
		pikadayResponsive($(this.rootElement).find(".searchDate"), {
			classes: "form-control",
			placeholder: "DD-MM-YYYY",
			outputFormat: "YYYY-MM-DD",
			format: "DD-MM-YYYY",
			checkIfNativeDate: function () {
		    	return Modernizr.inputtypes.date && (Modernizr.touch && navigator.appVersion.indexOf("Win") === -1);
			}
		}); 

		$(this.rootElement).find('button.refresh').click($.proxy(this.GetProducts, this));
		$(this.rootElement).find('select, input').change($.proxy(
			function(sender) {
				_.forEach(this.productControls, function(x) {
					x.destinationElement.slideUp();
				})
			}, 
			this))

		this.GetProducts();
	
	} else {
		this.GetRates();
	}
}

TourplanNonAccomProductControlGroup.prototype.GetProducts = function() {
	var searchData = {
		qty: this.GetQty(),
		scu: 1,
		date: this.GetSearchDate()
	}

	_.forEach(this.productControls, function(control) {
		control.destinationElement.html(TourplanRetailUtilities.loadingImage());
		control.destinationElement.slideDown();
		control.GetRates(searchData, function(response) {
			var prod = (response && response.products && response.products.length > 0) ? 
				new TourplanProduct(response.products[0]) : 
				new TourplanProduct({});

			prod['configs'] = this.configs;

			this.destinationElement.html(this.template(prod));

			this.destinationElement.find('button.book').click(function() {


				var config = TourplanRetailUtilities.ParseQty(prod.availability[0].Qty);
				var pax = [];

				for (i = 0; i < config.adults; i++) { pax.push({paxtype:'A'}); }
				for (i = 0; i < config.children; i++) { pax.push({paxtype:'C'}); }
				for (i = 0; i < config.infants; i++) { pax.push({paxtype:'I'}); }

				config['pax'] = pax;

				CartInterface.addServiceLine(
					prod,
					prod.availabilty[0],
					{
						rateid: 'Default',
						config: [config]
					},
					{
						success: function() { window.location = $("#tourplanRetailConfig").attr("itinerarypage"); }
					}
				);
			});
		});
	})	
}

TourplanNonAccomProductControlGroup.prototype.GetSearchDate = function() {
	var searchDate = null;
	if (this.searchConfigs.enableSearch) { 
		searchDate = moment($(this.rootElement).find('.searchDate').val());
	}
	else { 
		searchDate =  _.isEmpty(this.searchConfigs.searchdate) ?
			moment().add(getServiceButtonConfig(this.searchConfigs.srb, "searchDateOffset", 0), "days") :
			moment(this.searchConfigs.searchdate, 'YYYY-MM-DD');
	}
	return searchDate.format('YYYY-MM-DD');
}

TourplanNonAccomProductControlGroup.prototype.GetQty = function() {
	var qty = "";
	if (this.searchConfigs.enableSearch) {
		var adultQty = $(this.rootElement).find('.adultQty').val() || "0A";
		var childQty = $(this.rootElement).find('.childQty').val() || "0C";
		var infantQty = $(this.rootElement).find('.infantQty').val() || "0I";
		qty = adultQty + childQty + infantQty;
	} else {
		qty = _.isEmpty(this.searchConfigs.searchqty) ? 
			getServiceButtonConfig(this.srb, 'defaultQty') :
			this.searchConfigs.searchqty;
	}

	return qty;
}