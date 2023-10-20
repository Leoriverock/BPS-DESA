<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/
require_once 'config.ludere.php';
class AtencionesWeb_Record_Model extends Vtiger_Record_Model {

	public function ContarMails($sumar = false){
		global $adb;
		$sql = "UPDATE vtiger_atencionesweb SET aw_mails = ? WHERE atencioneswebid = ?";
		$valor = 0;
		if($sumar){
			$valor = intval($this->get('aw_mails')) + 1;
		}
		$adb->pquery($sql, array($valor, $this->getId()));
	}

	public function esSupervisor(){
        global $adb, $log;
        $ConsultasWeb = Vtiger_Module_Model::getInstance('ConsultasWeb');
       
        $awid = $ConsultasWeb->getId();

        $currentUserId = Users_Record_Model::getCurrentUserModel()->get('id');
        
            $permiso = $adb->query_result($adb->query("SELECT pu.permission
                                        FROM vtiger_profile2utility pu
                                        INNER JOIN vtiger_role2profile rp ON pu.profileid = rp.profileid
                                        INNER JOIN vtiger_user2role ur ON ur.roleid = rp.roleid
                                        INNER JOIN vtiger_actionmapping am ON am.actionid = pu.activityid
                                        WHERE ur.userid = $currentUserId AND pu.tabid = $awid AND am.actionname = 'Asignar'"), 0, 
                                            'permission');
            if ($permiso == 0) {
                return true;
            } else {
                return false;
        	}
    }

    public function esAgente(){
        global $log,$adb,$current_user;
		$log->info("esta entrando a esAgente");

		$usuario_logueado = $current_user->id;
       
        $sql = "SELECT roleid FROM vtiger_user2role WHERE userid = ?";
		
		$result = $adb->pquery($sql, array($usuario_logueado));
		$roleid = $adb->query_result($result, 0, 'roleid');	

		$permiso = false;

		$consultasweb_roles = unserialize(LISTVIEW_CONSULTASWEB_ROLES);
		
		$existe = in_array($roleid,$consultasweb_roles);
        $log->info("existe la cosa esta".$existe);

		if($existe){
			$log->info("entra al if de ");
			$permiso = true;
		}
        $log->info("permiso es ".$permiso);
        return $permiso;

    }
}
