<?php

/**
 * Ocultacion generica de campos por parametrizacion
 */
class LpDynFields {

    /**
     * Retorna las reglas a aplicar
     */
    public static function rules($moduleName) {

        global $adb, $log;

        $log->debug(array(__METHOD__, $moduleName));

        return json_encode(self::getAllRules($moduleName));

    }

    /**
     * Construye todas las reglas a retornar
     */
    private static function getAllRules($moduleName) {

        global $adb, $log;

        $log->debug(array(__METHOD__, $moduleName));

        // Templates del modulo:
        $resultado = $adb->pquery(
            
            "SELECT 
                lptempcamposid, tc_nombre
            FROM
                vtiger_lptempcampos
                    JOIN
                vtiger_crmentity AS c1 ON lptempcamposid = c1.crmid
                    AND c1.deleted = 0
            WHERE
                tc_modulo = ?
                    AND lptempcamposid IN (SELECT 
                        ts_template
                    FROM
                        vtiger_lptempcamposseleccion
                            JOIN
                        vtiger_crmentity AS c2 ON lptempcamposseleccionid = c2.crmid
                            AND c2.deleted = 0)"

            ,

            array($moduleName)
            
        );

        $retorno = array();

        foreach ($resultado as $r) {

            $retorno[] = array(                
                'id' => $r['lptempcamposid'], 'nombre' => $r['tc_nombre'],
                'reglas' => self::getRulesOf($moduleName, $r['lptempcamposid']),
            );

        }

        return $retorno;

    }

    /**
     * Obtiene las reglas de un modulo y template
     */
    private static function getRulesOf($moduleName, $templateId) {

        global $adb, $log;

        $log->debug(array(__METHOD__, $moduleName));

        // Consultar campos a mostrar:
        $resultado_mostrar = $adb->pquery(
            
            "SELECT 
                fcond.fieldlabel AS condflabel,
                fcond.fieldname AS condfname,
                fcond.uitype AS conduitype,
                ts_valor,
                fshow.fieldlabel AS showflabel,
                fshow.fieldname AS showfname,
                fshow.uitype AS showuitype,
                tcd_obligatorio
            FROM
                vtiger_lptempcampos
                    JOIN
                vtiger_crmentity AS c1 ON lptempcamposid = c1.crmid
                    AND c1.deleted = 0
                    JOIN
                vtiger_lptempcamposdetalle ON tcd_template = lptempcamposid
                    JOIN
                vtiger_crmentity AS c2 ON lptempcamposdetalleid = c2.crmid
                    AND c2.deleted = 0
                    JOIN
                vtiger_lptempcamposseleccion ON ts_template = lptempcamposid
                    AND ts_modulo = tc_modulo
                    AND ts_campo = tc_campo
                    JOIN
                vtiger_crmentity AS c3 ON lptempcamposseleccionid = c3.crmid
                    AND c3.deleted = 0
                    JOIN
                vtiger_field AS fcond ON fcond.fieldid = ts_campo
                    JOIN
                vtiger_field AS fshow ON fshow.fieldid = tcd_campo
            WHERE
                ts_modulo = ? AND ts_template = ?
            GROUP BY tcd_campo
            ORDER BY tcd_orden"

            ,

            array($moduleName, $templateId)
        
        );

        // Consultar campos a ocultar:
        $resultado_ocultar = $adb->pquery(
            
            "SELECT 
                fieldname, uitype, fieldlabel
            FROM
                vtiger_field AS f
                    JOIN
                vtiger_tab AS t ON f.tabid = t.tabid
            WHERE
                t.name = ?
                    AND fieldid NOT IN (SELECT 
                        tcd_campo
                    FROM
                        vtiger_lptempcampos
                            JOIN
                        vtiger_crmentity AS c1 ON lptempcamposid = c1.crmid
                            AND c1.deleted = 0
                            JOIN
                        vtiger_lptempcamposdetalle ON tcd_template = lptempcamposid
                            JOIN
                        vtiger_crmentity AS c2 ON lptempcamposdetalleid = c2.crmid
                            AND c2.deleted = 0
                            JOIN
                        vtiger_lptempcamposseleccion ON ts_template = lptempcamposid
                            AND ts_modulo = tc_modulo
                            AND ts_campo = tc_campo
                            JOIN
                        vtiger_crmentity AS c3 ON lptempcamposseleccionid = c3.crmid
                            AND c3.deleted = 0
                    WHERE
                        ts_modulo = t.name AND ts_template = ?
                    GROUP BY tcd_campo)
            ORDER BY uitype"

            ,

            array($moduleName, $templateId)
        
        );

        // Consultar todos los campos:
        $resultado_todos = $adb->pquery(
            
            "SELECT 
                fieldname, uitype, fieldlabel
            FROM
                vtiger_field AS f
                    JOIN
                vtiger_tab AS t ON f.tabid = t.tabid
            WHERE
                t.name = ?
            ORDER BY uitype"

            ,

            array($moduleName)
        
        );

        // Construir reglas:

        $retorno = array(
            
            // Condicion necesaria
            'condicion' => array(
                'es_es' => null,
                'campo' => null,
                'uitype' => null,
                'valores' => array()
            ),
            
            // Campos a mostrar
            'mostrar' => array(),

            // Campos ocultar
            'ocultar' => array(),

            // Todos los campos
            'todos' => array(),
        
        );

        foreach ($resultado_mostrar as $r) {

            // Construir info de las posibles condiciones:
            $retorno['condicion']['es_es'] = vtranslate($r['condflabel'], $moduleName);
            $retorno['condicion']['campo'] = $r['condfname'];
            $retorno['condicion']['uitype'] = $r['conduitype'];
            $retorno['condicion']['valores'] = explode(" |##| ", $r['ts_valor']);
            
            // Construir info de que mostrar:
            $retorno['mostrar'][] = array(
                'es_es' => vtranslate($r['showflabel'], $moduleName),
                'campo' => $r['showfname'],
                'uitype' => $r['showuitype'],
                'mandatory' => !!$r['tcd_obligatorio'],
            );

        }

        foreach ($resultado_ocultar as $r) {
            
            // Si el campo no es el de la condicion del template...
            if ($retorno['condicion']['campo'] <> $r['fieldname']) {

                // ... agregar para ocultar:
                $retorno['ocultar'][] = array(
                    'es_es' => vtranslate($r['fieldlabel'], $moduleName),
                    'campo' => $r['fieldname'],
                    'uitype' => $r['uitype'],
                );

            }

        }

        foreach ($resultado_todos as $r) {
            
            // Si el campo no es el de la condicion del template...
            if ($retorno['condicion']['campo'] <> $r['fieldname']) {

                // ... agregar a todos:
                $retorno['todos'][] = array(
                    'es_es' => vtranslate($r['fieldlabel'], $moduleName),
                    'campo' => $r['fieldname'],
                    'uitype' => $r['uitype'],
                );

            }

        }

        $log->debug(array(__METHOD__, $retorno));

        return $retorno;

    }

    public static function translate($key, $moduleName = '') {

        if (!in_array($moduleName, array(
            
            'LPTempFlujos',
            'LPTempFlujoCambios',
            'LPTempCampos',
            'LPTempCamposDetalle',
            'LPTempCamposSeleccion'
        
        ))) {
    
            $args = func_get_args();

            $formattedString = call_user_func_array(array('Vtiger_Language_Handler', 'getTranslatedString'), $args);
            
            array_shift($args);
            array_shift($args);
            
            if (is_array($args) && !empty($args)) {
                $formattedString = call_user_func_array('vsprintf', array($formattedString, $args));
            }
            
            return $formattedString;
    
        }
    
        return $key;
    
    }

}