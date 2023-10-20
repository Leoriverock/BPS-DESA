<?php

include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');
include_once("modules/ModTracker/ModTracker.php");
include_once('include/utils/utils.php');
include_once('modules/com_vtiger_workflow/VTEntityMethodManager.inc');

global $adb;

$MODULENAME = 'LPTempCampos';

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

$LBL_LPTempCampos_General = Vtiger_Block::getInstance('LBL_LPTempCampos_General', $moduleInstance);

if (!$LBL_LPTempCampos_General) {
    $LBL_LPTempCampos_General = new Vtiger_Block();
    $LBL_LPTempCampos_General->label = 'LBL_LPTempCampos_General';
    $moduleInstance->addBlock($LBL_LPTempCampos_General);
}

/*******************************************************************************
Se crea el bloque de auditoria
*******************************************************************************/

$LBL_LPTempCampos_Auditoria = Vtiger_Block::getInstance('LBL_LPTempCampos_Auditoria', $moduleInstance);

if (!$LBL_LPTempCampos_Auditoria) {
    $LBL_LPTempCampos_Auditoria = new Vtiger_Block();
    $LBL_LPTempCampos_Auditoria->label = 'LBL_LPTempCampos_Auditoria';
    $moduleInstance->addBlock($LBL_LPTempCampos_Auditoria);
}

/*******************************************************************************
Se crean los CAMPOS para el bloque de datos generales
*******************************************************************************/

$tc_nombre = Vtiger_Field::getInstance('tc_nombre', $moduleInstance);

if (!$tc_nombre) {
    $tc_nombre = new Vtiger_Field();
    $tc_nombre->name = 'tc_nombre';
    $tc_nombre->label = 'tc_nombre';
    $tc_nombre->table = $moduleInstance->basetable;
    $tc_nombre->uitype = 1;
    $tc_nombre->column = $tc_nombre->name;
    $tc_nombre->columntype = 'VARCHAR(100)';
    $tc_nombre->typeofdata = 'V~O';
    $LBL_LPTempCampos_General->addField($tc_nombre);
}

$tc_modulo = Vtiger_Field::getInstance('tc_modulo', $moduleInstance);

if ($tc_modulo) {
    $tc_modulo->delete();
    $tc_modulo = null;
}

if (!$tc_modulo) {
    
    $tc_modulo = new Vtiger_Field();
    
    $tc_modulo->name = 'tc_modulo';
    $tc_modulo->label = 'tc_modulo';
    $tc_modulo->table = $moduleInstance->basetable;
    $tc_modulo->uitype = 16;
    $tc_modulo->column = $tc_modulo->name;
    $tc_modulo->columntype = 'VARCHAR(100)';
    $tc_modulo->typeofdata = 'V~O';
    
    $LBL_LPTempCampos_General->addField($tc_modulo);
    
    $tc_modulo->setPicklistValues(Array('Dummy'));

    $adb->query("DROP TABLE IF EXISTS vtiger_tc_modulo");
    $adb->query("DROP VIEW IF EXISTS vtiger_tc_modulo");

    $adb->query(
        "CREATE VIEW vtiger_tc_modulo AS
        SELECT 
            t.tabid AS tc_moduloid,
            t.name AS tc_modulo,
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

$tc_campo = Vtiger_Field::getInstance('tc_campo', $moduleInstance);

if (!$tc_campo) {
    $tc_campo = new Vtiger_Field();
    $tc_campo->name = 'tc_campo';
    $tc_campo->label = 'tc_campo';
    $tc_campo->table = $moduleInstance->basetable;
    $tc_campo->uitype = 16;
    $tc_campo->column = $tc_campo->name;
    $tc_campo->columntype = 'VARCHAR(100)';
    $tc_campo->typeofdata = 'V~O';
    $LBL_LPTempCampos_General->addField($tc_campo);
    $tc_campo->setPicklistValues(Array('Dummy'));
}

/******************************************************************************/

$moduleInstance->setEntityIdentifier($tc_nombre); // Para vtiger_entityname

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
    $LBL_LPTempCampos_Auditoria->addField($assigned_user_id);
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
    $LBL_LPTempCampos_Auditoria->addField($createdtime);
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
    $LBL_LPTempCampos_Auditoria->addField($modifiedtime);
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
    
    $filter1->addField($tc_nombre)
    ->addField($tc_modulo, 1)
    ->addField($tc_campo, 2);

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