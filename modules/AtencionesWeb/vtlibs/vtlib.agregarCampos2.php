<?php 
$Vtiger_Utils_Log = true;

include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');
global $log;
$moduleInstance = Vtiger_Module::getInstance("AtencionesWeb");
//var_dump($moduleInstance);
//echo "<br><br><br>";
$blockInstance = Vtiger_Block::getInstance("LBL_ATENCIONESWEB_INFORMATION",$moduleInstance);
//var_dump($blockInstance);

$aw_mails = Vtiger_Field::getInstance('aw_mails', $module);
if(!$aw_mails){
    $aw_mails = new Vtiger_Field();
    $aw_mails->name = 'aw_mails';
    $aw_mails->label= 'Mails';
    $aw_mails->table =  'vtiger_atencionesweb';
    $aw_mails->column = 'aw_mails';
    $aw_mails->columntype = 'INT(3)';
    $aw_mails->uitype = 1;
    $aw_mails->typeofdata = 'I~O';
    $aw_mails->displaytype = 6;
    $blockInstance->addField($aw_mails);
}    

