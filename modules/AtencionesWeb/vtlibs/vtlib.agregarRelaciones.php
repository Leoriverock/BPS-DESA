<?php 

include_once("vtlib/Vtiger/Module.php");

$Vtiger_Utils_Log = true;

echo "Agregando related list de Documents a ConsultasWeb...<br>";

//vtlib para agregar las related list a programas
$moduleInstance = Vtiger_Module::getInstance('AtencionesWeb');

//Related list con documents
$HelpDesk = Vtiger_Module::getInstance('HelpDesk');
$moduleInstance->unsetRelatedList($HelpDesk);
// Initialize all the tables required
$moduleInstance->setRelatedList($HelpDesk, 'HelpDesk', Array('ADD'),'get_related_list');

//Related list con documents
$ModComments = Vtiger_Module::getInstance('ModComments');
$moduleInstance->unsetRelatedList($ModComments);
// Initialize all the tables required
$moduleInstance->setRelatedList($ModComments, 'ModComments', Array('ADD'),'get_related_list');


?>