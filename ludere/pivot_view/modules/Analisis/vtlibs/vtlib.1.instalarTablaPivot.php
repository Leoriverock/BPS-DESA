<?php
/******
 * Instalador de Tabla Pivot para Vtiger 6.5
 * @author: Maximiliano Fernández
 * @date: 2016
 ****/

//// Importaciones
$Vtiger_Utils_Log = true;

include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');

//// Módulos donde se desea instalar la Tabla Pivot
$modulos = array('Accounts');

//// Verificar que el módulo Análisis esté instalado.
$analisis = Vtiger_Module::getInstance('Analisis');

if (!$analisis)
{
	echo "<h2>Instalando m&oacute;dulo An&aacute;lisis</h2>";

	// Crear el módulo y guardarlo
	$module = new Vtiger_Module();
	$module->name = 'Analisis';
	$module->save();
	$module->initWebservice();

	// Inicializar las tablas necesarias
	$module->initTables();

	// Agregarlo al menú
	$menu = Vtiger_Menu::getInstance('Analytics');
	$menu->addModule($module);

	echo "&iexcl;Listo!<hr>";
}

//// Instalar Tabla Pivot en los módulos seleccionados
foreach ($modulos as $modulo)
{
	echo "Procesando el m&oacute;dulo '$modulo'...<br>";

	$moduleInstance = Vtiger_Module::getInstance($modulo);

	if ($moduleInstance)
	{
		echo "&gt;&gt; ";
		$moduleInstance->addLink('LISTVIEW', 'Tabla Pivot', "javascript:window.location='index.php?module=".$modulo."&view=TablaPivot&viewname='+$('.list-menu-content li.active a').attr('href').split('viewname=').pop().split('&')[0]+'&finame='+$('.list-menu-content li.active').text().trim()");

		echo "<strong>Listo</strong><br>";
	} else
	{
		echo "&gt;&gt; <span style='color: red;'>No existe el m&oacute;dulo '$modulo'. Verificar.</span><br>";
	}

	agregarAction($moduleInstance->getId(),"TablaPivot");
}


function agregarAction($moduloTabId,$acceso){
	global $adb;
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