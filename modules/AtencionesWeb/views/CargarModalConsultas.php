<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/
require_once("config.ludere.php");
require_once ('include/utils/LP_utils.php');
class AtencionesWeb_CargarModalConsultas_View extends Vtiger_IndexAjax_View {

	public function checkPermission(Vtiger_Request $request) {
	}

	public function process(Vtiger_Request $request) {
		global $log, $adb,$current_user;
		
		//$usuarioLogueado = 11; //Id usuario de prueba trae consultas de diego da costa
		$usuarioLogueado = $current_user->id; //usuario que estoy logueado
		$grupoActual = ''; //Grupo que pertenece el usuario - hacer consultas a vtiger_user2goups (puede pertencer a mas de un grupo)

		$sql_group_pref = "SELECT us_grupopref id FROM vtiger_users WHERE  id = ?";
		$result = $adb->pquery($sql_group_pref,array($usuarioLogueado));
		$grupo_pref = $adb->query_result($result, 0, 'id');
		
		$moduleName = $request->getModule();
		$consultaswebid = $request->get("id");

		//Verificar si tengo atencion activa
		/*$aw_activa = "SELECT 1
					  FROM  vtiger_atencionesweb  
					  INNER JOIN vtiger_crmentity  ON (vtiger_atencionesweb.atencioneswebid = vtiger_crmentity.crmid  )
					  INNER JOIN vtiger_atencioneswebcf ON (vtiger_atencioneswebcf.atencioneswebid = vtiger_crmentity.crmid  ) 
					  LEFT JOIN vtiger_crmentity_user_field ON (vtiger_crmentity_user_field.recordid = vtiger_crmentity.crmid  ) 
					  WHERE  vtiger_crmentity.crmid
					  AND  vtiger_crmentity_user_field.userid = ?
					  AND AW_ESTADO = ?";*/
		$aw_activa = 'SELECT 1 FROM vtiger_atencionesweb a INNER JOIN vtiger_crmentity e ON e.crmid = a.atencioneswebid AND e.deleted = 0 WHERE a.aw_estado = ? AND e.smownerid = ?' ;
		$result = $adb->pquery($aw_activa,array('Asignado',$usuarioLogueado));
		$asignado = $adb->num_rows($result);
		//Si hay atencion web asignada no debe dejar mostrar la consulta
		$log->debug("toy en AtencionesWeb_CargarModalConsultas_View usuario: $asignado  ");
		if($asignado == 0){
					
					$sql = "SELECT groupname FROM vtiger_groups WHERE groupid = ? ";
					$res = $adb->pquery($sql,array($grupo_pref));
					$grupo_pref_nombre = $adb->query_result($res, 0, 'groupname');

					if($grupo_pref_nombre != ''){
						$and_condition = ' AND cw_grupo = ? ';
					}
					//Consultas asignas al usuario
					$sql = "SELECT  consultaswebid id,
									cw_asunto asunto,
									cw_origen origen, 
									cw_estado estado, 
									cw_contribuyente contribuyente,
									createdtime fecha, 
									cw_aportacion aportacion, 
									cw_de_mail deemail, 
									cw_para para, 
									cw_contenido contenido,
									a.accountname cuenta,
									a.accountid cuentaid,
									cw_empresa empresa,
									cw_persona personaid,
									cw_grupo cw_grupo,
									cw_tema tema,
									cw_categoria categoria,
									CASE
									WHEN cw_estado = 'Pendiente Agente' THEN 1
									ELSE 2
										END AS estado_order
							FROM vtiger_consultasweb  
							INNER JOIN vtiger_crmentity c ON vtiger_consultasweb.consultaswebid = c.crmid  
							LEFT JOIN vtiger_account a ON accountid = cw_persona
							WHERE c.deleted=0 
							AND (cw_estado = ? or cw_estado = ?)".
							$and_condition
							." AND c.smownerid = ?
							ORDER BY estado_order, CREATEDTIME
							LIMIT 1"; 

					if($grupo_pref_nombre != ''){	
						$datos_consultaweb = $adb->pquery($sql, array('Pendiente Agente','Pendiente',$grupo_pref_nombre,$usuarioLogueado));
					}else{
						$datos_consultaweb = $adb->pquery($sql, array('Pendiente Agente','Pendiente',$usuarioLogueado));
					}

					$id = $adb->query_result($datos_consultaweb, 0, 'id');
					$estado = $adb->query_result($datos_consultaweb, 0, 'estado');
					$asunto = $adb->query_result($datos_consultaweb, 0, 'asunto');
					$contribuyente = $adb->query_result($datos_consultaweb, 0, 'contribuyente');
					$fecha = $adb->query_result($datos_consultaweb, 0, 'fecha');
					$aportacion = $adb->query_result($datos_consultaweb, 0, 'aportacion');
					$origen = $adb->query_result($datos_consultaweb, 0, 'origen');
					$de_email = $adb->query_result($datos_consultaweb, 0, 'deemail');
					$para = $adb->query_result($datos_consultaweb, 0, 'para');
					$contenido = $adb->query_result($datos_consultaweb, 0, 'contenido');
					$nombre_cuenta = $adb->query_result($datos_consultaweb, 0, 'cuenta');
					$id_cuenta = $adb->query_result($datos_consultaweb, 0, 'cuentaid');
					$empresa = $adb->query_result($datos_consultaweb, 0, 'empresa');
					$personaid = $adb->query_result($datos_consultaweb, 0, 'personaid');
					$grupo = $adb->query_result($datos_consultaweb, 0, 'cw_grupo');
					//$grupo = $grupo_pref_nombre;
					$log->info("mostrando el grupo".$grupo);
					$temaid = $adb->query_result($datos_consultaweb, 0, 'tema');
					$categoria = $adb->query_result($datos_consultaweb, 0, 'categoria');

					$consulta = "SELECT topicname nombre FROM vtiger_topics WHERE topicsid = ?";
					$resultado = $adb->pquery($consulta,array($temaid));

					$tema = $adb->query_result($resultado, 0, 'nombre');


					$count_row = $adb->num_rows($datos_consultaweb);
					$log->debug("Estoy en consulta de grupos :$count_row");
					
				//Si no hay consultas asignadas al usuario chequeo grupos
				if ($count_row == 0){	

					$log->debug("Estoy en consulta de grupos");
					$consulta_grupos = "SELECT 1 
							   FROM vtiger_users2group
							   WHERE userid = ?";
					$result = $adb->pquery($consulta_grupos,array($usuarioLogueado));
					//Busco si hay consultas asignadas al usuario
					if($adb->num_rows($result) > 0)
					{	
							$sql = "SELECT  consultaswebid id,
									cw_origen origen, 
									cw_asunto asunto,
									cw_estado estado, 
									cw_contribuyente contribuyente,
									createdtime fecha, 
									cw_aportacion aportacion, 
									cw_de_mail deemail, 
									cw_para para, 
									cw_contenido contenido,
									a.accountname cuenta,
									a.accountid cuentaid,
									cw_empresa empresa,
									cw_persona personaid,
									cw_grupo grupo,
									cw_tema tema,
									cw_categoria categoria
							FROM vtiger_consultasweb  
							INNER JOIN vtiger_crmentity c ON vtiger_consultasweb.consultaswebid = c.crmid  
							LEFT JOIN vtiger_account a ON accountid = cw_persona
							LEFT JOIN vtiger_users2group ON c.smownerid = userid
							WHERE c.deleted=0 
							 
							AND cw_estado = ?"; 

							$log->info("entra a la consulta");
							$log->info($grupo_pref);
							if($grupo_pref == 0){
								$idgrupos = "SELECT groupid
								   FROM vtiger_users2group
								   WHERE userid = ?";

								$result2 = $adb->pquery($idgrupos,array($usuarioLogueado));

								$sql.=" AND c.smownerid IN ( " ;
								while($fila = $adb->fetch_array($result2)){

									 $sql.=  $fila['groupid'].",";
								}
							}else{
								$sql.=" AND c.smownerid IN ( " ;
								$sql.= $grupo_pref ;
							}
							


							$sql = trim($sql, ',');
							$sql .= " )  ORDER BY c.createdtime LIMIT 1";

							$grupo_consultaweb = $adb->pquery($sql, array('Pendiente'));
							$id = $adb->query_result($grupo_consultaweb, 0, 'id');
							$estado = $adb->query_result($grupo_consultaweb, 0, 'estado');
							$asunto = $adb->query_result($grupo_consultaweb, 0, 'asunto');
							$contribuyente = $adb->query_result($grupo_consultaweb, 0, 'contribuyente');
							$fecha = $adb->query_result($grupo_consultaweb, 0, 'fecha');
							$aportacion = $adb->query_result($grupo_consultaweb, 0, 'aportacion');
							$origen = $adb->query_result($grupo_consultaweb, 0, 'origen');
							$de_email = $adb->query_result($grupo_consultaweb, 0, 'deemail');
							$para = $adb->query_result($grupo_consultaweb, 0, 'para');
							$contenido = $adb->query_result($grupo_consultaweb, 0, 'contenido');
							$nombre_cuenta = $adb->query_result($grupo_consultaweb, 0, 'cuenta');
							$id_cuenta = $adb->query_result($grupo_consultaweb, 0, 'cuentaid');
							$empresa = $adb->query_result($grupo_consultaweb, 0, 'empresa');
							$personaid = $adb->query_result($grupo_consultaweb, 0, 'personaid');
							$grupo = $adb->query_result($grupo_consultaweb, 0, 'grupo');
							$log->info("el otro grupo es".$grupo);
							$temaid = $adb->query_result($grupo_consultaweb, 0, 'tema');
							$categoria = $adb->query_result($grupo_consultaweb, 0, 'categoria');

							$consulta = "SELECT topicname nombre FROM vtiger_topics WHERE topicsid = ?";
							$resultado = $adb->pquery($consulta,array($temaid));

							$tema = $adb->query_result($resultado, 0, 'nombre');
					}		
					
				}

		
					//Si no hay consultas en los grupos traigo la mas "vieja"
					/*$count_row_gr = $adb->num_rows($grupo_consultaweb);
							$log->debug("Estoy en consulta de grupos :$count_row");

					if($count_row == 0 && $count_row_gr == 0){
						$log->debug("Estoy en consulta mas vieja");
						$sql = "SELECT  consultaswebid id,
										cw_asunto asunto,
										cw_origen origen, 
										cw_estado estado, 
										cw_contribuyente contribuyente,
										createdtime fecha, 
										cw_aportacion aportacion, 
										cw_de_mail deemail, 
										cw_para para, 
										cw_contenido contenido,
										a.accountname cuenta,
										a.accountid cuentaid,
										cw_empresa empresa,
										cw_persona personaid,
										cw_grupo grupo,
										cw_tema tema,
										cw_categoria categoria
								FROM vtiger_consultasweb  
								INNER JOIN vtiger_crmentity c ON vtiger_consultasweb.consultaswebid = c.crmid  
								LEFT JOIN vtiger_account a ON accountid = cw_persona
								WHERE c.deleted=0 AND cw_estado = ?
								ORDER BY c.createdtime 
								LIMIT 1
								"; 


						$consultaweb = $adb->pquery($sql, array('Pendiente'));

						$id = $adb->query_result($consultaweb, 0, 'id');
						$asunto = $adb->query_result($consultaweb, 0, 'asunto');
						$origen = $adb->query_result($consultaweb, 0, 'origen');
						$estado = $adb->query_result($consultaweb, 0, 'estado');
						$contribuyente = $adb->query_result($consultaweb, 0, 'contribuyente');
						$fecha = $adb->query_result($consultaweb, 0, 'fecha');
						$aportacion = $adb->query_result($consultaweb, 0, 'aportacion');
						$de_email = $adb->query_result($consultaweb, 0, 'deemail');
						$para = $adb->query_result($consultaweb, 0, 'para');
						$contenido = $adb->query_result($consultaweb, 0, 'contenido');
						$nombre_cuenta = $adb->query_result($consultaweb, 0, 'cuenta');
						$id_cuenta = $adb->query_result($consultaweb, 0, 'cuentaid');
						$empresa = $adb->query_result($consultaweb, 0, 'empresa');
						$personaid = $adb->query_result($consultaweb, 0, 'personaid');
						$grupo = $adb->query_result($consultaweb, 0, 'grupo');
						$temaid = $adb->query_result($consultaweb, 0, 'tema');
						$categoria = $adb->query_result($consultaweb, 0, 'categoria');

						$consulta = "SELECT topicname nombre FROM vtiger_topics WHERE topicsid = ?";
						$resultado = $adb->pquery($consulta,array($temaid));

						$tema = $adb->query_result($resultado, 0, 'nombre');
					}*/
					

					//Falta agregar filtro datos de empresa
					$consulta = "SELECT consultaswebid id,
										cw_origen origen,
										cw_de_mail deemail,
										cw_estado estado,
										cw_tema tema, 
										cw_grupo grupo,
										cw_contenido contenido
								  FROM vtiger_consultasweb
								  INNER JOIN vtiger_crmentity ON vtiger_consultasweb.consultaswebid = vtiger_crmentity.crmid 
								  INNER JOIN vtiger_account a ON accountid = cw_persona
			    				  WHERE vtiger_crmentity.deleted=0 
			    				  AND cw_persona = ?
			    				  AND cw_de_mail = ?
			    				  AND cw_grupo = ?
			    				  AND cw_tema = ?
								  AND cw_estado = ?
								  AND (cw_empresa = ? AND cw_contribuyente = ?)
								  AND consultaswebid NOT IN (?)";
					// 

					//$consultasweb[] = "";	
					$result = $adb->pquery($consulta,array($personaid,$de_email,$grupo,$temaid,$estado,$empresa, $contribuyente,$id)); 
		

					
		
					while($fila = $adb->fetch_array($result)){

						$consulta = "SELECT topicname nombre FROM vtiger_topics WHERE topicsid = ?";
						$resultado = $adb->pquery($consulta,array($temaid));

						$tema = $adb->query_result($resultado, 0, 'nombre');

						$contenido_consulta = $fila["contenido"];
						$contenido_formateado = nl2br($contenido_consulta); 
						$consultasweb[] = array("id" => $fila["id"],
												"origen" => $fila["origen"],
												"deemail" => $fila["deemail"],
												"estado" => $fila["estado"],
												"tema" => $tema,
												"grupo" => $fila["grupo"],
												"contenido" => $contenido_formateado
						);

						$log->debug("entrando al while ".$consultasweb[id]);
						//Cuando cargo la consulta dejo el estado en asignado temporalmente para las similares
						//$update = "UPDATE vtiger_consultasweb SET cw_estado = ? WHERE consultaswebid = ?";
						//$result_update = $adb->pquery($update,array('Asignado',$fila["id"])); 
						
						}

						//Cuando cargo la consulta dejo el estado en asignado temporalmente
						$recordModel = Vtiger_Record_Model::getInstanceById($id, 'ConsultasWeb');
						$recordModel->set('cw_estado', 'Asignado');
						$recordModel->set('mode', 'edit');
						$recordModel->save();
						/*$update = "UPDATE vtiger_consultasweb SET cw_estado = ? WHERE consultaswebid = ?";
						$result_update = $adb->pquery($update,array('Asignado',$id));*/

						//Traigo archivos adjuntos relacionado a la consultaweb
						$adjunto = "SELECT DISTINCT vtiger_crmentity.crmid id,
													 vtiger_notes.title titulo, 
													 vtiger_notes.filename nombre,
													 vtiger_attachments.attachmentsid aid
									FROM vtiger_notes 
									INNER JOIN vtiger_senotesrel ON vtiger_senotesrel.notesid= vtiger_notes.notesid 
									LEFT JOIN vtiger_notescf ON vtiger_notescf.notesid= vtiger_notes.notesid
									INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid= vtiger_notes.notesid 
									AND vtiger_crmentity.deleted=0 
									INNER JOIN vtiger_crmentity crm2 ON crm2.crmid=vtiger_senotesrel.crmid 
									LEFT JOIN vtiger_groups ON vtiger_groups.groupid = vtiger_crmentity.smownerid 
									LEFT JOIN vtiger_seattachmentsrel ON vtiger_seattachmentsrel.crmid =vtiger_notes.notesid 
									LEFT JOIN vtiger_attachments ON vtiger_seattachmentsrel.attachmentsid = vtiger_attachments.attachmentsid 
									LEFT JOIN vtiger_users ON vtiger_crmentity.smownerid= vtiger_users.id  
									WHERE crm2.crmid=? AND vtiger_notes.filestatus = ? ";
						$resultado = $adb->pquery($adjunto,array($id,1));
						while($fila = $adb->fetch_array($resultado)){
								$adjuntos[] = array("id" => $fila["id"],
													"attachmentsid" => $fila["aid"],
													"titulo" => $fila["titulo"],
													"nombre" => $fila["nombre"]);
						}

		$log->debug("toy en $contenido");
		$viewer = $this->getViewer($request);
		//Datos enviados
		$viewer->assign('ID',$id);
		$viewer->assign('ADJUNTOS',$adjuntos);
		$viewer->assign('ASUNTO',$asunto);
		$viewer->assign('ORIGEN',$origen);	
		$viewer->assign('ESTADO',$estado);
		$viewer->assign('CONTRIBUYENTE',$contribuyente);
		$viewer->assign('FECHA',$fecha);
		$viewer->assign('GRUPO',$grupo);
		$viewer->assign('APORTACION',$aportacion);
		$viewer->assign('DEEMAIL',$de_email);
		$viewer->assign('PARA',$para);
		$viewer->assign('CONTENIDO',$contenido);
		$viewer->assign('CUENTA',$nombre_cuenta);
		$viewer->assign('CUENTAID',$id_cuenta);
		$viewer->assign('EMPRESA',$empresa);
		$viewer->assign('TEMA',$tema);
		$viewer->assign('TEMAID',$temaid);
		$viewer->assign('CATEGORIA',$categoria);
		$viewer->assign('CONTENIDO',nl2br($contenido));
		$viewer->assign('CONSULTASWEB',$consultasweb);
		$viewer->assign('ERROR', 0);

				
			}else{
				$viewer = $this->getViewer($request);
				$viewer->assign('ERROR', 1);
				
			}
		
		 

		

		

		/*$log->debug("toy en $contenido");
		$viewer = $this->getViewer($request);
		//Datos enviados
		$viewer->assign('ID',$id);
		$viewer->assign('ORIGEN',$origen);
		$viewer->assign('DEEMAIL',$de_email);
		$viewer->assign('PARA',$para);
		$viewer->assign('CONTENIDO',$contenido);
		$viewer->assign('CUENTA',$nombre_cuenta);
		$viewer->assign('CUENTAID',$id_cuenta);
		$viewer->assign('EMPRESA',$empresa);
		$viewer->assign('TEMA',$tema);
		$viewer->assign('CATEGORIA',$categoria);
		$viewer->assign('CONTENIDO',nl2br($contenido));
		$viewer->assign('CONSULTASWEB',$consultasweb);*/


		$viewer->assign('MODULE', $moduleName);
		//$viewer->assign('SCRIPTS', $this->getHeaderScripts($request));
		
			
		
		echo $viewer->view('modalConsultasWeb.tpl',$moduleName,true);
	}
	
	
}