<?php
include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');
include_once("modules/ModTracker/ModTracker.php");
include_once('include/utils/utils.php');
include_once('modules/com_vtiger_workflow/VTEntityMethodManager.inc');

$MODULENAME = 'LPTempCamposDetalle';

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

$LBL_LPTempCamposDetalle_General = Vtiger_Block::getInstance('LBL_LPTempCamposDetalle_General', $moduleInstance);

if (!$LBL_LPTempCamposDetalle_General) {
    $LBL_LPTempCamposDetalle_General = new Vtiger_Block();
    $LBL_LPTempCamposDetalle_General->label = 'LBL_LPTempCamposDetalle_General';
    $moduleInstance->addBlock($LBL_LPTempCamposDetalle_General);
}

/*******************************************************************************
Se crea el bloque de auditoria
*******************************************************************************/

$LBL_LPTempCamposDetalle_Auditoria = Vtiger_Block::getInstance('LBL_LPTempCamposDetalle_Auditoria', $moduleInstance);

if (!$LBL_LPTempCamposDetalle_Auditoria) {
    $LBL_LPTempCamposDetalle_Auditoria = new Vtiger_Block();
    $LBL_LPTempCamposDetalle_Auditoria->label = 'LBL_LPTempCamposDetalle_Auditoria';
    $moduleInstance->addBlock($LBL_LPTempCamposDetalle_Auditoria);
}

/*******************************************************************************
Se crean los CAMPOS para el bloque de datos generales
*******************************************************************************/

$tcd_template = Vtiger_Field::getInstance('tcd_template', $moduleInstance);

if (!$tcd_template) {
    $tcd_template = new Vtiger_Field();
    $tcd_template->name = 'tcd_template';
    $tcd_template->label = 'tcd_template';
    $tcd_template->table = $moduleInstance->basetable;
    $tcd_template->uitype = 10;
    $tcd_template->column = $tcd_template->name;
    $tcd_template->columntype = 'INT';
    $tcd_template->typeofdata = 'I~O';
    $LBL_LPTempCamposDetalle_General->addField($tcd_template);
    $tcd_template->setRelatedModules(Array('LPTempCampos'));
}

$tcd_campo = Vtiger_Field::getInstance('tcd_campo', $moduleInstance);

if (!$tcd_campo) {
    $tcd_campo = new Vtiger_Field();
    $tcd_campo->name = 'tcd_campo';
    $tcd_campo->label = 'tcd_campo';
    $tcd_campo->table = $moduleInstance->basetable;
    $tcd_campo->uitype = 16;
    $tcd_campo->column = $tcd_campo->name;
    $tcd_campo->columntype = 'VARCHAR(100)';
    $tcd_campo->typeofdata = 'V~O';
    $LBL_LPTempCamposDetalle_General->addField($tcd_campo);
    $tcd_campo->setPicklistValues(Array('Dummy'));
}

$tcd_obligatorio = Vtiger_Field::getInstance('tcd_obligatorio', $moduleInstance);

if (!$tcd_obligatorio) {
    $tcd_obligatorio = new Vtiger_Field();
    $tcd_obligatorio->name = 'tcd_obligatorio';
    $tcd_obligatorio->label = 'tcd_obligatorio';
    $tcd_obligatorio->table = $moduleInstance->basetable;
    $tcd_obligatorio->uitype = 56;
    $tcd_obligatorio->column = $tcd_obligatorio->name;
    $tcd_obligatorio->columntype = 'INT';
    $tcd_obligatorio->typeofdata = 'C~O';
    $LBL_LPTempCamposDetalle_General->addField($tcd_obligatorio);
}

$tcd_orden = Vtiger_Field::getInstance('tcd_orden', $moduleInstance);

if (!$tcd_orden) {
    $tcd_orden = new Vtiger_Field();
    $tcd_orden->name = 'tcd_orden';
    $tcd_orden->label = 'tcd_orden';
    $tcd_orden->table = $moduleInstance->basetable;
    $tcd_orden->uitype = 1;
    $tcd_orden->column = $tcd_orden->name;
    $tcd_orden->columntype = 'INT';
    $tcd_orden->typeofdata = 'I~O';
    $LBL_LPTempCamposDetalle_General->addField($tcd_orden);
}

/******************************************************************************/

$moduleInstance->setEntityIdentifier($tcd_template); // Para vtiger_entityname

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
    $LBL_LPTempCamposDetalle_Auditoria->addField($assigned_user_id);
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
    $LBL_LPTempCamposDetalle_Auditoria->addField($createdtime);
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
    $LBL_LPTempCamposDetalle_Auditoria->addField($modifiedtime);
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
    
    $filter1->addField($tcd_template)
    ->addField($tcd_campo, 1)
    ->addField($tcd_obligatorio, 2)
    ->addField($tcd_orden, 3);

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

$moduloPadre = Vtiger_Module::getInstance('LPTempCampos'); // Este es el 'padre'
$etiqueta = 'LPTempCamposDetalle'; // Tener en cuenta a la hora de unsetear (*)
$permisos = Array('ADD'); // Posibles operaciones permitidas
$funcion = 'get_dependents_list'; // Tambien puede ser una custom (*)
$campoHijo = $tcd_template->id; // Para que se autocomplete
$moduloPadre->unsetRelatedList($moduleInstance, $etiqueta, $funcion);
$moduloPadre->setRelatedList($moduleInstance, $etiqueta, $permisos, $funcion, $campoHijo);

echo "<br>LISTO $MODULENAME";