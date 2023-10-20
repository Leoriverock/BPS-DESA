<?php

include_once 'vtlib/Vtiger/Module.php';
global $adb;
$moduleLink = 'index.php?module=Accounts';

$links = array(
    array('linklabel' => 'Buscar usuario', 'linkurl' => $moduleLink . '&view=ShowWidget&name=SearchUser', 'type' => 'DASHBOARDWIDGET'),
);

$moduleInstance = Vtiger_Module::getInstance('Home');

if ($moduleInstance) {
    foreach ($links as $link) {
        $query = 'SELECT * FROM vtiger_links WHERE tabid = ? AND linklabel = ?';

        $result = $adb->pquery($query, array($moduleInstance->getId(), $link['linklabel'])); //Busca si existe el link en la base de datos

        if ($adb->num_rows($result) == 0) {
            // En caso de que no exista lo crea
            $moduleInstance->addLink($link['type'], $link['linklabel'], $link['linkurl']);
        }
    }
}

echo "<br>Fin";