<?php
require_once 'include/utils/utils.php';
require_once 'includes/Loader.php';
require_once 'libraries/nusoap/nusoap.php';
require_once 'config.ludere.php';
require_once 'modules/Calls/WF_Funciones.php';

vimport('includes.http.Request');
vimport('includes.runtime.Globals');
vimport('includes.runtime.BaseModel');
vimport('includes.runtime.Controller');
vimport('includes.runtime.LanguageHandler');
vimport('modules.Install.models.Utils');

global $adb;

$sql_calls = "SELECT callsid
                FROM vtiger_calls
                JOIN vtiger_crmentity ON crmid = callsid
                WHERE deleted = 0";
$calls = $adb->query($sql_calls);
foreach ($calls as $call) {
    $entity = VTEntityData::fromEntityId($adb, $call['callsid']);
    WF_Cantidad_Incidencias($entity);
    WF_Cantidad_Comentarios($entity);
}

echo "Ok";
