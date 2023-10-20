<?php 

include_once("vtlib/Vtiger/Module.php");

$Vtiger_Utils_Log = true;

$moduleInstance = Vtiger_Module::getInstance("AtencionesWeb");


$aw_tema = Vtiger_Field::getInstance('aw_tema', $moduleInstance);
if ($aw_tema && $aw_tema->uitype != 10) {
	global $adb;
	$sql = "UPDATE vtiger_field SET uitype = 10 WHERE fieldid = ?";
	$adb->pquery($sql, array($aw_tema->id));
	$aw_tema->setRelatedmodules(array('Topics'));
}
