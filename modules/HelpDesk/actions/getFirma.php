<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class HelpDesk_getFirma_Action extends Vtiger_Action_Controller {

	function checkPermission(Vtiger_Request $request) {
	}

	public function process(Vtiger_Request $request) {
		global $log,$adb,$current_user;
		$log->info("HelpDesk_getFirma_Action");
		$usuario_logueado = $current_user->id;
		$currentUserModel = Users_Record_Model::getCurrentUserModel();
		$log->info($currentUserModel);
		$mailer->Signature = $currentUserModel->get('signature');
		$log->info("mostrame la firmaaaaa");
		$log->info($mailer->Signature);
		//$mailer->Signature = utf8_decode($mailer->Signature);
		//$firma =  decode_html($mailer->Signature);
		
		//$log->info($firma);

		echo json_encode($mailer->Signature);

		
	}

}
?>