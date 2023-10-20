<?php

/* +*********************************************************************************** 
 * Un custom del import bastante mas simple
 * *********************************************************************************** */

class LPTempFlujos_Import_View extends Vtiger_Import_View {
	
	function importBasicStep(Vtiger_Request $request) {
		$viewer = $this->getViewer($request);
		$moduleName = $request->getModule();

		$moduleModel = Vtiger_Module_Model::getInstance($moduleName);
		$moduleMeta = $moduleModel->getModuleMeta();

		$supportedFileTypes = Import_Utils_Helper::getSupportedFileExtensions();
		if ($moduleName == 'Calendar') {
			$supportedFileTypes[] = 'ics';
		}

		$viewer->assign('FOR_MODULE', $moduleName);
		$viewer->assign('MODULE', 'Import');
		$viewer->assign('SUPPORTED_FILE_TYPES', $supportedFileTypes);
		$viewer->assign('SUPPORTED_FILE_ENCODING', Import_Utils_Helper::getSupportedFileEncoding());
		$viewer->assign('SUPPORTED_DELIMITERS', Import_Utils_Helper::getSupportedDelimiters());
		$viewer->assign('AUTO_MERGE_TYPES', Import_Utils_Helper::getAutoMergeTypes($moduleName));

		//Duplicate records handling not supported for inventory moduels
		$duplicateHandlingNotSupportedModules = $this->getUnsupportedDuplicateHandlingModules();
		if(in_array($moduleName, $duplicateHandlingNotSupportedModules)){
			$viewer->assign('DUPLICATE_HANDLING_NOT_SUPPORTED', true);
		}
		//End

		$fileFormat = $request->get('fileFormat');
		if (!$fileFormat || !in_array($fileFormat, $supportedFileTypes)) {
			$fileFormat = 'csv';
		} else {
			$fileFormat = strtolower($fileFormat);
		}

		$viewer->assign('AVAILABLE_FIELDS', $moduleMeta->getMergableFields());
		$viewer->assign('ENTITY_FIELDS', $moduleMeta->getEntityFields());
		$viewer->assign('ERROR_MESSAGE', $request->get('error_message'));
		$viewer->assign('IMPORT_UPLOAD_SIZE_MB', Vtiger_Util_Helper::getMaxUploadSize());
		$viewer->assign('IMPORT_UPLOAD_SIZE', Vtiger_Util_Helper::getMaxUploadSizeInBytes());

		if(in_array($moduleName, Vtiger_Functions::getLineItemFieldModules())){
			$viewer->assign('MULTI_CURRENCY',true);
			$viewer->assign('CURRENCIES', getAllCurrencies());
		}

		$viewer->assign('FORMAT', $fileFormat);
		return $viewer->view('import/ImportBasicStep.tpl', 'LPTempFlujos');
	}
}