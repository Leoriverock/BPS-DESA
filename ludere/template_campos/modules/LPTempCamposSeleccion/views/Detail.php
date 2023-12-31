<?php

/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class LPTempCamposSeleccion_Detail_View extends Vtiger_Detail_View {

	public function process(Vtiger_Request $request) {

		$viewer = $this->getViewer($request);

		$viewer->assign('POSIBLES_TS_CAMPO', LPTempCamposSeleccion_Views_Helper::get_posibles_ts_campo());
		$viewer->assign('POSIBLES_TS_VALOR', LPTempCamposSeleccion_Views_Helper::get_posibles_ts_valor());

		parent::process($request);

	}

}