<?php

global $adb;

$insert = "INSERT INTO vtiger_modtracker_tabs (tabid, visible)  VALUES ((SELECT tabid FROM vtiger_tab WHERE NAME = 'ConsultasWeb' ),1)";
$res = $adb->pquery($insert);

echo "Se genero la auditoria para ConsultasWeb";