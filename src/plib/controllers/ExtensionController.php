<?php

class ExtensionController extends pm_Controller_Action
{
    public function listAction()
    {
        $installation = (new Modules_JoomlaToolkit_Model_Broker_Installations())->findOne($this->_request->getParam('id'));
        $this->view->pageTitle = $this->lmsg('controllers.extension.list.pageTitle');
        $this->view->list = $this->_getList($installation);
    }

    public function listDataAction()
    {
        $installation = (new Modules_JoomlaToolkit_Model_Broker_Installations())->findOne($this->_request->getParam('id'));
        $this->_helper->json($this->_getList($installation)->fetchData());
    }

    private function _getList(Modules_JoomlaToolkit_Model_Row_Installation $installation)
    {
        $list = new Modules_JoomlaToolkit_View_List_Extensions($installation, $this->view, $this->_request);
        $list->setDataUrl(['action' => 'list-data']);
        return $list;
    }
}
