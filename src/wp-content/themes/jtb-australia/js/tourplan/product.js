
var TourplanProduct = function(initialiser) {
	$.extend(this, initialiser);
	this.info = (this.info) ? JSON.parse(this.info) : null;
	this.sanitizedcode = (this.code) ? this.code.replace(/ /g, '-') : null;
	if (this.info) {
		this.ageRanges = {
			infantAgeRange: this.getAgeRangeString(this.info.Option.OptGeneral.Infant_From, this.info.Option.OptGeneral.Infant_To),
			childAgeRange: this.getAgeRangeString(this.info.Option.OptGeneral.Child_From, this.info.Option.OptGeneral.Child_To),
			adultAgeRange: this.getAgeRangeString(this.info.Option.OptGeneral.Adult_From, this.info.Option.OptGeneral.Adult_To)
		};
		var ageLimits = {};
		var optGeneral = this.info.Option.OptGeneral;

		if (_.has(optGeneral, 'Adult_From') && _.has(optGeneral, 'Adult_To')) {
			ageLimits['A'] = {
				min: optGeneral.Adult_From,
				max: optGeneral.Adult_To
			};
		}
		if (_.has(optGeneral, 'Child_From') && _.has(optGeneral, 'Child_To')) {
			ageLimits['C'] = {
				min: optGeneral.Child_From,
				max: optGeneral.Child_To
			};
		}
		if (_.has(optGeneral, 'Infant_From') && _.has(optGeneral, 'Infant_To')) {
			ageLimits['I'] = {
				min: optGeneral.Infant_From,
				max: optGeneral.Infant_To
			};
		}

		if (!_.isEmpty(ageLimits)) {
			this.ageLimits = ageLimits;
		}
	}
}

TourplanProduct.prototype.LowestPricedAvailability = function() {
	return _.min(this.availability, function(avail) {
		return avail.TotalPrice;
	});
}

TourplanProduct.prototype.getAgeRangeString = function(ageFrom, ageTo) {
	if (!ageFrom && !ageTo) {
		return undefined;
	} else if (ageTo >= 999) {
		return ageFrom + '+';
	} else {
		return ageFrom + '-' + ageTo;
	}
}

TourplanProduct.prototype.getPaxCount = {
	adults: function() {
		return this.availability[0].Qty.match(/[0-9]*(?=A)/);
	},
	children: function() {
		return this.availability[0].Qty.match(/[0-9]*(?=C)/);
	},
	infants: function() {
		return this.availability[0].Qty.match(/[0-9]*(?=I)/);
	}
}