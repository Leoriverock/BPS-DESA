<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class HelpDesk_GetUserGroups_Action extends Vtiger_Action_Controller {

  public function checkPermission(Vtiger_Request $request) {
		parent::checkPermission($request);
		return true;
	}
  
  public function process(Vtiger_Request $request) {

    global $adb;
    

    $sql = "SELECT g.groupid,g.groupname FROM vtiger_users2group ug INNER JOIN vtiger_groups g ON ug.groupid = g.groupid WHERE ug.userid = ?";
    $result = $adb->pquery( $sql, array( $request->get( 'userid' ) ) );

    $groups = array();
    foreach( $result as $rs ){
        $groups[] = array(
            "groupid" => $rs['groupid'],
            "groupname" => $rs['groupname']
        );
    }

    $response = new Vtiger_Response();
    $response->setResult($groups);
    $response->emit();
  }

}