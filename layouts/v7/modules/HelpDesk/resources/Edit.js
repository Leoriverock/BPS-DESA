    Vtiger_Edit_Js("HelpDesk_Edit_Js", {}, {
    groups: [],
    preventChangeAssignedUserId : false,
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
            console.log("mostra la respuesta");
            console.log(responseData);
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
    activas: function(){
        console.log("estoy controlando si hay mas de una activas");
        jQuery.ajax({
                url: 'index.php?module=HelpDesk&action=controlActivas',
                dataType: 'json',
                success: function (data) {
                    //console.log(data);
                    llamada = data.result.llamada.callsid; 
                    atweb = data.result.atencionWeb; 
                    atpre = data.result.AtencionPresencial.atencionpresencialid;
                    console.log(atweb + " " + atpre + " " + llamada );
                    error = 0;
                    if(llamada){ error =error + 1; }
                    if(atweb ){ error = error + 1; }
                    if(atpre){ error = error + 1; }
                     console.log("error "+ error);
                    if(error > 1){
                        alert("Debe finalizar o pausar alguna atencion una para continuar");
                    }

                    //data.result.llamada.callsid
                    //
                }
            });

    },
    onChangeParentId: function () {
        const self = this
        id = jQuery('[name="parent_id"]').val();
        console.log(self);            

       
        jQuery.ajax({
            url: "index.php?module=Accounts&action=IsPerson&accountid=" + jQuery('[name="parent_id"]').val(),
            dataType: 'json',
            async: false
        })
                .done(function (data) {
                    //console.log("Mostrando data");
                    //console.log(data);
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
                    } else {
                        
                        
                        //jQuery('[name="ticketdenominacion"]').val($("#parent_id_display").val());
                        //console.log(jQuery('[name="ticketnumeroexterno"]').val());
                        if (jQuery('[name="ticketnumeroexterno"]').val() != ''){   
                                
                                console.log("entra en el if");
                                if(jQuery('[name="ticketnumeroexterno"]').val() != self.accempexternalnumberModal && self.accempexternalnumberModal != null){
                                    jQuery('[name="ticketnumeroexterno"]').val(self.accempexternalnumberModal);
                                    jQuery('[name="ticketdenominacion"]').attr('disabled', 'disabled');
                                    //console.log("entra en el if 2");
                                     if(self.accempexternalnumberModal == ''){
                                        //console.log("entra en el if 3");
                                        jQuery('[name="ticketdenominacion"]').val('');
                                        jQuery('[name="ticketdenominacion"]').attr('disabled', 'disabled');
                                    }else{
                                        //console.log("entra en el if 3 else");
                                        jQuery('[name="ticketdenominacion"]').attr('disabled', 'disabled');
                                         //jQuery('[name="ticketdenominacion"]').val($("#parent_id_display").val()); este
                                    }
                                }


                            }else{
                                
                                //console.log("entra en el else");
                                
                                jQuery('[name="ticketnumeroexterno"]').val(self.accempexternalnumberModal);
                               
                                
                                if(self.accempexternalnumberModal == '' || jQuery('[name="ticketnumeroexterno"]').val() == ''){
                                    //console.log("entra en el else if");
                                    jQuery('[name="ticketdenominacion"]').val('');
                                    
                                }else{
                                    //console.log("entra en el else else");
                                    jQuery('[name="ticketdenominacion"]').attr('disabled', 'disabled');
                                    //jQuery('[name="ticketdenominacion"]').val($("#parent_id_display").val()); este
                                }

                                
                            }

                        
                        

                        jQuery('[name="ticketnumeroexterno"]').removeAttr('disabled');
                        //jQuery('[name="ticketdenominacion"]').removeAttr('disabled');
                        jQuery('[name="ticketcodigoaportacion"]').removeAttr('disabled');
                        jQuery('[name="ticketnroobra"]').removeAttr('readonly');
                }
            });
    },
    accempexternalnumberModal: null,
    registerSearchEvent: function (container) {
        const self = this;
        jQuery('[name="ticketdenominacion"]').attr('disabled', 'disabled');
        jQuery('[name=search]', container).on('click', function (e) {
            const params = {}
            
            const inputs = jQuery('[name=searchParams]', container).serializeArray();
            
            var error = jQuery('#error', container).val();
            const mensaje = jQuery('#campos', container).val();
            console.log(error);
            if(error == undefined) error = 0;

            self.accempexternalnumberModal = null
            const mandatoryInputsFilled = inputs.filter(item => ['accdocumentnumber', 'acccontexternalnumber', 'accempexternalnumber'].includes(item.name)).some(item => item.value);
           
            
            if (!mandatoryInputsFilled) {
                return;
            }

            self.accempexternalnumberModal = jQuery('[name=searchParams] [name=accempexternalnumber]', container).val()

            if(error < 2){
                app.helper.showProgress();
                inputs.forEach(input => params[input.name] = input.value);
                params.module = jQuery('#module', container).val();
                

                params.action = 'Search';
                app.request.get({
                    data: params
                }).then(function (e, res) {
                    app.helper.hideProgress();
                    if (!e) {
                        console.log("Mostra el res ");
                        console.log(res);
                        if (res && res.record) {
                            const selection = {};
                            selection[res.record] = {
                                id: res.record,
                                name: app.getDecodedValue(res.recordLabel),
                                info: res.info
                            };
                            jQuery('[name="ticketdenominacion"]').val(res.info.accaux);
                            jQuery('[name="ticketdenominacion"]').attr('disabled', 'disabled');
                            //console.log(selection);
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
            }else{
                app.helper.showErrorNotification({
                                message: "Debe buscar por un solo campo, intente quitar uno: " + mensaje
                            });
            }
            
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
            const nroExterno = jQuery('[name=ticketnumeroexterno]', form).val();
            var denominacion = '';
            console.log("Empresa:"+nroExterno);
            var recordid  = jQuery('[name=record]').val();
            //console.log("los datos son: ");
            //console.log(thisInstance);
            if (nroExterno) {
                let codigoaportacion = jQuery('[name="ticketcodigoaportacion"]').val();
                codigoaportacion = codigoaportacion != undefined && codigoaportacion != "" ? codigoaportacion.split(" ")[0] : "";
                jQuery.ajax({
                    url: "index.php?module=HelpDesk&action=CheckRelation&user=" + jQuery('[name="parent_id"]').val() + "&empresa=" + nroExterno + "&codigoaportacion=" + codigoaportacion,
                    dataType: 'json',
                    async: false
                })
                    .done(function (data) {
                        denominacion = data.result.resultado;
                        //console.log("Consulta el ws");
                        //console.log(data.result.resultado);
                        if (data.success) {
                            if (data.result.error) {
                                app.helper.showErrorNotification({ 'message': data.result.resultado });
                                e.preventDefault();
                            }
                        }
                        else {
                            app.helper.showErrorNotification({ 'message': "Error interno" });
                            e.preventDefault();
                        }
                    });
                
                    
            }
            //Guardo la denominacion
                jQuery.ajax({
                    url: 'index.php?module=HelpDesk&action=setDenominacion',
                    dataType: 'json',
                    data: { denominacion,
                            recordid },
                    success: function (data) {
                        console.log(data);
                    }
                });
        });

    },
    registerEvents: function () {
        this._super();
        this.activas();
        jQuery('[name="ticketdenominacion"]').attr('disabled', 'disabled');
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

    onChangeParentIdAtencion: function (id) {
        const self = this
        jQuery.ajax({
            url: "index.php?module=Accounts&action=DisplayName&record=" + id,
            dataType: 'json',
            async: false
        }).done(function(dataSearch){
            jQuery('[name="parent_id_display"]').val(dataSearch.result.recordLabel);
            //jQuery('[name="ticketdenominacion"]').val(dataSearch.result.info.accdenoempresa);
        })
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

        if (jQuery('[name=record]').val() && jQuery('#lblcallphone').text() != 'Sin llamadas') {
            jQuery('[name=contact_id]').siblings('.clearReferenceSelection, .customRelatedPopup').addClass('hide');
        }

        if (jQuery('[name=record]').val() && jQuery('#lblatention').length > 0) {
            jQuery('[name=contact_id]').siblings('.clearReferenceSelection, .customRelatedPopup').addClass('hide');
        }

        if(jQuery('#lblatention').length > 0){
            /*if (!jQuery('[name=record]').val() && jQuery('[name="parent_id"]').val() && jQuery('[name="parent_id"]').val() != '' && jQuery('[name="sourceRecord"]').val() != '' && jQuery('[name="parent_id"]').val() != jQuery('[name="sourceRecord"]').val()) {
                thisInstance.onChangeParentIdAtencion(jQuery('[name="sourceRecord"]').val());
                console.log('distintos')
                jQuery('[name="parent_id"]').val(jQuery('[name="sourceRecord"]').val());
            }*/
            if (!(jQuery('[name=record]').val() || jQuery('[name=record]').val() !== "")){
                if(jQuery('[name="sourceRecord"]').val() && jQuery('[name="sourceRecord"]').val() !== '' && jQuery('[name="parent_id"]').val() !== jQuery('[name="sourceRecord"]').val()) {
                    thisInstance.onChangeParentIdAtencion(jQuery('[name="sourceRecord"]').val());
                    var id = jQuery('[name="sourceRecord"]').val();
                    console.log('distintos ', id);
                    jQuery('[name="parent_id"]').val(id);
                }
            }

            if (!jQuery('[name=record]').val()){
                jQuery('[name=contact_id]').siblings('.clearReferenceSelection, .customRelatedPopup').addClass('hide');
            }
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