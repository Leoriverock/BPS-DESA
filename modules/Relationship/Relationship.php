<?php
include_once 'modules/Vtiger/CRMEntity.php';

class Relationship extends Vtiger_CRMEntity
{
    public $table_name  = "vtiger_relationship";
    public $table_index = "relationshipid";

    public $customFieldTable = array('vtiger_relationshipcf', 'relationshipid');

    public $tab_name = array('vtiger_crmentity', 'vtiger_relationship', 'vtiger_relationshipcf');

    public $tab_name_index = array(
        'vtiger_crmentity'      => 'crmid',
        'vtiger_relationship'   => 'relationshipid',
        'vtiger_relationshipcf' => 'relationshipid',
    );

    public $list_fields = array(
        /* Format: Field Label => Array(tablename, columnname) */
        // tablename should not have prefix 'vtiger_'
        'Rol'        => array('relationship', 'rerol'),
        'Asignado A' => array('crmentity', 'smownerid'),
    );

    public $list_fields_name = array(
        /* Format: Field Label => fieldname */
        'Rol'        => 'rerol',
        'Asignado A' => 'assigned_user_id',
    );

    // Make the field link to detail view
    public $list_link_field = 'rerol';
    // For Popup listview and UI type support
    public $search_fields = array(
        /* Format: Field Label => Array(tablename, columnname) */
        // tablename should not have prefix 'vtiger_'
        'Rol'        => array('relationship', 'rerol'),
        'Asignado A' => array('vtiger_crmentity', 'assigned_user_id'),
    );

    public $search_fields_name = array(
        /* Format: Field Label => fieldname ~ columnname */
        'Rol'        => 'rerol',
        'Asignado A' => 'assigned_user_id',
    );

    // For Popup window record selection
    public $popup_fields = array('rerol');

    // For Alphabetical search
    public $def_basicsearch_col = 'rerol';

    // Column value to use on detail view record text display
    public $def_detailview_recname = 'rerol';

    // Used when enabling/disabling the mandatory fields for the module.
    // Refers to vtiger_field.fieldname values.
    public $mandatory_fields = array('rerol', 'assigned_user_id');

    public $default_order_by   = 'rerol';
    public $default_sort_order = 'ASC';
}
