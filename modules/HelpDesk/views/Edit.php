<?php

Class HelpDesk_Edit_View extends Vtiger_Edit_View {

	public function process(Vtiger_Request $request) {
		$viewer = $this->getViewer ($request);
		$moduleName = $request->getModule();
		$record = $request->get('record');
		if(!empty($record) && $request->get('isDuplicate') == true) {
			$recordModel = $this->record?$this->record:Vtiger_Record_Model::getInstanceById($record, $moduleName);
			$viewer->assign('MODE', '');

			//While Duplicating record, If the related record is deleted then we are removing related record info in record model
			$mandatoryFieldModels = $recordModel->getModule()->getMandatoryFieldModels();
			foreach ($mandatoryFieldModels as $fieldModel) {
				if ($fieldModel->isReferenceField()) {
					$fieldName = $fieldModel->get('name');
					if (Vtiger_Util_Helper::checkRecordExistance($recordModel->get($fieldName))) {
						$recordModel->set($fieldName, '');
					}
				}
			}  
		}else if(!empty($record)) {
			$recordModel = $this->record?$this->record:Vtiger_Record_Model::getInstanceById($record, $moduleName);
			$viewer->assign('RECORD_ID', $record);
			$viewer->assign('MODE', 'edit');
		} else {
			$recordModel = Vtiger_Record_Model::getCleanInstance($moduleName);
			$viewer->assign('MODE', '');
		}
		if(!$this->record){
			$this->record = $recordModel;
		}

		$moduleModel = $recordModel->getModule();
		$fieldList = $moduleModel->getFields();
		$requestFieldList = array_intersect_key($request->getAllPurified(), $fieldList);

		$relContactId = $request->get('contact_id');
		$contact_es_empresa = false;
		if ($relContactId && $moduleName == 'Calendar') {
			$contactRecordModel = Vtiger_Record_Model::getInstanceById($relContactId);
			$requestFieldList['parent_id'] = $contactRecordModel->get('account_id');
			$contact_es_empresa = !!intval($contactRecordModel->get("acccontexternalnumber"));
			if ($contact_es_empresa) {
				$requestFieldList['parent_id'] = $relContactId;
			}
		}

		foreach($requestFieldList as $fieldName=>$fieldValue){
			$fieldModel = $fieldList[$fieldName];
			if ($contact_es_empresa && $fieldName === "contact_id") continue;
			$specialField = false;
			// We collate date and time part together in the EditView UI handling 
			// so a bit of special treatment is required if we come from QuickCreate 
			if ($moduleName == 'Calendar' && empty($record) && $fieldName == 'time_start' && !empty($fieldValue)) { 
				$specialField = true; 
				// Convert the incoming user-picked time to GMT time 
				// which will get re-translated based on user-time zone on EditForm 
				$fieldValue = DateTimeField::convertToDBTimeZone($fieldValue)->format("H:i"); 

			}

			if ($moduleName == 'Calendar' && empty($record) && $fieldName == 'date_start' && !empty($fieldValue)) { 
				$startTime = Vtiger_Time_UIType::getTimeValueWithSeconds($requestFieldList['time_start']);
				$startDateTime = Vtiger_Datetime_UIType::getDBDateTimeValue($fieldValue." ".$startTime);
				list($startDate, $startTime) = explode(' ', $startDateTime);
				$fieldValue = Vtiger_Date_UIType::getDisplayDateValue($startDate);
			}
			if($fieldModel->isEditable() || $specialField) {
				$recordModel->set($fieldName, $fieldModel->getDBInsertValue($fieldValue));
			}
		}

		//Atenciones o Llamadas activas
		$currentUser = Users_Record_Model::getCurrentUserModel();
		$atencionWeb = $currentUser->getAtencionActiva();
		$llamada = Calls_Module_Model::getLlamadaActiva(Users_Record_Model::getCurrentUserModel()->id); 
		$AtencionPresencial = AtencionPresencial_Module_Model::getAtencionpPActiva(Users_Record_Model::getCurrentUserModel()->id);

		// para controlar si hay una llamada activa y es una nueva incidencia => precargo la cuenta y no permito editarla
		
		if ($llamada && !$atencionWeb && !$AtencionPresencial) {
			// solo editar el secontacta cunado se esta creando
			if(empty($record)) $recordModel->set('contact_id', $llamada['callaccount']);
			if (!$recordModel->get('parent_id')) {
				$recordModel->set('parent_id', $llamada['callaccount']);
			}
			$viewer->assign('HAY_LLAMADA_ACTIVA', is_null($recordModel->getId()));
			// TT2621 precargo el canal Teléfono solo si hay llamada activa y es la creacion de un ticket
			if (is_null($recordModel->getId())) {
				$recordModel->set('ticketcanal', 'Teléfono');
			}
		}

		// Si al crear un ticket hay una atencion web activa
		
		if(empty($record) && $atencionWeb && !$llamada && !$AtencionPresencial) {
			$recordAtencion = Vtiger_Record_Model::getInstanceById($atencionWeb, "AtencionesWeb");
			$aw_persona = $recordAtencion->get("aw_persona");
			$recordModel->set('contact_id', $aw_persona);
			$aw_cont_empresa = $recordAtencion->get("aw_cont_empresa");
			if(intval($aw_cont_empresa) > 0) {
				try {
					$empresa = Accounts_Record_Model::getInstanceBySearch(array(
						"acccontexternalnumber" => "",
						"accempexternalnumber" => $aw_cont_empresa,
						"acccountry" => NULL,
						"accdocumenttype" => "Documento",
					));
					if ($empresa) {
						$aw_cont_aportacion = $recordAtencion->get("aw_cont_aportacion");
						$tipoaportacion = combo($aw_cont_aportacion);
						$recordModel->set('parent_id', $empresa->getId());
						$recordModel->set('ticketnumeroexterno', $aw_cont_empresa);
						$recordModel->set('ticketcodigoaportacion', $tipoaportacion);
					}
				} catch(Exception $e) {

				}
			} else {
				$recordModel->set('parent_id', $aw_persona);
			}
			$aw_tema = $recordAtencion->get("aw_tema");
			$recordModel->set('tickettema', $aw_tema);
			$recordModel->set('ticketcanal', "Mail");
		}

		// Si al crear un ticket hay una atencion presencial activa
		
		if ($AtencionPresencial && !$llamada && !$atencionWeb) {
			// solo editar el secontacta cunado se esta creando
			if(empty($record)) $recordModel->set('contact_id', $AtencionPresencial['ap_persona']);
			if (!$recordModel->get('parent_id')) {
				$recordModel->set('parent_id', $AtencionPresencial['ap_persona']);
			}
			$viewer->assign('HAY_ATENCIONPRESENCIAL_ACTIVA', is_null($recordModel->getId()));
			if (is_null($recordModel->getId())) {
				$recordModel->set('ticketcanal', 'Presencial');
			}
			
		}

		if(!$llamada && !$AtencionPresencial && !$atencionWeb && empty($record)){
			if ( $recordModel->get('parent_id') ){
				//Checkear primero si es un contribuyente o no
				if( !Accounts_Module_Model::isContribuyente($recordModel->get('parent_id')) )
					$recordModel->set('contact_id', $recordModel->get('parent_id'));
			} else {
				//Si no hay parent_id, y viene de la vista relacionada de Accounts, y no es contribuyente, completamos el En Nombre de
				if ( $request->get('returnmodule') == "Accounts" && !empty($request->get('returnrecord')) ){
					//Checkear primero si es un contribuyente o no
					if( !Accounts_Module_Model::isContribuyente($request->get('returnrecord')) )
						$recordModel->set('parent_id', $request->get('returnrecord'));
				}
			}
		}


		$recordStructureInstance = Vtiger_RecordStructure_Model::getInstanceFromRecordModel($recordModel, Vtiger_RecordStructure_Model::RECORD_STRUCTURE_MODE_EDIT);
		$picklistDependencyDatasource = Vtiger_DependencyPicklist::getPicklistDependencyDatasource($moduleName);

		$viewer->assign('PICKIST_DEPENDENCY_DATASOURCE',Vtiger_Functions::jsonEncode($picklistDependencyDatasource));
		$viewer->assign('RECORD_STRUCTURE_MODEL', $recordStructureInstance);
		$viewer->assign('RECORD_STRUCTURE', $recordStructureInstance->getStructure());
		$viewer->assign('MODULE', $moduleName);
		$viewer->assign('CURRENTDATE', date('Y-n-j'));
		$viewer->assign('USER_MODEL', Users_Record_Model::getCurrentUserModel());

		$isRelationOperation = $request->get('relationOperation');

		//if it is relation edit
		$viewer->assign('IS_RELATION_OPERATION', $isRelationOperation);
		if($isRelationOperation) {
			$viewer->assign('SOURCE_MODULE', $request->get('sourceModule'));
			$viewer->assign('SOURCE_RECORD', $request->get('sourceRecord'));
		}

		// added to set the return values
		if($request->get('returnview')) {
			$request->setViewerReturnValues($viewer);
		}
		$viewer->assign('MAX_UPLOAD_LIMIT_MB', Vtiger_Util_Helper::getMaxUploadSize());
		$viewer->assign('MAX_UPLOAD_LIMIT_BYTES', Vtiger_Util_Helper::getMaxUploadSizeInBytes());
		if($request->get('displayMode')=='overlay'){
			$viewer->assign('SCRIPTS',$this->getOverlayHeaderScripts($request));
			$viewer->view('OverlayEditView.tpl', $moduleName);
		}
		else{
			$viewer->view('EditView.tpl', $moduleName);
		}
	}
}
