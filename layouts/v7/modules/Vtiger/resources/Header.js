/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

jQuery.Class("Vtiger_Header_Js", {
   
    previewFile : function(e,recordId, modulo="") {
        e.stopPropagation();
        var currentTarget = e.currentTarget;
        var currentTargetObject = jQuery(currentTarget);
        if(typeof recordId == 'undefined') {
            if(currentTargetObject.closest('tr').length) {
                recordId = currentTargetObject.closest('tr').data('id');
            } else {
                recordId = currentTargetObject.data('id');
            }
        }
        console.log("entrando"+ recordId);
        var fileLocationType = currentTargetObject.data('filelocationtype');
        var fileName = currentTargetObject.data('filename'); 
        console.log("mostrame el filename");
        console.log(fileName);
        if(fileLocationType == 'I'){
            var params = {
                module : 'Documents',
                view : 'FilePreview',
                record : recordId
            };
            app.request.post({"data":params}).then(function(err,data){
              console.log(data);  
                if(modulo === "ConsultasWeb")
                    app.helper.showModal2(data);
                else if(modulo === "AtencionesWeb")
                    app.helper.showModal2(data);
                else 
                    app.helper.showModal(data);
            });
        } else {
            var win = window.open(fileName, '_blank');
            win.focus();
        }
    }
},{
});