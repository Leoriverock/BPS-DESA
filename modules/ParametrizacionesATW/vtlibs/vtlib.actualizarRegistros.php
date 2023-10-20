<?php

global $adb;

$sql = "DELETE FROM vtiger_pt_grupoatw";
$result = $adb->pquery($sql);

$sql = 'SELECT picklistid FROM vtiger_picklist WHERE NAME = ?';
$res = $adb->pquery($sql, array('pt_grupoatw'));

$picklistid = $adb->query_result($res,0,'picklistid');


$sql = 'SELECT groupname FROM vtiger_groups WHERE TYPE = ?';
$rs = $adb->pquery($sql, array('mail'));

$sortorderid = 1;




while($fila = $adb->fetch_array($rs)){

    $insert = 'INSERT INTO vtiger_pt_grupoatw  (pt_grupoatw,presence,picklist_valueid,sortorderid,color)
               VALUES (?,?,?,?,?)';
	$result = $adb->pquery($insert,array($fila['groupname'], 1,$picklistid,$sortorderid, null));
	$sortorderid = $sortorderid+1;
    

}
echo "<hr><br>";
echo "done!";

	