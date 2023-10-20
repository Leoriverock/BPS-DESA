<?php

include_once 'vtlib/Vtiger/Module.php';

$Vtiger_Utils_Log = true;

$MODULENAME = 'Relationship';

//creamos el modulo y verifico
$moduleInstance = Vtiger_Module::getInstance($MODULENAME);
if (!$moduleInstance) {
    $moduleInstance         = new Vtiger_Module();
    $moduleInstance->name   = $MODULENAME;
    $moduleInstance->parent = 'Tools';
    $moduleInstance->save();
    $moduleInstance->initWebservice();
    $moduleInstance->initTables();
}

$block = Vtiger_Block::getInstance('LBL_RELATIONSHIP_INFORMATION', $moduleInstance);
if (!$block) {
    $block        = new Vtiger_Block();
    $block->label = "LBL_RELATIONSHIP_INFORMATION"; //Información de relaciones
    $moduleInstance->addBlock($block);
}

$fieldName     = 'nrorelacion';
$fieldInstance = Vtiger_Field::getInstance($fieldName, $moduleInstance);
if (!$fieldInstance) {
    $fieldInstance             = new Vtiger_Field();
    $fieldInstance->name       = $fieldName;
    $fieldInstance->label      = $fieldName;
    $fieldInstance->table      = $moduleInstance->basetable;
    $fieldInstance->column     = $fieldInstance->name;
    $fieldInstance->columntype = 'VARCHAR(10)';
    $fieldInstance->uitype     = 4;
    $fieldInstance->typeofdata = 'V~M';
    $block->addField($fieldInstance);
}


$fieldInstance = Vtiger_Field::getInstance('ticketa', $moduleInstance);
if (!$fieldInstance) {
    $fieldInstance             = new Vtiger_Field();
    $fieldInstance->name       = 'ticketa';
    $fieldInstance->label      = 'ticketa';
    $fieldInstance->column     = $fieldInstance->name;
    $fieldInstance->uitype     = 10;
    $fieldInstance->columntype = 'VARCHAR(100)';
    $fieldInstance->typeofdata = 'V~O';
    $block->addField($fieldInstance);
    $fieldInstance->headerfield  = 1;
    $fieldInstance->summaryfield = 1;
    $fieldInstance->setRelatedModules(array('HelpDesk'));
}
$ticketA = $fieldInstance;

$fieldInstance = Vtiger_Field::getInstance('ticketb', $moduleInstance);
if (!$fieldInstance) {
    $fieldInstance               = new Vtiger_Field();
    $fieldInstance->name         = 'ticketb';
    $fieldInstance->label        = 'ticketb';
    $fieldInstance->column       = $fieldInstance->name;
    $fieldInstance->uitype       = 10;
    $fieldInstance->columntype   = 'VARCHAR(100)';
    $fieldInstance->typeofdata   = 'V~O';
    $fieldInstance->headerfield  = 1;
    $fieldInstance->summaryfield = 1;
    $block->addField($fieldInstance);
    $fieldInstance->setRelatedModules(array('HelpDesk'));
}
$ticketB = $fieldInstance;

$fieldInstance = Vtiger_Field::getInstance('rerol', $moduleInstance);
if (!$fieldInstance) {
    $fieldInstance             = new Vtiger_Field();
    $fieldInstance->name       = 'rerol';
    $fieldInstance->label      = 'rerol';
    $fieldInstance->column     = $fieldInstance->name;
    $fieldInstance->uitype     = 16;
    $fieldInstance->columntype = 'VARCHAR(100)';
    $fieldInstance->typeofdata = 'V~O';
    $block->addField($fieldInstance);
    $fieldInstance->setPicklistValues(array('Rol A', 'Rol B'));
}
$rol = $fieldInstance;

// Recommended common fields every Entity module should have (linked to core table)
$mfield1 = Vtiger_Field::getInstance('assigned_user_id', $moduleInstance);

if (!$mfield1) {
    $mfield1             = new Vtiger_Field();
    $mfield1->name       = 'assigned_user_id';
    $mfield1->label      = 'Asignado A';
    $mfield1->table      = 'vtiger_crmentity';
    $mfield1->column     = 'smownerid';
    $mfield1->uitype     = 53;
    $mfield1->typeofdata = 'V~M';
    $block->addField($mfield1);
}

$mfield2 = Vtiger_Field::getInstance('CreatedTime', $moduleInstance);

if (!$mfield2) {
    $mfield2              = new Vtiger_Field();
    $mfield2->name        = 'CreatedTime';
    $mfield2->label       = 'Fecha de creacion';
    $mfield2->table       = 'vtiger_crmentity';
    $mfield2->column      = 'createdtime';
    $mfield2->uitype      = 70;
    $mfield2->typeofdata  = 'T~O';
    $mfield2->displaytype = 2;
    $block->addField($mfield2);
}

$mfield3 = Vtiger_Field::getInstance('ModifiedTime', $moduleInstance);

if (!$mfield3) {
    $mfield3              = new Vtiger_Field();
    $mfield3->name        = 'ModifiedTime';
    $mfield3->label       = 'Fecha de modificacion';
    $mfield3->table       = 'vtiger_crmentity';
    $mfield3->column      = 'modifiedtime';
    $mfield3->uitype      = 70;
    $mfield3->typeofdata  = 'T~O';
    $mfield3->displaytype = 2;
    $block->addField($mfield3);
}

$moduleInstance->enableTools(array('Import', 'Export'));
$moduleInstance->disableTools('Merge');

// Sharing Access Setup
$moduleInstance->setDefaultSharing();

Vtiger_Filter::deleteForModule($moduleInstance); // borra los filtros si existieran
$filter1            = new Vtiger_Filter();
$filter1->name      = 'All';
$filter1->isdefault = true;
$moduleInstance->addFilter($filter1);
// Add fields to the filter created
$filter1->addField($ticketA)->addField($ticketB, 1)->addField($rol, 2)->addField($mfield1, 3)->addField($mfield2, 4)->addField($mfield3, 5);

global $adb;

// Initialize module sequence for the module
$q = $adb->pquery('SELECT 1 FROM vtiger_modentity_num WHERE semodule = ?', array($MODULENAME));
if ($adb->num_rows($q) === 0) {
    $adb->pquery("INSERT into vtiger_modentity_num values(?,?,?,?,?,?)", array($adb->getUniqueId("vtiger_modentity_num"), $MODULENAME, 'REL', 1, 1, 1));
}

// para que se vea en el menu
$APPNAME = 'TOOLS';
$q       = $adb->pquery('SELECT 1 FROM vtiger_app2tab WHERE tabid = ? AND appname = ?', array($moduleInstance->getId(), $APPNAME));
if ($adb->num_rows($q) === 0) {
    $adb->pquery("INSERT INTO vtiger_app2tab (tabid, appname, sequence) SELECT * FROM (SELECT ?, ?, -1) AS tmp WHERE NOT EXISTS (SELECT 1 FROM vtiger_app2tab WHERE tabid = ? AND appname = ?) LIMIT 1",
        array($moduleInstance->getId(), $APPNAME, $moduleInstance->getId(), $APPNAME));
}

echo "\n\nOK\n";
