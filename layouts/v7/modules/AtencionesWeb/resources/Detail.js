/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger_Detail_Js("AtencionesWeb_Detail_Js",{},{

	/**
	 * Ajax Edit Save Event
	 * @param {type} currentTdElement
	 * @returns {undefined}
	 */
	registerAjaxEditSaveEvent : function(contentHolder){
		var thisInstance = this;
		if(typeof contentHolder === 'undefined') {
			contentHolder = this.getContentHolder();
		}
		contentHolder.off('click','.inlineAjaxSave');
		contentHolder.on('click','.inlineAjaxSave',function(e){
			e.preventDefault();
			e.stopPropagation();
			var currentTarget = jQuery(e.currentTarget);
			var currentTdElement = thisInstance.getInlineWrapper(currentTarget); 
			var detailViewValue = jQuery('.value',currentTdElement);
			var editElement = jQuery('.edit',currentTdElement);
			var actionElement = jQuery('.editAction', currentTdElement);
			var fieldBasicData = jQuery('.fieldBasicData', editElement);
			var fieldName = fieldBasicData.data('name');
			var fieldType = fieldBasicData.data("type");
			var previousValue = jQuery.trim(fieldBasicData.data('displayvalue'));

			var fieldElement = jQuery('[name="'+ fieldName +'"]', editElement);
			var ajaxEditNewValue = fieldElement.val();

			 // ajaxEditNewValue should be taken based on field Type
			if(fieldElement.is('input:checkbox')) {
				if(fieldElement.is(':checked')) {
					ajaxEditNewValue = '1';
				} else {
					ajaxEditNewValue = '0';
				}
				fieldElement = fieldElement.filter('[type="checkbox"]');
			} else if(fieldType == 'reference'){
				ajaxEditNewValue = fieldElement.data('value');
			}

			// prev Value should be taken based on field Type
			var customHandlingFields = ['owner','ownergroup','picklist','multipicklist','reference','boolean']; 
			if(jQuery.inArray(fieldType, customHandlingFields) !== -1){
				previousValue = fieldBasicData.data('value');
			}

			// Field Specific custom Handling
			if(fieldType === 'multipicklist'){
				var multiPicklistFieldName = fieldName.split('[]');
				fieldName = multiPicklistFieldName[0];
			} 

			var fieldValue = ajaxEditNewValue;

			//Before saving ajax edit values we need to check if the value is changed then only we have to save
			if(previousValue == ajaxEditNewValue) {
				detailViewValue.css('display', 'inline-block');
				editElement.addClass('hide');
				editElement.removeClass('ajaxEdited');
				jQuery('.editAction').removeClass('hide');
				actionElement.show();
			}else{
				var fieldNameValueMap = {};
				fieldNameValueMap['value'] = fieldValue;
				fieldNameValueMap['field'] = fieldName;
				var form = currentTarget.closest('form');
				var params = {
					'ignore' : 'span.hide .inputElement,input[type="hidden"]',
					submitHandler : function(form){
						var preAjaxSaveEvent = jQuery.Event(Vtiger_Detail_Js.PreAjaxSaveEvent);
						app.event.trigger(preAjaxSaveEvent,{form:jQuery(form),triggeredFieldInfo:fieldNameValueMap});
						if(preAjaxSaveEvent.isDefaultPrevented()) {
							return false;
						}

						jQuery(currentTdElement).find('.input-group-addon').addClass('disabled');
						app.helper.showProgress();
						thisInstance.saveFieldValues(fieldNameValueMap).then(function(err, response) {
							app.helper.hideProgress();
							if (err !== null) {
								app.event.trigger('post.save.failed', err);
								jQuery(currentTdElement).find('.input-group-addon').removeClass('disabled');
								return true;
							}
							jQuery('.vt-notification').remove();
							var postSaveRecordDetails = response;
							if(fieldBasicData.data('type') == 'picklist' && app.getModuleName() != 'Users') {
								var color = postSaveRecordDetails[fieldName].colormap[postSaveRecordDetails[fieldName].value];
								if(color) {
									var contrast = app.helper.getColorContrast(color);
									var textColor = (contrast === 'dark') ? 'white' : 'black';
									var picklistHtml = '<span class="picklist-color" style="background-color: ' + color + '; color: '+ textColor + ';">' +
															postSaveRecordDetails[fieldName].display_value + 
														'</span>';
								} else {
									var picklistHtml = '<span class="picklist-color">' +
															postSaveRecordDetails[fieldName].display_value + 
														'</span>';
								}
								detailViewValue.html(picklistHtml);
							} else if(fieldBasicData.data('type') == 'multipicklist' && app.getModuleName() != 'Users') {
								var picklistHtml = '';
								var rawPicklistValues = postSaveRecordDetails[fieldName].value;
								rawPicklistValues = rawPicklistValues.split('|##|');
								var picklistValues = postSaveRecordDetails[fieldName].display_value;
									picklistValues = picklistValues.split(',');
								for(var i=0; i< rawPicklistValues.length; i++) {
									var color = postSaveRecordDetails[fieldName].colormap[rawPicklistValues[i].trim()];
									if(color) {
										var contrast = app.helper.getColorContrast(color);
										var textColor = (contrast === 'dark') ? 'white' : 'black';
										picklistHtml = picklistHtml +
														'<span class="picklist-color" style="background-color: ' + color + '; color: '+ textColor + ';">' +
															 picklistValues[i] + 
														'</span>';
									} else {
										picklistHtml = picklistHtml +
														'<span class="picklist-color">' + 
															 picklistValues[i] + 
														'</span>';
									}
									if(picklistValues[i+1]!==undefined)
										picklistHtml+=' , ';
								}
								detailViewValue.html(picklistHtml);
							} else if(fieldBasicData.data('type') == 'currency' && app.getModuleName() != 'Users') {
								detailViewValue.find('.currencyValue').html(postSaveRecordDetails[fieldName].display_value);
								contentHolder.closest('.detailViewContainer').find('.detailview-header-block').find('.'+fieldName).html(postSaveRecordDetails[fieldName].display_value);
							}else {
								detailViewValue.html(postSaveRecordDetails[fieldName].display_value);
								//update namefields displayvalue in header
								if(contentHolder.hasClass('overlayDetail')) {
									contentHolder.find('.overlayDetailHeader').find('.'+fieldName)
									.html(postSaveRecordDetails[fieldName].display_value);
								} else {
									contentHolder.closest('.detailViewContainer').find('.detailview-header-block')
									.find('.'+fieldName).html(postSaveRecordDetails[fieldName].display_value);
							}
							}
							fieldBasicData.data('displayvalue',postSaveRecordDetails[fieldName].display_value);
							fieldBasicData.data('value',postSaveRecordDetails[fieldName].value);
							jQuery(currentTdElement).find('.input-group-addon').removeClass("disabled");

							detailViewValue.css('display', 'inline-block');
							editElement.addClass('hide');
							editElement.removeClass('ajaxEdited');
							jQuery('.editAction').removeClass('hide');
							actionElement.show();
							var postAjaxSaveEvent = jQuery.Event(Vtiger_Detail_Js.PostAjaxSaveEvent);
							app.event.trigger(postAjaxSaveEvent, fieldBasicData, postSaveRecordDetails, contentHolder);
							//After saving source field value, If Target field value need to change by user, show the edit view of target field.
							if(thisInstance.targetPicklistChange) {
								var sourcePicklistname = thisInstance.sourcePicklistname;
								thisInstance.targetPicklist.find('.editAction').trigger('click');
								thisInstance.targetPicklistChange = false;
								thisInstance.targetPicklist = false;
								thisInstance.handlePickListDependencyMap(sourcePicklistname);
								thisInstance.sourcePicklistname = false;
							}

							if(fieldName == 'aw_estado' && fieldValue == 'Finalizada'){
								var params = {
									"module" : app.getModuleName(),
									"record" : jQuery("#recordId").val(),
									"action" : 'Finalizar',
									"detailajax" : true
								}
								app.request.post({data:params}).then(function(err,data){
									if(err){
										app.helper.showErrorNotification({message:err.message});
									}else{
										jQuery('[name="fin_atencion"]').remove();
										jQuery('.attentionactiva').remove();
										jQuery('#AtencionesWeb_detailView_fieldValue_aw_fechafin .value').text(data.hora);
									}
								});
							}
						});
					}
				};
				validateAndSubmitForm(form,params);
			}
		});
	},
	registerAjaxEditEvent : function(){
		console.log("Si dejo funcion vacia no permite edicion");
		//$('a').removeClass();
		
	},

	pausarAtencion : function(){
		jQuery('[name="pausa_atencion"]').click(function(e){
			app.helper.showConfirmationBox({'message' : '¿Desea pausar la atención?'})
			.then(function(){
				//app.helper.showProgress();
				var params = {
					"module" : app.getModuleName(),
					"record" : jQuery("#recordId").val(),
					"action" : 'Pausar'
				}
				//jQuery('[name="pausa_atencion"]').hide();
				//jQuery('[name="reanudar_atencion"]').show();
				app.request.post({data:params}).then(function(err,data){
					app.helper.hideProgress();
					if(err){
						app.helper.showErrorNotification({message:err.message});
					}else{
						app.helper.showSuccessNotification({message: 'La atención se pauso'});
						jQuery(e.currentTarget).remove();
						jQuery('.attentionactiva').remove();
						jQuery('[data-name="aw_estado"]').data('displayvalue', data.status);
						jQuery('[data-name="aw_estado"]').data('value', data.status);
						jQuery('#AtencionesWeb_detailView_fieldValue_aw_estado .value span').text(data.status);
						jQuery('#AtencionesWeb_detailView_fieldValue_aw_fechafin .value').text(data.hora);
						location.reload();
					}
				});
			});
		});
	},
	reanudarAtencion : function(){
		jQuery('[name="reanudar_atencion"]').click(function(e){
			app.helper.showConfirmationBox({'message' : '¿Desea volver a activar la atención?'})
			.then(function(){
				//app.helper.showProgress();
				var params = {
					"module" : app.getModuleName(),
					"record" : jQuery("#recordId").val(),
					"action" : 'Reanudar'
				}
				//jQuery('[name="pausa_atencion"]').show();
				//jQuery('[name="reanudar_atencion"]').hide();
				app.request.post({data:params}).then(function(err,data){
					app.helper.hideProgress();
					console.log(data.status);
					if(err){
						app.helper.showErrorNotification({message:err.message});
					}else{
						if (data.status == 'Pausado'){
							app.helper.showErrorNotification({message: 'Hay una atención activa, terminela primero'});
						}else{
							app.helper.showSuccessNotification({message: 'La atención se reanudo'});
							jQuery(e.currentTarget).remove();
							jQuery('.attentionactiva').add();
							jQuery('[data-name="aw_estado"]').data('displayvalue', data.status);
							jQuery('[data-name="aw_estado"]').data('value', data.status);
							jQuery('#AtencionesWeb_detailView_fieldValue_aw_estado .value span').text(data.status);
							jQuery('#AtencionesWeb_detailView_fieldValue_aw_fechafin .value').text(data.hora);
							location.reload();
						}
						
					}
				});
			});
		});
	},

	registerEvents: function(container){
		this._super(container);
		this.registerAjaxEditSaveEvent();
		this.pausarAtencion();
		this.reanudarAtencion();
		//jQuery('[name="reanudar_atencion"]').hide();
		jQuery('[name="fin_atencion"]').click(function(e){
			app.helper.showConfirmationBox({'message' : '¿Desea dar por finalizada la atención?'})
			.then(function(){
				app.helper.showProgress();
				var params = {
					"module" : app.getModuleName(),
					"record" : jQuery("#recordId").val(),
					"action" : 'ConsultaMails'
				}
				app.request.post({data:params}).then(function(err,data){
					if(data && data.habilitado){
						var params = {
							"module" : app.getModuleName(),
							"record" : jQuery("#recordId").val(),
							"action" : 'Finalizar'
						}
						app.request.post({data:params}).then(function(err,data){
							app.helper.hideProgress();
							if(err){
								app.helper.showErrorNotification({message:err.message});
							}else{
								app.helper.showSuccessNotification({message: 'La atención se finalizó'});
								jQuery(e.currentTarget).remove();
								jQuery('.attentionactiva').remove();
								jQuery('[data-name="aw_estado"]').data('displayvalue', data.status);
								jQuery('[data-name="aw_estado"]').data('value', data.status);
								jQuery('#AtencionesWeb_detailView_fieldValue_aw_estado .value span').text(data.status);
								jQuery('#AtencionesWeb_detailView_fieldValue_aw_fechafin .value').text(data.hora);
								jQuery('[name="pausa_atencion"]').remove();
								location.reload();
							}
						});
					}else{
						app.helper.hideProgress();
						app.helper.showErrorNotification({message:'Está inhabilitado para finalizar la atención, no se ha respondido la consulta'});
					}
					
				});
			});
		});
	}
});