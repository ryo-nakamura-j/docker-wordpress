
var TourplanCommonControl = function(rootElement, searchConfigs, productConfigs) {
	this.rootElement = rootElement;
	this.templates = this.LoadTemplates();
	this.searchConfigs = searchConfigs;
	this.productConfigs = productConfigs;
	this.pluginControl = $(this.rootElement).find('.plugin_control')[0];
}

TourplanCommonControl.prototype.LoadTemplates = function() {
	var templates = {};
	_.forEach(
		$(this.rootElement).children('script[type="text/x-handlebars-template"]'), 
		function(x) {
			templates[$(x).attr('class')] = Handlebars.compile($(x).html());
		}
	);
	return templates;
}



function containsAll(needles, haystack){ 
  for(var i = 0 , len = needles.length; i < len; i++){
     if($.inArray(needles[i].toLowerCase(), $.map(haystack, function(entry) {
     	return entry.toLowerCase();
     })) == -1) return false;
  }
  return true;
}

function getLookup(required, target) {
	return _.filter(window.tpSearchParams.lookupmaps, function(obj) {
		return containsAll(required, obj.bindings) && (target ? obj.target == target : true);
	})
}

function getServiceButtonConfigs(serviceButton) {
	var buttonConfigs = _.find(window.tpServiceButtonConfigs, function(configs) {
		return configs.serviceButton == serviceButton;
	});
	return buttonConfigs == undefined ? null : buttonConfigs.config;
}

function getServiceButtonConfig(serviceButton, configName, dft) {
	var buttonConfig = _.find(getServiceButtonConfigs(serviceButton), function(config) {
		return config.name ==  configName;
	});
	if (buttonConfig == undefined) {
		buttonConfig = _.find(getServiceButtonConfigs(''), function(config) {
			return config.name == configName;
		});
	}
	return buttonConfig == undefined ? (dft == null ? null : dft) : buttonConfig.value;
}

function trimSlash(input) {
	return (input.substr(input.length -1) == '/') ? input.substr(0, input.length -1) : input;
}

function getCodes(sources) {
	return _.uniq($.map(sources, function(obj) {
		return obj.codes;
	}));
}

function getAllCodes(required, target) {
	var lookups = getLookup(required, target);
	return getCodes(lookups).sort();
}

function getLookupsbyCodes(codes) {
	return window.tpSearchParams.lookups.filter(function(obj) {
		return codes.indexOf(obj.code) > -1;
	})
}