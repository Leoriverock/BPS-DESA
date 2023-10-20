<?php 
error_reporting(E_ERROR);

include_once("vtlib/Vtiger/Module.php");
$Vtiger_Utils_Log = true;
global $adb;

$modulename = "LPTempFlujoCambios";

$moduleInstance = Vtiger_Module::getInstance($modulename);

if(!$moduleInstance){       
    $moduleInstance = new Vtiger_Module();
    $moduleInstance->name = $modulename;
    $moduleInstance->parent = "Tools";
    $moduleInstance->save();

    // Schema Setup
    $moduleInstance->initTables();
    createFiles($moduleInstance);
    // addModuleToApp($moduleInstance);
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
    
    $tfc_template = Vtiger_Field::getInstance('tfc_template', $moduleInstance);
    if(!$tfc_template) {
        $tfc_template = new Vtiger_Field();
        $tfc_template->name = 'tfc_template';
        $tfc_template->label = $tfc_template->name;
        $tfc_template->table = $moduleInstance->basetable;
        $tfc_template->column = $tfc_template->name;
        $tfc_template->columntype = 'INT(11)';
        $tfc_template->uitype = 10;
        $tfc_template->typeofdata = 'I~M';
        $tf_modulo->summaryfield = 1;
        $blockInstance->addField($tfc_template);
        $tfc_template->setRelatedModules(Array('LPTempFlujos'));
    }

    $tfc_origen = Vtiger_Field::getInstance('tfc_origen', $moduleInstance);
    if(!$tfc_origen) {
        $tfc_origen = new Vtiger_Field();
        $tfc_origen->name = 'tfc_origen';
        $tfc_origen->label = $tfc_origen->name;
        $tfc_origen->table = $moduleInstance->basetable;
        $tfc_origen->column = $tfc_origen->name;
        $tfc_origen->columntype = 'varchar(255)';
        $tfc_origen->typeofdata = 'V~O';
        $tfc_origen->uitype = 16;
        $tf_modulo->summaryfield = 1;
        $blockInstance->addField($tfc_origen);
        $tfc_origen->setPicklistValues( array() );
    }

    $tfc_destino = Vtiger_Field::getInstance('tfc_destino', $moduleInstance);
    if(!$tfc_destino) {
        $tfc_destino = new Vtiger_Field();
        $tfc_destino->name = 'tfc_destino';
        $tfc_destino->label = $tfc_destino->name;
        $tfc_destino->table = $moduleInstance->basetable;
        $tfc_destino->column = $tfc_destino->name;
        $tfc_destino->columntype = 'varchar(255)';
        $tfc_destino->uitype = 16;
        $tf_modulo->summaryfield = 1;
        $tfc_destino->typeofdata = 'V~O';
        $blockInstance->addField($tfc_destino);
        $tfc_destino->setPicklistValues( array() );
    }

    $tfc_etiqueta = Vtiger_Field::getInstance('tfc_etiqueta', $moduleInstance);

    if(!$tfc_etiqueta) {
        $tfc_etiqueta = new Vtiger_Field();
        $tfc_etiqueta->name = 'tfc_etiqueta';
        $tfc_etiqueta->label = $tfc_etiqueta->name;
        $tfc_etiqueta->table = $moduleInstance->basetable;
        $tfc_etiqueta->column = $tfc_etiqueta->name;
        $tfc_etiqueta->columntype = 'varchar(255)';
        $tfc_etiqueta->uitype = 1;
        $tfc_etiqueta->typeofdata = 'V~O';
        $blockInstance->addField($tfc_etiqueta);
    }

    $tfc_color = Vtiger_Field::getInstance('tfc_color', $moduleInstance);

    if(!$tfc_color) {
        $tfc_color = new Vtiger_Field();
        $tfc_color->name = 'tfc_color';
        $tfc_color->label = $tfc_color->name;
        $tfc_color->table = $moduleInstance->basetable;
        $tfc_color->column = $tfc_color->name;
        $tfc_color->columntype = 'varchar(255)';
        $tfc_color->uitype = 1;
        $tfc_color->typeofdata = 'V~O';
        $blockInstance->addField($tfc_color);
    }
    
    $tfc_comentario = Vtiger_Field::getInstance('tfc_comentario', $moduleInstance);

    if(!$tfc_comentario) {
        $tfc_comentario = new Vtiger_Field();
        $tfc_comentario->name = 'tfc_comentario';
        $tfc_comentario->label = $tfc_comentario->name;
        $tfc_comentario->table = $moduleInstance->basetable;
        $tfc_comentario->column = $tfc_comentario->name;
        $tfc_comentario->columntype = 'varchar(3)';
        $tfc_comentario->typeofdata = 'C~O';
        $tfc_comentario->uitype = 56;
        $blockInstance->addField($tfc_comentario);
    }    
        
    $tfc_paracrm = Vtiger_Field::getInstance('tfc_paracrm', $moduleInstance);

    if(!$tfc_paracrm) {
        $tfc_paracrm = new Vtiger_Field();
        $tfc_paracrm->name = 'tfc_paracrm';
        $tfc_paracrm->label = $tfc_paracrm->name;
        $tfc_paracrm->table = "vtiger_lptempflujocambios";
        $tfc_paracrm->column = $tfc_paracrm->name;
        $tfc_paracrm->columntype = 'varchar(3)';
        $tfc_paracrm->typeofdata = 'C~O';
        $tfc_paracrm->uitype = 56;
        $blockInstance->addField($tfc_paracrm);
    }
    
    $tfc_paraportal = Vtiger_Field::getInstance('tfc_paraportal', $moduleInstance);

    if(!$tfc_paraportal) {
        $tfc_paraportal = new Vtiger_Field();
        $tfc_paraportal->name = 'tfc_paraportal';
        $tfc_paraportal->label = $tfc_paraportal->name;
        $tfc_paraportal->table = "vtiger_lptempflujocambios";
        $tfc_paraportal->column = $tfc_paraportal->name;
        $tfc_paraportal->columntype = 'varchar(3)';
        $tfc_paraportal->typeofdata = 'C~O';
        $tfc_paraportal->uitype = 56;
        $blockInstance->addField($tfc_paraportal);
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
    $filter1->addField($tfc_template)->addField($tfc_origen,1)->addField($tfc_destino, 2)->addField($tfc_etiqueta, 3)->addField($createdtime, 4)->addField($assigneduserid, 5);

    $modulo = Vtiger_Module::getInstance('LPTempFlujos');
    $etiqueta = 'Cambios';
    $permisos = Array("ADD");
    $funcion = 'get_dependents_list';

    // La forma correcta de invocar a la funcion es la siguiente:
    $modulo->unsetRelatedList($moduleInstance, $etiqueta, $funcion);
    $modulo->setRelatedList($moduleInstance, $etiqueta, $permisos, $funcion, $tfc_template->id);

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


?>
    