Vtiger_Edit_Js("LPTempCamposSeleccion_Edit_Js", {}, {

    getSoloCampos() {
        return JSON.parse($("[name='posibles_ts_campo']").val());
    },

    getSoloValores() {
        return JSON.parse($("[name='posibles_ts_valor']").val());
    },

    ts_template: undefined,
    ts_campo: undefined,
    ts_valor: undefined,

    changeEvents() {

        // Cuando se elija otro campo poblar los valores:

        $("[name='ts_campo']").change((e) => {

            this.ts_campo = $("[name='ts_campo']").val();
            console.log('this.ts_campo', this.ts_campo);

            if (this.ts_valor === undefined) {
                this.ts_valor = JSON.parse($("[name='actuales_ts_valor']").val());
            }

            console.log('this.ts_valor', this.ts_valor);

            let valores = this.getSoloValores()[this.ts_campo];
            console.log('valores', valores);

            if (valores && valores.length) {

                let html = '';

                for (let i = 0; i < valores.length; i++) {
                    let selected = this.ts_valor.includes(valores[i].value) ? 'selected' : '';
                    html += `<option ${selected} value="${valores[i].value}">${valores[i].traduccion}</option>`;
                }

                $("[name='ts_valor[]']").html(html);
                $("[name='ts_valor[]']").select2();
                $("[name='ts_valor[]']").trigger("change");

            }

        });

        // Cuando se elija otro template poblar los campos:

        $("[name='ts_template']").change((e) => {

            this.ts_template = $("[name='ts_template']").val();
            console.log('this.ts_template', this.ts_template);

            let valores = this.getSoloCampos()[this.ts_template];
            console.log('valores', valores);

            if (valores && valores.length) {

                // Definir el posible valor del campo y el modulo:

                for (let i = 0; i < valores.length; i++) {

                    this.ts_campo = valores[i].value;
                    let es_es = valores[i].traduccion;

                    let nombre_modulo = valores[i].value_modulo;
                    let es_es_modulo = valores[i].traduccion_modulo;

                    $("[name='ts_campo']").html(`<option value="${this.ts_campo}">${es_es}</option>`);
                    $("[name='ts_campo']").select2().val(this.ts_campo).trigger("change");

                    $("[name='ts_modulo']").html(`<option value="${nombre_modulo}">${es_es_modulo}</option>`);
                    $("[name='ts_modulo']").select2().val(nombre_modulo).trigger("change");

                }

            } else {

                this.ts_campo = undefined; // No hay un posible valor, por lo que se vacia el del campo, modulo y valor:
                $("[name='ts_campo']").html(`<option value="">Sin Valor</option>`).select2().val(null).trigger("change");
                $("[name='ts_modulo']").html(`<option value="">Sin Valor</option>`).select2().val(null).trigger("change");
                $("[name='ts_valor[]']").html(`<option value="">Sin Valor</option>`).select2().val(null).trigger("change");
                
            }

        });

        setTimeout(() => $("[name='ts_template']").trigger("change"), 1);

    },

    initFields() {

        // Observar cambios de valores en campos ocultos de UITypes 10:

        MutationObserver = window.MutationObserver || window.WebKitMutationObserver;

        var trackChange = function (element) {

            var observer = new MutationObserver(function (mutations, observer) {
                if (mutations[0].attributeName == "value") {
                    $(element).trigger("change");
                }
            });

            observer.observe(element, {
                attributes: true
            });

        }

        trackChange($("[name='ts_template']")[0]);

        // Grisar campos que no se deberian editar:

        $("[name=ts_campo]").select2("readonly", true);

        $("[name=ts_modulo]").select2("readonly", true);

    },

    registerBasicEvents: function (container) {
        this._super(container);
        this.changeEvents();
        this.initFields();
    },

});