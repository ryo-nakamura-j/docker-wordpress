var TourplanRetailUtilities = function(){};

TourplanRetailUtilities.loadingImage = function() {
	return "<div style='width:100%;text-align:center'><img src='" + $("#tourplanRetailConfig").attr('loadingimage') + "' /></div>";
}

TourplanRetailUtilities.ParseQty = function(qty) {

	var regexResult = qty.match(/(?:(\d+)A)*(?:(\d+)C)*(?:(\d+)I)*/);

	var adults = regexResult[1] ? parseInt(regexResult[1]) : 0;
	var children = regexResult[2] ? parseInt(regexResult[2]) : 0;
	var infants = regexResult[3] ? parseInt(regexResult[3]) : 0;

	return {
		adults: adults,
		children:children,
		infants:infants
	}
}

TourplanRetailUtilities.subarray = function(arr, subArr) {
	for (i = 0; i < subArr.length; i++) {
		if (!_.includes(arr, subArr[i])) { return false; }
	}
	return true;
}

TourplanRetailUtilities.GetLookupMap = function(mapping, target, knowns) {
	return _.filter(window.tpSearchParams.lookupmaps, function(lookup) {
		return lookup.target == target &&
				lookup.mapping == mapping &&
				TourplanRetailUtilities.subarray(lookup.bindings, knowns);
	});
}

TourplanRetailUtilities.GenerateOptionsString = function(options, all, disabledOption) {
	var optionsString = "";
	if (disabledOption) {
		optionsString += "<option value='' disabled></option>";
	}
	if (all) {
		optionsString += "<option value='All'>All</option>";
	}
	_.forEach(options, function(option) {
		if (!(option.label === "")){
			optionsString += "<option value='" + option.value + "'>" + option.label + "</option>";
		}
	});
	return optionsString;
}

TourplanRetailUtilities.DeserializeURLParameters = function(urlString) {
	return _.filter(
		_.map(urlString.split('&'), function(pair) {
			var tmp = pair.split('=');
			return {
				name: tmp[0],
				value: decodeURIComponent(tmp[1])
			}
		}), function(x) {
			return !_.isEmpty(x.name);
		});
}

TourplanRetailUtilities.UpdateURLSearch = function(newSearchString) {
	if ( window.history.pushState == null )
		return;
	window.history.pushState(
		{},
		"",
		window.location.href.substr(0, window.location.href.indexOf(window.location.search)) + '?' + newSearchString
	);
}

TourplanRetailUtilities.GetNameValuePair = function(array, name) {
	return _.find(array, function(e) { return e.name == name; });
}

TourplanRetailUtilities.DesktopDevice = function() {
	return (!Modernizr.touch || 
		!Modernizr.inputtypes.date ||
		$("body").hasClass("desktop"));
}

TourplanRetailUtilities.GetDefaultConfigs = function(serviceButton) {
	return _.find(tpServiceButtonConfigs, function(configs) {
		return configs.serviceButton == serviceButton;
	}).config;
}

TourplanRetailUtilities.ToSafeCSSName = function(name) {
    return name.replace(/[^a-zA-Z0-9]/g, function(s) {
        var c = s.charCodeAt(0);
        if (c == 32) return '-';
        if (c >= 65 && c <= 90) return '_' + s.toLowerCase();
        return '__' + ('000' + c.toString(16)).slice(-4);
    });
}

TourplanRetailUtilities.SerializeKeyValuePair = function(elements) {
	var kvp = {};
	_.forEach(elements, function(x) {
		kvp[$(x).attr('name')] = $(x).val();
	});
	return kvp;
}

TourplanRetailUtilities.GetDeliveryFees = function(srb, suppliercode, productcode) {
	var deliveryFees = _.filter(window.tpDeliveryFees, function(fee) {
		return !(_.isEmpty(fee.srbs) && _.isEmpty(fee.suppliercodes) && _.isEmpty(fee.productcodes)) &&
			(_.isEmpty(fee.srbs) || _.includes(fee.srbs.split(','), srb)) &&
			(_.isEmpty(fee.suppliercodes) || _.includes(fee.suppliercodes.split(','), suppliercode)) &&
			(_.isEmpty(fee.productcodes) || _.includes(fee.productcodes.split(','), productcode));
	})
	return !_.isEmpty(deliveryFees) ? deliveryFees : false;
}

TourplanRetailUtilities.AvailabilityLookup = function(avail) {
	switch(avail) {
		case "OK":
			return "Available";
		case "RQ":
			return "On Request";
		case "NO":
			return "Full";
		default:
			return avail;
	}
}

/// dob: Moment.js Date of Birth
/// ageLimits: associative array of age limits
/// serviceDate: Moment.js Service Date
TourplanRetailUtilities.PaxTypeFromDob = function(dob, ageLimits, serviceDate) {
	if (_.has(ageLimits, 'A')) {
		var minDate = serviceDate.clone().subtract((ageLimits['A'].max + 1), 'years').add(1, 'days');
		var maxDate = serviceDate.clone().subtract(ageLimits['A'].min, 'years');

		if (dob.isBetween(minDate, maxDate, null, '[]')) {
			return 'A';
		}
	}
	if (_.has(ageLimits, 'C')) {
		var minDate = serviceDate.clone().subtract((ageLimits['C'].max + 1), 'years').add(1, 'days');
		var maxDate = serviceDate.clone().subtract(ageLimits['C'].min, 'years');

		if (dob.isBetween(minDate, maxDate, null, '[]')) {
			return 'C';
		}
	}
	if (_.has(ageLimits, 'I')) {
		var minDate = serviceDate.clone().subtract((ageLimits['I'].max + 1), 'years').add(1, 'days');
		var maxDate = serviceDate.clone().subtract(ageLimits['I'].min, 'years');

		if (dob.isBetween(minDate, maxDate, null, '[]')) {
			return 'I';
		}
	}
	return false;
}

TourplanRetailUtilities.ValueOrDefault = function(value, dft) {
	return _.isEmpty(value) ? dft : value;
}

TourplanRetailUtilities.ROOMTYPES = [
	{name:'Single',	value:'sg',	max:1,	max_ad:1},
	{name:'Double',	value:'db',	max:2,	max_ad:2},
	{name:'Twin',	value:'tw',	max:2,	max_ad:2},
	{name:'Triple',	value:'tr',	max:3,	max_ad:3},
	{name:'Quad',	value:'qd',	max:4,	max_ad:4}
];

TourplanRetailUtilities.DATEFORMATS = {
	DISPLAY: 'DD-MM-YYYY',
	DATA: 'YYYY-MM-DD'
}

TourplanRetailUtilities.PIKADAYDEFAULTS = {
	classes: "form-control",
	placeholder: "DD-MM-YYYY",
	outputFormat: "YYYY-MM-DD",
	format: "DD-MM-YYYY",
	checkIfNativeDate: function () {
    	return Modernizr.inputtypes.date && (Modernizr.touch && navigator.appVersion.indexOf("Win") === -1);
	}
}