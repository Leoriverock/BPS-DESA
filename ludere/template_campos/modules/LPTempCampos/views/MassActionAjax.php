<?php

/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class LPTempCampos_MassActionAjax_View extends Vtiger_MassActionAjax_View {

    // Permitir crear registros pero no editar existentes
    public function checkPermission(Vtiger_Request $request) {

        if ($request->get('record')) {
            throw new AppException(vtranslate('LBL_PERMISSION_DENIED'));
        }

    }

}
