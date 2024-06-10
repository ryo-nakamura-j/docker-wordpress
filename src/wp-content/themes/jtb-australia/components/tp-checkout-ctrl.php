<!-- tp-checkout-ctrl -->
<script>
	var checkoutVueData = null;
	$(window).load(function() {

		var sectionConfig = <?php echo json_encode($this->sectionConfig); ?>;
		var lproduct = <?php echo json_encode($lproduct); ?>;

		checkoutVueData = new Vue( {
			el: "#tp_checkout_section",
			mixins: [ tpFieldRetainMixin ],
			data: {
				sectionConfig: sectionConfig,
				onRequest: false,
				cardTypes: [],
				maskCount: 0,
				paymentfeePrice: 0,
				isLoading: true,
				isWaiting: false,
				selections: {
					country: [],
					groupedDeliveryFeeOption: [],
					deliveryFeeOption:[],
				},
				data: {
					input_country: null,
					input_deliveryCountry: null,
					input_deliveryFeeOption: null,
				},
				cart_price: "",
				deliveryFeeAmount: "",
				subTotal: 0,
				serviceLineList: [],
			},
			created: function() {
				this.initCountry();
			},
			computed: {
				jsRoot: function(){ return $("#tp_checkout_panel")[0]; },
				input_country: { 
					get: function() { return this.data.input_country; },
					set: function(v) { this.saveFieldValue("input_country", v); this.data.input_country = v; }
				},
				input_deliveryCountry: { 
					get: function() { return this.data.input_deliveryCountry; },
					set: function(v) { this.saveFieldValue("input_deliveryCountry", v); this.data.input_deliveryCountry = v; }
				},
				input_deliveryFeeOption: {
					get: function() { return this.data.input_deliveryFeeOption; },
					set: function(v) { 
						this.saveFieldValue("input_deliveryFeeOption", v); 
						this.data.input_deliveryFeeOption = v; 
					}
				},
				allDeliveryOptions: function(){
					var vm = this;
					var rlt = [];
					if ( vm.selections.groupedDeliveryFeeOption ) {
						if ( vm.selections.groupedDeliveryFeeOption[false] )
							rlt = rlt.concat( vm.selections.groupedDeliveryFeeOption[false] );
						if ( vm.selections.groupedDeliveryFeeOption[true] )
							rlt = rlt.concat( vm.selections.groupedDeliveryFeeOption[true] );
					}
					return rlt;
				},
			},
			mounted: function() {
				$("#tp_checkout_section").removeAttr('hidden');
				if ( typeof onVueMounted === "function" ) {
					onVueMounted();
				}
				$('html').animate({
				    scrollTop: $("html").offset().top
				 }, 300);
			},
			methods: {
				resetCheckingOut: function() {
					$("#tp_checkout_panel").unmask();
					this.isWaiting = false;
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
				initMagnetEffect: function() {
					// Fix scroll-magnet effect init issue
					if ( !this.helper.isMobile() ) {
						this.$refs.tpScrollMagnet.init();
					}
				},
				deliveryFeeChanged: function() {
					var vm = this;
					var selected = this.input_deliveryFeeOption;
					CartInterface.cart['deliveryMethod'] = selected;

					var selectedFeeObject = _.find(this.selections.deliveryFeeOption, function(df) { return df.label == selected; });

					CartInterface.cart['deliveryMethodLang'] = vm.helper.propWithLang( selectedFeeObject, "label" );

					if (selectedFeeObject.hideDeliveryAddress) {
						$(".deliverySection").slideUp();
					} else {
						$(".deliverySection").slideDown();
					}
					$("#deliveryFeesSection").mask("");
					$("#summery_section").mask("");
					vm.maskCount += 1;
					vm.initMagnetEffect();
					CartInterface.CalculateFees(function(newPriceCents, paymentFeeCents, newServiceLineList) {
						$("#deliveryFeesSection").unmask();
						vm.maskCount -= 1;
						if ( vm.maskCount == 0 ) {
							$("#summery_section").unmask("");
						}

						// Calculate delivery fee amount
						vm.deliveryFeeAmount = 0;
						_.forEach( newServiceLineList.availability, function( sl ) {
							if ( sl.Option ) {
								_.forEach( vm.allDeliveryOptions, function(o) {
									if ( sl.Option.OptionNumber == o.id &&
										sl.Option.OptStayResults ) {
										vm.deliveryFeeAmount = sl.Option.OptStayResults.TotalPrice;
									}
								})
							}
						});

						$(vm.jsRoot).find('.totalPrice').html(
							getServiceButtonConfig("", "cartPricePrefix") + (newPriceCents / 100).toFixed(2)
						)

						if (paymentFeeCents != null && paymentFeeCents > 0) {
							$(vm.jsRoot).find("#paymentfeeLabel").html(
								getServiceButtonConfig("", "paymentFeeLabel").replace("{type}", CartInterface.cart.paymentfee.cardtype)
							);
							vm.paymentfeePrice = paymentFeeCents;
						}
						else {
							vm.paymentfeePrice = 0;
							$(vm.jsRoot).find("#paymentfeeLabel").html("");
						}

						if (_.isFunction(window.tpCartRepriceCallback)) {
							window.tpCartRepriceCallback(newPriceCents);
						}

						vm.initMagnetEffect();
					});
				},
				initCountry: function() {
					// Load country list from config
					var countryList = [];
					_.forEach( getServiceButtonConfig("", "countries", "").split(','), function(c) {
						if (c) {
							var spl = c.split('=');
							if ( spl.length >= 1 )
								countryList.push( spl[0] );
							else
								countryList.push( c );
						}
					});
					this.selections.country = countryList;
					if ( this.selections.country.length > 0 ) {
						this.data.input_country = this.loadField("input_country");
						this.data.input_deliveryCountry = this.loadField("input_deliveryCountry");
					}
				},
				initDeliveryFee: function() {
					var deliveryFees = [];
					_.forEach(CartInterface.cart.servicelines, function(x) { 
						var slFee = TourplanRetailUtilities.GetDeliveryFees(x.servicetype, x.suppliercode, x.productcode);
						if (!_.isEmpty(slFee)) {
							deliveryFees = deliveryFees.concat(slFee);
						}		
					});

					var uniqueFees = _.uniq(deliveryFees, function(x) { return x.label; });

					var groupedDeliveryFees = _.groupBy(uniqueFees, function(df) { return df.hideDeliveryAddress == undefined || !df.hideDeliveryAddress; });

					this.selections.deliveryFeeOption = uniqueFees;
					this.selections.groupedDeliveryFeeOption = groupedDeliveryFees;

					// retain fields.
					this.input_deliveryFeeOption = this.loadField("input_deliveryFeeOption") || 
						(this.selections.groupedDeliveryFeeOption[false] && this.selections.groupedDeliveryFeeOption[false][0].label) || 
						(this.selections.groupedDeliveryFeeOption[true] && this.selections.groupedDeliveryFeeOption[true][0].label); 
				},
			}
		})

		dataLayer.push({
			'event': 'checkout',
			'eventCategory': 'Ecommerce', 
			'eventAction': 'Checkout',
			'ecommerce': {
			  	'checkout': {
			  		'actionField': {
			  			'step': 2, 
			  			'option': 'Address Details'
			  		},
			  		'products': lproduct
			  	}
			}
		});

		new TourplanCheckoutControl( $("#tp_checkout_panel")[0], checkoutVueData );
	});


	TourplanCheckoutControl = function(rootElement, vueData) {
		_.extend(this, new TourplanCommonControl(rootElement, null, null));

		var vm = this;
		this.vueData = vueData;
		this.configs = vueData.sectionConfig;
		var parentControl = this;

		CartInterface.GetCart(true, function(newCart) {
			vueData.isLoading = false;
			vm.vueData.initMagnetEffect();
			vueData.onRequest = CartInterface.IsOnRequest();

			vueData.serviceLineList = newCart.servicelines;

			var customerSection = $(parentControl.rootElement).find('.customerSection');

			customerSection.find('select[name=title]').html(
				TourplanRetailUtilities.GenerateOptionsString(
					vm.vueData.helper.getServiceButtonMapConfig("", "titles", ""),
					false,
					true
				)
			).val('');

			var branchSelect  = customerSection.find('select[name=branch]');

			branchSelect.html(
				TourplanRetailUtilities.GenerateOptionsString(
					vm.vueData.helper.getServiceButtonMapConfig("", "branches", ""),
					false,
					true
				)
			).change(function(e) {
				customerSection.find('input[name=branch_label]').val($(this).find("option:selected").prop("label"));
			});

			// PAYMENT TYPES
			vueData.cardTypes = [];
			try {
				vueData.cardTypes = _.map(window.tpPaymentFees, function(fee) {
					return {
						label: vueData.helper.propWithLang( fee, "label" ),
						value: fee.cardTypes[0]
					}
				});
			} catch (e) {
				console.log("cardTypes not found");
			}
			
			// DELIVERY FEES
			vueData.initDeliveryFee();
			deliverySameAsCustomer = vueData.loadField('deliverySameAsCustomer' ) == 1 || false;

			// Calculate subtotal
			// Asuming that sub total will always stays the same on checkout page.
			vueData.subTotal = 0;
			_.forEach( vueData.serviceLineList, function( sl ) {
				var isDeliveryOption = false;
				_.forEach( vueData.allDeliveryOptions, function(o) {
					if ( sl.productid == o.id ) 
						isDeliveryOption = true;
				})
				if ( !isDeliveryOption ) {
					vueData.subTotal += sl.price;
				}
			});

			Vue.nextTick( function() {

				// DELIVERY FEES
				var deliveryFeesSection = $("#deliveryFeesSection");
				if (!_.isEmpty(vueData.selections.deliveryFeeOption)) {
					vueData.deliveryFeeChanged();

					deliveryFeesSection.find('input[name=deliverySameAsCustomer]').change(function(x) {
						deliverySameAsCustomer = $(this).is(':checked');
						deliveryFeesSection.find('.deliveryAddressSection input').attr('disabled', deliverySameAsCustomer);
						deliveryFeesSection.find('.deliveryAddressSection select').attr('disabled', deliverySameAsCustomer);
					}).change();
				}

				// PAYMENT TYPES
				if (!_.isEmpty(vueData.cardTypes)) {

					var paymentTypes = vueData.cardTypes;

					if (!_.isEmpty(paymentTypes)) {
						var paymentTypesSection = $(parentControl.rootElement).find("#paymentTypesSection");

						paymentTypesSection.find('input[name=paymenttype]').change(function(e) {
							var selectedCardType = $(this).val();
							var paymentFee = _.find(window.tpPaymentFees, function(fee) { return _.includes(fee.cardTypes, selectedCardType); });
							if (paymentFee) {
								paymentFee['cardtype'] = selectedCardType;
								CartInterface.cart['paymentfee'] = paymentFee;
								$("#paymentTypesSection").mask("");
								$("#summery_section").mask("");
								vueData.maskCount += 1;
								CartInterface.CalculateFees(function(newPriceCents, paymentFeeCents){
									$("#paymentTypesSection").unmask();
									vueData.maskCount -= 1;
									if ( vueData.maskCount == 0 ) {
										$("#summery_section").unmask();
									}

									$(parentControl.rootElement).find('.totalPrice').html(
										getServiceButtonConfig("", "cartPricePrefix") + (newPriceCents / 100).toFixed(2)
									);

									if (paymentFeeCents != null && paymentFeeCents > 0) {
										$(parentControl.rootElement).find("#paymentfeeLabel").html(
											getServiceButtonConfig("", "paymentFeeLabel").replace("{type}", selectedCardType)
										);
										vm.paymentfeePrice = paymentFeeCents;
									}
									else {
										vm.paymentfeePrice = 0;
										$(parentControl.rootElement).find("#paymentfeeLabel").html("");
									}
					
									if (_.isFunction(window.tpCartRepriceCallback)) {
										window.tpCartRepriceCallback(newPriceCents);
									}
								});
							}
						}).first().prop("checked", true).change();
					}
				}


				// CREDIT CARD FORM
				var creditCardSection = $(parentControl.rootElement).find('#tpcreditcardsection');

				$(creditCardSection).find("#creditcard_number").change(function() {
					if (!$(this).val().match(/^(?:4[0-9]{12}(?:[0-9]{3})?|[25][1-7][0-9]{14}|6(?:011|5[0-9][0-9])[0-9]{12}|3[47][0-9]{13}|3(?:0[0-5]|[68][0-9])[0-9]{11}|(?:2131|1800|35\d{3})\d{11})$/)) {
						$(this).closest(".required").addClass("has-error");
					} else {
						$(this).closest(".required").removeClass("has-error");
					}
				});

				$(creditCardSection).find("#creditcard_cvn").change(function() {
					if (!$(this).val().match(/[0-9]{3,4}/)) {
						$(this).closest(".required").addClass("has-error");
					} else {
						$(this).closest(".required").removeClass("has-error");
					}
				})

				$(creditCardSection).find("#creditcard_number").keypress(function (e) {
				    if ((e.which < 48 || e.which > 57) && (e.which !== 8) && (e.which !== 0)) {
				        return false;
				    }

				    return true;
				});

				$(creditCardSection).find("#creditcard_expiryyear").html(
					TourplanRetailUtilities.GenerateOptionsString(
						_.map(_.range(moment().format("YY"), parseInt(moment().format("YY")) + 11), function(x) { return {label:x, value:x} }),
						false
					)
				).change(function() {
					var monthControl = $(creditCardSection).find("#creditcard_expirymonth");
					var year = $(this).val();
					var selectedMonth = monthControl.val();
					var startMonth = (parseInt(year) == moment().format("YY")) ? parseInt(moment().format("MM")): 1;
					monthControl.html(
						TourplanRetailUtilities.GenerateOptionsString(
							_.map(_.range(startMonth, 13), function(x) {
								return {
									label: (x.toString().length < 2 ? "0" + x : x),
									value: (x.toString().length < 2 ? "0" + x : x)
								}
							}),
							false
						)
					);
					if (monthControl.children('option[value=' + selectedMonth + ']').length > 0) {
						monthControl.val(selectedMonth);
					}

				}).change();
				
				CartInterface.CalculateFees(function(newPriceCents) {
					$(parentControl.rootElement).find('.totalPrice').html(
						getServiceButtonConfig("", "cartPricePrefix") + (newPriceCents / 100).toFixed(2)
					)

					if (_.isFunction(window.tpCartRepriceCallback)) {
						window.tpCartRepriceCallback(newPriceCents);
					}
				});
			});


			$(parentControl.rootElement).find('button[name=confirm]').click(function() {
				parentControl.Validate( successCallback, function() {
					var errs = $(".has-error, .bg-danger");
					if (errs.length > 0) {
						errs[0].scrollIntoView();
					}
				});
				function successCallback() {
					_.each(customerSection.find('select, input'), function(x) {
						if ($(x).attr('name') != 'email' && $(x).attr('name') != 'email_confirm') {
							if ( $(x).val() )
								$(x).val($(x).val().toUpperCase());
						}
					});
					var customer = TourplanRetailUtilities.SerializeKeyValuePair(customerSection.find('select, input'));
					if ( vueData.input_country != null )
						customer.country = vueData.input_country;
					customer.name = templatesHelper.getNameDesc( customer, "paxNameFormat" );

					customer.address = _.filter(
						[customer.address1, 
						customer.address2, 
						customer.address3, 
						customer.address4, 
						customer.address5, 
						customer.postCode, 
						customer.branch_label,
						customer.country,
						customer.address6, 
						customer.address7 ], function(line) { return !_.isEmpty(line) }).join(', ');

					_.merge(CartInterface.cart, customer);

					if (customer.branch) {
						CartInterface.cart.branch = customer.branch;
						var branchSelect = customerSection.find('select[name=branch]');
						CartInterface.cart.branch_label = customer.branch_label;
					}

					var delivery = vueData.input_deliveryFeeOption;

					if (!_.isEmpty(delivery)) {
						CartInterface.cart['deliveryMethod'] = delivery;

						if ($(parentControl.rootElement).find('input[name=deliverySameAsCustomer]').is(':checked')) {
							CartInterface.cart['deliveryAddress'] = "";
							CartInterface.cart['deliveryAddress1'] = customer.address1;
							CartInterface.cart['deliveryAddress2'] = customer.address2;
							CartInterface.cart['deliveryAddress3'] = customer.address3;
							CartInterface.cart['deliveryAddress4'] = customer.address4;
							CartInterface.cart['deliveryAddress5'] = customer.address5;
							CartInterface.cart['deliveryPostCode'] = customer.postCode;
							CartInterface.cart['deliveryCountry'] = customer.country;
							CartInterface.cart['deliveryAddress6'] = customer.address6;
							CartInterface.cart['deliveryAddress7'] = customer.address7;
						} else {
							var delivery = TourplanRetailUtilities.SerializeKeyValuePair($(parentControl.rootElement).find('.deliveryAddressSection input'));
							if ( vueData.input_deliveryCountry != null )
								delivery.deliveryCountry = vueData.input_deliveryCountry;

							var deliveryAddress = _.filter([
								delivery.deliveryAddress1,
								delivery.deliveryAddress2,
								delivery.deliveryAddress3,
								delivery.deliveryAddress4,
								delivery.deliveryAddress5,
								delivery.deliveryPostCode,
								delivery.deliveryCountry,
								delivery.deliveryAddress6,
								delivery.deliveryAddress7], function(line) { return !_.isEmpty(line); }).join(', ');

							CartInterface.cart['deliveryAddress'] = deliveryAddress.toUpperCase();
							CartInterface.cart['deliveryAddress1'] = delivery.deliveryAddress1;
							CartInterface.cart['deliveryAddress2'] = delivery.deliveryAddress2;
							CartInterface.cart['deliveryAddress3'] = delivery.deliveryAddress3;
							CartInterface.cart['deliveryAddress4'] = delivery.deliveryAddress4;
							CartInterface.cart['deliveryAddress5'] = delivery.deliveryAddress5;
							CartInterface.cart['deliveryPostCode'] = delivery.deliveryPostCode;
							CartInterface.cart['deliveryCountry'] = delivery.deliveryCountry;
							CartInterface.cart['deliveryAddress6'] = customer.address6;
							CartInterface.cart['deliveryAddress7'] = customer.address7;
						}
					}

					vueData.isWaiting = true;
					$(parentControl.rootElement).mask("");
					CartInterface.PushCart({
						success: function() {

							dataLayer.push({
								"event":"payment",
								"eventCategory":"Ecommerce",
								"eventAction":"Payment",
								"ecommerce": {
									"checkout": {
										"actionField": {
											"step":3,
											"option":"Payment Info"
										},
										"products": _.map(CartInterface.cart.servicelines, function(sl) {
											return {
												"id": sl.productid,
												"name": sl.productname,
												"price": sl.pricedisplay,
												"quantity": sl.qtyNum,
												"category": sl.servicetype,
												"list": sl.servicetype
											}
										}),
										"payment": {
											"firstname": CartInterface.cart.firstname,
											"middlename": CartInterface.cart.middlename,
											"lastname": CartInterface.cart.lastname,
											"email": CartInterface.cart.email,
											"phone": CartInterface.cart.phone,
											"postCode": CartInterface.cart.postCode
										}
									}
								}
							});

							if (!_.isFunction(window.tpCartSubmit)) {
								$('<form method="post"></form>').appendTo(parentControl.rootElement).submit();
							} else {
								window.tpCartSubmit();
							}
						}
					});
				}
			});

			
		});
	}

	TourplanCheckoutControl.prototype.Validate = function( successCallback, failedCallback ) {
		var valid = true;
		var emailRegex = /.+@.+/;
		var emailInput = $("input[name=email]");
		var emailConfirmInput = $("input[name=email_confirm]");

		var requiredNodes = $(this.rootElement).find('.customerSection .required');
		requiredNodes = requiredNodes.add($(this.rootElement).find('#tpcreditcardsection .required'));
		
		var delivery = $(this.rootElement).find('input[name=deliveryfee]:checked').val();
		var deliveryConfig = _.find(this.vueData.selections.deliveryFeeOption, function(x) { return x.label == delivery; });

		if (!$(this.rootElement).find('input[name=deliverySameAsCustomer]').is(':checked') && 
			(deliveryConfig !== undefined ) && ( deliveryConfig.hideDeliveryAddress == undefined || deliveryConfig.hideDeliverAddress == false)) {
			requiredNodes = requiredNodes.add(".deliveryFeesSection .required");
		} else {
			_.forEach($(".deliveryFeesSection .required"), function(element) {
				$(element).removeClass('has-error');
			}); 
		}

		_.forEach(requiredNodes, function(element) {
			var childrenValid = true;
			// A hidden input field will be selected in IE (only IE)
			// It will cause a validation failed for "State" dropdown, even a value has been selected.
			_.forEach($(element).find('select,input[type!="hidden"]'), function(subElement) {
				if (_.isEmpty($(subElement).val())) {
					childrenValid = false;
				};
			});
			if (!childrenValid) {
				valid = false;
				$(element).addClass('has-error');
			}
			else {
				$(element).removeClass('has-error');
			}
		});

		if ((emailInput.val() != emailConfirmInput.val()) || (!emailInput.val().match(emailRegex))) {
			valid = false;
			emailInput.closest('.required').addClass('has-error');
			emailConfirmInput.closest('.required').addClass('has-error');
		} else {
			emailInput.closest('.required').removeClass('has-error');
			emailConfirmInput.closest('.required').removeClass('has-error');
		}

		if ( !valid )
			failedCallback();
		else {
			this.vueData.isValidated( successCallback, failedCallback );
		}
	}
</script>