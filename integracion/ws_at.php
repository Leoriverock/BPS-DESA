<?php
ini_set('display_errors',1);
error_reporting(E_ALL);


function abrirPuesto($equipo,$usuario)
{

		global $log;

		$log->info("entra a getDatosPersona");

		$options = array(
		    'http' => array(
		        'method'  => 'get',
		    ),
		);	


		$context  = stream_context_create($options);
		$result = file_get_contents('https://wsgap-backend-dev.apps.ocpp.bps.net/wsgap-backend/api/rest/abrirPuesto?nombrepuesto={$equipo}&usuario={$usuario}', true, $context); //True para que lo guarde en un array, false en objeto


		$datos = json_decode($result);
		$log->info($datos);

		return json_encode($datos);
		
		/*$lugcod = $datos->{'lugcod'};
		$puestobaja = $datos->{'puestobaja'};
		$puestoestado = $datos->{'puestoestado'};
		$puestoid = $datos->{'puestoid'};
		$puestoip = $datos->{'puestoip'};
		$puestonombre = $datos->{'puestonombre'};
		$puestototem = $datos->{'puestototem'};
		$usucod = $datos->{'usucod'};

		
		echo "<br>";
		echo "var_dump";
		var_dump(json_decode($result, true));

		$datos = array[];
		array_push($datos, $lugcod);
		array_push($datos, $puestobaja);
		array_push($datos, $puestoestado);
		array_push($datos, $puestoid);
		array_push($datos, $puestoip);
		array_push($datos, $puestonombre);
		array_push($datos, $puestototem);
		array_push($datos, $usucod);*/

		
}



function cerrarPuesto($equipo)
{

		global $log;

		$log->info("entra a cerrarPuesto");

		$options = array(
		    'http' => array(
		        'method'  => 'get',
		    ),
		);	


		$context  = stream_context_create($options);
		$result = file_get_contents('https://wsgap-backend-dev.apps.ocpp.bps.net/wsgap-backend/api/rest/cerrarPuesto?nombrepuesto=$equipo', true, $context); //True para que lo guarde en un array, false en objeto


		$datos = json_decode($result);
		$log->info($datos);

		return json_encode($datos);
		

		
}

function traerAtenciones($equipo,$codlugar)
{

		global $log;

		$log->info("entra a getDatosPersona");

		$options = array(
		    'http' => array(
		        'method'  => 'get',
		    ),
		);	


		$context  = stream_context_create($options);
		$result = file_get_contents("https://wsgap-backend-dev.apps.ocpp.bps.net/wsgap-backend/api/rest/listarNumerosNuevos?lugcod={$codlugar}&nombrepuesto={$equipo}", true, $context); //True para que lo guarde en un array, false en objeto


		$datos = json_decode($result);

		return json_encode($datos);
}

function traerAtencionesAtrasadas($equipo,$codlugar)
{

		global $log;

		$log->info("entra a getDatosPersona");

		$options = array(
		    'http' => array(
		        'method'  => 'get',
		    ),
		);	


		$context  = stream_context_create($options);
		$result = file_get_contents("https://wsgap-backend-dev.apps.ocpp.bps.net/wsgap-backend/api/rest/listarNumerosAtrasados?lugcod={$codlugar}&nombrepuesto={$equipo}", true, $context); //True para que lo guarde en un array, false en objeto


		$datos = json_decode($result);

		return json_encode($datos);
}


function llamarNumero($lugar,$sector,$numero,$fecha,$usuario,$equipo)
{

		global $log;

		$log->info("entra a llamarNumero");

		$options = array(
		    'http' => array(
		        'method'  => 'get',
		    ),
		);	

		/*$lugar = 228;
		$sector = 12;
		$usuario = 'DDACOSTA';
		$equipo = 'fin13e1523w073';*/

		$context  = stream_context_create($options);
		$result = file_get_contents('https://wsgap-backend-dev.apps.ocpp.bps.net/wsgap-backend/api/rest/llamarNumero?lugcod=$lugar&sectorid=$sector&numerocod=$numero&numerofecha=$fecha&nombreusuario=$usuario&nombrepuesto=$equipo', true, $context); //True para que lo guarde en un array, false en objeto


		$datos = json_decode($result);
		$log->info($datos);

		return json_encode($datos);
		

		
}


function atenderNumero($lugarcod,$sectorid,$numerocod,$fecha,$usuario,$equipo)
{

		global $log;

		$log->info("entra a atenderNumero");

		$options = array(
		    'http' => array(
		        'method'  => 'get',
		    ),
		);	


		$context  = stream_context_create($options);
		$result = file_get_contents("https://wsgap-backend-dev.apps.ocpp.bps.net/wsgap-backend/api/rest/atenderNumero?lugcod=$lugarcod&sectorid=$sectorid&numerocod=$numerocod&numerofecha=$fecha&nombreusuario=$usuario&nombrepuesto=$equipo", true, $context); 


		$datos = json_decode($result);

		return json_encode($datos);
}


function liberarNumero($lugarcod,$sectorid,$numerocod,$fecha,$usuario,$equipo)
{

		global $log;

		$log->info("entra a liberarNumero");

		$options = array(
		    'http' => array(
		        'method'  => 'get',
		    ),
		);	


		$context  = stream_context_create($options);
		$result = file_get_contents("https://wsgap-backend-dev.apps.ocpp.bps.net/wsgap-backend/api/rest/liberarNumero?lugcod=$lugarcod&sectorid=$sectorid&numerocod=$numerocod&numerofecha=$fecha&nombreusuario=$usuario&nombrepuesto=$equipo", true, $context); 


		$datos = json_decode($result);

		return json_encode($datos);
}




