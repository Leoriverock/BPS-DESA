<?php

require_once 'config.ludere.php';
require_once 'integracion/ws.php';

class Calls_Module_Model extends Vtiger_Module_Model
{

    public static function getLlamadaActiva($userId = false)
    {
        global $adb, $log, $site_URL;
        $log->info("el userid que viene es: $userid");
        $userId = $userId ? $userId : Users_Record_Model::getCurrentUserModel()->getId();
        $log->info("el userid que queda es: $userid");
        /*$q      = $adb->pquery("SELECT callsid, callphonenumber, callaccount
                                FROM vtiger_calls
                                JOIN vtiger_crmentity ON crmid = callsid
                                WHERE deleted = 0 AND smownerid = ? AND callenddate IS NULL", array($userId));*/
        $q      = $adb->pquery("SELECT callsid, callphonenumber, callaccount
                                FROM vtiger_calls
                                JOIN vtiger_crmentity ON crmid = callsid
                                
                                JOIN vtiger_users2group ON userid = smownerid
                                JOIN vtiger_groups ON  vtiger_users2group.groupid = vtiger_groups.groupid
                                WHERE deleted = 0 AND smownerid = ? AND callenddate IS NULL
                                AND TYPE LIKE '%llamadas%'", array($userId));                        
        $log->debug("Filas: " . $adb->num_rows($q));
        $log->debug("Telefono: " . $adb->query_result($q, 0, 'callphonenumber'));
        return $adb->num_rows($q) > 0 ? [
            "callsid"         => $adb->query_result($q, 0, 'callsid'),
            "callurl"         => "index.php?module=Calls&view=Detail&record=" . $adb->query_result($q, 0, 'callsid') . "&app=SUPPORT",
            "callphonenumber" => $adb->query_result($q, 0, 'callphonenumber'),
            "callaccount"     => $adb->query_result($q, 0, 'callaccount'),
        ] : null;
    }

    public static function addCall($params)
    {
        global $log;
        $callid          = $params["callid"];
        $callphonenumber = $params["callphonenumber"];
        $documentTypeWS  = $params["calldocumenttype"];
        $documentNumber  = $params["calldocumentnumber"];
        $callcountryWS   = $params['calldocumentcountry'];
        $callwg          = $params['callwg'];
        $callpin         = $params['callpin'];
        $callmultiple    = !isset($params['callmultiple']) || empty($params['callmultiple']) ? false : $params['callmultiple'];

        // Mandatory fields

        if (!isset($callid) || empty(trim($callid))) {
            return array(
                "ok"    => false,
                "error" => "No se ha indicado el ID de la llamada",
                "message" => null
            );
        }

        if (!isset($callphonenumber) || empty(trim($callphonenumber))) {
            return array(
                "ok"    => false,
                "error" => "No se ha indicado número de teléfono",
                "message" => null
            );
        }

        if (!isset($params['calluser']) || empty(trim($params['calluser']))) {
            return array(
                "ok"    => false,
                "error" => "No se ha indicado el nombre del usuario que atendió",
                "message" => null
            );
        }

        // Max. Long. Controls

        if (strlen($callid) > 18) {
            return array(
                "ok"    => false,
                "error" => "El ID de la llamada debe tener una longitud máxima de 18 caracteres",
                "message" => null
            );
        }

        if (strlen($params['callstartdate']) > 10) {
            return array(
                "ok"    => false,
                "error" => "La fecha de inicio debe tener una longitud máxima de 10 caracteres",
                "message" => null
            );
        }

        if (strlen($params['callstarttime']) > 5) {
            return array(
                "ok"    => false,
                "error" => "La hora de inicio debe tener una longitud máxima de 5 caracteres",
                "message" => null
            );
        }

        if (strlen($callphonenumber) > 25) {
            return array(
                "ok"    => false,
                "error" => "El número de teléfono debe tener una longitud máxima de 25 caracteres",
                "message" => null
            );
        }

        if (strlen($documentTypeWS) > 2) {
            return array(
                "ok"    => false,
                "error" => "El código del tipo de documento debe tener una longitud máxima de 2 caracteres",
                "message" => null
            );
        }

        if (strlen($documentNumber) > 25) {
            return array(
                "ok"    => false,
                "error" => "El número de documento debe tener una longitud máxima de 25 caracteres",
                "message" => null
            );
        }

        if (strlen($params["calluser"]) > 100) {
            return array(
                "ok"    => false,
                "error" => "El nombre de usuario que atendió debe tener una longitud máxima de 100 caracteres",
                "message" => null
            );
        }

        if (strlen($callwg) > 100) {
            return array(
                "ok"    => false,
                "error" => "El WG debe tener una longitud máxima de 100 caracteres",
                "message" => null
            );
        }

        if (strlen($callpin) > 100) {
            return array(
                "ok"    => false,
                "error" => "El PIN debe tener una longitud máxima de 100 caracteres",
                "message" => null
            );
        }

        $calluser = self::findUser($params["calluser"]);

        if (!$calluser) {
            return array(
                "ok"    => false,
                "error" => "No se ha encontrado el usuario que atendió",
                "message" => null
            );
        }

        if (self::existsId($callid, $calluser)) {
            return array(
                "ok"    => false,
                "error" => "El ID de Llamada ya se encuentra en uso para el usuario indicado",
                "message" => null
            );
        }

        if (empty(trim($documentTypeWS))) {
            $documentType = "Documento";
        } else {
            $documentType = self::findDocumentType($documentTypeWS);
        }

        if (empty(trim($callcountryWS))) {
            $callcountry = "URUGUAY";
        } else {
            $callcountry = self::findCountry($callcountryWS);
        }

        if (!$documentType) {
            return array(
                "ok"    => false,
                "error" => "El código del tipo de documento es inválido",
                "message" => null
            );
        }

        if (!$callcountry) {
            return array(
                "ok"    => false,
                "error" => "El código del país es inválido",
                "message" => null
            );
        }

        $responseStartData = self::setStartData($params['callstartdate'], $params['callstarttime']);

        if (!$responseStartData['ok']) {
            return $responseStartData;
        }

        $callstartdate = $responseStartData['callstartdate'];
        $callstarttime = $responseStartData['callstarttime'];

        $callaccount = self::findAccount($documentType, $documentNumber, $callcountry);

        if (!$callaccount) {
            $callaccount = (!isset($documentNumber) || empty(trim($documentNumber))) ?
            USER_DEFAULT :
            self::searchAndCreateUserFromBPS($documentType, $documentTypeWS, $documentNumber, $callcountry, $callcountryWS);
        }

        $log->debug("___ calluser ___");
        $log->debug($calluser);

        self::controlPreviousCall($calluser);

        $id = self::registerCall($callid, $callphonenumber, $callstartdate, $callstarttime, $callaccount, $calluser, $callwg, $callpin, $callmultiple);

        return array(
            "ok"      => true,
            "error" => null,
            "message" => "Se ha creado la llamada correctamente (ID: $id)",
        );
    }

    public static function searchUser($params)
    {

        global $site_URL;

        $documentType   = $params["userdocumenttype"];
        $documentNumber = $params["userdocumentnumber"];
        $callcountry    = $params['userdocumentcountry'];

        $documentTypeWS = $params["userdocumenttype"];        
        $callcountryWS = $params['userdocumentcountry'];

        if (strlen($documentTypeWS) > 2) {
            return array(
                "ok"    => false,
                "error" => "El código del tipo de documento debe tener una longitud máxima de 2 caracteres",
                "message" => null
            );
        }

        if (strlen($documentNumber) > 25) {
            return array(
                "ok"    => false,
                "error" => "El número de documento debe tener una longitud máxima de 25 caracteres",
                "message" => null
            );
        }

        if (empty(trim($documentType))) {
            $documentType   = "Documento";
            $documentTypeWS = 'DO';
        } else {
            $documentType = self::findDocumentType($documentType);
        }

        if (empty(trim($callcountry))) {
            $callcountry   = "URUGUAY";
            $callcountryWS = 1;
        } else {
            $callcountry = self::findCountry($callcountry);
        }

        if (!$documentType) {
            return array(
                "ok"    => false,
                "error" => "El código del tipo de documento es inválido",
                "message" => null
            );
        }

        if (!$callcountry) {
            return array(
                "ok"    => false,
                "error" => "El código del país es inválido",
                "message" => null
            );
        }

        $calluser = (!isset($documentNumber) || empty(trim($documentNumber))) ? USER_DEFAULT : self::findAccount($documentType, $documentNumber, $callcountry);

        if (empty(trim($calluser))) {
            $calluser = self::searchAndCreateUserFromBPS($documentType, $documentTypeWS, $documentNumber, $callcountry, $callcountryWS);
        }

        return array(
            "ok"  => true,
            "error" => null,
            "url" => "{$site_URL}verusuario.php?record={$calluser}",
        );

    }

    public static function endCall($params)
    {

        $callid = $params["callid"];

        if (!isset($callid) || empty(trim($callid))) {
            return array(
                "ok"    => false,
                "error" => "No se ha indicado el ID de la llamada",
                "message" => null
            );
        }

        if (!isset($params['calluser']) || empty(trim($params['calluser']))) {
            return array(
                "ok" => false,
                "error" => "No se ha indicado el nombre del usuario que atendió",
                "message" => null
            );
        }

        if (strlen($callid) > 18) {
            return array(
                "ok" => false,
                "error" => "El ID de la llamada debe tener una longitud máxima de 18 caracteres",
                "message" => null
            );
        }

        if (strlen($params["calluser"]) > 100) {
            return array(
                "ok"    => false,
                "error" => "El nombre de usuario que atendió debe tener una longitud máxima de 100 caracteres",
                "message" => null
            );
        }

        $responseEndData = self::setEndData(null, null, true);

        $callenddate = $responseEndData['callenddate'];
        $callendtime = $responseEndData['callendtime'];

        $calluser = self::findUser($params['calluser']);

        if (!$calluser) {
            return array(
                "ok"    => false,
                "error" => "No se ha encontrado el usuario que atendió",
                "message" => null
            );
        }

        return self::setEndDateAndTime($callid, $calluser, $callenddate, $callendtime);
    }

    public static function searchTicketsAndComments($params)
    {
        $callid = $params["callid"];
        if (!isset($callid) || empty(trim($callid))) {
            return array(
                "ok"    => false,
                "error" => "No se ha indicado el ID de la llamada",
                "hasTickets" => false
            );
        }

        if (strlen($callid) > 18) {
            return array(
                "ok"    => false,
                "error" => "El ID de la llamada debe tener una longitud máxima de 18 caracteres",
                "hasTickets" => false
            );
        }

        if (!isset($params['calluser']) || empty(trim($params['calluser']))) {
            return array(
                "ok"    => false,
                "error" => "No se ha indicado el nombre del usuario que atendió",
                "hasTickets" => false
            );
        }

        if (strlen($params["calluser"]) > 100) {
            return array(
                "ok"    => false,
                "error" => "El nombre de usuario que atendió debe tener una longitud máxima de 100 caracteres",
                "hasTickets" => false
            );
        }

        $calluser = self::findUser($params["calluser"]);

        if (!$calluser) {
            return array(
                "ok"    => false,
                "error" => "No se ha encontrado el usuario que atendió",
                "hasTickets" => false
            );
        }

        $callsid = self::findCallsId($callid, $calluser);

        if (!$callsid) {
            return array(
                "ok"    => false,
                "error" => "El ID de llamada no existe para el usuario",
                "hasTickets" => false
            );
        }

        if (self::hasComments($callsid)) {
            return array(
                "ok"         => true,
                "hasTickets" => true,
                "error" => null
            );
        }

        if (self::hasTickets($callsid)) {
            return array(
                "ok"         => true,
                "hasTickets" => true,
                "error" => null
            );
        }

        return array(
            "ok"         => true,
            "hasTickets" => false,
            "error" => null
        );

    }

    private function searchAndCreateUserFromBPS($documentType, $documentTypeWS, $documentNumber, $callcountry, $callcountryWS)
    {
        $calluser = USER_DEFAULT;

        if( $documentType == 'Documento' ) $documentTypeWS = 'DO';
        if( $callcountry == 'URUGUAY' ) $callcountryWS = 1;

        $userFromBPS = getDatosPersona([
                                'acccountry' => $callcountryWS,
                                'accdocumenttype' => $documentTypeWS,
                                'accdocumentnumber' => $documentNumber
                            ]);
        
        if( !$userFromBPS['error'] ){
            $user = Vtiger_Record_Model::getCleanInstance("Accounts");
            $user->set('accountname', $userFromBPS['resultado']['Nombre1'] . " " . $userFromBPS['resultado']['Nombre2'] . " " . $userFromBPS['resultado']['Apellido1'] . " " . $userFromBPS['resultado']['Apellido2']);
            $user->set('accdocumenttype', $documentType);
            $user->set('accdocumentnumber', $documentNumber);
            $user->set('acccountry', $callcountry);
            $user->set('accpersid', $userFromBPS['resultado']['PersIdentificador'] );
            $user->set('mode', '');
            $activeAdminUser = Users::getActiveAdminUser();
            $user->set('assigned_user_id', $activeAdminUser->id);
            $user->save();
            $calluser = $user->getId();
        }

        return $calluser;
    }

    private function existsId($callid, $calluser)
    {
        global $adb;
        $sql = "SELECT callsid
                FROM vtiger_calls c
                INNER JOIN vtiger_crmentity crm
                ON c.callsid = crm.crmid
                WHERE crm.deleted = 0
                    AND c.callid = ?
                    AND crm.smownerid = ?
                    AND ISNULL(c.callenddate)";
        $result = $adb->pquery($sql, array($callid, $calluser));
        return $adb->num_rows($result) > 0;
    }

    
    private function setStartData($startDate, $startTime)
    {
        $callstartdate = !isset($startDate) || empty(trim($startDate)) ? date("Y-m-d") : $startDate;
        $callstarttime = !isset($startTime) || empty(trim($startTime)) ? date("H:i") : $startTime;

        if (!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $callstartdate)) {
            return array(
                "ok"    => false,
                "error" => "El formato de la fecha de inicio es incorrecto. Se espera YYYY-mm-dd o nula",
                "message" => null
            );
        }
        if (!preg_match("/^[0-9]{2}:[0-9]{2}$/", $callstarttime)) {
            return array(
                "ok"    => false,
                "error" => "El formato de la hora de inicio es incorrecto. Se espera HH:mm o nula",
                "message" => null
            );
        }

        return array(
            "ok"            => true,
            "callstartdate" => $callstartdate,
            "callstarttime" => $callstarttime,
        );
    }

    private function setEndData($endDate, $endTime, $endCallApi = false)
    {
        if (!empty($endDate) && !preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $endDate)) {
            return array(
                "ok"    => false,
                "error" => "El formato de la fecha de fin es incorrecto. Se espera YYYY-mm-dd o nula",
                "message" => null
            );
        }

        if (!empty($endTime) && !preg_match("/^[0-9]{2}:[0-9]{2}$/", $endTime)) {
            return array(
                "ok"    => false,
                "error" => "El formato de la hora de fin es incorrecto. Se espera HH:mm o nula",
                "message" => null
            );
        }

        if ($endCallApi) {
            $endDate = date("Y-m-d");
            $endTime = date("H:i");
        }

        return array(
            "ok"          => true,
            "callenddate" => $endDate,
            "callendtime" => $endTime,
        );
    }

    private function findDocumentType($id)
    {
        global $adb;

        $sql = "SELECT value
                FROM lp_accdocumenttypes
                WHERE id = ?";

        $result = $adb->pquery($sql, array($id));
        return $adb->num_rows($result) > 0 ? $adb->query_result($result, 0, 'value') : null;
    }

    private function findCountry($id)
    {
        global $adb;

        $sql = "SELECT value
                FROM lp_acccountries
                WHERE id = ?";

        $result = $adb->pquery($sql, array($id));
        return $adb->num_rows($result) > 0 ? $adb->query_result($result, 0, 'value') : null;
    }

    private function findAccount($documentType, $documentNumber, $country)
    {
        global $adb;

        $documentType = empty(trim($documentType)) ? "CI" : $documentType;
        $country      = empty(trim($country)) ? "Uruguay" : $country;

        $sql = "SELECT accountid
                FROM vtiger_account a
                INNER JOIN vtiger_crmentity c
                ON a.accountid = c.crmid
                WHERE accdocumenttype = ?
                    AND accdocumentnumber = ?
                    AND acccountry = ?
                    AND c.deleted = 0";
        $result = $adb->pquery($sql, array($documentType, $documentNumber, $country));
        return $adb->num_rows($result) > 0 ? $adb->query_result($result, 0, "accountid") : null;
    }

    private function findUser($username)
    {
        global $adb;

        if (empty(trim($username))) {
            $username = "admin";
        }

        $sql = "SELECT id
                FROM vtiger_users
                WHERE user_name = ?";
        $result = $adb->pquery($sql, array($username));
        return $adb->num_rows($result) > 0 ? $adb->query_result($result, 0, "id") : null;
    }

    private function findCallsId($callid, $calluser = null, $isNullControl = false)
    {
        global $adb;
        $sql = "SELECT callsid
                FROM vtiger_calls c
                INNER JOIN vtiger_crmentity crm
                ON c.callsid = crm.crmid
                WHERE c.callid = ?
                    AND crm.deleted = 0";

        if ($calluser) {
            $sql .= " AND crm.smownerid = ?";
        }

        if ($isNullControl) {
            $sql .= " AND ISNULL(c.callenddate)";
        }

        $params = $calluser ? array($callid, $calluser) : array($callid);

        $result = $adb->pquery($sql, $params);
        return $adb->num_rows($result) > 0 ? $adb->query_result($result, 0, "callsid") : null;
    }

    private function hasComments($callsid)
    {
        global $adb;

        $sql = "SELECT *
                FROM vtiger_modcomments m
                INNER JOIN vtiger_crmentity c
                ON m.modcommentsid = c.crmid
                WHERE m.related_to = ?
                    AND c.deleted = 0";

        $result = $adb->pquery($sql, array($callsid));
        return $adb->num_rows($result) > 0;
    }

    private function hasTickets($callsid)
    {
        global $adb;

        $sql = "SELECT *
                FROM vtiger_crmentityrel e
                WHERE ( e.crmid = ?
                    AND e.relmodule = 'HelpDesk'
                    AND ( SELECT c1.deleted FROM vtiger_crmentity c1 WHERE c1.crmid = e.relcrmid ) = 0 )
                    OR ( e.relcrmid = ?
                    AND e.module = 'HelpDesk'
                    AND ( SELECT c2.deleted FROM vtiger_crmentity c2 WHERE c2.crmid = e.crmid ) = 0)";

        $result = $adb->pquery($sql, array($callsid, $callsid));
        return $adb->num_rows($result) > 0;
    }

    private function controlPreviousCall($calluser)
    {
        global $adb;
        $sql = "SELECT callid
                FROM vtiger_calls c
                INNER JOIN vtiger_crmentity crm
                ON c.callsid = crm.crmid
                WHERE crm.smownerid = ?
                    AND crm.deleted = 0
                    AND ISNULL(c.callenddate)";

        $result = $adb->pquery($sql, array($calluser));

        if( $adb->num_rows($result) > 0 ){
            foreach( $result as $rs ){
                $endDate = date("Y-m-d");
                $endTime = date("H:i");

                self::setEndDateAndTime($rs['callid'], $calluser, $endDate, $endTime);
            }
        }
    }

    private function registerCall($callid, $callphonenumber, $callstartdate, $callstarttime, $callaccount, $calluser, $callwg, $callpin, $callmultiple)
    {
        $call = Vtiger_Record_Model::getCleanInstance("Calls");
        $call->set("callid", $callid);
        $call->set("callphonenumber", $callphonenumber);
        $call->set("callstartdate", $callstartdate);
        $call->set("callstarttime", $callstarttime);
        $call->set("callaccount", $callaccount);
        $call->set("calluser", $calluser);
        $call->set("callwg", $callwg);
        $call->set("callpin", $callpin);
        $call->set("callmultiple", $callmultiple);
        $call->set("assigned_user_id", $calluser);
        $call->set("mode", "");
        $call->save();
        return $call->getId();
    }

    private function setEndDateAndTime($callid, $calluser, $callenddate, $callendtime)
    {

        global $adb, $log;

        $callsid = self::findCallsId($callid, $calluser, true);

        if (!$callsid) {
            return array(
                "ok"    => false,
                "error" => "El ID de llamada no existe para el usuario",
                "message" => null
            );
        } else {
            $call = Vtiger_Record_Model::getInstanceById( $callsid, "Calls" );
            $call->set('callenddate', $callenddate);
            $call->set('callendtime', $callendtime);
            $call->set('mode', 'edit');
            $call->save();
            return array(
                "ok" => true,
                "message" => "Llamada finalizada con éxito",
                "error" => null
            );
        }
    }
}
