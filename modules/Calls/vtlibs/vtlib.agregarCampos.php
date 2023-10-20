<?php

$actualizarCampos = false;

include_once("vtlib/Vtiger/Module.php");
$Vtiger_Utils_Log = true;

$moduleInstance = Vtiger_Module::getInstance("Calls");

if ($moduleInstance) {
    $blockInstance = Vtiger_Block::getInstance("LBL_CALLS_INFORMATION", $moduleInstance);

    $callcantinc = Vtiger_Field::getInstance('callcantinc', $moduleInstance);

    if (!$callcantinc) {
        $callcantinc = new Vtiger_Field();
        $callcantinc->name = 'callcantinc';
        $callcantinc->label = $callcantinc->name;
        $callcantinc->uitype = 7;
        $callcantinc->column = $callcantinc->name;
        $callcantinc->table = $moduleInstance->basetable;
        $callcantinc->columntype = 'INTEGER';
        $callcantinc->typeofdata = 'NN~O~10,0';
        $callcantinc->displaytype = 2;
        $blockInstance->addField($callcantinc);
    }

    $callcantcom = Vtiger_Field::getInstance('callcantcom', $moduleInstance);

    if (!$callcantcom) {
        $callcantcom = new Vtiger_Field();
        $callcantcom->name = 'callcantcom';
        $callcantcom->label = $callcantcom->name;
        $callcantcom->uitype = 7;
        $callcantcom->column = $callcantcom->name;
        $callcantcom->table = $moduleInstance->basetable;
        $callcantcom->columntype = 'INTEGER';
        $callcantcom->typeofdata = 'NN~O~10,0';
        $callcantcom->displaytype = 2;
        $blockInstance->addField($callcantcom);
    }

    echo "Ok";
}
