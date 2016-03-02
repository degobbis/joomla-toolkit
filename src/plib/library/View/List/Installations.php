<?php

class Modules_JoomlaToolkit_View_List_Installations extends pm_View_List_Simple
{
    protected function _init()
    {
        parent::_init();

        $this->setData($this->_fetchData());
        $this->setColumns($this->_getColumns());
    }

    private function _fetchData()
    {
        $overviewLink = pm_Context::getActionUrl('index', 'view');

        $data = [];
        foreach ((new Modules_JoomlaToolkit_Model_Broker_Installations())->fetchAll() as $installation) {
            $data[] = [
                'id' => $installation->id,
                'path' => "<a href='{$overviewLink}/id/{$installation->id}'>{$this->_view->escape($installation->path)}</a>",
            ];
        }

        return $data;
    }

    private function _getColumns()
    {
        return [
            pm_View_List_Simple::COLUMN_SELECTION,
            'path' => [
                'title' => $this->lmsg('components.list.installations.pathColumn'),
                'noEscape' => true,
                'searchable' => true,
            ],
        ];
    }
}
