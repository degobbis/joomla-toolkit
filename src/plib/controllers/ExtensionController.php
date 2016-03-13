<?php

class ExtensionController extends pm_Controller_Action
{
    public function listAction()
    {
        $this->view->pageTitle = $this->lmsg('controllers.extension.list.pageTitle');
        $this->view->list = $this->_getList($this->_getInstallation());
    }

    public function listDataAction()
    {
        $this->_helper->json($this->_getList($this->_getInstallation())->fetchData());
    }

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
}
