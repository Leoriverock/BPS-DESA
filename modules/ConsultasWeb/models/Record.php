<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class ConsultasWeb_Record_Model extends Vtiger_Record_Model
{

    
    public function tieneAcceso()
    {
        global $log, $adb;

        $ConsultasWeb = Vtiger_Module_Model::getInstance('ConsultasWeb');
        $cwid = $ConsultasWeb->getId();
        $log->info("Estoy en ConsultasWeb_Record_Model");
        $log->info($cwid);
        $currentUserId = Users_Record_Model::getCurrentUserModel()->get('id');
        //$widgets =  array('ausencias','telegram');
        //for ($i=0; $i<count($widgets); $i++){
            $permiso = $adb->query_result($adb->query("SELECT pu.permission
                                        FROM vtiger_profile2utility pu
                                        INNER JOIN vtiger_role2profile rp ON pu.profileid = rp.profileid
                                        INNER JOIN vtiger_user2role ur ON ur.roleid = rp.roleid
                                        INNER JOIN vtiger_actionmapping am ON am.actionid = pu.activityid
                                        WHERE ur.userid = $currentUserId AND pu.tabid = $cwid AND am.actionname = 'Asignar'"), 0, 
                                            'permission');
        //$resultado[$widgets[$i]] = $this->tienePermiso($permiso);
        //}
         if ($permiso == 0) {
            return true;
        } else {
            return false;
        }


       /*
        $currentUserId = Users_Record_Model::getCurrentUserModel()->get('id');
        $log->info("Tiene acceso");
        $sql = "SELECT 1 FROM vtiger_user2role WHERE userid = ? AND roleid = ?";
        $result = $adb->pquery($sql,array($currentUserId ,'H6'));//Si es supervisor de mails
        $acceso = $adb->num_rows($result);
        if ($acceso > 0) {
            return true;
        } else {
            return false;
        }*/

        
    }

    public function perteneceGrupo($group){
        $CURRENT_USER_MODEL = Users_Record_Model::getCurrentUserModel();
        $grupos = $CURRENT_USER_MODEL->getGroupsPreferencesOption();
        $return = false;
        foreach($grupos as $g){
            if($g->label == $group){
                $return = true;
                break;
            }
        }
        return $return; 
    }
   

}