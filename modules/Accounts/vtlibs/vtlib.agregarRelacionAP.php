<?php 

include_once("vtlib/Vtiger/Module.php");

$Vtiger_Utils_Log = true;

echo "Agregando related list de AtencionPresencial a Accounts...<br>";

//vtlib para agregar las related list a programas
$moduleInstance = Vtiger_Module::getInstance('Accounts');

//Related list con documents
$AtencionPresencial = Vtiger_Module::getInstance('AtencionPresencial');
$moduleInstance->unsetRelatedList($AtencionPresencial);
// Initialize all the tables required
$moduleInstance->setRelatedList($AtencionPresencial, 'AtencionPresencial', Array(),'get_dependents_list');

?>