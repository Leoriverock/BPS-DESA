<?php 


class Analisis_Filtros_Action extends Vtiger_BasicAjax_Action {


	public function process(Vtiger_Request $request){
		$mode = $request->getMode();
		if($mode == 'getdata'){
			$this->getFiltros($request);
			return;
		}
		if($mode == 'create'){
			$this->guardarFiltro($request);
			return;
		}
		if($mode == 'delete'){

		}
	}

	public function getFiltros($request){
		global $log;
		$data = array();
		$adb = PearDatabase::getInstance(); 
		$currentUserModel = Users_Record_Model::getCurrentUserModel();
		$idUser = $currentUserModel->getId();

		$module = $request->get('modulename');
		$viewname = $request->get('viewname');
		$resultSet = $adb->pquery("SELECT * FROM lp_analisis_filtros WHERE (usuario = ? OR publico = 1) AND  deleted = 0 AND module = ? AND viewname = ? ",array($idUser,$module,$viewname));
		if( $adb->num_rows($resultSet) ){
			while( $row = $adb->fetch_array($resultSet) ){
				$log->debug(json_encode($row));
				$data[] = array(
					'usuario'	=>	$row['usuario'],
					'nombre'	=>  $this->parseTildes($row['nombre']),
					'columnas'	=>  $this->parseColumnasFilas($this->parseTildes($row['columnas'])),
					'filas'		=>	$this->parseColumnasFilas($this->parseTildes($row['filas'])),
					'filtros'	=>  $this->parseFiltros($row['filtros']),
					'id'		=>	$row['id']
					);
			}
		}

		$response = new Vtiger_Response();
		$response->setResult($data);
		$response->emit();
	}


	/*
	* Funcion para parsear las columnas obtenidas de la base de datos
	*/
	public function parseColumnasFilas($data){
		if($data == "") return array();
		return explode("|##|", $data);
	}
	
	/*
	* Funcion para parsear los filtros obtenidos en la base de datos
	*/
	public function parseFiltros($data){
		global $log;
		$log->debug($data);
		$ret = array();
		$arr = explode("|##|", $data);	
		foreach ($arr as $single) {
			$e = explode(" : ", $single);
			$ret[$e[0]] = $e[1];
		}
		return $ret;
	}

	/*
	* Funcion para parsear los filtros a string para guardarlos en la base de datos
	*/
	public function filtrosToDb($data){
		$stringReturn = "";
		foreach ($data as $key => $value) {
			$stringReturn .= $key." : ".$value."|##|";
		}
		$stringReturn = rtrim($stringReturn,"|##|");
		return $stringReturn;
	}
	/*
	*	Funcion para parsear las columnas y filas a string para guardalos en la base de datos
	*/
	public function columnasFilasToDb($data){		
		$stringReturn = "";
		if($data != ""){
			foreach ($data as $single) {
				$stringReturn .= $single."|##|";
			}
			$stringReturn = rtrim($stringReturn,"|##|");
		}
		return $stringReturn;
	}

	public function guardarFiltro(Vtiger_Request $request){
		global $log;
		$log->debug("En la guardarFiltro");
		$adb = PearDatabase::getInstance();
		$currentUserModel = Users_Record_Model::getCurrentUserModel();
		$usuario 	= $currentUserModel->getId();
		$datos 		= $request->get('datos');
		$module 	= $datos['module'];
		$viewname 	= $datos['viewname'];
		$datos['usuario'] 	= $usuario;	
		$log->debug("Datos::".json_encode($datos));
		$existe = $adb->pquery("SELECT 1 FROM lp_analisis_filtros WHERE nombre = ? AND usuario = ? AND viewname = ? AND module = ?  ",array($datos['nombre'],$usuario,$viewname,$module));
		if($adb->num_rows($existe)){
			return $this->modificarFiltro($datos);
		}
		else{
			return $this->crearFiltro($datos);
		}
	}
	
	public function eliminarFiltro(){

	}

	public function modificarFiltro($datos){
		global $log;
		$adb = PearDatabase::getInstance();
		//Checkear que sea del mismo usuario
		$nombre = $datos['nombre'];	
		$usuario = $datos['usuario'];
		$cols = $this->columnasFilasToDb($datos['columnas']);
		$rows = $this->columnasFilasToDb($datos['filas']);
		$filtros = $this->filtrosToDb($datos['filtros']);
		$module 	= $datos['module'];
		$viewname 	= $datos['viewname'];

		$params = array($rows,$cols,$filtros,$nombre,$usuario,$module,$viewname);	
		$result = $adb->pquery("UPDATE lp_analisis_filtros SET filas = ? ,columnas = ? ,filtros = ? WHERE nombre = ? AND usuario = ? AND module = ? AND viewname = ? ",array($params));


		$response = new Vtiger_Response();
		$response->setResult($datos);
		$response->emit();

	}
	public function crearFiltro($datos){
		global $log;		
		$adb = PearDatabase::getInstance();
		$result = $adb->pquery("SELECT IFNULL(MAX(id)+1,1) AS id FROM lp_analisis_filtros",array());
		$row = $adb->fetch_array($result);
		$id = $datos['id'] = $row['id'];
		$nombre = $datos['nombre'];	
		$usuario = $datos['usuario'];
		$cols = $this->columnasFilasToDb($datos['columnas']);
		$rows = $this->columnasFilasToDb($datos['filas']);
		$filtros = $this->filtrosToDb($datos['filtros']);		
		$module 	= $datos['module'];
		$viewname 	= $datos['viewname'];
		$params = array($id,$nombre,$rows,$cols,$filtros,$usuario,$module,$viewname);		
		$result = $adb->pquery("INSERT INTO lp_analisis_filtros (id,nombre,filas,columnas,filtros,usuario,module,viewname) 
			VALUES(?,?,?,?,?,?,?,?)",$params);
		$log->debug(json_encode($datos));
		
		$response = new Vtiger_Response();
		$response->setResult($datos);
		$response->emit();
	}

	function parseTildes($texto){
		return str_replace(	array('&ntilde;','&aacute;','&eacute;','&iacute;','&oacute;','&uacute;','&Aacute;','&Eacute;','&Iacute;','&Oacute;','&Uacute;'),array('ñ','á','é','í','ó','ú','Á','É','Í','Ó','Ú'),$texto);
	}
}

?>