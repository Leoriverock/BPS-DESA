<?php

include_once 'modules/Vtiger/CRMEntity.php';

class LPTempCampos extends Vtiger_CRMEntity {
        var $table_name = 'vtiger_lptempcampos';
        var $table_index= 'lptempcamposid';

        var $customFieldTable = Array('vtiger_lptempcamposcf', 'lptempcamposid');

        var $tab_name = Array('vtiger_crmentity', 'vtiger_lptempcampos', 'vtiger_lptempcamposcf');

        var $tab_name_index = Array(
                'vtiger_crmentity' => 'crmid',
                'vtiger_lptempcampos' => 'lptempcamposid',
                'vtiger_lptempcamposcf'=>'lptempcamposid');

        var $list_fields = Array (
                /* Format: Field Label => Array(tablename, columnname) */
                // tablename should not have prefix 'vtiger_'
                'tc_nombre' => Array('lptempcampos', 'tc_nombre'),
                'Assigned To' => Array('crmentity','smownerid')
        );
        var $list_fields_name = Array (
                /* Format: Field Label => fieldname */
                'tc_nombre' => 'tc_nombre',
                'Assigned To' => 'assigned_user_id',
        );

        // Make the field link to detail view
        var $list_link_field = 'tc_nombre';

        // For Popup listview and UI type support
        var $search_fields = Array(
                /* Format: Field Label => Array(tablename, columnname) */
                // tablename should not have prefix 'vtiger_'
                'tc_nombre' => Array('lptempcampos', 'tc_nombre'),
                'Assigned To' => Array('vtiger_crmentity','assigned_user_id'),
        );
        var $search_fields_name = Array (
                /* Format: Field Label => fieldname */
                'tc_nombre' => 'tc_nombre',
                'Assigned To' => 'assigned_user_id',
        );

        // For Popup window record selection
        var $popup_fields = Array ('tc_nombre');

        // For Alphabetical search
        var $def_basicsearch_col = 'tc_nombre';

        // Column value to use on detail view record text display
        var $def_detailview_recname = 'tc_nombre';

        // Used when enabling/disabling the mandatory fields for the module.
        // Refers to vtiger_field.fieldname values.
        var $mandatory_fields = Array('tc_nombre','assigned_user_id');

        var $default_order_by = 'tc_nombre';
        var $default_sort_order='ASC';
}