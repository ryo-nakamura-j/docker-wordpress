
TourplanExtensionFunctions = function(rootElement) {
	this.rootElement = rootElement;
	this.templates = this.LoadTemplates();
}

TourplanExtensionFunctions.prototype.LoadTemplates = function() {
	var templates = {};
	_.forEach(
		$(this.rootElement).children('script[type="text/x-handlebars-template"]'),
		function(template) {
			templates[$(template).attr('class')] = Handlebars.compile($(template).html());
		});	
	return templates;
}