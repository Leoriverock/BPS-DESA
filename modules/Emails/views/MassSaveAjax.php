<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/
include_once('include/utils/utils.php');
class Emails_MassSaveAjax_View extends Vtiger_Footer_View {
	function __construct() {
		parent::__construct();
		$this->exposeMethod('massSave');
	}
	
	public function requiresPermission(\Vtiger_Request $request) {
		$permissions = parent::requiresPermission($request);
		$permissions[] = array('module_parameter' => 'module', 'action' => 'DetailView');
		return $permissions;
	}
	
	public function checkPermission(Vtiger_Request $request) {
		return parent::checkPermission($request);
	}

	public function process(Vtiger_Request $request) {
		$mode = $request->getMode();
		if(!empty($mode)) {
			echo $this->invokeExposedMethod($mode, $request);
			return;
		}
	}

	/**
	 * Function Sends/Saves mass emails
	 * @param <Vtiger_Request> $request
	 */
	public function massSave(Vtiger_Request $request) {
		global $upload_badext, $log;
		$log->info("request a la abuelaa");
		$log->info($request);
		$adb = PearDatabase::getInstance();
		$moduleName = $request->getModule();
		$currentUserModel = Users_Record_Model::getCurrentUserModel();
		$recordIds = $this->getRecordsListFromRequest($request);
		$documentIds = $request->get('documentids');
		$signature = $request->get('signature');
		// This is either SENT or SAVED 
		$flag = $request->get('flag');

		$result = Vtiger_Util_Helper::transformUploadedFiles($_FILES, true);
		$_FILES = $result['file'];

		$recordId = $request->get('record');

		if(!empty($recordId)) {
			$recordModel = Vtiger_Record_Model::getInstanceById($recordId,$moduleName);
			$recordModel->set('mode', 'edit');
		}else{
			$recordModel = Vtiger_Record_Model::getCleanInstance($moduleName);
			$recordModel->set('mode', '');
		}

		$parentEmailId = $request->get('parent_id',null);
		$attachmentsWithParentEmail = array();
		if(!empty($parentEmailId) && !empty ($recordId)) {
			$parentEmailModel = Vtiger_Record_Model::getInstanceById($parentEmailId);
			$attachmentsWithParentEmail = $parentEmailModel->getAttachmentDetails();
		}
		$existingAttachments = $request->get('attachments',array());
		if(empty($recordId)) {
			if(is_array($existingAttachments)) {
                /**
				 * When no document is selected from CRM in compose mail form, the $documentIds will be an empty string and not an array
				 * Since $documentIds is string and here we are trying to array push string it will throw fatal error.
				 */
				$documentIds = $documentIds ? $documentIds : array();
				foreach ($existingAttachments as $index =>  $existingAttachInfo) {
					$existingAttachInfo['tmp_name'] = $existingAttachInfo['name'];
					$existingAttachments[$index] = $existingAttachInfo;
					if(array_key_exists('docid',$existingAttachInfo)) {
						$documentIds[] = $existingAttachInfo['docid'];
						unset($existingAttachments[$index]);
					}

				}
			}
		}else{
			//If it is edit view unset the exising attachments
			//remove the exising attachments if it is in edit view

			$attachmentsToUnlink = array();
			$documentsToUnlink = array();


			foreach($attachmentsWithParentEmail as $i => $attachInfo) {
				$found = false;
				foreach ($existingAttachments as $index =>  $existingAttachInfo) {
					if($attachInfo['fileid'] == $existingAttachInfo['fileid']) {
						$found = true;
						break;
					}
				}
				//Means attachment is deleted
				if(!$found) {
					if(array_key_exists('docid',$attachInfo)) {
						$documentsToUnlink[] = $attachInfo['docid'];
					}else{
						$attachmentsToUnlink[] = $attachInfo;
					}
				}
				unset($attachmentsWithParentEmail[$i]);
			}
			//Make the attachments as empty for edit view since all the attachments will already be there
			$existingAttachments = array();
			if(!empty($documentsToUnlink)) {
				$recordModel->deleteDocumentLink($documentsToUnlink);
			}

			if(!empty($attachmentsToUnlink)){
				$recordModel->deleteAttachment($attachmentsToUnlink);
			}

		}

		// This will be used for sending mails to each individual
		$toMailInfo = $request->get('toemailinfo');
		$to = $request->get('to');

		if ($request->get('source_module')=='HelpDesk' || $request->get('emailMode')=='reply'){
			$to = $email_to = $request->get('toEmail');
			$sql = "SELECT from_email_field FROM vtiger_systems";
			$rs = $adb->pquery($sql);
			$from_email = $adb->query_result($rs, 0, 'from_email_field');
			$recordModel->set('from_email',$from_email);


		}

		
		if(is_array($to)) {
			$to = implode(',',$to);
		}
		global $adb,$log;
		$hpid = $request->get('selected_ids');
		//Obtener el nro de titulo
		$sql = "SELECT ticket_no nro,title titulo
				FROM  vtiger_crmentity 
				LEFT JOIN vtiger_troubletickets ON (vtiger_troubletickets.ticketid = vtiger_crmentity.crmid  ) 
				LEFT JOIN vtiger_ticketcf ON (vtiger_ticketcf.ticketid = vtiger_crmentity.crmid  ) 
				LEFT JOIN vtiger_ticketcomments ON (vtiger_ticketcomments.ticketid = vtiger_crmentity.crmid  ) 
				LEFT JOIN vtiger_crmentity_user_field ON (vtiger_crmentity_user_field.recordid = vtiger_crmentity.crmid  
				AND vtiger_crmentity_user_field.userid = ? ) 
				WHERE  vtiger_crmentity.crmid= ?";

		$result = $adb->pquery($sql,array($currentUserModel->getId(),$hpid));

		$log->debug("controlando que tenga datos");

		$nro = $adb->query_result($result, 0, 'nro');

		/******************************************************/

		$log->info("enviando");
		$log->info($to);
		$content = $request->getRaw('description');
		//$subject = utf8_decode(decode_html());
		$subject = decodeTildes($request->get('subject'));
		$subject = eliminar_acentos($subject);
		$log->info($signature);
		//$subject = $tema . " - " . $subject;
		$processedContent = Emails_Mailer_Model::getProcessedContent($content); // To remove script tags
		$mailerInstance = Emails_Mailer_Model::getInstance();
		$processedContentWithURLS = utf8_decode(decode_html($mailerInstance->convertToValidURL($processedContent)));
		$recordModel->set('description', $processedContentWithURLS);
		$recordModel->set('subject',$nro ." - " .$subject);
		$recordModel->set('toMailNamesList',$request->get('toMailNamesList'));
		$recordModel->set('saved_toid', $to);
		$recordModel->set('ccmail', $request->get('cc'));
		$recordModel->set('bccmail', $request->get('bcc'));
		$recordModel->set('assigned_user_id', $currentUserModel->getId());
		$recordModel->set('email_flag', $flag);
		$recordModel->set('documentids', $documentIds);
		$recordModel->set('signature',$signature);
		

		$recordModel->set('toemailinfo', $toMailInfo);
		foreach($toMailInfo as $recordId=>$emailValueList) {
			if($recordModel->getEntityType($recordId) == 'Users'){
				$parentIds .= $recordId.'@-1|';
			}else{
				$parentIds .= $recordId.'@1|';
			}
		}
		$recordModel->set('parent_id', $parentIds);

		//save_module still depends on the $_REQUEST, need to clean it up
		$_REQUEST['parent_id'] = $parentIds;

		$success = false;
		$viewer = $this->getViewer($request);
		if ($recordModel->checkUploadSize($documentIds)) {
			// Fix content format acceptable to be preserved in table.
			$decodedHtmlDescriptionToSend = $recordModel->get('description');
			$recordModel->set('description', to_html($decodedHtmlDescriptionToSend));
			$recordModel->save();

			// Restore content to be dispatched through HTML mailer.
			$recordModel->set('description', $decodedHtmlDescriptionToSend);

			// To add entry in ModTracker for email relation
			$emailRecordId = $recordModel->getId();
			foreach ($toMailInfo as $recordId => $emailValueList) {
				$relatedModule = $recordModel->getEntityType($recordId);
				if (!empty($relatedModule) && $relatedModule != 'Users') {
					$relatedModuleModel = Vtiger_Module_Model::getInstance($relatedModule);
					$relationModel = Vtiger_Relation_Model::getInstance($relatedModuleModel, $recordModel->getModule());
					if ($relationModel) {
						$relationModel->addRelation($recordId, $emailRecordId);
					}
				}
			}
			// End

			//To Handle existing attachments
			$current_user = Users_Record_Model::getCurrentUserModel();
			$ownerId = $recordModel->get('assigned_user_id');
			$date_var = date("Y-m-d H:i:s");
			if(is_array($existingAttachments)) {
				foreach ($existingAttachments as $index =>  $existingAttachInfo) {
					/**
					 * For download or send email filename should not be in encoded format (md5)
					 * Ex: for PDF: if filename - abc_md5(abc).pdf then raw filename - abc.pdf
					 * For Normal documents: rawFileName is not exist in the attachments info. So it fallback to normal filename
					 */
					$rawFileName = $existingAttachInfo['storedname'];
					if (!$rawFileName) {
						$rawFileName = $existingAttachInfo['attachment'];
					}
					$file_name = $existingAttachInfo['attachment'];
					$path = $existingAttachInfo['path'];
					$fileId = $existingAttachInfo['fileid'];

					$oldFileName = $file_name;
					//SEND PDF mail will not be having file id
					if(!empty ($fileId)) {
						$oldFileName = $existingAttachInfo['fileid'].'_'.$file_name;
					}
					$oldFilePath = $path.'/'.$oldFileName;

					$binFile = sanitizeUploadFileName($rawFileName, $upload_badext);

					$current_id = $adb->getUniqueID("vtiger_crmentity");

					$filename = ltrim(basename(" " . $binFile)); //allowed filename like UTF-8 characters
					$filetype = $existingAttachInfo['type'];
					$filesize = $existingAttachInfo['size'];

					//get the file path inwhich folder we want to upload the file
					$upload_file_path = decideFilePath();
					$newFilePath = $upload_file_path . $current_id . "_" . $binFile;

					copy($oldFilePath, $newFilePath);

					$sql1 = "insert into vtiger_crmentity (crmid,smcreatorid,smownerid,setype,description,createdtime,modifiedtime) values(?, ?, ?, ?, ?, ?, ?)";
					$params1 = array($current_id, $current_user->getId(), $ownerId, $moduleName . " Attachment", $recordModel->get('description'), $adb->formatDate($date_var, true), $adb->formatDate($date_var, true));
					$adb->pquery($sql1, $params1);

					$sql2 = "insert into vtiger_attachments(attachmentsid, name, description, type, path) values(?, ?, ?, ?, ?)";
					$params2 = array($current_id, $filename, $recordModel->get('description'), $filetype, $upload_file_path);
					$result = $adb->pquery($sql2, $params2);

					$sql3 = 'insert into vtiger_seattachmentsrel values(?,?)';
					$adb->pquery($sql3, array($recordModel->getId(), $current_id));
				}
			}
			$success = true;
			if($flag == 'SENT') {
				$status = $recordModel->send();
				if ($status === true) {
					// This is needed to set vtiger_email_track table as it is used in email reporting
					$recordModel->setAccessCountValue();
				} else {
					$success = false;
					$message = $status;
				}
			}

		} else {
			$message = vtranslate('LBL_MAX_UPLOAD_SIZE', $moduleName).' '.vtranslate('LBL_EXCEEDED', $moduleName);
		}
		$viewer->assign('SUCCESS', $success);
		$viewer->assign('MESSAGE', $message);
		$viewer->assign('FLAG', $flag);
		$viewer->assign('MODULE',$moduleName);
		$loadRelatedList = $request->get('related_load');
		if(!empty($loadRelatedList)){
			$viewer->assign('RELATED_LOAD',true);
		}

		global $log,$current_user;
		$log->info('estoy en Emails_MassSaveAjax_View');
		$log->info($request->get('source_module'));
		$log->info($request);
		$usuario_logueado = $current_user->id;
		
		$ids = $request->get('selected_ids');
		
		//agrego esto para que si manda el mail desde HelpDesk, entonces adjunta el mail como comentario
		if ($request->get('source_module')=='HelpDesk'  || $request->get('emailMode') =='reply'){
			//$log->info('estoy en aaaaaa');
			$contenido = strip_tags(decode_html($mailerInstance->convertToValidURL($processedContent)));
			$log->info($contenido);
			$id_helpdesk 	=$ids[0];
			$email_to = $request->get('toEmail');
			//$log->info('estoy en aaaaaa '.$email_to);
			$id_email 		=$recordModel->getId();
			$this->adjuntarMailEnComentario($id_helpdesk,$id_email,$email_to);
			$log->info($email_to[0]);
			//agrego el mail a la relacion incidencia - email
			$insert_rel = "INSERT INTO vtiger_crmentityrel (crmid, module, relcrmid, relmodule) VALUES(?,?,?,?)";
			$adb->pquery($insert_rel, array($id_helpdesk,'HelpDesk',$emailRecordId,'Emails'));
			$sql = "SELECT atencioneswebid id FROM vtiger_atencionesweb a INNER JOIN vtiger_crmentity e ON e.crmid = a.atencioneswebid AND e.deleted = 0 WHERE a.aw_estado = ? AND e.smownerid = ?";
			$result = $adb->pquery($sql, array('Asignado', $usuario_logueado));
			$atencioneswebid = $adb->query_result($result, 0, 'id');

			//agrego el mail a la relacion atencionweb - email si la atencion esta abierta
			/*$sql = "SELECT DISTINCT vtiger_atencionesweb.atencioneswebid id
					FROM vtiger_atencionesweb 
					INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_atencionesweb.atencioneswebid 
					INNER JOIN vtiger_crmentityrel ON (vtiger_crmentityrel.relcrmid = vtiger_crmentity.crmid OR vtiger_crmentityrel.crmid = vtiger_crmentity.crmid) 
					LEFT JOIN vtiger_atencioneswebcf ON vtiger_atencioneswebcf.atencioneswebid = vtiger_atencionesweb.atencioneswebid 
					LEFT JOIN vtiger_users ON vtiger_users.id = vtiger_crmentity.smownerid 
					LEFT JOIN vtiger_groups ON vtiger_groups.groupid = vtiger_crmentity.smownerid  
					WHERE vtiger_crmentity.deleted = 0 AND (vtiger_crmentityrel.crmid = ? OR vtiger_crmentityrel.relcrmid = ?)
					AND aw_estado = ? ";
			$result = $adb->pquery($sql, array($id_helpdesk,$id_helpdesk,'Asignado'));	

			while($fila = $adb->fetch_array($result)){

								 $atencioneswebid =  $fila['id'];
							}	*/

			//Asocio la consultaweb a la atencionweb
		    $insert = "INSERT INTO vtiger_crmentityrel (crmid, module, relcrmid, relmodule) VALUES(?,?,?,?)";
		   	$adb->pquery($insert, array($id_helpdesk,'HelpDesk',$atencioneswebid,'AtencionesWeb'));

			$cant_row = $adb->num_rows($result);
			if ($cant_row > 0){

				$insert_rel = "INSERT INTO vtiger_crmentityrel (crmid, module, relcrmid, relmodule) VALUES(?,?,?,?)";
				$adb->pquery($insert_rel, array($atencioneswebid,'AtencionesWeb',$emailRecordId,'Emails'));

			}

			if($status && $atencioneswebid){
				$recordModelAten = Vtiger_Record_Model::getInstanceById($atencioneswebid, "AtencionesWeb");
				$recordModelAten->ContarMails(true);
			}



		}

		

		$viewer->view('SendEmailResult.tpl', $moduleName);
	}

	public function adjuntarMailEnComentario($id_helpdesk,$id_email,$email){
		global $log,$adb;
		$log->info("en la funcion adjuntarMailEnComentario");
		$log->info("el id_helpdesk es $id_helpdesk");
		$log->info("el id_email es $id_email");
		$log->info("el contenido es: $email");
		
		$comment = ModComments_Record_Model::getCleanInstance("ModComments");
		$comment->set('mode', 'create');
		$comment->set('related_to', $id_helpdesk);
		$comment->set('commentcontent','Respuesta enviada a '. trim($email));
		$comment->save();	
		$id_comentario= $comment->getId();
		$log->info("el id del comentario es: $id_comentario");




		//vamos a obtener los id de los adjuntos que se mandaron en el mail
		//y luego se lo agregamos al id del comentario 
		//(LOS ADJUNTOS QUE SE ENVIARON EN EL MAIL, YA ESTAN EN EL FILESYSTEM, YA QUE ESA FUNCIONALIDAD LO HACE CON LAS FUNCIONES ESTANDARES)
		//POR LO QUE SOLO HAY QUE AGREGAR LOS IDS (attachmentsid) Y TAMBIEN AGREGARSELO AL COMENTARIO ($id_comentario)
		$consulta ="SELECT attachmentsid
					FROM vtiger_seattachmentsrel
					WHERE crmid=?";
		$result=$adb->pquery($consulta,array($id_email)); 
		while ($fila = $adb->fetch_array($result)) {
			$query ="INSERT INTO vtiger_seattachmentsrel VALUES(?,?)";
			$adb->pquery($query,array($id_comentario,$fila['attachmentsid'])); 
		}
		
	}

	/**
	 * Function returns the record Ids selected in the current filter
	 * @param Vtiger_Request $request
	 * @return integer
	 */
	public function getRecordsListFromRequest(Vtiger_Request $request) {
		$cvId = $request->get('viewname');
		$selectedIds = $request->get('selected_ids');
		$excludedIds = $request->get('excluded_ids');

		if(!empty($selectedIds) && $selectedIds != 'all') {
			if(!empty($selectedIds) && count($selectedIds) > 0) {
				return $selectedIds;
			}
		}

		if($selectedIds == 'all'){
			$sourceRecord = $request->get('sourceRecord');
			$sourceModule = $request->get('sourceModule');
			if ($sourceRecord && $sourceModule) {
				$sourceRecordModel = Vtiger_Record_Model::getInstanceById($sourceRecord, $sourceModule);
				return $sourceRecordModel->getSelectedIdsList($request->get('parentModule'), $excludedIds);
			}

			$customViewModel = CustomView_Record_Model::getInstanceById($cvId);
			if($customViewModel) {
				$searchKey = $request->get('search_key');
				$searchValue = $request->get('search_value');
				$operator = $request->get('operator');
				if(!empty($operator)) {
					$customViewModel->set('operator', $operator);
					$customViewModel->set('search_key', $searchKey);
					$customViewModel->set('search_value', $searchValue);
				}
				return $customViewModel->getRecordIds($excludedIds);
			}
		}
		return array();
	}

	public function validateRequest(Vtiger_Request $request) {
		$request->validateWriteAccess();
	}
}
