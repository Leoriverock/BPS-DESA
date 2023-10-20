<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Vtiger_GroupPref_Action extends Vtiger_Action_Controller {
    
    public function checkPermission(Vtiger_Request $request) {
    	$currentUser = Users_Record_Model::getCurrentUserModel();
    	$id = $request->get('record');
    	if($id != $currentUser->getId()){
    		throw new AppException('LBL_RECORD_PERMISSION_DENIED');
    	}
		return true;
	}

	public function process(Vtiger_Request $request) {
		global $adb,$log;
		$moduleName = $request->getModule();
		$recordId = $request->get('record');
		$groupid = $request->get('groupid');
		$result = array('success' => true);
		$recordModel = Vtiger_Record_Model::getInstanceById($recordId, 'Users');
		$log->info("Vtiger_GroupPref_Action");
		$log->info($groupid);
		/*$recordModel->set('us_grupopref', $groupid);
		$recordModel->set('mode', 'edit');
		$recordModel->save();*/
		$sql = "UPDATE vtiger_users SET us_grupopref = ? WHERE id = ?";
		$adb->pquery($sql, array($groupid, $recordId));
		$response = new Vtiger_Response();
		$response->setResult($result);
		$response->emit();
	}
}
