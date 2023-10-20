Vtiger_Detail_Js("LPTempCamposSeleccion_Detail_Js", {

    // No permitir editar ningun campo en esta vista:
    ajaxEditHandling: function (currentTdElement) { },

    initFields() {

        // Traducir los valores actuales entre todos los posibles:

        var posibles_ts_valor = JSON.parse($("[name='posibles_ts_valor']").val());
        var actuales_ts_valor = $("[data-name='ts_valor[]']").data('value').split(" |##| ");
        
        var ts_campo_id = jQuery("[data-name=ts_campo]").data('value');
        var ts_valor_para_campo_id = posibles_ts_valor[ts_campo_id];

        var ts_valor_td = jQuery("#LPTempCamposSeleccion_detailView_fieldValue_ts_valor");
        var ts_valor_multipicklist = ts_valor_td.find("[data-field-type=multipicklist]");

        var traducidos = []; // (*) para luego implotarlos con 'join'

        for (var i = 0; i < ts_valor_para_campo_id.length; i++) {

            var posible = ts_valor_para_campo_id[i], es_es = posible.traduccion;

            for (var j = 0; j < actuales_ts_valor.length; j++) {

                if (posible.value == actuales_ts_valor[j]) {
                    traducidos.push(`<span class="picklist-color">${es_es}</span>`);
                }

            }
        
        }

        ts_valor_multipicklist.html(traducidos.join(" , ")); // (*)

    },

    registerBasicEvents: function (container) {
        this._super(container);
        this.initFields();
    },

})