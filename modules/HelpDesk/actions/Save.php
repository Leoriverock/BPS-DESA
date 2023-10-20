<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class HelpDesk_Save_Action extends Vtiger_Save_Action {

	/**
	 * Function to save record
	 * @param <Vtiger_Request> $request - values of the record
	 * @return <RecordModel> - record Model of saved record
	 */
	public function saveRecord($request) {
		$recordModel = $this->getRecordModelFromRequest($request);
		
		$recordModel->save();
		global $adb, $log;

		$moduleName = 'HelpDesk';
		$log->info("estoy en saveRecord ");
		//$log->info($recordModel);

		$recordID = $request->get('record');
		$ticketnumeroexterno = $request->get('ticketnumeroexterno');
		if ($ticketnumeroexterno == ''){
			$sql = 'UPDATE vtiger_troubletickets SET ticketcodigoaportacion = NULL, ticketnroobra = NULL, ticketnumeroexterno = NULL
					WHERE ticketid = ?';
			$result = $adb->pquery($sql,array($recordID)); 
		}



		return $recordModel;
	}
}
