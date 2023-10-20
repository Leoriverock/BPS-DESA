<?php

include_once 'modules/Vtiger/CRMEntity.php';

class Topics extends Vtiger_CRMEntity
{
    var $table_name = 'vtiger_topics';
    var $table_index = 'topicsid';

    var $customFieldTable = array('vtiger_topicscf', 'topicsid');

    var $tab_name = array('vtiger_crmentity', 'vtiger_topics', 'vtiger_topicscf');

    var $tab_name_index = array(
        'vtiger_crmentity' => 'crmid',
        'vtiger_topics' => 'topicsid',
        'vtiger_topicscf' => 'topicsid'
    );

    var $list_fields = array(
        /* Format: Field Label => Array(tablename, columnname) */
        // tablename should not have prefix 'vtiger_'
        'topicname' => array('topics', 'topicname'),
        'topiccateogry' => array('topics', 'topiccategory'),
        'Assigned To' => array('crmentity', 'smownerid')
    );
    var $list_fields_name = array(
        /* Format: Field Label => fieldname */
        'topicname' => 'topicname',
        'topiccateogry' => 'topiccateogry',
        'Assigned To' => 'assigned_user_id',
    );

    // Make the field link to detail view
    var $list_link_field = 'topicname';

    // For Popup listview and UI type support
    var $search_fields = array(
        /* Format: Field Label => Array(tablename, columnname) */
        // tablename should not have prefix 'vtiger_'
        'topicname' => array('topics', 'topicname'),
        'topiccateogry' => array('topics', 'topiccategory'),
        'Assigned To' => array('crmentity', 'smownerid')
    );
    var $search_fields_name = array(
        /* Format: Field Label => fieldname */
        'topicsname' => 'topicname',
        'topicscateogry' => 'topiccateogry',
        'Assigned To' => 'assigned_user_id',
    );

    // For Popup window record selection
    var $popup_fields = array('topicname');

    // For Alphabetical search
    var $def_basicsearch_col = 'topicname';

    // Column value to use on detail view record text display
    var $def_detailview_recname = 'topicname';

    // Used when enabling/disabling the mandatory fields for the module.
    // Refers to vtiger_field.fieldname values.
    var $mandatory_fields = array('topicname', 'assigned_user_id');

    var $default_order_by = 'topicname';
    var $default_sort_order = 'ASC';
}
