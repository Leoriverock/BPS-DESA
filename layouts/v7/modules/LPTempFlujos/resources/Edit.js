Vtiger_Edit_Js("LPTempFlujos_Edit_Js", {}, {
    getModulosCampos(){
        return JSON.parse($("[name='modulos_campos']").val());
    },
    getCamposValores(){
        return JSON.parse($("[name='campos_valores']").val());
    },
    tf_modulo:undefined,
    tf_campo:undefined,
    tf_campo_mod:undefined,
    tf_valor:undefined,
    changeEvents(){
        $("[name='tf_modulo']").change((e) => {
            if (this.tf_modulo === undefined) {
                this.tf_modulo = $("[name='tf_modulo']").data("selectedValue");
            } else {
                this.tf_modulo = $("[name='tf_modulo']").val();
            }
            let campos = this.getModulosCampos()[this.tf_modulo];
            if (campos && campos.length) {                
                if (this.tf_campo === undefined) {
                    this.tf_campo = $("[name='tf_campo']").data("selectedValue");
                } else {
                    this.tf_campo = $("[name='tf_campo']").val();
                }
                if (this.tf_campo_mod === undefined) {
                    this.tf_campo_mod = $("[name='tf_campo_mod']").data("selectedValue");
                } else {
                    this.tf_campo_mod = $("[name='tf_campo_mod']").val();
                }
                let html='';
                for(let i=0; i<campos.length; i++)
                    html+=`<option value="${campos[i].fieldname}">${campos[i].traduccion}</option>`;
                $("[name='tf_campo']").html(html);
                $("[name='tf_campo']").select2();
                $("[name='tf_campo']").val(this.tf_campo).trigger("change");
                
                $("[name='tf_campo_mod']").html(html);
                $("[name='tf_campo_mod']").select2();
                $("[name='tf_campo_mod']").val(this.tf_campo_mod).trigger("change");
            }
        });
        $("[name='tf_campo']").change((e) => {            
            if (this.tf_campo === undefined) {
                this.tf_campo = $("[name='tf_campo']").data("selectedValue");
            } else {
                this.tf_campo = $("[name='tf_campo']").val();
            }
            if (this.tf_valor === undefined) {
                this.tf_valor = $("[name='tf_valor']").data("selectedValue");
            } else {
                this.tf_valor = $("[name='tf_valor']").val();
            }
            let valores = this.getCamposValores()[this.tf_campo];
            if (valores && valores.length) {
                let html='<option value="">Sin Valor</option>';
                for(let i=0; i<valores.length; i++){
                    html+=`<option value="${valores[i].value}">${valores[i].traduccion}</option>`;
                }
                $("[name='tf_valor']").html(html);
                $("[name='tf_valor']").select2();
                $("[name='tf_valor']").val(this.tf_valor).trigger("change");
            }
        });
        setTimeout(() => $("[name='tf_modulo']").trigger("change"), 1);
    },
	registerBasicEvents: function(container) {
        this._super(container);
        this.changeEvents();
	},
});