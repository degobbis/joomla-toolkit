<?php

abstract class Modules_JoomlaToolkit_JoomlaCli_AbstractInstallationCommand extends Modules_JoomlaToolkit_JoomlaCli_AbstractCommand
{
    /**
     * @var Modules_JoomlaToolkit_Model_Row_Installation
     */
    protected $_installation;

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
}