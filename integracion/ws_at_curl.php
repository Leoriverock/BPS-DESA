<?php
//ini_set('display_errors',1);
//error_reporting(E_ALL);

class WS_AP{
	private $urlbase = '';
	function __construct($url){
		$this->urlbase = $url;
	}

	private function getUrl(){
		return substr($this->urlbase, -1) == '/' ? $this->urlbase : $this->urlbase.'/'; 
	}

	private function paramGet($params){
		$stringArray = array();
		foreach($params as $key => $value){
			$stringArray[] = "{$key}={$value}";
		}
		return implode('&', $stringArray);
	}

	private function consultaGET($operacion, $params){
		$curl = curl_init();

		curl_setopt_array($curl, array(
		  CURLOPT_URL => $this->urlbase.$operacion.'?'.$this->paramGet($params),
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => '',
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 0,
		  CURLOPT_FOLLOWLOCATION => true,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => 'GET'
		));

		$response = curl_exec($curl);

		curl_close($curl);
		return $response;
	}

	public function abrirPuesto($equipo, $usuario){
		return $this->consultaGET('abrirPuesto', array('nombrepuesto' => $equipo, 'usuario' => $usuario));
	}

	public function cerrarPuesto($equipo){
		return $this->consultaGET('cerrarPuesto', array('nombrepuesto' => $equipo));
	}

	public function listarNumerosNuevos($equipo, $codlugar){
		return $this->consultaGET('listarNumerosNuevos', array('nombrepuesto' => $equipo, 'lugcod' => $codlugar));
	}

	public function listarNumerosAtrasados($equipo, $codlugar){
		return $this->consultaGET('listarNumerosAtrasados', array('nombrepuesto' => $equipo, 'lugcod' => $codlugar));
	}

	public function llamarNumero($lugar,$sector,$numero,$fecha,$usuario,$equipo){
		return $this->consultaGET('llamarNumero', array('nombrepuesto' => $equipo, 'lugcod' => $lugar, 'sectorid' => $sector, 'numerocod' => $numero, 'numerofecha' => $fecha, 'nombreusuario' => $usuario));
	}

	public function atenderNumero($lugar,$sector,$numero,$fecha,$usuario,$equipo){
		return $this->consultaGET('atenderNumero', array('nombrepuesto' => $equipo, 'lugcod' => $lugar, 'sectorid' => $sector, 'numerocod' => $numero, 'numerofecha' => $fecha, 'nombreusuario' => $usuario));
	}

	public function liberarNumero($lugar,$sector,$numero,$fecha,$usuario,$equipo){
		return $this->consultaGET('liberarNumero', array('nombrepuesto' => $equipo, 'lugcod' => $lugar, 'sectorid' => $sector, 'numerocod' => $numero, 'numerofecha' => $fecha, 'nombreusuario' => $usuario));
	}
}
