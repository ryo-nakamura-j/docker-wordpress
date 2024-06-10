
var TourplanCart = function(cartUrl) { 
	this.cartUrl = cartUrl;
	this.cart = {servicelines:[]};

	this.GetCart(true, function(x) { console.log(x); });
	console.log(this);
}

TourplanCart.prototype.GetCart = function(refresh, callback) {
	var cartRef = this;
	if (!this.cart || refresh == true) {
		$.ajax({
			type:'get',
			url:this.cartUrl,
			dataType:'json'
		}).done(function(cartData) {
			cartRef.cart.servicelines = _.map(cartData.servicelines, function(serviceline) { return new TourplanServiceLine(serviceline); });
			if (callback) {
				callback(cartData)
			}
		});
	} else {
		callback(this.cart);
	}
}

TourplanCart.prototype.PushCart = function(callbacks) {
	var newCart = JSON.stringify(this.cart);
	return $.ajax({
		type:'post',
		url:this.cartUrl,
		data:newCart,
		contentType: "application/json",
		success: (callbacks.success ? callbacks.success : $.noop)
	});
}

TourplanCart.prototype.RepriceCart = function(pricing, callback) {
	var self = this;
	this.GetCart(false, function(cartData) {
		$.ajax({
			type:'get',
			url:self.cartUrl + '?pricing=' + pricing,
			dataType:'json',
			success:callback
		})
	})
}

TourplanCart.prototype.addServiceLine = function(supplier, product, availability, slOverride, callbacks) {

	var firstNonNull = function(choices, dft) {
		var retVal = _.find(choices, function(x) { return x != null });
		return retVal == undefined ? dft : retVal;
	}

	var cartInstance = this;

	var curScu = (slOverride.scu == null) ? (availability.Scu > 0 ? availability.Scu : 1) : slOverride.scu;
	var servicelineObj = {
		// productid: (slOverride.productid == null) ? product.productid : slOverride.productid,
		productid: firstNonNull([slOverride.productid, product.productid]),
		rateid: (slOverride.rateid == null) ? availability.RateId : slOverride.rateid,
		scu: curScu,
		currency: (slOverride.currency == null) ? $("#tourplanRetailConfig").attr('currency') : slOverride.currency,
		date: (slOverride.date == null) ? availability.Date : slOverride.date,
		arrangementDisplay: (slOverride.arrangementDisplay == null) ? "" : slOverride.arrangementDisplay,
		remarks: (slOverride.remarks == null) ? "" : slOverride.remarks,
		datein: moment(availability.PeriodValueAdds.PeriodValueAdd.DateFrom).format('ddd DD MMM YYYY'),
		dateout: moment(availability.PeriodValueAdds.PeriodValueAdd.DateFrom).add(curScu, 'days').format('ddd DD MMM YYYY'),
		adultages: product.ageRanges.adultAgeRange,
		childages: product.ageRanges.childAgeRange,
		infantages: product.ageRanges.infantAgeRange,
		ageLimits: product.ageLimits,
		qty: ((tmp = slOverride.qty ? slOverride.qty : availability.Qty) ? tmp : getServiceButtonConfig(product.srb, 'defaultQty')),
		price: (slOverride.price == null) ? availability.AgentPrice : slOverride.price,
		pricedisplay: (slOverride.pricedisplay == null) ? product.availability[0].AgentPrice / 100 : slOverride.pricedisplay,
		availability: TourplanRetailUtilities.AvailabilityLookup(availability.Availability),
		servicetype: product.srb,
		productcode: product.code,
		suppliercode: supplier.code,
		suppliername: supplier.name,
		source_url: window.location.href,
		unique_ui_id: templatesHelper.randomId(),
		productname: _.has(slOverride, 'productname') ? slOverride.productname : product.name,
		productcomment: product.comment,
		classCode: product.info.Option.OptGeneral.Class,
		className: product.info.Option.OptGeneral.ClassDescription,
		qtyConfig: slOverride.qtyConfig
	};

	// Read translations for multi-lang
	templatesHelper.readPropTranslations( servicelineObj, 'productname', 
		servicelineObj.servicetype, product, 'productNameLangNote');
	templatesHelper.readPropTranslations( servicelineObj, 'suppliername', 
		servicelineObj.servicetype, supplier, 'supplierNameLangNote');

	var serviceline = new TourplanServiceLine( servicelineObj );

	serviceline.configs = serviceline.BuildEmptyConfigs();

	this.GetCart(true, function() {
		cartInstance.cart.servicelines.push(serviceline);

		cartInstance.PushCart({
			success: function() { 
				dataLayer.push({
					"event":"productClick",
					"eventCategory":"Ecommerce",
					"eventAction":"productClick",
					"ecommerce": {
						"click": {
							"actionField": {
								"list": product.srb
							},
							"products": [
								{
									"id": firstNonNull([slOverride.productid, product.productid]),
									"name":firstNonNull([slOverride.productname, product.name]),
									"price":firstNonNull([slOverride.pricedisplay, product.availability[0].AgentPrice / 100]),
									"category":product.srb,
									"position":1
								}
							]
						}
					}
				});

				window.location = $("#tourplanRetailConfig").attr("itinerarypage"); 
			}
		});
	});
}

TourplanCart.prototype.RemoveAllServicelines = function() {
	this.cart.servicelines = {};
	this.PushCart({});
}

TourplanCart.prototype.RemoveServiceLine = function(serviceline) {
	var idx = _.findIndex(this.cart.servicelines, function(sl) {
		return _.isEqual(sl, serviceline);
	});

	this.cart.servicelines = _.slice(this.cart.servicelines, 0, idx).concat(_.slice(this.cart.servicelines, idx + 1, this.cart.servicelines.length));
}

TourplanCart.prototype.TotalPrice = function() {
	var total = 0;
	_.forEach(this.cart.servicelines, function(x) {
		total += x.pricebookingfee ? x.price + x.pricebookingfee : x.price;
	});
	return total;
}

TourplanCart.prototype.TotalBookingFee = function() {
	var total = 0;
	_.forEach(this.cart.servicelines, function(x) {
		if ( x.hidden == null || x.hidden == false )
			total += x.pricebookingfee ? x.pricebookingfee : 0;
	});
	return total;
}

TourplanCart.prototype.IsOnRequest = function() {
	if (_.isEmpty(this.cart.servicelines)) { return false; }

	var rq = false;

	_.forEach(this.cart.servicelines, function(x) {
		console.log(x);
		if (x['availability'] !== 'Available') {
			rq = true;
		}
	})
	return rq;
}

TourplanCart.prototype.CalculateFees = function(callback) {
	var cartInstance = this;
	this.GetCart(true, function(data) {
		function totalFeeCalc() {
			if (cartInstance.cart.paymentfee) {
				var paymentfeeObj = cartInstance.cart.paymentfee;
				var basePrice = ( !paymentfeeObj.isIncludingFee || paymentfeeObj.isIncludingFee == null )
					? totalPrice - totalBookingFee : totalPrice;
				var totalFee = Math.round(basePrice * (paymentfeeObj.percentage / 100));
				var agentExclusive = totalFee;
				var agentTax = 0;
				if ( paymentfeeObj.AgentTaxPercentageOfFee != null &&
					paymentfeeObj.AgentTaxPercentageOfFee > 0 &&
					paymentfeeObj.AgentTaxPercentageOfFee < 100 ) {
					agentExclusive = Math.round( totalFee * ( 1 - paymentfeeObj.AgentTaxPercentageOfFee / 100 ) );
					agentTax = totalFee - agentExclusive;
				}

				cartInstance.cart.paymentfee['AgentExclusive'] = agentExclusive;
				cartInstance.cart.paymentfee['AgentTax'] = agentTax;
				cartInstance.PushCart({});
				return totalFee;
			}
			return 0;
		}
		var totalPrice = cartInstance.TotalPrice();
		var totalBookingFee = cartInstance.TotalBookingFee();
		if (cartInstance.cart.deliveryMethod) {
			CartInterface.RepriceCart(cartInstance.cart.deliveryMethod, function(repriceData) {
				totalPrice = repriceData.price;
				var totalFee = totalFeeCalc();
				totalPrice += totalFee;
				callback(totalPrice, totalFee, repriceData);
			});
		}
		else if (cartInstance.cart.paymentfee) {
			var totalFee = totalFeeCalc();
			totalPrice += totalFee;
			callback(totalPrice, totalFee);
		}
		else {
			callback(totalPrice);
		}
	})
}