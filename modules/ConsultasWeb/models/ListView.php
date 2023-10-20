<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/
require_once 'config.ludere.php';
/***********************************************************
01/09/23 LR 
Nota: Por reglas de acceso consultasweb modulo queda privado 
Si es perfil agente se ajusta el Limit de la query a 0
************************************************************/
class ConsultasWeb_ListView_Model extends Vtiger_ListView_Model {

	/**
	 * Function to get the list of Mass actions for the module
	 * @param <Array> $linkParams
	 * @return <Array> - Associative array of Link type to List of  Vtiger_Link_Model instances for Mass Actions
	 */
	

	/**
	 * Function to get the list of listview links for the module
	 * @param <Array> $linkParams
	 * @return <Array> - Associate array of Link Type to List of Vtiger_Link_Model instances
	 */
	

	/**
	 * Function to get the list view entries
	 * @param Vtiger_Paging_Model $pagingModel
	 * @return <Array> - Associative array of record id mapped to Vtiger_Record_Model instance.
	 */
	public function getListViewEntries($pagingModel) {
		$db = PearDatabase::getInstance();
		global $log;
		$log->debug("Entre al getListViewEntries de ConsultasWeb");


		$moduleName = $this->getModule()->get('name');
		$moduleFocus = CRMEntity::getInstance($moduleName);
		$moduleModel = Vtiger_Module_Model::getInstance($moduleName);

		$queryGenerator = $this->get('query_generator');
		//$log->debug("El query_generator es $queryGenerator");
		$listViewContoller = $this->get('listview_controller');

		$limite=1;
		$searchParams = $this->get('search_params');
		$log->debug("searchParams".$searchParams);
		$log->info($searchParams);
		$value = '';
		foreach ($searchParams as $element) {
			$columns = $element['columns'];
			foreach ($columns as $column) {	
	   			$value = $column['value'];
	   			$log->info("el value es $value");
	   			if($value == 'Pendiente Agente'){
	   				$condicionPendiente = true; 
	   			}
			}
		}	

		//si no tiene filtros, se define el array $searchParams vacio 
        if(empty($searchParams)) {
            $searchParams = array();
            $limite=0;
        }

        $glue = "";
        
        if(count($queryGenerator->getWhereFields()) > 0 && (count($searchParams)) > 0) {

            $glue = QueryGenerator::$AND;


        }
        $queryGenerator->parseAdvFilterList($searchParams, $glue);
        
		$searchKey = $this->get('search_key');
		$log->info("el values es otro: ");

		$searchValue = $this->get('search_value');
		$log->info($searchValue);
		$operator = $this->get('operator');
		if(!empty($searchKey)) {
			$queryGenerator->addUserSearchConditions(array('search_field' => $searchKey, 'search_text' => $searchValue, 'operator' => $operator));

		}

        
        $orderBy = $this->getForSql('orderby');
		$sortOrder = $this->getForSql('sortorder');

		//List view will be displayed on recently created/modified records
		if(empty($orderBy) && empty($sortOrder) && $moduleName != "ConsultasWeb"){
			$orderBy = 'modifiedtime';
			$sortOrder = 'DESC';
			if (PerformancePrefs::getBoolean('LISTVIEW_DEFAULT_SORTING', true)) {
                                $orderBy = $moduleFocus->default_order_by;
                                $sortOrder = $moduleFocus->default_sort_order;
                        }
		}

        if(!empty($orderBy)){
            $columnFieldMapping = $moduleModel->getColumnFieldMapping();
            $orderByFieldName = $columnFieldMapping[$orderBy];
            $orderByFieldModel = $moduleModel->getField($orderByFieldName);
            if($orderByFieldModel && $orderByFieldModel->getFieldDataType() == Vtiger_Field_Model::REFERENCE_TYPE){
                //IF it is reference add it in the where fields so that from clause will be having join of the table
                $queryGenerator = $this->get('query_generator');
                $queryGenerator->addWhereField($orderByFieldName);
                
                //$queryGenerator->whereFields[] = $orderByFieldName;
            }
        }
		$listQuery = $this->getQuery();

		$sourceModule = $this->get('src_module');
		if(!empty($sourceModule)) {
			if(method_exists($moduleModel, 'getQueryByModuleField')) {
				$overrideQuery = $moduleModel->getQueryByModuleField($sourceModule, $this->get('src_field'), $this->get('src_record'), $listQuery);
				if(!empty($overrideQuery)) {
					$listQuery = $overrideQuery;
				}
			}
		}

		$startIndex = $pagingModel->getStartIndex();
		$pageLimit = $pagingModel->getPageLimit();


		global $current_user;
		$rol_usuario = $current_user->roleid;
		$usuario_logueado = $current_user->id;
		$listview_consultasweb_roles = unserialize(LISTVIEW_CONSULTASWEB_ROLES);
		$listview_consultasweb_roles_sup = unserialize(LISTVIEW_ATENCIONESWEB_ROLES);

		//$listview_consultasweb_roles = array("H7");
		$log->info('Los roles definidos son');
		$log->info($listview_consultasweb_roles);
		$log->info('El rol del usuario es ');
		$log->info($rol_usuario);
		//Obtener los cambos
		$selectCampos = substr($listQuery, 0, strpos($listQuery, "FROM"));
		$selectCampos = str_replace("vtiger_consultasweb", "c2", $selectCampos);


		$existe = in_array($rol_usuario,$listview_consultasweb_roles);

		$existe_supervisor = in_array($rol_usuario,$listview_consultasweb_roles_sup);
		
		$log->info("tiene condicion: ");
		$log->info($listQuery);
		$where_ = "OR (vtiger_users.userlabel) LIKE '%".$value."%' OR vtiger_groups.groupname LIKE '%".$value."%'";
		$where = 'vtiger_crmentity.deleted=0 AND vtiger_consultasweb.consultaswebid > 0';
		$log->info("esto es: ");
		$log->info($where_);
		//if (strpos($listQuery, $where_) != false ) {
		if($existe_supervisor){
			$log->debug("Entra a existe supervisor");
			//$log->info(json_encode(strpos($listQuery, "vtiger_consultasweb.consultaswebid LIKE '")));
			$grupoactual  = "SELECT groupname FROM vtiger_users2group  u
							 INNER JOIN vtiger_groups G ON  G.GROUPID = U.GROUPID
							 WHERE USERID = ?
							 UNION 
							 SELECT CONCAT(first_name,' ',last_name) AS NAME FROM vtiger_users
							 WHERE id = ?
								 ";
			$res = $db->pquery($grupoactual,array($usuario_logueado,$usuario_logueado));
			if($res)
				$listQuery .= " AND (( ";

			while($fila = $db->fetch_array($res)){
				$grupo = trim($fila["groupname"]);
				
				$listQuery .= " cw_grupo LIKE '$grupo' OR cw_grupo LIKE '$grupo' OR ";
			}
			$listQuery = rtrim($listQuery, ' OR ');
			if (strpos($listQuery, $where) == false ) {
				$log->info("Si se cumple, entonces entra");
				//$listQuery .= $where_ ;
				$log->info("Show me the query".$where_);
			}
			
			if ($grupo){
				$listQuery .= " )) ";
			}

			/*$reemplazar = "INNER JOIN vt_tmp_u7 vt_tmp_u7 ON vt_tmp_u7.id = vtiger_crmentity.smownerid";
			$listQuery = str_replace($reemplazar, "", $listQuery);*/

			
			//Trae consulta de supervisores que pertenecen a mis grupos
			/*$listQuery .= "  AND vtiger_crmentity.smownerid IN (SELECT DISTINCT (vtiger_users2group.userid) userid 
			FROM vtiger_users2group
			INNER JOIN vtiger_user2role ON vtiger_users2group.userid = vtiger_user2role.userid AND roleid = 'H6'
			WHERE vtiger_users2group.groupid IN (
			SELECT g.groupid FROM vtiger_users2group g
			WHERE g.userid = 7
			))  ";*/
			//Agregado  03/05/23
			/*$log->info("cual es la condicion $condicionPendiente");
			if (!$searchParams OR $condicionPendiente){
			$listQuery .= " Union ". $selectCampos ."
									FROM vtiger_consultasweb C2
									INNER JOIN vtiger_crmentity ON C2.consultaswebid = vtiger_crmentity.crmid AND deleted = 0
									INNER JOIN vtiger_users2group g ON g.userid = vtiger_crmentity.smownerid
									LEFT JOIN vtiger_users ON vtiger_crmentity.smownerid = vtiger_users.id 
									LEFT JOIN vtiger_groups ON vtiger_crmentity.smownerid = vtiger_groups.groupid
									WHERE g.groupid  IN(

									SELECT g.groupid 
									FROM vtiger_users us
									JOIN vtiger_users2group ug ON ug.userid = us.id
									JOIN vtiger_groups g ON g.groupid = ug.groupid
									WHERE us.id = $usuario_logueado) ";
									if($condicionPendiente){
										$listQuery .= ' AND C2.cw_estado = "Pendiente Agente" ';
									}
								}*/

		
		}

		
		$log->info('existe el rol en la varibale LISTVIEW_CONTACTS_ROLES del config.ludere');
		$log->info(json_encode($existe));


		//si es que no se ha colocado en filtro, se definio limite=0 mas arriba
		if ($limite == 0 && $existe)
			$pageLimit = $limite;
		

		if(!empty($orderBy)) {
            if($orderByFieldModel && $orderByFieldModel->isReferenceField()){
                $referenceModules = $orderByFieldModel->getReferenceList();
                $referenceNameFieldOrderBy = array();
                foreach($referenceModules as $referenceModuleName) {
                    $referenceModuleModel = Vtiger_Module_Model::getInstance($referenceModuleName);
                    $referenceNameFields = $referenceModuleModel->getNameFields();

                    $columnList = array();
                    foreach($referenceNameFields as $nameField) {
                        $fieldModel = $referenceModuleModel->getField($nameField);
                        $columnList[] = $fieldModel->get('table').$orderByFieldModel->getName().'.'.$fieldModel->get('column');
                    }
                    //if(count($columnList) > 1) {
                     //   $referenceNameFieldOrderBy[] = getSqlForNameInDisplayFormat(array('first_name'=>$columnList[0],'last_name'=>$columnList[1]),'Users', '').' '.$sortOrder;
                   // } else {
                        $referenceNameFieldOrderBy[] = implode('', $columnList).' '.$sortOrder ;
                    //}
                }
                $listQuery .= ' ORDER BY '. implode(',',$referenceNameFieldOrderBy);
            }
           
		}

		$viewid = ListViewSession::getCurrentView($moduleName);
		if(empty($viewid)) {
            $viewid = $pagingModel->get('viewid');
		}
        $_SESSION['lvs'][$moduleName][$viewid]['start'] = $pagingModel->get('page');

		ListViewSession::setSessionQuery($moduleName, $listQuery, $viewid);


		$log->debug("La consulta que esta haciendo el usuario es $listQuery");
		$log->info(json_encode(strpos($listQuery, "vtiger_consultasweb.consultaswebid LIKE '")));
		$listQuery .= ' ORDER BY consultaswebid DESC'; //OR cw_estado = "Pendiente Agente" 
		//Preguntamos si es del rol definido en config.ludere y no ha buscado por documento 
		if ($existe && !strpos($listQuery, "vtiger_consultasweb.consultaswebid LIKE '")) {

			
			//$pageLimit = $limite;;
			$log->info('el pageLimit es: '.$pageLimit);
			$log->info('el limite es: '.$limite);
			$listQuery .= " LIMIT $startIndex,0";
		
		}else{

			if ($existe) {
				$consulta = $listQuery;
				$result=$db->pquery($consulta,array()); 
				$numero_filas = $db->num_rows($result);
				$log->info("LA CANTIDAD DE RESULTADOS ES: $numero_filas");
				if ($numero_filas>1) {
					$listQuery .= " LIMIT $startIndex,0";
				}else{
					$listQuery .= " LIMIT $startIndex,".($pageLimit+1);	
				}

			}else
				$listQuery .= " LIMIT $startIndex,".($pageLimit+1);	
		}

		
		$log->debug("La consulta que se ejecuta es $listQuery");

		
		
		$listResult = $db->pquery($listQuery, array());

		$listViewRecordModels = array();
		$listViewEntries =  $listViewContoller->getListViewRecords($moduleFocus,$moduleName, $listResult);

		$pagingModel->calculatePageRange($listViewEntries);

		if($db->num_rows($listResult) > $pageLimit){
			array_pop($listViewEntries);
			$pagingModel->set('nextPageExists', true);
		}else{
			$pagingModel->set('nextPageExists', false);
		}

		$index = 0;
		foreach($listViewEntries as $recordId => $record) {
			$rawData = $db->query_result_rowdata($listResult, $index++);
			$record['id'] = $recordId;
			$listViewRecordModels[$recordId] = $moduleModel->getRecordFromArray($record, $rawData);
		}
		return $listViewRecordModels;
	}
}