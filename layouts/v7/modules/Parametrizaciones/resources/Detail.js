/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger_Detail_Js("Parametrizaciones_Detail_Js",{
},{
	registerSaveOnEnterEvent: function(editElement) {
		editElement.find('.inputElement:not(textarea)').on('keyup', function(e) {
			var textArea = editElement.find('textarea');
			var ignoreList = ['reference','picklist','multipicklist','owner'];
			var fieldType = jQuery(e.target).closest('.ajaxEdited').find('.fieldBasicData').data('type');
			if(ignoreList.indexOf(fieldType) !== -1) return;
			if(!textArea.length){
				(e.keyCode || e.which) === 13  && editElement.find('.inlineAjaxSave').trigger('click');
			}
		});

		var fieldName = jQuery(editElement).closest('.ajaxEdited').find('.fieldBasicData').data('name');
		if(fieldName != 'pt_grupo') return;
		editElement.find('select [label="Usuarios"]').remove();
		editElement.find('select').select2('destroy').select2();
	},
});