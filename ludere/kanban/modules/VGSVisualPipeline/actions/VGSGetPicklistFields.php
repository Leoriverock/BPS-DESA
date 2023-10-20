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

class VGSVisualPipeline_VGSGetPicklistFields_Action extends Vtiger_Action_Controller {

    public function checkPermission(Vtiger_Request $request) {
        /*
        global $current_user;
        
        if (!is_admin($current_user)) {
            throw new AppException('LBL_PERMISSION_DENIED');
        }
        */
    }

    public function process(Vtiger_Request $request) {
        $db = PearDatabase::getInstance();
        
        $sql = "SELECT fieldname, fieldlabel, uitype in ('15','16') AS picklist
               FROM vtiger_field INNER JOIN vtiger_tab ON vtiger_field.tabid=vtiger_tab.tabid 
               WHERE vtiger_tab.name = ? 
               AND vtiger_field.presence in (0,2)
               AND fieldname != 'hdnTaxType'
               AND fieldname != 'campaignrelstatus'";

        $result = $db->pquery($sql, array($request->get('source_module')));
        if ($db->num_rows($result) > 0) {
            $htmlOptions = $this->buildOptionsArray($result, $request->get('source_module'));
            $fieldsResponse['result'] = 'ok';
            $fieldsResponse['options'] = $htmlOptions;
        } else {
            $fieldsResponse['result'] = 'fail';
        }


        $response = new Vtiger_Response();
        $response->setResult($fieldsResponse);
        $response->emit();
    }

    function buildOptionsArray($result, $selectedModule) {
        $db = PearDatabase::getInstance();
        $optionArray = array();
        
        for ($i = 0; $i < $db->num_rows($result); $i++) {
            $optionArray[$db->query_result($result, $i, 'fieldname')] = array('label' => getTranslatedString($db->query_result($result, $i, 'fieldlabel'), $selectedModule), 'picklist' => $db->query_result($result, $i, 'picklist'));
        }
        return $optionArray;
    }

}
