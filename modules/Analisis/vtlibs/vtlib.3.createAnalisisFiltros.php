<?php

error_reporting(E_ERROR);

// ini_set("display_errors", 1);

require_once('include/utils/utils.php');

global $adb, $log;

echo "Creando tabla lp_analisis_filtros".PHP_EOL;


$adb->query("CREATE TABLE `lp_analisis_filtros` (
	`id` INT (11),
	`nombre` VARCHAR (100),
	`filas` VARCHAR (400),
	`columnas` VARCHAR (400),
	`filtros` VARCHAR (400),
	`module` VARCHAR (400),
	`viewname` INTEGER (10),  
	`usuario` VARCHAR(15),
	`publico` INT(1) DEFAULT 1, 
	`deleted` INT(1) DEFAULT 0,
	PRIMARY KEY (`id`)
);");

echo "OK".PHP_EOL;