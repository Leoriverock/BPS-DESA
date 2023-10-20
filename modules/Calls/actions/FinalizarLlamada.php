<?php

class Calls_FinalizarLlamada_Action extends Vtiger_Action_Controller
{
    public function process(Vtiger_Request $request)
    {
        global $log;
        $log->info(__CLASS__);
        $log->debug($request->getAll());
        $params = $request->getAll();
        $result   = Calls_Module_Model::endCall($params);
        $response = new Vtiger_Response();
        $response->setResult($result);
        $response->emit();
    }
}
