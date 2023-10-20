<?php
ini_set('display_errors',1);
error_reporting(E_ALL);

echo "entro";

$options = array(
    'http' => array(
        'method'  => 'get',
    ),
);

$context  = stream_context_create($options);
$result = file_get_contents('https://wsgap-backend-dev.apps.ocpp.bps.net/wsgap-backend/api/rest/abrirPuesto?nombrepuesto=fin13e1523w073&usuario=DDACOSTA', false, $context);
echo "<br>";
echo "abrirPuesto";
echo "<br>";

print_r($result);




$context  = stream_context_create($options);
$result = file_get_contents('https://wsgap-backend-dev.apps.ocpp.bps.net/wsgap-backend/api/rest/listarNumerosNuevos?lugcod=228&nombrepuesto=VIR20E0402W106', false, $context);




echo "<br>";
echo "listar()";
echo "<br>";
print_r($result);
/*
$obj = json_decode($result);

print $obj->{'lugcod'}; 

$lugcod = $obj->{'lugcod'};

echo "<br>";
echo "var_dump";
var_dump(json_decode($result, true));*/