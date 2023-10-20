<?php

include_once 'modules/Vtiger/CRMEntity.php';

class LPTempCamposSeleccion extends Vtiger_CRMEntity {
        var $table_name = 'vtiger_lptempcamposseleccion';
        var $table_index= 'lptempcamposseleccionid';

        var $customFieldTable = Array('vtiger_lptempcamposseleccioncf', 'lptempcamposseleccionid');

        var $tab_name = Array('vtiger_crmentity', 'vtiger_lptempcamposseleccion', 'vtiger_lptempcamposseleccioncf');

        var $tab_name_index = Array(
                'vtiger_crmentity' => 'crmid',
                'vtiger_lptempcamposseleccion' => 'lptempcamposseleccionid',
                'vtiger_lptempcamposseleccioncf'=>'lptempcamposseleccionid');

        var $list_fields = Array (
                /* Format: Field Label => Array(tablename, columnname) */
                // tablename should not have prefix 'vtiger_'
                'ts_modulo' => Array('lptempcamposseleccion', 'ts_modulo'),
                'Assigned To' => Array('crmentity','smownerid')
        );
        var $list_fields_name = Array (
                /* Format: Field Label => fieldname */
                'ts_modulo' => 'ts_modulo',
                'Assigned To' => 'assigned_user_id',
        );

        // Make the field link to detail view
        var $list_link_field = 'ts_modulo';

        // For Popup listview and UI type support
        var $search_fields = Array(
                /* Format: Field Label => Array(tablename, columnname) */
                // tablename should not have prefix 'vtiger_'
                'ts_modulo' => Array('lptempcamposseleccion', 'ts_modulo'),
                'Assigned To' => Array('vtiger_crmentity','assigned_user_id'),
        );
        var $search_fields_name = Array (
                /* Format: Field Label => fieldname */
                'ts_modulo' => 'ts_modulo',
                'Assigned To' => 'assigned_user_id',
        );

        // For Popup window record selection
        var $popup_fields = Array ('ts_modulo');

        // For Alphabetical search
        var $def_basicsearch_col = 'ts_modulo';

        // Column value to use on detail view record text display
        var $def_detailview_recname = 'ts_modulo';

        // Used when enabling/disabling the mandatory fields for the module.
        // Refers to vtiger_field.fieldname values.
        var $mandatory_fields = Array('ts_modulo','assigned_user_id');

        var $default_order_by = 'ts_modulo';
        var $default_sort_order='ASC';
}