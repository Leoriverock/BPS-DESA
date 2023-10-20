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
	'LPTempFlujoCambios'                     => 'Estados de Flujo',
	'SINGLE_LPTempFlujoCambios'              => 'Estado de Flujo',
	'LBL_ADD_RECORD'               => 'Añadir Estado de Flujo',
	'LBL_RECORDS_LIST'             => 'Lista de Estados de Flujo',
	'LBL_LPTEMPFLUJOCAMBIOS_INFORMATION'      => 'Detalle de Estado de Flujo',
	'tfc_template' => 'Plantilla de Flujo',
	'tfc_origen' => 'Origen',
	'tfc_destino' => 'Destino',
	'tfc_etiqueta' => 'Etiqueta',
	'tfc_color' => 'Color',
	'tfc_comentario' => 'Requiere comentario',
);
$languageStrings = array_merge($allstrings, $languageStrings);

$jsLanguageStrings = array();
$jsLanguageStrings = array_merge($allstringsjs, $jsLanguageStrings);

