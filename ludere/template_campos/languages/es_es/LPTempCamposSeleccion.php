<?php

require_once 'libraries/lp_dyn_fields/LpDynFields.php';

global $adb, $log;

// Traducciones normales:

$languageStrings = array(

	'LPTempCamposSeleccion' => 'Seleccion de T. de C.',
	'SINGLE_LPTempCamposSeleccion' => 'Seleccion de T. de C.',

	'LBL_LPTempCamposSeleccion_General' => 'General',
	'ts_modulo' => 'Módulo',
	'ts_template' => 'Template',
	'ts_campo' => 'Campo',
	'ts_valor' => 'Valor',

	'LBL_LPTempCamposSeleccion_Auditoria' => 'Auditoría',

);

// Consultar todos los ID de los campos:

$resultado = $adb->pquery(
	
	"SELECT 
		fieldid, fieldlabel, name
	FROM
		vtiger_field AS f
			JOIN
		vtiger_tab AS t ON f.tabid = t.tabid"

	,

	array()

);

// Traducir el ID del campo como etiqueta:

foreach ($resultado as $r) {
	$languageStrings[$r['fieldid']] = LpDynFields::translate($r['fieldlabel'], $r['name']);
}