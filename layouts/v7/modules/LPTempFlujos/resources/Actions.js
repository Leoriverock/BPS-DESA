jQuery.Class("LPTempFlujos_Actions_Js",{
    aplicarFlujo: function(flujo) {
        app.helper.showProgress();
        app.request.post({
            data : {
                module: 'LPTempFlujos',
                action: 'LPAjax',
                mode: 'ejecutarFlujo',
                source_module : app.getModuleName(),
                recordid:  app.getRecordId(),
                flujo
            }
        })
        .then(function(err,response) {
            location.reload();
            if(err) app.helper.hideProgress();
        });
    },
    registerActionsButtons: function (container){
        var self = this;
        container.find('.LpTempFlujosActionBtn').on('click', function(e){
            var btn = $(this);
            var flujo = btn.data('idFlujo');
            var comentario = btn.data('comentario');
            if (!comentario){ // no requiere comentario
                self.aplicarFlujo(flujo);
            } else { // requiere comentario
                app.helper.showProgress();
                app.request.get({'url': `index.php?module=LPTempFlujos&view=RequiredComment`}).then(
                    function (error, data) {
                        app.helper.hideModal();
                        app.helper.hideProgress();
                        if (data) {
                            setTimeout(() =>{                        
                                app.helper
                                .showModal(data, {cb: function (data) {
                                    var formulario = jQuery('#FormEnvio');
                                    formulario.vtValidate({
                                        submitHandler: function (form) {
                                            var formData = new FormData(form); 
                                            var postData = {
                                                'commentcontent' : jQuery(form).find('#commentcontent').val(),
                                                'related_to': app.getRecordId(),
                                                'module' : 'ModComments',
                                                'is_private' : false
                                            };
                                            jQuery.each(postData, (key, value) => formData.append(key, value) );
                                            app.helper.showProgress();
                                            app.helper.hideModal();
                                            setTimeout(() => {                                        
                                                app.request
                                                .post({ 
                                                    'url': 'index.php', 
                                                    'type': 'POST', 
                                                    'data': formData, 
                                                    processData: false, 
                                                    contentType: false 
                                                }).then(function(err, data) {
                                                    app.helper.hideProgress();
                                                    self.aplicarFlujo(flujo);
                                                });
                                            });
                                            return false;
                                        }
                                    });
                                }});
                            }, 100)
                        }
                    }
                ); 
            }
        });
    },
    registerFieldRevision() {
        if (typeof _TF_CAMPO_MOD !== 'undefined') {
            // deshabilitar edicion de la vista de detalle
            // ejemplo: #HelpDesk_detailView_fieldValue_ticketstatus
            $(`#${app.getModuleName()}_detailView_fieldValue_${_TF_CAMPO_MOD}`).css({ 'pointer-events':'none' });
            // deshabilitar edicion de la vista de summary
            // field_HelpDesk_ticketstatus  
            $(`#field_${app.getModuleName()}_${_TF_CAMPO_MOD}`).disable();
            // deshabilitar click en el campo de la vista de summary
            $(`[data-name="${_TF_CAMPO_MOD}"]`).parent().parent().parent().css({ 'pointer-events':'none' });
        }
    }
},{
    verificarFlujos: function() {
        var recordid = app.getRecordId();
        if (!recordid) recordid = $("[name='record']").val();
        var params = {
            module: 'LPTempFlujos',
            view: 'ControlFlujos',
            actual_view: app.view(),
            source_module : app.getModuleName(),
            recordid
        };
        // al cambiar de pestaña, revisar los campos
        app.event.on("post.relatedListLoad.click",LPTempFlujos_Actions_Js.registerFieldRevision);
        // CARGA LA VISTA ControlFlujos en el header 
        var detailViewButtonContainerDiv = jQuery('.detailview-header');
        app.request.post({'data' : params})
        .then(function(err, response) {
            if(err === null){
                if(app.view() == 'Detail') {
                    if (detailViewButtonContainerDiv) {
                        detailViewButtonContainerDiv.append(response);                    
                        var lptempflujoscontainer = detailViewButtonContainerDiv.find('#MainContainerLPTempFlujos');
                        if (!!lptempflujoscontainer) {
                            LPTempFlujos_Actions_Js.registerActionsButtons(lptempflujoscontainer);
                        }
                    }
                } else {
                    // aca llega cuando se pasa el id record que se aplica al modulo actual
                    // solo para la vista Edit ???
                    if (app.view() == 'Edit' && response && response.tf_campo_mod) {
                        var tf_campo_mod = response.tf_campo_mod;
                        $(`[name=${tf_campo_mod}]`).disable();
                    }
                }
                LPTempFlujos_Actions_Js.registerFieldRevision();
            }
        });

    },
    registerEvents: function() {
        this.verificarFlujos();
    }
});

// se ejecuta para todos los modulos
jQuery(document).ready(new LPTempFlujos_Actions_Js().registerEvents());