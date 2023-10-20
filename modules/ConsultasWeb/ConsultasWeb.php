<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

include_once 'modules/Vtiger/CRMEntity.php';

class ConsultasWeb extends Vtiger_CRMEntity {
	var $table_name = 'vtiger_consultasweb';
	var $table_index= 'consultaswebid';

	/**
	 * Mandatory table for supporting custom fields.
	 */
	var $customFieldTable = Array('vtiger_consultaswebcf', 'consultaswebid');

	/**
	 * Mandatory for Saving, Include tables related to this module.
	 */
	var $tab_name = Array('vtiger_crmentity', 'vtiger_consultasweb', 'vtiger_consultaswebcf');

	/**
	 * Mandatory for Saving, Include tablename and tablekey columnname here.
	 */
	var $tab_name_index = Array(
		'vtiger_crmentity' => 'crmid',
		'vtiger_consultasweb' => 'consultaswebid',
		'vtiger_consultaswebcf'=>'consultaswebid');

	/**
	 * Mandatory for Listing (Related listview)
	 */
	var $list_fields = Array (
		/* Format: Field Label => Array(tablename, columnname) */
		// tablename should not have prefix 'vtiger_'
		'consultaswebid' => Array('consultasweb','consultaswebid'),
		'Origen' => Array('consultasweb', 'cw_origen'),
		'De Correo' => Array('consultasweb', 'cw_de_mail'),
		'Asunto' => Array('consultasweb', 'cw_asunto'),
		'Para' => Array('consultasweb', 'cw_para'),
		//'Para To' => Array('crmentity','smownerid')
	);
	var $list_fields_name = Array (
		/* Format: Field Label => fieldname */
		'consultaswebid' => 'consultaswebid',
		'Origen' => 'cw_origen',
		'De Correo' => 'cw_de_mail',
		'Asunto' => 'cw_asunto',
		'Para' => 'cw_para',
		//'Assigned To' => 'assigned_user_id',
	);

	// Make the field link to detail view
	var $list_link_field = 'cw_de_mail';

	// For Popup listview and UI type support
	var $search_fields = Array(
		/* Format: Field Label => Array(tablename, columnname) */
		// tablename should not have prefix 'vtiger_'
		'consultaswebid' => Array('consultasweb','consultaswebid'),
		'Origen' => Array('consultasweb', 'cw_origen'),
		'De Correo' => Array('consultasweb', 'cw_de_mail'),
		'Asunto' => Array('consultasweb', 'cw_asunto'),
		'Para' => Array('consultasweb', 'cw_para'),
		//'Assigned To' => Array('vtiger_crmentity','assigned_user_id'),
	);
	var $search_fields_name = Array (
		/* Format: Field Label => fieldname */
		'Origen' => 'cw_origen',
		'De Correo' => 'cw_de_mail',
		'Asunto' => 'cw_asunto',
		'Para' => 'cw_para',
		//'Assigned To' => 'assigned_user_id',
	);

	// For Popup window record selection
	var $popup_fields = Array ('consultaswebid');

	// For Alphabetical search
	var $def_basicsearch_col = 'consultaswebid';

	// Column value to use on detail view record text display
	var $def_detailview_recname = 'consultaswebid';

	// Used when enabling/disabling the mandatory fields for the module.
	// Refers to vtiger_field.fieldname values.
	var $mandatory_fields = Array('consultaswebid');

	var $default_order_by = 'consultaswebid';
	var $default_sort_order='ASC';

	/**
	* Invoked when special actions are performed on the module.
	* @param String Module name
	* @param String Event Type
	*/
	function vtlib_handler($moduleName, $eventType) {
		global $adb;
 		if($eventType == 'module.postinstall') {
			// TODO Handle actions after this module is installed.
		} else if($eventType == 'module.disabled') {
			// TODO Handle actions before this module is being uninstalled.
		} else if($eventType == 'module.preuninstall') {
			// TODO Handle actions when this module is about to be deleted.
		} else if($eventType == 'module.preupdate') {
			// TODO Handle actions before this module is updated.
		} else if($eventType == 'module.postupdate') {
			// TODO Handle actions after this module is updated.
		}
 	}
}