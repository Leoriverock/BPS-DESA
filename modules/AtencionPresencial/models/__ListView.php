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
class AtencionPresencial_ListView_Model extends Vtiger_ListView_Model {

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
		//$log->debug("searchParams".$searchParams);

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
		$searchValue = $this->get('search_value');
		$operator = $this->get('operator');
		if(!empty($searchKey)) {
			$queryGenerator->addUserSearchConditions(array('search_field' => $searchKey, 'search_text' => $searchValue, 'operator' => $operator));
		}

        
        $orderBy = $this->getForSql('orderby');
		$sortOrder = $this->getForSql('sortorder');

		//List view will be displayed on recently created/modified records
		if(empty($orderBy) && empty($sortOrder) && $moduleName != "AtencionesWeb"){
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
		$usuario_logueado = $current_user->id;
		$rol_usuario = $current_user->roleid;
		
		$sql = 'SELECT equipo FROM vtiger_users WHERE id = ? AND deleted = 0';
		$result = $db->pquery($sql, array($usuario_logueado));
		if($db->num_rows($result) > 0){
			$conectado = $db->query_result($result, 0, 'equipo');	
		}
		if(!$conectado){
			$log->info("no conectado");
			$listQuery.= ' LIMIT 0';
		}


		

		$viewid = ListViewSession::getCurrentView($moduleName);
		if(empty($viewid)) {
            $viewid = $pagingModel->get('viewid');
		}
        $_SESSION['lvs'][$moduleName][$viewid]['start'] = $pagingModel->get('page');

		ListViewSession::setSessionQuery($moduleName, $listQuery, $viewid);

		
				
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