<?php

class LudereProVtiger_Record_Model extends Vtiger_Record_Model
{
    public function getRollupCommentsForModule($startIndex = 0, $pageLimit = 10)
    {
        $rollupComments = array();
        $modulename     = $this->getModuleName();
        $recordId       = $this->getId();

        $relatedModuleRecordIds = $this->getCommentEnabledRelatedEntityIds($modulename, $recordId);
        array_unshift($relatedModuleRecordIds, $recordId);

        if ($relatedModuleRecordIds) {

            $listView       = Vtiger_ListView_Model::getInstance('ModComments');
            $queryGenerator = $listView->get('query_generator');
            $queryGenerator->setFields(array('parent_comments', 'createdtime', 'modifiedtime', 'related_to', 'assigned_user_id',
                'commentcontent', 'creator', 'id', 'customer', 'reasontoedit', 'userid', 'from_mailconverter', 'is_private', 'customer_email', 'callsid'));

            $query = $queryGenerator->getQuery();

            $query .= " AND vtiger_modcomments.related_to IN (" . generateQuestionMarks($relatedModuleRecordIds)
                . ") AND vtiger_modcomments.parent_comments=0 ORDER BY vtiger_crmentity.createdtime DESC LIMIT "
                . " $startIndex,$pageLimit";

            $db     = PearDatabase::getInstance();
            $result = $db->pquery($query, $relatedModuleRecordIds);
            if ($db->num_fields($result)) {
                for ($i = 0; $i < $db->num_rows($result); $i++) {
                    $rowdata           = $db->query_result_rowdata($result, $i);
                    $recordInstance    = new ModComments_Record_Model();
                    $rowdata['module'] = getSalesEntityType($rowdata['related_to']);
                    $recordInstance->setData($rowdata);
                    $rollupComments[] = $recordInstance;
                }
            }
        }

        return $rollupComments;
    }
}