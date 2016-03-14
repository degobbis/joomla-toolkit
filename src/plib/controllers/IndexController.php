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
        /** @var Modules_JoomlaToolkit_Model_Row_Installation $installation */
        $installation = (new Modules_JoomlaToolkit_Model_Broker_Installations())->findOne($this->_getParam('id'));

        $this->view->pageTitle = $installation->sitename;
        $this->view->tools = [
            [
                'title' => $this->lmsg('controllers.index.view.resetCacheButtonTitle'),
                'description' => $this->lmsg('controllers.index.view.resetCacheButtonDesc'),
                //'class' => 'sb-app-info', // TODO: Add class
                'link' => pm_Context::getActionUrl('index', 'reset-cache-item') . '/id/' . $installation->id,
            ],
            [
                'title' => $this->lmsg('controllers.index.view.extensionsButtonTitle'),
                'description' => $this->lmsg('controllers.index.view.extensionsButtonDesc'),
                //'class' => 'sb-app-info', // TODO: Add class
                'link' => pm_Context::getActionUrl('extension', 'list') . '/id/' . $installation->id,
            ],
        ];

        $this->view->form = new Modules_JoomlaToolkit_View_Form_Installation([
            'installation' => $installation,
        ]);
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

    public function updateAction()
    {
        if (!$this->getRequest()->isPost()) {
            throw new Modules_JoomlaToolkit_Exception_PostMethodRequiredException();
        }
        foreach ((array)$this->_getParam('ids') as $id) {
            /** @var Modules_JoomlaToolkit_Model_Row_Installation $installation */
            $installation = (new Modules_JoomlaToolkit_Model_Broker_Installations())->findOne($id);
            $command = new Modules_JoomlaToolkit_JoomlaCli_Core($installation);
            $command->call();
            $subscription = new pm_Domain($installation->subscriptionId);
            Modules_JoomlaToolkit_Helper_ScanVhost::scanInstallations($subscription->getId(), $subscription->getName());
        }
        $this->_helper->json([
            'status' => 'success',
            'statusMessages' => [[
                'status' => 'info',
                'content' => $this->lmsg('controllers.index.update.successMsg'),
            ]],
        ]);
    }

    public function updateItemAction()
    {
        if (!$this->getRequest()->isPost()) {
            throw new Modules_JoomlaToolkit_Exception_PostMethodRequiredException();
        }
        /** @var Modules_JoomlaToolkit_Model_Row_Installation $installation */
        $installation = (new Modules_JoomlaToolkit_Model_Broker_Installations())->findOne($this->_getParam('id'));
        $command = new Modules_JoomlaToolkit_JoomlaCli_Core($installation);
        $command->call();
        $subscription = new pm_Domain($installation->subscriptionId);
        Modules_JoomlaToolkit_Helper_ScanVhost::scanInstallations($subscription->getId(), $subscription->getName());
        $this->_status->addInfo($this->lmsg('controllers.index.updateItem.successMsg'));
        if ($this->_getParam('return', 'list') == 'list') {
            $this->_redirect('index/list');
        }
    }

    public function resetCacheAction()
    {
        if (!$this->getRequest()->isPost()) {
            throw new Modules_JoomlaToolkit_Exception_PostMethodRequiredException();
        }
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

    public function resetCacheItemAction()
    {
        // TODO: check POST request
        /** @var Modules_JoomlaToolkit_Model_Row_Installation $installation */
        $installation = (new Modules_JoomlaToolkit_Model_Broker_Installations())->findOne($this->_getParam('id'));
        Modules_JoomlaToolkit_Helper_ScanVhost::scanInstallation($installation);
        $this->_status->addInfo($this->lmsg('controllers.index.resetCacheItem.successMsg'));
        $this->_redirect('index/view/id/' . $installation->id);
    }

    public function updateExtensionsAction()
    {
        if (!$this->getRequest()->isPost()) {
            throw new Modules_JoomlaToolkit_Exception_PostMethodRequiredException();
        }
        foreach ((array)$this->_getParam('ids') as $id) {
            /** @var Modules_JoomlaToolkit_Model_Row_Installation $installation */
            $installation = (new Modules_JoomlaToolkit_Model_Broker_Installations())->findOne($id);

            $command = new Modules_JoomlaToolkit_JoomlaCli_Extensions($installation);
            $command->call();
            Modules_JoomlaToolkit_Helper_ScanVhost::scanExtensions($installation);
        }
        $this->_helper->json([
            'status' => 'success',
            'statusMessages' => [[
                'status' => 'info',
                'content' => $this->lmsg('controllers.index.updateExtensions.successMsg'),
            ]],
        ]);
    }
}
