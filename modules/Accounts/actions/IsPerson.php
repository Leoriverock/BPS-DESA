<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Accounts_IsPerson_Action extends Vtiger_Action_Controller {

    public function checkPermission(Vtiger_Request $request) {
        parent::checkPermission($request);
        return true;
    }

	public function process(Vtiger_Request $request) {
        global $adb;

        $sql = "SELECT acccontexternalnumber
                FROM vtiger_account
                WHERE accountid = ?";
        $result = $adb->pquery( $sql, array( $request->get('accountid') ) );
        $acccontexternalnumber = $adb->query_result( $result, 0, 'acccontexternalnumber' );
		$response = new Vtiger_Response();
        $response->setResult([ 'isPerson' => empty($acccontexternalnumber) || $acccontexternalnumber == 0 ]);
        $response->emit();		
	}
}
?>