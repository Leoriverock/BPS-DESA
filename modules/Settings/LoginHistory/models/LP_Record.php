<?php

class LudereProSettings_LoginHistory_Record_Model extends Settings_LoginHistory_Record_Model
{

    /**
     * Function to retieve display value for a field
     * @param <String> $fieldName - field name for which values need to get
     * @return <String>
     */
    public function getDisplayValue($fieldName, $recordId = false)
    {
        $fieldValue = $this->get($fieldName);

        if ($fieldName == 'login_time') {
            if ($fieldValue != '0000-00-00 00:00:00') {
                $fieldValue = Vtiger_Datetime_UIType::getDateTimeValue($fieldValue);
            } else {
                $fieldValue = '---';
            }
        } else if ($fieldName == 'logout_time') {
            if ($fieldValue != '0000-00-00 00:00:00' && $this->get('status') != 'Signed in') {
                $fieldValue = Vtiger_Datetime_UIType::getDateTimeValue($fieldValue);
            } else {
                $fieldValue = '---';
            }
        }

        return $fieldName == 'status' ? vtranslate($fieldValue, 'Settings:LoginHistory') : $fieldValue;
    }
}
