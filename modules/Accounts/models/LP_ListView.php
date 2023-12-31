<?php

class LudereProAccounts_ListView_Model extends Accounts_ListView_Model
{

    /**
     * Function to get the list of Mass actions for the module
     * @param <Array> $linkParams
     * @return <Array> - Associative array of Link type to List of  Vtiger_Link_Model instances for Mass Actions
     */
    public function getListViewMassActions($linkParams)
    {
        $massActionLinks = parent::getListViewMassActions($linkParams);

        $currentUserModel = Users_Privileges_Model::getCurrentUserPrivilegesModel();

        $SMSNotifierModuleModel = Vtiger_Module_Model::getInstance('SMSNotifier');
        if (!empty($SMSNotifierModuleModel) && $currentUserModel->hasModulePermission($SMSNotifierModuleModel->getId())) {
            $massActionLink = array(
                'linktype'  => 'LISTVIEWMASSACTION',
                'linklabel' => 'LBL_SEND_SMS',
                'linkurl'   => 'javascript:Vtiger_List_Js.triggerSendSms("index.php?module=' . $this->getModule()->getName() . '&view=MassActionAjax&mode=showSendSMSForm","SMSNotifier");',
                'linkicon'  => '',
            );
            $massActionLinks['LISTVIEWMASSACTION'][] = Vtiger_Link_Model::getInstanceFromValues($massActionLink);
        }

        $moduleModel = $this->getModule();
        if ($currentUserModel->hasModuleActionPermission($moduleModel->getId(), 'EditView')) {
            $massActionLink = array(
                'linktype'  => 'LISTVIEWMASSACTION',
                'linklabel' => 'LBL_TRANSFER_OWNERSHIP',
                'linkurl'   => 'javascript:Vtiger_List_Js.triggerTransferOwnership("index.php?module=' . $moduleModel->getName() . '&view=MassActionAjax&mode=transferOwnership")',
                'linkicon'  => '',
            );
            $massActionLinks['LISTVIEWMASSACTION'][] = Vtiger_Link_Model::getInstanceFromValues($massActionLink);
        }

        return $massActionLinks;
    }

    //se ocultan los enlaces que se muestran en el btn de "Más"
    public function getAdvancedLinks()
    {
        return [];
    }
}
