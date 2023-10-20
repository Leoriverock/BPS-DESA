<?php

include_once 'vtlib/Vtiger/Module.php';

global $adb;
if (empty($adb)) {
    $adb = PearDatabase::getInstance();
}

$MODULENAME = 'Accounts';

$moduleInstance = Vtiger_Module::getInstance($MODULENAME);

$campos = ['emailoptout', 'isconvertedfromlead', 'notify_owner'];

foreach ($campos as $campo) {
    $fieldInstance = Vtiger_Field::getInstance($campo, $moduleInstance);
    if ($fieldInstance) {
        $adb->pquery('UPDATE vtiger_field SET presence=1 WHERE fieldid=?', array($fieldInstance->id));
    }
}
