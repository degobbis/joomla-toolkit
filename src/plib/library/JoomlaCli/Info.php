<?php

class Modules_JoomlaToolkit_JoomlaCli_Info extends Modules_JoomlaToolkit_JoomlaCli_AbstractCommand
{
    /**
     * @var Modules_JoomlaToolkit_Model_Row_Installation
     */
    private $_installation;

    public function __construct(Modules_JoomlaToolkit_Model_Row_Installation $installation)
    {
        $this->_installation = $installation;
        parent::__construct();
    }

    protected function _getInstallationPath()
    {
        $subscription = new pm_Domain($this->_installation->subscriptionId);
        return Modules_JoomlaToolkit_CmsScanner::getAbsoluteVhostPath($subscription->getName()) .
            $this->_installation->path;
    }

    public function call()
    {
        return json_decode($this->_call(['--info']), true);
    }
}