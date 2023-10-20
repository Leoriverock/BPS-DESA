Vtiger_Edit_Js("Calls_Edit_Js", {}, {
    referenceModuleCustomPopupRegisterEvent: function(container) {
        var thisInstance = this;
        container.off('click', '.customRelatedPopup');
        container.on('click', '.customRelatedPopup', function(e) {
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
        app.event.on(Vtiger_Edit_Js.popupSelectionEvent, function(e, data) {
            var responseData = JSON.parse(data);
            var dataList = new Array();
            jQuery.each(responseData, function(key, value) {
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
        popupInstance.showPopup(params, Vtiger_Edit_Js.popupSelectionEvent, function(container) {
            thisInstance.registerSearchEvent(container);
        });
    },
    registerSearchEvent: function(container) {
        jQuery('[name=search]', container).on('click', function(e) {
            const params = {}
            const inputs = jQuery('[name=searchParams]', container).serializeArray();
            const mandatoryInputsFilled = inputs.filter(item => ['accdocumentnumber'].includes(item.name)).some(item => item.value);
            if (!mandatoryInputsFilled) {
                return;
            }
            app.helper.showProgress();
            inputs.forEach(input => params[input.name] = input.value);
            params.module = jQuery('#module', container).val();
            params.action = 'Search';
            app.request.get({
                data: params
            }).then(function(e, res) {
                app.helper.hideProgress();
                if (!e) {
                    if (res && res.record) {
                        const selection = {};
                        selection[res.record] = {
                            id: res.record,
                            name: res.recordLabel,
                            info: res.info
                        };
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
    /**
     * Function which will handle the reference auto complete event registrations
     * @params - container <jQuery> - element in which auto complete fields needs to be searched
     */
    registerAutoCompleteFields : function(container) {
        var thisInstance = this;
        console.log("aca en el edit");
        container.find('input.autoComplete').autocomplete({
            'minLength' : '3',
            'source' : function(request, response){
                console.log("aca en el edit .");
                //element will be array of dom elements
                //here this refers to auto complete instance
                var inputElement = jQuery(this.element[0]);
                var searchValue = request.term;
                var params = thisInstance.getReferenceSearchParams(inputElement);
                params.module = app.getModuleName();
                if (jQuery('#QuickCreate').length > 0) {
                    params.module = container.find('[name="module"]').val();
                }
                params.search_value = searchValue;
                if(params.search_module && params.search_module!= 'undefined') {
                    thisInstance.searchModuleNames(params).then(function(data){
                        var reponseDataList = new Array();
                        var serverDataFormat = data;
                        if(serverDataFormat.length <= 0) {
                            jQuery(inputElement).val('');
                            serverDataFormat = new Array({
                                'label' : 'No Results Found',
                                'type'  : 'no results'
                            });
                        }
                        console.log(serverDataFormat);
                        for(var id in serverDataFormat){
                            var responseData = serverDataFormat[id];
                            jQuery.ajax({
                                url: 'index.php?module=Accounts&action=IsPerson',
                                data: {
                                    'accountid': responseData['id']
                                },
                                dateType: 'json',
                                async: false
                            })
                            .done( (resp) => {
                                if( resp['result'].isPerson ){
                                    reponseDataList.push(responseData);
                                }
                            } );
                            if( reponseDataList.length < 1 ){
                                jQuery(inputElement).val('');
                                serverDataFormat = new Array({
                                    'label' : 'No Results Found',
                                    'type'  : 'no results'
                                });
                                response(serverDataFormat);
                            }
                        }
                        response(reponseDataList);
                    });
                } else {
                    jQuery(inputElement).val('');
                    serverDataFormat = new Array({
                        'label' : 'No Results Found',
                        'type'  : 'no results'
                    });
                    response(serverDataFormat);
                }
            },
            'select' : function(event, ui ){
                console.log("aca en el edit ..");
                var selectedItemData = ui.item;
                //To stop selection if no results is selected
                if(typeof selectedItemData.type != 'undefined' && selectedItemData.type=="no results"){
                    return false;
                }
                var element = jQuery(this);
                var parent = element.closest('td');
                if(parent.length == 0){
                    parent = element.closest('.fieldValue');
                }
                var sourceField = parent.find('.sourceField');
                selectedItemData.record = selectedItemData.id;
                selectedItemData.source_module = parent.find('input[name="popupReferenceModule"]').val();
                selectedItemData.selectedName = selectedItemData.label;
                var fieldName = sourceField.attr("name");
                parent.find('input[name="'+fieldName+'"]').val(selectedItemData.id);
                element.attr("value",selectedItemData.id);
                element.data("value",selectedItemData.id);
                parent.find('.clearReferenceSelection').removeClass('hide');
                parent.find('.referencefield-wrapper').addClass('selected');
                element.attr("disabled","disabled");
                //trigger reference field selection event
                sourceField.trigger(Vtiger_Edit_Js.referenceSelectionEvent,selectedItemData);
                //trigger post reference selection
                sourceField.trigger(Vtiger_Edit_Js.postReferenceSelectionEvent,{'data':selectedItemData});
            }
        });
    },
    registerEvents: function() {
        this._super();
    },
    registerBasicEvents: function(container) {
        this._super(container);
        this.referenceModuleCustomPopupRegisterEvent(container);
        this.registerAutoCompleteFields(container);
    }
});