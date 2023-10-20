<?php

include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');
include_once("modules/ModTracker/ModTracker.php");
include_once('include/utils/utils.php');
include_once('modules/com_vtiger_workflow/VTEntityMethodManager.inc');

global $adb, $site_URL;

$MODULENAME = 'Accounts';

$xml = simplexml_load_file($site_URL . "modules/Accounts/vtlibs/PaisesBPS.xml");
$countries = array();
foreach ($xml->colPaises as $pais) {
    $countries[] = $pais->nombre;
}

$moduleInstance = Vtiger_Module::getInstance($MODULENAME);

$blockInstance = Vtiger_Block::getInstance('LBL_ACCOUNT_INFORMATION', $moduleInstance);

$acccontinternalnumber = Vtiger_Field::getInstance('acccontinternalnumber', $moduleInstance);

if (!$acccontinternalnumber) {
    $acccontinternalnumber = new Vtiger_Field();
    $acccontinternalnumber->name = 'acccontinternalnumber';
    $acccontinternalnumber->label = 'acccontinternalnumber';
    $acccontinternalnumber->table = $moduleInstance->basetable;
    $acccontinternalnumber->uitype = 7;
    $acccontinternalnumber->column = $acccontinternalnumber->name;
    $acccontinternalnumber->columntype = 'INTEGER';
    $acccontinternalnumber->typeofdata = 'NN~O~10,0';
    $blockInstance->addField($acccontinternalnumber);
}

$acccontexternalnumber = Vtiger_Field::getInstance('acccontexternalnumber', $moduleInstance);

if (!$acccontexternalnumber) {
    $acccontexternalnumber = new Vtiger_Field();
    $acccontexternalnumber->name = 'acccontexternalnumber';
    $acccontexternalnumber->label = 'acccontexternalnumber';
    $acccontexternalnumber->table = $moduleInstance->basetable;
    $acccontexternalnumber->uitype = 7;
    $acccontexternalnumber->column = $acccontexternalnumber->name;
    $acccontexternalnumber->columntype = 'BIGINT';
    $acccontexternalnumber->typeofdata = 'NN~O~10,0';
    $blockInstance->addField($acccontexternalnumber);
}

$accentinternalnumber = Vtiger_Field::getInstance('accentinternalnumber', $moduleInstance);

if (!$accentinternalnumber) {
    $accentinternalnumber = new Vtiger_Field();
    $accentinternalnumber->name = 'accentinternalnumber';
    $accentinternalnumber->label = 'accentinternalnumber';
    $accentinternalnumber->table = $moduleInstance->basetable;
    $accentinternalnumber->uitype = 7;
    $accentinternalnumber->column = $accentinternalnumber->name;
    $accentinternalnumber->columntype = 'INTEGER';
    $accentinternalnumber->typeofdata = 'NN~M~10,0';
    $blockInstance->addField($accentinternalnumber);
}

$accentexternalnumber = Vtiger_Field::getInstance('accentexternalnumber', $moduleInstance);

if (!$accentexternalnumber) {
    $accentexternalnumber = new Vtiger_Field();
    $accentexternalnumber->name = 'accentexternalnumber';
    $accentexternalnumber->label = 'accentexternalnumber';
    $accentexternalnumber->table = $moduleInstance->basetable;
    $accentexternalnumber->uitype = 7;
    $accentexternalnumber->column = $accentexternalnumber->name;
    $accentexternalnumber->columntype = 'INTEGER';
    $accentexternalnumber->typeofdata = 'NN~M~10,0';
    $blockInstance->addField($accentexternalnumber);
}

$accinputcode = Vtiger_Field::getInstance('accinputcode', $moduleInstance);

if (!$accinputcode) {
    $accinputcode = new Vtiger_Field();
    $accinputcode->name = 'accinputcode';
    $accinputcode->label = 'accinputcode';
    $accinputcode->table = $moduleInstance->basetable;
    $accinputcode->uitype = 7;
    $accinputcode->column = $accinputcode->name;
    $accinputcode->columntype = 'INTEGER';
    $accinputcode->typeofdata = 'NN~M~10,0';
    $blockInstance->addField($accinputcode);
}

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

$accdocumentnumber = Vtiger_Field::getInstance('accdocumentnumber', $moduleInstance);

if (!$accdocumentnumber) {
    $accdocumentnumber = new Vtiger_Field();
    $accdocumentnumber->name = 'accdocumentnumber';
    $accdocumentnumber->label = 'accdocumentnumber';
    $accdocumentnumber->table = $moduleInstance->basetable;
    $accdocumentnumber->uitype = 7;
    $accdocumentnumber->column = $accdocumentnumber->name;
    $accdocumentnumber->columntype = 'INTEGER';
    $accdocumentnumber->typeofdata = 'NN~O~20,0';
    $blockInstance->addField($accdocumentnumber);
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

$accbpscode = Vtiger_Field::getInstance('accbpscode', $moduleInstance);

if (!$accbpscode) {
    $accbpscode = new Vtiger_Field();
    $accbpscode->name = 'accbpscode';
    $accbpscode->label = 'accbpscode';
    $accbpscode->table = $moduleInstance->basetable;
    $accbpscode->uitype = 7;
    $accbpscode->column = $accbpscode->name;
    $accbpscode->columntype = 'INTEGER';
    $accbpscode->typeofdata = 'NN~O~10,0';
    $blockInstance->addField($accbpscode);
}

$accpersid = Vtiger_Field::getInstance('accpersid', $moduleInstance);

if (!$accpersid) {
    $accpersid = new Vtiger_Field();
    $accpersid->name = 'accpersid';
    $accpersid->label = 'accpersid';
    $accpersid->table = $moduleInstance->basetable;
    $accpersid->uitype = 7;
    $accpersid->column = $accpersid->name;
    $accpersid->columntype = 'INTEGER';
    $accpersid->typeofdata = 'NN~O~10,0';
    $blockInstance->addField($accpersid);
}

echo "<br>LISTO $MODULENAME";
