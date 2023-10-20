<?php

class LudereProModComments_Record_Model extends ModComments_Record_Model {
	public function getDatosLlamada() {
		global $log;
		$recordModel = Vtiger_Record_Model::getInstanceById($this->get('callsid'));
		$log->debug($recordModel->getData());
		$inicio = $this->getFechaHoraFormateada($recordModel->get('callstartdate'), $recordModel->get('callstarttime'));
		$fin = '';
		if ($recordModel->get('callenddate')) {
			$fin = ' | ';
			$fin .= $this->getFechaHoraFormateada($recordModel->get('callenddate'), $recordModel->get('callendtime'));
		}
		return $inicio . $fin;
	}

	public function getFechaHoraFormateada($fecha, $hora) {
		return date('d/m/Y', strtotime($fecha)) . ' - ' . date('H:i', strtotime($hora));
	}

	/**
	 * Function returns all the parent comments model
	 * @param <Integer> $parentId
	 * @return ModComments_Record_Model(s)
	 */
	public static function getAllParentComments($parentId) {
		$db = PearDatabase::getInstance();
		$focus = CRMEntity::getInstance('ModComments');
		$query = $focus->get_comments();
		if($query) {
			$query .= " AND related_to = ? AND parent_comments = ? OR callsid = ? ORDER BY vtiger_crmentity.createdtime DESC";
			$result = $db->pquery($query, array($parentId, '', $parentId));
			$count = $db->num_rows($result);
			for($i = 0; $i < $count; $i++) {
				$rowData = $db->query_result_rowdata($result, $i);
				$recordInstance = new self();
				$recordInstance->setData($rowData);
				$recordInstances[] = $recordInstance;
			}

			return $recordInstances;
		} else {
			return array();
		}
	}
}