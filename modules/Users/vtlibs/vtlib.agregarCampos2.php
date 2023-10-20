<?php

include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');
include_once("modules/ModTracker/ModTracker.php");
include_once('include/utils/utils.php');
include_once('modules/com_vtiger_workflow/VTEntityMethodManager.inc');

global $adb;

$MODULENAME = 'Users';

echo "agregando campos $MODULENAME <br>";

$moduleInstance = Vtiger_Module::getInstance($MODULENAME);
if($moduleInstance){
	$blockInstance = Vtiger_Block::getInstance('LBL_MORE_INFORMATION', $moduleInstance);
	$fieldName     = 'equipo';
	$fieldInstance = Vtiger_Field::getInstance($fieldName, $moduleInstance);
	if (!$fieldInstance) {
	    $fieldInstance             = new Vtiger_Field();
	    $fieldInstance->name       = $fieldName;
	    $fieldInstance->label      = $fieldName;
	    $fieldInstance->table      = $moduleInstance->basetable;
	    $fieldInstance->column     = $fieldInstance->name;
	    $fieldInstance->uitype     = 1;
	    $fieldInstance->displaytupe = 2;
	    $fieldInstance->columntype = 'VARCHAR(200)';
	    $fieldInstance->typeofdata = 'V~O';
	    $blockInstance->addField($fieldInstance);
	}

	$lugcod = Vtiger_Field::getInstance("lugcod", $moduleInstance);
		if(!$lugcod){
			$lugcod = new Vtiger_Field();
			$lugcod->name = "lugcod";
			$lugcod->label = "lugcod";
			$lugcod->table = $moduleInstance->basetable;
			$lugcod->column = "lugcod";
			$lugcod->columntype = "VARCHAR(100)";
			$lugcod->displaytype = 2;
		 	$lugcod->uitype = 1;
		 	$lugcod->typeofdata = 'V~O';
			$blockInstance->addField($lugcod);
		}
	
}





echo "agregando campos $MODULENAME FIN <br>";