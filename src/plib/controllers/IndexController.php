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

    public function scanAction()
    {
        $subscriptions = $this->_getSubscriptions();
        $this->view->message = "TODO: implement";
    }

    public function registerAction()
    {
        $this->view->pageTitle = $this->lmsg('controllers.index.register.pageTitle');
        $installation = (new Modules_JoomlaToolkit_Model_Broker_Installations())->createRow();
        try {
            $installation->subscriptionId = pm_Session::getCurrentDomain()->getId();
        } catch (Exception $e) {
            $installation->subscriptionId = 0;
        }

        $returnUrl = pm_Context::getActionUrl('index', 'list');
        $form = new Modules_JoomlaToolkit_View_Form_Installation([
            'installation' => $installation,
            'returnUrl' => $returnUrl,
        ]);

        if ($this->_request->isPost() && $form->isValid($this->_request->getPost())) {
            try {
                $form->process();
            } catch (pm_Exception $e) {
                $this->_status->addError($e->getMessage());
                $this->_helper->json(['redirect' => $returnUrl]);
            }
            $this->_status->addInfo($this->lmsg('controllers.index.register.successMsg'));
            $this->_helper->json(['redirect' => $returnUrl]);
        }
        $this->_status->addWarning($this->lmsg('controllers.index.register.pageHint'));
        $this->view->form = $form;
    }

    private function _getSubscriptions()
    {
        $login = pm_Session::getClient()->getProperty('login');
        if ('admin' != $login) {
            return [pm_Session::getCurrentDomain()->getName()];
        }
        $request = "<webspace>
            <get>
                <filter/>
                <dataset>
                    <gen_info/>
                </dataset>
            </get>
        </webspace>";
        $response = pm_ApiRpc::getService()->call($request);
        $responseSubscriptions = reset($response->webspace->get);
        if ($responseSubscriptions instanceof SimpleXMLElement) {
            $responseSubscriptions = [$responseSubscriptions];
        }

        $subscriptions = [];
        foreach ($responseSubscriptions as $subscription) {
            $subscriptions[] = (string)$subscription->data->gen_info->name;
        }
        return $subscriptions;
    }
}
