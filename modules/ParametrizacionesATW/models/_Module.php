<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * ************************************************************************************/

class ParametrizacionesAWT_Module_Model extends Vtiger_Module_Model {
	public function isStarredEnabled(){
		 return false;
	}

    public function getModuleIcon($viewname = null) {
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
                $style = 'width:30px; filter: hue-rotate(0deg) saturate(0) brightness(0.60);\' class=\''.$moduleName.'RelTab';
            }
            $moduleIcon = "<img src='$imageFilePath' style='$style' title='$title' alt='$imageFilePath'/>";
            if ($viewname == 'related') {
                $moduleIcon .= '<style type="text/css">.tab-item.active .'.$moduleName.'RelTab{filter: hue-rotate(0deg) saturate(0) brightness(0.30) !important;}</style>';
            }
        }
        return $moduleIcon;
    }
}