

TourplanNonAccomProductControl = function(configs, destinationElement, template) {
	this.destinationElement = destinationElement;
	this.template = template;
	this.configs = configs;

	this.destinationElement.html(TourplanRetailUtilities.loadingImage());
}

TourplanNonAccomProductControl.prototype.GetRates = function(data, callback) {
	/// data is an object of key value pairs that are to appear in the URL
	REI.Product_Old(this.configs.productid, data).done(
		$.proxy(callback, this)
	);
}

TourplanNonAccomProductControl.prototype.GetQty = function() {
	var adultQty = $(this.destinationElement).find('select[name=adults]').val() || "0A";
	var childQty = $(this.destinationElement).find('select[name=children]').val() || "0C";
	var infantQty = $(this.destinationElement).find('select[name=infants]').val() || "0I";
	qty = adultQty + childQty + infantQty;

	return qty;
}