<?php

// Turn on debugging level
$Vtiger_Utils_Log = true;

include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');

$module = Vtiger_Module::getInstance('AtencionPresencial');
//
if($module){
	$blockInstance = Vtiger_Block::getInstance('LBL_ATENCIONPRESENCIAL_INFORMATION2', $module);

	$ap_lugcod = Vtiger_Field::getInstance("ap_lugcod", $module);
	if(!$ap_lugcod){
		$ap_lugcod = new Vtiger_Field();
		$ap_lugcod->name = "ap_lugcod";
		$ap_lugcod->label = "LugCod";
		$ap_lugcod->table = 'vtiger_atencionpresencial';
		$ap_lugcod->column = "ap_lugcod";
		$ap_lugcod->columntype = "VARCHAR(100)";
	 	$ap_lugcod->uitype = 1;
	 	$ap_lugcod->typeofdata = 'V~O';
		$blockInstance->addField($ap_lugcod);
	}

	$ap_numerocod = Vtiger_Field::getInstance("ap_numerocod", $module);
	if(!$ap_numerocod){
		$ap_numerocod = new Vtiger_Field();
		$ap_numerocod->name = "ap_numerocod";
		$ap_numerocod->label = "NumeroCod";
		$ap_numerocod->table = 'vtiger_atencionpresencial';
		$ap_numerocod->column = "ap_numerocod";
		$ap_numerocod->columntype = "VARCHAR(100)";
	 	$ap_numerocod->uitype = 1;
	 	$ap_numerocod->typeofdata = 'V~O';
		$blockInstance->addField($ap_numerocod);
	}

	$ap_numerofecha = Vtiger_Field::getInstance("ap_numerofecha", $module);
	if(!$ap_numerofecha){
		$ap_numerofecha = new Vtiger_Field();
		$ap_numerofecha->name = "ap_numerofecha";
		$ap_numerofecha->label = "NumeroFecha";
		$ap_numerofecha->table = $module->basetable;
		$ap_numerofecha->column = "ap_numerofecha";
		$ap_numerofecha->columntype = "VARCHAR(100)";
	 	$ap_numerofecha->uitype = 1;
	 	$ap_numerofecha->typeofdata = 'V~O';
		$blockInstance->addField($ap_numerofecha);
	}

	 $ap_numerohora =  Vtiger_Field::getInstance("ap_numerohora",$module);
	    if(!$ap_numerohora){
	        $ap_numerohora = new Vtiger_Field();
	        $ap_numerohora->name = "ap_numerohora";
	        $ap_numerohora->label = "NumeroHora";
	        $ap_numerohora->columntype = 'VARCHAR(15)';
	        $ap_numerohora->uitype = 1;
	        $ap_numerohora->typeofdata = 'V~O';
	        $ap_numerohora->table = $module->basetable;
	        $ap_numerohora->column = "ap_numerohora";
	        $blockInstance->addField($ap_numerohora);
	}
	$ap_tramite = Vtiger_Field::getInstance("ap_tramite", $module);
	if(!$ap_tramite){
		$ap_tramite = new Vtiger_Field();
		$ap_tramite->name = "ap_tramite";
		$ap_tramite->label = "ap_tramite";
		$ap_tramite->table = $module->basetable;
		$ap_tramite->column = "ap_tramite";
		$ap_tramite->columntype = "VARCHAR(100)";
	 	$ap_tramite->uitype = 1;
	 	$ap_tramite->typeofdata = 'V~O';
		$blockInstance->addField($ap_tramite);
	}
	/*$ap_datosextras = Vtiger_Field::getInstance('ap_datosextras', $module);
	if(!$ap_datosextras){
		$ap_datosextras = new Vtiger_Field();
	    $ap_datosextras->name = 'ap_datosextras';
	    $ap_datosextras->label= 'ap_datosextras';
	    $ap_datosextras->table =  'vtiger_atencionpresencial';
	    $ap_datosextras->column = 'ap_datosextras';
	    $ap_datosextras->columntype = 'TEXT';
	    $ap_datosextras->uitype = 19;
	    $ap_datosextras->typeofdata = 'V~O';
	    $ap_datosextras->displaytype = 2;
	    $blockInstance->addField($ap_datosextras);
	}*/

}