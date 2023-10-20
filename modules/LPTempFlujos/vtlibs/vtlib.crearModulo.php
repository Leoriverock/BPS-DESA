<?php 
error_reporting(E_ERROR);

include_once("vtlib/Vtiger/Module.php");
require_once('includes/Loader.php');
$Vtiger_Utils_Log = true;
global $adb;

$modulename = "LPTempFlujos";

$moduleInstance = Vtiger_Module::getInstance($modulename);

if(!$moduleInstance){       
    $moduleInstance = new Vtiger_Module();
    $moduleInstance->name = $modulename;
    $moduleInstance->parent = "Tools";
    $moduleInstance->save();

    // Schema Setup
    $moduleInstance->initTables();
    createFiles($moduleInstance);
    addModuleToApp($moduleInstance);
    addLinks();
} else {
    // $moduleInstance->delete();
    // return;
}


if($moduleInstance){
    $blockInstance = Vtiger_Block::getInstance("LBL_".strtoupper($modulename)."_INFORMATION",$moduleInstance);
    if(!$blockInstance){
        $blockInstance = new Vtiger_Block();
        $blockInstance->label = "LBL_".strtoupper($modulename)."_INFORMATION";
        $moduleInstance->addBlock($blockInstance); 
    }
    
    $tf_nombre = Vtiger_Field::getInstance('tf_nombre', $moduleInstance);

    if(!$tf_nombre) {
        $tf_nombre = new Vtiger_Field();
        $tf_nombre->name = 'tf_nombre';
        $tf_nombre->label = $tf_nombre->name;
        $tf_nombre->table = $moduleInstance->basetable;
        $tf_nombre->column = $tf_nombre->name;
        $tf_nombre->columntype = 'varchar(255)';
        $tf_nombre->uitype = 1;
        $tf_nombre->typeofdata = 'V~M';
        $blockInstance->addField($tf_nombre);
        $moduleInstance->setEntityIdentifier($tf_nombre); 
    }

    $tf_modulo = Vtiger_Field::getInstance('tf_modulo', $moduleInstance);

    if(!$tf_modulo) {
        $tf_modulo = new Vtiger_Field();
        $tf_modulo->name = 'tf_modulo';
        $tf_modulo->label = $tf_modulo->name;
        $tf_modulo->table = $moduleInstance->basetable;
        $tf_modulo->column = $tf_modulo->name;
        $tf_modulo->columntype = 'varchar(255)';
        $tf_modulo->uitype = 1;
        $tf_modulo->summaryfield = 1;
        $tf_modulo->typeofdata = 'V~M';
        $tf_modulo->uitype = 16;
        $blockInstance->addField($tf_modulo);
        $tf_modulo->setPicklistValues( array() );
    }

    $adb->query("DROP TABLE IF EXISTS vtiger_tf_modulo");
    $adb->query("DROP VIEW IF EXISTS vtiger_tf_modulo");

    $adb->query(
        "CREATE VIEW vtiger_tf_modulo AS
        SELECT 
            t.tabid AS tf_moduloid,
            t.name AS tf_modulo,
            1 AS precense,
            t.tabid AS picklist_valueid,
            1 AS sortorderid,
            '' AS color
        FROM vtiger_tab t
        WHERE t.tabid IN (SELECT DISTINCT tabid FROM vtiger_field WHERE uitype IN (15,16,33)) 
        AND t.name NOT IN (
            'LPTempFlujos', 
            'LPTempFlujoCambios',
            'LPTempCampos',
            'LPTempCamposDetalle',
            'LPTempCamposSeleccion'
        )
        ORDER BY tabid"
    );

    $tf_campo = Vtiger_Field::getInstance('tf_campo', $moduleInstance);

    if(!$tf_campo) {
        $tf_campo = new Vtiger_Field();
        $tf_campo->name = 'tf_campo';
        $tf_campo->label = $tf_campo->name;
        $tf_campo->table = $moduleInstance->basetable;
        $tf_campo->column = $tf_campo->name;
        $tf_campo->columntype = 'varchar(255)';
        $tf_campo->uitype = 16;
        $tf_campo->summaryfield = 1;
        $tf_campo->typeofdata = 'V~M';
        $blockInstance->addField($tf_campo);
        $tf_campo->setPicklistValues( array() );
    }

    $tf_valor = Vtiger_Field::getInstance('tf_valor', $moduleInstance);

    if(!$tf_valor) {
        $tf_valor = new Vtiger_Field();
        $tf_valor->name = 'tf_valor';
        $tf_valor->label = $tf_valor->name;
        $tf_valor->table = $moduleInstance->basetable;
        $tf_valor->column = $tf_valor->name;
        $tf_valor->columntype = 'varchar(255)';
        $tf_valor->uitype = 16;
        $tf_valor->summaryfield = 1;
        $tf_valor->typeofdata = 'V~O';
        $blockInstance->addField($tf_valor);
        $tf_valor->setPicklistValues( array() );
    }

    $tf_campo_mod = Vtiger_Field::getInstance('tf_campo_mod', $moduleInstance);

    if(!$tf_campo_mod) {
        $tf_campo_mod = new Vtiger_Field();
        $tf_campo_mod->name = 'tf_campo_mod';
        $tf_campo_mod->label = $tf_campo_mod->name;
        $tf_campo_mod->table = $moduleInstance->basetable;
        $tf_campo_mod->column = $tf_campo_mod->name;
        $tf_campo_mod->columntype = 'varchar(255)';
        $tf_campo_mod->typeofdata = 'V~M';
        $tf_campo_mod->uitype = 16;
        $blockInstance->addField($tf_campo_mod);
        $tf_campo_mod->setPicklistValues( array() );
    }
    // Campos comunes recomendados
    //Campo assigned_user_id
    $assigneduserid = Vtiger_Field::getInstance("assigned_user_id",$moduleInstance);
    
    if(!$assigneduserid){ //Si no existe crea el campo assigned_user_id
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
    $createdtime = Vtiger_Field::getInstance("CreatedTime",$moduleInstance);
    
    if(!$createdtime){ //Si no existe crea el campo CreatedTime
        $createdtime = new Vtiger_Field();
        $createdtime->name = "CreatedTime";
        $createdtime->label= "Created Time";
        $createdtime->table = "vtiger_crmentity";
        $createdtime->column = "createdtime";
        $createdtime->uitype = 70;
        $createdtime->typeofdata = "T~O";
        $createdtime->displaytype= 2;
        $blockInstance->addField($createdtime);//Agrega el campo al bloque
    }

    //Campo ModifiedTime
    $modifiedtime = Vtiger_Field::getInstance("ModifiedTime",$moduleInstance);

    if(!$modifiedtime){ //Si no existe crea el campo ModifiedTime
        $modifiedtime = new Vtiger_Field();
        $modifiedtime->name = "ModifiedTime";
        $modifiedtime->label= "Modified Time";
        $modifiedtime->table = "vtiger_crmentity";
        $modifiedtime->column = "modifiedtime";
        $modifiedtime->uitype = 70;
        $modifiedtime->typeofdata = "T~O";
        $modifiedtime->displaytype= 2;
        $blockInstance->addField($modifiedtime);
    }

    /** Set sharing access of this module */
    $moduleInstance->setDefaultSharing('Public'); 

    /** Enable and Disable available tools */
    $moduleInstance->enableTools(Array('Import', 'Export'));
    $moduleInstance->disableTools('Merge');

    $moduleInstance->initWebservice();

    Vtiger_Filter::deleteForModule($moduleInstance); // borra los filtros si existieran
    $filter1 = new Vtiger_Filter();
    $filter1->name = 'All';
    $filter1->isdefault = true;
    $moduleInstance->addFilter($filter1);
    // Add fields to the filter created
    $filter1->addField($tf_nombre)->addField($tf_modulo,1)->addField($tf_campo, 2)->addField($tf_valor, 3)->addField($createdtime, 4)->addField($assigneduserid, 5);
    
} 

function createFiles($module) {
    $targetpath = 'modules/' . $module->name;

    $fieldid  = strtolower($module->name);

    if (!is_file($targetpath)) {
        mkdir($targetpath);
        mkdir($targetpath . '/language');

        $templatepath = 'vtlib/ModuleDir/6.0.0';

        $moduleFileContents = file_get_contents($templatepath . '/ModuleName.php');
        $replacevars = array(
            'ModuleName' => $module->name,
            '<modulename>' => strtolower($module->name),
            '<entityfieldlabel>' => $fieldid,
            '<entitycolumn>' => $fieldid,
            '<entityfieldname>' => $fieldid,
        );

        foreach ($replacevars as $key => $value) {
            $moduleFileContents = str_replace($key, $value, $moduleFileContents);
        }
        file_put_contents($targetpath.'/'.$module->name.'.php', $moduleFileContents);
    }
}

function addModuleToApp($module){
    $db   = PearDatabase::getInstance();
    $parent = strtoupper($module->parent);
    $result = $db->pquery('SELECT * FROM vtiger_app2tab WHERE tabid = ? AND appname = ?', array($module->getId(), $parent));
    $sequence = getMaxSequenceForApp($parent) + 1;
    if ($db->num_rows($result) == 0) {
        $db->pquery('INSERT INTO vtiger_app2tab(tabid,appname,sequence) VALUES(?,?,?)', array($module->getId(), $parent, $sequence));
    }
}

/**
 * Function to get the max sequence number for an app
 * @param <string> $appName
 * @return <integer>
 */
function getMaxSequenceForApp($appName) {
    $db = PearDatabase::getInstance();
    $result = $db->pquery('SELECT MAX(sequence) AS maxsequence FROM vtiger_app2tab WHERE appname=?', array($appName));
    $sequence = 0;
    if ($db->num_rows($result) > 0) {
        $sequence = $db->query_result($result, 0, 'maxsequence');
    }

    return $sequence;
}

function addLinks() {
    
$links = array(
    array(
        "tabid" => Vtiger_Module_Model::getInstance("LPTempFlujos")->getId(),
        "linklabel" => "LPTempFlujosActionsJS",
        "linkurl" => "layouts/v7/modules/LPTempFlujos/resources/Actions.js",
        "linktype" => "HEADERSCRIPT",
    ),
);
foreach($links as $l){
    global $adb;
    $paramsHeader = array();
    $paramsValues = array();
    $queryE = "SELECT count(linkid) as exist FROM vtiger_links WHERE 1 ";
    foreach($l as $h => $v){
        $paramsHeader[] = $h; $paramsValues[] = $v;
        $queryE.= "AND $h = ? ";
    }
    $existQuery = $adb->pquery($queryE, $paramsValues);
    if(!$adb->query_result($existQuery, 0, 'exist') ){
        $seqQuery = $adb->query("SELECT id FROM vtiger_links_seq ORDER BY id DESC LIMIT 1");
        $linkid = $adb->query_result($seqQuery, 0, 'id');  
        $paramsHeader = array_merge($paramsHeader, ['linkid'] );
        $paramsValues = array_merge($paramsValues, [$linkid+1]);
        $adb->pquery(
            "INSERT INTO vtiger_links(".implode(',', $paramsHeader).") VALUES (".generateQuestionMarks($paramsValues).")",
            array_merge($paramsValues)
        );
        echo "Agregado al listado inicial <br/> Ok...";
        $adb->query("UPDATE vtiger_links_seq SET id = id + 1"); 
    } else {
        echo "Link $l[linklabel] ($l[tabid]) ya esta agregado  <br/>";
    }
}
}


?>
    