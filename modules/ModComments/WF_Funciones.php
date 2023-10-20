<?php

require_once 'include/utils/LP_utils.php';
require_once 'vtlib/Vtiger/Functions.php';

function WF_Asociar_Comment_Llamada($entity)
{
    global $adb, $log;
    $log->info('INICIO ' . __FUNCTION__);
    try {
        $recordId  = getSafeId($entity->getId());
        $usuarioId = getSafeId($entity->get('assigned_user_id'));
        $relatedId = getSafeId($entity->get('related_to'));
        //buscar llamada activa para el usuario asignado al ticket (debería ser el usuario logueado). ⚠ no uso el current_user ya que no es tan fiable
        $llamada = Calls_Module_Model::getLlamadaActiva($usuarioId);
        $callsid = null;
        $relWith = "Calls";
        if (!$llamada) {
            $log->info("No hay llamada activa ya tu sabes");
            $currentUserModel = Users_Record_Model::getInstanceById($usuarioId, "Users");
            $callsid = $currentUserModel->getAtencionActiva();
            $relWith = "AtencionesWeb";
            if (!$callsid){
                $log->info("No hay atencion web $callsid");

                //Si no hay atencion web activa
                $callsid = $currentUserModel->getAtencionPActiva();
                $relWith = "AtencionPresencial";
                if (!$callsid){
                    $log->info("No hay atencion presencial $callsid");
                    throw new Exception("No hay llamada activa para el usuario: $usuarioId");
                }else{
                    $log->info("No hay nada $callsid");
                    $relWith = "AtencionPresencial";
                }
            }

           $log->info("mostrameeee"); 
           $log->info($callsid);
           $log->info($relWith);
        }
        else {
          
            $callsid = $llamada['callsid'];
            
            
        }

        $adb->pquery('UPDATE vtiger_modcomments SET callsid = ? WHERE modcommentsid = ?', array($callsid , $recordId));
        //busco si el comentario se esta creando sobre un ticket para que si no existe la relacion ticket-llamada entonces crearla
        if (Vtiger_Functions::getCRMRecordType($relatedId) === 'HelpDesk') {
            $q = $adb->pquery('SELECT 1 FROM vtiger_crmentityrel WHERE crmid = ? AND relcrmid = ?', array($relatedId, $callsid));
            if ($adb->num_rows($q) === 0) {
                $adb->pquery('INSERT INTO vtiger_crmentityrel VALUES (?,?,?,?)', array($relatedId, 'HelpDesk', $callsid, $relWith));
            }
        }
    } catch (Exception $e) {
        $log->fatal('❌ - Error ' . __FUNCTION__);
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
        
        $sql_record = "SELECT related_to, callsid FROM vtiger_modcomments WHERE modcommentsid = ?";
        $result_record = $adb->pquery( $sql_record, array( $recordId ) );
        $relatedId = $adb->query_result( $result_record, 0, 'related_to' );
        $callsId = $adb->query_result( $result_record, 0, 'callsid' );

        $sql = "SELECT setype FROM vtiger_crmentity WHERE crmid = ?";
        $result = $adb->pquery( $sql, array( $relatedId ) );
        $module = $adb->query_result( $result, 0, 'setype' );
        if( $module == 'Calls' ){
            $sql_comments = "SELECT COUNT(*) AS cantidad
                                FROM vtiger_modcomments m
                                INNER JOIN vtiger_crmentity c
                                ON m.modcommentsid = c.crmid
                                WHERE m.callsid = ?
                                    AND c.deleted = 0";
            $result_comments = $adb->pquery( $sql_comments, array( $relatedId ) );
            $cantidad = $adb->query_result( $result_comments, 0, 'cantidad' );
            $sql_update = "UPDATE vtiger_calls SET callcantcom = ? WHERE callsid = ?";
            $adb->pquery( $sql_update, array( $cantidad, $relatedId ) );        
        }
        else if( $module == 'HelpDesk' ){
            WF_Cantidad_Llamadas( $relatedId );
            WF_Cantidad_Comentarios_En_Llamada( $callsId );
            WF_Cantidad_Incidencias_En_Llamada( $callsId );
        }

    } catch (Exception $e) {
        $log->fatal('❌ - Error ' . __FUNCTION__);
        $log->debug($e->getMessage());
    }
    $log->info('FIN ' . __FUNCTION__);
}

function WF_Cantidad_Llamadas($recordId)
{
    global $adb, $log;
    $log->info('INICIO ' . __FUNCTION__);
    try {
        $sql = "SELECT COUNT(*) AS cantidad
                FROM vtiger_crmentityrel rel
                JOIN vtiger_crmentity crm ON crm.crmid = rel.relcrmid
                WHERE rel.crmid = ? AND relmodule = 'Calls' AND crm.deleted = 0";
        $result = $adb->pquery( $sql, array( $recordId ) );
        $cantidad = $adb->query_result( $result, 0, 'cantidad' );
        $sql_update = "UPDATE vtiger_troubletickets SET ticketcantillam = ? WHERE ticketid = ?";
        $adb->pquery( $sql_update, array( $cantidad, $recordId ) );        
    } catch (Exception $e) {
        $log->fatal('❌ - Error ' . __FUNCTION__);
        $log->debug($e->getMessage());
    }
    $log->info('FIN ' . __FUNCTION__);
}

function WF_Cantidad_Incidencias_En_Llamada($recordId)
{
    global $adb, $log;
    $log->info('INICIO ' . __FUNCTION__);
    try {
        
        $sql = "SELECT COUNT(*) AS cantidad
                FROM vtiger_crmentityrel rel
                JOIN vtiger_crmentity crm ON crm.crmid = rel.crmid
                WHERE rel.relcrmid = ? AND rel.module = 'HelpDesk' AND crm.deleted = 0";
        $result = $adb->pquery( $sql, array( $recordId ) );
        $cantidad = $adb->query_result( $result, 0, 'cantidad' );
        $sql_update = "UPDATE vtiger_calls SET callcantinc = ? WHERE callsid = ?";
        $adb->pquery( $sql_update, array( $cantidad, $recordId ) );        
    } catch (Exception $e) {
        $log->fatal('❌ - Error ' . __FUNCTION__);
        $log->debug($e->getMessage());
    }
    $log->info('FIN ' . __FUNCTION__);
}

function WF_Cantidad_Comentarios_En_Llamada($recordId)
{
    global $adb, $log;
    $log->info('INICIO ' . __FUNCTION__);
    try {
        
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
        $log->fatal('❌ - Error ' . __FUNCTION__);
        $log->debug($e->getMessage());
    }
    $log->info('FIN ' . __FUNCTION__);
}
