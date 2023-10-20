<?php

class LudereProAccounts_DetailView_Model extends Accounts_DetailView_Model
{

    /**
     * Function to get the detail view links (links and widgets)
     * @param <array> $linkParams - parameters which will be used to calicaulate the params
     * @return <array> - array of link models in the format as below
     *                   array('linktype'=>list of link models);
     */
    public function getDetailViewLinks($linkParams)
    {
        $currentUserModel = Users_Privileges_Model::getCurrentUserPrivilegesModel();
        $recordModel      = $this->getRecord();

        $linkModelList = parent::getDetailViewLinks($linkParams);

        //TODO: update the database so that these separate handlings are not required
        $index = 0;
        foreach ($linkModelList['DETAILVIEW'] as $link) {
            if ($link->linklabel == 'View History' || $link->linklabel == 'Send SMS') {
                unset($linkModelList['DETAILVIEW'][$index]);
            } else if ($link->linklabel == 'LBL_SHOW_ACCOUNT_HIERARCHY') {
                $linkURL       = 'index.php?module=Accounts&view=AccountHierarchy&record=' . $recordModel->getId();
                $link->linkurl = 'javascript:Accounts_Detail_Js.triggerAccountHierarchy("' . $linkURL . '");';
                unset($linkModelList['DETAILVIEW'][$index]);
                $linkModelList['DETAILVIEW'][$index] = $link;
            }
            $index++;
        }

        $CalendarActionLinks = array();
        $CalendarModuleModel = Vtiger_Module_Model::getInstance('Calendar');
        if ($currentUserModel->hasModuleActionPermission($CalendarModuleModel->getId(), 'CreateView')) {
            $CalendarActionLinks[] = array(
                'linktype'  => 'DETAILVIEW',
                'linklabel' => 'LBL_ADD_EVENT',
                'linkurl'   => $recordModel->getCreateEventUrl(),
                'linkicon'  => '',
            );

            $CalendarActionLinks[] = array(
                'linktype'  => 'DETAILVIEW',
                'linklabel' => 'LBL_ADD_TASK',
                'linkurl'   => $recordModel->getCreateTaskUrl(),
                'linkicon'  => '',
            );
        }

        $SMSNotifierModuleModel = Vtiger_Module_Model::getInstance('SMSNotifier');
        if (!empty($SMSNotifierModuleModel) && $currentUserModel->hasModulePermission($SMSNotifierModuleModel->getId())) {
            $basicActionLink = array(
                'linktype'  => 'DETAILVIEWBASIC',
                'linklabel' => 'LBL_SEND_SMS',
                'linkurl'   => 'javascript:Vtiger_Detail_Js.triggerSendSms("index.php?module=' . $this->getModule()->getName() .
                '&view=MassActionAjax&mode=showSendSMSForm","SMSNotifier");',
                'linkicon'  => '',
            );
            $linkModelList['DETAILVIEW'][] = Vtiger_Link_Model::getInstanceFromValues($basicActionLink);
        }

        $moduleModel = $this->getModule();
        if ($currentUserModel->hasModuleActionPermission($moduleModel->getId(), 'EditView')) {
            $massActionLink = array(
                'linktype'  => 'LISTVIEWMASSACTION',
                'linklabel' => 'LBL_TRANSFER_OWNERSHIP',
                'linkurl'   => 'javascript:Vtiger_Detail_Js.triggerTransferOwnership("index.php?module=' . $moduleModel->getName() . '&view=MassActionAjax&mode=transferOwnership")',
                'linkicon'  => '',
            );
            $linkModelList['DETAILVIEW'][] = Vtiger_Link_Model::getInstanceFromValues($massActionLink);
        }

        foreach ($CalendarActionLinks as $basicLink) {
            $linkModelList['DETAILVIEW'][] = Vtiger_Link_Model::getInstanceFromValues($basicLink);
        }

        return $linkModelList;
    }
}
