Vtiger_Popup_Js.prototype.registerCreateRecordFromNoResultsSearch = function() {
	var popupPageContainer = this.getPopupPageContainer();
	const params = {};
	jQuery('.searchRow .inputElement, .searchRow .select2', popupPageContainer).each((i, e) => {
		const valor = jQuery(e).val();
		const fieldName = jQuery(e).attr('name');
		if (valor) {
			if (jQuery(e).is('select')) {
				//como los campos tipo select pueden tener +1 valores seleccionados => me quedo con el 1°
				params[fieldName] = valor[0];
			} else {
				params[fieldName] = valor;
			}
		}
	});
	app.helper.hidePopup();
    jQuery('[name=parent_id]').parent().find('.createReferenceRecord').trigger('click', params);
};
Vtiger_Index_Js.prototype.referenceCreateHandler = function(container, paramsData) {
	var thisInstance = this;
	var postQuickCreateSave = function(data) {
		var moduleName = thisInstance.getReferencedModuleName(container);
		var params = {};
		params.name = data._recordLabel;
		params.id = data._recordId;
		params.module = moduleName;
		thisInstance.setReferenceFieldValue(container, params);

		var tdElement = thisInstance.getParentElement(container.find(`[value="${moduleName}"]`));
		var sourceField = tdElement.find('input[class="sourceField"]').attr('name');
		var fieldElement = tdElement.find('input[name="'+sourceField+'"]');
		thisInstance.autoFillElement = fieldElement;
		thisInstance.postRefrenceSearch(params, container);

		tdElement.find('input[class="sourceField"]').trigger(Vtiger_Edit_Js.postReferenceQuickCreateSave, {'data' : data});
	}

	var referenceModuleName = this.getReferencedModuleName(container);
	var quickCreateNode = jQuery('#quickCreateModules').find('[data-name="'+ referenceModuleName +'"]');
	if(quickCreateNode.length <= 0) {
		var notificationOptions = {
			'title' : app.vtranslate('JS_NO_CREATE_OR_NOT_QUICK_CREATE_ENABLED')
		}
		app.helper.showAlertNotification(notificationOptions);
	}
	quickParams = {'callbackFunction':postQuickCreateSave};
	if (paramsData) {
		quickParams.data = paramsData;
	}
	quickCreateNode.trigger('click',[quickParams]);
};
Vtiger_Index_Js.prototype.registerReferenceCreate = function(container) {
	var thisInstance = this;
	container.on('click','.createReferenceRecord', function(e, paramsData) {
		var element = jQuery(e.currentTarget);
		var controlElementTd = thisInstance.getParentElement(element);
		thisInstance.referenceCreateHandler(controlElementTd, paramsData);
	});
};