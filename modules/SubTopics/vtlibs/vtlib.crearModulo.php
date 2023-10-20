<?php

$actualizarCampos = false;

include_once("vtlib/Vtiger/Module.php");
$Vtiger_Utils_Log = true;

$MODULENAME = "SubTopics";

$moduleInstance = Vtiger_Module::getInstance( $MODULENAME );

if (!$moduleInstance) {
    $moduleInstance = new Vtiger_Module();
    $moduleInstance->name = "SubTopics";
    $moduleInstance->parent = "SUPPORT";
    $moduleInstance->save();

    // Schema Setup
    $moduleInstance->initTables();
    mkdir("modules/SubTopics");
}
else{
    $moduleInstance->delete();
    exit();
}


if ($moduleInstance) {
    $blockInstance = Vtiger_Block::getInstance("LBL_SUBTOPICS_INFORMATION", $moduleInstance);
    if (!$blockInstance) {
        $blockInstance = new Vtiger_Block();
        $blockInstance->label = "LBL_SUBTOPICS_INFORMATION";
        $moduleInstance->addBlock($blockInstance);
    }

    $subtopicentityid = Vtiger_Field::getInstance('subtopicentityid', $moduleInstance);

    if (!$subtopicentityid) {
        $subtopicentityid = new Vtiger_Field();
        $subtopicentityid->name   = 'subtopicentityid';
        $subtopicentityid->label   = 'subtopicentityid';
        $subtopicentityid->table   = $moduleInstance->basetable;
        $subtopicentityid->column   = 'subtopicentityid';
        $subtopicentityid->columntype  = 'varchar(19)';
        $subtopicentityid->uitype   = 4;/* es un autonumerico que va a ser la clave */
        $subtopicentityid->typeofdata  = 'V~M';
        $blockInstance->addField($subtopicentityid); /* Creates the field and adds to block */
    }

    $subtopicname = Vtiger_Field::getInstance('subtopicname', $moduleInstance);

    if (!$subtopicname) {
        $subtopicname = new Vtiger_Field();
        $subtopicname->name = 'subtopicname';
        $subtopicname->label = $subtopicname->name;
        $subtopicname->uitype = 1;
        $subtopicname->column = $subtopicname->name;
        $subtopicname->table = $moduleInstance->basetable;
        $subtopicname->columntype = 'VARCHAR(100)';
        $subtopicname->typeofdata = 'V~M';
        $subtopicname->displaytype = 1;
        $subtopicname->headerfield = 1;
        $subtopicname->summaryfield = 1;
        $blockInstance->addField($subtopicname);
        $moduleInstance->setEntityIdentifier($subtopicname);
    }

    $subtopiccategory = Vtiger_Field::getInstance('subtopiccategory', $moduleInstance);

    if (!$subtopiccategory) {
        $subtopiccategory = new Vtiger_Field();
        $subtopiccategory->name = 'subtopiccategory';
        $subtopiccategory->label = $subtopiccategory->name;
        $subtopiccategory->uitype = 1;
        $subtopiccategory->column = $subtopiccategory->name;
        $subtopiccategory->table = $moduleInstance->basetable;
        $subtopiccategory->columntype = 'VARCHAR(100)';
        $subtopiccategory->typeofdata = 'V~M';
        $subtopiccategory->displaytype = 1;
        $subtopiccategory->headerfield = 1;
        $subtopiccategory->summaryfield = 1;
        $blockInstance->addField($subtopiccategory);
    }

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

    $moduleInstance->initWebservice();

    Vtiger_Filter::deleteForModule($moduleInstance); // borra los filtros si existieran
    $filter1 = new Vtiger_Filter();
    $filter1->name = 'All';
    $filter1->isdefault = true;
    $moduleInstance->addFilter($filter1);
    // Add fields to the filter created
    $filter1->addField($subtopicname)
        ->addField($subtopiccategory, 1);
    

        global $adb;

        // Initialize module sequence for the module
        $q = $adb->pquery('SELECT 1 FROM vtiger_modentity_num WHERE semodule = ?', array($MODULENAME));
        if ($adb->num_rows($q) === 0) {
            $adb->pquery("INSERT into vtiger_modentity_num values(?,?,?,?,?,?)", array($adb->getUniqueId("vtiger_modentity_num"), $MODULENAME, 'TEM', 1, 1, 1));
        }
    
        // para que se vea en el menu
        $APPNAME = 'SUPPORT';
        $q       = $adb->pquery('SELECT 1 FROM vtiger_app2tab WHERE tabid = ? AND appname = ?', array($moduleInstance->getId(), $APPNAME));
        if ($adb->num_rows($q) === 0) {
            $adb->pquery("INSERT INTO vtiger_app2tab (tabid, appname, sequence) SELECT * FROM (SELECT ?, ?, -1) AS tmp WHERE NOT EXISTS (SELECT 1 FROM vtiger_app2tab WHERE tabid = ? AND appname = ?) LIMIT 1",
                array($moduleInstance->getId(), $APPNAME, $moduleInstance->getId(), $APPNAME));
        }
    
    echo "Ok";
}
