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
        $broker = new Modules_JoomlaToolkit_Model_Broker_Installations();
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

            // Test

            $fileManager = new pm_ServerFileManager();
            $resultJson = $fileManager->fileGetContents($resultFile);
            $fileManager->removeFile($resultFile);
            $result = json_decode($resultJson, true);

            foreach ($broker->findByField('subscriptionId', $id) as $installation) {
                $installation->delete();
            }

            foreach ($result as $installationInfo) {
                if ('Joomla' != $installationInfo['name']) {
                    continue;
                }
                $installation = $broker->createRow();
                $installation->subscriptionId = $id;
                $installation->sitename = $this->_getInstallationName($installationInfo['path']);
                $installation->path = substr($installationInfo['path'], strlen($vhost));
                $installation->version = $installationInfo['version'];
                $installation->save();
            }
        }
        $this->_status->addInfo($this->lmsg('controllers.index.scan.successMsg'));
        $this->_redirect('index/list');
    }

    private function _getInstallationName($path)
    {
        if (!file_exists($path . "/cli/update.php"))
        {
            return "none";
        }

        // Call php cli script TODO: it should be run as user with less rights!
        $result = json_decode(pm_ApiCli::callSbin("php", ["-f", $path . "/cli/update.php", "--sitename"]));

        return $result->sitename ? $result->sitename : "";
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
