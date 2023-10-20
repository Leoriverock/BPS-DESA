<?php

// Turn on debugging level
$Vtiger_Utils_Log = true;

include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');

$module = Vtiger_Module::getInstance('Parametrizaciones');

$pt_grupo = Vtiger_Field::getInstance('pt_grupo', $module);
if ($pt_grupo) {
    global $adb;
    $sql = "UPDATE vtiger_field SET uitype = 53 WHERE fieldid = ?";
    $adb->pquery($sql, array($pt_grupo->id)); 
}

$assigned_user_id = Vtiger_Field::getInstance('assigned_user_id', $module);
if ($assigned_user_id) {
    $assigned_user_id->delete();
}

$blockInstance = Vtiger_Block::getInstance('LBL_PARAMETRIZACIONES_INFORMATION', $module);
if ($blockInstance){
   $pt_motivo = Vtiger_Field::getInstance("pt_motivo", $module);
    if(!$pt_motivo){
        $pt_motivo = new Vtiger_Field();
        $pt_motivo->name = "pt_motivo";
        $pt_motivo->label = "pt_motivo";
        $pt_motivo->table = $module->basetable;
        $pt_motivo->column = "pt_motivo";
        $pt_motivo->columntype = "VARCHAR(100)";
        $pt_motivo->uitype = 1;
        $pt_motivo->typeofdata = 'V~O';
        $blockInstance->addField($pt_motivo);
    }
}


echo "FIN!!"
?>