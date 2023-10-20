<?php

global $adb;

$sql =" INSERT INTO `vtiger_app2tab` (`tabid`, `appname`, `sequence`, `visible`) VALUES ((SELECT tabid FROM vtiger_tab WHERE `name` = 'AtencionesWeb'), 'SUPPORT', 5, 1), ((SELECT tabid FROM vtiger_tab WHERE `name` = 'ConsultasWeb'), 'SUPPORT', 6, 1),
((SELECT tabid FROM vtiger_tab WHERE `name` = 'Parametrizaciones'), 'SUPPORT', 6, 1)";
//var_dump($sql);
echo "Exito!!";
$adb->pquery($sql);


