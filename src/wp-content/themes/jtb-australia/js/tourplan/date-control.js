
TourplanDateControl = function(container, fromInput, toInput, scuInput, 
	configs, configuration, classes) {
	var dateControl = this;
	this.fromInput = fromInput;
	this.toInput = toInput;
	this.scuInput = scuInput;
	this.container = container;
	this.configs = configs;
	this.configuration = configuration;
	if ( classes == null )
		 classes = "form-control";

	var minDate;
	var maxDate;
	var maxScu = parseInt(this.configuration.max_scu);

	if (this.configuration.min_date_type == "fixed") {
		minDate = moment(this.configuration.min_date_val, TourplanRetailUtilities.DATEFORMATS.DATA);
	} else {
		minDate = moment().add(this.configuration.min_date_val, "days");
	}

	if (this.configuration.max_date_type == "fixed") {
		maxDate = moment(this.configuration.max_date_val, TourplanRetailUtilities.DATEFORMATS.DATA);
	} else {
		maxDate = moment().add(this.configuration.max_date_val, "days");
	}

	this.customDatePicker = (!Modernizr.touch || 
		!Modernizr.inputtypes.date ||
		$("body").hasClass("desktop"));

	if (configs.startDate.isAfter(minDate)) {
		this.fromInput.val(configs.startDate.format(TourplanRetailUtilities.DATEFORMATS.DATA));
		var toDate = new moment(configs.startDate).add(configs.scu ? parseInt(configs.scu) : parseInt(configs.minLength), 'days');
		this.toInput.val(toDate.format(TourplanRetailUtilities.DATEFORMATS.DATA));
		this.scuInput.val(toDate.diff(configs.startDate, "days"));
	} else {
		this.fromInput.val(minDate.format(TourplanRetailUtilities.DATEFORMATS.DATA));
		var toDate = new moment(minDate).add(configs.scu ? parseInt(configs.scu) : parseInt(configs.minLength), 'days');
		this.toInput.val(toDate.format(TourplanRetailUtilities.DATEFORMATS.DATA));
		this.scuInput.val(toDate.diff(minDate, "days"));
	}


	if (this.fromInput.length > 0) {
		this.fromInputPika = pikadayResponsive(this.fromInput, {
			classes: classes,
			placeholder: TourplanRetailUtilities.DATEFORMATS.DISPLAY,
			outputFormat: TourplanRetailUtilities.DATEFORMATS.DATA,
			format: TourplanRetailUtilities.DATEFORMATS.DISPLAY,
			minDate: minDate,
			maxDate: maxDate,
			checkIfNativeDate: function () {
		    	return Modernizr.inputtypes.date && (Modernizr.touch && navigator.appVersion.indexOf("Win") === -1);
			}
		}); 
	}

	if (this.toInput.length > 0) {
		this.toInputPika = pikadayResponsive(this.toInput, {
			classes: classes,
			placeholder: TourplanRetailUtilities.DATEFORMATS.DISPLAY,
			outputFormat: TourplanRetailUtilities.DATEFORMATS.DATA,
			format: TourplanRetailUtilities.DATEFORMATS.DISPLAY,
			// minDate: configs.scu ? new moment(minDate).add(configs.scu, "days") : minDate,
			minDate: minDate,
			maxDate: maxDate,
			checkIfNativeDate: function () {
		    	return Modernizr.inputtypes.date && (Modernizr.touch && navigator.appVersion.indexOf("Win") === -1);
			}
		});
	}

	this.fromInput.change(function(e) {
		var days = dateControl.scuInput.val();

		var fromDate = new moment(dateControl.fromInput.val(), TourplanRetailUtilities.DATEFORMATS.DATA);
		var newToDate = new moment(fromDate).add(days, "days");
		var minToDate = new moment(fromDate).add(1, "days");
		var maxToDate = new moment(minToDate).add(maxScu, "days");

		if (dateControl.toInputPika !== undefined) {
			dateControl.toInputPika.setMinDate(minToDate);
			
			if (maxDate.isBefore(maxToDate)) {
				var dateDiff = maxDate.diff(fromDate, "days");
				dateControl.scuInput.empty();
				_.forEach(_.range(1, dateDiff + 1), function(x) {
					dateControl.scuInput.append("<option value='" + x + "'>" + x + "</option>");
				});

				dateControl.toInputPika.setMaxDate(maxDate);
				dateControl.toInputPika.setDate(maxDate);
				dateControl.scuInput.val(dateDiff);

			} else {
				dateControl.scuInput.empty();
				_.forEach(_.range(1, maxScu + 1), function(x) {
					dateControl.scuInput.append("<option value='" + x + "'>" + x + "</option>");
				});

				dateControl.toInputPika.setMaxDate(maxToDate);
				dateControl.toInputPika.setDate(newToDate);
			}
		}
	});

	this.scuInput.change(function(e) {
		var fromDate = new moment(dateControl.fromInput.val(), TourplanRetailUtilities.DATEFORMATS.DATA);
		var scuQty = dateControl.scuInput.val();
		var newToDate = new moment(fromDate).add(scuQty, "days");

		if (dateControl.toInputPika !== undefined) {
			dateControl.toInputPika.setDate(newToDate);
		}
	});

	this.toInput.change(function(e) {
		var fromDate = new moment(dateControl.fromInput.val(), 'YYYY-MM-DD');
		var toDate = new moment(dateControl.toInput.val(), 'YYYY-MM-DD');

		var difference = toDate.diff(fromDate, 'days');

		dateControl.scuInput.val(difference);
	});
}

TourplanDateControl.prototype.GetFromDate = function() {
	return this.fromInput.val();
}

TourplanDateControl.prototype.GetSCU = function() {
	return this.scuInput.val();
}