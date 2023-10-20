<?php
include_once 'include/utils/utils.php';

function finalizadaAP($entity){
	global $adb;
	$idParts = explode('x', $entity->getId());
	$id = $idParts[1];
	date_default_timezone_set('America/Argentina/Buenos_Aires');
	$fecha = new DateTime();
	$sql = "UPDATE vtiger_atencionpresencial SET ap_fechafin = ? WHERE atencionpresencialid = ?";
	$adb->pquery($sql, array($fecha->format('Y-m-d H:i:s'), $id));
}