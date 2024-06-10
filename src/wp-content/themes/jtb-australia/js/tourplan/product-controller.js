
TourplanProductController = function(productConfig) {
	this.controlConfig = {
		title: productConfig.title,
		paxType: productConfig.paxtype,
		defaultQty: productConfig.default_qty
	};
	this.qtyObj = {
		adult:0,
		child:0,
		infant:0
	}
	this.scu = getServiceButtonConfig(productConfig.service_button, "defaultScu", "1");
	this.productID = productConfig.productID;
	this.service_button = productConfig.service_button;
}

TourplanProductController.prototype.ProductRequest = function(overrides, updateProduct, successCallback, failureCallback) {
	var parentController = this;

	var searchData = {
		date: TourplanRetailUtilities.ValueOrDefault(overrides.date, this.GetDate()),
		scu: TourplanRetailUtilities.ValueOrDefault(overrides.scu, this.scu),
		qty: TourplanRetailUtilities.ValueOrDefault(overrides.qty, this.GetQty())
	}

	return REI.Product(this.productID, searchData, function(data) {
		if (data != null) {
			if (updateProduct) {
				parentController.data = new TourplanProduct(data.products[0]);
				parentController.supplier = new TourplanSupplier(data.supplier);
			}
			if (successCallback) {
				successCallback(data);
			}
		} else if (failureCallback) {
			failureCallback();
		}
	});
}

TourplanProductController.prototype.SetQty = function(qty) {
	$(this.view).find("select[name=" + this.controlConfig.paxType + "]").val(qty).change();
}

TourplanProductController.prototype.GetQty = function() {
	var qtyString = "";
	if (!_.isNull(this.qtyObj.adult)) {
		qtyString += this.qtyObj.adult + "A";
	}
	if (!_.isNull(this.qtyObj.child)) {
		qtyString += this.qtyObj.child + "C";
	}
	if (!_.isNull(this.qtyObj.infant)) {
		qtyString += this.qtyObj.infant + "I";
	}
	return qtyString;
}

TourplanProductController.prototype.GetDate = function() {
	return TourplanRetailUtilities.ValueOrDefault(
		$(this.view).find('.datepicker').val(),
		new moment().add(parseInt(getServiceButtonConfig(this.service_button, '0')), 'days').format(TourplanRetailUtilities.DATEFORMATS.DATA)
	);
}

TourplanProductController.prototype.Render = function(template) {
	return template(this);
}

TourplanProductController.prototype.UpdatePrice = function(newPrice) {
	this.price = newPrice;
	$(this.view).find('.priceElement').html(getServiceButtonConfig(this.service_button, "productPricePrefix") + (newPrice / 100).toFixed(2));
}

TourplanProductController.prototype.SetView = function(viewElement) {
	this.view = viewElement;
}

TourplanProductController.prototype.BuildServiceline = function() {

	var availability = this.data.LowestPricedAvailability();
	if (availability != Infinity) {
		var serviceline = new TourplanServiceLine({
			productid: this.data.productid,
			rateid: availability.RateId,
			date: availability.Date,
			scu: availability.Scu,
			price: availability.AgentPrice,
			qty: availability.Qty,
			currency: availability.Currency,
			availability: TourplanRetailUtilities.AvailabilityLookup(availability.Availability),
			productname: this.data.name,
			servicetype: this.data.srb,
			adultages: this.data.ageRanges.adultAgeRange,
			childages: this.data.ageRanges.childAgeRange,
			infantages: this.data.ageRanges.infantAgeRange,
			ageLimits: this.data.ageLimits,
			suppliercode: this.supplier.info.Supplier.SupplierCode,
			productcode: this.data.code,
			source_url: window.location.href,
			unique_ui_id: templatesHelper.randomId(),
		});
		serviceline['configs'] = serviceline.BuildEmptyConfigs();
		return serviceline;
	} else {
		return false;
	}
}