<?php

class Accounts_Search_Action extends Vtiger_Action_Controller
{

    public function process(Vtiger_Request $request)
    {

        global $log;
        $log->info(__CLASS__ . __FUNCTION__);
        $log->debug($request->getAll());
        $params      = $request->getAll();
        $recordModel = Accounts_Record_Model::getInstanceBySearch($params);
        $log->info("process de account");
        $log->info($recordModel);
        $result      = array('record' => $recordModel->getId(), 'recordLabel' => $recordModel->getName(), 'info' => $recordModel->getData());

        $response = new Vtiger_Response();
        $response->setResult($result);
        $response->emit();
    }

}
