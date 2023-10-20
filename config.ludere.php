<?php

define('AMBIENTE', 'DESA');

define('WS_URL', 'https://appuypruebas.migrate.info/InvoiCy/');
$ambiente = strtolower(AMBIENTE);
$config   = "config.ludere.${ambiente}.php";
if (file_exists($config)) {
    require_once $config;
}
define('HINT_ACTIVO', false);
