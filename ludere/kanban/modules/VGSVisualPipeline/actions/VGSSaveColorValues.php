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

class VGSVisualPipeline_VGSSaveColorValues_Action extends Vtiger_Action_Controller {

    public function checkPermission(Vtiger_Request $request) {
        /*
        global $current_user;
        
        if (!is_admin($current_user)) {
            throw new AppException('LBL_PERMISSION_DENIED');
        }
        */
    }

    public function process(Vtiger_Request $request) {
        global $current_user, $adb;
        $id = $request->get("source_id");
        $color = $request->get("source_color");
        $eliminar = $request->get("delete");

        $params = array($id, $current_user->id);
        if($eliminar)
            $sql = "DELETE FROM vtiger_vgsvisualpipeline_settings WHERE vgscrmid = ? AND vgsuserid = ?";
        else{
            $sql = "INSERT INTO vtiger_vgsvisualpipeline_settings VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE vgscolor = ?";
            $params[] = $color;
            $params[] = $color;
        }
        $ret = $adb->pquery($sql, $params);

        $response = new Vtiger_Response();
        $response->setResult($ret);
        $response->emit();
    }

}
