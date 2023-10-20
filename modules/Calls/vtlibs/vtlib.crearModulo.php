<?php

$actualizarCampos = false;

include_once("vtlib/Vtiger/Module.php");
$Vtiger_Utils_Log = true;

$moduleInstance = Vtiger_Module::getInstance("Calls");

if (!$moduleInstance) {
    $moduleInstance = new Vtiger_Module();
    $moduleInstance->name = "Calls";
    $moduleInstance->parent = "Sales";
    $moduleInstance->save();

    // Schema Setup
    $moduleInstance->initTables();
    mkdir("modules/Calls");
}


if ($moduleInstance) {
    $blockInstance = Vtiger_Block::getInstance("LBL_CALLS_INFORMATION", $moduleInstance);
    if (!$blockInstance) {
        $blockInstance = new Vtiger_Block();
        $blockInstance->label = "LBL_CALLS_INFORMATION";
        $moduleInstance->addBlock($blockInstance);
    }

    $callentityid = Vtiger_Field::getInstance('callentityid', $moduleInstance);

    if (!$callentityid) {
        $callentityid = new Vtiger_Field();
        $callentityid->name   = 'callentityid';
        $callentityid->label   = 'callentityid';
        $callentityid->table   = $moduleInstance->basetable;
        $callentityid->column   = 'callentityid';
        $callentityid->columntype  = 'varchar(19)';
        $callentityid->uitype   = 4;/* es un autonumerico que va a ser la clave */
        $callentityid->typeofdata  = 'V~M';
        $blockInstance->addField($callentityid); /* Creates the field and adds to block */
        $moduleInstance->setEntityIdentifier($callentityid);
    }

    $callid = Vtiger_Field::getInstance('callid', $moduleInstance);

    if (!$callid) {
        $callid = new Vtiger_Field();
        $callid->name = 'callid';
        $callid->label = $callid->name;
        $callid->uitype = 7;
        $callid->column = $callid->name;
        $callid->table = $moduleInstance->basetable;
        $callid->columntype = 'INTEGER';
        $callid->typeofdata = 'NN~O~10,0';
        $callid->displaytype = 1;
        $blockInstance->addField($callid);
    }

    $callstartdate = Vtiger_Field::getInstance('callstartdate', $moduleInstance);

    if (!$callstartdate) {
        $callstartdate = new Vtiger_Field();
        $callstartdate->name = 'callstartdate';
        $callstartdate->label = $callstartdate->name;
        $callstartdate->uitype = 5;
        $callstartdate->column = $callstartdate->name;
        $callstartdate->table = $moduleInstance->basetable;
        $callstartdate->columntype = 'DATE';
        $callstartdate->typeofdata = 'D~O';
        $blockInstance->addField($callstartdate); //Agrega el campo al bloque

    }

    $callstarttime =  Vtiger_Field::getInstance("callstarttime", $moduleInstance);
    if (!$callstarttime) {
        $callstarttime = new Vtiger_Field();
        $callstarttime->name = "callstarttime";
        $callstarttime->label = "callstarttime";
        $callstarttime->uitype = 70;
        $callstarttime->typeofdata = "T~O";
        $callstarttime->table = $moduleInstance->basetable;
        $callstarttime->column = "callstarttime";
        $callstarttime->columntype = "TIME";
        $blockInstance->addField($callstarttime);
    }

    $callphonenumber =  Vtiger_Field::getInstance("callphonenumber", $moduleInstance);
    if (!$callphonenumber) {
        $callphonenumber = new Vtiger_Field();
        $callphonenumber->name = "callphonenumber";
        $callphonenumber->label = "callphonenumber";
        $callphonenumber->uitype = 1;
        $callphonenumber->typeofdata = "V~O";
        $callphonenumber->table = $moduleInstance->basetable;
        $callphonenumber->column = "callphonenumber";
        $callphonenumber->columntype = "VARCHAR(25)";
        $callphonenumber->displaytype = 1;
        $blockInstance->addField($callphonenumber);
    }

    $callaccount =  Vtiger_Field::getInstance("callaccount", $moduleInstance);
    if (!$callaccount) {
        $callaccount = new Vtiger_Field();
        $callaccount->name = "callaccount";
        $callaccount->label = "callaccount";
        $callaccount->uitype = 10;
        $callaccount->typeofdata = "V~O";
        $callaccount->table = $moduleInstance->basetable;
        $callaccount->column = "callaccount";
        $callaccount->columntype = "VARCHAR(25)";
        $blockInstance->addField($callaccount);
        $callaccount->setRelatedModules(array("Accounts"));
    }

    $callenddate =  Vtiger_Field::getInstance("callenddate", $moduleInstance);
    if (!$callenddate) {
        $callenddate = new Vtiger_Field();
        $callenddate->name = "callenddate";
        $callenddate->label = "callenddate";
        $callenddate->uitype = 5;
        $callenddate->typeofdata = "D~O";
        $callenddate->table = $moduleInstance->basetable;
        $callenddate->column = "callenddate";
        $callenddate->columntype = "DATE";
        $blockInstance->addField($callenddate);
    }

    $callendtime =  Vtiger_Field::getInstance("callendtime", $moduleInstance);
    if (!$callendtime) {
        $callendtime = new Vtiger_Field();
        $callendtime->name = "callendtime";
        $callendtime->label = "callendtime";
        $callendtime->uitype = 70;
        $callendtime->typeofdata = "T~O";
        $callendtime->table = $moduleInstance->basetable;
        $callendtime->column = "callendtime";
        $callendtime->columntype = "TIME";
        $blockInstance->addField($callendtime);
    } 

    $calluser =  Vtiger_Field::getInstance("calluser", $moduleInstance);
    if (!$calluser) {
        $calluser = new Vtiger_Field();
        $calluser->name = "calluser";
        $calluser->label = "calluser";
        $calluser->uitype = 53;
        $calluser->typeofdata = "V~O";
        $calluser->table = $moduleInstance->basetable;
        $calluser->column = "calluser";
        $calluser->displaytype = 1;
        $blockInstance->addField($calluser);
    }

    $callwg =  Vtiger_Field::getInstance("callwg", $moduleInstance);
    if (!$callwg) {
        $callwg = new Vtiger_Field();
        $callwg->name = "callwg";
        $callwg->label = "callwg";
        $callwg->uitype = 1;
        $callwg->typeofdata = "V~O";
        $callwg->table = $moduleInstance->basetable;
        $callwg->column = "callwg";
        $callwg->displaytype = 1;
        $callenddate->columntype = "VARCHAR(100)";
        $blockInstance->addField($callwg);
    }

    $callpin =  Vtiger_Field::getInstance("callpin", $moduleInstance);
    if (!$callpin) {
        $callpin = new Vtiger_Field();
        $callpin->name = "callpin";
        $callpin->label = "callpin";
        $callpin->uitype = 1;
        $callpin->typeofdata = "V~O";
        $callpin->table = $moduleInstance->basetable;
        $callpin->column = "callpin";
        $callpin->displaytype = 1;
        $callenddate->columntype = "VARCHAR(100)";
        $blockInstance->addField($callpin);
    }

    $callmultiple =  Vtiger_Field::getInstance("callmultiple", $moduleInstance);
    if (!$callmultiple) {
        $callmultiple = new Vtiger_Field();
        $callmultiple->name = "callmultiple";
        $callmultiple->label = "callmultiple";
        $callmultiple->uitype = 1;
        $callmultiple->typeofdata = "V~O";
        $callmultiple->table = $moduleInstance->basetable;
        $callmultiple->column = "callmultiple";
        $callmultiple->displaytype = 1;
        $callenddate->columntype = "VARCHAR(100)";
        $blockInstance->addField($callmultiple);
    }

    // Campos comunes recomendados

    //Campo assigned_user_id
    $assigneduserid = Vtiger_Field::getInstance("assigned_user_id", $moduleInstance);

    if (!$assigneduserid) { //Si no existe crea el campo assigned_user_id
        $assigneduserid = new Vtiger_Field();
        $assigneduserid->name = "assigned_user_id";
        $assigneduserid->label = "Assigned To";
        $assigneduserid->table = "vtiger_crmentity";
        $assigneduserid->column = "smownerid";
        $assigneduserid->uitype = 53;
        $assigneduserid->typeofdata = "V~M";
        $blockInstance->addField($assigneduserid); //Agrega el campo al bloque
    }

    //Campo CreatedTime
    $createdtime = Vtiger_Field::getInstance("CreatedTime", $moduleInstance);

    if (!$createdtime) { //Si no existe crea el campo CreatedTime
        $createdtime = new Vtiger_Field();
        $createdtime->name = "CreatedTime";
        $createdtime->label = "Created Time";
        $createdtime->table = "vtiger_crmentity";
        $createdtime->column = "createdtime";
        $createdtime->uitype = 70;
        $createdtime->typeofdata = "T~O";
        $createdtime->displaytype = 2;
        $blockInstance->addField($createdtime); //Agrega el campo al bloque
    }

    //Campo ModifiedTime
    $modifiedtime = Vtiger_Field::getInstance("ModifiedTime", $moduleInstance);

    if (!$modifiedtime) {    //Si no existe crea el campo ModifiedTime
        $modifiedtime = new Vtiger_Field();
        $modifiedtime->name = "ModifiedTime";
        $modifiedtime->label = "Modified Time";
        $modifiedtime->table = "vtiger_crmentity";
        $modifiedtime->column = "modifiedtime";
        $modifiedtime->uitype = 70;
        $modifiedtime->typeofdata = "T~O";
        $modifiedtime->displaytype = 2;
        $blockInstance->addField($modifiedtime);
    }

    /** Set sharing access of this module*/
    $moduleInstance->setDefaultSharing('Public');

    $moduleInstance->enableTools(array('Import', 'Export'));
    $moduleInstance->disableTools('Merge');

    $moduleInstance->initWebservice();

    Vtiger_Filter::deleteForModule($moduleInstance); // borra los filtros si existieran
    $filter1 = new Vtiger_Filter();
    $filter1->name = 'All';
    $filter1->isdefault = true;
    $moduleInstance->addFilter($filter1);
    // Add fields to the filter created
    $filter1->addField($callid)
        ->addField($callstartdate, 1)
        ->addField($callstarttime, 2)
        ->addField($callphonenumber, 3)
        ->addField($callaccount, 4)
        ->addField($callenddate, 5)
        ->addField($callendtime, 6);

    global $adb;

    // Initialize module sequence for the module
    $q = $adb->pquery('SELECT 1 FROM vtiger_modentity_num WHERE semodule = ?', array($MODULENAME));
    if ($adb->num_rows($q) === 0) {
        $adb->pquery("INSERT into vtiger_modentity_num values(?,?,?,?,?,?)", array($adb->getUniqueId("vtiger_modentity_num"), $MODULENAME, 'CALL', 1, 1, 1));
    }

    // para que se vea en el menu
    $APPNAME = 'INVENTORY';
    $q       = $adb->pquery('SELECT 1 FROM vtiger_app2tab WHERE tabid = ? AND appname = ?', array($moduleInstance->getId(), $APPNAME));
    if ($adb->num_rows($q) === 0) {
        $adb->pquery("INSERT INTO vtiger_app2tab (tabid, appname, sequence) SELECT * FROM (SELECT ?, ?, -1) AS tmp WHERE NOT EXISTS (SELECT 1 FROM vtiger_app2tab WHERE tabid = ? AND appname = ?) LIMIT 1",
            array($moduleInstance->getId(), $APPNAME, $moduleInstance->getId(), $APPNAME));
    }

    echo "Ok";
}
