<?php

// Turn on debugging level
$Vtiger_Utils_Log = true;

include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');

$module = Vtiger_Module::getInstance('AtencionPresencial');
if (!$module) {
	// Create module instance and save it first
	$module = new Vtiger_Module();
	$module->name = 'AtencionPresencial';
    $module->parent = "SUPPORT";
	$module->save();
	$module->initTables();
	global $adb;
	$adb->pquery('INSERT INTO vtiger_app2tab (tabid, appname, sequence, visible) VALUES (?, "SUPPORT", 7, 1)', array($module->id));   
}

$blockInstance = Vtiger_Block::getInstance('LBL_ATENCIONPRESENCIAL_INFORMATION', $module);
if (!$blockInstance){
	$blockInstance = new Vtiger_Block();
	$blockInstance->label = 'LBL_ATENCIONPRESENCIAL_INFORMATION';
	$module->addBlock($blockInstance);
}

/*$ap_numero = Vtiger_Field::getInstance("ap_numero", $module);
if(!$ap_numero){
	$ap_numero = new Vtiger_Field();
	$ap_numero->name = "ap_numero";
	$ap_numero->label = "ap_numero";
	$ap_numero->table = $module->basetable;
	$ap_numero->column = "ap_numero";
	$ap_numero->columntype = "VARCHAR(19)";
	$ap_numero->displaytype = 1;
 	$ap_numero->uitype = 4;
 	$ap_numero->typeofdata = 'V~M';
	$blockInstance->addField($ap_numero);
}*/
$module->setEntityIdentifier($ap_numero);

$ap_fechacomienzo = Vtiger_Field::getInstance("ap_fechacomienzo", $module);
if(!$ap_fechacomienzo){
	$ap_fechacomienzo = new Vtiger_Field();
	$ap_fechacomienzo->name = "ap_fechacomienzo";
	$ap_fechacomienzo->label = "ap_fechacomienzo";
	$ap_fechacomienzo->table = $module->basetable;
	$ap_fechacomienzo->column = "ap_fechacomienzo";
	$ap_fechacomienzo->columntype = "DATETIME";
	$ap_fechacomienzo->displaytype = 2;
 	$ap_fechacomienzo->uitype = 70;
 	$ap_fechacomienzo->typeofdata = 'DT~O';
	$blockInstance->addField($ap_fechacomienzo);
}

$ap_fechafin = Vtiger_Field::getInstance("ap_fechafin", $module);
if(!$ap_fechafin){
	$ap_fechafin = new Vtiger_Field();
	$ap_fechafin->name = "ap_fechafin";
	$ap_fechafin->label = "ap_fechafin";
	$ap_fechafin->table = $module->basetable;
	$ap_fechafin->column = "ap_fechafin";
	$ap_fechafin->columntype = "DATETIME";
	$ap_fechafin->displaytype = 2;
 	$ap_fechafin->uitype = 70;
 	$ap_fechafin->typeofdata = 'DT~O';
	$blockInstance->addField($ap_fechafin);
}

$ap_persona = Vtiger_Field::getInstance('ap_persona', $module);
if (!$ap_persona) {
	$ap_persona = new Vtiger_Field();
	$ap_persona->name = 'ap_persona';
	$ap_persona->label = 'ap_persona';
	$ap_persona->table = $module->basetable;
	$ap_persona->column = 'ap_persona';
	$ap_persona->columntype = 'INT(19)';
	$ap_persona->uitype = 10;
	$ap_persona->typeofdata = 'NN~O~10,0';
	$ap_persona->displaytype = 1;
	$blockInstance->addField($ap_persona);
	$ap_persona->setRelatedmodules(Array("Accounts")); 
}

$ap_numerows = Vtiger_Field::getInstance("ap_numerows", $module);
if(!$ap_numerows){
	$ap_numerows = new Vtiger_Field();
	$ap_numerows->name = "ap_numerows";
	$ap_numerows->label = "ap_numerows";
	$ap_numerows->table = $module->basetable;
	$ap_numerows->column = "ap_numerows";
	$ap_numerows->columntype = "VARCHAR(100)";
	$ap_numerows->displaytype = 2;
 	$ap_numerows->uitype = 1;
 	$ap_numerows->typeofdata = 'V~O';
	$blockInstance->addField($ap_numerows);
}

$ap_estado = Vtiger_Field::getInstance('ap_estado', $module);
if (!$ap_estado) {
    $ap_estado = new Vtiger_Field();
    $ap_estado->name = 'ap_estado';
    $ap_estado->label= 'ap_estado';
    $ap_estado->table =  $module->basetable;
    $ap_estado->column = 'ap_estado';
    $ap_estado->columntype = 'VARCHAR(100)';
    $ap_estado->uitype = 16;
    $ap_estado->typeofdata = 'V~M';
    $ap_estado->displaytype = 1;
    $blockInstance->addField($ap_estado);
    $ap_estado->setPicklistValues( Array ());
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
$filter1->addField($ap_numero)->addField($ap_fechacomienzo, 1)->addField($ap_fechafin, 2)->addField($ap_persona, 3);

/** Set sharing access of this module */
$module->setDefaultSharing('Public'); 

echo "ok";

include_once('includes\Loader.php');
include_once('modules\Vtiger\models\Module.php');
include_once('modules\Settings\Vtiger\models\CustomRecordNumberingModule.php');
$moduleSequence = Settings_Vtiger_CustomRecordNumberingModule_Model::getInstance($module->name);
$moduleSequence->set('prefix', 'ATP');
$moduleSequence->set('sequenceNumber', 1);
$moduleSequence->setModuleSequence();
?>