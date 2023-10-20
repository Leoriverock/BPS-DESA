<?php 

include_once("vtlib/Vtiger/Module.php");

$Vtiger_Utils_Log = true;

echo "Agregando related list de Documents a ConsultasWeb...<br>";

//vtlib para agregar las related list a programas
$moduleInstance = Vtiger_Module::getInstance('Accounts');

//Related list con documents
$AtencionesWeb = Vtiger_Module::getInstance('AtencionesWeb');
$moduleInstance->unsetRelatedList($AtencionesWeb);
// Initialize all the tables required
$moduleInstance->setRelatedList($AtencionesWeb, 'AtencionesWeb', Array(),'get_related_list');

?>