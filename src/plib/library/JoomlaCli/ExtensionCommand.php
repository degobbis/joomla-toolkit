<?php

class Modules_JoomlaToolkit_JoomlaCli_ExtensionCommand extends Modules_JoomlaToolkit_JoomlaCli_AbstractCommand
{
    /**
     * @var Modules_JoomlaToolkit_Model_Row_Extension
     */
    protected $_extension;

    public function __construct(Modules_JoomlaToolkit_Model_Row_Extension $extension)
    {
        $this->_extension = $extension;
        parent::__construct();
    }

    protected function _getInstallationPath()
    {
        $installation = (new Modules_JoomlaToolkit_Model_Broker_Installations())->findOne($this->_extension->installationId);
        $subscription = new pm_Domain($installation->subscriptionId);
        return Modules_JoomlaToolkit_CmsScanner::getAbsoluteVhostPath($subscription->getName()) .
            $installation->path;
    }

    public function call()
    {
        $this->_call(['--extension=' . $this->_extension->joomlaId]);
    }
}
