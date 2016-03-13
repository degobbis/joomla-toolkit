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
        foreach ($this->_getSubscriptions() as $id => $subscription) {
            try {
                Modules_JoomlaToolkit_Helper_ScanVhost::scanInstallations($id, $subscription);
            } catch (Modules_JoomlaToolkit_Exception_UtilityException $e) {
                $this->_status->addError($this->lmsg('controllers.index.scan.failureMsg', [
                    'msg' => $e->getMessage()
                ]));
                $this->_redirect('index/list');
            }
        }
        $this->_status->addInfo($this->lmsg('controllers.index.scan.successMsg'));
        $this->_redirect('index/list');
    }

    private function _getSubscriptions()
    {
        $login = pm_Session::getClient()->getProperty('login');
        if ('admin' != $login) {
            $subscription = pm_Session::getCurrentDomain();
            return [$subscription->getId() => $subscription->getName()];
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
            $subscriptions[(int)$subscription->id] = (string)$subscription->data->gen_info->name;
        }
        return $subscriptions;
    }

    public function resetCacheAction()
    {
        foreach ((array)$this->_getParam('ids') as $id) {
            /** @var Modules_JoomlaToolkit_Model_Row_Installation $installation */
            $installation = (new Modules_JoomlaToolkit_Model_Broker_Installations())->findOne($id);
            $subscription = new pm_Domain($installation->subscriptionId);
            Modules_JoomlaToolkit_Helper_ScanVhost::scanInstallations($subscription->getId(), $subscription->getName());
        }
        $this->_helper->json([
            'status' => 'success',
            'statusMessages' => [[
                'status' => 'info',
                'content' => $this->lmsg('controllers.index.resetCache.successMsg'),
            ]],
        ]);
    }
}
