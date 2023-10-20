<?php

/* * *******************************************************************************
 * The content of this file is subject to the PDF Maker Free license.
 * ("License"); You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is IT-Solutions4You s.r.o.
 * Portions created by IT-Solutions4You s.r.o. are Copyright(C) IT-Solutions4You s.r.o.
 * All Rights Reserved.
 * ****************************************************************************** */

class LPTempFlujos_ModalEdicionArista_View extends Vtiger_BasicAjax_View {

    public function process(Vtiger_Request $request) {
        $viewer = $this->getViewer($request);
        $viewer->view("ModalEdicionArista.tpl", 'LPTempFlujos');
    }
}

