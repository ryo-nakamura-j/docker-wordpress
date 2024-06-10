
var TemplatesHelper = function() {
	if ($("#tourplanRetailConfig").length > 0) {
		var codes = {
			TVI: getAllCodes(['amc=tvi'], 'AMN'),
			TIC: getAllCodes(['amc=tic'], 'AMN'),
			TDE: getAllCodes(['amc=tde'], 'AMN'),
			TDU: getAllCodes(['amc=tdu'], 'AMN')
		};
	}
	return {
		ageRange: ageRange,
		amenities: amenities,
		availabilityString: availabilityString,
		cartTotal: cartTotal,
		case: caseFunc,
		cleanCSSString: cleanCSSString,
		convertContentMaxLimit: convertContentMaxLimit,
		curLang: curLang,
		curLangExt: curLangExt,
		default: defaultFunc,
		defaultImageURL: defaultImageURL,
		displayDate: displayDate,
		displayPrice: displayPrice,
		everyNth: everyNth,
		forLoop: forLoop,
		getCart: getCart,
		getValueByKey: getValueByKey,
		getNameDesc: getNameDesc,
		getOptionImageRoot: getOptionImageRoot,
		getOptionImageSrc: getOptionImageSrc,
		getServiceButtonConfig: getServiceButtonConfig,
		getServiceButtonMapConfig: getServiceButtonMapConfig,
		getSupplierImageSrcList: getSupplierImageSrcList,
		getProductImageSrcList: getProductImageSrcList,
		getNote: getNote,
		getTourIconRoot: getTourIconRoot,
		getTourIconSrc: getTourIconSrc,
		getProductPaxCount: getProductPaxCount,
		lowestPricedAvailability: lowestPricedAvailability,
		partOfString: partOfString,
		paxConfigLoop: paxConfigLoop,
		paxQtyLoop: paxQtyLoop,
		paxQty: paxQty,
		paxString: paxString,
		productURL: productURL,
		propWithLang: propWithLang,
		qtyLoop: qtyLoop,
		randomId: randomId,
		readPropTranslations: readPropTranslations,
		repeatString: repeatString,
		roomQtyLoop: roomQtyLoop,
		roomTypeString: roomTypeString,
		nth: nth,
		isMobile: isMobile,
		supplierURL: supplierURL,
		switch: switchFunc,
		tourIcon: tourIcon,
		tp_lookup: tp_lookup,
		imagesBaseURL: imagesBaseURL,
		formatDate: formatDate,
		getProperty: getProperty,
		getNotes: getNotes,
		optionImage: optionImage,
		productLink: productLink,
		loadingImage: loadingImage,
		defaultImage: defaultImage,
		getAvailability: getAvailability,
		serviceButtonConfig: serviceButtonConfig,
		serviceButtonConfigContains: serviceButtonConfigContains,
		bookingFeeLabel: bookingFeeLabel,
		getSearchLabel: getSearchLabel,
		visitString: visitString,
		resetCheckoutPage: resetCheckoutPage,
	};
	function readPropTranslations( target, propName, servicetype, source, noteConfigName ) {
		var code = getServiceButtonConfig( 
			servicetype, noteConfigName,"" );
		if ( code != "" && curLangExt() != "" )
			target[ propName + curLangExt() ] = getNote( source, code, 'text', target[ propName ] );
	}
	function getNameDesc( pax, format ) {
		var f = getServiceButtonConfig("", format, "" );
		f = f.replace("firstnamelang", pax.firstnamelang);
		f = f.replace("lastnamelang", pax.lastnamelang);
		f = f.replace("firstname", pax.firstname);
		f = f.replace("middlename", pax.middlename);
		f = f.replace("lastname", pax.lastname);
		f = f.replace("title", pax.title);
		return f;
	}
	// format is either html or text
	function getNote( product, code, format, dft ) {
		var rlt = dft;
		_.forEach( product.notes, function( n ) {
			if ( n.code == code )
				rlt = n[format];
		});
		return rlt;
	}
	function propWithLang( obj, propname ) {
		if ( obj == null || propname == null )
			return null;
		var propnameWithLang = propname + curLangExt();
		if ( obj[propnameWithLang] )
			return obj[propnameWithLang];
		return obj[propname];
	}
	function curLangExt() {
		if ( window && window.tpCurLang && window.tpCurLang != "" )
			return "-" + window.tpCurLang;
		return "";
	}
	function curLang() {
		if ( window && window.tpCurLang )
			return window.tpCurLang;
		return "";
	}
	function resetCheckoutPage() {
		if ( checkoutVueData )
			checkoutVueData.resetCheckingOut();
	}
	function getCart( callback, cartUrl ) {
		var cartUrl = cartUrl ? cartUrl : $("#tourplanRetailConfig").attr('carturl');
		$.ajax({
			type:'get',
			url:cartUrl,
			dataType:'json',
		}).done(function(cartData) {
			if (callback) {
				callback( cartData )
			}
		});
	}
	function isMobile() {
		return $(window).width() <= 768;
	}
	function lowestPricedAvailability( product ) {
		return _.min(product.availability, function(avail) {
			return avail.TotalPrice;
		});
	}
	function visitString( amenitiesList, code ) {
		var rlt = "";
		var list = amenities( amenitiesList, code);
		for ( var idx in list ) {
			var a = list[idx];
			rlt += a.value;
			if ( idx != list.length - 1 )
				rlt += ', ';
		}
		return rlt;
	}
	function randomId() {
		// https://gist.github.com/gordonbrander/2230317
		return 'rand_' + Math.random().toString(36).substr(2, 9);
	}
	function getProductPaxCount(p, matching){
		if ( p == null || p.availability == null || p.availability.length == 0 )
			return "Not Available";
		var rlt;
		if ( matching == 'A' )
			rlt = p.availability[0].Qty.match(/[0-9]*(?=A)/);
		else if ( matching == "C" )
			rlt = p.availability[0].Qty.match(/[0-9]*(?=C)/);
		else if ( matching == "I" ) 
			rlt = p.availability[0].Qty.match(/[0-9]*(?=I)/);
		if ( rlt )
			return rlt[0];
	}
	function partOfString( str, separator, index ) {
		var list = str.split( separator );
		if ( list.length > index )
			return _.trim( list[index] );
		return str;
	}
	function getProductImageSrcList( product, max ) {
		var a = [];
		a.push( getOptionImageRoot() + '/' + product.code + "tn.jpg" );
		for ( var i = 1; i <= max - 1; i++ ) {
			a.push( getOptionImageRoot() + '/' + product.code + "tn." + i + ".jpg" );
		}
		return a;
	}
	function getSupplierImageSrcList( supplier, max ){
        var a = [];
        for ( var i = 1; i <= max; i++ ) {
            a.push(imagesBaseURL() + '/Supplier_' + supplier.code + '/' + supplier.code + '.' + i + '.jpg');
        }
        return a;
    }
	function getSearchLabel( config, key, labelName, defaultLabel ) {
		var k = getValueByKey( config, key);
		if ( k == null || serviceButtonConfig(k, labelName) == null )
			return defaultLabel;
		return serviceButtonConfig(k, labelName);
	}
	function cleanCSSString(string) {
		return TourplanRetailUtilities.ToSafeCSSName(string);
	}
	function repeatString(string, times, delimiter) {
		var tmp = [];
		for (var i = 0; i <= parseInt(times); i++) {
			tmp.push(string);
		}
		return tmp.join(delimiter);
	}
	function forLoop(from, to, incr, options) {
		var fn = options.fn; inverse = options.inverse;
		var ret = '';
		for (var i = from; i < to; i += incr) {
			ret += fn(_.extend({}, {
				index: i, 
				indexPlusOne: i + 1,
				first: i == 0,
			}));
		}
		return ret;
	}
	function convertContentMaxLimit( str, max ) {
        if ( str == null || str.length < max )
          return str;
        var rlt = str.substring(0, max);
        //re-trim if we are in the middle of a word
        rlt = rlt.substr(0, Math.min(rlt.length, rlt.lastIndexOf(" ")));
        return rlt + "...";
	}
	function paxConfigLoop(config, options) {
		var fn = options.fn; inverse = options.inverse;
		var ret = "";
		var tmp = [
			{ paxType:'A', paxTypeLong:'adult', count:config.adults },
			{ paxType: 'C', paxTypeLong:'child', count:config.children },
			{ paxType: 'I', paxTypeLong:'infant', count:config.infants }
		];

		var index = 0;
		for (var i = 0; i < tmp.length; i++) {
			for (var j = 0; j < tmp[i].count; j++) {
				ret += options.fn(_.extend(config, {
					index:index,
					paxNum:index + 1,
					paxNumOfType: j + 1,
					paxType: tmp[i].paxType,
					paxTypeLong:  tmp[i].paxTypeLong
				}));
				index += 1;
			}
		}
		return ret;
	}
	function qtyLoop(qty, options) {
		var fn = options.fn; inverse = options.inverse;
		var ret = '';
		var qtyList = [];
		qtyList.push({type:'A', longtype:'adult', value: (_.isEmpty(ad = qty.match(/([0-9]*)A/)) ? 0 : parseInt(ad[1]))});
		qtyList.push({type:'C', longtype:'child', value: (_.isEmpty(ch = qty.match(/([0-9]*)C/)) ? 0 : parseInt(ch[1]))});
		qtyList.push({type:'I', longtype:'infant', value:(_.isEmpty(inf = qty.match(/([0-9]*)I/)) ? 0 : parseInt(inf[1]))});
		var paxNum = 1;
		for (var i = 0; i < qtyList.length; i++) {
			for (var j = 0; j < qtyList[i].value; j++) {
				ret += fn(_.extend({}, {
					paxtype: qtyList[i].type,
					longpaxtype: qtyList[i].longtype, 
					paxtypenum:j,
					paxnum:paxNum
				}));
				paxNum += 1;
			}
		}
		return ret;
	}
	function paxQtyLoop(qty, options) {
		var quants = TourplanRetailUtilities.ParseQty(qty);
		var ret = "";
		var tmp = [
			{ paxType:'A', paxTypeLong:'adult', count:quants.adults },
			{ paxType: 'C', paxTypeLong:'child', count:quants.children },
			{ paxType: 'I', paxTypeLong:'infant', count:quants.infants }
		];

		var index = 0;
		for (var i = 0; i < tmp.length; i++) {
			for (var j = 0; j < tmp[i].count; j++) {
				ret += options.fn(_.extend(quants, {
					index:index,
					paxNum:index + 1,
					paxNumOfType: j + 1,
					paxType: tmp[i].paxType,
					paxTypeLong:  tmp[i].paxTypeLong
				}));
				index += 1;
			}
		}
		return ret;
	}
	function roomQtyLoop(qty, options) {
		var rooms = qty.split(',');
		var ret = "";

		// var index = 0;
		for (var i = 0; i < rooms.length; i++) {
			ret += options.fn(_.extend({}, {
				index: i,
				roomNum: index + 1,
			}));
			// index += 1;
		}
		return ret;
	}
	function switchFunc(value, options) {
		/// https://github.com/wycats/handlebars.js/issues/927
	    this._switch_value_ = value;
	    this._switch_break_ = false;
	    var html = options.fn(this);
	    delete this._switch_break_;
	    delete this._switch_value_;
	    return html;
	}
	function caseFunc(value, options) {
		var args = Array.prototype.slice.call(arguments);
	    var options    = args.pop();
	    var caseValues = args;

	    if (this._switch_break_ || caseValues.indexOf(this._switch_value_) === -1) {
	        return '';
	    } else {
	        if (options.hash.break === true) {
	            this._switch_break_ = true;
	        }
	        return options.fn(this);
	    }
	}
	function defaultFunc(options) {
	    if (!this._switch_break_) {
	        return options.fn(this);
	    }
	}
	function paxQty(qty, paxType) {
		return _.isEmpty(num = qty.match(new RegExp("([0-9]*)" + paxType))) ? 0 : parseInt(num[1]);
	}
	function paxString(qty, adultLabel, childLabel, infantLabel ) {
		var qtyList = Array();
		var adults = _.isEmpty(ad = qty.match(/([0-9]*)A/)) ? 0 : parseInt(ad[1]);
		var children = _.isEmpty(ch = qty.match(/([0-9]*)C/)) ? 0 : parseInt(ch[1]);
		var infants = _.isEmpty(inf = qty.match(/([0-9]*)I/)) ? 0 : parseInt(inf[1]);
		if ( !adultLabel )
			adultLabel = adults > 1 ? " Adults" : " Adult"
		if ( !childLabel )
			childLabel = children > 1 ? " Children" : " Child"
		if ( !infantLabel )
			infantLabel = infants > 1 ? " Infants" : " Infant"

		if (adults > 0) {
			qtyList.push( '' + adults + adultLabel );
		}
		if (children > 0) {
			qtyList.push( '' + children + childLabel );
		}
		if (infants > 0) {
			qtyList.push( '' + infants + infantLabel );
		}

		return qtyList.join(', ');
	}
	function cartTotal(servicelines) {
		var cartTotal = 0;
		_.forEach(servicelines, function(serviceline) {
			cartTotal += parseInt(serviceline.price);
		});
		return cartTotal;
	}
	function nth(array, n, options) {
		return options.fn(array[n]);
	}
	function supplierURL(supplierBasePage, supplierData, productData) {
		// var destUrl = supplierBasePage;
		// destURL += '/' + encodeURIComponent(supplierData.name);
		var searchParams = [
			{ name:'qty',	value:productData.availability[0].Qty },
			{ name:'date',	value:productData.availability[0].Date },
			{ name:'srb',	value:productData.srb },
			{ name:'scu',	value:productData.availability[0].Scu },
			{ name:'cty',	value:productData.cty },
			{ name:'dst',	value:productData.dst },
			{ name:'lcl',	value:productData.lcl },
			{ name:'supplierid', value:supplierData.supplierid },
			{ name:'info',	value:'roomtypes'
		}];
		return supplierBasePage + '/' + encodeURIComponent(supplierData.name) + '?' + $.param(searchParams);
	}
	function productURL(productBasePage, productData) {
		var searchParams = [
			{ name:'qty',	value:productData.availability[0].Qty },
			{ name:'date',	value:productData.availability[0].Date },
			{ name:'scu',	value:productData.availability[0].Scu },
			{ name:'productid', value:productData.productid }
		]
		return productBasePage + '/' + '?' + $.param(searchParams);
	}
	function roomTypeString(roomType) {
		var roomTypes = [
			{key:'sg',value:'Single'},
			{key:'db',value:'Double'},
			{key:'tw',value:'Twin'},
			{key:'tr',value:'Triple'},
			{key:'qd',value:'Quad'}
		];
		var roomCounts = [];
		_.forEach(roomTypes, function(rt) {
			var tmp = _.filter(roomType.split(','), function(x) { return x == rt.key; }).length;
			if (tmp > 0) { roomCounts.push(tmp + " " + rt.value); }
		});

		return roomCounts.join(',');
	}
	function availabilityString(avail) {
		var availTypes = {
			'rq':'On Request',
			'ok':'Available'
		};
		return (availTypes[avail.toLowerCase()] ? availTypes[avail.toLowerCase()] : avail);
	}
	function getValueByKey(kvpList, key) {
		return _.find(kvpList, function(kvp) { return kvp.name == key; }).value;
	}
	function tp_lookup(type, code, options) {
		if ( options ) {
			var lookup = _.find(window.tpSearchParams.lookups, function(x) {
				return x.type == type && x.code == code;
			});
			return options.fn(lookup);
		}
		else {
			var lookup = _.find(window.tpSearchParams.lookups, function(x) {
				return x.type == type && x.code == code;
			});
			return lookup;
		}
	}
	function imagesBaseURL() {
		return $("#tourplanRetailConfig").attr('imagesurl');
	}
	function defaultImageURL() {
		return $("#tourplanRetailConfig").attr('defaultimage');
	}
	function formatDate(date, dateFormat) {
		return moment(date, 'YYYY-MM-DD').format(dateFormat);
	}
	function getProperty(object,property) {
		return object[property];
	}
	function amenities(obj, category, options) {
		if ( options != null ) {
			var rVal = "";
			var lookups = getLookupsbyCodes(_.intersection(codes[category], obj));
			for (i = 0; i < lookups.length; i++) {
				var frst = i == 0;
				var lst = i == lookups.length - 1;
				rVal += options.fn({key:lookups[i].code, value:lookups[i].name, first:frst, last:lst});
			}
			return rVal;
		}
		else {
			var rVal = [];
			var lookups = getLookupsbyCodes(_.intersection(codes[category], obj));
			for (i = 0; i < lookups.length; i++) {
				var frst = i == 0;
				var lst = i == lookups.length - 1;
				rVal.push({ key:lookups[i].code, value:lookups[i].name });
			}
			return rVal;
		}
	}
	function getNotes(obj, category, type) {
		var noteObj = _.find(obj, function(note) {
			return note.code == category;
		});

		return noteObj ? noteObj[type] : undefined;
	}
	function getOptionImageRoot() {
		return $("#tourplanRetailConfig").attr('searchimagesurl');
	}
	function getOptionImageSrc( obj ) {
		return getOptionImageRoot() + '/' + obj + "tn.jpg";
	}
	function getTourIconRoot() {
		return $("#tourplanRetailConfig").attr('imagesurl');
	}
	function getTourIconSrc( iconCode ) {
		return getTourIconRoot() + '/TourIcons/' + iconCode + '.png';
	}
	function optionImage(obj, htmlClass) {
		var imgRoot = $("#tourplanRetailConfig").attr('imagesurl');
		var src = getOptionImageSrc( obj );
		var rlt = "<img class='" + htmlClass + "' src='" + src + "' onerror='this.onerror=null;this.src=\"" + imgRoot + "/default.jpg\"'>";
		return new Handlebars.SafeString(rlt);
	}
	function tourIcon(iconCode, altText, htmlClass) {
		var imgRoot = $("#tourplanRetailConfig").attr('imagesurl');
		var src = getTourIconSrc(iconCode);
		var rlt = "<img class='" + htmlClass + "' src='" + src + "' alt='" + altText + "' title='" + altText + "' />";
		return new Handlebars.SafeString(rlt);
	}
	function everyNth(context, every, options) {
	  var fn = options.fn, inverse = options.inverse;
	  var ret = "";
	  if(context && context.length > 0) {
	    for(var i=0, j=context.length; i<j; i++) {
	      var modZero = i % every === 0;
	      ret = ret + fn(_.extend({}, context[i], {
	        isModZero: modZero,
	        isModZeroNotFirst: modZero && i > 0,
	        isLast: i === context.length - 1,
	        index: i
	      }));
	    }
	  } else {
	    ret = inverse(this);
	  }
	  return ret;
	}
	function productLink(obj, serviceButton) {
		var productInfoURL = getServiceButtonConfig(serviceButton, 'productInfoPage');
		var seoSegment = (obj.productid + ' ' + obj.name).replace(/\s/g, '-').toLowerCase();
		var searchParams = "?";
		searchParams += "supplierid=" + obj.supplierid;
		searchParams += "&productid=" + obj.productid;
		searchParams += "&date=" + obj.availability[0].Date;
		searchParams += "&scu=1";
		searchParams += "&qty=1A";
		searchParams += "&srb=" + obj.srb;
		searchParams += "&cty=" + obj.cty;
		searchParams += "&dst=" + obj.dst;
		searchParams += "&searchurl=";

		var urlOrigin =  window.location.origin ? window.location.origin : window.location.protocol + '//' + window.location.hostname + (window.location.port ? ':' + window.location.port : "");
		return urlOrigin + productInfoURL + '/' + seoSegment + searchParams;
	}
	function displayPrice(price, decimalPlaces) {
		return (parseInt(price) / 100).toFixed(decimalPlaces);
	}
	function displayDate(date, outputFormat) {
		return moment(date, 'YYYY-MM-DD').format(outputFormat);
	}
	function loadingImage() {
		return $("#tourplanRetailConfig").attr('loadingimage');
	}
	function defaultImage() {
		return $("#tourplanRetailConfig").attr('defaultimage')
	}
	function getAvailability(product, options) {
		if ( options != null ) {
			var externalRate = _.find(product.availability, function(avail) {
				return avail.RateId != 'Default';
			});
			return externalRate ? options.fn(externalRate) : options.fn(product.availability[0]);
		}
		else {
			var externalRate = _.find(product.availability, function(avail) {
				return avail.RateId != 'Default';
			});
			return externalRate ? externalRate : product.availability[0];
		}
	}
	function serviceButtonConfig(serviceButton, configName, dft) {
		return getServiceButtonConfig(serviceButton, configName, dft);
	}
	function getServiceButtonMapConfig( serviceButton, configName, dft ) {
		return _.map(
			getServiceButtonConfig(serviceButton, configName, dft).split(','),
			function(x) {
				var spl = x.split('=');
				if (spl.length < 2) { 
					return {label:x, value:x}
				}
				else {
					return {label:spl[0], value:spl[1]}
				}
				
			}
		);
	}
	function serviceButtonConfigContains( serviceButton, configName, value ) {
		var l = getServiceButtonConfig(serviceButton, configName, "");
		if ( l != null )
			l = l.split(',');
		if ( l == null || l.length == 0 )
			return false;
		var rlt = false;
		_.forEach( l, function(ll) {
			if ( ll == value )
				rlt = true;
		})
		return rlt;
	}
	function bookingFeeLabel( serviceline ) {
		// This is calculated when repricing.
		// # TourplanServiceLineControl.prototype.UpdatePrice
		return ""; 
	}
	function ageRange(from, to) {
		if (!from || !to) { return ''; }
		else if (to >= 999) { return '(' + from + '+' + ')' }
		else { return '(' + from + '-' + to + ')'; }
	}
};

// Create helper instance
var templatesHelper = new TemplatesHelper();
// Regist handlebar functions
for ( var key in templatesHelper ) {
	Handlebars.registerHelper(key, templatesHelper[key]);
}

