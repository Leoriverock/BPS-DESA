<?php

/* +***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * *********************************************************************************** */

class LPTempCampos_ExportData_Action extends Vtiger_ExportData_Action {


    /**
     * Function exports the data based on the mode
     * @param Vtiger_Request $request
     */
    function ExportData(Vtiger_Request $request) {
        $db = PearDatabase::getInstance();
        $moduleName = $request->get('source_module');
        $cvId = $request->get('viewname');
        $pageNumber = $request->get('page');

        $searchParams = $request->get('search_params');
        if (empty($pageNumber)) {
            $pageNumber = '1';
        }

        $pagingModel = new Vtiger_Paging_Model();
        $pagingModel->set('page', $pageNumber);
        
        $this->moduleInstance = Vtiger_Module_Model::getInstance($moduleName);
        $this->moduleFieldInstances = $this->moduleInstance->getFields();
        $this->focus = CRMEntity::getInstance($moduleName);

        $query = $this->getExportQuery($request);
        $result = $db->pquery($query, array());

        $mode = $request->getMode();
        if ($mode === 'ExportCustomFlows') {
                        
            $listViewModel = Vtiger_ListView_Model::getInstance($moduleName, $cvId);
            $columnas = $listViewModel->getListViewHeaders();
            $datos = $listViewModel->getListViewEntriesFilter($pagingModel,$searchParams);
            
            $idList = $this->getRecordsListFromRequest($request);
            $excludes = $request->get('excluded_ids');
            
            $entries = array();
            foreach ($datos as $valueDatos) {
                $rawData = $valueDatos->rawData;
                $lptempcamposid = $rawData["lptempcamposid"];
                $entry["tc_nombre"] = $rawData["tc_nombre"];
                $entry["tc_modulo"] = $rawData["tc_modulo"];
                $entry["tc_campo"] = $rawData["tc_campo"];
                
                $fieldname_sql = $db->pquery("SELECT fieldname FROM vtiger_field WHERE fieldid = ?", array($rawData["tc_campo"]));
                $entry["fieldname"] = $db->query_result($fieldname_sql, 0, 'fieldname');

                if (!empty($idList)) {
                    $va = false;
                    if (in_array($lptempcamposid, $idList)) $va = true;
                } else {
                    $va = true;
                    if (in_array($lptempcamposid, $excludes)) $va = false;
                }
                if ($va) {                    
                    $fields_sql = "SELECT tcd_campo,
                        tcd_obligatorio, tcd_orden, fieldname 
                        FROM vtiger_lptempcamposdetalle as chan 
                        JOIN vtiger_crmentity ON chan.lptempcamposdetalleid = crmid AND NOT deleted
                        JOIN vtiger_field ON fieldid = tcd_campo 
                        WHERE tcd_template = ?";
                    $entry['fields'] = array();
                    $result_fields = $db->pquery($fields_sql, array($lptempcamposid));
                    for ($i = 0; $i < $db->num_rows($result_fields); $i++)
                        $entry['fields'][]=$db->fetchByAssoc($result_fields, $i);
                    $selections_sql = "SELECT ts_valor
                        FROM vtiger_lptempcamposseleccion as chan 
                        JOIN vtiger_crmentity ON chan.lptempcamposseleccionid = crmid AND NOT deleted
                        JOIN vtiger_field ON fieldid = ts_campo 
                        WHERE ts_template = ?";
                    $entry['selections'] = array();
                    $result_selections = $db->pquery($selections_sql, array($lptempcamposid));
                    for ($i = 0; $i < $db->num_rows($result_selections); $i++)
                        $entry['selections'][]=$db->fetchByAssoc($result_selections, $i);
                    $entries[] = $entry;
                }
            }

            $fileName = str_replace(' ', '_', decode_html(vtranslate($moduleName, $moduleName))).".json";

            if (isset($_SERVER['HTTP_USER_AGENT']) && strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE')) {
                header('Pragma: public');
                header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            }

            header('Content-Type: application/json');
            header('Content-disposition: attachment; filename="' . $fileName . '"');
            echo json_encode($entries);

        } else {
            return parent::ExportData($request);
        }

    }


    function getExportQuery(Vtiger_Request $request) {
        $currentUser = Users_Record_Model::getCurrentUserModel();
        $mode = $request->getMode();
        $cvId = $request->get('viewname');
        $moduleName = $request->get('source_module');
        // JA - 20210324 - cambio QueryGenerator por EnhancedQueryGenerator, para que funcione el exportar todo
        $queryGenerator = new EnhancedQueryGenerator($moduleName, $currentUser);
        $queryGenerator->initForCustomViewById($cvId);
        $fieldInstances = $this->moduleFieldInstances;

        $accessiblePresenceValue = array(0, 2);
        foreach ($fieldInstances as $field) {
            // Check added as querygenerator is not checking this for admin users
            $presence = $field->get('presence');
            if (in_array($presence, $accessiblePresenceValue)) {
                $fields[] = $field->getName();
            }
        }
        $queryGenerator->setFields($fields);
        $query = $queryGenerator->getQuery();

        if (in_array($moduleName, getInventoryModules())) {
            $query = $this->moduleInstance->getExportQuery($this->focus, $query);
        }

        $this->accessibleFields = $queryGenerator->getFields();

        switch ($mode) {
            case 'ExportAllData' : return $query;
                break;

            case 'ExportCurrentPage' : $pagingModel = new Vtiger_Paging_Model();
                $limit = $pagingModel->getPageLimit();

                $currentPage = $request->get('page');
                if (empty($currentPage))
                    $currentPage = 1;

                $currentPageStart = ($currentPage - 1) * $limit;
                if ($currentPageStart < 0)
                    $currentPageStart = 0;
                $query .= ' LIMIT ' . $currentPageStart . ',' . $limit;

                return $query;
                break;
            case 'ExportCustomFlows':
            case 'ExportSelectedRecords' : $idList = $this->getRecordsListFromRequest($request);
                $baseTable = $this->moduleInstance->get('basetable');
                $baseTableColumnId = $this->moduleInstance->get('basetableid');
                if (!empty($idList)) {
                    if (!empty($baseTable) && !empty($baseTableColumnId)) {
                        $idList = implode(',', $idList);
                        $query .= ' AND ' . $baseTable . '.' . $baseTableColumnId . ' IN (' . $idList . ')';
                    }
                } else {
                    $query .= ' AND ' . $baseTable . '.' . $baseTableColumnId . ' NOT IN (' . implode(',', $request->get('excluded_ids')) . ')';
                }
                return $query;
                break;


            default : return $query;
                break;
        }
    }    
}