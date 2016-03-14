<?php

/**
 * @property int id
 * @property int subscriptionId
 * @property string sitename
 * @property string path
 * @property string version
 * @property string newVersion
 * @property bool needsUpdate
 */
class Modules_JoomlaToolkit_Model_Row_Installation extends Modules_JoomlaToolkit_Model_Row
{
    public function getUrl()
    {
        // TODO: process different doc roots
        $subscription = new pm_Domain($this->subscriptionId);
        return 'http://' . $subscription->getName() . substr($this->path, strlen('/httpdocs'));
    }
}
