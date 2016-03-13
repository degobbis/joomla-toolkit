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
        $installationsBroker = new Modules_JoomlaToolkit_Model_Broker_Installations();
        $extensionsBroker = new Modules_JoomlaToolkit_Model_Broker_Extensions();
        foreach ($subscriptions as $id => $subscription) {
            $vhost = '/var/www/vhosts/' . $subscription;
            $resultFile = tempnam(pm_Context::getVarDir(), 'result_');
            // TODO: use correct PHP version
            $result = pm_ApiCli::callSbin('cmsscanner.phar', [
                'cmsscanner:detect',
                '--report=' . $resultFile,
                '--versions',
                $vhost,
            ]);
            if (0 != $result['code']) {
                $this->_status->addError($this->lmsg('controllers.index.scan.failureMsg', [
                    'msg' => $result['stdout']
                ]));
                $this->_redirect('index/list');
            }

            $fileManager = new pm_ServerFileManager();
            $resultJson = $fileManager->fileGetContents($resultFile);
            $fileManager->removeFile($resultFile);
            $result = json_decode($resultJson, true);

            foreach ($installationsBroker->findByField('subscriptionId', $id) as $installation) {
                $installation->delete();
            }

            foreach ($result as $installationInfo) {
                if ('Joomla' != $installationInfo['name']) {
                    continue;
                }
                $installation = $installationsBroker->createRow();
                $installation->subscriptionId = $id;
                $installation->sitename = $this->_getInstallationName($installationInfo['path']);
                $installation->path = substr($installationInfo['path'], strlen($vhost));
                $installation->version = $installationInfo['version'];
                $installation->save();

                $extensions = Modules_JoomlaToolkit_JoomlaCli_Update::getInfo($installation);
                foreach ($extensions as $extensionInfo) {
                    $extension = $extensionsBroker->createRow();
                    $extension->installationId = $installation->id;
                    $extension->name = $extensionInfo['name'];
                    $extension->currentVersion = $extensionInfo['currentVersion'];
                    $extension->newVersion = $extensionInfo['newVersion'];
                    $extension->needsUpdate = $extensionInfo['needsUpdate'];
                    $extension->save();
                }
            }
        }
        $this->_status->addInfo($this->lmsg('controllers.index.scan.successMsg'));
        $this->_redirect('index/list');
    }

    private function _getInstallationName($path)
    {
        Modules_JoomlaToolkit_JoomlaCli_Update::checkUpdateScript($path);

        // Call php cli script TODO: it should be run as user with less rights!
        $result = pm_ApiCli::callSbin("php", [$path . "/cli/update.php", "--sitename"]);
        $result = json_decode($result['stdout']);

        return $result->sitename ? $result->sitename : "empty";
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
}
