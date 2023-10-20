<?php

class LudereProVtiger_RelationListView_Model extends Vtiger_RelationListView_Model {

	public function getEntries($pagingModel) {
		$db = PearDatabase::getInstance();
		$parentModule = $this->getParentRecordModel()->getModule();
                $parentModuleName = $parentModule->get('name');
		$relationModule = $this->getRelationModel()->getRelationModuleModel();
		$relationModuleName = $relationModule->get('name');
		$relatedColumnFields = $relationModule->getConfigureRelatedListFields();
		if(count($relatedColumnFields) <= 0){
			$relatedColumnFields = $relationModule->getRelatedListFields();
		}

		if($relationModuleName == 'Calendar') {
			//Adding visibility in the related list, showing records based on the visibility
			$relatedColumnFields['visibility'] = 'visibility';
		}

		if($relationModuleName == 'PriceBooks') {
			//Adding fields in the related list
			$relatedColumnFields['unit_price'] = 'unit_price';
			$relatedColumnFields['listprice'] = 'listprice';
			$relatedColumnFields['currency_id'] = 'currency_id';
		}

		$query = $this->getRelationQuery();

		if ($this->get('whereCondition') && is_array($this->get('whereCondition'))) {
			$currentUser = Users_Record_Model::getCurrentUserModel();
			$queryGenerator = new QueryGenerator($relationModuleName, $currentUser);
			$queryGenerator->setFields(array_values($relatedColumnFields));
			$whereCondition = $this->get('whereCondition');
			foreach ($whereCondition as $fieldName => $fieldValue) {
				if (is_array($fieldValue)) {
					$comparator = $fieldValue[1];
					$searchValue = $fieldValue[2];
					$type = $fieldValue[3];
					if ($type == 'time') {
						$searchValue = Vtiger_Time_UIType::getTimeValueWithSeconds($searchValue);
					}
					$queryGenerator->addCondition($fieldName, $searchValue, $comparator, "AND");
				}
			}
			$whereQuerySplit = split("WHERE", $queryGenerator->getWhereClause());
			if($parentModuleName == 'Accounts' && $relationModuleName == 'Calendar' && (stripos($query, "GROUP BY") !== false)) {
                            $splitQuery = split('GROUP BY', $query);
                            $query = $splitQuery[0]." AND ".$whereQuerySplit[1].' GROUP BY '.$splitQuery[1];
                        } else {
                            $query.=" AND " . $whereQuerySplit[1];
                        }
		}

		$startIndex = $pagingModel->getStartIndex();
		$pageLimit = $pagingModel->getPageLimit();

		$orderBy = $this->getForSql('orderby');
		$sortOrder = $this->getForSql('sortorder');
		$orderByClause = '';

		if($orderBy) {

			$orderByFieldModuleModel = $relationModule->getFieldByColumn($orderBy);
			if($orderByFieldModuleModel && $orderByFieldModuleModel->isReferenceField()) {
				//If reference field then we need to perform a join with crmentity with the related to field
				$queryComponents = $split = preg_split('/ where /i', $query);
				$selectAndFromClause = $queryComponents[0];
				$whereCondition = $queryComponents[1];
				$qualifiedOrderBy = 'vtiger_crmentity'.$orderByFieldModuleModel->get('column');
				$selectAndFromClause .= ' LEFT JOIN vtiger_crmentity AS '.$qualifiedOrderBy.' ON '.
										$orderByFieldModuleModel->get('table').'.'.$orderByFieldModuleModel->get('column').' = '.
										$qualifiedOrderBy.'.crmid ';
				$query = $selectAndFromClause.' WHERE '.$whereCondition;
				$query .= ' ORDER BY '.$qualifiedOrderBy.'.label '.$sortOrder;
				$orderByClause = $qualifiedOrderBy.'.label';
			} elseif($orderByFieldModuleModel && $orderByFieldModuleModel->isOwnerField()) {
				$query .= ' ORDER BY COALESCE(vtiger_users.userlabel,vtiger_groups.groupname) '.$sortOrder;
				$orderByClause = 'COALESCE(vtiger_users.userlabel,vtiger_groups.groupname)';
			} else{
				// Qualify the the column name with table to remove ambugity
				$qualifiedOrderBy = $orderBy;
				$orderByField = $relationModule->getFieldByColumn($orderBy);
				if ($orderByField) {
					$qualifiedOrderBy = $relationModule->getOrderBySql($qualifiedOrderBy);
				}
				if($qualifiedOrderBy == 'vtiger_activity.date_start' && ($relationModuleName == 'Calendar' || $relationModuleName == 'Emails')) {
					$qualifiedOrderBy = "str_to_date(concat(vtiger_activity.date_start,vtiger_activity.time_start),'%Y-%m-%d %H:%i:%s')";
				}
				$query = "$query ORDER BY $qualifiedOrderBy $sortOrder";
				$orderByClause = $qualifiedOrderBy;
			}
		} else if($relationModuleName == 'HelpDesk' && empty($orderBy) && empty($sortOrder) && $moduleName != "Users") {
			$query .= ' ORDER BY vtiger_crmentity.modifiedtime DESC';
			$orderByClause = 'vtiger_crmentity.modifiedtime';
		}

		//para que no de error, tengo que asegurarme que la condicion del order by este en el select
		//este bug seria consecuencia de que ONLY_FULL_GROUP_BY este habilitado
		if (stripos($query, ' order by ')) {
			$split = preg_split('/ from /i', $query);
			if (!stripos($split[0], $orderByClause)) {
				$selectWithOrderByClause = $split[0]. ',' . $orderByClause;
				$query = $selectWithOrderByClause . ' FROM ' . $split[1];
			}
		}

		$limitQuery = $query .' LIMIT '.$startIndex.','.$pageLimit;

		$result = $db->pquery($limitQuery, array());
		$relatedRecordList = array();
		$currentUser = Users_Record_Model::getCurrentUserModel();
		$groupsIds = Vtiger_Util_Helper::getGroupsIdsForUsers($currentUser->getId());
		$recordsToUnset = array();
		for($i=0; $i< $db->num_rows($result); $i++ ) {
			$row = $db->fetch_row($result,$i);
			$newRow = array();
			foreach($row as $col=>$val){
				if(array_key_exists($col,$relatedColumnFields)){
					$newRow[$relatedColumnFields[$col]] = $val;
				}
			}
			//To show the value of "Assigned to"
			$ownerId = $row['smownerid'];
			$newRow['assigned_user_id'] = $row['smownerid'];
			if($relationModuleName == 'Calendar') {
				$visibleFields = array('activitytype','date_start','time_start','due_date','time_end','assigned_user_id','visibility','smownerid','parent_id');
				$visibility = true;
				if(in_array($ownerId, $groupsIds)) {
					$visibility = false;
				} else if($ownerId == $currentUser->getId()){
					$visibility = false;
				}
				if(!$currentUser->isAdminUser() && $newRow['activitytype'] != 'Task' && $newRow['visibility'] == 'Private' && $ownerId && $visibility) {
					foreach($newRow as $data => $value) {
						if(in_array($data, $visibleFields) != -1) {
							unset($newRow[$data]);
						}
					}
					$newRow['subject'] = vtranslate('Busy','Events').'*';
				}
				if($newRow['activitytype'] == 'Task') {
					unset($newRow['visibility']);
				}

			}

			$record = Vtiger_Record_Model::getCleanInstance($relationModule->get('name'));
			$record->setData($newRow)->setModuleFromInstance($relationModule)->setRawData($row);
			$record->setId($row['crmid']);
			$relatedRecordList[$row['crmid']] = $record;
			if($relationModuleName == 'Calendar' && !$currentUser->isAdminUser() && $newRow['activitytype'] == 'Task' && isToDoPermittedBySharing($row['crmid']) == 'no') { 
				$recordsToUnset[] = $row['crmid'];
			}
		}
		$pagingModel->calculatePageRange($relatedRecordList);

		$nextLimitQuery = $query. ' LIMIT '.($startIndex+$pageLimit).' , 1';
		$nextPageLimitResult = $db->pquery($nextLimitQuery, array());
		if($db->num_rows($nextPageLimitResult) > 0){
			$pagingModel->set('nextPageExists', true);
		}else{
			$pagingModel->set('nextPageExists', false);
		}
		//setting related list view count before unsetting permission denied records - to make sure paging should not fail
		$pagingModel->set('_relatedlistcount', count($relatedRecordList));
		foreach($recordsToUnset as $record) {
			unset($relatedRecordList[$record]);
		}

		return $relatedRecordList;
	}

}