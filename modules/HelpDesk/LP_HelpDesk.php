<?php

class LudereProHelpDesk extends HelpDesk {
	
	function get_related_tickets($id, $cur_tab_id, $rel_tab_id, $actions=false) {
        global $log, $singlepane_view,$currentModule,$current_user;
        $log->debug("Entering get_related_tickets(".$id.") method ...");
        $this_module = $currentModule;

        $related_module = vtlib_getModuleNameById($rel_tab_id);
        require_once("modules/$related_module/$related_module.php");
        $other = new $related_module();
        vtlib_setup_modulevars($related_module, $other);
        $singular_modname = vtlib_toSingular($related_module);

        $parenttab = getParentTab();

        if($singlepane_view == 'true')
            $returnset = '&return_module='.$this_module.'&return_action=DetailView&return_id='.$id;
        else
            $returnset = '&return_module='.$this_module.'&return_action=CallRelatedList&return_id='.$id;

        $button = '';

        if($actions && getFieldVisibilityPermission($related_module, $current_user->id, 'parent_id','readwrite') == '0') {
            if(is_string($actions)) $actions = explode(',', strtoupper($actions));
            if(in_array('SELECT', $actions) && isPermitted($related_module,4, '') == 'yes') {
                $button .= "<input title='".getTranslatedString('LBL_SELECT')." ". getTranslatedString($related_module). "' class='crmbutton small edit' type='button' onclick=\"return window.open('index.php?module=$related_module&return_module=$currentModule&action=Popup&popuptype=detailview&select=enable&form=EditView&form_submit=false&recordid=$id&parenttab=$parenttab','test','width=640,height=602,resizable=0,scrollbars=0');\" value='". getTranslatedString('LBL_SELECT'). " " . getTranslatedString($related_module) ."'>&nbsp;";
            }
            if(in_array('ADD', $actions) && isPermitted($related_module,1, '') == 'yes') {
                $button .= "<input title='".getTranslatedString('LBL_ADD_NEW'). " ". getTranslatedString($singular_modname) ."' class='crmbutton small create'" .
                    " onclick='this.form.action.value=\"EditView\";this.form.module.value=\"$related_module\"' type='submit' name='button'" .
                    " value='". getTranslatedString('LBL_ADD_NEW'). " " . getTranslatedString($singular_modname) ."'>&nbsp;";
            }
        }

        $userNameSql = getSqlForNameInDisplayFormat(array('first_name'=>'vtiger_users.first_name', 'last_name' => 'vtiger_users.last_name'), 'Users');

        $query = "SELECT case when (vtiger_users.user_name not like '') then $userNameSql else vtiger_groups.groupname end as user_name, vtiger_users.id, t1.*, t2.*, vtiger_crmentity.smownerid, vtiger_crmentity.modifiedtime
                FROM vtiger_relationship
                INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_relationship.relationshipid
                LEFT JOIN vtiger_users ON vtiger_users.id=vtiger_crmentity.smownerid
                LEFT JOIN vtiger_groups ON vtiger_groups.groupid = vtiger_crmentity.smownerid
                JOIN vtiger_troubletickets t1 ON vtiger_relationship.ticketa = t1.ticketid
                JOIN vtiger_troubletickets t2 ON vtiger_relationship.ticketb = t2.ticketid
                WHERE vtiger_crmentity.deleted = 0 and (vtiger_relationship.ticketa = $id OR vtiger_relationship.ticketb = $id)";
        $return_value = GetRelatedList($this_module, $related_module, $other, $query, $button, $returnset);

        if($return_value == null) $return_value = Array();
        $return_value['CUSTOM_BUTTON'] = $button;

        $log->debug("Exiting get_related_tickets method ...");
        return $return_value;
    }
}