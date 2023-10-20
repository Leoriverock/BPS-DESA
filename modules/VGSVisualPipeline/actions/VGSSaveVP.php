<?php

/**
 * VGS Visual Pipeline Module
 *
 *
 * @package        VGSVisualPipeline Module
 * @author         Curto Francisco - www.vgsglobal.com
 * @license        vTiger Public License.
 * @version        Release: 1.0
 */
class VGSVisualPipeline_VGSSaveVP_Action extends Vtiger_Action_Controller {

    public function checkPermission(Vtiger_Request $request) {
        /*global $current_user;

        if (!is_admin($current_user)) {
            throw new AppException('LBL_PERMISSION_DENIED');
        }
        */
    }

    public function process(Vtiger_Request $request) {
        global $current_user,$log;

        include_once 'include/Webservices/Create.php';
        include_once 'include/Webservices/Revise.php';
        include_once 'include/Webservices/Delete.php';

        $id = $request->get('id');
        $modulo = $request->get('modulo');
        $columna = $request->get('columna');
        $valor = $request->get('valor');
        $sorting = $request->get('sort_order');

        $campo = $columna;
        
        $db = PearDatabase::getInstance();

        $result = $db->pquery("SELECT fieldid FROM `vtiger_field` WHERE fieldname= ?", array($campo));
        $idCampo = $db->query_result($result, 0,'fieldid');
        
        $vpFieldName = Vtiger_Field_Model::getInstance($idCampo)->getPermissions('readwrite');

        if ($vpFieldName) {
            if (in_array($modulo, array('Quotes', 'SalesOrder', 'Invoice', 'PurchaseOrder'))) {
                $this->updateInventoryModule($modulo,$columna,$valor,$id);
                $this->saveSorting($modulo, $sorting);
            }
            else {
                try {

                    $visualPipeline = array(
                        'id' => vtws_getWebserviceEntityId($modulo, $id),
                        $columna => $valor,
                    );

                    vtws_revise($visualPipeline, $current_user);

                    $this->saveSorting($modulo, $sorting);
                    $response = new Vtiger_Response();
                    $response->setResult('ok');
                    $response->emit();
                } catch (Exception $exc) {
                    echo $exc->getTraceAsString();
                    $response = new Vtiger_Response();
                    $response->setResult('error');
                    $response->emit();
                }
            }
        }
        else{
            $response = new Vtiger_Response();
            $response->setResult('sin privilegio');
            $response->emit();
        }
    }

    function saveSorting($moduleName, $sorting) {
        $db = PearDatabase::getInstance();

        $db->pquery("DELETE FROM vtiger_vgsvisualsorting WHERE module=?", array($moduleName));
        $db->pquery("INSERT INTO vtiger_vgsvisualsorting (module,sorting) VALUES (?,?)", array($moduleName, serialize($sorting)));
    }

    function updateInventoryModule($targetModule, $targetFieldName, $targetFieldValue, $targetRecordId) {
        $db = PearDatabase::getInstance();
        $moduleInstance = Vtiger_Module_Model::getInstance($targetModule);
        $fieldInstance = Vtiger_Field_Model::getInstance($targetFieldName, $moduleInstance);

        $sql = "UPDATE $fieldInstance->table SET $fieldInstance->column = ? WHERE $moduleInstance->basetableid = ?";
        $db->pquery($sql, array($targetFieldValue, $targetRecordId));
    }

}
