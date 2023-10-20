<?php 
include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');
include_once("modules/ModTracker/ModTracker.php");
include_once('include/utils/utils.php');
include_once('modules/com_vtiger_workflow/VTEntityMethodManager.inc');

$module = $_GET["module"];
$vtlib = $_GET["vtlib"];

if(!$module)
	exit("Falta especificar el parametro module en la url");

if(!$vtlib)
	exit("Falta especificar el parametro vtlib en la url");

$archivo = "modules/".$module."/vtlibs/".$vtlib;

if(!file_exists($archivo))
	exit("No se encuentra el archivo: ".$archivo);

require_once($archivo);

?>