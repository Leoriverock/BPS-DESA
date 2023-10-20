Vtiger_Edit_Js("LPTempCamposDetalle_Edit_Js", {}, {

    getCamposValores() {
        return JSON.parse($("[name='posibles_tcd_campo']").val());
    },

    tcd_template: undefined,
    tcd_campo: undefined,

    changeEvents() {

        // Cuando se elija otro template poblar los campos:

        $("[name='tcd_template']").change((e) => {

            this.tcd_template = $("[name='tcd_template']").val();
            console.log('this.tcd_template', this.tcd_template);

            if (this.tcd_campo === undefined) this.tcd_campo = $("[name='tcd_campo']").data("selectedValue");
            else this.tcd_campo = $("[name='tcd_campo']").val();

            let valores = this.getCamposValores()[this.tcd_template];
            console.log('valores', valores);

            if (valores && valores.length) {

                let html = ''; // (no agregar opcion 'Sin Valor')

                for (let i = 0; i < valores.length; i++) {
                    html += `<option value="${valores[i].value}">${valores[i].traduccion}</option>`;
                }

                $("[name='tcd_campo']").html(html);
                $("[name='tcd_campo']").select2();
                $("[name='tcd_campo']").val(this.tcd_campo).trigger("change");

            }

        });

        setTimeout(() => $("[name='tcd_template']").trigger("change"), 1);

    },

    initFields() {

        // Ocultar el boton de quitar relacion, buscar registros y crear relacion (UIType 10)
        $("[name='tcd_template']").next().next().hide().next().hide().parent().next().hide();

    },

    registerBasicEvents: function (container) {
        this._super(container);
        this.changeEvents();
        this.initFields();
    },

});