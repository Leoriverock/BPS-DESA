<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class AtencionesWeb_getAccionesConsulta_Action extends Vtiger_Action_Controller {

	function checkPermission(Vtiger_Request $request) {
	}

	public function process(Vtiger_Request $request) {
		global $log,$adb,$current_user;

		$log->debug("entre al process de AtencionesWeb_getAccionesConsulta_Action");
		
		$json_data = array(); 
		
		$id = $request->get('id');

		//Actualizo el estado de la consultaweb a Asignado
		//$update = "UPDATE vtiger_consultasweb SET cw_estado = ? WHERE consultaswebid = ?";
		//$result_update = $adb->pquery($update,array('Asignado',$id)); 
		//Capturo datos para la atencion  web
		$aw_persona = $request->get('persona');
		//$log->info($aw_persona);
		if(!$aw_persona){
			$aw_persona = 13;
			$log->info($aw_persona);
		}
		$asignado = $request->get('asignado');
		$estado = $request->get('estado');
		$aw_de = $request->get('de');
		$aw_cont_empresa = $request->get('empresa');
		$array_id_rel = $request->get('relacionados');
		$modo = $request->get('modo');
		$tema = $request->get('tema');
		$aw_cont_aportacion = $request->get('aportacion');

		$log->debug("soy el id $aw_de   $aw_cont_empresa  $aw_persona ");	

		//Cargo contenido de la consulta seleccionada
		if($modo == 'getContenido'){
			$consulta = "SELECT consultaswebid id,
							cw_contenido contenido,
							a.accountname cuenta,
							cw_empresa empresa
					  FROM vtiger_consultasweb
					  INNER JOIN vtiger_crmentity ON vtiger_consultasweb.consultaswebid = vtiger_crmentity.crmid 
					  INNER JOIN vtiger_account a ON accountid = cw_persona
    				  WHERE consultaswebid = ?";
		$result = $adb->pquery($consulta,array($id));

		$contenido = $adb->query_result($result, 0, 'contenido');
		



		$log->debug(json_encode($contenido));		
		
		echo json_encode($contenido);
		}

		if($modo == 'cerrar'){
			/*$update = "UPDATE vtiger_consultasweb SET cw_estado = ? WHERE consultaswebid = ?";
			$result_update = $adb->pquery($update,array($estado,$id)); */
			$usuarioLogueado = $current_user->id;
			$recordModelUsers = Vtiger_Record_Model::getInstanceById($usuarioLogueado, 'Users');


			$fechaHora = date('Y-m-d H:i:s');
			$linea = "Se realizo la accion 'Cancelar' en la consulta web record=" . $id;

			$file = "logs/logsCW.csv";

			if (file_exists($file)) {
				$csvFile = fopen($file, 'a');
			} else {
				$csvFile = fopen($file, 'w');

				$headers = array("Usuario", "Estado Actual", "URL", "Fecha y Hora", "Descripción");
				fputcsv($csvFile, $headers, ';', '"', 'UTF-8');
			}

			$data = array(
				$recordModelUsers->getDisplayName(),
				$estado,
				"index.php?module=ConsultasWeb&view=Detail&record=" . $id . "&app=SUPPORT",
				$fechaHora,
				$linea
			);

			fputcsv($csvFile, $data, ';', '"', 'UTF-8');
			fclose($csvFile);



			$recordModel = Vtiger_Record_Model::getInstanceById($id, 'ConsultasWeb');
            $recordModel->set('cw_estado', $estado);
            $recordModel->set('mode', 'edit');
            $recordModel->save();

			echo json_encode("");
		}

		if($modo == 'asignar'){
			//Creo la atencion web

		   	if($asignado != ''){

		   			$fecha_hora_actual = date('Y-m-d h:i:s');
					$log->debug("estoy en atencionweb $fecha_hora_actual");
					$recordModel = Vtiger_Record_Model::getCleanInstance("AtencionesWeb");     
					$recordModel->set("aw_persona", $aw_persona);
					$recordModel->set('assigned_user_id',$asignado);
					$recordModel->set("aw_tema", $tema);
					$recordModel->set("mode","create");
				    $recordModel->save();

				    $atencionwebid = $recordModel->getId();

				    $update = "UPDATE vtiger_atencionesweb 
				    		   SET aw_de = ?, 
				    		   	   aw_fechacomienzo = ?, 
				    		   	   aw_cont_empresa = ?,
				    		   	   aw_cont_aportacion = ?,
				    		   	   aw_estado = ?
				    		   WHERE atencioneswebid = ? ";
				   	$result_update = $adb->pquery($update,array($aw_de,$fecha_hora_actual,$aw_cont_empresa,$aw_cont_aportacion,'Asignado',$atencionwebid));

				   	//Asigno la atencion a la persona
				   	$asignar = "UPDATE vtiger_crmentity SET smownerid=?, 
		   											smgroupid=?,
		   											modifiedby=?,
		   											description=?, 
		   											modifiedtime=?, 
		   											label = ?  
		   						WHERE crmid=?";
		   			$resultado_update = $adb->pquery($asignar, array($asignado,0,1,"",$fecha_hora_actual,"",$id));	

		   			//Asocio la consultaweb a la atencionweb
				    $insert = "INSERT INTO vtiger_crmentityrel (crmid, module, relcrmid, relmodule) VALUES(?,?,?,?)";
				   	$adb->pquery($insert, array($atencionwebid,'AtencionesWeb',$id,'ConsultasWeb'));

		   			foreach ($array_id_rel as &$id_rel) {
				    	//Creo la relacion de las consultas
				    	$insert_rel = "INSERT INTO vtiger_crmentityrel (crmid, module, relcrmid, relmodule) VALUES(?,?,?,?)";
				   		$adb->pquery($insert_rel, array($atencionwebid,'AtencionesWeb',$id_rel,'ConsultasWeb'));
				   		//Cambio el estado a asignado
				   		$update = "UPDATE vtiger_consultasweb SET cw_estado = ? WHERE consultaswebid = ?";
						$result_update = $adb->pquery($update,array('Asignado',$id_rel));
						//Asigno las relacionadas
						$asignados = "UPDATE vtiger_crmentity SET smownerid=?, 
		   											smgroupid=?,
		   											modifiedby=?,
		   											description=?, 
		   											modifiedtime=?, 
		   											label = ?  
		   								WHERE crmid=?";
		   				$resultado_update = $adb->pquery($asignados, array($asignado,0,1,"",$fecha_hora_actual,"",$id_rel));

				   		$log->debug("mostrame el id relacionado $id_rel");
			    	}
		   					

		   			/*$asignar = "UPDATE vtiger_crmentity SET smownerid=?, 
		   											smgroupid=?,
		   											modifiedby=?,
		   											description=?, 
		   											modifiedtime=?, 
		   											label = ?  
		   						WHERE crmid=?";

			   		$resultado_update = $adb->pquery($asignar, array($asignado,0,1,"",$fecha_hora_actual,"",$id));
			   		//Si solo asigno, la consulta queda pendiente	
			   		$update = "UPDATE vtiger_consultasweb SET cw_estado = ? WHERE consultaswebid = ?";
					$result_update = $adb->pquery($update,array('Pendiente',$id)); 
					//Cambio asignacion tambien para consultasweb relacionadas
					foreach ($array_id_rel as &$id_rel) {
						$asignados = "UPDATE vtiger_crmentity SET smownerid=?, 
		   											smgroupid=?,
		   											modifiedby=?,
		   											description=?, 
		   											modifiedtime=?, 
		   											label = ?  
		   						WHERE crmid=?";
		   				$resultado_update = $adb->pquery($asignados, array($asignado,0,1,"",$fecha_hora_actual,"",$id_rel));	
		   				//Si solo asigno, la consulta queda pendiente	
				   		$update2 = "UPDATE vtiger_consultasweb SET cw_estado = ? WHERE consultaswebid = ?";
						$result = $adb->pquery($update2,array('Pendiente',$id_rel));	
					}*/
		   	}
		   	
		   
		 	$log->debug("ConsultasWeb asignada");	
			echo json_encode("");

			
		}


		if($modo == 'atencionweb'){
			//Creo la atencion web

			$fecha_hora_actual = date('Y-m-d h:i:s');
			$log->debug("estoy en atencionweb $fecha_hora_actual");
			$recordModel = Vtiger_Record_Model::getCleanInstance("AtencionesWeb");     
			$recordModel->set("aw_persona", $aw_persona);
			$recordModel->set("aw_tema", $tema);
			$recordModel->set("mode","create");
		    $recordModel->save();

		    $atencionwebid = $recordModel->getId();

		    $update = "UPDATE vtiger_atencionesweb 
		    		   SET aw_de = ?, 
		    		   	   aw_fechacomienzo = ?, 
		    		   	   aw_cont_empresa = ?,	
		    		   	   aw_cont_aportacion = ?,
		    		   	   aw_estado = ?
		    		   WHERE atencioneswebid = ? ";
		   	$result_update = $adb->pquery($update,array($aw_de,$fecha_hora_actual,$aw_cont_empresa,$aw_cont_aportacion,'Asignado',$atencionwebid));	   
		   	$log->debug("soy la atencion  $aw_de   $aw_cont_empresa  $aw_persona ");	
		   	//Asocio la consultaweb a la atencionweb
		    $insert = "INSERT INTO vtiger_crmentityrel (crmid, module, relcrmid, relmodule) VALUES(?,?,?,?)";
		   	$adb->pquery($insert, array($atencionwebid,'AtencionesWeb',$id,'ConsultasWeb'));
		   	$log->debug("soy la atencion  $aw_de   $aw_cont_empresa  $aw_persona ");	
		   	//Asocio la atencionweb a la persona
		   	$insert = "INSERT INTO vtiger_crmentityrel (crmid, module, relcrmid, relmodule) VALUES(?,?,?,?)";
		   	$adb->pquery($insert, array($aw_persona,'Accouts',$atencionwebid,'AtencionesWeb'));
		   	$log->debug("soy la atencion  $asignado   $aw_cont_empresa  $aw_persona ");	
		   	/*//Si cambia el grupo asignado a lo seteo
		   	*/
		   
		 	$log->debug("soy la atencion  $aw_de   $aw_cont_empresa  $aw_persona ");	

		    
		    foreach ($array_id_rel as &$id_rel) {
		    	//Creo la relacion de las consultas
		    	$insert_rel = "INSERT INTO vtiger_crmentityrel (crmid, module, relcrmid, relmodule) VALUES(?,?,?,?)";
		   		$adb->pquery($insert_rel, array($atencionwebid,'AtencionesWeb',$id_rel,'ConsultasWeb'));
		   		//Cambio el estado a asignado
		   		$update = "UPDATE vtiger_consultasweb SET cw_estado = ? WHERE consultaswebid = ?";
				$result_update = $adb->pquery($update,array('Asignado',$id_rel));

		   		$log->debug("mostrame el id relacionado $id_rel");
		    }
		    
			$log->debug("Guardado en atencionweb  $atencionwebid");
			echo json_encode("");
		}
		if($modo == 'transferir'){
			//Transfiero la consulta y sus relacionadas

			$log->debug("estoy en transferir $fecha_hora_actual");
			$consultaswebid = $id;
			$grupo = $asignado;
			 //Obtener grupo
		    $sql = "SELECT groupname FROM vtiger_groups
					WHERE GROUPID = ? ";
			$rs = $adb->pquery($sql, array($asignado));
			$gruponombre = $adb->query_result($rs, 0, 'groupname');

			//Capturar la consulta web y cambio el asignado
			$recordModel = Vtiger_Record_Model::getInstanceById($consultaswebid, 'ConsultasWeb');    
			$recordModel->set('assigned_user_id',$asignado);
			$recordModel->set('cw_grupo',$gruponombre);
			$recordModel->set('cw_estado','Pendiente');
			$log->info("mostrando el grupo: ".$gruponombre);
			$recordModel->set("mode","edit");
		    $recordModel->save();



			foreach ($array_id_rel as &$id_rel) {
				    	//Cambio el asignado de las consultas relacioandas
				    	$recordModel = Vtiger_Record_Model::getInstanceById($id_rel, 'ConsultasWeb');    
						$recordModel->set('assigned_user_id',$asignado);
						$recordModel->set('cw_grupo',$gruponombre);
						$recordModel->set('cw_estado','Pendiente');
						$recordModel->set("mode","edit");
					    $recordModel->save();
				   		$log->debug("mostrame el id relacionado $id_rel");
			    	}
		    
		    
			
			echo json_encode("");
		}

		if($modo == 'chequear'){

			$log->debug("estoy en chequear ");
			$consultaswebid = $id;
			$grupo = $asignado;

		    $sql = "SELECT aw_de email FROM vtiger_atencionesweb a INNER JOIN vtiger_crmentity e ON e.crmid = a.atencioneswebid AND e.deleted = 0 WHERE a.aw_estado = ? AND e.smownerid = ?";
			$result = $adb->pquery($sql, array('Asignado', $grupo));

			$respuesta = $adb->num_rows($result);
		
			$log->info("mostrame la respuesta");
			$log->info($respuesta);

			echo json_encode(strtolower($respuesta));
		}

		
	}

}
?>