<?php

set_time_limit(0);
date_default_timezone_set('America/Argentina/Buenos_Aires');
error_reporting(E_ALL & ~E_STRICT & ~E_NOTICE & ~E_WARNING & ~E_DEPRECATED);
ini_set("display_errors", 1);

require_once('includes/runtime/Globals.php');
require_once('include/utils/utils.php');
require_once('include/database/PearDatabase.php');
require_once('includes/LudereProLoader.php');
require_once('includes/Loader.php');
require_once 'includes/runtime/LanguageHandler.php';

global $adb, $log, $default_timezone;
global $site_URL, $application_unique_key;
global $default_language;
global $current_language;
global $default_theme;
global $current_user;

if (!$current_user)
{
    $current_user = new Users();
    $current_user->id = 1;
    $current_user = $current_user->retrieve_entity_info($current_user->id, "Users");
}

$sql = "SELECT * FROM lp_asignadoa_ticket";
$rs = $adb->pquery($sql);
$sql = "DELETE FROM lp_asignadoa_ticket WHERE ticketid = ?";
echo "Iniciando <hr>";
foreach($rs as $fila){
	error_reporting(E_ALL & ~E_STRICT & ~E_NOTICE & ~E_WARNING & ~E_DEPRECATED);
	ini_set("display_errors", 1);
	$id = $fila['ticketid'];
	$groupId = $fila['assigned_user_id'];
	$usuario = $fila['userid'];

	echo "Datos ticket => $id || grupo => $groupId || usuario => $usuario <hr>";
	$current_user = new Users();
    $current_user = $current_user->retrieve_entity_info(intval($usuario), "Users");

    $recordModel = Vtiger_Record_Model::getInstanceById($id, 'HelpDesk');
    if($recordModel){
    	$grupoModel = Settings_Groups_Record_Model::getInstance($recordModel->get('ticketgrupo'));
    	if($grupoModel){
    		$recordModel->set('assigned_user_id', $grupoModel->getId());
		    $recordModel->set('mode', 'edit');
		    $recordModel->save();
		    $adb->pquery($sql, array($id));
    	}
    }
}
echo "Fin <br>";