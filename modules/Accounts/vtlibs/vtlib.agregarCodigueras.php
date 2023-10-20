<?php

include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');
include_once("modules/ModTracker/ModTracker.php");
include_once('include/utils/utils.php');
include_once('modules/com_vtiger_workflow/VTEntityMethodManager.inc');

global $adb, $site_URL;

$MODULENAME = 'Accounts';

$documentTypes = array(
    "DO" => "Documento",
    "PA" => "Pasaporte",
    "FR" => "Fronterizo"
);

$adb->pquery("CREATE TABLE IF NOT EXISTS lp_accdocumenttypes(
                id VARCHAR(5) NOT NULL PRIMARY KEY,
                value VARCHAR(50) NOT NULL 
            )", array());

foreach( $documentTypes as $key => $value ){
    $adb->pquery("INSERT lp_accdocumenttypes VALUES (?, ?)", array( $key, $value ));
}
$adb->pquery("CREATE TABLE IF NOT EXISTS lp_acccountries(
                id INT NOT NULL PRIMARY KEY,
                value VARCHAR(50) NOT NULL 
            )", array());

$xml = simplexml_load_file($site_URL . "modules/Accounts/vtlibs/PaisesBPS.xml");
$countries = array();
foreach ($xml->colPaises as $pais) {
    $countries[(int) $pais->codigoPais] = $pais->nombre;
}

foreach( $countries as $key => $value ){
    $adb->pquery("INSERT lp_acccountries VALUES (?, ?)", array( $key, $value ));
} 