<?php
//ini_set('display_errors',1);
//error_reporting(E_ALL);
require_once('config.ludere.php');
require_once 'libraries/nusoap/nusoap.php';
class WS_AP{
	const URL = URL_WS_ATENCIONES;

	static function getUrl(){
		return substr(self::URL, -1) == '/' ? self::URL : self::URL.'/'; 
	}

	static function paramGet($params){
		$stringArray = array();
		foreach($params as $key => $value){
			$stringArray[] = "{$key}={$value}";
		}
		return implode('&', $stringArray);
	}

	static function consultaGET($operacion, $params){
		$filename = __DIR__.'/../logs/ws_ap.log';
		$logCurl = fopen($filename, 'a');
		if(filesize($filename) > 10485760){
			fclose($logCurl);
			unlink($filename);
			$logCurl = fopen($filename, 'a');
		}
		$curl = curl_init();

		curl_setopt_array($curl, array(
		  CURLOPT_URL => self::getUrl().$operacion.'?'.self::paramGet($params),
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => '',
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 0,
		  CURLOPT_FOLLOWLOCATION => true,
		  CURLOPT_VERBOSE => 1,
		  CURLOPT_STDERR => $logCurl,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => 'GET'
		));

		$response = curl_exec($curl);

		curl_close($curl);
		return $response;
	}

	static function consultaPOST($operacion, $params){
		$filename = __DIR__.'/../logs/ws_ap.log';
		$logCurl = fopen($filename, 'a');
		if(filesize($filename) > 10485760){
			fclose($logCurl);
			unlink($filename);
			$logCurl = fopen($filename, 'a');
		}
		$curl = curl_init();

		curl_setopt_array($curl, array(
		  CURLOPT_URL => self::getUrl().$operacion.'?'.self::paramGet($params),
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => '',
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 0,
		  CURLOPT_FOLLOWLOCATION => true,
		  CURLOPT_VERBOSE => 1,
		  CURLOPT_STDERR => $logCurl,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => 'POST',
		  CURLOPT_HTTPHEADER => array(
		    'Cookie: af37af078ffa88eff032b0e9e656ff4b=eb428988529cfd81b81e5fa8a30c2a35'
		  ),
		));

		$response = curl_exec($curl);

		curl_close($curl);
		return $response;
	}
	static function consultaPOST_SC($operacion, $params){ //Sin cookies
		$filename = __DIR__.'/../logs/ws_ap.log';
		$logCurl = fopen($filename, 'a');
		if(filesize($filename) > 10485760){
			fclose($logCurl);
			unlink($filename);
			$logCurl = fopen($filename, 'a');
		}
		$curl = curl_init();

		curl_setopt_array($curl, array(
		  CURLOPT_URL => self::getUrl().$operacion.'?'.self::paramGet($params),
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => '',
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 0,
		  CURLOPT_FOLLOWLOCATION => true,
		  CURLOPT_VERBOSE => 1,
		  CURLOPT_STDERR => $logCurl,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => 'POST',
		  
		));

		$response = curl_exec($curl);

		curl_close($curl);
		return $response;
	}


	public static function abrirPuesto($equipo, $usuario){
		//return self::consultaGET('abrirPuesto', array('nombrepuesto' => $equipo, 'usuario' => $usuario));
		return self::consultaPOST('abrirPuesto', array('nombrepuesto' => $equipo, 'usuario' => $usuario));
	}

	public static function cerrarPuesto($equipo){
		//return self::consultaGET('cerrarPuesto', array('nombrepuesto' => $equipo));
		return self::consultaPOST('cerrarPuesto', array('nombrepuesto' => $equipo));
	}

	public static function obtenerTramitesPuesto($equipo){
		return self::consultaGET('obtenerTramitesPuesto', array('nombrepuesto' => $equipo));
	}
	
	public static function obtenerEstadoPuesto($equipo){
		return self::consultaGET('obtenerEstadoPuesto', array('nombrepuesto' => $equipo));		
	}


	public static function listarNumerosNuevos($equipo, $codlugar){
		return self::consultaGET('listarNumerosNuevos', array('nombrepuesto' => $equipo, 'lugcod' => $codlugar));
		//return self::consultaPOST('listarNumerosNuevos', array('nombrepuesto' => $equipo, 'lugcod' => $codlugar));
	}

	public static function listarNumerosAtrasados($equipo, $codlugar){
		return self::consultaGET('listarNumerosAtrasados', array('nombrepuesto' => $equipo, 'lugcod' => $codlugar));
		//return self::consultaPOST('listarNumerosAtrasados', array('nombrepuesto' => $equipo, 'lugcod' => $codlugar));
	}

	public static function llamarNumero($lugar,$sector,$numero,$fecha,$usuario,$equipo,$numeroHora){
		//return self::consultaGET('llamarNumero', array('nombrepuesto' => $equipo, 'lugcod' => $lugar, 'sectorid' => $sector, 'numerocod' => $numero, 'numerofecha' => $fecha, 'nombreusuario' => $usuario));
		return self::consultaPOST('llamarNumero', array('nombrepuesto' => $equipo, 'lugcod' => $lugar, 'sectorid' => $sector, 'numerocod' => $numero, 'numerofecha' => $fecha, 'nombreusuario' => $usuario,'numerohora' => $numeroHora));
	}

	public static function atenderNumero($lugar,$sector,$numero,$fecha,$usuario,$equipo,$numeroHora){
		//return self::consultaGET('atenderNumero', array('nombrepuesto' => $equipo, 'lugcod' => $lugar, 'sectorid' => $sector, 'numerocod' => $numero, 'numerofecha' => $fecha, 'nombreusuario' => $usuario));
		return self::consultaPOST('atenderNumero', array('nombrepuesto' => $equipo, 'lugcod' => $lugar, 'sectorid' => $sector, 'numerocod' => $numero, 'numerofecha' => $fecha, 'nombreusuario' => $usuario,'numerohora' => $numeroHora));
	}

	public static function liberarNumero($lugar,$sector,$numero,$fecha,$usuario,$equipo,$numeroHora){
		//return self::consultaGET('liberarNumero', array('nombrepuesto' => $equipo, 'lugcod' => $lugar, 'sectorid' => $sector, 'numerocod' => $numero, 'numerofecha' => $fecha, 'nombreusuario' => $usuario));
		return self::consultaPOST('liberarNumero', array('nombrepuesto' => $equipo, 'lugcod' => $lugar, 'sectorid' => $sector, 'numerocod' => $numero, 'numerofecha' => $fecha, 'nombreusuario' => $usuario, 'numerohora' => $numeroHora));
	}
	public static function finalizarNumero($lugar,$sector,$numero,$fecha,$usuario,$equipo,$tipoConsulta,$numeroHora,$idtramite){
		//return self::consultaGET('liberarNumero', array('nombrepuesto' => $equipo, 'lugcod' => $lugar, 'sectorid' => $sector, 'numerocod' => $numero, 'numerofecha' => $fecha, 'nombreusuario' => $usuario));
		return self::consultaPOST('finalizarNumero', array('lugcod' => $lugar, 'sectorid' => $sector, 'numerocod' => $numero, 'numerofecha' => $fecha, 'nombreusuario' => $usuario, 'nombrepuesto' => $equipo, 'numerohora' => $numeroHora, 'idTramite' =>$idtramite, 'tipoConsulta' => $tipoConsulta));
	}


	
}
