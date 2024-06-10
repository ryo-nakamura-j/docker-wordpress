
TourplanAmenityFilter = function(element, amenityCategory) {
	this.element = element;
	this.allLookups = _.map(
		TourplanRetailUtilities.GetLookupMap("AMCAMN","AMN", ["AMC=" + amenityCategory])[0].codes,
		function(code) {
			return _.find(window.tpSearchParams.lookups, function(lookup) { return lookup.code == code; });
		}
	);
	if (TourplanRetailUtilities.DesktopDevice()) {
		$(this.element).multiselect({});
	}
}

TourplanAmenityFilter.prototype.Clear = function() {
	$(this.element).empty();
	if (TourplanRetailUtilities.DesktopDevice()) {
		$(this.element).multiselect('destroy').multiselect({});
	}
}

TourplanAmenityFilter.prototype.SetOptions = function(possibleCodes) {
	var toDisplay = _.filter(this.allLookups, function(lookup) {
		return _.find(possibleCodes, function(code) { return lookup.code == code; });
	});

	$(this.element).empty();

	$(this.element).html(TourplanRetailUtilities.GenerateOptionsString(_.map(
		toDisplay, 
		function(map) { 
			return {label:map.name, value:map.code}
		}
	)));

	if (TourplanRetailUtilities.DesktopDevice()) {
		$(this.element).multiselect('destroy').multiselect({});
	}
}

TourplanAmenityFilter.prototype.GetSelected = function() {
	return $(this.element).val() ? $(this.element).val().join(',') : null;
}

TourplanAmenityFilter.prototype.SetSelected = function(selected) {
	$(this.element).val(selected);

	if (TourplanRetailUtilities.DesktopDevice()) {
		$(this.element).multiselect('destroy').multiselect({});
	}
}