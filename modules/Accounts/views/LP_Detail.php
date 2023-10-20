<?php

require_once 'config.ludere.php';
require_once 'include/utils/LP_utils.php';

class LudereProAccounts_Detail_View extends Accounts_Detail_View
{

    public function showModuleBasicView($request)
    {
        global $log;
        $log->info("estoy en LudereProAccounts_Detail_View");
        //$log->info($request);
        $recordId   = $request->get('record');
        $moduleName = $request->getModule();

        if (!$this->record) {
            $this->record = Vtiger_DetailView_Model::getInstance($moduleName, $recordId);
        }
        $recordModel  = $this->record->getRecord();
        
        $lastsTickets = $recordModel->getLastsTickets();
        $lastsAtencionesWeb = $recordModel->getLastsAtencionesWeb();

        $viewer = $this->getViewer($request);

        if ($recordModel->get('accpersid')) {
            $idCountry               = findCountryIdByValue($recordModel->get('acccountry'));
            $idDocumentType          = findDocumentTypeIdByValue($recordModel->get('accdocumenttype'));
            $nroDoc                  = $recordModel->get('accdocumentnumber');
            $linkEscritorioCiudadano = URL_ESCRITORIO_CIUDADANO . "?pais={$idCountry}&tipoDoc={$idDocumentType}&nroDoc={$nroDoc}";
            $viewer->assign('LINK_ESCRITORIO_CIUDADANO', $linkEscritorioCiudadano);
            $linkPortadocumentos = URL_PORTADOCUMENTOS."?show=View&viewId=c9d257ac-6af7-4e59-b2dd-f21c5b91b303&PaisDeDocumentoDeIdentificacion={$idCountry}&&TipoDeDocumentoDeIdentificacion={$idDocumentType}&NroDeDocumentoDeIdentificacion={$nroDoc}";
            $viewer->assign('LINK_PORTADOCUMENTOS', $linkPortadocumentos);
        }

        
        $accountry = trim(strtolower($recordModel->get('acccountry')));
        //$log->info($accountry);
        $viewer->assign('ACCOUNTRY', $accountry);
        $viewer->assign('LASTS_TICKETS', $lastsTickets);
        $viewer->assign('LASTS_ATENCIONESWEB', $lastsAtencionesWeb);

        return parent::showModuleBasicView($request);
    }
}
