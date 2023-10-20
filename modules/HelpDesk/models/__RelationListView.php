<?php
/* +***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * *********************************************************************************** */

class HelpDesk_RelationListView_Model extends Vtiger_RelationListView_Model {

	public function getCreateViewUrl() {
		$createViewUrl = parent::getCreateViewUrl();
		$parentRecordModule = $this->getParentRecordModel();
		$parentModule = $parentRecordModule->getModule();
		$createViewUrl .= '&relationOperation=true&contact_id='.$parentRecordModule->get('contact_id').'&account_id='.$parentRecordModule->get('parent_id').'&sourceRecord='.$parentRecordModule->getId().'&sourceModule='.$parentModule->getName();
		return $createViewUrl;
	}
	public function getRelationQuery() {
		global $log;
		$log->info("getRelationQuery");
		$relationModel = $this->getRelationModel();

		if(!empty($relationModel) && $relationModel->get('name') != NULL){
			$recordModel = $this->getParentRecordModel();
			$query = $relationModel->getQuery($recordModel);

			//04/08/23 LR - Busco el id para suplantar la consulta

			$patron = "/\b\d+\b/";
			$recordID = '';
			// Buscar todos los números en el string usando expresiones regulares
			if (preg_match_all($patron, $query, $coincidencias)) {
			    $recordID = $coincidencias[0];
			    $log->info($recordID[1]);
			   	
			}

			if (strpos($query, 'vtiger_calls') !== false) {
    			$query = '';

				$log->info("La query del generador: ");
				$query = '	SELECT * FROM (
							SELECT vtiger_calls.*, vtiger_crmentity.*  FROM vtiger_calls 
							INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_calls.callsid 
							INNER JOIN vtiger_crmentityrel ON (vtiger_crmentityrel.relcrmid = vtiger_crmentity.crmid) 
							LEFT JOIN vtiger_callscf ON vtiger_callscf.callsid = vtiger_calls.callsid 
							LEFT JOIN vtiger_users ON vtiger_users.id = vtiger_crmentity.smownerid 
							LEFT JOIN vtiger_groups ON vtiger_groups.groupid = vtiger_crmentity.smownerid
							WHERE vtiger_crmentity.deleted = 0 AND (vtiger_crmentityrel.crmid = '.$recordID[1].' OR vtiger_crmentityrel.relcrmid = '.$recordID[1].' )
							UNION
							SELECT vtiger_calls.*, vtiger_crmentity.*  FROM vtiger_calls 
							INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_calls.callsid 
							INNER JOIN vtiger_crmentityrel ON (vtiger_crmentityrel.crmid = vtiger_crmentity.crmid) 
							LEFT JOIN vtiger_callscf ON vtiger_callscf.callsid = vtiger_calls.callsid 
							LEFT JOIN vtiger_users ON vtiger_users.id = vtiger_crmentity.smownerid 
							LEFT JOIN vtiger_groups ON vtiger_groups.groupid = vtiger_crmentity.smownerid
 							WHERE vtiger_crmentity.deleted = 0 AND (vtiger_crmentityrel.crmid = '.$recordID[1].' OR vtiger_crmentityrel.relcrmid = '.$recordID[1].')) as dat INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = dat.callsid 
							'; 
							/*) AS dat*/

			}
			return $query;
		}
		$relatedModuleModel = $this->getRelatedModuleModel();
		$relatedModuleName = $relatedModuleModel->getName();

		$relatedModuleBaseTable = $relatedModuleModel->basetable;
		$relatedModuleEntityIdField = $relatedModuleModel->basetableid;

		$parentModuleModel = $relationModel->getParentModuleModel();
		$parentModuleBaseTable = $parentModuleModel->basetable;
		$parentModuleEntityIdField = $parentModuleModel->basetableid;
		$parentRecordId = $this->getParentRecordModel()->getId();
		$parentModuleDirectRelatedField = $parentModuleModel->get('directRelatedFieldName');

		$relatedModuleFields = array_keys($this->getHeaders());
		$currentUserModel = Users_Record_Model::getCurrentUserModel();
		$queryGenerator = new QueryGenerator($relatedModuleName, $currentUserModel);
		$queryGenerator->setFields($relatedModuleFields);

		$query = $queryGenerator->getQuery();

		$queryComponents = preg_split('/ FROM /i', $query);
		$query = $queryComponents[0].' ,vtiger_crmentity.crmid FROM '.$queryComponents[1];

		$whereSplitQueryComponents = preg_split('/ WHERE /i', $query);
		$joinQuery = ' INNER JOIN '.$parentModuleBaseTable.' ON '.$parentModuleBaseTable.'.'.$parentModuleDirectRelatedField." = ".$relatedModuleBaseTable.'.'.$relatedModuleEntityIdField;

		$query = "$whereSplitQueryComponents[0] $joinQuery WHERE $parentModuleBaseTable.$parentModuleEntityIdField = $parentRecordId AND $whereSplitQueryComponents[1]";

		return $query;
	}


	public function getRelatedEntriesCount() {
		global $log;
		$log->info("entro getRelatedEntriesCount");
		$db = PearDatabase::getInstance();
		$currentUser = Users_Record_Model::getCurrentUserModel();
		$realtedModuleModel = $this->getRelatedModuleModel();
		$relatedModuleName = $realtedModuleModel->getName();
		$relationQuery = $this->getRelationQuery();
		$relationQuery = preg_replace("/[ \t\n\r]+/", " ", $relationQuery);
		$position = stripos($relationQuery,' from ');
		if ($position) {
			$split = preg_split('/ FROM /i', $relationQuery);
			$splitCount = count($split);
			if($relatedModuleName == 'Calendar') {
				$relationQuery = 'SELECT DISTINCT vtiger_crmentity.crmid, vtiger_activity.activitytype ';
			} else {
				
				$relationQuery = 'SELECT COUNT(DISTINCT vtiger_crmentity.crmid) AS count';
				/*if (strpos($query, 'vtiger_calls') !== false) {
					$relationQuery = 'SELECT COUNT(DISTINCT crmid) AS count';
				}*/
			}
			for ($i=1; $i<$splitCount; $i++) {
				$relationQuery = $relationQuery. ' FROM ' .$split[$i];
			}
		}
		if(strpos($relationQuery,' GROUP BY ') !== false){
			$parts = explode(' GROUP BY ',$relationQuery);
			$relationQuery = $parts[0];
		}
		$result = $db->pquery($relationQuery, array());
		$log->info("esta haciendo esta query");
		$log->info($relationQuery);
		if ($result) {
			if($relatedModuleName == 'Calendar') {
				$count = 0;
				for($i=0;$i<$db->num_rows($result);$i++) {
					$id = $db->query_result($result, $i, 'crmid');
					$activityType = $db->query_result($result, $i, 'activitytype');
					if(!$currentUser->isAdminUser() && $activityType == 'Task' && isToDoPermittedBySharing($id) == 'no') {
						continue;
					} else {
						$count++;
					}
				}
				return $count;
			} else {
				return $db->query_result($result, 0, 'count');
			}
		} else {
			return 0;
		}
	}

}

?>
