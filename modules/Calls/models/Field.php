<?php

class Calls_Field_Model extends Vtiger_Field_Model
{
    //para evitar soportar la busqueda custom desde la vista detalle no permito editar el campo Persona
    public function isAjaxEditable()
    {
        if ($this->getName() === 'callaccount') {
            return false;
        }
        $ajaxRestrictedFields = array('4', '72', '61', '27', '28');
        if (!$this->isEditable() || in_array($this->get('uitype'), $ajaxRestrictedFields)) {
            return false;
        }
        return true;
    }
}
