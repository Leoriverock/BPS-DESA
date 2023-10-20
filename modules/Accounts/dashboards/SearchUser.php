<?php

require_once 'include/utils/LP_utils.php';

class Accounts_SearchUser_Dashboard extends Vtiger_IndexAjax_View
{

    public function process(Vtiger_Request $request)
    {
        $currentUser  = Users_Record_Model::getCurrentUserModel();
        $moduleName   = $request->getModule();
        $linkId       = $request->get('linkid');
        $sourceModule = $request->get('src_module');
        $sourceField  = $request->get('src_field');
        $sourceRecord = $request->get('src_record');

        $viewer = $this->getViewer($request);

        $widget = Vtiger_Widget_Model::getInstance($linkId, $currentUser->getId());

        $viewer->assign('WIDGET', $widget);
        $viewer->assign('SOURCE_MODULE', $sourceModule);
        $viewer->assign('SOURCE_FIELD', $sourceField);
        $viewer->assign('SOURCE_RECORD', $sourceRecord);
        $viewer->assign('DOCUMENT_TYPES', getDocumentTypes());
        $viewer->assign('COUNTRIES', getCountries());
        $viewer->view('dashboards/SearchUser.tpl', $moduleName);
    }
}
