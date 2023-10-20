<?php

class SubTopics_ListView_Model extends Vtiger_ListView_Model
{
    //se ocultan los enlaces que se muestran en el btn de "Más"
    public function getAdvancedLinks()
    {
        return [];
    }
}
