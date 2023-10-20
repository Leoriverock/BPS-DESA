<?php

include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');
include_once("modules/ModTracker/ModTracker.php");
include_once('include/utils/utils.php');
include_once('modules/com_vtiger_workflow/VTEntityMethodManager.inc');

global $adb, $site_URL;

$MODULENAME = 'Accounts';

$adb->pquery("DELETE FROM vtiger_accdocumenttype", array());
$adb->pquery("DELETE FROM vtiger_acccountry", array());

$xml = simplexml_load_file($site_URL . "modules/Accounts/vtlibs/PaisesBPS.xml");
$countries = array();
foreach ($xml->colPaises as $pais) {
    $countries[] = $pais->nombre;
}

$moduleInstance = Vtiger_Module::getInstance($MODULENAME);

$blockInstance = Vtiger_Block::getInstance('LBL_ACCOUNT_INFORMATION', $moduleInstance);

$accdocumenttype = Vtiger_Field::getInstance('accdocumenttype', $moduleInstance);

if (!$accdocumenttype) {
    $accdocumenttype = new Vtiger_Field();
    $accdocumenttype->name = 'accdocumenttype';
    $accdocumenttype->label = 'accdocumenttype';
    $accdocumenttype->table = $moduleInstance->basetable;
    $accdocumenttype->uitype = 15;
    $accdocumenttype->column = $accdocumenttype->name;
    $accdocumenttype->columntype = 'VARCHAR(20)';
    $accdocumenttype->typeofdata = 'V~O';
    $blockInstance->addField($accdocumenttype);
    $accdocumenttype->setPicklistValues(array("Documento", "Pasaporte", "Fronterizo"));
}
else{
    $accdocumenttype->delete();
}

$acccountry = Vtiger_Field::getInstance('acccountry', $moduleInstance);

if (!$acccountry) {
    $acccountry = new Vtiger_Field();
    $acccountry->name = 'acccountry';
    $acccountry->label = 'acccountry';
    $acccountry->table = $moduleInstance->basetable;
    $acccountry->uitype = 15;
    $acccountry->column = $acccountry->name;
    $acccountry->columntype = 'VARCHAR(30)';
    $acccountry->typeofdata = 'V~O';
    $blockInstance->addField($acccountry);
    $acccountry->setPicklistValues($countries);
}
else{
    $acccountry->delete();
}

echo "<br>LISTO $MODULENAME";
