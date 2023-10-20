<?php

require_once 'libraries/lp_dyn_fields/LpDynFields.php';

global $adb, $log;

// Traducciones normales:

$languageStrings = array(
	
	'LPTempCamposDetalle' => 'Detalles de T. de C.',
	'SINGLE_LPTempCamposDetalle' => 'Detalle de T. de C.',
	
	'LBL_LPTempCamposDetalle_General' => 'General',
	'tcd_template' => 'Template',
	'tcd_campo' => 'Campo',
	'tcd_obligatorio' => 'Obligatorio',
	'tcd_orden' => 'Orden',

	'LBL_LPTempCamposDetalle_Auditoria' => 'Auditoría',

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