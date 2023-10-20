<?php

// Turn on debugging level
$Vtiger_Utils_Log = true;

include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');

$module = Vtiger_Module::getInstance('AtencionesWeb');
if (!$module) {
	// Create module instance and save it first
	$module = new Vtiger_Module();
	$module->name = 'AtencionesWeb';
    //$module->parent = "";
	$module->save();
	$module->initTables();   
}

$blockInstance = Vtiger_Block::getInstance('LBL_ATENCIONESWEB_INFORMATION', $module);
if (!$blockInstance){
	$blockInstance = new Vtiger_Block();
	$blockInstance->label = 'LBL_ATENCIONESWEB_INFORMATION';
	$module->addBlock($blockInstance);
}

$aw_numero = Vtiger_Field::getInstance("aw_numero", $module);
if(!$aw_numero){
	$aw_numero = new Vtiger_Field();
	$aw_numero->name = "aw_numero";
	$aw_numero->label = "Numero";
	$aw_numero->table = $module->basetable;
	$aw_numero->column = "aw_numero";
	$aw_numero->columntype = "VARCHAR(50)";
 	$aw_numero->uitype = 4;
 	$aw_fechacomienzo->displaytype = 2;
 	$aw_numero->typeofdata = 'I~M';
	$blockInstance->addField($aw_numero);	
}

$aw_fechacomienzo = Vtiger_Field::getInstance('aw_fechacomienzo', $module);
if (!$aw_fechacomienzo) {
	$aw_fechacomienzo= new Vtiger_Field();
	$aw_fechacomienzo->name = 'aw_fechacomienzo';
	$aw_fechacomienzo->label = 'Fecha/Hora comienzo';
	$aw_fechacomienzo->table = $module->basetable;
	$aw_fechacomienzo->column = 'aw_fechacomienzo';
	$aw_fechacomienzo->columntype = 'datetime';
	$aw_fechacomienzo->uitype = 70;
	$aw_fechacomienzo->typeofdata = 'DT~M';
	$aw_fechacomienzo->displaytype = 2;
	$aw_fechacomienzo->summaryfield = 1;
	$blockInstance->addField($aw_fechacomienzo); 
}


$aw_fechafin = Vtiger_Field::getInstance('aw_fechafin', $module);
if (!$aw_fechafin) {
	$aw_fechafin= new Vtiger_Field();
	$aw_fechafin->name = 'aw_fechafin';
	$aw_fechafin->label = 'Fecha/Hora fin';
	$aw_fechafin->table = $module->basetable;
	$aw_fechafin->column = 'aw_fechafin';
	$aw_fechafin->columntype = 'datetime';
	$aw_fechafin->uitype = 70;
	$aw_fechafin->typeofdata = 'DT~M';
	$aw_fechafin->displaytype = 2;
	$aw_fechafin->summaryfield = 1;
	$blockInstance->addField($aw_fechafin); 
}

$aw_de = Vtiger_Field::getInstance("aw_de", $module);
if(!$aw_de){
	$aw_de = new Vtiger_Field();
	$aw_de->name = "aw_de";
	$aw_de->label = "De";
	$aw_de->table = $module->basetable;
	$aw_de->column = "aw_de";
	$aw_de->columntype = "VARCHAR(30)";
	$aw_de->displaytype = 2;
 	$aw_de->uitype = 1;
 	$aw_de->typeofdata = 'V~O';
	$blockInstance->addField($aw_de);
}

$aw_persona = Vtiger_Field::getInstance('aw_persona', $module);
if (!$aw_persona) {
	$aw_persona = new Vtiger_Field();
	$aw_persona->name = 'aw_persona';
	$aw_persona->label = 'Persona';
	$aw_persona->table = $module->basetable;
	$aw_persona->column = 'aw_persona';
	$aw_persona->columntype = 'INT(19)';
	$aw_persona->uitype = 10;
	$aw_persona->typeofdata = 'NN~O~10,0';
	$aw_persona->displaytype = 1;
	$blockInstance->addField($aw_persona);
	$aw_persona->setRelatedmodules(Array("Accounts")); 
}

$aw_cont_empresa = Vtiger_Field::getInstance("aw_cont_empresa", $module);
if($aw_cont_empresa) $aw_cont_empresa->delete();
$aw_cont_empresa = Vtiger_Field::getInstance("aw_cont_empresa", $module);
if(!$aw_cont_empresa){
	$aw_cont_empresa = new Vtiger_Field();
	$aw_cont_empresa->name = "aw_cont_empresa";
	$aw_cont_empresa->label = "Contribuyente Empresa";
	$aw_cont_empresa->table = 'vtiger_atencionesweb';
	$aw_cont_empresa->column = "aw_cont_empresa";
	$aw_cont_empresa->columntype = "INT(20)";
	$aw_cont_empresa->displaytype = 2;
 	$aw_cont_empresa->uitype = 1;
 	$aw_cont_empresa->typeofdata = 'I~O';
	$blockInstance->addField($aw_cont_empresa);
}


$aw_cont_aportacion = Vtiger_Field::getInstance("aw_cont_aportacion", $module);
if(!$aw_cont_aportacion){
	$aw_cont_aportacion = new Vtiger_Field();
	$aw_cont_aportacion->name = "aw_cont_aportacion";
	$aw_cont_aportacion->label = "Contribuyente Aportacion";
	$aw_cont_aportacion->table = $module->basetable;
	$aw_cont_aportacion->column = "aw_cont_aportacion";
	$aw_cont_aportacion->columntype = "VARCHAR(100)";
	$aw_cont_aportacion->displaytype = 2;
 	$aw_cont_aportacion->uitype = 1;
 	$aw_cont_aportacion->typeofdata = 'V~O';
	$blockInstance->addField($aw_cont_aportacion);
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
$filter1->addField($aw_fechacomienzo)->addField($aw_fechafin, 1)->addField($aw_de, 2)->addField($aw_persona, 3);

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