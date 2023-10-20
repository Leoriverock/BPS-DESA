<?php

class LudereProEmailTemplates_Module_Model extends EmailTemplates_Module_Model
{
    /**
	 * Function to get module list which has the email field.
	 * @return type
	 */
	public function getAllModuleList(){
		$db = PearDatabase::getInstance();
		// Get modules names only those are active
		$query = 'SELECT DISTINCT(name) AS modulename FROM vtiger_tab 
					LEFT JOIN vtiger_field ON vtiger_field.tabid = vtiger_tab.tabid
					WHERE (vtiger_field.uitype = ? AND vtiger_tab.presence = ?)
                    OR vtiger_tab.name = "HelpDesk"';
		$params = array('13',0);
		// Check whether calendar module is active or not.
		if(vtlib_isModuleActive("Calendar")){
			$eventsTabid = getTabid('Events');
			$query .= ' OR vtiger_tab.tabid IN (?, ?)';
			array_push($params, $eventsTabid, getTabid('Calendar'));
		}
		$result = $db->pquery($query, $params);
		$num_rows = $db->num_rows($result);
		$moduleList = array();
		for($i=0; $i<$num_rows; $i++){
			$moduleList[] = $db->query_result($result, $i, 'modulename');
		}
		return $moduleList;
	}
}

?>