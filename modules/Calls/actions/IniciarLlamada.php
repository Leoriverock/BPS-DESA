<?php

class Calls_IniciarLlamada_Action extends Vtiger_Action_Controller
{
    public function process(Vtiger_Request $request)
    {
        global $log;
        $log->info(__CLASS__);
        $params = $request->getAll();
        $log->debug($params);
        $result   = Calls_Module_Model::addCall($params);
        $response = new Vtiger_Response();
        $response->setResult($result);
        $response->emit();
    }
}
