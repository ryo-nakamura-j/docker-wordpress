
TourplanMultiProductController = function(rootElement, controlConfiguration) {
	console.log(controlConfiguration);
	var parentControl = this;
	this.controlConfiguration = controlConfiguration;
	this.rootElement = rootElement;
	this.pluginControl = $(rootElement).find('.plugin_control').first();
	this.products = [];

	this.templates = {};


	_.forEach(
		$(this.rootElement).children('script[type="text/x-handlebars-template"]'), 
		function(x) {
			parentControl.templates[$(x).attr('class')] = Handlebars.compile($(x).html());
		}
	);

	this.pluginControl.html(this.templates.masterTemplate(controlConfiguration));

	var initialSearchDate = new moment().add(parseInt(getServiceButtonConfig(controlConfiguration.service_button, "searchDateOffset")), "days");
	var minDate = controlConfiguration.min_date_type == "fixed" ? 
		new moment(controlConfiguration.min_date_val, "DD/MM/YYYY") : 
		new moment().add(parseInt(controlConfiguration.min_date_val), "days");
	var maxDate = controlConfiguration.max_date_type == "fixed" ? 
		new moment(controlConfiguration.max_date_val, "DD/MM/YYYY") : 
		new moment().add(parseInt(controlConfiguration.max_date_val), "days");

	if (minDate.isAfter(initialSearchDate)) {
		initialSearchDate = minDate;
	}

	pikadayResponsive(
		$(this.rootElement).find(".datepicker"), 
		_.extend({},
			{
				minDate: minDate,
				maxDate: maxDate
			}, 
			TourplanRetailUtilities.PIKADAYDEFAULTS
		)
	).setDate(initialSearchDate);

	_.forEach(controlConfiguration.productConfs, function(productConf) {
		var productController = new TourplanProductController(_.extend(productConf, { 'service_button':parentControl.controlConfiguration.service_button }));
		var productView = $(productController.Render(parentControl.templates.productSearchTemplate)).appendTo(
			$(parentControl.rootElement).find('.productSearchSection')
		);
		productController.SetView(productView);


		_.forEach($(productView).find('select'), function(el) {
			$(el).change(function(e) {
				productController.qtyObj[$(e.target).attr('name')] = parseInt($(this).val());
				console.log(productController);
			});
		});

		productController.SetQty(productController.controlConfig.defaultQty);

		productController.ProductRequest({
			date: $(parentControl.rootElement).find(".datepicker").val(),
			qty: '1' + productController.controlConfig.paxType[0].toUpperCase()
		}, 
		true,
		function(data) {
			var product = new TourplanProduct(data.products[0]);

			if (product.LowestPricedAvailability().AgentPrice != null) {
				productController.UpdatePrice(product.LowestPricedAvailability().AgentPrice);
			}
			// var ageString = productController.data.ageRanges[productController.controlConfig.paxType + 'AgeRange'];
			// console.log(ageString);
			// $(productController.view).find(".controlTitle").html(
			// 	productController.controlConfig.title + " (" + ageString + ")"
			// );

		},
		function() {
			console.log("ERROR PRICING");
		});

		parentControl.products.push(productController);
	});

	$(this.rootElement).find('select, input').change(function() {
		$(parentControl.rootElement).find(".productInfoSection").slideUp();
	});

	$(this.rootElement).find('button[name=search]').click(function(e) {
		// $(parentControl.rootElement).mask("Please Wait");

		$(parentControl.rootElement).find('.productInfoSection').slideUp(400, function() {
			$(this).find(".productResultSection").empty();
			$(parentControl.rootElement).mask("");
		});

		var requestList = [];

		_.forEach(parentControl.products, function(productController) {
			var searchParams = { date: $(parentControl.rootElement).find('.datepicker').val() };
			requestList.push(productController.ProductRequest(searchParams, true));
		});

		$.when.apply($, requestList).done(function() {
			var totalPrice = 0;
			var atLeastOneResponse = false;
			if (requestList.length < 2) {
				arguments = [arguments];
			}
			_.forEach(arguments, function(response) {
				if (response[0] != null) {
					var product = new TourplanProduct(response[0].products[0]);

					if (product.LowestPricedAvailability() != Infinity) {
						totalPrice += product.LowestPricedAvailability().AgentPrice;
						atLeastOneResponse = true;
					}
				}

			});

			if (atLeastOneResponse) {


				console.log(totalPrice);

				$(parentControl.rootElement).find('.productInfoSection').html(parentControl.templates.productInfoTemplate(parentControl.controlConfiguration));

				$(parentControl.rootElement).find('button[name=book]').click(function(e) {
					$(parentControl.rootElement).mask("");
					var servicelines = [];

					_.forEach(parentControl.products, function(productController) {
						var serviceline = productController.BuildServiceline();

						if (serviceline) {
							servicelines.push(serviceline);
						}
					});


					CartInterface.GetCart(true, function() {
						CartInterface.cart.servicelines = CartInterface.cart.servicelines.concat(servicelines);

						CartInterface.PushCart({
							success: function() { 
								$(parentControl.rootElement).unmask();
								window.location = $("#tourplanRetailConfig").attr("itinerarypage"); 
							}
						});
					});

				});

				parentControl.UpdatePrice(totalPrice);

				_.forEach(parentControl.products, function(productController) {
					$(productController.Render(parentControl.templates.productResultTemplate)).appendTo(
						$(parentControl.rootElement).find('.productResultSection'));
				});

				$(parentControl.rootElement).find(".productResultsSection").slideDown();
			} else {
				$(parentControl.rootElement).find('.productInfoSection').html(parentControl.templates.noResultTemplate(parentControl.controlConfiguration));
			}


			$(parentControl.rootElement).find('.productInfoSection').slideDown(400, function() {
				$(parentControl.rootElement).unmask();
			});
		});
	});

	if (controlConfiguration.search_on_load) { 
		$(this.rootElement).find('button[name=search]').click();
	}
}

TourplanMultiProductController.prototype.UpdatePrice = function(newPrice) {
	$(this.rootElement).find('.productInfoSection .totalPrice').html(getServiceButtonConfig(this.service_button, "productPricePrefix") + (newPrice / 100).toFixed(2));
}