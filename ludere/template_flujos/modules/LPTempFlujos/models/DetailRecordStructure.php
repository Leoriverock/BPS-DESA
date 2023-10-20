<?php

class LPTempFlujos_DetailRecordStructure_Model extends Vtiger_DetailRecordStructure_Model {

	public function getStructure() {
		global $adb;
		if(!empty($this->structuredValues)) {
			return $this->structuredValues;
		}
		$values = array();
		$recordModel = $this->getRecord();
		$recordExists = !empty($recordModel);
		$moduleModel = $this->getModule();
		$blockModelList = $moduleModel->getBlocks();
		foreach($blockModelList as $blockLabel=>$blockModel) {
			$fieldModelList = $blockModel->getFields();
			if (!empty ($fieldModelList)) {
				$values[$blockLabel] = array();
				foreach($fieldModelList as $fieldName=>$fieldModel) {
					if($fieldModel->isViewableInDetailView()) {
						if($recordExists) {
							$rawvalue = $recordModel->get($fieldName);
							if (in_array($fieldName, array('tf_modulo', 'tf_campo', 'tf_campo_mod', 'tf_valor'))) {
								$tf_modulo = $recordModel->get('tf_modulo');
								if (in_array($fieldName, array('tf_campo_mod', 'tf_campo'))) {
									$query = 'SELECT f.fieldlabel 
										FROM vtiger_field f 
										JOIN vtiger_tab t ON t.tabid=f.tabid AND t.name = ?
										WHERE f.fieldname = ?
										LIMIT 1';
									$qv = $adb->pquery($query, array($tf_modulo, $rawvalue));
									if ($adb->num_rows($qv) > 0)
										$rawvalue = $adb->query_result($qv, 0, 'fieldlabel');
									$value = vtranslate($rawvalue, $tf_modulo);
								} else {
									$value = vtranslate($rawvalue, $tf_modulo);
								}
								$fieldModel->set('fieldvalue', $value);
								$fieldModel->set('uitype', 4); // que no sea editable
							} else {
								$fieldModel->set('fieldvalue', $rawvalue);
							}
						}
						$values[$blockLabel][$fieldName] = $fieldModel;
					}
				}
			}
		}
		$this->structuredValues = $values;
		return $values;
	}
}