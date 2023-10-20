/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger_Detail_Js("ConsultasWeb_Detail_Js",{
    init : function() {
        console.log("holaaaa");
        estado =  $("#ConsultasWeb_detailView_fieldValue_cw_estado .value span").text().trim();
        console.log("state "+estado);
        if(estado === 'Asignado') jQuery('#ConsultasWeb_detailView_basicAction_LBL_EDIT').hide();
    },
	cargarModal : function(id) {
		console.log("this is the "+id);
		var progressIndicatorElement = jQuery.progressIndicator();
 		var params = {
 			id: id,
 		};
        params['module'] = 'ConsultasWeb';
        params['view'] = 'CargarModalConsultas';
        AppConnector.request(params)
    	.then(function(data) {
            app.showModalWindow(data,function(data) {
            }, false);
            progressIndicatorElement.progressIndicator({'mode' : 'hide'});
        },
        function(error) {
            console.log("error");
            progressIndicatorElement.progressIndicator({'mode' : 'hide'});
    	});
	},
},{	
});

