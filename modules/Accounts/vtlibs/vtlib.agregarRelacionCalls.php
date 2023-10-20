<?php 

include_once("vtlib/Vtiger/Module.php");

$Vtiger_Utils_Log = true;

echo "Agregando related list de Calls a Accounts...<br>";

//vtlib para agregar las related list a programas
$moduleInstance = Vtiger_Module::getInstance('Accounts');

//Related list con documents
$Calls = Vtiger_Module::getInstance('Calls');
$moduleInstance->unsetRelatedList($Calls);
// Initialize all the tables required
$moduleInstance->setRelatedList($Calls, 'Calls', Array(),'get_dependents_list');

?>