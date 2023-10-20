<?php

require_once 'config.ludere.php';
require_once 'integracion/ws.php';

class AtencionPresencial_Module_Model extends Vtiger_Module_Model
{

    public static function getAtencionpPActiva($userId = false)
    {
        global $adb, $log, $site_URL;

        $userId = $userId ? $userId : Users_Record_Model::getCurrentUserModel()->getId();
        $log->info("getAtencionpPActiva".$userId);
        $q      = $adb->pquery("SELECT atencionpresencialid, ap_numero, ap_persona
                                FROM vtiger_atencionpresencial
                                JOIN vtiger_crmentity ON crmid = atencionpresencialid
                                WHERE deleted = 0 AND ap_estado = 'en Proceso' AND smownerid = ? AND ap_fechafin IS NULL", array($userId));
        $log->debug("Filas: " . $adb->num_rows($q));
        $log->debug("Numero: " . $adb->query_result($q, 0, 'ap_numero'));
        return $adb->num_rows($q) > 0 ? [
            "atencionpresencialid"         => $adb->query_result($q, 0, 'atencionpresencialid'),
            "atencionpresencialurl"         => "index.php?module=AtencionPresencial&view=Detail&record=" . $adb->query_result($q, 0, 'atencionpresencialid') . "&app=SUPPORT",
            "ap_numero" => $adb->query_result($q, 0, 'ap_numero'),
            "ap_persona"     => $adb->query_result($q, 0, 'ap_persona'),
        ] : null;
    }

    public function isStarredEnabled(){
         return false;
    }

    public function getModuleIcon($viewname = null) {
        global $log;
        $log->info("Entra a getModuleIcon ATP $viewname");
        $moduleName = $this->getName();
        $lowerModuleName = strtolower($moduleName);
        $title = vtranslate($moduleName, $moduleName);

        $moduleIcon = "<i class='vicon-$lowerModuleName' title='$title'></i>";
        if ($this->source == 'custom') {
            $moduleShortName = mb_substr(trim($title), 0, 2);
            $moduleIcon = "<span class='custom-module' title='$title'>$moduleShortName</span>";
        }

        $imageFilePath = 'layouts/'.Vtiger_Viewer::getLayoutName()."/modules/$moduleName/$moduleName.png";
        if (file_exists($imageFilePath)) {
            $moduleIcon = "<img src='$imageFilePath' title='$title'/>";
        }
        
        $imageFilePath = 'layouts/' . Vtiger_Viewer::getLayoutName() . "/icons_custom_modules/$moduleName.png";
        
        if (file_exists($imageFilePath)) {
            $style = 'width:30px;';
            if($viewname == 'sidebar'){
                $style = 'width:23px;';
            }
            if($viewname == 'menu'){
                $style = 'width:25px; margin-top:-10px';
            }
            if ($viewname == 'detail') {
                $style = 'width:40px; filter: hue-rotate(0deg) saturate(0) brightness(100);';
            }

            if ($viewname == 'related') {
                $style = 'width:24px; filter: hue-rotate(0deg) saturate(0) brightness(0.60);\' class=\''.$moduleName.'RelTab';
            }
            $moduleIcon = "<img src='$imageFilePath' style='$style' title='$title' alt='$imageFilePath'/>";
            if ($viewname == 'related') {
                $moduleIcon .= '<style type="text/css">.tab-item.active .'.$moduleName.'RelTab{filter: hue-rotate(0deg) saturate(0) brightness(0.30) !important;}</style>';
            }
        }
        return $moduleIcon;
    }
    
}
