<?php

require_once 'include/utils/utils.php';
require_once 'includes/Loader.php';
require_once 'libraries/nusoap/nusoap.php';
require_once 'config.ludere.php';

vimport('includes.http.Request');
vimport('includes.runtime.Globals');
vimport('includes.runtime.BaseModel');
vimport('includes.runtime.Controller');
vimport('includes.runtime.LanguageHandler');
vimport('modules.Install.models.Utils');

global $adb, $current_user;

if (!$current_user) {
    $current_user = Users::getActiveAdminUser();
}

header("Content-type:text/html; charset=UTF-8");

function GuardarLog($request, $response)
{
    global $adb;
    if (LOGS_WS_ENABLED) {
        $adb->pquery('INSERT INTO logs_ws_in (request, response, reqdate) VALUES (?,?,?)', array(is_array($request) ? json_encode($request) : $request, is_array($response) ? json_encode($response) : $response, date("Y-m-d h:i:s")));
    }
}

function CrearLlamada($params)
{
    $response = Calls_Module_Model::addCall($params);
    GuardarLog($params, $response);
    return new soapval( 'return', 'tns:returnCall', $response );
}

function CortarLlamada($params)
{
    $response = Calls_Module_Model::endCall($params);
    GuardarLog($params, $response);
    return new soapval( 'return', 'tns:returnCall', $response );
}

function DarURL($params)
{
    $response = Calls_Module_Model::searchUser($params);
    GuardarLog($params, $response);
    return new soapval( 'return', 'tns:returnUser', $response );
}

function TicketCreado($params)
{

    $response = Calls_Module_Model::searchTicketsAndComments($params);
    GuardarLog($params, $response);
    return new soapval( 'return', 'tns:returnCallData', $response );

}

$server = new soap_server();
$server->configureWSDL("call", "urn:call", false, 'document');
$server->wsdl->schemaTargetNamespace = "urn:call";

$server->wsdl->addComplexType(
    'call',
    'complexType',
    'struct',
    'all',
    '',
    array(
        "callid"              => array("name" => "callid", "type" => "xsd:string", "info" => "ID de llamada"),
        "callstartdate"       => array("name" => "callstartdate", "type" => "xsd:string", "info" => "Fecha de inicio de la llamada"),
        "callstarttime"       => array("name" => "callstarttime", "type" => "xsd:string", "info" => "Hora de inicio de la llamada"),
        "callphonenumber"     => array("name" => "callphonenumber", "type" => "xsd:string", "info" => "Número de teléfono"),
        "calldocumenttype"    => array("name" => "calldocumenttype", "type" => "xsd:string", "info" => "Tipo de documento del cliente"),
        "calldocumentnumber"  => array("name" => "calldocumentnumber", "type" => "xsd:string", "info" => "Número de documento del cliente"),
        "calldocumentcountry" => array("name" => "calldocumentcountry", "type" => "xsd:int", "info" => "País del cliente"),
        "calluser"            => array("name" => "calluser", "type" => "xsd:string", "info" => "Nombre de Usuario que atiende la llamada"),
        "callwg"              => array("name" => "callwg", "type" => "xsd:string", "info" => "WG de la llamada"),
        "callpin"             => array("name" => "callpin", "type" => "xsd:string", "info" => "PIN de la llamada"),
        "callmultiple"        => array("name" => "callmultiple", "type" => "xsd:boolean", "info" => "Múltiple de la llamada"),
    )
);

$server->wsdl->addComplexType(
    'endCall',
    'complexType',
    'struct',
    'all',
    '',
    array(
        "callid"   => array("name" => "callid", "type" => "xsd:string", "info" => "ID de llamada"),
        "calluser" => array("name" => "calluser", "type" => "xsd:string", "info" => "Nombre de Usuario que atiende la llamada"),
    )
);

$server->wsdl->addComplexType(
    'user',
    'complexType',
    'struct',
    'all',
    '',
    array(
        "userdocumenttype"    => array("name" => "userdocumenttype", "type" => "xsd:string", "info" => "Tipo de documento del cliente"),
        "userdocumentnumber"  => array("name" => "userdocumentnumber", "type" => "xsd:string", "info" => "Número de documento del cliente"),
        "userdocumentcountry" => array("name" => "userdocumentcountry", "type" => "xsd:int", "info" => "País del cliente"),
    )
);

$server->wsdl->addComplexType(
    'callData',
    'complexType',
    'struct',
    'all',
    '',
    array(
        "callid"   => array("name" => "callid", "type" => "xsd:string", "info" => "ID de llamada"),
        "calluser" => array("name" => "calluser", "type" => "xsd:string", "info" => "Nombre de Usuario que atiende la llamada"),
    )
);

$server->wsdl->addComplexType(
    'returnCall',
    'complexType',
    'struct',
    'all',
    '',
    array(
        "ok"      => array("name" => "ok", "type" => "xsd:boolean"),
        "error"   => array("name" => "error", "type" => "xsd:string"),
        "message" => array("name" => "message", "type" => "xsd:string"),
    )
);

$server->wsdl->addComplexType(
    'returnUser',
    'complexType',
    'struct',
    'all',
    '',
    array(
        "ok"    => array("name" => "ok", "type" => "xsd:boolean"),
        "error" => array("name" => "error", "type" => "xsd:string"),
        "url"   => array("name" => "url", "type" => "xsd:string"),
    )
);

$server->wsdl->addComplexType(
    'returnCallData',
    'complexType',
    'struct',
    'all',
    '',
    array(
        "ok"         => array("name" => "ok", "type" => "xsd:boolean"),
        "error"      => array("name" => "error", "type" => "xsd:string"),
        "hasTickets" => array("name" => "hasTickets", "type" => "xsd:boolean"),
    )
);

$server->register(
    "CrearLlamada",
    array("call" => "tns:call"),
    array("response" => "tns:returnCall"),
    "urn:call",
    false,
    "document",
    "literal",
    "Registra una nueva llamada"
);

$server->register(
    "DarURL",
    array("user" => "tns:user"),
    array("response" => "tns:returnUser"),
    "urn:call",
    false,
    "document",
    "literal",
    "Retorna URL de un usuario"
);

$server->register(
    "CortarLlamada",
    array("endCall" => "tns:endCall"),
    array("response" => "tns:returnCall"),
    "urn:call",
    false,
    "document",
    "literal",
    "Finaliza una llamada"
);

$server->register(
    "TicketCreado",
    array("callData" => "tns:callData"),
    array("response" => "tns:returnCallData"),
    "urn:call",
    false,
    "document",
    "literal",
    "Consulta si una llamada tiene tickets o comentarios asociados"
);

$server->soap_defencoding = 'UTF-8';

$POST_DATA = file_get_contents("php://input");
ob_clean();
$server->service($POST_DATA);
exit();
?>