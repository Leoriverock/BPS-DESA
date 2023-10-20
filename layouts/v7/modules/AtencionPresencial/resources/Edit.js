/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger_Edit_Js("AtencionPresencial_Edit_Js", {
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
    controlarPersona: function(){
        console.log("estoy en controlarPersona");
         persona = $('#ap_persona_display').val();
         if ($.trim(persona.toLowerCase()) === $.trim('USUARIO GENERICO').toLowerCase()) {
            $('#ap_persona_display').css('background-color', 'tomato');
            app.helper.showErrorNotification({
                            message: 'Advertencia!!! debe cambiar el usuario de la atención.',
                            delay: 10000
                        });
         }
        
    },
    registerEvents: function (container) {
        this._super();
        if(!container) container = this.getEditViewContainer();
        this.referenceModuleCustomPopupRegisterEvent(container);
        this.registerAutoCompleteFields(container);
    },
    registerBasicEvents: function (container) {
        this._super(container);
        this.controlarPersona();
        record = jQuery('[name="record"]').val();
        console.log(record);

        //Si estoy editando
        if (record != ''){
            jQuery('[name="ap_estado"]').prop( "disabled", true );
            jQuery('[name="assigned_user_id"]').prop( "disabled", true );
        }
    }
})