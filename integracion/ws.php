<?php

require_once 'config.ludere.php';
require_once 'libraries/nusoap/nusoap.php';

function getDatosPersona($params)
{
    global $adb, $log;
    $log->info(__FUNCTION__);
    $log->debug($params);
    //$log->debug(extension_loaded('soap') ? 'SOAP OK' : 'SOAP MISSING');
    $start     = microtime(true);
    $operacion = 'ObtenerListaPersonaPorDocumento';
    $servicio  = URL_WS_PERSONAS;
    $client    = new soapclient2($servicio);
    if (!array_key_exists('user', $params)) {
        //parece buena idea enviar algo si bien no sería obligatorio
        $params['user'] = 'crm';
    }
    $xml = "<soapenv:Envelope xmlns:soapenv=\"http://schemas.xmlsoap.org/soap/envelope/\" xmlns:wsp=\"http://bps.gub.uy/RegistrosCorporativos/Persona/WSPersona\">
            <soapenv:Header></soapenv:Header>
            <soapenv:Body>
                <wsp:ObtenerListaPersonaPorDocumento>
                    <wsp:ParamObtenerListaPersonaPorDocumento>
                        <wsp:ColDocumentos>
                            <wsp:ObtenerPersonaPorDocumento>
                                <wsp:CodPaisEmisor>${params['acccountry']}</wsp:CodPaisEmisor>
                                <wsp:CodTipoDocumento>${params['accdocumenttype']}</wsp:CodTipoDocumento>
                                <wsp:NroDocumento>${params['accdocumentnumber']}</wsp:NroDocumento>
                            </wsp:ObtenerPersonaPorDocumento>
                        </wsp:ColDocumentos>
                        <wsp:UsuarioActual>${params['user']}</wsp:UsuarioActual>
                    </wsp:ParamObtenerListaPersonaPorDocumento>
                </wsp:ObtenerListaPersonaPorDocumento>
            </soapenv:Body>
        </soapenv:Envelope>";
    
    $client->setHTTPEncoding();
    $result   = $client->send($xml, 'http://bps.gub.uy/RegistrosCorporativos/Persona/WSPersona/ObtenerListaPersonaPorDocumento', 30);
    $response = array();

    if (!$result || $client->fault || array_key_exists('faultcode', $result)) {
        $response["error"]     = true;
        $response["resultado"] = $client->fault ? $result["faultstring"] : 'Error';
    } else {
        $error = $client->getError();
        if ($error) {
            $response["error"]     = true;
            $response["resultado"] = $error;
        } else {
            if ($result['ResultObtenerListaPersonaPorDocumento']['ColResultados']['ResultObtenerPersonaPorDocumento']['ColErrorNegocio']) {
                $response["error"]     = true;
                $response["resultado"] = $result['ResultObtenerListaPersonaPorDocumento']['ColResultados']['ResultObtenerPersonaPorDocumento']['ColErrorNegocio']['ErrorNegocio']['Descripcion'];
            } else {
                $response["error"]     = false;
                $response["resultado"] = $result['ResultObtenerListaPersonaPorDocumento']['ColResultados']['ResultObtenerPersonaPorDocumento']['Persona'];
            }
        }
    }

    $time = microtime(true) - $start;
    $log->info(__FUNCTION__ . ' tardó: ' . $time . ' segundos');
    if (LOGS_WS_ENABLED) {
        $adb->pquery("INSERT INTO logs_ws_out (operation, request, response, reqdate, reqtime) VALUES (?,?,?,?,?)", array($operacion, $client->request, $client->response, date("Y-m-d h:i:s"), $time));
    }
    return $response;
}

function getDatosContribuyente($params)
{
    global $adb, $log;
    $log->info(__FUNCTION__);
    $log->debug($params);
    //$log->debug(extension_loaded('soap') ? 'SOAP OK' : 'SOAP MISSING');
    $start     = microtime(true);
    $operacion = 'obtListaEmpContrPorExternos';
    $servicio  = URL_WS_CONTRIBUYENTES;
    $client    = new soapclient2($servicio);
    $client->setHTTPEncoding();

    $xml = "<soapenv:Envelope xmlns:soapenv=\"http://schemas.xmlsoap.org/soap/envelope/\" xmlns:v002=\"http://bps.gub.uy/atyr/registro/utilitarios/v002\">
               <soapenv:Header/>
               <soapenv:Body>
                  <v002:obtListaEmpContrPorExternos>
                     <!--Optional:-->
                     <paramObtListaEmpContrPorExternos>
                        <!--Zero or more repetitions:-->
                        <colParamObtEmpContrPorExternos>
                           <!--Optional:-->
                           <!--claveContribuyente></claveContribuyente-->
                           <!--Optional:-->
                           <nroContribuyente>${params['acccontexternalnumber']}</nroContribuyente>
                           <!--Optional:-->
                           <!--nroEmpresa></nroEmpresa-->
                        </colParamObtEmpContrPorExternos>
                     </paramObtListaEmpContrPorExternos>
                  </v002:obtListaEmpContrPorExternos>
               </soapenv:Body>
            </soapenv:Envelope>";

    $result   = $client->send($xml, '', 30);
    $response = array();

    if (!$result || $client->fault || array_key_exists('faultcode', $result)) {
        $response["error"]     = true;
        $response["resultado"] = $client->fault ? $result["faultstring"] : 'Error';
    } else {
        $error = $client->getError();
        if ($error) {
            $response["error"]     = true;
            $response["resultado"] = $error;
        } else {
            if ($result['resultObtListaEmpContrPorExternos']['colResultObtEmpContrPorExternos']['colError']) {
                $response["error"]     = true;
                $response["resultado"] = $result['resultObtListaEmpContrPorExternos']['colResultObtEmpContrPorExternos']['colError']['errorDescription'];
            } else {
                $response["error"]     = false;
                $response["resultado"] = $result['resultObtListaEmpContrPorExternos']['colResultObtEmpContrPorExternos']['colObjContribuyente'];
            }
        }
    }

    $time = microtime(true) - $start;
    $log->info(__FUNCTION__ . ' tardó: ' . $time . ' segundos');
    if (LOGS_WS_ENABLED) {
        $adb->pquery("INSERT INTO logs_ws_out (operation, request, response, reqdate, reqtime) VALUES (?,?,?,?,?)", array($operacion, $client->request, $client->response, date("Y-m-d h:i:s"), $time));
    }
    return $response;
}

function buscarEmpresa($params)
{
    global $adb, $log;
    $log->info(__FUNCTION__);
    $log->debug($params);
    //$log->debug(extension_loaded('soap') ? 'SOAP OK' : 'SOAP MISSING');
    $start     = microtime(true);
    $operacion = 'obtenerListaRelContribuyente';
    $servicio  = URL_WS_BUSQUEDA_EMPRESAS;
    
    $client    = new soapclient2($servicio);
    $client->setHTTPEncoding();

    $xml = "<soapenv:Envelope xmlns:soapenv=\"http://schemas.xmlsoap.org/soap/envelope/\" xmlns:v003=\"http://bps.gub.uy/atyr/registro/empresas/v003\">
    <soapenv:Header/>
    <soapenv:Body>
       <v003:obtenerListaRelContribuyente>
          <!--Optional:-->
          <paramObtenerListaRelContribuyente>
                 <nroEmpresa>${params['accempexternalnumber']}</nroEmpresa>
          </paramObtenerListaRelContribuyente>
       </v003:obtenerListaRelContribuyente>
    </soapenv:Body>
 </soapenv:Envelope>";

    $result   = $client->send($xml, '', 30);
    $log->info("probando el ws");
    $response = array();
    if (!$result || $client->fault || array_key_exists('faultcode', $result)) {
        $response["error"]     = true; 
        $response["resultado"] = $client->fault ? $result["faultstring"] : 'Error';
        $log->info("faultstring");
    } else {
        $error = $client->getError();
        if ($error) {
            $response["error"]     = true; 
            $response["resultado"] = $error; 
            $log->info("error");
        } else {
            if ($result['resultObtenerListaRelContribuyente']['erroresNegocio']) {
                $response["error"]     = true;
                $log->info("error2");
                $response["resultado"] = $result['resultObtenerListaRelContribuyente']['erroresNegocio']['descripcion'];
            } else {
                $response["error"]     = false;
                
                 $log->info("mostrame el result");
                 $log->info($result);
                foreach ( $result['resultObtenerListaRelContribuyente']['contribuyentesRelacionados'] as $child ) {
                    $log->info("entra al foreach");
                    $log->info($child);

                   /* if(!isset($child['[objContribuyente]'])){
                        $response["resultado"] = $result['resultObtenerListaRelContribuyente']['contribuyentesRelacionados']['objRelacionEmpContr'];
                        $log->info($response);
                    }else*/
                    //$child['objRelacionEmpContr']['periodo']['fechaHasta'] == '' ||
                         
                     if (!isset($child['objRelacionEmpContr']['periodo']['fechaHasta']) ){
                            $log->info("entra al if");
                            $log->info($child);
                            $response["resultado"] = $child;
                    }

                    if(!isset($child['objContribuyente'])){
                        $response["resultado"] = $result['resultObtenerListaRelContribuyente'];
                    }

                }
                    $response["resultado"]["empresa"]  =  $result['resultObtenerListaRelContribuyente']['empresa'];        
                
            }
        }
    }

    $time = microtime(true) - $start;
    $log->info(__FUNCTION__ . ' tardó: ' . $time . ' segundos');
    if (LOGS_WS_ENABLED) {
        $adb->pquery("INSERT INTO logs_ws_out (operation, request, response, reqdate, reqtime) VALUES (?,?,?,?,?)", array($operacion, $client->request, $client->response, date("Y-m-d h:i:s"), $time));
    }
    $log->info("mostrame el response");
    $log->info($response);
    return $response;
}

function getDatosEmpresa($params)
{
    global $adb, $log;
    $log->info(__FUNCTION__);
    $log->debug($params);
    //$log->debug(extension_loaded('soap') ? 'SOAP OK' : 'SOAP MISSING');
    $start     = microtime(true);
    $operacion = 'obtECAV002';
    $servicio  = URL_WS_EMPRESAS;
    $client    = new soapclient2($servicio);
    $client->setHTTPEncoding();

    $xml = "<soapenv:Envelope xmlns:soapenv=\"http://schemas.xmlsoap.org/soap/envelope/\" xmlns:v001=\"http://bps.gub.uy/atyr/registro/eca/v001\" xmlns:dat=\"http://bps.gub.uy/atyr/registro/eca/v001/datatypes\" xmlns:atyr=\"http://bps.gub.uy/atyr/\">
            <soapenv:Header/>
            <soapenv:Body>
            <v001:obtECAV002>
                <!--Optional:-->
                <v001:paramObtECA>
                    <dat:nroContribuyente>${params['ticketcontribuyente']}</dat:nroContribuyente>
                    <dat:nroEmpresa>${params['ticketempresa']}</dat:nroEmpresa>
                    <dat:permitirRelAnuladas>
                        <atyr:integer>0</atyr:integer>
                    </dat:permitirRelAnuladas>";
    if( !empty( $params['ticketcodigoaportacion'] ) ){
        $xml .= "<dat:codAportacion>
                        <atyr:integer>${params['ticketcodigoaportacion']}</atyr:integer>
                    </dat:codAportacion>";
    }
                    
    $xml    .= "</v001:paramObtECA>
                </v001:obtECAV002>
            </soapenv:Body>
        </soapenv:Envelope>";

    $result   = $client->send($xml, '', 30);
    $response = array();

    if (!$result || $client->fault || array_key_exists('faultcode', $result)) {
        $response["error"]     = true;
        $response["resultado"] = $client->fault ? $result["faultstring"] : 'Error';
    } else {
        $error = $client->getError();
        if ($error) {
            $response["error"]     = true;
            $response["resultado"] = $error;
        } else {
            if ($result['obtECAV002Return']['colError']['objsError']) {
                $response["error"]     = true;
                if(isset($result['obtECAV002Return']['colError']['objsError']['errorCode'])){
                    if($result['obtECAV002Return']['colError']['objsError']['errorCode'] == 12021){
                        $response["error"] = false;
                        //$response["resultado"] = $result['obtECAV002Return']['contribuyente']['denominacion']['denominacion'];
                        $response["resultado"] = $result['obtECAV002Return']['empresa']['denominacion']['denominacion'];
                    }
                }

                if($response['error'])
                    $response["resultado"] = $result['obtECAV002Return']['colError']['objsError'][0] ? 
                                            $result['obtECAV002Return']['colError']['objsError'][0]['errorDescription'] :
                                            $result['obtECAV002Return']['colError']['objsError']['errorDescription'];
            } 
            else if($result['detail']['ErrorGenerico']){
                $response["error"]     = true;
                $response["resultado"] = $result['detail']['ErrorGenerico']['errorCode'] . ": " .$result['detail']['ErrorGenerico']['errorDescription'];
            }
            else {
                $response["error"]     = false;
                //$response["resultado"] = $result['obtECAV002Return']['contribuyente']['denominacion']['denominacion'];
                $response["resultado"] = $result['obtECAV002Return']['empresa']['denominacion']['denominacion'];
            }
        }
    }

    $time = microtime(true) - $start;
    $log->info(__FUNCTION__ . ' tardó: ' . $time . ' segundos');
    if (LOGS_WS_ENABLED) {
        $adb->pquery("INSERT INTO logs_ws_out (operation, request, response, reqdate, reqtime) VALUES (?,?,?,?,?)", array($operacion, $client->request, $client->response, date("Y-m-d h:i:s"), $time));
    }
    $log->info("El response es: ");
    $log->info($response);
    return $response;
}

function getDatosUsuarioInt($user)
{
    global $adb, $log;
    $log->info(__FUNCTION__);
    $log->debug($user);
    //$log->debug(extension_loaded('soap') ? 'SOAP OK' : 'SOAP MISSING');
    $start     = microtime(true);
    $operacion = 'ObtIdUsu';
    $servicio  = URL_WS_USUARIOSINT;
    $client    = new soapclient2($servicio);
    $client->setHTTPEncoding();

    $xml = "<soapenv:Envelope xmlns:soapenv=\"http://schemas.xmlsoap.org/soap/envelope/\" xmlns:type=\"http://bps.gub.uy/wsGup/type/\">
        <soapenv:Header/>
        <soapenv:Body>
            <type:ObtIdUsu_Param>
                <!--Optional:-->
                <type:IdExterno>{$user}</type:IdExterno>
                <!--Optional:-->
                <type:UsuarioExt>S</type:UsuarioExt>
            </type:ObtIdUsu_Param>
        </soapenv:Body>
    </soapenv:Envelope>";

    $result   = $client->send($xml, 'http://bps.gub.uy/wsGup/action/cAdmGUP.ObtIdUsu', 30);
    $response = array();

    if (!$result || $client->fault || array_key_exists('faultcode', $result)) {
        $response["error"]     = true;
        $response["resultado"] = $client->fault ? $result["faultstring"] : 'Error';
    } else {
        $error = $client->getError();
        if ($error) {
            $response["error"]     = true;
            $response["resultado"] = $error;
        } else {
            $response["error"]     = false;
            $response["resultado"] = $result['ObtIdUsu']['ObtIdUsu_Registro'];
            //$response["resultado"] = $result['ObtIdUsu']['ObtIdUsu_Registro']['PERS_IDENTIFICADOR'];
        }
    }

    $time = microtime(true) - $start;
    $log->info(__FUNCTION__ . ' tardó: ' . $time . ' segundos');
    if (LOGS_WS_ENABLED) {
        $adb->pquery("INSERT INTO logs_ws_out (operation, request, response, reqdate, reqtime) VALUES (?,?,?,?,?)", array($operacion, $client->request, $client->response, date("Y-m-d h:i:s"), $time));
    }
    return $response;
}

function ObtIdUsu($user){
    //$wsdl = 'http://serviciointernobpst.bps.net:8001/WS/wsGup';
    $wsdl = URL_WS_USUARIOSINT;
    $client    = new soapclient2($wsdl);  
    $client->setHTTPEncoding();    
    $xml = "<soapenv:Envelope xmlns:soapenv=\"http://schemas.xmlsoap.org/soap/envelope/\" xmlns:type=\"http://bps.gub.uy/wsGup/type/\">
           <soapenv:Header/>
           <soapenv:Body>
              <type:ObtIdUsu_Param>
                 <!--Optional:-->
                 <type:IdExterno>{$user}</type:IdExterno>
                 <!--Optional:-->
                 <type:UsuarioExt>N</type:UsuarioExt>
              </type:ObtIdUsu_Param>
           </soapenv:Body>
        </soapenv:Envelope>";
               

 
    $result = $client->send($xml,'http://bps.gub.uy/wsGup/action/cAdmGUP.ObtIdUsu', 30);
    $response = array();
    if (!$result || $client->fault || array_key_exists('faultcode', $result)) {
        $response["error"]     = true;
        $response["resultado"] = $client->fault ? $result["faultstring"] : 'Error';
    } else {
        $error = $client->getError();
        if ($error) {
            $response["error"]     = true;
            $response["resultado"] = $error;
        } else {
            $response["error"]     = false;
            $response["resultado"] = $result['ObtIdUsu']['ObtIdUsu_Registro']['USUARIO_ID'];
        }
    }

    return $response;
}


function ObtPerfilesUsuOf($user_id){
    //$wsdl = 'http://serviciointernobpst.bps.net:8001/WS/wsGup';
    $wsdl = URL_WS_USUARIOSINT;
    $client    = new soapclient2($wsdl);  
    $client->setHTTPEncoding();    
    $xml = "<soapenv:Envelope xmlns:soapenv=\"http://schemas.xmlsoap.org/soap/envelope/\" xmlns:type=\"http://bps.gub.uy/wsGup/type/\">
           <soapenv:Header/>
           <soapenv:Body>
              <type:ObtPerfilesUsuOf_Param>
                 <!--Optional:-->
                 <type:UsuarioId>{$user_id}</type:UsuarioId>
                 <!--Optional:-->
                 
              </type:ObtPerfilesUsuOf_Param>
           </soapenv:Body>
        </soapenv:Envelope>";

    $result = $client->send($xml,'http://bps.gub.uy/wsGup/action/cAdmGUP.ObtPerfilesUsuOf', 30);
    $response = array();
    if (!$result || $client->fault || array_key_exists('faultcode', $result)) {
        $response["error"]     = true;
        $response["resultado"] = $client->fault ? $result["faultstring"] : 'Error';
    } else {
        $error = $client->getError();
        if ($error) {
            $response["error"]     = true;
            $response["resultado"] = $error;
        } else {
            $response["error"]     = false;
            $perfilBuscado = "GAP_CONSULTOR";

            foreach ($result["ObtPerfilesUsuOf"]["ObtPerfilesUsuOf_Registro"] as $elemento) {
                if ($elemento["PERFIL"] === $perfilBuscado) {
                    // El perfil buscado se encontró en este elemento
                    //var_dump($elemento);
                    $response["resultado"] = $perfilBuscado;
                    }
            
            //['ObtPerfilesUsuOf']['ObtPerfilesUsuOf_Registro'][0]['PERFIL'];
            }
        }
    }

    return $response;
}