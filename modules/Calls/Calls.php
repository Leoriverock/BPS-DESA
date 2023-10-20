<?php

include_once 'modules/Vtiger/CRMEntity.php';

class Calls extends Vtiger_CRMEntity
{
    var $table_name = 'vtiger_calls';
    var $table_index = 'callsid';

    var $customFieldTable = array('vtiger_callscf', 'callsid');

    var $tab_name = array('vtiger_crmentity', 'vtiger_calls', 'vtiger_callscf');

    var $tab_name_index = array(
        'vtiger_crmentity' => 'crmid',
        'vtiger_calls' => 'callsid',
        'vtiger_callscf' => 'callsid'
    );

    var $list_fields = array(
        /* Format: Field Label => Array(tablename, columnname) */
        // tablename should not have prefix 'vtiger_'
        'callid' => array('calls', 'callid'),
        'callstartdate' => array('calls', 'callstartdate'),
        'callstarttime' => array('calls', 'callstarttime'),
        'callphonenumber' => array('calls', 'callphonenumber'),
        'callaccount' => array('calls', 'callaccount'),
        'callenddate' => array('calls', 'callenddate'),
        'callendtime' => array('calls', 'callendtime'),
        'calluser' => array('calls', 'calluser'),
        'Assigned To' => array('crmentity', 'smownerid')
    );
    var $list_fields_name = array(
        /* Format: Field Label => fieldname */
        'callid' => 'callid',
        'callstartdate' => 'callstartdate',
        'callstarttime' => 'callstarttime',
        'callphonenumber' => 'callphonenumber',
        'callaccount' => 'callaccount',
        'callenddate' => 'callenddate',
        'callendtime' => 'callendtime',
        'calluser' => 'calluser',
        'Assigned To' => 'assigned_user_id',
    );

    // Make the field link to detail view
    var $list_link_field = 'tc_nombre';

    // For Popup listview and UI type support
    var $search_fields = array(
        /* Format: Field Label => Array(tablename, columnname) */
        // tablename should not have prefix 'vtiger_'
        'callid' => array('calls', 'callid'),
        'callstartdate' => array('calls', 'callstartdate'),
        'callstarttime' => array('calls', 'callstarttime'),
        'callphonenumber' => array('calls', 'callphonenumber'),
        'callaccount' => array('calls', 'callaccount'),
        'callenddate' => array('calls', 'callenddate'),
        'callendtime' => array('calls', 'callendtime'),
        'calluser' => array('calls', 'calluser'),
        'Assigned To' => array('crmentity', 'smownerid')
    );
    var $search_fields_name = array(
        /* Format: Field Label => fieldname */
        'callid' => 'callid',
        'callstartdate' => 'callstartdate',
        'callstarttime' => 'callstarttime',
        'callphonenumber' => 'callphonenumber',
        'callaccount' => 'callaccount',
        'callenddate' => 'callenddate',
        'callendtime' => 'callendtime',
        'calluser' => 'calluser',
        'Assigned To' => 'assigned_user_id',
    );

    // For Popup window record selection
    var $popup_fields = array('callstartdate');

    // For Alphabetical search
    var $def_basicsearch_col = 'callstartdate';

    // Column value to use on detail view record text display
    var $def_detailview_recname = 'callstartdate';

    // Used when enabling/disabling the mandatory fields for the module.
    // Refers to vtiger_field.fieldname values.
    var $mandatory_fields = array('callstartdate', 'assigned_user_id');

    var $default_order_by = 'callstartdate';
    var $default_sort_order = 'ASC';
}
