

var TourplanRetailInterface = function(engineURL) {
	this.url = engineURL;
}

TourplanRetailInterface.prototype.ajax = function(overloads) {
	return $.ajax({
		type: overloads.type == null ? 'GET' : overloads.type,
		url: overloads.url == null ? this.engineURL : overloads.url,
		dataType: overloads.dataType == null ? "jsonp" : overloads.dataType,
		jsonp: overloads.jsonp == null ? 'jsonp' : overloads.jsonp,
		success: overloads.success == null ? $.noop : overloads.success,
		error: overloads.error == null ? $.noop : overloads.error,
		timeout: overloads.timeout == null ? parseInt($("#tourplanRetailConfig").attr("reqTimeout")) : overloads.timeout
	});
}

TourplanRetailInterface.prototype.Supplier_Old = function(supplierId, params) {
	var url = this.url + "/supplier/" + supplierId;

	var paramString = "?" + $.param(params);

	url += paramString.length > 1 ? paramString : '';

	console.log(url);

	return $.ajax({
		type: 'GET',
		url: url,
		dataType: 'jsonp',
		jsonp: 'jsonp'
	});
}

TourplanRetailInterface.prototype.Supplier = function(supplierId, params, callback) {
	var url = this.url + "/supplier/" + supplierId;

	var paramString = "?" + $.param(params);

	url += paramString.length > 1 ? paramString : '';

	console.log(url);

	return $.ajax({
		type: 'GET',
		url: url,
		dataType: 'jsonp',
		jsonp: 'jsonp',
		success: callback
	});
}

TourplanRetailInterface.prototype.Rates = function(params) {
	var url = this.url + "/rates";

	var paramString = "?" + $.param(params);

	url += paramString.length > 1 ? paramString : '';

	console.log(url);

	return this.ajax({url: url});
}

TourplanRetailInterface.prototype.Product = function(id, params, callback) {
	var url = this.url + "/product";

	if (id) { url += "/" + id; }

	var paramString = "?" + $.param(params);

	url += paramString.length > 1 ? paramString : '';

	console.log(url);

	return this.ajax({
		url: url,
		success:callback
	});
}

TourplanRetailInterface.prototype.Product_Old = function(id, params) {
	var url = this.url + "/product";

	if (id) { url += "/" + id; }

	var paramString = "?" + $.param(params);

	url += paramString.length > 1 ? paramString : '';

	console.log(url);

	return this.ajax({url: url});
}

TourplanRetailInterface.prototype.Availability = function(params, successCallback, errorCallback) {
	var thisInterface = this;
	var url = this.url + '/availability';

	var paramString = "?" + $.param(params);

	url += paramString.length > 1 ? paramString : '';

	console.log(url);

	return this.ajax({
		url: url
	}).done(function(x) {
		return thisInterface.ajax({
			url: url + '&reqid=' + x.reqid, //thisInterface.url + '/availability?reqid=' + x.reqid,
			success: successCallback
		}).fail(errorCallback);
	}).fail(errorCallback);
}

TourplanRetailInterface.prototype.Pricing = function(bookingData, callback) {
	var thisInterface = this;
	var url = this.url + '/pricing';

	console.log(url);

	return this.ajax({
		url: url,
		type: "POST",
		data: bookingData,
		success: callback
	});
}
