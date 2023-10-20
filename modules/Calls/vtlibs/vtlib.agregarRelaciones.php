<?php

include_once 'vtlib/Vtiger/Module.php';

$MODULENAME = 'Calls';

$moduleInstance = Vtiger_Module::getInstance($MODULENAME);

$relaciones = array(
    'HelpDesk'    => array(
        'fn'      => 'get_related_list',
        'label'   => 'Tickets relacionados',
        'field'   => null,
        'actions' => array('ADD', 'SELECT'),
    ),
    'ModComments' => array(
        'fn'      => 'get_comentarios',
        'label'   => 'Comentarios relacionados',
        'field'   => null,
        'actions' => array('ADD', 'SELECT'),
    ),
);

foreach ($relaciones as $modulo => $detalles) {
    $relatedToModule = Vtiger_Module::getInstance($modulo);

    $moduleInstance->unsetRelatedList($relatedToModule, $detalles['label'], $detalles['fn']);
    $moduleInstance->setRelatedList($relatedToModule, $detalles['label'], $detalles['actions'], $detalles['fn'], $detalles['field'] ? $detalles['field']->id : null);
}

echo "Fin";