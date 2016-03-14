<?php

class Modules_JoomlaToolkit_View_List_Extensions extends pm_View_List_Simple
{
    public function __construct(Modules_JoomlaToolkit_Model_Row_Installation $installation, Zend_View $view, Zend_Controller_Request_Abstract $request)
    {
        parent::__construct($view, $request);

        $this->setData($this->_fetchData($installation));
        $this->setColumns($this->_getColumns());
    }

    private function _fetchData(Modules_JoomlaToolkit_Model_Row_Installation $installation)
    {
        $updateItemLink = pm_Context::getActionUrl('extension', 'update-item');
        $extensions = (new Modules_JoomlaToolkit_Model_Broker_Extensions())->findByField('installationId', $installation->id);

        $data = [];
        foreach ($extensions as $extension) {
            $version = '<span class="jsItemTitle">' . $this->_view->escape($extension['currentVersion']) . '</span>';
            if ($extension['needsUpdate']) {
                $version .= '<div class="hint-sub hint-attention update-available">' .
                    $this->lmsg('components.list.extensions.updateAvailable', ['version' => $extension['newVersion']]) .
                    "&nbsp;" . '<a href="' . $updateItemLink . '/id/' . $extension->id . '" class="jsUpdateItem" data-method="post">' .
                        $this->lmsg('components.list.extensions.updateButton') .
                    '</a>' .
                '</div>';
            }
            $data[] = [
                'name' => $extension['name'],
                'version' => $version,
            ];
        }

        return $data;
    }

    private function _getColumns()
    {
        return [
            'name' => [
                'title' => $this->lmsg('components.list.extensions.nameColumn'),
                'searchable' => true,
            ],
            'version' => [
                'title' => $this->lmsg('components.list.extensions.versionColumn'),
                'noEscape' => true,
            ],
        ];
    }
}
