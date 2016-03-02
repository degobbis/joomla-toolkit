<?php

class Modules_JoomlaToolkit_View_List_Installations extends pm_View_List_Simple
{
    protected function _init()
    {
        parent::_init();

        $this->setData($this->_fetchData());
        $this->setColumns($this->_getColumns());
        $this->setTools([
            [
                //'class' => '', // TODO: add class with button
                'title' => $this->lmsg('components.list.installations.registerButtonTitle'),
                'description' => $this->lmsg('components.list.installations.registerButtonDesc'),
                'link' => pm_Context::getActionUrl('index', 'register'),
            ], [
                //'class' => '', // TODO: add class with button
                'title' => $this->lmsg('components.list.installations.scanButtonTitle'),
                'description' => $this->lmsg('components.list.installations.scanButtonDesc'),
                'link' => pm_Context::getActionUrl('index', 'scan'),
            ], [
                //'class' => '', // TODO: add class with button
                'title' => $this->lmsg('components.list.installations.pluginButtonTitle'),
                'description' => $this->lmsg('components.list.installations.pluginButtonDesc'),
                'execGroupOperation' => [
                    'url' => pm_Context::getActionUrl('plugin', 'manage'),
                ],
            ],
        ]);
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
