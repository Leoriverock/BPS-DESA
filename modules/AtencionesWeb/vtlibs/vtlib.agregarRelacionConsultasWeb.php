<?php 

include_once("vtlib/Vtiger/Module.php");

$Vtiger_Utils_Log = true;

echo "Agregando related list de Documents a ConsultasWeb...<br>";

//vtlib para agregar las related list a programas
$moduleInstance = Vtiger_Module::getInstance('AtencionesWeb');

//Related list con documents
$ConsultasWeb = Vtiger_Module::getInstance('ConsultasWeb');
$moduleInstance->unsetRelatedList($ConsultasWeb);
// Initialize all the tables required
$moduleInstance->setRelatedList($ConsultasWeb, 'ConsultasWeb', Array('ADD'),'get_related_list');

?>