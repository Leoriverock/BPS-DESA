<?php

include_once 'vtlib/Vtiger/Module.php';

$links = array(
    array('linklabel' => 'Tabla Pivot', 'type' => 'LISTVIEW'),
);

$tabid = Vtiger_Functions::getModuleId('Accounts');

foreach ($links as $link) {
    Vtiger_Link::deleteLink($tabid, $link['type'], $link['linklabel']);
}

echo "<br>Fin";
