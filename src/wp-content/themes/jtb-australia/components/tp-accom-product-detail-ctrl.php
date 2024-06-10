<!-- tp-product-detail-ctrl -->
<script>
	$(window).load(function() {

		var sectionConfig = <?php echo json_encode($this->sectionConfig); ?>;
		var themeUrl = '<?php echo get_template_directory_uri() ?>';

		var currency = '<?php echo get_option("tp_currency"); ?>';
		var hasProductImpressions = <?php echo ( isset($this->productImpressions) ? "true" : "false" ); ?>;
		var productImpressions = <?php echo json_encode($this->productImpressions); ?>;

		var vue = new Vue( {
			el: "#tp_detail_section",
			data: {
				sectionConfig: sectionConfig,
				supplier: null,
				productList: null,
				productWrapper: null,
				hasRefreshSearchSection: false,
				error: null,
			},
			mounted: function() {
				$("#tp_detail_section").removeAttr('hidden');
				if ( typeof onVueMounted === "function" ) {
					onVueMounted();
				}
			},
			methods: {
				getProductPaxCount: function(p){
					if ( p == null || p.availability == null || p.availability.length == 0 )
						return {
							adults: this.dataListLabel("not_available", "Not Available"),
							children: this.dataListLabel("not_available", "Not Available"),
							infants: this.dataListLabel("not_available", "Not Available"),
						};
					return {
						adults: (p.availability[0].Qty.match(/[0-9]*(?=A)/))[0],
						children: (p.availability[0].Qty.match(/[0-9]*(?=C)/))[0],
						infants: (p.availability[0].Qty.match(/[0-9]*(?=I)/))[0],
					};
				},
				getBedImage: function ( roomtype ) {
					if ( roomtype == "sg" )
						return themeUrl + "/templates/img/result/bed_single.svg";
					else if ( roomtype == "tw" || roomtype == "db" )
						return themeUrl + "/templates/img/result/bed_double.svg";
					return "";
				},
			},
		})

		if ( hasProductImpressions ) {
			dataLayer.push({
				'event':'gtm.dom',
				'eventCategory':'Ecommerce',
				'eventAction':'Impression',
				'currencyCode':currency,
				'ecommerce': {
					'impressions': productImpressions
				}
			});
		}

		new TourplanProductControl( $("#tp_detail_panel")[0], vue );
	});


	TourplanProductControl = function(rootElement, vueData) {
		_.extend(this, new TourplanCommonControl(rootElement, null, null));

		var productControl = this;
		this.vueData = vueData;

		this.urlSearchParams = TourplanRetailUtilities.DeserializeURLParameters(window.location.search[0] == "?" ? window.location.search.substr(1) : window.location.search);

		this.pageSearchParams = vueData.sectionConfig.static_config;

		if (vueData.sectionConfig.supplier_based) {
			if ( productControl.GetSearchParam("supplierid") == null ) {
				vueData.error = "Supplier not found";
				return;
			}

			REI.Product(
				productControl.GetSearchParam("supplierid").value, 
				_.map( productControl.GetAllSearchParams() ), 
				function(supplierData) {
					if ( supplierData == null ) {
						vueData.error = "Product data not found for Google Analytics";
						return;
					}
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
					  			};
					  		})
					  	}
					});
				}
			);

			var initialSearchDate = new moment().add(parseInt(getServiceButtonConfig(vueData.sectionConfig.service_button, "searchDateOffset")), "days");
			var minDate = vueData.sectionConfig.date_config.min_date_type == "fixed" ? 
				new moment(vueData.sectionConfig.date_config.min_date_val, "DD/MM/YYYY") : 
				new moment().add(parseInt(vueData.sectionConfig.date_config.min_date_val), "days");
			var maxDate = vueData.sectionConfig.date_config.max_date_type == "fixed" ? 
				new moment(vueData.sectionConfig.date_config.max_date_val, "DD/MM/YYYY") : 
				new moment().add(parseInt(vueData.sectionConfig.date_config.max_date_val), "days");

			if (minDate.isAfter(initialSearchDate)) {
				initialSearchDate = minDate;
			}

			REI.Supplier(productControl.GetSearchParam("supplierid").value, this.GetAllSearchParams(), function(supplierData) {
				var supplier = new TourplanSupplier(supplierData);
				vueData.supplier = supplier.supplier;

				if (vueData.sectionConfig && !_.isEmpty(vueData.sectionConfig.modify_search_config)) {
					vueData.hasRefreshSearchSection = true;

					Vue.nextTick(function() {
						var refreshSearchSection = $(productControl.rootElement).find('#refreshSearchSection');

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
							vueData.sectionConfig.date_config
						);

						if (vueData.sectionConfig.qtyConfig == "roombased") {
							var qtyParam = productControl.GetSearchParam('qty');

							var options = [];
							for (var i = 1; i <= 10; i++) {
								options.push({
									label: i,
									value: _.times(i, function(x) { return vueData.sectionConfig.default_room_type; }).join(',')
								});
							}

							$(productControl.rootElement).find("select[name=qty]").html(TourplanRetailUtilities.GenerateOptionsString(options, false)).val(qtyParam.value);
						}

						refreshSearchSection.find('button.refresh-search').click(function(e) {
							e.preventDefault();
							vueData.productList = null;

							var newQty;
							var qtySelectVal = refreshSearchSection.find('select[name=qty]').val();
							if (vueData.sectionConfig.room_based) {
								var roomList = [];
								for (var i = 0; i < qtySelectVal; i++) {
									roomList.push(vueData.sectionConfig.default_room_type);
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

								productControl.RenderSupplierProducts(supplier);
							});
						});
				    });
				}

				productControl.RenderSupplierProducts(supplier);
			});
		}

		else {
			vueData.hasRefreshSearchSection = true;
			REI.Product(productControl.GetSearchParam("productid").value, _.map(productControl.GetAllSearchParams()), function(productData) {
				if ( productData ){
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
				}
			});

			if (vueData.sectionConfig && !_.isEmpty(vueData.sectionConfig.modify_search_config)) {

				Vue.nextTick(function() {
					var refreshSearchSection = $(productControl.rootElement).find('#refreshSearchSection');

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

					var initialSearchDate = new moment().add(parseInt(getServiceButtonConfig(vueData.sectionConfig.service_button, "searchDateOffset")), "days");
					var minDate = vueData.sectionConfig.date_config.min_date_type == "fixed" ? 
						new moment(vueData.sectionConfig.date_config.min_date_val, "DD/MM/YYYY") : 
						new moment().add(parseInt(vueData.sectionConfig.date_config.min_date_val), "days");
					var maxDate = vueData.sectionConfig.date_config.max_date_type == "fixed" ? 
						new moment(vueData.sectionConfig.date_config.max_date_val, "DD/MM/YYYY") : 
						new moment().add(parseInt(vueData.sectionConfig.date_config.max_date_val), "days");

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
						vueData.sectionConfig.date_config
					);

					$(refreshSearchSection).find('select, input').change(function() {
						$(productControl.rootElement).find(".productInfoSection").slideUp();
					});

					refreshSearchSection.find('button.refresh-search').click(function(e) {
						e.preventDefault();
						vueData.productList = null;
						
						var newQty;
						var qtySelectVal = refreshSearchSection.find('select[name=qty]').val();
						if (productControl.GetSearchParam('qtyConfig').value == 'roombased') {
							var roomList = [];
							for (var i = 0; i < qtySelectVal; i++) {
								roomList.push(vueData.sectionConfig.default_room_type);
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

						productControl.LoadProducts( newSearchParams );

					});

					if (vueData.sectionConfig.search_on_load) {
						if (vueData.sectionConfig && vueData.sectionConfig.modify_search_config) {
							refreshSearchSection.find("button.refresh-search").click();
						}
						else {
							productControl.LoadProducts( newSearchParams );
						}
					}
				});
			}
		}
	}

	TourplanProductControl.prototype.LoadProducts = function( newSearchParams ) {
		var productControl = this;
		var vueData = this.vueData;

		if (vueData.sectionConfig.supplier_based) {
			REI.Supplier(productControl.GetSearchParam("supplierid").value, _.map(newSearchParams), function(supplierData) {

				var supplier = new TourplanSupplier(supplierData);

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

	TourplanProductControl.prototype.BuildProduct = function( supplier, p, rateControlId, buttonId ) {
		Vue.nextTick( function() {
			var rateControl = $( rateControlId );
			rateControl.find( buttonId ).click(function(e) {
				if ( p.isWaiting )
					return;
				p.isWaiting = true;
				CartInterface.addServiceLine(
					supplier,
					p.product,
					p.availability,
					{},
					$.noop
				)
			});
		});
	}

	TourplanProductControl.prototype.RenderSupplierProducts = function(supplier) {
		var productControl = this;
		var vueData = this.vueData;

		vueData.productList = [];
		_.forEach(supplier.products, function(product) {
			var avail = product.LowestPricedAvailability();
			var p = { product: product, availability: avail, isWaiting: false};
			vueData.productList.push( p );
			productControl.BuildProduct( supplier.supplier, p, 
				"#" + TourplanRetailUtilities.ToSafeCSSName(p.availability.RateId), 
				"a[name=book]" );
		});

		var productInfoSection = $(this.rootElement).find('.productInfoSection');
		productInfoSection.slideDown();
	}

	TourplanProductControl.prototype.RenderSingleProduct = function(supplier, product, avail) {
		var productControl = this;
		var vueData = this.vueData;

		var p = { product: product, availability: avail, isWaiting: false };
		vueData.productWrapper = p;

		productControl.BuildProduct( supplier, p, "#productInfoSection", "button[name=book]" );

		var productInfoSection = $(this.rootElement).find('.productInfoSection');
		productInfoSection.slideDown();
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
			TourplanRetailUtilities.GetDefaultConfigs(this.vueData.sectionConfig.srb)
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
</script>