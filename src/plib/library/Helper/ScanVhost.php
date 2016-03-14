<?php

class Modules_JoomlaToolkit_Helper_ScanVhost
{
    const JOOMLA_CORE_EXTENSION_NAME = 'files_joomla';

    /**
     * @param int $subscriptionId
     * @param string $subscriptionName
     * @throws Zend_Db_Table_Row_Exception
     */
    public static function scanInstallations($subscriptionId, $subscriptionName)
    {
        $foundInstallations = Modules_JoomlaToolkit_CmsScanner::scanVhost($subscriptionName);

        $installationsBroker = new Modules_JoomlaToolkit_Model_Broker_Installations();
        foreach ($installationsBroker->findByField('subscriptionId', $subscriptionId) as $installation) {
            $installation->delete();
        }

        foreach ($foundInstallations as $installationInfo) {
            if (Modules_JoomlaToolkit_CmsScanner::NAME_JOOMLA != $installationInfo['name']) {
                continue;
            }
            $installation = $installationsBroker->createRow();
            $installation->subscriptionId = $subscriptionId;
            $installation->sitename = static::_getInstallationName($installationInfo['path']);
            $installation->path = static::_getInstallationPath($installationInfo['path'], $subscriptionName);
            $installation->version = $installationInfo['version'];
            $installation->save();

            static::scanExtensions($installation);
        }
    }

    /**
     * @param Modules_JoomlaToolkit_Model_Row_Installation $installation
     * @throws Zend_Db_Table_Row_Exception
     */
    public static function scanInstallation(Modules_JoomlaToolkit_Model_Row_Installation $installation)
    {
        $subscription = new pm_Domain($installation->subscriptionId);
        $fullPath = Modules_JoomlaToolkit_CmsScanner::getAbsoluteVhostPath($subscription->getName()) . $installation->path;
        $installation->sitename = static::_getInstallationName($fullPath);
        $installation->save();

        static::scanExtensions($installation);
    }

    /**
     * @param Modules_JoomlaToolkit_Model_Row_Installation $installation
     * @throws pm_Exception
     */
    public static function scanExtensions(Modules_JoomlaToolkit_Model_Row_Installation $installation)
    {
        $extensionsBroker = new Modules_JoomlaToolkit_Model_Broker_Extensions();

        foreach ($extensionsBroker->findByField('installationId', $installation->id) as $extension) {
            $extension->delete();
        }

        $command = new Modules_JoomlaToolkit_JoomlaCli_Info($installation);
        foreach ($command->call() as $extensionInfo) {
            if (static::JOOMLA_CORE_EXTENSION_NAME == $extensionInfo['name']) {
                if (!is_null($extensionInfo['currentVersion'])) {
                    $installation->version = $extensionInfo['currentVersion'];
                }
                $installation->newVersion = $extensionInfo['newVersion'];
                $installation->needsUpdate = $extensionInfo['needsUpdate'];
                $installation->save();
                continue;
            }
            $extension = $extensionsBroker->createRow();
            $extension->installationId = $installation->id;
            $extension->joomlaId = $extensionInfo['extension_id'];
            $extension->name = $extensionInfo['name'];
            $extension->currentVersion = $extensionInfo['currentVersion'];
            $extension->newVersion = $extensionInfo['newVersion'];
            $extension->needsUpdate = $extensionInfo['needsUpdate'];
            $extension->save();
        }
    }

    /**
     * @param string $path
     * @return string
     */
    private static function _getInstallationName($path)
    {
        return (new Modules_JoomlaToolkit_JoomlaCli_Sitename($path))->call();
    }

    /**
     * @param string $path
     * @param string $subscriptionName
     * @return string
     */
    private static function _getInstallationPath($path, $subscriptionName)
    {
        return substr($path, strlen(Modules_JoomlaToolkit_CmsScanner::getAbsoluteVhostPath($subscriptionName)));
    }
}
