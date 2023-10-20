/**
 * VGS Visual Pipeline Module
 *
 *
 * @package        VGSVisualPipeline Module
 * @author         Curto Francisco - www.vgsglobal.com
 * @license        vTiger Public License.
 * @version        Release: 1.0
 */

jQuery.Class("VGSVisualPipelineSetting_Js", {}, {

    setSelectValue: function(nombre, valor){
        if(typeof valor == "undefined" || valor == "")
            valor = "-";
        var texto = $("select[name="+nombre+"] > option[value='"+valor+"']").text();
        $('select[name='+nombre+']').val(valor);
        var toElement = $('select[name='+nombre+']');
        toElement.siblings().find('span.select2-chosen').html(texto);
        toElement.siblings().find("li").each(function() {
            $(this).removeClass('result-selected');
            if($(this).text().toUpperCase() == texto.toUpperCase())
                $(this).addClass('result-selected');
        });
    },

    colorear: function(nombre, color){
        if(typeof color == "undefined" || color == "")
            color = "rgba(0, 0, 0, 1)";
        jQuery("i[name="+nombre+"]").css('background-color', color);
    },

    SourceModuleUpdate: function () {
        let thisInstance = this;
        jQuery('#module1').on('change', function (e) {
            let module1 = jQuery(this).val();
            jQuery("#amostrar1, #amostrar2, #amostrar3, #amostrar4").find('option[value!="-"]').remove().val('--').trigger('liszt:updated');

            jQuery(".notice").hide();
            let loadingMessage = "Cargando valores...";

            let progressIndicatorElement = jQuery.progressIndicator({
                'message': loadingMessage,
                'position': 'html',
                'blockInfo': {
                    'enabled': true
                }
            });

            let params = {
                module: "VGSVisualPipeline",
                action: "VGSGetPicklistFields",
                source_module: module1
            };

            AppConnector.request(params).then(
                function (data) {
                    if (data.success) {
                        var result = data.result;
                        if (result.result == 'ok') {
                            jQuery.each(result.options, function (i, item) {
                                let o = '<option value="'+i+'">'+item.label+'</option>';
                                if(~~item.picklist)
                                    jQuery("#picklist1").append(o);
                                jQuery("#amostrar1, #amostrar2, #amostrar3, #amostrar4").append(o);
                            });
                            if(typeof jQuery("#sourcefieldname").val() != "undefined"){
                                thisInstance.setSelectValue("picklist1", jQuery("#sourcefieldname").val());
                                thisInstance.setSelectValue("amostrar1", jQuery("#fieldname1").val());
                                thisInstance.setSelectValue("amostrar2", jQuery("#fieldname2").val());
                                thisInstance.setSelectValue("amostrar3", jQuery("#fieldname3").val());
                                thisInstance.setSelectValue("amostrar4", jQuery("#fieldname4").val());
                                jQuery("input[name=negrita1]").attr('checked', !!~~jQuery("#negrita1").val());
                                jQuery("input[name=negrita2]").attr('checked', !!~~jQuery("#negrita2").val());
                                jQuery("input[name=negrita3]").attr('checked', !!~~jQuery("#negrita3").val());
                                jQuery("input[name=negrita4]").attr('checked', !!~~jQuery("#negrita4").val());
                                thisInstance.colorear("icoloreador1", jQuery("#color1").val());
                                thisInstance.colorear("icoloreador2", jQuery("#color2").val());
                                thisInstance.colorear("icoloreador3", jQuery("#color3").val());
                                thisInstance.colorear("icoloreador4", jQuery("#color4").val());
                            }
                            jQuery("#picklist1, #amostrar1, #amostrar2, #amostrar3, #amostrar4").trigger('liszt:updated');
                        }
                        else
                            Vtiger_Helper_Js.showPnotify("Error cargando campos");
                    }
                },
                function (error, err) {
                }
            );
            progressIndicatorElement.progressIndicator({
                'mode': 'hide'
            });
        });
        jQuery(".colorpicker-element").colorpicker();
        thisInstance.colorear("icoloreador1", "");
        thisInstance.colorear("icoloreador2", "");
        thisInstance.colorear("icoloreador3", "");
        thisInstance.colorear("icoloreador4", "");
    },
    saveEntry: function () {
        jQuery('#add_entry').on('click', function (e) {

            jQuery(".notices").hide();
            var loadingMessage = jQuery('.listViewLoadingMsg').text();

            if(jQuery("#module1").val() == "-"){
                var params = {
                    text : "Debe seleccionar un Módulo",
                    type: 'error'
                }
                Vtiger_Helper_Js.showPnotify(params);
                return;
            }

            if(jQuery("#picklist1").val() == "-"){
                var params = {
                    text : "Debe seleccionar un campo para segmentar",
                    type: 'error'
                }
                Vtiger_Helper_Js.showPnotify(params);
                return;
            }

            if(jQuery("#amostrar1").val() != "-" && (jQuery("#amostrar1").val() == jQuery("#amostrar2").val() || jQuery("#amostrar1").val() == jQuery("#amostrar3").val() || jQuery("#amostrar1").val() == jQuery("#amostrar4").val())){
                var params = {
                    text : "Debe seleccionar campos distintos para mostrar",
                    type: 'error'
                }
                Vtiger_Helper_Js.showPnotify(params);
                return;
            }

            if(jQuery("#amostrar2").val() != "-" && (jQuery("#amostrar2").val() == jQuery("#amostrar1").val() || jQuery("#amostrar2").val() == jQuery("#amostrar3").val() || jQuery("#amostrar2").val() == jQuery("#amostrar4").val())){
                var params = {
                    text : "Debe seleccionar campos distintos para mostrar",
                    type: 'error'
                }
                Vtiger_Helper_Js.showPnotify(params);
                return;
            }

            if(jQuery("#amostrar3").val() != "-" && (jQuery("#amostrar3").val() == jQuery("#amostrar1").val() || jQuery("#amostrar3").val() == jQuery("#amostrar2").val() || jQuery("#amostrar3").val() == jQuery("#amostrar4").val())){
                var params = {
                    text : "Debe seleccionar campos distintos para mostrar",
                    type: 'error'
                }
                Vtiger_Helper_Js.showPnotify(params);
                return;
            }

            if(jQuery("#amostrar4").val() != "-" && (jQuery("#amostrar4").val() == jQuery("#amostrar1").val() || jQuery("#amostrar4").val() == jQuery("#amostrar2").val() || jQuery("#amostrar4").val() == jQuery("#amostra34").val())){
                var params = {
                    text : "Debe seleccionar campos distintos para mostrar",
                    type: 'error'
                }
                Vtiger_Helper_Js.showPnotify(params);
                return;
            }

            if(jQuery("#amostrar1").val() == "-" && jQuery("#amostrar2").val() == "-" && jQuery("#amostrar3").val() == "-" && jQuery("#amostrar4").val() == "-"){
                var params = {
                    text : "Debe seleccionar algun campo a mostrar",
                    type: 'error'
                }
                Vtiger_Helper_Js.showPnotify(params);
                return;
            }

            var progressIndicatorElement = jQuery.progressIndicator({
                'message': loadingMessage,
                'position': 'html',
                'blockInfo': {
                    'enabled': true
                }
            });

            let campos = {};

            for (let i = 1; i <= 4; i++) {
                let color = jQuery("i[name=icoloreador"+i+"]:visible").css('background-color');
                color = (!!color.match(/rgb[a]?\(0, 0, 0.*/g) || !!color.match(/rgb[a]?\(255, 255, 255.*/g))?"":color;
                campos[i] = {
                    fieldname: jQuery("#amostrar"+i).val(),
                    negrita: ~~(jQuery("input[name=negrita"+i+"]:visible").attr("checked") == "checked"),
                    color: color
                }
            }

            var params = {
                module: 'VGSVisualPipeline',
                action: 'VGSsave',
                mode: 'addEntry',
                module1: jQuery("#module1").val(),
                picklist1: jQuery("#picklist1").val(),
                campos: campos,
                vgsid: jQuery("#vgsid").val(),
            };

            AppConnector.request(params).then(
                    function (data) {
                        if (data.success) {
                            var response = data.result;

                            if (response.result == 'ok')
                                   window.location = 'index.php?module=VGSVisualPipeline&view=VGSIndexSettings&parent=Settings';
                            else{
                                var params = {
                                    text : response.message,
                                    type: 'error'
                                }
                                Vtiger_Helper_Js.showPnotify(params);
                            }
                        }
                        progressIndicatorElement.progressIndicator({
                            'mode': 'hide'
                        });
                    },
                    function (error, err) {
                        progressIndicatorElement.progressIndicator({
                            'mode': 'hide'
                        });
                    }
            );
        });
    },
    deleteEntry: function () {
        jQuery('.deleteRecordButton').on('click', function (e) {

            jQuery(".notices").hide();
            var loadingMessage = jQuery('.listViewLoadingMsg').text();

            var progressIndicatorElement = jQuery.progressIndicator({
                'message': loadingMessage,
                'position': 'html',
                'blockInfo': {
                    'enabled': true
                }
            });

            var params = {
                module: 'VGSVisualPipeline',
                action: 'VGSsave',
                mode: 'deleteRecord',
                module1: jQuery(this).data('sourcemodule'),
                record_id: jQuery(this).attr('id')
            };
            
            var line = jQuery(this).closest('tr');

            AppConnector.request(params).then(
                    function (data) {
                        if (data.success) {
                            var response = data.result;
                            if (response.result == 'ok')
                                line.hide('slow');
                            else{ 
                                var params = {
                                    text : "Error al eliminar",
                                    type: 'error'
                                }
                                Vtiger_Helper_Js.showPnotify(params);
                            }
                        }
                        progressIndicatorElement.progressIndicator({
                            'mode': 'hide'
                        });
                    },
                    function (error, err) {
                        progressIndicatorElement.progressIndicator({
                            'mode': 'hide'
                        });
                    }
            );
        });
    },
    checkEdit: function (){
        if(jQuery("#sourcemodule").val() != "")
            jQuery('#module1').val(jQuery("#sourcemodule").val()).trigger('liszt:updated').trigger("change");
    },
    toSelect2: function (){
        jQuery("#module1, #picklist1, #amostrar1, #amostrar2, #amostrar3, #amostrar4").select2({width: "75%"});
    },
    registerEvents: function () {
        this.toSelect2();
        this.SourceModuleUpdate();
        this.saveEntry();
        this.deleteEntry();
        this.checkEdit();
    }
});

jQuery(document).ready(function () {
    var instance = new VGSVisualPipelineSetting_Js();
    instance.registerEvents();
});