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
}