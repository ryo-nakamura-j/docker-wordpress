function SGPBModals() {
}

SGPBModals.prototype.changeModalContent = function (modal, newContent, targetId)
{
	this.moveContentBeforeModalDestroy(targetId, modal);
	const header = newContent.find(`#${targetId}Header`),
		body = newContent.find(`#${targetId}Body`),
		footer = newContent.find(`#${targetId}Footer`);
	modal.find('.sgpb-modal-header').text(header.text());
	modal.find('.sgpb-modal-body').html(body.children());
	modal.find('#sgpb-modal-footer-action-btn').html(footer.children());
	modal.data('target', newContent.attr('id'));
	modal.find('select').sgpbselect2();
};

/* TODO fix and remove this function */
SGPBModals.prototype.modalInit = function ()
{
	const that = this;

	jQuery('.sgpb-modal-btn').on('click', function () {
		const targetId = jQuery(this).data('target'),
			target = jQuery('#' + targetId),
			header = target.find(`#${targetId}Header`),
			body = target.find(`#${targetId}Body`),
			footer = target.find(`#${targetId}Footer`);
		const content = that.modalContent(targetId, header.text(), body.children(), footer.children());
		jQuery(content).appendTo(document.body);
		that.actionsCloseModal();
	})
};
SGPBModals.prototype.actionsCloseModal = function (closeOnBackDropClick = false, handleCloseEvent = undefined, handleEventArguments = null)
{
	const that = this;
	jQuery('.sgpb-close-icon, .sgpb-modal-cancel').on('click', function () {
		if (typeof handleCloseEvent != 'undefined') {
			handleCloseEvent(handleEventArguments)
		}
		that.moveContentBeforeModalDestroy(jQuery('.sgpb-modal').data('target'), jQuery('.sgpb-modal'));
		that.destroyModal('cancel');
	});
	/* handle backdrop click and close */
	if (closeOnBackDropClick){
		jQuery('.sgpb.sgpb-modal').on('click', function (e) {
			if(e.target !== this) return;
			if (typeof handleCloseEvent != 'undefined') {
				handleCloseEvent(handleEventArguments)
			}
			that.moveContentBeforeModalDestroy(jQuery('.sgpb-modal').data('target'), jQuery('.sgpb-modal'));
			that.destroyModal('cancel');
		});
	}

	jQuery(document).keyup(function (e) {
		if (e.keyCode === 27) {
			if (jQuery('.sgpb-modal')) {
				if (typeof handleCloseEvent != 'undefined') {
					handleCloseEvent(handleEventArguments)
				}
				that.moveContentBeforeModalDestroy(jQuery('.sgpb-modal').data('target'), jQuery('.sgpb-modal'));
				that.destroyModal('cancel');
			}
		}
	});

};

SGPBModals.prototype.openModal = function (content)
{
	jQuery(content).appendTo(document.body);
};
/* TODO add reason logic callback for full control modal destroy moment */
SGPBModals.prototype.destroyModal = function (reason)
{
	jQuery('.sgpb-modal').remove();
	jQuery(document.body).removeClass('sgpb-overflow-hidden');
};
SGPBModals.prototype.moveContentBeforeModalDestroy = function (id, modal)
{
	let target = jQuery('#' + id),
		body = target.find(`#${id}Body`),
		footer = target.find(`#${id}Footer`);
	if (!target.length)
		return;
	if (!body.length) {
		target.append(modal.find('.sgpb-modal-body').children());
	} else {
		body.append(modal.find('.sgpb-modal-body').children());
	}
	if (footer.length)
		footer.append(modal.find('#sgpb-modal-footer-action-btn').children());
};
SGPBModals.prototype.modalContent = function (targetId, header = '', body = '', footerConfirmBtn = '', hideFooter = false, styles = '', hideCloseBtn = false)
{
	jQuery(document.body).addClass('sgpb-overflow-hidden');
	return jQuery('<div/>', {
		"class": 'sgpb sgpb-modal',
		"style": styles,
		"data-target": targetId,
		html: jQuery('<div/>', {
			"class": 'sgpb-modal-main',
			html: [
				jQuery('<span/>', {
					"class": `sgpb-close-icon ${hideCloseBtn ? 'sgpb-display-none' : ''}`,
				}),
				jQuery('<h2/>', {
					"class": 'sgpb-modal-header',
					html: header
				}),
				jQuery('<div/>', {
					"class": 'sgpb-modal-body sgpb-width-100',
					html: body
				}),
				jQuery('<div/>', {
					"class": `sgpb-modal-footer ${hideFooter ? 'sgpb-display-none' : ''}`,
					html: [
						jQuery('<button/>', {
							"class": "sgpb-btn sgpb-btn-dark-outline sgpb-modal-cancel sgpb-margin-right-10",
							text: 'cancel'
						}),
						jQuery('<div/>', {
							id: 'sgpb-modal-footer-action-btn',
							"class": "sgpb-display-inline-block",
							html: footerConfirmBtn
						}),
					]
				})
			]
		})
	});
};

/*this action is to pass already opened modal new content for each part of source code*/
SGPBModals.prototype.changeModalContentAdvanced = function (modal, header = '', body = '', confirmBtn = '', oldTargetId, newTargetId)
{
	if (!modal)
		return;
	this.moveContentBeforeModalDestroy(oldTargetId, modal);
	if (header) {
		modal.find('.sgpb-modal-header').text(header);
	}
	if (body) {
		modal.find('.sgpb-modal-body').html(body);
	}
	if (confirmBtn) {
		modal.find('#sgpb-modal-footer-action-btn').html(confirmBtn);
	}
	if (newTargetId) {
		modal.data('target', newTargetId);
	}
};
jQuery(document).ready(function () {
	sgpbModalsObj = new SGPBModals();
});
