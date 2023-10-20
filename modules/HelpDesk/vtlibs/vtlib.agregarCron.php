<?php

// Turn on debugging level
$Vtiger_Utils_Log = true;

include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');
require_once 'include/utils/utils.php';

Vtiger_Cron::register( 'Asignado de Tickets', 'lp_cron_cambioAsignado.php', 300, 'HelpDesk', 1, 16, 'Recommended frequency for Asignado de Tickets is 15 mins');
?>