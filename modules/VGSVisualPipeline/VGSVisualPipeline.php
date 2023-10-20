<?php

/**
 * VGS Visual Pipeline Module
 *
 *
 * @package        VGSVisualPipeline Module
 * @author         Curto Francisco - www.vgsglobal.com
 * @license        vTiger Public License.
 * @version        Release: 1.0
 */

include_once 'modules/Vtiger/CRMEntity.php';
include_once 'include/utils/utils.php';

class VGSVisualPipeline extends Vtiger_CRMEntity {

    public function __construct() {
        
    }

    /**
     * Invoked when special actions are performed on the module.
     * @param String Module name
     * @param String Event Type
     */
    function vtlib_handler($moduleName, $eventType) {
        $adb = PearDatabase::getInstance();
        if ($eventType == 'module.postinstall') {
           
            $otherSettingsBlock = $adb->pquery('SELECT * FROM vtiger_settings_blocks WHERE label=?', array('LBL_OTHER_SETTINGS'));
            $otherSettingsBlockCount = $adb->num_rows($otherSettingsBlock);

            if ($otherSettingsBlockCount > 0) {
                $blockid = $adb->query_result($otherSettingsBlock, 0, 'blockid');
                $sequenceResult = $adb->pquery("SELECT max(sequence) as sequence FROM vtiger_settings_blocks WHERE blockid=", array($blockid));
                if ($adb->num_rows($sequenceResult)) {
                    $sequence = $adb->query_result($sequenceResult, 0, 'sequence');
                }
            }

            $fieldid = $adb->getUniqueID('vtiger_settings_field');
            $adb->pquery("INSERT INTO vtiger_settings_field(fieldid, blockid, name, iconpath, description, linkto, sequence, active) 
                        VALUES(?,?,?,?,?,?,?,?)", array($fieldid, $blockid, 'VGS Visual Pipeline', '', 'VGSVisualPipeline Configuration', 'index.php?module=VGSVisualPipeline&view=VGSIndexSettings&parent=Settings', $sequence++, 0));
        
                        
        } else if ($eventType == 'module.disabled') {
            //remove all links

        } else if ($eventType == 'module.enabled') {
        } else if ($eventType == 'module.preuninstall') {
            //Remove all link


        } else if ($eventType == 'module.preupdate') {
        } else if ($eventType == 'module.postupdate') {
        }
    }
}
