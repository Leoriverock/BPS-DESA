<?php

class LudereProHelpDesk_Detail_View extends HelpDesk_Detail_View {

	/**
	 No se permite la edición de tickets desde la vista detalle/resumen
	 */
	function isAjaxEnabled($recordModel) {
		return false;
	}

}