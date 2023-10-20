<?php

global $adb; 
$sql = "UPDATE vtiger_relatedlists 
		SET actions = '' 
		WHERE
		tabid = (SELECT tabid FROM vtiger_tab WHERE tablabel = ?) 
		OR tabid =  (SELECT tabid FROM vtiger_tab WHERE tablabel = ?) 
		AND LABEL = ?";
$rs = $adb->pquery($sql,array('AtencionPresencial','AtencionesWeb','HelpDesk'));

echo "Se quitaron los botones Añadir de las relaciones";