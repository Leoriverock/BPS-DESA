/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger_Detail_Js("AtencionPresencial_Detail_Js",{},{

	registerAjaxEditEvent : function(){
		console.log("Si dejo funcion vacia no permite edicion");
		//$('a').removeClass();
		
	},

	enCola : function(){
		jQuery('[name="cola_atencion"]').click(function(e){
			app.helper.showConfirmationBox({'message' : '¿Desea liberar la atención?'})
			.then(function(){
				app.helper.showProgress();
				var params = {
					"module" : app.getModuleName(),
					"record" : jQuery("#recordId").val(),
					"action" : 'enCola'
				}
				app.request.post({data:params}).then(function(err,data){
					app.helper.hideProgress();
					if(err){
						app.helper.showErrorNotification({message:err.message});
					}else{
						app.helper.showSuccessNotification({message: 'Se ha liberado la atencion'});
						jQuery(e.currentTarget).remove();
						//jQuery('.attentionactiva').remove();
						jQuery('[data-name="ap_estado"]').data('displayvalue', data.status);
						jQuery('[data-name="ap_estado"]').data('value', data.status);
						//console.log("hora: "+data.hora);
						jQuery('#AtencionPresencial_detailView_fieldValue_ap_estado .value span').text(data.status);
						//jQuery('#AtencionPresencial_detailView_fieldValue_ap_fechafin .value').text(data.hora);
						//let hour = jQuery('#AtencionPresencial_detailView_fieldValue_ap_fechafin .value').text();
						//console.log("hour "+hour);
						/*url = 'http://localhost:81/bpsdesa/index.php?module=AtencionPresencial&view=List';
						$(location).attr('href',url);*/
					}
				});
			});
		});
	},

	Finalizar : function(){
		jQuery('[name="fin_atencion"]').click(function(e){
			app.helper.showConfirmationBox({'message' : '¿Desea dar por finalizada la atención?'})
			.then(function(){
				app.helper.showProgress();
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
						//jQuery('.attentionactiva').remove();
						jQuery('[data-name="ap_estado"]').data('displayvalue', data.status);
						jQuery('[data-name="ap_estado"]').data('value', data.status);
						console.log("hora: "+data.hora);
						jQuery('#AtencionPresencial_detailView_fieldValue_ap_estado .value span').text(data.status);
						jQuery('#AtencionPresencial_detailView_fieldValue_ap_fechafin .value').text(data.hora);
						let hour = jQuery('#AtencionPresencial_detailView_fieldValue_ap_fechafin .value').text();
						console.log("hour "+hour);
						location.reload();
					}
				});
			});
		});
	},

	registerEvents: function(container){
		this._super(container);
		//this.Finalizar();
		this.enCola();
		this.registerAjaxEditSaveEvent();
		$('.editAction').addClass('hide');
	}
});