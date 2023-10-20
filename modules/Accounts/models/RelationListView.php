<?php

class Accounts_RelationListView_Model extends Vtiger_RelationListView_Model {

	public function getCreateViewUrl() {
		$parentRecordModule = $this->getParentRecordModel();
		if ($this->getRelatedModuleModel()->getName() !== "HelpDesk") return parent::getCreateViewUrl();
		// para las incidencias el link de agregar queda igual que en el widget de tickets
		return "index.php?module=HelpDesk&view=Edit&sourceModule=Accounts&parent_id={$parentRecordModule->getId()}&relationOperation=true&sourceRecord={$parentRecordModule->getId()}";
	}

}

?>
