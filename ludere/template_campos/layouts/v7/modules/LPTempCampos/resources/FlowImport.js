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
        importActionJSONOK: function() {
            app.helper.showProgress();
            app.request.post({
                data : {
                    module: 'LPTempCampos',
                    action: 'LPAjax', 
                    mode: 'json_import',
                    campos: this.jsonToLoad
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
                        <td colspan=4>${app.vtranslate(element.tc_nombre)}</td>
                        <td colspan=2>${Vtiger_FlowImport_Js.traducciones[element.tc_modulo]['__VTIGER_TRANSLATE']}</td>
                        <td colspan=2>${app.vtranslate(element.tc_campo)}: ${Vtiger_FlowImport_Js.traducciones[element.tc_modulo][element.fieldname]['traduccion']}</td>
                    </tr>`;
                    row += `<tr">
                        <th  colspan=4> Campo </t>
                        <th  colspan=4> Seleccion </th>
                    </tr>`;
                    row += `<tr>`;
                        row += `<td  colspan=4>`;                 
                            row += `<table width="100%" border="0" >
                            <tr">
                        <th > Orden </t>
                        <th > Campo </t>
                        <th > Obligatorio </th>
                    </tr>`;
                            for(const field of element.fields) {
                                row += `<tr>
                                    <td>${app.vtranslate(field.tcd_orden)}</td>
                                    <td>${app.vtranslate(field.tcd_campo)}: ${Vtiger_FlowImport_Js.traducciones[element.tc_modulo][field.fieldname]['traduccion']}</td>
                                    <td>${field.tcd_obligatorio ? 'si' : 'no'}</td>
                                </tr>`;
                            }
                            row += `</table>`;
                        row += `</td>`;

                        row += `<td  colspan=4>`;                    
                        row += `<table width="100%" border="0" >`;
                            for(const selection of element.selections) {
                                row += `<tr>
                                    <td>${app.vtranslate(selection.ts_valor)}</td>
                                </tr>`;
                            }
                            row += `</table>`;
                        row += `</td>`;
                    row += `</tr>`;
                    
                    $("#jsonResult").append(row);
                }
                Vtiger_FlowImport_Js.jsonToLoad = results;
            } catch (error) {
                console.log(error);
            }
            console.log(Vtiger_FlowImport_Js.jsonToLoad);
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

