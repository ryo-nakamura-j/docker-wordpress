<!-- tp-checkout-ctrl -->
<script>
	$(window).load(function() {

		var sectionConfig = <?php echo json_encode($this->sectionConfig); ?>;
		var itinProducts = <?php echo json_encode($itinProducts); ?>;

		var vue = new Vue( {
            mixins: [tpCachedData],
			el: "#tp_itinerary_section",
			data: {
				sectionConfig: sectionConfig,
				cart_price: "",
				internal_serviceLineList: [],
				deletedServiceLineUniqueIdList: [],
				minMaxPref1DateObjMap: {},
			},
			mounted: function() {
				if ( typeof onVueMounted === "function" ) {
					onVueMounted();
				}
				$('html').animate({
				    scrollTop: $("html").offset().top
				 }, 300);
				// Hide shopping-cart item count on itinerary page
				Vue.nextTick( function() {
					if ( headerVueData )
						headerVueData.itineraryItemCount = 0;
				});
			},
			computed: {
				isViewMobile: function() {
					return this.helper.isMobile();
				},
				originalServiceLineList: {
					// Since Vue will re-render the servicelines, if a serviceline is deleted, we'll lose changes made by JS DOM midification. The solution here is to keeping track of a list of deleted servicelines and hide them, rather than delete them from the list.
					get: function() {
						return this.internal_serviceLineList;
					},
					set: function(v) {
						this.internal_serviceLineList = v;
					}
				},
				displayServiceLineList: {
					get: function() { 
						var vm = this;
						var rlt = [];
						_.forEach( vm.originalServiceLineList, function(sl, idx) {
							if ( !vm.isDeletedServiceLine(sl) )
								rlt.push( sl );
						});
						return rlt; 
					}
				},
			},
			methods: {
				sectionHeaderClassLayout1Check: function( serviceType ) {
					return this.helper.serviceButtonConfigContains(serviceType, 'cartSections', 'paxCount') &&
					!this.helper.serviceButtonConfigContains(serviceType, 'cartSections', 'roomCount') &&
					!this.helper.serviceButtonConfigContains(serviceType, 'cartSections', 'productDate');
				},
				sectionHeaderClassLayout2Check: function( serviceType ) {
					return this.helper.serviceButtonConfigContains(serviceType, 'cartSections', 'paxCount') &&
					!this.helper.serviceButtonConfigContains(serviceType, 'cartSections', 'roomCount') &&
					this.helper.serviceButtonConfigContains(serviceType, 'cartSections', 'productDate');
				},
				sectionHeaderClassLayout3Check: function( serviceType ) {
					return !this.helper.serviceButtonConfigContains(serviceType, 'cartSections', 'paxCount') &&
					!this.helper.serviceButtonConfigContains(serviceType, 'cartSections', 'roomCount') &&
					this.helper.serviceButtonConfigContains(serviceType, 'cartSections', 'productDate');
				},
				sectionHeaderClassProductName: function( serviceType ) { 
					if ( this.sectionHeaderClassLayout1Check( serviceType ) )
						return "col-xs-12 col-sm-8";
					else if ( this.sectionHeaderClassLayout2Check( serviceType ) )
						return "col-xs-12 col-md-6";
					else if ( this.sectionHeaderClassLayout3Check( serviceType ) )
						return "col-xs-12 col-md-7";
					return "col-xs-12 col-md-6";
				},
				sectionHeaderClassPaxConfig: function( serviceType ) { 
					if ( this.sectionHeaderClassLayout1Check( serviceType ) )
						return "col-xs-12 col-sm-4 col-md-2";
					else if ( this.sectionHeaderClassLayout2Check( serviceType ) )
						return "col-xs-12 col-sm-4 col-md-4";
					else if ( this.sectionHeaderClassLayout3Check( serviceType ) )
						return "col-xs-12 col-sm-4 col-md-3";
					return "col-xs-12 col-sm-4 col-md-4";
				},
				sectionHeaderClassServicePrice: function( serviceType ) { 
					return "col-xs-12 col-sm-4 col-md-2";
				},
				isValidated: function( successCallback, failedCallback ) {
					this.$validator.validateAll().then( function(result) {
						if ( !result ) {
							failedCallback();
							return;
						}
						successCallback();
					});
				},
				isDeletedServiceLine: function( sl ) {
					rlt = false;
					_.forEach( this.deletedServiceLineUniqueIdList, function(id) {
						if ( sl.serviceline.unique_ui_id == id )
							rlt = true;
					});
					return rlt;
				},
				deleteServiceLine: function( sl ) {
					var vm = this;
					this.deletedServiceLineUniqueIdList.push( sl.serviceline.unique_ui_id );
					// Trigger re-render
					vm.originalServiceLineList = _.cloneDeep(vm.originalServiceLineList);
				},
				afterLoading: function() {
					$("#tp_itinerary_section").removeAttr('hidden');
					this.initMagnetEffect();
				},
				onHeightChanged: function() {
					this.initMagnetEffect();
				},
				initMagnetEffect: function() {
					// Fix scroll-magnet effect init issue
					if ( !this.isViewMobile ) {
						this.$refs.tpScrollMagnet.init();
					}
				},
				collapseAll: function() {
					var vm = this;
					for( var i = 0; i < vm.displayServiceLineList.length; i++ ) {
						if ( vm.$refs['vueServiceLine' + i] && vm.$refs['vueServiceLine' + i].length > 0 )
							vm.$refs['vueServiceLine' + i][0].collapseClose();
					}
					$("#servicelineSectionHeight")[0].scrollIntoView();
				},
				expandAll: function() {
					var vm = this;
					for( var i = 0; i < vm.displayServiceLineList.length; i++ ) {
						if ( vm.$refs['vueServiceLine' + i] && vm.$refs['vueServiceLine' + i].length > 0 )
							vm.$refs['vueServiceLine' + i][0].collapseOpen();
					}
					$("#servicelineSectionHeight")[0].scrollIntoView();
				},
				onEdit: function( parentControl, servicelineControl ) {
					if ( !servicelineControl.serviceline.source_url )
						return;
					this.onRemove( parentControl, servicelineControl, function() {
						console.log( "Redirect to booking page" );
						window.location.href = servicelineControl.serviceline.source_url;
					} );
				},
				onRemove: function( parentControl, servicelineControl, callback ) {
					var vm = this;
					vm.initMagnetEffect();
					$("#servicelineSectionHeight")[0].scrollIntoView();
					$(parentControl.rootElement).mask("");

					dataLayer.push({
						'event':'removeFromCart',
						'eventCategory':'Ecommerce',
						'eventAction':'removeFromCart',
						'ecommerce': {
							'remove': {
								'products': [{
									'name':servicelineControl.serviceline.productname,
									'id':servicelineControl.serviceline.productid,
									'price':servicelineControl.serviceline.pricedisplay,
									'category':servicelineControl.serviceline.servicetype,
									'quntity':servicelineControl.serviceline.qtyNum
								}]
							}
						}
					});

					vm.deleteServiceLine( servicelineControl );
					CartInterface.cart.servicelines = vm.getServiceLineListForCart();
					CartInterface.PushCart({
						success: function(d) { 
							if (CartInterface.cart.servicelines.length < 1) {
								$(".navbar-mobile .mobile-cart").slideUp();
							}
							CartInterface.RepriceCart("default", function(e) {
								parentControl.UpdateServicelines(e);
								parentControl.UpdatePrice(e.price);
								$(parentControl.rootElement).unmask();
								if ( callback )
									callback();
							})	
						}
					});
				},
				getServiceLineListForCart: function() {
					var vm = this;
					var rlt = _.cloneDeep( _.map(vm.displayServiceLineList, 'serviceline') );
					_.forEach( rlt, function(sl) {
						_.forEach( sl.configs, function(c){
							_.forEach( c.pax, function(p) {
								// Remove large key-value pairs that should not sent to retail engine
								delete p["pikadayOptions"];
							});
						});
					});
					return rlt;
				},
				setupDepartureDate: function() {
					var vm = this;
					_.forEach( vm.displayServiceLineList, function( slc, slIndex) {
						var departureDateOffset = getServiceButtonConfig(slc.serviceline.servicetype, "departureDateOffset");
						var departureDateMaxOffset = getServiceButtonConfig(slc.serviceline.servicetype, "departureDateMaxOffset");

						// minMaxPref1DateObj contains extended prototypes, which will mess up with vue.
						var minMaxPref1DateObj = {};
						if (departureDateOffset != undefined && parseInt(departureDateOffset) != NaN) {
							var minDate = new moment().add(parseInt(departureDateOffset), "days");
							minMaxPref1DateObj["minDate"] = minDate;
							minMaxPref1DateObj["pikadayOptions"] = {
								defaultDate:minDate.toDate()
							};
						}
						if (departureDateMaxOffset != undefined && parseInt(departureDateMaxOffset) != NaN) {
							minMaxPref1DateObj["maxDate"] = new moment().add(parseInt(departureDateMaxOffset), "days");
						}
						var tmptmp = {};
						tmptmp[slc.serviceline.unique_ui_id] = minMaxPref1DateObj;
						vm.minMaxPref1DateObjMap = _.extend( vm.minMaxPref1DateObjMap, tmptmp );
					});
				}
			}
		})

		dataLayer.push({
			'ecommerce': {
			  	'detail': itinProducts
			}
		});

		new TourplanItineraryControl( $("#tp_itinerary_panel")[0], vue );
	});


	TourplanItineraryControl = function(rootElement, vueData) {
		_.extend(this, new TourplanCommonControl(rootElement, null, null));

		var vm = this;
		var parentControl = this;
		this.vueData = vueData;

		this.configs = vueData.sectionConfig;


		CartInterface.GetCart(true, function(data) {
			vueData.afterLoading();

			vueData.cart_price = getServiceButtonConfig("", "cartPricePrefix") + (CartInterface.TotalPrice() / 100).toFixed(2);

			vueData.originalServiceLineList = [];
			if (data.servicelines) {
				_.forEach(data.servicelines, function(sl, idx) { 
					// For Dom modification
					var jsServicelineId = 'serviceline-input-' + idx;
					vueLineData = {
						serviceline: sl,
						jsServicelineId: jsServicelineId,
						qtyConfig: getServiceButtonConfig(sl.servicetype, 'qtyConfig'),
						pikaObjects: [],
						service_price: getServiceButtonConfig(sl.srb, 'productPricePrefix') + templatesHelper.displayPrice( sl.price, 2 ),
						bookingFeeLabel: templatesHelper.bookingFeeLabel( sl )
					};
					_.forEach( sl.configs, function(c, ii) {
						// For Dom modification
						// Index of room config start from 1
						c.jsRoomConfigId = jsServicelineId + '-room-' + (ii + 1) 
					});
					if ( sl.configs.length > 0 && sl.configs[0] != null ) {
						_.forEach( sl.configs[0].pax, function(p, ii ) {
							// For Dom modification
							// Index of room config start from 1
							p.jsPaxConfigId = jsServicelineId + '-pax-' + (ii + 1 );
							// Regist other input fields
							p.dob = "";
							p.firstname = "";
							p.lastname = "";
							p.firstnamelang = "";
							p.lastnamelang = "";
							p.middlename = "";
							p.nationality = "";
							p.passport = "";
							p.title = "";
							p.pikadayOptions = null;
						});
					}
					vueData.originalServiceLineList.push( vueLineData );
				});

				vueData.setupDepartureDate();

				Vue.nextTick( function() {
					_.forEach(vueData.displayServiceLineList, function(servicelineControl, slIndex) {

						var slc = parentControl.RenderServiceLine( servicelineControl );

						slc.find('button[name=remove]').click(function(e) {
							vueData.onRemove( parentControl, servicelineControl );
						});

						slc.find('button[name=edit]').click(function(e){
							vueData.onEdit( parentControl, servicelineControl );
						});

						slc.find('button[name=update]').click(function(e) {
							parentControl.Validate( successCallback, function() {
								var errs = $(".has-error, .duplicate");
								if (errs.length > 0) {
									errs[0].scrollIntoView();
								}
							}); 
							function successCallback() {
								$(parentControl.rootElement).mask("");

								var paxChanged = false;
								_.forEach(vueData.displayServiceLineList, function(slc) {
									// compare DOB with max DOB
									if (_.has(slc.serviceline, 'ageLimits')) {
										var dobCheckDate;
										if (getServiceButtonConfig(slc.serviceline.servicetype, "useDepartureDate") == "true") {
											dobCheckDate = slc.serviceline.preference1;
										}
										else {
											dobCheckDate = slc.serviceline.date;
										}
										_.forEach(slc.serviceline.configs[0].pax, function(pax) {
											var pType = TourplanRetailUtilities.PaxTypeFromDob(
												moment(pax.dob, TourplanRetailUtilities.DATEFORMATS.DATA), 
												slc.serviceline.ageLimits,
												moment(dobCheckDate, TourplanRetailUtilities.DATEFORMATS.DATA)
											);

											if (pax.paxtype != pType) {
												paxChanged = true;
												pax.paxtype = pType;
											}

										});
									}

									if (paxChanged) {

										var newAdults = _.filter(slc.serviceline.configs[0].pax, function(p) { return p.paxtype == 'A'; }).length;
										var newChildren = _.filter(slc.serviceline.configs[0].pax, function(p) { return p.paxtype == 'C'; }).length;
										var newInfants = _.filter(slc.serviceline.configs[0].pax, function(p) { return p.paxtype == 'I'; }).length;

										slc.serviceline.configs[0].adults = newAdults;
										slc.serviceline.configs[0].children = newChildren;
										slc.serviceline.configs[0].infants = newInfants;

										slc.serviceline.qty = newAdults + 'A' + newChildren + 'C' + newInfants + 'I';

									}

									if (getServiceButtonConfig(slc.serviceline.servicetype, "useDepartureDate") == "true") {
										slc.serviceline.date = slc.serviceline.preference1;
										slc.serviceline.datein = new moment(slc.serviceline.date, TourplanRetailUtilities.DATEFORMATS.DATA).format("ddd DD MMM YYYY");
										slc.serviceline.dateout = new moment(slc.serviceline.date, TourplanRetailUtilities.DATEFORMATS.DATA).add(slc.serviceline.scu, "days").format("ddd DD MMM YYYY");
									}
								});
								CartInterface.cart.servicelines = vm.vueData.getServiceLineListForCart();

								CartInterface.PushCart({
									success:function() {
										CartInterface.RepriceCart("default", function(e) {
											parentControl.UpdateServicelines(e);
											parentControl.UpdatePrice(e.price);
											$(parentControl.rootElement).unmask();
											if (paxChanged) {
												alert("Pricing may have changed");
											}
										})
									}
								});
							}
						});
					});
				})

				var groupedServicelines = _.groupBy(
				 	_.map(
				 		vueData.displayServiceLineList, 
				 		function(x) { return  x.serviceline }
				 	), 
				 	function(y) { return y.servicetype }
				);

				_.forEach(groupedServicelines, function(value, key) {
					dataLayer.push({
						'event': key + 'DetailProduct',
						'eventCategory': 'Ecommerce', 
						'eventAction': 'detail',
						'ecommerce': {
							'detail': {
								'actionField' : {'list' : key},
								'products' : _.map(value, function(x) {
									return {
										'id' : x.productid,
										'name' : x.productname,
										'price' : x.pricedisplay,
										'category' : x.servicetype,
										'qty' : x.qty
									}
								})
							}
						}
					})
				});


				dataLayer.push({
					'event': 'addToCart',
					'eventCategory': 'Ecommerce', 'eventAction': 'addtoCart',
					'ecommerce': {
						'currencyCode': $("#tourplanRetailConfig").attr("currency"),
					  	'add': {
					    	'products': _.map(vueData.displayServiceLineList, function(servicelineControl) {
					    		return {
									'id' : servicelineControl.serviceline.productid,
									'name' : servicelineControl.serviceline.productname,
									'price': servicelineControl.serviceline.pricedisplay,
									'quantity': servicelineControl.serviceline.qtyNum,
									'category': servicelineControl.serviceline.servicetype,
									'list': servicelineControl.serviceline.servicetype
					    		}
					    	})
						}
					}
				});

				$(parentControl.rootElement).find('button[name=checkout]').click(function() {
					parentControl.Validate( successCallback, function() {
						var errs = $(".has-error");
						if ( errs.length > 0 ) 
							errs[0].scrollIntoView();
					});
					function successCallback() {
						_.forEach(vueData.displayServiceLineList, function(slc) {
							if (getServiceButtonConfig(slc.serviceline.servicetype, "useDepartureDate") == "true") {
								slc.serviceline.date = slc.serviceline.preference1;
								slc.serviceline.datein = new moment(slc.serviceline.date, TourplanRetailUtilities.DATEFORMATS.DATA).format("ddd DD MMM YYYY");
								slc.serviceline.dateout = new moment(slc.serviceline.date, TourplanRetailUtilities.DATEFORMATS.DATA).add(slc.serviceline.scu, "days").format("ddd DD MMM YYYY");
							}
						});
						CartInterface.cart.servicelines = vm.vueData.getServiceLineListForCart();
						$(parentControl.rootElement).mask("");
						CartInterface.PushCart({
							success: function() { 
								dataLayer.push({
									'event':'checkout',
									'eventCategory':'Ecommerce',
									'eventAction':'Checkout',
									'ecommerce': {
										'checkout': {
											'actionField': {
												'step':1,
												'option':'Cart'
											},
											'products':_.map(CartInterface.cart.servicelines, function(sl) {
												return {
													'id': sl.productid,
													'name':sl.productname,
													'price':sl.pricedisplay,
													'quantity':sl.quantity,
													'category':sl.servicetype,
													'list':sl.servicetype
												}
											})
										}
									}
								});
								window.location.href = $("#tourplanRetailConfig").attr("checkoutpage"); 
							}
						});
					}
				});

				$(parentControl.rootElement).find('button[name=empty]').click(function() {
					dataLayer.push({
						'event':'removeFromCart',
						'eventCategory':'Ecommerce',
						'eventAction':'removeFromCart',
						'ecommerce': {
							'remove': {
								'products': _.map(vueData.displayServiceLineList, function(slc) {
									return {
										'name': slc.serviceline.productname,
										'id':slc.serviceline.productid,
										'price':slc.serviceline.pricedisplay,
										'category':slc.serviceline.servicetype,
										'quantity':slc.serviceline.qtyNum
									}
								})
							}
						}
					});
					CartInterface.cart.servicelines = [];
					vueData.originalServiceLineList = [];
					$(parentControl.rootElement).mask("");
					CartInterface.PushCart({
						success: function(d) { 
							vueData.initMagnetEffect();
							$(".navbar-mobile .mobile-cart").slideUp();
							CartInterface.RepriceCart("default", function(e) {
								parentControl.UpdatePrice(e.price);
								$(parentControl.rootElement).unmask();
							})	
						}
					});
				});

				$(parentControl.rootElement).mask("");
				CartInterface.RepriceCart("default", function(e) {
					vueData.afterLoading();
					parentControl.UpdateServicelines(e);
					parentControl.UpdatePrice(e.price);
					$(parentControl.rootElement).unmask();
				});
			}
		});
	}

	TourplanItineraryControl.prototype.ServicelineValidate = function(slWrapper) {
		var elementId = "#" + slWrapper.jsServicelineId;
		var view = $( elementId );
		var vm = this;

		var paxNames = [];

		var valid = true;
		_.forEach($(view).find('.required'), function(element) {
			var childrenValid = true;
			_.forEach($(element).find('select,input'), function(subElement) {
				if (_.isEmpty($(subElement).val()) || ( $(subElement)[0].type == 
					"checkbox" && $(subElement)[0].checked == false ) ) {
					childrenValid = false;
				}
			})
			if (!childrenValid) {
				valid = false;
				$(element).addClass('has-error');
				$(element).closest('.collapse').addClass('in');
			} else {
				$(element).removeClass('has-error');
			}
		});

		var serviceDate = moment(slWrapper.serviceline.date, TourplanRetailUtilities.DATEFORMATS.DATA);

		_.forEach($(view).find(".passenger"), function(passenger, index) {

			$(passenger).removeClass("duplicate");

			paxNames.push({
				firstname: $(passenger).find("input[name=firstname]").val(),
				lastname: $(passenger).find("input[name=lastname]").val(),
				firstnamelang: $(passenger).find("input[name=firstnamelang]").val(),
				lastnamelang: $(passenger).find("input[name=lastnamelang]").val(),
				title: $(passenger).find("select[name=title]").val()
			});

			if (_.has(slWrapper.serviceline, 'ageLimits')) {
				var dobElement = $(passenger).find("input[name=dob]");
				var dob = moment($(passenger).find("input[name=dob]").val(), TourplanRetailUtilities.DATEFORMATS.DATA);
				
				var paxtype = slWrapper.serviceline.configs[0].pax[index].paxtype;

				var minDate, maxDate;
				// "useDepartureDate" is only expeted to be used by "Rail" type service at current stage.
				// However, we should not hard coded any service types, because they are expected to be expandable.
				if(getServiceButtonConfig(slWrapper.serviceline.servicetype, 'useDepartureDate') == "true") {
					var preference1 = moment(slWrapper.serviceline.preference1, TourplanRetailUtilities.DATEFORMATS.DATA);
					if ( slWrapper.serviceline.ageLimits ) {
						minDate = preference1.clone().subtract((slWrapper.serviceline.ageLimits[paxtype].max + 1), 'years').add(1, 'days');
						maxDate = preference1.clone().subtract(slWrapper.serviceline.ageLimits[paxtype].min, 'years');
					}
				}
				else {
					if ( slWrapper.serviceline.ageLimits ) {
						minDate = serviceDate.clone().subtract((slWrapper.serviceline.ageLimits[paxtype].max + 1), 'years').add(1, 'days');
						maxDate = serviceDate.clone().subtract(slWrapper.serviceline.ageLimits[paxtype].min, 'years');
					}
				}
		
				if (!dob.isBetween(minDate, maxDate, null, '[]') || _.isEmpty(dobElement.val())) {
					$(dobElement).closest('.required').addClass('has-error');
					valid = false;
				}
				else {
					$(dobElement).closest('.required').removeClass('has-error');
				}
			}
		});

		var pref1Element = $(view).find("input[name=preference1]");
		var pref1 = moment($(pref1Element).val(), TourplanRetailUtilities.DATEFORMATS.DATA);
		var minMaxPref1DateObj = vm.vueData.minMaxPref1DateObjMap[slWrapper.serviceline.unique_ui_id];
		if (pref1Element.length > 0 && (!pref1.isValid() || !pref1.isBetween(minMaxPref1DateObj.minDate.clone().subtract(1, 'days'), minMaxPref1DateObj.maxDate, null, '[]'))) {
			$(pref1Element).closest('.required').addClass('has-error');
			valid = false;
		} else {
			$(pref1Element).closest('.required').removeClass('has-error');
		}

		/// Check for duplicate name
		var uniquePax = _.uniq(paxNames, function(p) { return JSON.stringify([p.firstname, p.lastname, p.title])});
		var duplicatePax = _.difference(paxNames, uniquePax);
		valid = valid && _.isEmpty(duplicatePax);

		if (!_.isEmpty(duplicatePax)) {
			var duplicatePaxElements = [];
			_.forEach(duplicatePax, function(dp, idx) {

				duplicatePaxElements.push($(view).find(".passenger").filter(function(idx, p) {
					return $(p).find("select[name=title]").val() == dp.title
							&& $(p).find("input[name=firstname]").val() == dp.firstname
							&& $(p).find("input[name=lastname]").val() == dp.lastname
				}));
			});

			_.forEach(duplicatePaxElements, function(el) {
				$(el).addClass("duplicate");
			});

			alert(getServiceButtonConfig("", "duplicatePaxLabel", "Each passenger can only be entered once."));

		}

		return valid;
	}

	TourplanItineraryControl.prototype.ServicelineUpdatePrice = function(vueLineData, price, bookingFeePrice, bookingFeeLabel) {
		if ( bookingFeePrice == null )
			bookingFeePrice = 0;
		if ( bookingFeeLabel == null )
			bookingFeeLabel = "";
		var servicelineControl = this;
		vueLineData.serviceline.price = price;
		vueLineData.serviceline.pricedisplay = ( parseInt( price + bookingFeePrice ) / 100).toFixed(2);
		vueLineData.serviceline.pricebookingfee = bookingFeePrice;
		vueLineData.service_price = getServiceButtonConfig(vueLineData.serviceline.srb, 'productPricePrefix') + vueLineData.serviceline.pricedisplay;
		vueLineData.bookingFeeLabel = bookingFeeLabel;
	}

	TourplanItineraryControl.prototype.UpdateServicelines = function(pricingResponse) {

		var itineraryControl = this;
		_.forEach(pricingResponse.availability, function(avail) {
			var osr = avail.Option.OptStayResults;
			var slList = _.filter(itineraryControl.vueData.displayServiceLineList, function(servicelineControl) {
				return avail.Option.OptionNumber == servicelineControl.serviceline.productid &&
						osr.Date == servicelineControl.serviceline.date &&
						osr.Qty == servicelineControl.serviceline.qty &&
						osr.RateId == servicelineControl.serviceline.rateid &&
						osr.Scu == servicelineControl.serviceline.scu;
			});
			_.forEach( slList, function(sl) {
				var bookingFeePrice = 0;
				var bookingFeeLabel = "";
				var bookingFeeList = null;
				if ( sl != null )
					bookingFeeList = itineraryControl.tpFindBookingFee( sl.serviceline );
				if ( bookingFeeList != null && bookingFeeList.length > 0 ) {
					// Find booking fee option
					bookingFeeOption = _.find( pricingResponse.availability, 
							function(a) { return a.Option.OptionNumber == bookingFeeList[0].id;
						});
					// Apply price
					if ( bookingFeeOption != null 
						&& bookingFeeOption.Option
						&& bookingFeeOption.Option.OptStayResults ) {
						bookingFeePrice += bookingFeeOption.Option.OptStayResults.TotalPrice; // Or AgentPrice?
						bookingFeeLabel = itineraryControl.vueData.helper.propWithLang( bookingFeeList[0], "label" );
					}
				}

				if (sl != undefined) {
					itineraryControl.ServicelineUpdatePrice( sl, osr.AgentPrice, bookingFeePrice, bookingFeeLabel );
				}
			});
		});
	}

	TourplanItineraryControl.prototype.RenderServiceLine = function( slc ) {
		var elementId = "#" + slc.jsServicelineId;
		var servicelineControl = $( elementId );
		var vm = this;

		// Preference Section

		var preferenceSection = $(servicelineControl).find('.preferenceSection');

		_.forEach(preferenceSection.find('select,input'), function(el) {

			$(el).change(function(e) {
				slc.serviceline[$(this).attr('name')] = $(this).val();
				var tmp = this;
				// "useDepartureDate" is only expeted to be used by "Rail" type service at current stage.
				// However, we should not hard coded any service types, because they are expected to be expandable.
				if(getServiceButtonConfig(slc.serviceline.servicetype, 'useDepartureDate') == "true") {
					if($(this).hasClass('datepicker')) {
						_.forEach(slc.serviceline.configs[0].pax, function(paxConfig) {
							var pika = paxConfig.pikadayOptions;
							var paxType = paxConfig.paxtype;
							// Min and Max day is depending on preference Section
							// Calculate whether a user is at the right age, whey they travel.
							if(_.isObject(pika) && pika.pikadayOptions) {
								var curDate = $(tmp).val();
								paxConfig.pikadayOptions = _.extend( {}, pika, {
									minDate: moment(curDate).subtract(slc.serviceline.ageLimits[paxType].max + 1, 'years').add(1, 'days'),
									maxDate: moment(curDate).subtract(slc.serviceline.ageLimits[paxType].min, 'years'),
								});
							}
						});
					}
				}
			});
			$(el).change();
		});

		if (slc.qtyConfig == "roombased") {
			if ($(servicelineControl).find('select[name=roomCount]').length > 0) {
				$(servicelineControl).find('select[name=roomCount]').html(
					TourplanRetailUtilities.GenerateOptionsString(
						_.map(_.range(1, 11), function(x) { return {label:x,value:x}; })
					)
				).val(slc.serviceline.configs.length).change(function(e) {
					while($(this).val() > slc.serviceline.configs.length) {
						slc.serviceline.configs.push({type:'tw'});
					}
					if ($(this).val() < slc.serviceline.configs.length) {
						slc.serviceline.configs = _.take(slc.serviceline.configs, $(this).val());
					}

					_.forEach(slc.serviceline.configs, function(roomConfig, index) {

						var roomConfigElement = servicelineControl.find( "#" + roomConfig.jsRoomConfigId);

						roomConfigElement.find('select[name=type]').html(
							TourplanRetailUtilities.GenerateOptionsString(
								_.map(TourplanRetailUtilities.ROOMTYPES, function(x) { return {label:x.name,value:x.value}; }),
								false
							)
						).change(function() {
							// Change Room Type
							var roomtypeConf = _.find(TourplanRetailUtilities.ROOMTYPES, {value:$(this).val()});
							roomConfigElement.find('select[name=adults]').html(
								TourplanRetailUtilities.GenerateOptionsString(
									_.map(_.range(1, roomtypeConf.max_ad + 1), function(x) { return {label:x,value:x}; })
								)
							).change(function() {
								// Change Adult Num
								var childMax = (roomtypeConf.max - $(this).val());
								roomConfigElement.find('select[name=children]').html(
									TourplanRetailUtilities.GenerateOptionsString(
										_.map(_.range(0, childMax + 1), function(x) { return {label:x, value:x}; })
									)
								).change(function() {
									
									slc.serviceline.configs[index].children = parseInt($(this).val());

								}).val(parseInt(roomConfig.children) == 0 || roomConfig.children > childMax || _.isUndefined(roomConfig.children) ? 
									childMax : roomConfig.children).change();

								slc.serviceline.configs[index].adults = parseInt($(this).val());

							}).val(parseInt(roomConfig.adults) == 0 || parseInt(roomConfig.adults) > roomtypeConf.max_ad || _.isUndefined(roomConfig.adults) ? 
								roomtypeConf.max_ad : roomConfig.adults).change();

							slc.serviceline.configs[index].type = $(this).val();
							slc.serviceline.qty = _.map(slc.serviceline.configs, 'type').join(',');

						}).val(roomConfig.type).change();
					});
				}).change();
			}
			else {
				_.forEach(slc.serviceline.configs, function(roomConfig, index) {
					slc.serviceline.configs[index].adults = _.find(TourplanRetailUtilities.ROOMTYPES, function(rt) { return rt.value == roomConfig.type; }).max_ad;
				});
			}

		}
		else if (slc.qtyConfig == "paxbased") {

			// Generate HTML for each PaxConfig element
			if ( slc.serviceline.configs != null ) {
				_.forEach(slc.serviceline.configs[0].pax, function(paxConfig, index) {

					var paxType = paxConfig["paxtype"];
					var paxConfigElement = servicelineControl.find( "#" + paxConfig.jsPaxConfigId);
					var dobElement = $(paxConfigElement).find('input[name=dob]');
					var extraConfig = {
						pikadayOptions: {
							yearRange: [1900, new Date().getFullYear() + 1]
						}
					};

					if (_.has(slc.serviceline, 'ageLimits') && _.has(slc.serviceline.ageLimits, paxType)) {
						extraConfig['minDate'] = moment(slc.serviceline.date).subtract(slc.serviceline.ageLimits[paxType].max + 1, 'years').add(1, 'days');
						extraConfig['maxDate'] = moment(slc.serviceline.date).subtract(slc.serviceline.ageLimits[paxType].min, 'years');
						extraConfig.pikadayOptions['defaultDate'] = extraConfig['maxDate'].toDate();
					}
					paxConfig.pikadayOptions = _.extend(extraConfig, TourplanRetailUtilities.PIKADAYDEFAULTS);

				});
			}
		}

		return servicelineControl;
	}

	TourplanItineraryControl.prototype.UpdatePrice = function(newPrice) {
		this.vueData.cart_price = getServiceButtonConfig("", "cartPricePrefix") + (newPrice / 100).toFixed(2);
	}

	TourplanItineraryControl.prototype.Validate = function( successCallback, failedCallback ) {
		var valid = true;
		var ctrl = this;
		_.forEach(this.vueData.displayServiceLineList, function(serviceline) {
			if (!ctrl.ServicelineValidate( serviceline )) {
				valid = false;
			}
		});
		function failedCallbackWrap() {
			ctrl.vueData.expandAll();
			failedCallback();
		}

		if ( !valid )
			failedCallbackWrap();
		else {
			ctrl.vueData.isValidated( successCallback, failedCallbackWrap );
		}
	}


	TourplanItineraryControl.prototype.tpFindBookingFee = function(serviceline)
	{
		var feeIdList = [];
		// Related: tp-cart.php > tpFindFees
		// # The logic of getting booking fee should stay the same in php and js
		if ( serviceline == null )
			return feeIdList;
		var firstonly = true;
		var fees = window.tpBookingFees;
		_.forEach( fees, function( fee ) {
			var srbs = null;
			var suppcodes = null;
			var prodcodes = null;
			if ( fee['srbs'] != null )
				srbs = fee['srbs'].split( ',' );
			if ( fee['suppliercodes'] != null )
				suppcodes = fee['suppliercodes'].split( ',' );
			if ( fee['productcodes'] != null )
				prodcodes = fee['productcodes'].split( ',' );

			var srb = serviceline.servicetype;
			var suppliercode = serviceline.suppliercode;
			var productcode = serviceline.productcode;

			if ( (srbs != null || suppcodes != null || prodcodes != null ) 
				&& ( srbs == null || _.indexOf( srbs, srb ) > -1 )
				&& ( suppcodes == null || _.indexOf( suppcodes, suppliercode ) > -1 )
				&& ( prodcodes == null || _.indexOf( prodcodes, productcode ) > -1 ) ) {
				feeIdList.push( fee );
				if ( firstonly )
					return feeIdList;
			}
		});
		return feeIdList;
	}

</script>