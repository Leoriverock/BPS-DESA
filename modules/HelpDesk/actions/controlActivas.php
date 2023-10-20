<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

require_once 'config.ludere.php';

class HelpDesk_controlActivas_Action extends Vtiger_Action_Controller {

  public function checkPermission(Vtiger_Request $request) {
		parent::checkPermission($request);
		return true;
	}
  
    public function process(Vtiger_Request $request) {
        global $adb;

        $currentUser = Users_Record_Model::getCurrentUserModel();
        $atencionWeb = $currentUser->getAtencionActiva();
        $llamada = Calls_Module_Model::getLlamadaActiva(Users_Record_Model::getCurrentUserModel()->id); 
        $AtencionPresencial = AtencionPresencial_Module_Model::getAtencionpPActiva(Users_Record_Model::getCurrentUserModel()->id);
        
        if($llamada == null){   $llamada = '';   };
        if($AtencionPresencial == null){   $AtencionPresencial = '';   };
        if($atencionWeb == null){   $atencionWeb = '';   };

        $result = array('success' =>  true, 'atencionWeb' => $atencionWeb, 'llamada' => $llamada,'AtencionPresencial' => $AtencionPresencial);
        $response = new Vtiger_Response();
        $response->setResult($result);
        $response->emit();  
    }

}