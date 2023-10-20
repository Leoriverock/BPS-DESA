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

class HelpDesk_CheckRelation_Action extends Vtiger_Action_Controller {

  public function checkPermission(Vtiger_Request $request) {
		parent::checkPermission($request);
		return true;
	}
  
    public function process(Vtiger_Request $request) {
        global $adb;

        $sql = "SELECT acccontexternalnumber FROM vtiger_account WHERE accountid = ?";
        $result = $adb->pquery( $sql, array( $request->get('user') ) );
        $contribuyente = $adb->query_result( $result, 0, 'acccontexternalnumber' );

        $response = new Vtiger_Response();
        $response->setResult( $contribuyente != 0 ? HelpDesk_Module_Model::isRelation( $contribuyente, $request->get('empresa'), $request->get('codigoaportacion') ) : ["error" => false]);
        $response->emit();
    }

}