/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger_Edit_Js("AtencionesWeb_Edit_Js", {
	groups: [],
    preventChangeAssignedUserId : false,
    controlfinalizar: false,
    referenceModuleCustomPopupRegisterEvent: function (container) {
        var thisInstance = this;
        container.off('click', '.customRelatedPopup');
        container.on('click', '.customRelatedPopup', function (e) {
            thisInstance.openSearchPopUp(e);
        });
    },
    openSearchPopUp(e) {
        var thisInstance = this;
        var vtigerIndex = Vtiger_Index_Js.getInstance();
        var parentElem = vtigerIndex.getParentElement(jQuery(e.target));
        var params = vtigerIndex.getPopUpParams(parentElem);
        params.view = 'Search';
        var isMultiple = false;
        if (params.multi_select) {
            isMultiple = true;
        }
        var sourceFieldElement = jQuery('input[class="sourceField"]', parentElem);
        var prePopupOpenEvent = jQuery.Event(Vtiger_Edit_Js.preReferencePopUpOpenEvent);
        sourceFieldElement.trigger(prePopupOpenEvent);
        if (prePopupOpenEvent.isDefaultPrevented()) {
            return;
        }
        var popupInstance = Vtiger_Popup_Js.getInstance();
        app.event.off(Vtiger_Edit_Js.popupSelectionEvent);
        app.event.on(Vtiger_Edit_Js.popupSelectionEvent, function (e, data) {
            var responseData = JSON.parse(data);
            var dataList = new Array();
            jQuery.each(responseData, function (key, value) {
                var counter = 0;
                for (var valuekey in value) {
                    if (valuekey == 'name') continue;
                    if (typeof valuekey == 'object') continue;
                    var data = {
                        'name': value.name,
                        'id': key
                    }
                    if (valuekey == 'info') {
                        data['name'] = value.name;
                    }
                    dataList.push(data);
                    if (!isMultiple && counter === 0) {
                        counter++;
                        vtigerIndex.setReferenceFieldValue(parentElem, data);
                    }
                }
                if (params["src_field"] == "parent_id") {
                    thisInstance.onChangeParentId();
                }
            });
            if (isMultiple) {
                sourceFieldElement.trigger(Vtiger_Edit_Js.refrenceMultiSelectionEvent, {
                    'data': dataList
                });
            }
            sourceFieldElement.trigger(Vtiger_Edit_Js.postReferenceSelectionEvent, {
                'data': responseData
            });
            app.helper.hidePopup();
        });
        popupInstance.showPopup(params, Vtiger_Edit_Js.popupSelectionEvent, function (container) {
            thisInstance.registerSearchEvent(container);
        });
    },
    onChangeParentId: function () {
        jQuery.ajax({
            url: "index.php?module=Accounts&action=IsPerson&accountid=" + jQuery('[name="parent_id"]').val(),
            dataType: 'json',
            async: false
        })
            .done(function (data) {
                if (data.result.isPerson) {
                    jQuery('select[name="ticketcodigoaportacion"]').val(null).trigger('change');
                    jQuery('[name="ticketnumeroexterno"]').attr('disabled', 'disabled');
                    jQuery('[name="ticketdenominacion"]').attr('disabled', 'disabled');
                    jQuery('[name="ticketcodigoaportacion"]').attr('disabled', 'disabled');
                    jQuery('[name="ticketnroobra"]').attr('readonly', 'readonly');

                    jQuery('[name="ticketnumeroexterno"]').val("");
                    jQuery('[name="ticketdenominacion"]').val("");
                    jQuery('[name="ticketcodigoaportacion"]').val("");
                    jQuery('[name="ticketnroobra"]').val("");
                }
                else {
                    jQuery('[name="ticketnumeroexterno"]').removeAttr('disabled');
                    jQuery('[name="ticketdenominacion"]').removeAttr('disabled');
                    jQuery('[name="ticketcodigoaportacion"]').removeAttr('disabled');
                    jQuery('[name="ticketnroobra"]').removeAttr('readonly');
                }
            });
    },
    registerSearchEvent: function (container) {
        jQuery('[name=search]', container).on('click', function (e) {
            const params = {}
            const inputs = jQuery('[name=searchParams]', container).serializeArray();
            const mandatoryInputsFilled = inputs.filter(item => ['accdocumentnumber', 'acccontexternalnumber', 'accempexternalnumber'].includes(item.name)).some(item => item.value);
            if (!mandatoryInputsFilled) {
                return;
            }
            app.helper.showProgress();
            inputs.forEach(input => params[input.name] = input.value);
            params.module = jQuery('#module', container).val();
            params.action = 'Search';
            app.request.get({
                data: params
            }).then(function (e, res) {
                app.helper.hideProgress();
                if (!e) {
                    if (res && res.record) {
                        const selection = {};
                        selection[res.record] = {
                            id: res.record,
                            name: app.getDecodedValue(res.recordLabel),
                            info: res.info
                        };
                        // console.log(selection);
                        app.event.trigger(Vtiger_Edit_Js.popupSelectionEvent, JSON.stringify(selection));
                    } else {
                        app.helper.showErrorNotification({
                            message: 'No se encontraron registros para la búsqueda realizada'
                        });
                    }
                } else {
                    app.helper.showErrorNotification({
                        message: 'Error al realizar búsqueda'
                    });
                }
            });
        });
    },
    registerAutoCompleteFields: function (container) {
        var thisInstance = this;
        container.find('input.autoComplete').autocomplete({
            'minLength': '3',
            'source': function (request, response) {
                //element will be array of dom elements
                //here this refers to auto complete instance
                var inputElement = jQuery(this.element[0]);
                var searchValue = request.term;
                var params = thisInstance.getReferenceSearchParams(inputElement);
                params.search_value = searchValue;
                thisInstance.searchModuleNames(params).then(function (data) {
                    var reponseDataList = new Array();
                    var serverDataFormat = data.result
                    if (serverDataFormat.length <= 0) {
                        jQuery(inputElement).val('');
                        serverDataFormat = new Array({
                            'label': app.vtranslate('JS_NO_RESULTS_FOUND'),
                            'type': 'no results'
                        });
                    }
                    for (var id in serverDataFormat) {
                        var responseData = serverDataFormat[id];
                        jQuery.ajax({
                            url: 'index.php?module=Accounts&action=IsPerson',
                            data: {
                                'accountid': responseData['id']
                            },
                            dateType: 'json',
                            async: false
                        })
                            .done((resp) => {
                                if (resp['result'].isPerson) {
                                    reponseDataList.push(responseData);
                                }
                            });
                        if (reponseDataList.length < 1) {
                            jQuery(inputElement).val('');
                            serverDataFormat = new Array({
                                'label': 'No Results Found',
                                'type': 'no results'
                            });
                            response(serverDataFormat);
                        }
                    }
                    response(reponseDataList);
                });
            },
            'select': function (event, ui) {
                var selectedItemData = ui.item;
                //To stop selection if no results is selected
                if (typeof selectedItemData.type != 'undefined' && selectedItemData.type == "no results") {
                    return false;
                }
                selectedItemData.name = selectedItemData.value;
                var element = jQuery(this);
                var tdElement = element.closest('td');
                thisInstance.setReferenceFieldValue(tdElement, selectedItemData);

                var sourceField = tdElement.find('input[class="sourceField"]').attr('name');
                var fieldElement = tdElement.find('input[name="' + sourceField + '"]');

                fieldElement.trigger(Vtiger_Edit_Js.postReferenceSelectionEvent, { 'data': selectedItemData });
                if (sourceField == "parent_id") {
                    thisInstance.onChangeParentId();
                }
            },
            'change': function (event, ui) {
                var element = jQuery(this);
                //if you dont have readonly attribute means the user didnt select the item
                if (element.attr('readonly') == undefined) {
                    element.closest('td').find('.clearReferenceSelection').trigger('click');
                }
            },
            'open': function (event, ui) {
                //To Make the menu come up in the case of quick create
                jQuery(this).data('autocomplete') && jQuery(this).data('autocomplete').menu.element.css('z-index', '100001');

            }
        });
    },
    searchModuleNames: function (params) {
        var aDeferred = jQuery.Deferred();

        if (typeof params.module == 'undefined') {
            params.module = app.getModuleName();
        }

        if (typeof params.action == 'undefined') {
            params.action = 'BasicAjax';
        }
        AppConnector.request(params).then(
            function (data) {
                aDeferred.resolve(data);
            },
            function (error) {
                //TODO : Handle error
                aDeferred.reject();
            }
        )
        return aDeferred.promise();
    },
    registerRecordPreSaveEvent: function (form) {
        var thisInstance = this;
        if (typeof form == 'undefined') {
            form = thisInstance.getForm();
        }

        app.event.on(Vtiger_Edit_Js.recordPresaveEvent, function (e) {
            if(!thisInstance.controlfinalizar && jQuery('[name=aw_estado]').val() == 'Finalizada'){
                e.preventDefault();
                var params = {
                    "module" : app.getModuleName(),
                    "record" : $('[name="record"]').val(),
                    "action" : 'ConsultaMails'
                }
                app.request.post({data:params}).then(function(err,data){
                    if(data && data.habilitado){
                        thisInstance.controlfinalizar = true;
                        form.submit();
                    }else{
                        if(!data){
                            if(err){
                                app.helper.showErrorNotification({message:err.message});
                            }else
                                app.helper.showErrorNotification({message:'Error interno'});
                            
                        }else{
                            app.helper.showErrorNotification({message:data.message});
                        }
                    }    
                });
            }
        });

    },
     //REDMIN 152149 LR 
    /* getPermisosRol: function () {
        var self = this;
        var promises = [];
    
        var permisosPromise = new Promise(function(resolve, reject) {
            jQuery.ajax({
                async: true,
                url: 'index.php?module=Users&action=getPermisosRol',
                dataType: "json",
                success: function (data) {
                    console.log("data");
                    console.log(data);
                    resolve(data);
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    reject(thrownError);
                }
            });
        });
    
        promises.push(permisosPromise);
    
        var estadoPromise = new Promise(function(resolve, reject) {
            var id = jQuery('[name=record]').val();
            jQuery.ajax({
                async: true,
                data: {
                    id
                },
                url: 'index.php?module=AtencionesWeb&action=getEstado',
                dataType: "json",
                success: function (response) {
                    resolve(response.result.estado);
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    reject(thrownError);
                }
            });
        });
    
        promises.push(estadoPromise);
    
        Promise.all(promises)
            .then(function (results) {
                var data = results[0];
                var estado = results[1];
    
                // Ocultar los campos después de completar las promesas
                jQuery(document).ready(function () {
                    if (data) {
                        jQuery('[name=aw_estado]').val(estado);
                        // Ocultar todos los campos excepto "Persona" y "aw_persona_display"
                        jQuery('.table.table-borderless td.fieldValue').not(':has(input[name="aw_persona_display"])').hide();
                        jQuery('.table.table-borderless td.fieldLabel').not(':contains("Persona")').hide();
                    } else {
                        persona = jQuery('#aw_persona_display').val();
                        jQuery('[name=aw_estado]').val(estado);
                        if (persona === 'USUARIO GENERICO') {
                            jQuery('.table.table-borderless td.fieldValue').not(':has(input[name="aw_persona_display"])').hide();
                            jQuery('.table.table-borderless td.fieldLabel').not(':contains("Persona")').hide();
                        }
                        // Otro código que quieras ejecutar en función de 'data' y 'estado'
                    }
                });
            })
            .catch(function (error) {
                console.log(error);
            });
    },*/
    ocultarCampos: function(){

        var self = this;
        var promises = [];
        var estadoPromise = new Promise(function (resolve, reject) {
            var id = jQuery('[name=record]').val();
            jQuery.ajax({
                async: true,
                data: {
                    id
                },
                url: 'index.php?module=AtencionesWeb&action=getEstado',
                dataType: "json",
                success: function (response) {
                    resolve(response.result.estado);
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    reject(thrownError);
                }
            });
        });
        
        promises.push(estadoPromise);
        
        Promise.all(promises)
            .then(function (results) {
                var estado = results[0]; // Cambiado results[1] a results[0]
        
                
                    
                        persona = jQuery('#aw_persona_display').val();
                        jQuery('[name=aw_estado]').val(estado);
                        if (persona === 'USUARIO GENERICO') {
                            jQuery('.table.table-borderless td.fieldValue').not(':has(input[name="aw_persona_display"])').hide();
                            jQuery('.table.table-borderless td.fieldLabel').not(':contains("Persona")').hide();
                        }
                        // Otro código que quieras ejecutar en función de 'data' y 'estado'
                    
               
            })
            .catch(function (error) {
                console.log(error);
            });
        

    },
    registerEvents: function () {
        this._super();
        this.ocultarCampos();
        
		
        //this.getPermisosRol();
    },
    isGroup: function (assigned) {
        for (group of groups) {
            if (group.groupid == assigned)
                return true;
        }
        return false;
    },

    getGroupName: function (groupid) {
        for (group of groups) {
            if (group.groupid == groupid)
                return group.groupname;
        }
    },

    completeWithSpecificsGroups: function (groups) {
        jQuery('[name="ticketgrupo"]').empty().trigger("change");
        jQuery('[name="ticketgrupo"]').append($('<option>', { value: "", text: 'Selecciona una Opción' }));
        for (let d of groups) {
            jQuery('[name="ticketgrupo"]').append($('<option>', { value: d.groupname, text: d.groupname }));
        }
    },

    completeWithAllGroups: function () {
        jQuery('[name="ticketgrupo"]').empty().trigger("change");
        jQuery('[name="ticketgrupo"]').append($('<option>', { value: "", text: 'Selecciona una Opción' }));
        for (let d of groups) {
            jQuery('[name="ticketgrupo"]').append($('<option>', { value: d.groupname, text: d.groupname }));
        }
    },
    getUserGroups: function (userid) {
        return new Promise(function (resolve, reject) {
            jQuery.ajax({
                url: 'index.php?module=HelpDesk&action=GetUserGroups',
                dataType: 'json',
                data: { userid },
                success: function (data) {
                    resolve(data);
                }
            });
        });
    },
    onChangeAssignedUserId: function () {
        var thisInstance = this;
        // hacer un proceso asincrono para que no pase e
        return new Promise(function (resolve, reject) {
            if (thisInstance.preventChangeAssignedUserId) {
                // console.log("preventChangeAssignedUserId");
                resolve(false);
                return 
            }
            let assigned = jQuery('[name="assigned_user_id"]').val();
            if (thisInstance.isGroup(assigned)) {
                console.log("onChangeAssignedUserId thisInstance.isGroup", assigned)
                jQuery('[name="ticketgrupo"]').empty().trigger("change");
                jQuery('[name="ticketgrupo"]').append($('<option>', { value: "", text: 'Selecciona una Opción' }));
                thisInstance.completeWithAllGroups();
                jQuery('[name="ticketgrupo"]').val(thisInstance.getGroupName(assigned)).trigger('change');
                jQuery('[name="ticketgrupo"]').select2("readonly", true);
                resolve(true);
            } else {
                // console.log("onChangeAssignedUserId thisInstance.isGroup", assigned)
                jQuery.ajax({
                    url: 'index.php?module=HelpDesk&action=GetUserGroups',
                    dataType: 'json',
                    data: { 'userid': assigned },
                    success: function (data) {
                        let promiseReturn = true;
                        console.log("onChangeAssignedUserId ajax", data)
                        jQuery('[name="ticketgrupo"]').select2("readonly", false);
                        if (data['result'].length == 0) {
                            thisInstance.completeWithAllGroups();
                        }
                        else if (data['result'].length == 1) {
                            thisInstance.completeWithAllGroups();
                            jQuery('[name="ticketgrupo"]').val(data['result'][0]['groupname']).trigger('change');
                            jQuery('[name="ticketgrupo"]').select2("readonly", true);
                            promiseReturn = "ONLYone"
                        }
                        else {
                            thisInstance.completeWithSpecificsGroups(data['result']);
                            jQuery('[name="ticketgrupo"]').val(jQuery('[name="ticketgrupo"] option:first-child').val()).trigger('change');
                        }
                        resolve(promiseReturn);
                    }
                });
            }
        });
    },
    registerBasicEvents: function (container) {
        this._super(container);
        this.referenceModuleCustomPopupRegisterEvent(container);
        this.registerAutoCompleteFields(container);
        this.registerRecordPreSaveEvent(container);
        var thisInstance = this;

        jQuery('[name="ticketnumeroexterno"]').focusout(function () {
            if (jQuery('[name="ticketnumeroexterno"]').val().length > 0) {
                jQuery('select[name="ticketcodigoaportacion"]').attr('readonly', false);
                jQuery('select[name="ticketcodigoaportacion"]').parent().css("pointer-events", "");
            }
            else {
                jQuery('select[name="ticketcodigoaportacion"]').val(null).trigger('change');
                jQuery('select[name="ticketcodigoaportacion"]').attr('readonly', true);
                jQuery('select[name="ticketcodigoaportacion"]').parent().css("pointer-events", "none");
            }
        });

        if (jQuery('[name="parent_id"]').val() && jQuery('[name="parent_id"]').val() != '') {
            thisInstance.onChangeParentId();
        }

        if (jQuery('[name=record]').val()) {
            jQuery('[name=contact_id]').siblings('.clearReferenceSelection, .customRelatedPopup').addClass('hide');
        }

        if (window.location.href.indexOf('record') != -1) {
            jQuery('[name="tickettema"] ~ .createReferenceRecord').css('display', 'none');
        }

        jQuery('[name="assigned_user_id"]').change(function () {
            thisInstance.onChangeAssignedUserId();
        });

        const isEdition = jQuery('[name=record]').val() !== "";
        jQuery.ajax({
            url: 'index.php?module=HelpDesk&action=GetGroups',
            dataType: 'json',
            success: function (data) {
                groups = data['result'];
                thisInstance.completeWithAllGroups();
                // al ser una funcion asyncrona es necesario esperar a que se ejecute (por la peticion que se hace)
                // si no a veces queda mal el resultado
                // cuando changeAssignedUserIdResult === "ONLYone" es porque el asignado a tenia un solo grupo asi que no es necesario que se setee 
                // y si se hace se pudre todo
                thisInstance.onChangeAssignedUserId().then((changeAssignedUserIdResult) => {
                    //para evitar perder el grupo en la edicion
                    if (isEdition) {
                        let gruposPosibles = jQuery('[name=ticketgrupo] option').toArray().map(option => option.value);
                        app.request.get({
                            data: {
                                source_module: 'HelpDesk',
                                action: 'GetData',
                                record: jQuery('[name=record]').val()
                            }
                        }).then(function (err, r) {
                            if (!err && r && r.success && r.data) {
                                const { assigned_user_id, ticketgrupo: grupo } = r.data
                                if (grupo !== '') {
                                    // console.log(r.data)
                                    // si el asignado no es grupo
                                    if (!thisInstance.isGroup(assigned_user_id)) {
                                        // consulta que grupos tiene
                                        thisInstance.getUserGroups(assigned_user_id).then(({result: gs_act}) => {
                                            const ngs_act = gs_act.map(({groupname}) => groupname);
                                            // si no tiene el grupo actual entre los grupos del usuario, se asigna al grupo actual
                                            if (!ngs_act.includes(grupo)) {
                                                const grupoOption = jQuery('[name=assigned_user_id] option').toArray().find(option => option.innerHTML === grupo);
                                                if (grupoOption) {
                                                    jQuery('[name=assigned_user_id]').val(grupoOption.value).trigger('change');
                                                }
                                            }
                                        })
                                        if (changeAssignedUserIdResult !== "ONLYone") jQuery('[name=ticketgrupo]').val(grupo).trigger('change');
                                    } else {
                                        // si es un grupo lo asigna asi ya queda
                                        const grupoOption = jQuery('[name=assigned_user_id] option').toArray().find(option => option.innerHTML === grupo);
                                        if (grupoOption) {
                                            jQuery('[name=assigned_user_id]').val(grupoOption.value).trigger('change');
                                        }
                                    }
                                    //////////////////////////////////////////////////
                                    // Asi estaba antes, lo dejo por si queda algo mal
                                    // if (!gruposPosibles.includes(grupo)) {
                                    //     jQuery('[name=ticketgrupo]').append($('<option>', { value: grupo, text: grupo }));
                                    //     jQuery('[name=ticketgrupo]').trigger('change');
                                    //     const grupoOption = jQuery('[name=assigned_user_id] option').toArray().find(option => option.innerHTML === grupo);
                                    //     if (grupoOption) {
                                    //         jQuery('[name=assigned_user_id]').val(grupoOption.value).trigger('change');
                                    //     }
                                    // } 
                                    // jQuery('[name=ticketgrupo]').val(grupo).trigger('change');
                                    //////////////////////////////////////////////////
                                }
                            }
                        })
                    }
                })
            }
        });
    }
})