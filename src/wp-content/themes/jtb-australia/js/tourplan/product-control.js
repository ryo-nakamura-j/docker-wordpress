
TourplanProductControl = function(rootElement, controlConfiguration) {
	_.extend(this, new TourplanCommonControl(rootElement, null, null));
	this.controlConfiguration = controlConfiguration;

	console.log(this);

	var productControl = this;

	$(this.rootElement).html(this.templates.masterTemplate({controlConfig:productControl.controlConfiguration}));

	this.urlSearchParams = TourplanRetailUtilities.DeserializeURLParameters(window.location.search[0] == "?" ? window.location.search.substr(1) : window.location.search);

	this.pageSearchParams = controlConfiguration.static_config;

	if (productControl.controlConfiguration.supplier_based) {

		REI.Product(productControl.GetSearchParam("supplierid").value, _.map(productControl.GetAllSearchParams()), function(supplierData) {
			dataLayer.push({
				'event':'gtm.dom',
				'eventCategory': 'Ecommerce',
				'eventAction': 'Impression',
				'currencyCode': $("#tourplanRetailConfig").attr("currency"),
				'ecommerce': {
				  		'impressions': _.map(supplierData.products, function(product, index) {
				  			return {
				  				'id': product.productid,
				  				'name': product.name,
				  				'position': index,
				  				'list': product.srb,
				  				'category': product.srb
				  			}
				  		})
				  	}
			});
		});

		var initialSearchDate = new moment().add(parseInt(getServiceButtonConfig(controlConfiguration.service_button, "searchDateOffset")), "days");
		var minDate = productControl.controlConfiguration.date_config.min_date_type == "fixed" ? 
			new moment(productControl.controlConfiguration.date_config.min_date_val, "DD/MM/YYYY") : 
			new moment().add(parseInt(productControl.controlConfiguration.date_config.min_date_val), "days");
		var maxDate = productControl.controlConfiguration.date_config.max_date_type == "fixed" ? 
			new moment(productControl.controlConfiguration.date_config.max_date_val, "DD/MM/YYYY") : 
			new moment().add(parseInt(productControl.controlConfiguration.date_config.max_date_val), "days");

		if (minDate.isAfter(initialSearchDate)) {
			initialSearchDate = minDate;
		}
		
		REI.Supplier(productControl.GetSearchParam("supplierid").value, this.GetAllSearchParams(), function(supplierData) {
			var supplier = new TourplanSupplier(supplierData);
			console.log(supplier);
			$(productControl.rootElement).find('.supplierInfoSection').html(productControl.templates.supplierInfoTemplate(supplier.supplier));

			if (productControl.controlConfiguration && !_.isEmpty(productControl.controlConfiguration.modify_search_config)) {
				var refreshSearchSection = $(productControl.rootElement).find('.refreshSearchSection');

				refreshSearchSection.html(productControl.templates.refreshSearchTemplate(
					productControl.controlConfiguration)
				);
				console.log(productControl.controlConfiguration);

				if (productControl.GetSearchParam('qtyConfig').value == 'roombased') {
					$(productControl.rootElement).find('select[name=qty]')
						.val(productControl.GetSearchParam('qty').value.split(',').length);
				}
				else if (productControl.GetSearchParam('qtyConfig').value == 'paxbased') {
					var qtys = TourplanRetailUtilities.ParseQty(productControl.GetSearchParam('qty').value);

					$(productControl.rootElement).find('select[name=adultQty]').val(qtys.adults + "A");
					$(productControl.rootElement).find('select[name=childQty]').val(qtys.children + "C");
					$(productControl.rootElement).find('select[name=infantQty]').val(qtys.infants + "I");
				}

				var scuParam = productControl.GetSearchParam('scu');
				var dateParam = productControl.GetSearchParam('date');

				var dateControl = new TourplanDateControl(
					refreshSearchSection.find('div.input-daterange'),
					refreshSearchSection.find('input[name=date]'),
					refreshSearchSection.find('input[name=toDate]'),
					refreshSearchSection.find('select[name=scu]'),
					{
						startDate: dateParam ? moment(dateParam.value, "YYYY-MM-DD") : new moment(),
						scu: scuParam ? scuParam.value : null,
						minLength: 1
					},
					productControl.controlConfiguration.date_config
				);

				if (productControl.controlConfiguration.qtyConfig == "roombased") {
					var qtyParam = productControl.GetSearchParam('qty');

					var options = [];
					for (var i = 1; i <= 10; i++) {
						options.push({
							label: i,
							value: _.times(i, function(x) { return productControl.controlConfiguration.default_room_type; }).join(',')
						});
					}

					// this.inputs.qty.html(TourplanRetailUtilities.GenerateOptionsString(options, false));
					$(productControl.rootElement).find("select[name=qty]").html(TourplanRetailUtilities.GenerateOptionsString(options, false)).val(qtyParam.value);


				}

				refreshSearchSection.find('button.refresh-search').click(function() {
					$(productControl.rootElement).find('.productInfoSection').html(TourplanRetailUtilities.loadingImage());
					var newQty;
					var qtySelectVal = refreshSearchSection.find('select[name=qty]').val();
					if (productControl.controlConfiguration.room_based) {
						var roomList = [];
						for (var i = 0; i < qtySelectVal; i++) {
							roomList.push(productControl.controlConfiguration.default_room_type);
						}
						newQty = roomList.join(',');
					}
					else {
						newQty = qtySelectVal;
					}

					var newSearchParams = _.map(productControl.GetAllSearchParams(), function(param) {
						switch(param.name) {
							case 'date':
								return {name:'date',value:dateControl.GetFromDate()};
							case 'scu':
								return (dateControl.GetSCU() ? {name:'scu',value:dateControl.GetSCU()} : param);
							case 'qty':
								return {name:'qty',value:newQty};
							default:
								return param;
						}
					});

					TourplanRetailUtilities.UpdateURLSearch($.param(newSearchParams));

					REI.Supplier(productControl.GetSearchParam("supplierid").value, _.map(newSearchParams), function(supplierData) {
						
						var supplier = new TourplanSupplier(supplierData);
						console.log(supplier);
						$(productControl.rootElement).find('.productInfoSection').empty();

						productControl.RenderSupplierProducts(supplier);
					});
				});
			}

			productControl.RenderSupplierProducts(supplier);
		});
	}

	else {
		REI.Product(productControl.GetSearchParam("productid").value, _.map(productControl.GetAllSearchParams()), function(productData) {
			dataLayer.push({
				'event':'gtm.dom',
				'eventCategory': 'Ecommerce',
				'eventAction': 'Impression',
				'currencyCode': $("#tourplanRetailConfig").attr("currency"),
				'ecommerce': {
				  		'impressions': _.map(productData.products, function(product, index) {
				  			return {
				  				'id': product.productid,
				  				'name': product.name,
				  				'position': index,
				  				'list': product.srb,
				  				'category': product.srb
				  			}
				  		})
				  	}
			});
		});

		if (productControl.controlConfiguration && !_.isEmpty(productControl.controlConfiguration.modify_search_config)) {
			var refreshSearchSection = $(productControl.rootElement).find('.refreshSearchSection');

			refreshSearchSection.html(productControl.templates.refreshSearchTemplate(
				productControl.controlConfiguration)
			);

			if (productControl.GetSearchParam('qtyConfig').value == 'roombased') {
				$(productControl.rootElement).find('select[name=qty]')
					.val(productControl.GetSearchParam('qty').value.split(',').length);
			}
			else if (productControl.GetSearchParam('qtyConfig').value == 'paxbased') {
				var qtys = TourplanRetailUtilities.ParseQty(productControl.GetSearchParam('qty').value);

				$(productControl.rootElement).find('select[name=adultQty]').val(qtys.adults + 'A');
				$(productControl.rootElement).find('select[name=childQty]').val(qtys.children + 'C');
				$(productControl.rootElement).find('select[name=infantQty]').val(qtys.infants + 'I');
			}

			var scuParam = productControl.GetSearchParam('scu');
			var dateParam = productControl.GetSearchParam('date');

			var initialSearchDate = new moment().add(parseInt(getServiceButtonConfig(controlConfiguration.service_button, "searchDateOffset")), "days");
			var minDate = productControl.controlConfiguration.date_config.min_date_type == "fixed" ? 
				new moment(productControl.controlConfiguration.date_config.min_date_val, "DD/MM/YYYY") : 
				new moment().add(parseInt(productControl.controlConfiguration.date_config.min_date_val), "days");
			var maxDate = productControl.controlConfiguration.date_config.max_date_type == "fixed" ? 
				new moment(productControl.controlConfiguration.date_config.max_date_val, "DD/MM/YYYY") : 
				new moment().add(parseInt(productControl.controlConfiguration.date_config.max_date_val), "days");

			if (minDate.isAfter(initialSearchDate)) {
				initialSearchDate = minDate;
			}

			var dateControl = new TourplanDateControl(
				refreshSearchSection.find('div.input-daterange'),
				refreshSearchSection.find('input[name=date]'),
				refreshSearchSection.find('input[name=toDate]'),
				refreshSearchSection.find('select[name=scu]'),
				{
					startDate: dateParam ? moment(dateParam.value, "YYYY-MM-DD") : new moment(),
					scu: scuParam ? scuParam.value : null,
					minLength: 1,
					minDate: minDate,
					maxDate: maxDate
				},
				productControl.controlConfiguration.date_config
			);

			// $(this.rootElement).find('select, input').change($.proxy(
			// function(sender) {
			// 	_.forEach(this.productControls, function(x) {
			// 		x.destinationElement.slideUp();
			// 	})
			// }, 
			// this));

			$(refreshSearchSection).find('select, input').change(function() {
				$(productControl.rootElement).find(".productInfoSection").slideUp();
			});

			console.log(refreshSearchSection);
			refreshSearchSection.find('button.refresh-search').click(function() {
				$(productControl.rootElement).find('.productInfoSection').html(TourplanRetailUtilities.loadingImage());
				
				var newQty;
				var qtySelectVal = refreshSearchSection.find('select[name=qty]').val();
				if (productControl.GetSearchParam('qtyConfig').value == 'roombased') {
					var roomList = [];
					for (var i = 0; i < qtySelectVal; i++) {
						roomList.push(productControl.controlConfiguration.default_room_type);
					}
					newQty = roomList.join(',');
				}
				else if (productControl.GetSearchParam('qtyConfig').value == 'paxbased') {
					newQty = _.map(
						$(productControl.rootElement).find('select[name=adultQty],select[name=childQty],select[name=infantQty]'),
						function(control) {
							return $(control).val();
						}).join('');
				}
				else {
					newQty = qtySelectVal;
				}


				var newSearchParams = _.map(productControl.GetAllSearchParams(), function(param) {
					switch(param.name) {
						case 'date':
							return {name:'date',value:dateControl.GetFromDate()};
						case 'scu':
							return (dateControl.GetSCU() ? {name:'scu',value:dateControl.GetSCU()} : param);
						case 'qty':
							return {name:'qty',value:newQty};
						default:
							return param;
					}
				});

				newSearchParams = _.filter(newSearchParams, function(param) { return param.name != 'supplierid' });

				TourplanRetailUtilities.UpdateURLSearch($.param(newSearchParams));


				if (productControl.controlConfiguration.supplier_based) {
					REI.Supplier(productControl.GetSearchParam("supplierid").value, _.map(newSearchParams), function(supplierData) {
						var supplier = new TourplanSupplier(supplierData);
						$(productControl.rootElement).find('.productInfoSection').empty();

						// dataLayer.push({
						// 	"event":"gtm.dom",
						// 	"eventCategory":"Ecommerce",
						// 	"eventAction":"Impression",
						// 	"currencyCode": $("#tourplanRetailConfig").attr("currency"),
						// 	"ecommerce": {
						// 		"impressions": _.map([], x => x);
						// 	}
						// })

						productControl.RenderSupplierProducts(supplier);
					});
				}
				else {
					REI.Product(productControl.GetSearchParam("productid").value, _.map(newSearchParams), function(productData) {
						
						var supplier = new TourplanSupplier(productData.supplier);
						var product = new TourplanProduct(productData.products[0]);

						productControl.RenderSingleProduct(supplier, product, product.LowestPricedAvailability());
					});
				}

			});
		}

		
		if (productControl.controlConfiguration.search_on_load) {
			if (productControl.controlConfiguration && productControl.controlConfiguration.modify_search_config) {
				refreshSearchSection.find("button.refresh-search").click();
			}
			else {
				if (productControl.controlConfiguration.supplier_based) {
					REI.Supplier(productControl.GetSearchParam("supplierid").value, _.map(newSearchParams), function(supplierData) {
						var supplier = new TourplanSupplier(supplierData);
						$(productControl.rootElement).find('.productInfoSection').empty();

						productControl.RenderSupplierProducts(supplier);
					});
				}
				else {
					REI.Product(productControl.GetSearchParam("productid").value, _.map(newSearchParams), function(productData) {
						
						var supplier = new TourplanSupplier(productData.supplier);
						var product = new TourplanProduct(productData.products[0]);

						productControl.RenderSingleProduct(supplier, product, product.LowestPricedAvailability());
					});
				}
			}
		}
	}
}

TourplanProductControl.prototype.RenderSupplierProducts = function(supplier) {
	var productControl = this;
	var productInfoSection = $(this.rootElement).find('.productInfoSection');
	productInfoSection.empty();
	_.forEach(supplier.products, function(product) {
		var avail = product.LowestPricedAvailability();
		productInfoSection.append(
			productControl.templates.productInfoTemplate({
				product:product,
				availability:avail
			})
		);
		var rateControl = $("." + TourplanRetailUtilities.ToSafeCSSName(avail.RateId));
		rateControl.find('button[name=book]').click(function(e) {
			CartInterface.addServiceLine(
				supplier.supplier,
				product,
				avail,
				{},
				function(x) { console.log(x); }
			)
		});
	});
	productInfoSection.slideDown();
}

TourplanProductControl.prototype.RenderSingleProduct = function(supplier, product, avail) {
	var productControl = this;
	var productInfoSection = $(this.rootElement).find('.productInfoSection');
	productInfoSection
		.empty()
		.html(productControl.templates.productInfoTemplate(product))
		.slideDown()
		.find('button[name=book]').click(function(e) {
			CartInterface.addServiceLine(
				supplier,
				product,
				avail,
				{},
				$.noop
			)
		});
}

/**
 *	R
 *	@param key
 *	@return {Boolean|Object} Name:Value pair of 
 */
TourplanProductControl.prototype.GetSearchParam = function(key) {
	var sources = [
		this.urlSearchParams,
		this.pageSearchParams,
		TourplanRetailUtilities.GetDefaultConfigs(this.controlConfiguration.srb)
	];
	var sourceNum = 0;
	var param = false;

	while (_.isEmpty(param) && sourceNum < sources.length) {
		param = _. find(sources[sourceNum], function(p) {
			return p.name == key;
		});
		sourceNum += 1;
	}

	return param;
}

TourplanProductControl.prototype.GetAllSearchParams = function() {
	var productControl = this;
	var searchParamKeys = ['supplierid', 'productid', 'date', 'scu', 'qty', 'info'];
	return _.filter(_.map(searchParamKeys, function(key) { return productControl.GetSearchParam(key); }), function(param) { return !_.isEmpty(param); });
}