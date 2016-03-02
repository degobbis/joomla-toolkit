<?php

class IndexController extends pm_Controller_Action
{
    public function indexAction()
    {
        $this->_forward('list');
    }

    public function listAction()
    {
        $this->view->pageTitle = $this->lmsg('controllers.index.list.pageTitle');
        $this->view->list = $this->_getList();
    }

    public function listDataAction()
    {
        $this->_helper->json($this->_getList()->fetchData());
    }

    private function _getList()
    {
        $list = new Modules_JoomlaToolkit_View_List_Installations($this->view, $this->_request);
        $list->setDataUrl(['action' => 'list-data']);
        return $list;
    }

    public function viewAction()
    {
        $this->view->message = "TODO: implement";
    }
}
