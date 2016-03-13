<?php

class Modules_JoomlaToolkit_JoomlaCli_Update
{
    public static function getInfo(Modules_JoomlaToolkit_Model_Row_Installation $installation)
    {
        $subscription = new pm_Domain($installation->subscriptionId);
        // TODO: use correct PHP version and user access
        $result = pm_ApiCli::callSbin('php', [
            '/var/www/vhosts/' . $subscription->getName() . $installation->path . '/cli/update.php',
            '--info',
        ]);
        if (0 != $result['code']) {
            throw new pm_Exception('Cannot load extension info: ' . $result['stdout']);
        }
        return json_decode($result['stdout'], true);
    }
}