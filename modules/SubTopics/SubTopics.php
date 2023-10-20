<?php

include_once 'modules/Vtiger/CRMEntity.php';

class SubTopics extends Vtiger_CRMEntity
{
    var $table_name = 'vtiger_subtopics';
    var $table_index = 'subtopicsid';

    var $customFieldTable = array('vtiger_subtopicscf', 'subtopicsid');

    var $tab_name = array('vtiger_crmentity', 'vtiger_subtopics', 'vtiger_subtopicscf');

    var $tab_name_index = array(
        'vtiger_crmentity' => 'crmid',
        'vtiger_subtopics' => 'subtopicsid',
        'vtiger_subtopicscf' => 'subtopicsid'
    );

    var $list_fields = array(
        /* Format: Field Label => Array(tablename, columnname) */
        // tablename should not have prefix 'vtiger_'
        'subtopicname' => array('subtopics', 'subtopicname'),
        'subtopiccateogry' => array('subtopics', 'topiccategory'),
        'Assigned To' => array('crmentity', 'smownerid')
    );
    var $list_fields_name = array(
        /* Format: Field Label => fieldname */
        'subtopicname' => 'subtopicname',
        'subtopiccateogry' => 'subtopiccateogry',
        'Assigned To' => 'assigned_user_id',
    );

    // Make the field link to detail view
    var $list_link_field = 'subtopicname';

    // For Popup listview and UI type support
    var $search_fields = array(
        /* Format: Field Label => Array(tablename, columnname) */
        // tablename should not have prefix 'vtiger_'
        'subtopicname' => array('subtopics', 'subtopicname'),
        'subtopiccateogry' => array('subtopics', 'topiccategory'),
        'Assigned To' => array('crmentity', 'smownerid')
    );
    var $search_fields_name = array(
        /* Format: Field Label => fieldname */
        'subtopicsname' => 'subtopicname',
        'subtopicscateogry' => 'subtopiccateogry',
        'Assigned To' => 'assigned_user_id',
    );

    // For Popup window record selection
    var $popup_fields = array('subtopicname');

    // For Alphabetical search
    var $def_basicsearch_col = 'subtopicname';

    // Column value to use on detail view record text display
    var $def_detailview_recname = 'subtopicname';

    // Used when enabling/disabling the mandatory fields for the module.
    // Refers to vtiger_field.fieldname values.
    var $mandatory_fields = array('subtopicname', 'assigned_user_id');

    var $default_order_by = 'subtopicname';
    var $default_sort_order = 'ASC';
}
