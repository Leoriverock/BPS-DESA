

/**
 * Ocultacion generica de campos por parametrizacion
 */
var LpDynFields = {

    // EVENTOS
    listeners: [],

    /**
     * Aplica las reglas
     */
    aply: function () {

        // Si hay templates,
        if (LP_DYNFIELD_RULES && LP_DYNFIELD_RULES.length > 0) {

            var vista = _META.view; // (VISTA ACTUAL)

            // Si la vista actual es listado o detalle...
            if (['List', 'Detail'].includes(vista)) {

                console.log('LP_DYNFIELD_RULES', 'omitir', vista);

                // 1. Definir evento para mutaciones al DOM:
                const observer = new MutationObserver(list => {

                    /*
                        QUITAR EL LAPIZ para no permitir editar.
                    */

                    jQuery('.editAction').each(function (index, element) {
                        console.log('borrando', index, element);
                        element.remove(); // (QUITAR EL LAPIZ)
                    });

                });

                // 2. Configurar el tipo de mutaciones al DOM que van a disparar el evento y observar:
                observer.observe(document.body, { attributes: true, childList: true, subtree: true });

                // 3. Si fuera un listado, no permitir editar campos haciendo doble click en ellos:
                if (vista == 'List') jQuery('#listViewContent').off('dblclick', '.listViewEntries');

                return; // 4. Finalizar el flujo del programa.

            }

            // iterar cada uno de ellos:
            LP_DYNFIELD_RULES.forEach(template => {

                console.log('LP_DYNFIELD_RULES', template);

                // Si hay reglas
                if (template.reglas) {

                    // y si hay un campo condicionado a,
                    if (template.reglas.condicion.campo) {

                        // si el evento no estuvo agendado previamente para el campo...
                        if (!this.listeners.includes(template.reglas.condicion.campo)) {

                            console.log('agendando', template.reglas.condicion.campo);

                            // cuando dicho campo haya cambiado, entonces verificar cual es la condicion:
                            jQuery(`select[name=${template.reglas.condicion.campo}]`).on('change', function () {

                                var valor = this.value;
                                
                                //Muestro todos los bloques y las filas de cada uno
                                jQuery(".fieldBlockContainer").show();
                                jQuery(".fieldBlockContainer").find("tr").show();

                                // 1. Ocultar todos los campos:
                                LP_DYNFIELD_RULES.forEach(tmp => {

                                    // console.log('tmp1', tmp);

                                    jQuery(tmp.reglas.todos).each(function (i, item) {
                                        LpDynFields.hideAll(i, item);
                                        LpDynFields.setMandatory(i, item, false);
                                    });

                                });

                                // 2. Mostrarlos segun condicion:
                                LP_DYNFIELD_RULES.forEach(tmp => {

                                    // console.log('tmp2', tmp);

                                    // (si el campo en la condicion es con el que se agenda el evento)
                                    if (tmp.reglas.condicion.campo == template.reglas.condicion.campo) {

                                        // (y si el valor esta entre los seleccionados)
                                        if (tmp.reglas.condicion.valores.includes(valor)) {

                                            // console.log(`"${valor}"`, 'includes', tmp.reglas.condicion.valores);

                                            jQuery(tmp.reglas.mostrar).each(function (i, item) {
                                                LpDynFields.showAll(i, item);
                                                LpDynFields.setMandatory(i, item, item.mandatory);
                                            });

                                        } else {

                                            // console.log(`"${valor}"`, 'not_includes', tmp.reglas.condicion.valores);

                                        }

                                    }

                                });

                                //me fijo si para cada fila quedo sin campos visibles, si es asi la oculto, lo mismo para el bloque, si queda sin filas visibles lo oculto
                                jQuery(".fieldBlockContainer").each((index, block) => {
                                    let visiblesBlock = 0;
                                    jQuery(block).find("tr").each((index, tr) => {
                                        let visibles = 0;

                                        jQuery(tr).find("td.fieldValue").each((index, td) => {
                                            visibles += jQuery(td).children(':visible').length;
                                        })

                                        if(visibles == 0)
                                            jQuery(tr).hide();
                                        else
                                            visiblesBlock++;
                                    });

                                    if(visiblesBlock == 0)
                                        jQuery(block).hide();
                                });

                            }).trigger('change'); // (forzar la primera vez)

                            // REGISTRAR CAMPO ENTRE LOS QUE TIENEN EVENTOS
                            this.listeners.push(template.reglas.condicion.campo);

                        }

                    } else {

                        console.log('LP_DYNFIELD_RULES', 'sin_condicion');

                    }

                } else {

                    console.log('LP_DYNFIELD_RULES', 'sin_reglas');

                }

            });

        } else {

            console.log('LP_DYNFIELD_RULES', 'no_instalado');

        }

    },

    // /////////////////////////////////////////////////////////////////////////

    /**
     * Oculta todos los campos
     * @param {*} i indice
     * @param {*} item elemento
     * @returns nada
     */
    hideAll: function (i, item) {

        // console.log(i, `Ocultando '${item.campo}'`, `UIType '${item.uitype}'`, item);

        if (item.uitype == 1) return LpDynFields.hideUIType1(item.campo);
        if (item.uitype == 2) return LpDynFields.hideUIType1(item.campo);
        if (item.uitype == 22) return LpDynFields.hideUIType1(item.campo);

        if (item.uitype == 19) return LpDynFields.hideUIType19(item.campo);

        if (item.uitype == 15) return LpDynFields.hideUIType15(item.campo);
        if (item.uitype == 16) return LpDynFields.hideUIType15(item.campo);
        if (item.uitype == 53) return LpDynFields.hideUIType15(item.campo);

        if (item.uitype == 10) return LpDynFields.hideUIType10(item.campo);
        if (item.uitype == 59) return LpDynFields.hideUIType10(item.campo);

        // console.log(i, `Error al ocultar '${item.campo}'`, `UIType '${item.uitype}' no soportado`, item);

    },

    /**
     * Muestra todos los campos
     * @param {*} i indice
     * @param {*} item elemento
     * @returns nada
     */
    showAll: function (i, item) {

        // console.log(i, `Mostrando '${item.campo}'`, `UIType '${item.uitype}'`, item);

        if (item.uitype == 1) return LpDynFields.showUIType1(item.campo);
        if (item.uitype == 2) return LpDynFields.showUIType1(item.campo);
        if (item.uitype == 22) return LpDynFields.showUIType1(item.campo);

        if (item.uitype == 19) return LpDynFields.showUIType19(item.campo);

        if (item.uitype == 15) return LpDynFields.showUIType15(item.campo);
        if (item.uitype == 16) return LpDynFields.showUIType15(item.campo);
        if (item.uitype == 53) return LpDynFields.showUIType15(item.campo);

        if (item.uitype == 10) return LpDynFields.showUIType10(item.campo);
        if (item.uitype == 59) return LpDynFields.showUIType10(item.campo);

        // console.log(i, `Error al mostrar '${item.campo}'`, `UIType '${item.uitype}' no soportado`, item);

    },

    // /////////////////////////////////////////////////////////////////////////

    /**
     * OCULTA campos UIType: 1, 2, 22, 
     */
    hideUIType1: function (fieldname) {

        var nodo = jQuery(`[name=${fieldname}]`);

        // console.log('hideUIType1', fieldname, nodo);

        // OCULTA la etiqueta del campo y si tiene el asterisco rojo
        nodo.parent().prev().css('color', 'white').find('span').hide();

        // OCULTA el valor del campo
        jQuery(`[name=${fieldname}]`).hide();

        return nodo;

    },

    /**
     * MUESTRA campos UIType: 1, 2, 22, 
     */
    showUIType1: function (fieldname) {

        var nodo = jQuery(`[name=${fieldname}]`);

        // console.log('showUIType1', fieldname, nodo);

        // MUESTRA la etiqueta del campo y si tiene el asterisco rojo
        nodo.parent().prev().css('color', 'black').find('span').show();

        // MUESTRA el valor del campo
        nodo.show();

        return nodo;

    },

    // /////////////////////////////////////////////////////////////////////////

    /**
     * OCULTA campos UIType: 19 - reutiliza el 1
     */
    hideUIType19: function (fieldname) {

        var nodo = LpDynFields.hideUIType1(fieldname);
        var tabla = nodo.parent().parent().parent().parent();

        if (tabla.find('tr').length == 1) {
            // SI ES EL UNICO ROW:
            tabla.parent().hide();
        }

    },

    /**
     * MUESTRA campos UIType: 19 - reutiliza el 1
     */
    showUIType19: function (fieldname) {

        var nodo = LpDynFields.showUIType1(fieldname);
        var tabla = nodo.parent().parent().parent().parent();

        if (tabla.find('tr').length == 1) {
            // SI ES EL UNICO ROW:
            tabla.parent().show();
        }

    },

    // /////////////////////////////////////////////////////////////////////////

    /**
     * OCULTA campos UIType: 15, 16, 53
     */
    hideUIType15: function (fieldname) {
        console.log(fieldname);

        var nodo = jQuery(`[name=${fieldname}]`);

        // console.log('hideUIType15', fieldname, nodo);

        // OCULTA la etiqueta del campo y si tiene el asterisco rojo
        nodo.parent().prev().css('color', 'white').find('span').hide();

        // OCULTA el valor del campo
        nodo.prev().hide();
        nodo.hide();

    },

    /**
     * MUESTRA campos UIType: 15, 16, 53
     */
    showUIType15: function (fieldname) {

        var nodo = jQuery(`[name=${fieldname}]`);

        // console.log('showUIType15', fieldname, nodo);

        // MUESTRA la etiqueta del campo y si tiene el asterisco rojo
        nodo.parent().prev().css('color', 'black').find('span').show();

        // MUESTRA el valor del campo
        nodo.prev().show();
        nodo.show();

    },

    // /////////////////////////////////////////////////////////////////////////

    /**
     * OCULTA campos UIType: 10, 59
     */
    hideUIType10: function (fieldname) {

        var nodo = jQuery(`[name=${fieldname}]`);

        // console.log('hideUIType10', fieldname, nodo);

        // OCULTA la etiqueta del campo y si tiene el asterisco rojo
        nodo.parent().parent().parent().prev().first().
            css('color', 'white').find('span').hide();

        // OCULTA el valor del campo
        nodo.parent().parent().hide();

    },

    /**
     * MUESTRA campos UIType: 10, 59
     */
    showUIType10: function (fieldname) {

        var nodo = jQuery(`[name=${fieldname}]`);

        // console.log('showUIType10', fieldname, nodo);

        // MUESTRA la etiqueta del campo y si tiene el asterisco rojo
        nodo.parent().parent().parent().prev().first().
            css('color', 'black').find('span').show();

        // MUESTRA el valor del campo
        nodo.parent().parent().show();

    },

    // /////////////////////////////////////////////////////////////////////////

    /**
     * HACE que un campo sea obligatorio u opcional
     */
    setMandatory: function (i, item, required) {

        var aster = '<span class="redColor">*</span>';
        var fieldname = item.campo;
        var uitype = item.uitype;
        var nodo = null, ref = null;

        if (['10', '59'].includes(uitype)) {
            nodo = jQuery("input[name='" + fieldname + "_display']");
        } else {
            nodo = jQuery("[name='" + fieldname + "']");
        }

        // ///////////////////////////////////////////////////
        // Esto no se ve por HTML para verlo usar .data('xxx')
        nodo.data('ruleRequired', required); // (no usar attr)
        // ///////////////////////////////////////////////////

        // Obtener la referencia en funcion del grupo de uitype:

        if (['1', '2', '15', '16', '19', '22', '53'].includes(uitype)) {
            ref = nodo.parent().prev();
        }

        if (['10', '59'].includes(uitype)) {
            ref = nodo.parent().parent().parent().prev();
        }

        console.log('setMandatory', fieldname, uitype, required, nodo);

        if (ref) { // si el campo pertenece a un grupo...

            // ////////////////////////////////////////
            if (required) { // EL CAMPO ES OBLIGATORIO:
                // ////////////////////////////////////

                if (!ref.find('.redColor').length) {
                    // console.log(`agregando asterisco a '${fieldname}'`);
                    ref.append(aster); // (si es que no existia de antes)
                } else {
                    // console.log(`mostrando asterisco a '${fieldname}'`);
                    ref.find('.redColor').show(); // (ya existia - mostrar)
                }

            }

            // ////////////////////////////            
            else { // EL CAMPO ES OPCIONAL:
                // ////////////////////////

                // console.log(`ocultando asterisco a '${fieldname}'`);
                ref.find('.redColor').hide(); // (ya existia - ocultar)

            }

        } else { // sino, entonces mostrar un error:

            // if (required) {
            //     console.log(i, `Error al hacer obligatorio '${fieldname}'`, `UIType '${uitype}' no soportado`, item);
            // } else {
            //     console.log(i, `Error al hacer opcional '${fieldname}'`, `UIType '${uitype}' no soportado`, item);
            // }

        }

    },

}

// SI EL DOCUMENTO ESTA CARGADO...
jQuery(document).ready(function () {

    // ///////////////////
    // ... APLICAR REGLAS: 
    LpDynFields.aply();
    // ///////////////////

});