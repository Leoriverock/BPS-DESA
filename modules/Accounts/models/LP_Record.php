<?php

require_once 'config.ludere.php';
require_once 'integracion/ws.php';
require_once 'include/utils/LP_utils.php';

class LudereProAccounts_Record_Model extends Accounts_Record_Model
{


    public function getLastsTickets($limit = 10)
    {
        global $adb, $site_URL, $log;
       
        $tickets = array();
        //Consulta optimizada -
        $sql = "SELECT tt.*, t.topicname, c.createdtime
                FROM vtiger_troubletickets tt
                INNER JOIN vtiger_crmentity c
                ON tt.ticketid = c.crmid
                INNER JOIN vtiger_topics t
                ON tt.tickettema = t.topicsid
                WHERE c.deleted = 0
                    AND (tt.parent_id = ?
                    OR tt.contact_id = ?)
                ORDER BY c.crmid DESC
                LIMIT ?";
       
       /* $sql = "SELECT tt.*, t.topicname, c.createdtime
                FROM (
                  SELECT tt.ticketid, tt.parent_id, tt.contact_id, tt.tickettema
                  FROM vtiger_troubletickets tt
                  WHERE tt.parent_id = ? OR tt.contact_id = ?
                  ORDER BY tt.ticketid DESC
                  LIMIT ?
                ) AS sub
                INNER JOIN vtiger_troubletickets tt ON sub.ticketid = tt.ticketid
                INNER JOIN vtiger_crmentity c ON tt.ticketid = c.crmid AND c.deleted = 0
                INNER JOIN vtiger_topics t ON tt.tickettema = t.topicsid
                ORDER BY c.createdtime DESC";*/

        //$idString = strval($this->getId());        
        //$log->info("mostrame el $idString");
        $result = $adb->pquery($sql, array($this->getId(),$this->getId(), $limit));

        foreach ($result as $rs) {
            $rs['status']   = vtranslate($rs['status'], "HelpDesk");
            $rs['priority'] = vtranslate($rs['priority'], "HelpDesk");
            $rs['url']      = $site_URL . "index.php?module=HelpDesk&record=" . $rs['ticketid'] . "&view=Detail&app=SUPPORT";
            $tickets[]      = $rs;
        }

        return $tickets;
    }

    public function getLastsAtencionesWeb($limit = 10)
    {
        global $adb, $site_URL, $log;

        $atencioneswebs = array();

        $sql = "SELECT * 
                FROM vtiger_atencionesweb a 
                INNER JOIN vtiger_crmentity e ON e.crmid = a.atencioneswebid AND e.deleted = 0 
                WHERE a.aw_persona = ?
                ORDER BY e.crmid DESC
                LIMIT ?";

        $result = $adb->pquery($sql, array($this->getId(), $limit));

        foreach ($result as $rs) {
            $rs['aw_categoria']   = vtranslate($rs['aw_categoria'], "AtencionesWeb");
            $rs['aw_estado'] = vtranslate($rs['aw_estado'], "AtencionesWeb");
            $rs['aw_tema'] = vtranslate($rs['aw_tema'], "AtencionesWeb");
            $rs['url']      = $site_URL . "index.php?module=AtencionesWeb&record=" . $rs['crmid'] . "&view=Detail&app=SUPPORT";
            $atencioneswebs[]      = $rs;
        }

        return $atencioneswebs;
    }

    /*
    El algoritmo solicitado es:
    (1) Consultar WS de BPS
    (2) Si se retorna informacion y ya existe el usuario en CRM actualizarlo, sino crearlo
     */
    public static function getInstanceBySearch($params) {
        global $log,$adb;
        if ( $params['acccontexternalnumber'] != '' ) {
            $tipoBusqueda = "contribuyente";
        } else if( $params['accempexternalnumber'] != '' ) {
            $tipoBusqueda = "empresa";
        } else {
            $tipoBusqueda = "persona";
        }

        // busca por el parametro recibido persona, empresa o contribuyente localmente 
        // para empresa y contrubuyente si no encuentra localmente por el valor dado consulta el nro interno para buscar si no extste previamnte en la base de datos creado por el otro metodo
        $userFromBPS = array();
        $nroIntContr = null;
        switch($tipoBusqueda) {
            case "contribuyente" :
                $params['acccontexternalnumber'] = str_pad($params['acccontexternalnumber'], 14, '0', STR_PAD_LEFT);
                $userFromBPS = getDatosContribuyente($params);
                if ($userFromBPS && !$userFromBPS['error']) {
                    $nroIntContr = $userFromBPS['resultado']['nroIntContr'];
                } 
                $recordModel = self::getLocalInstanceContribuyente($params);
            break;
            case "empresa" :
                //$params['accempexternalnumber'] = str_pad($params['accempexternalnumber'], 13, '0', STR_PAD_LEFT);
                //Verifico si es solo numero o contiene letra
                $valor = $params['accempexternalnumber'];
                if (ctype_digit($valor)) {
                    $params['accempexternalnumber'] = $params['accempexternalnumber'];
                } else {
                    $letras = preg_replace("/[^A-Za-z]/", "", $valor);
                    $numeros = preg_replace("/[^0-9]/", "", $valor);
                    $params['accempexternalnumber']=  $letras . str_pad($numeros, 12, "0", STR_PAD_LEFT);
                }


                $log->info($params['accempexternalnumber']);
                $userFromBPS = buscarEmpresa($params);
                $log->info("mostrame el userFromBPS");
                $log->info($userFromBPS);
                $log->info($userFromBPS['error']);
                if ($userFromBPS && !$userFromBPS['error']) {
                    //$nroIntContr = $userFromBPS['resultado']['contribuyentesRelacionados']['objContribuyente']['nroIntContr'];
                    $nroIntContr = $userFromBPS['resultado']['objContribuyente']['nroIntContr'];
                    if ($nroIntContr == ''){
                        $nroIntContr = $userFromBPS['resultado']['contribuyentesRelacionados']['objContribuyente']['nroIntContr'];
                    }
                    $log->info($nroIntContr);
                    $params['acccontexternalnumber'] = $userFromBPS['resultado']['objContribuyente']['nroContribuyente'];
                    $log->info($params);
                    $recordModel = self::getLocalInstanceBySearch($params);
                }
                
            break;
            case "persona" :
                $userFromBPS = getDatosPersona($params);
                $log->info("mostrame el userFromBPS");
                $log->info( $userFromBPS);
                $persid = $userFromBPS['resultado']['PersIdentificador'];
                $recordModel = self::getLocalInstancePersona($params,$persid);
            break;
        }
        // solo para emrpesa y contribuyente
        if (!$recordModel && $nroIntContr) {
            $recordModel = self::getInstanceByInternalNroCont($nroIntContr);
        }
        // si no existe el la cuenta se crea una nueva
        $log->info("mostrame el recordModel");
        $log->info($recordModel);
        if (!$recordModel) {
            $log->info("Creando");
            $recordModel = Vtiger_Record_Model::getCleanInstance('Accounts');
            $recordModel->set('mode', '');
        } else {
            $log->info("Editando");
            $recordModel->set('mode', 'edit');
        } 

        if ($userFromBPS && !$userFromBPS['error']) {
            $data = $userFromBPS['resultado'];
            $log->info("Mostrar data");
            $log->info($data);
            switch($tipoBusqueda) {
                case "contribuyente" :
                    //para evitar que venga otra data no deseada
                    $contribuyente = ltrim($data['nroContribuyente'],"0");
                    $recordModel->set('accountname', $data['denominacion']['denominacion']);
                    $recordModel->set('acccontinternalnumber', $data['nroIntContr']);
                    $recordModel->set('acccontexternalnumber',  $contribuyente);
                    $recordModel->set('accpersid', $data['PersIdentificador']);
                    $recordModel->set('accdocumenttype', 'Documento');
                break;  
                case "empresa" :
                    $denominacion_empresa = $data['empresa']['denominacion']['denominacion'];
                    if ($denominacion_empresa == ''){ $denominacion_empresa = $data['denominacion']['denominacion']; }

                    $log->info("denominacion_empresa ".$denominacion_empresa);

                    $denominacion = $data['objContribuyente']['denominacion']['denominacion'];
                    if ($denominacion == ''){ $denominacion = $data['denominacion']['denominacion']; }
                    if ($denominacion == ''){$denominacion = $data['contribuyentesRelacionados']['objContribuyente']['denominacion']['denominacion'];}

                    $log->info("denominacion ".$denominacion);

                    $nroContribuyente = $data['contribuyentesRelacionados']['objContribuyente']['nroContribuyente'];
                    if ($nroContribuyente == ''){ $nroContribuyente =  $data['objContribuyente']['nroContribuyente'];}

                    $log->info("nroContribuyente ".$nroContribuyente);

                    $nroIntContr = $data['contribuyentesRelacionados']['objContribuyente']['nroIntContr'];
                    if ($nroIntContr == ''){$nroIntContr = $data['objContribuyente']['nroIntContr']; }

                    $log->info("nroIntContr ".$nroIntContr);
                    //$nroEmpresa = $data['empresa']['nroEmpresa']; Ya no guarda estos datos
                    //$nroIntEmp = $data['empresa']['nroIntEmp'];

                    $recordModel->set('accountname', $denominacion);
                    $recordModel->set('accaux', $denominacion_empresa);
                    $recordModel->set('acccontinternalnumber', $nroIntContr);
                    $recordModel->set('acccontexternalnumber', $nroContribuyente);
                    //$recordModel->set('accempexternalnumber', $nroEmpresa);
                    //$recordModel->set('accempinternalnumber', $nroIntEmp);
                break;  
                case "persona" :
                    $codigosDocuments = getDocumentTypes();
                    $codigosCountries = getCountries();
                    $log->info("Pais");
                    $log->info($codigosCountries[$params['acccountry']]);
                    $recordModel->set('accountname', "{$data['Nombre1']} {$data['Nombre2']} {$data['Apellido1']} {$data['Apellido2']}");
                    $recordModel->set('accpersid', $data['PersIdentificador']);
                    $recordModel->set('accdocumenttype', $codigosDocuments[$params['accdocumenttype']]);
                    $recordModel->set('acccountry', $codigosCountries[$params['acccountry']]);
                    $recordModel->set('accdocumentnumber', $params['accdocumentnumber']);
                    break;
            }
            $recordModel->save();
            //$recordID  = $recordModel->getId();
            //Actualizo el tipo de busqueda en la cuenta
            //$update = 'UPDATE vtiger_account SET acctipobusqueda  = ? WHERE accountid = ?';
            //$res = $adb->pquery($update,array($tipoBusqueda,$recordID));
        } elseif (!$recordModel->get('mode')) {
            //TODO ver que pasa si da error el WS y no existe el usuario en el CRM, probablemente haya que redireccionar al crear
        }
        return $recordModel;
    }

    public static function getLocalInstanceBySearch($params)
    {
       /*if($params['accempexternalnumber'] != '') {
            return self::getLocalInstancePersona($params);
        }*/
        return $params['acccontexternalnumber'] != '' ? self::getLocalInstanceContribuyente($params) : self::getLocalInstancePersona($params,$persid='');
    }

    public static function getLocalInstancePersona($params,$persid)
    {
        global $adb,$log;
        $log->info("persid ".$persid);
        $codigos                   = getDocumentTypes();
        $params['accdocumenttype'] = $codigos[$params['accdocumenttype']];
        $q                         = $adb->pquery('SELECT crmid
                        FROM vtiger_account
                        JOIN vtiger_crmentity ON crmid = accountid
                        WHERE accdocumenttype = ? AND accdocumentnumber = ? AND acccountry = (SELECT value FROM lp_acccountries WHERE id = ?) AND deleted = 0
                        UNION
                         SELECT  crmid FROM  vtiger_account a
                       JOIN vtiger_crmentity c ON c.crmid = a.accountid
                       WHERE a.accpersid = ? AND a.accpersid != "" AND a.accpersid != 0 AND a.accpersid IS NOT NULL AND c.deleted = 0 LIMIT 1', array($params['accdocumenttype'], $params['accdocumentnumber'], $params['acccountry'],$persid));
        $log->info("la consulta en getLocalInstancePersona");
        $log->info($q);
        return $adb->num_rows($q) > 0 ? Vtiger_Record_Model::getInstanceById($adb->query_result($q, 0, 'crmid')) : false;
    }

    public static function getLocalInstanceContribuyente($params)
    {
        global $adb;
        $q = $adb->pquery('SELECT crmid
                        FROM vtiger_account
                        JOIN vtiger_crmentity ON crmid = accountid
                        WHERE acccontexternalnumber = ?
                        AND deleted = 0', array(intVal($params['acccontexternalnumber'])));
        return $adb->num_rows($q) > 0 ? Vtiger_Record_Model::getInstanceById($adb->query_result($q, 0, 'crmid')) : false;
    }

    public static function getLocalInstanceEmpresa($params)
    {
        global $adb;
        $q = $adb->pquery('SELECT crmid
                        FROM vtiger_account
                        JOIN vtiger_crmentity ON crmid = accountid
                        WHERE accempexternalnumber = ? AND deleted = 0', array($params['accempexternalnumber']));
        return $adb->num_rows($q) > 0 ? Vtiger_Record_Model::getInstanceById($adb->query_result($q, 0, 'crmid')) : false;
    }

    public static function getInstanceByInternalNroCont($acccontinternalnumber) {
        global $adb;
        $q = $adb->pquery('SELECT crmid
                        FROM vtiger_account
                        JOIN vtiger_crmentity ON crmid = accountid
                        WHERE acccontinternalnumber = ? AND deleted = 0', array($acccontinternalnumber));
        return $adb->num_rows($q) > 0 ? Vtiger_Record_Model::getInstanceById($adb->query_result($q, 0, 'crmid')) : false;
    }

    public static function getUsuarioForName($name){
        global $adb, $log;
        $log->info("estoy en getUsuarioForName ");
        $rs = getDatosUsuarioInt($name);
        if($rs['error']) return null;
        $persid = $rs['resultado']['PERS_IDENTIFICADOR'];
        $model = self::getLocalInstancePersona($params,$persid);
        if($model == false){
            $model = self::getCleanInstance('Accounts');
            $model->set('accountname', $rs['resultado']['DESCRIPCION']);
            $model->set('accpersid', $persid);
            $model->set('mode', 'create');
            $model->save();
        }
        return $model;
    }
}
