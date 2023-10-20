<?php

global $adb;

$sql = 'SELECT accountid, acccontexternalnumber FROM vtiger_account WHERE LENGTH(acccontexternalnumber ) > 1';
$rs = $adb->pquery($sql);

while($fila = $adb->fetch_array($rs)){

	$accountid =  $fila["accountid"];
	$acccontexternalnumber =  ltrim($fila["acccontexternalnumber"],"0");

	$update = 'UPDATE vtiger_account SET acccontexternalnumber = ? WHERE accountid = ?';
	$result = $adb->pquery($update,array($acccontexternalnumber, $accountid));
	echo "<br>";
	echo $accountid. " - ".$acccontexternalnumber;

}
echo "<hr><br>";
echo "done!";

	