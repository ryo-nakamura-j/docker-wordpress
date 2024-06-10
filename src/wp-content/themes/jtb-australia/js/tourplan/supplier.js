

var TourplanSupplier = function(initialiser) {
	$.extend(this, initialiser);
	this.info = (this.info) ? JSON.parse(this.info) : null;
	this.products = _.map(this.products, function(x) { return new TourplanProduct(x); });
}

TourplanSupplier.prototype.LowestPricedProduct = function() {
	return _.min(this.products, function(x) { 
		return x.LowestPricedAvailability().TotalPrice; 
	});
}