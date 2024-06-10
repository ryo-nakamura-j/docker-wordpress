function SGPBDetect() {
}

SGPBDetect.prototype.init = function () {
	this.pluginData = [];
	this.finishBtn = '<div class="sgpb-text-center"><button class="sgpb-btn sgpb-btn-more_extensions" id="sgpbFinishUpdatingProcess">Finish the process</button></div>';
	this.blockScreen();
	this.modalDetectionStyles();
	this.checkIfModalClosed();
};
SGPBDetect.prototype.blockScreen = function () {
	var body = this.generateHtml(SGPB_JS_DETECTIONS);
	this.modalDetection(SGPB_JS_DETECTIONS.modalData.header, SGPB_JS_DETECTIONS.modalData.logo, body);
	this.updateActions();
};
SGPBDetect.prototype.generateHtml = function (dataToGenerate) {
	let html = '';
	if (dataToGenerate.autoUpdate.length) {
		html = '<ul class="sgpb-plugins-list sgpb-plugins-list-auto-update">';
		for (let i = 0; i < dataToGenerate.autoUpdate.length; i++) {
			html += `<li class="sgpb-update-plugin sgpb-update-plugin-progress updateMyPlugin"
data-name="${dataToGenerate.autoUpdate[i].plugin.name}"
data-key="${dataToGenerate.autoUpdate[i].plugin.pluginKey}"
data-slug="${dataToGenerate.autoUpdate[i].slug}">updating... ${dataToGenerate.autoUpdate[i].plugin.name}</li>`
		}
		html += '<input type="hidden" id="sgpbCanUpdateNextPlugin" /><input type="hidden" id="sgpbNextPluginIndex" value="0" /></ul>';
	}

	if (dataToGenerate.manualUpdate.length) {
		html += dataToGenerate.modalData.manualMessage;
		html += '<ul class="sgpb-plugins-list"><li class="sgpb-text-center">';
		for (let i = 0; i < dataToGenerate.manualUpdate.length; i++) {
			html += `<a href="${dataToGenerate.manualUpdate[i].plugin.url}" target="_blank">${dataToGenerate.manualUpdate[i].plugin.name}</a>`;
			html += dataToGenerate.manualUpdate[i + 1] !== undefined ? ', ' : '';
		}
		html += '</li></ul>';
		html += dataToGenerate.modalData.footerMessage;
	}

	return html;
};

SGPBDetect.prototype.update = function (args) {
	var that = this;
	args = _.extend({
		success: that.updatePluginSuccess,
		error: that.updatePluginError,
	}, args);
	return wp.updates.ajax('update-plugin', args);
};
SGPBDetect.prototype.updatePluginSuccess = function (response) {
	jQuery("li[data-key='" + response.plugin + "']").removeClass('updating sgpb-update-plugin-progress');
	jQuery("li[data-key='" + response.plugin + "']").addClass('sgpb-update-plugin-success');
	jQuery("#sgpbCanUpdateNextPlugin").val(1);
};
SGPBDetect.prototype.updatePluginError = function (response) {
	jQuery("li[data-key='" + response.plugin + "']").removeClass('updating sgpb-update-plugin-progress');
	jQuery("li[data-key='" + response.plugin + "']").addClass('sgpb-update-plugin-failed');
	jQuery("#sgpbCanUpdateNextPlugin").val(0);
};
SGPBDetect.prototype.updateActions = function () {
	var that = this;
	jQuery('.updateMyPlugin').each(function () {
		that.pluginData.push({
			plugin: jQuery(this).data('key'),
			slug: jQuery(this).data('slug'),
			name: jQuery(this).data('name')
		});
	});
	this.updateRecursion();
	setInterval(function () {
		if (jQuery("#sgpbCanUpdateNextPlugin").val() === '1') {
			that.updateRecursion();
		}
	}, 1000);
};
SGPBDetect.prototype.updateRecursion = function () {
	jQuery("#sgpbCanUpdateNextPlugin").val(0);
	if (!this.pluginData[+jQuery("#sgpbNextPluginIndex").val()]) {
		jQuery('.sgpb-plugins-list-auto-update').empty();
		if (!SGPB_JS_DETECTIONS.manualUpdate.length) {
			jQuery('.sgpb-modal-detection .sgpb-modal-body').append(this.finishBtn);
			jQuery('#sgpbFinishUpdatingProcess').on('click', function () {
				window.location.reload();
			});
		}
		return;
	}
	this.singleItem = this.pluginData[+jQuery("#sgpbNextPluginIndex").val()];
	jQuery("li[data-slug='" + this.singleItem.slug + "']").addClass('updating');
	this.update({plugin: this.singleItem.plugin, slug: this.singleItem.slug});
	jQuery("#sgpbNextPluginIndex").val(+jQuery("#sgpbNextPluginIndex").val() + 1)
};

SGPBDetect.prototype.modalDetectionStyles = function () {
	var css = '<style id="sgpb-modal-detection-styles">.sgpb-overflow-hidden{overflow: hidden!important;}.sgpb.sgpb-modal-detection {position: fixed;top: 0;bottom: 0;left: 0;right: 0;display: flex;justify-content: center;align-items: center;background: #00000082;z-index: 999;-webkit-backdrop-filter: blur(7px); backdrop-filter: blur(7px);}';
	css += '.sgpb.sgpb-modal-detection .sgpb-modal-detection-main {max-width: calc(100vw - 42%);max-height: calc(100vh - 15%); overflow-x: auto; background: #FFFFFF 0 0 no-repeat padding-box;box-shadow: 0 3px 20px #00000029;border-radius: 12px;padding: 20px;position: relative;}';
	css += '.sgpb.sgpb-modal-detection .sgpb-modal-detection-header {font-size: 30px; text-align: center}';
	css += '.sgpb.sgpb-modal-detection .sgpb-modal-logo {width: 150px; margin: 15px auto; display: block}';
	css += '.sgpb.sgpb-modal-detection .sgpb-modal-body {font-size: 24px;}';
	css += '.sgpb.sgpb-modal-detection .sgpb-modal-body a, .sgpb.sgpb-modal-detection .sgpb-modal-body .sgpb-plugins-list .sgpb-update-plugin {font-weight: normal; font-size: 17px; color: #2873EB;}';
	css += '.sgpb.sgpb-modal-detection .sgpb-modal-body p{font-size: 20px;margin: 0;}';
	css += '.sgpb.sgpb-modal-detection .sgpb-plugins-list-auto-update {max-width: 73%; margin: 0 auto;}';
	css += '.sgpb.sgpb-modal-detection .sgpb-modal-body p.sgpb-modal-footer-message{font-size: 15px;}';
	css += '.sgpb-plugins-list {display: flex; flex-direction: column;} .sgpb-update-plugin { display: inline-flex;justify-content: space-between;color: #2873eb} .sgpb-update-plugin:after { content: "";font: normal 20px/1 dashicons;display: block;}.sgpb-update-plugin-success{color:#00ae5d}.sgpb-update-plugin-success:after { content: "\\f147";}.sgpb-update-plugin-failed{color:#c00}.sgpb-update-plugin-failed:after { content: "\\f534";}.sgpb-update-plugin-progress:after { content: "\\f463";}.sgpb-update-plugin-progress.updating:after{animation: sgpbRotation 2s infinite linear;}@keyframes sgpbRotation { 0% { transform: rotate(0deg); } 100% { transform: rotate(359deg);}}';
	css += '</style>';
	jQuery(css).appendTo(document.body);
};

SGPBDetect.prototype.modalDetection = function (header, logo, body, url) {
	jQuery(document.body).addClass('sgpb-overflow-hidden');
	var modal = jQuery('<div/>', {
		"class": 'sgpb sgpb-modal-detection',
		html: jQuery('<div/>', {
			"class": 'sgpb-modal-detection-main',
			html:
				jQuery('<div/>', {
					"class": 'sgpb-modal-body',
					html: [
						jQuery(header),
						jQuery('<img />', {
							"class": 'sgpb-modal-logo',
							src: logo,
							alt: 'logo'
						}),
						body
					]
				})
		})
	});
	jQuery(modal).appendTo(document.body);
};

SGPBDetect.prototype.checkIfModalClosed = function () {
	var that = this;
	setInterval(function () {
		if (!jQuery('.sgpb.sgpb-modal-detection').length || !jQuery('#sgpb-modal-detection-styles').length) {
			that.init();
		}
	}, 800)
};

jQuery(document).ready(function () {
	sgpbDetect = new SGPBDetect();
	sgpbDetect.init();
});
