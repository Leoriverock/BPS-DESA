/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger_Edit_Js("Parametrizaciones_Edit_Js",{
},{
	/**
	 * Function which will register basic events which will be used in quick create as well
	 *
	 */
	registerEvents : function(container) {
		this._super(container);
		jQuery("[name='pt_grupo'] [label='Usuarios']").remove();
		jQuery("[name='pt_grupo']").select2('destroy').select2();
	}
});