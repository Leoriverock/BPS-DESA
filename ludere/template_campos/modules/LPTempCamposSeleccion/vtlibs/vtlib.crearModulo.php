<?php
include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');
include_once("modules/ModTracker/ModTracker.php");
include_once('include/utils/utils.php');
include_once('modules/com_vtiger_workflow/VTEntityMethodManager.inc');

$MODULENAME = 'LPTempCamposSeleccion';

echo "CREAR $MODULENAME";

/*******************************************************************************
Se crea el MODULO
*******************************************************************************/

$moduleInstance = Vtiger_Module::getInstance($MODULENAME);

if (!$moduleInstance) {
    $moduleInstance = new Vtiger_Module();
    $moduleInstance->name = $MODULENAME;
    $moduleInstance->parent = 'Support';
    $moduleInstance->save();
    $moduleInstance->initTables();
}

/*******************************************************************************
Se crea el bloque de datos generales
*******************************************************************************/

$LBL_LPTempCamposSeleccion_General = Vtiger_Block::getInstance('LBL_LPTempCamposSeleccion_General', $moduleInstance);

if (!$LBL_LPTempCamposSeleccion_General) {
    $LBL_LPTempCamposSeleccion_General = new Vtiger_Block();
    $LBL_LPTempCamposSeleccion_General->label = 'LBL_LPTempCamposSeleccion_General';
    $moduleInstance->addBlock($LBL_LPTempCamposSeleccion_General);
}

/*******************************************************************************
Se crea el bloque de auditoria
*******************************************************************************/

$LBL_LPTempCamposSeleccion_Auditoria = Vtiger_Block::getInstance('LBL_LPTempCamposSeleccion_Auditoria', $moduleInstance);

if (!$LBL_LPTempCamposSeleccion_Auditoria) {
    $LBL_LPTempCamposSeleccion_Auditoria = new Vtiger_Block();
    $LBL_LPTempCamposSeleccion_Auditoria->label = 'LBL_LPTempCamposSeleccion_Auditoria';
    $moduleInstance->addBlock($LBL_LPTempCamposSeleccion_Auditoria);
}

/*******************************************************************************
Se crean los CAMPOS para el bloque de datos generales
*******************************************************************************/

$ts_modulo = Vtiger_Field::getInstance('ts_modulo', $moduleInstance);

if ($ts_modulo) {
    $ts_modulo->delete();
    $ts_modulo = null;
}

if (!$ts_modulo) {

    $ts_modulo = new Vtiger_Field();
    
    $ts_modulo->name = 'ts_modulo';
    $ts_modulo->label = 'ts_modulo';
    $ts_modulo->table = $moduleInstance->basetable;
    $ts_modulo->uitype = 16;
    $ts_modulo->column = $ts_modulo->name;
    $ts_modulo->columntype = 'VARCHAR(100)';
    $ts_modulo->typeofdata = 'V~O';
    
    $LBL_LPTempCamposSeleccion_General->addField($ts_modulo);
    
    $ts_modulo->setPicklistValues(Array('Dummy'));

    $adb->query("DROP TABLE IF EXISTS vtiger_ts_modulo");
    $adb->query("DROP VIEW IF EXISTS vtiger_ts_modulo");

    $adb->query(
        "CREATE VIEW vtiger_ts_modulo AS
        SELECT 
            t.tabid AS ts_moduloid,
            t.name AS ts_modulo,
            1 AS precense,
            t.tabid AS picklist_valueid,
            1 AS sortorderid,
            '' AS color
        FROM
            vtiger_tab t
        WHERE
            t.tabid IN (SELECT DISTINCT
                    tabid
                FROM
                    vtiger_field
                WHERE
                    uitype IN (15 , 16, 33))
        ORDER BY tabid"
    );

}

$ts_template = Vtiger_Field::getInstance('ts_template', $moduleInstance);

if (!$ts_template) {
    $ts_template = new Vtiger_Field();
    $ts_template->name = 'ts_template';
    $ts_template->label = 'ts_template';
    $ts_template->table = $moduleInstance->basetable;
    $ts_template->uitype = 10;
    $ts_template->column = $ts_template->name;
    $ts_template->columntype = 'INT';
    $ts_template->typeofdata = 'I~O';
    $LBL_LPTempCamposSeleccion_General->addField($ts_template);
    $ts_template->setRelatedModules(Array('LPTempCampos'));
}

$ts_campo = Vtiger_Field::getInstance('ts_campo', $moduleInstance);

if (!$ts_campo) {
    $ts_campo = new Vtiger_Field();
    $ts_campo->name = 'ts_campo';
    $ts_campo->label = 'ts_campo';
    $ts_campo->table = $moduleInstance->basetable;
    $ts_campo->uitype = 16;
    $ts_campo->column = $ts_campo->name;
    $ts_campo->columntype = 'VARCHAR(100)';
    $ts_campo->typeofdata = 'V~O';
    $LBL_LPTempCamposSeleccion_General->addField($ts_campo);
    $ts_campo->setPicklistValues(Array('Dummy'));
}

$ts_valor = Vtiger_Field::getInstance('ts_valor', $moduleInstance);

if (!$ts_valor) {
    $ts_valor = new Vtiger_Field();
    $ts_valor->name = 'ts_valor';
    $ts_valor->label = 'ts_valor';
    $ts_valor->table = $moduleInstance->basetable;
    $ts_valor->uitype = 33;
    $ts_valor->column = $ts_valor->name;
    $ts_valor->columntype = 'TEXT';
    $ts_valor->typeofdata = 'V~O';
    $LBL_LPTempCamposSeleccion_General->addField($ts_valor);
    $ts_valor->setPicklistValues(Array('Dummy'));
}

/******************************************************************************/

$moduleInstance->setEntityIdentifier($ts_modulo); // Para vtiger_entityname

/******************************************************************************/

// Se asignan los CAMPOS ESCENCIALES / para el bloque de auditoria...

$assigned_user_id = Vtiger_Field::getInstance('assigned_user_id', $moduleInstance);

if (!$assigned_user_id) {
    $assigned_user_id = new Vtiger_Field();
    $assigned_user_id->name = 'assigned_user_id';
    $assigned_user_id->label = 'Assigned To';
    $assigned_user_id->table = 'vtiger_crmentity';
    $assigned_user_id->column = 'smownerid';
    $assigned_user_id->uitype = 53;
    $assigned_user_id->typeofdata = 'V~M';
    $LBL_LPTempCamposSeleccion_Auditoria->addField($assigned_user_id);
}

$createdtime = Vtiger_Field::getInstance('createdtime', $moduleInstance);

if (!$createdtime) {
    $createdtime = new Vtiger_Field();
    $createdtime->name = 'createdtime';
    $createdtime->label= 'Created Time';
    $createdtime->table = 'vtiger_crmentity';
    $createdtime->column = 'createdtime';
    $createdtime->uitype = 70;
    $createdtime->typeofdata = 'T~O';
    $createdtime->displaytype = 2;
    $LBL_LPTempCamposSeleccion_Auditoria->addField($createdtime);
}

$modifiedtime = Vtiger_Field::getInstance('modifiedtime', $moduleInstance);

if (!$modifiedtime) {
    $modifiedtime = new Vtiger_Field();
    $modifiedtime->name = 'modifiedtime';
    $modifiedtime->label = 'Modified Time';
    $modifiedtime->table = 'vtiger_crmentity';
    $modifiedtime->column = 'modifiedtime';
    $modifiedtime->uitype = 70;
    $modifiedtime->typeofdata = 'T~O';
    $modifiedtime->displaytype = 2;
    $LBL_LPTempCamposSeleccion_Auditoria->addField($modifiedtime);
}

/******************************************************************************/

// Crear un filtro por defecto con al menos un campo obligatorio...

Vtiger_Filter::deleteForModule($moduleInstance); // BORRAR FILTROS

$filter1 = Vtiger_Filter::getInstance('All', $moduleInstance);

if (!$filter1) {
    
    $filter1 = new Vtiger_Filter();
    
    $filter1->name = 'All';
    $filter1->isdefault = true;
    
    $moduleInstance->addFilter($filter1);
    
    $filter1->addField($ts_modulo)
    ->addField($ts_template, 1)
    ->addField($ts_campo, 2)
    ->addField($ts_valor, 3);

}

/******************************************************************************/

// Establecer la modalidad de permisos...

$moduleInstance->setDefaultSharing();

$moduleInstance->initWebservice();

mkdir('modules/'.$MODULENAME);

/******************************************************************************/

// Desactivar y volver activar el registro de auditoria...

ModTracker::disableTrackingForModule($moduleInstance->id); 
ModTracker::enableTrackingForModule($moduleInstance->id); 

/******************************************************************************/

// Los registros de este tipo apuntan al modulo padre...

// $moduloPadre = Vtiger_Module::getInstance('Xxx'); // Este es el 'padre'
// $etiqueta = 'Aaa bbb ccc'; // Tener en cuenta a la hora de unsetear (*)
// $permisos = Array('SELECT', 'ADD'); // Posibles operaciones permitidas
// $funcion = 'get_dependents_list'; // Tambien puede ser una custom (*)
// $campoHijo = $YYY->id; // Para que se autocomplete
// $moduloPadre->unsetRelatedList($moduleInstance, $etiqueta, $funcion);
// $moduloPadre->setRelatedList($moduleInstance, $etiqueta, $permisos, $funcion, $campoHijo);

echo "<br>LISTO $MODULENAME";