<?php

include_once "vtlib/Vtiger/Module.php";
$Vtiger_Utils_Log = true;

$moduleInstance = Vtiger_Module::getInstance("Calls");

$fieldInstance = Vtiger_Field::getInstance("calluser", $moduleInstance);
if ($fieldInstance) {
	$fieldInstance->delete();
}

echo "FIN";