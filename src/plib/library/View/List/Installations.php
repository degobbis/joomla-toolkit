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

        $broker = new Modules_JoomlaToolkit_Model_Broker_Installations();
        if (pm_Session::getClient()->isAdmin()) {
            $installations = $broker->fetchAll();
        } else {
            $installations = $broker->findByField('subscriptionId', pm_Session::getCurrentDomain()->getId());
        }

        $data = [];
        foreach ($installations as $installation) {
            $data[] = [
                'id' => $installation->id,
                'subscription' => (new pm_Domain($installation->subscriptionId))->getName(),
                'path' => "<a href='{$overviewLink}/id/{$installation->id}'>{$this->_view->escape($installation->path)}</a>",
            ];
        }

        return $data;
    }

    private function _getColumns()
    {
        $columns = [pm_View_List_Simple::COLUMN_SELECTION];
        if (pm_Session::getClient()->isAdmin()) {
            $columns['subscription'] = [
                'title' => $this->lmsg('components.list.installations.subscriptionColumn'),
                'searchable' => true,
            ];
        }
        $columns['path'] = [
            'title' => $this->lmsg('components.list.installations.pathColumn'),
            'noEscape' => true,
            'searchable' => true,
        ];
        return $columns;
    }
}
