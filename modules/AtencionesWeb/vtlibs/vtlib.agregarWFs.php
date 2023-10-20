<?php

require_once('include/utils/utils.php');
require_once('modules/com_vtiger_workflow/VTEntityMethodManager.inc');
global $adb;

$emm = new VTEntityMethodManager($adb);

$modulename = "AtencionesWeb";
//Modulo, nombre WF, archivo donde esta la funcion, función a ejecutar
$wfs = array(
	array($modulename, "Se actualiza la fecha fin", "modules/".$modulename."/wf_funciones.php", "finalizada"),
	array($modulename, "Se actualiza el número de mails enviados", "modules/".$modulename."/wf_funciones.php", "cuentaACero")
);

$WFActuales = $emm->methodsForModule($modulename);

foreach($wfs as $wf)
	if(!in_array($wf[1], $WFActuales))
		$emm->addEntityMethod($wf[0], $wf[1], $wf[2], $wf[3]);

echo "Fin";