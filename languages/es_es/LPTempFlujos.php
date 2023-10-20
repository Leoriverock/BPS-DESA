<?php

$directory = 'languages/es_es';
$scanned_directory = array_diff(scandir($directory), 
	array(
		'.', 
		'..',
		'LPTempFlujos.php', 
		'LPTempFlujoCambios.php', 
		'LPTempCampos.php',
		'LPTempCamposDetalle.php',
		'LPTempCamposSeleccion.php'
	)
);

$allstrings = array();
$allstringsjs = array();
foreach ($scanned_directory as $key => $value){
	// ignorar modulos de template para evitar errores
	$parts = explode(".", $value);
	if (!is_dir($directory . DIRECTORY_SEPARATOR . $value) && $parts[count($parts) -1] == 'php' ){
		include_once($directory . DIRECTORY_SEPARATOR . $value);
		$allstrings = array_merge($allstrings, $languageStrings);
		$allstringsjs = array_merge($allstrings, $jsLanguageStrings);
	}
}

$languageStrings = array(
	'LPTempFlujos'                     => 'Plantillas de Flujo',
	'SINGLE_LPTempFlujos'              => 'Plantilla de Flujo',
	'LBL_ADD_RECORD'               => 'Añadir Plantilla de Flujo',
	'LBL_RECORDS_LIST'             => 'Lista de Plantillas de Flujo',
	'LBL_LPTEMPFLUJOS_INFORMATION'      => 'Detalle de Plantilla de Flujo',
	"tf_nombre" => "Nombre",
	"tf_modulo" => "Modulo",
	"tf_campo" => "Campo",
	"tf_valor" => "Valor",
	"tf_campo_mod" => "Campo a modificar",
);
$languageStrings = array_merge($allstrings, $languageStrings);

global $adb;

$notin = array('LPTempFlujos','LPTempFlujoCambios','LPTempCampos','LPTempCamposDetalle','LPTempCamposSeleccion');

foreach($adb->pquery("SELECT fieldname, fieldlabel, name 
	FROM vtiger_field f 
	JOIN vtiger_tab t
		ON f.tabid=t.tabid 
		AND name NOT IN(?,?,?,?,?)", $notin) as $f)
	$languageStrings[$f['fieldname']] = vtranslate($f['fieldlabel'], $f['name']); 

$jsLanguageStrings = array();
$jsLanguageStrings = array_merge($allstringsjs, $jsLanguageStrings);

