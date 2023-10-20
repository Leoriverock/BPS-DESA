<?php 
$Vtiger_Utils_Log = true;

include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');
global $log;
$moduleInstance = Vtiger_Module::getInstance("AtencionesWeb");
//var_dump($moduleInstance);
//echo "<br><br><br>";
$blockInstance = Vtiger_Block::getInstance("LBL_ATENCIONESWEB_INFORMATION",$moduleInstance);
//var_dump($blockInstance);


$aw_categoria = Vtiger_Field::getInstance('aw_categoria', $moduleInstance);
if(!$aw_categoria){
	echo "<br><br><br>entra";
	$aw_categoria = new Vtiger_Field();
	$aw_categoria->name = 'aw_categoria';
	$aw_categoria->label= 'Categoria';
	$aw_categoria->table =  'vtiger_atencionesweb';
	$aw_categoria->column = 'aw_categoria';
	$aw_categoria->columntype = 'VARCHAR(200)';
	$aw_categoria->uitype = 16;
	$aw_categoria->typeofdata = 'V~O';
	$aw_categoria->displaytype = 2;
	$blockInstance->addField($aw_categoria);
	$aw_categoria->setPicklistValues( Array ("Inicial"));
}

$aw_estado = Vtiger_Field::getInstance('aw_estado', $module);
if(!$aw_estado){
	$aw_estado = new Vtiger_Field();
    $aw_estado->name = 'aw_estado';
    $aw_estado->label= 'Estado';
    $aw_estado->table =  'vtiger_atencionesweb';
    $aw_estado->column = 'aw_estado';
    $aw_estado->columntype = 'VARCHAR(20)';
    $aw_estado->uitype = 16;
    $aw_estado->typeofdata = 'V~O';
    $aw_estado->displaytype = 1;
    $blockInstance->addField($aw_estado);
    $aw_estado->setPicklistValues( Array ('Asignado','Finalizada','Pausado'));
}
   
$aw_tema = Vtiger_Field::getInstance('aw_tema', $module);
if ($aw_tema) $aw_tema->delete();
$aw_tema = Vtiger_Field::getInstance('aw_tema', $module);
if (!$aw_tema) {
    $aw_tema = new Vtiger_Field();
    $aw_tema->name = 'aw_tema';
    $aw_tema->label = 'Tema';
    $aw_tema->table = 'vtiger_atencionesweb';
    $aw_tema->column = 'aw_tema';
    $aw_tema->columntype = 'INT(19)';
    $aw_tema->uitype = 10;
    $aw_tema->typeofdata = 'NN~O~10,0';
    $aw_tema->displaytype = 1;
    $blockInstance->addField($aw_tema);
    $aw_tema->setRelatedmodules(Array("Topics")); 
}

