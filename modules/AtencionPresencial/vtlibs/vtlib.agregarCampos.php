<?php

// Turn on debugging level
$Vtiger_Utils_Log = true;

include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');

$module = Vtiger_Module::getInstance('AtencionPresencial');

if($module){
	$blockInstance = Vtiger_Block::getInstance('LBL_ATENCIONPRESENCIAL_INFORMATION', $module);

	$ap_sectorid = Vtiger_Field::getInstance("ap_sectorid", $module);
	if(!$ap_sectorid){
		$ap_sectorid = new Vtiger_Field();
		$ap_sectorid->name = "ap_sectorid";
		$ap_sectorid->label = "ap_sectorid";
		$ap_sectorid->table = 'vtiger_atencionpresencial';
		$ap_sectorid->column = "ap_sectorid";
		$ap_sectorid->columntype = "VARCHAR(100)";
		$ap_sectorid->displaytype = 6;
	 	$ap_sectorid->uitype = 1;
	 	$ap_sectorid->typeofdata = 'V~O';
		$blockInstance->addField($ap_sectorid);
	}

	$ap_sector = Vtiger_Field::getInstance("ap_sector", $module);
	if(!$ap_sector){
		$ap_sector = new Vtiger_Field();
		$ap_sector->name = "ap_sector";
		$ap_sector->label = "ap_sector";
		$ap_sector->table = 'vtiger_atencionpresencial';
		$ap_sector->column = "ap_sector";
		$ap_sector->columntype = "VARCHAR(100)";
		$ap_sector->displaytype = 2;
	 	$ap_sector->uitype = 1;
	 	$ap_sector->typeofdata = 'V~O';
		$blockInstance->addField($ap_sector);
	}
	$ap_tipoconsulta = Vtiger_Field::getInstance('ap_tipoconsulta', $module);
	if(!$ap_tipoconsulta){
		$ap_tipoconsulta = new Vtiger_Field();
	    $ap_tipoconsulta->name = 'ap_tipoconsulta';
	    $ap_tipoconsulta->label= 'Resultado';
	    $ap_tipoconsulta->table =  'vtiger_atencionpresencial';
	    $ap_tipoconsulta->column = 'ap_tipoconsulta';
	    $ap_tipoconsulta->columntype = 'VARCHAR(20)';
	    $ap_tipoconsulta->uitype = 16;
	    $ap_tipoconsulta->typeofdata = 'V~O';
	    $ap_tipoconsulta->displaytype = 2;
	    $blockInstance->addField($ap_tipoconsulta);
	    $ap_tipoconsulta->setPicklistValues( Array ('Atendido con éxito','Falta documentación','No corresponde el trámite','Desiste del trámite'));
	}

	$blockInstance = Vtiger_Block::getInstance('LBL_ATENCIONPRESENCIAL_INFORMATION2', $module);
	if (!$blockInstance){
		$blockInstance = new Vtiger_Block();
		$blockInstance->label = 'LBL_ATENCIONPRESENCIAL_INFORMATION2';
		$module->addBlock($blockInstance);
	}

	$ap_datosextras = Vtiger_Field::getInstance('ap_datosextras', $module);
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
	}
	
}

echo "ok";
?>