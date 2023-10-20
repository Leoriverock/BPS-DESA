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

class VGSVisualPipeline_VGSIndexSettings_View extends Settings_Vtiger_Index_View {

    public function process(Vtiger_Request $request) {

        $vgsRelUpdatesList = $this->getList();

        $viewer = $this->getViewer($request);
        $viewer->assign('RMU_FIELDS_ARRAY', $vgsRelUpdatesList);
        $viewer->assign('IS_VALIDATED', true);
        $viewer->view('VGSIndexSettings.tpl', $request->getModule());

    }

    function getPageTitle(Vtiger_Request $request) {
        return vtranslate('LBL_MODULE_NAME', $request->getModule());
    }

    function getList() {
        $db = PearDatabase::getInstance();
        $relModFieldList = Array();
        $sql = "SELECT 
                    vv.vgsvisualpipelineid,
                    vv.sourcemodule, 
                    f.fieldlabel as sourcefieldlabel, 
                    f1.fieldlabel as targetfieldlabel1, 
                    f2.fieldlabel as targetfieldlabel2, 
                    f3.fieldlabel as targetfieldlabel3, 
                    f4.fieldlabel as targetfieldlabel4
                FROM vtiger_vgsvisualpipeline vv
                INNER JOIN vtiger_field f ON vv.sourcefieldname = f.fieldname
                LEFT JOIN vtiger_field f1 ON vv.fieldname1 = f1.fieldname
                LEFT JOIN vtiger_field f2 ON vv.fieldname2 = f2.fieldname
                LEFT JOIN vtiger_field f3 ON vv.fieldname3 = f3.fieldname
                LEFT JOIN vtiger_field f4 ON vv.fieldname4 = f4.fieldname
                GROUP BY  vgsvisualpipelineid";
        $result = $db->pquery($sql, array());
        $i = 0;
        while ($row = $db->fetchByAssoc($result)) {
            $relModFieldList[$i]['id'] = $row['vgsvisualpipelineid'];
            $relModFieldList[$i]['source_module'] = vtranslate($row['sourcemodule']);
            $relModFieldList[$i]['source_field_name'] = vtranslate($row['sourcefieldlabel'], $row['sourcemodule']);

            $campos = array();
            if($row['targetfieldlabel1'])
                $campos[] = vtranslate($row['targetfieldlabel1'], $row['sourcemodule']);

            if($row['targetfieldlabel2'])
                $campos[] = vtranslate($row['targetfieldlabel2'], $row['sourcemodule']);

            if($row['targetfieldlabel3'])
                $campos[] = vtranslate($row['targetfieldlabel3'], $row['sourcemodule']);

            if($row['targetfieldlabel4'])
                $campos[] = vtranslate($row['targetfieldlabel4'], $row['sourcemodule']);

            if(count($campos) > 2)
                $campos[2] = "<br>".$campos[2];

            $relModFieldList[$i]['filter_fields_name'] = implode(", ", $campos);
            $i++;
        }
        return $relModFieldList;
    }

    /**
     * Function to get the list of Script models to be included
     * @param Vtiger_Request $request
     * @return <Array> - List of Vtiger_JsScript_Model instances
     */
    function getHeaderScripts(Vtiger_Request $request) {
        $headerScriptInstances = parent::getHeaderScripts($request);

        $jsFileNames = array(
            "layouts.v7.modules.VGSVisualPipeline.resources.VGSVisualPipelineSettings",
        );

        $jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
        $headerScriptInstances = array_merge($headerScriptInstances, $jsScriptInstances);
        return $headerScriptInstances;
    }

}
