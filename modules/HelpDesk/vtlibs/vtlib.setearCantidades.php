<?php
require_once 'include/utils/utils.php';
require_once 'includes/Loader.php';
require_once 'libraries/nusoap/nusoap.php';
require_once 'config.ludere.php';
require_once 'modules/HelpDesk/WF_Funciones.php';

vimport('includes.http.Request');
vimport('includes.runtime.Globals');
vimport('includes.runtime.BaseModel');
vimport('includes.runtime.Controller');
vimport('includes.runtime.LanguageHandler');
vimport('modules.Install.models.Utils');

global $adb;

$sql_tickets = "SELECT ticketid 
                FROM vtiger_troubletickets
                JOIN vtiger_crmentity ON crmid = ticketid
                WHERE deleted = 0";
$tickets     = $adb->query($sql_tickets);
foreach ($tickets as $ticket) {
    $entity = VTEntityData::fromEntityId($adb, $ticket['ticketid']);
    WF_Cantidad_Llamadas($entity);
    WF_Cantidad_Tickets($entity);
}

echo "Ok";
