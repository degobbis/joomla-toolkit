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

    public static function checkUpdateScript($path)
    {
        // TODO: remove when will be available in core
        $fileManager = new pm_ServerFileManager();
        $file = $path . '/cli/update.php';
        if ($fileManager->fileExists($file)) {
            return;
        }
        $content = file_get_contents('https://raw.githubusercontent.com/joomla-projects/cli-update/develop/src/cli/update.php');
        $fileManager->filePutContents($file, $content);
    }
}