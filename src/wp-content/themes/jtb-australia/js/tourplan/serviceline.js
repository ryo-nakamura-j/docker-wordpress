
var TourplanServiceLine = function(initialiser) {
	$.extend(this, initialiser);
}

TourplanServiceLine.prototype.BuildEmptyConfigs = function() {
	var qtyConfig = getServiceButtonConfig(this.servicetype, 'qtyConfig');

	var configs = [];

	if (qtyConfig == 'roombased') {
		this.qty.split(',');
		_.forEach(this.qty.split(','), function(x) { configs.push({type:x}); });
	}
	else if (qtyConfig == 'paxbased') {
		var parsedQty = TourplanRetailUtilities.ParseQty(this.qty);

		var config = {
			adults: parsedQty.adults,
			children: parsedQty.children,
			infants: parsedQty.infants,
			pax: []
		}

		for (var i = 0; i < parsedQty.adults; i++) {
			config.pax.push({paxtype:'A'});
		}
		for (var i = 0; i < parsedQty.children; i++) {
			config.pax.push({paxtype:'C'});
		}
		for (var i = 0; i < parsedQty.infants; i++) {
			config.pax.push({paxtype:'I'});
		}

		configs.push(config);
	}
	return configs;
}