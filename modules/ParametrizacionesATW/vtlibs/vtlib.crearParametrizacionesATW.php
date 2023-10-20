<?php

// Turn on debugging level
$Vtiger_Utils_Log = true;

include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');

$module = Vtiger_Module::getInstance('ParametrizacionesATW');
if (!$module) {
	// Create module instance and save it first
	$module = new Vtiger_Module();
	$module->name = 'ParametrizacionesATW';
    $module->parent = 'TOOLS';
	$module->save();
	$module->initTables();   
}

$blockInstance = Vtiger_Block::getInstance('LBL_PARAMETRIZACIONESATW_INFORMATION', $module);
if (!$blockInstance){
	$blockInstance = new Vtiger_Block();
	$blockInstance->label = 'LBL_PARAMETRIZACIONESATW_INFORMATION';
	$module->addBlock($blockInstance);
}



$pt_grupoatw = Vtiger_Field::getInstance('pt_grupoatw', $module);
if (!$pt_grupoatw) {
    $pt_grupoatw = new Vtiger_Field();
    $pt_grupoatw->name = 'pt_grupoatw';
    $pt_grupoatw->label= 'Grupos';
    $pt_grupoatw->table =  $module->basetable;
    $pt_grupoatw->column = 'pt_grupoatw';
    $pt_grupoatw->columntype = 'VARCHAR(500)';
    $pt_grupoatw->uitype = 33;
    $pt_grupoatw->typeofdata = 'V~O';
    $blockInstance->addField($pt_grupoatw);
    $pt_grupoatw->setPicklistValues( Array ('A','B'));
}


/** Common fields that should be in every module, linked to vtiger CRM core table */
$assignedto = Vtiger_Field::getInstance('assigned_user_id', $module);
if (!$assignedto) {
	$assignedto = new Vtiger_Field();
	$assignedto->name = 'assigned_user_id';
	$assignedto->label = 'Assigned To';
	$assignedto->table = 'vtiger_crmentity'; 
	$assignedto->column = 'smownerid';
	$assignedto->uitype = 53;
	$assignedto->typeofdata = 'V~M';
	$blockInstance->addField($assignedto);
}

$createdtime = Vtiger_Field::getInstance('CreatedTime', $module);
if (!$createdtime) {
	$createdtime = new Vtiger_Field();
	$createdtime->name = 'CreatedTime';
	$createdtime->label= 'Created Time';
	$createdtime->table = 'vtiger_crmentity';
	$createdtime->column = 'createdtime';
	$createdtime->uitype = 70;
	$createdtime->typeofdata = 'T~O';
	$createdtime->displaytype= 2;
	$blockInstance->addField($createdtime);
}

$modifiedtime = Vtiger_Field::getInstance('ModifiedTime', $module);
if (!$modifiedtime) {
	$modifiedtime = new Vtiger_Field();
	$modifiedtime->name = 'ModifiedTime';
	$modifiedtime->label= 'Modified Time';
	$modifiedtime->table = 'vtiger_crmentity';
	$modifiedtime->column = 'modifiedtime';
	$modifiedtime->uitype = 70;
	$modifiedtime->typeofdata = 'T~O';
	$modifiedtime->displaytype= 2;
	$blockInstance->addField($modifiedtime);
}

/** END */


$module->initWebService();

// Create default custom filter (mandatory)
// borro todos los filtros (esto se hace por si se corre mas de una vez este script)
Vtiger_Filter::deleteForModule($module); // borra los filtros si existieran
$filter1 = new Vtiger_Filter();
$filter1->name = 'All';
$filter1->isdefault = true;
$module->addFilter($filter1);

// Add fields to the filter created
$filter1->addField($pt_grupoatw);

/** Set sharing access of this module */
$module->setDefaultSharing('Public'); 

echo "ok";

include_once('includes\Loader.php');
include_once('modules\Vtiger\models\Module.php');
include_once('modules\Settings\Vtiger\models\CustomRecordNumberingModule.php');
$moduleSequence = Settings_Vtiger_CustomRecordNumberingModule_Model::getInstance($module->name);
$moduleSequence->set('prefix', 'CDC');
$moduleSequence->set('sequenceNumber', 1);
$moduleSequence->setModuleSequence();
?>