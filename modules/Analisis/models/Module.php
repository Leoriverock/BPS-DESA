<?php
/* +***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * *********************************************************************************** */

class Analisis_Module_Model extends Vtiger_Module_Model {


    /**
     * Function to check if the extension module is permitted for utility action
     * @return <boolean> true
     */
    public function isUtilityActionEnabled() {
        return true;
    }

}