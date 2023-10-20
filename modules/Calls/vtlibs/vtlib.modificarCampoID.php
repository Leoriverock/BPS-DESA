<?php

include_once("vtlib/Vtiger/Module.php");
$Vtiger_Utils_Log = true;

$moduleInstance = Vtiger_Module::getInstance("Calls");

if (!$moduleInstance) {
    $moduleInstance = new Vtiger_Module();
    $moduleInstance->name = "Calls";
    $moduleInstance->parent = "Sales";
    $moduleInstance->save();

    // Schema Setup
    $moduleInstance->initTables();
    mkdir("modules/Calls");
}


if ($moduleInstance) {
    $blockInstance = Vtiger_Block::getInstance("LBL_CALLS_INFORMATION", $moduleInstance);

    $callid = Vtiger_Field::getInstance('callid', $moduleInstance);
    
    if (!$callid) {
        $callid = new Vtiger_Field();
        $callid->name = 'callid';
        $callid->label = $callid->name;
        $callid->uitype = 1;
        $callid->column = $callid->name;
        $callid->table = $moduleInstance->basetable;
        $callid->columntype = 'VARCHAR(18)';
        $callid->typeofdata = 'V~O';
        $callid->displaytype = 1;
        $blockInstance->addField($callid);
    }
    else{
        $callid->delete();
    }
}
