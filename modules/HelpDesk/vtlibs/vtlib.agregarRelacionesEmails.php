<?php 

include_once("vtlib/Vtiger/Module.php");

$Vtiger_Utils_Log = true;

echo "Agregando related list de Documents a ConsultasWeb...<br>";

//vtlib para agregar las related list a programas
$moduleInstance = Vtiger_Module::getInstance('HelpDesk');

//Related list con documents
$Emails = Vtiger_Module::getInstance('Emails');
$moduleInstance->unsetRelatedList($Emails);
// Initialize all the tables required
$moduleInstance->setRelatedList($Emails, 'Emails', Array('ADD'),'get_related_list');

?>