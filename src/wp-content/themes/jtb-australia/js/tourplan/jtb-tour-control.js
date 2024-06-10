
TourplanJTBTourControl = function(rootElement, controlConfiguration) {
	console.log(this);
	_.extend(this, new TourplanExtensionFunctions(rootElement));
	var productControl = this;
	this.displayPrice = null;
	this.controlConfiguration = controlConfiguration;

	this.urlSearchParams = TourplanRetailUtilities.DeserializeURLParameters(window.location.search[0] == "?" ? window.location.search.substr(1) : window.location.search);

	var pathParts = trimSlash(window.location.pathname).split('/');
	document.title = unescape(pathParts[pathParts.length - 1]) + ' - ' + $("meta[name=site-title]").attr("value");

	this.pageSearchParams = controlConfiguration.static_config;

	this.pageLoaded = false;

	$(this.rootElement).html(this.templates.masterTemplate());

	this.GeneratePricePanel('.product-section.search-date', this.GetAllSearchParams());


	if (this.controlConfiguration.date_range_config) {
		var sp = _.filter(this.GetAllSearchParams(), function(p) { return p.name != 'date'; });
		var origDate = _.find(this.GetAllSearchParams(), function(p) { return p.name == 'date'; }).value;
		
		var additionalDates = [];

		var days_before = parseInt(TourplanRetailUtilities.GetNameValuePair(this.controlConfiguration.date_range_config,'days_before').value);
		var days_after = parseInt(TourplanRetailUtilities.GetNameValuePair(this.controlConfiguration.date_range_config,'days_after').value);
		if (days_before > 0) {
			for (var i = 1; i <= days_before; i++) {
				additionalDates.push({
					selector: ".product-section.search-date-minus-" + i,
					date: moment(origDate, 'YYYY-MM-DD').subtract(i, 'days').format('YYYY-MM-DD')
				});
			}
		}
		if (days_after > 0) {
			for (var i = 1; i <= days_after; i++) {
				additionalDates.push({
					selector: ".product-section.search-date-plus-" + i,
					date: moment(origDate, 'YYYY-MM-DD').add(i, 'days').format('YYYY-MM-DD')
				});
			}
		}

		_.forEach(additionalDates, function(date) {
			productControl.GeneratePricePanel(
				date.selector,
				sp.concat([{name:'date',value:date.date}])
			);
		});
	}
}

TourplanJTBTourControl.prototype = _.create(TourplanProductControl.prototype, _.assign({
	'_super': TourplanProductControl.prototype,
	'constructor': TourplanJTBTourControl
}));

TourplanJTBTourControl.prototype.UpdatePrice = function(newPrice) {
	this.displayPrice = newPrice;
	var priceString = getServiceButtonConfig(this.controlConfiguration.srb, "productPricePrefix");
	priceString += (newPrice / 100).toFixed(2);
	$(this.rootElement).find('.fromPrice').html(priceString);
}


TourplanJTBTourControl.prototype.GenerateArrangementPanel = function( externalRates, paxCount, adultAvail, childAvail, infantAvail ) {
	var productControl = this;
	if ( paxCount == 0 )
		return;
	if ( externalRates && adultAvail != null 
		&& adultAvail.ExternalRateDetails != null 
		&& adultAvail.ExternalRateDetails.AdditionalDetails != null ) {
		var details = adultAvail.ExternalRateDetails.AdditionalDetails.AdditionalDetail;
		var arrangementElement = $("#tourArrangementsPanel");
		if ( adultAvail ) {
			var data = ArrangementPanel.GenerateFromAdditionalDetailXml( productControl.controlConfiguration, details, paxCount );
			$(arrangementElement).html(productControl.templates.arrangementsTemplate(data));

			_.forEach(arrangementElement.find(".arrangements-datepicker .datepicker"), function(el) {
				pikadayResponsive(el, {
					classes: "form-control",
					placeholder: "DD-MM-YYYY",
					outputFormat: "YYYYMMDD",
					format: "DD-MM-YYYY",
					checkIfNativeDate: function () {
			    		return Modernizr.inputtypes.date && (Modernizr.touch && navigator.appVersion.indexOf("Win") === -1);
					}
				}); 
			});
		}
	}
}

TourplanJTBTourControl.prototype.GeneratePricePanel = function(destinationSelector, searchParams) {

	var productControl = this;

	var productID = this.GetSearchParam('productid').value;
	var paxSearchParams = _.map(['1A','1C','1I'], function(pax) {
		return _.filter(searchParams, function(x) { return x.name != "qty"; }).concat([{name:'qty',value:pax}]);
	});

	return $.when.apply(
		$,
		_.map(paxSearchParams, function(x) { return REI.Product(productID, x, null); })
	).done(function(adultResponse, childResponse, infantResponse) {

		var supplier = new TourplanSupplier(adultResponse[0].supplier);
		var product = new TourplanProduct(adultResponse[0].products[0]);


		console.log(arguments);


		var productInfo = JSON.parse(adultResponse[0].products[0].info).Option.OptGeneral;

		var ageBrackets = {};
		ageBrackets['infant'] = productInfo.Infant_From + ((productInfo.Infant_To != "999") ? '-' + productInfo.Infant_To : "");
		ageBrackets['child'] = productInfo.Child_From + ((productInfo.Child_To != "999") ? '-' + productInfo.Child_To : "");
		ageBrackets['adult'] = productInfo.Adult_From + ((productInfo.Adult_To != "999") ? '-' + productInfo.Adult_To : "+");
		// Get external availabilities
		var adultAvails = _.filter(adultResponse[0].products[0].availability, function(avail) {
			return avail.RateId != "Default";
		});
		var childAvails = _.filter(childResponse[0].products[0].availability, function(avail) {
			return avail.RateId != "Default";
		});
		var infantAvails = _.filter(infantResponse[0].products[0].availability, function(avail) {
			return avail.RateId != "Default";
		});
		// If any external rates exist, use external rates instead of the internal ones.
		var externalRates = adultAvails.length > 0 || childAvails.length > 0 || infantAvails.length > 0;
		var adultAvail = externalRates ? adultAvails[0] : null;
		var childAvail = externalRates ? childAvails[0] : null;
		var infantAvail = externalRates ? infantAvails[0] : null;

		// No longer using pickup points on date select, since GAccess introduced the concept of arrangements.
		var pickupPoints = [];

		var hasRates = adultAvail || childAvail || infantAvail;

		if (!productControl.pageLoaded) {
			$(productControl.rootElement).html(productControl.templates.productInfoTemplate({
				product:product,
				supplier:supplier,
				availablity:{
					adult:adultAvail,
					child:childAvail,
					infant:infantAvail
				}
			}));

			if (_.isEmpty(pickupPoints)) {
				$(productControl.rootElement).find('.pickupPointsDetail').parent().remove();
			} else {
				$(productControl.rootElement).find('.pickupPointsDetail').html(_.map(pickupPoints, function(pp) { return pp.label; }).join(' '));
			}

			productControl.pageLoaded = true;
		}

		var hasPrice = (_.has(adultAvail, 'TotalPrice'));
			
		if ((hasPrice && adultAvail.TotalPrice < productControl.displayPrice) || (hasPrice && _.isEmpty(productControl.displayPrice))) {
			productControl.UpdatePrice(adultAvail.TotalPrice);
		}

		var destinationElement = $(productControl.rootElement).find(destinationSelector);

		$(destinationElement).html(productControl.templates.resultTemplate({
			product:product,
			availability:{
				adult:adultAvail,
				child:childAvail,
				infant:infantAvail
			},
			ageBrackets: ageBrackets,
			buttonName: externalRates ? "Select" : "Book"
		}));

		$(destinationElement).find('select[name=pickup]').html(
			'<option value="">--- Select Pickup Point ---</option>' + 
			TourplanRetailUtilities.GenerateOptionsString(pickupPoints)
		);

		var enabledPaxSelects = $(destinationElement).find('select[name=adult],select[name=child],select[name=infant]');

		var enabledSelects = $(destinationElement).find('select[name=adult],select[name=child],select[name=infant]');

		if (pickupPoints.length > 0) {
			enabledSelects.push($(destinationElement).find('select[name=adult],select[name=child],select[name=infant],select[name=pickup]'));
		} else {
			$(destinationElement).find('select[name=pickup]').remove();
		}

		_.forEach(enabledSelects, function(select) {
			$(select).change(function() {


				var paxCount = _.reduce(enabledPaxSelects, function(total, enabledSelect) {
					return total + parseInt($(enabledSelect).val());
				}, 0);

				// Clear dropdown value for all other panels
				var allOtherPricePanels = $("#tourPricePanel .product-section" ).filter(":not(" + destinationSelector + ")").each( 
					function() {
						$(this).find("select[name=adult],select[name=child],select[name=infant]").val("0");
						$(this).find("button[name=book]").prop('disabled', true);
				});

				var pickupSelected = (pickupPoints.length > 0) ? 
					$(destinationElement).find('select[name=pickup]').val().trim() != "" : 
					true;
				
				$(destinationElement).find('button[name=book]').prop('disabled', (paxCount == 0) || !pickupSelected);

			}).change();
		})

		$(productControl.rootElement)
			.find(destinationSelector)
			.find('button[name=book]')
			.click( function() {
				var adultCount = $(destinationElement).find('select[name=adult]').val();
				var childCount = $(destinationElement).find('select[name=child]').val();
				var infantCount = $(destinationElement).find('select[name=infant]').val();
				var adultCountInt = !_.isEmpty(adultCount) ? parseInt(adultCount) : 0;
				var childCountInt = !_.isEmpty(childCount) ? parseInt(childCount) : 0;
				var infantCountInt = !_.isEmpty(infantCount) ? parseInt(infantCount) : 0;
				var paxCount = adultCountInt + childCountInt + infantCountInt;
				if ( externalRates ) {
					productControl.GenerateArrangementPanel(externalRates, paxCount, adultAvail, childAvail, infantAvail);
					// Scroll to book_now button
					$('html, body').animate({
					    scrollTop: $("#tourArrangementsPanel").offset().top
					 }, 300);
					$(productControl.rootElement)
						.find('#book_now')
						.click( function() {
							if ( ArrangementPanel.Validate( $("#tourArrangementsPanel") ) ) {
								$(this).addClass("disabled");
								$("#tourArrangementsPanel .arrangements-container").mask("");
								var remarks = ArrangementPanel.GetArrangementRequestString( $("#tourArrangementsPanel") );
								var arrangementDisplay = ArrangementPanel.GetArrangementDisplay( $("#tourArrangementsPanel") );
								book(remarks, arrangementDisplay)
							}
							else {
								var errs = $(".has-error, .duplicate");

								if (errs.length > 0) {
									errs[0].scrollIntoView();
								}
							}
					});
				}
				else book();
				function book( remarks, arrangementDisplay ) {
					var adults = adultCount == '0'  || adultCount == undefined ? '' : adultCount + 'A';

					var childNum = childCountInt + infantCountInt;
					var children = childNum.toString() + 'C';

					var adultPrice = adultAvail != undefined ? adultAvail.TotalPrice : 0;
					var childPrice = childAvail != undefined ? childAvail.TotalPrice : 0;
					var infantPrice = infantAvail != undefined ? infantAvail.TotalPrice : 0;

					var totalPrice = 0;

					if (adultCount != undefined) {
						totalPrice += parseInt(adultCount) * parseInt(adultPrice);
					}
					if (childCount != undefined) {
						totalPrice += parseInt(childCount) * parseInt(childPrice);
					}
					if (infantCount != undefined) {
						totalPrice += parseInt(infantCount) * parseInt(infantPrice);
					}

					var serviceline = {}

					var productName = adultResponse[0].products[0].name;
					if (externalRates) {
						productName += " - Pickup from " + $(destinationElement).find('select[name=pickup]').find('option:selected').text();
					}

					var serviceDate = TourplanRetailUtilities.GetNameValuePair(searchParams, 'date').value;

					// No longer using pickup points to determine rateid, since GAccess introduced the concept of arrangements.
					var rateid = adultAvail.RateId;

					serviceline['rateid'] = rateid;

					serviceline['productid'] = adultResponse[0].products[0].productid;
					serviceline['datein'] = moment(serviceDate, 'YYYY-MM-DD').format("ddd D MMM YYYY");
					serviceline['date'] = serviceDate;
					serviceline['scu'] = '1';
					serviceline['adultages'] = ageBrackets['adult'];
					serviceline['childages'] = ageBrackets['infant'].split('-')[0] + '-' + ageBrackets['child'].split('-')[1];
					serviceline['pricedisplay'] = '$' + (totalPrice / 100).toFixed(2);
					serviceline['price'] = totalPrice;
					serviceline['qty'] = adults + children;// + infants;
					serviceline['availability'] = adultAvail.Availability;
					serviceline['servicetype'] = adultResponse[0].products[0].srb;
					serviceline['suppliercode'] = adultResponse[0].supplier.code;
					serviceline['suppliername'] = adultResponse[0].supplier.name;
					serviceline['productcode'] = adultResponse[0].products[0].code;
					serviceline['productname'] = productName;
					serviceline['productcomment'] = adultResponse[0].products[0].comment;
					serviceline['currency'] = adultAvail.Currency;
					serviceline['qtyConfig'] = 'paxbased';
					serviceline['unique_ui_id'] = templatesHelper.randomId();
					serviceline['qtyNum'] = parseInt(adultCount) + parseInt(childCount);

					if ( externalRates ) {
						serviceline['remarks'] = remarks;
						serviceline['arrangementDisplay'] = arrangementDisplay;
					}

					var correctAvail = adultAvail;
					if (externalRates) {
						correctAvail = _.find(product.availability, function(a) { return a.RateId == rateid; });
					}

					var cartUrl = $("#tourplanRetailConfig").attr('carturl');

					CartInterface.addServiceLine(
						supplier,
						product,
						correctAvail,
						serviceline, 
						function() {
						CartInterface.GetCart(true, function(x) {
							console.log(x);
						})
					})

					console.log(serviceline);
				}
		});
	});
}