<?php

// Turn on debugging level
$Vtiger_Utils_Log = true;

include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');

$module = Vtiger_Module::getInstance('Parametrizaciones');
if (!$module) {
	// Create module instance and save it first
	$module = new Vtiger_Module();
	$module->name = 'Parametrizaciones';
    $module->parent = 'TOOLS';
	$module->save();
	$module->initTables();   
}

$blockInstance = Vtiger_Block::getInstance('LBL_PARAMETRIZACIONES_INFORMATION', $module);
if (!$blockInstance){
	$blockInstance = new Vtiger_Block();
	$blockInstance->label = 'LBL_PARAMETRIZACIONES_INFORMATION';
	$module->addBlock($blockInstance);
}

$pt_origen = Vtiger_Field::getInstance("pt_origen", $module);
if(!$pt_origen){
	$pt_origen = new Vtiger_Field();
	$pt_origen->name = "pt_origen";
	$pt_origen->label = "Origen";
	$pt_origen->table = $module->basetable;
	$pt_origen->column = "pt_origen";
	$pt_origen->columntype = "VARCHAR(100)";
 	$pt_origen->uitype = 1;
 	$pt_origen->typeofdata = 'V~M';
	$blockInstance->addField($pt_origen);
}

$pt_tema = Vtiger_Field::getInstance("pt_tema", $module);
if(!$pt_tema){
	$pt_tema = new Vtiger_Field();
	$pt_tema->name = "pt_tema";
	$pt_tema->label = "Tema";
	$pt_tema->table = $module->basetable;
	$pt_tema->column = "pt_tema";
	$pt_tema->columntype = "VARCHAR(100)";
 	$pt_tema->uitype = 1;
 	$pt_tema->typeofdata = 'V~M';
	$blockInstance->addField($pt_tema);
}


$pt_grupo = Vtiger_Field::getInstance('pt_grupo', $module);
if (!$pt_grupo) {
    $pt_grupo = new Vtiger_Field();
    $pt_grupo->name = 'pt_grupo';
    $pt_grupo->label= 'Grupo';
    $pt_grupo->table =  $module->basetable;
    $pt_grupo->column = 'pt_grupo';
    $pt_grupo->columntype = 'VARCHAR(20)';
    $pt_grupo->uitype = 16;
    $pt_grupo->typeofdata = 'V~O';
    $blockInstance->addField($pt_grupo);
    $pt_grupo->setPicklistValues( Array (''));
}

$pt_temavtiger = Vtiger_Field::getInstance('pt_temavtiger', $module);
if (!$pt_temavtiger) {
	$pt_temavtiger = new Vtiger_Field();
	$pt_temavtiger->name = 'pt_temavtiger';
	$pt_temavtiger->label = 'Tema Vtiger';
	$pt_temavtiger->table = $module->basetable;
	$pt_temavtiger->column = 'pt_temavtiger';
	$pt_temavtiger->columntype = 'INT(19)';
	$pt_temavtiger->uitype = 10;
	$pt_temavtiger->typeofdata = 'NN~O~10,0';
	$pt_temavtiger->displaytype = 1;
	$blockInstance->addField($pt_temavtiger);
	$pt_temavtiger->setRelatedmodules(Array("Topics")); 
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
$filter1->addField($pt_origen)->addField($pt_tema, 1)->addField($pt_grupo, 2);

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