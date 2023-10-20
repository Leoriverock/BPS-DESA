<?php 

include_once("vtlib/Vtiger/Module.php");

$Vtiger_Utils_Log = true;

echo "Agregando related list de Documents a ConsultasWeb...<br>";

//vtlib para agregar las related list a programas
$moduleInstance = Vtiger_Module::getInstance('ConsultasWeb');

//Related list con documents
$Documents = Vtiger_Module::getInstance('Documents');
$moduleInstance->unsetRelatedList($Documents);
// Initialize all the tables required
$moduleInstance->setRelatedList($Documents, 'Documents', Array('add,select'),'get_attachments');

?>