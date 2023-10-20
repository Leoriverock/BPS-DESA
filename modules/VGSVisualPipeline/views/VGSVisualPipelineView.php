<?php

/**
 * VGS Visual Pipeline Module
 *
 *
 * @package        VGSVisualPipeline Module
 * @author         Curto Francisco, Conrado Maggi - www.vgsglobal.com
 * @license        vTiger Public License.
 * @version        Release: 1.0
 */

class VGSVisualPipeline_VGSVisualPipelineView_View extends Vtiger_IndexAjax_View {

    function __construct() {
        parent::__construct();
        $this->exposeMethod('showVPView');
        $this->exposeMethod('get_string_between');
    }

    function process(Vtiger_Request $request) {
        $mode = $request->get('mode');
        if (!empty($mode)) {
            $this->invokeExposedMethod($mode, $request);
            return;
        }
    }

    function showVPView(Vtiger_Request $request) {
        global $current_user;

        $moduleName = $request->get('module1');
        $db = PearDatabase::getInstance();

        $sql = "SELECT sourcefieldname, fieldname1, fieldname2, fieldname3, fieldname4, negrita1, negrita2, negrita3, negrita4, color1, color2, color3, color4
                FROM vtiger_vgsvisualpipeline 
                WHERE sourcemodule = ?";
        $result = $db->pquery($sql, array($moduleName));

        $vpFieldName = $db->query_result($result, 0, 'sourcefieldname');
        $vpFilterFieldName1 = $db->query_result($result, 0, 'fieldname1');
        $vpFilterFieldName2 = $db->query_result($result, 0, 'fieldname2');
        $vpFilterFieldName3 = $db->query_result($result, 0, 'fieldname3');
        $vpFilterFieldName4 = $db->query_result($result, 0, 'fieldname4');
        $vpnegrita1 = $db->query_result($result, 0, 'negrita1');
        $vpnegrita2 = $db->query_result($result, 0, 'negrita2');
        $vpnegrita3 = $db->query_result($result, 0, 'negrita3');
        $vpnegrita4 = $db->query_result($result, 0, 'negrita4');
        $vpcolor1 = $db->query_result($result, 0, 'color1');
        $vpcolor2 = $db->query_result($result, 0, 'color2');
        $vpcolor3 = $db->query_result($result, 0, 'color3');
        $vpcolor4 = $db->query_result($result, 0, 'color4');

        $moduleInstance = Vtiger_Module_Model::getInstance($moduleName);
        $fieldInstance = Vtiger_Field_Model::getInstance($vpFieldName, $moduleInstance);
        $selectedIds = $request->get('seleccionados');
        $vpColumn = $fieldInstance->column;
        $vpLabel = vtranslate($fieldInstance->label, $moduleName);

        $tooltipoFields = array();
        $negritas = array();
        $colores = array();
        $vpFilterFieldColumns = array();
        if($vpFilterFieldName1){
            $fieldInstance = Vtiger_Field_Model::getInstance($vpFilterFieldName1, $moduleInstance);
            if($fieldInstance){
                $tooltipoFields[] = $fieldInstance;
                $negritas[] = $vpnegrita1;
                $colores[] = $vpcolor1;
            }
        }

        if($vpFilterFieldName2){
            $fieldInstance = Vtiger_Field_Model::getInstance($vpFilterFieldName2, $moduleInstance);
            if($fieldInstance){
                $tooltipoFields[] = $fieldInstance;
                $negritas[] = $vpnegrita2;
                $colores[] = $vpcolor2;
            }
        }

        if($vpFilterFieldName3){
            $fieldInstance = Vtiger_Field_Model::getInstance($vpFilterFieldName3, $moduleInstance);
            if($fieldInstance){
                $tooltipoFields[] = $fieldInstance;
                $negritas[] = $vpnegrita3;
                $colores[] = $vpcolor3;
            }
        }

        if($vpFilterFieldName4){
            $fieldInstance = Vtiger_Field_Model::getInstance($vpFilterFieldName4, $moduleInstance);
            if($fieldInstance){
                $tooltipoFields[] = $fieldInstance;
                $negritas[] = $vpnegrita4;
                $colores[] = $vpcolor4;
            }
        }

        $list = $this->getListViewResult($moduleName, $selectedIds, $vpColumn, $vpFieldName);

        $list = $this->sortListArray($moduleName, $list);

        $picklistValues = array_merge(array(""), $this->getPicklistValues($moduleName, $vpFieldName));

        foreach ($picklistValues as $picklistOrder => $picklistValue) {
            $tmp = array();
            if (count($list) > 0)
                foreach ($list as $key => $value)
                    if ($value == $picklistValue) {
                        $record = Vtiger_Record_Model::getInstanceById($key, $moduleName);
                        $esgrupo = !!count(Vtiger_Functions::getGroupName($record->get("assigned_user_id")));
                        if(!$esgrupo){
                            $imagen = Users_Record_Model::getInstanceById($record->get("assigned_user_id"), "Users")->getImageDetails();
                            $imagen = $imagen[0];
                            $imagen = $imagen["path"]?$imagen["path"]."_".$imagen["name"]:"layouts/vlayout/skins/images/user_kanban.png";
                        }
                        else
                            $imagen = "layouts/vlayout/skins/images/group_kanban.png";
                        
                        $recordArray = array(
                            'RECORD' => $key,
                            'RECORD_LABEL' => Vtiger_Functions::getCRMRecordLabel($key),
                            'MODULE_MODEL' => $moduleInstance,
                            'TOOLTIP_FIELDS' => $tooltipoFields,
                            'RECORD_MODEL' => $record,
                            'PATHIMAGEN' => $imagen,
                            'NEGRITAS' => $negritas,
                            'COLORES' => $colores
                        );
                        array_push($tmp, $recordArray);
                    }

            $array_datos[$picklistOrder][$picklistValue] = $tmp;
        }
        //Si no hay registros sin valor en el campo por el que se filtra, sacamos la columna correspondiente
        if(!count($array_datos[0][""]))
            unset($array_datos[0]);

        $recordsSettings = $this->getRecordsSettings(array_keys($list));

        $viewer = $this->getViewer($request);
        $viewer->assign('MODULE', $moduleName);
        $viewer->assign('USER_MODEL', Users_Record_Model::getCurrentUserModel());

        $viewer->assign('PUEDECREAR', Users_Privileges_Model::isPermitted($moduleName, 'EditView'));
        $viewer->assign('MODULENAME', $moduleName);
        $viewer->assign('COLUMNA', $vpFieldName);
        $viewer->assign('FILTERFIELDLABEL', $vpLabel);
        $viewer->assign('RECORDS_ARRAY', $array_datos);
        $viewer->assign('RECORDS_SETTINGS', $recordsSettings);
        echo $viewer->view('VGSVisualPipelineView.tpl', 'VGSVisualPipeline', true);
    }

    function get_string_between($string, $start, $end) {
        $string = " " . $string;
        $ini = strpos($string, $start);
        if ($ini == 0)
            return "";
        $ini += strlen($start);
        $len = strpos($string, $end, $ini) - $ini;
        return substr($string, $ini, $len);
    }

    function getPicklistValues($targetModule, $targetFieldName) {
        global $log;
        $currentUser = Users_Record_Model::getCurrentUserModel();
        $fieldModel = Vtiger_Field_Model::getInstance($targetFieldName, Vtiger_Module_Model::getInstance($targetModule));

        /* queda comentado para que si no tiene permisos de modificacion, por lo menos vea el pipline 
        Check is the user can write in that field
        if (!$fieldModel->getPermissions('readwrite')) {
            $log->debug('User id: ' . $currentUser->id . ' is not permitted to edit the field:' . $targetFieldName);
            return false;
        }
        */
        //Check if the user can choose that picklist value.
        
        if ($fieldModel->isRoleBased() && !$currentUser->isAdminUser())
            $picklistValues = Vtiger_Util_Helper::getRoleBasedPicklistValues($targetFieldName, $currentUser->get('roleid'));
        else
            $picklistValues = Vtiger_Util_Helper::getPickListValues($targetFieldName);

        return $picklistValues;
    }

    function getListViewResult($moduleName, $selectedIds, $vpColumn, $vpFieldName) {
        $db = PearDatabase::getInstance();
        $current_user = Users_Record_Model::getCurrentUserModel();

        $customView = new CustomView($moduleName);
        $viewid = $customView->getViewId($moduleName);
        $moduleInstance = Vtiger_Module_Model::getInstance($moduleName);

        if (is_array($selectedIds) && count($selectedIds) > 0)
            $selected_ids = '(' . implode(',', $selectedIds) . ')';
        elseif ($selectedIds != '')
            $selected_ids = '(' . $selectedIds . ')';

        $queryGenerator = new QueryGenerator($moduleName, $current_user);

        if ($viewid != "0")
            $queryGenerator->initForCustomViewById($viewid);
        else
            $queryGenerator->initForDefaultCustomView();

        $list_query = $queryGenerator->getQuery();
        $campos = $this->get_string_between($list_query, 'SELECT', 'FROM');
        $arr_campos = explode(",", $campos);
        $listaCamposFiltro = " ".$moduleInstance->basetable.".".$vpColumn.", ".end($arr_campos);

        $list_query = str_replace($campos, $listaCamposFiltro, $list_query);

        $where = $queryGenerator->getConditionalWhere();
        if (isset($where) && $where != '')
            $_SESSION['export_where'] = $where;
        else
            unset($_SESSION['export_where']);

        //Selected Ids
        if (!empty($selectedIds))
            $list_query .= ' AND ' . $moduleInstance->basetableid . ' IN ' . $selected_ids;

        $list = array();

        $result = $db->query($list_query);
        if ($db->num_rows($result) > 0)
            for ($i = 0; $i < $db->num_rows($result); $i++)
                if($vpColumn)
                    $list[$db->query_result($result, $i, $moduleInstance->basetableid)] = $db->query_result($result, $i, $vpColumn);

        return $list;
    }

    function getRecordsSettings($ids) {
        global $adb, $current_user;

        $sql = "SELECT vgscrmid, vgscolor 
                FROM vtiger_vgsvisualpipeline_settings
                WHERE vgsuserid = ? AND vgscrmid IN (".implode(",", $ids).")";
        $result = $adb->pquery($sql, array($current_user->id));

        $ret = array();

        foreach ($result as $dato)
            $ret[$dato["vgscrmid"]] = $dato["vgscolor"];

        return $ret;
    }

    function sortListArray($moduleName, $array_datos) {
        $db = PearDatabase::getInstance();
        $result = $db->pquery("SELECT * FROM vtiger_vgsvisualsorting WHERE module = ?", array($moduleName));
        if ($db->num_rows($result) > 0 && is_array($array_datos)) {
            $sorting = array();
            while ($row = $db->fetch_row($result))
                $sorting = array_merge($sorting, unserialize(htmlspecialchars_decode($row['sorting'])));

            $array_datos = $this->sortArrayByArray($array_datos, $sorting);
        }
        return $array_datos;
    }

    function sortArrayByArray(Array $array, Array $orderArray) {
        $ordered = array();
        foreach ($orderArray as $key) {
            if (array_key_exists($key, $array)) {
                $ordered[$key] = $array[$key];
                unset($array[$key]);
            }
        }
        return $ordered + $array;
    }

}
