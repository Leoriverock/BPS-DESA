<?php

require_once 'include/utils/LP_utils.php';

class Accounts_Search_View extends Vtiger_Popup_View
{
    public function process(Vtiger_Request $request)
    {
        $viewer     = $this->getViewer($request);
        $moduleName = $this->getModule($request);

        $sourceModule = $request->get('src_module');
        $sourceField  = $request->get('src_field');
        $sourceRecord = $request->get('src_record');

        $viewer->assign('SOURCE_MODULE', $sourceModule);
        $viewer->assign('SOURCE_FIELD', $sourceField);
        $viewer->assign('SOURCE_RECORD', $sourceRecord);
        $viewer->assign('MODULE', $moduleName);

        $viewer->assign('DOCUMENT_TYPES', getDocumentTypes());
        $viewer->assign('COUNTRIES', getCountries());

        $viewer->view('Search.tpl', $moduleName);
    }
}
