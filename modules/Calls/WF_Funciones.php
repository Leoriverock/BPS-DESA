<?php

require_once 'include/utils/LP_utils.php';

function WF_Cantidad_Incidencias($entity)
{
    global $adb, $log;
    $log->info('INICIO ' . __FUNCTION__);
    try {
        $recordId  = getSafeId($entity->getId());
        
        $sql = "SELECT COUNT(*) AS cantidad
                FROM vtiger_crmentityrel rel
                JOIN vtiger_crmentity crm ON crm.crmid = rel.crmid
                WHERE rel.relcrmid = ? AND rel.module = 'HelpDesk' AND crm.deleted = 0";
        $result = $adb->pquery( $sql, array( $recordId ) );
        $cantidad = $adb->query_result( $result, 0, 'cantidad' );
        $sql_update = "UPDATE vtiger_calls SET callcantinc = ? WHERE callsid = ?";
        $adb->pquery( $sql_update, array( $cantidad, $recordId ) );        
    } catch (Exception $e) {
        $log->debug('❌ - Error ' . __FUNCTION__);
        $log->debug($e->getMessage());
    }
    $log->info('FIN ' . __FUNCTION__);
}

function WF_Cantidad_Comentarios($entity)
{
    global $adb, $log;
    $log->info('INICIO ' . __FUNCTION__);
    try {
        $recordId  = getSafeId($entity->getId());
        
        $sql = "SELECT COUNT(*) AS cantidad
                FROM vtiger_modcomments m
                INNER JOIN vtiger_crmentity c
                ON m.modcommentsid = c.crmid
                WHERE m.callsid = ?
                    AND c.deleted = 0";
        $result = $adb->pquery( $sql, array( $recordId ) );
        $cantidad = $adb->query_result( $result, 0, 'cantidad' );
        $sql_update = "UPDATE vtiger_calls SET callcantcom = ? WHERE callsid = ?";
        $adb->pquery( $sql_update, array( $cantidad, $recordId ) );        
    } catch (Exception $e) {
        $log->debug('❌ - Error ' . __FUNCTION__);
        $log->debug($e->getMessage());
    }
    $log->info('FIN ' . __FUNCTION__);
}
