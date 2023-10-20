<?php

include_once 'vtlib/Vtiger/Module.php';

$Vtiger_Utils_Log = true;

$MODULENAME = 'HelpDesk';

$moduleInstance = Vtiger_Module::getInstance($MODULENAME);

$fieldInstance = Vtiger_Field::getInstance("solution", $moduleInstance);

$fieldInstance->delete();

?>