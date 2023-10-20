<?php

include_once 'vtlib/Vtiger/Module.php';

$Vtiger_Utils_Log = true;

$MODULENAME = 'AtencionPresencial';

$moduleInstance = Vtiger_Module::getInstance($MODULENAME);

if($moduleInstance){
	$commentsModule = Vtiger_Module::getInstance('ModComments');
	$fieldInstance = Vtiger_Field::getInstance('related_to', $commentsModule);
	$fieldInstance->setRelatedModules(array($MODULENAME));
	$relatedLists = array(
	    array('modulo' => 'HelpDesk', 'nombre' => 'HelpDesk', 'acciones' => array('ADD'), 'funcion' => 'get_related_list'),
	    array('modulo' => 'ModComments', 'nombre' => 'ModComments', 'acciones' => array(), 'funcion' => 'get_comments'),
	);

	foreach ($relatedLists as $value) {
	    $relatedModule = Vtiger_Module::getInstance($value['modulo']);
	    $fieldInstance = Vtiger_Field::getInstance($value['campo'], $relatedModule);
	    $campoid = null;
	    if($fieldInstance) $campoid = $fieldInstance->id;
	    if($moduleInstance && $relatedModule){
	        $moduleInstance->unsetRelatedList($relatedModule, $value['nombre'], $value['funcion']);
	        $moduleInstance->setRelatedList($relatedModule, $value['nombre'], $value['acciones'], $value['funcion'], $campoid);
	    }
	}
}

echo "listo!!";