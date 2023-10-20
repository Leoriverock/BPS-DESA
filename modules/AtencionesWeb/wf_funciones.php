<?php
include_once 'include/utils/utils.php';

function finalizada($entity){
	global $adb;
	$idParts = explode('x', $entity->getId());
	$id = $idParts[1];
	date_default_timezone_set('America/Argentina/Buenos_Aires');
	$fecha = new DateTime();
	$sql = "UPDATE vtiger_atencionesweb SET aw_fechafin = ? WHERE atencioneswebid = ?";
	$adb->pquery($sql, array($fecha->format('Y-m-d H:i:s'), $id));
	$sql = "UPDATE vtiger_consultasweb SET cw_estado = 'Contestada' WHERE consultaswebid IN (SELECT relcrmid FROM vtiger_crmentityrel WHERE crmid = ? )";
	$adb->pquery($sql, array($id));
}

function cuentaACero($entity){
	require_once 'includes/LudereProLoader.php';
	require_once 'includes/Loader.php';
	$idParts = explode('x', $entity->getId());
	$id = $idParts[1];
	$record = Vtiger_Record_Model::getInstanceById($id, 'AtencionesWeb');
	$record->ContarMails();
}