/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
if (typeof (Vtiger_FlowImport_Js) == 'undefined') {

    Vtiger_FlowImport_Js = {  
        jsonToLoad: null,
        traducciones: null,
        importActionJSONOK: function() {
            app.helper.showProgress();
            app.request.post({
                data : {
                    module: 'LPTempFlujos',
                    action: 'LPAjax', 
                    mode: 'json_import',
                    flujos: this.jsonToLoad
                }
            })
            .then(function(err,response) {
                $('#importContainer').hide();
                $('#importContainerFinish').show();
                $('#importContainerJson').hide();
                $('#bottombarnormal').hide();
                $('#bottombarjson').hide();
                $('#bottombarfinish').show();
                app.helper.hideProgress();
                if(err){
                    jQuery("#resultImportFinish")
                    .append(`<div class="errorResult"><h3>${err.title}</h3>
                        <p>${err.message}</p></div>`);
                } else {
                    jQuery("#resultImportFinish")
                    .append(`<div class="okResult"><h3>Importacion Finalizada</h3>
                        <p>Registos Importados correctamente</p></div>`);
                    /*
                    app.helper.hidePageContentOverlay().then(function(){
                        Vtiger_Import_Js.loadListRecords();
                    });
                    */
                }
            });
        },
        handleFileTypeChange: function(event){
            try {
                var results = JSON.parse(event.target.result);
                $('#importContainer').hide();
                $('#importContainerFinish').hide();
                $('#importContainerJson').show();
                $('#bottombarnormal').hide();
                $('#bottombarjson').show();
                $('#bottombarfinish').hide();
                for(const element of results) {
                    var row = `<tr class="flujo">
                        <td colspan="3">${element.tf_nombre}</td>
                        <td>${app.vtranslate(Vtiger_FlowImport_Js.traducciones[element.tf_modulo]["__VTIGER_TRANSLATE"])}</td>
                        <td>${Vtiger_FlowImport_Js.traducciones[element.tf_modulo][element.tf_campo]['traduccion']}</td>
                        <td>${Vtiger_FlowImport_Js.traducciones[element.tf_modulo][element.tf_campo_mod]['traduccion']}</td>
                        <td>${app.vtranslate(element.tf_valor)}</td>
                    </tr>`;
                    row += `<tr class="flujo-cambio">
                        <th> Flujo </th>
                        <th> Desde </t>
                        <th> Hasta </t>
                        <th> Comentario </t>
                        <th> Crm </t>
                        <th> Portal </t>
                        <th> Color </t>
                    </tr>`;
                    for(const cambios of element.changes) {
                        row += `<tr class="flujo-cambio">
                        <td>${cambios.tfc_etiqueta}</td>
                        <td>${app.vtranslate(cambios.tfc_origen)}</td>
                        <td>${app.vtranslate(cambios.tfc_destino)}</td>
                        <td>${cambios.tfc_comentario ? 'si' : 'no'}</td>
                        <td>${!! parseInt(cambios.tfc_paracrm) ? 'si' : 'no'}</td>
                        <td>${!! parseInt(cambios.tfc_paraportal) ? 'si' : 'no'}</td>
                        <td style="background:${cambios.tfc_color}"></td>
                    </tr>`;
                    }
                    $("#jsonResult").append(row);
                }
                Vtiger_FlowImport_Js.jsonToLoad = results;
            } catch (error) {
                console.log(error);
            }
            // console.log(Vtiger_FlowImport_Js.jsonToLoad);
        },
        checkFileType: function(e) {
            var filePath = jQuery('#import_file_json').val();
            if (filePath != '') {
                var reader = new FileReader();
                reader.onload = Vtiger_FlowImport_Js.handleFileTypeChange;
                reader.readAsText(e.target.files[0]);
            } 
        },
        registerEvents: function(traducciones) {
            this.traducciones = traducciones;           
        }
    }
    jQuery(document).ready(function() {        
        app.request.post({
            data : {
                module: 'LPTempFlujos',
                action: 'LPAjax', 
                mode: 'getFieldLabels'
            }
        })
        .then(function(err,response) {
            Vtiger_FlowImport_Js.registerEvents(response);
        })
    });
}

