Vtiger_Edit_Js("ConsultasWeb_Edit_Js", {}, {
    groups: [],

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

                    jQuery('[name="ticketnumeroexterno"]').val("");
                    jQuery('[name="ticketdenominacion"]').val("");
                    jQuery('[name="ticketcodigoaportacion"]').val("");
                }
                else {
                    jQuery('[name="ticketnumeroexterno"]').removeAttr('disabled');
                    jQuery('[name="ticketdenominacion"]').removeAttr('disabled');
                    jQuery('[name="ticketcodigoaportacion"]').removeAttr('disabled');
                }
            });
    },
    registerSearchEvent: function (container) {
        jQuery('[name=search]', container).on('click', function (e) {
            const params = {}
            const inputs = jQuery('[name=searchParams]', container).serializeArray();
            const mandatoryInputsFilled = inputs.filter(item => ['accdocumentnumber', 'acccontexternalnumber'].includes(item.name)).some(item => item.value);
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
                        console.log(selection);
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
            form = this.getForm();
        }

        app.event.on(Vtiger_Edit_Js.recordPresaveEvent, function (e) {
            const nroExterno = jQuery('[name=ticketnumeroexterno]', form).val();
            if (nroExterno) {
                let codigoaportacion = jQuery('[name="ticketcodigoaportacion"]').val();
                codigoaportacion = codigoaportacion != undefined && codigoaportacion != "" ? codigoaportacion.split(" ")[0] : "";
                jQuery.ajax({
                    url: "index.php?module=HelpDesk&action=CheckRelation&user=" + jQuery('[name="parent_id"]').val() + "&empresa=" + nroExterno + "&codigoaportacion=" + codigoaportacion,
                    dataType: 'json',
                    async: false
                })
                    .done(function (data) {
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
        });

    },
    registerEvents: function () {
        this._super();
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
        console.log("completeWithSpecificsGroups");
        jQuery('[name="cw_grupo"]').empty().trigger("change");
        jQuery('[name="cw_grupo"]').append($('<option>', { value: "", text: 'Selecciona una Opción' }));
        for (let d of groups) {
            //console.log("los grupos son ")
            //console.log(d.groupname);
            if (d.groupname.trim() !== "") {
                jQuery('[name="cw_grupo"]').append($('<option>', { value: d.groupname, text: d.groupname }));
            }
        }

    },

    completeWithAllGroups: function () {
        console.log("completeWithAllGroups");
        jQuery('[name="cw_grupo"]').empty().trigger("change");
        jQuery('[name="cw_grupo"]').append($('<option>', { value: "", text: 'Selecciona una Opción' }));
        for (let d of groups) {
            //console.log("los grupos son ")
            //console.log(d.groupname);
            if (d.groupname.trim() !== "") {
                jQuery('[name="cw_grupo"]').append($('<option>', { value: d.groupname, text: d.groupname }));
            }
        }



    },

    onChangeAssignedUserId: function () {
        var thisInstance = this;
        var selectElement = $('select[name="cw_estado"]');
        let assigned = jQuery('[name="assigned_user_id"]').val();
        //console.log(assigned);
        /******************************************************** */
        /* Si se selecciona el estado "pendiente agente" 
        en el asignado solo debe dejar guardar usuarios, 
        en el caso de que se seleccione "pendiente"
        solo grupos.         
        */
        var asignadoASelect = $('select[name="assigned_user_id"]');
        var usersOptionGroup = asignadoASelect.find('optgroup[label="Usuarios"]');
        var grupoSelect = asignadoASelect.find('optgroup[label="Grupos"]');
        var listasLlenas = false;
        var usuariosOpciones, gruposOpciones;

        $('select[name="cw_estado"]').on('change', function () {
            var selectedValue = $(this).val();

            if (!listasLlenas) {
                // Almacena las opciones de usuarios y grupos solo una vez
                usuariosOpciones = usersOptionGroup.html();
                gruposOpciones = grupoSelect.html();
                listasLlenas = true;
            }

            if (selectedValue === 'Pendiente Agente') {
                // Si se selecciona "Pendiente Agente", muestra las opciones de usuarios y elimina las opciones de grupos
                usersOptionGroup.html(usuariosOpciones);
                grupoSelect.find('option').remove();
            } else if (selectedValue === 'Pendiente') {
                // Si se selecciona "Pendiente", muestra las opciones de grupos y elimina las opciones de usuarios
                usersOptionGroup.find('option').remove();
                grupoSelect.html(gruposOpciones);
            } else {
                // En otros casos, muestra ambas listas
                usersOptionGroup.html(usuariosOpciones);
                grupoSelect.html(gruposOpciones);
            }

            // Actualiza el select2
            asignadoASelect.select2();
            grupoSelect.select2();
        });
        /******************************************************** */



        if (thisInstance.isGroup(assigned)) {

            if (!selectElement.find('option[value="Pendiente"]').length) {
                selectElement.append('<option value="Pendiente">Pendiente</option>');
            }
            selectElement.find('option[value="Pendiente Agente"]').remove();
            jQuery('[name="cw_grupo"]').empty().trigger("change");
            jQuery('[name="cw_grupo"]').append($('<option>', { value: "", text: 'Selecciona una Opción' }));
            thisInstance.completeWithAllGroups();
            jQuery('[name="cw_grupo"]').val(this.getGroupName(assigned)).trigger('change');
            jQuery('[name="cw_grupo"]').select2("readonly", true);
        }
        else {
            if (!selectElement.find('option[value="Pendiente Agente"]').length) {
                selectElement.append('<option value="Pendiente Agente">Pendiente Agente</option>');
            }
            if (assigned != '') {
                selectElement.find('option[value="Pendiente"]').remove();
            }

            jQuery.ajax({
                //url: 'index.php?module=ConsultasWeb&action=GetUserGroups',
                url: 'index.php?module=ConsultasWeb&action=GetUserGroups',
                dataType: 'json',
                data: { 'userid': assigned },
                success: function (data) {
                    jQuery('[name="cw_grupo"]').select2("readonly", false);
                    if (data['result'].length == 0) {
                        thisInstance.completeWithAllGroups();
                    }
                    else if (data['result'].length == 1) {
                        thisInstance.completeWithAllGroups();
                        jQuery('[name="cw_grupo"]').val(data['result'][0]['groupname']).trigger('change');
                        jQuery('[name="cw_grupo"]').select2("readonly", true);


                    }
                    else {
                        thisInstance.completeWithSpecificsGroups(data['result']);
                        jQuery('[name="cw_grupo"]').val(jQuery('[name="cw_grupo"] option:first-child').val()).trigger('change');


                    }
                }
            });
        }
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
            this.onChangeParentId();
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
        //url: 'index.php?module=HelpDesk&action=GetGroups',
        jQuery.ajax({
            url: 'index.php?module=ConsultasWeb&action=GetGroups',
            dataType: 'json',
            success: function (data) {
                groups = data['result'];
                thisInstance.completeWithAllGroups();
                thisInstance.onChangeAssignedUserId();
                //para evitar perder el grupo en la edicion
                if (isEdition) {
                    const gruposPosibles = jQuery('[name=cw_grupo] option').toArray().map(option => option.value);
                    app.request.get({
                        data: {
                            source_module: 'HelpDesk',
                            action: 'GetData',
                            record: jQuery('[name=record]').val()
                        }
                    }).then(function (err, r) {
                        if (!err && r && r.success && r.data) {
                            const grupo = r.data.cw_grupo;
                            if (grupo !== '') {
                                if (!gruposPosibles.includes(grupo)) {

                                    jQuery('[name=cw_grupo]').append($('<option>', { value: grupo, text: grupo }));
                                    jQuery('[name=cw_grupo]').trigger('change');
                                    const grupoOption = jQuery('[name=assigned_user_id] option').toArray().find(option => option.innerHTML === grupo);
                                    if (grupoOption) {
                                        jQuery('[name=assigned_user_id]').val(grupoOption.value).trigger('change');
                                    }
                                }
                                jQuery('[name=cw_grupo]').val(grupo).trigger('change');
                            }
                        }
                    })
                }
            }
        });


    }
})