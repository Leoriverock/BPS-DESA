<?php

include_once 'vtlib/Vtiger/Module.php';

$Vtiger_Utils_Log = true;

$MODULENAME = 'HelpDesk';

$moduleInstance = Vtiger_Module::getInstance($MODULENAME);


$blockName     = 'LBL_TICKET_INFORMATION';
$blockInstance = Vtiger_Block::getInstance($blockName, $moduleInstance);


$ticketcreadopor = Vtiger_Field::getInstance('smcreatorid', $moduleInstance);
if (!$ticketcreadopor) {
    $ticketcreadopor = new Vtiger_Field();
    $ticketcreadopor->name = 'creator';
    $ticketcreadopor->label = 'creator';
    $ticketcreadopor->table = 'vtiger_crmentity';
    $ticketcreadopor->column = 'smcreatorid';
    $ticketcreadopor->columntype = 'INT(19)';
    $ticketcreadopor->uitype = 52;
    $ticketcreadopor->displaytype = 2;
    $ticketcreadopor->typeofdata = 'V~O';
    $blockInstance->addField($ticketcreadopor);
}