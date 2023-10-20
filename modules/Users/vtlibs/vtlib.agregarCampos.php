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
	$us_grupopref = Vtiger_Field::getInstance('us_grupopref', $moduleInstance);
	if (!$us_grupopref) {
	    $us_grupopref = new Vtiger_Field();
	    $us_grupopref->name = 'us_grupopref';
	    $us_grupopref->label = 'us_grupopref';
	    $us_grupopref->table = $moduleInstance->basetable;
	    $us_grupopref->uitype = 53;
	    $us_grupopref->column = $us_grupopref->name;
	    $us_grupopref->columntype = 'int(19)';
	    $us_grupopref->typeofdata = 'I~O';
	    $blockInstance->addField($us_grupopref);
	}
	
}

echo "agregando campos $MODULENAME FIN <br>";