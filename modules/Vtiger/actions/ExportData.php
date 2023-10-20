<?php

/* +***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * *********************************************************************************** */
set_time_limit(0);
error_reporting(E_ALL & ~E_STRICT & ~E_NOTICE & ~E_WARNING & ~E_DEPRECATED);
ini_set("display_errors", 1);
class Vtiger_ExportData_Action extends Vtiger_Mass_Action
{

    function checkPermission(Vtiger_Request $request)
    {
        $moduleName = $request->getModule();
        $moduleModel = Vtiger_Module_Model::getInstance($moduleName);

        $currentUserPriviligesModel = Users_Privileges_Model::getCurrentUserPrivilegesModel();
        if (!$currentUserPriviligesModel->hasModuleActionPermission($moduleModel->getId(), 'Export')) {
            throw new AppException('LBL_PERMISSION_DENIED');
        }
    }

    /**
     * Function is called by the controller
     * @param Vtiger_Request $request
     */
    function process(Vtiger_Request $request)
    {
        $this->ExportData($request);
    }

    private $moduleInstance;
    private $focus;

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

        global $log; 
        
        $mode = $request->getMode();

        switch ($mode) {
            case 'ExportFilter' :
            case 'ExportFilterWithParams':

                $listViewModel = Vtiger_ListView_Model::getInstance($moduleName, $cvId);

                $columnas = $listViewModel->getListViewHeaders();
                
                if( $mode == 'ExportFilter' )
                    $searchParams = null;

                $datos = $listViewModel->getListViewEntriesFilter($pagingModel,$searchParams);
    
                $headers = array();
                $column_names = array();
                foreach ($columnas as $value) {
                    $headers[$value->get('name')] = vtranslate($value->get('label'), $moduleName);
                    $column_names[] = $value->get('listViewRawFieldName');
                }

                $campos_picklist = array();

                foreach( $column_names as $column_name ){
                    if( $this->campoEsPicklist($column_name, $moduleName) )
                        $campos_picklist[] = $column_name;
                }

                $translatedHeaders = array();
                foreach ($headers as $headerName => $header)
                    $translatedHeaders[$headerName] = vtranslate(html_entity_decode($header, ENT_QUOTES), $moduleName);
                   
                $entries = array();
                foreach ($datos as $valueDatos) {
                    $fila = array();
                    if ($request->get("export_type") != 'excel') {
                        foreach ($columnas as $valueColumna) {
                            /// correccion para campos relacionados
                            $webserviceField = $valueColumna->get('webserviceField');
                            if ($webserviceField) {
                                $rawData = $valueDatos->rawData;
                                $fila[] = $rawData[$valueColumna->get('listViewRawFieldName')];            
                            } else {
                                $headerName = $valueColumna->get('name');
                                $fila[] = $valueDatos->get($headerName);
                            }
                            // fin correccion
                        }
                    } else {
                        $rawData = $valueDatos->rawData;

                        foreach ($rawData as $key => $value) {
                            if (is_numeric($key) || !in_array($key, $column_names)) {
                                unset($rawData[$key]);
                            } else {
                                if( in_array($key, $campos_picklist) )
                                    $rawData[$key] = vtranslate($value, $moduleName);
                            }
                        }

                        $fila = $rawData;
                    }
                    $entries[] = $fila;
                }
                break;

            default :

                $query = $this->getExportQuery($request);
                $log->debug("_____getExportQuery", $query);
                $result = $db->pquery($query, array());

                $headers = array();
                //Query generator set this when generating the query
                if (!empty($this->accessibleFields)) {
                    $accessiblePresenceValue = array(0, 2);
                    foreach ($this->accessibleFields as $fieldName) {
                        $fieldModel = $this->moduleFieldInstances[$fieldName];
                        // Check added as querygenerator is not checking this for admin users
                        //VR - los campos relacionados acÃ¡ daban error, lo saque nomass
                        if( !empty($fieldModel) ){
                            $presence = $fieldModel->get('presence');
                            if (in_array($presence, $accessiblePresenceValue)) {
                                $headers[] = $fieldModel->get('label');
                            }
                        }
                    }
                } else {
                    foreach ($this->moduleFieldInstances as $field)
                        $headers[] = $field->get('label');
                }
                $translatedHeaders = array();
                foreach ($headers as $headerName => $header)
                    $translatedHeaders[$headerName] = vtranslate(html_entity_decode($header, ENT_QUOTES), $moduleName);

                $entries = array();
                for ($j = 0; $j < $db->num_rows($result); $j++) {
                    $entries[] = $this->sanitizeValuesModified($db->fetchByAssoc($result, $j));
                }
                break;
        }

        $this->output($request, $translatedHeaders, $entries);
    }

    public function transferListSearchParamsToFilterCondition($listSearchParams, $moduleModel)
    {
        return Vtiger_Util_Helper::transferListSearchParamsToFilterCondition($listSearchParams, $moduleModel);
    }

    /**
     * Function that generates Export Query based on the mode
     * @param Vtiger_Request $request
     * @return <String> export query
     */
    function getExportQuery(Vtiger_Request $request)
    {
        global $log;
        $currentUser = Users_Record_Model::getCurrentUserModel();
        $mode = $request->getMode();
        $cvId = $request->get('viewname');
        $moduleName = $request->get('source_module');
        // JA - 20210324 - cambio QueryGenerator por EnhancedQueryGenerator, para que funcione el exportar todo
        $queryGenerator = new EnhancedQueryGenerator($moduleName, $currentUser);
        $queryGenerator->initForCustomViewById($cvId);
        $fieldInstances = $this->moduleFieldInstances;
        $listViewModel = Vtiger_ListView_Model::getInstance($moduleName, $cvId);
        $fieldInstances2 = $listViewModel->getListViewHeaders();
        $accessiblePresenceValue = array(0, 2);
        $orderby = $request->get('orderby');
        $sortorder = $request->get('sortorder');

        $searchKey = $request->get('search_key');
        $searchValue = $request->get('search_value');
        $operator = $request->get('operator');
        $searchParams = $request->get('search_params');
        $tagParams = $request->get('tag_params');
        if (empty($searchParams)) {
            $searchParams = array();
        }
        if (count($searchParams) == 2 && empty($searchParams[1])) {
            unset($searchParams[1]);
        }
        if (empty($tagParams)) {
            $tagParams = array();
        }
        $searchAndTagParams = array_merge($searchParams, $tagParams);
        $correcciones = $this->corregirLabelField($searchAndTagParams, $moduleName);
        $transformedSearchParams = $this->transferListSearchParamsToFilterCondition($correcciones['params'], $listViewModel->getModule());

        $fields = array();
        $fieldInstances = array_merge($fieldInstances, $correcciones['campos']);
        foreach ($fieldInstances2 as $field) {
            // Check added as querygenerator is not checking this for admin users
            $presence = $field->get('presence');
            if (in_array($presence, $accessiblePresenceValue)) {
                $fields[] = $field->getName();
            }
        }
        $tablacf = '';
        foreach ($fieldInstances as $field) {
            // Check added as querygenerator is not checking this for admin users
            $presence = $field->get('presence');
            if ($field->isCustomField()) $tablacf = $field->get('table');
            if ($field && $field->isReferenceField()) {
                $moduleList = $field->getReferenceList();
                foreach ($moduleList as $modulerel) {
                    $queryGenerator->getMeta($modulerel);
                }
            }
            if (in_array($presence, $accessiblePresenceValue) && !in_array($field->getName(), $fields)) {
                $fields[] = $field->getName();
            }
        }

        $queryGenerator->setFields($fields);
        $query = $queryGenerator->getQuery();
        if ($tablacf != '') {
            $moduleModel = Vtiger_Module_Model::getInstance($moduleName);
            $tabla = $moduleModel->get('basetable');
            $tablaid = $moduleModel->get('basetableid');
            $reemplazo = "$tabla INNER JOIN $tablacf ON $tabla.$tablaid = $tablacf.$tablaid";
            if (!(strpos($query, $tablacf) > 0)) $query = str_replace($tabla . ' ', $reemplazo . ' ', $query);
        }

        if (in_array($moduleName, getInventoryModules())) {
            $query = $this->moduleInstance->getExportQuery($this->focus, $query);
        }

        $ordersql = "";
        if (!empty($orderby)) {
            $ordersql .= ' ORDER BY ' . $queryGenerator->getOrderByColumn($orderby) . ' ' . $sortorder;
        } else if (!empty($this->focus->default_order_by) && !empty($this->focus->default_sort_order)) {
            $log->info("el modulo es");
            $log->info($moduleName);
            if($moduleName != 'ConsultasWeb' && $moduleName != 'AtencionesWeb' && $moduleName != 'AtencionPresencial'){
                $ordersql .= ' ORDER BY ' . $queryGenerator->getOrderByColumn($this->getFieldNameFromColumn($moduleName, $this->focus->default_order_by)) . ' ' . $this->focus->default_sort_order;
            }
            
        } else
            $ordersql .= ' ORDER BY vtiger_crmentity.modifiedtime DESC';

        $this->accessibleFields = $queryGenerator->getFields();
        switch ($mode) {
            case 'ExportAllData':
                return $query .= $ordersql;
                break;

            case 'ExportCurrentPage':
                $pagingModel = new Vtiger_Paging_Model();
                $limit = $pagingModel->getPageLimit();
                $query .= $ordersql;
                $currentPage = $request->get('page');
                if (empty($currentPage))
                    $currentPage = 1;

                $currentPageStart = ($currentPage - 1) * $limit;
                if ($currentPageStart < 0)
                    $currentPageStart = 0;
                $query .= ' LIMIT ' . $currentPageStart . ',' . $limit;
                return $query;
                break;

            case 'ExportSelectedRecords':
                $idList = implode(',', $request->get('selected_ids'));
                if (!empty($idList)) {
                    $query .= ' AND vtiger_crmentity.crmid IN (' . $idList . ')';
                } else {
                    $query .= ' AND vtiger_crmentity.crmid NOT IN (' . implode(',', $request->get('excluded_ids')) . ')';
                }
                $query .= $ordersql;
                return $query;
                break;
            case 'ExportFilterWithParams':
                $queryGenerator->parseAdvFilterList($transformedSearchParams, 'AND');
                if (!empty($searchKey)) {
                    $queryGenerator->addUserSearchConditions(array('search_field' => $searchKey, 'search_text' => $searchValue, 'operator'  => $operator));
                }
                $moduleModel = Vtiger_Module_Model::getInstance($moduleName);
                $tabla = $moduleModel->get('basetable');
                $reemplazo = " $tabla ";
                foreach ($correcciones['joins'] as $tabla2 => $join) {
                    if (!(strpos($query, $tabla2) > 0)) $reemplazo .= " $join ";
                }
                $query = str_replace(' ' . $tabla . ' ', $reemplazo, $query);
                $where = $queryGenerator->getWhereClause();
                $query = substr($query, 0, strpos($query, 'WHERE')) . $where;
                $query = str_replace('AND  AND', 'AND', $query);
                $query .= $ordersql;
                return $query;
                break;
            default:
                $query .= $ordersql;
                return $query;
                break;
        }
    }

    /**
     * Function returns the export type - This can be extended to support different file exports
     * @param Vtiger_Request $request
     * @return <String>
     */
    function getExportContentType(Vtiger_Request $request)
    {
        $type = $request->get('export_type');
        if (empty($type)) {
            return 'text/csv';
        }
    }

    private function getFieldNameFromColumn($module_name, $column_name) {
        global $adb;
        $query = "SELECT vf.fieldname FROM vtiger_field vf 
                    INNER JOIN vtiger_tab vt ON vt.tabid = vf.tabid 
                    WHERE vt.name = ? AND vf.columnname = ?";
        
        $result = $adb->pquery($query, array($module_name, $column_name));

        $field_name = $adb->query_result($result, 0, 'fieldname');

        return $field_name;
    }

    function corregirLabelField($params, $moduleName)
    {
        $modelModule = Vtiger_Module_Model::getInstance($moduleName);
        $tabla = $modelModule->get('basetable');
        $arraynuevo = array();
        $grupoor = array();
        $campos = array();
        $joins = array();
        foreach ($params as $igrp => $grupo) {
            $arraux = array();
            foreach ($grupo as $campo) {
                $nombreCampo = $campo[0];
                $fieldInstance = Vtiger_Field_Model::getInstance($nombreCampo, $modelModule);
                if ($fieldInstance && $fieldInstance->isReferenceField()) {
                    $moduleList = $fieldInstance->getReferenceList();
                    foreach ($moduleList as $modulerel) {
                        $table = "vtiger_crmentity";
                        $join = " LEFT JOIN " . $table . " " . $table . $nombreCampo . " ON " . $table . $nombreCampo . ".crmid = $tabla.$nombreCampo";
                        $joins[$table . $nombreCampo] = $join;
                        $arraux[] = $campo;
                    }
                } else {
                    $arraux[] = $campo;
                }
            }
            if ($igrp == 1) $arraux = array_merge($arraux, $grupoor);
            $arraynuevo[] = $arraux;
        }
        if (count($grupoor) > 0 && count($arraynuevo) == 1) {
            $arraynuevo[] = $grupoor;
        }
        return array('params' => $arraynuevo, 'campos' => $campos, 'joins' => $joins);
    }

    function campoEsPicklist($nombreCampo, $moduleName){
        global $adb;

        $result = $adb->pquery("SELECT 1 FROM vtiger_field vf
                                INNER JOIN vtiger_tab vt ON vf.tabid = vt.tabid 
                                WHERE (vf.fieldname = ? OR vf.columnname = ?) AND vt.name = ? and uitype in (?, ?, ?)",
            array($nombreCampo, $nombreCampo, $moduleName, 15, 16, 33));

        return $adb->num_rows($result) > 0;
    }

    function output($request, $headers, $entries)
    {

        if ($request->get("export_type") != 'excel') {
            $moduleName = $request->get('source_module');
            $fileName = str_replace(' ', '_', vtranslate($moduleName, $moduleName));
            $exportType = $this->getExportContentType($request);

            header("Content-Disposition:attachment;filename=$fileName.csv");
            header("Content-Type:$exportType;charset=UTF-8");
            header("Expires: Mon, 31 Dec 2000 00:00:00 GMT");
            header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
            header("Cache-Control: post-check=0, pre-check=0", false);

            $header = implode("\"; \"", $headers);
            $header = "\"" . $header;
            $header .= "\"\r\n";
            echo $header;

            foreach ($entries as $row) {
                $line = implode("\";\"", $row);
                $line = "\"" . $line;
                $line .= "\"\r\n";
                echo $line;
            }
        } else {
            $data = array();
            $data = $entries;

            $rootDirectory = vglobal('root_directory');
            $tmpDir = vglobal('tmp_dir');

            $tempFileName = tempnam($rootDirectory . $tmpDir, 'xls');
            $moduleName = $request->get('source_module') . '.xls';
            $fileName = str_replace(' ', '_', decode_html(vtranslate($moduleName, $moduleName)));
            $this->writeReportToExcelFile($tempFileName, $headers, $data, $request->get('source_module'));

            if (isset($_SERVER['HTTP_USER_AGENT']) && strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE')) {
                header('Pragma: public');
                header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            }

            header('Content-Type: application/x-msexcel');
            header('Content-Length: ' . @filesize($tempFileName));
            header('Content-disposition: attachment; filename="' . $fileName . '"');

            $fp = fopen($tempFileName, 'rb');
            fpassthru($fp);
        }
    }

    function writeReportToExcelFile($fileName, $header, $data = '', $MODULENAME="")
    {

        $moduleInstance = Vtiger_Module::getInstance($MODULENAME);

        global $currentModule, $current_language, $adb;
        $mod_strings = return_module_language($current_language, $currentModule);

        require_once("libraries/PHPExcel/PHPExcel.php");
        //require_once("Excel/PHPExcel/Classes/PHPExcel.php");
        $workbook = new PHPExcel();
        $worksheet = $workbook->setActiveSheetIndex(0);


        $arr_val = (array) $data;

        $header_styles = array(
            'fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => 'E1E0F7')),
            //'font' => array( 'bold' => true )
        );

        if (isset($arr_val)) {
            // logero(array("header", $header));
            $count = 0;
            $rowcount = 1;
            //copy the first value details
            $arrayFirstRowValues = $header;
            //array_pop($arrayFirstRowValues);   // removed action link in details
            foreach ($arrayFirstRowValues as $key => $value) {
                $worksheet->setCellValueExplicitByColumnAndRow($count, $rowcount, $value, true);
                $worksheet->getStyleByColumnAndRow($count, $rowcount)->applyFromArray($header_styles);

                // NOTE Performance overhead: http://stackoverflow.com/questions/9965476/phpexcel-column-size-issues
                //$worksheet->getColumnDimensionByColumn($count)->setAutoSize(true);

                $count = $count + 1;
            }

            

            $rowcount++;
            global $log;
            // logero($arr_val);
            foreach ($arr_val as $key => $array_value) {
                $count = 0;
                //array_pop($array_value); // removed action link in details
                $indexheader = 0;
                $headersNames = array_keys($arrayFirstRowValues);

                
                foreach ($array_value as $hdr => $value) {
                    $hdr =  $headersNames[$indexheader];
                    $indexheader++;
                    $value = decode_html($value);
                    
                    // TODO Determine data-type based on field-type.
                    // String type helps having numbers prefixed with 0 intact.
                    // logero(array($hdr, $moduleInstance));
                    $fieldInstance = Vtiger_Field_Model::getInstance($hdr, $moduleInstance);
                    $cellType = PHPExcel_Cell_DataType::TYPE_STRING;
                    $cellForm = NULL;
                    // logero($fieldInstance);
                    if (!!$fieldInstance) {
                        
                        $ftype = strtoupper(substr($fieldInstance->typeofdata, 0, 1));
                        // logero($ftype);
                        switch($ftype) {
                            case "I":
                                if (intval($fieldInstance->uitype) !== 10 && !in_array($fieldInstance->getFieldDataType(), array("reference", "owner"))) {
                                    $cellType = PHPExcel_Cell_DataType::TYPE_NUMERIC;
                                    $cellForm = PHPExcel_Style_NumberFormat::FORMAT_NUMBER_00;
                                }
                                
                                break;
                            case "D":
                                $value = PHPExcel_Shared_Date::PHPToExcel( strtotime($value) );
                                $cellType = PHPExcel_Cell_DataType::TYPE_NUMERIC;
                                $cellForm = PHPExcel_Style_NumberFormat::FORMAT_DATE_YYYYMMDD2;
                                break;
                            case "Ts":
                                $value = PHPExcel_Shared_Date::PHPToExcel( strtotime($value) );
                                $cellType = PHPExcel_Cell_DataType::TYPE_NUMERIC;
                                if ($fieldInstance->uitype === 2) {
                                    $cellForm = PHPExcel_Style_NumberFormat::FORMAT_DATE_TIME3;
                                } else {
                                    $cellForm = PHPExcel_Style_NumberFormat::FORMAT_DATE_DATETIME;
                                }
                                break;
                        }

                        if( $fieldInstance->getFieldDataType() == "reference" ){
                            $result = $adb->pquery("SELECT setype FROM vtiger_crmentity WHERE crmid=?", array($value));
                            if ($adb->num_rows($result)) {
                                $relatedModuleName = $adb->query_result($result, 0, 'setype');
                                $entityNames = getEntityName($relatedModuleName, array($value));
                                if (!!$entityNames[$value]) $value = $entityNames[$value];
                            }
                        } else if( $fieldInstance->getFieldDataType() == "owner" ){
                            $value = Vtiger_Util_Helper::getOwnerName($value);
                        }
                        
                    } else {
                        // aca entran los que son columnas de la cionadas de modulos relacionados
                        $result = $adb->pquery("SELECT setype FROM vtiger_crmentity WHERE crmid=?", array($value));
                        if ($adb->num_rows($result)) {
                            $relatedModuleName = $adb->query_result($result, 0, 'setype');
                            $entityNames = getEntityName($relatedModuleName, array($value));
                            if (!!$entityNames[$value]) $value = $entityNames[$value];
                        }
                    }
                    $worksheet->setCellValueExplicitByColumnAndRow($count, $rowcount, $value, $cellType);
                    if ($cellForm)
                        $worksheet->getStyleByColumnAndRow($count, $rowcount)->getNumberFormat()->setFormatCode($cellForm);
                    $count = $count + 1;
                }
                $rowcount++;
            }
        }

        $workbookWriter = PHPExcel_IOFactory::createWriter($workbook, 'Excel5');
        $workbookWriter->save($fileName);
        return $arr_val;
    }

    private $picklistValues;
    private $fieldArray;
    private $fieldDataTypeCache = array();

    /**
     * this function takes in an array of values for an user and sanitizes it for export
     * @param array $arr - the array of values
     */
    function sanitizeValues($arr)
    {
        $db = PearDatabase::getInstance();
        $currentUser = Users_Record_Model::getCurrentUserModel();
        $roleid = $currentUser->get('roleid');
        if (empty($this->fieldArray)) {
            $this->fieldArray = $this->moduleFieldInstances;
            foreach ($this->fieldArray as $fieldName => $fieldObj) {
                //In database we have same column name in two tables. - inventory modules only
                if ($fieldObj->get('table') == 'vtiger_inventoryproductrel' && ($fieldName == 'discount_amount' || $fieldName == 'discount_percent')) {
                    $fieldName = 'item_' . $fieldName;
                    $this->fieldArray[$fieldName] = $fieldObj;
                } else {
                    $columnName = $fieldObj->get('column');
                    $this->fieldArray[$columnName] = $fieldObj;
                }
            }
        }
        $moduleName = $this->moduleInstance->getName();
        foreach ($arr as $fieldName => &$value) {
            if (isset($this->fieldArray[$fieldName])) {
                $fieldInfo = $this->fieldArray[$fieldName];
            } else {
                unset($arr[$fieldName]);
                continue;
            }
            $value = decode_html($value);
            $uitype = $fieldInfo->get('uitype');
            $fieldname = $fieldInfo->get('name');

            if (!$this->fieldDataTypeCache[$fieldName]) {
                $this->fieldDataTypeCache[$fieldName] = $fieldInfo->getFieldDataType();
            }
            $type = $this->fieldDataTypeCache[$fieldName];

            if ($fieldname != 'hdnTaxType' && ($uitype == 15 || $uitype == 16 || $uitype == 33)) {
                if (empty($this->picklistValues[$fieldname])) {
                    $this->picklistValues[$fieldname] = $this->fieldArray[$fieldname]->getPicklistValues();
                }
                // If the value being exported is accessible to current user
                // or the picklist is multiselect type.
                if ($uitype == 33 || $uitype == 16 || in_array($value, $this->picklistValues[$fieldname])) {
                    // NOTE: multipicklist (uitype=33) values will be concatenated with |# delim
                    $value = trim($value);
                } else {
                    $value = '';
                }
            } elseif ($uitype == 52 || $type == 'owner') {
                $value = Vtiger_Util_Helper::getOwnerName($value);
            } elseif ($type == 'reference') {
                $value = trim($value);
                if (!empty($value)) {
                    $parent_module = getSalesEntityType($value);
                    $displayValueArray = getEntityName($parent_module, $value);
                    if (!empty($displayValueArray)) {
                        foreach ($displayValueArray as $k => $v) {
                            $displayValue = $v;
                        }
                    }
                    if (!empty($parent_module) && !empty($displayValue)) {
                        $value = $parent_module . "::::" . $displayValue;
                    } else {
                        $value = "";
                    }
                } else {
                    $value = '';
                }
            } elseif ($uitype == 72 || $uitype == 71) {
                $value = CurrencyField::convertToUserFormat($value, null, true, true);
            } elseif ($uitype == 7 && $fieldInfo->get('typeofdata') == 'N~O' || $uitype == 9) {
                $value = decimalFormat($value);
            }
            if ($moduleName == 'Documents' && $fieldname == 'description') {
                $value = strip_tags($value);
                $value = str_replace('&nbsp;', '', $value);
                array_push($new_arr, $value);
            }
        }
        return $arr;
    }

    /**
     * this function takes in an array of values for an user and sanitizes it for export
     * @param array $arr - the array of values
     */
    function sanitizeValuesModified($arr)
    {
        global $log;
        $log->debug("sanitizeValuesModified: ".var_export($arr, true));
        $db = PearDatabase::getInstance();
        $currentUser = Users_Record_Model::getCurrentUserModel();
        $roleid = $currentUser->get('roleid');
        if (empty($this->fieldArray)) {
            $this->fieldArray = $this->moduleFieldInstances;
            foreach ($this->fieldArray as $fieldName => $fieldObj) {
                //In database we have same column name in two tables. - inventory modules only
                if ($fieldObj->get('table') == 'vtiger_inventoryproductrel' && ($fieldName == 'discount_amount' || $fieldName == 'discount_percent')) {
                    $fieldName = 'item_' . $fieldName;
                    $this->fieldArray[$fieldName] = $fieldObj;
                } else {
                    $columnName = $fieldObj->get('column');
                    $this->fieldArray[$columnName] = $fieldObj;
                }
            }
        }
        $moduleName = $this->moduleInstance->getName();

        //Tuve que usar estas funciones en vez del isset porque el isset devuelve falso si esta seteado pero el valor es null
        $existe_ramos = array_key_exists('ac_ramos', $arr);
        $existe_roles = array_key_exists('ac_roles', $arr);

        if( $moduleName == "Accounts" && ($existe_ramos || $existe_roles) ){
            global $adb;
            
            if( isset($arr['accountid']) ){
                $account_id = $arr['accountid'];
            } else {
                
                $result_account_id = $adb->pquery("SELECT accountid FROM vtiger_account WHERE account_no = ?", array($arr['account_no']));
                $account_id = $adb->query_result($result_account_id, 0, 'accountid');
            }
            //Si vienen alguno de esos valores, hay que sacarlos de otro lado:
            if( $existe_ramos  ){
                $sql = 'SELECT group_concat(DISTINCT po_ramo SEPARATOR ", ") as ramos  FROM vtiger_polizas p INNER JOIN vtiger_crmentity e ON e.crmid = p.polizasid AND e.deleted = 0 WHERE po_persona = ?';
                $result = $adb->pquery($sql, array($account_id));
                $ramos = $adb->num_rows($result) > 0 ? html_entity_decode($adb->query_result($result, 0, 'ramos')) : "";

                $arr['ac_ramos'] = $ramos;
            }

            if( $existe_roles  ){
                $sql = 'SELECT group_concat(rol_rol SEPARATOR " |##| ") as roles FROM vtiger_roles r INNER JOIN vtiger_crmentity crm ON crm.crmid = r.rolesid WHERE deleted = 0 AND rol_persona = ?';
                $result = $adb->pquery($sql, array($account_id));
                $roles = $adb->num_rows($result) > 0 ? html_entity_decode($adb->query_result($result, 0, 'roles')) : "";

                $arr['ac_roles'] = $roles;
            }
        }
        
        foreach ($arr as $fieldName => &$value) {
            if (isset($this->fieldArray[$fieldName])) {
                $fieldInfo = $this->fieldArray[$fieldName];
            } else {
                unset($arr[$fieldName]);
                continue;
            }
            $value = decode_html($value);
            $uitype = $fieldInfo->get('uitype');
            $fieldname = $fieldInfo->get('name');

            if (!$this->fieldDataTypeCache[$fieldName]) {
                $this->fieldDataTypeCache[$fieldName] = $fieldInfo->getFieldDataType();
            }
            $type = $this->fieldDataTypeCache[$fieldName];

            if ($fieldname != 'hdnTaxType' && ($uitype == 15 || $uitype == 16 || $uitype == 33)) {
                if (empty($this->picklistValues[$fieldname])) {
                    $this->picklistValues[$fieldname] = $this->fieldArray[$fieldname]->getPicklistValues();
                }
                // If the value being exported is accessible to current user
                // or the picklist is multiselect type.
                if ($uitype == 33 || $uitype == 16 || in_array($value, $this->picklistValues[$fieldname])) {
                    // NOTE: multipicklist (uitype=33) values will be concatenated with |# delim
                    $value = trim($value);
                } else {
                    $value = '';
                }
            } elseif ($uitype == 52 || $type == 'owner') {
                $value = Vtiger_Util_Helper::getOwnerName($value);
            } elseif ($type == 'reference') {
                $value = trim($value);
                if (!empty($value)) {
                    $parent_module = getSalesEntityType($value);
                    $displayValueArray = getEntityName($parent_module, $value);
                    if (!empty($displayValueArray)) {
                        foreach ($displayValueArray as $k => $v) {
                            $displayValue = $v;
                        }
                    }
                    if (!empty($parent_module) && !empty($displayValue)) {
                        $value =  $displayValue;
                    } else {
                        $value = "";
                    }
                } else {
                    $value = '';
                }
            } elseif ($uitype == 72 || $uitype == 71) {
                $value = CurrencyField::convertToUserFormat($value, null, true, true);
            } elseif ($uitype == 7 && $fieldInfo->get('typeofdata') == 'N~O' || $uitype == 9) {
                $value = decimalFormat($value);
            }
            if ($moduleName == 'Documents' && $fieldname == 'description') {
                $value = strip_tags($value);
                $value = str_replace('&nbsp;', '', $value);
                array_push($new_arr, $value);
            }
        }
        return $arr;
    }
}
