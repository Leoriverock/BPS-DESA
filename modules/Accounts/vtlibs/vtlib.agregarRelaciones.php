<?php

include_once 'vtlib/Vtiger/Module.php';

$MODULENAME = 'Accounts';

$moduleInstance = Vtiger_Module::getInstance($MODULENAME);
$ticketModuleInstance = Vtiger_Module::getInstance('HelpDesk');

$relaciones = array(
    'HelpDesk'    => array(
        'fn'      => 'get_tickets',
        'label'   => 'HelpDesk',
        'field'   => Vtiger_Field::getInstance('contact_id', $ticketModuleInstance),
        'actions' => array('ADD'),
    )
);

foreach ($relaciones as $modulo => $detalles) {
    $relatedToModule = Vtiger_Module::getInstance($modulo);

    $moduleInstance->unsetRelatedList($relatedToModule, $detalles['label'], $detalles['fn']);
    $moduleInstance->setRelatedList($relatedToModule, $detalles['label'], $detalles['actions'], $detalles['fn'], $detalles['field'] ? $detalles['field']->id : null);
}

echo "Fin";