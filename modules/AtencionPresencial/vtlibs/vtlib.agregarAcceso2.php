<?php 

$Vtiger_Utils_Log = true;

include_once('vtlib/Vtiger/Module.php');
include_once('include/utils/CommonUtils.php');

/*$tools = array("herramienta1", "herramienta2");
$MODULENAME = "Nombre_Modulo";*/

$nombreModulo = 'AtencionPresencial';

$accesosA = array('btnVerAtencion');

$moduloTabId = $adb->query_result($adb->query("SELECT tabid FROM vtiger_tab WHERE name = '$nombreModulo'"), 0, 'tabid');

echo "$nombreModulo tab ID: $moduloTabId <br>";

$perfilesResult = $adb->query("SELECT profileid FROM vtiger_profile");

foreach ($accesosA as $acceso) {

	// Ver si el acceso esta defindo en el mapeo de acciones:

	$actionId = $adb->query_result($adb->query("SELECT actionid FROM vtiger_actionmapping WHERE actionname = '$acceso'"), 0, 'actionid');

    // Si no esta definido, se crea en la tabla de mapeos:

	if (!$actionId) {
		$actionId = $adb->query_result($adb->query('SELECT MAX(actionid) + 1 AS nuevoid FROM vtiger_actionmapping'), 0, 'nuevoid');
		$adb->query("INSERT INTO vtiger_actionmapping VALUES ($actionId, '$acceso', 0)");
	}

	echo "Acceso A: $acceso | ID: $actionId <br>";

	// Para cada perfil, agregar una tupla relacionada a esta accion:

	foreach ($perfilesResult as $perfil) {

        // (ver si ya existe, para no volver aguardar)

		$profileId = $perfil['profileid'];
		
		$estaGuardado = $adb->query(
            "SELECT 1 FROM vtiger_profile2utility WHERE profileid = $profileId 
            AND tabid = $moduloTabId AND activityid = $actionId"
        );
		
        if ($adb->num_rows($estaGuardado) == 0) { // NO EXISTE:

			echo "Se guarda por unica vez en la tabla vtiger_profile2utility <br>";
            
            $adb->query(
                "INSERT INTO vtiger_profile2utility 
                VALUES ($profileId, $moduloTabId, $actionId, 0)"
            );
		
        } else { // YA EXISTE:

			echo "No se guarda de vuelta en la tabla vtiger_profile2utility <br>";
        
        }
	
    }

}


?>