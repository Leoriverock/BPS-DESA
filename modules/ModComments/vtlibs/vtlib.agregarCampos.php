<?php

include_once 'vtlib/Vtiger/Module.php';

$Vtiger_Utils_Log = true;

$MODULENAME = 'ModComments';

$moduleInstance = Vtiger_Module::getInstance($MODULENAME);

$blockName = 'LBL_OTHER_INFORMATION';
$blockInstance = Vtiger_Block::getInstance($blockName, $moduleInstance);

$fieldName = 'callsid';
$fieldInstance = Vtiger_Field::getInstance($fieldName, $moduleInstance);
if (!$fieldInstance) {
    $fieldInstance             = new Vtiger_Field();
    $fieldInstance->name       = $fieldName;
    $fieldInstance->label      = $fieldName;
    $fieldInstance->table      = $moduleInstance->basetable;
    $fieldInstance->column     = $fieldInstance->name;
    $fieldInstance->uitype     = 10;
    $fieldInstance->columntype = 'VARCHAR(100)';
    $fieldInstance->typeofdata = 'V~O';
    $blockInstance->addField($fieldInstance);
    $fieldInstance->setRelatedModules(array('Calls'));
}