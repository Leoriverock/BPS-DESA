<?php

require_once 'include/utils/LP_utils.php';
require_once 'vtlib/Vtiger/Functions.php';
require_once 'config.ludere.php';
require_once 'modules/Calls/WF_Funciones.php';

function WF_Asociar_Ticket_Llamada($entity)
{
    global $log;
    $log->info('INICIO ' . __FUNCTION__);
    try {
        $recordId  = getSafeId($entity->getId());
        //$usuarioId = getSafeId($entity->get('assigned_user_id'));
        session_start(); // ... reanudar la sesión y obtenerlo:
        $usuarioId = $_SESSION['authenticated_user_id'];
        $log->info("estoy mostrando el id de usuario");
        $log->info($usuarioId);
        //buscar llamada activa para el usuario asignado al ticket (debería ser el usuario logueado). ⚠ no uso el current_user ya que no es tan fiable
        $llamada = Calls_Module_Model::getLlamadaActiva($usuarioId);
        if (!$llamada) {
            throw new Exception("No hay llamada activa para el usuario: $usuarioId");
        }
        $sourceModuleModel  = Vtiger_Module_Model::getInstance('HelpDesk');
        $relatedModuleModel = Vtiger_Module_Model::getInstance('Calls');
        $relationModel      = Vtiger_Relation_Model::getInstance($sourceModuleModel, $relatedModuleModel);
        $relationModel->addRelation($recordId, $llamada['callsid']);
    } catch (Exception $e) {
        $log->debug('❌ - Error ' . __FUNCTION__);
        $log->debug($e->getMessage());
    }
    $log->info('FIN ' . __FUNCTION__);
}
function WF_Asociar_Ticket_AtencionPresencial($entity)
{
    global $log,$current_user,$adb;
    $log->info('INICIO ' . __FUNCTION__);
    try {
        $recordId  = getSafeId($entity->getId());
        //$usuarioId = $current_user->id;
        //if (!$usuarioId) { // Si aún no hay ID de usuario actual...
        session_start(); // ... reanudar la sesión y obtenerlo:
        $usuarioId = $_SESSION['authenticated_user_id'];
        //} 
        $log->info("usuario- ".$usuarioId);
        //buscar llamada activa para el usuario asignado al ticket (debería ser el usuario logueado). ⚠ no uso el current_user ya que no es tan fiable
        $AtencionPresencial = AtencionPresencial_Module_Model::getAtencionpPActiva($usuarioId);
        if (!$AtencionPresencial) {
            throw new Exception("No hay llamada activa para el usuario: $usuarioId");
        }
        $sourceModuleModel  = Vtiger_Module_Model::getInstance('HelpDesk');
        $relatedModuleModel = Vtiger_Module_Model::getInstance('AtencionPresencial');
        $relationModel      = Vtiger_Relation_Model::getInstance($sourceModuleModel, $relatedModuleModel);
        $log->info("recordid $recordId ");
        $log->info($AtencionPresencial['atencionpresencialid']);
        $relationModel->addRelation($recordId, $AtencionPresencial['atencionpresencialid']);
    } catch (Exception $e) {
        $log->debug('❌ - Error ' . __FUNCTION__);
        $log->debug($e->getMessage());
    }
    $log->info('FIN ' . __FUNCTION__);
}

function WF_Enviar_Correo($entity)
{
    global $adb, $log, $HELPDESK_SUPPORT_NAME, $HELPDESK_SUPPORT_EMAIL_ID,$site_URL;
    $log->info('INICIO ' . __FUNCTION__);
    try {
        $id             = getSafeId($entity->getId());
        $assignedUserId = getSafeId($entity->get('assigned_user_id'));
        $elCC           = "";
        $elBCC          = "";
        $asunto         = $entity->get('ticket_no') . " - " . $entity->get('ticket_title');
        $sql            = "SELECT body FROM vtiger_emailtemplates WHERE templateid = ?";
        $result         = $adb->pquery($sql, array(EMAIL_TEMPLATE_ID));

        $sql_email    = "SELECT email1 FROM vtiger_users WHERE id = ?";
        $result_email = $adb->pquery($sql_email, array($assignedUserId));
        $email        = $adb->query_result($result_email, 0, 'email1');
        $emails = array();
        if (!empty($email)) {
            $emails[] = $email;
        } else {
            $sql_email_groups    = "SELECT email1 FROM vtiger_users WHERE id IN ( SELECT userid FROM vtiger_users2group WHERE groupid = ? )";
            $result_email_groups = $adb->pquery($sql_email_groups, array($assignedUserId));
            foreach ($result_email_groups as $reg) {
                $emails[] = $reg['email1'];
            }
        }
        //Obtengo el usuario
        $sql_usuario = 'SELECT CONCAT(accdocumentnumber," " ,accdocumenttype," " ,acccountry )  AS usuario 
                        FROM vtiger_account
                        INNER JOIN vtiger_troubletickets ON parent_id = accountid 
                        INNER JOIN vtiger_crmentity ON  crmid = accountid AND deleted = 0
                        WHERE ticketid = ?';
        $result_usuario = $adb->pquery($sql_usuario, array($id));
        $usuario        = $adb->query_result($result_usuario, 0, 'usuario');

        $cuerpo = $adb->query_result($result, 0, 'body');
        $log->info("el cuerpo es");
        $log->info($cuerpo);
        $linkToTicket = "{$site_URL}index.php?module=HelpDesk&view=Detail&record={$id}";
        $cuerpo = $cuerpo."<br>".$linkToTicket;
        $cuerpo = str_replace("Descripcion", "<b>Usuario: </b>" . $usuario . "<br>\r\nDescripcion:", $cuerpo);


        
        $comments_content = "<br><br><br><b>Comentarios:</b><br><br>";

        $sql_comments = "SELECT modcommentsid, commentcontent, c.createdtime, CONCAT(u.first_name, ' ', u.last_name) AS username
                    FROM vtiger_modcomments m
                     INNER JOIN vtiger_crmentity c
                    ON m.modcommentsid = c.crmid
                    INNER JOIN vtiger_users u
                    ON m.userid = u.id
                    WHERE m.related_to = ?
                    AND c.deleted = 0";

        $result_comments = $adb->pquery($sql_comments, array($id));
        $modcommentsid   = array();
        foreach ($result_comments as $comment) {
            $comments_content .= "<b>" . $comment['username'] . " (" . $comment['createdtime'] . ")</b><br>" . $comment['commentcontent'] . "<br><br>";
            $modcommentsid[] = $comment['modcommentsid'];
        }

        $cuerpo = str_replace("[Comentarios]", $comments_content, $cuerpo);
        $cuerpo = Vtiger_Functions::getMergedDescription($cuerpo, $id, "HelpDesk");
        foreach ($emails as $email) {
            send_mail("HelpDesk", $email, $HELPDESK_SUPPORT_NAME, $HELPDESK_SUPPORT_EMAIL_ID, $asunto, $cuerpo, $elCC, $elBCC, 'vinculados', $modcommentsid);
        }
    } catch (Exception $e) {
        $log->debug('❌ - Error ' . __FUNCTION__);
        $log->debug($e->getMessage());
    }
    $log->info('FIN ' . __FUNCTION__);
}

function WF_Cambiar_Asignado($entity)
{
    global $adb, $log;
    $log->info('INICIO ' . __FUNCTION__);
    try {
        $recordId  = getSafeId($entity->getId());
        $usuarioId = getSafeId($entity->get('modifiedby'));
        //$groups    = Users_Record_Model::getUserGroups($usuarioId);
        $recordModel = Vtiger_Record_Model::getInstanceById($recordId, 'HelpDesk');
        
        $grupoModel = Settings_Groups_Record_Model::getInstance($recordModel->get('ticketgrupo'));

        if (count($grupoModel) > 0) {
            //dado que puede pertenecer a más de un grupo seteo el del grupoactual
            $adb->pquery('UPDATE vtiger_crmentity SET smownerid = ? WHERE crmid = ?', array($grupoModel->getId(), $recordId));
        } else {
            //dado que el usuario no tiene grupos se asigna el ticket a él mismo
            $adb->pquery('UPDATE vtiger_crmentity SET smownerid = ? WHERE crmid = ?', array($usuarioId, $recordId));
        }
    } catch (Exception $e) {
        $log->debug('❌ - Error ' . __FUNCTION__);
        $log->debug($e->getMessage());
    }
    $log->info('FIN ' . __FUNCTION__);
}

function WF_Responder_Usuario($entity)
{
    global $adb, $log, $site_URL, $HELPDESK_SUPPORT_NAME, $HELPDESK_SUPPORT_EMAIL_ID;
    $log->info('INICIO ' . __FUNCTION__);
    try {
        $recordId    = getSafeId($entity->getId());
        $topicId     = getSafeId($entity->get('tickettema'));
        $parentId    = getSafeId($entity->get('parent_id'));
        $title       = $entity->get('ticket_title');
        $nroTicket   = $entity->get('ticket_no');
        $type        = $entity->get('ticketcategories');
        $description = $entity->get('description');
        $topicName   = Vtiger_Functions::getCRMRecordLabel($topicId);
        $userName    = Vtiger_Functions::getCRMRecordLabel($parentId);
        $ownerIdInfo = getRecordOwnerId($recordId);

        if (!empty($ownerIdInfo['Users'])) {
            $ownerId   = $ownerIdInfo['Users'];
            $ownerName = getOwnerName($ownerId);
            $toEmail   = getUserEmailId('id', $ownerId);
        }
        if (!empty($ownerIdInfo['Groups'])) {
            $ownerId   = $ownerIdInfo['Groups'];
            $groupInfo = getGroupName($ownerId);
            $ownerName = $groupInfo[0];
            $toEmail   = implode(',', getDefaultAssigneeEmailIds($ownerId));
        }

        $linkToTicket = "{$site_URL}index.php?module=HelpDesk&view=Detail&record={$recordId}";

        $subject = "Ticket :: $nroTicket :: Para Resolver";
        $body    = "Hola
                <br>
                Ticket: $nroTicket fue respondido por el área a la que se derivó
                <br>
                Asunto: $title
                <br>
                Usuario: $userName
                <br>
                Tema: $topicName
                <br>
                Tipo: $type
                <br>
                Descripción: $description
                <br>
                <br>
                Está pronto para RESPONDER AL USUARIO
                <br>
                <br>
                Para ir al ticket click acá <a href=\"$linkToTicket\">$linkToTicket</a>
                <br>
                Gracias
            ";

        $result = send_mail("HelpDesk", $toEmail, $HELPDESK_SUPPORT_NAME, $HELPDESK_SUPPORT_EMAIL_ID, $subject, $body);
        $log->debug($result == 1 ? "📧 ENVIADO CORRECTAMENTE ✔" : "❌ AL ENVIAR 📧");
    } catch (Exception $e) {
        $log->debug('❌ - Error ' . __FUNCTION__);
        $log->debug($e->getMessage());
    }
    $log->info('FIN ' . __FUNCTION__);
}

function WF_Cantidad_Llamadas($entity)
{
    global $adb, $log;
    $log->info('INICIO ' . __FUNCTION__);
    try {
        $recordId  = getSafeId($entity->getId());

        $sql = "SELECT COUNT(*) AS cantidad
                FROM vtiger_crmentityrel rel
                JOIN vtiger_crmentity crm ON crm.crmid = rel.relcrmid
                WHERE rel.crmid = ? AND relmodule = 'Calls' AND crm.deleted = 0";
        $result = $adb->pquery($sql, array($recordId));
        $cantidad = $adb->query_result($result, 0, 'cantidad');
        $sql_update = "UPDATE vtiger_troubletickets SET ticketcantillam = ? WHERE ticketid = ?";
        $adb->pquery($sql_update, array($cantidad, $recordId));
    } catch (Exception $e) {
        $log->debug('❌ - Error ' . __FUNCTION__);
        $log->debug($e->getMessage());
    }
    $log->info('FIN ' . __FUNCTION__);
}

function WF_Cantidad_Tickets($entity)
{
    global $adb, $log;
    $log->info('INICIO ' . __FUNCTION__);
    try {
        $recordId  = getSafeId($entity->getId());

        $sql = "SELECT relcrmid
                FROM vtiger_crmentityrel rel
                JOIN vtiger_crmentity crm ON crm.crmid = rel.relcrmid
                WHERE rel.crmid = ? AND relmodule = 'Calls' AND crm.deleted = 0";
        $result = $adb->pquery($sql, array($recordId));

        foreach ($result as $row) {
            $entityCall = VTEntityData::fromEntityId($adb, $row['relcrmid']);
            WF_Cantidad_Incidencias($entityCall);
        }
    } catch (Exception $e) {
        $log->debug('❌ - Error ' . __FUNCTION__);
        $log->debug($e->getMessage());
    }
    $log->info('FIN ' . __FUNCTION__);
}

function WF_Actualizar_Campos_Persona($entity)
{
    global $adb, $log;
    $log->info('INICIO ' . __FUNCTION__);
    try {
        $recordId  = getSafeId($entity->getId());
        $parentId = getSafeId($entity->get('parent_id'));
        if (isPerson($parentId)) {
            $sql = "UPDATE vtiger_troubletickets 
                    SET ticketcodigoaportacion = NULL, 
                        ticketnumeroexterno = NULL,
                        ticketdenominacion = NULL
                    WHERE ticketid = ?";
            $adb->pquery($sql, array($recordId));
        }
    } catch (Exception $e) {
        $log->debug('❌ - Error ' . __FUNCTION__);
        $log->debug($e->getMessage());
    }
    $log->info('FIN ' . __FUNCTION__);
}

function isPerson($recordId)
{
    global $adb;

    $sql = "SELECT acccontexternalnumber
            FROM vtiger_account
            WHERE accountid = ?";
    $result = $adb->pquery($sql, array($recordId));
    $acccontexternalnumber = $adb->query_result($result, 0, 'acccontexternalnumber');
    return (!isset($acccontexternalnumber) || empty($acccontexternalnumber) || $acccontexternalnumber == 0);
}

function WF_Igualar_Asignado_Al_Grupo($entity)
{
    global $adb, $log, $current_user;
    session_start(); // ... reanudar la sesión y obtenerlo:
    $userid = $_SESSION['authenticated_user_id'];
    $log->info("estoy mostrando el id de usuario");
    $log->info("usuario- ".$userid);
    $log->info('INICIO ' . __FUNCTION__);
    try {
        $recordId  = getSafeId($entity->getId());
        $result = $adb->pquery('SELECT groupid FROM vtiger_groups WHERE groupname = ?', array($entity->get('ticketgrupo')));
        $flag = $adb->num_rows($result);
        $log->info("valor: ".$flag);
        if ($adb->num_rows($result) > 0) {
            //$userid = $current_user->id;
            
            if($userid == 1) $userid = Users_Record_Model::getCurrentUserModel()->getId();
            $groupId = $adb->query_result($result, 0, 'groupid');
            $sql = "CREATE TABLE IF NOT EXISTS lp_asignadoa_ticket (
                ticketid INT(19) NOT NULL,
                assigned_user_id INT(19) NOT NULL,
                userid INT(19) NOT NULL
            );";
            $adb->pquery($sql);
            $sql = "INSERT INTO lp_asignadoa_ticket VALUES (?,?,?)";
            $adb->pquery($sql, array($recordId, $groupId, $userid));
            
            //$adb->pquery('UPDATE vtiger_crmentity SET smownerid = ? WHERE crmid = ?', array($groupId, $recordId));
            //Esto viene del cron
            $sql = "SELECT * FROM lp_asignadoa_ticket";
            $rs = $adb->pquery($sql);
            $sql = "DELETE FROM lp_asignadoa_ticket WHERE ticketid = ?";
            echo "Iniciando <hr>";
            foreach($rs as $fila){
                
                $groupId = $fila['assigned_user_id'];
                $usuario = $fila['userid'];

            
                $current_user = new Users();
                $current_user = $current_user->retrieve_entity_info(intval($usuario), "Users");

                $recordModel = Vtiger_Record_Model::getInstanceById($recordId, 'HelpDesk');
                if($recordModel){
                    $grupoModel = Settings_Groups_Record_Model::getInstance($recordModel->get('ticketgrupo'));
                    if($grupoModel){
                        $recordModel->set('ticketstatus', 'Closed');
                        $recordModel->set('assigned_user_id', $grupoModel->getId());
                        $recordModel->set('mode', 'edit');
                        $recordModel->save();
                        $adb->pquery($sql, array($recordId));
                    }
                }
            }
        } else {
            throw new Exception('No se encontró el grupo con el nombre: ' . $entity->get('ticketgrupo'));
        }
    } catch (Exception $e) {
        $log->debug('❌ - Error ' . __FUNCTION__);
        $log->debug($e->getMessage());
    }
    $log->info('FIN ' . __FUNCTION__);
}


function WF_Respuesta_Usuario($entity)
{
    global $adb, $log, $site_URL, $HELPDESK_SUPPORT_NAME, $HELPDESK_SUPPORT_EMAIL_ID;
    $log->info('INICIO ' . __FUNCTION__);
    try {
        $recordId    = getSafeId($entity->getId());
        $topicId     = getSafeId($entity->get('tickettema'));
        $parentId    = getSafeId($entity->get('parent_id'));
        $title       = $entity->get('ticket_title');
        $nroTicket   = $entity->get('ticket_no');
        $topicName   = Vtiger_Functions::getCRMRecordLabel($topicId);
        $userName    = Vtiger_Functions::getCRMRecordLabel($parentId);
        $ownerIdInfo = getRecordOwnerId($recordId);

        if (!empty($ownerIdInfo['Users'])) {
            $ownerId   = $ownerIdInfo['Users'];
            $ownerName = getOwnerName($ownerId);
            $toEmail   = getUserEmailId('id', $ownerId);
        }
        if (!empty($ownerIdInfo['Groups'])) {
            $ownerId   = $ownerIdInfo['Groups'];
            $groupInfo = getGroupName($ownerId);
            $ownerName = $groupInfo[0];
            $toEmail   = implode(',', getDefaultAssigneeEmailIds($ownerId));
        }

        $linkToTicket = "{$site_URL}index.php?module=HelpDesk&view=Detail&record={$recordId}";

        //$subject = "Ticket :: $nroTicket :: Respuesta recibida";
        //Modificado 11/08/23 LR - Solicitud #150788 redmine
        $subject = "Vtiger: respuesta $nroTicket";
        /*$body    = "Hola
                <br>
                <p>Asunto: $title</p>
                <br>
                <p>Ticket $nroTicket fue respondido por el usuario</p>
                <br>
                <br>
                <p>Para ir al ticket haz click aqui&#769; <a href=\"$linkToTicket\">$linkToTicket</a></p>
                <br>
                Gracias
            ";*/
        $body = "
            <br>
            <p>Vtiger: respuesta $nroTicket</p>
            <br>
            <p>Tienes una respuesta en el ticket $nroTicket </p>
            <br>
            <br>
            <p>Para acceder al ticket haz click aqui&#769; <a href=\"$linkToTicket\">$linkToTicket</a></p>
            <br>
        ";    

        $result = send_mail("HelpDesk", $toEmail, $HELPDESK_SUPPORT_NAME, $HELPDESK_SUPPORT_EMAIL_ID, $subject, $body);
        $log->debug($result == 1 ? "📧 ENVIADO CORRECTAMENTE ✔" : "❌ AL ENVIAR 📧");
    } catch (Exception $e) {
        $log->debug('❌ - Error ' . __FUNCTION__);
        $log->debug($e->getMessage());
    }
    $log->info('FIN ' . __FUNCTION__);
}

function WF_setDenominacionCreateTicket($entity)
{
    global $adb, $log;
    $log->info('INICIO ' . __FUNCTION__);
    try {
        $recordId  = getSafeId($entity->getId());
        session_start();
        $usuarioId = $_SESSION['authenticated_user_id'];
        $log->info("estoy mostrando el id de usuario");
        $log->info($usuarioId);
        $recordModel = Vtiger_Record_Model::getInstanceById($recordId, 'HelpDesk');
        
        $parent_id = $recordModel->get('parent_id');
        $sql = "SELECT accountname FROM `vtiger_account` WHERE accountid = ? ";
        $result = $adb->pquery($sql, array($parent_id));
        $denominacion = $adb->query_result($result, 0, 'accountname');
        $recordModel->set('ticketdenominacion', $denominacion);
        $recordModel->set('mode', 'edit');
        $recordModel->save();
        
    } catch (Exception $e) {
        $log->debug('❌ - Error ' . __FUNCTION__);
        $log->debug($e->getMessage());
    }
    $log->info('FIN ' . __FUNCTION__);
}