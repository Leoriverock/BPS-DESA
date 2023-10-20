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

class VGSVisualPipeline_VGSAddNew_View extends Settings_Vtiger_Index_View {

    public function process(Vtiger_Request $request) {
        global $adb;
        $viewer = $this->getViewer($request);
        $id = $request->get("id");
        
        $entityModules = Vtiger_Module_Model::getEntityModules();
        $restrictedModules = array('Emails','Documents','Campaigns', 'Calendar','Faq','Webmails','ModComments', 'SMSNotifier', 'PBXManager'); //Modules where related fields do not work as expected

        $modules = array();
        foreach ($entityModules as $entityModule) {
            if(!in_array($entityModule->name, $restrictedModules)){
                array_push($modules, $entityModule->name);
            }
        }

        $viewer->assign('ENTITY_MODULES', $modules);
        if($id){
            $sql = "SELECT *
                    FROM vtiger_vgsvisualpipeline
                    WHERE vgsvisualpipelineid = ?";
            $result = $adb->pquery($sql, array($id));

            foreach ($result as $dato) {
                $viewer->assign('SOURCEMODULE', $dato["sourcemodule"]);
                $viewer->assign('SOURCEFIELDNAME', $dato["sourcefieldname"]);
                $viewer->assign('VGSID', $id);
                $campos = array(
                    array($dato["fieldname1"], $dato["negrita1"], $dato["color1"]),
                    array($dato["fieldname2"], $dato["negrita2"], $dato["color2"]),
                    array($dato["fieldname3"], $dato["negrita3"], $dato["color3"]),
                    array($dato["fieldname4"], $dato["negrita4"], $dato["color4"])
                );
                $viewer->assign('CAMPOS', $campos);
            }
        }

        $viewer->view('VGSAddNew.tpl', $request->getModule());
    }

    
     function getPageTitle(Vtiger_Request $request) {
        return vtranslate('LBL_MODULE_NAME', $request->getModule());
    }

    /**
     * Function to get the list of Script models to be included
     * @param Vtiger_Request $request
     * @return <Array> - List of Vtiger_JsScript_Model instances
     */
    function getHeaderScripts(Vtiger_Request $request) {
        $headerScriptInstances = parent::getHeaderScripts($request);
        $moduleName = $request->getModule();

        $jsFileNames = array(
            "layouts.v7.modules.VGSVisualPipeline.resources.VGSVisualPipelineSettings",
        );

        $jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
        $headerScriptInstances = array_merge($headerScriptInstances, $jsScriptInstances);
        return $headerScriptInstances;
    }
    
}
