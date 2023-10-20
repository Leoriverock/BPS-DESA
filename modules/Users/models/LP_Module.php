<?php

class LudereProUsers_Module_Model extends Users_Module_Model {

	/**
	 * Function to save a given record model of the current module
	 * @param Vtiger_Record_Model $recordModel
	 */
	public function saveRecord(Vtiger_Record_Model $recordModel) {
		global $log;
		$log->info("entro a saveRecord");
		$moduleName = $this->get('name');
		$focus = CRMEntity::getInstance($moduleName);
		$fields = $focus->column_fields;

		//cuando se crean usuarios por defecto se hardcodean estos campos ya que se quitó la pantalla de primer login donde se piden
		if (!$recordModel->getId()) {
			$recordModel->set('language', 'es_es');
            $recordModel->set('time_zone', 'America/Montevideo');
            $recordModel->set('date_format', 'yyyy-mm-dd');
		}

		foreach ($fields as $fieldName => $fieldValue) {
			if ($fieldName === 'us_grupopref') {
				continue;
			}
			$fieldValue = $recordModel->get($fieldName);
			$log->info("los campos son: ");
			$log->info($fieldName);
			if (is_array($fieldValue)) {
				$focus->column_fields[$fieldName] = $fieldValue;
			} else if ($fieldValue !== null) {
				$focus->column_fields[$fieldName] = decode_html($fieldValue);
			}
		}

		$focus->mode = $recordModel->get('mode');
		$focus->id = $recordModel->getId();
		$log->info($moduleName);
		$focus->save($moduleName);
		return $recordModel->setId($focus->id);
	}
}
