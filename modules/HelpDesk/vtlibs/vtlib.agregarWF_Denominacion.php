<?php

require_once 'vtlib/Vtiger/Menu.php';
require_once 'vtlib/Vtiger/Module.php';
require_once 'include/utils/utils.php';
require_once 'modules/com_vtiger_workflow/VTEntityMethodManager.inc';

global $adb;
if(empty($adb)) {
	$adb = PearDatabase::getInstance();
}
	
$emm = new VTEntityMethodManager($adb);

$moduleName = 'HelpDesk';
$methodsNames = array('WF_setDenominacionCreateTicket');

$methods = $emm->methodsForModule($moduleName);

foreach ($methodsNames as $methodName) {
	if (!in_array($methodName, $methods)) {
		$emm->addEntityMethod($moduleName, $methodName, "modules/$moduleName/WF_Funciones.php", $methodName);
	}
}

echo "Fin";