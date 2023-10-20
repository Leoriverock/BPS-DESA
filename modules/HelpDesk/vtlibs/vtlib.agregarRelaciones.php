<?php

include_once 'vtlib/Vtiger/Module.php';

$MODULENAME = 'HelpDesk';

$moduleInstance = Vtiger_Module::getInstance($MODULENAME);

$relaciones = array(
    'Calls' => array(
        'fn'      => 'get_related_list',
        'label'   => 'Llamadas relacionadas',
        'field'   => null,
        'actions' => array(),
    ),
    'AtencionesWeb' => array(
        'fn'      => 'get_related_list',
        'label'   => 'Atenciones relacionadas',
        'field'   => null,
        'actions' => array(),
    ),
    'Relationship' => array(
        'fn'      => 'get_dependents_list',
        'label'   => 'Relaciones',
        'field'   => null,
        'actions' => array(),
    ),
);

foreach ($relaciones as $modulo => $detalles) {
    $relatedToModule = Vtiger_Module::getInstance($modulo);

    $moduleInstance->unsetRelatedList($relatedToModule, $detalles['label'], $detalles['fn']);
    $moduleInstance->setRelatedList($relatedToModule, $detalles['label'], $detalles['actions'], $detalles['fn'], $detalles['field'] ? $detalles['field']->id : null);
}

echo "Fin";
