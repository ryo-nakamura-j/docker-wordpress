<!-- tp-product-search-ctrl -->
<script>
	$(window).load(function() {
		var sectionConfig = <?php echo json_encode($this->sectionConfig); ?>;
		var vue = new Vue( {
			el: "#tp_search_section",
			data: {
				sectionConfig: sectionConfig,
				results: null,
				additionalProducts: null,
				datePickClasses: "search__date",
				inputData: {},
				jsDomInputList: {},
				jsDomAmenityFilters: {},
				dstValue: "",
				dstDefault: "Tokyo",
				dstList: [],
				dstKey: "label",
				dstLast: "UNINITIALIZED",
				dstSeparator: ",",
				isDstAutoHide: true,
			},
			mounted: function() {
				$("#tp_search_section").removeAttr('hidden');
				if ( typeof onVueMounted === "function" )
					onVueMounted();
			},
			computed: {
				dstDstPart: function() {
					var rlt = this.dstValue.split(this.dstSeparator);
					if ( rlt != null && rlt.length > 0 )
						return _.trim( rlt[0] );
					return "";
				},
				dstLclPart: function() {
					var rlt = this.dstValue.split(this.dstSeparator);
					if ( rlt != null && rlt.length > 1 )
						return _.trim( rlt[1] );
					return "";
				},
				dstIsValid: function() {
					if ( this.dstValue == "" )
						return false;
		            for( var i = 0; i < this.dstList.length; i++ ) {
		                var entry = this.dstList[i];
		                if ( true && entry[this.dstKey].toLowerCase() == this.dstValue.toLowerCase() )
		                        return true;
		                if ( entry[this.dstKey] == this.dstValue )
		                    return true;
		            }
		            return false;
		        }
			},
			methods: {
				dstOnChange: function() {
					var v = this;
					if ( this.dstLast == this.dstValue )
						return;
					this.dstLast = this.dstValue;

					var srb = this.jsDomInputList.srb.val();
					var cty = this.jsDomInputList.cty.val();
					var dst = this.dstValue;

					var mappings = TourplanRetailUtilities.GetLookupMap("SRBCTYDST", "DST", ['SRB=' + srb, 'CTY=' + cty])[0];

					this.dstList = [];
					var isDstDstPartMatching = false;
					var matchingDst = [];
					v.isDstAutoHide = v.sectionConfig.room_based ? false : true;
					// Construct Destination list without Locations
					_.map(mappings.codes, function(code) {
						v.dstList.push({label:code, value:code});
						if ( code == v.dstDstPart ) {
							isDstDstPartMatching = true;
							matchingDst.push( code );
						}
					});
					if ( isDstDstPartMatching && v.sectionConfig.room_based ) {
						// If input match one of the destination, construct destination-location list.
						v.dstList = [];
						v.isDstAutoHide = true;
						_.forEach( matchingDst, function(d) {
							v.dstList.push({label:d + ", " + "All", value:d + ", " + "All"});
							var lclMappings = TourplanRetailUtilities.GetLookupMap("SRBDSTLCL", "LCL", ['SRB=' + srb, 'DST=' + d])[0];
							if ( lclMappings ) {
								_.map(lclMappings.codes, function(c) {
									if ( !_.isEmpty( c ) )
										v.dstList.push({label:d + ", " + c, value:d + ", " + c});
								});
							}
						});
					}

					var amnMappings = TourplanRetailUtilities.GetLookupMap("SRBDSTAMN", "AMN", ["SRB=" + srb, "DST=" + dst])[0];

					if ( amnMappings ) {
						_.forEach(this.jsDomAmenityFilters, function(filter) {
							// Setup disable property
							$(filter.element).attr("disabled", !v.dstIsValid);
							filter.SetOptions(amnMappings.codes);
						});
					}
					else {
						_.forEach(this.jsDomAmenityFilters, function(filter) {
							// Setup disable property
							$(filter.element).attr("disabled", !v.dstIsValid);
							filter.Clear();
						});
					}
				}
			}
		})

		new TourplanProductSearchControl( $("#tp_search_panel")[0], vue);
	});

	TourplanProductSearchControl = function(rootElement, vueData) {
		var searchControl = this;

		this.rootElement = rootElement;
		this.searchConfigs = vueData.sectionConfig;
		this.urlSearchParams = TourplanRetailUtilities.DeserializeURLParameters(window.location.search[0] == "?" ? window.location.search.substr(1) : window.location.search);
		this.pageSearchParams = vueData.sectionConfig.search_config;

		this.inputs = {};
		// find all input items in on the page by name.
		_.forEach($(this.rootElement).find('input,select'), function(el) {
			if (!_.isEmpty(el.name)) {
				searchControl.inputs[el.name] = $(el);
				vueData.jsDomInputList[el.name] = $(el);
			}
		});
		// Init amenity filter
		this.amenityFilters = _.map($(this.rootElement).find('.tp_amenity_filter'), function(x) {
			return new TourplanAmenityFilter(x, x.name);
		});
		vueData.jsDomAmenityFilters = this.amenityFilters;
		// Init srb & cty
		this.inputs.srb.val(this.GetSearchParam('srb').value);
		this.inputs.cty.val(this.GetSearchParam('cty').value);
		// Init cty
		if (this.inputs.cty && this.inputs.cty.is('select')) {
			var srb = this.inputs.srb.val();

			var mappings = TourplanRetailUtilities.GetLookupMap("SRBCTY", "CTY", ['SRB=' + srb])[0];

			this.inputs.cty.empty();

			this.inputs.cty.html(
				TourplanRetailUtilities.GenerateOptionsString(
					_.map(mappings.codes, function(code) {
						return { label:code, value:code };
					}),
					true
				)
			);

			this.inputs.cty.val(this.GetSearchParam('cty').value);
		}

		// Init dst
		var srb = this.inputs.srb.val();
		var cty = this.inputs.cty.val();

		var mappings = TourplanRetailUtilities.GetLookupMap("SRBCTYDST", "DST", ['SRB=' + srb, 'CTY=' + cty])[0];

		vueData.dstList = [];
		_.map(mappings.codes, function(code) {
			vueData.dstList.push({label:code, value:code});
		});

		vueData.dstDefault = this.GetSearchParam('dst').value;
		if (!_.isEmpty(this.urlSearchParams) && !searchControl.searchConfigs.quicksearch) {
			// If it's a quick search. We'll need to construct destination-location structure for default value
			var lclDefault = this.GetSearchParam('lcl').value;
			if ( lclDefault )
				vueData.dstDefault += ", " + lclDefault;
		}
		vueData.dstOnChange();
		// ====
		var searchDateUrlParam = _.find(this.urlSearchParams, function(x) {
			return x.name == 'date';
		});
		// Init qty
		if (this.searchConfigs.room_based) {
			var options = [];
			for (var i = 1; i <= 10; i++) {
				options.push({
					label: i + " Rooms",
					value: _.times(i, function(x) { return searchControl.searchConfigs.default_room_type; }).join(',')
				});
			}
			this.inputs.qty.html(TourplanRetailUtilities.GenerateOptionsString(options, false));
		}

		this.inputs.qty.val(this.GetSearchParam('qty').value);
		$( this.inputs.qty ).selectmenu();

		// Init date control
		var scuParam = this.GetSearchParam('scu').value;
		var dateParam = this.GetSearchParam('date').value;

		this.dateControl = new TourplanDateControl(
			$(this.rootElement).find('.input-daterange'),
			$(this.rootElement).find('input[name=date]'),
			$(this.rootElement).find('input[name=toDate]'),
			$(this.rootElement).find('select[name=scu]'),
			{
				startDate: (dateParam ? moment(dateParam, 'YYYY-MM-DD') : new moment()),
				scu: scuParam ? scuParam : null,
				minLength: 1
			},
			this.searchConfigs.date_config,
			vueData.datePickClasses
		);
		// click logic
		$(this.rootElement).find('button[name=search]').click(function(e) {
			e.preventDefault();
			vueData.results = null;
			var searchP = searchControl.GetSearchParams( vueData );
			if (searchControl.searchConfigs.quicksearch) {
				var searchString = _.map(searchP, function(x) { return x.name + '=' + x.value; }).join('&');
				window.location.href = searchControl.searchConfigs.results_page + "?" + searchString;
			} else {
				// Get search params and save in vue data.
				//execute search
				REI.Availability(searchP, function(availData) {

					searchControl.results = [];

					if (searchControl.searchConfigs.supplier_level) {
						_.forEach(availData, function(supplier) {
							if ( supplier.supplier == null )
								return;
							var supplier = new TourplanSupplier(supplier);
							searchControl.results.push({
								supplier:supplier.supplier,
								product:supplier.LowestPricedProduct(),
								availability:new TourplanAvailability(supplier.LowestPricedProduct().LowestPricedAvailability()),
								destinationUrl:searchControl.searchConfigs.product_info_page
							});
						})
					} 
					else {
						_.forEach(availData, function(supplier) {
							if ( supplier.supplier == null )
								return;
							var supplier = new TourplanSupplier(supplier);
							_.forEach(supplier.products, function(product) {
								searchControl.results.push({
									supplier:supplier.supplier,
									product:product,
									availability:new TourplanAvailability(product.LowestPricedAvailability()),
									destinationUrl:searchControl.searchConfigs.product_info_page
								});
							});
						});
					}

					vueData.results = searchControl.results;

					if (searchControl.searchConfigs.date_range_config) {
						var searchParams = _.filter(searchControl.GetSearchParams(), function(x) {
							return x.name != 'date';
						});

						var searchDates = [];

						var days_before = parseInt(TourplanRetailUtilities.GetNameValuePair(searchControl.searchConfigs.date_range_config,'days_before').value);
						var days_after = parseInt(TourplanRetailUtilities.GetNameValuePair(searchControl.searchConfigs.date_range_config,'days_after').value);
						if (days_after > 0) {
							for (var i = 1; i <= days_after; i++) {
								searchDates.push(moment(searchControl.dateControl.GetFromDate()).add(i, 'days'));
							}
						}
						if (days_before > 0) {
							for (var i = 1; i <= days_before; i++) {
								searchDates.push(moment(searchControl.dateControl.GetFromDate()).subtract(i, 'days'));
							}
						}

						searchDates = _.map(searchDates, function(sd) {
							return _.clone(searchParams).concat([{name:'date',value:sd.format('YYYY-MM-DD')}]);
						});

						$.when.apply(
							$,
							_.map(searchDates, function(x) { return REI.Availability(x, null, null); })
						).done(function() {
							var additionalProducts = [];

							_.forEach(arguments, function(response) {

								if (searchControl.searchConfigs.supplier_level) {
									_.forEach(response[0], function(supplier) {
										var supplier = new TourplanSupplier(supplier);

										if (!_.find(additionalProducts, function(prod) {
											return prod.product.productid == product.productid;
										}) && !_.find(searchControl.results, function(prod) {
											return prod.product.productid == product.productid;
										})) {
											additionalProducts.push({
												supplier:supplier.supplier,
												product:supplier.LowestPricedProduct(),
												availability:new TourplanAvailability(supplier.LowestPricedProduct().LowestPricedAvailability()),
												destinationUrl:searchControl.searchConfigs.product_info_page
											});
										}
									});
								}
								else {
									_.forEach(response[0], function(supplier) {
										var supplier = new TourplanSupplier(supplier);

										_.forEach(supplier.products, function(product) {
											if (!_.find(additionalProducts, function(prod) {
												return prod.product.productid == product.productid;
											}) && !_.find(searchControl.results, function(prod) {
												return prod.product.productid == product.productid;
											})) 
											{
												additionalProducts.push({
													supplier:supplier.supplier,
													product:product,
													availability:new TourplanAvailability(product.LowestPricedAvailability()),
													destinationUrl:searchControl.searchConfigs.product_info_page
												});
											}
										});
									});
								}
							});
							vueData.additionalProducts = additionalProducts;
						});
					}
				},
				function() {
					vueData.results = null;
				});

			}
		});
		// auto search
		if (!_.isEmpty(this.urlSearchParams) && !searchControl.searchConfigs.quicksearch) {
			vueData.dstValue = vueData.dstDefault;
			$(this.rootElement).find('button[name=search]').click();
		}
	}

	TourplanProductSearchControl.prototype.GetSearchParams = function( vueData ) {
		var productControl = this;
		var inputData = $(this.rootElement).find('input:not(.tp_date),select:not(.tp_amenity_filter)').serializeArray();
		inputData.push({name:'date', value:this.dateControl.GetFromDate()});
		inputData.push({name:'info', value:'roomtypes'});
		inputData.push({name:'esm', value: this.searchConfigs.search_external_rate_availability ? 'E' : 'I' });
		inputData.push({name:'dst', value: vueData ? vueData.dstDstPart : ""});
		inputData.push({name:'lcl', value: vueData ? vueData.dstLclPart : ""});

		if (!_.isEmpty(this.amenityFilters)) {
			inputData.push({
				name:'amn', 
				value: _.filter(_.map(this.amenityFilters, 
						function(filter) { 
							return filter.GetSelected(); 
						}),
					function(x) { return !_.isEmpty(x); }).join(',')
			});
		}

		var required = ['scu','qty','date'];

		_.forEach(required, function(x) {
			if (!_.find(inputData, function(y) { return y.name == x; })) {
				inputData.push(productControl.GetSearchParam(x));
			}
		});

		inputData = _.filter(inputData, function(param) {
			return param.value != "All";
		});

		if ( vueData != null ) {
			vueData.inputData = {};
			_.forEach( inputData, function(o) {
				if ( o.name != null && o.value != null )
					vueData.inputData[o.name] = o.value;
			});
		}
		return inputData;
	}

	TourplanProductSearchControl.prototype.GetSearchParam = function(key) {
		var sources = [
			this.urlSearchParams,
			this.pageSearchParams
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

	TourplanProductSearchControl.prototype.GetQty = function() {
		if (this.searchConfigs.room_based) {
			var roomList = [];
			for (var i = 0; i < this.inputs.qty.val(); i++) {
				roomList.push(this.searchConfigs.default_room_type);
			}
			return roomList.join(',');
		}
		else {
			return this.inputs.qty.val();
		}
	}
</script>