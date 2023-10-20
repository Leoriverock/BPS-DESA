<?php

include_once 'vtlib/Vtiger/Module.php';

$Vtiger_Utils_Log = true;

$MODULENAME = 'HelpDesk';

$moduleInstance = Vtiger_Module::getInstance($MODULENAME);

$fieldName     = 'contact_id';
$fieldInstance = Vtiger_Field::getInstance($fieldName, $moduleInstance);
if ($fieldInstance) {
    /*$fieldInstance->unsetRelatedModules(array('Contacts'));
    $fieldInstance->setRelatedModules(array('Accounts'));*/
}

$blockName     = 'LBL_TICKET_INFORMATION';
$blockInstance = Vtiger_Block::getInstance($blockName, $moduleInstance);

$fieldName     = 'tickettema';
$fieldInstance = Vtiger_Field::getInstance($fieldName, $moduleInstance);
if (!$fieldInstance) {
    $fieldInstance             = new Vtiger_Field();
    $fieldInstance->name       = $fieldName;
    $fieldInstance->label      = $fieldName;
    $fieldInstance->table      = $moduleInstance->basetable;
    $fieldInstance->column     = $fieldInstance->name;
    $fieldInstance->uitype     = 16;
    $fieldInstance->columntype = 'VARCHAR(100)';
    $fieldInstance->typeofdata = 'V~M';
    $blockInstance->addField($fieldInstance);
    $fieldInstance->setPicklistValues(array('Tema a', 'Tema b'));
}

$fieldName     = 'ticketcategories';
$fieldInstance = Vtiger_Field::getInstance($fieldName, $moduleInstance);
if ($fieldInstance) {
    //⚠ para evitar duplicar picklistvalues
    /*$picklistValues       = vtlib_getPicklistValues($fieldName);
    $pickListValuesNuevos = array('Consulta', 'Tramitación', 'Reclamo', 'Sugerencia', 'Queja', 'No corresponde');
    $postFiltro           = array_values(array_filter($pickListValuesNuevos, function ($val) use ($picklistValues) {
        return !in_array($val, $picklistValues);
    }));
    if (count($postFiltro) > 0) {
        $fieldInstance->setPicklistValues($postFiltro);
    }*/
}

$fieldName = 'ticketcanal';
$fieldInstance = Vtiger_Field::getInstance($fieldName, $moduleInstance);
if (!$fieldInstance) {
    $fieldInstance             = new Vtiger_Field();
    $fieldInstance->name       = $fieldName;
    $fieldInstance->label      = $fieldName;
    $fieldInstance->table      = $moduleInstance->basetable;
    $fieldInstance->column     = $fieldInstance->name;
    $fieldInstance->uitype     = 16;
    $fieldInstance->columntype = 'VARCHAR(100)';
    $fieldInstance->typeofdata = 'V~O';
    $blockInstance->addField($fieldInstance);
    $fieldInstance->setPicklistValues(array('Teléfono', 'Mail'));
}

$fieldName = 'ticketempresa';
$fieldInstance = Vtiger_Field::getInstance($fieldName, $moduleInstance);
if (!$fieldInstance) {
    $fieldInstance             = new Vtiger_Field();
    $fieldInstance->name       = $fieldName;
    $fieldInstance->label      = $fieldName;
    $fieldInstance->table      = $moduleInstance->basetable;
    $fieldInstance->column     = $fieldInstance->name;
    $fieldInstance->uitype     = 1;
    $fieldInstance->columntype = 'VARCHAR(30)';
    $fieldInstance->typeofdata = 'V~O';
    $blockInstance->addField($fieldInstance);
}

$fieldName = 'ticketcodigoaportacion';
$fieldInstance = Vtiger_Field::getInstance($fieldName, $moduleInstance);
if (!$fieldInstance) {
    $fieldInstance             = new Vtiger_Field();
    $fieldInstance->name       = $fieldName;
    $fieldInstance->label      = $fieldName;
    $fieldInstance->table      = $moduleInstance->basetable;
    $fieldInstance->column     = $fieldInstance->name;
    $fieldInstance->uitype     = 15;
    $fieldInstance->columntype = 'VARCHAR(100)';
    $fieldInstance->typeofdata = 'V~O';
    $blockInstance->addField($fieldInstance);
    $fieldInstance->setPicklistValues( Array(
        "1 - Industria y comercio",
        "2 – Civil", 
        "3 – Rural", 
        "4 – Construcción", 
        "5 – Notarial",
        "6 – Bancaria", 
        "7 - Trabajadores a domicilio",  
        "11 - Servicios personales no profesionales y profesionales", 
        "48 - Servicio doméstico" ) );
}

$fieldName = 'ticketnumeroexterno';
$fieldInstance = Vtiger_Field::getInstance($fieldName, $moduleInstance);
if (!$fieldInstance) {
    $fieldInstance             = new Vtiger_Field();
    $fieldInstance->name       = $fieldName;
    $fieldInstance->label      = $fieldName;
    $fieldInstance->table      = $moduleInstance->basetable;
    $fieldInstance->column     = $fieldInstance->name;
    $fieldInstance->uitype     = 1;
    $fieldInstance->columntype = 'VARCHAR(30)';
    $fieldInstance->typeofdata = 'V~O';
    $blockInstance->addField($fieldInstance);
}

$fieldName = 'ticketnumeroexterno';
$fieldInstance = Vtiger_Field::getInstance($fieldName, $moduleInstance);
if (!$fieldInstance) {
    $fieldInstance             = new Vtiger_Field();
    $fieldInstance->name       = $fieldName;
    $fieldInstance->label      = $fieldName;
    $fieldInstance->table      = $moduleInstance->basetable;
    $fieldInstance->column     = $fieldInstance->name;
    $fieldInstance->uitype     = 1;
    $fieldInstance->columntype = 'VARCHAR(30)';
    $fieldInstance->typeofdata = 'V~O';
    $blockInstance->addField($fieldInstance);
}

$fieldName = 'ticketdenominacion';
$fieldInstance = Vtiger_Field::getInstance($fieldName, $moduleInstance);
if (!$fieldInstance) {
    $fieldInstance              = new Vtiger_Field();
    $fieldInstance->name        = $fieldName;
    $fieldInstance->label       = $fieldName;
    $fieldInstance->table       = $moduleInstance->basetable;
    $fieldInstance->column      = $fieldInstance->name;
    $fieldInstance->uitype      = 1;
    $fieldInstance->columntype  = 'VARCHAR(30)';
    $fieldInstance->typeofdata  = 'V~O';
    $fieldInstance->displaytype = 1;
    $blockInstance->addField($fieldInstance);
}

$fieldName = 'ticketcantillam';
$fieldInstance = Vtiger_Field::getInstance($fieldName, $moduleInstance);
if (!$fieldInstance) {
    $fieldInstance              = new Vtiger_Field();
    $fieldInstance->name        = $fieldName;
    $fieldInstance->label       = $fieldName;
    $fieldInstance->table       = $moduleInstance->basetable;
    $fieldInstance->column      = $fieldInstance->name;
    $fieldInstance->uitype      = 7;
    $fieldInstance->columntype  = 'INTEGER';
    $fieldInstance->typeofdata  = 'NN~O~10,0';
    $fieldInstance->displaytype = 1;
    $blockInstance->addField($fieldInstance);
}

$fieldName = 'ticketgrupo';
$fieldInstance = Vtiger_Field::getInstance($fieldName, $moduleInstance);
if (!$fieldInstance) {
    $fieldInstance              = new Vtiger_Field();
    $fieldInstance->name        = $fieldName;
    $fieldInstance->label       = $fieldName;
    $fieldInstance->table       = $moduleInstance->basetable;
    $fieldInstance->column      = $fieldInstance->name;
    $fieldInstance->uitype      = 15;
    $fieldInstance->columntype  = 'VARCHAR(100)';
    $fieldInstance->typeofdata  = 'V~M';
    $fieldInstance->displaytype = 1;
    $blockInstance->addField($fieldInstance);
}

$fieldName = 'ticketnroobra';
$fieldInstance = Vtiger_Field::getInstance($fieldName, $moduleInstance);
if (!$fieldInstance) {
    $fieldInstance              = new Vtiger_Field();
    $fieldInstance->name        = $fieldName;
    $fieldInstance->label       = $fieldName;
    $fieldInstance->table       = $moduleInstance->basetable;
    $fieldInstance->column      = $fieldInstance->name;
    $fieldInstance->uitype      = 1;
    $fieldInstance->columntype  = 'VARCHAR(100)';
    $fieldInstance->typeofdata  = 'V~O';
    $fieldInstance->displaytype = 1;
    $blockInstance->addField($fieldInstance);
}
