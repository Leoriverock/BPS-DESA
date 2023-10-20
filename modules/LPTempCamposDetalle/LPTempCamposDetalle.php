<?php

include_once 'modules/Vtiger/CRMEntity.php';

class LPTempCamposDetalle extends Vtiger_CRMEntity {
        var $table_name = 'vtiger_lptempcamposdetalle';
        var $table_index= 'lptempcamposdetalleid';

        var $customFieldTable = Array('vtiger_lptempcamposdetallecf', 'lptempcamposdetalleid');

        var $tab_name = Array('vtiger_crmentity', 'vtiger_lptempcamposdetalle', 'vtiger_lptempcamposdetallecf');

        var $tab_name_index = Array(
                'vtiger_crmentity' => 'crmid',
                'vtiger_lptempcamposdetalle' => 'lptempcamposdetalleid',
                'vtiger_lptempcamposdetallecf'=>'lptempcamposdetalleid');

        var $list_fields = Array (
                /* Format: Field Label => Array(tablename, columnname) */
                // tablename should not have prefix 'vtiger_'
                'tcd_template' => Array('lptempcamposdetalle', 'tcd_template'),
                'Assigned To' => Array('crmentity','smownerid')
        );
        var $list_fields_name = Array (
                /* Format: Field Label => fieldname */
                'tcd_template' => 'tcd_template',
                'Assigned To' => 'assigned_user_id',
        );

        // Make the field link to detail view
        var $list_link_field = 'tcd_template';

        // For Popup listview and UI type support
        var $search_fields = Array(
                /* Format: Field Label => Array(tablename, columnname) */
                // tablename should not have prefix 'vtiger_'
                'tcd_template' => Array('lptempcamposdetalle', 'tcd_template'),
                'Assigned To' => Array('vtiger_crmentity','assigned_user_id'),
        );
        var $search_fields_name = Array (
                /* Format: Field Label => fieldname */
                'tcd_template' => 'tcd_template',
                'Assigned To' => 'assigned_user_id',
        );

        // For Popup window record selection
        var $popup_fields = Array ('tcd_template');

        // For Alphabetical search
        var $def_basicsearch_col = 'tcd_template';

        // Column value to use on detail view record text display
        var $def_detailview_recname = 'tcd_template';

        // Used when enabling/disabling the mandatory fields for the module.
        // Refers to vtiger_field.fieldname values.
        var $mandatory_fields = Array('tcd_template','assigned_user_id');

        var $default_order_by = 'tcd_template';
        var $default_sort_order='ASC';
}