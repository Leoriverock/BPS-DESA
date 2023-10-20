Vtiger_Edit_Js("LPTempFlujoCambios_Edit_Js", {}, {
    getCamposValores(){
        return JSON.parse($("[name='campos_valores']").val());
    },
    tf_campo:undefined,
    tfc_origen:undefined,
    tfc_destino:undefined,
    changeEvents(){
        $("[name='tfc_template']").change((e) => {
            this.tf_campo = $("[name='tfc_template']").find("option:selected").data("campo");
            // obtener info origen
            if (this.tfc_origen === undefined) this.tfc_origen = $("[name='tfc_origen']").data("selectedValue");
            else this.tfc_origen = $("[name='tfc_origen']").val();   
            // obtener info destino
            if (this.tfc_destino === undefined) this.tfc_destino = $("[name='tfc_destino']").data("selectedValue");
            else this.tfc_destino = $("[name='tfc_destino']").val();
            

            let valores = this.getCamposValores()[this.tf_campo];
            if (valores && valores.length) {
                let html='<option value="">Sin Valor</option>';
                for(let i=0; i<valores.length; i++){
                    html+=`<option value="${valores[i].value}">${valores[i].traduccion}</option>`;
                }
                $("[name='tfc_origen']").html(html);
                $("[name='tfc_origen']").select2();
                $("[name='tfc_origen']").val(this.tfc_origen).trigger("change");
                $("[name='tfc_destino']").html(html);
                $("[name='tfc_destino']").select2();
                $("[name='tfc_destino']").val(this.tfc_destino).trigger("change");
            }   
        });
        setTimeout(() => $("[name='tfc_template']").trigger("change"), 1);
    },
	registerBasicEvents: function(container) {
        this._super(container);
        this.changeEvents();
	},
});