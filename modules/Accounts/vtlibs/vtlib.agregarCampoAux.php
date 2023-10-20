<?php

include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');
include_once("modules/ModTracker/ModTracker.php");
include_once('include/utils/utils.php');
include_once('modules/com_vtiger_workflow/VTEntityMethodManager.inc');

global $adb, $site_URL;

$MODULENAME = 'Accounts';

$moduleInstance = Vtiger_Module::getInstance($MODULENAME);

$blockInstance = Vtiger_Block::getInstance('LBL_ACCOUNT_INFORMATION', $moduleInstance);

$accaux = Vtiger_Field::getInstance("accaux", $moduleInstance);
if(!$accaux){
	$accaux = new Vtiger_Field();
	$accaux->name = "accaux";
	$accaux->label = "Auxiliar";
	$accaux->table = $moduleInstance->basetable;
	$accaux->column = "accaux";
	$accaux->columntype = "VARCHAR(300)";
	$accaux->displaytype = 2;
	$accaux->presence = 1;
 	$accaux->uitype = 1;
 	$accaux->typeofdata = 'V~O';
	$blockInstance->addField($accaux);
}

