<?php

/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class LPTempCampos_DetailView_Model extends Vtiger_DetailView_Model {

    public function getDetailViewLinks($linkParams) {

        // Obtener todos los links que vtiger agregaria siempre:
        $linkModelList = parent::getDetailViewLinks($linkParams);

        // Quitar los links de editar, etc:
        unset($linkModelList['DETAILVIEWBASIC']);
        
        // Recorrer todos los links que van en 'Mas', etc:
        foreach ($linkModelList['DETAILVIEW'] as $l => $link) {
            
            // Si el link es el de duplicar registro...
            if ($link->get('linklabel') == 'LBL_DUPLICATE') {

                // ... quitarlo para no permitir tal accion:
                unset($linkModelList['DETAILVIEW'][$l]);
            
            }
            
        }
        
        // Mostrar el resto:
        return $linkModelList;
    
    }

}
