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
        $extensionLink = pm_Context::getActionUrl('extension', 'list');

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
                'sitename' => $installation->sitename,
                'subscription' => (new pm_Domain($installation->subscriptionId))->getName(),
                'path' => "<a href='{$overviewLink}/id/{$installation->id}'>{$this->_view->escape($installation->path)}</a>",
                'version' => $installation->version,
                'extensions' => "<a href='{$extensionLink}/id/{$installation->id}'>{$this->lmsg('components.list.installations.manageExtensionsTitle')}</a>",
            ];
        }

        return $data;
    }

    private function _getColumns()
    {
        $columns = [pm_View_List_Simple::COLUMN_SELECTION];
        $columns['sitename'] = [
            'title' => $this->lmsg('components.list.installations.sitenameColumn'),
            'searchable' => true,
        ];
        $columns['path'] = [
            'title' => $this->lmsg('components.list.installations.pathColumn'),
            'noEscape' => true,
            'searchable' => true,
        ];
        if (pm_Session::getClient()->isAdmin()) {
            $columns['subscription'] = [
                'title' => $this->lmsg('components.list.installations.subscriptionColumn'),
                'searchable' => true,
            ];
        }
        $columns['version'] = [
            'title' => $this->lmsg('components.list.installations.versionColumn'),
        ];
        $columns['extensions'] = [
            'title' => $this->lmsg('components.list.installations.extensionsColumn'),
            'noEscape' => true,
        ];
        return $columns;
    }
}
