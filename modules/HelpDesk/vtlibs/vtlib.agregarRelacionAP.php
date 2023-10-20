<?php 

include_once("vtlib/Vtiger/Module.php");

$Vtiger_Utils_Log = true;

echo "Agregando related list de Atenciones Presenciales a HelpDesk...<br>";

//vtlib para agregar las related list a programas
$moduleInstance = Vtiger_Module::getInstance('HelpDesk');

//Related list con atencion presencial
$AtencionPresencial = Vtiger_Module::getInstance('AtencionPresencial');
$moduleInstance->unsetRelatedList($AtencionPresencial);
// Initialize all the tables required
$moduleInstance->setRelatedList($AtencionPresencial, 'AtencionPresencial', Array(''),'get_related_list');

?>