<?php 

include_once("vtlib/Vtiger/Module.php");

$Vtiger_Utils_Log = true;

$moduleInstance = Vtiger_Module::getInstance("ConsultasWeb");


$cw_tema = Vtiger_Field::getInstance('cw_tema', $moduleInstance);
if ($cw_tema && $cw_tema->uitype != 10) {
	global $adb;
	$sql = "UPDATE vtiger_field SET uitype = 10 WHERE fieldid = ?";
	$adb->pquery($sql, array($cw_tema->id));
	$cw_tema->setRelatedmodules(array('Topics'));
}
