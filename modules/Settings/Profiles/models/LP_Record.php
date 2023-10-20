<?php

class LudereProSettings_Profiles_Record_Model extends Settings_Profiles_Record_Model
{
    public function isModuleFieldLocked($module, $field)
    {
        $fieldModel = $this->getProfileTabFieldModel($module, $field);
        if (is_object($module) && is_a($module, 'Vtiger_Module_Model')) {
            $moduleName = $module->getName();
        } else {
            $moduleName = $module;
        }
        //⚠ excepcion para que a nivel de permisos se pueda poner como readonly el asignado a
        if ($moduleName === 'Calls' && $fieldModel->getName() === 'assigned_user_id') {
            return false;
        }
        if (!$fieldModel->isEditable() || $fieldModel->isMandatory()
            || (in_array($fieldModel->get('uitype'), self::$fieldLockedUiTypes) && $moduleName !== 'Calls') || $fieldModel->hasCustomLock()) {
            return true;
        }
        return false;
    }
}
