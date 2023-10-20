<?php 

include_once("vtlib/Vtiger/Module.php");

$Vtiger_Utils_Log = true;

$moduleInstance = Vtiger_Module::getInstance("ConsultasWeb");


$blockInstance = Vtiger_Block::getInstance("LBL_CONSULTASWEB_INFORMATION",$moduleInstance);


$cw_categoria = Vtiger_Field::getInstance('cw_categoria', $moduleInstance);
if (!$cw_categoria) {
	$cw_categoria = new Vtiger_Field();
	$cw_categoria->name = 'cw_categoria';
	$cw_categoria->label= 'Categoria';
	$cw_categoria->table =  'vtiger_consultasweb';
	$cw_categoria->column = 'cw_categoria';
	$cw_categoria->columntype = 'VARCHAR(200)';
	$cw_categoria->uitype = 16;
	$cw_categoria->typeofdata = 'V~O';
	$cw_categoria->displaytype = 2;
	$blockInstance->addField($cw_categoria);
	$cw_categoria->setPicklistValues( Array ("Inicial"));
}
