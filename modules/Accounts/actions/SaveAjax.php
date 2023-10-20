<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Accounts_SaveAjax_Action extends Vtiger_SaveAjax_Action {

	public function process(Vtiger_Request $request) {
		$accdocumenttype = $request->get('accdocumenttype');
		$accdocumentnumber = $request->get('accdocumentnumber');
		$acccountry = $request->get('acccountry');

		$response = new Vtiger_Response();

		try{
			$userFromBPS = Calls_Module_Model::getUserData([
				'countrycode' => $acccountry,
				'documenttype' => $accdocumenttype,
				'documentnumber' => $accdocumentnumber
			]);
			
	
			if( !$userFromBPS['error'] ){
				$user = Vtiger_Record_Model::getCleanInstance("Accounts");
				$user->set('accountname', $userFromBPS['resultado']['Nombre1'] . " " . $userFromBPS['resultado']['Nombre2'] . " " . $userFromBPS['resultado']['Apellido1'] . " " . $userFromBPS['resultado']['Apellido2'] );
				$user->set('accdocumenttype', self::findDocumentType( $accdocumenttype ) );
				$user->set('accdocumentnumber', $accdocumentnumber );
				$user->set('acccountry', self::findCountry( $acccountry ) );
				$user->set('mode', '');
				$activeAdminUser = Users::getActiveAdminUser();
				$user->set('assigned_user_id', $activeAdminUser->id);
				$user->save();
				
				$result['_recordLabel'] = $user->get('accountname');
				$result['_recordId'] = $user->getId();
				$response->setEmitType(Vtiger_Response::$EMIT_JSON);
				$response->setResult($result);
			}
			else{
				$response->setError($userFromBPS['resultado']);
			}
		}
		catch( Exception $e ){
			$response->setError($e->getMessage());
		}
		$response->emit();		
	}

	private function findDocumentType( $id ){
        global $adb;

        $sql = "SELECT value
                FROM lp_accdocumenttypes
                WHERE id = ?";
        
        $result = $adb->pquery( $sql, array( $id ) );
        return $adb->num_rows( $result ) > 0 ? $adb->query_result( $result, 0, 'value' ) : null;
    }

    private function findCountry( $id ){
        global $adb;

        $sql = "SELECT value
                FROM lp_acccountries
                WHERE id = ?";
        
        $result = $adb->pquery( $sql, array( $id ) );
        return $adb->num_rows( $result ) > 0 ? $adb->query_result( $result, 0, 'value' ) : null;
    }
}
