<?php

include_once 'vtlib/Vtiger/Module.php';

$Vtiger_Utils_Log = true;

$MODULENAME = 'TemaAsignado';

//creamos el modulo y verifico
$moduleInstance = Vtiger_Module::getInstance($MODULENAME);
if (!$moduleInstance) {
	$moduleInstance = new Vtiger_Module();
	$moduleInstance->name = $MODULENAME;
	$moduleInstance->parent= 'Support';
	$moduleInstance->save();
	$moduleInstance->initWebservice();
	$moduleInstance->initTables();
}

$block = Vtiger_Block::getInstance('LBL_TEMAASIGNADO_INFORMATION',$moduleInstance);
if(!$block){
	$block = new Vtiger_Block();
	$block->label = "LBL_TEMAASIGNADO_INFORMATION"; //Información de relaciones
	$moduleInstance->addBlock($block);
}

$ta_tema = Vtiger_Field::getInstance('ta_tema', $moduleInstance);
if(!$ta_tema) {
	$ta_tema = new Vtiger_Field();
	$ta_tema->name = 'ta_tema';
	$ta_tema->label = "ta_tema";
	$ta_tema->table = $moduleInstance->basetable;
	$ta_tema->column = $ta_tema->name;
	$ta_tema->columntype = 'VARCHAR(200)';
	$ta_tema->uitype = 1;
	$ta_tema->typeofdata = 'V~O';
	$block->addField($ta_tema);
}

$ta_motivo = Vtiger_Field::getInstance('ta_motivo', $moduleInstance);
if(!$ta_motivo) {
	$ta_motivo = new Vtiger_Field();
	$ta_motivo->name = 'ta_motivo';
	$ta_motivo->label = "ta_motivo";
	$ta_motivo->table = $moduleInstance->basetable;
	$ta_motivo->column = $ta_motivo->name;
	$ta_motivo->columntype = 'VARCHAR(200)';
	$ta_motivo->uitype = 1;
	$ta_motivo->typeofdata = 'V~O';
	$block->addField($ta_motivo);
}

$field = new Vtiger_Field();
$field->name = 'ta_tema,ta_motivo';
$moduleInstance->setEntityIdentifier($field);

$ta_grupo = Vtiger_Field::getInstance('ta_grupo', $moduleInstance);
if(!$ta_grupo) {
	$ta_grupo = new Vtiger_Field();
	$ta_grupo->name = 'ta_grupo';
	$ta_grupo->label = "ta_grupo";
	$ta_grupo->table = $moduleInstance->basetable;
	$ta_grupo->column = $ta_grupo->name;
	$ta_grupo->columntype = 'INT(19)';
	$ta_grupo->uitype = 53;
	$ta_grupo->typeofdata = 'I~O';
	$block->addField($ta_grupo);
}

$ta_topic = Vtiger_Field::getInstance('ta_topic', $moduleInstance);
if(!$ta_topic) {
	$ta_topic = new Vtiger_Field();
	$ta_topic->name = 'ta_topic';
	$ta_topic->label = "ta_topic";
	$ta_topic->table = $moduleInstance->basetable;
	$ta_topic->column = $ta_topic->name;
	$ta_topic->columntype = 'INT(19)';
	$ta_topic->uitype = 10;
	$ta_topic->typeofdata = 'I~O';
	$block->addField($ta_topic);
	$ta_topic->setRelatedmodules(array('Topics'));
}


$moduleInstance->enableTools(Array('Import', 'Export'));
$moduleInstance->disableTools('Merge');

// Sharing Access Setup
$moduleInstance->setDefaultSharing();
//mkdir('modules/'.$MODULENAME);


$moduleInstance->initWebservice();

Vtiger_Filter::deleteForModule($moduleInstance); // borra los filtros si existieran
$filter1 = new Vtiger_Filter();
$filter1->name = 'All';
$filter1->isdefault = true;
$moduleInstance->addFilter($filter1);
// Add fields to the filter created
$filter1->addField($ta_tema)->addField($ta_motivo,1)->addField($ta_topic, 2)->addField($ta_grupo, 3);

echo "\n\nOK\n";


?>
