<?php

global $adb;

$sql_tema = "UPDATE vtiger_field 
			 SET typeofdata=?, presence=?, quickcreate=?, masseditable=?,defaultvalue=?,summaryfield=?,headerfield=?, uitype=?, fieldlabel=?
			 WHERE fieldid = (SELECT fieldid FROM vtiger_field WHERE fieldname = 'aw_tema')";
$result = $adb->pquery($sql_tema, array('V~M',2,2,1,'',1,0,10,'Tema'));

$sql_estado = "UPDATE vtiger_field 
			   SET typeofdata=?,presence=?,quickcreate=?,masseditable=?,defaultvalue=?,summaryfield=?,headerfield=?, uitype=?, fieldlabel=?
			   WHERE fieldid= (SELECT fieldid FROM vtiger_field WHERE fieldname = 'aw_estado')";
$result = $adb->pquery($sql_estado, array('V~M',2,2,1,'',1,0,16,'Estado'));

$sql_persona = "UPDATE vtiger_field 
			   SET typeofdata=?,presence=?,quickcreate=?,masseditable=?,defaultvalue=?,summaryfield=?,headerfield=?, uitype=?, fieldlabel=? 
			   WHERE fieldid= (SELECT fieldid FROM vtiger_field WHERE fieldname = 'aw_persona')";
$result = $adb->pquery($sql_persona, array('V~M',2,2,1,'',1,0,16,'Persona'));



echo "Campos actualizados";

