<?php

class LudereProVtiger_Module_Model extends Vtiger_Module_Model {

	public function getModuleIcon() {
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
			$moduleIcon = "<img src='$imageFilePath' width='30' height='30' title='$title' alt='$imageFilePath'/>";
		}


		switch ($moduleName) {
			/*case 'AtencionesWeb':
				$moduleIcon = "<i class='fa-sharp fa-solid fa-envelope-open-text' style='font-size: 2em' title='$title'></i>";
				break;
			case 'ConsultasWeb':
				$moduleIcon = "<i class='fa-solid fa-mailbox' style='font-size: 2em' title='$title'></i>";
				break;
			case 'Parametrizaciones':
				$moduleIcon = "<i class='fa-solid fa-square-pen' style='font-size: 2em' title='$title'></i>";
				break;*/
			case 'Calls':
				$moduleIcon = "<i class='vicon-call' title='$title'></i>";
				break;
			case 'Relationship':
				$moduleIcon = "<i class='vicon-link' title='$title'></i>";
				break;
			case 'Topics':
				$moduleIcon = "<i class='vicon-tag' title='$title'></i>";
				break;
			default:
			break;
		}

		return $moduleIcon;
	}

}