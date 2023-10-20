<?php

include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');
include_once("modules/ModTracker/ModTracker.php");
include_once('include/utils/utils.php');
include_once('modules/com_vtiger_workflow/VTEntityMethodManager.inc');

global $adb, $site_URL;

$MODULENAME = 'Accounts';


$moduleInstance = Vtiger_Module::getInstance($MODULENAME);

$blockInstance = Vtiger_Block::getInstance('LBL_ACCOUNT_INFORMATION', $moduleInstance);

/*
$accempexternalnumber = Vtiger_Field::getInstance('accempexternalnumber', $moduleInstance);

if (!$accempexternalnumber) {
    $accempexternalnumber = new Vtiger_Field();
    $accempexternalnumber->name = 'accempexternalnumber';
    $accempexternalnumber->label = 'accempexternalnumber';
    $accempexternalnumber->table = $moduleInstance->basetable;
    $accempexternalnumber->uitype = 7;
    $accempexternalnumber->column = $accempexternalnumber->name;
    $accempexternalnumber->columntype = 'INTEGER';
    $accempexternalnumber->typeofdata = 'NN~O~20,0';
    $blockInstance->addField($accempexternalnumber);
}

$accempinternalnumber = Vtiger_Field::getInstance('accempinternalnumber', $moduleInstance);

if (!$accempinternalnumber) {
    $accempinternalnumber = new Vtiger_Field();
    $accempinternalnumber->name = 'accempinternalnumber';
    $accempinternalnumber->label = 'accempinternalnumber';
    $accempinternalnumber->table = $moduleInstance->basetable;
    $accempinternalnumber->uitype = 7;
    $accempinternalnumber->column = $accempinternalnumber->name;
    $accempinternalnumber->columntype = 'INTEGER';
    $accempinternalnumber->typeofdata = 'NN~O~20,0';
    $blockInstance->addField($accempinternalnumber);
}
*/

echo "<br>LISTO $MODULENAME";
