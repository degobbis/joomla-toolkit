<?php

class ExtensionController extends pm_Controller_Action
{
    public function listAction()
    {
        $installation = $this->_getInstallation();
        $this->view->pageTitle = $this->lmsg('controllers.extension.list.pageTitle', [
            'url' => pm_Context::getActionUrl('index', 'view') . '/id/' . $installation->id,
            'name' => $this->view->escape($installation->sitename),
        ]);
        $this->view->list = $this->_getList($installation);
    }

    public function listDataAction()
    {
        $this->_helper->json($this->_getList($this->_getInstallation())->fetchData());
    }

    /**
     * @return null|Modules_JoomlaToolkit_Model_Row_Installation
     */
    private function _getInstallation()
    {
        return (new Modules_JoomlaToolkit_Model_Broker_Installations())->findOne($this->_getParam('id'));
    }

    private function _getList(Modules_JoomlaToolkit_Model_Row_Installation $installation)
    {
        $list = new Modules_JoomlaToolkit_View_List_Extensions($installation, $this->view, $this->_request);
        $list->setDataUrl(['link' => pm_Context::getActionUrl('extension', 'list-data') . '/id/' . $installation->id]);
        return $list;
    }

    public function updateAction()
    {
        if (!$this->getRequest()->isPost()) {
            throw new Modules_JoomlaToolkit_Exception_PostMethodRequiredException();
        }
        /** @var Modules_JoomlaToolkit_Model_Row_Installation $installation */
        $installation = (new Modules_JoomlaToolkit_Model_Broker_Installations())->findOne($this->_getParam('id'));
        $command = new Modules_JoomlaToolkit_JoomlaCli_Extensions($installation);
        $command->call();
        Modules_JoomlaToolkit_Helper_ScanVhost::scanExtensions($installation);

        $this->_status->addInfo($this->lmsg('controllers.extension.update.successMsg', [
            'name' => $installation->name
        ]));
        $this->_redirect('extension/list');
    }

    public function updateItemAction()
    {
        if (!$this->getRequest()->isPost()) {
            throw new Modules_JoomlaToolkit_Exception_PostMethodRequiredException();
        }
        /** @var Modules_JoomlaToolkit_Model_Row_Extension $extension */
        $extension = (new Modules_JoomlaToolkit_Model_Broker_Extensions())->findOne($this->_getParam('id'));
        $installation = (new Modules_JoomlaToolkit_Model_Broker_Installations())->findOne($extension->installationId);

        $command = new Modules_JoomlaToolkit_JoomlaCli_Extension($extension);
        $command->call();
        Modules_JoomlaToolkit_Helper_ScanVhost::scanExtensions($installation);

        $this->_status->addInfo($this->lmsg('controllers.extension.updateItem.successMsg', [
            'name' => $extension->name
        ]));
        $this->_redirect('extension/list/id/' . $extension->installationId);
    }
}
