<?php

$actualizarCampos = false;

include_once("vtlib/Vtiger/Module.php");
$Vtiger_Utils_Log = true;

$MODULENAME = "Topics";

$moduleInstance = Vtiger_Module::getInstance( $MODULENAME );

if (!$moduleInstance) {
    $moduleInstance = new Vtiger_Module();
    $moduleInstance->name = "Topics";
    $moduleInstance->parent = "SUPPORT";
    $moduleInstance->save();

    // Schema Setup
    $moduleInstance->initTables();
    mkdir("modules/Topics");
}
else{
    $moduleInstance->delete();
    exit();
}


if ($moduleInstance) {
    $blockInstance = Vtiger_Block::getInstance("LBL_TOPICS_INFORMATION", $moduleInstance);
    if (!$blockInstance) {
        $blockInstance = new Vtiger_Block();
        $blockInstance->label = "LBL_TOPICS_INFORMATION";
        $moduleInstance->addBlock($blockInstance);
    }

    $topicentityid = Vtiger_Field::getInstance('topicentityid', $moduleInstance);

    if (!$topicentityid) {
        $topicentityid = new Vtiger_Field();
        $topicentityid->name   = 'topicentityid';
        $topicentityid->label   = 'topicentityid';
        $topicentityid->table   = $moduleInstance->basetable;
        $topicentityid->column   = 'topicentityid';
        $topicentityid->columntype  = 'varchar(19)';
        $topicentityid->uitype   = 4;/* es un autonumerico que va a ser la clave */
        $topicentityid->typeofdata  = 'V~M';
        $blockInstance->addField($topicentityid); /* Creates the field and adds to block */
    }

    $topicname = Vtiger_Field::getInstance('topicname', $moduleInstance);

    if (!$topicname) {
        $topicname = new Vtiger_Field();
        $topicname->name = 'topicname';
        $topicname->label = $topicname->name;
        $topicname->uitype = 1;
        $topicname->column = $topicname->name;
        $topicname->table = $moduleInstance->basetable;
        $topicname->columntype = 'VARCHAR(100)';
        $topicname->typeofdata = 'V~M';
        $topicname->displaytype = 1;
        $topicname->headerfield = 1;
        $topicname->summaryfield = 1;
        $blockInstance->addField($topicname);
        $moduleInstance->setEntityIdentifier($topicname);
    }

    $topiccategory = Vtiger_Field::getInstance('topiccategory', $moduleInstance);

    if (!$topiccategory) {
        $topiccategory = new Vtiger_Field();
        $topiccategory->name = 'topiccategory';
        $topiccategory->label = $topiccategory->name;
        $topiccategory->uitype = 1;
        $topiccategory->column = $topiccategory->name;
        $topiccategory->table = $moduleInstance->basetable;
        $topiccategory->columntype = 'VARCHAR(100)';
        $topiccategory->typeofdata = 'V~M';
        $topiccategory->displaytype = 1;
        $topiccategory->headerfield = 1;
        $topiccategory->summaryfield = 1;
        $blockInstance->addField($topiccategory);
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
    $filter1->addField($topicname)
        ->addField($topiccategory, 1);
    

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
