<?php

class Accounts_DisplayName_Action extends Vtiger_Action_Controller
{

    public function process(Vtiger_Request $request)
    {

        $id      = $request->get('record');
        $recordModel = Vtiger_Record_Model::getInstanceById($id, 'Accounts');
        $result      = array('record' => $recordModel->getId(), 'recordLabel' => $recordModel->getName(), 'info' => $recordModel->getData());

        $response = new Vtiger_Response();
        $response->setResult($result);
        $response->emit();
    }

}
