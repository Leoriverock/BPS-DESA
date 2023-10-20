<?php

class LudereProVtiger_Widget_Model extends Vtiger_Widget_Model
{

    public function getWidth()
    {
        $largerSizedWidgets = array('GroupedBySalesPerson', 'PipelinedAmountPerSalesPerson', 'GroupedBySalesStage', 'Funnel Amount', 'Prototipo');
        $title              = $this->getName();
        if (in_array($title, $largerSizedWidgets)) {
            $this->set('width', '2');
        }

        $width = $this->get('width');
        if (empty($width)) {
            $this->set('width', '1');
        }
        return $this->get('width');
    }

    public function getHeight()
    {
        //Special case for History widget
        $title = $this->getTitle();
        if ($title == 'History' || $title == 'Prototipo') {
            $this->set('height', '2');
        }
        $height = $this->get('height');
        if (empty($height)) {
            $this->set('height', '1');
        }
        return $this->get('height');
    }
}
