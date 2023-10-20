<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class AtencionPresencial_Record_Model extends Vtiger_Record_Model {

	public static function getAtencion($numerows, $fecha, $sectorid){
        global $adb;
        $sql = "SELECT crmid FROM vtiger_atencionpresencial a INNER JOIN vtiger_crmentity e ON e.crmid = a.atencionpresencialid AND e.deleted = 0 WHERE a.ap_numerows = ? AND DATE(a.ap_fechacomienzo) = ? AND a.ap_sectorid = ?";
        $rs = $adb->pquery($sql, array($numerows, $fecha, $sectorid));
        if ($adb->num_rows($rs) > 0) {
            $id = $rs->fields[0];
            return self::getInstanceById($id, 'AtencionPresencial');
        }
        return null;
    }

    public static function createTabla(){
        global $adb;
        $sql = "CREATE TABLE IF NOT EXISTS lp_status_ap(
            numerocod INT(19),
            sectorid INT(19),
            fecha DATE,
            lugcod INT(19),
            `json` TEXT,
            `status` VARCHAR(50),
            userid INT(19),
            fechalog DATETIME DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (numerocod, sectorid, fecha, lugcod) 
        )";
        $adb->pquery($sql);
    }

    public static function statusLog($numerocod, $sectorid, $fecha, $lugcod, $json, $status = ''){
        global $adb, $current_user;
        $fecha_actual = date("Y-m-d H:i");
        self::createTabla();
        if(empty($status)){
            $sql = "SELECT * FROM lp_status_ap WHERE numerocod = ? AND sectorid = ? AND fecha = ? AND lugcod = ?";
            $rs = $adb->pquery($sql, array($numerocod, $sectorid, $fecha, $lugcod));
            if($adb->num_rows($rs) == 0){
                $sql = "INSERT INTO lp_status_ap VALUES(?,?,?,?,?,?,?,CURRENT_TIMESTAMP)";
                $adb->pquery($sql, array($numerocod, $sectorid, $fecha, $lugcod, $json, $status, $current_user->id));
            }
        }else{
            $sql = "UPDATE lp_status_ap SET status = ?, fechalog = ? WHERE numerocod = ? AND sectorid = ? AND fecha = ? AND lugcod = ?";
            $rs = $adb->pquery($sql, array($status,$fecha_actual, $numerocod, $sectorid, $fecha, $lugcod));
        }
    }

    public static function getStatusLog($numerocod, $sectorid, $fecha, $lugcod){
        global $adb;
        $sql = "SELECT status FROM lp_status_ap WHERE numerocod = ? AND sectorid = ? AND fecha = ? AND lugcod = ?";
        $rs = $adb->pquery($sql, array($numerocod, $sectorid, $fecha, $lugcod));
        if($adb->num_rows($rs) > 0){
            return $rs->fields['status'];
        }
        return null;
    }

    public static function getJSONStatusLog($numerocod, $sectorid, $fecha, $lugcod){
        global $adb;
        $sql = "SELECT json FROM lp_status_ap WHERE numerocod = ? AND sectorid = ? AND fecha = ? AND lugcod = ?";
        $rs = $adb->pquery($sql, array($numerocod, $sectorid, $fecha, $lugcod));
        if($adb->num_rows($rs) > 0){
            return $rs->fields['json'];
        }
        return null;
    }

    public static function getStatusLogByStatus($status = ''){
        global $adb, $current_user;
        $sql = "SELECT json FROM lp_status_ap WHERE userid = ? AND status = ?";
        $rs = $adb->pquery($sql, array($current_user->id, $status));
        if($adb->num_rows($rs) > 0){
            return json_decode($rs->fields[0]);
        }
        return null;
    }

}
