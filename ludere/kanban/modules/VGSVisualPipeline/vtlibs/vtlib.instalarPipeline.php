<?php
/******
 * Instalador de Vista PipeLine para Vtiger
 * @author: Manuel "Gucci" Estefanell
 * @date: 2018
 ****/

//// Importaciones
$Vtiger_Utils_Log = true;

include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');

if(!function_exists("vtlib_addSettingsLink")){
	function vtlib_addSettingsLink($linkName, $linkURL, $blockName = false) {
		$success = true;
		$db = PearDatabase::getInstance();

		//Check entry name exist in DB or not
		$result = $db->pquery('SELECT 1 FROM vtiger_settings_field WHERE name=?', array($linkName));
		if ($result && !$db->num_rows($result)) {
			$blockId = 0;
			if ($blockName)
				$blockId = getSettingsBlockId($blockName);//Check block name exist in DB or not

			if (!$blockId) {
				$blockName = 'LBL_OTHER_SETTINGS';
				$blockId = getSettingsBlockId($blockName);//Check block name exist in DB or not
			}

			//Add block in to DB if not exists
			if (!$blockId) {
				$blockSeqResult = $db->pquery('SELECT MAX(sequence) AS sequence FROM vtiger_settings_blocks', array());
				if ($db->num_rows($blockSeqResult)) {
					$blockId = $db->getUniqueID('vtiger_settings_blocks');
					$blockSequence = $db->query_result($blockSeqResult, 0, 'sequence');
					$db->pquery('INSERT INTO vtiger_settings_blocks(blockid, label, sequence) VALUES(?,?,?)', array($blockId, 'LBL_OTHER_SETTINGS', $blockSequence++));
				}
			}

			//Add settings field in to DB
			if ($blockId) {
				$fieldSeqResult = $db->pquery('SELECT MAX(sequence) AS sequence FROM vtiger_settings_field WHERE blockid=?', array($blockId));
				if ($db->num_rows($fieldSeqResult)) {
					$fieldId = $db->getUniqueID('vtiger_settings_field');
					$linkURL = ($linkURL) ? $linkURL : '';
					$fieldSequence = $db->query_result($fieldSeqResult, 0, 'sequence');

					$db->pquery('INSERT INTO vtiger_settings_field(fieldid, blockid, name, iconpath, description, linkto, sequence, active, pinned) VALUES(?,?,?,?,?,?,?,?,?)', array($fieldId, $blockId, $linkName, '', $linkName, $linkURL, $fieldSequence++, 0, 0));
				}
			}
			else
				$success = false;
		}
		return $success;
	}
}

global $adb;

$sql = "CREATE TABLE vtiger_vgsvisualpipeline (
			vgsvisualpipelineid int(11) unsigned NOT NULL AUTO_INCREMENT,
			sourcefieldname varchar(50) DEFAULT NULL,
			sourcemodule varchar(50) DEFAULT NULL,
			fieldname1 varchar(50) DEFAULT NULL,
			fieldname2 varchar(50) DEFAULT NULL,
			fieldname3 varchar(50) DEFAULT NULL,
			fieldname4 varchar(50) DEFAULT NULL,
			negrita1 tinyint(1) DEFAULT NULL,
			negrita2 tinyint(1) DEFAULT NULL,
			negrita3 tinyint(1) DEFAULT NULL,
			negrita4 tinyint(1) DEFAULT NULL,
			color1 varchar(30) DEFAULT NULL,
			color2 varchar(30) DEFAULT NULL,
			color3 varchar(30) DEFAULT NULL,
			color4 varchar(30) DEFAULT NULL,
			PRIMARY KEY (vgsvisualpipelineid)
		) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8";
$adb->pquery($sql, array());

$sql = "CREATE TABLE vtiger_vgsvisualpipeline_settings (
		  	vgscrmid int(11) NOT NULL,
		  	vgsuserid int(11) NOT NULL,
		  	vgscolor varchar(30) DEFAULT NULL,
		  	PRIMARY KEY (vgscrmid,vgsuserid)
		) ENGINE=InnoDB DEFAULT CHARSET=latin1";
$adb->pquery($sql, array());

$sql = "CREATE TABLE vtiger_vgsvisualsorting (
		  	module varchar(50) DEFAULT NULL,
		  	sorting text
		) ENGINE=InnoDB DEFAULT CHARSET=utf8";
$adb->pquery($sql, array());

$moduleInstance = Vtiger_Module::getInstance("VGSVisualPipeline");

if(!$moduleInstance){		
	$moduleInstance = new Vtiger_Module();
    $moduleInstance->name = "VGSVisualPipeline";
    $moduleInstance->parent = "";
    $moduleInstance->save();
    $moduleInstance->initTables();
}

Vtiger_Link::addLink($moduleInstance->getId(), 'HEADERSCRIPT', 'LP Pipeline', "layouts/v7/modules/VGSVisualPipeline/resources/VGSVisualPipeline.js", '', 0, '');
Vtiger_Link::addLink($moduleInstance->getId(), 'HEADERSCRIPT', 'ColorPicker', "libraries/bootstrap/js/bootstrap-colorpicker.min.js", '', 0, '');
Vtiger_Link::addLink($moduleInstance->getId(), 'HEADERCSS', 'ColorPicker CSS', "libraries/bootstrap/css/bootstrap-colorpicker.min.css", '', 0, '');

vtlib_addSettingsLink("LP Pipeline","index.php?module=VGSVisualPipeline&view=VGSIndexSettings&parent=Settings");

$layouts = "layouts/vlayout/modules/VGSVisualPipeline/";
$colorpicker_js = "libraries/bootstrap/js/bootstrap-colorpicker.min.js";
$colorpicker_css = "libraries/bootstrap/css/bootstrap-colorpicker.min.css";
$modules = "modules/VGSVisualPipeline/";

if(!file_exists($layouts))
	echo "<br>COPIAR!!!   ->   ".$layouts."*";

if(!file_exists($colorpicker_css))
	echo "<br>COPIAR!!!   ->   ".$colorpicker_css;

if(!file_exists($colorpicker_js))
	echo "<br>COPIAR!!!   ->   ".$colorpicker_js;

if(!file_exists($modules))
	echo "<br>COPIAR!!!   ->   ".$modules."*";

echo "<br><br>OK";