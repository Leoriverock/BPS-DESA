--------------------------------------------------------------------------------

                --------------------------------------------
                Instrucciones de como instalar LpDynFields.x
                --------------------------------------------

        Modulos que se deben instalar (modules y layouts/v7/modules):
        LPTempCampos, LPTempCamposDetalle y LPTempCamposSeleccion 

--------------------------------------------------------------------------------

1.0 - Editar el archivo modules/Vtiger/views/Index.php:

    1.1 - Incluir antes de la definicion de la clase:

        // /////////////////////////////////////////////////////
        // 		Para ocultar campos por parametrizacion:
        require_once('libraries/lp_dyn_fields/LpDynFields.php');
        // /////////////////////////////////////////////////////

    1.2 - Agregar en la funcion preProcess luego del $moduleName = $request->getModule();:
    
        // ///////////////////////////////////////////////////////////////////
        // Para obtener las reglas de como ocultar campos por parametrizacion:
        $viewer->assign('LP_DYNFIELD_RULES', LpDynFields::rules($moduleName));
        // ///////////////////////////////////////////////////////////////////

    1.3 - Incluir al ultimo en el getHeaderScripts:
        
        // ////////////////////////////////////////
        // Para ocultar campos por parametrizacion:
        "~libraries/lp_dyn_fields/LpDynFields.js", 
        // ////////////////////////////////////////

2.0 - Agregar abajo de _META en el archvo layouts/v7/modules/Vtiger/Header.tpl:
    
    var LP_DYNFIELD_RULES = '{$LP_DYNFIELD_RULES}' ? JSON.parse('{$LP_DYNFIELD_RULES}') : null;
    (pero no hacer comentarios con barras al estilo "// ///..." porque sino se rompe el JS)

