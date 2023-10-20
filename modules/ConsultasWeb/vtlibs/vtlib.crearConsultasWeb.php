<?php

// Turn on debugging level
$Vtiger_Utils_Log = true;

include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');

$module = Vtiger_Module::getInstance('ConsultasWeb');
if (!$module) {
	// Create module instance and save it first
	$module = new Vtiger_Module();
	$module->name = 'ConsultasWeb';
    $module->parent = "SUPPORT";
	$module->save();
	$module->initTables();   
}

$blockInstance = Vtiger_Block::getInstance('LBL_CONSULTASWEB_INFORMATION', $module);
if (!$blockInstance){
	$blockInstance = new Vtiger_Block();
	$blockInstance->label = 'LBL_CONSULTASWEB_INFORMATION';
	$module->addBlock($blockInstance);
}

$cw_origen = Vtiger_Field::getInstance("cw_origen", $module);
if(!$cw_origen){
	$cw_origen = new Vtiger_Field();
	$cw_origen->name = "cw_origen";
	$cw_origen->label = "Origen";
	$cw_origen->table = $module->basetable;
	$cw_origen->column = "cw_origen";
	$cw_origen->columntype = "VARCHAR(100)";
	$cw_origen->displaytype = 2;
 	$cw_origen->uitype = 1;
 	$cw_origen->typeofdata = 'V~M';
	$blockInstance->addField($cw_origen);
}

$cw_de_mail = Vtiger_Field::getInstance("cw_de_mail", $module);
if(!$cw_de_mail){
	$cw_de_mail = new Vtiger_Field();
	$cw_de_mail->name = "cw_de_mail";
	$cw_de_mail->label = "De Correo";
	$cw_de_mail->table = $module->basetable;
	$cw_de_mail->column = "cw_de_mail";
	$cw_de_mail->columntype = "VARCHAR(100)";
	$cw_de_mail->displaytype = 2;
 	$cw_de_mail->uitype = 1;
 	$cw_de_mail->typeofdata = 'V~M';
	$blockInstance->addField($cw_de_mail);
}

$cw_para = Vtiger_Field::getInstance("cw_para", $module);
if(!$cw_para){
	$cw_para = new Vtiger_Field();
	$cw_para->name = "cw_para";
	$cw_para->label = "Para";
	$cw_para->table = $module->basetable;
	$cw_para->column = "cw_para";
	$cw_para->columntype = "VARCHAR(100)";
	$cw_para->displaytype = 2;
 	$cw_para->uitype = 1;
 	$cw_para->typeofdata = 'V~M';
	$blockInstance->addField($cw_para);
}


$cw_asunto = Vtiger_Field::getInstance("cw_asunto", $module);
if(!$cw_asunto){
	$cw_asunto = new Vtiger_Field();
	$cw_asunto->name = "cw_asunto";
	$cw_asunto->label = "Asunto";
	$cw_asunto->table = $module->basetable;
	$cw_asunto->column = "cw_asunto";
	$cw_asunto->columntype = "VARCHAR(200)";
	$cw_asunto->displaytype = 2;
 	$cw_asunto->uitype = 1;
 	$cw_asunto->typeofdata = 'V~M';
	$blockInstance->addField($cw_asunto);
}

$cw_contenido = Vtiger_Field::getInstance("cw_contenido", $module);
if(!$cw_contenido){
	$cw_contenido = new Vtiger_Field();
	$cw_contenido->name = "cw_contenido";
	$cw_contenido->label = "Contenido";
	$cw_contenido->table = $module->basetable;
	$cw_contenido->column = "cw_contenido";
	$cw_contenido->columntype = "text";
	$cw_contenido->displaytype = 2;
 	$cw_contenido->uitype = 19;
 	$cw_contenido->typeofdata = 'V~O';
	$blockInstance->addField($cw_contenido);	
}


$cw_empresa = Vtiger_Field::getInstance("cw_empresa", $module);
if(!$cw_empresa){
	$cw_empresa = new Vtiger_Field();
	$cw_empresa->name = "cw_empresa";
	$cw_empresa->label = "Nro empresa";
	$cw_empresa->table = $module->basetable;
	$cw_empresa->column = "cw_empresa";
	$cw_empresa->columntype = "VARCHAR(100)";
	$cw_empresa->displaytype = 2;
 	$cw_empresa->uitype = 1;
 	$cw_empresa->typeofdata = 'V~O';
	$blockInstance->addField($cw_empresa);
}

$cw_contribuyente = Vtiger_Field::getInstance("cw_contribuyente", $module);
if(!$cw_contribuyente){
	$cw_contribuyente = new Vtiger_Field();
	$cw_contribuyente->name = "cw_contribuyente";
	$cw_contribuyente->label = "Numero de Contribuyente";
	$cw_contribuyente->table = $module->basetable;
	$cw_contribuyente->column = "cw_contribuyente";
	$cw_contribuyente->columntype = "VARCHAR(100)";
	$cw_contribuyente->displaytype = 2;
 	$cw_contribuyente->uitype = 1;
 	$cw_contribuyente->typeofdata = 'V~O';
	$blockInstance->addField($cw_contribuyente);	
}

$cw_aportacion = Vtiger_Field::getInstance("cw_aportacion", $module);
if(!$cw_aportacion){
	$cw_aportacion = new Vtiger_Field();
	$cw_aportacion->name = "cw_aportacion";
	$cw_aportacion->label = "Tipo aportacion";
	$cw_aportacion->table = $module->basetable;
	$cw_aportacion->column = "cw_aportacion";
	$cw_aportacion->columntype = "VARCHAR(100)";
	$cw_aportacion->displaytype = 2;
 	$cw_aportacion->uitype = 1;
 	$cw_aportacion->typeofdata = 'V~O';
	$blockInstance->addField($cw_aportacion);
}

$cw_obra = Vtiger_Field::getInstance("cw_obra", $module);
if(!$cw_obra){
	$cw_obra = new Vtiger_Field();
	$cw_obra->name = "cw_obra";
	$cw_obra->label = "Obra";
	$cw_obra->table = $module->basetable;
	$cw_obra->column = "cw_obra";
	$cw_obra->columntype = "VARCHAR(100)";
	$cw_obra->displaytype = 2;
 	$cw_obra->uitype = 1;
 	$cw_obra->typeofdata = 'V~O';
	$blockInstance->addField($cw_obra);
}

$cw_persona = Vtiger_Field::getInstance('cw_persona', $module);
if (!$cw_persona) {
	$cw_persona = new Vtiger_Field();
	$cw_persona->name = 'cw_persona';
	$cw_persona->label = 'Persona';
	$cw_persona->table = $module->basetable;
	$cw_persona->column = 'cw_persona';
	$cw_persona->columntype = 'INT(19)';
	$cw_persona->uitype = 10;
	$cw_persona->typeofdata = 'NN~O~10,0';
	$cw_persona->displaytype = 2;
	$blockInstance->addField($cw_persona);
	$cw_persona->setRelatedmodules(Array("Accounts")); 
}


$cw_usuario = Vtiger_Field::getInstance("cw_usuario", $module);
if(!$cw_usuario){
	$cw_usuario = new Vtiger_Field();
	$cw_usuario->name = "cw_usuario";
	$cw_usuario->label = "Usuario";
	$cw_usuario->table = $module->basetable;
	$cw_usuario->column = "cw_usuario";
	$cw_usuario->columntype = "VARCHAR(30)";
	$cw_usuario->displaytype = 2;
 	$cw_usuario->uitype = 1;
 	$cw_usuario->typeofdata = 'V~O';
	$blockInstance->addField($cw_usuario);
}

$cw_nombre = Vtiger_Field::getInstance("cw_nombre",$module);
if(!$cw_nombre){
	$cw_nombre = new Vtiger_Field();
	$cw_nombre->name = "cw_nombre";
	$cw_nombre->label = "Nombre";
	$cw_nombre->table = $module->basetable;
	$cw_nombre->column = "cw_nombre";
	$cw_nombre->columntype = "VARCHAR(100)";
	$cw_nombre->displaytype = 2;
	$cw_nombre->uitype = 1;
	$cw_nombre->typeofdata = 'V~O';
	$blockInstance->addField($cw_nombre);
}

/*$cw_telefono = Vtiger_Field::getInstance("cw_telefono",$module);
if(!$cw_telefono){
	$cw_telefono = new Vtiger_Field();
	$cw_telefono->name = "cw_telefono";
	$cw_telefono->label = "Telefono";
	$cw_telefono->table = $module->basetable;
	$cw_telefono->column = "cw_telefono";
	$cw_telefono->columntype = "VARCHAR(100)";
	$cw_telefono->displaytype = 2;
	$cw_telefono->uitype = 11;
	$cw_telefono->typeofdata = 'V~O';
	$blockInstance->addField($cw_telefono);
}*/

$cw_grupo = Vtiger_Field::getInstance('cw_grupo', $module);
if (!$cw_grupo) {
    $cw_grupo = new Vtiger_Field();
    $cw_grupo->name = 'cw_grupo';
    $cw_grupo->label= 'Grupo';
    $cw_grupo->table =  $module->basetable;
    $cw_grupo->column = 'cw_grupo';
    $cw_grupo->columntype = 'VARCHAR(20)';
    $cw_grupo->uitype = 16;
    $cw_grupo->typeofdata = 'V~O';
    $cw_grupo->displaytype = 1;
    $blockInstance->addField($cw_grupo);
    $cw_grupo->setPicklistValues( Array ());
}

$cw_tema = Vtiger_Field::getInstance('cw_tema', $module);
if (!$cw_tema) {
    $cw_tema = new Vtiger_Field();
    $cw_tema->name = 'cw_tema';
    $cw_tema->label= 'Tema';
    $cw_tema->table =  $module->basetable;
    $cw_tema->column = 'cw_tema';
    $cw_tema->columntype = 'VARCHAR(20)';
    $cw_tema->uitype = 16;
    $cw_tema->typeofdata = 'V~O';
    $cw_tema->displaytype = 1;
    $blockInstance->addField($cw_tema);
    $cw_tema->setPicklistValues( Array ());
}

$cw_estado = Vtiger_Field::getInstance('cw_estado', $module);
if (!$cw_estado) {
    $cw_estado = new Vtiger_Field();
    $cw_estado->name = 'cw_estado';
    $cw_estado->label= 'Estado';
    $cw_estado->table =  $module->basetable;
    $cw_estado->column = 'cw_estado';
    $cw_estado->columntype = 'VARCHAR(20)';
    $cw_estado->uitype = 16;
    $cw_estado->typeofdata = 'V~O';
    $blockInstance->addField($cw_estado);
    $cw_estado->setPicklistValues( Array ('Pendiente','Asignada','Contestada','Transferida','Descartada','Pendiente Agente'));
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
$filter1->addField($cw_origen)->addField($cw_de_mail, 1)->addField($cw_asunto, 2)->addField($cw_para, 3);

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