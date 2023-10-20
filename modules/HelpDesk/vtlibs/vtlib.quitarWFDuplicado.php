<?php

global $adb;

$sql = 'UPDATE com_vtiger_workflows SET STATUS = 0 WHERE workflow_id = 34';
$result = $adb->pquery($sql);

echo "ok";


//ejecutarVtlib.php?module=HelpDesk&vtlib=vtlib.quitarWFDuplicado.php