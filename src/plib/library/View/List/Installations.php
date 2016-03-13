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
            ],
        ]);
    }

    private function _fetchData()
    {
        $overviewLink = pm_Context::getActionUrl('index', 'view');
        $extensionLink = pm_Context::getActionUrl('extension', 'list');

        $broker = new Modules_JoomlaToolkit_Model_Broker_Installations();
        $select = $broker->select()
            ->setIntegrityCheck(false)
            ->from(['i'  => 'installations'])
            ->joinLeft(
                ['e' => 'extensions'],
                '(e.installationId = i.id)',
                'needsUpdate'
            );

        if (!pm_Session::getClient()->isAdmin()) {
            $select->where = $broker->getAdapter()->quoteInto('subscriptionId = ?', pm_Session::getCurrentDomain()->getId());
        }

        $installations = $broker->fetchAll($select);

        $data = [];
        foreach ($installations as $installation) {
            if (!isset($data[$installation->id])) {
                $data[$installation->id] = [
                    'id' => $installation->id,
                    'sitename' => "<a href='{$overviewLink}/id/{$installation->id}'>{$this->_view->escape($installation->sitename)}</a>",
                    'subscription' => (new pm_Domain($installation->subscriptionId))->getName(),
                    'path' => $installation->path,
                    'version' => $installation->version,
                    'extensionsTotal' => 0,
                    'extensionsOutdated' => 0,
                ];
            }
            $data[$installation->id]['extensionsTotal']++;
            if ($installation->needsUpdate == 1) {
                $data[$installation->id]['extensionsOutdated']++;
            }

        }

        foreach ($data as &$item) {
            $extensions = "<a href='{$extensionLink}/id/{$item['id']}'>" .
                $this->lmsg('components.list.installations.manageExtensionsTitle', ['count' => $item['extensionsTotal']]) .
                '</a>';
            if ($item['extensionsOutdated'] > 0) {
                $extensions .= '<div class="hint-sub hint-attention update-available">' .
                    $this->lmsg('components.list.installations.extensionsUpdateAvailable', ['outdated' => $item['extensionsOutdated']]) .
                    "&nbsp;" . '<a href="#" class="jsUpdateItem" data-item-id="YWtpc21ldF8zLjEuNw==" wp-instances="[1]">' .
                        $this->lmsg('components.list.installations.extensionsUpdateButton') .
                    '</a>' .
                '</div>';
            }
            $item['extensions'] = $extensions;
        }

        return $data;
    }

    private function _getColumns()
    {
        $columns = [pm_View_List_Simple::COLUMN_SELECTION];
        $columns['sitename'] = [
            'title' => $this->lmsg('components.list.installations.sitenameColumn'),
            'noEscape' => true,
            'searchable' => true,
        ];
        $columns['path'] = [
            'title' => $this->lmsg('components.list.installations.pathColumn'),
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
