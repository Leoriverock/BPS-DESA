/**
 * VGS Visual Pipeline Module
 *
 *
 * @package        VGSVisualPipeline Module
 * @author         Curto Francisco - www.vgsglobal.com
 * @license        vTiger Public License.
 * @version        Release: 1.0
 */

jQuery.Class("VGSVisualPipeline_Js", {}, {
    showVisualPipeline: function(){
        let progressIndicatorElement = jQuery.progressIndicator({
            'message' : "<h3>Cargando vista pipeline<h3>",
            'position' : 'html',
            'blockInfo' : {
                'enabled' : true
            }
        });
        var instance = this;
        var seleccionados = new Array();
        jQuery('.listViewEntriesCheckBox:checkbox:checked').each(function(){
            seleccionados.push(jQuery(this).val());
        });
        params = {
            'module': 'VGSVisualPipeline',
            'view': 'VGSVisualPipelineView',
            'mode': 'showVPView',
            'module1': _META.module,
            'seleccionados': seleccionados,
        }
        AppConnector.request(params).then(
            function (data) {
                jQuery('div.listViewPageDiv').html(data.result);
                instance.fixHeight();
                instance.registerVPEvents();
                progressIndicatorElement.progressIndicator({
                    'mode' : 'hide'
                });
                jQuery("#divcoloreador").colorpicker();
            },
            function (jqXHR, textStatus, errorThrown) {
                progressIndicatorElement.progressIndicator({
                    'mode' : 'hide'
                })
            }
        );
    },
    fixHeight: function() {
        jQuery('.column').each(function(){
            jQuery(this).height(jQuery('.listViewPageDiv').height());
        });

    },
    tilt_direction: function(item) {
        let left_pos = item.position().left, move_handler = function (e) {
            item.addClass(e.pageX >= left_pos?"right":"left");
            item.removeClass(e.pageX >= left_pos?"left":"right");
            left_pos = e.pageX;
        };
        jQuery("html").bind("mousemove", move_handler);
        item.data("move_handler", move_handler);
    },
    registerVPEvents: function(){
        let thisInstance = this;
        jQuery(".column-list").sortable({
            connectWith: ".column-list",
            handle: ".portlet-header",
            cancel: ".portlet-toggle",
            start: function (event, ui) {
                ui.item.addClass('tilt');
                var instance = new VGSVisualPipeline_Js();
                instance.tilt_direction(ui.item);
            },
            stop: function (event, ui) {
                ui.item.removeClass("tilt");
                jQuery("html").unbind('mousemove', ui.item.data("move_handler"));
                ui.item.removeData("move_handler");
                let valor_columna = ui.item.closest('div.column-list').attr('id');
                let id = ui.item.closest('div.portlet').attr('id');
                let sorting = [];
                jQuery("div.column-list").each(function () {
                    jQuery("div[id='" + $(this).attr('id') + "']>div.portlet").each(function () {
                        sorting.push($(this).attr('id'));
                    });
                });

                let params = {
                    'module': 'VGSVisualPipeline',
                    'action': 'VGSSaveVP',
                    'id': id,
                    'modulo': jQuery('#modulo').val(),
                    'columna': jQuery('#columna_filtro').val(),
                    'valor': valor_columna,
                    'sort_order': sorting
                }

                AppConnector.request(params).then(
                    function (data) {
                    },
                    function (error, err) {
                    }
                );          
            }
        });
        jQuery(".portlet")
            .addClass("ui-widget ui-widget-content ui-helper-clearfix ui-corner-all")
            .find(".portlet-header")
            .addClass("ui-widget-header ui-corner-all")
            .prepend("<span class='fa fa-minus portlet-toggle'></span>")
            .prepend("<span class='fa fa-gear portlet-settings'></span>");

        jQuery(".portlet-toggle").click(function() {
            jQuery(this).toggleClass("fa-plus fa-minus").closest(".portlet").find(".portlet-content").toggle();
        });
        jQuery(".portlet-settings").click(function() {
            console.log("click");
            let id = jQuery(this).parent().parent().attr("id");
            let color = jQuery(this).parent().parent().css("border-left-color");
            jQuery("#idregistro").val(id);
            jQuery("#icoloreador").css('background-color', color);
            thisInstance.abrirModalSettings();
        });
    },
    abrirModalSettings: function(){
        $("#modalSettings").modal({
            "backdrop"  : "static",
            "keyboard"  : false,
            "show"      : true
        });
    },
    registerToggleChange: function(){
        jQuery(document).on('click', '#alterarToggle', function(event) {
            let data = jQuery(this).data("actual");
            if(data == "contraer"){
                $(".fa.fa-minus.portlet-toggle").trigger("click");
                jQuery(this).text('Expandir tareas');
                jQuery(this).data("actual", "expandir");
            }
            else{
                $(".fa.fa-plus.portlet-toggle").trigger("click");
                jQuery(this).text('Contraer tareas');
                jQuery(this).data("actual", "contraer");
            }
        });
    },
    registerModalSettingsClick: function(){
        jQuery(document).on('click', '#closeSettings', function(event) {
            $("#icoloreador").css("background-color", "rgba(0, 0, 0, 0)");
            $("#modalSettings").modal('hide');
        });
        jQuery(document).on('click', '#saveSettings', function(event) {
            let progressIndicatorElement = jQuery.progressIndicator({
                'message' : "<h3>Cargando vista pipeline<h3>",
                'position' : 'html',
                'blockInfo' : {
                    'enabled' : true
                }
            });
            let id = jQuery("#idregistro").val();
            let color = jQuery("#icoloreador").css('background-color');
            let eliminar = ~~(!!color.match(/rgb[a]?\(0, 0, 0.*/g) || !!color.match(/rgb[a]?\(255, 255, 255.*/g));

            params = {
                'module': 'VGSVisualPipeline',
                'action': 'VGSSaveColorValues',
                'source_id': id,
                'source_color': color,
                'delete' : eliminar
            }
            AppConnector.request(params).then(
                function (data) {
                    progressIndicatorElement.progressIndicator({
                        'mode' : 'hide'
                    });

                    if(!data.result){
                        var params = {
                            text : 'Error al guardar configuraciones',
                            type: 'error'
                        }
                        Vtiger_Helper_Js.showPnotify(params);
                        return;
                    }

                    jQuery("#"+id).css({
                        'border-left': (eliminar?"2px solid #acacac":"10px solid "+color)
                    });

                    $("#modalSettings").modal('hide');
                },
                function (jqXHR, textStatus, errorThrown) {
                    var params = {
                        text : 'Error al guardar configuraciones',
                        type: 'error'
                    }
                    Vtiger_Helper_Js.showPnotify(params);

                    progressIndicatorElement.progressIndicator({
                        'mode' : 'hide'
                    })
                }
            );
        });
    }
});
function changeView() {
    var instance = new VGSVisualPipeline_Js();
    instance.showVisualPipeline();
    instance.registerToggleChange();
    instance.registerModalSettingsClick();
}