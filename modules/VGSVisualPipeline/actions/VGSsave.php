<?php

/**
 * VGS Visual Pipeline Module
 *
 *
 * @package        VGSVisualPipeline Module
 * @author         Curto Francisco, Maggi Conrado - www.vgsglobal.com
 * @license        vTiger Public License.
 * @version        Release: 1.0
 */

class VGSVisualPipeline_VGSsave_Action extends Vtiger_Action_Controller {

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
        $fieldsResponse = array("result" => "fail");

        switch ($request->get('mode')) {
            case 'deleteRecord':
                $sql = "DELETE FROM vtiger_vgsvisualpipeline WHERE vgsvisualpipelineid = ?";
                $result = $db->pquery($sql, array($request->get('record_id')));

                if($result){
                    $fieldsResponse['result'] = 'ok';
                    $tabid = getTabId($request->get('module1'));
                    Vtiger_Link::deleteLink($tabid, 'LISTVIEWBASIC', 'Pipeline View');
                }
                else
                    $fieldsResponse['message'] = 'Problemas al eliminar';

                $response = new Vtiger_Response();
                $response->setResult($fieldsResponse);
                $response->emit();
                break;

            default:
                $sql = "SELECT * FROM vtiger_vgsvisualpipeline WHERE sourcemodule = ? AND vgsvisualpipelineid != ?";

                $result = $db->pquery($sql, array($request->get('module1'), $request->get('vgsid')));

                if ($db->num_rows($result) > 0)
                    $fieldsResponse['message'] = vtranslate('ALREADY_EXISTS', 'VGSVisualPipeline');
                else {
                    $id = $request->get('vgsid');
                    $campos = $request->get('campos');

                    $strcampos = "";
                    $strvalores = "";
                    $strupdate = "";

                    $params = Array(
                        $request->get('picklist1'),
                        $request->get('module1'),
                    );

                    foreach ($campos as $key => $campo){
                        foreach ($campo as $nombre => $valor) {
                            if(!$id){
                                $strcampos .= ", ".$nombre.$key;
                                $strvalores .= ", ?";
                            }
                            else
                                $strupdate .= ", ".$nombre.$key." = ?";
                            $params[] = $valor;
                        }
                    }
                    
                    try {
                        if(!$id)
                            $sql = "INSERT INTO vtiger_vgsvisualpipeline (sourcefieldname, sourcemodule".$strcampos.") VALUES (?, ?".$strvalores.")";
                        else{
                            $sql = "UPDATE vtiger_vgsvisualpipeline SET sourcefieldname = ?, sourcemodule = ?".$strupdate." WHERE vgsvisualpipelineid = ?";
                            $params[] = $id;
                        }
                        $result = $db->pquery($sql, $params);

                        if ($result) {
                            $fieldsResponse['result'] = 'ok';
                            if(!$id){
                                $tabid = getTabId($request->get('module1'));
                                Vtiger_Link::addLink($tabid, 'LISTVIEW', 'Pipeline View', "javascript:changeView()", '', 0, '');
                                $module = Vtiger_Module::getInstance($request->get('module1'));
                                if($module){
                                    // Instalar el script JS
                                    $module->addLink('HEADERSCRIPT', 'ColorPicker', 'libraries/bootstrap/js/bootstrap-colorpicker.min.js');
                                    // Instalar estilos
                                    $module->addLink('HEADERCSS', 'ColorPicker CSS', 'libraries/bootstrap/css/bootstrap-colorpicker.min.css');
                                }
                            }
                        } else
                            $fieldsResponse['message'] = vtranslate('LBL_DB_INSERT_FAIL','VGSVisualPipeline');

                    } catch (Exception $exc){
                        $fieldsResponse['message'] = $exc->message;
                    }
                }

                $response = new Vtiger_Response();
                $response->setResult($fieldsResponse);
                $response->emit();
                break;
        }
    }

}
