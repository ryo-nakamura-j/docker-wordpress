

TourplanRailPassControlGroup = function(rootElement, searchConfigs, productConfigs) {
	_.extend(this, new TourplanCommonControl(rootElement, searchConfigs, productConfigs));

	var parentGroup = this;

	this.productControls = _.map(this.productConfigs, function(conf) {
		var className = '.rail-product-' + conf.productid + ' .plugin_control';
		var destElement = $(parentGroup.rootElement).find(className);
		var template = parentGroup.templates.resultTemplate;

		return new TourplanNonAccomProductControl(conf, destElement, template);
	});

	var productImpressions = [];

	this.GetRates(function(supplier, rates) {
		var ind = 1;
		_.forEach(rates, function(rate) {
			var control = _.find(parentGroup.productControls, function(ctrl) {return rate.productid == ctrl.configs.productid; });
			var product = new TourplanProduct(_.find(supplier.products, function(prod) {return rate.productid == prod.productid; }));
			product['rates'] = rate;
			product['configs'] = _.find(parentGroup.productConfigs, function(conf) { return rate.productid == conf.productid; }).configs;
			// console.log(product);
			control.destinationElement.html(control.template(product));

			productImpressions.push({
				'id' : product.productid,
				'name' : product.name,
				'category' : product.srb,
				'list' : 'Japan Rail Pass',
				'price' : {'adult' :(product.rates.adult/100),'child':(product.rates.child/100)},
				'position' : ind
			});
			ind++;

			control.destinationElement.find('select.passenger-counter').change(function() {
				var counters = _.sum(_.map(control.destinationElement.find('select.passenger-counter'), function(x) { return parseInt($(x).val()); }));
				if (counters > 0) {
					control.destinationElement.find('button.book').removeProp("disabled");
				} else {
					control.destinationElement.find('button.book').prop("disabled", "disabled");
				}
			}).change();

			control.destinationElement.find('button.book').click(function() {

				var config = TourplanRetailUtilities.ParseQty(control.GetQty());
				var pax = [];
				var price = 0;

				for (i = 0; i < config.adults; i++) { pax.push({paxtype:'A'}); price += rate.adult; }
				for (i = 0; i < config.children; i++) { pax.push({paxtype:'C'}); price += rate.child; }
				for (i = 0; i < config.infants; i++) { pax.push({paxtype:'I'}); price += rate.infant; }

				config['pax'] = pax;


				CartInterface.addServiceLine(
					supplier.supplier,
					product,
					product.availability[0],
					{ 
						rateid: 'Default',
						qty: control.GetQty(),
						configs:[config],
						price:price,
						pricedisplay: (price / 100).toFixed(2)
					},
					{
						success: function() { window.location = $("#tourplanRetailConfig").attr("itinerarypage"); }
					}
				);
			});
		})

		dataLayer.push({
			'event':'gtm.dom',
			'eventCategory':'Ecommerce', 
			'eventAction': 'Impression',
			'currencyCode': $("#tourplanRetailConfig").attr('currency'),
			'ecommerce': {
				'impressions': productImpressions
			}
		})
	});
}

TourplanRailPassControlGroup.prototype.GetSearchDate = function() {
	return (_.isEmpty(this.searchConfigs.searchDate) ?
		moment().add(getServiceButtonConfig(this.searchConfigs.srb, "searchDateOffset", 0), "days").format('YYYY-MM-DD') :
		moment(this.searchConfigs.searchDate, 'YYYY-MM-DD').format('YYYY-MM-DD')
		);
}

TourplanRailPassControlGroup.prototype.GetQty = function() {
	return (_.isEmpty(this.searchConfigs.defaultQty) ?
		getServiceButtonConfig(this.searchConfigs.srb, "defaultQty") :
		this.searchConfigs.defaultQty);
}

TourplanRailPassControlGroup.prototype.GetRates = function(callback) {
	var parentControl = this;
	var productList = _.map(this.productConfigs, 'productid');
	REI.Supplier_Old(parentControl.searchConfigs.supplierid, {
		date:parentControl.GetSearchDate(),
		scu:1,
		qty:parentControl.GetQty(),
		info:'roomTypes'
	}).done(function(supplier) {
		REI.Rates({
			ids:productList.toString(','),
			date:parentControl.GetSearchDate()
		}).done(function(data) {
			callback(supplier, data);
		});
	})
}