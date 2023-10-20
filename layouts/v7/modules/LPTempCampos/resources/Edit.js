Vtiger_Edit_Js("LPTempCampos_Edit_Js", {}, {

    getCamposValores() {
        return JSON.parse($("[name='posibles_tc_campo']").val());
    },

    tc_modulo: undefined,
    tc_campo: undefined,

    changeEvents() {

        // Cuando se elija otro modulo poblar los campos:

        $("[name='tc_modulo']").change((e) => {

            this.tc_modulo = $("[name='tc_modulo']").find("option:selected").val();
            console.log('this.tc_modulo', this.tc_modulo);

            if (this.tc_campo === undefined) this.tc_campo = $("[name='tc_campo']").data("selectedValue");
            else this.tc_campo = $("[name='tc_campo']").val();

            let valores = this.getCamposValores()[this.tc_modulo];
            console.log('valores', valores);

            if (valores && valores.length) {

                let html = '<option value="">Sin Valor</option>';

                for (let i = 0; i < valores.length; i++) {
                    html += `<option value="${valores[i].value}">${valores[i].traduccion}</option>`;
                }

                $("[name='tc_campo']").html(html);
                $("[name='tc_campo']").select2();
                $("[name='tc_campo']").val(this.tc_campo).trigger("change");

            }

        });

        setTimeout(() => $("[name='tc_modulo']").trigger("change"), 1);

    },

    registerBasicEvents: function (container) {
        this._super(container);
        this.changeEvents();
    },

});